<?php

namespace App\Services;

use App\Models\MlResult;
use App\Models\Questionnaire;
use App\Models\Recommendation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * MlService
 * ──────────────────────────────────────────────────────────
 * Bertanggung jawab mengirim data survey ke Flask REST-API
 * (Member 5 — ML / Data Science) dan menyimpan hasilnya
 * ke MongoDB melalui model Laravel.
 *
 * Flow:
 *  Laravel (Member 1)  →  HTTP POST  →  Flask (Member 5)
 *                      ←  JSON result ←
 *  Laravel simpan ml_results + recommendations ke MongoDB
 */
class MlService
{
    private string $baseUrl;
    private int    $timeout;

    public function __construct()
    {
        // URL Flask didapat dari .env: ML_SERVICE_URL=http://localhost:5000
        $this->baseUrl = config('services.ml.url', 'http://localhost:5000');
        $this->timeout = config('services.ml.timeout', 30);
    }

    /**
     * Kirim data questionnaire ke Flask dan simpan hasilnya.
     *
     * @return array{success: bool, data?: array, error?: string}
     */
    public function predict(Questionnaire $questionnaire): array
    {
        $payload = $this->buildPayload($questionnaire);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['Accept' => 'application/json'])
                ->post("{$this->baseUrl}/predict", $payload);

            if (! $response->successful()) {
                Log::error('ML Service error', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'survey'  => $questionnaire->_id,
                ]);
                return [
                    'success' => false,
                    'error'   => "Flask mengembalikan HTTP {$response->status()}",
                ];
            }

            $mlData = $response->json();

            // Validasi response dari Flask
            if (! $this->isValidResponse($mlData)) {
                return [
                    'success' => false,
                    'error'   => 'Response Flask tidak sesuai format yang diharapkan',
                ];
            }

            // Simpan ke MongoDB
            $mlResult = $this->saveMlResult($questionnaire, $mlData);

            return [
                'success' => true,
                'data'    => $mlResult->load('recommendations'),
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('ML Service unreachable', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => 'Flask ML service tidak bisa dihubungi',
            ];
        } catch (\Exception $e) {
            Log::error('ML Service unexpected error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => 'Terjadi kesalahan tak terduga',
            ];
        }
    }

    // ─── Private Helpers ────────────────────────────────────────────────────

    /**
     * Buat payload JSON yang akan dikirim ke Flask.
     * Sesuaikan field name dengan yang diharapkan model ML.
     */
    private function buildPayload(Questionnaire $questionnaire): array
    {
        return [
            // Identifikasi
            'questionnaire_id'       => (string) $questionnaire->_id,

            // Penggunaan perangkat
            'device_hours_per_day'   => $questionnaire->device_hours_per_day,
            'phone_unlocks_per_day'  => $questionnaire->phone_unlocks_per_day,
            'notifications_per_day'  => $questionnaire->notifications_per_day,
            'social_media_minutes'   => $questionnaire->social_media_minutes,

            // Aktivitas sehari-hari
            'study_minutes'          => $questionnaire->study_minutes,
            'physical_activity_days' => $questionnaire->physical_activity_days,
            'daily_role'             => $questionnaire->daily_role,
            'income_level'           => $questionnaire->income_level,

            // Tidur
            'sleep_hours'            => $questionnaire->sleep_hours,
            'sleep_quality'          => $questionnaire->sleep_quality,

            // Kesehatan mental
            'anxiety_score'          => $questionnaire->anxiety_score,
            'depression_score'       => $questionnaire->depression_score,
            'stress_level'           => $questionnaire->stress_level,
            'happiness_score'        => $questionnaire->happiness_score,
        ];
    }

    /**
     * Validasi bahwa response Flask mengandung field wajib.
     *
     * Flask (Member 5) harus return:
     * {
     *   "focus_score": float,
     *   "productivity_score": float,
     *   "digital_dependence_score": float,
     *   "high_risk_flag": bool,
     *   "recommendations": array
     * }
     */
    private function isValidResponse(array $data): bool
    {
        $required = [
            'focus_score',
            'productivity_score',
            'digital_dependence_score',
            'high_risk_flag',
            'recommendations',
        ];

        foreach ($required as $field) {
            if (! array_key_exists($field, $data)) {
                Log::warning("ML response missing field: {$field}");
                return false;
            }
        }

        return true;
    }

    /**
     * Simpan hasil prediksi ke koleksi ml_results dan recommendations.
     */
    private function saveMlResult(Questionnaire $questionnaire, array $mlData): MlResult
    {
        // Upsert: kalau sudah ada result untuk questionnaire ini, update
        $mlResult = MlResult::updateOrCreate(
            ['questionnaire_id' => $questionnaire->_id],
            [
                'user_id'                   => $questionnaire->user_id,
                'focus_score'               => $mlData['focus_score'],
                'productivity_score'        => $mlData['productivity_score'],
                'digital_dependence_score'  => $mlData['digital_dependence_score'],
                'high_risk_flag'            => $mlData['high_risk_flag'],
            ]
        );

        // Simpan recommendations (hapus lama dulu kalau retry)
        $mlResult->recommendations()->delete();

        if (! empty($mlData['recommendations'])) {
            Recommendation::create([
                'result_id'       => $mlResult->_id,
                'recommendations' => $mlData['recommendations'],
            ]);
        }

        return $mlResult;
    }
}