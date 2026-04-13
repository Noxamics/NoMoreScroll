<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AdminOtp;
use App\Models\User;
use App\Notifications\AdminOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminController extends Controller
{
    /**
     * POST /api/admin/request-otp
     * Admin meminta kode OTP untuk login
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Cai admin user berdasarkan email
        $admin = AdminUser::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password tidak valid',
            ], 401);
        }

        try {
            // Generate OTP code 6 digit
            $otp_code = AdminOtp::generateOtp();

            // Hapus OTP lama jika ada
            AdminOtp::where('admin_id', $admin->id)
                ->where('verified', false)
                ->delete();

            // Simpan OTP baru (berlaku 10 menit)
            $admin_otp = AdminOtp::create([
                'admin_id'   => $admin->id,
                'email'      => $admin->email,
                'otp_code'   => $otp_code,
                'attempts'   => 0,
                'expired_at' => now()->addMinutes(10),
                'verified'   => false,
            ]);

            // Kirim email dengan OTP
            $admin->notify(new AdminOtpNotification($otp_code, $admin->name));

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda',
                'data'    => [
                    'email'      => $admin->email,
                    'otp_token'  => $admin_otp->id, // Untuk tracking
                    'expires_in' => 600, // 10 menit dalam detik
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/admin/verify-otp
     * Admin memverifikasi OTP dan mendapat JWT token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'otp_code' => 'required|string|size:6',
        ]);

        try {
            // Cari OTP berdasarkan email
            $admin_otp = AdminOtp::where('email', $request->email)
                ->where('verified', false)
                ->latest()
                ->first();

            // Validasi OTP ada
            if (!$admin_otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP tidak ditemukan atau sudah digunakan',
                ], 401);
            }

            // Validasi OTP belum expired
            if ($admin_otp->isExpired()) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP sudah expired, silakan minta OTP baru',
                ], 401);
            }

            // Validasi attempts belum limit
            if ($admin_otp->isLimitExceeded()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan, silakan minta OTP baru',
                ], 429);
            }

            // Increment attempts
            $admin_otp->increment('attempts');

            // Validasi kode OTP
            if ($admin_otp->otp_code !== $request->otp_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak sesuai',
                    'data'    => [
                        'attempts_left' => 5 - $admin_otp->attempts,
                    ],
                ], 401);
            }

            // OTP berhasil, mark sebagai verified
            $admin_otp->update(['verified' => true]);

            // Get admin user
            $admin = AdminUser::find($admin_otp->admin_id);

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin tidak ditemukan',
                ], 404);
            }

            // Generate JWT token
            $token = Auth::guard('admin')->login($admin);

            return response()->json([
                'success' => true,
                'message' => 'Login admin berhasil',
                'data'    => [
                    'admin'      => $admin,
                    'token'      => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal verifikasi OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Admin Login (tanpa OTP - optional)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        try {
            // Attempt to authenticate using admin guard dengan Auth facade
            if (!$token = Auth::guard('admin')->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password admin tidak valid',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login admin berhasil',
            'data'    => [
                'admin'      => Auth::guard('admin')->user(),
                'token'      => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ],
        ]);
    }

    /**
     * Get Dashboard Statistics
     * 
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse
    {
        $totalUsers = User::count();
        $totalAdmins = AdminUser::count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved',
            'data'    => [
                'total_users'  => $totalUsers,
                'total_admins' => $totalAdmins,
                'stats'        => [
                    'users'  => $totalUsers,
                    'admins' => $totalAdmins,
                ]
            ]
        ]);
    }

    /**
     * Get All Users
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $users = User::paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully',
            'data'    => $users,
        ]);
    }

    /**
     * Get User Detail
     * 
     * @param string $id
     * @return JsonResponse
     */
    public function userDetail($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User detail retrieved',
            'data'    => $user,
        ]);
    }

    /**
     * Export Report
     * 
     * @return JsonResponse
     */
    public function exportReport(): JsonResponse
    {
        $users = User::all();
        $admins = AdminUser::all();

        $report = [
            'generated_at' => now(),
            'total_users'  => $users->count(),
            'total_admins' => $admins->count(),
            'users'        => $users,
            'admins'       => $admins,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Report exported successfully',
            'data'    => $report,
        ]);
    }
}
