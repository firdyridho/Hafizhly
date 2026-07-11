<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

// --- AJAX HANDLER UNTUK BOOKMARK ---
if (isset($_POST['action']) && $_POST['action'] == 'bookmark') {
    $surah = (int)$_POST['surah'];
    $ayat = (int)$_POST['ayat'];

    $cek = mysqli_query($conn, "SELECT id FROM bookmark WHERE user_id='$user_id' AND surah_nomor='$surah' AND ayat='$ayat'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO bookmark (user_id, surah_nomor, ayat, catatan) VALUES ('$user_id', '$surah', '$ayat', 'Disimpan otomatis')");
        echo "saved";
    } else {
        echo "exist";
    }
    exit();
}

$nomor_surat = isset($_GET['nomor']) ? (int)$_GET['nomor'] : 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Al-Qur'an</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Scheherazade+New:wght@400;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
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
            --gold: #b8912f;
            --gold-deep: #8f6f1f;
            --gold-soft: #f6ecc9;
            --arabic-scale: 1;
            /* hanya untuk mode daftar */
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
            scroll-behavior: smooth;
        }

        .read-header {
            background: var(--card-bg);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .h-btn {
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }

        .h-btn:hover,
        .h-btn.active {
            color: var(--primary);
        }

        .surah-name-mini {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.05rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mode-toggle {
            display: flex;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3px;
            gap: 2px;
        }

        .mode-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 13px;
            border-radius: 16px;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }

        .mode-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.35);
        }

        .font-toggle {
            display: flex;
            align-items: center;
            gap: 2px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3px;
        }

        .font-toggle .h-btn {
            font-size: 0.85rem;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .font-toggle .h-btn:hover {
            background: var(--primary-light);
        }

        .font-toggle .ft-label {
            font-size: 0.68rem;
            font-weight: 700;
            color: var(--text-muted);
            padding: 0 4px;
            white-space: nowrap;
        }

        @media (max-width: 480px) {
            .mode-label {
                display: none;
            }

            .mode-btn {
                padding: 8px 11px;
            }
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .surah-info-card {
            background: linear-gradient(135deg, var(--primary), #10b981);
            border-radius: 20px;
            padding: 30px 20px;
            color: white;
            text-align: center;
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.2);
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .surah-info-card::before {
            content: '۞';
            position: absolute;
            font-size: 10rem;
            opacity: 0.1;
            right: -20px;
            bottom: -40px;
            color: white;
        }

        .sic-ar {
            font-family: 'Scheherazade New', serif;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sic-la {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .sic-details {
            font-size: 0.85rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            gap: 10px;
            align-items: center;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
        }

        .bismillah-img {
            text-align: center;
            font-family: 'Scheherazade New', serif;
            font-size: 2.2rem;
            color: var(--dark);
            margin: 10px 0 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
            line-height: 1.5;
            display: none;
        }

        .ayat-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .ayat-card {
            background: var(--card-bg);
            padding: 25px 20px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border);
            transition: 0.3s;
        }

        .ayat-card.playing {
            border-color: var(--primary);
            background: #f0fdf4;
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.1);
        }

        .ayat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: var(--bg);
            padding: 8px 12px;
            border-radius: 12px;
        }

        .ayat-number-badge {
            width: 30px;
            height: 30px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .ayat-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .ayat-action-btn {
            color: var(--text-muted);
            font-size: 1.15rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .ayat-action-btn:hover {
            color: var(--primary);
            transform: scale(1.1);
        }

        .ayat-action-btn.playing {
            color: #f59e0b;
        }

        .teks-arab {
            font-family: 'Scheherazade New', serif;
            font-size: calc(2.3rem * var(--arabic-scale));
            text-align: right;
            line-height: 2.2;
            color: var(--quran-text);
            margin-bottom: 20px;
            direction: rtl;
        }

        .teks-container {
            transition: 0.3s;
        }

        .body-no-terjemah .teks-container {
            display: none;
        }

        .teks-latin {
            font-size: 1rem;
            color: var(--primary);
            margin-bottom: 8px;
            font-weight: 500;
            line-height: 1.5;
        }

        .teks-indo {
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.6;
        }

        .tafsir-box {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: var(--bg);
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--dark);
            line-height: 1.6;
            text-align: justify;
        }

        .tafsir-box.show {
            display: block;
        }

        .t-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .islamic-alert {
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: white;
            padding: 14px 24px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            transition: 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            width: max-content;
            max-width: 90vw;
        }

        .islamic-alert.show {
            top: 30px;
        }

        .ia-icon {
            font-size: 1.2rem;
            color: #fbbf24;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: flex-end;
        }

        .modal-content {
            background: var(--card-bg);
            width: 100%;
            max-width: 600px;
            border-top-left-radius: 25px;
            border-top-right-radius: 25px;
            padding: 30px 20px;
            max-height: 80vh;
            overflow-y: auto;
            transform: translateY(100%);
            transition: 0.3s;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary);
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }

        #loading {
            text-align: center;
            margin-top: 50px;
            font-size: 1.1rem;
            color: var(--primary);
            font-weight: 600;
        }

        /* ============================================================ */
        /* ======================  MODE MUSHAF  ========================= */
        /* ============================================================ */

        .mushaf-wrapper {
            max-width: 760px;
            margin: 0 auto;
            padding: 16px 12px 40px;
        }

        .mushaf-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 14px;
        }

        .mtb-btn {
            background: var(--card-bg);
            border: 1px solid var(--border);
            color: var(--gold-deep);
            font-weight: 700;
            font-size: 0.8rem;
            padding: 9px 14px;
            border-radius: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.2s;
            flex-shrink: 0;
        }

        .mtb-btn:hover {
            border-color: var(--gold);
            background: var(--gold-soft);
        }

        .mtb-btn.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .mtb-pagegroup {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            justify-content: center;
            min-width: 0;
        }

        .mtb-nav {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--card-bg);
            border: 1px solid var(--border);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            flex-shrink: 0;
        }

        .mtb-nav:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .mtb-nav:active {
            transform: scale(0.92);
        }

        .mtb-pageinfo {
            text-align: center;
            min-width: 0;
        }

        .mtb-pageinfo #mushaf-surah-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 46vw;
        }

        .mtb-pagejump {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .mtb-pagejump input {
            width: 42px;
            text-align: center;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 2px 4px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary);
        }

        .mushaf-stage {
            position: relative;
            perspective: 1200px;
            touch-action: pan-y;
        }

        .mushaf-loading {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 60px 0;
            color: var(--gold-deep);
            font-weight: 600;
        }

        .mushaf-page {
            background: transparent;
            border-radius: 0;
            padding: 6px clamp(8px, 3vw, 20px) 40px;
            box-shadow: none;
            border: none;
            outline: none;
            min-height: 300px;
            position: relative;
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mushaf-page.no-transition {
            transition: none !important;
        }

        .mushaf-page.anim-out-next {
            transform: translateX(-36px) rotateY(-6deg);
            opacity: 0;
        }

        .mushaf-page.anim-out-prev {
            transform: translateX(36px) rotateY(6deg);
            opacity: 0;
        }

        .mushaf-page.anim-in-fromright {
            transform: translateX(36px) rotateY(6deg);
            opacity: 0;
        }

        .mushaf-page.anim-in-fromleft {
            transform: translateX(-36px) rotateY(-6deg);
            opacity: 0;
        }

        .mushaf-error {
            text-align: center;
            padding: 50px 10px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Baris teks mushaf: rata kanan, tanpa justify, ukuran font responsif mengecil di mobile */
        .mushaf-line-text {
            direction: rtl;
            font-family: 'Scheherazade New', serif;
            /* Ukuran font mengikuti lebar viewport: di mobile kecil, di desktop besar */
            font-size: clamp(1rem, 4.2vw, 2.2rem);
            line-height: 2.4;
            color: var(--quran-text);
            text-align: right;
            /* rata kanan, tidak dipaksa justify */
            margin-bottom: 2px;
        }

        /* Header surah & basmala tidak terpengaruh */
        .line-surah-header {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            background: linear-gradient(90deg, transparent, var(--gold-soft), transparent);
            border-top: 1px solid var(--gold);
            border-bottom: 1px solid var(--gold);
            padding: 8px 0;
            margin: 14px 0 12px;
            font-size: clamp(1.3rem, 4.5vw, 1.7rem);
            color: var(--gold-deep);
            font-weight: 700;
        }

        .lsh-orn {
            color: var(--gold);
            font-size: 0.85em;
        }

        .line-basmala {
            display: block;
            text-align: center;
            font-size: clamp(1.5rem, 5vw, 1.9rem);
            color: var(--primary);
            margin: 4px 0 14px;
            font-weight: 700;
        }

        .ayah-word {
            cursor: pointer;
            border-radius: 6px;
            padding: 1px 2px;
            transition: background 0.2s;
            -webkit-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
        }

        .ayah-word:hover {
            background: rgba(5, 150, 105, 0.1);
        }

        .ayah-word.pressing {
            background: rgba(184, 145, 47, 0.22);
        }

        .ayah-word.playing {
            background: rgba(245, 158, 11, 0.18);
        }

        /* Nomor ayat menempel tanpa jarak */
        .ayah-end-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'Amiri', serif;
            font-size: 0.62em;
            color: var(--gold-deep);
            background: var(--gold-soft);
            border: 1px solid var(--gold);
            border-radius: 50%;
            width: 1.7em;
            height: 1.7em;
            margin: 0;
            vertical-align: middle;
        }

        .mushaf-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            cursor: pointer;
            transition: 0.2s;
            z-index: 5;
        }

        .mushaf-arrow:hover {
            background: var(--primary);
            color: #fff;
        }

        .mushaf-arrow.left {
            left: -6px;
        }

        .mushaf-arrow.right {
            right: -6px;
        }

        @media (max-width: 640px) {
            .mushaf-arrow {
                display: none;
            }
        }

        .mushaf-hint {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .mushaf-translation-panel {
            display: none;
            margin-top: 18px;
            background: var(--card-bg);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 6px;
        }

        .mushaf-translation-panel.show {
            display: block;
        }

        .ptp-item {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: 0.2s;
        }

        .ptp-item:last-child {
            border-bottom: none;
        }

        .ptp-item:hover {
            background: var(--bg);
        }

        .ptp-badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-light);
            padding: 2px 9px;
            border-radius: 20px;
            margin-bottom: 5px;
        }

        .ptp-text {
            display: block;
            font-size: 0.88rem;
            color: #475569;
            line-height: 1.5;
        }

        /* Ayah action sheet (mode mushaf) */
        .sheet-close {
            position: absolute;
            top: 14px;
            right: 16px;
            font-size: 1.2rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .sheet-actions {
            display: flex;
            gap: 10px;
            margin: 16px 0;
            flex-wrap: wrap;
        }

        .sheet-action-btn {
            flex: 1;
            min-width: 100px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--dark);
            cursor: pointer;
            transition: 0.2s;
        }

        .sheet-action-btn i {
            display: block;
            font-size: 1.1rem;
            margin-bottom: 6px;
            color: var(--primary);
        }

        .sheet-action-btn:hover {
            background: var(--primary-light);
        }

        #sheet-latin {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        #sheet-indo {
            color: #475569;
            font-size: 0.92rem;
            line-height: 1.6;
            margin-bottom: 14px;
        }

        #sheet-tafsir-box {
            display: none;
            background: var(--bg);
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            padding: 14px;
            font-size: 0.88rem;
            line-height: 1.6;
            color: var(--dark);
            text-align: justify;
        }

        #sheet-tafsir-box.show {
            display: block;
        }

        @media (prefers-reduced-motion: reduce) {
            .mushaf-page {
                transition: none !important;
            }
        }
    </style>
