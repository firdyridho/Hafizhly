<?php
session_start();
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
}

/** @var mysqli $conn */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit();
}

function getYouTubeEmbedUrl($url)
{
    if (empty($url)) return false;
    if (preg_match('/youtu.be\/([a-zA-Z0-9_-]+)\??/i', $url, $matches)) return "https://www.youtube.com/embed/" . $matches[1];
    if (preg_match('/youtube.com\/.*v=([a-zA-Z0-9_-]+)/i', $url, $matches)) return "https://www.youtube.com/embed/" . $matches[1];
    return $url;
}

$view_materi_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$materi_detail = null;
$soal_kuis = [];

if ($view_materi_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM tajwid_materi WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $view_materi_id);
    mysqli_stmt_execute($stmt);
    $materi_detail = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($materi_detail) {
        $stmtK = mysqli_prepare($conn, "SELECT * FROM tajwid_kuis WHERE materi_id=?");
        mysqli_stmt_bind_param($stmtK, "i", $view_materi_id);
        mysqli_stmt_execute($stmtK);
        $resK = mysqli_stmt_get_result($stmtK);
        while ($row = mysqli_fetch_assoc($resK)) {
            $soal_kuis[] = $row;
        }
        mysqli_stmt_close($stmtK);
    }
} else {
    $materi_q = mysqli_query($conn, "SELECT * FROM tajwid_materi ORDER BY created_at DESC");
    $daftar_materi = [];
    while ($row = mysqli_fetch_assoc($materi_q)) {
        $daftar_materi[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Belajar Tajwid - Hafizhly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --primary-light: #d1fae5;
            --dark: #0f172a;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --ease: cubic-bezier(.22, 1, .36, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        html {
            -webkit-text-size-adjust: 100%;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: clamp(80px, 20vw, 100px);
            overflow-x: hidden;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: clamp(14px, 4vw, 20px);
            width: 100%;
        }

        .header {
            display: flex;
            align-items: center;
            gap: clamp(10px, 3vw, 15px);
            margin-bottom: clamp(20px, 5vw, 30px);
        }

        .back-btn {
            background: white;
            width: clamp(40px, 10vw, 45px);
            height: clamp(40px, 10vw, 45px);
            flex-shrink: 0;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            transition: 0.2s var(--ease);
        }

        .back-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-title {
            font-size: clamp(1.2rem, 4.5vw, 1.6rem);
            font-weight: 800;
            color: var(--dark);
            flex-grow: 1;
            min-width: 0;
            overflow-wrap: break-word;
        }

        /* ---------- LIST MATERI ---------- */
        .grid-materi {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));
            gap: clamp(14px, 3vw, 20px);
        }

        .materi-card {
            position: relative;
            background: var(--card-bg);
            border-radius: clamp(16px, 3vw, 24px);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            text-decoration: none;
            color: var(--dark);
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: 0.3s var(--ease);
        }

        .materi-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(5, 150, 105, 0.1);
            border-color: var(--primary);
        }

        .mc-cover {
            width: 100%;
            height: clamp(140px, 30vw, 180px);
            object-fit: cover;
            background: var(--border);
        }

        .mc-body {
            padding: clamp(16px, 4vw, 25px);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .mc-title {
            font-size: clamp(1.05rem, 3.5vw, 1.3rem);
            font-weight: 800;
            margin-bottom: clamp(10px, 3vw, 15px);
            line-height: 1.4;
        }

        .mc-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: clamp(0.72rem, 2vw, 0.8rem);
            font-weight: 700;
            margin-top: auto;
        }

        .badge {
            background: var(--bg);
            padding: 6px 14px;
            border-radius: 20px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 20px;
            border: 1px dashed var(--border);
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 12px;
            display: block;
        }

        /* ---------- HEADER (LIST) ---------- */
        .list-header {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 1px solid var(--primary-light);
            color: var(--primary-dark);
            font-size: clamp(.68rem, 2vw, .75rem);
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            padding: 6px 14px;
            border-radius: 999px;
            box-shadow: 0 4px 14px rgba(5, 150, 105, .08);
        }

        .eyebrow i {
            color: var(--primary);
        }

        .list-header .page-title {
            margin-top: 10px;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: clamp(.85rem, 2.5vw, .98rem);
            max-width: 480px;
            line-height: 1.6;
        }

        /* ---------- SHARE ---------- */
        .mc-cover-wrap {
            position: relative;
        }

        .mc-stretched-link {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        .share-chip {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
            width: clamp(34px, 8vw, 38px);
            height: clamp(34px, 8vw, 38px);
            border-radius: 12px;
            border: none;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: .9rem;
            transition: 0.25s var(--ease);
        }

        .share-chip:hover {
            background: var(--primary);
            transform: scale(1.08);
        }

        .share-btn {
            margin-left: auto;
        }

        #hzToast {
            position: fixed;
            left: 50%;
            bottom: clamp(90px, 20vw, 115px);
            transform: translateX(-50%) translateY(16px);
            background: var(--dark);
            color: #fff;
            padding: 12px 22px;
            border-radius: 14px;
            font-size: .85rem;
            font-weight: 600;
            line-height: 1.5;
            box-shadow: 0 16px 34px rgba(0, 0, 0, .25);
            opacity: 0;
            pointer-events: none;
            transition: opacity .35s var(--ease), transform .35s var(--ease);
            z-index: 2000;
            max-width: min(90vw, 360px);
            text-align: center;
        }

        #hzToast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* ---------- REVEAL ANIMATION ---------- */
        .reveal {
            opacity: 0;
            transform: translateY(18px);
            animation: reveal-up .6s var(--ease) forwards;
        }

        @keyframes reveal-up {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .reveal {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }

        /* ---------- DETAIL MATERI ---------- */
        .detail-cover {
            width: 100%;
            height: clamp(160px, 40vw, 350px);
            object-fit: cover;
            border-radius: clamp(16px, 3vw, 24px);
            margin-bottom: clamp(16px, 4vw, 25px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .detail-card {
            background: white;
            padding: clamp(18px, 5vw, 40px);
            border-radius: clamp(16px, 3vw, 24px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            margin-bottom: clamp(18px, 4vw, 25px);
            border: 1px solid var(--border);
            width: 100%;
            overflow: hidden;
        }

        .detail-card h1 {
            font-size: clamp(1.35rem, 5vw, 2.2rem);
            margin-bottom: clamp(16px, 4vw, 25px);
            color: var(--dark);
            font-weight: 800;
            line-height: 1.3;
            overflow-wrap: break-word;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: clamp(12px, 3vw, 20px);
            margin-bottom: clamp(18px, 4vw, 30px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .rich-content {
            line-height: 1.8;
            font-size: clamp(0.95rem, 2vw, 1.1rem);
            color: #334155;
            width: 100%;
            overflow-wrap: break-word;
        }

        .rich-content img {
            max-width: 100%;
            height: auto;
            border-radius: 16px;
            margin: 20px 0;
        }

        .rich-content h2,
        .rich-content h3 {
            margin-top: clamp(20px, 4vw, 30px);
            margin-bottom: 15px;
            color: var(--dark);
            font-weight: 800;
        }

        .table-scroll {
            width: 100%;
            overflow-x: auto;
            margin: 20px 0;
            -webkit-overflow-scrolling: touch;
            border-radius: 10px;
        }

        .rich-content table {
            width: 100%;
            min-width: 420px;
            border-collapse: collapse;
        }

        .rich-content th,
        .rich-content td {
            border: 1px solid var(--border);
            padding: 12px;
            text-align: left;
            font-size: clamp(0.85rem, 2vw, 1rem);
        }

        .rich-content th {
            background: #f8fafc;
            font-weight: 700;
        }

        .action-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(10px, 3vw, 15px);
            margin-top: 10px;
        }

        .btn {
            padding: clamp(13px, 3vw, 16px) clamp(10px, 3vw, 16px);
            border-radius: 16px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-size: clamp(0.85rem, 2.8vw, 1rem);
            transition: 0.3s var(--ease);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            text-align: center;
            line-height: 1.3;
        }

        .btn-pdf {
            background: white;
            color: var(--dark);
            border: 2px solid var(--border);
        }

        .btn-pdf:hover {
            background: #f1f5f9;
        }

        .btn-quiz {
            background: var(--dark);
            color: white;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
        }

        .btn-quiz:hover {
            background: var(--primary);
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
        }

        @media (max-width: 420px) {
            .action-bar {
                grid-template-columns: 1fr;
            }
        }

        /* ---------- QUIZ OVERLAY ---------- */
        #quizOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            height: 100dvh;
            background: #f1f5f9;
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow-y: auto;
        }

        .quiz-header {
            padding: clamp(14px, 3vw, 20px);
            padding-top: max(clamp(14px, 3vw, 20px), env(safe-area-inset-top));
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        .quiz-header button {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .timer-badge {
            background: #fee2e2;
            color: #ef4444;
            padding: clamp(8px, 2vw, 10px) clamp(14px, 3vw, 20px);
            border-radius: 20px;
            font-weight: 800;
            font-size: clamp(0.9rem, 2.8vw, 1.1rem);
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .quiz-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: clamp(18px, 5vw, 40px) clamp(12px, 4vw, 20px);
            width: 100%;
        }

        .quiz-card {
            background: white;
            padding: clamp(20px, 5vw, 40px);
            border-radius: clamp(18px, 4vw, 30px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 600px;
            display: none;
            animation: slideIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        .quiz-card.active {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        .q-img {
            width: 100%;
            max-height: clamp(160px, 35vw, 250px);
            object-fit: contain;
            background: var(--bg);
            border-radius: 16px;
            margin-bottom: clamp(14px, 3vw, 20px);
            border: 1px solid var(--border);
        }

        .q-number {
            font-size: clamp(0.8rem, 2.3vw, 0.95rem);
            color: var(--primary);
            font-weight: 800;
            margin-bottom: clamp(10px, 3vw, 15px);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .q-text {
            font-size: clamp(1.1rem, 4.5vw, 1.4rem);
            font-weight: 800;
            color: var(--dark);
            margin-bottom: clamp(20px, 5vw, 30px);
            line-height: 1.5;
            overflow-wrap: break-word;
        }

        .gemini-option {
            display: block;
            padding: clamp(14px, 3.5vw, 20px);
            border: 2px solid var(--border);
            border-radius: clamp(14px, 3vw, 20px);
            margin-bottom: clamp(10px, 2.5vw, 15px);
            cursor: pointer;
            transition: 0.2s var(--ease);
            font-weight: 700;
            font-size: clamp(0.92rem, 2.8vw, 1.05rem);
            color: #475569;
            position: relative;
            overflow-wrap: break-word;
        }

        .gemini-option:hover {
            border-color: var(--primary-light);
            background: #f8fafc;
            transform: translateY(-2px);
        }

        .gemini-option input[type="radio"] {
            display: none;
        }

        .gemini-option.selected {
            border-color: var(--primary);
            background: #f0fdf4;
            color: var(--primary-dark);
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.15);
            transform: translateY(-2px);
        }

        .quiz-footer {
            padding: clamp(14px, 3vw, 20px);
            padding-bottom: max(clamp(14px, 3vw, 20px), env(safe-area-inset-bottom));
            display: flex;
            justify-content: center;
            background: white;
            position: sticky;
            bottom: 0;
            box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-next {
            background: var(--primary);
            color: white;
            border: none;
            padding: clamp(14px, 3vw, 16px) clamp(24px, 6vw, 40px);
            border-radius: 20px;
            font-weight: 800;
            font-size: clamp(0.95rem, 3vw, 1.1rem);
            cursor: pointer;
            opacity: 0.5;
            pointer-events: none;
            transition: 0.3s var(--ease);
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        .btn-next.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        /* ---------- REVIEW SCREEN ---------- */
        .review-card {
            background: white;
            padding: clamp(20px, 5vw, 40px);
            border-radius: clamp(18px, 4vw, 30px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            display: none;
        }

        .score-circle {
            width: clamp(100px, 25vw, 140px);
            height: clamp(100px, 25vw, 140px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(2rem, 7vw, 3rem);
            font-weight: 800;
            margin: 0 auto clamp(20px, 4vw, 30px) auto;
            color: white;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        #scoreTitle {
            font-size: clamp(1.4rem, 5vw, 2rem);
        }

        .review-item {
            padding: clamp(14px, 3.5vw, 20px);
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 15px;
            text-align: left;
            background: #f8fafc;
        }

        .ri-q {
            font-weight: 700;
            font-size: clamp(1rem, 3vw, 1.1rem);
            margin-bottom: 15px;
            color: var(--dark);
            overflow-wrap: break-word;
        }

        .ri-ans {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-weight: 600;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 8px;
            font-size: clamp(0.88rem, 2.5vw, 1rem);
            overflow-wrap: break-word;
        }

        .ri-correct {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #34d399;
        }

        .ri-wrong {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }
    </style>
</head>

<body>
    <div class="container">

        <?php if (!$view_materi_id): ?>
            <div class="header list-header reveal">
                <span class="eyebrow"><i class="fa-solid fa-book-quran"></i> Kelas Tajwid</span>
                <h1 class="page-title">Modul Pembelajaran</h1>
                <p class="page-subtitle">Pilih materi, kuasai kaidah bacaan Al-Qur'an, lalu uji pemahamanmu lewat kuis interaktif.</p>
            </div>
            <div class="grid-materi">
                <?php if (empty($daftar_materi)): ?>
                    <div class="empty-state reveal"><i class="fa-solid fa-book-open-reader"></i>Belum ada materi tersedia.</div>
                <?php endif; ?>
                <?php foreach ($daftar_materi as $i => $m):
                    $img_src = !empty($m['cover_image']) ? '../uploads/' . htmlspecialchars($m['cover_image']) : 'https://via.placeholder.com/600x400/e2e8f0/64748b?text=Materi+Hafizhly';
                    $delay = min($i, 6) * 0.08;
                ?>
                    <div class="materi-card reveal" style="animation-delay: <?= $delay ?>s;">
                        <a href="tajwid.php?id=<?= (int)$m['id'] ?>" class="mc-stretched-link" aria-label="Buka materi <?= htmlspecialchars($m['judul']) ?>"></a>
                        <div class="mc-cover-wrap">
                            <img src="<?= $img_src ?>" class="mc-cover" alt="Cover" loading="lazy">
                            <button type="button" class="share-chip js-share" title="Bagikan materi ini"
                                data-judul="<?= htmlspecialchars($m['judul'], ENT_QUOTES) ?>"
                                data-cover="<?= htmlspecialchars($img_src, ENT_QUOTES) ?>"
                                data-id="<?= (int)$m['id'] ?>">
                                <i class="fa-solid fa-share-nodes"></i>
                            </button>
                        </div>
                        <div class="mc-body">
                            <div class="mc-title"><?= htmlspecialchars($m['judul']) ?></div>
                            <div class="mc-badges">
                                <?php if ($m['waktu_kuis'] > 0): ?><span class="badge"><i class="fa-regular fa-clock"></i> <?= (int)$m['waktu_kuis'] ?>m Kuis</span><?php endif; ?>
                                <?php if ($m['youtube_url']): ?><span class="badge"><i class="fa-brands fa-youtube" style="color:#ef4444;"></i> Video</span><?php endif; ?>
                                <?php if ($m['pdf_file']): ?><span class="badge"><i class="fa-solid fa-file-pdf" style="color:#3b82f6;"></i> Modul PDF</span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif (!$materi_detail): ?>
            <div class="header">
                <a href="tajwid.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title" style="font-size:1.3rem;">Materi Tidak Ditemukan</h1>
            </div>

        <?php else:
            $detail_cover_src = !empty($materi_detail['cover_image']) ? '../uploads/' . htmlspecialchars($materi_detail['cover_image']) : '';
        ?>
            <div class="header">
                <a href="tajwid.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title" style="font-size:clamp(1.05rem, 4vw, 1.3rem);">Detail Modul</h1>
                <button type="button" class="back-btn share-btn" id="shareMateriBtn" title="Bagikan materi ini"
                    data-judul="<?= htmlspecialchars($materi_detail['judul'], ENT_QUOTES) ?>"
                    data-cover="<?= htmlspecialchars($detail_cover_src, ENT_QUOTES) ?>">
                    <i class="fa-solid fa-share-nodes"></i>
                </button>
            </div>

            <div id="materiToPdf">
                <?php if (!empty($materi_detail['cover_image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($materi_detail['cover_image']) ?>" class="detail-cover reveal" alt="Cover">
                <?php endif; ?>

                <div class="detail-card reveal" style="animation-delay:.08s;">
                    <h1><?= htmlspecialchars($materi_detail['judul']) ?></h1>

                    <?php $embed_url = getYouTubeEmbedUrl($materi_detail['youtube_url']);
                    if ($embed_url): ?>
                        <div class="video-container" data-html2canvas-ignore="true">
                            <iframe src="<?= htmlspecialchars($embed_url) ?>" allowfullscreen loading="lazy"></iframe>
                        </div>
                    <?php endif; ?>

                    <div class="rich-content" id="richContent">
                        <?= $materi_detail['konten'] ?>
                    </div>
                </div>
            </div>

            <div class="action-bar reveal" id="materiActions" style="animation-delay:.16s;">
                <?php if (!empty($materi_detail['pdf_file'])): ?>
                    <a href="../uploads/<?= htmlspecialchars($materi_detail['pdf_file']) ?>" target="_blank" class="btn btn-pdf"><i class="fas fa-file-download"></i> Unduh Modul (PDF)</a>
                <?php else: ?>
                    <button class="btn btn-pdf" onclick="exportPDF()"><i class="fas fa-print"></i> Cetak ke PDF</button>
                <?php endif; ?>

                <?php if (count($soal_kuis) > 0): ?>
                    <button class="btn btn-quiz" onclick="startQuiz()"><i class="fas fa-rocket"></i> Mulai Kuis Evaluasi</button>
                <?php endif; ?>
            </div>

            <!-- LAYAR KUIS INTERAKTIF -->
            <div id="quizOverlay">
                <div class="quiz-header">
                    <button onclick="location.reload()" style="background:none; border:none; font-size:1.5rem; color:var(--text-muted); cursor:pointer;"><i class="fas fa-times"></i></button>
                    <?php if ($materi_detail['waktu_kuis'] > 0): ?>
                        <div class="timer-badge" id="timerDisplay"><i class="fas fa-stopwatch"></i> --:--</div>
                    <?php else: ?>
                        <div class="timer-badge" style="background:#d1fae5; color:#059669;"><i class="fas fa-infinity"></i> Santai</div>
                    <?php endif; ?>
                </div>

                <div class="quiz-container">
                    <div id="slideContainer" style="width:100%; display:flex; justify-content:center;"></div>

                    <!-- LAYAR REVIEW HASIL (Disembunyikan di awal) -->
                    <div class="review-card" id="reviewArea">
                        <div id="scoreCircle" class="score-circle">100</div>
                        <h2 id="scoreTitle" style="margin-bottom:10px; color:var(--dark); text-align:center;">Luar Biasa!</h2>
                        <p id="scoreMessage" style="color:var(--text-muted); margin-bottom:clamp(20px,4vw,30px); text-align:center; font-size:clamp(0.95rem,2.5vw,1.1rem);"></p>

                        <h3 style="margin-bottom:15px; border-bottom:2px solid var(--border); padding-bottom:10px; font-size:clamp(1rem,3vw,1.15rem);">Pembahasan Kuis:</h3>
                        <div id="reviewList"></div>

                        <button class="btn btn-quiz" style="width:100%; margin-top:20px;" onclick="location.reload()"><i class="fas fa-home"></i> Kembali ke Modul</button>
                    </div>
                </div>

                <div class="quiz-footer" id="quizFooter">
                    <button class="btn-next" id="nextBtn" onclick="nextSlide()">Pilih Jawaban Dulu</button>
                </div>
            </div>

            <script>
                // Bungkus semua tabel di dalam konten materi agar bisa di-scroll horizontal di layar kecil
                document.querySelectorAll('#richContent table').forEach(table => {
                    const wrap = document.createElement('div');
                    wrap.className = 'table-scroll';
                    table.parentNode.insertBefore(wrap, table);
                    wrap.appendChild(table);
                });

                // --- Fitur Export PDF Alternatif ---
                function exportPDF() {
                    const element = document.getElementById('materiToPdf');
                    html2pdf().set({
                        margin: 15,
                        filename: 'Materi_Hafizhly.pdf',
                        html2canvas: {
                            scale: 2
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        }
                    }).from(element).save();
                }

                // --- QUIZ ENGINE ---
                const questions = <?= json_encode($soal_kuis) ?>;
                const timeLimit = <?= (int)$materi_detail['waktu_kuis'] ?>;
                let currentSlide = 0;
                let userAnswers = [];
                let timerInterval;

                function startQuiz() {
                    document.body.style.overflow = 'hidden'; // Kunci scroll layar utama
                    document.getElementById('quizOverlay').style.display = 'flex';
                    buildSlides();
                    if (timeLimit > 0) startTimer(timeLimit * 60);
                }

                function escapeHtml(str) {
                    const div = document.createElement('div');
                    div.textContent = str || '';
                    return div.innerHTML;
                }

                function buildSlides() {
                    const container = document.getElementById('slideContainer');
                    container.innerHTML = '';

                    questions.forEach((q, index) => {
                        const slide = document.createElement('div');
                        slide.className = `quiz-card ${index === 0 ? 'active' : ''}`;
                        slide.id = `slide-${index}`;

                        const imgHtml = q.gambar ? `<img src="../uploads/${escapeHtml(q.gambar)}" class="q-img">` : '';

                        slide.innerHTML = `
                        <div class="q-number">Soal ${index + 1} dari ${questions.length}</div>
                        ${imgHtml}
                        <div class="q-text">${escapeHtml(q.pertanyaan)}</div>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'a')"><input type="radio" name="q_${index}" value="a"> A. ${escapeHtml(q.opsi_a)}</label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'b')"><input type="radio" name="q_${index}" value="b"> B. ${escapeHtml(q.opsi_b)}</label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'c')"><input type="radio" name="q_${index}" value="c"> C. ${escapeHtml(q.opsi_c)}</label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'd')"><input type="radio" name="q_${index}" value="d"> D. ${escapeHtml(q.opsi_d)}</label>
                    `;
                        container.appendChild(slide);
                    });
                }

                function selectOption(labelElement, qIndex, answer) {
                    const slide = document.getElementById(`slide-${qIndex}`);
                    slide.querySelectorAll('.gemini-option').forEach(el => el.classList.remove('selected'));
                    labelElement.classList.add('selected');
                    labelElement.querySelector('input').checked = true;
                    userAnswers[qIndex] = answer;

                    const nextBtn = document.getElementById('nextBtn');
                    nextBtn.classList.add('enabled');
                    nextBtn.innerHTML = currentSlide === questions.length - 1 ? 'Kumpulkan Jawaban <i class="fas fa-check-double"></i>' : 'Lanjut ke Soal Berikutnya <i class="fas fa-arrow-right"></i>';
                }

                function nextSlide() {
                    document.getElementById(`slide-${currentSlide}`).classList.remove('active');
                    currentSlide++;
                    const nextBtn = document.getElementById('nextBtn');
                    nextBtn.classList.remove('enabled');
                    nextBtn.innerHTML = 'Pilih Jawaban Dulu';

                    if (currentSlide < questions.length) {
                        document.getElementById(`slide-${currentSlide}`).classList.add('active');
                    } else {
                        submitQuiz();
                    }
                }

                function startTimer(durationSeconds) {
                    let timer = durationSeconds;
                    const display = document.getElementById('timerDisplay');
                    timerInterval = setInterval(() => {
                        let minutes = parseInt(timer / 60, 10);
                        let seconds = parseInt(timer % 60, 10);
                        display.innerHTML = `<i class="fas fa-stopwatch"></i> ${minutes < 10 ? '0'+minutes : minutes}:${seconds < 10 ? '0'+seconds : seconds}`;
                        if (--timer < 0) {
                            clearInterval(timerInterval);
                            submitQuiz();
                        }
                    }, 1000);
                }

                function submitQuiz() {
                    if (timerInterval) clearInterval(timerInterval);
                    document.getElementById('slideContainer').style.display = 'none';
                    document.getElementById('quizFooter').style.display = 'none';

                    const reviewArea = document.getElementById('reviewArea');
                    const reviewList = document.getElementById('reviewList');
                    let skorBenar = 0;
                    let reviewHTML = '';

                    questions.forEach((q, i) => {
                        const uAns = userAnswers[i] || 'kosong';
                        const isBenar = uAns === q.jawaban_benar;
                        if (isBenar) skorBenar++;

                        const opsiMap = {
                            'a': q.opsi_a,
                            'b': q.opsi_b,
                            'c': q.opsi_c,
                            'd': q.opsi_d,
                            'kosong': 'Tidak dijawab'
                        };
                        const textUser = escapeHtml(opsiMap[uAns]);
                        const textBenar = escapeHtml(opsiMap[q.jawaban_benar]);

                        reviewHTML += `
                        <div class="review-item">
                            <div class="ri-q">${i+1}. ${escapeHtml(q.pertanyaan)}</div>
                            ${isBenar 
                                ? `<div class="ri-ans ri-correct"><i class="fas fa-check-circle"></i> Jawabanmu Benar: ${textUser}</div>` 
                                : `<div class="ri-ans ri-wrong"><i class="fas fa-times-circle"></i> Jawabanmu: ${textUser}</div>
                                   <div class="ri-ans ri-correct"><i class="fas fa-check-circle"></i> Jawaban Tepat: ${textBenar}</div>`
                            }
                        </div>
                    `;
                    });

                    const nilaiAkhir = questions.length ? Math.round((skorBenar / questions.length) * 100) : 0;
                    const circle = document.getElementById('scoreCircle');
                    circle.innerText = nilaiAkhir;

                    if (nilaiAkhir >= 80) {
                        circle.style.background = 'linear-gradient(135deg, #059669, #10b981)';
                        document.getElementById('scoreTitle').innerText = "Hebat Sekali! 🎉";
                    } else if (nilaiAkhir >= 50) {
                        circle.style.background = 'linear-gradient(135deg, #f59e0b, #fbbf24)';
                        document.getElementById('scoreTitle').innerText = "Lumayan Bagus! 👍";
                    } else {
                        circle.style.background = 'linear-gradient(135deg, #ef4444, #f87171)';
                        document.getElementById('scoreTitle').innerText = "Belajar Lagi Ya! 💪";
                    }

                    document.getElementById('scoreMessage').innerText = `Kamu berhasil menjawab ${skorBenar} dari ${questions.length} soal dengan benar.`;
                    reviewList.innerHTML = reviewHTML;
                    reviewArea.style.display = 'block';

                    document.getElementById('quizOverlay').scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            </script>
        <?php endif; ?>
    </div>

    <div id="hzToast"></div>

    <script>
        // ============ FITUR BAGIKAN MATERI ============
        // Membagikan sampul materi + judul + ajakan belajar yang rapi,
        // lewat share sheet asli HP (WhatsApp, dsb) kalau didukung,
        // atau fallback salin ke clipboard.
        async function shareMateri(judul, coverUrl, pageUrl) {
            const teks = `📖✨ Ayo belajar "${judul}" bareng aku di Hafizhly!\nYuk kita sama-sama perbaiki bacaan Al-Qur'an, sedikit demi sedikit menuju bacaan yang makin tartil. 🌿🕌`;
            const shareData = {
                title: `Ayo belajar ${judul} di Hafizhly`,
                text: teks,
                url: pageUrl
            };

            try {
                if (coverUrl && window.navigator.canShare) {
                    try {
                        const resp = await fetch(coverUrl);
                        const blob = await resp.blob();
                        const file = new File([blob], 'cover-tajwid.jpg', {
                            type: blob.type || 'image/jpeg'
                        });
                        if (navigator.canShare({
                                files: [file]
                            })) {
                            shareData.files = [file];
                        }
                    } catch (imgErr) {
                        // Gagal ambil gambar, tetap lanjut bagikan teks + link saja
                    }
                }

                if (navigator.share) {
                    await navigator.share(shareData);
                    return;
                }
                await copyShareFallback(teks, pageUrl);
            } catch (err) {
                if (err && err.name === 'AbortError') return; // dibatalkan user sendiri
                await copyShareFallback(teks, pageUrl);
            }
        }

        async function copyShareFallback(teks, pageUrl) {
            try {
                await navigator.clipboard.writeText(`${teks}\n${pageUrl}`);
                showToast('Ajakan belajar & tautannya disalin! Tinggal tempel ke chat kamu 📋');
            } catch (e2) {
                showToast('Yah, gagal membagikan. Coba lagi ya.');
            }
        }

        function showToast(msg) {
            const toast = document.getElementById('hzToast');
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.add('show');
            clearTimeout(window.__hzToastTimer);
            window.__hzToastTimer = setTimeout(() => toast.classList.remove('show'), 3200);
        }

        // Tombol share di kartu daftar materi
        document.querySelectorAll('.js-share').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const id = this.dataset.id;
                const judul = this.dataset.judul;
                const cover = this.dataset.cover;
                const pageUrl = new URL(`tajwid.php?id=${id}`, location.href).href;
                shareMateri(judul, cover, pageUrl);
            });
        });

        // Tombol share di halaman detail
        const shareMateriBtn = document.getElementById('shareMateriBtn');
        if (shareMateriBtn) {
            shareMateriBtn.addEventListener('click', function() {
                shareMateri(this.dataset.judul, this.dataset.cover, location.href);
            });
        }
    </script>

    <?php if (!$view_materi_id) include '../components/nav.php'; ?>
</body>

</html>