{{--
═══════════════════════════════════════════════
resources/views/layouts/app.blade.php
Layout utama Activa Admin Panel
═══════════════════════════════════════════════
--}}
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/svg+xml" href="{{ asset('../images/NewLogoEmblem2.svg') }}">
  <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
  <title>@yield('title', 'Activa Admin')</title>
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
  <link rel="stylesheet" href="{{ asset('css/rules.css') }}">
  @stack('head-scripts')
  <style>
    :root {
      --navy: #1E3A5F;
      --navy-dk: #162D4A;
      --navy-lt: #264875;
      --navy-xlt: rgba(30, 58, 95, 0.08);
      --teal: #0D9488;
      --teal-dk: #0A7A6F;
      --teal-lt: rgba(13, 148, 136, 0.10);
      --teal-md: rgba(13, 148, 136, 0.18);
      --ice: #F0F9FF;
      --white: #FFFFFF;
      --border: #E2EAF2;
      --border2: #C8D8EA;
      --red: #E05252;
      --red-lt: rgba(224, 82, 82, 0.10);
      --amber: #D97706;
      --amber-lt: rgba(217, 119, 6, 0.10);
      --green: #059669;
      --green-lt: rgba(5, 150, 105, 0.10);
      --blue: #2563EB;
      --blue-lt: rgba(37, 99, 235, 0.10);
      --violet: #7C3AED;
      --violet-lt: rgba(124, 58, 237, 0.10);
      --text: #0F1F35;
      --text2: #4A6180;
      --text3: #8BA3BE;
      --serif: 'DM Serif Display', serif;
      --sans: 'DM Sans', sans-serif;
      --sidebar-w: 248px;
      --radius: 12px;
      --radius-lg: 16px;
      --shadow: 0 1px 3px rgba(30, 58, 95, 0.06), 0 4px 16px rgba(30, 58, 95, 0.05);
      --shadow-md: 0 2px 8px rgba(30, 58, 95, 0.08), 0 8px 24px rgba(30, 58, 95, 0.07);
    }

    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      font-size: 15px;
    }

    body {
      background: var(--ice);
      color: var(--text);
      font-family: var(--sans);
      min-height: 100vh;
      display: flex;
    }

    /* SIDEBAR */
    .sidebar {
      width: var(--sidebar-w);
      background: var(--navy);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      left: 0;
      bottom: 0;
      z-index: 100;
      padding-bottom: 20px;
    }

    .logo-img {
      width: 130px;
      height: 100px;
      object-fit: contain;
      margin-bottom: 20px;
      margin-left: 30px;
    }

    .sidebar-logo {
      padding: 15px 20px 20px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 11px;
    }

    .logo-icon {
      width: 38px;
      height: 38px;
      background: var(--teal);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(13, 148, 136, 0.35);
    }

    .logo-name {
      font-family: var(--serif);
      font-size: 20px;
      color: #fff;
      letter-spacing: -0.3px;
    }

    .logo-name em {
      color: #5EEAD4;
      font-style: italic;
    }

    .logo-sub {
      font-size: 10px;
      color: rgba(255, 255, 255, 0.4);
      letter-spacing: 0.1em;
      text-transform: uppercase;
      margin-top: 1px;
    }

    .nav-group {
      padding: 0 10px;
      margin-bottom: 4px;
    }

    .nav-group-label {
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.3);
      padding: 0 10px;
      margin: 10px 0 4px;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 9px 10px;
      border-radius: 10px;
      color: rgba(255, 255, 255, 0.55);
      font-size: 13px;
      text-decoration: none;
      transition: all .15s;
      position: relative;
    }

    .nav-link:hover {
      background: rgba(255, 255, 255, 0.07);
      color: rgba(255, 255, 255, 0.85);
    }

    .nav-link.active {
      background: rgba(255, 255, 255, 0.12);
      color: #fff;
      font-weight: 500;
    }

    .nav-link.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 20%;
      bottom: 20%;
      width: 3px;
      background: var(--teal);
      border-radius: 0 3px 3px 0;
    }

    .nav-icon {
      width: 30px;
      height: 30px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      flex-shrink: 0;
      background: rgba(255, 255, 255, 0.07);
    }

    .nav-link.active .nav-icon {
      background: var(--teal-md);
    }

    .sidebar-footer {
      margin-top: auto;
      padding: 14px 10px 0;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .admin-pill {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 10px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.07);
    }

    .admin-av {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--teal);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      color: #fff;
      flex-shrink: 0;
    }

    .admin-name {
      font-size: 12px;
      font-weight: 600;
      color: #fff;
    }

    .admin-role {
      font-size: 10px;
      color: rgba(255, 255, 255, 0.4);
    }

    .logout-btn {
      margin-left: auto;
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.35);
      cursor: pointer;
      font-size: 15px;
      transition: color .2s;
    }

    .logout-btn:hover {
      color: var(--red);
    }

    /* MAIN */
    .main {
      margin-left: var(--sidebar-w);
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      background: var(--white);
      border-bottom: 1px solid var(--border);
      padding: 0 28px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 50;
      box-shadow: 0 1px 0 var(--border);
    }

    .topbar-title {
      font-family: var(--serif);
      font-size: 20px;
      color: var(--navy);
    }

    .topbar-sub {
      font-size: 11px;
      color: var(--text3);
      margin-top: 1px;
    }

    .topbar-right {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .topbar-badge {
      display: flex;
      align-items: center;
      gap: 6px;
      background: var(--teal-lt);
      border: 1px solid rgba(13, 148, 136, 0.2);
      padding: 5px 12px;
      border-radius: 99px;
      font-size: 11px;
      color: var(--teal);
      font-weight: 600;
    }

    .topbar-badge::before {
      content: '';
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--teal);
      animation: blink 2s infinite;
    }

    @keyframes blink {
      0%, 100% { opacity: 1 }
      50% { opacity: .3 }
    }

    .content {
      padding: 28px;
    }

    /* CARDS */
    .card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 22px;
      box-shadow: var(--shadow);
    }

    .card-title {
      font-family: var(--serif);
      font-size: 16px;
      color: var(--navy);
      margin-bottom: 2px;
    }

    .card-sub {
      font-size: 11px;
      color: var(--text3);
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 18px;
    }

    .card-action {
      font-size: 11px;
      font-weight: 600;
      color: var(--teal);
      background: none;
      border: none;
      cursor: pointer;
      font-family: var(--sans);
      text-decoration: none;
    }

    .card-action:hover {
      opacity: .7;
    }

    /* STAT CARDS */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
      margin-bottom: 22px;
    }

    .stat-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 18px 20px;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }

    .stat-card:hover {
      box-shadow: var(--shadow-md);
    }

    .top-line {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      border-radius: 99px 99px 0 0;
    }

    .stat-icon {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 17px;
      margin-bottom: 12px;
    }

    .stat-label {
      font-size: 11px;
      color: var(--text3);
      font-weight: 500;
      margin-bottom: 4px;
    }

    .stat-val {
      font-size: 28px;
      font-weight: 700;
      letter-spacing: -1px;
      margin-bottom: 4px;
      font-family: var(--serif);
    }

    .stat-change {
      font-size: 11px;
      font-weight: 500;
    }

    .up  { color: var(--green) }
    .down { color: var(--red) }
    .neu  { color: var(--text3) }

    /* PILLS */
    .pill {
      font-size: 11px;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 99px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .pill-teal   { background: var(--teal-lt);   color: var(--teal);   }
    .pill-red    { background: var(--red-lt);    color: var(--red);    }
    .pill-amber  { background: var(--amber-lt);  color: var(--amber);  }
    .pill-blue   { background: var(--blue-lt);   color: var(--blue);   }
    .pill-violet { background: var(--violet-lt); color: var(--violet); }
    .pill-navy   { background: var(--navy-xlt);  color: var(--navy);   }

    /* BUTTONS */
    .btn {
      padding: 9px 18px;
      border-radius: var(--radius);
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      font-family: var(--sans);
      transition: all .15s;
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 7px;
    }

    .btn-teal {
      background: var(--teal);
      color: #fff;
      box-shadow: 0 3px 10px rgba(13, 148, 136, 0.28);
    }

    .btn-teal:hover {
      background: var(--teal-dk);
      transform: translateY(-1px);
    }

    .btn-navy {
      background: var(--navy);
      color: #fff;
      box-shadow: 0 3px 10px rgba(30, 58, 95, 0.25);
    }

    .btn-navy:hover { background: var(--navy-lt); }

    .btn-ghost {
      background: var(--white);
      color: var(--text2);
      border: 1px solid var(--border2);
    }

    .btn-ghost:hover {
      background: var(--ice);
      color: var(--navy);
    }

    .btn-danger {
      background: var(--red-lt);
      color: var(--red);
      border: 1px solid rgba(224, 82, 82, 0.2);
    }

    .btn-danger:hover { background: rgba(224, 82, 82, 0.18); }

    .btn-sm {
      padding: 6px 12px;
      font-size: 12px;
    }

    /* INPUTS */
    .inp {
      background: var(--white);
      border: 1px solid var(--border2);
      border-radius: var(--radius);
      color: var(--text);
      font-size: 13px;
      font-family: var(--sans);
      outline: none;
      transition: all .2s;
      padding: 9px 13px;
    }

    .inp:focus {
      border-color: var(--teal);
      box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
    }

    .inp::placeholder { color: var(--text3); }

    /* TABLE */
    .tbl-wrap { overflow-x: auto; }

    table { width: 100%; border-collapse: collapse; }

    thead th {
      padding: 10px 14px;
      text-align: left;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .09em;
      text-transform: uppercase;
      color: var(--navy);
      background: #EEF4FB;
      border-bottom: 2px solid var(--border2);
    }

    tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background .1s;
    }

    tbody tr:hover { background: #F5F9FF; }
    tbody tr:last-child { border-bottom: none; }

    td { padding: 12px 14px; font-size: 13px; }

    /* GRIDS */
    .grid-2   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .grid-3   { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .grid-4   { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .grid-2-1 { display: grid; grid-template-columns: 1fr 320px; gap: 16px; }

    /* SCORE BAR */
    .score-bar-wrap { display: flex; align-items: center; gap: 8px; }

    .score-bar {
      flex: 1;
      height: 5px;
      background: var(--border);
      border-radius: 99px;
      overflow: hidden;
      min-width: 50px;
    }

    .score-bar-fill { height: 100%; border-radius: 99px; }

    /* TOGGLE SWITCH */
    .tog { position: relative; width: 38px; height: 21px; flex-shrink: 0; }
    .tog input { opacity: 0; width: 0; height: 0; }

    .tog-track {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background: var(--border2);
      border-radius: 10px;
      transition: background .2s;
    }

    .tog-track::before {
      content: '';
      position: absolute;
      height: 15px;
      width: 15px;
      left: 3px;
      top: 3px;
      border-radius: 50%;
      background: #fff;
      transition: transform .2s;
    }

    .tog input:checked + .tog-track { background: var(--teal); }
    .tog input:checked + .tog-track::before { transform: translateX(17px); }

    /* ANIMATIONS */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(12px) }
      to   { opacity: 1; transform: translateY(0) }
    }

    .stat-card { animation: fadeUp .4s ease both; }
    .stat-card:nth-child(1) { animation-delay: .05s }
    .stat-card:nth-child(2) { animation-delay: .10s }
    .stat-card:nth-child(3) { animation-delay: .15s }
    .stat-card:nth-child(4) { animation-delay: .20s }

    @keyframes slideUp {
      from { opacity: 0; transform: translateY(10px) }
      to   { opacity: 1; transform: translateY(0) }
    }

    .card { animation: slideUp .35s ease both; }

    /* MODAL */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 31, 53, 0.55);
      backdrop-filter: blur(4px);
      z-index: 200;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .modal-overlay.show { display: flex; }

    .modal-box {
      background: var(--white);
      border: 1px solid var(--border2);
      border-radius: 20px;
      padding: 28px;
      width: 100%;
      max-width: 480px;
      animation: fadeUp .3s ease;
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .modal-title {
      font-family: var(--serif);
      font-size: 18px;
      color: var(--navy);
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 18px;
      cursor: pointer;
      color: var(--text3);
      line-height: 1;
    }

    .modal-close:hover { color: var(--text); }

    /* FORM FIELDS */
    .field { margin-bottom: 14px; }

    .field label {
      display: block;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: var(--text2);
      margin-bottom: 6px;
    }

    .field-row {
      display: grid;
      grid-template-columns: 1fr 100px 80px;
      gap: 8px;
    }

    /* ALERT BANNERS */
    .alert {
      border-radius: var(--radius);
      padding: 12px 16px;
      font-size: 13px;
      line-height: 1.6;
      margin-bottom: 16px;
      display: flex;
      align-items: flex-start;
      gap: 10px;
      border: 1px solid;
    }

    .alert-info {
      background: rgba(13, 148, 136, 0.06);
      border-color: rgba(13, 148, 136, 0.18);
      color: var(--teal);
    }

    .alert-navy {
      background: var(--navy-xlt);
      border-color: rgba(30, 58, 95, 0.15);
      color: var(--navy);
    }

    .alert-warning {
      background: var(--amber-lt);
      border-color: rgba(217, 119, 6, 0.2);
      color: var(--amber);
    }

    /* SCROLLBAR */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }

    /* ── FIX CANVAS CHART ── */
    canvas {
      display: block;
      width: 100% !important;
      max-height: 300px;
    }

    /* RESPONSIVE */
    @media(max-width:1200px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .grid-2-1   { grid-template-columns: 1fr; }
    }

    @media(max-width:768px) {
      .sidebar  { display: none; }
      .main     { margin-left: 0; }
      .content  { padding: 16px; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
      .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
    }
  </style>
  @stack('styles')
</head>

<body>

  {{-- ═══ SIDEBAR ═══ --}}
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div>
        <div class="brand-icon">
          <img src="../images/NewLogoPutih.svg" alt="Logo" class="logo-img">
        </div>
        <div class="logo-sub">Admin Panel</div>
      </div>
    </div>

    <nav>
      <div class="nav-group">
        <div class="nav-group-label">Utama</div>
        <a href="{{ route('admin.dashboard') }}"
          class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <div class="nav-icon">◈</div> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
          <div class="nav-icon">⊞</div> User Management
        </a>
      </div>

      <div class="nav-group">
        <div class="nav-group-label">Analitik</div>
        <a href="{{ route('admin.monitoring') }}"
          class="nav-link {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}">
          <div class="nav-icon">◎</div> Monitoring ML
        </a>
        <a href="{{ route('admin.kuesioner') }}"
          class="nav-link {{ request()->routeIs('admin.kuesioner*') ? 'active' : '' }}">
          <div class="nav-icon">≡</div> Data Kuesioner
        </a>
      </div>

      <div class="nav-group">
        <div class="nav-group-label">Konfigurasi</div>
        <a href="{{ route('admin.rules') }}" class="nav-link {{ request()->routeIs('admin.rules*') ? 'active' : '' }}">
          <div class="nav-icon">⌘</div> Rule Rekomendasi
        </a>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="admin-pill">
        <div class="admin-av">
          {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
        </div>
        <div>
          <div class="admin-name">{{ auth()->user()->name ?? 'Admin' }}</div>
          <div class="admin-role">Super Admin</div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}" style="margin-left:auto">
          @csrf
          <button type="submit" class="logout-btn" title="Logout">⏻</button>
        </form>
      </div>
    </div>
  </aside>

  {{-- ═══ MAIN ═══ --}}
  <main class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="topbar-sub" id="topbar-date"></div>
      </div>
      <div class="topbar-right">
        @yield('topbar-actions')
        <div class="topbar-badge">System Online</div>
      </div>
    </div>

    <div class="content">

      {{-- Flash messages --}}
      @if(session('success'))
        <div class="alert alert-info" style="margin-bottom:20px">
          <span>✅</span> {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-warning" style="margin-bottom:20px">
          <span>⚠️</span> {{ session('error') }}
        </div>
      @endif

      @yield('content')
    </div>
  </main>

  <script>
    document.getElementById('topbar-date').textContent =
      new Date().toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric',
        month: 'long', year: 'numeric'
      });
  </script>
  @stack('scripts')

</body>

</html>