<?php
session_start();
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
}

$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['role'] === 'user';

// Auto-create table + AJAX save handler untuk hafalan (mutabaah)
if ($is_logged_in && isset($conn)) {
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `mutabaah` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `activity_type` enum('tilawah','murojaah','hafalan_baru','setoran') NOT NULL,
        `activity_date` date NOT NULL,
        `activity_time` time NOT NULL,
        `surah` varchar(100) NOT NULL,
        `ayah_start` int(11) NOT NULL,
        `ayah_end` int(11) NOT NULL,
        `notes` text DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_hafalan_progress' && $is_logged_in && isset($conn)) {
    $uid = $_SESSION['user_id'];
    $surah = mysqli_real_escape_string($conn, $_POST['surah']);
    $ayat = (int)$_POST['ayat'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $today = date('Y-m-d');
    $now = date('H:i:s');
    $notes = ucfirst($status) . ' - ' . $surah . ' ayat ' . $ayat;
    $q = "INSERT INTO mutabaah (user_id, activity_type, activity_date, activity_time, surah, ayah_start, ayah_end, notes) 
          VALUES ('$uid', 'hafalan_baru', '$today', '$now', '$surah', '$ayat', '$ayat', '$notes')";
    mysqli_query($conn, $q);
    echo json_encode(['status' => 'success']);
    exit;
}

// Ambil data hafalan terbaru dari DB untuk sync antar device
$db_hafalan = null;
if ($is_logged_in && isset($conn)) {
    $uid = $_SESSION['user_id'];
    $q = mysqli_query($conn, "SELECT surah, ayah_end, created_at FROM mutabaah WHERE user_id='$uid' AND activity_type='hafalan_baru' ORDER BY created_at DESC LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        $db_hafalan = mysqli_fetch_assoc($q);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Hafalan Presisi — Hifzhly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* FONT KHUSUS MUSHAF MADINAH */
        @font-face {
            font-family: 'Uthmani';
            src: url('https://cdn.jsdelivr.net/gh/fawazahmed0/quran-api@1/fonts/KFGQPC_Uthmanic_Script_HAFS_Regular.ttf') format('truetype');
            font-display: swap;
        }

        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --dark: #0f172a;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --mushaf-line: #cbd5e1;
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
            overflow-x: hidden;
            padding-bottom: 30px;
            /* Mencegah seleksi teks saat long press */
            user-select: none;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
            width: 100%;
        }

        /* ANIMASI UI */
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-flip-left {
            animation: slideLeft 0.3s ease-out forwards;
        }

        .page-flip-right {
            animation: slideRight 0.3s ease-out forwards;
        }

        @keyframes slideLeft {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* HEADER GLOBAL */
        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            padding-top: 5px;
        }

        .back-btn {
            background: white;
            width: 45px;
            height: 45px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            border: 1px solid var(--border);
            transition: 0.2s;
            cursor: pointer;
        }

        .back-btn:active {
            transform: scale(0.9);
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .header-hint {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }

        /* VIEW 1: INDEX */
        #view-index {
            display: block;
        }

        .last-read-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 18px;
            padding: 20px;
            color: white;
            margin-bottom: 25px;
            display: none;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.25);
            transition: 0.2s ease;
        }

        .last-read-card:active {
            transform: scale(0.98);
        }

        .last-read-info h3 {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .last-read-info p {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 4px;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.2);
        }

        .status-badge.lancar {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.ulang {
            background: #fee2e2;
            color: #991b1b;
        }

        .last-read-icon {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .search-box {
            width: 100%;
            padding: 14px 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
            font-size: 0.95rem;
            margin-bottom: 20px;
            outline: none;
            font-weight: 500;
        }

        .search-box:focus {
            border-color: var(--primary);
        }

        .surah-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        @media (min-width: 768px) {
            .surah-list {
                grid-template-columns: 1fr 1fr;
            }
        }

        .surah-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: 0.2s;
        }

        .surah-card:active {
            transform: scale(0.98);
            background: #f8fafc;
        }

        .surah-num {
            width: 38px;
            height: 38px;
            background: #f1f5f9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--primary);
        }

        .surah-info h3 {
            font-size: 1rem;
            font-weight: 700;
        }

        .surah-info p {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
        }

        .surah-arab {
            font-family: 'Uthmani', serif;
            font-size: 1.3rem;
            color: var(--dark);
        }

        /* VIEW 2: MUSHAF */
        #view-mushaf {
            display: none;
        }

        /* KONTROL ATAS */
        .top-controls {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 10px 15px;
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .page-navigator {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #f1f5f9;
            color: var(--dark);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
        }

        .btn-nav-icon:active:not(:disabled) {
            background: var(--primary);
            color: white;
            transform: scale(0.9);
        }

        .btn-nav-icon:disabled {
            opacity: 0.3;
        }

        .page-indicator {
            font-weight: 800;
            font-size: 0.9rem;
            min-width: 55px;
            text-align: center;
        }

        .sensor-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-sensor {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-sensor:active:not(:disabled) {
            background: #e2e8f0;
        }

        .sensor-val {
            font-weight: 800;
            font-size: 0.85rem;
            color: var(--primary);
            min-width: 50px;
            text-align: center;
        }

        .btn-audio-top {
            background: var(--dark);
            color: white;
            border: none;
            height: 34px;
            padding: 0 15px;
            border-radius: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-audio-top.playing {
            background: #ef4444;
        }

        /* MUSHAF TEKS (DIPERBAIKI UNTUK MOBILE) */
        .quran-page {
            display: flex;
            flex-direction: column;
            width: 100%;
            touch-action: pan-y;
        }

        .mushaf-line {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            flex-wrap: nowrap;
            border-bottom: 1px dashed var(--mushaf-line);
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .mushaf-line:last-child {
            border-bottom: none;
        }

        .mushaf-line.centered {
            justify-content: center;
            gap: 8px;
        }

        .ayah-word {
            font-family: 'Uthmani', serif;
            /* Perbaikan: Ukuran maksimal diturunkan sedikit agar tidak meluber di HP layar sempit */
            font-size: clamp(1rem, 4vw, 2.2rem);
            line-height: 1.7;
            color: #000000;
            transition: all 0.2s ease;
            position: relative;
            padding: 0 1px;
            /* Dikurangi agar muat */
            white-space: nowrap;
            cursor: pointer;
        }

        /* Highlight saat long press */
        .ayah-word.pressed {
            background: rgba(5, 150, 105, 0.1);
            border-radius: 4px;
        }

        .ayah-word.hidden-word {
            color: transparent;
            border-bottom: 2px solid #000000;
            user-select: none;
        }

        .ayah-word.hidden-word:active {
            color: #dddddd;
        }

        .ayah-word.active-audio {
            color: var(--primary);
            background: #d1fae5;
            border-radius: 5px;
        }

        .ayah-end {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(26px, 5.5vw, 40px);
            height: clamp(26px, 5.5vw, 40px);
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%23000000" stroke-width="5"/><circle cx="50" cy="50" r="36" fill="none" stroke="%23000000" stroke-width="1.5" stroke-dasharray="3,3"/></svg>') no-repeat center;
            background-size: contain;
            font-size: clamp(0.6rem, 1.8vw, 0.9rem);
            color: #000000;
            margin: 0 2px;
            font-weight: 800;
            flex-shrink: 0;
            cursor: pointer;
        }

        .surah-title-banner {
            width: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="30" viewBox="0 0 100 30" preserveAspectRatio="none"><rect x="0" y="0" width="100" height="30" fill="%23ffffff" stroke="%23000000" stroke-width="3"/></svg>') no-repeat center;
            background-size: 100% 100%;
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: clamp(1.1rem, 4vw, 1.5rem);
            color: #000000;
            padding: 6px 0;
            margin: 15px 0;
            font-weight: bold;
        }

        .bismillah {
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: clamp(1.3rem, 4.5vw, 2rem);
            margin: 5px 0 15px 0;
            width: 100%;
        }

        .skeleton-wrapper {
            display: none;
            width: 100%;
        }

        .skeleton-line {
            height: clamp(1.2rem, 4.5vw, 2.2rem);
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* CUSTOM TOAST ALERT */
        #customToast {
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: top 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        #customToast.show {
            top: 20px;
        }

        /* ==============================================
           MODAL / BOTTOM SHEET (Tahan Lama Ayat)
           ============================================== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            z-index: 2000;
            display: none;
            align-items: flex-end;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .modal-overlay.show {
            opacity: 1;
        }

        .bottom-sheet {
            background: white;
            width: 100%;
            max-width: 500px;
            border-radius: 24px 24px 0 0;
            padding: 25px 20px 30px;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .modal-overlay.show .bottom-sheet {
            transform: translateY(0);
        }

        .sheet-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .sheet-handle {
            width: 40px;
            height: 5px;
            background: #cbd5e1;
            border-radius: 10px;
            margin: 0 auto 15px;
        }

        .sheet-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sheet-btn {
            width: 100%;
            padding: 14px;
            border-radius: 14px;
            border: none;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: 0.2s;
        }

        .sheet-btn.play {
            background: #f1f5f9;
            color: var(--dark);
        }

        .sheet-btn.play:active {
            background: #e2e8f0;
        }

        .sheet-btn.save-lancar {
            background: #d1fae5;
            color: #047857;
        }

        .sheet-btn.save-lancar:active {
            background: #a7f3d0;
        }

        .sheet-btn.save-ulang {
            background: #fee2e2;
            color: #b91c1c;
        }

        .sheet-btn.save-ulang:active {
            background: #fecaca;
        }

        @media (min-width: 600px) {
            .top-controls {
                justify-content: center;
            }

            .sensor-group,
            .btn-audio-top {
                flex-grow: 1;
                justify-content: center;
            }

            /* Modal di Desktop jadi Popup Tengah */
            .modal-overlay {
                align-items: center;
            }

            .bottom-sheet {
                border-radius: 20px;
                transform: scale(0.9);
                padding: 25px;
            }

            .modal-overlay.show .bottom-sheet {
                transform: scale(1);
            }

            .sheet-handle {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!-- Kustom Alert -->
    <div id="customToast"><i class="fas fa-check-circle" style="color: #10b981;"></i><span id="toastMessage">Tersimpan</span></div>

    <!-- MODAL / BOTTOM SHEET MENU AYAT -->
    <div class="modal-overlay" id="ayahModal" onclick="closeAyahMenu(event)">
        <div class="bottom-sheet" onclick="event.stopPropagation()">
            <div class="sheet-handle"></div>
            <div class="sheet-header">
                <h3 class="sheet-title" id="sheetAyahTitle">Ayat -</h3>
            </div>

            <button class="sheet-btn play" onclick="playSingleAyah()">
                <i class="fas fa-play-circle"></i> Putar Audio Ayat Ini
            </button>
            <div style="height: 1px; background: var(--border); margin: 5px 0;"></div>
            <p style="text-align: center; font-size: 0.8rem; color: #64748b; font-weight: 600; margin:0;">Evaluasi Hafalan & Simpan</p>
            <button class="sheet-btn save-lancar" onclick="saveAyahProgress('lancar')">
                <i class="fas fa-check-circle"></i> Lancar (Aman)
            </button>
            <button class="sheet-btn save-ulang" onclick="saveAyahProgress('ulang')">
                <i class="fas fa-redo"></i> Perlu Diulang (Uraja'ah)
            </button>
        </div>
    </div>

    <div class="container">
        <!-- VIEW 1: INDEX -->
        <div id="view-index" class="fade-in">
            <div class="header">
                <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <div>
                    <h1 class="page-title">Pilih Surah</h1>
                    <div class="header-hint">Pilih surah untuk mulai menghafal</div>
                </div>
            </div>

            <!-- CARD LANJUTKAN HAFALAN -->
            <div class="last-read-card" id="lastReadCard" onclick="continueReading()">
                <div class="last-read-info">
                    <h3>Lanjutkan Hafalan</h3>
                    <p id="lastReadText">Memuat...</p>
                    <span id="lastReadStatus" class="status-badge"></span>
                </div>
                <div class="last-read-icon"><i class="fas fa-book-open"></i></div>
            </div>

            <input type="text" id="searchInput" class="search-box" placeholder="Cari nama surah... (Cth: Al-Kahf)" onkeyup="filterSurah()">
            <div class="surah-list" id="surahListContainer"></div>
        </div>

        <!-- VIEW 2: MUSHAF -->
        <div id="view-mushaf">
            <div class="header">
                <button class="back-btn" onclick="closeMushaf()"><i class="fas fa-arrow-left"></i></button>
                <div>
                    <h1 class="page-title" id="pageSurahName">Surah...</h1>
                    <div class="header-hint"><i class="fas fa-hand-pointer"></i> Tahan ayat untuk menu • Geser untuk pindah</div>
                </div>
            </div>

            <div class="top-controls fade-in">
                <div class="page-navigator">
                    <button class="btn-nav-icon" id="btnNext" onclick="changePage(1)"><i class="fas fa-chevron-left"></i></button>
                    <div class="page-indicator" id="pageNumberLabel">Hal 1</div>
                    <button class="btn-nav-icon" id="btnPrev" onclick="changePage(-1)"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="sensor-group">
                    <button class="btn-sensor" onclick="changeSensor(-20)" id="btnMinSensor"><i class="fas fa-minus"></i></button>
                    <div class="sensor-val" id="lblSensor">0%</div>
                    <button class="btn-sensor" onclick="changeSensor(20)" id="btnMaxSensor"><i class="fas fa-plus"></i></button>
                </div>
                <button class="btn-audio-top" id="btnAudio" onclick="toggleAudioFull()">
                    <i class="fas fa-play"></i> <span>Audio Hal</span>
                </button>
            </div>

            <div class="skeleton-wrapper" id="loader">
                <?php for ($i = 0; $i < 15; $i++): ?>
                    <div class="skeleton-line" style="width: <?= rand(85, 100) ?>%;"></div>
                <?php endfor; ?>
            </div>

            <div class="quran-page" id="quranPage"></div>
        </div>
    </div>

    <audio id="quranAudio" onended="handleAudioEnd()"></audio>

    <?php if ($is_logged_in) include '../components/nav.php'; ?>

    <script>
        function showToast(message, isError = false) {
            const toast = document.getElementById('customToast');
            const msgEl = document.getElementById('toastMessage');
            const icon = toast.querySelector('i');
            msgEl.innerText = message;
            if (isError) {
                icon.className = 'fas fa-exclamation-circle';
                icon.style.color = '#ef4444';
            } else {
                icon.className = 'fas fa-check-circle';
                icon.style.color = '#10b981';
            }
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2500);
        }

        const surahs = [{
                id: 1,
                name: "Al-Fatihah",
                arab: "الفاتحة",
                page: 1
            }, {
                id: 2,
                name: "Al-Baqarah",
                arab: "البقرة",
                page: 2
            }, {
                id: 3,
                name: "Ali 'Imran",
                arab: "آل عمران",
                page: 50
            }, {
                id: 4,
                name: "An-Nisa'",
                arab: "النساء",
                page: 77
            }, {
                id: 5,
                name: "Al-Ma'idah",
                arab: "المائدة",
                page: 106
            }, {
                id: 6,
                name: "Al-An'am",
                arab: "الأنعام",
                page: 128
            }, {
                id: 7,
                name: "Al-A'raf",
                arab: "الأعراف",
                page: 151
            }, {
                id: 8,
                name: "Al-Anfal",
                arab: "الأنفال",
                page: 177
            }, {
                id: 9,
                name: "At-Taubah",
                arab: "التوبة",
                page: 187
            }, {
                id: 10,
                name: "Yunus",
                arab: "يونس",
                page: 208
            },
            {
                id: 11,
                name: "Hud",
                arab: "هود",
                page: 221
            }, {
                id: 12,
                name: "Yusuf",
                arab: "يوسف",
                page: 235
            }, {
                id: 13,
                name: "Ar-Ra'd",
                arab: "الرعد",
                page: 249
            }, {
                id: 14,
                name: "Ibrahim",
                arab: "إبراهيم",
                page: 255
            }, {
                id: 15,
                name: "Al-Hijr",
                arab: "الحجر",
                page: 262
            }, {
                id: 16,
                name: "An-Nahl",
                arab: "النحل",
                page: 267
            }, {
                id: 17,
                name: "Al-Isra'",
                arab: "الإسراء",
                page: 282
            }, {
                id: 18,
                name: "Al-Kahf",
                arab: "الكهف",
                page: 293
            }, {
                id: 19,
                name: "Maryam",
                arab: "مريم",
                page: 305
            }, {
                id: 20,
                name: "Taha",
                arab: "طه",
                page: 312
            },
            {
                id: 21,
                name: "Al-Anbiya'",
                arab: "الأنبياء",
                page: 322
            }, {
                id: 22,
                name: "Al-Hajj",
                arab: "الحج",
                page: 332
            }, {
                id: 23,
                name: "Al-Mu'minun",
                arab: "المؤمنون",
                page: 342
            }, {
                id: 24,
                name: "An-Nur",
                arab: "النور",
                page: 350
            }, {
                id: 25,
                name: "Al-Furqan",
                arab: "الفرقان",
                page: 359
            }, {
                id: 26,
                name: "Asy-Syu'ara'",
                arab: "الشعراء",
                page: 367
            }, {
                id: 27,
                name: "An-Naml",
                arab: "النمل",
                page: 377
            }, {
                id: 28,
                name: "Al-Qasas",
                arab: "القصص",
                page: 385
            }, {
                id: 29,
                name: "Al-'Ankabut",
                arab: "العنكبوت",
                page: 396
            }, {
                id: 30,
                name: "Ar-Rum",
                arab: "الروم",
                page: 404
            },
            {
                id: 31,
                name: "Luqman",
                arab: "لقمان",
                page: 411
            }, {
                id: 32,
                name: "As-Sajdah",
                arab: "السجدة",
                page: 415
            }, {
                id: 33,
                name: "Al-Ahzab",
                arab: "الأحزاب",
                page: 418
            }, {
                id: 34,
                name: "Saba'",
                arab: "سبأ",
                page: 428
            }, {
                id: 35,
                name: "Fatir",
                arab: "فاطر",
                page: 434
            }, {
                id: 36,
                name: "Yasin",
                arab: "يس",
                page: 440
            }, {
                id: 37,
                name: "As-Saffat",
                arab: "الصافات",
                page: 446
            }, {
                id: 38,
                name: "Sad",
                arab: "ص",
                page: 453
            }, {
                id: 39,
                name: "Az-Zumar",
                arab: "الزمر",
                page: 458
            }, {
                id: 40,
                name: "Ghafir",
                arab: "غافر",
                page: 467
            },
            {
                id: 41,
                name: "Fussilat",
                arab: "فصلت",
                page: 477
            }, {
                id: 42,
                name: "Asy-Syura",
                arab: "الشورى",
                page: 483
            }, {
                id: 43,
                name: "Az-Zukhruf",
                arab: "الزخرف",
                page: 489
            }, {
                id: 44,
                name: "Ad-Dukhan",
                arab: "الدخان",
                page: 496
            }, {
                id: 45,
                name: "Al-Jasiyah",
                arab: "الجاثية",
                page: 499
            }, {
                id: 46,
                name: "Al-Ahqaf",
                arab: "الأحقاف",
                page: 502
            }, {
                id: 47,
                name: "Muhammad",
                arab: "محمد",
                page: 507
            }, {
                id: 48,
                name: "Al-Fath",
                arab: "الفتح",
                page: 511
            }, {
                id: 49,
                name: "Al-Hujurat",
                arab: "الحجرات",
                page: 515
            }, {
                id: 50,
                name: "Qaf",
                arab: "ق",
                page: 518
            },
            {
                id: 51,
                name: "Az-Zariyat",
                arab: "الذاريات",
                page: 520
            }, {
                id: 52,
                name: "At-Tur",
                arab: "الطور",
                page: 523
            }, {
                id: 53,
                name: "An-Najm",
                arab: "النجم",
                page: 526
            }, {
                id: 54,
                name: "Al-Qamar",
                arab: "القمر",
                page: 528
            }, {
                id: 55,
                name: "Ar-Rahman",
                arab: "الرحمن",
                page: 531
            }, {
                id: 56,
                name: "Al-Waqi'ah",
                arab: "الواقعة",
                page: 534
            }, {
                id: 57,
                name: "Al-Hadid",
                arab: "الحديد",
                page: 537
            }, {
                id: 58,
                name: "Al-Mujadilah",
                arab: "المجادلة",
                page: 542
            }, {
                id: 59,
                name: "Al-Hasyr",
                arab: "الحشر",
                page: 545
            }, {
                id: 60,
                name: "Al-Mumtahanah",
                arab: "الممتحنة",
                page: 549
            },
            {
                id: 61,
                name: "As-Saff",
                arab: "الصف",
                page: 551
            }, {
                id: 62,
                name: "Al-Jumu'ah",
                arab: "الجمعة",
                page: 553
            }, {
                id: 63,
                name: "Al-Munafiqun",
                arab: "المنافقون",
                page: 554
            }, {
                id: 64,
                name: "At-Tagabun",
                arab: "التغابن",
                page: 556
            }, {
                id: 65,
                name: "At-Talaq",
                arab: "الطلاق",
                page: 558
            }, {
                id: 66,
                name: "At-Tahrim",
                arab: "التحريم",
                page: 560
            }, {
                id: 67,
                name: "Al-Mulk",
                arab: "الملك",
                page: 562
            }, {
                id: 68,
                name: "Al-Qalam",
                arab: "القلم",
                page: 564
            }, {
                id: 69,
                name: "Al-Haqqah",
                arab: "الحاقة",
                page: 566
            }, {
                id: 70,
                name: "Al-Ma'arij",
                arab: "المعارج",
                page: 568
            },
            {
                id: 71,
                name: "Nuh",
                arab: "نوح",
                page: 570
            }, {
                id: 72,
                name: "Al-Jinn",
                arab: "الجن",
                page: 572
            }, {
                id: 73,
                name: "Al-Muzzammil",
                arab: "المزمل",
                page: 574
            }, {
                id: 74,
                name: "Al-Muddassir",
                arab: "المدثر",
                page: 575
            }, {
                id: 75,
                name: "Al-Qiyamah",
                arab: "القيامة",
                page: 577
            }, {
                id: 76,
                name: "Al-Insan",
                arab: "الإنسان",
                page: 578
            }, {
                id: 77,
                name: "Al-Mursalat",
                arab: "المرسلات",
                page: 580
            }, {
                id: 78,
                name: "An-Naba'",
                arab: "النبأ",
                page: 582
            }, {
                id: 79,
                name: "An-Nazi'at",
                arab: "النازعات",
                page: 583
            }, {
                id: 80,
                name: "'Abasa",
                arab: "عبس",
                page: 585
            },
            {
                id: 81,
                name: "At-Takwir",
                arab: "التكوير",
                page: 586
            }, {
                id: 82,
                name: "Al-Infitar",
                arab: "الانفطار",
                page: 587
            }, {
                id: 83,
                name: "Al-Mutaffifin",
                arab: "المطففين",
                page: 587
            }, {
                id: 84,
                name: "Al-Insyiqaq",
                arab: "الانشقاق",
                page: 589
            }, {
                id: 85,
                name: "Al-Buruj",
                arab: "البروج",
                page: 590
            }, {
                id: 86,
                name: "At-Tariq",
                arab: "الطارق",
                page: 591
            }, {
                id: 87,
                name: "Al-A'la",
                arab: "الأعلى",
                page: 591
            }, {
                id: 88,
                name: "Al-Gasyiyah",
                arab: "الغاشية",
                page: 592
            }, {
                id: 89,
                name: "Al-Fajr",
                arab: "الفجر",
                page: 593
            }, {
                id: 90,
                name: "Al-Balad",
                arab: "البلد",
                page: 594
            },
            {
                id: 91,
                name: "Asy-Syams",
                arab: "الشمس",
                page: 595
            }, {
                id: 92,
                name: "Al-Lail",
                arab: "الليل",
                page: 595
            }, {
                id: 93,
                name: "Ad-Duha",
                arab: "الضحى",
                page: 596
            }, {
                id: 94,
                name: "Asy-Syarh",
                arab: "الشرح",
                page: 596
            }, {
                id: 95,
                name: "At-Tin",
                arab: "التين",
                page: 597
            }, {
                id: 96,
                name: "Al-'Alaq",
                arab: "العلق",
                page: 597
            }, {
                id: 97,
                name: "Al-Qadr",
                arab: "القدر",
                page: 598
            }, {
                id: 98,
                name: "Al-Bayyinah",
                arab: "البينة",
                page: 598
            }, {
                id: 99,
                name: "Az-Zalzalah",
                arab: "الزلزلة",
                page: 599
            }, {
                id: 100,
                name: "Al-'Adiyat",
                arab: "العاديات",
                page: 599
            },
            {
                id: 101,
                name: "Al-Qari'ah",
                arab: "القارعة",
                page: 600
            }, {
                id: 102,
                name: "At-Takasur",
                arab: "التكاثر",
                page: 600
            }, {
                id: 103,
                name: "Al-'Asr",
                arab: "العصر",
                page: 601
            }, {
                id: 104,
                name: "Al-Humazah",
                arab: "الهمزة",
                page: 601
            }, {
                id: 105,
                name: "Al-Fil",
                arab: "الفيل",
                page: 601
            }, {
                id: 106,
                name: "Quraisy",
                arab: "قريش",
                page: 602
            }, {
                id: 107,
                name: "Al-Ma'un",
                arab: "الماعون",
                page: 602
            }, {
                id: 108,
                name: "Al-Kausar",
                arab: "الكوثر",
                page: 602
            }, {
                id: 109,
                name: "Al-Kafirun",
                arab: "الكافرون",
                page: 603
            }, {
                id: 110,
                name: "An-Nasr",
                arab: "النصر",
                page: 603
            },
            {
                id: 111,
                name: "Al-Lahab",
                arab: "المسد",
                page: 603
            }, {
                id: 112,
                name: "Al-Ikhlas",
                arab: "الإخلاص",
                page: 604
            }, {
                id: 113,
                name: "Al-Falaq",
                arab: "الفلق",
                page: 604
            }, {
                id: 114,
                name: "An-Nas",
                arab: "الناس",
                page: 604
            }
        ];

        let currentPage = 1;
        const totalPages = 604;
        let sensorLevel = 0;
        let wordElements = [];
        let shuffledIndices = [];

        // State Audio
        let audioPlaylists = [];
        let currentAyahIndex = -1;
        let isPlayingFull = false;
        let isPlayingSingle = false;
        const audioElement = document.getElementById('quranAudio');

        // State Save
        let savedPageToResume = null;
        const dbHafalan = <?= json_encode($db_hafalan) ?>;

        // State Menu Long Press
        let targetVerseKeyForMenu = null;
        let currentSurahNameGlobal = "";

        document.addEventListener('DOMContentLoaded', () => {
            renderSurahList(surahs);
            checkSavedProgress();
        });

        // ==========================================
        // Cek History Terakhir
        // ==========================================
        function checkSavedProgress() {
            // Langsung baca dari DB, bukan localStorage — biar lintas device sinkron
            if (dbHafalan && dbHafalan.surah) {
                const found = surahs.find(function(s) { return s.name === dbHafalan.surah; });
                if (found) {
                    savedPageToResume = found.page;
                    document.getElementById('lastReadText').innerText = `Surah ${dbHafalan.surah} • Ayat ${dbHafalan.ayah_end} (Hal ${found.page})`;
                    const badge = document.getElementById('lastReadStatus');
                    badge.className = 'status-badge lancar';
                    badge.innerText = "🟢 Hafalan Lancar";
                    document.getElementById('lastReadCard').style.display = 'flex';
                }
            }
        }

        function continueReading() {
            if (savedPageToResume) openMushaf(savedPageToResume);
        }

        function renderSurahList(dataList) {
            const container = document.getElementById('surahListContainer');
            container.innerHTML = '';
            dataList.forEach(s => {
                container.innerHTML += `
                    <div class="surah-card" onclick="openMushaf(${s.page})">
                        <div style="display:flex; align-items:center; gap:15px;">
                            <div class="surah-num">${s.id}</div>
                            <div class="surah-info"><h3>${s.name}</h3><p>Halaman ${s.page}</p></div>
                        </div>
                        <div class="surah-arab">${s.arab}</div>
                    </div>`;
            });
        }

        function filterSurah() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            renderSurahList(surahs.filter(s => s.name.toLowerCase().includes(query) || s.id.toString() === query));
        }

        function openMushaf(page) {
            document.getElementById('view-index').style.display = 'none';
            document.getElementById('view-mushaf').style.display = 'block';
            currentPage = page;
            loadQuranPage(currentPage);
            window.scrollTo(0, 0);
        }

        function closeMushaf() {
            stopAudio();
            document.getElementById('view-mushaf').style.display = 'none';
            document.getElementById('view-index').style.display = 'block';
            checkSavedProgress();
        }

        function changePage(direction) {
            let newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                stopAudio();
                const qPage = document.getElementById('quranPage');
                qPage.className = 'quran-page';
                void qPage.offsetWidth;
                if (direction > 0) qPage.classList.add('page-flip-left');
                else qPage.classList.add('page-flip-right');
                loadQuranPage(currentPage);
            }
        }

        // ==========================================
        // SWIPE & LONG PRESS LOGIC
        // ==========================================
        let touchstartX = 0;
        let touchendX = 0;
        const mushafArea = document.getElementById('quranPage');

        mushafArea.addEventListener('touchstart', e => {
            touchstartX = e.changedTouches[0].screenX;
        }, {
            passive: true
        });
        mushafArea.addEventListener('touchend', e => {
            touchendX = e.changedTouches[0].screenX;
            if (touchstartX - touchendX > 60) changePage(1); // Swipe Kiri
            if (touchendX - touchstartX > 60) changePage(-1); // Swipe Kanan
        }, {
            passive: true
        });

        let pressTimer;
        let isDragging = false;

        function onWordTouchStart(e, verseKey, el) {
            isDragging = false;
            el.classList.add('pressed');
            pressTimer = setTimeout(() => {
                if (!isDragging) openAyahMenu(verseKey);
                el.classList.remove('pressed');
            }, 600); // Tahan 0.6 detik
        }

        function onWordTouchMove(el) {
            isDragging = true;
            clearTimeout(pressTimer);
            el.classList.remove('pressed');
        }

        function onWordTouchEnd(el) {
            clearTimeout(pressTimer);
            el.classList.remove('pressed');
        }

        // ==========================================
        // MENU AYAT (MODAL/BOTTOM SHEET)
        // ==========================================
        function openAyahMenu(verseKey) {
            targetVerseKeyForMenu = verseKey;
            const ayahNum = verseKey.split(':')[1];
            document.getElementById('sheetAyahTitle').innerText = `Surah ${currentSurahNameGlobal}, Ayat ${ayahNum}`;

            const modal = document.getElementById('ayahModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);

            // Haptic feedback jika didukung browser HP
            if (navigator.vibrate) navigator.vibrate(50);
        }

        function closeAyahMenu(e) {
            if (e) e.stopPropagation();
            const modal = document.getElementById('ayahModal');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        function saveAyahProgress(status) {
            const ayahNum = targetVerseKeyForMenu.split(':')[1];
            const surahNum = targetVerseKeyForMenu.split(':')[0];
            const saveData = {
                page: currentPage,
                surahName: currentSurahNameGlobal,
                verseKey: targetVerseKeyForMenu,
                verseNum: ayahNum,
                status: status // 'lancar' atau 'ulang'
            };
            localStorage.setItem('hifzly_save_data', JSON.stringify(saveData));
            closeAyahMenu();
            showToast("Hafalan ayat berhasil disimpan!");
            const loggedIn = <?= $is_logged_in ? 'true' : 'false' ?>;
            if (loggedIn) {
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'action=save_hafalan_progress&surah=' + encodeURIComponent(currentSurahNameGlobal) + '&ayat=' + ayahNum + '&status=' + status
                }).catch(function(){});
            }
        }

        // ==========================================
        // SENSOR
        // ==========================================
        function changeSensor(amount) {
            sensorLevel += amount;
            if (sensorLevel < 0) sensorLevel = 0;
            if (sensorLevel > 100) sensorLevel = 100;
            document.getElementById('lblSensor').innerText = sensorLevel === 0 ? "Tampil" : `${sensorLevel}%`;
            document.getElementById('btnMinSensor').disabled = (sensorLevel === 0);
            document.getElementById('btnMaxSensor').disabled = (sensorLevel === 100);
            applySensor();
        }

        // ==========================================
        // LOAD QURAN (API)
        // ==========================================
        async function loadQuranPage(page) {
            document.getElementById('quranPage').innerHTML = '';
            document.getElementById('loader').style.display = 'block';
            document.getElementById('btnPrev').disabled = (page === 1);
            document.getElementById('btnNext').disabled = (page === totalPages);

            wordElements = [];
            audioPlaylists = [];
            currentAyahIndex = -1;

            try {
                const response = await fetch(`https://api.quran.com/api/v4/verses/by_page/${page}?language=id&words=true&word_fields=text_uthmani,line_number&audio=7`);
                const data = await response.json();
                renderExactMushafLayout(data.verses, page);
                applySensor();
            } catch (error) {
                showToast("Koneksi gagal", true);
            } finally {
                document.getElementById('loader').style.display = 'none';
            }
        }

        function renderExactMushafLayout(verses, pageNum) {
            const container = document.getElementById('quranPage');
            const firstSurahId = parseInt(verses[0].verse_key.split(':')[0]);
            currentSurahNameGlobal = surahs.find(s => s.id === firstSurahId)?.name || "";

            document.getElementById('pageSurahName').innerText = `Surah ${currentSurahNameGlobal}`;
            document.getElementById('pageNumberLabel').innerText = `Hal ${pageNum}`;

            let linesMap = {};
            let globalWordIdx = 0;

            verses.forEach((verse) => {
                if (verse.audio && verse.audio.url) {
                    audioPlaylists.push({
                        url: verse.audio.url.startsWith('http') ? verse.audio.url : `https://verses.quran.com/${verse.audio.url}`,
                        verseKey: verse.verse_key
                    });
                }

                const ayahNum = parseInt(verse.verse_key.split(':')[1]);
                const surahId = parseInt(verse.verse_key.split(':')[0]);

                if (ayahNum === 1) {
                    const sName = surahs.find(s => s.id === surahId)?.arab || `Surah ${surahId}`;
                    if (!linesMap['header_' + surahId]) linesMap['header_' + surahId] = [];
                    linesMap['header_' + surahId].push({
                        type: 'surah_header',
                        text: `سورة ${sName}`
                    });
                    if (surahId !== 1 && surahId !== 9) {
                        if (!linesMap['bismillah_' + surahId]) linesMap['bismillah_' + surahId] = [];
                        linesMap['bismillah_' + surahId].push({
                            type: 'bismillah',
                            text: 'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ'
                        });
                    }
                }

                verse.words.forEach(word => {
                    const vKey = verse.verse_key;
                    // Bind Long Press events
                    const events = `ontouchstart="onWordTouchStart(event, '${vKey}', this)" ontouchmove="onWordTouchMove(this)" ontouchend="onWordTouchEnd(this)" onmousedown="onWordTouchStart(event, '${vKey}', this)" onmouseleave="onWordTouchEnd(this)" onmouseup="onWordTouchEnd(this)"`;

                    if (word.char_type_name === 'end') {
                        if (!linesMap[word.line_number]) linesMap[word.line_number] = [];
                        linesMap[word.line_number].push({
                            type: 'end',
                            text: convertToArabicNumber(ayahNum),
                            verseKey: vKey,
                            events: events
                        });
                    } else if (word.text_uthmani) {
                        if (!linesMap[word.line_number]) linesMap[word.line_number] = [];
                        linesMap[word.line_number].push({
                            type: 'word',
                            text: word.text_uthmani,
                            id: globalWordIdx,
                            verseKey: vKey,
                            events: events
                        });
                        globalWordIdx++;
                    }
                });
            });

            let htmlContent = '';
            const sortedLines = Object.keys(linesMap).sort((a, b) => {
                if (a.includes('header') || a.includes('bismillah')) return -1;
                if (b.includes('header') || b.includes('bismillah')) return 1;
                return parseInt(a) - parseInt(b);
            });

            sortedLines.forEach(lineNum => {
                const items = linesMap[lineNum];
                if (items[0].type === 'surah_header') {
                    htmlContent += `<div class="surah-title-banner">${items[0].text}</div>`;
                    return;
                }
                if (items[0].type === 'bismillah') {
                    htmlContent += `<div class="bismillah">${items[0].text}</div>`;
                    return;
                }

                const isCentered = items.length < 6 ? 'centered' : '';
                htmlContent += `<div class="mushaf-line ${isCentered}">`;
                items.forEach(item => {
                    if (item.type === 'end') {
                        htmlContent += `<span class="ayah-end verse-${item.verseKey.replace(':','-')}" ${item.events}>${item.text}</span>`;
                    } else {
                        htmlContent += `<span class="ayah-word verse-${item.verseKey.replace(':','-')}" id="w-${item.id}" ${item.events}>${item.text}</span>`;
                    }
                });
                htmlContent += `</div>`;
            });

            container.innerHTML = htmlContent;

            wordElements = [];
            for (let i = 0; i < globalWordIdx; i++) {
                const el = document.getElementById(`w-${i}`);
                if (el) wordElements.push(el);
            }
            shuffledIndices = Array.from({
                length: wordElements.length
            }, (_, i) => i);
            shuffleArray(shuffledIndices);
        }

        function applySensor() {
            wordElements.forEach(el => el.classList.remove('hidden-word'));
            if (sensorLevel > 0 && wordElements.length > 0) {
                const hideCount = Math.floor((sensorLevel / 100) * wordElements.length);
                for (let i = 0; i < hideCount; i++) {
                    const idx = shuffledIndices[i];
                    if (wordElements[idx]) wordElements[idx].classList.add('hidden-word');
                }
            }
        }

        // ==========================================
        // AUDIO (PERBAIKAN FITUR STOP)
        // ==========================================
        function toggleAudioFull() {
            if (isPlayingFull || isPlayingSingle) {
                stopAudio();
            } else {
                if (audioPlaylists.length > 0) {
                    isPlayingFull = true;
                    isPlayingSingle = false;
                    document.getElementById('btnAudio').classList.add('playing');
                    document.getElementById('btnAudio').innerHTML = `<i class="fas fa-pause"></i> <span>Berhenti</span>`;
                    currentAyahIndex = -1;
                    playNextAyah();
                } else {
                    showToast("Audio tidak tersedia", true);
                }
            }
        }

        function playSingleAyah() {
            closeAyahMenu();
            stopAudio(); // pastikan audio lain mati dulu

            const targetData = audioPlaylists.find(a => a.verseKey === targetVerseKeyForMenu);
            if (targetData && targetData.url) {
                isPlayingSingle = true;
                highlightAyah(targetVerseKeyForMenu);
                audioElement.src = targetData.url;
                audioElement.play().catch(e => {
                    showToast("Gagal memutar audio", true);
                    stopAudio();
                });
            } else {
                showToast("Audio untuk ayat ini tidak tersedia", true);
            }
        }

        function playNextAyah() {
            if (!isPlayingFull) return; // Jika ditekan stop, jangan lanjut

            if (currentAyahIndex >= 0 && audioPlaylists[currentAyahIndex]) {
                removeHighlight(audioPlaylists[currentAyahIndex].verseKey);
            }

            currentAyahIndex++;

            if (currentAyahIndex < audioPlaylists.length) {
                const currentData = audioPlaylists[currentAyahIndex];
                highlightAyah(currentData.verseKey);

                if (currentData.url) {
                    audioElement.src = currentData.url;
                    audioElement.play().catch(e => playNextAyah());
                } else {
                    playNextAyah();
                }
            } else {
                stopAudio();
            }
        }

        function handleAudioEnd() {
            if (isPlayingFull) playNextAyah();
            else if (isPlayingSingle) stopAudio();
        }

        function stopAudio() {
            isPlayingFull = false;
            isPlayingSingle = false;
            audioElement.pause();
            audioElement.currentTime = 0;

            const btn = document.getElementById('btnAudio');
            btn.classList.remove('playing');
            btn.innerHTML = `<i class="fas fa-play"></i> <span>Audio Hal</span>`;

            document.querySelectorAll('.active-audio').forEach(el => el.classList.remove('active-audio'));
            currentAyahIndex = -1;
        }

        function highlightAyah(verseKey) {
            document.querySelectorAll(`.verse-${verseKey.replace(':','-')}`).forEach(el => el.classList.add('active-audio'));
        }

        function removeHighlight(verseKey) {
            document.querySelectorAll(`.verse-${verseKey.replace(':','-')}`).forEach(el => el.classList.remove('active-audio'));
        }

        // ==========================================
        // UTILS
        // ==========================================
        function convertToArabicNumber(num) {
            const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return num.toString().split('').map(digit => arabicNumbers[parseInt(digit)]).join('');
        }

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
        }
    </script>
</body>

</html>