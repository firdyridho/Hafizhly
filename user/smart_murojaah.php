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
    <title>Murojaah Realtime — Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ========== GAYA (sama seperti sebelumnya) ========== */
        @font-face {
            font-family: 'Uthmani';
            src: url('https://cdn.jsdelivr.net/gh/fawazahmed0/quran-api@1/fonts/KFGQPC_Uthmanic_Script_HAFS_Regular.ttf') format('truetype');
        }

        :root {
            --primary: #059669;
            --primary-light: #d1fae5;
            --bg-color: #f8fafc;
            --text-dark: #0f172a;
            --line-color: #e2e8f0;
            --paper: #fffcf2;
            --paper-border: #e8e2c8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: var(--bg-color);
            color: var(--text-dark);
            overflow-x: hidden;
            touch-action: pan-y;
        }

        #dashboardView {
            padding: clamp(20px, 5vw, 30px);
            max-width: 600px;
            margin: 0 auto;
            padding-bottom: 100px;
        }

        .dash-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .dash-title {
            font-size: 1.5rem;
            font-weight: 800;
        }

        .bookmark-card {
            background: linear-gradient(135deg, var(--primary), #10b981);
            color: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.2);
            margin-bottom: 25px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .bookmark-card::after {
            content: '\f02e';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: -10px;
            bottom: -20px;
            font-size: 5rem;
            opacity: 0.1;
        }

        .bookmark-label {
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 5px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bookmark-surah {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .bookmark-meta {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-box input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border-radius: 16px;
            border: 1px solid var(--line-color);
            background: white;
            font-size: 1rem;
            outline: none;
            transition: 0.2s;
        }

        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-light);
        }

        .surah-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .surah-item {
            background: white;
            border: 1px solid var(--line-color);
            padding: 15px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .surah-item:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .si-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .si-number {
            width: 40px;
            height: 40px;
            background: var(--bg-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .si-name {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 3px;
        }

        .si-meta {
            font-size: 0.8rem;
            color: #64748b;
        }

        .si-arabic {
            font-family: 'Uthmani', serif;
            font-size: 1.3rem;
            color: var(--primary);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: none;
            justify-content: center;
            align-items: flex-end;
            z-index: 1000;
        }

        .range-modal {
            background: white;
            width: 100%;
            max-width: 600px;
            border-radius: 24px 24px 0 0;
            padding: 25px;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        .range-modal h3 {
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .range-modal select {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid var(--line-color);
            margin-bottom: 15px;
            font-size: 1rem;
            outline: none;
            background: #f8fafc;
            font-weight: 600;
        }

        .btn-start {
            background: var(--primary);
            color: white;
            width: 100%;
            padding: 16px;
            border-radius: 14px;
            border: none;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
        }

        #murojaahView {
            display: none;
            padding-bottom: 180px;
        }

        .top-bar {
            position: sticky;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            padding-top: max(15px, env(safe-area-inset-top));
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            z-index: 50;
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-btn {
            font-size: 1.3rem;
            color: var(--text-dark);
            background: none;
            border: none;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #f1f5f9;
        }

        .surah-info-card {
            display: flex;
            flex-direction: column;
        }

        .surah-name {
            font-weight: 800;
            font-size: 1rem;
        }

        .surah-meta {
            font-size: 0.8rem;
            color: #64748b;
        }

        .bookmark-btn {
            font-size: 1.4rem;
            color: #cbd5e1;
            background: none;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .bookmark-btn.active {
            color: var(--primary);
        }

        .mushaf-container {
            max-width: 700px;
            margin: 20px auto;
            padding: clamp(20px, 4vw, 40px) clamp(15px, 3vw, 30px);
            background: var(--paper);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--paper-border);
            min-height: 60vh;
        }

        .mushaf-line {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            align-items: center;
            min-height: 55px;
            padding: 5px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            flex-wrap: nowrap;
            width: 100%;
        }

        .mushaf-line.centered {
            justify-content: center;
            gap: 15px;
        }

        .ayah-word {
            font-family: 'Uthmani', serif;
            font-size: clamp(1.4rem, 5vw, 2.2rem);
            line-height: 1.6;
            color: var(--text-dark);
            transition: 0.2s;
            padding: 0 2px;
            position: relative;
            cursor: pointer;
        }

        .mode-murojaah .ayah-word {
            color: transparent;
            border-bottom: 2px dashed #b4a269;
            user-select: none;
        }

        .mode-murojaah .ayah-word:hover {
            border-bottom-color: var(--primary);
        }

        .mode-murojaah .ayah-word.read-correctly {
            color: var(--primary) !important;
            border-bottom-style: solid;
            text-shadow: 0 0 1px var(--primary);
        }

        .mode-murojaah .ayah-word.target-word {
            border-bottom: 3px solid #ef4444;
        }

        .ayah-end {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%23b4a269" stroke-width="3"/><circle cx="50" cy="50" r="38" fill="none" stroke="%23b4a269" stroke-width="1" stroke-dasharray="2,2"/></svg>') no-repeat center;
            background-size: contain;
            font-size: 0.9rem;
            color: #b4a269;
            font-weight: 700;
            margin: 0 5px;
            flex-shrink: 0;
        }

        .surah-title-banner {
            width: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="30" viewBox="0 0 100 30" preserveAspectRatio="none"><rect x="0" y="0" width="100" height="30" fill="%23fefaf0" stroke="%23b4a269" stroke-width="2"/></svg>') no-repeat center;
            background-size: 100% 100%;
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: 1.8rem;
            color: #857a55;
            padding: 10px 0;
            margin: 15px 0;
        }

        .bismillah {
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: 2rem;
            margin: 5px 0 15px 0;
            width: 100%;
        }

        .bottom-wrapper {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 92%;
            max-width: 600px;
            z-index: 50;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .live-transcript {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(5px);
            color: white;
            width: 100%;
            padding: 10px 15px;
            border-radius: 12px;
            font-size: 0.9rem;
            text-align: center;
            direction: rtl;
            font-family: 'Uthmani', serif;
            min-height: 40px;
            display: none;
        }

        .live-transcript.active {
            display: block;
        }

        .bottom-bar {
            width: 100%;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            border: 1px solid var(--line-color);
        }

        .bb-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #f1f5f9;
            border: none;
            font-size: 1.1rem;
            color: var(--text-dark);
            cursor: pointer;
            transition: 0.2s;
        }

        .bb-btn:hover {
            background: #e2e8f0;
        }

        .bb-text {
            font-size: 0.85rem;
            font-weight: 700;
            background: var(--bg-color);
            padding: 8px 14px;
            border-radius: 12px;
        }

        .mic-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #10b981);
            color: white;
            font-size: 1.8rem;
            border: none;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            margin-top: -30px;
            border: 5px solid white;
            position: relative;
        }

        .mic-btn.listening {
            animation: pulse-mic 1.5s infinite;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
        }

        @keyframes pulse-mic {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .toast {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-dark);
            color: white;
            padding: 12px 24px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            opacity: 0;
            pointer-events: none;
            transition: 0.3s;
            z-index: 200;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <div id="dashboardView">
        <div class="dash-header">
            <h1 class="dash-title">Murojaah Realtime</h1>
            <a href="index.php" style="color:var(--text-dark); font-size:1.2rem;"><i class="fas fa-home"></i></a>
        </div>
        <div class="bookmark-card" id="bookmarkCard" onclick="loadBookmarkedPage()">
            <div class="bookmark-label"><i class="fas fa-bookmark"></i> Lanjutkan Murojaah</div>
            <div class="bookmark-surah" id="bmSurahName">Belum ada hafalan</div>
            <div class="bookmark-meta" id="bmPageInfo">Pilih surah di bawah untuk mulai</div>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama surah..." onkeyup="filterSurah()">
        </div>
        <div class="surah-list" id="surahListContainer"></div>
    </div>

    <div class="modal-overlay" id="rangeModal">
        <div class="range-modal">
            <h3 id="rmTitle">Al-Baqarah</h3>
            <label style="font-size:0.9rem;font-weight:600;margin-bottom:8px;display:block;">Pilih Halaman Mushaf:</label>
            <select id="rmPageSelect"></select>
            <button class="btn-start" onclick="startMurojaahFromModal()">Mulai Hafalan <i class="fas fa-arrow-right"></i></button>
            <button style="width:100%;padding:14px;background:none;border:none;margin-top:10px;font-weight:600;color:#64748b;cursor:pointer;" onclick="document.getElementById('rangeModal').style.display='none'">Batal</button>
        </div>
    </div>

    <div id="murojaahView" class="mode-murojaah">
        <div class="top-bar">
            <div class="top-left">
                <button class="back-btn" onclick="backToDashboard()"><i class="fas fa-arrow-left"></i></button>
                <div class="surah-info-card">
                    <span class="surah-name" id="uiSurahName">Memuat...</span>
                    <span class="surah-meta" id="uiPageMeta">Halaman -</span>
                </div>
            </div>
            <button class="bookmark-btn" id="btnBookmark" onclick="toggleBookmark()"><i class="fas fa-bookmark"></i></button>
        </div>

        <div class="mushaf-container">
            <div style="text-align:center;font-size:0.85rem;color:#64748b;margin-bottom:15px;">
                <i class="fas fa-lightbulb" style="color:#eab308;"></i> Klik kata jika suara tidak terdeteksi.
            </div>
            <div id="quranPageContainer" style="text-align:center;">
                <i class="fas fa-spinner fa-spin" style="font-size:2rem;color:var(--primary);margin:50px 0;"></i>
            </div>
        </div>

        <div class="bottom-wrapper">
            <div class="live-transcript" id="liveTranscript">...</div>
            <div class="bottom-bar">
                <button class="bb-btn" id="btnEye" onclick="toggleMode()"><i class="fas fa-eye-slash"></i></button>
                <button class="bb-btn" onclick="changePage(-1)"><i class="fas fa-chevron-right"></i></button>
                <button class="mic-btn" id="btnMic" onclick="toggleRecording()">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="bb-btn" onclick="changePage(1)"><i class="fas fa-chevron-left"></i></button>
                <span class="bb-text">Hlm <span id="lblBottomPage">-</span></span>
            </div>
        </div>
    </div>
    <div class="toast" id="toastMsg">Notifikasi</div>

    <script>
        // ==================== DATA SURAH LENGKAP 114 SURAH ====================
        const surahsData = [{
                id: 1,
                name: "Al-Fatihah",
                ayahs: 7,
                startPage: 1,
                endPage: 1,
                arabic: "الفاتحة"
            },
            {
                id: 2,
                name: "Al-Baqarah",
                ayahs: 286,
                startPage: 2,
                endPage: 49,
                arabic: "البقرة"
            },
            {
                id: 3,
                name: "Ali 'Imran",
                ayahs: 200,
                startPage: 50,
                endPage: 76,
                arabic: "آل عمران"
            },
            {
                id: 4,
                name: "An-Nisa'",
                ayahs: 176,
                startPage: 77,
                endPage: 106,
                arabic: "النساء"
            },
            {
                id: 5,
                name: "Al-Ma'idah",
                ayahs: 120,
                startPage: 106,
                endPage: 127,
                arabic: "المائدة"
            },
            {
                id: 6,
                name: "Al-An'am",
                ayahs: 165,
                startPage: 128,
                endPage: 150,
                arabic: "الأنعام"
            },
            {
                id: 7,
                name: "Al-A'raf",
                ayahs: 206,
                startPage: 151,
                endPage: 176,
                arabic: "الأعراف"
            },
            {
                id: 8,
                name: "Al-Anfal",
                ayahs: 75,
                startPage: 177,
                endPage: 186,
                arabic: "الأنفال"
            },
            {
                id: 9,
                name: "At-Taubah",
                ayahs: 129,
                startPage: 187,
                endPage: 207,
                arabic: "التوبة"
            },
            {
                id: 10,
                name: "Yunus",
                ayahs: 109,
                startPage: 208,
                endPage: 220,
                arabic: "يونس"
            },
            {
                id: 11,
                name: "Hud",
                ayahs: 123,
                startPage: 221,
                endPage: 235,
                arabic: "هود"
            },
            {
                id: 12,
                name: "Yusuf",
                ayahs: 111,
                startPage: 235,
                endPage: 248,
                arabic: "يوسف"
            },
            {
                id: 13,
                name: "Ar-Ra'd",
                ayahs: 43,
                startPage: 249,
                endPage: 254,
                arabic: "الرعد"
            },
            {
                id: 14,
                name: "Ibrahim",
                ayahs: 52,
                startPage: 255,
                endPage: 261,
                arabic: "إبراهيم"
            },
            {
                id: 15,
                name: "Al-Hijr",
                ayahs: 99,
                startPage: 262,
                endPage: 267,
                arabic: "الحجر"
            },
            {
                id: 16,
                name: "An-Nahl",
                ayahs: 128,
                startPage: 267,
                endPage: 281,
                arabic: "النحل"
            },
            {
                id: 17,
                name: "Al-Isra'",
                ayahs: 111,
                startPage: 282,
                endPage: 293,
                arabic: "الإسراء"
            },
            {
                id: 18,
                name: "Al-Kahf",
                ayahs: 110,
                startPage: 293,
                endPage: 304,
                arabic: "الكهف"
            },
            {
                id: 19,
                name: "Maryam",
                ayahs: 98,
                startPage: 305,
                endPage: 312,
                arabic: "مريم"
            },
            {
                id: 20,
                name: "Taha",
                ayahs: 135,
                startPage: 312,
                endPage: 321,
                arabic: "طه"
            },
            {
                id: 21,
                name: "Al-Anbiya'",
                ayahs: 112,
                startPage: 322,
                endPage: 331,
                arabic: "الأنبياء"
            },
            {
                id: 22,
                name: "Al-Hajj",
                ayahs: 78,
                startPage: 332,
                endPage: 341,
                arabic: "الحج"
            },
            {
                id: 23,
                name: "Al-Mu'minun",
                ayahs: 118,
                startPage: 342,
                endPage: 349,
                arabic: "المؤمنون"
            },
            {
                id: 24,
                name: "An-Nur",
                ayahs: 64,
                startPage: 350,
                endPage: 359,
                arabic: "النور"
            },
            {
                id: 25,
                name: "Al-Furqan",
                ayahs: 77,
                startPage: 359,
                endPage: 366,
                arabic: "الفرقان"
            },
            {
                id: 26,
                name: "Asy-Syu'ara'",
                ayahs: 227,
                startPage: 367,
                endPage: 376,
                arabic: "الشعراء"
            },
            {
                id: 27,
                name: "An-Naml",
                ayahs: 93,
                startPage: 377,
                endPage: 385,
                arabic: "النمل"
            },
            {
                id: 28,
                name: "Al-Qasas",
                ayahs: 88,
                startPage: 385,
                endPage: 396,
                arabic: "القصص"
            },
            {
                id: 29,
                name: "Al-'Ankabut",
                ayahs: 69,
                startPage: 396,
                endPage: 404,
                arabic: "العنكبوت"
            },
            {
                id: 30,
                name: "Ar-Rum",
                ayahs: 60,
                startPage: 404,
                endPage: 410,
                arabic: "الروم"
            },
            {
                id: 31,
                name: "Luqman",
                ayahs: 34,
                startPage: 411,
                endPage: 414,
                arabic: "لقمان"
            },
            {
                id: 32,
                name: "As-Sajdah",
                ayahs: 30,
                startPage: 415,
                endPage: 417,
                arabic: "السجدة"
            },
            {
                id: 33,
                name: "Al-Ahzab",
                ayahs: 73,
                startPage: 418,
                endPage: 427,
                arabic: "الأحزاب"
            },
            {
                id: 34,
                name: "Saba'",
                ayahs: 54,
                startPage: 428,
                endPage: 434,
                arabic: "سبأ"
            },
            {
                id: 35,
                name: "Fatir",
                ayahs: 45,
                startPage: 434,
                endPage: 440,
                arabic: "فاطر"
            },
            {
                id: 36,
                name: "Yasin",
                ayahs: 83,
                startPage: 440,
                endPage: 445,
                arabic: "يس"
            },
            {
                id: 37,
                name: "As-Saffat",
                ayahs: 182,
                startPage: 446,
                endPage: 452,
                arabic: "الصافات"
            },
            {
                id: 38,
                name: "Sad",
                ayahs: 86,
                startPage: 453,
                endPage: 458,
                arabic: "ص"
            },
            {
                id: 39,
                name: "Az-Zumar",
                ayahs: 75,
                startPage: 458,
                endPage: 467,
                arabic: "الزمر"
            },
            {
                id: 40,
                name: "Ghafir",
                ayahs: 85,
                startPage: 467,
                endPage: 476,
                arabic: "غافر"
            },
            {
                id: 41,
                name: "Fussilat",
                ayahs: 54,
                startPage: 477,
                endPage: 482,
                arabic: "فصلت"
            },
            {
                id: 42,
                name: "Asy-Syura",
                ayahs: 53,
                startPage: 483,
                endPage: 489,
                arabic: "الشورى"
            },
            {
                id: 43,
                name: "Az-Zukhruf",
                ayahs: 89,
                startPage: 489,
                endPage: 495,
                arabic: "الزخرف"
            },
            {
                id: 44,
                name: "Ad-Dukhan",
                ayahs: 59,
                startPage: 496,
                endPage: 498,
                arabic: "الدخان"
            },
            {
                id: 45,
                name: "Al-Jasiyah",
                ayahs: 37,
                startPage: 499,
                endPage: 502,
                arabic: "الجاثية"
            },
            {
                id: 46,
                name: "Al-Ahqaf",
                ayahs: 35,
                startPage: 502,
                endPage: 506,
                arabic: "الأحقاف"
            },
            {
                id: 47,
                name: "Muhammad",
                ayahs: 38,
                startPage: 507,
                endPage: 510,
                arabic: "محمد"
            },
            {
                id: 48,
                name: "Al-Fath",
                ayahs: 29,
                startPage: 511,
                endPage: 515,
                arabic: "الفتح"
            },
            {
                id: 49,
                name: "Al-Hujurat",
                ayahs: 18,
                startPage: 515,
                endPage: 517,
                arabic: "الحجرات"
            },
            {
                id: 50,
                name: "Qaf",
                ayahs: 45,
                startPage: 518,
                endPage: 520,
                arabic: "ق"
            },
            {
                id: 51,
                name: "Az-Zariyat",
                ayahs: 60,
                startPage: 520,
                endPage: 523,
                arabic: "الذاريات"
            },
            {
                id: 52,
                name: "At-Tur",
                ayahs: 49,
                startPage: 523,
                endPage: 525,
                arabic: "الطور"
            },
            {
                id: 53,
                name: "An-Najm",
                ayahs: 62,
                startPage: 526,
                endPage: 528,
                arabic: "النجم"
            },
            {
                id: 54,
                name: "Al-Qamar",
                ayahs: 55,
                startPage: 528,
                endPage: 531,
                arabic: "القمر"
            },
            {
                id: 55,
                name: "Ar-Rahman",
                ayahs: 78,
                startPage: 531,
                endPage: 534,
                arabic: "الرحمن"
            },
            {
                id: 56,
                name: "Al-Waqi'ah",
                ayahs: 96,
                startPage: 534,
                endPage: 537,
                arabic: "الواقعة"
            },
            {
                id: 57,
                name: "Al-Hadid",
                ayahs: 29,
                startPage: 537,
                endPage: 541,
                arabic: "الحديد"
            },
            {
                id: 58,
                name: "Al-Mujadilah",
                ayahs: 22,
                startPage: 542,
                endPage: 545,
                arabic: "المجادلة"
            },
            {
                id: 59,
                name: "Al-Hasyr",
                ayahs: 24,
                startPage: 545,
                endPage: 548,
                arabic: "الحشر"
            },
            {
                id: 60,
                name: "Al-Mumtahanah",
                ayahs: 13,
                startPage: 549,
                endPage: 551,
                arabic: "الممتحنة"
            },
            {
                id: 61,
                name: "As-Saff",
                ayahs: 14,
                startPage: 551,
                endPage: 552,
                arabic: "الصف"
            },
            {
                id: 62,
                name: "Al-Jumu'ah",
                ayahs: 11,
                startPage: 553,
                endPage: 554,
                arabic: "الجمعة"
            },
            {
                id: 63,
                name: "Al-Munafiqun",
                ayahs: 11,
                startPage: 554,
                endPage: 555,
                arabic: "المنافقون"
            },
            {
                id: 64,
                name: "At-Tagabun",
                ayahs: 18,
                startPage: 556,
                endPage: 557,
                arabic: "التغابن"
            },
            {
                id: 65,
                name: "At-Talaq",
                ayahs: 12,
                startPage: 558,
                endPage: 559,
                arabic: "الطلاق"
            },
            {
                id: 66,
                name: "At-Tahrim",
                ayahs: 12,
                startPage: 560,
                endPage: 561,
                arabic: "التحريم"
            },
            {
                id: 67,
                name: "Al-Mulk",
                ayahs: 30,
                startPage: 562,
                endPage: 564,
                arabic: "الملك"
            },
            {
                id: 68,
                name: "Al-Qalam",
                ayahs: 52,
                startPage: 564,
                endPage: 566,
                arabic: "القلم"
            },
            {
                id: 69,
                name: "Al-Haqqah",
                ayahs: 52,
                startPage: 566,
                endPage: 568,
                arabic: "الحاقة"
            },
            {
                id: 70,
                name: "Al-Ma'arij",
                ayahs: 44,
                startPage: 568,
                endPage: 570,
                arabic: "المعارج"
            },
            {
                id: 71,
                name: "Nuh",
                ayahs: 28,
                startPage: 570,
                endPage: 571,
                arabic: "نوح"
            },
            {
                id: 72,
                name: "Al-Jinn",
                ayahs: 28,
                startPage: 572,
                endPage: 573,
                arabic: "الجن"
            },
            {
                id: 73,
                name: "Al-Muzzammil",
                ayahs: 20,
                startPage: 574,
                endPage: 575,
                arabic: "المزمل"
            },
            {
                id: 74,
                name: "Al-Muddassir",
                ayahs: 56,
                startPage: 575,
                endPage: 577,
                arabic: "المدثر"
            },
            {
                id: 75,
                name: "Al-Qiyamah",
                ayahs: 40,
                startPage: 577,
                endPage: 578,
                arabic: "القيامة"
            },
            {
                id: 76,
                name: "Al-Insan",
                ayahs: 31,
                startPage: 578,
                endPage: 580,
                arabic: "الإنسان"
            },
            {
                id: 77,
                name: "Al-Mursalat",
                ayahs: 50,
                startPage: 580,
                endPage: 581,
                arabic: "المرسلات"
            },
            {
                id: 78,
                name: "An-Naba'",
                ayahs: 40,
                startPage: 582,
                endPage: 583,
                arabic: "النبأ"
            },
            {
                id: 79,
                name: "An-Nazi'at",
                ayahs: 46,
                startPage: 583,
                endPage: 584,
                arabic: "النازعات"
            },
            {
                id: 80,
                name: "'Abasa",
                ayahs: 42,
                startPage: 585,
                endPage: 585,
                arabic: "عبس"
            },
            {
                id: 81,
                name: "At-Takwir",
                ayahs: 29,
                startPage: 586,
                endPage: 586,
                arabic: "التكوير"
            },
            {
                id: 82,
                name: "Al-Infitar",
                ayahs: 19,
                startPage: 587,
                endPage: 587,
                arabic: "الانفطار"
            },
            {
                id: 83,
                name: "Al-Mutaffifin",
                ayahs: 36,
                startPage: 587,
                endPage: 589,
                arabic: "المطففين"
            },
            {
                id: 84,
                name: "Al-Insyiqaq",
                ayahs: 25,
                startPage: 589,
                endPage: 590,
                arabic: "الانشقاق"
            },
            {
                id: 85,
                name: "Al-Buruj",
                ayahs: 22,
                startPage: 590,
                endPage: 590,
                arabic: "البروج"
            },
            {
                id: 86,
                name: "At-Tariq",
                ayahs: 17,
                startPage: 591,
                endPage: 591,
                arabic: "الطارق"
            },
            {
                id: 87,
                name: "Al-A'la",
                ayahs: 19,
                startPage: 591,
                endPage: 592,
                arabic: "الأعلى"
            },
            {
                id: 88,
                name: "Al-Gasyiyah",
                ayahs: 26,
                startPage: 592,
                endPage: 592,
                arabic: "الغاشية"
            },
            {
                id: 89,
                name: "Al-Fajr",
                ayahs: 30,
                startPage: 593,
                endPage: 594,
                arabic: "الفجر"
            },
            {
                id: 90,
                name: "Al-Balad",
                ayahs: 20,
                startPage: 594,
                endPage: 594,
                arabic: "البلد"
            },
            {
                id: 91,
                name: "Asy-Syams",
                ayahs: 15,
                startPage: 595,
                endPage: 595,
                arabic: "الشمس"
            },
            {
                id: 92,
                name: "Al-Lail",
                ayahs: 21,
                startPage: 595,
                endPage: 596,
                arabic: "الليل"
            },
            {
                id: 93,
                name: "Ad-Duha",
                ayahs: 11,
                startPage: 596,
                endPage: 596,
                arabic: "الضحى"
            },
            {
                id: 94,
                name: "Asy-Syarh",
                ayahs: 8,
                startPage: 596,
                endPage: 596,
                arabic: "الشرح"
            },
            {
                id: 95,
                name: "At-Tin",
                ayahs: 8,
                startPage: 597,
                endPage: 597,
                arabic: "التين"
            },
            {
                id: 96,
                name: "Al-'Alaq",
                ayahs: 19,
                startPage: 597,
                endPage: 597,
                arabic: "العلق"
            },
            {
                id: 97,
                name: "Al-Qadr",
                ayahs: 5,
                startPage: 598,
                endPage: 598,
                arabic: "القدر"
            },
            {
                id: 98,
                name: "Al-Bayyinah",
                ayahs: 8,
                startPage: 598,
                endPage: 599,
                arabic: "البينة"
            },
            {
                id: 99,
                name: "Az-Zalzalah",
                ayahs: 8,
                startPage: 599,
                endPage: 599,
                arabic: "الزلزلة"
            },
            {
                id: 100,
                name: "Al-'Adiyat",
                ayahs: 11,
                startPage: 599,
                endPage: 600,
                arabic: "العاديات"
            },
            {
                id: 101,
                name: "Al-Qari'ah",
                ayahs: 11,
                startPage: 600,
                endPage: 600,
                arabic: "القارعة"
            },
            {
                id: 102,
                name: "At-Takasur",
                ayahs: 8,
                startPage: 600,
                endPage: 600,
                arabic: "التكاثر"
            },
            {
                id: 103,
                name: "Al-'Asr",
                ayahs: 3,
                startPage: 601,
                endPage: 601,
                arabic: "العصر"
            },
            {
                id: 104,
                name: "Al-Humazah",
                ayahs: 9,
                startPage: 601,
                endPage: 601,
                arabic: "الهمزة"
            },
            {
                id: 105,
                name: "Al-Fil",
                ayahs: 5,
                startPage: 601,
                endPage: 601,
                arabic: "الفيل"
            },
            {
                id: 106,
                name: "Quraisy",
                ayahs: 4,
                startPage: 602,
                endPage: 602,
                arabic: "قريش"
            },
            {
                id: 107,
                name: "Al-Ma'un",
                ayahs: 7,
                startPage: 602,
                endPage: 602,
                arabic: "الماعون"
            },
            {
                id: 108,
                name: "Al-Kausar",
                ayahs: 3,
                startPage: 602,
                endPage: 602,
                arabic: "الكوثر"
            },
            {
                id: 109,
                name: "Al-Kafirun",
                ayahs: 6,
                startPage: 603,
                endPage: 603,
                arabic: "الكافرون"
            },
            {
                id: 110,
                name: "An-Nasr",
                ayahs: 3,
                startPage: 603,
                endPage: 603,
                arabic: "النصر"
            },
            {
                id: 111,
                name: "Al-Lahab",
                ayahs: 5,
                startPage: 603,
                endPage: 603,
                arabic: "المسد"
            },
            {
                id: 112,
                name: "Al-Ikhlas",
                ayahs: 4,
                startPage: 604,
                endPage: 604,
                arabic: "الإخلاص"
            },
            {
                id: 113,
                name: "Al-Falaq",
                ayahs: 5,
                startPage: 604,
                endPage: 604,
                arabic: "الفلق"
            },
            {
                id: 114,
                name: "An-Nas",
                ayahs: 6,
                startPage: 604,
                endPage: 604,
                arabic: "الناس"
            }
        ];

        // ==================== VARIABEL UTAMA ====================
        let currentPage = 1;
        let isMurojaahMode = true;
        let quranWords = [];
        let currentWordTargetIdx = 0;

        // ==================== SPEECH RECOGNITION ====================
        let recognition = null;
        let isRecording = false;       // status yang diinginkan user (mic ON/OFF)
        let recognitionActive = false; // status aktual apakah engine speech sedang berjalan
        let speechBuffer = '';         // akumulasi teks FINAL yang sudah dikonfirmasi browser
        let interimBuffer = '';        // akumulasi teks INTERIM (sedang diucapkan, belum final)
        let silenceTimer = null;
        let restartTimer = null;
        const SILENCE_TIMEOUT = 4000; // ms tanpa suara sebelum buffer direset
        const RESTART_DELAY = 150;    // ms delay sebelum recognition di-restart

        document.addEventListener('DOMContentLoaded', () => {
            renderSurahList();
            checkBookmarkStatus();
            initSwipeGestures();
            initSpeechRecognition();
        });

        // ==================== DASHBOARD & NAVIGASI ====================
        function renderSurahList(filter = '') {
            const container = document.getElementById('surahListContainer');
            container.innerHTML = '';
            surahsData.forEach(s => {
                if (s.name.toLowerCase().includes(filter.toLowerCase())) {
                    container.innerHTML += `
                    <div class="surah-item" onclick="openRangeModal(${s.id})">
                        <div class="si-left">
                            <div class="si-number">${s.id}</div>
                            <div>
                                <div class="si-name">${s.name}</div>
                                <div class="si-meta">${s.ayahs} Ayat</div>
                            </div>
                        </div>
                        <div class="si-arabic">${s.arabic}</div>
                    </div>`;
                }
            });
        }

        function filterSurah() {
            renderSurahList(document.getElementById('searchInput').value);
        }

        function openRangeModal(surahId) {
            const surah = surahsData.find(s => s.id === surahId);
            document.getElementById('rmTitle').innerText = surah.name;
            const pSelect = document.getElementById('rmPageSelect');
            pSelect.innerHTML = '';
            for (let p = surah.startPage; p <= surah.endPage; p++) pSelect.innerHTML += `<option value="${p}">Halaman ${p}</option>`;
            document.getElementById('rangeModal').style.display = 'flex';
        }

        function startMurojaahFromModal() {
            const page = parseInt(document.getElementById('rmPageSelect').value);
            document.getElementById('rangeModal').style.display = 'none';
            openMurojaah(page);
        }

        function checkBookmarkStatus() {
            const saved = localStorage.getItem('hifzly_murojaah_bookmark');
            if (saved) {
                const page = parseInt(saved);
                const surah = surahsData.slice().reverse().find(s => s.startPage <= page);
                document.getElementById('bmSurahName').innerText = surah?.name || '?';
                document.getElementById('bmPageInfo').innerText = `Melanjutkan Halaman ${page}`;
            }
        }

        function loadBookmarkedPage() {
            const saved = localStorage.getItem('hifzly_murojaah_bookmark');
            if (saved) openMurojaah(parseInt(saved));
        }

        function openMurojaah(page) {
            currentPage = page;
            document.getElementById('dashboardView').style.display = 'none';
            document.getElementById('murojaahView').style.display = 'block';
            window.scrollTo(0, 0);
            loadQuranPage(currentPage);
        }

        function backToDashboard() {
            if (isRecording) toggleRecording();
            document.getElementById('murojaahView').style.display = 'none';
            document.getElementById('dashboardView').style.display = 'block';
            checkBookmarkStatus();
        }

        async function loadQuranPage(page) {
            document.getElementById('quranPageContainer').innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size:2rem;color:var(--primary);margin:50px 0;"></i>';
            document.getElementById('lblBottomPage').innerText = page;

            const saved = localStorage.getItem('hifzly_murojaah_bookmark');
            const btnBm = document.getElementById('btnBookmark');
            if (saved && parseInt(saved) === page) btnBm.classList.add('active');
            else btnBm.classList.remove('active');

            try {
                const res = await fetch(`https://api.quran.com/api/v4/verses/by_page/${page}?language=id&words=true&word_fields=text_uthmani,line_number`);
                const data = await res.json();
                renderExactMushafLayout(data.verses, page);
                currentWordTargetIdx = 0;
                speechBuffer = '';
                interimBuffer = '';
                updateTargetIndicator();
            } catch (err) {
                showToast("Gagal memuat halaman");
            }
        }

        function renderExactMushafLayout(verses, pageNum) {
            const container = document.getElementById('quranPageContainer');
            container.innerHTML = '';
            quranWords = [];

            const firstSurahId = parseInt(verses[0].verse_key.split(':')[0]);
            const sName = surahsData.find(s => s.id === firstSurahId)?.name || "";
            document.getElementById('uiSurahName').innerText = sName;
            document.getElementById('uiPageMeta').innerText = `Halaman ${pageNum} | Juz ${verses[0].juz_number}`;

            let linesMap = {};
            verses.forEach(verse => {
                const ayahNum = parseInt(verse.verse_key.split(':')[1]);
                const sId = parseInt(verse.verse_key.split(':')[0]);

                if (ayahNum === 1) {
                    const namaS = surahsData.find(s => s.id === sId)?.arabic;
                    if (!linesMap['h_' + sId]) linesMap['h_' + sId] = [];
                    linesMap['h_' + sId].push({
                        type: 'header',
                        text: `سورة ${namaS}`
                    });
                    if (sId !== 1 && sId !== 9) {
                        if (!linesMap['b_' + sId]) linesMap['b_' + sId] = [];
                        linesMap['b_' + sId].push({
                            type: 'bismillah',
                            text: 'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ'
                        });
                    }
                }

                verse.words.forEach(word => {
                    if (!linesMap[word.line_number]) linesMap[word.line_number] = [];
                    if (word.char_type_name === 'end') {
                        linesMap[word.line_number].push({
                            type: 'end',
                            text: convertToArabicNumber(ayahNum)
                        });
                    } else if (word.text_uthmani) {
                        linesMap[word.line_number].push({
                            type: 'word',
                            text: word.text_uthmani,
                            text_raw: word.text_uthmani
                        });
                    }
                });
            });

            let html = '';
            const sortedLines = Object.keys(linesMap).sort((a, b) => {
                if (a.includes('h_') || a.includes('b_')) return -1;
                if (b.includes('h_') || b.includes('b_')) return 1;
                return parseInt(a) - parseInt(b);
            });

            let wordCounter = 0;
            sortedLines.forEach(lineNum => {
                const items = linesMap[lineNum];
                if (items[0].type === 'header') {
                    html += `<div class="surah-title-banner">${items[0].text}</div>`;
                    return;
                }
                if (items[0].type === 'bismillah') {
                    html += `<div class="bismillah">${items[0].text}</div>`;
                    return;
                }

                const centeredClass = items.length < 6 ? 'centered' : '';
                html += `<div class="mushaf-line ${centeredClass}">`;
                items.forEach(item => {
                    if (item.type === 'end') {
                        html += `<span class="ayah-end">${item.text}</span>`;
                    } else {
                        html += `<span class="ayah-word" id="w-${wordCounter}" onclick="manualReveal(${wordCounter})">${item.text}</span>`;
                        quranWords.push({
                            id: `w-${wordCounter}`,
                            normalized: normalizeArabicExtreme(item.text_raw)
                        });
                        wordCounter++;
                    }
                });
                html += `</div>`;
            });
            container.innerHTML = html;
        }

        // ==================== MANUAL TAP ====================
        function manualReveal(index) {
            if (index >= currentWordTargetIdx) {
                for (let i = currentWordTargetIdx; i <= index; i++) {
                    document.getElementById(quranWords[i].id).classList.add('read-correctly');
                    document.getElementById(quranWords[i].id).classList.remove('target-word');
                }
                currentWordTargetIdx = index + 1;
                updateTargetIndicator();
                speechBuffer = '';
                interimBuffer = '';
                if (currentWordTargetIdx >= quranWords.length) {
                    showToast("Halaman selesai!");
                    setTimeout(() => changePage(1), 1500);
                }
            }
        }

        // ==================== SPEECH RECOGNITION (DIPERBAIKI) ====================
        function initSpeechRecognition() {
            window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!window.SpeechRecognition) {
                alert("Browser tidak support Mic. Gunakan Google Chrome.");
                return;
            }

            recognition = new SpeechRecognition();
            recognition.lang = 'ar-SA';
            // continuous + interimResults: supaya suara diproses SAAT diucapkan (real-time),
            // bukan menunggu jeda diam dulu baru dicek. Ini kunci biar tidak "nge-lag/nge-loop".
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.maxAlternatives = 1;

            recognition.onresult = (event) => {
                let newFinal = '';
                let newInterim = '';

                // Proses hanya hasil BARU sejak resultIndex
                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const res = event.results[i];
                    if (res.isFinal) {
                        newFinal += ' ' + res[0].transcript;
                    } else {
                        newInterim += ' ' + res[0].transcript;
                    }
                }

                // Akumulasi final ke speechBuffer
                if (newFinal.trim()) {
                    speechBuffer += ' ' + newFinal;
                    interimBuffer = ''; // final sudah ada → reset interim lama
                }

                // Interim selalu diganti dengan yang terbaru (bukan diakumulasi)
                interimBuffer = newInterim;

                const liveText = (speechBuffer + ' ' + interimBuffer).trim();
                document.getElementById('liveTranscript').innerText = liveText || '...';

                resetSilenceTimer();
                // Proses real-time: gabungkan final + interim untuk cocokkan kata target
                processSpeechBuffer();
            };

            recognition.onerror = (e) => {
                console.warn('Speech error:', e.error);
                recognitionActive = false;
                if (e.error === 'not-allowed' || e.error === 'service-not-allowed') {
                    isRecording = false;
                    renderMicState();
                    showToast("Akses Mikrofon diblokir!");
                    return;
                }
                // Error lain (no-speech, network, aborted) → onend yang tangani restart
            };

            recognition.onend = () => {
                recognitionActive = false;
                if (isRecording) {
                    clearTimeout(restartTimer);
                    restartTimer = setTimeout(startRecognition, RESTART_DELAY);
                }
            };
        }

        function startRecognition() {
            if (!recognition || !isRecording || recognitionActive) return;
            try {
                recognition.start();
                recognitionActive = true;
                document.getElementById('liveTranscript').classList.add('active');
            } catch (e) {
                recognitionActive = false;
                clearTimeout(restartTimer);
                restartTimer = setTimeout(startRecognition, RESTART_DELAY);
            }
        }

        function resetSilenceTimer() {
            clearTimeout(silenceTimer);
            silenceTimer = setTimeout(() => {
                speechBuffer = '';
                interimBuffer = '';
                document.getElementById('liveTranscript').innerText = isRecording ? 'Mendengarkan...' : '';
            }, SILENCE_TIMEOUT);
        }

        // ==================== PROSES PENCOCOKAN KATA (REAL-TIME) ====================
        function processSpeechBuffer() {
            if (currentWordTargetIdx >= quranWords.length) return;

            // Gabungkan final + interim untuk pencocokan real-time
            const combined = (speechBuffer + ' ' + interimBuffer).trim();
            if (!combined) return;

            // Pecah teks yang diucapkan menjadi array kata per-kata
            // → cocokkan KATA DEMI KATA, bukan substring mentah
            const spokenWords = normalizeArabicExtreme(combined)
                .split(/\s+/)
                .filter(w => w.length > 0);

            if (spokenWords.length === 0) return;

            let checkIdx = currentWordTargetIdx;
            let matchFound = false;
            let spokenPos = 0; // posisi pointer di array kata yang diucapkan

            // Scan maju di dalam kata yang diucapkan untuk cocokkan kata target satu per satu
            while (spokenPos < spokenWords.length && checkIdx < quranWords.length) {
                const targetWord = quranWords[checkIdx].normalized;
                if (!targetWord) { checkIdx++; continue; }

                // Cari kata target di sisa array spokenWords mulai dari spokenPos
                // Toleransi: cari hingga MAX_LOOKAHEAD posisi ke depan
                const MAX_LOOKAHEAD = 4;
                let foundAt = -1;
                for (let la = spokenPos; la < Math.min(spokenPos + MAX_LOOKAHEAD, spokenWords.length); la++) {
                    if (spokenWords[la] === targetWord ||
                        spokenWords[la].startsWith(targetWord) || // partial match (sedang diucapkan)
                        targetWord.startsWith(spokenWords[la]) && spokenWords[la].length >= 2) { // kata target belum selesai diucapkan
                        foundAt = la;
                        break;
                    }
                }

                if (foundAt >= 0) {
                    const el = document.getElementById(quranWords[checkIdx].id);
                    if (el) {
                        el.classList.add('read-correctly');
                        el.classList.remove('target-word');
                    }
                    checkIdx++;
                    spokenPos = foundAt + 1;
                    matchFound = true;
                } else {
                    // Kata target tidak ditemukan di sisa spokenWords → hentikan scan
                    break;
                }
            }

            if (matchFound) {
                currentWordTargetIdx = checkIdx;
                updateTargetIndicator();

                // Buang bagian buffer yang sudah terpakai (kata-kata yang sudah di-match)
                // Sisa kata yang belum di-match tetap ada di buffer untuk event berikutnya
                const usedWords = spokenWords.slice(0, spokenPos);
                const remainWords = spokenWords.slice(spokenPos);

                // Update speechBuffer hanya berisi sisa kata yang belum di-match
                speechBuffer = remainWords.join(' ');
                interimBuffer = '';

                clearTimeout(silenceTimer);
                resetSilenceTimer();
            }

            if (currentWordTargetIdx >= quranWords.length) {
                showToast("Masya Allah! Halaman selesai.");
                setTimeout(() => changePage(1), 1500);
            }
        }

        function toggleRecording() {
            if (!recognition) return;
            const transcriptBox = document.getElementById('liveTranscript');

            if (isRecording) {
                isRecording = false;
                clearTimeout(restartTimer);
                clearTimeout(silenceTimer);
                try {
                    recognition.stop();
                } catch (e) {}
                speechBuffer = '';
                interimBuffer = '';
                transcriptBox.classList.remove('active');
            } else {
                isRecording = true;
                speechBuffer = '';
                interimBuffer = '';
                transcriptBox.innerText = "Mendengarkan...";
                transcriptBox.classList.add('active');
                startRecognition();
            }
            renderMicState();
            updateTargetIndicator();
        }

        function renderMicState() {
            const btn = document.getElementById('btnMic');
            if (isRecording) {
                btn.classList.add('listening');
                btn.innerHTML = '<i class="fas fa-stop"></i>';
            } else {
                btn.classList.remove('listening');
                btn.innerHTML = '<i class="fas fa-microphone"></i>';
            }
        }

        function updateTargetIndicator() {
            document.querySelectorAll('.target-word').forEach(el => el.classList.remove('target-word'));
            if (isRecording && currentWordTargetIdx < quranWords.length) {
                const el = document.getElementById(quranWords[currentWordTargetIdx].id);
                if (el) el.classList.add('target-word');
            }
        }

        function normalizeArabicExtreme(text) {
            if (!text) return "";
            return text
                .replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED]/g, '')
                .replace(/[ٱآإأ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/[ىيئ]/g, 'ي')
                .replace(/ؤ/g, 'و')
                .replace(/ء/g, '')
                .replace(/\s+/g, '')
                .trim();
        }

        // ==================== UTILITAS ====================
        function changePage(direction) {
            let newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= 604) {
                loadQuranPage(newPage);
                currentPage = newPage;
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }

        function toggleMode() {
            isMurojaahMode = !isMurojaahMode;
            document.getElementById('murojaahView').className = isMurojaahMode ? 'mode-murojaah' : '';
            const btn = document.getElementById('btnEye');
            btn.innerHTML = isMurojaahMode ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        }

        function toggleBookmark() {
            const saved = localStorage.getItem('hifzly_murojaah_bookmark');
            const btn = document.getElementById('btnBookmark');
            if (saved && parseInt(saved) === currentPage) {
                localStorage.removeItem('hifzly_murojaah_bookmark');
                btn.classList.remove('active');
                showToast("Bookmark dihapus");
            } else {
                localStorage.setItem('hifzly_murojaah_bookmark', currentPage);
                btn.classList.add('active');
                showToast("Halaman disimpan");
            }
        }

        function showToast(msg) {
            const t = document.getElementById('toastMsg');
            t.innerText = msg;
            t.style.opacity = 1;
            setTimeout(() => t.style.opacity = 0, 2500);
        }

        function convertToArabicNumber(num) {
            const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return num.toString().split('').map(digit => arabicNumbers[parseInt(digit)]).join('');
        }

        function initSwipeGestures() {
            let touchstartX = 0,
                touchendX = 0;
            document.getElementById('murojaahView').addEventListener('touchstart', e => touchstartX = e.changedTouches[0].screenX, {
                passive: true
            });
            document.getElementById('murojaahView').addEventListener('touchend', e => {
                touchendX = e.changedTouches[0].screenX;
                if (touchstartX - touchendX > 80) changePage(1);
                if (touchendX - touchstartX > 80) changePage(-1);
            }, {
                passive: true
            });
        }
    </script>
</body>

</html>