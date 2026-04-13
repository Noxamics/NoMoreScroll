{{--
  ═══════════════════════════════════════════════
  resources/views/admin/login.blade.php
  Halaman Login OTP — Passwordless
  ═══════════════════════════════════════════════
--}}
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login — Activa Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy:    #1E3A5F;
      --navy-lt: #264875;
      --teal:    #0D9488;
      --teal-dk: #0A7A6F;
      --teal-lt: rgba(13,148,136,0.10);
      --ice:     #F0F9FF;
      --white:   #FFFFFF;
      --border:  #E2EAF2;
      --border2: #C8D8EA;
      --red:     #E05252;
      --red-lt:  rgba(224,82,82,0.10);
      --text:    #0F1F35;
      --text2:   #4A6180;
      --text3:   #8BA3BE;
      --serif:   'DM Serif Display', serif;
      --sans:    'DM Sans', sans-serif;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }
    body {
      background: var(--ice);
      font-family: var(--sans);
      color: var(--text);
      min-height: 100vh;
      display: flex;
    }

    /* ── Split layout ── */
    .split { display: flex; width: 100%; min-height: 100vh; }

    /* ── Left brand panel ── */
    .left {
      background: var(--navy);
      width: 420px; flex-shrink: 0;
      display: flex; flex-direction: column;
      justify-content: space-between;
      padding: 40px; position: relative; overflow: hidden;
    }
    .left::before {
      content: ''; position: absolute;
      bottom: -80px; right: -80px;
      width: 300px; height: 300px;
      background: rgba(13,148,136,0.12);
      border-radius: 50%; pointer-events: none;
    }
    .left::after {
      content: ''; position: absolute;
      top: -60px; left: -60px;
      width: 200px; height: 200px;
      background: rgba(255,255,255,0.04);
      border-radius: 50%; pointer-events: none;
    }
    .brand { display: flex; align-items: center; gap: 12px; position: relative; z-index: 1; }
    .brand-icon {
      width: 44px; height: 44px;
      background: var(--teal); border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px;
      box-shadow: 0 4px 16px rgba(13,148,136,0.4);
    }
    .brand-name { font-family: var(--serif); font-size: 24px; color: #fff; }
    .brand-name em { color: #5EEAD4; font-style: italic; }
    .brand-tag { font-size: 10px; color: rgba(255,255,255,0.4); letter-spacing: .1em; text-transform: uppercase; margin-top: 1px; }

    .left-body { position: relative; z-index: 1; }
    .left-headline { font-family: var(--serif); font-size: 34px; color: #fff; line-height: 1.25; letter-spacing: -.5px; margin-bottom: 14px; }
    .left-headline em { color: #5EEAD4; font-style: italic; }
    .left-desc { font-size: 13px; color: rgba(255,255,255,0.6); line-height: 1.8; margin-bottom: 28px; }
    .feat-list { display: flex; flex-direction: column; gap: 11px; }
    .feat-item { display: flex; align-items: center; gap: 10px; font-size: 13px; color: rgba(255,255,255,0.7); }
    .feat-dot {
      width: 22px; height: 22px; border-radius: 50%;
      background: rgba(13,148,136,0.25); border: 1px solid rgba(13,148,136,0.35);
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; color: #5EEAD4; flex-shrink: 0;
    }
    .left-foot { font-size: 11px; color: rgba(255,255,255,0.25); letter-spacing: .05em; position: relative; z-index: 1; }

    /* ── Right login area ── */
    .right { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px; }

    .login-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 36px 32px;
      width: 100%; max-width: 400px;
      box-shadow: 0 4px 24px rgba(30,58,95,0.10), 0 1px 4px rgba(30,58,95,0.06);
      position: relative; overflow: hidden;
    }
    .login-card::before {
      content: '';
      position: absolute; top: 0; left: 0; right: 0; height: 4px;
      background: linear-gradient(90deg, var(--navy), var(--teal));
    }

    /* Step indicator */
    .step-ind { display: flex; align-items: center; gap: 6px; margin-bottom: 28px; }
    .s-dot { height: 6px; border-radius: 99px; background: var(--border2); transition: all .3s; }
    .s-dot.active { background: var(--teal); width: 22px; }
    .s-dot.done   { background: var(--teal); opacity: .4; width: 6px; }
    .s-dot.idle   { width: 6px; }
    .s-lbl { margin-left: auto; font-size: 11px; color: var(--text3); font-weight: 500; }

    /* Panels */
    .panel { display: none; animation: sIn .3s ease; }
    .panel.show { display: block; }
    @keyframes sIn { from{opacity:0;transform:translateX(10px)} to{opacity:1;transform:translateX(0)} }

    .p-title { font-family: var(--serif); font-size: 24px; color: var(--navy); margin-bottom: 6px; letter-spacing: -.3px; }
    .p-sub { font-size: 13px; color: var(--text2); line-height: 1.7; margin-bottom: 24px; }
    .p-sub strong { color: var(--teal); font-weight: 600; }

    /* Form field */
    .field { margin-bottom: 16px; }
    .field label { display: block; font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--text2); margin-bottom: 7px; }
    .field input {
      width: 100%; padding: 11px 14px;
      background: var(--ice); border: 1.5px solid var(--border2);
      border-radius: 10px; color: var(--text); font-size: 14px;
      font-family: var(--sans); outline: none; transition: all .2s;
    }
    .field input:focus { border-color: var(--teal); background: var(--white); box-shadow: 0 0 0 3px rgba(13,148,136,0.12); }
    .field input::placeholder { color: var(--text3); }

    /* OTP boxes */
    .otp-row { display: flex; gap: 8px; justify-content: center; margin-bottom: 16px; }
    .otp-box {
      width: 50px; height: 58px;
      background: var(--ice); border: 1.5px solid var(--border2);
      border-radius: 10px; text-align: center;
      font-size: 22px; font-weight: 700; color: var(--navy);
      font-family: var(--sans); outline: none; transition: all .2s;
      caret-color: var(--teal);
    }
    .otp-box:focus { border-color: var(--teal); background: var(--white); box-shadow: 0 0 0 3px rgba(13,148,136,0.12); }
    .otp-box.filled { border-color: var(--teal); color: var(--teal); background: rgba(13,148,136,0.04); }

    /* Timer */
    .timer-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 12px; }
    .timer-txt { color: var(--text2); }
    .timer-txt span { color: var(--navy); font-weight: 700; font-variant-numeric: tabular-nums; }
    .resend { background: none; border: none; font-size: 12px; color: var(--text3); cursor: pointer; font-family: var(--sans); font-weight: 500; transition: color .2s; }
    .resend:hover:not(:disabled) { color: var(--teal); }
    .resend:disabled { opacity: .4; cursor: default; }

    /* CTA buttons */
    .btn-cta {
      width: 100%; padding: 13px; border: none; border-radius: 10px;
      font-size: 14px; font-weight: 600; font-family: var(--sans);
      cursor: pointer; transition: all .2s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-navy-cta { background: var(--navy); color: #fff; box-shadow: 0 4px 14px rgba(30,58,95,0.3); }
    .btn-navy-cta:hover { background: var(--navy-lt); transform: translateY(-1px); }
    .btn-teal-cta { background: var(--teal); color: #fff; box-shadow: 0 4px 14px rgba(13,148,136,0.3); }
    .btn-teal-cta:hover { background: var(--teal-dk); transform: translateY(-1px); }
    .btn-cta.loading { opacity: .65; cursor: wait; pointer-events: none; }

    .btn-back { background: none; border: none; font-size: 12px; color: var(--text3); cursor: pointer; font-family: var(--sans); display: block; text-align: center; margin-top: 13px; width: 100%; transition: color .2s; }
    .btn-back:hover { color: var(--navy); }

    /* Error & success */
    .err-msg { background: var(--red-lt); border: 1px solid rgba(224,82,82,0.2); border-radius: 8px; padding: 9px 13px; font-size: 12px; color: var(--red); margin-bottom: 14px; display: none; }
    .err-msg.show { display: block; }

    .success-wrap { text-align: center; padding: 12px 0; }
    .success-circle { width: 64px; height: 64px; border-radius: 50%; background: var(--teal-lt); margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; }
    .success-title { font-family: var(--serif); font-size: 22px; color: var(--navy); margin-bottom: 8px; }
    .success-sub { font-size: 13px; color: var(--text2); line-height: 1.7; margin-bottom: 24px; }

    @media(max-width: 768px) { .left { display: none; } .right { padding: 24px; } }
  </style>
</head>
<body>

<div class="split">

  {{-- ── LEFT BRAND PANEL ── --}}
  <div class="left">
    <div class="brand">
      <div class="brand-icon">⚡</div>
      <div>
        <div class="brand-name">Acti<em>va</em></div>
        <div class="brand-tag">Admin Control Panel</div>
      </div>
    </div>

    <div class="left-body">
      <div class="left-headline">
        Monitor &amp; kelola<br>
        <em>digital wellness</em><br>
        dengan mudah.
      </div>
      <div class="left-desc">
        Dashboard terpusat untuk memantau hasil ML, mengelola pengguna, dan mengatur rekomendasi berbasis aturan — tanpa coding.
      </div>
      <div class="feat-list">
        <div class="feat-item"><div class="feat-dot">✓</div>Login aman tanpa password (OTP)</div>
        <div class="feat-item"><div class="feat-dot">✓</div>Monitoring real-time hasil ML</div>
        <div class="feat-item"><div class="feat-dot">✓</div>Export data kuesioner (CSV/JSON)</div>
      </div>
    </div>

    <div class="left-foot">ACTIVA · SISTEM MONITORING DIGITAL WELLNESS</div>
  </div>

  {{-- ── RIGHT LOGIN CARD ── --}}
  <div class="right">
    <div class="login-card">

      {{-- Step indicator --}}
      <div class="step-ind">
        <div class="s-dot active" id="d0"></div>
        <div class="s-dot idle"   id="d1"></div>
        <div class="s-lbl" id="s-lbl">Langkah 1 dari 2</div>
      </div>

      {{-- Error message --}}
      <div class="err-msg" id="err-msg"></div>

      {{-- ── PANEL 1: Input Email ── --}}
      <div class="panel show" id="p-email">
        <div class="p-title">Masuk ke Activa</div>
        <div class="p-sub">
          Masukkan email & password admin kamu. Kode OTP akan dikirim ke email tersebut.
        </div>
        <div class="field">
          <label>Email Admin</label>
          <input type="email"
                 id="inp-email"
                 placeholder="admin@activa.id"
                 autocomplete="email">
        </div>
        <div class="field">
          <label>Password</label>
          <input type="password"
                 id="inp-password"
                 placeholder="••••••••"
                 autocomplete="current-password"
                 onkeydown="if(event.key==='Enter') kirimOTP()">
        </div>
        <button class="btn-cta btn-navy-cta" id="btn-kirim" onclick="kirimOTP()">
          <span id="btn-kirim-text">Kirim Kode OTP</span>
          <span>→</span>
        </button>
      </div>

      {{-- ── PANEL 2: Input OTP ── --}}
      <div class="panel" id="p-otp">
        <div class="p-title">Cek email kamu 📬</div>
        <div class="p-sub">
          Kode 6 digit sudah dikirim ke<br>
          <strong id="email-show">—</strong>
        </div>

        <div class="otp-row">
          <input class="otp-box" maxlength="1" type="text" inputmode="numeric">
          <input class="otp-box" maxlength="1" type="text" inputmode="numeric">
          <input class="otp-box" maxlength="1" type="text" inputmode="numeric">
          <input class="otp-box" maxlength="1" type="text" inputmode="numeric">
          <input class="otp-box" maxlength="1" type="text" inputmode="numeric">
          <input class="otp-box" maxlength="1" type="text" inputmode="numeric">
        </div>

        <div class="timer-row">
          <div class="timer-txt">Expired dalam <span id="tmr">04:59</span></div>
          <button class="resend" id="btn-resend" onclick="resendOTP()" disabled>Kirim ulang</button>
        </div>

        <button class="btn-cta btn-teal-cta" id="btn-verify" onclick="verifyOTP()">
          <span id="btn-verify-text">Verifikasi &amp; Masuk</span>
          <span>→</span>
        </button>
        <button class="btn-back" onclick="backToEmail()">← Ganti email</button>
      </div>

      {{-- ── PANEL 3: Success ── --}}
      <div class="panel" id="p-success">
        <div class="success-wrap">
          <div class="success-circle">✅</div>
          <div class="success-title">Login Berhasil!</div>
          <div class="success-sub">
            Selamat datang kembali, Admin.<br>
            Kamu akan diarahkan ke dashboard Activa.
          </div>
          <a href="/admin/dashboard" class="btn-cta btn-teal-cta" style="text-decoration:none">
            Masuk ke Dashboard →
          </a>
        </div>
      </div>

    </div>
  </div>

</div>

<script>
let sisa = 299, interval = null;

// ── Kirim OTP ke API ──
function kirimOTP() {
  const email = document.getElementById('inp-email').value.trim();
  const password = document.getElementById('inp-password').value.trim();
  
  if (!email || !email.includes('@')) {
    tampilError('Masukkan email yang valid ya.');
    return;
  }
  
  if (!password || password.length < 6) {
    tampilError('Masukkan password dengan benar.');
    return;
  }
  
  sembunyiError();

  const btn  = document.getElementById('btn-kirim');
  const text = document.getElementById('btn-kirim-text');
  btn.classList.add('loading');
  text.textContent = 'Mengirim...';

  // Kirim request ke API endpoint
  fetch('/api/admin/request-otp', {
    method:  'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({ email, password }),
  })
  .then(res => res.json())
  .then(data => {
    btn.classList.remove('loading');
    text.textContent = 'Kirim Kode OTP';
    if (data.success) {
      document.getElementById('email-show').textContent = email;
      window.adminEmail = email; // Simpan untuk step berikutnya
      goPanel('otp');
      mulaiTimer();
      document.querySelector('.otp-box').focus();
    } else {
      tampilError(data.message ?? 'Gagal mengirim OTP, coba lagi.');
    }
  })
  .catch(() => {
    btn.classList.remove('loading');
    text.textContent = 'Kirim Kode OTP';
    tampilError('Koneksi gagal, periksa server kamu.');
  });
}

// ── Verifikasi OTP ──
function verifyOTP() {
  const boxes = document.querySelectorAll('.otp-box');
  const otp   = Array.from(boxes).map(b => b.value).join('');
  const email = document.getElementById('email-show').textContent;

  if (otp.length < 6) {
    tampilError('Masukkan 6 digit kode OTP.');
    return;
  }
  sembunyiError();

  const btn  = document.getElementById('btn-verify');
  const text = document.getElementById('btn-verify-text');
  btn.classList.add('loading');
  text.textContent = 'Memverifikasi...';

  fetch('/api/admin/verify-otp', {
    method:  'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({ email, otp_code: otp }),
  })
  .then(res => res.json())
  .then(data => {
    btn.classList.remove('loading');
    text.textContent = 'Verifikasi & Masuk';
    if (data.success && data.data && data.data.token) {
      clearInterval(interval);
      goPanel('success');
      
      // Simpan token ke localStorage
      localStorage.setItem('admin_token', data.data.token);
      localStorage.setItem('admin_token_type', data.data.token_type || 'bearer');
      
      // Redirect ke dashboard setelah 1.5 detik
      setTimeout(() => window.location.href = '/admin/dashboard', 1500);
    } else {
      tampilError(data.message ?? 'OTP salah atau sudah expired.');
    }
  })
  .catch(() => {
    btn.classList.remove('loading');
    text.textContent = 'Verifikasi & Masuk';
    tampilError('Koneksi gagal, coba lagi.');
  });
}

// ── Kirim ulang OTP ──
function resendOTP() {
  sisa = 299;
  mulaiTimer();
  document.getElementById('btn-resend').disabled = true;
  kirimOTP();
}

// ── Kembali ke input email ──
function backToEmail() {
  clearInterval(interval);
  goPanel('email');
  document.querySelectorAll('.otp-box').forEach(b => {
    b.value = '';
    b.classList.remove('filled');
  });
}

// ── Countdown timer ──
function mulaiTimer() {
  clearInterval(interval);
  interval = setInterval(() => {
    sisa--;
    const m = String(Math.floor(sisa / 60)).padStart(2, '0');
    const s = String(sisa % 60).padStart(2, '0');
    document.getElementById('tmr').textContent = `${m}:${s}`;
    if (sisa <= 0) {
      clearInterval(interval);
      document.getElementById('tmr').textContent = '00:00';
      document.getElementById('btn-resend').disabled = false;
    }
  }, 1000);
}

// ── OTP box auto-focus & auto-verify ──
document.querySelectorAll('.otp-box').forEach((box, i, all) => {
  box.addEventListener('input', () => {
    box.value = box.value.replace(/\D/g, '');
    if (box.value) {
      box.classList.add('filled');
      if (i < all.length - 1) all[i + 1].focus();
    } else {
      box.classList.remove('filled');
    }
    // Auto verify kalau semua 6 kotak terisi
    if (Array.from(all).every(b => b.value)) {
      setTimeout(verifyOTP, 300);
    }
  });
  box.addEventListener('keydown', e => {
    if (e.key === 'Backspace' && !box.value && i > 0) all[i - 1].focus();
  });
});

// ── Helpers ──
function goPanel(name) {
  ['email', 'otp', 'success'].forEach(n => {
    document.getElementById('p-' + n).classList.remove('show');
  });
  document.getElementById('p-' + name).classList.add('show');

  const step = name === 'email' ? 0 : name === 'otp' ? 1 : 2;
  ['d0', 'd1'].forEach((id, idx) => {
    document.getElementById(id).className =
      's-dot ' + (idx < step ? 'done' : idx === step ? 'active' : 'idle');
  });
  document.getElementById('s-lbl').textContent =
    `Langkah ${Math.min(step + 1, 2)} dari 2`;
}

function tampilError(msg) {
  const el = document.getElementById('err-msg');
  el.textContent = msg;
  el.classList.add('show');
}
function sembunyiError() {
  document.getElementById('err-msg').classList.remove('show');
}
</script>

</body>
</html>