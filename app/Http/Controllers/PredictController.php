<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MlResult;

/**
 * PredictController — Legacy direct predict endpoint
 *
 * Route: POST /api/predict
 * Digunakan untuk quick-test prediksi ML tanpa perlu JWT.
 * Untuk produksi, gunakan SurveyController::store() yang lewat JWT.
 */
class PredictController extends Controller
{
    public function predict(Request $request)
    {
        // 1. Validasi input dari Flutter
        $validated = $request->validate([
            'date_of_birth' => 'nullable|date',
            'gender' => 'required|string',
            'region' => 'required|string',
            'income_level' => 'required|string',
            'education_level' => 'required|string',
            'daily_role' => 'required|string',
            'device_type' => 'required|string',
            'device_hours_per_day' => 'required|numeric',
            'phone_unlocks' => 'required|numeric',
            'notifications_per_day' => 'required|numeric',
            'social_media_mins' => 'required|numeric',
            'study_minutes' => 'required|numeric',
            'physical_activity_days' => 'required|numeric',
            'sleep_hours' => 'required|numeric',
            'sleep_quality' => 'required|numeric',
            'anxiety_score' => 'required|numeric',
            'depression_score' => 'required|numeric',
            'stress_level' => 'required|numeric',
            'happiness_score' => 'required|numeric',
        ]);

        // 2. Kirim ke Flask
        $response = Http::post('http://127.0.0.1:5000/predict', $validated);

        if ($response->failed()) {
            return response()->json(['error' => 'Flask error'], 500);
        }

        $hasil = $response->json();

        // 3. Hitung week_group
        $weekGroup = now()->format('Y') . '-W' . str_pad(now()->isoWeek(), 2, '0', STR_PAD_LEFT);

        // 4. Simpan ke MongoDB dengan format embedded baru
        MlResult::create([
            'user_id' => auth()->id(),
            'questionnaire_id' => $request->questionnaire_id,
            'ml_result' => [
                'digital_dependence_score' => $hasil['digital_dependence_score'] ?? 0,
                'category' => $hasil['category'] ?? 'rendah',
                'confidence' => $hasil['confidence'] ?? 0,
            ],
            'ai_analysis' => $hasil['ai_analysis'] ?? [
                'penyebab' => [],
                'rekomendasi' => [],
                'summary' => '',
                'model' => 'unknown',
                'generated_at' => now()->toISOString(),
            ],
            'week_group' => $weekGroup,
        ]);

        // 5. Balas ke Flutter
        return response()->json([
            'digital_dependence_score' => $hasil['digital_dependence_score'] ?? 0,
            'category' => $hasil['category'] ?? 'rendah',
            'confidence' => $hasil['confidence'] ?? 0,
            'message' => 'Prediksi berhasil'
        ]);
    }
}