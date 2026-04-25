{{--
  ═══════════════════════════════════════════════
  resources/views/admin/dashboard.blade.php
  ═══════════════════════════════════════════════
--}}
@extends('layouts.app')
@section('title','Dashboard — Activa')
@section('page-title','Dashboard')

@push('head-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════
     DATA BRIDGE — semua variabel PHP → JSON → dibaca dashboard.js
     ══════════════════════════════════════════════════════════ --}}
<div id="chart-data"
     data-risk-dist='@json($riskDist ?? [])'
     data-score-trend='@json($scoreTrend ?? [])'
     data-grouped-bar='@json($groupedBar ?? [])'
     data-daily-submissions='@json($dailySubmissions ?? [])'
     data-score-histogram='@json($scoreHistogram ?? [])'
     hidden>
</div>

{{-- ══════════════════════════════════════════════════════════
     STATS GRID
     ══════════════════════════════════════════════════════════ --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="top-line top-line--navy"></div>
        <div class="stat-icon stat-icon--navy">👥</div>
        <div class="stat-label">Total User Aktif</div>
        <div class="stat-val stat-val--navy">{{ number_format($stats['total_users'] ?? 0) }}</div>
        <div class="stat-change up">↑ 12% bulan ini</div>
    </div>

    <div class="stat-card">
        <div class="top-line top-line--teal"></div>
        <div class="stat-icon stat-icon--teal">🎯</div>
        <div class="stat-label">Avg. Skor Ketergantungan</div>
        <div class="stat-val stat-val--teal">{{ $stats['avg_score'] ?? 0 }}</div>
        <div class="stat-change up">↑ 2.1 minggu ini</div>
    </div>

    <div class="stat-card">
        <div class="top-line top-line--amber"></div>
        <div class="stat-icon stat-icon--amber">📱</div>
        <div class="stat-label">Avg. Screen Time</div>
        <div class="stat-val stat-val--amber">
            {{ $stats['avg_screen'] ?? 0 }}<span class="stat-val-unit"> jam</span>
        </div>
        <div class="stat-change down">↑ 0.3j dari kemarin</div>
    </div>

    <div class="stat-card">
        <div class="top-line top-line--red"></div>
        <div class="stat-icon stat-icon--red">⚠️</div>
        <div class="stat-label">High Risk Users</div>
        <div class="stat-val stat-val--red">{{ $stats['high_risk'] ?? 0 }}</div>
        <div class="stat-change down">Perlu perhatian</div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════
     7. THRESHOLD INFO
     ══════════════════════════════════════════════════════════ --}}
<div class="section-header">
    <h2 class="section-title">Klasifikasi Skor Ketergantungan Digital</h2>
    <p class="section-sub">Rule-based threshold yang digunakan sistem untuk mengklasifikasikan pengguna</p>
</div>
<div class="threshold-grid">
    @foreach($thresholds ?? [] as $t)
    <div class="threshold-card threshold-card--{{ $t['color'] }}">
        <div class="threshold-icon">{{ $t['icon'] }}</div>
        <div class="threshold-label">{{ $t['label'] }}</div>
        <div class="threshold-range">{{ $t['range'] }}</div>
        <div class="threshold-desc">
            @if($t['color'] === 'teal') Penggunaan digital dalam batas wajar
            @elseif($t['color'] === 'amber') Perlu pemantauan & intervensi ringan
            @else Butuh perhatian & intervensi segera
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════════════════
     6. SUMMARY INSIGHTS
     ══════════════════════════════════════════════════════════ --}}
<div class="section-header">
    <h2 class="section-title">📋 Summary Insight</h2>
    <p class="section-sub">Ringkasan otomatis berdasarkan data terkini</p>
</div>
<div class="insights-grid">
    @foreach($insights ?? [] as $ins)
    <div class="insight-card insight-card--{{ $ins['color'] }}">
        <span class="insight-icon">{{ $ins['icon'] }}</span>
        <p class="insight-text">{!! $ins['text'] !!}</p>
    </div>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 1: Donut + Skor Trend
     ══════════════════════════════════════════════════════════ --}}
