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

{{-- ── Stats grid ──────────────────────────────────────────── --}}
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
        <div class="stat-label">Avg. Focus Score</div>
        <div class="stat-val stat-val--teal">{{ $stats['avg_focus'] ?? 0 }}</div>
        <div class="stat-change up">↑ 3.2 minggu ini</div>
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

{{--
    Data bridge: variabel PHP di-encode ke HTML attribute.
    dashboard.js membaca kedua attribute ini saat DOMContentLoaded.
--}}
<div id="chart-data"
     data-trend='@json($chartTrend ?? ["labels"=>[],"focus"=>[],"screen"=>[]])'
     data-dist='@json($riskDist ?? [])'
     hidden>
</div>

{{-- ── Charts row ──────────────────────────────────────────── --}}
<div class="grid-2-1 mb-charts">

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Tren Mingguan</div>
                <div class="card-sub">Focus score &amp; screen time 7 hari terakhir</div>
            </div>
        </div>
        <canvas id="trendChart"></canvas>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Distribusi Risk Level</div>
                <div class="card-sub">Berdasarkan digital dependence</div>
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
                    {{ $item['pct'] }}% · {{ $item['count'] }} user
                </span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ── Bottom row ──────────────────────────────────────────── --}}
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
                    <th>Focus Score</th>
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
                            $focusClass = $user['focus_score'] >= 70
                                ? 'focus-high'
                                : ($user['focus_score'] >= 50 ? 'focus-medium' : 'focus-low');
                        @endphp
                        <span class="focus-score {{ $focusClass }}">{{ $user['focus_score'] }}</span>
                    </td>
                    <td>
                        @php
                            $pillClass = match($user['risk']) {
                                'low'   => 'pill-teal',
                                'high'  => 'pill-red',
                                default => 'pill-amber',
                            };
                            $pillLabel = match($user['risk']) {
                                'low'   => 'Low Risk',
                                'high'  => 'High Risk',
                                default => 'Moderate',
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