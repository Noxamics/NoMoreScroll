<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserController extends Controller
{
    /**
     * Show users management page
     */
    public function index()
    {
        $total = User::count();
        $users = User::paginate(15);
        
        $stats = [
            'total' => $total,
            'active_today' => User::whereDate('last_login_at', Carbon::today())->count(),
            'new_7d' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'high_risk' => User::where('focus_score', '<', 4)->count(),
        ];
        
        return view('admin.users', compact('total', 'users', 'stats'));
    }

    /**
     * Delete a user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil dihapus');
    }
}
