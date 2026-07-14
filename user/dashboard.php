<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// --- MENGAMBIL DATA BOOKMARK TILAWAH TERAKHIR ---
$q_bm = mysqli_query($conn, "SELECT surah, ayat FROM bookmarks WHERE user_id='$user_id' LIMIT 1");
if ($q_bm && mysqli_num_rows($q_bm) > 0) {
    $bm = mysqli_fetch_assoc($q_bm);
    $bm_surah = (int) $bm['surah'];
    $bm_ayat = (int) $bm['ayat'];
} else {
    // Default jika belum pernah bookmark
    $bm_surah = 1;
    $bm_ayat = 1;
}

// --- MOCK DATA: 3 PELAJARAN TAJWID TERBARU ---
// Catatan: Nanti kamu bisa ubah array ini menjadi hasil query dari database (misal: SELECT * FROM tajwid ORDER BY id DESC LIMIT 3)
$tajwid_lessons = [
    [
        'id' => 1,
        'title' => 'Hukum Nun Mati & Tanwin',
        'desc' => 'Mengenal Idzhar, Idgham, Iqlab, dan Ikhfa secara mendalam.',
        'icon' => 'fa-book-open-reader',
        'color' => 'linear-gradient(135deg, #059669, #10b981)'
    ],
    [
        'id' => 2,
        'title' => 'Makharijul Huruf',
        'desc' => 'Memperbaiki tempat keluarnya huruf-huruf hijaiyah.',
        'icon' => 'fa-comment-dots',
        'color' => 'linear-gradient(135deg, #d97706, #f59e0b)'
    ],
    [
        'id' => 3,
        'title' => 'Hukum Mad (Pemanjangan)',
        'desc' => 'Dasar pemanjangan bacaan yang benar dalam Al-Qur\'an.',
        'icon' => 'fa-wave-square',
        'color' => 'linear-gradient(135deg, #2563eb, #3b82f6)'
    ],
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard - Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #064e3b;
            --primary-light: #059669;
            --accent: #fbbf24;
            --dark: #0f172a;
            --bg: #f8fafc;
            --card-bg: #ffffff;
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
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 100px;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* HEADER HERO */
        .hero-section {
            background: linear-gradient(160deg, #022c22, var(--primary), var(--primary-light));
            color: white;
            padding: max(30px, env(safe-area-inset-top)) 20px 85px 20px;
            position: relative;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(5, 150, 105, 0.2);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .location-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: 0.3s;
        }

        .location-badge:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.02);
        }

        .location-badge i {
            color: var(--accent);
        }

        .profile-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 800;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .bismillah {
            font-size: 2.2rem;
            font-family: 'Amiri', serif;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .date-time-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .date-box {
            text-align: center;
        }

        .date-box .label {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
            font-weight: 700;
        }

        .date-box .value {
            font-size: 0.95rem;
            font-weight: 700;
        }

        .realtime-clock {
            font-size: 2.8rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            font-variant-numeric: tabular-nums;
        }

        /* Jadwal Sholat */
        .prayer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 20px;
            border-radius: 24px;
            margin-bottom: 15px;
            backdrop-filter: blur(8px);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .prayer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            opacity: 0.5;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .prayer-item.active {
            opacity: 1;
            transform: scale(1.15) translateY(-2px);
        }

        .prayer-item.active .p-time,
        .prayer-item.active .p-icon {
            color: var(--accent);
        }

        .p-time {
            font-size: 0.85rem;
            font-weight: 800;
        }

        .p-icon {
            font-size: 1.4rem;
            margin: 3px 0;
        }

        .p-name {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .countdown-text {
            font-size: 0.95rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .countdown-text span {
            color: var(--accent);
            font-weight: 800;
        }

        /* MAIN CONTENT & SLIDER KARTU */
        .main-content {
            padding: 0;
            max-width: 800px;
            margin: -55px auto 0;
            position: relative;
            z-index: 5;
        }

        .slider-container {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding: 0 20px 20px 20px;
            scrollbar-width: none;
        }

        .slider-container::-webkit-scrollbar {
            display: none;
        }

        .floating-card {
            min-width: 90%;
            scroll-snap-align: center;
            background: var(--card-bg);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: var(--dark);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .fc-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .progress-circle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-light) 100%, #e5e7eb 0);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress-inner {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
            color: var(--primary);
            font-weight: 800;
        }

        .fc-text h4 {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .fc-text h3 {
            font-size: 1.15rem;
            color: var(--dark);
            margin-bottom: 6px;
            font-weight: 800;
        }

        .fc-badges {
            display: flex;
            gap: 8px;
        }

        .fc-badge {
            font-size: 0.75rem;
            font-weight: 700;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 12px;
            color: var(--text-muted);
        }

        .fc-arrow {
            width: 38px;
            height: 38px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--dark);
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .floating-card:hover .fc-arrow {
            background: var(--primary-light);
            color: white;
        }

        .coming-soon-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(2px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2;
        }

        .cs-badge {
            background: var(--dark);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .slider-dots {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: -10px;
            margin-bottom: 25px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: 0.3s;
        }

        .dot.active {
            background: var(--primary-light);
            width: 20px;
            border-radius: 10px;
        }

        /* Quick Access (Menus) */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 20px 15px;
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--dark);
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px 15px;
            padding: 0 20px 25px;
            transition: 0.3s;
        }

        .q-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: 0.3s;
        }

        .q-item.hidden-menu {
            display: none;
            animation: fadeIn 0.4s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .q-icon {
            width: 65px;
            height: 65px;
            background: var(--card-bg);
            border-radius: 22px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.7rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            color: var(--primary-light);
            border: 1px solid var(--border);
            transition: 0.3s;
        }

        .q-item:hover .q-icon {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.15);
            border-color: var(--primary-light);
        }

        .q-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--dark);
            text-align: center;
        }

        /* Tajwid Section */
        .tajwid-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 0 20px 30px;
        }

        .tajwid-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .tajwid-card:hover {
            border-color: var(--primary-light);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.08);
        }

        .tw-icon {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .tw-info {
            flex-grow: 1;
        }

        .tw-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .tw-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .tw-arrow {
            color: #cbd5e1;
            font-size: 1rem;
            transition: 0.3s;
        }

        .tajwid-card:hover .tw-arrow {
            color: var(--primary-light);
            transform: translateX(3px);
        }

        /* Media Queries Desktop */
        @media (min-width: 768px) {
            .hero-section {
                padding-top: 50px;
                border-bottom-left-radius: 60px;
                border-bottom-right-radius: 60px;
            }

            .floating-card {
                min-width: 48%;
            }

            .quick-grid {
                grid-template-columns: repeat(5, 1fr);
            }

            /* 5 items per row on desktop */
            .tajwid-list {
                flex-direction: row;
            }

            .tajwid-card {
                flex: 1;
                flex-direction: column;
                text-align: center;
                padding: 25px 20px;
            }

            .tajwid-card:hover .tw-arrow {
                transform: translateX(0) translateY(3px);
            }
        }
    </style>
</head>

<body>

    <div class="hero-section">
        <div class="top-bar">
            <!-- Tombol Lokasi (Bisa Diklik) -->
            <div class="location-badge" onclick="triggerLocationUpdate()">
                <i class="fas fa-map-marker-alt"></i>
                <span id="location-text">Mencari Lokasi...</span>
            </div>
            <div class="profile-btn"><?= strtoupper(substr($_SESSION['nama_lengkap'] ?? 'U', 0, 1)) ?></div>
        </div>

        <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>

        <div class="date-time-row">
            <div class="date-box">
                <div class="label">Hijriah</div>
                <div class="value" id="hijri-date">--</div>
            </div>
            <div class="realtime-clock" id="clock">00:00:00</div>
            <div class="date-box">
                <div class="label">Masehi</div>
                <div class="value" id="masehi-date">--</div>
            </div>
        </div>

        <div class="prayer-row" id="prayer-container">
            <div style="font-size: 0.85rem; width: 100%; text-align: center; font-weight:600;">Menyelaraskan jadwal sholat...</div>
        </div>

        <div class="countdown-text" id="countdown-text">--:--:-- menuju waktu sholat berikutnya</div>
    </div>

    <div class="main-content">

        <!-- SLIDER KARTU PROGRESS -->
        <div class="slider-container" id="cardSlider" onscroll="updateDots()">

            <!-- 1. KARTU TILAWAH -->
            <a href="baca.php?surah=<?= $bm_surah ?>&ayat=<?= $bm_ayat ?>" class="floating-card">
                <div class="fc-left">
                    <div class="progress-circle">
                        <div class="progress-inner"><i class="fas fa-book-open"></i></div>
                    </div>
                    <div class="fc-text">
                        <h4>Lanjutkan Baca</h4>
                        <h3 id="bm-surah-title">Memuat...</h3>
                        <div class="fc-badges">
                            <span class="fc-badge" id="bm-surah-no">Surah Ke-<?= $bm_surah ?></span>
                            <span class="fc-badge">Ayat <?= $bm_ayat ?></span>
                        </div>
                    </div>
                </div>
                <div class="fc-arrow"><i class="fas fa-play"></i></div>
            </a>

            <!-- 2. KARTU HAFALAN -->
            <div class="floating-card" style="opacity: 0.8;">
                <div class="coming-soon-overlay">
                    <div class="cs-badge">Segera Hadir</div>
                </div>
                <div class="fc-left">
                    <div class="progress-circle" style="background: conic-gradient(#94a3b8 100%, #e5e7eb 0);">
                        <div class="progress-inner" style="color: #64748b;"><i class="fas fa-brain"></i></div>
                    </div>
                    <div class="fc-text">
                        <h4>Target Hafalan</h4>
                        <h3>Surah Al-Mulk</h3>
                        <div class="fc-badges"><span class="fc-badge">Ayat 1-10</span></div>
                    </div>
                </div>
                <div class="fc-arrow"><i class="fas fa-lock"></i></div>
            </div>

            <!-- 3. KARTU MUROJAAH -->
            <div class="floating-card" style="opacity: 0.8;">
                <div class="coming-soon-overlay">
                    <div class="cs-badge">Segera Hadir</div>
                </div>
                <div class="fc-left">
                    <div class="progress-circle" style="background: conic-gradient(#94a3b8 100%, #e5e7eb 0);">
                        <div class="progress-inner" style="color: #64748b;"><i class="fas fa-sync-alt"></i></div>
                    </div>
                    <div class="fc-text">
                        <h4>Jadwal Murojaah</h4>
                        <h3>Surah Yasin</h3>
                        <div class="fc-badges"><span class="fc-badge">Waktunya Review</span></div>
                    </div>
                </div>
                <div class="fc-arrow"><i class="fas fa-lock"></i></div>
            </div>

        </div>
        <div class="slider-dots">
            <div class="dot active" id="dot1"></div>
            <div class="dot" id="dot2"></div>
            <div class="dot" id="dot3"></div>
        </div>

        <!-- MENU EKSPLORASI -->
        <div class="section-header">
            <h3 class="section-title" style="margin:0;">Menu Utama</h3>
        </div>
        <div class="quick-grid" id="menuGrid">
            <!-- Selalu Tampil (7 Menu) -->
            <a href="alquran.php" class="q-item">
                <div class="q-icon"><i class="fas fa-book-quran"></i></div>
                <div class="q-text">Qur'an</div>
            </a>
            <a href="hafalan.php" class="q-item">
                <div class="q-icon"><i class="fas fa-brain"></i></div>
                <div class="q-text">Hafalan</div>
            </a>
            <a href="smart_murojaah.php" class="q-item">
                <div class="q-icon"><i class="fas fa-microphone-alt"></i></div>
                <div class="q-text">Murojaah</div>
            </a>
            <a href="mutabaah.php" class="q-item">
                <div class="q-icon"><i class="fas fa-chart-line"></i></div>
                <div class="q-text">Mutabaah</div>
            </a>
            <a href="tajwid.php" class="q-item">
                <div class="q-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="q-text">Tajwid</div>
            </a>
            <a href="doa.php" class="q-item">
                <div class="q-icon"><i class="fas fa-hands-praying"></i></div>
                <div class="q-text">Doa</div>
            </a>
            <a href="target.php" class="q-item">
                <div class="q-icon"><i class="fas fa-bullseye"></i></div>
                <div class="q-text">Target</div>
            </a>

            <!-- Tombol Selengkapnya -->
            <a href="javascript:void(0)" class="q-item" id="toggleMenuBtn" onclick="toggleMoreMenu()">
                <div class="q-icon" style="background:#f1f5f9; border-color:#e2e8f0; color:#64748b;"><i class="fas fa-th-large" id="toggleIcon"></i></div>
                <div class="q-text" id="toggleText">Lainnya</div>
            </a>

            <!-- Menu Tersembunyi -->
            <a href="#" class="q-item hidden-menu" onclick="infoComingSoon()">
                <div class="q-icon"><i class="fas fa-medal"></i></div>
                <div class="q-text">Pencapaian</div>
            </a>
            <a href="#" class="q-item hidden-menu" onclick="infoComingSoon()">
                <div class="q-icon"><i class="fas fa-robot"></i></div>
                <div class="q-text">AI Coach</div>
            </a>
            <a href="game.php" class="q-item hidden-menu">
                <div class="q-icon"><i class="fas fa-gamepad"></i></div>
                <div class="q-text">Game</div>
            </a>
        </div>

        <!-- TAJWID TERBARU -->
        <div class="section-header">
            <h3 class="section-title" style="margin:0;">Pelajaran Tajwid</h3>
            <a href="tajwid.php" style="font-size:0.85rem; color:var(--primary-light); font-weight:700; text-decoration:none;">Lihat Semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="tajwid-list">
            <?php foreach ($tajwid_lessons as $tw): ?>
                <a href="tajwid_detail.php?id=<?= $tw['id'] ?>" class="tajwid-card">
                    <div class="tw-icon" style="background: <?= $tw['color'] ?>;">
                        <i class="fas <?= $tw['icon'] ?>"></i>
                    </div>
                    <div class="tw-info">
                        <h4 class="tw-title"><?= htmlspecialchars($tw['title']) ?></h4>
                        <p class="tw-desc"><?= htmlspecialchars($tw['desc']) ?></p>
                    </div>
                    <i class="fas fa-chevron-right tw-arrow"></i>
                </a>
            <?php endforeach; ?>
        </div>

    </div>

    <!-- Panggil Navigasi Bawah -->
    <?php include '../components/nav.php'; ?>

    <script>
        // --- 1. TOGGLE MENU SELENGKAPNYA ---
        function toggleMoreMenu() {
            const hiddenItems = document.querySelectorAll('.hidden-menu');
            const icon = document.getElementById('toggleIcon');
            const text = document.getElementById('toggleText');

            let isExpanded = text.innerText === 'Tutup';

            if (isExpanded) {
                hiddenItems.forEach(el => el.style.display = 'none');
                text.innerText = 'Lainnya';
                icon.className = 'fas fa-th-large';
            } else {
                hiddenItems.forEach(el => el.style.display = 'flex');
                text.innerText = 'Tutup';
                icon.className = 'fas fa-chevron-up';
            }
        }

        function infoComingSoon() {
            Swal.fire({
                icon: 'info',
                title: 'Insya Allah Segera Hadir',
                text: 'Fitur ini sedang dalam tahap pengembangan.',
                confirmButtonColor: '#059669',
                confirmButtonText: 'Alhamdulillah'
            });
        }

        // --- 2. BOOKMARK DATA ---
        fetch(`https://equran.id/api/v2/surat/<?= $bm_surah ?>`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('bm-surah-title').innerText = 'Surah ' + data.data.namaLatin;
            })
            .catch(e => {
                document.getElementById('bm-surah-title').innerText = 'Lanjutkan Tilawah';
            });

        function updateDots() {
            const slider = document.getElementById('cardSlider');
            const activeIndex = Math.round(slider.scrollLeft / slider.offsetWidth);
            document.querySelectorAll('.dot').forEach((dot, index) => {
                dot.classList.toggle('active', index === activeIndex);
            });
        }

        // --- 3. SISTEM LOKASI SMART CACHE & JADWAL SHOLAT ---
        let prayerTimesData = null;

        function updateClock() {
            const now = new Date();
            const format = n => String(n).padStart(2, '0');
            document.getElementById('clock').innerText = `${format(now.getHours())}:${format(now.getMinutes())}:${format(now.getSeconds())}`;
            if (prayerTimesData) updateCountdown(now);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Cek Cache Lokasi saat halaman dimuat
        function initLocation() {
            const savedLat = localStorage.getItem('hifzly_lat');
            const savedLon = localStorage.getItem('hifzly_lon');
            const savedCity = localStorage.getItem('hifzly_city');

            if (savedLat && savedLon && savedCity) {
                document.getElementById('location-text').innerText = savedCity;
                fetchPrayerAPI(savedLat, savedLon);
            } else {
                executeLocationTracking(false);
            }
        }

        // Tombol Perbarui Lokasi (Manual Click)
        function triggerLocationUpdate() {
            Swal.fire({
                title: 'Perbarui Lokasi?',
                text: 'Sistem akan melacak posisi Anda saat ini untuk menyesuaikan jadwal sholat secara akurat.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: '<i class="fas fa-location-arrow"></i> Bismillah, Lacak!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('location-text').innerText = "Melacak...";
                    executeLocationTracking(true);
                }
            });
        }

        function executeLocationTracking(showSuccessAlert = false) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        fetchCityName(lat, lon, showSuccessAlert);
                        fetchPrayerAPI(lat, lon);
                    },
                    () => fallbackLocation(showSuccessAlert), {
                        enableHighAccuracy: true,
                        timeout: 5000
                    }
                );
            } else {
                fallbackLocation(showSuccessAlert);
            }
        }

        function fallbackLocation(showAlert) {
            const defLat = -6.1824,
                defLon = 106.3351,
                defCity = "Cikande, Banten";
            saveLocationData(defLat, defLon, defCity);
            if (showAlert) Swal.fire('Gagal Melacak', 'Izin lokasi ditolak/gagal. Menggunakan lokasi default.', 'warning');
        }

        async function fetchCityName(lat, lon, showAlert) {
            try {
                const res = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=id`);
                const data = await res.json();
                const cityName = data.city || data.locality || "Lokasi Ditemukan";
                saveLocationData(lat, lon, cityName);

                if (showAlert) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Alhamdulillah',
                        text: `Lokasi diperbarui ke ${cityName}`,
                        confirmButtonColor: '#059669',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } catch (e) {
                saveLocationData(lat, lon, "Lokasi Tersimpan");
            }
        }

        function saveLocationData(lat, lon, city) {
            localStorage.setItem('hifzly_lat', lat);
            localStorage.setItem('hifzly_lon', lon);
            localStorage.setItem('hifzly_city', city);
            document.getElementById('location-text').innerText = city;
            fetchPrayerAPI(lat, lon);
        }

        async function fetchPrayerAPI(lat, lon) {
            try {
                const res = await fetch(`https://api.aladhan.com/v1/timings?latitude=${lat}&longitude=${lon}&method=11`);
                const result = await res.json();
                const data = result.data;
                prayerTimesData = data.timings;

                document.getElementById('masehi-date').innerText = data.date.gregorian.date;
                document.getElementById('hijri-date').innerText = `${data.date.hijri.day} ${data.date.hijri.month.en} ${data.date.hijri.year}`;
                renderPrayerTimes();
            } catch (e) {
                document.getElementById('prayer-container').innerHTML = "<div style='color:red;'>Gagal memuat jadwal.</div>";
            }
        }

        const prayerConfig = [{
                id: 'Fajr',
                name: 'Subuh',
                icon: '<i class="fas fa-cloud-moon"></i>'
            },
            {
                id: 'Dhuhr',
                name: 'Dzuhur',
                icon: '<i class="fas fa-sun"></i>'
            },
            {
                id: 'Asr',
                name: 'Ashar',
                icon: '<i class="fas fa-cloud-sun"></i>'
            },
            {
                id: 'Maghrib',
                name: 'Maghrib',
                icon: '<i class="fas fa-moon"></i>'
            },
            {
                id: 'Isha',
                name: 'Isya',
                icon: '<i class="fas fa-star"></i>'
            }
        ];

        function renderPrayerTimes() {
            let html = '';
            prayerConfig.forEach(p => {
                html += `<div class="prayer-item" id="pr-${p.id}">
                    <div class="p-time">${prayerTimesData[p.id]}</div>
                    <div class="p-icon">${p.icon}</div>
                    <div class="p-name">${p.name}</div>
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
                nextPrayerName = 'Subuh';
                activeId = 'Fajr';
            }

            document.querySelectorAll('.prayer-item').forEach(el => el.classList.remove('active'));
            if (document.getElementById(`pr-${activeId}`)) document.getElementById(`pr-${activeId}`).classList.add('active');

            const diffMs = nextPrayerTimeDate - now;
            const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
            const diffMins = Math.floor((diffMs % 3600000) / 60000);
            const diffSecs = Math.floor((diffMs % 60000) / 1000);
            const format = (num) => String(num).padStart(2, '0');

            document.getElementById('countdown-text').innerHTML = `<span>${format(diffHrs)}:${format(diffMins)}:${format(diffSecs)}</span> menuju ${nextPrayerName}`;
        }

        window.onload = initLocation;
    </script>
</body>

</html>