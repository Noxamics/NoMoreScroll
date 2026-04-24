<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MlResult;

class PredictController extends Controller
{
    public function predict(Request $request)
    {
        // 1. Validasi input dari Flutter
        $validated = $request->validate([
            'age' => 'required|numeric',
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
            'study_mins' => 'required|numeric',
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

        // 3. Simpan ke MongoDB
        MlResult::create([
            'user_id' => auth()->id(),
            'questionnaire_id' => $request->questionnaire_id,
            'digital_dependence_score' => $hasil['digital_dependence_score'],
            // 'recommendations' => $hasil['recommendations'] ?? [],
        ]);

        // 4. Balas ke Flutter
        return response()->json([
            'digital_dependence_score' => $hasil['digital_dependence_score'],
            'message' => 'Prediksi berhasil'
        ]);
    }
}