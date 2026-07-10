<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = (int) $_SESSION['user_id'];

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS murojaah_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    surah_nomor INT NOT NULL,
    last_ayat INT NOT NULL,
    last_page INT DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_surah (user_id, surah_nomor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// --- AJAX HANDLER UNTUK SRS ---
if (isset($_POST['action']) && $_POST['action'] == 'save_srs') {
    $surah = (int) $_POST['surah'];
    $grade = (int) $_POST['grade'];

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

    mysqli_query($conn, "DELETE FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
    echo "saved";
    exit();
}

// --- AJAX: SIMPAN PROGRESS ---
if (isset($_POST['action']) && $_POST['action'] == 'save_progress') {
    header('Content-Type: application/json');
    $surah = (int) $_POST['surah'];
    $ayat  = (int) $_POST['ayat'];
    $page  = isset($_POST['page']) && $_POST['page'] !== '' ? (int) $_POST['page'] : null;
    $pageVal = $page ? "'$page'" : "NULL";

    $cek = mysqli_query($conn, "SELECT id FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
    if (mysqli_num_rows($cek) > 0) {
        mysqli_query($conn, "UPDATE murojaah_progress SET last_ayat='$ayat', last_page=$pageVal, updated_at=NOW() WHERE user_id='$user_id' AND surah_nomor='$surah'");
    } else {
        mysqli_query($conn, "INSERT INTO murojaah_progress (user_id, surah_nomor, last_ayat, last_page) VALUES ('$user_id', '$surah', '$ayat', $pageVal)");
    }
    echo json_encode(['status' => 'ok']);
    exit();
}

// --- AJAX: AMBIL PROGRESS ---
if (isset($_POST['action']) && $_POST['action'] == 'get_progress') {
    header('Content-Type: application/json');
    $surah = (int) $_POST['surah'];
    $res = mysqli_query($conn, "SELECT last_ayat, last_page FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
    $row = mysqli_fetch_assoc($res);
    echo json_encode($row ?: null);
    exit();
}

// --- AJAX: HAPUS PROGRESS ---
if (isset($_POST['action']) && $_POST['action'] == 'clear_progress') {
    header('Content-Type: application/json');
    $surah = (int) $_POST['surah'];
    mysqli_query($conn, "DELETE FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
    echo json_encode(['status' => 'ok']);
    exit();
}

$q_last = mysqli_query($conn, "SELECT * FROM murojaah_progress WHERE user_id = '$user_id' ORDER BY updated_at DESC LIMIT 1");
$last_murojaah = mysqli_fetch_assoc($q_last);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Murojaah AI - Hifzly</title>
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
            --mushaf-bg: #fdf8e7;
            /* Warna Kertas Mushaf Madinah */
            --mushaf-border: #2c3e50;
            --mushaf-gold: #c6a87c;
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
            font-size: 1.05rem;
        }

        .s-ar {
            font-family: 'Scheherazade New', serif;
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: bold;
        }

        #session-screen {
            display: none;
            text-align: center;
        }

        .session-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .sh-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Scheherazade New', serif;
        }

        .tool-btn {
            background: var(--card-bg);
            border: 1px solid var(--border);
            color: var(--text-muted);
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .tool-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .finish-btn {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 0 16px;
            height: 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

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

        /* MUSHAF REALISTIS LAYOUT */
        .ayat-display {
            background: var(--mushaf-bg);
            padding: 45px 35px;
            border-radius: 4px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), inset 0 0 60px rgba(198, 168, 124, 0.15);
            min-height: 60vh;
            display: flex;
            flex-direction: column;
            margin-bottom: 30px;
            position: relative;
            /* Bingkai Luar */
            border: 2px solid var(--mushaf-border);
            outline: 6px solid var(--mushaf-gold);
            outline-offset: -12px;
        }

        /* Bingkai Dalam */
        .ayat-display::after {
            content: '';
            position: absolute;
            top: 15px;
            bottom: 15px;
            left: 15px;
            right: 15px;
            border: 1px solid var(--mushaf-border);
            pointer-events: none;
        }

        /* Label Halaman di atas bingkai */
        .mushaf-header-label {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--mushaf-bg);
            padding: 0 15px;
            font-family: 'Scheherazade New', serif;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--mushaf-border);
            z-index: 10;
        }

        .ayat-text {
            font-family: 'Scheherazade New', serif;
            font-size: 2.2rem;
            line-height: 2.4;
            direction: rtl;
            color: var(--quran-text);
            text-align: justify;
            /* Rata Kiri Kanan agar rapi seperti Mushaf */
            text-justify: kashida;
            text-align-last: center;
            /* Baris terakhir di tengah */
            z-index: 2;
        }

        .word {
            color: transparent;
            text-shadow: 0 0 15px rgba(17, 24, 39, 0.35);
            transition: 0.3s ease;
            position: relative;
            user-select: none;
            cursor: pointer;
            padding: 0 2px;
        }

        .word .waqaf-mark {
            color: #d97706;
            opacity: 0.85;
            text-shadow: none;
            font-size: 0.7em;
            margin-inline-start: 2px;
            position: relative;
            top: -10px;
        }

        .word.revealed {
            color: var(--quran-text);
            text-shadow: none;
            font-weight: 600;
            cursor: default;
        }

        .word.revealed .waqaf-mark {
            opacity: 0.6;
        }

        .word.active-listen {
            border-bottom: 3px solid #f59e0b;
            padding-bottom: 2px;
        }

        .word.correct-flash {
            color: #10b981 !important;
            text-shadow: none;
            animation: popGreen 0.3s ease;
        }

        @keyframes popGreen {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.15);
                color: #34d399;
            }

            100% {
                transform: scale(1);
            }
        }

        .word.wrong-shake {
            color: #ef4444 !important;
            text-shadow: none;
            animation: shakeRed 0.4s ease;
        }

        @keyframes shakeRed {

            0%,
            100% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(-6px);
            }

            40% {
                transform: translateX(6px);
            }

            60% {
                transform: translateX(-4px);
            }

            80% {
                transform: translateX(4px);
            }
        }

        .ayah-end-marker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%23c6a87c" stroke-width="4"/><circle cx="50" cy="50" r="38" fill="none" stroke="%23c6a87c" stroke-width="2" stroke-dasharray="2,2"/></svg>') no-repeat center;
            background-size: cover;
            color: var(--mushaf-border);
            font-size: 0.9rem;
            font-weight: 700;
            margin: 0 6px;
            font-family: 'Inter', sans-serif;
            direction: ltr;
        }

        .ayat-text.preview-mode .word {
            color: var(--quran-text) !important;
            text-shadow: none !important;
        }

        /* Mic Button */
        .mic-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
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
        }

        .mic-btn.listening {
            background: #ef4444;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 1.5s infinite;
        }

        @keyframes pulse-red {
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

        .status-text {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* CUSTOM BOTTOM SHEET */
        .custom-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: 0.3s ease;
        }

        .custom-overlay.show {
            display: flex;
            opacity: 1;
        }

        .custom-modal {
            background: white;
            padding: 25px;
            border-radius: 20px;
            width: 90%;
            max-width: 400px;
            transform: scale(0.9);
            transition: 0.3s ease;
        }

        .custom-overlay.show .custom-modal {
            transform: scale(1);
        }

        .cm-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .cm-subtitle {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .cm-input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            margin-bottom: 20px;
            outline: none;
        }

        .cm-actions {
            display: flex;
            gap: 10px;
        }

        .cm-btn {
            flex: 1;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .cm-btn-cancel {
            background: #f1f5f9;
            color: var(--dark);
        }

        .cm-btn-confirm {
            background: var(--primary);
            color: white;
        }

        @media (max-width: 768px) {
            .custom-overlay {
                align-items: flex-end;
            }

            .custom-modal {
                width: 100%;
                max-width: 100%;
                border-radius: 24px 24px 0 0;
                transform: translateY(100%);
                padding-bottom: 40px;
            }

            .custom-overlay.show .custom-modal {
                transform: translateY(0);
            }

            .ayat-text {
                font-size: 1.8rem;
                line-height: 2.2;
            }

            .ayat-display {
                padding: 30px 20px;
            }
        }

        #srs-screen {
            display: none;
            text-align: center;
            margin-top: 50px;
        }

        .srs-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .srs-options {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .srs-btn {
            padding: 15px 25px;
            border-radius: 16px;
            border: none;
            font-weight: 700;
            cursor: pointer;
            flex: 1;
            color: white;
        }
    </style>
</head>

<body>

    <div class="header">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div class="header-title">Smart Murojaah AI</div>
    </div>

    <div class="container">
        <!-- 1. SETUP SCREEN -->
        <div id="setup-screen">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari Surah..." autocomplete="off">
            </div>
            <div id="loading" style="text-align:center; padding:20px; color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Menyiapkan daftar surah...</div>
            <div class="surah-list" id="surahList"></div>
        </div>

        <!-- 2. SESSION SCREEN -->
        <div id="session-screen">
            <div class="session-toolbar">
                <button class="tool-btn" id="eyeBtn" onclick="toggleEye()" title="Intip Halaman"><i class="fas fa-eye"></i></button>
                <div class="session-header">
                    <div class="sh-title" id="ses-surah-ar">...</div>
                </div>
                <button class="finish-btn" onclick="endSessionManually()"><i class="fas fa-flag-checkered"></i> Selesai</button>
            </div>

            <div class="skip-hint">
                <i class="fas fa-lightbulb"></i> <strong>Tips:</strong> Ketuk kata bergaris kuning untuk skip jika AI nyangkut.
            </div>

            <div class="ayat-display" id="ayatDisplay">
                <div class="mushaf-header-label" id="mushaf-page-label">الجزء - صفحة</div>
                <div class="ayat-text" id="ayat-text-container"></div>
            </div>

            <div class="mic-container">
                <button class="mic-btn" id="micBtn" onclick="toggleMic()"><i class="fas fa-microphone"></i></button>
                <div class="status-text" id="micStatus">Ketuk mic untuk mulai menyetor</div>
            </div>
        </div>

        <!-- 3. SRS SCREEN -->
        <div id="srs-screen">
            <i class="fas fa-medal" style="font-size:4rem; color:#fbbf24; margin-bottom:20px;"></i>
            <div class="srs-title">Alhamdulillah, Selesai!</div>
            <div style="color:var(--text-muted);">Seberapa lancar hafalanmu?</div>
            <div class="srs-options">
                <button class="srs-btn" style="background:#ef4444;" onclick="saveSRS(1)">Sulit</button>
                <button class="srs-btn" style="background:#f59e0b;" onclick="saveSRS(2)">Lancar</button>
                <button class="srs-btn" style="background:#059669;" onclick="saveSRS(3)">Mudah</button>
            </div>
        </div>
    </div>

    <!-- BOTTOM SHEET -->
    <div class="custom-overlay" id="promptOverlay">
        <div class="custom-modal">
            <div class="cm-title" id="p-title">Mulai Murojaah</div>
            <div class="cm-subtitle" id="p-subtitle"></div>
            <input type="number" class="cm-input" id="p-input" min="1" value="1">
            <div class="cm-actions">
                <button class="cm-btn cm-btn-cancel" onclick="closePrompt()">Batal</button>
                <button class="cm-btn cm-btn-confirm" onclick="confirmPrompt()">Mulai</button>
            </div>
        </div>
    </div>

    <script>
        // --- KAMUS MUQATTA'AT (MENCEGAH BUG ALIF LAM MIM) ---
        const phoneticMap = {
            "الم": "الف لام ميم",
            "المص": "الف لام ميم صاد",
            "الر": "الف لام را",
            "المر": "الف لام ميم را",
            "كهيعص": "كاف ها يا عين صاد",
            "طه": "طا ها",
            "طسم": "طا سين ميم",
            "طس": "طا سين",
            "يس": "يا سين",
            "ص": "صاد",
            "حم": "حا ميم",
            "عسق": "عين سين قاف",
            "ق": "قاف",
            "ن": "نون"
        };

        let allSurah = [];
        let currentSurahId = null;
        let pSurahNo, pNamaLa, pNamaAr, pJumlahAyat;

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
                card.onclick = () => openPrompt(s.nomor, s.namaLatin, s.nama, s.jumlahAyat);
                card.innerHTML = `<div><div class="s-name">${s.nomor}. ${s.namaLatin}</div><div style="font-size:0.8rem; color:var(--text-muted);">${s.jumlahAyat} Ayat</div></div><div class="s-ar">${s.nama}</div>`;
                container.appendChild(card);
            });
        }

        document.getElementById('searchInput').addEventListener('input', (e) => {
            renderList(allSurah.filter(s => s.namaLatin.toLowerCase().includes(e.target.value.toLowerCase())));
        });

        async function openPrompt(surahNo, namaLa, namaAr, jumlahAyat) {
            pSurahNo = surahNo;
            pNamaLa = namaLa;
            pNamaAr = namaAr;
            pJumlahAyat = jumlahAyat;
            let prog = null;
            try {
                const res = await fetch('smart_murojaah.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=get_progress&surah=${surahNo}`
                });
                prog = await res.json();
            } catch (e) {
                prog = null;
            }

            const savedAyat = prog && prog.last_ayat ? prog.last_ayat : 1;
            document.getElementById('p-title').innerText = `Murojaah ${namaLa}`;
            document.getElementById('p-subtitle').innerHTML = `${jumlahAyat} Ayat. ${prog && prog.last_ayat ? `<br><b>Lanjut dari:</b> Ayat ${prog.last_ayat}` : ''}`;

            const inputEl = document.getElementById('p-input');
            inputEl.max = jumlahAyat;
            inputEl.value = savedAyat;

            const overlay = document.getElementById('promptOverlay');
            overlay.style.display = 'flex';
            setTimeout(() => overlay.classList.add('show'), 10);
        }

        function closePrompt() {
            const overlay = document.getElementById('promptOverlay');
            overlay.classList.remove('show');
            setTimeout(() => overlay.style.display = 'none', 300);
        }

        function confirmPrompt() {
            let start = parseInt(document.getElementById('p-input').value) || 1;
            start = Math.max(1, Math.min(pJumlahAyat, start));
            closePrompt();
            loadSurahData(pSurahNo, pNamaLa, pNamaAr, start);
        }

        // --- SPEECH AI ENGINE & MUSHAF LOGIC ---
        let recognition;
        let isListening = false;
        let pages = [];
        let tokens = [];
        let currentPageIdx = 0,
            currentPageNumber = 1,
            currentTokenIdx = 0;
        let wrongStrikes = 0; // Toleransi 3 kali salah

        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.lang = 'ar-SA';
            recognition.continuous = true;
            recognition.interimResults = true;
        }

        function normalizeArabic(text) {
            if (!text) return '';
            return text.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED\u06DF-\u06E8\u08D4-\u08E1]/g, '')
                .replace(/[أإآءئؤ]/g, 'ا').replace(/ة/g, 'ه').replace(/ى/g, 'ي')
                .replace(/[^ا-ي]/g, '').trim();
        }

        function getPhonetic(rawWord) {
            let clean = rawWord.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED\u06DF-\u06E8]/g, '').trim();
            if (phoneticMap[clean]) return normalizeArabic(phoneticMap[clean]);
            return normalizeArabic(rawWord);
        }

        async function loadSurahData(surahNo, namaLa, namaAr, startAyat) {
            document.getElementById('setup-screen').style.display = 'none';
            document.getElementById('session-screen').style.display = 'block';
            document.getElementById('ses-surah-ar').innerText = namaAr;
            document.getElementById('ayat-text-container').innerHTML = '<i class="fas fa-spinner fa-spin" style="color:var(--primary); font-size:2rem;"></i>';

            try {
                const [resEquran, resCloud] = await Promise.all([
                    fetch(`https://equran.id/api/v2/surat/${surahNo}`),
                    fetch(`https://api.alquran.cloud/v1/surah/${surahNo}/quran-uthmani`)
                ]);

                const jsonEquran = await resEquran.json();
                let verses = jsonEquran.data.ayat;
                if (surahNo !== 1 && verses[0].teksArab.includes('بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ')) {
                    verses[0].teksArab = verses[0].teksArab.replace('بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ', '').trim();
                }

                try {
                    const jsonCloud = await resCloud.json();
                    verses.forEach((v, i) => {
                        v.page = jsonCloud.data.ayahs[i].page;
                        v.juz = jsonCloud.data.ayahs[i].juz;
                    });
                } catch (e) {
                    verses.forEach(v => {
                        v.page = 1;
                        v.juz = '-';
                    });
                }

                pages = buildPages(verses);
                const resumeVerseIdx = Math.max(0, startAyat - 1);
                let targetPageIdx = pages.findIndex(p => p.tokens.some(t => t.verseIdx === resumeVerseIdx));
                if (targetPageIdx < 0) targetPageIdx = 0;

                renderPage(targetPageIdx, resumeVerseIdx);
            } catch (e) {
                alert("Gagal memuat ayat.");
            }
        }

        function buildPages(verses) {
            const pagesMap = {};
            let tokenCounter = 0;

            verses.forEach((v, vIdx) => {
                const pageNum = v.page || 1;
                if (!pagesMap[pageNum]) pagesMap[pageNum] = {
                    pageNumber: pageNum,
                    juz: v.juz,
                    tokens: []
                };

                const rawWords = v.teksArab.split(' ').filter(w => w.trim() !== '');
                let lastRealTokenIdx = null;

                rawWords.forEach(w => {
                    if (normalizeArabic(w) === '') {
                        if (lastRealTokenIdx !== null) pagesMap[pageNum].tokens[lastRealTokenIdx].decor += ' ' + w;
                        return;
                    }
                    const tok = {
                        type: 'word',
                        text: w,
                        decor: '',
                        phonetic: getPhonetic(w),
                        verseIdx: vIdx,
                        id: `tok-${tokenCounter++}`
                    };
                    pagesMap[pageNum].tokens.push(tok);
                    lastRealTokenIdx = pagesMap[pageNum].tokens.length - 1;
                });
                pagesMap[pageNum].tokens.push({
                    type: 'ayahEnd',
                    verseNumber: v.nomorAyat,
                    verseIdx: vIdx,
                    id: `tok-${tokenCounter++}`
                });
            });
            return Object.values(pagesMap).sort((a, b) => a.pageNumber - b.pageNumber);
        }

        function renderPage(pageIdx, resumeVerseIdx = null) {
            currentPageIdx = pageIdx;
            const page = pages[pageIdx];
            tokens = page.tokens;
            currentPageNumber = page.pageNumber;
            wrongStrikes = 0; // Reset kesalahan saat ganti halaman

            document.getElementById('mushaf-page-label').innerText = `الجزء ${page.juz} - صفحة ${page.pageNumber}`;

            const html = tokens.map(tok => {
                if (tok.type === 'ayahEnd') return `<span class="ayah-end-marker" id="${tok.id}">${tok.verseNumber}</span>`;
                return `<span class="word" id="${tok.id}" onclick="skipWord('${tok.id}')">${tok.text}<span class="waqaf-mark">${tok.decor||''}</span></span>`;
            }).join('');

            document.getElementById('ayat-text-container').innerHTML = html;
            document.getElementById('ayat-text-container').classList.remove('preview-mode');
            document.getElementById('eyeBtn').classList.remove('active');

            let startTokenIdx = 0;
            if (resumeVerseIdx !== null) {
                tokens.forEach(t => {
                    if (t.verseIdx < resumeVerseIdx) {
                        const el = document.getElementById(t.id);
                        if (el) el.classList.add('revealed');
                    }
                });
                const idx = tokens.findIndex(t => t.verseIdx === resumeVerseIdx && t.type === 'word');
                if (idx >= 0) startTokenIdx = idx;
            }

            currentTokenIdx = startTokenIdx;
            if (tokens[currentTokenIdx]) {
                document.getElementById(tokens[currentTokenIdx].id).classList.add('active-listen');
            }
        }

        window.skipWord = function(tokenId) {
            const token = tokens[currentTokenIdx];
            if (token && token.id === tokenId && token.type === 'word') revealWord();
        };

        function toggleEye() {
            document.getElementById('ayat-text-container').classList.toggle('preview-mode');
            document.getElementById('eyeBtn').classList.toggle('active');
        }

        function revealWord() {
            wrongStrikes = 0; // Reset jika benar
            const token = tokens[currentTokenIdx];
            const wEl = document.getElementById(token.id);
            wEl.classList.remove('active-listen');
            wEl.classList.add('correct-flash');
            setTimeout(() => {
                wEl.classList.remove('correct-flash');
                wEl.classList.add('revealed');
            }, 300);

            currentTokenIdx++;

            while (tokens[currentTokenIdx] && tokens[currentTokenIdx].type === 'ayahEnd') {
                const endToken = tokens[currentTokenIdx];
                document.getElementById(endToken.id).classList.add('revealed');
                autosaveProgress(endToken.verseNumber, currentPageNumber);
                currentTokenIdx++;
            }

            if (currentTokenIdx < tokens.length) {
                document.getElementById(tokens[currentTokenIdx].id).classList.add('active-listen');
            } else {
                if (currentPageIdx + 1 < pages.length) {
                    renderPage(currentPageIdx + 1);
                } else {
                    if (isListening) toggleMic();
                    fetch('smart_murojaah.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `action=clear_progress&surah=${currentSurahId}`
                    });
                    document.getElementById('session-screen').style.display = 'none';
                    document.getElementById('srs-screen').style.display = 'block';
                }
            }
        }

        function autosaveProgress(ayatNumber, pageNumber) {
            fetch('smart_murojaah.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=save_progress&surah=${currentSurahId}&ayat=${ayatNumber}&page=${pageNumber}`
            }).catch(() => {});
        }

        function endSessionManually() {
            if (isListening) toggleMic();
            window.location.href = 'dashboard.php';
        }

        function flashWrong(tokenId) {
            const el = document.getElementById(tokenId);
            if (el) {
                el.classList.add('wrong-shake');
                if (navigator.vibrate) navigator.vibrate(100);
                setTimeout(() => el.classList.remove('wrong-shake'), 400);
            }
        }

        // --- PENCOCOKAN DENGAN TOLERANSI (3 STRIKES) ---
        if (recognition) {
            recognition.onresult = function(event) {
                const latest = event.results[event.results.length - 1];
                const transcript = latest[0].transcript;
                const isFinal = latest.isFinal;
                if (transcript.trim() === '') return;

                const token = tokens[currentTokenIdx];
                if (!token || token.type !== 'word') return;

                const targetSpeak = token.phonetic; // Gunakan Phonetic
                const spokenWords = transcript.split(' ').map(w => normalizeArabic(w)).filter(w => w !== '');

                const isMatch = spokenWords.some(sw => sw === targetSpeak || sw.includes(targetSpeak) || targetSpeak.includes(sw));

                if (isMatch) {
                    revealWord();
                    return;
                }

                if (isFinal) {
                    wrongStrikes++;
                    if (wrongStrikes >= 3) {
                        flashWrong(token.id);
                        wrongStrikes = 0; // Reset setelah merah
                    }
                }
            };

            recognition.onend = function() {
                // Beri jeda 300ms sebelum restart untuk mencegah mic loop/beeping terus-menerus
                if (isListening) {
                    setTimeout(() => {
                        if (isListening) recognition.start();
                    }, 300);
                }
            };
        }

        function toggleMic() {
            const btn = document.getElementById('micBtn');
            const status = document.getElementById('micStatus');
            if (!isListening) {
                isListening = true;
                recognition.start();
                btn.classList.add('listening');
                btn.innerHTML = '<i class="fas fa-stop"></i>';
                status.innerHTML = "Mendengarkan... Silakan baca hafalanmu";
            } else {
                isListening = false;
                recognition.stop();
                btn.classList.remove('listening');
                btn.innerHTML = '<i class="fas fa-microphone"></i>';
                status.innerHTML = "Jeda. Ketuk mic untuk melanjutkan";
            }
        }

        function saveSRS(grade) {
            const fd = new URLSearchParams();
            fd.append('action', 'save_srs');
            fd.append('surah', currentSurahId);
            fd.append('grade', grade);
            fetch('smart_murojaah.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: fd.toString()
                })
                .then(() => window.location.href = 'dashboard.php');
        }

        fetchList();
    </script>
</body>

</html>