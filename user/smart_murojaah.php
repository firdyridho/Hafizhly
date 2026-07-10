<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

// --- AJAX: SIMPAN PROGRES AKHIR ---
if (isset($_POST['action']) && $_POST['action'] == 'finish_murojaah') {
    $surah = (int)$_POST['surah'];
    $last_ayat = (int)$_POST['last_ayat'];
    // Simpan ke mutabaah otomatis sebagai aktivitas murojaah
    $query = "INSERT INTO mutabaah (user_id, activity_type, activity_date, activity_time, surah, ayah_start, ayah_end, notes) 
              VALUES ('$user_id', 'murojaah', CURDATE(), CURTIME(), '$surah', '1', '$last_ayat', 'Selesai murojaah via Smart AI')";
    mysqli_query($conn, $query);
    echo "saved";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Murojaah Pro - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --error: #ef4444;
            --dark: #1e293b;
            --bg: #f0f2f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            overflow-x: hidden;
        }

        /* UI SETUP */
        .setup-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            font-size: 1rem;
            outline: none;
        }

        .btn-start {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-start:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.3);
        }

        /* MUSHAF INTERFACE */
        #murojaah-interface {
            display: none;
        }

        .mushaf-header {
            background: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .mushaf-page {
            max-width: 800px;
            margin: 30px auto;
            background: #fffcf0;
            /* Warna kertas Mushaf */
            padding: 50px 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            min-height: 80vh;
            position: relative;
            border: 1px solid #e5e0d0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Frame Mushaf Ornament */
        .mushaf-page::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2px solid #d4cfc0;
            pointer-events: none;
        }

        .ayat-flow {
            direction: rtl;
            text-align: justify;
            line-height: 2.8;
            font-family: 'Scheherazade New', serif;
            font-size: 2.4rem;
        }

        /* KATA ENGINE */
        .word {
            display: inline-block;
            transition: 0.4s;
            position: relative;
            margin: 0 4px;
            color: transparent;
            text-shadow: 0 0 12px rgba(0, 0, 0, 0.15);
            /* Hidden state */
            cursor: pointer;
        }

        .word.active {
            border-bottom: 3px solid #f59e0b;
            text-shadow: 0 0 12px rgba(245, 158, 11, 0.4);
        }

        .word.revealed {
            color: #111827;
            text-shadow: none;
        }

        .word.correct-flash {
            color: #059669 !important;
            font-weight: bold;
            transform: scale(1.2);
        }

        .word.error-flash {
            color: var(--error) !important;
            text-shadow: none;
            animation: shake 0.3s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        /* CONTROLS */
        .controls-bottom {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
            align-items: center;
            z-index: 200;
        }

        .ctrl-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
        }

        .btn-eye {
            background: white;
            color: var(--dark);
        }

        .btn-mic {
            background: var(--primary);
            color: white;
            width: 75px;
            height: 75px;
            font-size: 2rem;
        }

        .btn-mic.listening {
            background: var(--error);
            animation: pulse 1.5s infinite;
        }

        .btn-finish {
            background: #1e293b;
            color: white;
            width: auto;
            padding: 0 25px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* TOOLTIP MATA */
        .mushaf-page.peek .word {
            color: rgba(0, 0, 0, 0.3) !important;
            text-shadow: none !important;
        }

        .mushaf-page.peek .word.revealed {
            color: #111827 !important;
        }

        /* SEARCH DROPDOWN (Reuse from mutabaah) */
        #surah-list-dropdown {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            border-radius: 10px;
            display: none;
            z-index: 50;
        }

        .dropdown-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .dropdown-item:hover {
            background: #f0fdf4;
        }
    </style>
</head>

<body>

    <div id="setup-screen" class="container">
        <div class="setup-container">
            <h2 style="margin-bottom: 25px; color: var(--primary);"><i class="fas fa- Daly"></i> Murojaah Pro</h2>

            <div class="form-group" style="position:relative;">
                <label>Pilih Surah</label>
                <input type="text" id="surahSearch" class="form-control" placeholder="Cari Surah...">
                <input type="hidden" id="selectedSurah">
                <div id="surah-list-dropdown"></div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex:1;">
                    <label>Dari Ayat</label>
                    <input type="number" id="startAyat" class="form-control" value="1" min="1">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Sampai Ayat</label>
                    <input type="number" id="endAyat" class="form-control" placeholder="Max">
                </div>
            </div>

            <button class="btn-start" onclick="initMurojaah()">Mulai Setoran Suara</button>
        </div>
    </div>

    <div id="murojaah-interface">
        <div class="mushaf-header">
            <a href="smart_murojaah.php" style="color: var(--dark);"><i class="fas fa-times"></i></a>
            <div id="header-info" style="font-weight: 700; color: var(--primary);">Al-Baqarah: 1-10</div>
            <div id="percentage" style="font-size: 0.8rem; font-weight: 700;">0%</div>
        </div>

        <div class="mushaf-page" id="mushaf-container">
            <div class="ayat-flow" id="ayat-render-area">
            </div>
        </div>

        <div class="controls-bottom">
            <button class="ctrl-btn btn-eye" id="btnPeek" title="Intip Halaman">
                <i class="fas fa-eye"></i>
            </button>

            <button class="ctrl-btn btn-mic" id="micBtn" onclick="toggleMic()">
                <i class="fas fa-microphone"></i>
            </button>

            <button class="ctrl-btn btn-finish" onclick="finishMurojaah()">
                Selesai
            </button>
        </div>
    </div>

    <script>
        // --- LOGIKA SETUP ---
        let allSurat = [];
        let recognition;
        let isListening = false;
        let versesData = [];
        let currentWordIndex = 0;
        let wordsArray = []; // List all words in the selected range

        const surahSearch = document.getElementById('surahSearch');
        const dropdown = document.getElementById('surah-list-dropdown');

        async function fetchSurat() {
            const res = await fetch('https://equran.id/api/v2/surat');
            const json = await res.json();
            allSurat = json.data;
        }

        surahSearch.addEventListener('input', (e) => {
            const val = e.target.value.toLowerCase();
            const filtered = allSurat.filter(s => s.namaLatin.toLowerCase().includes(val));
            dropdown.innerHTML = filtered.map(s => `
                <div class="dropdown-item" onclick="selectSurah(${s.nomor}, '${s.namaLatin}', ${s.jumlahAyat})">
                    <span>${s.nomor}. ${s.namaLatin}</span>
                    <span style="color:var(--primary)">${s.jumlahAyat} Ayat</span>
                </div>
            `).join('');
            dropdown.style.display = 'block';
        });

        function selectSurah(no, nama, max) {
            document.getElementById('selectedSurah').value = no;
            surahSearch.value = nama;
            document.getElementById('endAyat').value = max;
            document.getElementById('endAyat').max = max;
            dropdown.style.display = 'none';
        }

        // --- ENGINE MUROJAAH ---
        async function initMurojaah() {
            const surahNo = document.getElementById('selectedSurah').value;
            const start = parseInt(document.getElementById('startAyat').value);
            const end = parseInt(document.getElementById('endAyat').value);

            if (!surahNo) return alert("Pilih surah dulu!");

            const res = await fetch(`https://equran.id/api/v2/surat/${surahNo}`);
            const json = await res.json();

            // Filter ayat sesuai range
            versesData = json.data.ayat.filter(a => a.nomorAyat >= start && a.nomorAyat <= end);

            document.getElementById('setup-screen').style.display = 'none';
            document.getElementById('murojaah-interface').style.display = 'block';
            document.getElementById('header-info').innerText = `${json.data.namaLatin}: ${start}-${end}`;

            renderMushaf();
        }

        function normalizeArabic(text) {
            return text.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED\u06DF-\u06E8]/g, '') // Hapus Harakat & WAQAF!
                .replace(/[أإآءئؤ]/g, 'ا').replace(/ة/g, 'ه').replace(/ى/g, 'ي')
                .replace(/[^ا-ي]/g, '').trim();
        }

        function renderMushaf() {
            const area = document.getElementById('ayat-render-area');
            area.innerHTML = '';
            wordsArray = [];

            versesData.forEach((ayat, aIdx) => {
                // Bersihkan bismillah jika ada di ayat 1 (kecuali Alfatihah)
                let cleanText = ayat.teksArab;
                if (ayat.nomorAyat === 1 && cleanText.includes("بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ")) {
                    cleanText = cleanText.replace("بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ", "");
                }

                const words = cleanText.split(' ').filter(w => w.trim() !== '');

                words.forEach((w, wIdx) => {
                    const wordObj = {
                        original: w,
                        normal: normalizeArabic(w),
                        ayat: ayat.nomorAyat,
                        globalIndex: wordsArray.length
                    };
                    wordsArray.push(wordObj);

                    const span = document.createElement('span');
                    span.className = 'word';
                    span.id = `word-${wordObj.globalIndex}`;
                    span.innerText = w;
                    span.onclick = () => revealManual(wordObj.globalIndex);
                    area.appendChild(span);
                });

                // Tambahkan Nomor Ayat di akhir flow ayat
                const badge = document.createElement('span');
                badge.style.cssText = "color: var(--primary); font-size: 1.2rem; margin: 0 10px; font-family: Inter;";
                badge.innerText = ` (${ayat.nomorAyat}) `;
                area.appendChild(badge);
            });

            currentWordIndex = 0;
            updateHighlight();
        }

        function updateHighlight() {
            document.querySelectorAll('.word').forEach(el => el.classList.remove('active'));
            const currentEl = document.getElementById(`word-${currentWordIndex}`);
            if (currentEl) currentEl.classList.add('active');

            // Progres
            const pct = Math.round((currentWordIndex / wordsArray.length) * 100);
            document.getElementById('percentage').innerText = pct + '%';
        }

        function revealManual(idx) {
            if (idx === currentWordIndex) processSuccess();
        }

        function processSuccess() {
            const el = document.getElementById(`word-${currentWordIndex}`);
            el.classList.add('revealed', 'correct-flash');

            setTimeout(() => {
                el.classList.remove('correct-flash');
                currentWordIndex++;
                if (currentWordIndex >= wordsArray.length) {
                    toggleMic(false);
                    alert("Alhamdulillah! Hafalan selesai.");
                }
                updateHighlight();
            }, 400);
        }

        function processError() {
            const el = document.getElementById(`word-${currentWordIndex}`);
            el.classList.add('error-flash');
            if (navigator.vibrate) navigator.vibrate(200); // Getar HP

            setTimeout(() => {
                el.classList.remove('error-flash');
            }, 500);
        }

        // --- SPEECH RECOGNITION ---
        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.lang = 'ar-SA';
            recognition.continuous = true;
            recognition.interimResults = true;

            recognition.onresult = (event) => {
                let transcript = '';
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    transcript += event.results[i][0].transcript;
                }

                const spokenWords = transcript.split(' ');
                const target = wordsArray[currentWordIndex];
                if (!target) return;

                const isMatch = spokenWords.some(w => {
                    const normSpoken = normalizeArabic(w);
                    return normSpoken === target.normal || target.normal.includes(normSpoken);
                });

                if (isMatch) {
                    processSuccess();
                }
            };

            recognition.onerror = () => processError();
            recognition.onend = () => {
                if (isListening) recognition.start();
            };
        }

        function toggleMic(force) {
            isListening = (force !== undefined) ? force : !isListening;
            const btn = document.getElementById('micBtn');
            if (isListening) {
                recognition.start();
                btn.classList.add('listening');
            } else {
                recognition.stop();
                btn.classList.remove('listening');
            }
        }

        // Fitur Intip
        const btnPeek = document.getElementById('btnPeek');
        btnPeek.addEventListener('mousedown', () => document.getElementById('mushaf-container').classList.add('peek'));
        btnPeek.addEventListener('mouseup', () => document.getElementById('mushaf-container').classList.remove('peek'));
        btnPeek.addEventListener('touchstart', () => document.getElementById('mushaf-container').classList.add('peek'));
        btnPeek.addEventListener('touchend', () => document.getElementById('mushaf-container').classList.remove('peek'));

        function finishMurojaah() {
            const surah = document.getElementById('selectedSurah').value;
            const last = wordsArray[currentWordIndex] ? wordsArray[currentWordIndex].ayat : versesData[versesData.length - 1].nomorAyat;

            const fd = new URLSearchParams();
            fd.append('action', 'finish_murojaah');
            fd.append('surah', surah);
            fd.append('last_ayat', last);

            fetch('smart_murojaah.php', {
                    method: 'POST',
                    body: fd
                })
                .then(() => window.location.href = 'dashboard.php');
        }

        fetchSurat();
    </script>
</body>

</html>