<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MlResult;
use App\Models\Questionnaire;
use App\Services\MlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrediksiController extends Controller
{
    public function __construct(protected MlService $mlService) {}

    /**
     * GET /api/prediksi
     * Semua hasil prediksi milik user (history)
     */
    public function index(): JsonResponse
    {
        $results = MlResult::where('user_id', auth()->id())
            ->with('questionnaire')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $results->map(fn($r) => $this->formatResult($r)),
        ]);
    }

    /**
     * GET /api/prediksi/latest
     * Hasil prediksi terbaru (untuk dashboard Flutter/Web)
     */
    public function latest(): JsonResponse
    {
        $result = MlResult::where('user_id', auth()->id())
            ->with('questionnaire')
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $result) {
            return response()->json([
                'success' => true,
                'message' => 'Belum ada hasil prediksi',
                'data'    => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatResult($result),
        ]);
    }

    /**
     * GET /api/prediksi/{id}
     * Detail satu hasil prediksi
     */
    public function show(string $id): JsonResponse
    {
        $result = MlResult::where('_id', $id)
            ->where('user_id', auth()->id())
            ->with('questionnaire')
            ->first();

        if (! $result) {
            return response()->json([
                'success' => false,
                'message' => 'Hasil prediksi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatResult($result),
        ]);
    }

    /**
     * POST /api/prediksi/retry/{questionnaire_id}
     * Re-run prediksi ML untuk survey tertentu
     */
    public function retry(string $questionnaireId): JsonResponse
    {
        $questionnaire = Questionnaire::where('_id', $questionnaireId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $questionnaire) {
            return response()->json([
                'success' => false,
                'message' => 'Survey tidak ditemukan',
            ], 404);
        }

        $predictionResult = $this->mlService->predict($questionnaire);

        if (! $predictionResult['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Prediksi ML gagal: ' . $predictionResult['error'],
            ], 503);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prediksi berhasil dijalankan ulang',
            'data'    => $predictionResult['data'],
        ]);
    }

    /**
     * GET /api/prediksi/summary
     * Ringkasan statistik semua prediksi user (untuk grafik)
     */
    public function summary(): JsonResponse
    {
        $results = MlResult::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        $scores = $results->map(fn($r) => $r->ml_result['digital_dependence_score'] ?? 0);

        $summary = [
            'total_surveys'          => $results->count(),
            'avg_dependence_score'   => round($scores->avg(), 2),
            'high_risk_count'        => $results->filter(fn($r) =>
                ($r->ml_result['category'] ?? '') === 'tinggi'
            )->count(),
            'trend'                  => $results->map(fn($r) => [
                'date'               => $r->created_at?->format('Y-m-d'),
                'dependence_score'   => $r->ml_result['digital_dependence_score'] ?? 0,
                'category'           => $r->ml_result['category'] ?? 'rendah',
                'confidence'         => $r->ml_result['confidence'] ?? 0,
            ]),
        ];

        return response()->json([
            'success' => true,
            'data'    => $summary,
        ]);
    }

    // ─── Private Helpers ────────────────────────────────────────────────────

    private function formatResult(MlResult $result): array
    {
        return [
            'id'                      => $result->_id,
            'ml_result'               => $result->ml_result,
            'ai_analysis'             => $result->ai_analysis,
            'week_group'              => $result->week_group,
            'questionnaire'           => $result->questionnaire,
            'created_at'              => $result->created_at,
        ];
    }
}