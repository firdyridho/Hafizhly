<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}
/** @var mysqli $conn */
$user_id = (int) $_SESSION['user_id'];

// Pastikan tabel progress ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS murojaah_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    surah_nomor INT NOT NULL,
    last_ayat INT NOT NULL,
    last_page INT DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_surah (user_id, surah_nomor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// --- AJAX HANDLERS ---
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'save_srs') {
        $surah = (int) $_POST['surah'];
        $grade = (int) $_POST['grade'];
        $interval = ($grade == 2) ? 3 : (($grade == 3) ? 7 : 1);
        $next_review = date('Y-m-d', strtotime("+$interval days"));

        $cek = mysqli_query($conn, "SELECT id FROM murojaah_srs WHERE user_id='$user_id' AND surah_nomor='$surah'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE murojaah_srs SET interval_hari='$interval', next_review='$next_review', last_reviewed=NOW() WHERE user_id='$user_id' AND surah_nomor='$surah'");
        } else {
            mysqli_query($conn, "INSERT INTO murojaah_srs (user_id, surah_nomor, interval_hari, next_review) VALUES ('$user_id', '$surah', '$interval', '$next_review')");
        }
        mysqli_query($conn, "DELETE FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
        exit('saved');
    }

    if ($_POST['action'] == 'save_progress') {
        $surah = (int) $_POST['surah'];
        $ayat  = (int) $_POST['ayat'];
        $page  = !empty($_POST['page']) ? (int) $_POST['page'] : 'NULL';

        $cek = mysqli_query($conn, "SELECT id FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE murojaah_progress SET last_ayat='$ayat', last_page=$page, updated_at=NOW() WHERE user_id='$user_id' AND surah_nomor='$surah'");
        } else {
            mysqli_query($conn, "INSERT INTO murojaah_progress (user_id, surah_nomor, last_ayat, last_page) VALUES ('$user_id', '$surah', '$ayat', $page)");
        }
        exit(json_encode(['status' => 'ok']));
    }

    if ($_POST['action'] == 'get_progress') {
        $surah = (int) $_POST['surah'];
        $res = mysqli_query($conn, "SELECT last_ayat, last_page FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
        exit(json_encode(mysqli_fetch_assoc($res) ?: null));
    }

    if ($_POST['action'] == 'clear_progress') {
        $surah = (int) $_POST['surah'];
        mysqli_query($conn, "DELETE FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
        exit(json_encode(['status' => 'ok']));
    }
}

// Ambil data Terakhir Murojaah
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
            /* Kertas Mushaf */
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
            padding-bottom: 100px;
            overflow-x: hidden;
        }

        /* HEADER (Fixed & Rapi) */
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

        /* SETUP SCREEN & CARDS */
        #setup-screen {
            display: block;
        }

        .last-murojaah-card {
            background: linear-gradient(135deg, var(--primary), #10b981);
            color: white;
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
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

        /* SESSION SCREEN */
        #session-screen {
            display: none;
            text-align: center;
        }

        .session-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
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
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: 0.2s;
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
            height: 45px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .skip-hint {
            background: #fef3c7;
            color: #d97706;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            font-weight: 500;
            border: 1px dashed #f59e0b;
        }

        /* MUSHAF DISPLAY - DIPERBAIKI AGAR TIDAK KETUTUPAN */
        .ayat-display {
            background: var(--mushaf-bg);
            padding: 50px 30px 40px 30px;
            /* Padding atas dibesarkan */
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1), inset 0 0 60px rgba(198, 168, 124, 0.15);
            min-height: 50vh;
            margin-bottom: 30px;
            position: relative;
            border: 2px solid var(--mushaf-border);
            outline: 6px solid var(--mushaf-gold);
            outline-offset: -10px;
            margin-top: 10px;
        }

        .ayat-display::after {
            content: '';
            position: absolute;
            top: 13px;
            bottom: 13px;
            left: 13px;
            right: 13px;
            border: 1px solid var(--mushaf-border);
            pointer-events: none;
        }

        .mushaf-header-label {
            position: absolute;
            top: 18px;
            left: 50%;
            transform: translateX(-50%);
            background: transparent;
            font-family: 'Scheherazade New', serif;
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--mushaf-border);
            z-index: 10;
            width: 100%;
            text-align: center;
        }

        .ayat-text {
            font-family: 'Scheherazade New', serif;
            font-size: 2.3rem;
            line-height: 2.3;
            direction: rtl;
            color: var(--quran-text);
            text-align: justify;
            text-justify: kashida;
            text-align-last: center;
            z-index: 2;
            position: relative;
            margin-top: 20px;
        }

        .word {
            color: transparent;
            text-shadow: 0 0 12px rgba(17, 24, 39, 0.25);
            transition: 0.2s ease;
            position: relative;
            user-select: none;
            cursor: pointer;
            padding: 0 3px;
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
            font-weight: bold;
            cursor: default;
        }

        .word.revealed .waqaf-mark {
            opacity: 0.6;
        }

        .word.active-listen {
            border-bottom: 3px solid #f59e0b;
            padding-bottom: 2px;
        }

        /* FITUR MATA - MUNCULKAN TEKS TRANSPARAN */
        .ayat-text.preview-mode .word {
            color: rgba(17, 24, 39, 0.3) !important;
            text-shadow: none !important;
        }

        .ayat-text.preview-mode .word.revealed {
            color: var(--quran-text) !important;
        }

        .word.correct-flash {
            color: #10b981 !important;
            text-shadow: none !important;
        }

        .word.wrong-shake {
            color: #ef4444 !important;
            text-shadow: none !important;
            animation: shakeRed 0.4s ease;
        }

        @keyframes shakeRed {

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

        .ayah-end-marker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%23c6a87c" stroke-width="4"/><circle cx="50" cy="50" r="38" fill="none" stroke="%23c6a87c" stroke-width="2" stroke-dasharray="2,2"/></svg>') no-repeat center;
            background-size: cover;
            color: var(--mushaf-border);
            font-size: 1rem;
            font-weight: 700;
            margin: 0 6px;
            font-family: 'Inter', sans-serif;
            direction: ltr;
        }

        /* MIC BUTTON */
        .mic-container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 15px;
            position: fixed;
            bottom: 20px;
            left: 0;
            width: 100%;
            z-index: 200;
            background: linear-gradient(to top, var(--bg) 60%, transparent);
            padding-bottom: 20px;
        }

        .mic-btn {
            width: 75px;
            height: 75px;
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
            font-weight: 600;
            text-align: center;
            background: rgba(255, 255, 255, 0.8);
            padding: 5px 15px;
            border-radius: 20px;
        }

        /* BOTTOM SHEET */
        .custom-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            align-items: flex-end;
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
            width: 100%;
            max-width: 500px;
            border-radius: 24px 24px 0 0;
            transform: translateY(100%);
            transition: 0.3s ease;
            padding-bottom: 40px;
        }

        .custom-overlay.show .custom-modal {
            transform: translateY(0);
        }

        .cm-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .cm-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .cm-input {
            width: 100%;
            padding: 15px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 1.1rem;
            margin-bottom: 20px;
            outline: none;
        }

        .cm-actions {
            display: flex;
            gap: 10px;
        }

        .cm-btn {
            flex: 1;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .cm-btn-cancel {
            background: #f1f5f9;
            color: var(--dark);
        }

        .cm-btn-confirm {
            background: var(--primary);
            color: white;
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
            font-size: 1rem;
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
            <?php if ($last_murojaah): ?>
                <div class="last-murojaah-card">
                    <div class="lmc-label"><i class="fas fa-history"></i> Lanjutkan Murojaah</div>
                    <div class="lmc-title">Surah ke-<?= $last_murojaah['surah_nomor'] ?> (Ayat <?= $last_murojaah['last_ayat'] ?>)</div>
                </div>
            <?php endif; ?>

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari Surah..." autocomplete="off">
            </div>
            <div id="loading" style="text-align:center; padding:20px; color:var(--text-muted);"><i class="fas fa-spinner fa-spin"></i> Menyiapkan mushaf...</div>
            <div class="surah-list" id="surahList"></div>
        </div>

        <!-- 2. SESSION SCREEN -->
        <div id="session-screen">
            <div class="session-toolbar">
                <button class="tool-btn" id="eyeBtn" onclick="toggleEye()" title="Intip Teks"><i class="fas fa-eye"></i></button>
                <div class="sh-title" id="ses-surah-ar">...</div>
                <button class="finish-btn" onclick="endSessionManually()"><i class="fas fa-check"></i> Selesai</button>
            </div>

            <div class="skip-hint">
                <i class="fas fa-info-circle"></i> Jika AI kurang akurat/nyangkut, <strong>klik kata bergaris kuning</strong> untuk melewatinya.
            </div>

            <div class="ayat-display" id="ayatDisplay">
                <div class="mushaf-header-label" id="mushaf-page-label">الجزء - صفحة</div>
                <div class="ayat-text" id="ayat-text-container"></div>
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

    <!-- MIC CONTROL (Fixed Bottom) -->
    <div class="mic-container" id="micArea" style="display:none;">
        <div class="status-text" id="micStatus">Ketuk mic untuk mulai menyetor</div>
        <button class="mic-btn" id="micBtn" onclick="toggleMic()"><i class="fas fa-microphone"></i></button>
    </div>

    <!-- BOTTOM SHEET -->
    <div class="custom-overlay" id="promptOverlay">
        <div class="custom-modal">
            <div class="cm-title" id="p-title">Mulai Murojaah</div>
            <div class="cm-subtitle" id="p-subtitle"></div>
            <input type="number" class="cm-input" id="p-input" min="1" value="1">
            <div class="cm-actions">
                <button class="cm-btn cm-btn-cancel" onclick="closePrompt()">Batal</button>
                <button class="cm-btn cm-btn-confirm" onclick="confirmPrompt()">Mulai Sekarang</button>
            </div>
        </div>
    </div>

    <script>
        // Kamus Muqatta'at untuk memperbaiki bug Alif Lam Mim
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
            } catch (e) {}

            const savedAyat = prog && prog.last_ayat ? prog.last_ayat : 1;
            document.getElementById('p-title').innerText = `Murojaah ${namaLa}`;
            document.getElementById('p-subtitle').innerHTML = `${jumlahAyat} Ayat. ${prog && prog.last_ayat ? `Lanjut dari ayat ke-${prog.last_ayat}?` : ''}`;

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

        // --- CORE AI & MUSHAF ---
        let recognition;
        let isListening = false;
        let pages = [],
            tokens = [],
            currentPageIdx = 0,
            currentPageNumber = 1,
            currentTokenIdx = 0;
        let wrongStrikes = 0;

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
            return phoneticMap[clean] ? normalizeArabic(phoneticMap[clean]) : normalizeArabic(rawWord);
        }

        async function loadSurahData(surahNo, namaLa, namaAr, startAyat) {
            currentSurahId = surahNo;
            document.getElementById('setup-screen').style.display = 'none';
            document.getElementById('session-screen').style.display = 'block';
            document.getElementById('micArea').style.display = 'flex'; // Munculkan Mic
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
                    pagesMap[pageNum].tokens.push({
                        type: 'word',
                        text: w,
                        decor: '',
                        phonetic: getPhonetic(w),
                        verseIdx: vIdx,
                        id: `tok-${tokenCounter++}`
                    });
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
            wrongStrikes = 0;

            document.getElementById('mushaf-page-label').innerText = `الجزء ${page.juz} - صفحة ${page.pageNumber}`;

            const html = tokens.map(tok => {
                if (tok.type === 'ayahEnd') return `<span class="ayah-end-marker" id="${tok.id}">${tok.verseNumber}</span>`;
                return `<span class="word" id="${tok.id}" onclick="skipWord('${tok.id}')">${tok.text}<span class="waqaf-mark">${tok.decor||''}</span></span>`;
            }).join('');

            const container = document.getElementById('ayat-text-container');
            container.innerHTML = html;

            // Pastikan Mode Intip Tertutup saat ganti halaman
            container.classList.remove('preview-mode');
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

        // FUNGSI INTIP AYAT (MATA) DIPERBAIKI
        function toggleEye() {
            const container = document.getElementById('ayat-text-container');
            const btn = document.getElementById('eyeBtn');
            container.classList.toggle('preview-mode');
            btn.classList.toggle('active');
        }

        function revealWord() {
            wrongStrikes = 0;
            const token = tokens[currentTokenIdx];
            const wEl = document.getElementById(token.id);
            wEl.classList.remove('active-listen');
            wEl.classList.add('correct-flash');
            setTimeout(() => {
                wEl.classList.remove('correct-flash');
                wEl.classList.add('revealed');
            }, 200);

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
                    document.getElementById('micArea').style.display = 'none';
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
                if (navigator.vibrate) navigator.vibrate(50);
                setTimeout(() => el.classList.remove('wrong-shake'), 400);
            }
        }

        // --- PENCOCOKAN AI YANG SUPER LONGGAR (FUZZY MATCH) ---
        function fuzzyMatch(spokenWord, targetWord) {
            let s = normalizeArabic(spokenWord);
            let t = normalizeArabic(targetWord);
            if (s.length < 2 || t.length < 2) return s === t;
            // Jika suara mengandung kata target atau sebaliknya
            return s.includes(t) || t.includes(s);
        }

        if (recognition) {
            recognition.onresult = function(event) {
                let transcript = '';
                // Menggabungkan semua hasil (interim & final)
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    transcript += event.results[i][0].transcript;
                }

                if (transcript.trim() === '') return;

                const token = tokens[currentTokenIdx];
                if (!token || token.type !== 'word') return;

                const targetSpeak = token.phonetic;
                const spokenWords = transcript.split(' ').map(w => normalizeArabic(w)).filter(w => w !== '');

                // Cek dengan Fuzzy Match
                const isMatch = spokenWords.some(sw => fuzzyMatch(sw, targetSpeak));

                if (isMatch) {
                    revealWord();
                    return;
                }

                // Cek Toleransi Jika Final
                if (event.results[event.results.length - 1].isFinal) {
                    wrongStrikes++;
                    if (wrongStrikes >= 3) {
                        flashWrong(token.id);
                        wrongStrikes = 0;
                    }
                }
            };

            recognition.onend = function() {
                // Jangan paksa nyala jika di-stop manual
                if (isListening) {
                    // Beri jeda agak lama (800ms) agar tidak memicu "ting-tung" berulang cepat
                    setTimeout(() => {
                        if (isListening) recognition.start();
                    }, 800);
                }
            };
        }

        function toggleMic() {
            if (!recognition) {
                alert("Browser tidak mendukung AI Suara. Pakai Chrome Android.");
                return;
            }
            const btn = document.getElementById('micBtn');
            const status = document.getElementById('micStatus');
            if (!isListening) {
                isListening = true;
                recognition.start();
                btn.classList.add('listening');
                btn.innerHTML = '<i class="fas fa-stop"></i>';
                status.innerHTML = "Mendengarkan... Silakan baca";
            } else {
                isListening = false;
                recognition.stop();
                btn.classList.remove('listening');
                btn.innerHTML = '<i class="fas fa-microphone"></i>';
                status.innerHTML = "Jeda. Ketuk mic untuk lanjut";
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