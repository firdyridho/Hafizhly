<?php
session_start();
require_once 'config/database.php';
require_once 'config/email.php';

$pesan = '';
$pesanTipe = '';

if (isset($_POST['lupa_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);
        $kode = sprintf("%06d", random_int(0, 999999));
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        mysqli_query($conn, "UPDATE users SET reset_code = '$kode', reset_expiry = '$expiry' WHERE email = '$email'");

        if (kirim_kode_reset($email, $user['nama_lengkap'], $kode)) {
            header("Location: reset-password.php?email=" . urlencode($email));
            exit();
        } else {
            $pesan = 'Gagal mengirim email. Coba lagi nanti.';
            $pesanTipe = 'gagal';
        }
    } else {
        $pesan = 'Email tidak ditemukan!';
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
    <title>Lupa Password - Hifzhly</title>
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
        .btn { width: 100%; padding: 13px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; border: none; border-radius: 12px; font-size: 0.96rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 12px 24px var(--glow); transition: all 0.3s ease; margin-top: 6px; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 16px 28px var(--glow); }
        .alert { display: flex; align-items: center; gap: 8px; padding: 11px 14px; border-radius: 12px; margin-bottom: 16px; text-align: left; font-size: 0.85rem; }
        .alert-danger { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .link { margin-top: 20px; font-size: 0.86rem; color: var(--muted); }
        .link a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-big"><i class="fa-solid fa-key"></i></div>
        <h2>Lupa Password?</h2>
        <div class="subtitle">Masukkan email terdaftar, kami akan kirim kode reset password.</div>

        <?php if ($pesanTipe === 'gagal'): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($pesan) ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope field-icon"></i>
                    <input type="email" id="email" name="email" required placeholder="nama@email.com">
                </div>
            </div>
            <button type="submit" name="lupa_password" class="btn">Kirim Kode Reset</button>
        </form>

        <div class="link">
            <a href="login.php">&larr; Kembali ke halaman masuk</a>
        </div>
    </div>
    <script>
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                var btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.innerHTML = '<i class="fa-solid fa-spinner"></i> Memproses...';
                    setTimeout(function() { btn.disabled = true; }, 100);
                }
            });
        });
    </script>
</body>
</html>
