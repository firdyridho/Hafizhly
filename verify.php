<?php
session_start();
require_once 'config/database.php';

$email = $_GET['email'] ?? $_POST['email'] ?? '';
$pesan = '';
$pesanTipe = '';

if (isset($_POST['verify'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $kode = mysqli_real_escape_string($conn, $_POST['kode']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND is_verified = 0");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        $now = date('Y-m-d H:i:s');

        if ($user['verification_code'] === $kode && $user['verification_expiry'] >= $now) {
            mysqli_query($conn, "UPDATE users SET is_verified = 1, verification_code = NULL, verification_expiry = NULL WHERE email = '$email'");
            $pesan = 'Email berhasil diverifikasi! Silakan masuk.';
            $pesanTipe = 'sukses';
        } else {
            $pesan = 'Kode verifikasi salah atau sudah kedaluwarsa.';
            $pesanTipe = 'gagal';
        }
    } else {
        $pesan = 'Email tidak ditemukan atau sudah diverifikasi.';
        $pesanTipe = 'gagal';
    }
}

if (isset($_POST['kirim_ulang'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND is_verified = 0");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        $kode_baru = sprintf("%06d", random_int(0, 999999));
        $expiry_baru = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        mysqli_query($conn, "UPDATE users SET verification_code = '$kode_baru', verification_expiry = '$expiry_baru' WHERE email = '$email'");

        require_once 'config/email.php';
        $subject = 'Kode Verifikasi Baru - Hifzhly';
        $body = "
        <div style='font-family: sans-serif; max-width: 480px; margin: 0 auto; padding: 30px 20px; background: #f7faf8; border-radius: 20px;'>
            <div style='text-align: center; margin-bottom: 24px;'>
                <h2 style='color: #0f172a; margin-top: 10px; font-size: 1.3rem;'>Kode Verifikasi Baru</h2>
            </div>
            <p style='color: #475569; font-size: 0.95rem; line-height: 1.6;'>Berikut kode verifikasi yang baru:</p>
            <div style='text-align: center; margin: 28px 0;'>
                <span style='display: inline-block; background: #ffffff; border: 2px dashed #059669; border-radius: 16px; padding: 16px 40px; font-size: 2.2rem; font-weight: 800; letter-spacing: 10px; color: #059669;'>$kode_baru</span>
            </div>
            <p style='color: #64748b; font-size: 0.85rem;'>Kode ini berlaku selama <b>15 menit</b>.</p>
        </div>";

        if (kirim_email($email, $subject, $body)) {
            $pesan = 'Kode baru telah dikirim ke email kamu.';
            $pesanTipe = 'sukses';
        } else {
            $pesan = 'Gagal mengirim ulang kode. Coba lagi.';
            $pesanTipe = 'gagal';
        }
    } else {
        $pesan = 'Email tidak ditemukan atau sudah diverifikasi.';
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
    <title>Verifikasi Email - Hifzhly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #04785a;
            --primary-light: #34d399;
            --dark: #0f172a;
            --muted: #64748b;
            --bg: #f7faf8;
            --border: #e5e7eb;
            --glow: rgba(5, 150, 105, 0.35);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        h1, h2, h3, .display-font { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
        body { background-color: var(--bg); color: var(--dark); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 440px; padding: 36px 32px; text-align: center; opacity: 0; animation: cardIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes cardIn { from { opacity: 0; transform: translateY(24px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .icon-big { width: 72px; height: 72px; margin: 0 auto 20px; background: linear-gradient(135deg, var(--primary-light), var(--primary)); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.6rem; box-shadow: 0 14px 28px var(--glow); }
        h2 { font-size: 1.35rem; font-weight: 800; margin-bottom: 6px; }
        .subtitle { color: var(--muted); font-size: 0.88rem; margin-bottom: 24px; line-height: 1.6; }
        .subtitle strong { color: var(--dark); }
        .form-group { margin-bottom: 18px; text-align: left; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 0.83rem; font-weight: 600; color: var(--dark); }
        .form-group input { width: 100%; padding: 13px 16px; border: 1.5px solid var(--border); border-radius: 12px; outline: none; font-size: 1.5rem; font-weight: 700; text-align: center; letter-spacing: 10px; background: #fff; transition: border-color 0.25s ease, box-shadow 0.25s ease; }
        .form-group input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.12); }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border: none; border-radius: 12px; font-size: 0.96rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 12px 24px var(--glow); transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 16px 28px var(--glow); }
        .btn-outline { width: 100%; padding: 12px; background: #fff; color: var(--primary); border: 1.5px solid var(--border); border-radius: 12px; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: 12px; }
        .btn-outline:hover { border-color: var(--primary); }
        .alert { display: flex; align-items: center; gap: 8px; padding: 11px 14px; border-radius: 12px; margin-bottom: 18px; text-align: left; font-size: 0.85rem; }
        .alert-danger { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .alert-success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-success a { color: var(--primary-dark); font-weight: 700; }
        .link { margin-top: 18px; font-size: 0.85rem; color: var(--muted); }
        .link a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
        .timer { margin: 16px 0; font-size: 0.85rem; color: var(--muted); }
        .timer span { font-weight: 700; color: var(--primary); }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-big"><i class="fa-solid fa-envelope-open-text"></i></div>
        <h2>Verifikasi Email</h2>
        <div class="subtitle">Masukkan kode 6 digit yang telah dikirim ke<br><strong><?= htmlspecialchars($email) ?></strong></div>

        <?php if ($pesanTipe === 'gagal'): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($pesan) ?></div>
        <?php elseif ($pesanTipe === 'sukses'): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($pesan) ?></div>
        <?php endif; ?>

        <?php if ($pesanTipe === 'sukses' && !isset($_POST['kirim_ulang'])): ?>
            <div class="link"><a href="login.php" class="auth-transition-link">Ke halaman masuk &rarr;</a></div>
        <?php else: ?>
            <form action="" method="POST" id="verifyForm">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <div class="form-group">
                    <label for="kode">Kode Verifikasi</label>
                    <input type="text" id="kode" name="kode" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required placeholder="000000" autocomplete="one-time-code">
                </div>
                <button type="submit" name="verify" class="btn" id="btn-verify">
                    <span id="btn-text">Verifikasi</span>
                </button>
            </form>

            <form action="" method="POST" style="margin-top: 0;">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <button type="submit" name="kirim_ulang" class="btn-outline" id="btn-resend">
                    <i class="fa-solid fa-rotate"></i> Kirim ulang kode
                </button>
            </form>

            <div class="link">
                <a href="register.php">Gunakan email lain</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('kode')?.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').substring(0, 6);
        });
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = this.querySelector('button[type="submit"]');
                if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner"></i> Memproses...'; }
            });
        });
    </script>
</body>
</html>
