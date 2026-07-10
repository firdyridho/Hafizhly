<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

// --- AJAX HANDLER: AUTO-SAVE & FINISH ---
if (isset($_POST['action']) && $_POST['action'] == 'autosave_murojaah') {
    $surah = (int)$_POST['surah'];
    $ayat = (int)$_POST['ayat'];
    $is_finish = isset($_POST['is_finish']) ? (int)$_POST['is_finish'] : 0;

    // Cek apakah hari ini sudah ada catatan murojaah untuk surah ini
    $cek = mysqli_query($conn, "SELECT id FROM mutabaah WHERE user_id='$user_id' AND activity_type='murojaah' AND surah='$surah' AND activity_date=CURDATE()");

    $notes = $is_finish ? "Selesai murojaah via Smart AI" : "Murojaah tertunda (Auto-saved)";

    if (mysqli_num_rows($cek) > 0) {
        // Update ayat terakhir jika sudah ada row hari ini
        mysqli_query($conn, "UPDATE mutabaah SET ayah_end='$ayat', notes='$notes' WHERE user_id='$user_id' AND activity_type='murojaah' AND surah='$surah' AND activity_date=CURDATE()");
    } else {
        // Insert baru jika belum ada
        mysqli_query($conn, "INSERT INTO mutabaah (user_id, activity_type, activity_date, activity_time, surah, ayah_start, ayah_end, notes) 
                  VALUES ('$user_id', 'murojaah', CURDATE(), CURTIME(), '$surah', '1', '$ayat', '$notes')");
    }
    echo "saved";
    exit();
}

// Mengambil data Terakhir Murojaah untuk prefill
$q_last = mysqli_query($conn, "SELECT surah, ayah_end FROM mutabaah WHERE user_id = '$user_id' AND activity_type='murojaah' ORDER BY id DESC LIMIT 1");
$last_murojaah = mysqli_fetch_assoc($q_last);
$last_surah_no = $last_murojaah ? $last_murojaah['surah'] : 1;
$last_ayat_no = $last_murojaah ? $last_murojaah['ayah_end'] : 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Murojaah Pro - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #059669;
            --error: #ef4444;
            --dark: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --quran-text: #111827;
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

        .header {
            background: var(--card-bg);
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .back-btn {
            color: var(--text-muted);
            font-size: 1.2rem;
            text-decoration: none;
            transition: 0.2s;
        }

        .back-btn:hover {
            color: var(--primary);
        }

        .header-title {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.1rem;
            flex-grow: 1;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* UI SETUP */
        #setup-screen {
            display: block;
        }

        .last-murojaah-card {
            background: linear-gradient(135deg, var(--primary), #10b981);
            color: white;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.2);
        }

        .lmc-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .lmc-title {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border-radius: 16px;
            border: 1px solid var(--border);
            font-size: 1rem;
            outline: none;
            transition: 0.3s;
        }

        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .search-box i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .surah-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .s-card {
            background: var(--card-bg);
            padding: 15px 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .s-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .s-name {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .s-ar {
            font-family: 'Scheherazade New', serif;
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: bold;
        }

        /* MUSHAF INTERFACE (Page Mode) */
        #murojaah-interface {
            display: none;
            padding-bottom: 120px;
        }

        .mushaf-info-bar {
            background: var(--card-bg);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 55px;
            z-index: 99;
        }

        /* Layout Halaman Mushaf */
        .mushaf-page-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fffdf5;
            /* Warna kertas Mushaf */
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e0d8;
            min-height: 60vh;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Frame Ornamen Mushaf */
        .mushaf-page-container::before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2px solid #d4cfc0;
            pointer-events: none;
        }

        /* Alignment Kanan Kiri (Spine Buku) */
        .page-right {
            border-left: 12px solid #cbd5e1 !important;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .page-left {
            border-right: 12px solid #cbd5e1 !important;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .ayat-flow {
            direction: rtl;
            text-align: justify;
            line-height: 2.8;
            font-family: 'Scheherazade New', serif;
            font-size: 2.5rem;
            width: 100%;
            position: relative;
            z-index: 2;
        }

        /* Styling Kata (Hit to Reveal) */
        .word {
            display: inline-block;
            transition: color 0.1s, text-shadow 0.1s;
            position: relative;
            margin: 0 4px;
            color: transparent;
            text-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
            user-select: none;
            cursor: pointer;
        }

        .word.active {
            border-bottom: 3px solid #f59e0b;
            padding-bottom: 4px;
        }

        /* Efek Berhasil (Hijau kedip lalu hitam pekat) */
        .word.revealed {
            color: #111827;
            text-shadow: none;
            font-weight: bold;
        }

        .word.correct-flash {
            color: var(--primary) !important;
            text-shadow: 0 0 10px rgba(5, 150, 105, 0.5) !important;
            transform: scale(1.1);
            transition: 0.2s;
        }

        /* Efek Gagal (Merah Goyang) */
        .word.error-flash {
            color: var(--error) !important;
            text-shadow: none !important;
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

        /* Fitur Intip (Mata) */
        .ayat-flow.peek-mode .word {
            color: rgba(17, 24, 39, 0.3);
            text-shadow: none;
        }

        .ayat-flow.peek-mode .word.revealed {
            color: #111827;
        }

        .ayat-end-badge {
            color: var(--primary);
            font-size: 1.5rem;
            margin: 0 10px;
            font-weight: normal;
        }

        .bismillah {
            text-align: center;
            font-size: 2.2rem;
            color: #111827;
            margin-bottom: 20px;
            width: 100%;
        }

        /* CONTROLS */
        .controls-bottom {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 25px;
            align-items: center;
            z-index: 200;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px 25px;
            border-radius: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .ctrl-btn {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.3rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-eye {
            background: var(--bg);
            color: var(--dark);
            border: 1px solid var(--border);
        }

        .btn-eye:active {
            background: #e2e8f0;
        }

        .btn-mic {
            background: var(--primary);
            color: white;
            width: 70px;
            height: 70px;
            font-size: 1.8rem;
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.3);
        }

        .btn-mic.listening {
            background: var(--error);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse 1.5s infinite;
        }

        .btn-finish {
            background: var(--dark);
            color: white;
            width: auto;
            padding: 0 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.95rem;
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

        #loading-ui {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
            display: none;
        }
    </style>
</head>

<body>

    <div class="header" id="main-header">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div class="header-title">Smart Murojaah AI</div>
    </div>

    <!-- UI SETUP -->
    <div id="setup-screen" class="container">
        <!-- Card Terakhir Murojaah -->
        <?php if ($last_murojaah): ?>
            <div class="last-murojaah-card">
                <div class="lmc-label"><i class="fas fa-history"></i> Terakhir Murojaah</div>
                <div class="lmc-title">Surah ke-<?= $last_surah_no ?> (Ayat <?= $last_ayat_no ?>)</div>
            </div>
        <?php endif; ?>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Pilih Surah untuk dimurojaah..." autocomplete="off">
        </div>

        <div id="loading-surah"><i class="fas fa-spinner fa-spin"></i> Memuat daftar surah...</div>
        <div class="surah-list" id="surahList"></div>
    </div>

    <!-- UI MUSHAF & AI -->
    <div id="murojaah-interface">
        <div class="mushaf-info-bar">
            <span id="info-juz">Juz -</span>
            <span id="info-surah" style="color: var(--primary);">Surah</span>
            <span id="info-page">Hal -</span>
        </div>

        <div id="loading-ui">
            <i class="fas fa-spinner fa-spin" style="font-size:2rem; color:var(--primary); margin-bottom:10px;"></i><br>
            Menyusun Mushaf...
        </div>

        <div class="mushaf-page-container page-right" id="mushaf-container" style="display:none;">
            <div class="ayat-flow" id="ayat-render-area">
                <!-- Ayat akan digenerate ke sini -->
            </div>
        </div>

        <div class="controls-bottom">
            <button class="ctrl-btn btn-eye" id="btnPeek" title="Intip Halaman (Tahan)">
                <i class="fas fa-eye"></i>
            </button>
            <button class="ctrl-btn btn-mic" id="micBtn" onclick="toggleMic()">
                <i class="fas fa-microphone"></i>
            </button>
            <button class="ctrl-btn btn-finish" onclick="finishSession()">
                <i class="fas fa-check"></i> Selesai
            </button>
        </div>
    </div>

    <script>
        // --- PREFILL DATA ---
        const prefLastSurah = <?= $last_surah_no ?>;
        const prefLastAyat = <?= $last_ayat_no ?>;

        let allSurat = [];
        let mergedMushafData = [];
        let currentWordIndex = 0;
        let globalWordsArray = [];
        let renderedPageNo = 0;
        let selectedSurahNo = 0;
        let selectedStartAyat = 1;

        let recognition;
        let isListening = false;

        // Inisialisasi API Speech
        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.lang = 'ar-SA';
            recognition.continuous = true;
            recognition.interimResults = true;
        } else {
            Swal.fire('Browser Tidak Mendukung', 'Harap gunakan Google Chrome untuk fitur AI Suara.', 'error');
        }

        // 1. Fetch Daftar Surah
        async function fetchSuratList() {
            try {
                const res = await fetch('https://equran.id/api/v2/surat');
                const json = await res.json();
                allSurat = json.data;
                document.getElementById('loading-surah').style.display = 'none';
                renderSurahList(allSurat);
            } catch (e) {
                document.getElementById('loading-surah').innerHTML = "Gagal memuat.";
            }
        }

        function renderSurahList(data) {
            const container = document.getElementById('surahList');
            container.innerHTML = '';
            data.forEach(s => {
                const card = document.createElement('div');
                card.className = 's-card';
                card.onclick = () => promptStartAyat(s.nomor, s.namaLatin, s.jumlahAyat);
                card.innerHTML = `
                    <div><div class="s-name">${s.nomor}. ${s.namaLatin}</div><div style="font-size:0.8rem; color:#64748b;">${s.jumlahAyat} Ayat</div></div>
                    <div class="s-ar">${s.nama}</div>
                `;
                container.appendChild(card);
            });
        }

        document.getElementById('searchInput').addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase();
            renderSurahList(allSurat.filter(s => s.namaLatin.toLowerCase().includes(q)));
        });

        // 2. SweetAlert2 Prompt Mulai Ayat
        function promptStartAyat(surahNo, surahName, maxAyat) {
            let defAyat = (surahNo === prefLastSurah) ? prefLastAyat : 1;

            Swal.fire({
                title: `Murojaah ${surahName}`,
                text: `Mulai dari ayat berapa? (1 - ${maxAyat})`,
                input: 'number',
                inputValue: defAyat,
                showCancelButton: true,
                confirmButtonText: 'Mulai',
                confirmButtonColor: '#059669',
                inputAttributes: {
                    min: 1,
                    max: maxAyat
                },
                inputValidator: (value) => {
                    if (!value || value < 1 || value > maxAyat) {
                        return 'Nomor ayat tidak valid!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    startMurojaahEngine(surahNo, parseInt(result.value), surahName);
                }
            });
        }

        // 3. ENGINE: Tarik & Gabung API (equran.id + alquran.cloud)
        async function startMurojaahEngine(surahNo, startAyat, surahName) {
            selectedSurahNo = surahNo;
            selectedStartAyat = startAyat;

            document.getElementById('setup-screen').style.display = 'none';
            document.getElementById('main-header').style.display = 'none';
            document.getElementById('murojaah-interface').style.display = 'block';
            document.getElementById('loading-ui').style.display = 'block';

            try {
                // Fetch Paralel
                const [resId, resCloud] = await Promise.all([
                    fetch(`https://equran.id/api/v2/surat/${surahNo}`),
                    fetch(`https://api.alquran.cloud/v1/surah/${surahNo}`)
                ]);

                const jsonId = await resId.json();
                const jsonCloud = await resCloud.json();

                const ayatId = jsonId.data.ayat;
                const ayatCloud = jsonCloud.data.ayahs;

                mergedMushafData = [];
                // Filter mulai dari startAyat
                for (let i = startAyat - 1; i < ayatId.length; i++) {
                    mergedMushafData.push({
                        nomorAyat: ayatId[i].nomorAyat,
                        teksArab: ayatId[i].teksArab,
                        page: ayatCloud[i].page,
                        juz: ayatCloud[i].juz
                    });
                }

                document.getElementById('info-surah').innerText = surahName;
                document.getElementById('loading-ui').style.display = 'none';

                tokenizeWords();
                renderPage(mergedMushafData[0].page); // Render halaman pertama dari data

            } catch (e) {
                Swal.fire('Error', 'Gagal menyusun Mushaf. Cek koneksi.', 'error')
                    .then(() => location.reload());
            }
        }

        // 4. Tokenisasi Kata & Perbaikan Bug Waqaf
        function tokenizeWords() {
            globalWordsArray = [];
            let wIdx = 0;

            // Regex pendeteksi waqaf dan simbol ornamen
            const waqafRegex = /^[\u06D6-\u06ED۝۞۩]+$/;

            mergedMushafData.forEach((ayat, aIndex) => {
                let text = ayat.teksArab;
                if (ayat.nomorAyat === 1 && selectedSurahNo !== 1 && text.includes("بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ")) {
                    text = text.replace("بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ", "").trim();
                }

                // Pecah by spasi
                let rawWords = text.split(/\s+/).filter(w => w !== '');
                let finalAyatWords = [];

                rawWords.forEach(w => {
                    // Jika kata ini HANYA berisi waqaf, gabung ke kata sebelumnya di UI
                    if (waqafRegex.test(w)) {
                        if (finalAyatWords.length > 0) {
                            finalAyatWords[finalAyatWords.length - 1].display += ' ' + w;
                        }
                    } else {
                        // Kata normal
                        finalAyatWords.push({
                            display: w,
                            targetSpeak: normalizeArabic(w),
                            ayatNo: ayat.nomorAyat,
                            page: ayat.page,
                            juz: ayat.juz,
                            globalIdx: wIdx++
                        });
                    }
                });

                globalWordsArray = globalWordsArray.concat(finalAyatWords);
            });
        }

        function normalizeArabic(text) {
            return text.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED\u06DF-\u06E8]/g, '')
                .replace(/[أإآءئؤ]/g, 'ا').replace(/ة/g, 'ه').replace(/ى/g, 'ي')
                .replace(/[^ا-ي]/g, '').trim();
        }

        // 5. Render Per Halaman (Mushaf Page)
        function renderPage(pageNo) {
            renderedPageNo = pageNo;
            const container = document.getElementById('mushaf-container');
            const area = document.getElementById('ayat-render-area');
            area.innerHTML = '';

            // Set Alignment (Ganjil Kanan, Genap Kiri)
            if (pageNo % 2 !== 0) {
                container.className = 'mushaf-page-container page-right';
            } else {
                container.className = 'mushaf-page-container page-left';
            }
            container.style.display = 'flex';

            // Update Info Bar
            let firstWordInPage = globalWordsArray.find(w => w.page === pageNo);
            if (firstWordInPage) {
                document.getElementById('info-page').innerText = `Hal ${pageNo}`;
                document.getElementById('info-juz').innerText = `Juz ${firstWordInPage.juz}`;
            }

            // Cek apakah halaman ini punya ayat 1 (Tampilkan Bismillah)
            let hasAyat1 = mergedMushafData.some(a => a.page === pageNo && a.nomorAyat === 1);
            if (hasAyat1 && selectedSurahNo !== 1 && selectedSurahNo !== 9) {
                area.innerHTML += `<div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>`;
            }

            let currentAyatTracker = -1;

            globalWordsArray.filter(w => w.page === pageNo).forEach(w => {
                if (currentAyatTracker !== -1 && currentAyatTracker !== w.ayatNo) {
                    area.innerHTML += `<span class="ayat-end-badge"> ۝ </span>`;
                }
                currentAyatTracker = w.ayatNo;

                const span = document.createElement('span');
                span.className = 'word';
                span.id = `word-${w.globalIdx}`;
                span.innerText = w.display;

                // Kata yang sudah dilewati tetap hitam jika user buka page baru (scroll back scenario, walau di app ini maju terus)
                if (w.globalIdx < currentWordIndex) {
                    span.classList.add('revealed');
                }

                // Tap to Skip
                span.onclick = () => {
                    if (w.globalIdx === currentWordIndex) processSuccess();
                };

                area.appendChild(span);
            });
            area.innerHTML += `<span class="ayat-end-badge"> ۝ </span>`;

            updateHighlight();
        }

        function updateHighlight() {
            document.querySelectorAll('.word').forEach(el => el.classList.remove('active'));
            const currentEl = document.getElementById(`word-${currentWordIndex}`);

            if (currentEl) {
                currentEl.classList.add('active');
            } else {
                // Jika element tidak ada di page ini, berarti pindah page!
                const nextWord = globalWordsArray[currentWordIndex];
                if (nextWord && nextWord.page !== renderedPageNo) {
                    renderPage(nextWord.page);
                }
            }
        }

        // 6. Logika Benar/Salah (Green Flash & Red Shake)
        function processSuccess(skipCount = 0) {
            // Jika ada skip (karena delay match), reveal kata-kata yang terlewati
            for (let i = 0; i <= skipCount; i++) {
                let idx = currentWordIndex + i;
                let el = document.getElementById(`word-${idx}`);
                if (el) {
                    el.classList.remove('active');
                    el.classList.add('correct-flash', 'revealed');

                    // Hilangkan ijo setelah 400ms jadi hitam pekat (revealed)
                    setTimeout(() => el.classList.remove('correct-flash'), 400);
                }
            }

            currentWordIndex += (skipCount + 1);

            // Auto-Save tiap ganti ayat
            let prevWord = globalWordsArray[currentWordIndex - 1];
            let nextWord = globalWordsArray[currentWordIndex];
            if (nextWord && prevWord.ayatNo !== nextWord.ayatNo) {
                autoSave(prevWord.ayatNo);
            }

            if (currentWordIndex >= globalWordsArray.length) {
                // Selesai semua
                toggleMic(false);
                autoSave(prevWord.ayatNo, 1).then(() => {
                    Swal.fire('Alhamdulillah!', 'Murojaah selesai disimpan.', 'success').then(() => window.location.href = 'dashboard.php');
                });
            } else {
                updateHighlight();
            }
        }

        function processError() {
            const el = document.getElementById(`word-${currentWordIndex}`);
            if (el) {
                el.classList.add('error-flash');
                if (navigator.vibrate) navigator.vibrate([100, 50, 100]); // Getar 2 kali cepat
                setTimeout(() => el.classList.remove('error-flash'), 400);
            }
        }

        // 7. SPEECH RECOGNITION (Fuzzy Match + 2 Words Lookahead Window)
        if (recognition) {
            recognition.onresult = function(event) {
                let transcript = '';
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    transcript += event.results[i][0].transcript;
                }
                if (transcript.trim() === '') return;

                let spokenWords = transcript.split(' ').map(w => normalizeArabic(w)).filter(w => w !== '');

                // Ambil 3 target kata (Current, Current+1, Current+2)
                let target0 = globalWordsArray[currentWordIndex];
                let target1 = globalWordsArray[currentWordIndex + 1];
                let target2 = globalWordsArray[currentWordIndex + 2];

                let matchFound = false;
                let skipCount = 0;

                // Cek apakah suara user mengandung kata target
                spokenWords.forEach(sw => {
                    if (matchFound) return;
                    if (target0 && (sw === target0.targetSpeak || sw.includes(target0.targetSpeak) || target0.targetSpeak.includes(sw))) {
                        matchFound = true;
                        skipCount = 0;
                    } else if (target1 && (sw === target1.targetSpeak || sw.includes(target1.targetSpeak) || target1.targetSpeak.includes(sw))) {
                        matchFound = true;
                        skipCount = 1;
                    } else if (target2 && (sw === target2.targetSpeak || sw.includes(target2.targetSpeak) || target2.targetSpeak.includes(sw))) {
                        matchFound = true;
                        skipCount = 2;
                    }
                });

                if (matchFound) {
                    processSuccess(skipCount);
                } else {
                    // Jika user terus ngomong tapi gak match sama sekali (salah baca)
                    if (spokenWords.length > 1) processError();
                }
            };

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

        // 8. Fitur Intip Mata (Peek Mode)
        const btnPeek = document.getElementById('btnPeek');
        const area = document.getElementById('ayat-render-area');

        btnPeek.addEventListener('mousedown', () => area.classList.add('peek-mode'));
        btnPeek.addEventListener('mouseup', () => area.classList.remove('peek-mode'));
        btnPeek.addEventListener('mouseleave', () => area.classList.remove('peek-mode'));
        btnPeek.addEventListener('touchstart', (e) => {
            e.preventDefault();
            area.classList.add('peek-mode');
        });
        btnPeek.addEventListener('touchend', (e) => {
            e.preventDefault();
            area.classList.remove('peek-mode');
        });

        // 9. Auto-Save Action
        async function autoSave(ayatNo, isFinish = 0) {
            const fd = new URLSearchParams();
            fd.append('action', 'autosave_murojaah');
            fd.append('surah', selectedSurahNo);
            fd.append('ayat', ayatNo);
            fd.append('is_finish', isFinish);
            return fetch('smart_murojaah.php', {
                method: 'POST',
                body: fd
            });
        }

        function finishSession() {
            toggleMic(false);
            Swal.fire({
                title: 'Selesai Murojaah?',
                text: "Progresmu akan disimpan hingga ayat terakhir yang dibaca.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                confirmButtonText: 'Ya, Selesai'
            }).then((result) => {
                if (result.isConfirmed) {
                    let lastW = globalWordsArray[currentWordIndex - 1];
                    let ayatToSave = lastW ? lastW.ayatNo : selectedStartAyat;
                    autoSave(ayatToSave, 1).then(() => {
                        window.location.href = 'dashboard.php';
                    });
                }
            });
        }

        fetchSuratList();
    </script>
</body>

</html>