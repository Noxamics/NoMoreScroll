{{--
═══════════════════════════════════════════════
resources/views/admin/rules.blade.php
═══════════════════════════════════════════════
--}}
@extends('layouts.app')
@section('title', 'Rule Rekomendasi — Activa')
@section('page-title', 'Rule Rekomendasi')

@section('topbar-actions')
  <button onclick="openCreateModal()" style="
    display:inline-flex;align-items:center;gap:6px;
    padding:8px 15px;border-radius:9px;font-size:12px;font-weight:500;
    background:#1E3A5F;color:#fff;border:1px solid transparent;
    cursor:pointer;font-family:var(--sans);transition:background .15s;
  " onmouseover="this.style.background='#264875'" onmouseout="this.style.background='#1E3A5F'">
    <span style="font-size:17px;line-height:1;font-weight:300">+</span>
    Tambah Rule
  </button>
@endsection

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/rules.css') }}">
@endpush

@section('content')
  {{-- INFO BANNER --}}
  <div class="alert alert-navy mb-5">
    <span class="alert__icon">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
        stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
      </svg>
    </span>
    <div>
      <div class="alert__title">Cara kerja Rule-Based Recommendation</div>
      <div class="alert__body">
        Jika kondisi <strong class="text-amber">IF</strong> terpenuhi pada data user, sistem menampilkan rekomendasi
        <strong class="text-teal">THEN</strong> ke layar user. Toggle aktif/nonaktif atau klik Edit — tanpa coding sama
        sekali.
      </div>
    </div>
  </div>

  {{-- STATS --}}
  <div class="rules-stats mb-5">

    <div class="rules-stat-card">
      <div class="rules-stat-icon rules-stat-icon--navy">
        <svg viewBox="0 0 24 24">
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
          <line x1="9" y1="9" x2="15" y2="9" />
          <line x1="9" y1="12" x2="15" y2="12" />
          <line x1="9" y1="15" x2="11" y2="15" />
        </svg>
      </div>
      <div>
        <div class="rules-stat-label">Total Rules</div>
        <div class="rules-stat-val rules-stat-val--navy">{{ $rules->count() }}</div>
      </div>
    </div>

    <div class="rules-stat-card">
      <div class="rules-stat-icon">
        <svg viewBox="0 0 24 24">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
          <polyline points="22 4 12 14.01 9 11.01" />
        </svg>
      </div>
      <div>
        <div class="rules-stat-label">Rules Aktif</div>
        <div class="rules-stat-val rules-stat-val--teal">{{ $rules->where('is_active', true)->count() }}</div>
      </div>
    </div>

    <div class="rules-stat-card">
      <div class="rules-stat-icon rules-stat-icon--navy">
        <svg viewBox="0 0 24 24">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
          <path d="M16 3.13a4 4 0 0 1 0 7.75" />
        </svg>
      </div>
      <div>
        <div class="rules-stat-label">Total Diterapkan</div>
        <div class="rules-stat-val rules-stat-val--navy">{{ number_format($rules->sum('applied_count')) }}</div>
      </div>
    </div>

  </div>

  {{-- RULE LIST HEADING --}}
  <div class="rules-section-head mb-4">
    <div class="rules-section-title">Daftar Rule</div>
    <div class="rules-count-badge">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
        stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="8" x2="12" y2="16" />
        <line x1="8" y1="12" x2="16" y2="12" />
      </svg>
      {{ $rules->count() }} rule
    </div>
  </div>

  {{-- RULE LIST --}}
  @forelse($rules as $rule)
    @php
      $priMap = [
        'high' => ['pill-red', 'Tinggi'],
        'medium' => ['pill-amber', 'Sedang'],
        'low' => ['pill-teal', 'Rendah'],
      ];
      [$priCls, $priLbl] = $priMap[$rule->priority] ?? ['pill-navy', '—'];
    @endphp

    <div class="rule-card {{ $rule->is_active ? 'rule-on' : 'rule-off' }}">
      <div class="rule-card-inner">

        {{-- HEAD --}}
        <div class="rule-head">
          <div class="rule-id">R{{ str_pad($loop->index + 1, 2, '0', STR_PAD_LEFT) }}</div>
          <div class="rule-name">{{ $rule->name }}</div>

          {{-- Priority pill --}}
          <span class="pill {{ $priCls }} mr-2">
            <span class="pill-dot"></span>
            {{ $priLbl }}
          </span>

          {{-- Active status badge --}}
          <span class="rule-status-badge {{ $rule->is_active ? 'rule-status-badge--on' : 'rule-status-badge--off' }} mr-2">
            <span class="rule-status-dot"></span>
            {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
          </span>

          {{-- Toggle --}}
          <form method="POST" action="{{ route('admin.rules.toggle', $rule->_id) }}">
            @csrf @method('PATCH')
            <label class="tog">
              <input type="checkbox" {{ $rule->is_active ? 'checked' : '' }} onchange="this.form.submit()">
              <span class="tog-track"></span>
            </label>
          </form>
        </div>

        {{-- BODY: IF → THEN --}}
        <div class="rule-body">

          <div class="rule-side">
            <div class="rs-lbl rs-lbl--if">
              <svg viewBox="0 0 24 24">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
              </svg>
              IF — Kondisi
            </div>
            <div class="rs-cond">{{ $rule->variable }} {{ $rule->operator }} {{ $rule->value }}</div>
          </div>

          <div class="rule-arr">
            <svg viewBox="0 0 24 24">
              <line x1="5" y1="12" x2="19" y2="12" />
              <polyline points="12 5 19 12 12 19" />
            </svg>
          </div>

          <div class="rule-side">
            <div class="rs-lbl rs-lbl--then">
              <svg viewBox="0 0 24 24">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
              </svg>
              THEN — Rekomendasi
            </div>
            <div class="rs-then">{{ $rule->recommendation }}</div>
          </div>

        </div>

        {{-- FOOTER --}}
        <div class="rule-foot">
          <div class="rule-meta">
            <svg viewBox="0 0 24 24">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
            </svg>
            Diterapkan ke <strong>{{ number_format($rule->applied_count ?? 0) }}</strong> user
          </div>

          <div class="rule-actions">

            <button class="btn btn-ghost btn-sm" data-id="{{ $rule->_id }}" data-name="{{ $rule->name }}"
              data-variable="{{ $rule->variable }}" data-operator="{{ $rule->operator }}" data-value="{{ $rule->value }}"
              data-recommendation="{{ $rule->recommendation }}" data-priority="{{ $rule->priority }}"
              onclick="openEdit(this)">
              <span class="btn-icon-label">
                <svg viewBox="0 0 24 24">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                </svg>
                Edit
              </span>
            </button>

            <form method="POST" action="{{ route('admin.rules.destroy', $rule->_id) }}"
              onsubmit="return confirm('Hapus rule ini?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">
                <span class="btn-icon-label">
                  <svg viewBox="0 0 24 24">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                    <path d="M10 11v6" />
                    <path d="M14 11v6" />
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
                  </svg>
                  Hapus
                </span>
              </button>
            </form>

          </div>
        </div>

      </div>{{-- /.rule-card-inner --}}
    </div>

  @empty
    <div class="rules-empty">
      <div class="rules-empty-icon">
        <svg viewBox="0 0 24 24">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
          <polyline points="14 2 14 8 20 8" />
          <line x1="12" y1="18" x2="12" y2="12" />
          <line x1="9" y1="15" x2="15" y2="15" />
        </svg>
      </div>
      <div class="rules-empty-title">Belum ada rule</div>
      <div class="rules-empty-sub">Klik <strong>+ Tambah Rule</strong> di atas untuk memulai.</div>
    </div>
  @endforelse

  {{-- ══════════════════════════════════════════════════════════
  MODAL TAMBAH / EDIT
  ══════════════════════════════════════════════════════════ --}}
  <div class="modal-overlay" id="modal" onclick="if(event.target===this)closeModal()">
    <div class="modal-box modal-box--md">

      <div class="modal-header">
        <div class="modal-title" id="m-title">
          <span style="display:inline-flex;align-items:center;gap:7px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
              <line x1="9" y1="9" x2="15" y2="9" />
              <line x1="9" y1="12" x2="15" y2="12" />
              <line x1="9" y1="15" x2="11" y2="15" />
            </svg>
            Tambah Rule Baru
          </span>
        </div>
        <button class="modal-close" onclick="closeModal()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>

      {{-- Variable hint --}}
      <div class="modal-var-hint mb-4">
        <div class="modal-var-hint-title">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          Variabel yang tersedia
        </div>
        <div class="var-badge-row">
          <span class="var-badge">sleep_hours</span>
          <span class="var-badge">screen_time</span>
          <span class="var-badge">focus_score</span>
          <span class="var-badge">digital_dep</span>
          <span class="var-badge">productivity</span>
          <span class="var-badge">social_media</span>
        </div>
      </div>

      <form method="POST" id="rule-form" action="{{ route('admin.rules.store') }}">
        @csrf
        <input type="hidden" name="_method" id="m-method" value="POST">
        <input type="hidden" name="_id" id="m-id" value="">

        <div class="field">
          <label>Nama Rule</label>
          <input class="inp" name="name" id="m-name" required placeholder="contoh: Kurang Tidur Kronis">
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
            <input class="inp inp--center" name="value" id="m-val" type="number" step="any" required placeholder="nilai">
          </div>
        </div>

        <div class="field">
          <label>Rekomendasi THEN</label>
          <textarea class="inp inp--textarea" name="recommendation" id="m-then" required
            placeholder="Tulis rekomendasi yang akan ditampilkan ke user..."></textarea>
        </div>

        <div class="field">
          <label>Prioritas</label>
          <select class="inp" name="priority" id="m-pri">
            <option value="high">🔴 Tinggi — Perlu segera ditangani</option>
            <option value="medium">🟡 Sedang — Perhatikan secara rutin</option>
            <option value="low">🟢 Rendah — Tips umum kesehatan</option>
          </select>
        </div>

        <div class="modal-footer-btns">
          <button type="button" class="btn btn-ghost" onclick="closeModal()">
            <span class="btn-icon-label">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
              Batal
            </span>
          </button>
          <button type="submit" class="btn btn-teal">
            <span class="btn-icon-label">
              <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                <polyline points="17 21 17 13 7 13 7 21" />
                <polyline points="7 3 7 8 15 8" />
              </svg>
              Simpan Rule
            </span>
          </button>
        </div>

      </form>
    </div>
  </div>

@endsection

@push('scripts')
  <script>
    window.ROUTES = {
      store: "{{ route('admin.rules.store') }}",
      update: "/admin/rules/:id",
    };
  </script>
  <script src="{{ asset('js/rules.js') }}"></script>
@endpush