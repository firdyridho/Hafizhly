<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = (int) $_SESSION['user_id'];

// Auto-migrate: tabel buat nyimpen progress terakhir user per surah
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
    $grade = (int) $_POST['grade']; // 1=Sulit(Ulang Besok), 2=Bagus(3 Hari), 3=Mudah(7 Hari)

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

    // Surah dianggap kelar -> hapus progress sementara
    mysqli_query($conn, "DELETE FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");

    echo "saved";
    exit();
}

// --- AJAX: SIMPAN PROGRESS TERAKHIR (dipanggil otomatis tiap ayat kelar, atau saat tombol Selesai) ---
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

// --- AJAX: AMBIL PROGRESS TERAKHIR (dipanggil saat user pilih surah) ---
if (isset($_POST['action']) && $_POST['action'] == 'get_progress') {
    header('Content-Type: application/json');
    $surah = (int) $_POST['surah'];
    $res = mysqli_query($conn, "SELECT last_ayat, last_page FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
    $row = mysqli_fetch_assoc($res);
    echo json_encode($row ?: null);
    exit();
}

// --- AJAX: HAPUS PROGRESS (dipanggil saat surah kelar total) ---
if (isset($_POST['action']) && $_POST['action'] == 'clear_progress') {
    header('Content-Type: application/json');
    $surah = (int) $_POST['surah'];
    mysqli_query($conn, "DELETE FROM murojaah_progress WHERE user_id='$user_id' AND surah_nomor='$surah'");
    echo json_encode(['status' => 'ok']);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            --gold: #C9A227;
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

        .session-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .session-header {
            flex: 1;
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
            min-width: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            font-size: 1rem;
        }

        .tool-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
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
            white-space: nowrap;
            transition: 0.2s;
        }

        .finish-btn:hover {
            background: #fecaca;
        }

        /* Meta Halaman / Juz / Sisi Mushaf */
        .mushaf-meta {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .meta-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            background: linear-gradient(135deg, #fff7e6, #fef3c7);
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .meta-badge.page-badge {
            background: linear-gradient(135deg, var(--primary-light), #a7f3d0);
            color: #047857;
            border-color: #6ee7b7;
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

        /* Kotak Ayat (Halaman Mushaf) */
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
            transition: border-color 0.3s;
        }

        .ayat-display.page-complete {
            animation: pageGlow 0.6s ease;
            border-color: #10b981;
        }

        @keyframes pageGlow {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5);
            }

            100% {
                box-shadow: 0 0 0 25px rgba(16, 185, 129, 0);
            }
        }

        .ayat-text {
            font-family: 'Scheherazade New', serif;
            font-size: 2.5rem;
            line-height: 2.2;
            direction: rtl;
            color: var(--quran-text);
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
        }

        .ayat-text.preview-mode .word {
            color: var(--quran-text) !important;
            text-shadow: none !important;
        }

        /* Kata per Kata */
        .word {
            color: transparent;
            text-shadow: 0 0 15px rgba(17, 24, 39, 0.4);
            transition: 0.3s ease;
            position: relative;
            user-select: none;
            cursor: pointer;
        }

        .word .waqaf-mark {
            color: #f59e0b;
            opacity: 0.85;
            text-shadow: none;
            font-size: 0.8em;
            margin-inline-start: 2px;
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
            padding-bottom: 5px;
        }

        /* Feedback Benar */
        .word.correct-flash {
            color: #10b981 !important;
            text-shadow: none;
            animation: popGreen 0.35s ease;
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

        /* Feedback Salah */
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

        /* Penanda Akhir Ayat (bulatan mushaf) */
        .ayah-end-marker {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: radial-gradient(circle, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
            color: #b45309;
            font-size: 0.7rem;
            font-weight: 700;
            margin: 0 4px;
            font-family: 'Inter', sans-serif;
            direction: ltr;
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

        @media (max-width: 480px) {
            .sh-title {
                font-size: 1.2rem;
            }

            .ayat-text {
                font-size: 2rem;
            }
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

        <!-- 2. Layar Setoran AI (Mode Halaman Mushaf) -->
        <div id="session-screen">
            <div class="session-toolbar">
                <button class="tool-btn" id="eyeBtn" onclick="toggleEye()" title="Intip Halaman">
                    <i class="fas fa-eye"></i>
                </button>
                <div class="session-header">
                    <div class="sh-title" id="ses-surah-ar">...</div>
                    <div style="font-size:0.85rem; color:var(--text-muted);" id="ses-surah-info">...</div>
                </div>
                <button class="finish-btn" onclick="endSessionManually()">
                    <i class="fas fa-flag-checkered"></i> Selesai
                </button>
            </div>

            <div class="mushaf-meta">
                <span class="meta-badge page-badge" id="page-badge">Halaman -</span>
                <span class="meta-badge" id="juz-badge">Juz -</span>
                <span class="meta-badge" id="side-badge">-</span>
            </div>

            <!-- Petunjuk agar user tidak frustrasi -->
            <div class="skip-hint">
                <i class="fas fa-lightbulb"></i> <strong>Tips:</strong> Jika AI nyangkut, sentuh kata yang bergaris kuning untuk melewatinya. Kata yang meleset akan bergetar merah.
            </div>

            <div class="ayat-display" id="ayatDisplay">
                <div id="page-progress-info" style="font-weight:600;color:var(--text-muted);margin-bottom:15px;font-size:0.85rem;">Halaman 1 dari 1</div>
                <div class="ayat-text" id="ayat-text-container">
                    <!-- Token (kata & penanda akhir ayat) digenerate di sini -->
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
                card.onclick = () => startSession(s.nomor, s.namaLatin, s.nama, s.jumlahAyat);
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

        // --- STATE HALAMAN MUSHAF ---
        let verses = [];
        let pages = []; // [{ pageNumber, juz, tokens: [...] }]
        let tokens = []; // token halaman yang lagi aktif
        let currentPageIdx = 0;
        let currentPageNumber = 1;
        let currentTokenIdx = 0;
        let lastCompletedAyat = null;
        let lastCompletedPage = null;
        let wrongFlashLock = false;

        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.lang = 'ar-SA';
            recognition.continuous = true;
            recognition.interimResults = true;
        } else {
            Swal.fire('Tidak Didukung', 'Browser Anda tidak mendukung fitur AI Suara. Harap gunakan Google Chrome.', 'warning');
        }

        // FUNGSI NORMALISASI SUPER LONGGAR (Agar lebih peka)
        // Juga dipakai untuk mendeteksi token waqaf: kalau hasilnya '' berarti
        // token itu cuma tanda waqaf/harakat, bukan kata sungguhan.
        function normalizeArabic(text) {
            if (!text) return '';
            return text.replace(/[\u0610-\u061A\u064B-\u065F\u0670\u06D6-\u06ED\u06DF-\u06E8\u08D4-\u08E1]/g, '') // Hapus Harakat & Waqaf
                .replace(/[أإآءئؤ]/g, 'ا') // Jadikan semua bentuk Hamzah/Alif sama
                .replace(/ة/g, 'ه') // Samakan Ta Marbuthah & Ha
                .replace(/ى/g, 'ي') // Samakan Alif Maqsurah & Ya
                .replace(/[^ا-ي]/g, '') // Bersihkan semua karakter selain huruf Arab murni
                .trim();
        }

        function isDecorativeToken(token) {
            return normalizeArabic(token) === '';
        }

        // --- MULAI SESI: tanya progress & mau mulai dari ayat berapa ---
        async function startSession(surahNo, namaLa, namaAr, jumlahAyat) {
            currentSurahId = surahNo;

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

            const {
                value: startAyatRaw,
                isConfirmed
            } = await Swal.fire({
                title: 'Mulai dari Ayat Berapa?',
                html: `<div style="text-align:left;font-size:0.85rem;color:#64748b;margin-bottom:10px;">
                            ${namaLa} &bull; ${jumlahAyat} Ayat
                            ${prog && prog.last_ayat ? `<br><b>Progress tersimpan:</b> Ayat ${prog.last_ayat}${prog.last_page ? ' (Halaman ' + prog.last_page + ')' : ''}` : ''}
                       </div>`,
                input: 'number',
                inputValue: savedAyat,
                inputAttributes: {
                    min: 1,
                    max: jumlahAyat,
                    step: 1
                },
                showCancelButton: true,
                confirmButtonText: 'Mulai Menghafal',
                confirmButtonColor: '#059669',
                cancelButtonText: 'Batal'
            });

            if (!isConfirmed) return;

            let startAyat = parseInt(startAyatRaw) || 1;
            startAyat = Math.max(1, Math.min(jumlahAyat, startAyat));

            loadSurahData(surahNo, namaLa, namaAr, startAyat);
        }

        // --- AMBIL DATA AYAT + DATA HALAMAN MUSHAF ---
        async function loadSurahData(surahNo, namaLa, namaAr, startAyat) {
            document.getElementById('setup-screen').style.display = 'none';
            document.getElementById('session-screen').style.display = 'block';
            document.getElementById('ses-surah-ar').innerText = namaAr;
            document.getElementById('ses-surah-info').innerText = namaLa;
            document.getElementById('ayat-text-container').innerHTML = '<i class="fas fa-spinner fa-spin" style="color:var(--primary); font-size:2rem;"></i>';

            try {
                const res = await fetch(`https://equran.id/api/v2/surat/${surahNo}`);
                const json = await res.json();
                verses = json.data.ayat;

                if (surahNo !== 1 && verses[0].teksArab.includes('بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ')) {
                    verses[0].teksArab = verses[0].teksArab.replace('بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ', '').trim();
                }

                // Ambil nomor halaman & juz mushaf standar Madinah (604 halaman)
                try {
                    const res2 = await fetch(`https://api.alquran.cloud/v1/surah/${surahNo}/quran-uthmani`);
                    const json2 = await res2.json();
                    const ayahs = json2.data.ayahs;
                    verses.forEach((v, i) => {
                        v.page = ayahs[i] ? ayahs[i].page : 1;
                        v.juz = ayahs[i] ? ayahs[i].juz : '-';
                    });
                } catch (e2) {
                    // fallback: kalau API halaman gagal, semua dianggap 1 halaman
                    verses.forEach(v => {
                        v.page = 1;
                        v.juz = '-';
                    });
                }

                pages = buildPages(verses);

                const resumeVerseIdx = Math.max(0, Math.min(verses.length - 1, startAyat - 1));
                let targetPageIdx = pages.findIndex(p => p.tokens.some(t => t.verseIdx === resumeVerseIdx));
                if (targetPageIdx < 0) targetPageIdx = 0;

                renderPage(targetPageIdx, resumeVerseIdx);
            } catch (e) {
                Swal.fire('Gagal', 'Gagal memuat ayat, coba lagi.', 'error');
            }
        }

        // --- SUSUN AYAT MENJADI HALAMAN-HALAMAN MUSHAF ---
        function buildPages(verses) {
            const pagesMap = {};
            let tokenCounter = 0;

            verses.forEach((v, vIdx) => {
                const pageNum = v.page || 1;
                if (!pagesMap[pageNum]) {
                    pagesMap[pageNum] = {
                        pageNumber: pageNum,
                        juz: v.juz,
                        tokens: []
                    };
                }

                const rawWords = v.teksArab.split(' ').filter(w => w.trim() !== '');
                let lastRealTokenIdx = null;

                rawWords.forEach(w => {
                    if (isDecorativeToken(w)) {
                        // Tanda waqaf: nempel ke kata sebelumnya, bukan token target sendiri
                        if (lastRealTokenIdx !== null) {
                            pagesMap[pageNum].tokens[lastRealTokenIdx].decor += ' ' + w;
                        }
                        return;
                    }
                    const tok = {
                        type: 'word',
                        text: w,
                        decor: '',
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

        // --- RENDER SATU HALAMAN MUSHAF ---
        function renderPage(pageIdx, resumeVerseIdx = null) {
            currentPageIdx = pageIdx;
            const page = pages[pageIdx];
            tokens = page.tokens;
            currentPageNumber = page.pageNumber;

            document.getElementById('page-badge').innerText = `Halaman ${page.pageNumber}`;
            document.getElementById('juz-badge').innerText = `Juz ${page.juz}`;
            document.getElementById('side-badge').innerText = (page.pageNumber % 2 === 1) ? 'Sisi Kanan' : 'Sisi Kiri';
            document.getElementById('page-progress-info').innerText = `Halaman ${pageIdx + 1} dari ${pages.length}`;

            const html = tokens.map(tok => {
                if (tok.type === 'ayahEnd') {
                    return `<span class="ayah-end-marker" id="${tok.id}">&#1757;${tok.verseNumber}</span>`;
                }
                return `<span class="word" id="${tok.id}" onclick="skipWord('${tok.id}')">${tok.text}<span class="waqaf-mark">${tok.decor || ''}</span></span>`;
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
                const el = document.getElementById(tokens[currentTokenIdx].id);
                if (el) el.classList.add('active-listen');
            }
        }

        // FITUR TAP TO SKIP
        window.skipWord = function(tokenId) {
            const token = tokens[currentTokenIdx];
            if (token && token.id === tokenId && token.type === 'word') {
                revealWord();
            }
        };

        // FITUR MATA: intip seluruh halaman
        function toggleEye() {
            const container = document.getElementById('ayat-text-container');
            const btn = document.getElementById('eyeBtn');
            container.classList.toggle('preview-mode');
            btn.classList.toggle('active');
        }

        // Membuka kata (benar dibaca) & pindah target
        function revealWord() {
            const token = tokens[currentTokenIdx];
            const wEl = document.getElementById(token.id);
            wEl.classList.remove('active-listen');
            wEl.classList.add('correct-flash');
            setTimeout(() => {
                wEl.classList.remove('correct-flash');
                wEl.classList.add('revealed');
            }, 350);

            currentTokenIdx++;

            // Lewati penanda akhir ayat otomatis + autosave progress per ayat
            while (tokens[currentTokenIdx] && tokens[currentTokenIdx].type === 'ayahEnd') {
                const endToken = tokens[currentTokenIdx];
                const endEl = document.getElementById(endToken.id);
                if (endEl) endEl.classList.add('revealed');
                autosaveProgress(endToken.verseNumber, currentPageNumber);
                currentTokenIdx++;
            }

            if (currentTokenIdx < tokens.length) {
                const nextEl = document.getElementById(tokens[currentTokenIdx].id);
                if (nextEl) nextEl.classList.add('active-listen');
            } else {
                setTimeout(() => nextPage(), 500);
            }
        }

        function nextPage() {
            const display = document.getElementById('ayatDisplay');
            display.classList.add('page-complete');
            setTimeout(() => {
                display.classList.remove('page-complete');
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
            }, 600);
        }

        function autosaveProgress(ayatNumber, pageNumber) {
            lastCompletedAyat = ayatNumber;
            lastCompletedPage = pageNumber;
            fetch('smart_murojaah.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=save_progress&surah=${currentSurahId}&ayat=${ayatNumber}&page=${pageNumber}`
            }).catch(() => {});
        }

        // Tombol "Selesai" manual: simpan progress & keluar kapan saja
        function endSessionManually() {
            Swal.fire({
                title: 'Akhiri Sesi?',
                text: lastCompletedAyat ?
                    `Progress akan disimpan sampai Ayat ${lastCompletedAyat} (Halaman ${lastCompletedPage}).` :
                    'Belum ada ayat yang kelar dibaca, progress belum tersimpan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan & Keluar',
                cancelButtonText: 'Lanjut Menghafal',
                confirmButtonColor: '#059669'
            }).then(res => {
                if (res.isConfirmed) {
                    if (isListening) toggleMic();
                    window.location.href = 'dashboard.php';
                }
            });
        }

        function flashWrong(tokenId) {
            if (wrongFlashLock) return;
            wrongFlashLock = true;

            const el = document.getElementById(tokenId);
            const statusEl = document.getElementById('micStatus');
            if (el) el.classList.add('wrong-shake');
            if (statusEl) statusEl.innerHTML = '<span style="color:#ef4444;">Sepertinya ada bacaan yang meleset, coba ulangi</span>';

            setTimeout(() => {
                if (el) el.classList.remove('wrong-shake');
                wrongFlashLock = false;
                if (statusEl && isListening) statusEl.innerHTML = 'Mendengarkan... Silakan baca hafalanmu';
            }, 700);
        }

        // PENCOCOKAN AI (deteksi benar & salah baca)
        if (recognition) {
            recognition.onresult = function(event) {
                const latest = event.results[event.results.length - 1];
                const transcript = latest[0].transcript;
                const isFinal = latest.isFinal;

                if (transcript.trim() === '') return;

                const token = tokens[currentTokenIdx];
                if (!token || token.type !== 'word') return;

                const targetNormal = normalizeArabic(token.text);
                const spokenWords = transcript.split(' ');

                const isMatch = spokenWords.some(w => {
                    const sw = normalizeArabic(w);
                    if (sw.length === 0) return false;
                    return sw === targetNormal || sw.includes(targetNormal) || targetNormal.includes(sw);
                });

                if (isMatch) {
                    revealWord();
                    return;
                }

                // Baru dianggap "salah" kalau hasil final DAN gak cocok sama 2 kata ke depan
                // (toleransi buat delay natural antara ucapan & hasil speech-to-text)
                if (isFinal) {
                    let lookaheadMatch = false;
                    for (let k = 1; k <= 2; k++) {
                        const nt = tokens[currentTokenIdx + k];
                        if (nt && nt.type === 'word') {
                            const ntn = normalizeArabic(nt.text);
                            if (spokenWords.some(w => {
                                    const sw = normalizeArabic(w);
                                    return sw.length > 0 && (sw === ntn || sw.includes(ntn) || ntn.includes(sw));
                                })) {
                                lookaheadMatch = true;
                                break;
                            }
                        }
                    }
                    if (!lookaheadMatch) {
                        flashWrong(token.id);
                    }
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

        // --- AJAX PENILAIAN SRS (dipanggil saat surah kelar total) ---
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
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: 'Jadwal Murojaah berhasil disimpan.',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        window.location.href = 'dashboard.php';
                    });
                });
        }

        fetchList();
    </script>
</body>

</html>