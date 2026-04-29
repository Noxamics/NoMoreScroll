<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSurveyRequest;
use App\Models\Questionnaire;
use App\Services\MlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function __construct(protected MlService $mlService) {}

    /**
     * GET /api/surveys
     * Ambil semua survey milik user yang login
     */
    public function index(): JsonResponse
    {
        $surveys = Questionnaire::where('user_id', auth()->id())
            ->with('mlResult')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $surveys,
        ]);
    }

    /**
     * POST /api/surveys
     * Submit survey baru → otomatis trigger prediksi ML
     */
    public function store(StoreSurveyRequest $request): JsonResponse
    {
        // 1. Simpan data survey ke MongoDB
        $questionnaire = Questionnaire::create([
            'user_id'                  => auth()->id(),
            'device_type'              => $request->device_type ?? 'Android',
            'device_hours_per_day'     => $request->device_hours_per_day,
            'phone_unlocks_per_day'    => $request->phone_unlocks_per_day,
            'notifications_per_day'    => $request->notifications_per_day,
            'social_media_minutes'     => $request->social_media_minutes,
            'study_minutes'            => $request->study_minutes,
            'physical_activity_days'   => $request->physical_activity_days,
            'sleep_hours'              => $request->sleep_hours,
            'sleep_quality'            => $request->sleep_quality,
            'anxiety_score'            => $request->anxiety_score,
            'depression_score'         => $request->depression_score,
            'stress_level'             => $request->stress_level,
            'happiness_score'          => $request->happiness_score,
        ]);

        // 2. Kirim data ke Flask ML service untuk prediksi
        $predictionResult = $this->mlService->predict($questionnaire);

        if (! $predictionResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Survey tersimpan, tapi prediksi ML gagal: ' . $predictionResult['error'],
                'data'    => ['questionnaire' => $questionnaire],
            ], 207); // 207 Multi-Status: sebagian berhasil
        }

        return response()->json([
            'success' => true,
            'message' => 'Survey berhasil disubmit dan dianalisis',
            'data'    => [
                'questionnaire' => $questionnaire,
                'ml_result'     => $predictionResult['data'],
            ],
        ], 201);
    }

    /**
     * GET /api/surveys/{id}
     * Detail satu survey beserta hasil ML-nya
     */
    public function show(string $id): JsonResponse
    {
        $survey = Questionnaire::where('_id', $id)
            ->where('user_id', auth()->id())
            ->with('mlResult')
            ->first();

        if (! $survey) {
            return response()->json([
                'success' => false,
                'message' => 'Survey tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $survey,
        ]);
    }

    /**
     * DELETE /api/surveys/{id}
     * Hapus survey (dan cascade hapus ml_result)
     */
    public function destroy(string $id): JsonResponse
    {
        $survey = Questionnaire::where('_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (! $survey) {
            return response()->json([
                'success' => false,
                'message' => 'Survey tidak ditemukan',
            ], 404);
        }

        // Hapus relasi ML result (embedded, tidak perlu hapus recommendations terpisah)
        if ($survey->mlResult) {
            $survey->mlResult->delete();
        }

        $survey->delete();

        return response()->json([
            'success' => true,
            'message' => 'Survey berhasil dihapus',
        ]);
    }

    /**
     * GET /api/surveys/latest
     * Ambil survey terakhir + hasil prediksi untuk home screen Flutter
     */
    public function latest(): JsonResponse
    {
        $survey = Questionnaire::where('user_id', auth()->id())
            ->with('mlResult')
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $survey) {
            return response()->json([
                'success' => true,
                'message' => 'Belum ada data survey',
                'data'    => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $survey,
        ]);
    }
}