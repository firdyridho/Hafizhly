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
        /* ========== GAYA TIDAK BERUBAH ========== */
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
        const surahsData = [ /* ... data sama seperti sebelumnya ... */ ];
        // Lengkapi dengan data surah (sudah ada di kode asli, di sini saya singkat agar jawaban tidak terlalu panjang)
        // Pastikan array surahsData sudah lengkap 114 surah seperti di kode asli.

        let currentPage = 1;
        let isMurojaahMode = true;
        let quranWords = [];
        let currentWordTargetIdx = 0;

        // ========== PERBAIKAN UTAMA: VARIABEL SPEECH ==========
        let recognition = null;
        let isRecording = false;
        let speechBuffer = ''; // Menampung transkrip final
        let silenceTimer = null; // Untuk deteksi hening
        const SILENCE_TIMEOUT = 2000; // 2 detik hening -> proses buffer

        document.addEventListener('DOMContentLoaded', () => {
            renderSurahList();
            checkBookmarkStatus();
            initSwipeGestures();
            initSpeechRecognition();
        });

        // ==================== DASHBOARD & NAVIGASI ====================
        // (fungsi renderSurahList, filterSurah, openRangeModal, dll. TIDAK BERUBAH)
        // Saya sertakan ulang agar lengkap.
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

        // ========== MANUAL TAP ==========
        function manualReveal(index) {
            if (index >= currentWordTargetIdx) {
                for (let i = currentWordTargetIdx; i <= index; i++) {
                    document.getElementById(quranWords[i].id).classList.add('read-correctly');
                    document.getElementById(quranWords[i].id).classList.remove('target-word');
                }
                currentWordTargetIdx = index + 1;
                updateTargetIndicator();
                speechBuffer = ''; // bersihkan buffer agar tidak konflik

                if (currentWordTargetIdx >= quranWords.length) {
                    showToast("Halaman selesai!");
                    setTimeout(() => changePage(1), 1500);
                }
            }
        }

        // ==================== SPEECH RECOGNITION (PERBAIKAN) ====================
        function initSpeechRecognition() {
            window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!window.SpeechRecognition) {
                alert("Browser tidak support Mic. Gunakan Google Chrome.");
                return;
            }

            recognition = new SpeechRecognition();
            recognition.lang = 'ar-SA';
            recognition.continuous = false; // <-- PENTING: false agar tidak numpuk transkrip
            recognition.interimResults = false; // <-- Hanya hasil final yang diproses (mengurangi beban)

            recognition.onresult = (event) => {
                // Ambil hasil final (karena interimResults=false, hanya final)
                const transcript = event.results[0][0].transcript;
                speechBuffer += ' ' + transcript;
                document.getElementById('liveTranscript').innerText = speechBuffer.trim() || '...';

                // Reset timer hening setiap kali menerima suara
                resetSilenceTimer();

                // Proses buffer untuk mencocokkan kata
                processSpeechBuffer();
            };

            recognition.onerror = (e) => {
                console.warn('Speech error:', e.error);
                if (e.error === 'not-allowed') {
                    isRecording = false;
                    renderMicState();
                    showToast("Akses Mikrofon diblokir!");
                }
                // Jika error selain 'no-speech' atau 'aborted', jangan restart otomatis
                if (e.error === 'no-speech' || e.error === 'aborted') {
                    // Jika masih recording, coba restart setelah jeda
                    if (isRecording) setTimeout(startRecognition, 300);
                }
            };

            recognition.onend = () => {
                // Jika masih recording, mulai ulang setelah jeda (tetapi dengan timer hening)
                if (isRecording) {
                    // Jangan langsung start, beri waktu agar tidak cepat mati-nyala
                    setTimeout(() => {
                        if (isRecording) startRecognition();
                    }, 500);
                }
            };
        }

        function startRecognition() {
            if (!recognition || !isRecording) return;
            try {
                recognition.start();
                document.getElementById('liveTranscript').classList.add('active');
            } catch (e) {
                // Jika sudah berjalan, abaikan
                if (e.name === 'InvalidStateError') {
                    // Coba stop lalu start lagi
                    recognition.stop();
                    setTimeout(() => recognition.start(), 100);
                }
            }
        }

        function resetSilenceTimer() {
            clearTimeout(silenceTimer);
            // Jika setelah 2 detik tidak ada suara, buffer di-reset untuk mencegah akumulasi salah
            silenceTimer = setTimeout(() => {
                speechBuffer = '';
                document.getElementById('liveTranscript').innerText = '';
            }, SILENCE_TIMEOUT);
        }

        function processSpeechBuffer() {
            if (currentWordTargetIdx >= quranWords.length) return;

            const spokenClean = normalizeArabicExtreme(speechBuffer);
            let matchFound = false;

            // Coba cocokkan hingga 3 kata ke depan
            for (let i = 0; i < 3; i++) {
                if (currentWordTargetIdx >= quranWords.length) break;

                const targetWordClean = quranWords[currentWordTargetIdx].normalized;
                if (spokenClean.includes(targetWordClean)) {
                    document.getElementById(quranWords[currentWordTargetIdx].id).classList.add('read-correctly');
                    document.getElementById(quranWords[currentWordTargetIdx].id).classList.remove('target-word');
                    currentWordTargetIdx++;
                    matchFound = true;
                } else {
                    break;
                }
            }

            if (matchFound) {
                updateTargetIndicator();
                // Setelah berhasil, bersihkan buffer agar tidak terjadi pengulangan
                speechBuffer = '';
                document.getElementById('liveTranscript').innerText = '...';
                clearTimeout(silenceTimer);
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
                recognition.stop();
                transcriptBox.classList.remove('active');
                clearTimeout(silenceTimer);
            } else {
                isRecording = true;
                speechBuffer = '';
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

        // ========== UTILITAS ==========
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