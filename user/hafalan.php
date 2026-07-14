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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Hafalan Presisi — Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* FONT KHUSUS MUSHAF MADINAH */
        @font-face {
            font-family: 'Uthmani';
            src: url('https://cdn.jsdelivr.net/gh/fawazahmed0/quran-api@1/fonts/KFGQPC_Uthmanic_Script_HAFS_Regular.ttf') format('truetype');
        }

        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --primary-light: #d1fae5;
            --dark: #0f172a;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --paper: #fffcf2;
            /* Warna kuning gading kertas Al-Qur'an */
            --paper-border: #e8e2c8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 120px;
            overflow-x: hidden;
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
            padding: clamp(14px, 4vw, 20px);
            width: 100%;
        }

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

        .back-btn:hover {
            background: var(--primary);
            color: white;
        }

        .page-title {
            font-size: clamp(1.2rem, 4vw, 1.5rem);
            font-weight: 800;
        }

        .save-badge {
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        /* ---------- PANEL KONTROL ---------- */
        .controls-card {
            background: white;
            padding: clamp(16px, 4vw, 24px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            margin-bottom: 25px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .nav-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .control-group label {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--dark);
        }

        select {
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--bg);
            font-weight: 600;
            color: var(--dark);
            outline: none;
            cursor: pointer;
            width: 100%;
            font-size: 0.9rem;
        }

        /* Kontrol Sensor (Tombol) */
        .sensor-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 6px;
        }

        .btn-sensor {
            background: white;
            border: 1px solid var(--border);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--dark);
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-sensor:hover:not(:disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-sensor:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .sensor-val {
            font-weight: 800;
            font-size: 1rem;
            color: var(--primary-dark);
            min-width: 90px;
            text-align: center;
        }

        /* Audio & Action Bar */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid var(--border);
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-audio {
            background: var(--dark);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            font-size: 0.9rem;
            flex-grow: 1;
            justify-content: center;
        }

        .btn-audio:hover {
            background: var(--primary);
        }

        .btn-audio.playing {
            background: #ef4444;
        }

        /* ---------- TAMPILAN MUSHAF (PRESISI) ---------- */
        .mushaf-wrapper {
            background: var(--paper);
            padding: clamp(20px, 4vw, 40px) clamp(15px, 3vw, 30px);
            border-radius: 20px;
            box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.02), 0 20px 40px rgba(0, 0, 0, 0.06);
            border: 1px solid var(--paper-border);
            min-height: 60vh;
            position: relative;
        }

        .mushaf-header {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            font-size: clamp(0.85rem, 3vw, 1rem);
            color: #857a55;
            border-bottom: 2px solid var(--paper-border);
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .quran-page {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        /* Baris demi baris (Flexbox Justify) */
        .mushaf-line {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 10px;
            flex-wrap: nowrap;
        }

        .mushaf-line.centered {
            justify-content: center;
            gap: 15px;
        }

        /* Untuk baris terakhir yang tidak penuh */

        /* Styling Kata */
        .ayah-word {
            font-family: 'Uthmani', serif;
            font-size: clamp(1.4rem, 5vw, 2.4rem);
            line-height: 1.6;
            color: #1e1e1e;
            transition: color 0.3s, border-color 0.3s, background 0.3s;
            position: relative;
            padding: 0 2px;
        }

        /* State Hafalan (Disensor) */
        .ayah-word.hidden-word {
            color: transparent;
            border-bottom: 2px dashed #b4a269;
            user-select: none;
        }

        .ayah-word.hidden-word:hover {
            color: rgba(0, 0, 0, 0.15);
            border-bottom-color: var(--primary);
        }

        /* State Audio Bermain */
        .ayah-word.active-audio {
            color: var(--primary);
            background: var(--primary-light);
            border-radius: 8px;
        }

        /* Simbol Akhir Ayat */
        .ayah-end {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(35px, 8vw, 45px);
            height: clamp(35px, 8vw, 45px);
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%23b4a269" stroke-width="4"/><circle cx="50" cy="50" r="38" fill="none" stroke="%23b4a269" stroke-width="1" stroke-dasharray="2,2"/></svg>') no-repeat center;
            background-size: contain;
            font-size: clamp(0.8rem, 2.5vw, 1rem);
            color: #b4a269;
            margin: 0 5px;
            transform: translateY(-3px);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Judul Surah di Tengah Teks */
        .surah-title-banner {
            width: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="30" viewBox="0 0 100 30" preserveAspectRatio="none"><rect x="0" y="0" width="100" height="30" fill="%23fefaf0" stroke="%23b4a269" stroke-width="2"/></svg>') no-repeat center;
            background-size: 100% 100%;
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: clamp(1.2rem, 4vw, 1.8rem);
            color: #857a55;
            padding: 10px 0;
            margin: 15px 0;
        }

        /* Bismillah */
        .bismillah {
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: clamp(1.4rem, 4.5vw, 2.2rem);
            margin: 5px 0 15px 0;
            width: 100%;
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 60px auto;
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ---------- PAGINASI BAWAH ---------- */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            gap: 10px;
            width: 100%;
        }

        .btn-page {
            background: white;
            border: 1px solid var(--border);
            padding: 12px 18px;
            border-radius: 14px;
            font-weight: 700;
            color: var(--dark);
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            justify-content: center;
            font-size: clamp(0.85rem, 3vw, 0.95rem);
        }

        .btn-page:hover:not(:disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-page:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 600px) {
            .nav-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .mushaf-line {
                gap: 2px;
            }

            /* Kurangi jarak antar kata di layar kecil agar pas 15 baris */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title">Hafalan Mushaf</h1>
            </div>
            <div class="save-badge" id="saveBadge"><i class="fas fa-check-circle"></i> Tersimpan</div>
        </div>

        <div class="controls-card">
            <div class="nav-grid">
                <div class="control-group">
                    <label>Pilih Surah</label>
                    <select id="surahSelect" onchange="jumpToSurah()">
                        <option>Memuat...</option>
                    </select>
                </div>
                <div class="control-group">
                    <label>Halaman Mushaf (1-604)</label>
                    <select id="pageSelect" onchange="jumpToPage()">
                        <option>Memuat...</option>
                    </select>
                </div>
            </div>

            <div class="action-bar">
                <div class="control-group" style="flex: 1; min-width: 200px;">
                    <label>Tingkat Hafalan (Sensor Kata)</label>
                    <div class="sensor-controls">
                        <button class="btn-sensor" onclick="changeSensor(-20)" id="btnMinSensor"><i class="fas fa-minus"></i></button>
                        <div class="sensor-val" id="lblSensor">0% Hilang</div>
                        <button class="btn-sensor" onclick="changeSensor(20)" id="btnMaxSensor"><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <button class="btn-audio" id="btnAudio" onclick="toggleAudio()">
                    <i class="fas fa-play-circle"></i> Putar Murottal Halaman
                </button>
            </div>
        </div>

        <div class="mushaf-wrapper">
            <div class="mushaf-header">
                <span id="pageSurahName">Memuat...</span>
                <span id="pageNumberLabel">Halaman -</span>
            </div>

            <div class="loader" id="loader"></div>
            <div class="quran-page" id="quranPage">
                <!-- Teks Al-Qur'an (15 Baris) akan dirender secara presisi di sini -->
            </div>

            <div class="pagination">
                <button class="btn-page" id="btnPrev" onclick="changePage(-1)"><i class="fas fa-chevron-right"></i> Hal Sebelumnya</button>
                <button class="btn-page" id="btnNext" onclick="changePage(1)">Hal Berikutnya <i class="fas fa-chevron-left"></i></button>
            </div>
        </div>
    </div>

    <!-- Audio Element Tertutup -->
    <audio id="quranAudio" onended="playNextAyah()"></audio>

    <?php if ($is_logged_in) include '../components/nav.php'; ?>

    <script>
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
        let audioPlaylists = []; // URL audio per ayat di halaman ini
        let currentAyahIndex = -1;
        let isPlaying = false;
        const audioElement = document.getElementById('quranAudio');

        document.addEventListener('DOMContentLoaded', () => {
            // Ambil Data Save dari LocalStorage (Fitur Save)
            const savedPage = localStorage.getItem('hifzly_last_page');
            if (savedPage && !isNaN(savedPage)) currentPage = parseInt(savedPage);

            // Populate Dropdown Surah
            const sSelect = document.getElementById('surahSelect');
            surahs.forEach(s => {
                sSelect.innerHTML += `<option value="${s.page}">${s.id}. ${s.name} (Hal. ${s.page})</option>`;
            });

            // Populate Dropdown Halaman
            const pSelect = document.getElementById('pageSelect');
            for (let i = 1; i <= totalPages; i++) {
                pSelect.innerHTML += `<option value="${i}">Halaman ${i}</option>`;
            }

            loadQuranPage(currentPage);
        });

        // Simpan Halaman ke LocalStorage
        function saveProgress(page) {
            localStorage.setItem('hifzly_last_page', page);
            const badge = document.getElementById('saveBadge');
            badge.style.opacity = '1';
            setTimeout(() => {
                badge.style.opacity = '0';
            }, 2000);
        }

        // Navigasi Dropdown
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
                stopAudio(); // Matikan audio jika pindah halaman
                loadQuranPage(currentPage);
                saveProgress(currentPage);
            }
        }

        // KONTROL SENSOR (TOMBOL - / +)
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

            // Cari surah dominan di halaman ini untuk dropdown surah
            let closestSurah = surahs.slice().reverse().find(s => s.page <= page);
            if (closestSurah) document.getElementById('surahSelect').value = closestSurah.page;

            document.getElementById('btnPrev').disabled = (page === 1);
            document.getElementById('btnNext').disabled = (page === totalPages);

            // Reset state
            wordElements = [];
            audioPlaylists = [];
            currentAyahIndex = -1;

            try {
                // Menggunakan API Quran.com V4 untuk Presisi Garis (line_number) dan Audio
                const response = await fetch(`https://api.quran.com/api/v4/verses/by_page/${page}?language=id&words=true&word_fields=text_uthmani,line_number&audio=7`);
                const data = await response.json();

                renderExactMushafLayout(data.verses, page);
                applySensor(); // Terapkan level sensor saat ini ke halaman baru
            } catch (error) {
                document.getElementById('quranPage').innerHTML = '<div style="text-align:center; color:red;">Gagal memuat. Periksa internet Anda.</div>';
            } finally {
                document.getElementById('loader').style.display = 'none';
            }
        }

        function renderExactMushafLayout(verses, pageNum) {
            const container = document.getElementById('quranPage');

            // Ambil Info Juz/Surah dari ayat pertama di halaman
            const firstVerseKey = verses[0].verse_key; // Format "1:1" (Surah:Ayat)
            const firstSurahId = parseInt(firstVerseKey.split(':')[0]);
            const surahName = surahs.find(s => s.id === firstSurahId)?.name || "";

            document.getElementById('pageSurahName').innerText = `Surah ${surahName}`;
            document.getElementById('pageNumberLabel').innerText = `Halaman ${pageNum}`;

            // Mengelompokkan kata berdasarkan line_number (BARIS DEMI BARIS)
            let linesMap = {};
            let globalWordIdx = 0;

            verses.forEach((verse, vIndex) => {
                // Siapkan Audio untuk Ayat Ini
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

                // Cek apakah ayat ini ayat pertama surah (selain Al-Fatihah/At-Taubah) untuk render Header Surah / Bismillah
                const ayahNum = parseInt(verse.verse_key.split(':')[1]);
                const surahId = parseInt(verse.verse_key.split(':')[0]);

                if (ayahNum === 1) {
                    const sName = surahs.find(s => s.id === surahId)?.name || `Surah ${surahId}`;
                    // Paksa masuk ke baris khusus agar rapi
                    if (!linesMap['header_' + surahId]) linesMap['header_' + surahId] = [];
                    linesMap['header_' + surahId].push({
                        type: 'surah_header',
                        text: `سورة ${sName}`
                    });

                    if (surahId !== 1 && surahId !== 9) {
                        if (!linesMap['bismillah_' + surahId]) linesMap['bismillah_' + surahId] = [];
                        linesMap['bismillah_' + surahId].push({
                            type: 'bismillah',
                            text: 'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ'
                        });
                    }
                }

                // Render Kata Per Kata
                verse.words.forEach(word => {
                    if (word.char_type_name === 'end') {
                        // Simbol Akhir Ayat
                        if (!linesMap[word.line_number]) linesMap[word.line_number] = [];
                        linesMap[word.line_number].push({
                            type: 'end',
                            text: convertToArabicNumber(ayahNum),
                            verseKey: verse.verse_key
                        });
                    } else if (word.text_uthmani) {
                        // Teks Arab Biasa
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

            // RENDER HTML BERDASARKAN BARIS (1 sampai 15)
            let htmlContent = '';

            // Urutkan key baris (termasuk header jika ada)
            const sortedLines = Object.keys(linesMap).sort((a, b) => {
                if (a.includes('header') || a.includes('bismillah')) return -1; // Taruh atas
                if (b.includes('header') || b.includes('bismillah')) return 1;
                return parseInt(a) - parseInt(b);
            });

            sortedLines.forEach(lineNum => {
                const lineItems = linesMap[lineNum];

                // Cek Tipe Khusus
                if (lineItems[0].type === 'surah_header') {
                    htmlContent += `<div class="surah-title-banner">${lineItems[0].text}</div>`;
                    return;
                }
                if (lineItems[0].type === 'bismillah') {
                    htmlContent += `<div class="bismillah">${lineItems[0].text}</div>`;
                    return;
                }

                // Baris Normal (Flexbox)
                // Jika kata di baris ini sedikit (kurang dari 5 item), kita pusatkan (centered) agar tidak melar jelek
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

            // Koleksi elemen span kata untuk logika sensor
            wordElements = [];
            for (let i = 0; i < globalWordIdx; i++) {
                const el = document.getElementById(`w-${i}`);
                if (el) wordElements.push(el);
            }

            // Acak index untuk fitur sensor
            shuffledIndices = Array.from({
                length: wordElements.length
            }, (_, i) => i);
            shuffleArray(shuffledIndices);
        }

        // FITUR SENSOR KATA
        function applySensor() {
            // Tampilkan semua dulu
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

        // FITUR AUDIO MUROTTAL
        function toggleAudio() {
            const btn = document.getElementById('btnAudio');
            if (isPlaying) {
                stopAudio();
            } else {
                if (audioPlaylists.length > 0) {
                    isPlaying = true;
                    btn.classList.add('playing');
                    btn.innerHTML = `<i class="fas fa-stop-circle"></i> Hentikan Audio`;
                    currentAyahIndex = -1;
                    playNextAyah();
                }
            }
        }

        function playNextAyah() {
            // Hapus highlight dari ayat sebelumnya
            if (currentAyahIndex >= 0 && audioPlaylists[currentAyahIndex]) {
                const prevKey = audioPlaylists[currentAyahIndex].verseKey.replace(':', '-');
                document.querySelectorAll(`.verse-${prevKey}`).forEach(el => el.classList.remove('active-audio'));
            }

            currentAyahIndex++;

            if (currentAyahIndex < audioPlaylists.length) {
                const currentData = audioPlaylists[currentAyahIndex];

                // Highlight ayat saat ini
                const currentKey = currentData.verseKey.replace(':', '-');
                document.querySelectorAll(`.verse-${currentKey}`).forEach(el => el.classList.add('active-audio'));

                if (currentData.url) {
                    audioElement.src = currentData.url;
                    audioElement.play().catch(e => {
                        console.error("Audio play failed", e);
                        playNextAyah(); // Jika gagal, langsung skip ke ayat berikutnya
                    });
                } else {
                    playNextAyah(); // Skip jika URL audio null
                }
            } else {
                stopAudio(); // Selesai 1 halaman
            }
        }

        function stopAudio() {
            isPlaying = false;
            audioElement.pause();
            audioElement.currentTime = 0;
            const btn = document.getElementById('btnAudio');
            btn.classList.remove('playing');
            btn.innerHTML = `<i class="fas fa-play-circle"></i> Putar Murottal Halaman`;

            // Hapus semua highlight
            document.querySelectorAll('.active-audio').forEach(el => el.classList.remove('active-audio'));
            currentAyahIndex = -1;
        }

        // UTILS
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