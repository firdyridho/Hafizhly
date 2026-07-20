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
            var(--primary): #059669;
            /* Branding Hijau Hifzhly */
            --dark: #0f172a;
            --bg: #f4f7f6;
            --border: #e2e8f0;
            /* Tema Mushaf Hitam Putih */
            --mushaf-bg: #ffffff;
            --mushaf-line: #000000;
            --mushaf-ornament: #333333;
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
            padding-bottom: 140px;
            /* Ruang untuk floating bar */
            overflow-x: hidden;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 15px;
            width: 100%;
        }

        /* HEADER APP */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
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
        }

        .page-title {
            font-size: 1.3rem;
            font-weight: 800;
        }

        /* DROPDOWN NAVIGASI */
        .nav-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .nav-grid select {
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: white;
            font-weight: 700;
            color: var(--dark);
            outline: none;
            width: 100%;
            font-size: 0.9rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        /* TAMPILAN MUSHAF (PRESISI & HITAM PUTIH) */
        .mushaf-wrapper {
            background: var(--mushaf-bg);
            padding: 20px;
            border-radius: 16px;
            border: 2px solid var(--mushaf-line);
            min-height: 60vh;
            position: relative;
        }

        .mushaf-header {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--mushaf-ornament);
            border-bottom: 2px solid var(--mushaf-line);
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .quran-page {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        /* Baris demi baris Mushaf */
        .mushaf-line {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            flex-wrap: nowrap;
            margin-bottom: 5px;
        }

        .mushaf-line.centered {
            justify-content: center;
            gap: 10px;
        }

        /* Styling Kata Arab */
        .ayah-word {
            font-family: 'Uthmani', serif;
            /* Perhitungan responsif agar tidak tumpah di HP */
            font-size: clamp(1.1rem, 4.5vw, 2.2rem);
            line-height: 1.8;
            color: #000000;
            transition: all 0.3s ease;
            position: relative;
            padding: 0 1px;
            white-space: nowrap;
        }

        /* SENSOR HAFALAN (Garis Bawah Saja) */
        .ayah-word.hidden-word {
            color: transparent;
            border-bottom: 2px solid #000000;
            user-select: none;
        }

        .ayah-word.hidden-word:active {
            color: #dddddd;
            /* Sedikit mengintip saat ditekan */
        }

        /* Audio Bermain */
        .ayah-word.active-audio {
            color: #059669;
            background: #d1fae5;
            border-radius: 5px;
        }

        /* Simbol Akhir Ayat (Hitam Putih) */
        .ayah-end {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(28px, 6vw, 40px);
            height: clamp(28px, 6vw, 40px);
            /* Ornamen bundar SVG hitam */
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%23000000" stroke-width="5"/><circle cx="50" cy="50" r="36" fill="none" stroke="%23000000" stroke-width="1.5" stroke-dasharray="3,3"/></svg>') no-repeat center;
            background-size: contain;
            font-size: clamp(0.7rem, 2vw, 0.9rem);
            color: #000000;
            margin: 0 4px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            flex-shrink: 0;
        }

        /* Judul Surah (Banner Hitam Putih) */
        .surah-title-banner {
            width: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="30" viewBox="0 0 100 30" preserveAspectRatio="none"><rect x="0" y="0" width="100" height="30" fill="%23ffffff" stroke="%23000000" stroke-width="3"/></svg>') no-repeat center;
            background-size: 100% 100%;
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: clamp(1.2rem, 4vw, 1.6rem);
            color: #000000;
            padding: 8px 0;
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

        /* SKELETON LOADING (Pengganti Spinner Biasa) */
        .skeleton-wrapper {
            display: none;
            width: 100%;
        }

        .skeleton-line {
            height: clamp(1.5rem, 5vw, 2.5rem);
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            margin-bottom: 10px;
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

        /* PAGINASI */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            gap: 10px;
        }

        .btn-page {
            background: var(--dark);
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            flex: 1;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-page:hover {
            background: #000000;
        }

        .btn-page:disabled {
            opacity: 0.4;
            pointer-events: none;
        }

        /* FLOATING ACTION BAR (Untuk Sensor & Audio) */
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
            display: flex;
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

        .sensor-val {
            font-weight: 800;
            font-size: 0.9rem;
            color: #059669;
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
            .nav-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .mushaf-wrapper {
                padding: 15px 10px;
            }

            .floating-bar {
                padding: 8px;
            }

            .btn-audio-float span {
                display: none;
            }

            /* Hanya icon di layar sangat kecil */
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
        <div class="header">
            <div class="header-left">
                <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title">Hafalan Mushaf</h1>
            </div>
        </div>

        <div class="nav-grid">
            <select id="surahSelect" onchange="jumpToSurah()">
                <option>Memuat Surah...</option>
            </select>
            <select id="pageSelect" onchange="jumpToPage()">
                <option>Memuat Halaman...</option>
            </select>
        </div>

        <div class="mushaf-wrapper">
            <div class="mushaf-header">
                <span id="pageSurahName">Memuat...</span>
                <span id="pageNumberLabel">Halaman -</span>
            </div>

            <!-- Skeleton Loading 15 Baris -->
            <div class="skeleton-wrapper" id="loader">
                <?php for ($i = 0; $i < 15; $i++): ?>
                    <div class="skeleton-line" style="width: <?= rand(80, 100) ?>%;"></div>
                <?php endfor; ?>
            </div>

            <div class="quran-page" id="quranPage">
                <!-- Teks Al-Qur'an (15 Baris Presisi) dirender di sini -->
            </div>

            <div class="pagination">
                <button class="btn-page" id="btnPrev" onclick="changePage(-1)"><i class="fas fa-chevron-right"></i> Sebelumnya</button>
                <button class="btn-page" id="btnNext" onclick="changePage(1)">Berikutnya <i class="fas fa-chevron-left"></i></button>
            </div>
        </div>
    </div>

    <!-- FLOATING BAR BAWAH -->
    <div class="floating-bar">
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
        // FUNGSI CUSTOM TOAST (Ganti SweetAlert)
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
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }

        // Data Awal Surah (Nama & Halaman Awal)
        const surahs = [{
            id: 1,
            name: "Al-Fatihah",
            page: 1
        }, {
            id: 2,
            name: "Al-Baqarah",
            page: 2
        }, {
            id: 3,
            name: "Ali 'Imran",
            page: 50
        }, {
            id: 4,
            name: "An-Nisa'",
            page: 77
        }, {
            id: 5,
            name: "Al-Ma'idah",
            page: 106
        }, {
            id: 6,
            name: "Al-An'am",
            page: 128
        }, {
            id: 7,
            name: "Al-A'raf",
            page: 151
        }, {
            id: 8,
            name: "Al-Anfal",
            page: 177
        }, {
            id: 9,
            name: "At-Taubah",
            page: 187
        }, {
            id: 10,
            name: "Yunus",
            page: 208
        }, {
            id: 11,
            name: "Hud",
            page: 221
        }, {
            id: 12,
            name: "Yusuf",
            page: 235
        }, {
            id: 13,
            name: "Ar-Ra'd",
            page: 249
        }, {
            id: 14,
            name: "Ibrahim",
            page: 255
        }, {
            id: 15,
            name: "Al-Hijr",
            page: 262
        }, {
            id: 16,
            name: "An-Nahl",
            page: 267
        }, {
            id: 17,
            name: "Al-Isra'",
            page: 282
        }, {
            id: 18,
            name: "Al-Kahf",
            page: 293
        }, {
            id: 19,
            name: "Maryam",
            page: 305
        }, {
            id: 20,
            name: "Taha",
            page: 312
        }, {
            id: 21,
            name: "Al-Anbiya'",
            page: 322
        }, {
            id: 22,
            name: "Al-Hajj",
            page: 332
        }, {
            id: 23,
            name: "Al-Mu'minun",
            page: 342
        }, {
            id: 24,
            name: "An-Nur",
            page: 350
        }, {
            id: 25,
            name: "Al-Furqan",
            page: 359
        }, {
            id: 26,
            name: "Asy-Syu'ara'",
            page: 367
        }, {
            id: 27,
            name: "An-Naml",
            page: 377
        }, {
            id: 28,
            name: "Al-Qasas",
            page: 385
        }, {
            id: 29,
            name: "Al-'Ankabut",
            page: 396
        }, {
            id: 30,
            name: "Ar-Rum",
            page: 404
        }, {
            id: 31,
            name: "Luqman",
            page: 411
        }, {
            id: 32,
            name: "As-Sajdah",
            page: 415
        }, {
            id: 33,
            name: "Al-Ahzab",
            page: 418
        }, {
            id: 34,
            name: "Saba'",
            page: 428
        }, {
            id: 35,
            name: "Fatir",
            page: 434
        }, {
            id: 36,
            name: "Yasin",
            page: 440
        }, {
            id: 37,
            name: "As-Saffat",
            page: 446
        }, {
            id: 38,
            name: "Sad",
            page: 453
        }, {
            id: 39,
            name: "Az-Zumar",
            page: 458
        }, {
            id: 40,
            name: "Ghafir",
            page: 467
        }, {
            id: 41,
            name: "Fussilat",
            page: 477
        }, {
            id: 42,
            name: "Asy-Syura",
            page: 483
        }, {
            id: 43,
            name: "Az-Zukhruf",
            page: 489
        }, {
            id: 44,
            name: "Ad-Dukhan",
            page: 496
        }, {
            id: 45,
            name: "Al-Jasiyah",
            page: 499
        }, {
            id: 46,
            name: "Al-Ahqaf",
            page: 502
        }, {
            id: 47,
            name: "Muhammad",
            page: 507
        }, {
            id: 48,
            name: "Al-Fath",
            page: 511
        }, {
            id: 49,
            name: "Al-Hujurat",
            page: 515
        }, {
            id: 50,
            name: "Qaf",
            page: 518
        }, {
            id: 51,
            name: "Az-Zariyat",
            page: 520
        }, {
            id: 52,
            name: "At-Tur",
            page: 523
        }, {
            id: 53,
            name: "An-Najm",
            page: 526
        }, {
            id: 54,
            name: "Al-Qamar",
            page: 528
        }, {
            id: 55,
            name: "Ar-Rahman",
            page: 531
        }, {
            id: 56,
            name: "Al-Waqi'ah",
            page: 534
        }, {
            id: 57,
            name: "Al-Hadid",
            page: 537
        }, {
            id: 58,
            name: "Al-Mujadilah",
            page: 542
        }, {
            id: 59,
            name: "Al-Hasyr",
            page: 545
        }, {
            id: 60,
            name: "Al-Mumtahanah",
            page: 549
        }, {
            id: 61,
            name: "As-Saff",
            page: 551
        }, {
            id: 62,
            name: "Al-Jumu'ah",
            page: 553
        }, {
            id: 63,
            name: "Al-Munafiqun",
            page: 554
        }, {
            id: 64,
            name: "At-Tagabun",
            page: 556
        }, {
            id: 65,
            name: "At-Talaq",
            page: 558
        }, {
            id: 66,
            name: "At-Tahrim",
            page: 560
        }, {
            id: 67,
            name: "Al-Mulk",
            page: 562
        }, {
            id: 68,
            name: "Al-Qalam",
            page: 564
        }, {
            id: 69,
            name: "Al-Haqqah",
            page: 566
        }, {
            id: 70,
            name: "Al-Ma'arij",
            page: 568
        }, {
            id: 71,
            name: "Nuh",
            page: 570
        }, {
            id: 72,
            name: "Al-Jinn",
            page: 572
        }, {
            id: 73,
            name: "Al-Muzzammil",
            page: 574
        }, {
            id: 74,
            name: "Al-Muddassir",
            page: 575
        }, {
            id: 75,
            name: "Al-Qiyamah",
            page: 577
        }, {
            id: 76,
            name: "Al-Insan",
            page: 578
        }, {
            id: 77,
            name: "Al-Mursalat",
            page: 580
        }, {
            id: 78,
            name: "An-Naba'",
            page: 582
        }, {
            id: 79,
            name: "An-Nazi'at",
            page: 583
        }, {
            id: 80,
            name: "'Abasa",
            page: 585
        }, {
            id: 81,
            name: "At-Takwir",
            page: 586
        }, {
            id: 82,
            name: "Al-Infitar",
            page: 587
        }, {
            id: 83,
            name: "Al-Mutaffifin",
            page: 587
        }, {
            id: 84,
            name: "Al-Insyiqaq",
            page: 589
        }, {
            id: 85,
            name: "Al-Buruj",
            page: 590
        }, {
            id: 86,
            name: "At-Tariq",
            page: 591
        }, {
            id: 87,
            name: "Al-A'la",
            page: 591
        }, {
            id: 88,
            name: "Al-Gasyiyah",
            page: 592
        }, {
            id: 89,
            name: "Al-Fajr",
            page: 593
        }, {
            id: 90,
            name: "Al-Balad",
            page: 594
        }, {
            id: 91,
            name: "Asy-Syams",
            page: 595
        }, {
            id: 92,
            name: "Al-Lail",
            page: 595
        }, {
            id: 93,
            name: "Ad-Duha",
            page: 596
        }, {
            id: 94,
            name: "Asy-Syarh",
            page: 596
        }, {
            id: 95,
            name: "At-Tin",
            page: 597
        }, {
            id: 96,
            name: "Al-'Alaq",
            page: 597
        }, {
            id: 97,
            name: "Al-Qadr",
            page: 598
        }, {
            id: 98,
            name: "Al-Bayyinah",
            page: 598
        }, {
            id: 99,
            name: "Az-Zalzalah",
            page: 599
        }, {
            id: 100,
            name: "Al-'Adiyat",
            page: 599
        }, {
            id: 101,
            name: "Al-Qari'ah",
            page: 600
        }, {
            id: 102,
            name: "At-Takasur",
            page: 600
        }, {
            id: 103,
            name: "Al-'Asr",
            page: 601
        }, {
            id: 104,
            name: "Al-Humazah",
            page: 601
        }, {
            id: 105,
            name: "Al-Fil",
            page: 601
        }, {
            id: 106,
            name: "Quraisy",
            page: 602
        }, {
            id: 107,
            name: "Al-Ma'un",
            page: 602
        }, {
            id: 108,
            name: "Al-Kausar",
            page: 602
        }, {
            id: 109,
            name: "Al-Kafirun",
            page: 603
        }, {
            id: 110,
            name: "An-Nasr",
            page: 603
        }, {
            id: 111,
            name: "Al-Lahab",
            page: 603
        }, {
            id: 112,
            name: "Al-Ikhlas",
            page: 604
        }, {
            id: 113,
            name: "Al-Falaq",
            page: 604
        }, {
            id: 114,
            name: "An-Nas",
            page: 604
        }];

        let currentPage = 1;
        const totalPages = 604;
        let sensorLevel = 0; // 0 sampai 100
        let wordElements = [];
        let shuffledIndices = [];

        // Audio Variabel
        let audioPlaylists = [];
        let currentAyahIndex = -1;
        let isPlaying = false;
        const audioElement = document.getElementById('quranAudio');

        document.addEventListener('DOMContentLoaded', () => {
            const savedPage = localStorage.getItem('hifzly_last_page');
            if (savedPage && !isNaN(savedPage)) currentPage = parseInt(savedPage);

            const sSelect = document.getElementById('surahSelect');
            surahs.forEach(s => {
                sSelect.innerHTML += `<option value="${s.page}">${s.id}. ${s.name} (Hal. ${s.page})</option>`;
            });

            const pSelect = document.getElementById('pageSelect');
            for (let i = 1; i <= totalPages; i++) {
                pSelect.innerHTML += `<option value="${i}">Halaman ${i}</option>`;
            }
            loadQuranPage(currentPage);
        });

        function saveProgress(page) {
            localStorage.setItem('hifzly_last_page', page);
            showToast(`Halaman ${page} tersimpan!`);
        }

        function jumpToSurah() {
            const val = document.getElementById('surahSelect').value;
            if (val) changePage(parseInt(val) - currentPage);
        }

        function jumpToPage() {
            const val = document.getElementById('pageSelect').value;
            if (val) changePage(parseInt(val) - currentPage);
        }

        function changePage(direction) {
            let newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                stopAudio();
                loadQuranPage(currentPage);
                saveProgress(currentPage);
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
            document.getElementById('pageSelect').value = page;

            let closestSurah = surahs.slice().reverse().find(s => s.page <= page);
            if (closestSurah) document.getElementById('surahSelect').value = closestSurah.page;

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
                    const sName = surahs.find(s => s.id === surahId)?.name || `Surah ${surahId}`;
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
                    showToast("Audio tidak tersedia untuk halaman ini", true);
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