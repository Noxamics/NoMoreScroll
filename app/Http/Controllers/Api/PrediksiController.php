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
        // Ambil semua questionnaire user → join ml_results
        $results = MlResult::whereHas('questionnaire', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['questionnaire', 'recommendations'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }

    /**
     * GET /api/prediksi/latest
     * Hasil prediksi terbaru (untuk dashboard Flutter/Web)
     */
    public function latest(): JsonResponse
    {
        $result = MlResult::whereHas('questionnaire', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['questionnaire', 'recommendations'])
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
            ->whereHas('questionnaire', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with(['questionnaire', 'recommendations'])
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
     * (berguna kalau Flask sempat down saat survey disubmit)
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
        $results = MlResult::whereHas('questionnaire', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get(['focus_score', 'productivity_score', 'digital_dependence_score', 'high_risk_flag', 'created_at']);

        $summary = [
            'total_surveys'           => $results->count(),
            'avg_focus_score'         => round($results->avg('focus_score'), 2),
            'avg_productivity_score'  => round($results->avg('productivity_score'), 2),
            'avg_digital_dependence'  => round($results->avg('digital_dependence_score'), 2),
            'high_risk_count'         => $results->where('high_risk_flag', true)->count(),
            'trend'                   => $results->map(fn($r) => [
                'date'               => $r->created_at->format('Y-m-d'),
                'focus'              => $r->focus_score,
                'productivity'       => $r->productivity_score,
                'digital_dependence' => $r->digital_dependence_score,
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
            'focus_score'             => $result->focus_score,
            'productivity_score'      => $result->productivity_score,
            'digital_dependence_score'=> $result->digital_dependence_score,
            'high_risk_flag'          => $result->high_risk_flag,
            'risk_level'              => $this->getRiskLabel($result->digital_dependence_score, $result->high_risk_flag),
            'recommendations'         => $result->recommendations,
            'questionnaire'           => $result->questionnaire,
            'created_at'              => $result->created_at,
        ];
    }

    private function getRiskLabel(float $score, bool $highRisk): string
    {
        if ($highRisk || $score >= 75) return 'Tinggi';
        if ($score >= 50)              return 'Sedang';
        return 'Rendah';
    }
}