<div class="grid-2-1 mb-charts">

    {{-- 2. Rata-rata Skor Trend --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Tren Rata-rata Skor Ketergantungan</div>
                <div class="card-sub">Pantau apakah kondisi pengguna membaik atau memburuk</div>
            </div>
            <div class="tab-group" id="scoreTrendTabs">
                <button class="tab-btn active" data-period="daily">Harian</button>
                <button class="tab-btn" data-period="weekly">Mingguan</button>
                <button class="tab-btn" data-period="monthly">Bulanan</button>
            </div>
        </div>
        <canvas id="scoreTrendChart"></canvas>
    </div>

    {{-- 1. Distribusi Risk Level --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Distribusi Risk Level</div>
                <div class="card-sub">Berdasarkan tingkat ketergantungan digital</div>
            </div>
        </div>
        <canvas id="donutChart" class="donut-canvas"></canvas>

        <div class="risk-legend">
            @foreach($riskDist ?? [] as $item)
            <div class="risk-legend-row">
                <span class="risk-legend-label">
                    <span class="risk-legend-dot dot-{{ $item['color'] }}"></span>
                    {{ $item['label'] }}
                </span>
                <span class="risk-legend-value value-{{ $item['color'] }}">
                    {{ $item['pct'] }}% · {{ number_format($item['count']) }} user
                </span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 2: Grouped Bar + Daily Submissions
     ══════════════════════════════════════════════════════════ --}}
<div class="grid-2 mb-charts">

    {{-- 3. Grouped Bar Chart --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Perbandingan Berdasarkan Kategori</div>
                <div class="card-sub">Distribusi risk level antar kelompok pengguna</div>
            </div>
            <div class="tab-group" id="groupedBarTabs">
                <button class="tab-btn active" data-group="byRole">Peran</button>
                <button class="tab-btn" data-group="byGender">Gender</button>
            </div>
        </div>
        <canvas id="groupedBarChart"></canvas>
    </div>

    {{-- 4. Daily Submissions --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Kuesioner Terisi per Hari</div>
                <div class="card-sub">Pantau tren partisipasi pengguna 7 hari terakhir</div>
            </div>
        </div>
        <canvas id="submissionsChart"></canvas>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 3: Score Histogram (full width)
     ══════════════════════════════════════════════════════════ --}}
<div class="card mb-charts">
    <div class="card-header">
        <div>
            <div class="card-title">Distribusi Skor Pengguna</div>
            <div class="card-sub">Histogram untuk evaluasi distribusi data — apakah normal, skewed, atau multimodal</div>
        </div>
        <div class="histogram-legend">
            <span class="hist-badge hist-badge--teal">Rendah (0–39)</span>
            <span class="hist-badge hist-badge--amber">Sedang (40–69)</span>
            <span class="hist-badge hist-badge--red">Tinggi (70–100)</span>
        </div>
    </div>
    <canvas id="histogramChart" style="max-height: 260px;"></canvas>
</div>

{{-- ══════════════════════════════════════════════════════════
     ROW 4: Recent Users + Activity Log
     ══════════════════════════════════════════════════════════ --}}
<div class="grid-2">

    {{-- Recent users --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">User Terbaru</div>
                <div class="card-sub">Baru bergabung hari ini</div>
            </div>
            <a href="/users" class="card-action">Lihat semua →</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Skor</th>
                    <th>Risk Level</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers ?? [] as $user)
                <tr>
                    <td>
                        <div class="user-name">{{ $user['name'] }}</div>
                        <div class="user-email">{{ $user['email'] }}</div>
                    </td>
                    <td>
                        @php
                            $scoreClass = $user['focus_score'] >= 70
                                ? 'focus-low'
                                : ($user['focus_score'] >= 40 ? 'focus-medium' : 'focus-high');
                        @endphp
                        <span class="focus-score {{ $scoreClass }}">{{ $user['focus_score'] }}</span>
                    </td>
                    <td>
                        @php
                            $pillClass = match($user['risk']) {
                                'low'    => 'pill-teal',
                                'high'   => 'pill-red',
                                default  => 'pill-amber',
                            };
                            $pillLabel = match($user['risk']) {
                                'low'    => 'Rendah',
                                'high'   => 'Tinggi',
                                default  => 'Sedang',
                            };
                        @endphp
                        <span class="pill {{ $pillClass }}">{{ $pillLabel }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="td-empty">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Activity log --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Log Aktivitas</div>
                <div class="card-sub">Aktivitas sistem terkini</div>
            </div>
        </div>

        @foreach($activityLogs ?? [] as $log)
        <div class="activity-item">
            <div class="activity-icon">{{ $log['icon'] }}</div>
            <div>
                <div class="activity-text">{{ $log['text'] }}</div>
                <div class="activity-time">{{ $log['time'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush