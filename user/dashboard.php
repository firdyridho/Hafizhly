<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$nama_user = $_SESSION['nama_lengkap'] ?? 'Hamba Allah';
$nama_depan = explode(' ', trim($nama_user))[0];

// --- 1. DATA TILAWAH ---
$q_bm = mysqli_query($conn, "SELECT surah_nomor, ayat FROM bookmark WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
if ($q_bm && mysqli_num_rows($q_bm) > 0) {
    $bm = mysqli_fetch_assoc($q_bm);
    $bm_surah = (int) $bm['surah_nomor'];
    $bm_ayat = (int) $bm['ayat'];
} else {
    $bm_surah = 1;
    $bm_ayat = 1;
}

// --- 2. DATA MUROJAAH ---
$q_mur = mysqli_query($conn, "SELECT surah_nomor, last_ayat FROM murojaah_progress WHERE user_id='$user_id' ORDER BY updated_at DESC LIMIT 1");
if ($q_mur && mysqli_num_rows($q_mur) > 0) {
    $mur = mysqli_fetch_assoc($q_mur);
    $mur_surah = (int) $mur['surah_nomor'];
    $mur_ayat = (int) $mur['last_ayat'];
} else {
    $mur_surah = 78;
    $mur_ayat = 1;
}

// --- 3. DATA HAFALAN ---
$q_haf = mysqli_query($conn, "SELECT surah, ayah_end FROM mutabaah WHERE user_id='$user_id' AND activity_type='hafalan_baru' ORDER BY created_at DESC LIMIT 1");
if ($q_haf && mysqli_num_rows($q_haf) > 0) {
    $haf = mysqli_fetch_assoc($q_haf);
    $haf_surah_name = $haf['surah'];
    $haf_ayat = (int) $haf['ayah_end'];
} else {
    $haf_surah_name = "Al-Mulk";
    $haf_ayat = 1;
}

// --- 4. DATA TAJWID ---
$q_tajwid = mysqli_query($conn, "SELECT * FROM tajwid_materi ORDER BY created_at DESC LIMIT 3");
$tajwid_lessons = [];
while ($row = mysqli_fetch_assoc($q_tajwid)) {
    $tajwid_lessons[] = $row;
}

// --- 5. DATA NOTIFIKASI ---
$q_notif = mysqli_query($conn, "SELECT task_name, task_time, is_completed, created_at FROM user_todos WHERE user_id='$user_id' ORDER BY id DESC LIMIT 5");
$notifications = [];
while ($row = mysqli_fetch_assoc($q_notif)) {
    $notifications[] = $row;
}
$has_notif = count($notifications) > 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Ngajii - Dashboard</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            padding-bottom: 90px;
            overflow-x: hidden;
        }

        /* --- HERO SECTION --- */
        .hero-section {
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: white;
            padding: max(20px, env(safe-area-inset-top)) 20px 40px 20px;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            position: relative;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .greeting {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .hijri-date {
            font-size: 0.85rem;
            opacity: 0.9;
            font-weight: 500;
            min-height: 18px;
        }

        .location {
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: 0.9;
            cursor: pointer;
            margin-top: 2px;
            transition: 0.2s;
        }

        .location:hover {
            opacity: 1;
            transform: translateX(2px);
        }

        .action-icons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .icon-btn {
            color: white;
            font-size: 1.3rem;
            text-decoration: none;
            position: relative;
            cursor: pointer;
            background: none;
            border: none;
        }

        .notif-dot {
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid var(--primary-dark);
        }

        /* WAKTU SHOLAT */
        .clock-container {
            text-align: center;
            margin-bottom: 25px;
        }

        .clock-time {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 5px;
            font-variant-numeric: tabular-nums;
        }

        .countdown {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
            min-height: 20px;
        }

        .prayer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }

        .prayer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            opacity: 0.6;
            transition: 0.3s;
        }

        .prayer-item.active {
            opacity: 1;
        }

        .prayer-item.active .p-icon {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .p-name {
            font-size: 0.75rem;
            font-weight: 600;
        }

        .p-icon {
            font-size: 1.2rem;
            height: 40px;
            display: flex;
            align-items: center;
        }

        .p-time {
            font-size: 0.75rem;
            font-weight: 700;
            min-height: 16px;
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            padding: 25px 20px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .see-all {
            font-size: 0.85rem;
            color: var(--primary-dark);
            font-weight: 700;
            text-decoration: none;
        }

        /* MENU GRID */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            background: var(--card-bg);
            padding: 20px;
            border-radius: 24px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
        }

        .menu-icon {
            width: 55px;
            height: 55px;
            border-radius: 18px;
            background: var(--primary);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.4rem;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
            transition: 0.3s;
        }

        .menu-item:hover .menu-icon {
            transform: translateY(-3px);
        }

        .menu-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-dark);
            text-align: center;
        }

        .menu-item.menu-all .menu-icon {
            background: #f1f5f9;
            color: var(--primary-dark);
            box-shadow: none;
        }

        /* HORIZONTAL SCROLL CARDS */
        .cards-scroll {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 10px;
            scrollbar-width: none;
            scroll-snap-type: x mandatory;
            margin-bottom: 25px;
        }

        .cards-scroll::-webkit-scrollbar {
            display: none;
        }

        .activity-card {
            min-width: 85%;
            scroll-snap-align: center;
            background: var(--card-bg);
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: var(--text-dark);
        }

        .ac-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .ac-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: #d1fae5;
            color: var(--primary-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.3rem;
        }

        .ac-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary-dark);
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .ac-title {
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .ac-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        .ac-play {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-dark);
        }

        /* TAJWID LIST */
        .tajwid-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .tajwid-card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            text-decoration: none;
            transition: 0.3s;
        }

        .tajwid-card:hover {
            border-color: var(--primary);
        }

        .tw-cover {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .tw-cover-icon {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            background: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .tw-info {
            flex-grow: 1;
        }

        .tw-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .tw-date {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* MODALS */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: flex-end;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 600px;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
            padding: 30px 20px;
            animation: slideUp 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            max-height: 85vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .modal-header h3 {
            font-size: 1.3rem;
            font-weight: 800;
        }

        .btn-close {
            background: #f1f5f9;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: none;
            font-size: 1.2rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .notif-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            border-bottom: 1px solid var(--border);
            align-items: flex-start;
        }

        .notif-item:last-child {
            border-bottom: none;
        }

        .notif-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .notif-body {
            flex-grow: 1;
        }

        .notif-title {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--text-dark);
        }

        .notif-time {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .notif-success {
            background: #d1fae5;
            color: var(--primary-dark);
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        @media (min-width: 768px) {
            .hero-section {
                border-radius: 24px;
                margin: 20px auto;
                max-width: 1000px;
                padding: 40px;
                display: grid;
                grid-template-columns: 1fr 1fr;
                align-items: center;
                gap: 30px;
            }

            .top-bar {
                grid-column: 1 / -1;
                margin-bottom: 0;
            }

            .clock-container {
                text-align: left;
                margin-bottom: 0;
            }

            .prayer-row {
                justify-content: flex-end;
                gap: 20px;
            }

            .menu-grid {
                grid-template-columns: repeat(8, 1fr);
                padding: 25px;
            }

            .cards-scroll {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
            }

            .activity-card {
                min-width: auto;
            }

            .tajwid-list {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
            }

            .modal-overlay {
                align-items: center;
            }

            .modal-content {
                border-radius: 24px;
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="hero-section">
        <div class="top-bar">
            <div class="user-info">
                <div class="greeting">Assalamu'alaikum, <?= htmlspecialchars($nama_depan) ?></div>
                <div class="hijri-date" id="hijri-date"></div>

                <!-- Diarahkan ke setting.php jika diklik -->
                <div class="location" onclick="window.location.href='setting.php'">
                    <i class="fas fa-location-dot"></i> <span id="location-text">Mencari...</span>
                </div>
            </div>
            <div class="action-icons">
                <!-- Tautan ke setting.php -->
                <a href="setting.php" class="icon-btn"><i class="fas fa-cog"></i></a>
                <button class="icon-btn" onclick="toggleModal('notifModal')">
                    <i class="fas fa-bell"></i>
                    <?php if ($has_notif): ?><div class="notif-dot"></div><?php endif; ?>
                </button>
            </div>
        </div>

        <div class="clock-container">
            <div class="clock-time" id="clock">--:--</div>
            <div class="countdown" id="countdown-text"></div>
        </div>

        <div class="prayer-row" id="prayer-container">
            <!-- Loader Sementara untuk struktur -->
            <div class="prayer-item">
                <div class="p-name">Fajr</div>
                <div class="p-icon"><i class="fas fa-cloud-moon"></i></div>
                <div class="p-time">--:--</div>
            </div>
            <div class="prayer-item">
                <div class="p-name">Dzuhr</div>
                <div class="p-icon"><i class="fas fa-sun"></i></div>
                <div class="p-time">--:--</div>
            </div>
            <div class="prayer-item">
                <div class="p-name">Asr</div>
                <div class="p-icon"><i class="fas fa-cloud-sun"></i></div>
                <div class="p-time">--:--</div>
            </div>
            <div class="prayer-item">
                <div class="p-name">Maghrib</div>
                <div class="p-icon"><i class="fas fa-moon"></i></div>
                <div class="p-time">--:--</div>
            </div>
            <div class="prayer-item">
                <div class="p-name">Isha</div>
                <div class="p-icon"><i class="fas fa-star"></i></div>
                <div class="p-time">--:--</div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <!-- MENU GRID -->
        <div class="section-header">
            <h3 class="section-title">Fitur Utama</h3>
        </div>
        <div class="menu-grid">
            <a href="alquran.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-book-open"></i></div>
                <div class="menu-text">Qur'an</div>
            </a>
            <a href="hafalan.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-brain"></i></div>
                <div class="menu-text">Hafalan</div>
            </a>
            <a href="smart_murojaah.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-microphone-alt"></i></div>
                <div class="menu-text">Murojaah</div>
            </a>
            <a href="mutabaah.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-chart-line"></i></div>
                <div class="menu-text">Mutabaah</div>
            </a>
            <a href="tajwid.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="menu-text">Tajwid</div>
            </a>
            <a href="doa.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-hands-praying"></i></div>
                <div class="menu-text">Doa</div>
            </a>
            <a href="game.php" class="menu-item">
                <div class="menu-icon"><i class="fas fa-gamepad"></i></div>
                <div class="menu-text">Game</div>
            </a>
            <div class="menu-item menu-all" onclick="toggleModal('menuModal')">
                <div class="menu-icon"><i class="fas fa-th-large"></i></div>
                <div class="menu-text">Lainnya</div>
            </div>
        </div>

        <!-- AKTIVITAS -->
        <div class="section-header">
            <h3 class="section-title">Aktivitas Anda</h3>
        </div>
        <div class="cards-scroll">
            <a href="baca.php?surah=<?= $bm_surah ?>&ayat=<?= $bm_ayat ?>" class="activity-card">
                <div class="ac-left">
                    <div class="ac-icon"><i class="fas fa-book-quran"></i></div>
                    <div>
                        <div class="ac-label">Terakhir Dibaca</div>
                        <div class="ac-title" id="tilawah-title">Surah <?= $bm_surah ?></div>
                        <div class="ac-desc">Ayat <?= $bm_ayat ?></div>
                    </div>
                </div>
                <div class="ac-play"><i class="fas fa-play"></i></div>
            </a>
            <a href="smart_murojaah.php" class="activity-card">
                <div class="ac-left">
                    <div class="ac-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fas fa-sync-alt"></i></div>
                    <div>
                        <div class="ac-label">Progress Murojaah</div>
                        <div class="ac-title" id="mur-title">Surah <?= $mur_surah ?></div>
                        <div class="ac-desc">Selesai Ayat <?= $mur_ayat ?></div>
                    </div>
                </div>
                <div class="ac-play"><i class="fas fa-play"></i></div>
            </a>
            <a href="hafalan.php" class="activity-card">
                <div class="ac-left">
                    <div class="ac-icon" style="background:#fef3c7; color:#d97706;"><i class="fas fa-brain"></i></div>
                    <div>
                        <div class="ac-label">Hafalan Terbaru</div>
                        <div class="ac-title"><?= htmlspecialchars($haf_surah_name) ?></div>
                        <div class="ac-desc">Ayat <?= $haf_ayat ?></div>
                    </div>
                </div>
                <div class="ac-play"><i class="fas fa-play"></i></div>
            </a>
        </div>

        <!-- TAJWID TERBARU (Menggunakan Icon Jika Tidak Ada Gambar) -->
        <div class="section-header">
            <h3 class="section-title">Belajar Tajwid</h3>
            <a href="tajwid.php" class="see-all">Lihat Semua</a>
        </div>
        <div class="tajwid-list">
            <?php foreach ($tajwid_lessons as $tw):
                $date_tw = date('d M Y', strtotime($tw['created_at']));
            ?>
                <a href="tajwid.php?id=<?= $tw['id'] ?>" class="tajwid-card">
                    <?php if (!empty($tw['cover_image'])): ?>
                        <img src="../uploads/<?= htmlspecialchars($tw['cover_image']) ?>" alt="<?= htmlspecialchars($tw['judul']) ?>" class="tw-cover">
                    <?php else: ?>
                        <div class="tw-cover-icon"><i class="fas fa-book-open"></i></div>
                    <?php endif; ?>
                    <div class="tw-info">
                        <div class="tw-title"><?= htmlspecialchars($tw['judul']) ?></div>
                        <div class="tw-date"><i class="far fa-clock"></i> <?= $date_tw ?></div>
                    </div>
                    <i class="fas fa-chevron-right" style="color:var(--border);"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- MODAL MENU LENGKAP -->
    <div id="menuModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Semua Fitur</h3>
                <button class="btn-close" onclick="toggleModal('menuModal')"><i class="fas fa-times"></i></button>
            </div>
            <div style="margin-bottom:20px;">
                <p style="font-size:0.8rem; color:var(--text-muted); font-weight:700; margin-bottom:15px; letter-spacing:1px;">FITUR UTAMA</p>
                <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:15px;">
                    <a href="alquran.php" class="menu-item">
                        <div class="menu-icon"><i class="fas fa-book-open"></i></div>
                        <div class="menu-text">Qur'an</div>
                    </a>
                    <a href="hafalan.php" class="menu-item">
                        <div class="menu-icon"><i class="fas fa-brain"></i></div>
                        <div class="menu-text">Hafalan</div>
                    </a>
                    <a href="smart_murojaah.php" class="menu-item">
                        <div class="menu-icon"><i class="fas fa-microphone-alt"></i></div>
                        <div class="menu-text">Murojaah</div>
                    </a>
                    <a href="mutabaah.php" class="menu-item">
                        <div class="menu-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="menu-text">Mutabaah</div>
                    </a>
                </div>
            </div>
            <div>
                <p style="font-size:0.8rem; color:var(--text-muted); font-weight:700; margin-bottom:15px; letter-spacing:1px;">PENDUKUNG</p>
                <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:15px;">
                    <a href="tajwid.php" class="menu-item">
                        <div class="menu-icon" style="background:#0284c7;"><i class="fas fa-chalkboard-teacher"></i></div>
                        <div class="menu-text">Tajwid</div>
                    </a>
                    <a href="doa.php" class="menu-item">
                        <div class="menu-icon" style="background:#d97706;"><i class="fas fa-hands-praying"></i></div>
                        <div class="menu-text">Doa</div>
                    </a>
                    <a href="game.php" class="menu-item">
                        <div class="menu-icon" style="background:#7c3aed;"><i class="fas fa-gamepad"></i></div>
                        <div class="menu-text">Game</div>
                    </a>
                    <a href="target.php" class="menu-item">
                        <div class="menu-icon" style="background:#ea580c;"><i class="fas fa-bullseye"></i></div>
                        <div class="menu-text">Target</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL NOTIFIKASI -->
    <div id="notifModal" class="modal-overlay">
        <div class="modal-content" style="padding: 0;">
            <div class="modal-header" style="padding: 25px 25px 15px 25px; margin-bottom:0; border-bottom:1px solid var(--border);">
                <h3>Notifikasi</h3>
                <button class="btn-close" onclick="toggleModal('notifModal')"><i class="fas fa-times"></i></button>
            </div>
            <div style="padding: 10px 0;">
                <?php if (empty($notifications)): ?>
                    <div style="padding: 40px 20px; text-align:center; color:var(--text-muted);">
                        <i class="fas fa-bell-slash" style="font-size:2rem; margin-bottom:10px; color:#cbd5e1;"></i>
                        <p>Belum ada notifikasi baru.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $n):
                        $is_done = $n['is_completed'] == 1;
                        $icon = $is_done ? 'fa-check' : 'fa-clock';
                        $icon_class = $is_done ? 'notif-success' : '';
                        $time_str = date('H:i', strtotime($n['task_time']));
                    ?>
                        <div class="notif-item">
                            <div class="notif-icon <?= $icon_class ?>"><i class="fas <?= $icon ?>"></i></div>
                            <div class="notif-body">
                                <div class="notif-title">Target: <?= htmlspecialchars($n['task_name']) ?></div>
                                <div class="notif-time">
                                    <?= $is_done ? 'Selesai dikerjakan!' : "Jadwal jam {$time_str}. Jangan lupa ya!" ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="notif-item">
                    <div class="notif-icon notif-success"><i class="fas fa-hand-sparkles"></i></div>
                    <div class="notif-body">
                        <div class="notif-title">Ahlan Wa Sahlan, <?= htmlspecialchars($nama_depan) ?>!</div>
                        <div class="notif-time">Selamat datang di dashboard Hifzly. Semangat mengajinya!</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigasi Bawah -->
    <?php include '../components/nav.php'; ?>

    <script>
        // BUKA TUTUP MODAL
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            } else {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = "none";
                document.body.style.overflow = 'auto';
            }
        }

        // AMBIL NAMA SURAH (Tidak Memblokir UI)
        fetch(`https://equran.id/api/v2/surat/<?= $bm_surah ?>`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('tilawah-title').innerText = data.data.namaLatin;
            }).catch(() => {});

        fetch(`https://equran.id/api/v2/surat/<?= $mur_surah ?>`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('mur-title').innerText = data.data.namaLatin;
            }).catch(() => {});

        // --- SISTEM JAM & CACHE JADWAL SHOLAT SUPER CEPAT ---
        let prayerTimesData = null;

        function updateClock() {
            const now = new Date();
            const format = n => String(n).padStart(2, '0');
            document.getElementById('clock').innerText = `${format(now.getHours())}:${format(now.getMinutes())}`;
            if (prayerTimesData) updateCountdown(now);
        }
        setInterval(updateClock, 1000);
        updateClock();

        function initLocation() {
            const savedCity = localStorage.getItem('hifzly_city');
            const savedLat = localStorage.getItem('hifzly_lat');
            const savedLon = localStorage.getItem('hifzly_lon');

            if (savedCity && savedCity !== 'Belum diatur') {
                // Instantly update text from LocalStorage (0 delay)
                document.getElementById('location-text').innerText = savedCity;

                const todayStr = new Date().toDateString();
                const cachedData = localStorage.getItem('hifzly_prayer_data');
                const cachedDate = localStorage.getItem('hifzly_prayer_date');
                const cachedDataCity = localStorage.getItem('hifzly_prayer_city');

                // Jika jadwal sholat HARI INI dan untuk KOTA INI sudah ada di memori, tampilkan langsung!
                if (cachedData && cachedDate === todayStr && cachedDataCity === savedCity) {
                    processPrayerData(JSON.parse(cachedData), false);
                } else {
                    // Hanya fetch ulang jika beda hari atau beda kota
                    if (savedLat && savedLon) {
                        fetchPrayerAPIByCoords(savedLat, savedLon, savedCity);
                    } else {
                        fetchPrayerAPIByAddress(savedCity);
                    }
                }
            } else {
                document.getElementById('location-text').innerText = "Cikande, Banten";
                fetchPrayerAPIByCoords(-6.1824, 106.3351, "Cikande, Banten");
            }
        }

        async function fetchPrayerAPIByCoords(lat, lon, cityName) {
            try {
                const res = await fetch(`https://api.aladhan.com/v1/timings?latitude=${lat}&longitude=${lon}&method=11`);
                const result = await res.json();
                processPrayerData(result.data, true, cityName);
            } catch (e) {
                showErrorJadwal();
            }
        }

        async function fetchPrayerAPIByAddress(address) {
            try {
                const res = await fetch(`https://api.aladhan.com/v1/timingsByAddress?address=${encodeURIComponent(address)}&method=11`);
                const result = await res.json();
                processPrayerData(result.data, true, address);
            } catch (e) {
                showErrorJadwal();
            }
        }

        function processPrayerData(data, saveToCache = false, cityName = '') {
            if (saveToCache) {
                localStorage.setItem('hifzly_prayer_data', JSON.stringify(data));
                localStorage.setItem('hifzly_prayer_date', new Date().toDateString());
                if (cityName) localStorage.setItem('hifzly_prayer_city', cityName);
            }

            prayerTimesData = data.timings;
            const hijri = data.date.hijri;
            document.getElementById('hijri-date').innerText = `${hijri.day} ${hijri.month.en} ${hijri.year} H`;

            renderPrayerTimes();
            updateClock(); // Paksa hitung ulang segera
        }

        function showErrorJadwal() {
            document.getElementById('prayer-container').innerHTML = "<div style='font-size:0.8rem; text-align:center; width:100%;'>Gagal memuat jadwal.</div>";
        }

        const prayerConfig = [{
                id: 'Fajr',
                name: 'Fajr',
                icon: 'fa-cloud-moon'
            },
            {
                id: 'Dhuhr',
                name: 'Dzuhr',
                icon: 'fa-sun'
            },
            {
                id: 'Asr',
                name: 'Asr',
                icon: 'fa-cloud-sun'
            },
            {
                id: 'Maghrib',
                name: 'Maghrib',
                icon: 'fa-moon'
            },
            {
                id: 'Isha',
                name: 'Isha',
                icon: 'fa-star'
            }
        ];

        function renderPrayerTimes() {
            let html = '';
            prayerConfig.forEach(p => {
                html += `<div class="prayer-item" id="pr-${p.id}">
                    <div class="p-name">${p.name}</div>
                    <div class="p-icon"><i class="fas ${p.icon}"></i></div>
                    <div class="p-time">${prayerTimesData[p.id]}</div>
                </div>`;
            });
            document.getElementById('prayer-container').innerHTML = html;
        }

        function updateCountdown(now) {
            let nextPrayerName = "",
                nextPrayerTimeDate = null,
                activeId = "";
            for (let i = 0; i < prayerConfig.length; i++) {
                const p = prayerConfig[i];
                const [h, m] = prayerTimesData[p.id].split(':');
                let pTime = new Date();
                pTime.setHours(h, m, 0, 0);
                if (pTime > now) {
                    nextPrayerName = p.name;
                    nextPrayerTimeDate = pTime;
                    activeId = p.id;
                    break;
                }
            }
            if (!nextPrayerTimeDate) {
                const [fh, fm] = prayerTimesData['Fajr'].split(':');
                nextPrayerTimeDate = new Date();
                nextPrayerTimeDate.setDate(now.getDate() + 1);
                nextPrayerTimeDate.setHours(fh, fm, 0, 0);
                nextPrayerName = 'Fajr';
                activeId = 'Fajr';
            }

            document.querySelectorAll('.prayer-item').forEach(el => el.classList.remove('active'));
            if (document.getElementById(`pr-${activeId}`)) document.getElementById(`pr-${activeId}`).classList.add('active');

            const diffMs = nextPrayerTimeDate - now;
            const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
            const diffMins = Math.floor((diffMs % 3600000) / 60000);

            let timeText = "";
            if (diffHrs > 0) timeText += `${diffHrs} hour `;
            timeText += `${diffMins} min`;

            document.getElementById('countdown-text').innerText = `${nextPrayerName} ${timeText} left`;
        }

        window.onload = initLocation;
    </script>
</body>

</html>