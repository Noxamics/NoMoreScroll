{{--
  ═══════════════════════════════════════════════
  resources/views/admin/users.blade.php
  ═══════════════════════════════════════════════
--}}
@extends('layouts.app')
@section('title','User Management — Activa')
@section('page-title','User Management')

@section('topbar-actions')
  <span class="topbar-badge topbar-badge--navy">
    {{ number_format($total ?? 0) }} Users
  </span>
@endsection

@section('content')

{{-- STAT CARDS --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="top-line top-line--navy"></div>
    <div class="stat-icon stat-icon--navy">👥</div>
    <div class="stat-label">Total User</div>
    <div class="stat-val stat-val--navy">{{ number_format($stats['total'] ?? 0) }}</div>
    <div class="stat-change neu">Semua terdaftar</div>
  </div>
  <div class="stat-card">
    <div class="top-line top-line--teal"></div>
    <div class="stat-icon stat-icon--teal">✅</div>
    <div class="stat-label">Aktif Hari Ini</div>
    <div class="stat-val stat-val--teal">{{ number_format($stats['active_today'] ?? 0) }}</div>
    <div class="stat-change up">↑ dari kemarin</div>
  </div>
  <div class="stat-card">
    <div class="top-line top-line--amber"></div>
    <div class="stat-icon stat-icon--amber">🆕</div>
    <div class="stat-label">User Baru (7 Hari)</div>
    <div class="stat-val stat-val--amber">{{ number_format($stats['new_7d'] ?? 0) }}</div>
    <div class="stat-change up">↑ minggu ini</div>
  </div>
  <div class="stat-card">
    <div class="top-line top-line--red"></div>
    <div class="stat-icon stat-icon--red">⚠️</div>
    <div class="stat-label">High Risk</div>
    <div class="stat-val stat-val--red">{{ number_format($stats['high_risk'] ?? 0) }}</div>
    <div class="stat-change down">Perlu perhatian</div>
  </div>
</div>

{{-- FILTER TOOLBAR --}}
<form method="GET" action="{{ route('admin.users') }}" class="filter-bar">
  <div class="filter-bar__search">
    <span class="filter-bar__icon">⌕</span>
    <input class="inp inp--search" name="search"
           placeholder="Cari nama atau email..."
           value="{{ request('search') }}">
  </div>
  <select class="inp" name="risk" onchange="this.form.submit()">
    <option value="">Semua Level Risk</option>
    <option value="low"  {{ request('risk')==='low'  ? 'selected' : '' }}>✅ Low Risk</option>
    <option value="mid"  {{ request('risk')==='mid'  ? 'selected' : '' }}>⚠️ Moderate</option>
    <option value="high" {{ request('risk')==='high' ? 'selected' : '' }}>🔴 High Risk</option>
  </select>
  <select class="inp" name="sort" onchange="this.form.submit()">
    <option value="latest"     {{ request('sort')==='latest'     ? 'selected' : '' }}>Urutkan: Terbaru</option>
    <option value="focus_desc" {{ request('sort')==='focus_desc' ? 'selected' : '' }}>Focus Score ↓</option>
    <option value="focus_asc"  {{ request('sort')==='focus_asc'  ? 'selected' : '' }}>Focus Score ↑</option>
    <option value="screen_desc"{{ request('sort')==='screen_desc'? 'selected' : '' }}>Screen Time ↓</option>
  </select>
  <button type="submit" class="btn btn-navy btn-sm">Cari</button>
  @if(request()->hasAny(['search','risk','sort']))
    <a href="/users" class="btn btn-ghost btn-sm">Reset</a>
  @endif
</form>

{{-- TABLE --}}
<div class="card">
  <div class="card-header">
    <div>
      <div class="card-title">Daftar Pengguna</div>
      <div class="card-sub">Menampilkan {{ $users->count() }} dari {{ number_format($users->total()) }} pengguna</div>
    </div>
  </div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Pengguna</th>
          <th>Bergabung</th>
          <th>Focus Score</th>
          <th>Screen Time</th>
          <th>Risk Level</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        @php
          $fcClass   = $user['focus_score'] >= 70 ? 'score--teal'  : ($user['focus_score'] >= 50 ? 'score--amber'  : 'score--red');
          $barClass  = $user['focus_score'] >= 70 ? 'score-bar__fill--teal' : ($user['focus_score'] >= 50 ? 'score-bar__fill--amber' : 'score-bar__fill--red');
          $riskClass = $user['risk'] === 'low'  ? 'pill-teal'  : ($user['risk'] === 'high' ? 'pill-red'  : 'pill-amber');
          $riskLabel = $user['risk'] === 'low'  ? 'Low Risk'   : ($user['risk'] === 'high' ? 'High Risk' : 'Moderate');
        @endphp
        <tr>
          <td>
            <div class="user-cell">
              <div class="avatar avatar--teal">{{ strtoupper(substr($user['name'], 0, 2)) }}</div>
              <div>
                <div class="user-cell__name">{{ $user['name'] }}</div>
                <div class="user-cell__email">{{ $user['email'] }}</div>
              </div>
            </div>
          </td>
          <td class="cell--muted cell--sm">{{ $user['created_at'] ?? '-' }}</td>
          <td>
            <div class="score-bar-wrap">
              <span class="user-row__score {{ $fcClass }}">{{ $user['focus_score'] }}</span>
              {{-- FIX: gunakan data-width, set style via JS untuk hindari CSS false-positive VS Code --}}
              <div class="score-bar">
                <div class="score-bar__fill {{ $barClass }}"
                     data-width="{{ $user['focus_score'] }}"></div>
              </div>
            </div>
          </td>
          <td class="cell--body">{{ $user['screen_time'] }}j</td>
          <td><span class="pill {{ $riskClass }}">{{ $riskLabel }}</span></td>
          <td>
            <div class="tbl-actions">
              {{-- Gunakan individual data attributes untuk avoid JSON parsing issues --}}
              <button class="btn btn-ghost btn-sm"
                      data-id="{{ $user['id'] }}"
                      data-name="{{ $user['name'] }}"
                      data-email="{{ $user['email'] }}"
                      data-focus="{{ $user['focus_score'] ?? 0 }}"
                      data-screen="{{ $user['screen_time'] ?? 0 }}"
                      data-productivity="{{ $user['productivity'] ?? 0 }}"
                      data-dependency="{{ $user['digital_dependency'] ?? 0 }}"
                      data-risk="{{ $user['risk'] ?? 'low' }}"
                      data-joined="{{ $user['created_at'] ?? '' }}"
                      onclick="openModal(this)">Detail</button>
              <form method="POST"
                action="{{ route('admin.users.destroy', $user['id']) }}"
                data-name="{{ htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') }}"
                onsubmit="return confirm('Hapus ' + this.dataset.name + '?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="tbl-empty">Tidak ada data pengguna</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if(isset($users) && method_exists($users, 'links'))
  <div class="tbl-pagination">
    {{ $users->links() }}
  </div>
  @endif
</div>

{{-- MODAL DETAIL --}}
<div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">Detail Pengguna</div>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>

    <div class="user-modal-head">
      <div class="avatar avatar--teal avatar--lg" id="m-av">??</div>
      <div>
        <div class="user-modal-name" id="m-nm">—</div>
        <div class="user-modal-email" id="m-em">—</div>
        <div class="user-modal-joined" id="m-jn">—</div>
      </div>
    </div>

    <div class="modal-metric-grid">
      <div class="modal-metric">
        <div class="modal-metric__label">Focus Score</div>
        <div class="modal-metric__val modal-metric__val--teal" id="m-f">—</div>
        <div class="modal-metric__unit">dari 100</div>
      </div>
      <div class="modal-metric">
        <div class="modal-metric__label">Screen Time</div>
        <div class="modal-metric__val modal-metric__val--amber" id="m-s">—</div>
        <div class="modal-metric__unit">per hari</div>
      </div>
      <div class="modal-metric">
        <div class="modal-metric__label">Productivity</div>
        <div class="modal-metric__val modal-metric__val--navy" id="m-p">—</div>
        <div class="modal-metric__unit">dari 100</div>
      </div>
      <div class="modal-metric">
        <div class="modal-metric__label">Digital Dep.</div>
        <div class="modal-metric__val modal-metric__val--violet" id="m-d">—</div>
        <div class="modal-metric__unit">dari 100</div>
      </div>
    </div>

    <div class="modal-risk-row">
      <span class="modal-risk-row__label">Risk Level:</span>
      <span id="m-risk" class="pill pill-teal">—</span>
    </div>

    <div class="modal-footer-btns">
      <button class="btn btn-ghost">📊 Lihat Riwayat</button>
      <button class="btn btn-danger" id="m-del-btn">🗑 Hapus User</button>
    </div>
  </div>
</div>

{{-- Route bridge for JS --}}
<div id="route-data"
     data-destroy-base="/admin/users"
     data-csrf="{{ csrf_token() }}"
     hidden></div>

@endsection

@push('scripts')
<script src="{{ asset('js/users.js') }}"></script>
@endpush