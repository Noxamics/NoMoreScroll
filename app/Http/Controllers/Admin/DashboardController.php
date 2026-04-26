<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stats ──────────────────────────────────────────────
        $stats = [
            'total_users'  => User::count(),
            'total_admins' => Admin::count(),
            'avg_focus'    => 7.8,
            'avg_screen'   => 4.2,
            'high_risk'    => 12,
        ];

        // ── 1. Distribusi Risk Level (Donut Chart) ─────────────
        $riskDist = [
            ['label' => 'Rendah', 'pct' => 35, 'count' => 45, 'color' => 'teal'],
            ['label' => 'Sedang', 'pct' => 52, 'count' => 66, 'color' => 'amber'],
            ['label' => 'Tinggi', 'pct' => 13, 'count' => 17, 'color' => 'red'],
        ];

        // ── 2. Rata-rata Skor per Hari/Minggu/Bulan (Line Chart) ─
        $scoreTrend = [
            'daily' => [
                'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'data'   => [52, 58, 55, 63, 60, 67, 64],
            ],
            'weekly' => [
                'labels' => ['Mg 1', 'Mg 2', 'Mg 3', 'Mg 4'],
                'data'   => [54, 57, 61, 65],
            ],
            'monthly' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                'data'   => [48, 52, 55, 59, 62, 65],
            ],
        ];

        // ── 3. Perbandingan Kategori (Grouped Bar Chart) ───────
        $groupedBar = [
            'byRole' => [
                'labels' => ['Mahasiswa', 'Pekerja', 'Lainnya'],
                'rendah' => [30, 42, 28],
                'sedang' => [50, 43, 52],
                'tinggi' => [20, 15, 20],
            ],
            'byGender' => [
                'labels' => ['Laki-laki', 'Perempuan'],
                'rendah' => [30, 40],
                'sedang' => [50, 54],
                'tinggi' => [20, 6],
            ],
            'byAge' => [
                'labels' => ['< 18', '18–24', '25–34', '35–44', '45+'],
                'rendah' => [20, 28, 38, 45, 50],
                'sedang' => [55, 52, 48, 42, 38],
                'tinggi' => [25, 20, 14, 13, 12],
            ],
            'byRegion' => [
                'labels' => ['Jawa', 'Sumatera', 'Kalimantan', 'Sulawesi', 'Lainnya'],
                'rendah' => [33, 36, 40, 38, 42],
                'sedang' => [52, 50, 48, 50, 46],
                'tinggi' => [15, 14, 12, 12, 12],
            ],
        ];

        // ── 4. Kuesioner Terisi per Hari (Line Chart) ──────────
        $dailySubmissions = [
            'labels' => ['19 Apr', '20 Apr', '21 Apr', '22 Apr', '23 Apr', '24 Apr', '25 Apr'],
            'data'   => [8, 14, 9, 18, 22, 20, 27],
        ];

        // ── 5. Distribusi Skor (Histogram) ─────────────────────
        $scoreHistogram = [
            'labels' => ['0–9', '10–19', '20–29', '30–39', '40–49', '50–59', '60–69', '70–79', '80–89', '90–100'],
            'data'   => [2, 4, 8, 14, 22, 30, 24, 16, 10, 6],
        ];

        // ── 6. Summary Insight ─────────────────────────────────
        $summaryInsights = [
            ['icon' => '📊', 'type' => 'info',    'text' => 'Mayoritas user berada di kategori Sedang (52%)'],
            ['icon' => '📈', 'type' => 'up',      'text' => 'Rata-rata skor meningkat +5 poin minggu ini'],
            ['icon' => '⚠️', 'type' => 'warning', 'text' => 'Penggunaan device > 6 jam/hari berkorelasi dengan skor tinggi'],
            ['icon' => '🔴', 'type' => 'danger',  'text' => '12 user masuk kategori High Risk, perlu perhatian segera'],
        ];

        // ── 7. Threshold ───────────────────────────────────────
        $thresholds = [
            ['label' => 'Rendah', 'range' => '0 – 39',   'color' => 'teal',  'desc' => 'Ketergantungan digital masih dalam batas normal'],
            ['label' => 'Sedang', 'range' => '40 – 69',  'color' => 'amber', 'desc' => 'Mulai menunjukkan pola penggunaan yang berlebihan'],
            ['label' => 'Tinggi', 'range' => '70 – 100', 'color' => 'red',   'desc' => 'Ketergantungan digital sudah pada level mengkhawatirkan'],
        ];

        return view('admin.dashboard', compact(
            'stats',
            'riskDist',
            'scoreTrend',
            'groupedBar',
            'dailySubmissions',
            'scoreHistogram',
            'summaryInsights',
            'thresholds',
        ));
    }
}