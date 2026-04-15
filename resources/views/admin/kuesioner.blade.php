{{--
  ═══════════════════════════════════════════════
  resources/views/admin/kuesioner.blade.php
  Halaman Data Kuesioner — Activa Admin
  ═══════════════════════════════════════════════
--}}
@extends('layouts.app')
@section('title','Data Kuesioner — Activa')
@section('page-title','Data Kuesioner')

@section('topbar-actions')
  <button class="btn btn-ghost btn-sm" onclick="refreshData()">⟳ Refresh</button>
@endsection

@push('styles')
<style>
  .table-wrap {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow);
  }

  .table-container {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead th {
    padding: 12px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    color: var(--navy);
    background: #EEF4FB;
    border-bottom: 2px solid var(--border2);
  }

  tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.1s;
  }

  tbody tr:hover {
    background: #F5F9FF;
  }

  tbody tr:last-child {
    border-bottom: none;
  }

  td {
    padding: 12px 16px;
    font-size: 13px;
  }

  .badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
  }

  .badge-navy {
    background: rgba(30, 58, 95, 0.1);
    color: var(--navy);
  }

  .badge-teal {
    background: rgba(13, 148, 136, 0.1);
    color: var(--teal);
  }

  .badge-amber {
    background: rgba(217, 119, 6, 0.1);
    color: var(--amber);
  }

  .pagination-wrap {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 20px;
    padding: 0 16px 16px;
  }

  .pagination-wrap a, .pagination-wrap span {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid var(--border);
    font-size: 12px;
    text-decoration: none;
    color: var(--text2);
    transition: all 0.15s;
  }

  .pagination-wrap a:hover {
    background: var(--ice);
    color: var(--navy);
  }

  .pagination-wrap .active {
    background: var(--teal);
    color: white;
    border-color: var(--teal);
  }

  .no-data {
    text-align: center;
    padding: 40px 20px;
    color: var(--text3);
  }
</style>
@endpush

@section('content')

{{-- INFO BANNER --}}
<div class="alert alert-navy mb-5">
  <span style="font-size:16px;">≡</span>
  <div>
    <div style="font-weight:600;font-size:13px;margin-bottom:2px;">Data Respons Kuesioner User</div>
    <div style="font-size:12px;line-height:1.6;">Menampilkan semua respons kuesioner yang telah diisi oleh pengguna. Data ini digunakan untuk analisis fokus dan produktivitas.</div>
  </div>
</div>

{{-- TABLE --}}
<div class="table-wrap">
  <div class="table-container">
    @if($kuesioner->count() > 0)
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Email</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($kuesioner as $item)
            @php
              $user = \App\Models\User::find($item->user_id);
            @endphp
            <tr>
              <td>{{ $loop->iteration + ($kuesioner->perPage() * ($kuesioner->currentPage() - 1)) }}</td>
              <td>
                <div style="font-weight:500;color:var(--navy);">{{ $user->name ?? 'N/A' }}</div>
              </td>
              <td>
                <div style="font-size:12px;color:var(--text3);">{{ $user->email ?? 'N/A' }}</div>
              </td>
              <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
              <td>
                <span class="badge badge-teal">Selesai</span>
              </td>
              <td>
                <button class="btn btn-ghost btn-sm" style="padding:5px 12px;font-size:11px;" onclick="alert('Detail: ' + '{{ $item->_id }}')">
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
      <div class="no-data">
        <div style="font-size:40px;margin-bottom:10px;">≡</div>
        <div style="font-size:13px;font-weight:500;margin-bottom:4px;">Belum ada data kuesioner</div>
        <div style="font-size:12px;color:var(--text3);">Kuesioner akan muncul di sini setelah user mulai mengisi</div>
      </div>
    @endif
  </div>
</div>

<script>
  function refreshData() {
    location.reload();
  }
</script>

@endsection
