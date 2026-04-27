{{--
═══════════════════════════════════════════════
resources/views/admin/kuesioner.blade.php
Halaman Data Kuesioner — Activa Admin
═══════════════════════════════════════════════
--}}
@extends('layouts.app')
@section('title', 'Data Kuesioner — Activa')
@section('page-title', 'Data Kuesioner')

@section('topbar-actions')
  <button onclick="refreshTable()" id="btn-refresh" style="
          display:inline-flex;align-items:center;gap:6px;
          padding:8px 15px;border-radius:9px;font-size:12px;font-weight:500;
          background:#fff;color:#4A6180;border:1px solid #C8D8EA;
          cursor:pointer;font-family:var(--sans);transition:all .15s;
        " onmouseover="this.style.background='#F0F9FF';this.style.color='#1E3A5F'"
    onmouseout="this.style.background='#fff';this.style.color='#4A6180'">
    <span id="refresh-icon" style="font-size:15px;line-height:1;display:inline-flex">↻</span>
    Refresh Data
  </button>
@endsection

@push('scripts')
  <script>
    function refreshTable() {
      const icon = document.getElementById('refresh-icon');
      const btn = document.getElementById('btn-refresh');
      btn.disabled = true;
      icon.style.transition = 'transform .6s linear';
      icon.style.transform = 'rotate(360deg)';

      setTimeout(() => {
        icon.style.transition = 'none';
        icon.style.transform = 'rotate(0deg)';
        btn.disabled = false;
        // reload data tanpa full-page refresh:
        loadKuesionerData();
      }, 600);
    }
  </script>
@endpush

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/kuesioner.css') }}">
@endpush

@section('content')

  {{-- INFO BANNER --}}
  <div class="alert alert-navy mb-5">
    <div class="alert__icon">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
      </svg>
    </div>
    <div>
      <div class="alert__title">Data Respons Kuesioner User</div>
      <div class="alert__body">Menampilkan semua respons kuesioner yang telah diisi oleh pengguna.
        Data ini digunakan untuk analisis fokus dan produktivitas.</div>
    </div>
  </div>

  {{-- TABLE CARD --}}
  <div class="table-wrap">

    {{-- TABLE HEADER --}}
    <div class="table-topbar">
      <div class="table-topbar__left">
        <div class="table-topbar__icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <polyline points="14 2 14 8 20 8" />
            <line x1="16" y1="13" x2="8" y2="13" />
            <line x1="16" y1="17" x2="8" y2="17" />
            <polyline points="10 9 9 9 8 9" />
          </svg>
        </div>
        <div>
          <div class="table-topbar__title">Semua Kuesioner</div>
          <div class="table-topbar__sub">{{ $kuesioner->total() ?? 0 }} respons ditemukan</div>
        </div>
      </div>
      <div class="table-topbar__right">
        <span class="pill pill-teal">
          <svg width="7" height="7" viewBox="0 0 7 7">
            <circle cx="3.5" cy="3.5" r="3.5" fill="currentColor" />
          </svg>
          Aktif
        </span>
      </div>
    </div>

    <div class="table-container">
      @if($kuesioner->count() > 0)
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>User</th>
              <th>Tanggal</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($kuesioner as $item)
              @php
                $user = \App\Models\User::find($item->user_id);
                $initials = strtoupper(substr($user->name ?? 'NA', 0, 2));
                $avatarColors = ['teal', 'navy', 'violet', 'amber'];
                $color = $avatarColors[$loop->index % count($avatarColors)];
              @endphp
              <tr>
                <td class="td-num">
                  {{ $loop->iteration + ($kuesioner->perPage() * ($kuesioner->currentPage() - 1)) }}
                </td>
                <td>
                  <div class="user-cell">
                    <div class="avatar avatar--{{ $color }}">{{ $initials }}</div>
                    <div>
                      <div class="user-cell__name">{{ $user->name ?? 'N/A' }}</div>
                      <div class="user-cell__email">{{ $user->email ?? 'N/A' }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="td-date">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                      <line x1="16" y1="2" x2="16" y2="6" />
                      <line x1="8" y1="2" x2="8" y2="6" />
                      <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                  </div>
                </td>
                <td>
                  <span class="badge badge-teal">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                      stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Selesai
                  </span>
                </td>
                <td>
                  <button class="btn-detail" onclick="alert('Detail: ' + '{{ $item->_id }}')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                      stroke-linecap="round" stroke-linejoin="round">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                    Detail
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        {{-- PAGINATION --}}
        <div class="pagination-wrap">
          {{ $kuesioner->links() }}
        </div>

      @else
        {{-- EMPTY STATE --}}
        <div class="no-data">
          <div class="no-data__icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
              <polyline points="14 2 14 8 20 8" />
              <line x1="16" y1="13" x2="8" y2="13" />
              <line x1="16" y1="17" x2="8" y2="17" />
              <polyline points="10 9 9 9 8 9" />
            </svg>
          </div>
          <div class="no-data__title">Belum ada data kuesioner</div>
          <div class="no-data__sub">Kuesioner akan muncul di sini setelah user mulai mengisi</div>
        </div>
      @endif
    </div>
  </div>

  <script>
    function refreshData() { location.reload(); }
  </script>

@endsection