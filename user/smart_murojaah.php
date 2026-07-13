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
    <title>Murojaah Interaktif — Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* FONT MUSHAF MADINAH */
        @font-face {
            font-family: 'Uthmani';
            src: url('https://cdn.jsdelivr.net/gh/fawazahmed0/quran-api@1/fonts/KFGQPC_Uthmanic_Script_HAFS_Regular.ttf') format('truetype');
        }

        :root {
            --primary: #22c55e;
            --primary-light: #bbf7d0;
            --bg-color: #fcfcfc;
            --text-dark: #1f2937;
            --line-color: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            overflow-x: hidden;
            touch-action: pan-y;
        }

        /* ---------- TOP BAR ---------- */
        .top-bar {
            position: fixed;
            top: 0;
            width: 100%;
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            padding-top: max(10px, env(safe-area-inset-top));
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.05);
            z-index: 50;
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-btn {
            font-size: 1.5rem;
            color: var(--text-dark);
            background: none;
            border: none;
            cursor: pointer;
        }

        .surah-info-card {
            background: #f3f4f6;
            border-radius: 12px;
            padding: 6px 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            border: 1px solid var(--line-color);
        }

        .surah-text {
            display: flex;
            flex-direction: column;
        }

        .surah-name {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .surah-meta {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icon-btn {
            font-size: 1.3rem;
            color: var(--text-dark);
            background: none;
            border: none;
            cursor: pointer;
        }

        .bookmark-btn.active {
            color: var(--primary);
        }

        /* ---------- MUSHAF AREA (15 LINES) ---------- */
        .mushaf-container {
            margin-top: 80px;
            margin-bottom: 100px;
            padding: 0 15px;
            min-height: 70vh;
            display: flex;
            flex-direction: column;
        }

        .mushaf-line {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid var(--line-color);
            min-height: 55px;
            padding: 5px 0;
            flex-wrap: nowrap;
            width: 100%;
        }

        .mushaf-line.centered {
            justify-content: center;
            gap: 15px;
        }

        .ayah-word {
            font-family: 'Uthmani', serif;
            font-size: clamp(1.5rem, 5vw, 2.2rem);
            line-height: 1.6;
            color: var(--text-dark);
            transition: 0.2s;
            position: relative;
            padding: 0 2px;
        }

        /* STATE MUROJAAH (TEKS DIHILANGKAN) */
        .mode-murojaah .ayah-word {
            color: transparent;
            user-select: none;
        }

        /* STATE KATA TERBACA OLEH MIC */
        .ayah-word.read-correctly {
            color: var(--primary) !important;
            text-shadow: 0 0 1px var(--primary);
        }

        .ayah-end {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%231f2937" stroke-width="3"/><circle cx="50" cy="50" r="38" fill="none" stroke="%231f2937" stroke-width="1" stroke-dasharray="2,2"/></svg>') no-repeat center;
            background-size: contain;
            font-size: 0.9rem;
            color: var(--text-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            flex-shrink: 0;
            margin: 0 5px;
        }

        .surah-title-banner {
            width: 100%;
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: 1.8rem;
            padding: 10px 0;
            background: #f9fafb;
            border-radius: 12px;
            margin: 15px 0;
            border: 1px dashed var(--line-color);
        }

        .bismillah {
            text-align: center;
            font-family: 'Uthmani', serif;
            font-size: 2rem;
            margin: 5px 0 15px 0;
            width: 100%;
        }

        /* ---------- BOTTOM FLOATING BAR ---------- */
        .bottom-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 92%;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            border: 1px solid var(--line-color);
            z-index: 50;
        }

        .bb-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .bb-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #f3f4f6;
            border: none;
            font-size: 1.1rem;
            color: var(--text-dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .bb-btn:hover {
            background: #e5e7eb;
        }

        /* Mic Button (Super Prominent) */
        .mic-btn {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4ade80, #22c55e);
            color: white;
            font-size: 1.8rem;
            border: none;
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            position: relative;
            margin-top: -20px;
            /* Buat menonjol ke atas */
            border: 4px solid #fff;
        }

        .mic-btn.listening {
            animation: pulse-mic 1.5s infinite;
            background: linear-gradient(135deg, #f87171, #ef4444);
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

        /* ---------- START MODAL ---------- */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 100;
        }

        .modal-box {
            background: white;
            width: 90%;
            max-width: 400px;
            border-radius: 24px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        }

        .modal-box h2 {
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        .modal-box select {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid var(--line-color);
            margin-bottom: 15px;
            font-size: 1rem;
            outline: none;
            background: #f9fafb;
        }

        .modal-box .btn-start {
            background: var(--text-dark);
            color: white;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
        }

        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 50px auto;
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

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-dark);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            opacity: 0;
            pointer-events: none;
            transition: 0.3s;
            z-index: 200;
        }
    </style>
</head>

<body class="mode-murojaah"> <!-- Default langsung mode hafalan (teks hilang) -->

    <!-- MODAL PILIH HALAMAN -->
    <div class="modal-overlay" id="startModal">
        <div class="modal-box">
            <h2>Mulai Murojaah</h2>
            <select id="jumpSurah">
                <option value="">-- Pilih Surah --</option>
            </select>
            <select id="jumpPage">
                <option value="">-- Atau Pilih Halaman --</option>
            </select>
            <button class="btn-start" onclick="closeModalAndLoad()">Mulai Hafalan <i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="top-left">
            <button class="menu-btn" onclick="document.getElementById('startModal').style.display='flex'"><i class="fas fa-bars"></i></button>
            <div class="surah-info-card" onclick="document.getElementById('startModal').style.display='flex'">
                <i class="far fa-bookmark" id="topBookmarkIcon" style="color: #9ca3af;"></i>
                <div class="surah-text">
                    <span class="surah-name" id="uiSurahName">Al-Baqarah</span>
                    <span class="surah-meta" id="uiPageMeta">Halaman 3 | Juz 1</span>
                </div>
            </div>
        </div>
        <div class="top-right">
            <button class="icon-btn" onclick="toggleBookmark()"><i class="fas fa-bookmark" id="btnBookmarkStar"></i></button>
            <button class="icon-btn"><i class="fas fa-cog"></i></button>
        </div>
    </div>

    <div class="toast" id="toastMsg">Tersimpan</div>

    <!-- MUSHAF AREA -->
    <div class="mushaf-container">
        <div class="loader" id="loader"></div>
        <div id="quranPage">
            <!-- Render Baris Al-Qur'an Disini -->
        </div>
    </div>

    <!-- BOTTOM BAR -->
    <div class="bottom-bar">
        <div class="bb-group">
            <button class="bb-btn" id="btnEye" onclick="toggleMode()"><i class="fas fa-eye-slash"></i></button>
            <span style="font-size: 0.85rem; font-weight:700; background:#f3f4f6; padding: 6px 12px; border-radius:12px;">Hlm <span id="lblBottomPage">-</span></span>
        </div>

        <button class="mic-btn" id="btnMic" onclick="toggleMic()">
            <i class="fas fa-microphone"></i>
        </button>

        <div class="bb-group">
            <button class="bb-btn" onclick="changePage(-1)"><i class="fas fa-chevron-right"></i></button>
            <button class="bb-btn" onclick="changePage(1)"><i class="fas fa-chevron-left"></i></button>
        </div>
    </div>

    <script>
        // DATA SURAH UNTUK MENU
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
        let isMurojaahMode = true; // Default Teks Hilang

        // STATE SPEECH RECOGNITION
        let recognition = null;
        let isListening = false;
        let quranWords = []; // Array data { element, text_normalized }
        let currentWordTargetIdx = 0; // Index kata yang harus dijawab user

        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi Opsi Modal
            const sSelect = document.getElementById('jumpSurah');
            surahs.forEach(s => sSelect.innerHTML += `<option value="${s.page}">${s.id}. ${s.name}</option>`);
            const pSelect = document.getElementById('jumpPage');
            for (let i = 1; i <= totalPages; i++) pSelect.innerHTML += `<option value="${i}">Halaman ${i}</option>`;

            // Cek Bookmark / Last Page
            const savedPage = localStorage.getItem('hifzly_murojaah_bookmark');
            if (savedPage) {
                currentPage = parseInt(savedPage);
                document.getElementById('startModal').style.display = 'none'; // Langsung load jika ada save
                loadQuranPage(currentPage);
            }

            initSpeechRecognition();
            initSwipeGestures();
        });

        // ================= UI & NAVIGATION =================
        function closeModalAndLoad() {
            const surahPage = document.getElementById('jumpSurah').value;
            const pagePage = document.getElementById('jumpPage').value;

            if (pagePage) currentPage = parseInt(pagePage);
            else if (surahPage) currentPage = parseInt(surahPage);
            else currentPage = 1; // Default

            document.getElementById('startModal').style.display = 'none';
            loadQuranPage(currentPage);
        }

        function changePage(direction) {
            let newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                if (isListening) toggleMic(); // Matikan mic saat pindah
                loadQuranPage(currentPage);
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }

        function toggleMode() {
            isMurojaahMode = !isMurojaahMode;
            document.body.className = isMurojaahMode ? 'mode-murojaah' : '';
            const btn = document.getElementById('btnEye');
            btn.innerHTML = isMurojaahMode ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
        }

        function showToast(msg) {
            const t = document.getElementById('toastMsg');
            t.innerText = msg;
            t.style.opacity = 1;
            setTimeout(() => t.style.opacity = 0, 2500);
        }

        // ================= BOOKMARK =================
        function toggleBookmark() {
            const bookmarked = localStorage.getItem('hifzly_murojaah_bookmark');
            if (bookmarked && parseInt(bookmarked) === currentPage) {
                localStorage.removeItem('hifzly_murojaah_bookmark');
                updateBookmarkUI(false);
                showToast("Bookmark dihapus");
            } else {
                localStorage.setItem('hifzly_murojaah_bookmark', currentPage);
                updateBookmarkUI(true);
                showToast("Halaman disimpan");
            }
        }

        function updateBookmarkUI(isBookmarked) {
            const btn = document.getElementById('btnBookmarkStar');
            const icon = document.getElementById('topBookmarkIcon');
            if (isBookmarked) {
                btn.style.color = 'var(--primary)';
                icon.style.color = 'var(--primary)';
            } else {
                btn.style.color = 'var(--text-dark)';
                icon.style.color = '#9ca3af';
            }
        }

        // ================= GESTURE SWIPE =================
        function initSwipeGestures() {
            let touchstartX = 0;
            let touchendX = 0;
            document.addEventListener('touchstart', e => touchstartX = e.changedTouches[0].screenX, {
                passive: true
            });
            document.addEventListener('touchend', e => {
                touchendX = e.changedTouches[0].screenX;
                // Swipe Kiri (Ke Halaman Berikutnya)
                if (touchstartX - touchendX > 70) changePage(1);
                // Swipe Kanan (Ke Halaman Sebelumnya)
                if (touchendX - touchstartX > 70) changePage(-1);
            }, {
                passive: true
            });
        }

        // ================= LOAD MUSHAF (API V4) =================
        async function loadQuranPage(page) {
            document.getElementById('quranPage').innerHTML = '';
            document.getElementById('loader').style.display = 'block';
            document.getElementById('lblBottomPage').innerText = page;

            // Cek status bookmark
            const savedPage = localStorage.getItem('hifzly_murojaah_bookmark');
            updateBookmarkUI(savedPage && parseInt(savedPage) === page);

            try {
                const response = await fetch(`https://api.quran.com/api/v4/verses/by_page/${page}?language=id&words=true&word_fields=text_uthmani,line_number`);
                const data = await response.json();
                renderExactMushafLayout(data.verses, page);
            } catch (error) {
                showToast('Gagal memuat halaman. Periksa koneksi.');
            } finally {
                document.getElementById('loader').style.display = 'none';
            }
        }

        function renderExactMushafLayout(verses, pageNum) {
            const container = document.getElementById('quranPage');
            quranWords = []; // Reset kamus hafalan
            currentWordTargetIdx = 0;

            // Info Header
            const firstVerseKey = verses[0].verse_key;
            const surahId = parseInt(firstVerseKey.split(':')[0]);
            const sName = surahs.find(s => s.id === surahId)?.name || "";
            document.getElementById('uiSurahName').innerText = sName;
            document.getElementById('uiPageMeta').innerText = `Halaman ${pageNum} | Juz ${verses[0].juz_number}`;

            let linesMap = {};

            verses.forEach(verse => {
                const ayahNum = parseInt(verse.verse_key.split(':')[1]);
                const sId = parseInt(verse.verse_key.split(':')[0]);

                if (ayahNum === 1) {
                    const namaS = surahs.find(s => s.id === sId)?.name;
                    if (!linesMap['h_' + sId]) linesMap['h_' + sId] = [];
                    linesMap['h_' + sId].push({
                        type: 'header',
                        text: `سورة ${namaS}`
                    });
                    if (sId !== 1 && sId !== 9) {
                        if (!linesMap['b_' + sId]) linesMap['b_' + sId] = [];
                        linesMap['b_' + sId].push({
                            type: 'bismillah',
                            text: 'بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ'
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
                        html += `<span class="ayah-word" id="w-${wordCounter}">${item.text}</span>`;
                        // Simpan kata ke array untuk dicocokkan dengan suara mic
                        quranWords.push({
                            id: `w-${wordCounter}`,
                            normalized: normalizeArabic(item.text_raw)
                        });
                        wordCounter++;
                    }
                });
                html += `</div>`;
            });
            container.innerHTML = html;
        }

        // ================= SPEECH RECOGNITION (AUTO HEALING & SMART MATCH) =================
        function initSpeechRecognition() {
            window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!window.SpeechRecognition) {
                showToast("Browser tidak mendukung fitur Mic.");
                return;
            }

            recognition = new SpeechRecognition();
            recognition.lang = 'ar-SA';
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.maxAlternatives = 3;

            recognition.onresult = (event) => {
                let interimTranscript = '';
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    const text = event.results[i][0].transcript;
                    if (event.results[i].isFinal) matchVoiceWithQuran(text);
                    else interimTranscript += text;
                }
                // Jika ingin melihat teks yang didengar secara realtime, bisa di-log interimTranscript
            };

            // AUTO-RESTART LOGIC (Menyelesaikan masalah "sering mati")
            recognition.onend = () => {
                if (isListening) {
                    try {
                        recognition.start();
                    } catch (e) {}
                }
            };

            recognition.onerror = (e) => {
                if (e.error === 'not-allowed') {
                    isListening = false;
                    renderMicState();
                    showToast("Akses Mic diblokir");
                }
            };
        }

        function toggleMic() {
            if (!recognition) {
                showToast("Gunakan Google Chrome / Safari terbaru.");
                return;
            }

            if (isListening) {
                isListening = false;
                recognition.stop();
            } else {
                isListening = true;
                currentWordTargetIdx = 0; // Reset dari awal halaman tiap kali mic dinyalakan
                // Hilangkan warna hijau sebelumnya
                document.querySelectorAll('.read-correctly').forEach(el => el.classList.remove('read-correctly'));
                try {
                    recognition.start();
                } catch (e) {}
            }
            renderMicState();
        }

        function renderMicState() {
            const btn = document.getElementById('btnMic');
            if (isListening) {
                btn.classList.add('listening');
                btn.innerHTML = '<i class="fas fa-stop"></i>';
            } else {
                btn.classList.remove('listening');
                btn.innerHTML = '<i class="fas fa-microphone"></i>';
            }
        }

        // ================= ARABIC NLP (NORMALIZER & FUZZY MATCH) =================

        // Pemetaan kata-kata khusus (Muqatta'at / Kebiasaan pengucapan)
        const customDict = {
            "الف لام ميم": "الم",
            "الف لام را": "الر",
            "يا سين": "يس",
            "حا ميم": "حم",
            "طا ها": "طه",
            "كاف ها يا عين صاد": "كهيعص",
            "بسم الله": "بسم الله الرحمن الرحيم"
        };

        function normalizeArabic(text) {
            if (!text) return "";
            // 1. Ganti kata phonetik ke bentuk asli (menggunakan dict)
            for (let key in customDict) {
                if (text.includes(key)) text = text.replace(key, customDict[key]);
            }

            return text
                // 2. Hapus semua harakat dan tanda tajwid/waqaf
                .replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED]/g, '')
                // 3. Normalisasi huruf yang sering ambigu saat didengar mesin
                .replace(/(آ|إ|أ)/g, 'ا')
                .replace(/(ة)/g, 'ه')
                .replace(/(ى)/g, 'ي')
                .replace(/(ؤ)/g, 'و')
                .replace(/(ئ)/g, 'ي')
                // 4. Bersihkan spasi berlebih
                .trim();
        }

        function matchVoiceWithQuran(voiceRawText) {
            if (currentWordTargetIdx >= quranWords.length) return; // Halaman Selesai

            // Normalisasi suara yang didengar
            const spokenClean = normalizeArabic(voiceRawText);
            const spokenWords = spokenClean.split(' ');

            // Coba cocokkan kata demi kata (Maju ke depan)
            spokenWords.forEach(spokenW => {
                if (currentWordTargetIdx >= quranWords.length) return;

                // Ambil target kata di Al-Qur'an
                const targetW = quranWords[currentWordTargetIdx].normalized;

                // Jika cocok (atau string spoken mengandung target), tandai hijau
                if (spokenW === targetW || spokenClean.includes(targetW)) {
                    document.getElementById(quranWords[currentWordTargetIdx].id).classList.add('read-correctly');
                    currentWordTargetIdx++;
                }
            });

            // Auto Next Page jika sudah sampai ujung
            if (currentWordTargetIdx >= quranWords.length) {
                showToast("Masya Allah! Melanjut ke halaman berikutnya...");
                setTimeout(() => changePage(1), 2000);
            }
        }

        // ================= UTILS =================
        function convertToArabicNumber(num) {
            const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return num.toString().split('').map(digit => arabicNumbers[parseInt(digit)]).join('');
        }
    </script>
</body>

</html>