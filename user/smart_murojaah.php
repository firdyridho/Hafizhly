<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

// --- AJAX HANDLER UNTUK SRS ---
if (isset($_POST['action']) && $_POST['action'] == 'save_srs') {
    $surah = (int)$_POST['surah'];
    $grade = (int)$_POST['grade']; // 1=Sulit(Ulang Besok), 2=Bagus(3 Hari), 3=Mudah(7 Hari)

    $interval = 1;
    if ($grade == 2) $interval = 3;
    if ($grade == 3) $interval = 7;

    $next_review = date('Y-m-d', strtotime("+$interval days"));

    $cek = mysqli_query($conn, "SELECT id FROM murojaah_srs WHERE user_id='$user_id' AND surah_nomor='$surah'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE murojaah_srs SET interval_hari='$interval', next_review='$next_review', last_reviewed=NOW() WHERE user_id='$user_id' AND surah_nomor='$surah'");
    } else {
        mysqli_query($conn, "INSERT INTO murojaah_srs (user_id, surah_nomor, interval_hari, next_review) VALUES ('$user_id', '$surah', '$interval', '$next_review')");
    }
    echo "saved";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Murojaah AI - Hifzly</title>
    <!-- Font Arab -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-light: #d1fae5;
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
            padding-bottom: 90px;
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
        }

        .header-title {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* UI Pilih Surah */
        #setup-screen {
            display: block;
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
            gap: 10px;
        }

        .s-card {
            background: var(--card-bg);
            padding: 15px;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .s-card:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .s-name {
            font-weight: 600;
            color: var(--dark);
        }

        .s-ar {
            font-family: 'Scheherazade New', serif;
            font-size: 1.5rem;
            color: var(--primary);
        }

        /* UI Tarteel Mode (Session) */
        #session-screen {
            display: none;
            text-align: center;
        }

        .session-header {
            margin-bottom: 20px;
        }

        .sh-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Scheherazade New', serif;
        }

        /* Hint Skip */
        .skip-hint {
            background: #fef3c7;
            color: #d97706;
            padding: 10px 15px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: inline-block;
            font-weight: 500;
            border: 1px dashed #f59e0b;
        }

        /* Kotak Ayat */
        .ayat-display {
            background: var(--card-bg);
            padding: 40px 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 30px;
            border: 2px solid var(--primary-light);
        }

        .ayat-text {
            font-family: 'Scheherazade New', serif;
            font-size: 2.8rem;
            line-height: 2;
            direction: rtl;
            color: var(--quran-text);
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        /* Kata per Kata */
        .word {
            color: transparent;
            text-shadow: 0 0 15px rgba(17, 24, 39, 0.4);
            transition: 0.3s ease;
            position: relative;
            user-select: none;
            cursor: pointer;
            /* Kursor pointer agar tau bisa diklik */
        }

        .word.revealed {
            color: var(--primary);
            text-shadow: none;
            font-weight: bold;
            cursor: default;
        }

        .word.active-listen {
            border-bottom: 3px solid #f59e0b;
            padding-bottom: 5px;
        }

        /* Mic Button Animasi */
        .mic-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            flex-direction: column;
            gap: 15px;
        }

        .mic-btn {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            font-size: 2rem;
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4);
            transition: 0.3s;
            position: relative;
        }

        .mic-btn.listening {
            background: #ef4444;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 1.5s infinite;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 20px rgba(239, 68, 68, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .status-text {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* UI Evaluasi SRS */
        #srs-screen {
            display: none;
            text-align: center;
            margin-top: 50px;
        }

        .srs-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .srs-subtitle {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .srs-options {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .srs-btn {
            padding: 15px 25px;
            border-radius: 16px;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            flex: 1;
            min-width: 120px;
            transition: 0.2s;
            color: white;
        }

        .btn-hard {
            background: #ef4444;
        }

        .btn-hard:hover {
            background: #dc2626;
        }

        .btn-good {
            background: #f59e0b;
        }

        .btn-good:hover {
            background: #d97706;
        }

        .btn-easy {
            background: var(--primary);
        }

        .btn-easy:hover {
            background: #047857;
        }
    </style>
</head>

<body>

    <div class="header">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div class="header-title">Smart Murojaah AI <i class="fas fa-microphone-alt"></i></div>
    </div>

    <div class="container">
        <!-- 1. Layar Pilih Surah -->
        <div id="setup-screen">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Pilih Surah untuk disetor..." autocomplete="off">
            </div>
            <div id="loading"><i class="fas fa-spinner fa-spin"></i> Menyiapkan daftar surah...</div>
            <div class="surah-list" id="surahList"></div>
        </div>

        <!-- 2. Layar Setoran AI -->
        <div id="session-screen">
            <div class="session-header">
                <div class="sh-title" id="ses-surah-ar">...</div>
                <div style="font-size:0.9rem; color:var(--text-muted);" id="ses-surah-info">...</div>
            </div>

            <!-- Petunjuk baru agar user tidak frustrasi -->
            <div class="skip-hint">
                <i class="fas fa-lightbulb"></i> <strong>Tips:</strong> Jika AI nyangkut, sentuh kata yang bergaris kuning untuk melewatinya.
            </div>

            <div class="ayat-display">
                <div style="font-weight:700; color:var(--primary); margin-bottom:15px; font-size:1.1rem;">Ayat <span id="ayat-counter">1</span></div>
                <div class="ayat-text" id="ayat-text-container">
                    <!-- Kata akan digenerate di sini -->
                </div>
            </div>

            <div class="mic-container">
                <button class="mic-btn" id="micBtn" onclick="toggleMic()">
                    <i class="fas fa-microphone"></i>
                </button>
                <div class="status-text" id="micStatus">Ketuk mic untuk mulai menyetor</div>
            </div>
        </div>

        <!-- 3. Layar SRS Evaluasi -->
        <div id="srs-screen">
            <i class="fas fa-medal" style="font-size:4rem; color:#fbbf24; margin-bottom:20px;"></i>
            <div class="srs-title">Alhamdulillah, Selesai!</div>
            <div class="srs-subtitle">Seberapa lancar hafalanmu pada surah ini?</div>

            <div class="srs-options">
                <button class="srs-btn btn-hard" onclick="saveSRS(1)">Sulit<br><span style="font-size:0.75rem;font-weight:400;">Ulang Besok</span></button>
                <button class="srs-btn btn-good" onclick="saveSRS(2)">Lancar<br><span style="font-size:0.75rem;font-weight:400;">Ulang 3 Hari</span></button>
                <button class="srs-btn btn-easy" onclick="saveSRS(3)">Sangat Mudah<br><span style="font-size:0.75rem;font-weight:400;">Ulang 7 Hari</span></button>
            </div>
        </div>
    </div>

    <script>
        // --- LOGIK PILIH SURAH ---
        let allSurah = [];
        let currentSurahId = null;

        async function fetchList() {
            try {
                const res = await fetch('https://equran.id/api/v2/surat');
                const json = await res.json();
                allSurah = json.data;
                document.getElementById('loading').style.display = 'none';
                renderList(allSurah);
            } catch (e) {
                document.getElementById('loading').innerHTML = "Gagal memuat data.";
            }
        }

        function renderList(data) {
            const container = document.getElementById('surahList');
            container.innerHTML = '';
            data.forEach(s => {
                const card = document.createElement('div');
                card.className = 's-card';
                card.onclick = () => startSession(s.nomor, s.namaLatin, s.nama);
                card.innerHTML = `<div class="s-name">${s.nomor}. ${s.namaLatin}</div><div class="s-ar">${s.nama}</div>`;
                container.appendChild(card);
            });
        }

        document.getElementById('searchInput').addEventListener('input', (e) => {
            const q = e.target.value.toLowerCase();
            renderList(allSurah.filter(s => s.namaLatin.toLowerCase().includes(q)));
        });

        // --- WEB SPEECH AI ENGINE ---
        let recognition;
        let isListening = false;
        let verses = [];
        let currentVerseIndex = 0;
        let wordsArray = [];
        let currentWordIndex = 0;

        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.lang = 'ar-SA';
            recognition.continuous = true;
            recognition.interimResults = true;
        } else {
            alert("Browser Anda tidak mendukung fitur AI Suara. Harap gunakan Google Chrome.");
        }

        // FUNGSI NORMALISASI SUPER LONGGAR (Agar lebih peka)
        function normalizeArabic(text) {
            if (!text) return '';
            return text.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED\u06DF-\u06E8]/g, '') // Hapus Harakat & Waqaf
                .replace(/[أإآءئؤ]/g, 'ا') // Jadikan semua bentuk Hamzah/Alif sama
                .replace(/ة/g, 'ه') // Samakan Ta Marbuthah & Ha
                .replace(/ى/g, 'ي') // Samakan Alif Maqsurah & Ya
                .replace(/[^ا-ي]/g, '') // Bersihkan semua karakter selain huruf Arab murni
                .trim();
        }

        async function startSession(surahNo, namaLa, namaAr) {
            currentSurahId = surahNo;
            document.getElementById('setup-screen').style.display = 'none';
            document.getElementById('session-screen').style.display = 'block';
            document.getElementById('ses-surah-ar').innerText = namaAr;
            document.getElementById('ses-surah-info').innerText = namaLa;

            document.getElementById('ayat-text-container').innerHTML = '<i class="fas fa-spinner fa-spin" style="color:var(--primary); font-size:2rem;"></i>';

            try {
                const res = await fetch(`https://equran.id/api/v2/surat/${surahNo}`);
                const json = await res.json();
                verses = json.data.ayat;
                currentVerseIndex = 0;

                if (surahNo !== 1 && verses[0].teksArab.includes('بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ')) {
                    verses[0].teksArab = verses[0].teksArab.replace('بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ', '').trim();
                }

                loadVerse(currentVerseIndex);
            } catch (e) {
                alert("Gagal memuat ayat.");
            }
        }

        function loadVerse(index) {
            if (index >= verses.length) {
                if (isListening) toggleMic();
                document.getElementById('session-screen').style.display = 'none';
                document.getElementById('srs-screen').style.display = 'block';
                return;
            }

            document.getElementById('ayat-counter').innerText = verses[index].nomorAyat;

            let rawText = verses[index].teksArab;
            wordsArray = rawText.split(' ').filter(w => w.trim() !== '');
            currentWordIndex = 0;

            let html = '';
            wordsArray.forEach((word, i) => {
                let activeCls = (i === 0) ? 'active-listen' : '';
                // Tambahkan event onclick pada kata untuk fitur "TAP TO SKIP"
                html += `<span class="word ${activeCls}" id="word-${i}" onclick="skipWord(${i})">${word}</span>`;
            });
            document.getElementById('ayat-text-container').innerHTML = html;
        }

        // FITUR BARU: TAP TO SKIP
        // Jika AI tidak bisa mendeteksi kata yang diucapkan user, user bisa tap kata tersebut di layar
        window.skipWord = function(index) {
            if (index === currentWordIndex) {
                revealWord(); // Langsung buka
            }
        };

        // Fungsi Membuka Kata dan Pindah Target
        function revealWord() {
            let wEl = document.getElementById(`word-${currentWordIndex}`);
            wEl.classList.remove('active-listen');
            wEl.classList.add('revealed');

            currentWordIndex++;

            if (currentWordIndex < wordsArray.length) {
                document.getElementById(`word-${currentWordIndex}`).classList.add('active-listen');
            } else {
                setTimeout(() => {
                    currentVerseIndex++;
                    loadVerse(currentVerseIndex);
                }, 400);
            }
        }

        // PENCOCOKAN AI (Sekarang Jauh Lebih Longgar)
        if (recognition) {
            recognition.onresult = function(event) {
                let interimTranscript = '';
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    interimTranscript += event.results[i][0].transcript;
                }

                if (interimTranscript.trim() === '') return;

                let targetWordRaw = wordsArray[currentWordIndex];
                if (!targetWordRaw) return;

                let targetNormal = normalizeArabic(targetWordRaw);
                let spokenWords = interimTranscript.split(' ');

                // Pengecekan: Apakah kata yang diucapkan mirip dengan target
                let isMatch = spokenWords.some(w => {
                    let spokenNormal = normalizeArabic(w);
                    if (spokenNormal.length === 0) return false;

                    // Lolos jika: Sama persis, atau salah satu string mengandung string lainnya
                    // Contoh: AI nangkep "بسمالله", Target "بسم". Pasti lolos karena ada kesamaan huruf.
                    return spokenNormal === targetNormal ||
                        spokenNormal.includes(targetNormal) ||
                        targetNormal.includes(spokenNormal);
                });

                if (isMatch) {
                    revealWord();
                }
            };

            recognition.onend = function() {
                if (isListening) recognition.start();
            };
        }

        function toggleMic() {
            const btn = document.getElementById('micBtn');
            const status = document.getElementById('micStatus');

            if (!isListening) {
                recognition.start();
                isListening = true;
                btn.classList.add('listening');
                btn.innerHTML = '<i class="fas fa-stop"></i>';
                status.innerHTML = "Mendengarkan... Silakan baca hafalanmu";
            } else {
                recognition.stop();
                isListening = false;
                btn.classList.remove('listening');
                btn.innerHTML = '<i class="fas fa-microphone"></i>';
                status.innerHTML = "Jeda. Ketuk mic untuk melanjutkan";
            }
        }

        // --- AJAX PENILAIAN SRS ---
        function saveSRS(grade) {
            const formData = new URLSearchParams();
            formData.append('action', 'save_srs');
            formData.append('surah', currentSurahId);
            formData.append('grade', grade);

            fetch('smart_murojaah.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                })
                .then(res => res.text())
                .then(res => {
                    alert("Jadwal Murojaah berhasil disimpan! Kembali ke Dashboard.");
                    window.location.href = 'dashboard.php';
                });
        }

        fetchList();
    </script>
</body>

</html>