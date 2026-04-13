<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsLog;
use App\Models\MlResult;
use App\Models\Questionnaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * AnalyticsController
 * ─────────────────────────────────────────────────────
 * Endpoint insight & analitik untuk:
 *  - Dashboard Flutter (insight page)
 *  - Dashboard Web (Member 3 — grafik Chart.js)
 *  - Admin panel (Member 3)
 */
class AnalyticsController extends Controller
{
    /**
     * GET /api/analytics/insight
     * Insight personal user: perubahan fokus 7 hari, risk trend
     * → Digunakan di Flutter "Insight page" (Member 4)
     */
    public function insight(): JsonResponse
    {
        $userId = auth()->id();

        // Ambil hasil ML 7 hari terakhir
        $last7 = MlResult::whereHas('questionnaire', fn($q) => $q->where('user_id', $userId))
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'asc')
            ->get(['focus_score', 'productivity_score', 'digital_dependence_score', 'high_risk_flag', 'created_at']);

        // Ambil hasil ML 7 hari sebelumnya (untuk perbandingan)
        $prev7 = MlResult::whereHas('questionnaire', fn($q) => $q->where('user_id', $userId))
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->get(['focus_score', 'productivity_score', 'digital_dependence_score']);

        $avgFocusCurrent  = $last7->avg('focus_score') ?? 0;
        $avgFocusPrev     = $prev7->avg('focus_score') ?? 0;
        $avgProdCurrent   = $last7->avg('productivity_score') ?? 0;
        $avgDepCurrent    = $last7->avg('digital_dependence_score') ?? 0;

        $focusChange = $avgFocusPrev > 0
            ? round((($avgFocusCurrent - $avgFocusPrev) / $avgFocusPrev) * 100, 1)
            : 0;

        // Simpan ke analytics_logs
        AnalyticsLog::create([
            'user_id'                => $userId,
            'avg_focus_7_days'       => round($avgFocusCurrent, 2),
            'focus_change_percentage'=> $focusChange,
            'avg_productivity_7_days'=> round($avgProdCurrent, 2),
            'created_at'             => now(),
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'period'                  => '7 hari terakhir',
                'avg_focus_score'         => round($avgFocusCurrent, 2),
                'avg_productivity_score'  => round($avgProdCurrent, 2),
                'avg_digital_dependence'  => round($avgDepCurrent, 2),
                'focus_change_percentage' => $focusChange,        // negatif = turun
                'focus_change_label'      => $this->changeLabel($focusChange),
                'high_risk_days'          => $last7->where('high_risk_flag', true)->count(),
                'total_surveys_week'      => $last7->count(),
                'daily_trend'             => $last7->map(fn($r) => [
                    'date'               => Carbon::parse($r->created_at)->format('Y-m-d'),
                    'focus'              => $r->focus_score,
                    'productivity'       => $r->productivity_score,
                    'digital_dependence' => $r->digital_dependence_score,
                    'high_risk'          => $r->high_risk_flag,
                ]),
            ],
        ]);
    }

    /**
     * GET /api/analytics/comparison
     * Perbandingan user dengan rata-rata semua user (anonymized)
     * → Untuk insight page Flutter & web
     */
    public function comparison(): JsonResponse
    {
        $userId = auth()->id();

        // Data user sendiri (terbaru)
        $myLatest = MlResult::whereHas('questionnaire', fn($q) => $q->where('user_id', $userId))
            ->orderBy('created_at', 'desc')
            ->first(['focus_score', 'productivity_score', 'digital_dependence_score']);

        // Rata-rata global (semua user, 30 hari terakhir)
        $globalAvg = MlResult::where('created_at', '>=', now()->subDays(30))->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'my_scores' => $myLatest ? [
                    'focus'              => $myLatest->focus_score,
                    'productivity'       => $myLatest->productivity_score,
                    'digital_dependence' => $myLatest->digital_dependence_score,
                ] : null,
                'global_avg' => [
                    'focus'              => round($globalAvg->avg('focus_score'), 2),
                    'productivity'       => round($globalAvg->avg('productivity_score'), 2),
                    'digital_dependence' => round($globalAvg->avg('digital_dependence_score'), 2),
                    'sample_size'        => $globalAvg->count(),
                ],
            ],
        ]);
    }

    /**
     * GET /api/analytics/history?days=30
     * History lengkap user untuk grafik di web/Flutter
     */
    public function history(Request $request): JsonResponse
    {
        $days = min((int) $request->get('days', 30), 90); // max 90 hari

        $results = MlResult::whereHas('questionnaire', fn($q) => $q->where('user_id', auth()->id()))
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'asc')
            ->get(['focus_score', 'productivity_score', 'digital_dependence_score', 'high_risk_flag', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => [
                'days'    => $days,
                'records' => $results->map(fn($r) => [
                    'date'               => Carbon::parse($r->created_at)->format('Y-m-d'),
                    'focus'              => $r->focus_score,
                    'productivity'       => $r->productivity_score,
                    'digital_dependence' => $r->digital_dependence_score,
                    'high_risk'          => $r->high_risk_flag,
                ]),
            ],
        ]);
    }

    // ─── Private Helpers ─────────────────────────────────────

    private function changeLabel(float $pct): string
    {
        if ($pct > 10)  return 'Meningkat signifikan';
        if ($pct > 0)   return 'Sedikit meningkat';
        if ($pct === 0) return 'Tidak berubah';
        if ($pct > -10) return 'Sedikit menurun';
        return 'Menurun signifikan';
    }
}