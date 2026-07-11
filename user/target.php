<?php
session_start();
// Tampung semua output di buffer. Ini penting supaya kalau ada PHP notice/warning
// yang ke-print duluan (misal dari koneksi DB), itu tidak ikut nempel di depan
// response JSON dan bikin JS gagal parsing (yang muncul sebagai "periksa koneksi").
ob_start();

// Kalau ada error/exception fatal (misal query SQL error, mysqli exception mode di PHP 8+),
// tangkap di sini supaya response tetap JSON yang jelas, bukan HTTP 500 kosong yang
// bikin bingung ("respon server bukan JSON valid" tanpa keterangan apa-apa).
set_exception_handler(function ($e) {
    error_log('target.php uncaught exception: ' . $e->getMessage());
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit();
});

require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$current_date = date('Y-m-d');

// --- 1. AUTO-CREATE TABEL YANG DIBUTUHKAN ---
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS user_targets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    tipe_target VARCHAR(20) NOT NULL,
    jumlah_target INT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_kat (user_id, kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS user_todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    task_time TIME NOT NULL,
    task_date DATE NOT NULL,
    is_completed TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Tabel user_todos di database kamu sudah ada tapi belum punya kolom kategori & catatan
// (dipakai untuk kategori aktivitas & catatan opsional di planner). ADD COLUMN IF NOT EXISTS
// aman dijalankan berkali-kali setiap load halaman, tidak akan error walau kolomnya sudah ada.
mysqli_query($conn, "ALTER TABLE user_todos ADD COLUMN IF NOT EXISTS kategori VARCHAR(50) DEFAULT 'Tilawah'");
mysqli_query($conn, "ALTER TABLE user_todos ADD COLUMN IF NOT EXISTS catatan TEXT NULL");

// NOTE: Tabel bookmark memakai nama & kolom YANG SAMA dengan baca.php dan skema
// database kamu (tabel "bookmark", kolom surah_nomor & ayat) supaya setiap ayat
// yang ditandai lewat baca.php otomatis kebaca & tersinkron di halaman ini.
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS bookmark (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    surah_nomor INT NOT NULL,
    ayat INT NOT NULL,
    catatan VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// --- 2. AJAX HANDLERS ---
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'save_target') {
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
        $jumlah = (int) $_POST['jumlah'];

        $cek = mysqli_query($conn, "SELECT id FROM user_targets WHERE user_id='$user_id' AND kategori='$kategori'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE user_targets SET tipe_target='$tipe', jumlah_target='$jumlah' WHERE user_id='$user_id' AND kategori='$kategori'");
        } else {
            mysqli_query($conn, "INSERT INTO user_targets (user_id, kategori, tipe_target, jumlah_target) VALUES ('$user_id', '$kategori', '$tipe', '$jumlah')");
        }
        exit('ok');
    }
    if ($_POST['action'] == 'add_todo') {
        $task_raw = trim($_POST['task']);
        $time_raw = $_POST['time'];
        $date_raw = $_POST['date'];
        $kat_raw = $_POST['kategori'];
        $notes_raw = trim($_POST['notes']);

        $task = mysqli_real_escape_string($conn, $task_raw);
        $time = mysqli_real_escape_string($conn, $time_raw);
        $date = mysqli_real_escape_string($conn, $date_raw);
        $kat = mysqli_real_escape_string($conn, $kat_raw);
        $notes = mysqli_real_escape_string($conn, $notes_raw);

        mysqli_query($conn, "INSERT INTO user_todos (user_id, task_name, task_time, task_date, kategori, catatan) VALUES ('$user_id', '$task', '$time', '$date', '$kat', '$notes')");
        $new_id = mysqli_insert_id($conn);

        ob_clean(); // buang output nyasar (notice/warning) yang mungkin sudah tertampung
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'ok',
            'todo' => [
                'id' => $new_id,
                'task_name' => $task_raw,
                'task_time_label' => date('H:i', strtotime($time_raw)),
                'kategori' => $kat_raw,
                'catatan' => $notes_raw,
                'is_completed' => 0
            ]
        ]);
        exit();
    }
    if ($_POST['action'] == 'toggle_todo') {
        $todo_id = (int) $_POST['todo_id'];
        $status = (int) $_POST['status'];
        mysqli_query($conn, "UPDATE user_todos SET is_completed='$status' WHERE id='$todo_id' AND user_id='$user_id'");
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
        exit();
    }
    if ($_POST['action'] == 'delete_todo') {
        $todo_id = (int) $_POST['todo_id'];
        mysqli_query($conn, "DELETE FROM user_todos WHERE id='$todo_id' AND user_id='$user_id'");
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
        exit();
    }
}

