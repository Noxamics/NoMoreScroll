{{--
═══════════════════════════════════════════════
resources/views/admin/monitoring.blade.php
═══════════════════════════════════════════════
--}}
@extends('layouts.app')
@section('title', 'Monitoring ML — Activa')
@section('page-title', 'Monitoring ML')

@section('topbar-actions')
  <span class="pill pill-teal">Model v2.1 Aktif</span>
@endsection

@push('head-scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/monitoring.css') }}">
@endpush

@section('content')

  {{-- STAT CARDS --}}
  <div class="stats-grid">
    <div class="stat-card">
      <div class="top-line top-line--teal"></div>
      <div class="stat-icon stat-icon--teal">🎯</div>
      <div class="stat-label">Avg. Focus Score</div>
      <div class="stat-val stat-val--teal">{{ $metrics['avg_focus'] ?? 0 }}</div>
      <div class="stat-change up">↑ 3.2 dari bulan lalu</div>
    </div>
    <div class="stat-card">
      <div class="top-line top-line--navy"></div>
      <div class="stat-icon stat-icon--navy">⚡</div>
      <div class="stat-label">Avg. Productivity</div>
      <div class="stat-val stat-val--navy">{{ $metrics['avg_productivity'] ?? 0 }}</div>
      <div class="stat-change up">↑ 1.8 dari bulan lalu</div>
    </div>
    <div class="stat-card">
      <div class="top-line top-line--violet"></div>
      <div class="stat-icon stat-icon--violet">🔗</div>
      <div class="stat-label">Avg. Digital Dep.</div>
      <div class="stat-val stat-val--violet">{{ $metrics['avg_dependence'] ?? 0 }}</div>
      <div class="stat-change up">↓ 2.1 dari bulan lalu</div>
    </div>
    <div class="stat-card">
      <div class="top-line top-line--amber"></div>
      <div class="stat-icon stat-icon--amber">📱</div>
      <div class="stat-label">Avg. Screen Time</div>
      <div class="stat-val stat-val--amber">
        {{ $metrics['avg_screen'] ?? 0 }}<span class="stat-val__unit"> jam</span>
      </div>
      <div class="stat-change down">↑ 0.3j dari bulan lalu</div>
    </div>
  </div>

  {{-- CHARTS ROW 1 --}}
  <div class="grid-2 mb-4">
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Distribusi Screen Time</div>
          <div class="card-sub">Jumlah user per rentang jam/hari</div>
        </div>
      </div>
      <canvas id="c1"></canvas>
    </div>
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Profil: Low vs High Risk</div>
          <div class="card-sub">Perbandingan 6 dimensi wellness</div>
        </div>
      </div>
      <canvas id="c2"></canvas>
    </div>
  </div>

  {{-- TREND CHART --}}
  <div class="card mb-4">
    <div class="card-header">
      <div>
        <div class="card-title">Tren Bulanan Semua Metrik</div>
        <div class="card-sub">6 bulan terakhir</div>
      </div>
      <span class="pill pill-navy">{{ $modelAccuracy ?? 87 }}% Akurasi Model</span>
    </div>
    <canvas id="c3"></canvas>
  </div>

  {{-- BOTTOM GRID --}}
  <div class="grid-2">

    {{-- TOP FOCUS --}}
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Top Focus Score</div>
          <div class="card-sub">5 user dengan skor terbaik</div>
        </div>
        <a href="/users" class="card-action">Lihat semua →</a>
      </div>
      @foreach($topUsers ?? [] as $i => $u)
        <div class="user-row {{ !$loop->last ? 'user-row--border' : '' }}">
          <div class="user-row__rank">#{{ $i + 1 }}</div>
          <div class="avatar avatar--teal">{{ strtoupper(substr($u['name'], 0, 2)) }}</div>
          <div class="user-row__name">{{ $u['name'] }}</div>
          <div class="user-row__score score--teal">{{ $u['focus_score'] }}</div>
          {{-- FIX: gunakan data-width, set style via JS untuk hindari CSS false-positive VS Code --}}
          <div class="score-bar">
            <div class="score-bar__fill score-bar__fill--teal" data-width="{{ $u['focus_score'] }}"></div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- HIGH RISK --}}
    <div class="card">
      <div class="card-header">
        <div>
          <div class="card-title">Perlu Perhatian 🚨</div>
          <div class="card-sub">Digital dependence tertinggi</div>
        </div>
        <a href="/users?risk=high" class="card-action">Kelola →</a>
      </div>
      @foreach($riskUsers ?? [] as $i => $u)
        @php $isHighRisk = $u['digital_dep'] > 66; @endphp
        <div class="user-row {{ !$loop->last ? 'user-row--border' : '' }}">
          <div class="user-row__rank">#{{ $i + 1 }}</div>
          <div class="avatar {{ $isHighRisk ? 'avatar--red' : 'avatar--amber' }}">
            {{ strtoupper(substr($u['name'], 0, 2)) }}
          </div>
          <div class="user-row__name">{{ $u['name'] }}</div>
          <div class="user-row__score {{ $isHighRisk ? 'score--red' : 'score--amber' }}">
            {{ $u['digital_dep'] }}
          </div>
          {{-- FIX: gunakan data-width, set style via JS untuk hindari CSS false-positive VS Code --}}
          <div class="score-bar">
            <div class="score-bar__fill {{ $isHighRisk ? 'score-bar__fill--red' : 'score-bar__fill--amber' }}"
              data-width="{{ $u['digital_dep'] }}"></div>
          </div>
        </div>
      @endforeach
    </div>

  </div>

  {{-- Data bridge for JS --}}
  <div id="chart-data" data-screen-dist="{{ json_encode($screenDistribution ?? ['labels' => [], 'data' => [], 'colors' => []]) }}"
    data-radar="{{ json_encode($radarData ?? []) }}"
    data-trend="{{ json_encode($monthlyTrend ?? ['labels' => [], 'focus' => [], 'productivity' => [], 'dependence' => []]) }}"
    hidden></div>

@endsection

@push('scripts')
  <script src="{{ asset('js/monitoring.js') }}"></script>
@endpush