</head>

<body id="baca-body">

    <audio id="audioFull"></audio>
    <audio id="audioAyat"></audio>

    <div class="read-header">
        <div class="header-left">
            <a href="alquran.php" class="h-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="surah-name-mini" id="mini-title">Memuat...</div>
        </div>
        <div class="header-right">
            <div class="mode-toggle" id="modeToggle">
                <div class="mode-btn active" data-mode="list" onclick="switchMode('list')" title="Mode Daftar Ayat">
                    <i class="fas fa-align-right"></i><span class="mode-label">Ayat</span>
                </div>
                <div class="mode-btn" data-mode="page" onclick="switchMode('page')" title="Mode Mushaf">
                    <i class="fas fa-book-open"></i><span class="mode-label">Mushaf</span>
                </div>
            </div>
            <!-- Tombol pengatur font, hanya muncul di mode list -->
            <div class="font-toggle" id="fontToggleContainer" title="Ukuran teks arab (hanya mode daftar)">
                <div class="h-btn" onclick="changeFontSize(-1)"><i class="fas fa-minus"></i></div>
                <span class="ft-label">Aa</span>
                <div class="h-btn" onclick="changeFontSize(1)"><i class="fas fa-plus"></i></div>
            </div>
            <div class="h-btn active" id="btn-terjemah" onclick="toggleTerjemah()" title="Terjemahan"><i class="fas fa-language"></i></div>
            <div class="h-btn" id="btn-play-full" onclick="togglePlayFull()" title="Putar Murottal"><i class="fas fa-play-circle"></i></div>
            <div class="h-btn" onclick="openInfoModal()" title="Asbabun Nuzul"><i class="fas fa-info-circle"></i></div>
        </div>
    </div>

    <!-- ===================== MODE: PER AYAT (LIST) ===================== -->
    <div class="container" id="listView">
        <div class="surah-info-card" id="hero-card" style="display:none;">
            <div class="sic-ar" id="hero-ar">--</div>
            <div class="sic-la" id="hero-la">--</div>
            <div class="sic-details" id="hero-det">--</div>
        </div>

        <div class="bismillah-img" id="bismillah">
            بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ
        </div>

        <div id="loading"><i class="fas fa-spinner fa-spin"></i> Menyiapkan Mushaf...</div>
        <div class="ayat-list" id="ayatList"></div>
    </div>

    <!-- ===================== MODE: PER HALAMAN (MUSHAF) ===================== -->
    <div class="mushaf-wrapper" id="mushafView" style="display:none;">
        <div class="mushaf-toolbar">
            <div class="mtb-btn" id="mushaf-juz-badge" onclick="promptJuzJump()" title="Klik untuk lompat ke Juz tertentu">
                <i class="fas fa-bookmark"></i> Juz -
            </div>
            <div class="mtb-pagegroup">
                <div class="mtb-nav" onclick="goPrevPage()" title="Halaman sebelumnya"><i class="fas fa-chevron-left"></i></div>
                <div class="mtb-pageinfo">
                    <div id="mushaf-surah-title">Memuat...</div>
                    <div class="mtb-pagejump">
                        Hal. <input type="number" id="mushaf-page-input" min="1" max="604" onchange="jumpToPageInput()"> / 604
                    </div>
                </div>
                <div class="mtb-nav" onclick="goNextPage()" title="Halaman berikutnya"><i class="fas fa-chevron-right"></i></div>
            </div>
            <div class="mtb-btn" id="btn-mushaf-terjemah" onclick="toggleMushafTerjemah()" title="Tampilkan terjemah halaman ini">
                <i class="fas fa-language"></i>
            </div>
        </div>

        <div class="mushaf-stage" id="mushaf-stage">
            <div class="mushaf-loading" id="mushaf-loading"><i class="fas fa-spinner fa-spin"></i>&nbsp; Memuat halaman...</div>
            <div class="mushaf-page" id="mushaf-page"></div>
            <div class="mushaf-arrow left" onclick="goNextPage()" title="Lanjut"><i class="fas fa-chevron-left"></i></div>
            <div class="mushaf-arrow right" onclick="goPrevPage()" title="Mundur"><i class="fas fa-chevron-right"></i></div>
        </div>

        <div class="mushaf-hint"><i class="fas fa-hand-pointer"></i> Geser layar / ketuk tepi untuk pindah halaman &bull; Ketuk ayat untuk audio &bull; Tahan ayat untuk tafsir, terjemah &amp; simpan</div>

        <div class="mushaf-translation-panel" id="mushaf-translation-panel"></div>
    </div>

    <!-- Alert Custom -->
    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-check-circle ia-icon"></i>
        <div style="font-size:0.95rem; font-weight:600;" id="alertMsg">Berhasil!</div>
    </div>

    <!-- Modal Asbabun Nuzul -->
    <div class="modal" id="infoModal" onclick="closeInfoModal(event)">
        <div class="modal-content" id="infoContent">
            <h2 class="modal-title" id="m-title">Asbabun Nuzul</h2>
            <div style="font-size:0.95rem; line-height:1.7; color:#475569;" id="m-desc"></div>
        </div>
    </div>

    <!-- Ayah Action Sheet (mode mushaf) -->
    <div class="modal" id="ayahSheet" onclick="closeAyahSheet(event)">
        <div class="modal-content" style="position:relative;">
            <div class="sheet-close" onclick="closeAyahSheet(event)"><i class="fas fa-times"></i></div>
            <h2 class="modal-title" id="sheet-surah-ayat">-</h2>
            <div id="sheet-latin"></div>
            <div id="sheet-indo"></div>
            <div class="sheet-actions">
                <div class="sheet-action-btn" onclick="sheetPlayAudio()"><i class="fas fa-play-circle"></i> Audio</div>
                <div class="sheet-action-btn" onclick="sheetToggleTafsir()"><i class="fas fa-book-open"></i> Tafsir</div>
                <div class="sheet-action-btn" onclick="sheetBookmark()"><i class="fas fa-bookmark"></i> Tandai</div>
            </div>
            <div id="sheet-tafsir-box">
                <div class="t-title" style="color:var(--primary); font-weight:700; font-size:0.85rem; text-transform:uppercase; margin-bottom:5px;">Tafsir Kemenag RI</div>
                <div id="sheet-tafsir-text"></div>
            </div>
        </div>
    </div>

    <script>
        const noSurat = <?= $nomor_surat ?>;
        let surahData = null;
        let tafsirData = null;
        let audioFullEl = document.getElementById('audioFull');
        let audioAyatEl = document.getElementById('audioAyat');

        async function fetchAlQuranData() {
            try {
                const [resSurat, resTafsir] = await Promise.all([
                    fetch(`https://equran.id/api/v2/surat/${noSurat}`),
                    fetch(`https://equran.id/api/v2/tafsir/${noSurat}`)
                ]);

                const jsonSurat = await resSurat.json();
                const jsonTafsir = await resTafsir.json();

                surahData = jsonSurat.data;
                tafsirData = jsonTafsir.data.tafsir;

                equranCache[noSurat] = {
                    surah: surahData,
                    tafsir: tafsirData
                };

                setupUI();
                renderAyat(surahData.ayat);
            } catch (e) {
                document.getElementById('loading').innerHTML = "Gagal memuat ayat. Periksa koneksi internet.";
            }
        }

        function setupUI() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('mini-title').innerText = surahData.namaLatin;

            document.getElementById('hero-card').style.display = 'block';
            document.getElementById('hero-ar').innerText = surahData.nama;
            document.getElementById('hero-la').innerText = surahData.namaLatin;
            let tmpt = surahData.tempatTurun === 'Mekah' ? 'Makiyyah' : 'Madaniyyah';
            document.getElementById('hero-det').innerHTML = `<span>${surahData.arti}</span> • <span>${tmpt}</span> • <span>${surahData.jumlahAyat} Ayat</span>`;

            if (noSurat !== 1 && noSurat !== 9) {
                document.getElementById('bismillah').style.display = 'block';
            }

            document.getElementById('m-title').innerText = `Info & Asbabun Nuzul: ${surahData.namaLatin}`;
            document.getElementById('m-desc').innerHTML = surahData.deskripsi;
        }

        function renderAyat(ayatList) {
            const container = document.getElementById('ayatList');
            let html = '';

            ayatList.forEach(a => {
                let txtTafsir = "Tafsir tidak tersedia.";
                let findTafsir = tafsirData.find(t => t.ayat == a.nomorAyat);
                if (findTafsir) txtTafsir = findTafsir.teks;

                html += `
                <div class="ayat-card" id="ayat-${a.nomorAyat}">
                    <div class="ayat-header">
                        <div class="ayat-number-badge">${a.nomorAyat}</div>
                        <div class="ayat-actions">
                            <i class="fas fa-book-open ayat-action-btn" onclick="toggleTafsir(${a.nomorAyat})" title="Baca Tafsir"></i>
                            <i class="fas fa-bookmark ayat-action-btn" onclick="saveBookmark(${a.nomorAyat})" title="Tandai Terakhir Baca"></i>
                            <i class="fas fa-play ayat-action-btn" id="btn-play-ayat-${a.nomorAyat}" onclick="playAyat('${a.audio['05']}', ${a.nomorAyat})" title="Putar Audio"></i>
                        </div>
                    </div>
                    <div class="teks-arab">${a.teksArab}</div>
                    <div class="teks-container">
                        <div class="teks-latin">${a.teksLatin}</div>
                        <div class="teks-indo">${a.teksIndonesia}</div>
                        <div class="tafsir-box" id="tafsir-${a.nomorAyat}">
                            <div class="t-title">Tafsir Kemenag RI</div>
                            ${txtTafsir}
                        </div>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        }

        let isTerjemahTampil = true;

        function toggleTerjemah() {
            isTerjemahTampil = !isTerjemahTampil;
            const bodyEl = document.getElementById('baca-body');
            const btn = document.getElementById('btn-terjemah');
            if (isTerjemahTampil) {
                bodyEl.classList.remove('body-no-terjemah');
                btn.classList.add('active');
            } else {
                bodyEl.classList.add('body-no-terjemah');
                btn.classList.remove('active');
            }
        }

        function toggleTafsir(no) {
            if (!isTerjemahTampil) toggleTerjemah();
            document.getElementById(`tafsir-${no}`).classList.toggle('show');
        }

        let isFullAudioLoaded = false;

        function togglePlayFull() {
            const btn = document.getElementById('btn-play-full');

            if (!isFullAudioLoaded) {
                audioFullEl.src = surahData.audioFull['05'];
                isFullAudioLoaded = true;
            }

            if (audioFullEl.paused) {
                audioAyatEl.pause();
                resetAyatIcons();
                resetMushafHighlight();
                audioFullEl.play();
                btn.innerHTML = '<i class="fas fa-pause-circle"></i>';
                btn.classList.add('active');
            } else {
                audioFullEl.pause();
                btn.innerHTML = '<i class="fas fa-play-circle"></i>';
                btn.classList.remove('active');
            }
        }
        audioFullEl.onended = () => {
            document.getElementById('btn-play-full').innerHTML = '<i class="fas fa-play-circle"></i>';
            document.getElementById('btn-play-full').classList.remove('active');
        };

        let currentAyatCard = null;
        let currentAyatNo = null;

        function playAyat(url, nomor) {
            audioFullEl.pause();
            document.getElementById('btn-play-full').innerHTML = '<i class="fas fa-play-circle"></i>';
            document.getElementById('btn-play-full').classList.remove('active');
            resetMushafHighlight();

            if (currentAyatNo === nomor && !audioAyatEl.paused) {
                audioAyatEl.pause();
                resetAyatIcons();
                return;
            }

            resetAyatIcons();

            audioAyatEl.src = url;
            audioAyatEl.play();

            currentAyatNo = nomor;
            currentAyatCard = document.getElementById(`ayat-${nomor}`);
            currentAyatCard.classList.add('playing');
            document.getElementById(`btn-play-ayat-${nomor}`).className = "fas fa-pause ayat-action-btn playing";
        }

        function resetAyatIcons() {
            if (currentAyatCard) {
                currentAyatCard.classList.remove('playing');
                if (currentAyatNo) {
                    const btn = document.getElementById(`btn-play-ayat-${currentAyatNo}`);
                    if (btn) btn.className = "fas fa-play ayat-action-btn";
                }
                currentAyatNo = null;
                currentAyatCard = null;
            }
        }

        audioAyatEl.onended = () => {
            resetAyatIcons();
            resetMushafHighlight();
        };

        function saveBookmark(ayatNo) {
            saveBookmarkGeneric(noSurat, ayatNo);
        }

        function saveBookmarkGeneric(surahNum, ayatNo) {
            const formData = new URLSearchParams();
            formData.append('action', 'bookmark');
            formData.append('surah', surahNum);
            formData.append('ayat', ayatNo);

            fetch('baca.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                })
                .then(res => res.text())
                .then(res => {
                    if (res.trim() === 'saved') {
                        showAlert(`Ayat ${ayatNo} berhasil disimpan!`);
                    } else {
                        showAlert(`Ayat ${ayatNo} sudah tersimpan.`);
                    }
                });
        }

        function showAlert(msg) {
            document.getElementById('alertMsg').innerText = msg;
            const alertEl = document.getElementById('customAlert');
            alertEl.classList.add('show');
            setTimeout(() => alertEl.classList.remove('show'), 3000);
        }

        function openInfoModal() {
            document.getElementById('infoModal').style.display = 'flex';
            setTimeout(() => document.getElementById('infoModal').classList.add('show'), 10);
        }

        function closeInfoModal(e) {
            if (e.target.id === 'infoModal' || e.target.classList.contains('close-btn')) {
                document.getElementById('infoModal').classList.remove('show');
                setTimeout(() => document.getElementById('infoModal').style.display = 'none', 300);
            }
        }

        /* ================================================================ */
        /* ========================  MODE MUSHAF  ========================== */
        /* ================================================================ */

        let currentMode = 'list';
        let equranCache = {};
        let pageCache = {};
        let mushafSurahStartCache = {};
        let currentPageNum = null;
        let currentJuz = null;
        let currentMushafPlayKey = null;
        let mushafTerjemahShown = false;

        const MUSHAF_BASE = 'https://cdn.jsdelivr.net/gh/zonetecde/mushaf-layout@main/mushaf/page-';

        const JUZ_START = [
            [1, 1],
            [2, 142],
            [2, 253],
            [3, 93],
            [4, 24],
            [4, 148],
            [5, 82],
            [6, 111],
            [7, 88],
            [8, 41],
            [9, 93],
            [11, 6],
            [12, 53],
            [15, 1],
            [17, 1],
            [18, 75],
            [21, 1],
            [23, 1],
            [25, 21],
            [27, 56],
            [29, 46],
            [33, 31],
            [36, 28],
            [39, 32],
            [41, 47],
            [46, 1],
            [51, 31],
            [58, 1],
            [67, 1],
            [78, 1]
        ];

        function pad3(n) {
            return String(n).padStart(3, '0');
        }

        function wait(ms) {
            return new Promise(r => setTimeout(r, ms));
        }

        async function getSurahData(nomor) {
            if (equranCache[nomor]) return equranCache[nomor];
            const [resSurat, resTafsir] = await Promise.all([
                fetch(`https://equran.id/api/v2/surat/${nomor}`),
                fetch(`https://equran.id/api/v2/tafsir/${nomor}`)
            ]);
            const jsonSurat = await resSurat.json();
            const jsonTafsir = await resTafsir.json();
            const data = {
                surah: jsonSurat.data,
                tafsir: jsonTafsir.data.tafsir
            };
            equranCache[nomor] = data;
            return data;
        }

        async function getPage(n) {
            if (pageCache[n]) return pageCache[n];
            const res = await fetch(`${MUSHAF_BASE}${pad3(n)}.json`);
            if (!res.ok) throw new Error('Gagal memuat data halaman');
            const data = await res.json();
            pageCache[n] = data;
            return data;
        }

        async function leadingSurah(n) {
            const pg = await getPage(n);
            for (const line of pg.lines) {
                if (line.type === 'surah-header') return parseInt(line.surah, 10);
                if (line.type === 'text' && line.verseRange) return parseInt(line.verseRange.split('-')[0].split(':')[0], 10);
            }
            return null;
        }

        async function findStartPage(surahNum) {
            if (mushafSurahStartCache[surahNum]) return mushafSurahStartCache[surahNum];
            let lo = 1,
                hi = 604,
                ans = 604;
            while (lo <= hi) {
                const mid = (lo + hi) >> 1;
                const s = await leadingSurah(mid);
                if (s >= surahNum) {
                    ans = mid;
                    hi = mid - 1;
                } else {
                    lo = mid + 1;
                }
            }
            mushafSurahStartCache[surahNum] = ans;
            return ans;
        }

        function juzOf(surah, ayah) {
            for (let j = JUZ_START.length - 1; j >= 0; j--) {
                const [s, a] = JUZ_START[j];
                if (surah > s || (surah === s && ayah >= a)) return j + 1;
            }
            return 1;
        }

        function collectSurahsInPage(pg) {
            const set = new Set();
            pg.lines.forEach(line => {
                if (line.type === 'surah-header') set.add(parseInt(line.surah, 10));
                if (line.type === 'text' && line.words) {
                    line.words.forEach(w => set.add(parseInt(w.location.split(':')[0], 10)));
                }
            });
            return Array.from(set);
        }

        function firstVerseOfPage(pg) {
            for (const line of pg.lines) {
                if (line.type === 'text' && line.words && line.words.length) {
                    const [s, a] = line.words[0].location.split(':').map(Number);
                    return {
                        s,
                        a
                    };
                }
                if (line.type === 'surah-header') return {
                    s: parseInt(line.surah, 10),
                    a: 1
                };
            }
            return {
                s: 1,
                a: 1
            };
        }

        function renderLineWords(words) {
            let html = '';
            let currentKey = null;
            let currentText = '';
            for (let i = 0; i < words.length; i++) {
                const w = words[i];
                const key = w.location.split(':').slice(0, 2).join(':');
                if (key !== currentKey) {
                    if (currentText) {
                        currentText = currentText.replace(/([\u0660-\u0669]+)\s*$/, '<span class="ayah-end-badge">$1</span>');
                        html += `<span class="ayah-word" data-verse="${currentKey}">${currentText}</span>`;
                    }
                    currentKey = key;
                    currentText = '';
                }
                currentText += (currentText ? ' ' : '') + w.word;
            }
            if (currentText) {
                currentText = currentText.replace(/([\u0660-\u0669]+)\s*$/, '<span class="ayah-end-badge">$1</span>');
                html += `<span class="ayah-word" data-verse="${currentKey}">${currentText}</span>`;
            }
            return html;
        }

        function collectOrderedVerseKeys(pg) {
            const keys = [];
            const seen = new Set();
            pg.lines.forEach(line => {
                if (line.type === 'text' && line.words) {
                    line.words.forEach(w => {
                        const key = w.location.split(':').slice(0, 2).join(':');
                        if (!seen.has(key)) {
                            seen.add(key);
                            keys.push(key);
                        }
                    });
                }
            });
            return keys;
        }

        function renderPageTranslationPanel(pg) {
            const keys = collectOrderedVerseKeys(pg);
            let html = '';
            keys.forEach(k => {
                const [s, a] = k.split(':').map(Number);
                const data = equranCache[s];
                if (!data) return;
                const ay = data.surah.ayat.find(x => x.nomorAyat === a);
                if (!ay) return;
                html += `<div class="ptp-item" onclick="openAyahSheet('${k}')">
                    <span class="ptp-badge">${data.surah.namaLatin} : ${a}</span>
                    <span class="ptp-text">${ay.teksIndonesia}</span>
                </div>`;
            });
            document.getElementById('mushaf-translation-panel').innerHTML = html || '<div class="ptp-item">Terjemah tidak tersedia untuk halaman ini.</div>';
        }

        async function renderMushafPage(pg) {
            const surahs = collectSurahsInPage(pg);
            await Promise.all(surahs.map(s => getSurahData(s)));

            const {
                s: fs,
                a: fa
            } = firstVerseOfPage(pg);
            currentJuz = juzOf(fs, fa);
            currentPageNum = pg.page;

            const titleParts = surahs.map(s => equranCache[s].surah.namaLatin);
            document.getElementById('mushaf-surah-title').innerText = titleParts.join(' • ');
            document.getElementById('mushaf-juz-badge').innerHTML = `<i class="fas fa-bookmark"></i> Juz ${currentJuz}`;
            document.getElementById('mushaf-page-input').value = pg.page;
            document.getElementById('mini-title').innerText = titleParts[0] || '-';

            let html = '';
            pg.lines.forEach(line => {
                if (line.type === 'surah-header') {
                    const sNum = parseInt(line.surah, 10);
                    const sd = equranCache[sNum] ? equranCache[sNum].surah : null;
                    html += `<div class="mushaf-line line-surah-header"><span class="lsh-orn"><i class="fas fa-gem"></i></span><span>سورة ${sd ? sd.nama : ''}</span><span class="lsh-orn"><i class="fas fa-gem"></i></span></div>`;
                } else if (line.type === 'basmala') {
                    html += `<div class="mushaf-line line-basmala">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</div>`;
                } else if (line.type === 'text' && line.words) {
                    html += `<div class="mushaf-line-text">${renderLineWords(line.words)}</div>`;
                }
            });

            document.getElementById('mushaf-page').innerHTML = html;
            renderPageTranslationPanel(pg);
        }

        async function setCurrentPage(n, dir) {
            if (n < 1 || n > 604) return;
            const pageEl = document.getElementById('mushaf-page');
            const loadingEl = document.getElementById('mushaf-loading');

            if (dir) {
                pageEl.classList.remove('anim-in-fromleft', 'anim-in-fromright', 'no-transition');
                pageEl.classList.add(dir === 'next' ? 'anim-out-next' : 'anim-out-prev');
                await wait(220);
            } else {
                loadingEl.style.display = 'flex';
                pageEl.style.opacity = '0.4';
            }

            try {
                const pg = await getPage(n);
                await renderMushafPage(pg);
            } catch (e) {
                document.getElementById('mushaf-page').innerHTML = '<div class="mushaf-error"><i class="fas fa-triangle-exclamation"></i><br>Gagal memuat halaman. Periksa koneksi internet lalu coba lagi.</div>';
            }

            loadingEl.style.display = 'none';
            pageEl.style.opacity = '';
            pageEl.classList.remove('anim-out-next', 'anim-out-prev');

            if (dir) {
                pageEl.classList.add('no-transition', dir === 'next' ? 'anim-in-fromright' : 'anim-in-fromleft');
                void pageEl.offsetWidth;
                pageEl.classList.remove('no-transition');
                requestAnimationFrame(() => {
                    pageEl.classList.remove('anim-in-fromright', 'anim-in-fromleft');
                });
            }
        }

        function goNextPage() {
            if (currentPageNum && currentPageNum < 604) setCurrentPage(currentPageNum + 1, 'next');
        }

        function goPrevPage() {
            if (currentPageNum && currentPageNum > 1) setCurrentPage(currentPageNum - 1, 'prev');
        }

        function jumpToPageInput() {
            const v = parseInt(document.getElementById('mushaf-page-input').value, 10);
            if (v >= 1 && v <= 604) setCurrentPage(v, null);
        }

        function promptJuzJump() {
            const v = prompt('Lompat ke Juz berapa? (1-30)', currentJuz || 1);
            const j = parseInt(v, 10);
            if (j >= 1 && j <= 30) {
                const [s, a] = JUZ_START[j - 1];
                document.getElementById('mushaf-loading').style.display = 'flex';
                findStartPage(s).then(async startPage => {
                    let p = startPage;
                    for (let i = 0; i < 40; i++) {
                        const pg = await getPage(p);
                        const fv = firstVerseOfPage(pg);
                        if (fv.s > s || (fv.s === s && fv.a >= a)) break;
                        p++;
                    }
                    setCurrentPage(p, null);
                });
            }
        }

        function toggleMushafTerjemah() {
            mushafTerjemahShown = !mushafTerjemahShown;
            document.getElementById('mushaf-translation-panel').classList.toggle('show', mushafTerjemahShown);
            document.getElementById('btn-mushaf-terjemah').classList.toggle('active', mushafTerjemahShown);
        }

        async function switchMode(mode) {
            if (mode === currentMode) return;
            currentMode = mode;
            document.querySelectorAll('.mode-btn').forEach(b => b.classList.toggle('active', b.dataset.mode === mode));

            const fontToggler = document.getElementById('fontToggleContainer');
            if (mode === 'page') {
                fontToggler.style.display = 'none';
                document.getElementById('listView').style.display = 'none';
                document.getElementById('mushafView').style.display = 'block';
                if (currentPageNum === null) {
                    document.getElementById('mushaf-loading').style.display = 'flex';
                    try {
                        const startPage = await findStartPage(noSurat);
                        await setCurrentPage(startPage, null);
                    } catch (e) {
                        document.getElementById('mushaf-page').innerHTML = '<div class="mushaf-error"><i class="fas fa-triangle-exclamation"></i><br>Gagal memuat mode mushaf. Periksa koneksi internet.</div>';
                        document.getElementById('mushaf-loading').style.display = 'none';
                    }
                }
            } else {
                fontToggler.style.display = 'flex';
                document.getElementById('mushafView').style.display = 'none';
                document.getElementById('listView').style.display = 'block';
                document.getElementById('mini-title').innerText = surahData ? surahData.namaLatin : 'Memuat...';
                audioAyatEl.pause();
                resetMushafHighlight();
            }
        }

        // --- AUDIO & INTERAKSI AYAT (MODE MUSHAF) ---
        function playAyatMushaf(url, verseKey) {
            audioFullEl.pause();
            document.getElementById('btn-play-full').innerHTML = '<i class="fas fa-play-circle"></i>';
            document.getElementById('btn-play-full').classList.remove('active');
            resetAyatIcons();

            if (currentMushafPlayKey === verseKey && !audioAyatEl.paused) {
                audioAyatEl.pause();
                resetMushafHighlight();
                return;
            }

            resetMushafHighlight();
            audioAyatEl.src = url;
            audioAyatEl.play();
            currentMushafPlayKey = verseKey;
            document.querySelectorAll(`.ayah-word[data-verse="${verseKey}"]`).forEach(el => el.classList.add('playing'));
        }

        function resetMushafHighlight() {
            if (currentMushafPlayKey) {
                document.querySelectorAll(`.ayah-word[data-verse="${currentMushafPlayKey}"]`).forEach(el => el.classList.remove('playing'));
                currentMushafPlayKey = null;
            }
        }

        function openAyahSheet(verseKey) {
            const [s, a] = verseKey.split(':').map(Number);
            const data = equranCache[s];
            if (!data) return;
            const ayatObj = data.surah.ayat.find(x => x.nomorAyat === a);
            const tafsirObj = data.tafsir.find(t => t.ayat === a);
            if (!ayatObj) return;

            document.getElementById('sheet-surah-ayat').innerText = `${data.surah.namaLatin} : ${a}`;
            document.getElementById('sheet-latin').innerText = ayatObj.teksLatin;
            document.getElementById('sheet-indo').innerText = ayatObj.teksIndonesia;
            document.getElementById('sheet-tafsir-text').innerHTML = tafsirObj ? tafsirObj.teks : 'Tafsir tidak tersedia.';
            document.getElementById('sheet-tafsir-box').classList.remove('show');

            const sheet = document.getElementById('ayahSheet');
            sheet.dataset.verse = verseKey;
            sheet.dataset.surah = s;
            sheet.dataset.ayat = a;
            sheet.dataset.audio = ayatObj.audio['05'];
            sheet.style.display = 'flex';
            setTimeout(() => sheet.classList.add('show'), 10);
        }

        function closeAyahSheet(e) {
            if (!e || e.target.id === 'ayahSheet' || e.target.classList.contains('sheet-close') || e.target.closest('.sheet-close')) {
                const sheet = document.getElementById('ayahSheet');
                sheet.classList.remove('show');
                setTimeout(() => sheet.style.display = 'none', 300);
            }
        }

        function sheetToggleTafsir() {
            document.getElementById('sheet-tafsir-box').classList.toggle('show');
        }

        function sheetPlayAudio() {
            const sheet = document.getElementById('ayahSheet');
            playAyatMushaf(sheet.dataset.audio, sheet.dataset.verse);
        }

        function sheetBookmark() {
            const sheet = document.getElementById('ayahSheet');
            saveBookmarkGeneric(sheet.dataset.surah, sheet.dataset.ayat);
        }

        function tapPlayAyahWord(verseKey) {
            const [s, a] = verseKey.split(':').map(Number);
            const data = equranCache[s];
            if (!data) return;
            const ayatObj = data.surah.ayat.find(x => x.nomorAyat === a);
            if (!ayatObj) return;
            playAyatMushaf(ayatObj.audio['05'], verseKey);
        }

        (function bindAyahPress() {
            const page = document.getElementById('mushaf-page');
            const LONG_PRESS_MS = 420;
            let pressTimer = null;
            let longPressFired = false;
            let activeEl = null;

            function clearPress() {
                clearTimeout(pressTimer);
                pressTimer = null;
                if (activeEl) activeEl.classList.remove('pressing');
                activeEl = null;
            }

            page.addEventListener('pointerdown', e => {
                const el = e.target.closest('.ayah-word');
                if (!el) return;
                activeEl = el;
                longPressFired = false;
                el.classList.add('pressing');
                pressTimer = setTimeout(() => {
                    longPressFired = true;
                    el.classList.remove('pressing');
                    if (navigator.vibrate) navigator.vibrate(12);
                    openAyahSheet(el.dataset.verse);
                }, LONG_PRESS_MS);
            });

            page.addEventListener('pointerup', e => {
                const el = e.target.closest('.ayah-word');
                clearTimeout(pressTimer);
                if (activeEl) activeEl.classList.remove('pressing');
                if (el && !longPressFired) {
                    tapPlayAyahWord(el.dataset.verse);
                }
                activeEl = null;
            });

            page.addEventListener('pointerleave', clearPress, true);
            page.addEventListener('pointercancel', clearPress);
            page.addEventListener('contextmenu', e => {
                if (e.target.closest('.ayah-word')) e.preventDefault();
            });
        })();

        // --- KONTROL UKURAN FONT ARAB (hanya mode daftar) ---
        let arabicScale = parseFloat(localStorage.getItem('arabicScale')) || 1;
        document.documentElement.style.setProperty('--arabic-scale', arabicScale);

        function changeFontSize(dir) {
            arabicScale = Math.min(1.6, Math.max(0.75, +(arabicScale + dir * 0.1).toFixed(2)));
            document.documentElement.style.setProperty('--arabic-scale', arabicScale);
            try {
                localStorage.setItem('arabicScale', arabicScale);
            } catch (e) {}
        }

        // --- GESER (SWIPE) UNTUK GANTI HALAMAN ---
        (function bindSwipe() {
            const stage = document.getElementById('mushaf-stage');
            let startX = null,
                startY = null;
            stage.addEventListener('touchstart', e => {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            }, {
                passive: true
            });
            stage.addEventListener('touchend', e => {
                if (startX === null) return;
                const dx = e.changedTouches[0].clientX - startX;
                const dy = e.changedTouches[0].clientY - startY;
                if (Math.abs(dx) > 55 && Math.abs(dx) > Math.abs(dy)) {
                    if (dx < 0) goNextPage();
                    else goPrevPage();
                }
                startX = null;
                startY = null;
            }, {
                passive: true
            });

            let mouseStartX = null;
            stage.addEventListener('mousedown', e => {
                mouseStartX = e.clientX;
            });
            stage.addEventListener('mouseup', e => {
                if (mouseStartX === null) return;
                const dx = e.clientX - mouseStartX;
                if (Math.abs(dx) > 70) {
                    if (dx < 0) goNextPage();
                    else goPrevPage();
                }
                mouseStartX = null;
            });
        })();

        fetchAlQuranData();
    </script>
</body>

</html>