// --- 3. AMBIL DATA TARGET & SINKRONISASI BOOKMARK (TILAWAH) ---
$q_target = mysqli_query($conn, "SELECT * FROM user_targets WHERE user_id='$user_id' AND kategori='tilawah'");
if (mysqli_num_rows($q_target) > 0) {
    $row_tgt = mysqli_fetch_assoc($q_target);
    $tipe_target = $row_tgt['tipe_target'];
    $jumlah_target = (int) $row_tgt['jumlah_target'];
} else {
    $tipe_target = 'harian';
    $jumlah_target = 10;
}

$q_last_mutabaah = mysqli_query($conn, "SELECT surah, ayah_end FROM mutabaah WHERE user_id='$user_id' ORDER BY activity_date DESC, activity_time DESC LIMIT 1");
if (mysqli_num_rows($q_last_mutabaah) > 0) {
    $row_lm = mysqli_fetch_assoc($q_last_mutabaah);
    $start_surah = $row_lm['surah'];
    $start_ayat = $row_lm['ayah_end'] + 1;
} else {
    $start_surah = 1;
    $start_ayat = 1;
}

// Ambil bookmark TERBARU milik user dari tabel "bookmark" (dipakai bersama baca.php)
// supaya begitu ayat ditandai di baca.php, progress & "Current Bookmark" di sini
// otomatis ikut ter-update tanpa perlu aksi tambahan.
$q_bookmark = mysqli_query($conn, "SELECT surah_nomor, ayat FROM bookmark WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
if ($q_bookmark && mysqli_num_rows($q_bookmark) > 0) {
    $row_bm = mysqli_fetch_assoc($q_bookmark);
    $end_surah = $row_bm['surah_nomor'];
    $end_ayat = $row_bm['ayat'];
} else {
    $end_surah = $start_surah;
    $end_ayat = $start_ayat - 1;
}

// --- Hitung progress ayat sesuai periode target (harian/mingguan/bulanan/tahunan) ---
// Progress dihitung dari total ayat Tilawah yang sudah dicatat di Mutabaah pada periode
// yang sedang berjalan. (Cara lama membandingkan posisi bookmark vs entri mutabaah
// terakhir, tapi itu gagal karena kolom "surah" di tabel mutabaah berisi NAMA surah
// (teks, mis. "Al-Baqarah") sedangkan bookmark pakai NOMOR surah (angka, mis. 2) -
// keduanya tidak pernah bisa dibandingkan dengan benar, jadi progress selalu macet di 0.)
switch ($tipe_target) {
    case 'mingguan':
        $period_condition = "YEARWEEK(activity_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'bulanan':
        $period_condition = "YEAR(activity_date) = YEAR(CURDATE()) AND MONTH(activity_date) = MONTH(CURDATE())";
        break;
    case 'tahunan':
        $period_condition = "YEAR(activity_date) = YEAR(CURDATE())";
        break;
    case 'harian':
    default:
        $period_condition = "activity_date = CURDATE()";
        break;
}

$q_progress = mysqli_query($conn, "SELECT SUM(ayah_end - ayah_start + 1) as total_progress FROM mutabaah WHERE user_id='$user_id' AND activity_type='tilawah' AND $period_condition");
$row_progress = mysqli_fetch_assoc($q_progress);
$progress_ayat = (int) ($row_progress['total_progress'] ?? 0);
if ($progress_ayat < 0) $progress_ayat = 0;

$sisa_ayat = $jumlah_target - $progress_ayat;
if ($sisa_ayat < 0) $sisa_ayat = 0;

$persentase = ($jumlah_target > 0) ? ($progress_ayat / $jumlah_target) * 100 : 0;
if ($persentase > 100) $persentase = 100;
$target_tercapai = ($progress_ayat >= $jumlah_target && $jumlah_target > 0);

// --- 4. DATA STATISTIK GLOBAL ---
$q_all = mysqli_query($conn, "SELECT SUM(ayah_end - ayah_start + 1) as total_all, COUNT(DISTINCT activity_date) as active_days FROM mutabaah WHERE user_id = '$user_id'");
$row_all = mysqli_fetch_assoc($q_all);
$total_ayat_hafal = (int)$row_all['total_all'];
$hari_aktif = (int)$row_all['active_days'];
$total_halaman = floor($total_ayat_hafal / 15);
$total_juz = floor($total_ayat_hafal / 300);

// --- 5. DATA TODO LIST HARI INI ---
$q_todos = mysqli_query($conn, "SELECT * FROM user_todos WHERE user_id='$user_id' AND task_date='$current_date' ORDER BY task_time ASC");
$todos = [];
while ($row = mysqli_fetch_assoc($q_todos)) {
    $todos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target & Planner - Hafizhly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #059669;
            --primary-light: #d1fae5;
            --dark: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --spacing: 24px;
            --radius: 20px;
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
            -webkit-tap-highlight-color: transparent;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        /* General Card */
        .card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border);
            margin-bottom: var(--spacing);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-top: 10px;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* 1. Category Section */
        .cat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .cat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 15px 5px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .cat-card.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.2);
        }

        .cat-card:not(.active) {
            color: var(--text-muted);
        }

        .cat-card i {
            font-size: 1.3rem;
        }

        .cat-card span {
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        /* 2. Segmented Control */
        .segmented-control {
            display: flex;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 14px;
            margin-bottom: var(--spacing);
        }

        .segment-btn {
            flex: 1;
            padding: 10px 0;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 10px;
            transition: 0.3s;
        }

        .segment-btn.active {
            color: white;
            background: var(--primary);
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.2);
        }

        /* 3. Active Target Card */
        .target-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .target-badge {
            background: var(--primary-light);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-icon {
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
            transition: 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon:hover {
            color: var(--primary);
        }

        .target-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .progress-container {
            margin: 25px 0;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-muted);
        }

        .progress-bar-bg {
            width: 100%;
            height: 12px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 10px;
            width: <?= $persentase ?>%;
            transition: 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .progress-footer {
            font-size: 0.85rem;
            color: #d97706;
            font-weight: 600;
            margin-top: 10px;
            text-align: right;
        }

        .bookmark-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .bookmark-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: white;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .bookmark-text {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 3px;
        }

        .bookmark-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .btn-edit-mutabaah {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 16px;
            padding: 13px;
            border-radius: 14px;
            background: var(--dark);
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.2s;
        }

        .btn-edit-mutabaah:hover {
            background: #0f172a;
        }

        /* Auto Mutabaah Success Card */
        .success-card {
            background: linear-gradient(135deg, var(--primary), #10b981);
            color: white;
            padding: 24px;
            border-radius: var(--radius);
            text-align: center;
            margin-bottom: var(--spacing);
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
            display: <?= $target_tercapai ? 'block' : 'none' ?>;
        }

        .success-icon {
            font-size: 3rem;
            margin-bottom: 15px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        .success-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .success-desc {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .btn-white {
            display: inline-block;
            background: white;
            color: var(--primary);
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.95rem;
            transition: 0.2s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-white:hover {
            transform: translateY(-2px);
        }

        /* 4. Chart Section */
        .chart-container {
            position: relative;
            height: 230px;
            width: 100%;
            margin-top: 15px;
        }

        /* 5. Statistics */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: var(--spacing);
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 16px;
            border: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 6px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* 6. Todo Planner */
        .planner-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 15px;
        }

        .planner-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
        }

        .todo-form {
            background: #f8fafc;
            padding: 16px;
            border-radius: 16px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
        }

        .form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }

        .form-control {
            flex: 1;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.9rem;
            outline: none;
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        .btn-submit-todo {
            width: 100%;
            background: var(--dark);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-submit-todo:hover {
            background: #0f172a;
        }

        .btn-submit-todo:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .todo-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .todo-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            transition: 0.2s;
        }

        .todo-item:hover {
            border-color: var(--primary-light);
            background: #f8fafc;
        }

        .todo-item.completed .todo-task {
            text-decoration: line-through;
        }

        .todo-item.completed {
            opacity: 0.6;
            background: #f1f5f9;
        }

        .todo-checkbox {
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: var(--primary);
            margin-top: 2px;
        }

        .todo-content {
            flex: 1;
        }

        .todo-task {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .todo-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .todo-badge {
            background: #e2e8f0;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 600;
            color: var(--dark);
        }

        .todo-notes {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 8px;
            font-style: italic;
        }

        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            transition: 0.3s;
        }

        .modal-overlay.show {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 24px;
            width: 90%;
            max-width: 400px;
            transform: scale(0.9);
            transition: 0.3s;
        }

        .modal-overlay.show .modal-content {
            transform: scale(1);
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 500;
        }

        .radio-label:hover {
            background: #f8fafc;
        }

        .radio-label input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        .radio-label.active {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 600;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-cancel {
            flex: 1;
            padding: 14px;
            background: #f1f5f9;
            color: var(--dark);
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-save {
            flex: 1;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <div class="page-title">Target Planner</div>
            <i class="far fa-bell" style="font-size:1.2rem; color:var(--text-muted);"></i>
        </div>

        <!-- 1. Category Section -->
        <div class="cat-grid">
            <div class="cat-card active">
                <i class="fas fa-book-open"></i><span>Tilawah</span>
            </div>
            <div class="cat-card" onclick="alert('Hafalan segera hadir')">
                <i class="fas fa-brain"></i><span>Hafalan</span>
            </div>
            <div class="cat-card" onclick="alert('Murojaah segera hadir')">
                <i class="fas fa-sync-alt"></i><span>Murojaah</span>
            </div>
            <div class="cat-card" onclick="alert('Tajwid segera hadir')">
                <i class="fas fa-quran"></i><span>Tajwid</span>
            </div>
        </div>

        <!-- 2. Segmented Control -->
        <div class="segmented-control">
            <div class="segment-btn active" onclick="switchTab('hari', this)">Hari</div>
            <div class="segment-btn" onclick="switchTab('minggu', this)">Minggu</div>
            <div class="segment-btn" onclick="switchTab('bulan', this)">Bulan</div>
            <div class="segment-btn" onclick="switchTab('tahun', this)">Tahun</div>
        </div>

        <!-- 7. Auto Mutabaah Reminder (Success Card) -->
        <?php if ($target_tercapai): ?>
            <div class="success-card">
                <div class="success-icon"><i class="fas fa-medal"></i></div>
                <div class="success-title">🎉 Congratulations!</div>
                <div class="success-desc">Target Tilawah hari ini telah tercapai. <br>Semoga Allah memberkahi bacaanmu.</div>
                <a href="mutabaah.php?auto=1&kategori=Tilawah&surah=<?= $start_surah ?>&start=<?= $start_ayat ?>&end=<?= $end_ayat ?>" class="btn-white">
                    <i class="fas fa-check-circle"></i> Record to Mutabaah
                </a>
            </div>
        <?php endif; ?>

        <!-- 3. Active Target Card -->
        <div class="card">
            <div class="target-card-header">
                <div class="target-badge">Repeat: Every <?= ucfirst($tipe_target) ?></div>
                <div class="header-actions">
                    <a href="mutabaah.php" class="btn-icon" title="Edit Mutabaah"><i class="fas fa-edit"></i></a>
                    <button class="btn-icon" onclick="openTargetModal()"><i class="fas fa-sliders-h"></i></button>
                </div>
            </div>

            <div class="target-title">Read <?= $jumlah_target ?> Verses</div>

            <div class="progress-container">
                <div class="progress-header">
                    <span>Progress</span>
                    <span><?= $progress_ayat ?> / <?= $jumlah_target ?> verses (<?= round($persentase) ?>%)</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill"></div>
                </div>
                <?php if ($sisa_ayat > 0): ?>
                    <div class="progress-footer"><?= $sisa_ayat ?> verses left to complete target</div>
                <?php endif; ?>
            </div>

            <!-- Current Bookmark -->
            <div class="bookmark-box">
                <div class="bookmark-icon"><i class="fas fa-bookmark"></i></div>
                <div>
                    <div class="bookmark-text">Current Bookmark</div>
                    <div class="bookmark-value">
                        <span id="bm-surah-label">Surah <?= $end_surah ?></span>, Verse <?= $end_ayat ?>
                    </div>
                </div>
            </div>

            <a href="mutabaah.php" class="btn-edit-mutabaah">
                <i class="fas fa-pen"></i> Edit Mutabaah
            </a>
        </div>

        <!-- 4. Progress Section (Chart) -->
        <div class="card">
            <h3 style="font-size:1.1rem; color:var(--dark); margin-bottom:5px;">Progress Target</h3>
            <div class="chart-container">
                <canvas id="targetChart"></canvas>
            </div>
        </div>

        <!-- 5. Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Verses</div>
                <div class="stat-value"><?= number_format($total_ayat_hafal, 0, ',', '.') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pages</div>
                <div class="stat-value"><?= $total_halaman ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Juz</div>
                <div class="stat-value"><?= $total_juz ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Days</div>
                <div class="stat-value"><?= $hari_aktif ?></div>
            </div>
        </div>

        <!-- 6. Todo Planner -->
        <div class="card">
            <div class="planner-header">
                <div class="planner-title">Today's Planner</div>
                <div style="font-size:0.85rem; color:var(--text-muted); font-weight:600;"><i class="far fa-calendar-alt"></i> <?= date('d M Y') ?></div>
            </div>

            <form class="todo-form" id="todoForm" onsubmit="addTodo(event)">
                <input type="text" id="todoTask" class="form-control" placeholder="Activity name (e.g., Tilawah after Subuh)" required style="margin-bottom:12px; width: 100%;">

                <div class="form-row">
                    <input type="date" id="todoDate" class="form-control" value="<?= $current_date ?>" required>
                    <input type="time" id="todoTime" class="form-control" required>
                </div>

                <div class="form-row">
                    <select id="todoCat" class="form-control">
                        <option value="Tilawah">📖 Tilawah</option>
                        <option value="Hafalan">🧠 Hafalan</option>
                        <option value="Murojaah">🔁 Murojaah</option>
                        <option value="Tajwid">📚 Tajwid</option>
                    </select>
                </div>

                <input type="text" id="todoNotes" class="form-control" placeholder="Add notes (optional)..." style="margin-bottom:15px; width: 100%;">

                <button type="submit" class="btn-submit-todo" id="todoSubmitBtn">Add Activity</button>
            </form>

            <div class="todo-list" id="todoList">
                <?php if (empty($todos)): ?>
                    <div class="todo-empty" id="todoEmpty" style="text-align:center; padding:20px; color:var(--text-muted); font-size:0.9rem;">No activities planned for today.</div>
                <?php else: ?>
                    <?php foreach ($todos as $t): ?>
                        <div class="todo-item <?= $t['is_completed'] ? 'completed' : '' ?>" id="todo-<?= $t['id'] ?>" data-time="<?= date('H:i', strtotime($t['task_time'])) ?>">
                            <input type="checkbox" class="todo-checkbox" <?= $t['is_completed'] ? 'checked' : '' ?> onchange="toggleTodo(<?= $t['id'] ?>, this.checked)">
                            <div class="todo-content">
                                <div class="todo-task"><?= htmlspecialchars($t['task_name']) ?></div>
                                <div class="todo-meta">
                                    <span class="todo-badge"><?= date('H:i', strtotime($t['task_time'])) ?></span>
                                    <span>• <?= htmlspecialchars($t['kategori']) ?></span>
                                </div>
                                <?php if (!empty($t['catatan'])): ?>
                                    <div class="todo-notes">"<?= htmlspecialchars($t['catatan']) ?>"</div>
                                <?php endif; ?>
                            </div>
                            <button class="btn-icon" onclick="deleteTodo(<?= $t['id'] ?>)" style="color:#ef4444;"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- 8. Consistent Target Mode (Modal) -->
    <div class="modal-overlay" id="targetModal">
        <div class="modal-content">
            <div class="modal-title">Consistent Target Mode</div>

            <label style="font-size:0.85rem; font-weight:600; color:var(--text-muted); margin-bottom:10px; display:block;">Target Verses</label>
            <input type="number" id="modalJumlah" class="form-control" value="<?= $jumlah_target ?>" min="1" style="width:100%; margin-bottom:20px;">

            <label style="font-size:0.85rem; font-weight:600; color:var(--text-muted); margin-bottom:10px; display:block;">Repeat Target</label>
            <div class="radio-group">
                <label class="radio-label <?= $tipe_target == 'harian' ? 'active' : '' ?>" onclick="selectRadio(this, 'harian')">
                    <input type="radio" name="repeat" value="harian" <?= $tipe_target == 'harian' ? 'checked' : '' ?>> Every Day
                </label>
                <label class="radio-label <?= $tipe_target == 'mingguan' ? 'active' : '' ?>" onclick="selectRadio(this, 'mingguan')">
                    <input type="radio" name="repeat" value="mingguan" <?= $tipe_target == 'mingguan' ? 'checked' : '' ?>> Every Week
                </label>
                <label class="radio-label <?= $tipe_target == 'bulanan' ? 'active' : '' ?>" onclick="selectRadio(this, 'bulanan')">
                    <input type="radio" name="repeat" value="bulanan" <?= $tipe_target == 'bulanan' ? 'checked' : '' ?>> Every Month
                </label>
                <label class="radio-label <?= $tipe_target == 'tahunan' ? 'active' : '' ?>" onclick="selectRadio(this, 'tahunan')">
                    <input type="radio" name="repeat" value="tahunan" <?= $tipe_target == 'tahunan' ? 'checked' : '' ?>> Every Year
                </label>
            </div>

            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeTargetModal()">Cancel</button>
                <button class="btn-save" onclick="saveTarget()">Save Changes</button>
            </div>
        </div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script>
        // Fetch Surah Name for Bookmark
        fetch(`https://equran.id/api/v2/surat/<?= $end_surah ?>`)
            .then(r => r.json()).then(d => {
                document.getElementById('bm-surah-label').innerText = 'Surah ' + d.data.namaLatin;
            });

        // --- CHART JS SETUP ---
        const ctx = document.getElementById('targetChart').getContext('2d');
        const chartData = {
            hari: {
                labels: ['06:00', '12:00', '15:00', '18:00', '21:00'],
                data: [0, <?= floor($progress_ayat / 3) ?>, <?= floor($progress_ayat / 2) ?>, <?= $progress_ayat ?>, <?= $progress_ayat ?>]
            },
            minggu: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                data: [5, 8, 2, 10, 15, 7, <?= $progress_ayat ?>]
            },
            bulan: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                data: [20, 35, 28, 45]
            },
            tahun: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                data: [120, 150, 130, 200, 250, 280]
            }
        };

        let progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.hari.labels,
                datasets: [{
                    label: 'Verses',
                    data: chartData.hari.data,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#059669',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                family: 'Inter',
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9',
                            borderDash: [4, 4],
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                family: 'Inter',
                                size: 11
                            },
                            stepSize: 5
                        }
                    }
                }
            }
        });

        function switchTab(period, element) {
            document.querySelectorAll('.segment-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            progressChart.data.labels = chartData[period].labels;
            progressChart.data.datasets[0].data = chartData[period].data;
            progressChart.update();
        }

        // --- TARGET MODAL JS (TIDAK DIUBAH) ---
        function openTargetModal() {
            const m = document.getElementById('targetModal');
            m.style.display = 'flex';
            setTimeout(() => m.classList.add('show'), 10);
        }

        function closeTargetModal() {
            const m = document.getElementById('targetModal');
            m.classList.remove('show');
            setTimeout(() => m.style.display = 'none', 300);
        }

        function selectRadio(el, val) {
            document.querySelectorAll('.radio-label').forEach(lbl => lbl.classList.remove('active'));
            el.classList.add('active');
            el.querySelector('input').checked = true;
        }

        function saveTarget() {
            const jumlah = document.getElementById('modalJumlah').value;
            const tipe = document.querySelector('input[name="repeat"]:checked').value;

            const fd = new URLSearchParams();
            fd.append('action', 'save_target');
            fd.append('kategori', 'tilawah');
            fd.append('tipe', tipe);
            fd.append('jumlah', jumlah);

            fetch('target.php', {
                method: 'POST',
                body: fd
            }).then(() => location.reload());
        }

        // ==========================================================
        // --- TODO PLANNER (REAKTIF, TANPA RELOAD - ala Vue/Next) ---
        // ==========================================================
        const catEmoji = {
            Tilawah: '📖',
            Hafalan: '🧠',
            Murojaah: '🔁',
            Tajwid: '📚'
        };

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [m]));
        }

        // Helper request: ambil response sebagai teks dulu, baru coba parse JSON.
        // Kalau gagal parse, tampilkan isi respon aslinya di console supaya
        // kelihatan penyebab sebenarnya (bukan cuma dikira "koneksi putus").
        function postAction(fd) {
            return fetch('target.php', {
                    method: 'POST',
                    body: fd
                })
                .then(async res => {
                    const raw = await res.text();
                    try {
                        return JSON.parse(raw);
                    } catch (err) {
                        console.error('Respon server bukan JSON valid. Isi respon:', raw);
                        throw new Error('respon_tidak_valid');
                    }
                });
        }

        // "Komponen" render satu item todo (mirip render function Vue/React)
        function renderTodoItem(todo) {
            const notesHtml = todo.catatan ?
                `<div class="todo-notes">"${escapeHtml(todo.catatan)}"</div>` :
                '';
            return `<div class="todo-item ${todo.is_completed == 1 ? 'completed' : ''}" id="todo-${todo.id}" data-time="${todo.task_time_label}">
                <input type="checkbox" class="todo-checkbox" ${todo.is_completed == 1 ? 'checked' : ''} onchange="toggleTodo(${todo.id}, this.checked)">
                <div class="todo-content">
                    <div class="todo-task">${escapeHtml(todo.task_name)}</div>
                    <div class="todo-meta">
                        <span class="todo-badge">${todo.task_time_label}</span>
                        <span>• ${escapeHtml(todo.kategori)}</span>
                    </div>
                    ${notesHtml}
                </div>
                <button class="btn-icon" onclick="deleteTodo(${todo.id})" style="color:#ef4444;"><i class="fas fa-trash-alt"></i></button>
            </div>`;
        }

        function addTodo(e) {
            e.preventDefault();
            const btn = document.getElementById('todoSubmitBtn');
            const task = document.getElementById('todoTask').value.trim();
            const time = document.getElementById('todoTime').value;
            const date = document.getElementById('todoDate').value;
            const kat = document.getElementById('todoCat').value;
            const notes = document.getElementById('todoNotes').value.trim();

            if (!task || !time || !date) return;

            const fd = new URLSearchParams();
            fd.append('action', 'add_todo');
            fd.append('task', task);
            fd.append('time', time);
            fd.append('date', date);
            fd.append('kategori', kat);
            fd.append('notes', notes);

            btn.disabled = true;
            btn.innerText = 'Menambahkan...';

            postAction(fd)
                .then(data => {
                    if (data.status !== 'ok') {
                        alert('Gagal menambahkan aktivitas: ' + (data.message || 'tidak diketahui'));
                        return;
                    }
                    const emptyEl = document.getElementById('todoEmpty');
                    if (emptyEl) emptyEl.remove();

                    const list = document.getElementById('todoList');
                    list.insertAdjacentHTML('beforeend', renderTodoItem(data.todo));

                    // urutkan ulang list berdasarkan jam, seperti computed list di Vue
                    const items = Array.from(list.children).filter(el => el.classList.contains('todo-item'));
                    items.sort((a, b) => a.dataset.time.localeCompare(b.dataset.time));
                    items.forEach(el => list.appendChild(el));

                    document.getElementById('todoForm').reset();
                    document.getElementById('todoDate').value = '<?= $current_date ?>';
                })
                .catch(err => {
                    if (err.message === 'respon_tidak_valid') {
                        alert('Aktivitas mungkin gagal tersimpan: server mengembalikan respon tidak valid. Buka Console (F12) untuk lihat detailnya, atau muat ulang halaman untuk cek apakah datanya sebenarnya tersimpan.');
                    } else {
                        alert('Gagal menambahkan aktivitas. Periksa koneksi internet lalu coba lagi.');
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = 'Add Activity';
                });
        }

        function toggleTodo(id, isChecked) {
            const item = document.getElementById('todo-' + id);
            // update optimis dulu biar terasa instan, baru dikonfirmasi ke server
            if (item) item.classList.toggle('completed', isChecked);

            const fd = new URLSearchParams();
            fd.append('action', 'toggle_todo');
            fd.append('todo_id', id);
            fd.append('status', isChecked ? 1 : 0);

            postAction(fd)
                .then(data => {
                    if (data.status !== 'ok' && item) {
                        // rollback kalau gagal
                        item.classList.toggle('completed', !isChecked);
                    }
                })
                .catch(() => {
                    if (item) item.classList.toggle('completed', !isChecked);
                });
        }

        function deleteTodo(id) {
            if (!confirm('Hapus jadwal ini?')) return;

            const fd = new URLSearchParams();
            fd.append('action', 'delete_todo');
            fd.append('todo_id', id);

            postAction(fd)
                .then(data => {
                    if (data.status !== 'ok') return;
                    const item = document.getElementById('todo-' + id);
                    if (item) item.remove();

                    const list = document.getElementById('todoList');
                    const remaining = list.querySelectorAll('.todo-item').length;
                    if (remaining === 0) {
                        list.innerHTML = '<div class="todo-empty" id="todoEmpty" style="text-align:center; padding:20px; color:var(--text-muted); font-size:0.9rem;">No activities planned for today.</div>';
                    }
                })
                .catch(err => {
                    if (err.message === 'respon_tidak_valid') {
                        alert('Item mungkin sudah terhapus tapi respon server tidak valid. Muat ulang halaman untuk memastikan.');
                    } else {
                        alert('Gagal menghapus. Periksa koneksi internet lalu coba lagi.');
                    }
                });
        }
    </script>
</body>

</html>