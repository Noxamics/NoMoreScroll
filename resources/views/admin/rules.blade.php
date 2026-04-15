{{--
  ═══════════════════════════════════════════════
  resources/views/admin/rules.blade.php
  ═══════════════════════════════════════════════
--}}
@extends('layouts.app')
@section('title','Rule Rekomendasi — Activa')
@section('page-title','Rule Rekomendasi')

@section('topbar-actions')
  <button class="btn btn-ghost btn-sm" onclick="openAdd()">+ Tambah Rule</button>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/rules.css') }}">
@endpush

@section('content')

{{-- INFO BANNER --}}
<div class="alert alert-navy mb-5">
  <span class="alert__icon">⌘</span>
  <div>
    <div class="alert__title">Cara kerja Rule-Based Recommendation</div>
    <div class="alert__body">
      Jika kondisi <strong class="text-amber">IF</strong> terpenuhi pada data user, sistem menampilkan rekomendasi
      <strong class="text-teal">THEN</strong> ke layar user. Toggle aktif/nonaktif atau klik Edit — tanpa coding sama sekali.
    </div>
  </div>
</div>

{{-- STATS --}}
<div class="rules-stats mb-5">
  <div class="rules-stat-card">
    <div class="rules-stat-icon">⌘</div>
    <div>
      <div class="rules-stat-label">Total Rules</div>
      <div class="rules-stat-val rules-stat-val--navy">{{ $rules->count() }}</div>
    </div>
  </div>
  <div class="rules-stat-card">
    <div class="rules-stat-icon">✅</div>
    <div>
      <div class="rules-stat-label">Rules Aktif</div>
      <div class="rules-stat-val rules-stat-val--teal">{{ $rules->where('is_active', true)->count() }}</div>
    </div>
  </div>
  <div class="rules-stat-card">
    <div class="rules-stat-icon">👥</div>
    <div>
      <div class="rules-stat-label">Total Diterapkan</div>
      <div class="rules-stat-val rules-stat-val--navy">{{ number_format($rules->sum('applied_count')) }}</div>
    </div>
  </div>
</div>

{{-- RULE LIST --}}
@forelse($rules as $rule)
@php
  $priMap = [
    'high'   => ['pill-red',   'Prioritas Tinggi'],
    'medium' => ['pill-amber', 'Prioritas Sedang'],
    'low'    => ['pill-teal',  'Prioritas Rendah'],
  ];
  [$priCls, $priLbl] = $priMap[$rule->priority] ?? ['pill-navy', '—'];
@endphp
<div class="rule-card {{ $rule->is_active ? 'rule-on' : 'rule-off' }}">

  <div class="rule-head">
    <div class="rule-id">R{{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}</div>
    <div class="rule-name">{{ $rule->name }}</div>
    <span class="pill {{ $priCls }} mr-2">{{ $priLbl }}</span>
    <form method="POST" action="{{ route('admin.rules.toggle', $rule->_id) }}">
      @csrf @method('PATCH')
      <label class="tog">
        <input type="checkbox" {{ $rule->is_active ? 'checked' : '' }} onchange="this.form.submit()">
        <span class="tog-track"></span>
      </label>
    </form>
  </div>

  <div class="rule-body">
    <div class="rule-side">
      <div class="rs-lbl rs-lbl--if">IF — Kondisi Terpenuhi</div>
      <div class="rs-cond">{{ $rule->variable }} {{ $rule->operator }} {{ $rule->value }}</div>
    </div>
    <div class="rule-arr">→</div>
    <div class="rule-side">
      <div class="rs-lbl rs-lbl--then">THEN — Tampilkan ke User</div>
      <div class="rs-then">{{ $rule->recommendation }}</div>
    </div>
  </div>

  <div class="rule-foot">
    <div class="rule-meta">Diterapkan ke <strong>{{ number_format($rule->applied_count ?? 0) }}</strong> user</div>
    <div class="rule-actions">
      {{-- FIX: pindahkan json_encode ke data-rule attribute, hindari JS false-positive VS Code --}}
      <button class="btn btn-ghost btn-sm"
              data-rule="{{ htmlspecialchars(json_encode($rule), ENT_QUOTES, 'UTF-8') }}"
              onclick="openEdit(JSON.parse(this.dataset.rule))">✏ Edit</button>
      <form method="POST" action="{{ route('admin.rules.destroy', $rule->_id) }}"
            onsubmit="return confirm('Hapus rule ini?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm">✕ Hapus</button>
      </form>
    </div>
  </div>

</div>
@empty
<div class="rules-empty">
  Belum ada rule. Klik <strong>+ Tambah Rule</strong> untuk memulai.
</div>
@endforelse

{{-- MODAL TAMBAH / EDIT --}}
<div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
  <div class="modal-box modal-box--md">
    <div class="modal-header">
      <div class="modal-title" id="m-title">Tambah Rule Baru</div>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>

    <div class="alert alert-info mb-4 text-sm">
      💡 Variabel: <strong>sleep_hours</strong> · <strong>screen_time</strong> · <strong>focus_score</strong> ·
      <strong>digital_dep</strong> · <strong>productivity</strong> · <strong>social_media</strong>
    </div>

    <form method="POST" id="rule-form" action="{{ route('admin.rules.store') }}">
      @csrf
      <input type="hidden" name="_method" id="m-method" value="POST">
      <input type="hidden" name="_id"     id="m-id"     value="">

      <div class="field">
        <label>Nama Rule</label>
        <input class="inp" name="name" id="m-name" required placeholder="contoh: Kurang Tidur">
      </div>

      <div class="field">
        <label>Kondisi IF</label>
        <div class="field-row">
          <select class="inp" name="variable" id="m-var">
            <option value="sleep_hours">sleep_hours</option>
            <option value="screen_time">screen_time</option>
            <option value="focus_score">focus_score</option>
            <option value="digital_dep">digital_dep</option>
            <option value="productivity">productivity</option>
            <option value="social_media">social_media</option>
          </select>
          <select class="inp" name="operator" id="m-op">
            <option value="<">&lt; kurang dari</option>
            <option value=">">&gt; lebih dari</option>
            <option value="<=">&lt;= maks</option>
            <option value=">=">&gt;= min</option>
          </select>
          <input class="inp inp--center" name="value" id="m-val"
                 type="number" step="any" required placeholder="nilai">
        </div>
      </div>

      <div class="field">
        <label>Rekomendasi THEN</label>
        <textarea class="inp inp--textarea" name="recommendation" id="m-then"
                  required placeholder="Tulis rekomendasi untuk user..."></textarea>
      </div>

      <div class="field">
        <label>Prioritas</label>
        <select class="inp" name="priority" id="m-pri">
          <option value="high">🔴 Tinggi</option>
          <option value="medium">🟡 Sedang</option>
          <option value="low">🟢 Rendah</option>
        </select>
      </div>

      <div class="modal-footer-btns">
        <button type="button" class="btn btn-ghost" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn btn-teal">💾 Simpan Rule</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  window.ROUTES = {
    store:  "{{ route('admin.rules.store') }}",
    update: "/admin/rules/:id",
  };
</script>
<script src="{{ asset('js/rules.js') }}"></script>
@endpush