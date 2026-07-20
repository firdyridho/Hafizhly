<?php
session_start();
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
}

$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['role'] === 'user';
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
            --dark: #0f172a;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --mushaf-line: #cbd5e1;
            /* Warna garis-garis tipis */
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
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
            width: 100%;
        }

        /* HEADER GLOBAL */
        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-top: 10px;
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
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            transition: 0.2s;
            cursor: pointer;
        }

        .back-btn:hover {
            background: var(--bg);
        }

        .page-title {
            font-size: 1.3rem;
            font-weight: 800;
        }

        /* =========================================
           TAMPILAN 1: DAFTAR SURAH (INDEX)
           ========================================= */
        #view-index {
            padding-bottom: 90px;
        }

        .search-box {
            width: 100%;
            padding: 14px 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
            font-size: 1rem;
            margin-bottom: 20px;
            background: white;
            outline: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            font-weight: 500;
        }

        .search-box:focus {
            border-color: var(--primary);
        }

        .surah-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
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
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .surah-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.1);
        }

        .surah-num {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--primary);
        }

        .surah-info h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .surah-info p {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        .surah-arab {
            font-family: 'Uthmani', serif;
            font-size: 1.4rem;
            color: var(--dark);
        }

        /* =========================================
           TAMPILAN 2: MUSHAF HAFALAN
           ========================================= */
        #view-mushaf {
            display: none;
            padding-bottom: 120px;
            /* Jarak untuk floating bar di bawah */
        }

        .mushaf-header {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--dark);
            border-bottom: 2px solid var(--dark);
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        /* PAGINASI DI ATAS (Sesuai Permintaan) */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            /* Pindah ke atas text Al-Qur'an */
            gap: 10px;
        }

        .btn-page {
            background: white;
            color: var(--dark);
            border: 1px solid var(--border);
            padding: 10px 15px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            flex: 1;
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .btn-page:hover:not(:disabled) {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .btn-page:disabled {
            opacity: 0.4;
            pointer-events: none;
        }

        /* GAYA GARIS-GARIS (Tanpa Kotak Besar) */
        .quran-page {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .mushaf-line {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            flex-wrap: nowrap;
            /* Inilah efek Garis-garis per barisnya */
            border-bottom: 1px dashed var(--mushaf-line);
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .mushaf-line:last-child {
            border-bottom: none;
            /* Baris terakhir tanpa garis bawah */
        }

        .mushaf-line.centered {
            justify-content: center;
            gap: 10px;
        }

        /* Styling Kata Arab */
        .ayah-word {
            font-family: 'Uthmani', serif;
            font-size: clamp(1.2rem, 4.8vw, 2.3rem);
            line-height: 1.8;
            color: #000000;
            transition: all 0.3s ease;
            position: relative;
            padding: 0 2px;
            white-space: nowrap;
        }

        /* SENSOR HAFALAN (Tinta Transparan & Garis Bawah) */
        .ayah-word.hidden-word {
            color: transparent;
            border-bottom: 2px solid #000000;
            user-select: none;
        }

        .ayah-word.hidden-word:active {
            color: #dddddd;
        }

        /* Audio Bermain */
        .ayah-word.active-audio {
            color: var(--primary);
            background: #d1fae5;
            border-radius: 5px;
        }

        /* Simbol Akhir Ayat */
        .ayah-end {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(30px, 6vw, 42px);
            height: clamp(30px, 6vw, 42px);
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%23000000" stroke-width="5"/><circle cx="50" cy="50" r="36" fill="none" stroke="%23000000" stroke-width="1.5" stroke-dasharray="3,3"/></svg>') no-repeat center;
            background-size: contain;
            font-size: clamp(0.7rem, 2vw, 0.95rem);
            color: #000000;
            margin: 0 4px;
            font-weight: 800;
            flex-shrink: 0;
        }

        /* Banner Surah & Bismillah */
        .surah-title-banner {
            width: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="30" viewBox="0 0 100 30" preserveAspectRatio="none"><rect x="0" y="0" width="100" height="30" fill="%23ffffff" stroke="%23000000" stroke-width="3"/></svg>') no-repeat center;
            background-size: 100% 100%;
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: clamp(1.2rem, 4vw, 1.6rem);
            color: #000000;
            padding: 8px 0;
            margin: 20px 0;
            font-weight: bold;
            border-bottom: none;
            /* hapus garis jika ada */
        }

        .bismillah {
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: clamp(1.4rem, 4.8vw, 2.2rem);
            margin: 10px 0 20px 0;
            width: 100%;
        }

        /* SKELETON LOADING (Bayangan Teks) */
        .skeleton-wrapper {
            display: none;
            width: 100%;
        }

        .skeleton-line {
            height: clamp(1.5rem, 5vw, 2.5rem);
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            margin-bottom: 20px;
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

        /* =========================================
           FLOATING ACTION BAR (Untuk Hafalan)
           ========================================= */
        .floating-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 92%;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 100px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
            /* Disembunyikan di Index, Muncul di Mushaf */
            align-items: center;
            justify-content: space-between;
            padding: 8px 15px;
            z-index: 1000;
        }

        .sensor-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-sensor {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f8fafc;
            border: 1px solid var(--border);
            font-size: 1.1rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-sensor:active {
            background: #e2e8f0;
            transform: scale(0.9);
        }

        .btn-sensor:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .sensor-val {
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--primary);
            min-width: 70px;
            text-align: center;
        }

        .btn-audio-float {
            background: var(--dark);
            color: white;
            border: none;
            height: 45px;
            padding: 0 20px;
            border-radius: 100px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-audio-float.playing {
            background: #ef4444;
            animation: pulse-audio 1.5s infinite;
        }

        @keyframes pulse-audio {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* =========================================
           CUSTOM TOAST ALERT
           ========================================= */
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
            font-size: 0.95rem;
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: top 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        #customToast.show {
            top: 30px;
        }

        @media (max-width: 600px) {
            .floating-bar {
                padding: 8px;
            }

            .btn-audio-float span {
                display: none;
            }

            /* Sembunyikan teks audio di layar sangat kecil */
        }
    </style>
</head>

<body>
    <!-- Kustom Alert -->
    <div id="customToast">
        <i class="fas fa-check-circle" style="color: #10b981;"></i>
        <span id="toastMessage">Tersimpan</span>
    </div>

    <div class="container">

        <!-- ==============================================
             VIEW 1: DAFTAR SURAH (INDEX)
             ============================================== -->
        <div id="view-index">
            <div class="header">
                <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title">Pilih Surah</h1>
            </div>

            <input type="text" id="searchInput" class="search-box" placeholder="Cari nama surah... (Cth: Al-Kahf)" onkeyup="filterSurah()">

            <div class="surah-list" id="surahListContainer">
                <!-- Daftar Surah di-render via JS -->
            </div>
        </div>

        <!-- ==============================================
             VIEW 2: MUSHAF HAFALAN (READING MODE)
             ============================================== -->
        <div id="view-mushaf">
            <div class="header">
                <button class="back-btn" onclick="closeMushaf()"><i class="fas fa-arrow-left"></i></button>
                <h1 class="page-title" id="mushafTitle">Hafalan Mushaf</h1>
            </div>

            <div class="mushaf-header">
                <span id="pageSurahName">Memuat...</span>
                <span id="pageNumberLabel">Halaman -</span>
            </div>

            <!-- Tombol Navigasi Pindah ke Atas -->
            <div class="pagination">
                <button class="btn-page" id="btnPrev" onclick="changePage(-1)"><i class="fas fa-chevron-right"></i> Hal Sebelumnya</button>
                <button class="btn-page" id="btnNext" onclick="changePage(1)">Hal Berikutnya <i class="fas fa-chevron-left"></i></button>
            </div>

            <!-- Skeleton Loading 15 Baris -->
            <div class="skeleton-wrapper" id="loader">
                <?php for ($i = 0; $i < 15; $i++): ?>
                    <div class="skeleton-line" style="width: <?= rand(85, 100) ?>%;"></div>
                <?php endfor; ?>
            </div>

            <div class="quran-page" id="quranPage">
                <!-- Teks Al-Qur'an (15 Baris Presisi dengan Garis Bawah) dirender di sini -->
            </div>
        </div>

    </div>

    <!-- FLOATING BAR BAWAH (Muncul Saat Mode Hafalan Saja) -->
    <div class="floating-bar" id="floatingBar">
        <div class="sensor-group">
            <button class="btn-sensor" onclick="changeSensor(-20)" id="btnMinSensor"><i class="fas fa-minus"></i></button>
            <div class="sensor-val" id="lblSensor">0% Hilang</div>
            <button class="btn-sensor" onclick="changeSensor(20)" id="btnMaxSensor"><i class="fas fa-plus"></i></button>
        </div>
        <button class="btn-audio-float" id="btnAudio" onclick="toggleAudio()">
            <i class="fas fa-play"></i> <span>Murottal</span>
        </button>
    </div>

    <!-- Audio Element Tertutup -->
    <audio id="quranAudio" onended="playNextAyah()"></audio>

    <?php if ($is_logged_in) include '../components/nav.php'; ?>

    <script>
        // FUNGSI CUSTOM TOAST ALERT
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

        // Data Awal Surah (Nama Arab & Halaman Awal)
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

        let audioPlaylists = [];
        let currentAyahIndex = -1;
        let isPlaying = false;
        const audioElement = document.getElementById('quranAudio');

        // AWAL LOAD HALAMAN (Tampilkan Daftar Surah)
        document.addEventListener('DOMContentLoaded', () => {
            renderSurahList(surahs);

            // Cek jika ada save terakhir
            const savedPage = localStorage.getItem('hifzly_last_page');
            if (savedPage && !isNaN(savedPage)) {
                // Opsional: Jika ingin otomatis buka halaman terakhir
                // openMushaf(parseInt(savedPage));
            }
        });

        // ==========================================
        // LOGIKA TAMPILAN (SPA ROUTING)
        // ==========================================
        function renderSurahList(dataList) {
            const container = document.getElementById('surahListContainer');
            container.innerHTML = '';
            if (dataList.length === 0) {
                container.innerHTML = `<p style="text-align:center; color:#64748b; padding:20px;">Surah tidak ditemukan.</p>`;
                return;
            }
            dataList.forEach(s => {
                container.innerHTML += `
                    <div class="surah-card" onclick="openMushaf(${s.page})">
                        <div style="display:flex; align-items:center; gap:15px;">
                            <div class="surah-num">${s.id}</div>
                            <div class="surah-info">
                                <h3>${s.name}</h3>
                                <p>Halaman ${s.page}</p>
                            </div>
                        </div>
                        <div class="surah-arab">${s.arab}</div>
                    </div>
                `;
            });
        }

        function filterSurah() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            const filtered = surahs.filter(s => s.name.toLowerCase().includes(query) || s.id.toString() === query);
            renderSurahList(filtered);
        }

        function openMushaf(page) {
            document.getElementById('view-index').style.display = 'none';
            document.getElementById('view-mushaf').style.display = 'block';
            document.getElementById('floatingBar').style.display = 'flex';

            currentPage = page;
            loadQuranPage(currentPage);
            window.scrollTo(0, 0); // Scroll ke atas
        }

        function closeMushaf() {
            stopAudio();
            document.getElementById('view-mushaf').style.display = 'none';
            document.getElementById('floatingBar').style.display = 'none';
            document.getElementById('view-index').style.display = 'block';
        }

        // ==========================================
        // LOGIKA MUSHAF (API, SENSOR, AUDIO)
        // ==========================================
        function changePage(direction) {
            let newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                stopAudio();
                loadQuranPage(currentPage);
                localStorage.setItem('hifzly_last_page', currentPage);
                showToast(`Halaman ${currentPage} tersimpan`);
            }
        }

        // KONTROL SENSOR (TOMBOL FLOAT)
        function changeSensor(amount) {
            sensorLevel += amount;
            if (sensorLevel < 0) sensorLevel = 0;
            if (sensorLevel > 100) sensorLevel = 100;

            document.getElementById('lblSensor').innerText = sensorLevel === 0 ? "Tampil" : (sensorLevel === 100 ? "Kosong" : `${sensorLevel}% Hilang`);
            document.getElementById('btnMinSensor').disabled = (sensorLevel === 0);
            document.getElementById('btnMaxSensor').disabled = (sensorLevel === 100);

            applySensor();
        }

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
                showToast("Koneksi gagal. Periksa internet Anda.", true);
            } finally {
                document.getElementById('loader').style.display = 'none';
            }
        }

        function renderExactMushafLayout(verses, pageNum) {
            const container = document.getElementById('quranPage');
            const firstVerseKey = verses[0].verse_key;
            const firstSurahId = parseInt(firstVerseKey.split(':')[0]);
            const surahName = surahs.find(s => s.id === firstSurahId)?.name || "";

            document.getElementById('pageSurahName').innerText = `Surah ${surahName}`;
            document.getElementById('pageNumberLabel').innerText = `Halaman ${pageNum}`;

            let linesMap = {};
            let globalWordIdx = 0;

            verses.forEach((verse, vIndex) => {
                if (verse.audio && verse.audio.url) {
                    let audioUrl = verse.audio.url.startsWith('http') ? verse.audio.url : `https://verses.quran.com/${verse.audio.url}`;
                    audioPlaylists.push({
                        url: audioUrl,
                        verseKey: verse.verse_key
                    });
                } else {
                    audioPlaylists.push({
                        url: null,
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
                    if (word.char_type_name === 'end') {
                        if (!linesMap[word.line_number]) linesMap[word.line_number] = [];
                        linesMap[word.line_number].push({
                            type: 'end',
                            text: convertToArabicNumber(ayahNum),
                            verseKey: verse.verse_key
                        });
                    } else if (word.text_uthmani) {
                        if (!linesMap[word.line_number]) linesMap[word.line_number] = [];
                        linesMap[word.line_number].push({
                            type: 'word',
                            text: word.text_uthmani,
                            id: globalWordIdx,
                            verseKey: verse.verse_key
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
                const lineItems = linesMap[lineNum];
                if (lineItems[0].type === 'surah_header') {
                    htmlContent += `<div class="surah-title-banner">${lineItems[0].text}</div>`;
                    return;
                }
                if (lineItems[0].type === 'bismillah') {
                    htmlContent += `<div class="bismillah">${lineItems[0].text}</div>`;
                    return;
                }

                const isCentered = lineItems.length < 6 ? 'centered' : '';
                // Render baris dan garis pembatas (border-bottom css)
                htmlContent += `<div class="mushaf-line ${isCentered}">`;
                lineItems.forEach(item => {
                    if (item.type === 'end') {
                        htmlContent += `<span class="ayah-end verse-${item.verseKey.replace(':','-')}">${item.text}</span>`;
                    } else {
                        htmlContent += `<span class="ayah-word verse-${item.verseKey.replace(':','-')}" id="w-${item.id}">${item.text}</span>`;
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
                const wordsToHideCount = Math.floor((sensorLevel / 100) * wordElements.length);
                for (let i = 0; i < wordsToHideCount; i++) {
                    const targetIndex = shuffledIndices[i];
                    if (wordElements[targetIndex]) {
                        wordElements[targetIndex].classList.add('hidden-word');
                    }
                }
            }
        }

        function toggleAudio() {
            const btn = document.getElementById('btnAudio');
            if (isPlaying) {
                stopAudio();
            } else {
                if (audioPlaylists.length > 0) {
                    isPlaying = true;
                    btn.classList.add('playing');
                    btn.innerHTML = `<i class="fas fa-pause"></i> <span>Hentikan</span>`;
                    currentAyahIndex = -1;
                    playNextAyah();
                } else {
                    showToast("Audio tidak tersedia", true);
                }
            }
        }

        function playNextAyah() {
            if (currentAyahIndex >= 0 && audioPlaylists[currentAyahIndex]) {
                const prevKey = audioPlaylists[currentAyahIndex].verseKey.replace(':', '-');
                document.querySelectorAll(`.verse-${prevKey}`).forEach(el => el.classList.remove('active-audio'));
            }

            currentAyahIndex++;

            if (currentAyahIndex < audioPlaylists.length) {
                const currentData = audioPlaylists[currentAyahIndex];
                const currentKey = currentData.verseKey.replace(':', '-');
                document.querySelectorAll(`.verse-${currentKey}`).forEach(el => el.classList.add('active-audio'));

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

        function stopAudio() {
            isPlaying = false;
            audioElement.pause();
            audioElement.currentTime = 0;
            const btn = document.getElementById('btnAudio');
            btn.classList.remove('playing');
            btn.innerHTML = `<i class="fas fa-play"></i> <span>Murottal</span>`;
            document.querySelectorAll('.active-audio').forEach(el => el.classList.remove('active-audio'));
            currentAyahIndex = -1;
        }

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