<?php
session_start();
require_once 'config/database.php';

$email = $_GET['email'] ?? $_POST['email'] ?? '';
$pesan = '';
$pesanTipe = '';
$step = 'kode';

if (isset($_POST['verifikasi_kode'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($query) === 1) {
        $user_data = mysqli_fetch_assoc($query);
        $now = date('Y-m-d H:i:s');

        if ($user_data['reset_code'] === $kode && $user_data['reset_expiry'] >= $now) {
            $step = 'password';
        } else {
            $pesan = 'Kode verifikasi salah atau sudah kedaluwarsa.';
            $pesanTipe = 'gagal';
        }
    } else {
        $pesan = 'Email tidak ditemukan.';
        $pesanTipe = 'gagal';
    }
}

if (isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);
    $password_baru = mysqli_real_escape_string($conn, $_POST['password']);
    $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        $now = date('Y-m-d H:i:s');

        if ($user['reset_code'] === $kode && $user['reset_expiry'] >= $now) {
            mysqli_query($conn, "UPDATE users SET password = '$password_hashed', reset_code = NULL, reset_expiry = NULL WHERE email = '$email'");
            $pesan = 'Password berhasil direset! Silakan masuk dengan password baru.';
            $pesanTipe = 'sukses';
            $step = 'selesai';
        } else {
            $pesan = 'Kode verifikasi salah atau sudah kedaluwarsa.';
            $pesanTipe = 'gagal';
        }
    } else {
        $pesan = 'Email tidak ditemukan.';
        $pesanTipe = 'gagal';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#059669">
    <title>Reset Password - Hifzhly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #059669; --primary-dark: #04785a; --primary-light: #34d399; --dark: #0f172a; --muted: #64748b; --bg: #f7faf8; --border: #e5e7eb; --glow: rgba(5, 150, 105, 0.35); }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        h1, h2, h3, .display-font { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        body { background-color: var(--bg); color: var(--dark); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 420px; padding: 34px 30px; text-align: center; opacity: 0; animation: cardIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes cardIn { from { opacity: 0; transform: translateY(24px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .icon-big { width: 68px; height: 68px; margin: 0 auto 18px; background: linear-gradient(135deg, var(--primary-light), var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.4rem; box-shadow: 0 14px 28px var(--glow); }
        h2 { font-size: 1.35rem; font-weight: 800; margin-bottom: 6px; }
        .subtitle { color: var(--muted); font-size: 0.87rem; margin-bottom: 22px; line-height: 1.6; }
        .form-group { margin-bottom: 16px; text-align: left; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 0.83rem; font-weight: 600; color: var(--dark); }
        .input-wrap { position: relative; display: flex; align-items: center; }
        .input-wrap i.field-icon { position: absolute; left: 14px; color: var(--muted); font-size: 0.9rem; pointer-events: none; }
        .form-group input { width: 100%; padding: 12px 14px 12px 40px; border: 1.5px solid var(--border); border-radius: 12px; outline: none; font-size: 0.92rem; background: #fff; transition: border-color 0.25s ease, box-shadow 0.25s ease; }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12); }
        .input-wrap:focus-within i.field-icon { color: var(--primary); }
        .form-group input.kode-input { text-align: center; letter-spacing: 10px; font-size: 1.5rem; font-weight: 700; padding: 13px; }
        .toggle-password { position: absolute; right: 12px; background: none; border: none; color: var(--muted); cursor: pointer; font-size: 0.9rem; padding: 4px; }
        .btn { width: 100%; padding: 13px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border: none; border-radius: 12px; font-size: 0.96rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 12px 24px var(--glow); transition: all 0.3s ease; margin-top: 6px; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 16px 28px var(--glow); }
        .alert { display: flex; align-items: center; gap: 8px; padding: 11px 14px; border-radius: 12px; margin-bottom: 16px; text-align: left; font-size: 0.85rem; }
        .alert-danger { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-success a { color: var(--primary-dark); font-weight: 700; }
        .link { margin-top: 20px; font-size: 0.86rem; color: var(--muted); }
        .link a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($step === 'kode'): ?>
            <div class="icon-big"><i class="fa-solid fa-shield-halved"></i></div>
            <h2>Verifikasi Kode</h2>
            <div class="subtitle">Masukkan kode 6 digit yang telah dikirim ke<br><strong><?= htmlspecialchars($email) ?></strong></div>

            <?php if ($pesanTipe === 'gagal'): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($pesan) ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <div class="form-group">
                    <label for="kode">Kode Verifikasi</label>
                    <input type="text" id="kode" name="kode" class="kode-input" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required placeholder="000000">
                </div>
                <button type="submit" name="verifikasi_kode" class="btn">Verifikasi Kode</button>
            </form>

            <div class="link">
                <a href="forgot-password.php">&larr; Kirim ulang kode</a>
            </div>

        <?php elseif ($step === 'password'): ?>
            <div class="icon-big"><i class="fa-solid fa-lock"></i></div>
            <h2>Password Baru</h2>
            <div class="subtitle">Buat password baru untuk akun kamu.</div>

            <?php if ($pesanTipe === 'gagal'): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($pesan) ?></div>
            <?php endif; ?>

            <form action="" method="POST" onsubmit="return validasiPassword()">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <input type="hidden" name="kode" value="<?= htmlspecialchars($_POST['kode'] ?? ($_GET['kode'] ?? '')) ?>">
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter" minlength="6">
                        <button type="button" class="toggle-password" data-target="password" tabindex="-1">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="konfirmasi">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" id="konfirmasi" required placeholder="Ketik ulang password">
                        <button type="button" class="toggle-password" data-target="konfirmasi" tabindex="-1">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" name="reset_password" class="btn">Reset Password</button>
            </form>

        <?php else: ?>
            <div class="icon-big" style="background: linear-gradient(135deg, #34d399, #059669);"><i class="fa-solid fa-circle-check"></i></div>
            <h2>Password Berhasil Direset</h2>
            <div class="subtitle">Password kamu sudah diperbarui. Silakan masuk dengan password baru.</div>
            <a href="login.php" class="btn" style="text-decoration: none; margin-top: 10px;">Masuk Sekarang</a>
        <?php endif; ?>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach((btn) => {
            btn.addEventListener('click', function() {
                const target = document.getElementById(this.dataset.target);
                const isHidden = target.type === 'password';
                target.type = isHidden ? 'text' : 'password';
                this.innerHTML = isHidden ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
            });
        });
        document.getElementById('kode')?.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').substring(0, 6);
        });
        function validasiPassword() {
            const pass = document.getElementById('password').value;
            const konf = document.getElementById('konfirmasi').value;
            if (pass.length < 6) {
                alert('Password minimal 6 karakter!');
                return false;
            }
            if (pass !== konf) {
                alert('Password dan konfirmasi tidak sama!');
                return false;
            }
            return true;
        }
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = this.querySelector('button[type="submit"]');
                if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner"></i> Memproses...'; }
            });
        });
    </script>
</body>
</html>
