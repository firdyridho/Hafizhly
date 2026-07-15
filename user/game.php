<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// AUTO-CREATE TABEL GAME HISTORY & ACHIEVEMENTS JIKA BELUM ADA
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS game_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_type VARCHAR(50) NOT NULL,
    juz_start INT,
    juz_end INT,
    total_q INT,
    score INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_penghargaan VARCHAR(100) NOT NULL,
    tanggal_diraih DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Ambil ringkas skor tertinggi user untuk ditampilkan di header (opsional, aman jika kosong)
$bestScore = 0;
if ($stmt = mysqli_prepare($conn, "SELECT MAX(score) AS best FROM game_history WHERE user_id = ?")) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $bestScore = (int)($row['best'] ?? 0);
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcade Qur'an - Hafizhly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --primary-deep: #064e3b;
            --primary-light: #ecfdf5;
            --primary-mist: #d1fae5;
            --ink: #0f172a;
            --muted: #64748b;
            --line: #e2e8f0;
            --white: #ffffff;
            --ease: cubic-bezier(.22, 1, .36, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            margin: 0;
            padding-bottom: clamp(90px, 12vw, 110px);
            background:
                radial-gradient(circle at 12% 0%, var(--primary-mist) 0%, transparent 45%),
                radial-gradient(circle at 100% 20%, var(--primary-light) 0%, transparent 40%),
                #f6faf8;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ===== Page load / transition overlay =====
           Auto-hide murni pakai CSS animation (tidak bergantung JS sama
           sekali), supaya walau script gagal load, overlay TETAP hilang
           sendiri. JS hanya dipakai untuk efek slide balik saat pindah
           halaman (progressive enhancement, bukan syarat). */
        #page-transition {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, var(--primary-deep), var(--primary));
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            /* jangan pernah mengunci klik ke konten di baliknya */
            animation: curtain-out .7s var(--ease) .2s forwards;
        }

        @keyframes curtain-out {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(-100%);
            }
        }

        #page-transition.leaving {
            animation: curtain-in .4s var(--ease) forwards;
        }

        @keyframes curtain-in {
            from {
                transform: translateY(-100%);
            }

            to {
                transform: translateY(0);
            }
        }

        #page-transition i {
            color: #fff;
            font-size: 2rem;
            animation: spin-fade 1s linear infinite;
        }

        @keyframes spin-fade {
            0% {
                transform: rotate(0deg) scale(1);
                opacity: .6;
            }

            50% {
                opacity: 1;
            }

            100% {
                transform: rotate(360deg) scale(1);
                opacity: .6;
            }
        }

        .app-shell {
            max-width: 640px;
            margin: 0 auto;
            padding: clamp(16px, 5vw, 28px);
            position: relative;
            z-index: 1;
        }

        /* ===== Decorative arabesque corner pattern ===== */
        .motif {
            position: absolute;
            width: clamp(140px, 30vw, 220px);
            height: clamp(140px, 30vw, 220px);
            border: 2px solid var(--primary);
            opacity: .06;
            border-radius: 50%;
            pointer-events: none;
        }

        .motif.m1 {
            top: -60px;
            right: -60px;
        }

        .motif.m2 {
            top: 10px;
            right: 10px;
            width: 60%;
            height: 60%;
        }

        /* ===== Header ===== */
        .arcade-header {
            position: relative;
            text-align: center;
            padding: clamp(18px, 5vw, 26px) 10px clamp(26px, 6vw, 34px);
            overflow: hidden;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            border: 1px solid var(--primary-mist);
            color: var(--primary-dark);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 999px;
            box-shadow: 0 4px 14px rgba(5, 150, 105, .08);
        }

        .eyebrow i {
            color: var(--primary);
        }

        .arcade-header h1 {
            font-size: clamp(1.6rem, 5vw, 2.2rem);
            font-weight: 800;
            margin: 14px 0 6px;
            color: var(--ink);
        }

        .arcade-header h1 span {
            color: var(--primary);
        }

        .arcade-header p {
            color: var(--muted);
            font-size: clamp(.85rem, 2.6vw, .98rem);
            max-width: 420px;
            margin: 0 auto;
        }

        .best-score-pill {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 10px 18px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .05);
        }

        .best-score-pill i {
            color: #f59e0b;
        }

        .best-score-pill b {
            color: var(--primary-dark);
        }

        /* ===== Game cards ===== */
        .game-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }

        @media (min-width: 640px) {
            .game-grid {
                grid-template-columns: 1fr 1fr;
                gap: 22px;
            }
        }

        .game-card {
            --accent: var(--primary);
            --accent-soft: var(--primary-light);
            position: relative;
            display: block;
            text-decoration: none;
            color: var(--ink);
            background: rgba(255, 255, 255, .78);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, .6);
            border-radius: 26px;
            padding: clamp(24px, 6vw, 32px) clamp(20px, 5vw, 26px);
            overflow: hidden;
            box-shadow: 0 12px 34px rgba(15, 23, 42, .06);
            transition: transform .5s var(--ease), box-shadow .5s var(--ease), border-color .5s var(--ease);
            isolation: isolate;
        }

        .game-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, var(--accent-soft) 0%, transparent 55%);
            opacity: 0;
            transition: opacity .5s var(--ease);
            z-index: -1;
        }

        .game-card:hover,
        .game-card:focus-visible {
            transform: translateY(-8px) scale(1.01);
            border-color: var(--accent);
            box-shadow: 0 22px 44px -12px rgba(5, 150, 105, .28);
        }

        .game-card:hover::before {
            opacity: 1;
        }

        .gc-icon-wrap {
            width: clamp(64px, 16vw, 78px);
            height: clamp(64px, 16vw, 78px);
            border-radius: 20px;
            background: var(--accent-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            position: relative;
            transition: transform .5s var(--ease);
        }

        .game-card:hover .gc-icon-wrap {
            transform: rotate(-6deg) scale(1.06);
        }

        .gc-icon-wrap i {
            font-size: clamp(1.6rem, 5vw, 2rem);
            color: var(--accent);
        }

        .gc-badge {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--accent);
            background: var(--accent-soft);
            padding: 5px 12px;
            border-radius: 999px;
        }

        .gc-title {
            font-size: clamp(1.1rem, 3.4vw, 1.3rem);
            font-weight: 800;
            margin-bottom: 8px;
        }

        .gc-desc {
            font-size: clamp(.82rem, 2.4vw, .9rem);
            color: var(--muted);
            line-height: 1.6;
            margin-bottom: 22px;
            min-height: 66px;
        }

        .gc-meta {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: .78rem;
            color: var(--muted);
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .gc-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .gc-meta i {
            color: var(--accent);
        }

        .btn-play {
            background: var(--ink);
            color: #fff;
            border: none;
            padding: 13px 20px;
            border-radius: 16px;
            font-weight: 700;
            font-size: .92rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background .4s var(--ease), transform .3s var(--ease), letter-spacing .3s var(--ease);
        }

        .game-card:hover .btn-play {
            background: var(--accent);
            letter-spacing: .01em;
        }

        .btn-play i {
            transition: transform .4s var(--ease);
        }

        .game-card:hover .btn-play i {
            transform: translateX(4px);
        }

        /* ===== Reveal on load ===== */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            animation: reveal-up .7s var(--ease) forwards;
        }

        @keyframes reveal-up {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .delay-1 {
            animation-delay: .05s;
        }

        .delay-2 {
            animation-delay: .18s;
        }

        .delay-3 {
            animation-delay: .3s;
        }

        @media (prefers-reduced-motion: reduce) {

            .reveal,
            .game-card,
            .btn-play,
            #page-transition {
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>

    <div id="page-transition">
        <i class="fa-solid fa-book-quran"></i>
    </div>

    <div class="app-shell">

        <div class="arcade-header reveal">
            <div class="motif m1"></div>
            <div class="motif m2"></div>
            <span class="eyebrow"><i class="fa-solid fa-gamepad"></i> Arcade Qur'an</span>
            <h1>Uji <span>Kekuatan Hafalanmu</span></h1>
            <p>Pilih tantangan, asah kelancaran ayat, dan raih skor terbaikmu hari ini.</p>

            <?php if ($bestScore > 0): ?>
                <div class="best-score-pill">
                    <i class="fa-solid fa-trophy"></i>
                    Skor terbaikmu: <b><?php echo htmlspecialchars($bestScore); ?></b>
                </div>
            <?php endif; ?>
        </div>

        <div class="game-grid">

            <a href="lanjut_ayat.php" class="game-card js-game-link reveal delay-1">
                <span class="gc-badge">Mutqin</span>
                <div class="gc-icon-wrap">
                    <i class="fa-solid fa-headphones-simple"></i>
                </div>
                <div class="gc-title">Lanjut Ayat</div>
                <div class="gc-desc">Dengarkan lantunan ayat, lalu tebak potongan ayat berikutnya dengan tepat. Cocok untuk menguji kelancaran hafalanmu.</div>
                <div class="gc-meta">
                    <span><i class="fa-solid fa-clock"></i> ± 5 menit</span>
                    <span><i class="fa-solid fa-signal"></i> Semua level</span>
                </div>
                <div class="btn-play">Main Sekarang <i class="fa-solid fa-play"></i></div>
            </a>

            <a href="tebak_surah.php" class="game-card js-game-link reveal delay-2">
                <span class="gc-badge">Kuis</span>
                <div class="gc-icon-wrap">
                    <i class="fa-solid fa-circle-question"></i>
                </div>
                <div class="gc-title">Tebak Surah &amp; Ayat</div>
                <div class="gc-desc">Dengarkan audio acak dari juz pilihanmu, lalu tebak dari surah apa dan ayat ke berapa audio tersebut berasal.</div>
                <div class="gc-meta">
                    <span><i class="fa-solid fa-clock"></i> ± 7 menit</span>
                    <span><i class="fa-solid fa-layer-group"></i> Per juz</span>
                </div>
                <div class="btn-play">Main Sekarang <i class="fa-solid fa-play"></i></div>
            </a>

        </div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        // Overlay loading sudah hilang otomatis lewat CSS animation di atas,
        // JS di sini murni tambahan (progressive enhancement) dan dibungkus
        // try/catch supaya kalau ada yang gagal, halaman tetap normal.
        window.addEventListener('DOMContentLoaded', () => {
            try {
                if (typeof AOS !== 'undefined') {
                    AOS.init({
                        duration: 600,
                        once: true,
                        easing: 'ease-out-cubic'
                    });
                }
            } catch (err) {
                console.warn('AOS gagal dimuat, animasi scroll dilewati.', err);
            }
        });

        // Transisi ala-SPA saat pindah ke halaman game
        document.querySelectorAll('.js-game-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const overlay = document.getElementById('page-transition');
                if (!overlay) return; // fallback: biarkan link jalan normal
                e.preventDefault();
                const target = this.getAttribute('href');
                overlay.classList.add('leaving');
                setTimeout(() => {
                    window.location.href = target;
                }, 380);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>