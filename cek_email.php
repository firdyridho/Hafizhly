<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - Hifzhly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #1e293b;
        }
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-darker: #047857;
            --primary-light: #34d399;
            --primary-lighter: #6ee7b7;
            --mint: #ecfdf5;
            --gold: #c9a227;
            --dark: #0f172a;
            --ink: #1e293b;
            --muted: #64748b;
            --bg: #f8fafc;
        }

        /* Aurora Background */
        .aurora {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .aurora span {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.2;
        }
        .aurora span:nth-child(1) {
            background: #10b981;
            top: -150px;
            left: -100px;
            animation: drift 20s ease-in-out infinite;
        }
        .aurora span:nth-child(2) {
            background: #34d399;
            bottom: -150px;
            right: -100px;
            animation: drift 25s ease-in-out infinite reverse;
        }
        .aurora span:nth-child(3) {
            background: #c9a227;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            animation: drift 30s ease-in-out infinite;
        }
        @keyframes drift {
            0%,100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(30px,-30px) scale(1.1); }
            66% { transform: translate(-20px,20px) scale(0.95); }
        }

        /* Header */
        .header {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(15,23,42,0.06);
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .header-brand .brand-mark {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 4px 12px rgba(16,185,129,0.3);
        }
        .header-brand .brand-text {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -0.03em;
            color: var(--dark);
        }
        .header-brand .brand-text span {
            color: var(--primary);
        }
        .header-badge {
            font-size: 13px;
            color: var(--muted);
            background: var(--mint);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .header-badge i {
            font-size: 12px;
        }

        /* Main Content */
        .main {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
        }

        /* Card */
        .card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 24px;
            padding: 48px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.04);
        }

        .card-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }
        .card-icon.loading {
            background: #f1f5f9;
            color: var(--muted);
        }
        .card-icon.success {
            background: var(--mint);
            color: var(--primary);
        }
        .card-icon.fail {
            background: #fef2f2;
            color: #ef4444;
        }

        .card-icon .spinner {
            width: 30px;
            height: 30px;
            border: 3px solid #e2e8f0;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .card h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--dark);
            margin-bottom: 8px;
        }
        .card p {
            font-size: 15px;
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .card .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
        }
        .card .status-badge.success {
            background: var(--mint);
            color: var(--primary-dark);
        }
        .card .status-badge.fail {
            background: #fef2f2;
            color: #dc2626;
        }

        .card .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid rgba(15,23,42,0.06);
            font-size: 14px;
        }
        .card .detail-row:last-child {
            border-bottom: none;
        }
        .card .detail-row .label {
            color: var(--muted);
        }
        .card .detail-row .value {
            font-weight: 600;
            color: var(--dark);
        }

        .card .detail-section {
            background: rgba(15,23,42,0.02);
            border-radius: 16px;
            padding: 16px 20px;
            margin: 20px 0;
            text-align: left;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 10px 24px;
            border-radius: 12px;
            border: 1px solid rgba(15,23,42,0.1);
            background: #fff;
            color: var(--ink);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: var(--mint);
            border-color: var(--primary);
            color: var(--primary-dark);
        }

        /* Footer */
        .footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: var(--muted);
            border-top: 1px solid rgba(15,23,42,0.06);
            background: rgba(255,255,255,0.5);
        }
        .footer a {
            color: var(--primary);
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="aurora">
    <span></span>
    <span></span>
    <span></span>
</div>

<header class="header">
    <a href="index.php" class="header-brand">
        <div class="brand-mark">H</div>
        <span class="brand-text">Hifz<span>hly</span></span>
    </a>
    <div class="header-badge">
        <i class="fa-regular fa-envelope"></i>
        Email Test
    </div>
</header>

<main class="main">
    <div class="card">
        <div class="card-icon loading" id="statusIcon">
            <div class="spinner"></div>
        </div>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        require_once 'config/database.php';
        require_once 'config/email.php';
        ?>

        <h1 id="statusTitle">Mengirim Email...</h1>
        <p id="statusDesc">Harap tunggu, sedang mengirim email uji coba ke <strong>hifzhlyid@gmail.com</strong>.</p>

        <?php
        $result = kirim_email('hifzhlyid@gmail.com', 'Test Hifzhly', '<h1>Test</h1><p>Ini test dari Hifzhly</p>');

        if ($result) {
            echo '<script>
                document.getElementById("statusIcon").className = "card-icon success";
                document.getElementById("statusIcon").innerHTML = \'<i class="fa-regular fa-circle-check"></i>\';
                document.getElementById("statusTitle").textContent = "Email Berhasil Dikirim!";
                document.getElementById("statusDesc").innerHTML = "Email uji coba berhasil dikirim ke <strong>hifzhlyid@gmail.com</strong>.";
            </script>';
            echo '<div class="status-badge success"><i class="fa-regular fa-circle-check"></i> SUKSES — Email terkirim</div>';
        } else {
            echo '<script>
                document.getElementById("statusIcon").className = "card-icon fail";
                document.getElementById("statusIcon").innerHTML = \'<i class="fa-regular fa-circle-xmark"></i>\';
                document.getElementById("statusTitle").textContent = "Email Gagal Dikirim!";
                document.getElementById("statusDesc").innerHTML = "Email uji coba gagal dikirim. Silakan periksa konfigurasi email Anda.";
            </script>';
            echo '<div class="status-badge fail"><i class="fa-regular fa-circle-xmark"></i> GAGAL — Email tidak terkirim</div>';
        }
        ?>

        <div class="detail-section">
            <div class="detail-row">
                <span class="label"><i class="fa-regular fa-envelope" style="margin-right:6px;color:var(--primary);"></i> Penerima</span>
                <span class="value">hifzhlyid@gmail.com</span>
            </div>
            <div class="detail-row">
                <span class="label"><i class="fa-regular fa-tag" style="margin-right:6px;color:var(--primary);"></i> Subjek</span>
                <span class="value">Test Hifzhly</span>
            </div>
            <div class="detail-row">
                <span class="label"><i class="fa-regular fa-calendar" style="margin-right:6px;color:var(--primary);"></i> Waktu</span>
                <span class="value"><?= date('d M Y, H:i:s') ?></span>
            </div>
        </div>

        <a href="index.php" class="btn-back">
            <i class="fa-regular fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</main>

<footer class="footer">
    &copy; <?= date('Y') ?> Hifzhly — <a href="index.php">Kembali ke Beranda</a>
</footer>

</body>
</html>
