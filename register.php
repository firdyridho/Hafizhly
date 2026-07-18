<?php
// 1. Panggil koneksi database
require_once 'config/database.php';

$pesan = '';
$pesanTipe = ''; // 'sukses' atau 'gagal'

// 2. Cek apakah tombol "Daftar" sudah ditekan
if (isset($_POST['register'])) {
    // Ambil data dari form dan bersihkan dari karakter berbahaya
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Enkripsi password agar aman di database
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Role default untuk pendaftar baru adalah 'user'
    $role = 'user';

    // Cek apakah email sudah pernah didaftarkan
    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $pesan = 'Email sudah terdaftar! Gunakan email lain.';
        $pesanTipe = 'gagal';
    } else {
        // Jika email belum ada, masukkan data ke tabel users
        $query = "INSERT INTO users (nama_lengkap, email, password, role) 
                  VALUES ('$nama_lengkap', '$email', '$password_hashed', '$role')";

        if (mysqli_query($conn, $query)) {
            $pesan = 'Pendaftaran berhasil! Silakan masuk dengan akun barumu.';
            $pesanTipe = 'sukses';
        } else {
            $pesan = 'Terjadi kesalahan: ' . mysqli_error($conn);
            $pesanTipe = 'gagal';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#059669">
    <title>Sign Up - Hafizhly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #059669;
            --primary-dark: #04785a;
            --primary-light: #34d399;
            --gold: #c9a227;
            --gold-light: #e8c85f;
            --dark: #0f172a;
            --muted: #64748b;
            --bg: #f7faf8;
            --border: #e5e7eb;
            --glow: rgba(5, 150, 105, 0.35);
            --danger: #ef4444;
            --warn: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        h1,
        h2,
        h3,
        .display-font {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            min-height: 100vh;
            overflow-x: hidden;
            transition: opacity 0.32s ease, transform 0.32s ease;
        }

        body.leaving {
            opacity: 0;
            transform: translateY(-8px) scale(0.99);
        }

        /* ===== Ambient aurora background ===== */
        .aurora {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
        }

        .aurora span {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.3;
            animation: drift 16s ease-in-out infinite;
        }

        .aurora span:nth-child(1) {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, var(--gold-light), transparent 70%);
            top: -100px;
            right: -90px;
            opacity: 0.2;
        }

        .aurora span:nth-child(2) {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, var(--primary-light), transparent 70%);
            bottom: -120px;
            left: -100px;
            animation-delay: -6s;
        }

        @keyframes drift {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-25px, 25px) scale(1.06);
            }
        }

        /* ===== Layout ===== */
        .auth-wrap {
            display: flex;
            min-height: 100vh;
        }

        .auth-side {
            display: none;
            position: relative;
            flex: 1;
            background: linear-gradient(150deg, var(--dark), #0f2e22 55%, var(--primary-dark));
            color: #fff;
            padding: 60px;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .auth-side::before {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201, 162, 39, 0.32), transparent 70%);
            top: -80px;
            left: -80px;
        }

        .auth-side::after {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.35), transparent 70%);
            bottom: -60px;
            right: -60px;
        }

        .auth-side-arabic {
            position: absolute;
            font-family: 'Amiri', serif;
            font-size: 6rem;
            color: rgba(255, 255, 255, 0.06);
            top: 40px;
            right: 40px;
            white-space: nowrap;
        }

        .auth-side-brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.4rem;
        }

        .auth-side-content {
            position: relative;
            z-index: 2;
        }

        .auth-side-content h2 {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 16px;
        }

        .auth-side-content p {
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.95rem;
            line-height: 1.7;
            max-width: 380px;
        }

        .auth-side-steps {
            position: relative;
            z-index: 2;
            margin-top: 26px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .auth-side-steps .step {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .auth-side-steps .step i {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-light);
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .auth-side-foot {
            position: relative;
            z-index: 2;
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.5);
        }

        .auth-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
            padding: 32px 30px;
            border-radius: 26px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-align: center;
            opacity: 0;
            animation: cardIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .brand-mark-wrap {
            width: 58px;
            height: 58px;
            margin: 0 auto 14px;
            border-radius: 17px 6px 17px 17px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            box-shadow: 0 12px 26px var(--glow);
        }

        .card h2 {
            color: var(--dark);
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .card .subtitle {
            color: var(--muted);
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--dark);
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap i.field-icon {
            position: absolute;
            left: 14px;
            color: var(--muted);
            font-size: 0.88rem;
            pointer-events: none;
            transition: color 0.25s ease;
        }

        .form-group input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            outline: none;
            font-size: 0.9rem;
            background: #fff;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .form-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12);
        }

        .input-wrap:focus-within i.field-icon {
            color: var(--primary);
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 0.88rem;
            padding: 4px;
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        /* ===== Password strength meter ===== */
        .strength-meter {
            margin-top: 9px;
        }

        .strength-bar {
            display: flex;
            gap: 4px;
            height: 4px;
            margin-bottom: 6px;
        }

        .strength-bar span {
            flex: 1;
            border-radius: 3px;
            background: var(--border);
            transition: background 0.3s ease;
        }

        .strength-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--muted);
            transition: color 0.3s ease;
        }

        .strength-checklist {
            list-style: none;
            margin-top: 9px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .strength-checklist li {
            font-size: 0.74rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 7px;
            transition: color 0.25s ease;
        }

        .strength-checklist li i {
            font-size: 0.7rem;
            width: 13px;
        }

        .strength-checklist li.met {
            color: var(--primary-dark);
        }

        .match-hint {
            margin-top: 7px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
            min-height: 16px;
        }

        .match-hint.ok {
            color: var(--primary-dark);
        }

        .match-hint.bad {
            color: var(--danger);
        }

        /* ===== Persetujuan kebijakan privasi & syarat ketentuan ===== */
        .policy-box {
            margin-bottom: 15px;
            padding: 14px 16px;
            border: 1.5px solid var(--border);
            border-radius: 14px;
            background: #fbfdfc;
            transition: border-color 0.3s ease, background 0.3s ease;
        }

        .policy-box.unlocked {
            border-color: rgba(5, 150, 105, 0.35);
            background: rgba(5, 150, 105, 0.04);
        }

        .policy-links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .policy-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--dark);
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .policy-link i.link-icon {
            color: var(--primary);
            font-size: 0.8rem;
        }

        .policy-link i.ext-icon {
            font-size: 0.68rem;
            color: var(--muted);
            transition: all 0.25s ease;
        }

        .policy-link:hover {
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .policy-checkbox-wrap {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            font-size: 0.8rem;
            color: var(--dark);
            line-height: 1.5;
            cursor: pointer;
        }

        .policy-checkbox-wrap input[type="checkbox"] {
            margin-top: 2px;
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            flex-shrink: 0;
            cursor: pointer;
        }

        .policy-hint {
            margin-top: 8px;
            font-size: 0.72rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .policy-hint.ready {
            color: var(--primary-dark);
        }

        .policy-hint.error {
            color: var(--danger);
        }

        .policy-box.shake {
            animation: policyShake 0.4s ease;
        }

        @keyframes policyShake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .btn {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.96rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 12px 24px var(--glow);
            transition: all 0.3s ease;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px var(--glow);
        }

        .btn:disabled {
            background: #9ca3af;
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn .fa-spinner {
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: scale(0);
            animation: rippleAnim 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes rippleAnim {
            to {
                transform: scale(3);
                opacity: 0;
            }
        }

        .link {
            display: block;
            margin-top: 18px;
            font-size: 0.86rem;
            color: var(--muted);
        }

        .link a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            text-align: left;
            font-size: 0.83rem;
            animation: shakeIn 0.4s ease;
        }

        @keyframes shakeIn {
            0% {
                opacity: 0;
                transform: translateX(-8px);
            }

            50% {
                transform: translateX(4px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-success a {
            color: var(--primary-dark);
            font-weight: 700;
        }

        @media (min-width: 992px) {
            .auth-side {
                display: flex;
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 26px 20px;
                border-radius: 22px;
            }
        }
    </style>
</head>

<body>

    <div class="aurora"><span></span><span></span></div>

    <div class="auth-wrap">
        <!-- Panel dekoratif (desktop) -->
        <div class="auth-side">
            <div class="auth-side-arabic">اقرأ</div>

            <div class="auth-side-brand">
                Hafizhly
            </div>

            <div class="auth-side-content">
                <h2>Mulai Perjalanan <br>Menjaga Hafalanmu.</h2>
                <p>Buat akun gratis dan rasakan pengalaman murojaah yang lebih interaktif bersama AI Coach pribadi.</p>

                <div class="auth-side-steps">
                    <div class="step"><i class="fa-solid fa-microphone-lines"></i> Murojaah dengan koreksi suara real-time</div>
                    <div class="step"><i class="fa-solid fa-chart-simple"></i> Pantau progres hafalan harianmu</div>
                    <div class="step"><i class="fa-solid fa-book-open-reader"></i> Akses E-Qur'an lengkap 114 surat</div>
                </div>
            </div>

            <div class="auth-side-foot">&copy; <?= date('Y'); ?> Hafizhly. Pendamping Murojaah Al-Qur'an Berbasis AI.</div>
        </div>

        <!-- Panel form -->
        <div class="auth-form-panel">
            <div class="card">
                <h2>Buat Akun Baru</h2>
                <div class="subtitle">Gratis untuk memulai, cukup niat dan konsistensi</div>

                <?php if ($pesanTipe === 'gagal'): ?>
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($pesan) ?></div>
                <?php elseif ($pesanTipe === 'sukses'): ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($pesan) ?> <a href="login.php" class="auth-transition-link">Sign In di sini</a></div>
                <?php endif; ?>

                <form action="" method="POST" id="registerForm" onsubmit="return validasiForm()">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user field-icon"></i>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Masukkan nama lengkap">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope field-icon"></i>
                            <input type="email" id="email" name="email" required placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock field-icon"></i>
                            <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter">
                            <button type="button" class="toggle-password" data-target="password" tabindex="-1">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        <div class="strength-meter">
                            <div class="strength-bar" id="strengthBar">
                                <span></span><span></span><span></span><span></span>
                            </div>
                            <div class="strength-label" id="strengthLabel">Masukkan password</div>
                        </div>

                        <ul class="strength-checklist" id="strengthChecklist">
                            <li data-rule="length"><i class="fa-regular fa-circle"></i> Minimal 6 karakter</li>
                            <li data-rule="upper"><i class="fa-regular fa-circle"></i> Kombinasi huruf besar &amp; kecil</li>
                            <li data-rule="number"><i class="fa-regular fa-circle"></i> Mengandung angka</li>
                            <li data-rule="symbol"><i class="fa-regular fa-circle"></i> Karakter spesial (nilai plus)</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label for="konfirmasi_password">Konfirmasi Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock field-icon"></i>
                            <input type="password" id="konfirmasi_password" required placeholder="Ketik ulang password">
                            <button type="button" class="toggle-password" data-target="konfirmasi_password" tabindex="-1">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <div class="match-hint" id="matchHint"></div>
                    </div>

                    <div class="policy-box" id="policyBox">
                        <div class="policy-links">
                            <a href="privacy.php" target="_blank" rel="noopener" class="policy-link" data-policy="privacy" id="linkPrivacy">
                                <i class="fa-solid fa-shield-halved link-icon"></i> Kebijakan Privasi <i class="fa-solid fa-arrow-up-right-from-square ext-icon"></i>
                            </a>
                            <a href="terms.php" target="_blank" rel="noopener" class="policy-link" data-policy="terms" id="linkTerms">
                                <i class="fa-solid fa-file-contract link-icon"></i> Syarat &amp; Ketentuan <i class="fa-solid fa-arrow-up-right-from-square ext-icon"></i>
                            </a>
                        </div>

                        <label class="policy-checkbox-wrap" id="policyCheckboxWrap" for="agreePolicy">
                            <input type="checkbox" id="agreePolicy">
                            <span>Saya sudah membaca dan menyetujui <strong>Kebijakan Privasi</strong> dan <strong>Syarat &amp; Ketentuan</strong> Hafizhly.</span>
                        </label>
                    </div>

                    <button type="submit" name="register" class="btn" id="btn-register">
                        <span id="btn-text">Daftar Sekarang</span>
                    </button>
                </form>

                <div class="link">
                    Sudah punya akun? <a href="login.php" class="auth-transition-link">Sign In di sini</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ===== Toggle tampil/sembunyikan password (berlaku untuk kedua field) =====
        document.querySelectorAll('.toggle-password').forEach((btn) => {
            btn.addEventListener('click', function() {
                const target = document.getElementById(this.dataset.target);
                const isHidden = target.type === 'password';
                target.type = isHidden ? 'text' : 'password';
                this.innerHTML = isHidden ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
            });
        });

        // ===== Password strength meter =====
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar').querySelectorAll('span');
        const strengthLabel = document.getElementById('strengthLabel');
        const checklistItems = document.querySelectorAll('#strengthChecklist li');

        const levels = [{
                label: 'Masukkan password',
                color: '#e5e7eb'
            },
            {
                label: 'Lemah',
                color: '#ef4444'
            },
            {
                label: 'Sedang',
                color: '#f59e0b'
            },
            {
                label: 'Kuat',
                color: '#10b981'
            },
            {
                label: 'Sangat Kuat',
                color: '#059669'
            }
        ];

        function evaluatePassword(value) {
            const rules = {
                length: value.length >= 6,
                upper: /[a-z]/.test(value) && /[A-Z]/.test(value),
                number: /[0-9]/.test(value),
                symbol: /[^A-Za-z0-9]/.test(value)
            };
            const score = Object.values(rules).filter(Boolean).length;
            return {
                rules,
                score
            };
        }

        passwordInput.addEventListener('input', function() {
            const value = this.value;
            const {
                rules,
                score
            } = evaluatePassword(value);
            const level = value.length === 0 ? levels[0] : levels[score] || levels[1];

            strengthBar.forEach((seg, i) => {
                seg.style.background = (i < score && value.length > 0) ? level.color : '#e5e7eb';
            });
            strengthLabel.textContent = level.label;
            strengthLabel.style.color = value.length === 0 ? '#64748b' : level.color;

            checklistItems.forEach((li) => {
                const rule = li.dataset.rule;
                const met = rules[rule];
                li.classList.toggle('met', met);
                li.querySelector('i').className = met ? 'fa-solid fa-circle-check' : 'fa-regular fa-circle';
            });

            checkMatch();
        });

        // ===== Cek kecocokan konfirmasi password secara live =====
        const confirmInput = document.getElementById('konfirmasi_password');
        const matchHint = document.getElementById('matchHint');

        function checkMatch() {
            const pass = passwordInput.value;
            const confirm = confirmInput.value;

            if (confirm.length === 0) {
                matchHint.textContent = '';
                matchHint.className = 'match-hint';
                return;
            }

            if (pass === confirm) {
                matchHint.innerHTML = '<i class="fa-solid fa-circle-check"></i> Password cocok';
                matchHint.className = 'match-hint ok';
            } else {
                matchHint.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Password belum sama';
                matchHint.className = 'match-hint bad';
            }
        }

        confirmInput.addEventListener('input', checkMatch);

        // ===== Persetujuan cukup dengan centang langsung (tautan tersedia sebagai referensi) =====
        const policyBox = document.getElementById('policyBox');
        const agreeCheckbox = document.getElementById('agreePolicy');

        // ===== Validasi sebelum submit (tanpa alert() native, biar tetap premium) =====
        function validasiForm() {
            if (passwordInput.value !== confirmInput.value) {
                checkMatch();
                confirmInput.focus();
                confirmInput.style.borderColor = '#ef4444';
                setTimeout(() => {
                    confirmInput.style.borderColor = '';
                }, 1500);
                return false;
            }

            if (!agreeCheckbox.checked) {
                policyBox.classList.add('shake');
                setTimeout(() => policyBox.classList.remove('shake'), 400);
                policyBox.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                return false;
            }

            const btn = document.getElementById('btn-register');
            const btnText = document.getElementById('btn-text');
            btnText.innerHTML = '<i class="fa-solid fa-spinner"></i> Memproses...';
            setTimeout(function() {
                btn.disabled = true;
            }, 10);
            return true;
        }

        // ===== Efek ripple pada tombol =====
        document.getElementById('btn-register').addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const ripple = document.createElement('span');
            ripple.className = 'ripple';
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 650);
        });

        // ===== Transisi halus saat pindah ke halaman login =====
        document.querySelectorAll('.auth-transition-link').forEach((link) => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (!href || href.startsWith('#')) return;
                e.preventDefault();
                document.body.classList.add('leaving');
                setTimeout(() => {
                    window.location.href = href;
                }, 300);
            });
        });
    </script>

</body>

</html>