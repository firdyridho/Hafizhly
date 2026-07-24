<?php
session_start(); // Wajib dipanggil pertama kali untuk memulai sesi
require_once 'config/database.php';

$pesan = '';

// Jika user sudah login, langsung arahkan ke dashboard masing-masing
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cari user berdasarkan email
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        // Cek kecocokan password
        if (password_verify($password, $user['password'])) {
            // Set session jika berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            // Arahkan berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $pesan = "salah";
        }
    } else {
        $pesan = "tidak_ada";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#059669">
    <title>Sign In - Hafizhly</title>
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
            background: radial-gradient(circle, var(--primary-light), transparent 70%);
            top: -100px;
            left: -90px;
        }

        .aurora span:nth-child(2) {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, var(--gold-light), transparent 70%);
            bottom: -120px;
            right: -100px;
            opacity: 0.2;
            animation-delay: -6s;
        }

        @keyframes drift {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(25px, -30px) scale(1.06);
            }
        }

        /* ===== Layout ===== */
        .auth-wrap {
            display: flex;
            min-height: 100vh;
        }

        /* Panel kiri: dekoratif, hanya tampil di desktop */
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
            background: radial-gradient(circle, rgba(5, 150, 105, 0.4), transparent 70%);
            top: -80px;
            right: -80px;
        }

        .auth-side::after {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201, 162, 39, 0.28), transparent 70%);
            bottom: -60px;
            left: -60px;
        }

        .auth-side-arabic {
            position: absolute;
            font-family: 'Amiri', serif;
            font-size: 6rem;
            color: rgba(255, 255, 255, 0.06);
            top: 40px;
            left: 40px;
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

        .auth-side-waveform {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-end;
            gap: 5px;
            height: 50px;
            margin: 30px 0;
        }

        .auth-side-waveform span {
            width: 5px;
            border-radius: 4px;
            background: linear-gradient(180deg, var(--primary-light), var(--gold-light));
            animation: bar 1.2s ease-in-out infinite;
        }

        @keyframes bar {

            0%,
            100% {
                height: 8px;
            }

            50% {
                height: 40px;
            }
        }

        .auth-side-foot {
            position: relative;
            z-index: 2;
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Panel kanan: form */
        .auth-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 400px;
            padding: 34px 30px;
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

        /* Logo: kotak lembut, BUKAN lingkaran */
        .logo-frame {
            width: 76px;
            height: 76px;
            margin: 0 auto 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-frame img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card h2 {
            color: var(--dark);
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .card .subtitle {
            color: var(--muted);
            font-size: 0.87rem;
            margin-bottom: 22px;
        }

        .form-group {
            margin-bottom: 16px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.83rem;
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
            font-size: 0.9rem;
            transition: color 0.25s ease;
            pointer-events: none;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            outline: none;
            font-size: 0.92rem;
            background: #fff;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .form-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12);
        }

        .form-group input:focus+i.field-icon,
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
            font-size: 0.9rem;
            padding: 4px;
        }

        .toggle-password:hover {
            color: var(--primary);
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
            font-size: 0.98rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 6px;
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
            margin-top: 20px;
            font-size: 0.87rem;
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
            font-size: 0.85rem;
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

        @media (min-width: 992px) {
            .auth-side {
                display: flex;
            }
        }

        @media (max-width: 480px) {
            .card {
                padding: 28px 22px;
            }

            .logo-frame {
                width: 64px;
                height: 64px;
            }
        }
    </style>
</head>

<body>

    <div class="aurora"><span></span><span></span></div>

    <div class="auth-wrap">
        <!-- Panel dekoratif (desktop) -->
        <div class="auth-side">
            <div class="auth-side-arabic">القرآن</div>

            <div class="auth-side-brand">
                Hafizhly
            </div>

            <div class="auth-side-content">
                <h2>Jaga Hafalanmu, <br>Setiap Hari.</h2>
                <p>Masuk untuk melanjutkan murojaah dan pantau progres hafalanmu bersama AI Coach pribadi.</p>

                <div class="auth-side-waveform" id="sideWaveform"></div>
            </div>

            <div class="auth-side-foot">&copy; <?= date('Y'); ?> Hafizhly. Pendamping Murojaah Al-Qur'an Berbasis AI.</div>
        </div>

        <!-- Panel form -->
        <div class="auth-form-panel">
            <div class="card">
                <div class="logo-frame">
                    <img src="assets/icon/logo_kecil.png" alt="Logo Hafizhly" id="app-logo">
                </div>
                <h2>Selamat Datang Kembali</h2>
                <div class="subtitle">Masuk untuk melanjutkan murojaahmu</div>

                <?php if ($pesan === 'salah'): ?>
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Password salah!</div>
                <?php elseif ($pesan === 'tidak_ada'): ?>
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> Email tidak ditemukan!</div>
                <?php endif; ?>

                <form action="" method="POST" onsubmit="tampilkanLoading()">
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
                            <input type="password" id="password" name="password" required placeholder="Masukkan password">
                            <button type="button" class="toggle-password" id="togglePassword" tabindex="-1">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn" id="btn-login">
                        <span id="btn-text">Masuk</span>
                    </button>
                </form>

                <div class="link">
                    Belum punya akun? <a href="register.php" class="auth-transition-link">Daftar sekarang</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Waveform dekoratif di panel kiri
        const sideWaveform = document.getElementById('sideWaveform');
        if (sideWaveform) {
            for (let i = 0; i < 26; i++) {
                const bar = document.createElement('span');
                bar.style.animationDelay = (i * 0.05) + 's';
                bar.style.animationDuration = (0.9 + Math.random() * 0.6) + 's';
                sideWaveform.appendChild(bar);
            }
        }

        // Toggle tampil/sembunyikan password
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        togglePassword.addEventListener('click', function() {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            this.innerHTML = isHidden ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
        });

        // Loading state saat submit
        function tampilkanLoading() {
            const btn = document.getElementById('btn-login');
            const btnText = document.getElementById('btn-text');
            btnText.innerHTML = '<i class="fa-solid fa-spinner"></i> Memeriksa...';

            // Beri jeda sedikit sebelum tombol dinonaktifkan agar data form tetap terkirim ke PHP
            setTimeout(function() {
                btn.disabled = true;
            }, 10);
        }

        // Efek ripple pada tombol
        document.getElementById('btn-login').addEventListener('click', function(e) {
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

        // Transisi halus saat pindah ke halaman register
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