<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ==========================================
// AJAX HANDLER: SIMPAN, EDIT & HAPUS
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    $action = $_POST['ajax_action'];

    if ($action === 'simpan_aktivitas') {
        $type = mysqli_real_escape_string($conn, $_POST['activity_type']);
        $date = mysqli_real_escape_string($conn, $_POST['activity_date']);
        $time = mysqli_real_escape_string($conn, $_POST['activity_time']);
        $surah = mysqli_real_escape_string($conn, $_POST['surah']);
        $a_start = (int)$_POST['ayah_start'];
        $a_end = (int)$_POST['ayah_end'];
        $notes = mysqli_real_escape_string($conn, $_POST['notes']);

        $q = "INSERT INTO mutabaah (user_id, activity_type, activity_date, activity_time, surah, ayah_start, ayah_end, notes) 
              VALUES ('$user_id', '$type', '$date', '$time', '$surah', '$a_start', '$a_end', '$notes')";

        if (mysqli_query($conn, $q)) {
            echo json_encode(['status' => 'success', 'message' => 'Aktivitas berhasil dicatat!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mencatat aktivitas.']);
        }
        exit();
    }

    if ($action === 'edit_aktivitas') {
        $id = (int)$_POST['id'];
        $type = mysqli_real_escape_string($conn, $_POST['activity_type']);
        $date = mysqli_real_escape_string($conn, $_POST['activity_date']);
        $time = mysqli_real_escape_string($conn, $_POST['activity_time']);
        $surah = mysqli_real_escape_string($conn, $_POST['surah']);
        $a_start = (int)$_POST['ayah_start'];
        $a_end = (int)$_POST['ayah_end'];
        $notes = mysqli_real_escape_string($conn, $_POST['notes']);

        $q = "UPDATE mutabaah SET activity_type='$type', activity_date='$date', activity_time='$time', surah='$surah', ayah_start='$a_start', ayah_end='$a_end', notes='$notes' 
              WHERE id=$id AND user_id='$user_id'";

        if (mysqli_query($conn, $q)) {
            echo json_encode(['status' => 'success', 'message' => 'Aktivitas berhasil diperbarui!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui aktivitas.']);
        }
        exit();
    }

    if ($action === 'delete_aktivitas') {
        $id = (int)$_POST['id'];
        $q = "DELETE FROM mutabaah WHERE id=$id AND user_id='$user_id'";

        if (mysqli_query($conn, $q)) {
            echo json_encode(['status' => 'success', 'message' => 'Aktivitas berhasil dihapus!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus aktivitas.']);
        }
        exit();
    }
}

// Waktu saat ini untuk filter bulan
$m = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('m');
$y = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');
$month_name = date('F Y', mktime(0, 0, 0, $m, 1, $y));
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $m, $y);

// ==========================================
// FITUR EKSPOR LAPORAN KE MS WORD (.DOC)
// ==========================================
if (isset($_GET['export']) && $_GET['export'] == 'doc') {
    $m_exp = (int)$_GET['m'];
    $y_exp = (int)$_GET['y'];
    $month_name_exp = date('F Y', mktime(0, 0, 0, $m_exp, 1, $y_exp));

    $q_exp = mysqli_query($conn, "SELECT * FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m_exp AND YEAR(activity_date) = $y_exp ORDER BY activity_date ASC, activity_time ASC");
    $data_exp = [];
    while ($r = mysqli_fetch_assoc($q_exp)) {
        $data_exp[$r['activity_date']][] = $r;
    }

    $nama_user = htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Pengguna');
    $filename = "Laporan_Mutabaah_" . str_replace(' ', '_', $month_name_exp) . ".doc";

    header("Content-Type: application/vnd.ms-word");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("content-disposition: attachment;filename={$filename}");

    echo "<html><head><meta charset='utf-8'>";
    echo "<style>
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1e293b; background: #ffffff; }
        .header { text-align: center; border-bottom: 3px solid #059669; padding-bottom: 20px; margin-bottom: 30px; }
        .title { font-size: 28pt; color: #059669; font-weight: bold; margin: 0; }
        .subtitle { font-size: 12pt; color: #64748b; margin-top: 5px; }
        .day-block { margin-bottom: 25px; border-left: 4px solid #059669; padding-left: 15px; }
        .day-title { font-size: 14pt; font-weight: bold; color: #0f172a; margin-bottom: 10px; background: #f8fafc; padding: 5px 10px; border-radius: 5px; }
        .act-item { margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed #cbd5e1; }
        .act-badge { background: #d1fae5; color: #047857; padding: 4px 10px; font-size: 9pt; font-weight: bold; border: 1px solid #059669; border-radius: 12px; display: inline-block; }
        .act-time { color: #64748b; font-size: 10pt; margin-left: 10px; }
        .act-surah { font-size: 12pt; font-weight: bold; color: #0f172a; margin: 8px 0 4px 0; }
        .act-notes { font-style: italic; color: #475569; font-size: 11pt; padding-left: 10px; border-left: 2px solid #94a3b8; }
        .empty-msg { color: #94a3b8; font-style: italic; font-size: 11pt; }
    </style></head><body>";

    echo "<div class='header'><div class='title'>Laporan Mutabaah</div><div class='subtitle'>Bulan: {$month_name_exp} &bull; Nama: {$nama_user}</div></div>";

    for ($d = 1; $d <= $days_in_month; $d++) {
        $dateStr = sprintf("%04d-%02d-%02d", $y_exp, $m_exp, $d);
        $printDate = date('d F Y', strtotime($dateStr));

        echo "<div class='day-block'><div class='day-title'>&#128197; {$printDate}</div>";

        if (isset($data_exp[$dateStr])) {
            foreach ($data_exp[$dateStr] as $act) {
                $type = strtoupper(str_replace('_', ' ', $act['activity_type']));
                $time = date('H:i', strtotime($act['activity_time']));
                echo "<div class='act-item'><div><span class='act-badge'>{$type}</span><span class='act-time'>&#128337; Pukul {$time}</span></div>";
                echo "<div class='act-surah'>Surah {$act['surah']} (Ayat {$act['ayah_start']} - {$act['ayah_end']})</div>";
                if (!empty($act['notes'])) {
                    echo "<div class='act-notes'>\"{$act['notes']}\"</div>";
                }
                echo "</div>";
            }
        } else {
            echo "<div class='empty-msg'>- Tidak ada aktivitas -</div>";
        }
        echo "</div>";
    }
    echo "</body></html>";
    exit;
}

// ==========================================
// FITUR EKSPOR REKAP KE EXCEL (.XLS)
// ==========================================
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $m_exp = (int)$_GET['m'];
    $y_exp = (int)$_GET['y'];
    $days_in_month = cal_days_in_month(CAL_GREGORIAN, $m_exp, $y_exp);
    $month_name_exp = date('F Y', mktime(0, 0, 0, $m_exp, 1, $y_exp));

    $q_exp = mysqli_query($conn, "SELECT activity_type, DAY(activity_date) as tgl, COUNT(id) as jml FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m_exp AND YEAR(activity_date) = $y_exp GROUP BY activity_type, DAY(activity_date)");

    $data_matrix = [
        'tilawah' => array_fill(1, $days_in_month, 0),
        'murojaah' => array_fill(1, $days_in_month, 0),
        'hafalan_baru' => array_fill(1, $days_in_month, 0),
        'setoran' => array_fill(1, $days_in_month, 0)
    ];

    while ($r = mysqli_fetch_assoc($q_exp)) {
        $data_matrix[$r['activity_type']][$r['tgl']] = $r['jml'];
    }

    $filename = "Rekap_Mutabaah_" . str_replace(' ', '_', $month_name_exp) . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");

    echo "<style>table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #000; padding: 5px; text-align: center; } th { background-color: #059669; color: white; font-weight: bold; } .kategori { text-align: left; font-weight: bold; }</style>";
    echo "<table><tr><th colspan='" . ($days_in_month + 3) . "' style='font-size:16pt; padding:10px;'>Rekap Aktivitas Mutabaah - $month_name_exp</th></tr><tr><th width='40'>No</th><th width='150'>Kategori Aktivitas</th>";
    for ($i = 1; $i <= $days_in_month; $i++) echo "<th width='35'>$i</th>";
    echo "<th width='80'>Total</th></tr>";

    $no = 1;
    $labels = ['tilawah' => 'Tilawah', 'murojaah' => 'Murojaah', 'hafalan_baru' => 'Hafalan Baru', 'setoran' => 'Setoran'];
    foreach ($labels as $key => $label) {
        echo "<tr><td>$no</td><td class='kategori'>$label</td>";
        $total_baris = 0;
        for ($i = 1; $i <= $days_in_month; $i++) {
            $jml = $data_matrix[$key][$i];
            $total_baris += $jml;
            echo "<td>" . ($jml > 0 ? $jml : '-') . "</td>";
        }
        echo "<td><strong>$total_baris</strong></td></tr>";
        $no++;
    }
    echo "</table>";
    exit;
}

// 1. Hitung Statistik Bulanan
$stat_q = mysqli_query($conn, "SELECT activity_type, COUNT(id) as total FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m AND YEAR(activity_date) = $y GROUP BY activity_type");
$stats = ['tilawah' => 0, 'murojaah' => 0, 'hafalan_baru' => 0, 'setoran' => 0];
while ($row = mysqli_fetch_assoc($stat_q)) {
    $stats[$row['activity_type']] = $row['total'];
}

$hari_aktif_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT activity_date) as aktif FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m AND YEAR(activity_date) = $y"));
$hari_aktif = $hari_aktif_q['aktif'];

// ==========================================
// LOGIKA STREAK (KONSISTENSI HARI BERUNTUN)
// ==========================================
function getStreak($conn, $user_id, $type = null)
{
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    $query = "SELECT DISTINCT activity_date FROM mutabaah WHERE user_id = '$user_id'";
    if ($type) $query .= " AND activity_type = '$type'";
    $query .= " ORDER BY activity_date DESC";

    $q_dates = mysqli_query($conn, $query);
    $user_dates = [];
    while ($r = mysqli_fetch_assoc($q_dates)) {
        $user_dates[] = $r['activity_date'];
    }

    if (empty($user_dates)) return 0;

    $streak = 0;
    $current_check = $today;

    if (!in_array($today, $user_dates) && in_array($yesterday, $user_dates)) {
        $current_check = $yesterday;
    } else if (!in_array($today, $user_dates) && !in_array($yesterday, $user_dates)) {
        return 0; // Bolong lebih dari 1 hari = Streak 0
    }

    foreach ($user_dates as $d) {
        if ($d == $current_check) {
            $streak++;
            $current_check = date('Y-m-d', strtotime("-1 day", strtotime($current_check)));
        } elseif ($d > $current_check) {
            continue;
        } else {
            break;
        }
    }
    return $streak;
}

$streak_all = getStreak($conn, $user_id);
$streak_tilawah = getStreak($conn, $user_id, 'tilawah');
$streak_murojaah = getStreak($conn, $user_id, 'murojaah');
$streak_hafalan = getStreak($conn, $user_id, 'hafalan_baru');
$streak_setoran = getStreak($conn, $user_id, 'setoran');

// Menyimpan data streak untuk digunakan di Javascript AJAX
$app_data = [
    'streakData' => [
        ['title' => 'Konsistensi Semua', 'val' => $streak_all, 'icon' => 'fas fa-fire'],
        ['title' => 'Konsistensi Tilawah', 'val' => $streak_tilawah, 'icon' => 'fas fa-book-open'],
        ['title' => 'Konsistensi Murojaah', 'val' => $streak_murojaah, 'icon' => 'fas fa-sync-alt'],
        ['title' => 'Konsistensi Hafalan', 'val' => $streak_hafalan, 'icon' => 'fas fa-star'],
        ['title' => 'Konsistensi Setoran', 'val' => $streak_setoran, 'icon' => 'fas fa-microphone']
    ]
];

// 2. Data Kalender
$cal_q = mysqli_query($conn, "SELECT activity_date, COUNT(id) as jml FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m AND YEAR(activity_date) = $y GROUP BY activity_date");
$cal_data = [];
while ($row = mysqli_fetch_assoc($cal_q)) {
    $cal_data[$row['activity_date']] = $row['jml'];
}

// 3. Data Timeline (CRUD View)
$time_q = mysqli_query($conn, "SELECT * FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m AND YEAR(activity_date) = $y ORDER BY activity_date DESC, activity_time DESC");
$timeline = [];
while ($row = mysqli_fetch_assoc($time_q)) {
    $timeline[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutabaah - Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Data State JavaScript JSON -->
    <script type="application/json" id="app-data">
        <?= json_encode($app_data) ?>
    </script>
    <style>
        :root {
            --primary: #059669;
            --primary-hover: #047857;
            --primary-light: #d1fae5;
            --dark: #0f172a;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
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

        /* HEADER BULAN */
        .month-header {
            background: var(--card-bg);
            padding: 1.2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .month-header h2 {
            font-weight: 800;
            color: var(--dark);
            font-size: 1.25rem;
        }

        .month-nav-link {
            color: var(--primary);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            background: var(--primary-light);
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .month-nav-link:hover {
            background: var(--primary);
            color: white;
        }

        /* STATISTIK */
        .stat-card {
            background: var(--bg);
            border-radius: 16px;
            padding: 1.2rem;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-light);
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.08);
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            margin-top: 5px;
        }

        /* DYNAMIC STREAK STYLES (ORANGE THEME) */
        .streak-card {
            border: none;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(234, 88, 12, 0.3);
        }

        .streak-card .stat-label,
        .streak-card .stat-value {
            color: white;
        }

        .streak-anim-wrapper {
            transition: opacity 0.3s ease-in-out;
        }

        @keyframes firePulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.15);
            }

            100% {
                transform: scale(1);
            }
        }

        .streak-icon-anim {
            animation: firePulse 1.5s infinite ease-in-out;
            display: inline-block;
            transition: all 0.5s ease;
        }

        .streak-nav-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .streak-nav-btn:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
        }

        /* KALENDER */
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .cal-day-label {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .cal-box {
            aspect-ratio: 1;
            border-radius: 12px;
            background-color: #f1f5f9;
            position: relative;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.95rem;
            color: rgba(0, 0, 0, 0.3);
            border: 2px solid transparent;
        }

        .cal-box.active {
            background-color: var(--primary);
            color: white;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
        }

        .cal-box:hover {
            transform: translateY(-3px) scale(1.05);
            border-color: var(--primary-light);
            color: var(--dark);
            z-index: 2;
        }

        .cal-box.active:hover {
            border-color: white;
            color: white;
        }

        .cal-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            font-size: 0.65rem;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.4);
            border: 2px solid white;
        }

        /* TIMELINE & FILTER */
        .filter-scroll {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .filter-scroll::-webkit-scrollbar {
            display: none;
        }

        .filter-btn-custom {
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--bg);
            font-size: 0.85rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s ease;
            color: var(--text-muted);
            font-weight: 600;
        }

        .filter-btn-custom.active,
        .filter-btn-custom:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2);
        }

        .search-box-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-input {
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--bg);
            padding: 10px 15px;
            font-size: 0.9rem;
            width: 100%;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .tl-card {
            background: var(--card-bg);
            padding: 1.2rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            border-left: 6px solid var(--primary);
            transition: all 0.3s ease;
            animation: slideIn 0.3s ease forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .tl-card:hover {
            transform: translateX(8px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            border-left-color: var(--primary-hover);
        }

        .tl-type {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            background: var(--primary-light);
            color: var(--primary-hover);
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .tl-notes {
            font-style: italic;
            background: var(--bg);
            padding: 0.8rem 1rem;
            border-radius: 12px;
            margin-top: 1rem;
            color: #475569;
            font-size: 0.9rem;
            border-left: 3px solid #cbd5e1;
        }

        /* PAGINATION */
        .pagination {
            margin-top: 25px;
            margin-bottom: 0;
            gap: 5px;
        }

        .page-item .page-link {
            border-radius: 10px;
            border: none;
            color: var(--text-muted);
            background: var(--bg);
            font-weight: 600;
            padding: 8px 14px;
            transition: 0.3s;
        }

        .page-item.active .page-link,
        .page-item .page-link:hover {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2);
        }

        .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* FAB */
        .fab {
            position: fixed;
            bottom: 100px;
            right: 25px;
            width: 65px;
            height: 65px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: none;
            animation: bounceIn 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .fab:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 15px 35px rgba(5, 150, 105, 0.5);
            background: var(--primary-hover);
        }

        /* MODAL */
        .modal-backdrop-custom {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(5px);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .modal-backdrop-custom.show {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-content-custom {
            background: var(--card-bg);
            width: 100%;
            max-width: 500px;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-height: 90vh;
            overflow-y: auto;
            transform: translateY(30px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .modal-backdrop-custom.show .modal-content-custom {
            transform: translateY(0) scale(1);
        }

        @media (max-width: 767.98px) {
            .modal-backdrop-custom {
                align-items: flex-end;
            }

            .modal-content-custom {
                max-width: 100%;
                border-radius: 24px 24px 0 0;
                max-height: 85vh;
                transform: translateY(100%);
                padding: 1.5rem;
            }

            .modal-backdrop-custom.show .modal-content-custom {
                transform: translateY(0);
            }

            .fab {
                bottom: 85px;
                right: 20px;
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }

        /* DROPDOWN SURAH */
        .custom-select-wrapper {
            position: relative;
        }

        .dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            max-height: 220px;
            overflow-y: auto;
            z-index: 1060;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            margin-top: 6px;
            display: none;
            flex-direction: column;
        }

        .dropdown-list.show {
            display: flex;
        }

        .dropdown-item-custom {
            padding: 0.8rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid var(--bg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s;
        }

        .dropdown-item-custom:hover {
            background: var(--primary-light);
            color: var(--primary-hover);
        }

        .dropdown-item-custom .s-name {
            font-weight: 600;
        }

        .dropdown-item-custom .s-ayat {
            font-size: 0.75rem;
            color: var(--primary-hover);
            background: var(--primary-light);
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <?php include '../components/nav.php'; ?>

    <div class="container screen-area">
        <!-- HEADER BULAN -->
        <div class="month-header d-flex justify-content-between align-items-center">
            <?php
            $prev_m = $m - 1;
            $prev_y = $y;
            if ($prev_m < 1) {
                $prev_m = 12;
                $prev_y--;
            }
            $next_m = $m + 1;
            $next_y = $y;
            if ($next_m > 12) {
                $next_m = 1;
                $next_y++;
            }
            ?>
            <a href="?m=<?= $prev_m ?>&y=<?= $prev_y ?>" class="month-nav-link shadow-sm">
                <i class="fas fa-chevron-left"></i> <span class="d-none d-md-inline">Sebelumnya</span>
            </a>
            <h2 class="mb-0 text-center flex-grow-1"><?= $month_name ?></h2>
            <a href="?m=<?= $next_m ?>&y=<?= $next_y ?>" class="month-nav-link shadow-sm">
                <span class="d-none d-md-inline">Selanjutnya</span> <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <!-- GRID UTAMA -->
        <div class="row g-4">
            <!-- KOLOM KIRI: STATS + KALENDER + EXPORT -->
            <div class="col-lg-5">
                <!-- Ringkasan Aktivitas (Dibungkus agar mudah diupdate via AJAX) -->
                <div id="stats-grid" class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="fas fa-chart-pie me-2 text-primary"></i> Ringkasan Aktivitas</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Hari Aktif</div>
                                    <div class="stat-value"><?= $hari_aktif ?> <span class="fs-6 text-muted fw-medium">hari</span></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Tilawah</div>
                                    <div class="stat-value"><?= $stats['tilawah'] ?> <span class="fs-6 text-muted fw-medium">kali</span></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Murojaah</div>
                                    <div class="stat-value"><?= $stats['murojaah'] ?> <span class="fs-6 text-muted fw-medium">kali</span></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Setoran</div>
                                    <div class="stat-value"><?= $stats['setoran'] ?> <span class="fs-6 text-muted fw-medium">kali</span></div>
                                </div>
                            </div>

                            <!-- KARTU STREAK -->
                            <div class="col-12">
                                <div class="stat-card streak-card p-4 d-flex align-items-center justify-content-between">
                                    <button class="streak-nav-btn" onclick="prevStreak()"><i class="fas fa-chevron-left"></i></button>

                                    <div class="streak-anim-wrapper flex-grow-1 text-center px-2" id="streak-anim-wrapper">
                                        <i id="streak-icon" class="fas fa-fire streak-icon-anim fs-1 mb-2"></i>
                                        <div class="stat-label text-white opacity-75 mb-1" id="streak-title" style="font-size:0.85rem;">Konsistensi Semua</div>
                                        <div class="fs-2 fw-bold text-white lh-1">
                                            <span id="streak-val"><?= $streak_all ?></span> <span class="fs-5 fw-normal">Hari</span>
                                        </div>
                                        <div class="mt-1" style="font-size:0.7rem; opacity:0.8;">(Dihitung per hari beruntun)</div>
                                    </div>

                                    <button class="streak-nav-btn" onclick="nextStreak()"><i class="fas fa-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kalender Kontribusi -->
                <div id="calendar-grid" class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="fas fa-calendar-check me-2 text-primary"></i> Kalender Kontribusi</h5>
                        <div class="cal-grid">
                            <div class="cal-day-label">Min</div>
                            <div class="cal-day-label">Sen</div>
                            <div class="cal-day-label">Sel</div>
                            <div class="cal-day-label">Rab</div>
                            <div class="cal-day-label">Kam</div>
                            <div class="cal-day-label">Jum</div>
                            <div class="cal-day-label">Sab</div>
                            <?php
                            $first_day = date('w', mktime(0, 0, 0, $m, 1, $y));
                            for ($i = 0; $i < $first_day; $i++) echo "<div></div>";

                            for ($d = 1; $d <= $days_in_month; $d++) {
                                $date_str = sprintf("%04d-%02d-%02d", $y, $m, $d);
                                $count = isset($cal_data[$date_str]) ? $cal_data[$date_str] : 0;
                                $class = $count > 0 ? 'active' : '';
                                $badge = $count > 1 ? "<div class='cal-badge'>$count</div>" : "";
                                echo "<div class='cal-box $class' onclick='showDaily(\"$date_str\")'>
                                        <span style='opacity: " . ($class ? "1" : "0.6") . ";'>$d</span>
                                        $badge
                                      </div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Tombol Ekspor -->
                <div class="d-flex gap-2 mb-4">
                    <a href="?export=doc&m=<?= $m ?>&y=<?= $y ?>" class="btn btn-outline-primary flex-fill rounded-4 py-3 fw-bold shadow-sm text-decoration-none d-flex justify-content-center align-items-center">
                        <i class="fas fa-file-word me-2 fs-5"></i> Word
                    </a>
                    <a href="?export=excel&m=<?= $m ?>&y=<?= $y ?>" class="btn btn-outline-success flex-fill rounded-4 py-3 fw-bold shadow-sm text-decoration-none d-flex justify-content-center align-items-center">
                        <i class="fas fa-file-excel me-2 fs-5"></i> Excel
                    </a>
                </div>
            </div>

            <!-- KOLOM KANAN: TIMELINE (CRUD) -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4"><i class="fas fa-history me-2 text-primary"></i> Jejak Langkah</h5>

                        <!-- Filter Kategori -->
                        <div class="filter-scroll">
                            <button class="filter-btn-custom active" onclick="filterCategory('all', this)"><i class="fas fa-list me-1"></i> Semua</button>
                            <button class="filter-btn-custom" onclick="filterCategory('tilawah', this)"><i class="fas fa-book-open me-1"></i> Tilawah</button>
                            <button class="filter-btn-custom" onclick="filterCategory('murojaah', this)"><i class="fas fa-sync-alt me-1"></i> Murojaah</button>
                            <button class="filter-btn-custom" onclick="filterCategory('hafalan_baru', this)"><i class="fas fa-star me-1"></i> Hafalan Baru</button>
                            <button class="filter-btn-custom" onclick="filterCategory('setoran', this)"><i class="fas fa-microphone me-1"></i> Setoran</button>
                        </div>

                        <!-- Kolom Pencarian -->
                        <div class="search-box-container">
                            <input type="date" id="search_date" class="search-input" title="Cari berdasarkan tanggal" onchange="runSearchAndFilter()">
                            <input type="text" id="search_notes" class="search-input" placeholder="Cari catatan/pesan..." onkeyup="runSearchAndFilter()">
                        </div>

                        <!-- Timeline List Wrapper -->
                        <div id="timeline-wrapper">
                            <div id="timeline-container" class="d-flex flex-column gap-3">
                                <?php foreach ($timeline as $tl):
                                    $type_label = str_replace('_', ' ', $tl['activity_type']);
                                    $safe_notes = htmlspecialchars($tl['notes'], ENT_QUOTES);
                                    $safe_surah = htmlspecialchars($tl['surah'], ENT_QUOTES);
                                ?>
                                    <div class="tl-card tl-item" data-type="<?= $tl['activity_type'] ?>" data-date="<?= $tl['activity_date'] ?>" data-notes="<?= htmlspecialchars($tl['notes']) ?>">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="tl-type"><i class="fas fa-tag me-1"></i> <?= $type_label ?></span>
                                            <div class="text-muted small fw-medium">
                                                <span><i class="fas fa-calendar-day me-1"></i> <?= date('d M Y', strtotime($tl['activity_date'])) ?></span> &bull;
                                                <span><i class="fas fa-clock me-1"></i> <?= date('H:i', strtotime($tl['activity_time'])) ?></span>
                                            </div>
                                        </div>
                                        <div class="fw-bold fs-5 text-dark mb-1">Surah <?= $safe_surah ?></div>
                                        <div class="text-secondary fw-medium mb-2">
                                            <i class="fas fa-bookmark me-1 opacity-75"></i> Ayat <?= $tl['ayah_start'] ?> - <?= $tl['ayah_end'] ?>
                                        </div>
                                        <?php if ($tl['notes']): ?>
                                            <div class="tl-notes"><i class="fas fa-quote-left me-2 opacity-50"></i><?= htmlspecialchars($tl['notes']) ?></div>
                                        <?php endif; ?>

                                        <!-- Tombol Aksi (CRUD) -->
                                        <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" onclick="editAktivitas(<?= $tl['id'] ?>, '<?= $tl['activity_type'] ?>', '<?= $tl['activity_date'] ?>', '<?= $tl['activity_time'] ?>', '<?= $safe_surah ?>', <?= $tl['ayah_start'] ?>, <?= $tl['ayah_end'] ?>, '<?= $safe_notes ?>')">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="deleteAktivitas(<?= $tl['id'] ?>)">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div id="empty-state" class="text-center py-5 text-muted" style="display: <?= empty($timeline) ? 'block' : 'none' ?>;">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                    <h5 class="fw-bold">Tidak ada aktivitas</h5>
                                    <p>Coba sesuaikan filter atau pencarianmu.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Navigasi Pagination Angka (Maks 7) -->
                        <ul class="pagination justify-content-center" id="pagination-nav"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAB (ADD NEW) -->
    <button class="fab shadow-lg" onclick="openAddModal()" title="Catat Aktivitas Baru">
        <i class="fas fa-pen"></i>
    </button>

    <!-- MODAL / BOTTOM SHEET: Form Aktivitas (Tambah & Edit) -->
    <div class="modal-backdrop-custom" id="modalForm">
        <div class="modal-content-custom border-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark" id="modalFormTitle"><i class="fas fa-plus-circle text-primary me-2"></i>Catat Aktivitas</h5>
                <button type="button" class="btn-close shadow-none" onclick="closeModal('modalForm')"></button>
            </div>

            <form id="formMutabaah">
                <!-- Hidden Inputs untuk Sistem AJAX CRUD -->
                <input type="hidden" name="ajax_action" id="form_action" value="simpan_aktivitas">
                <input type="hidden" name="id" id="form_id" value="">

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">Kategori Aktivitas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i id="cat_icon" class="fas fa-book-open text-primary"></i></span>
                        <select name="activity_type" id="activity_type" class="form-select border-start-0 ps-0 bg-light fw-medium" required onchange="updateCatIcon()">
                            <option value="tilawah">Tilawah</option>
                            <option value="murojaah">Murojaah</option>
                            <option value="hafalan_baru">Hafalan Baru</option>
                            <option value="setoran">Setoran</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3 custom-select-wrapper">
                    <label class="form-label fw-bold text-secondary">Nama Surah</label>
                    <input type="hidden" name="surah" id="hidden_surah" required>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="search_surah" class="form-control border-start-0 ps-0 bg-light fw-medium" placeholder="Ketik & cari nama surah..." autocomplete="off" required>
                    </div>
                    <div class="dropdown-list" id="surah_dropdown">
                        <div class="p-3 text-center text-muted small"><i class="fas fa-spinner fa-spin me-2"></i>Memuat data surah...</div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold text-secondary">Ayat Awal</label>
                        <input type="number" name="ayah_start" id="ayah_start" class="form-control bg-light fw-medium" required min="1" disabled placeholder="Pilih Surah">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold text-secondary">Ayat Akhir</label>
                        <input type="number" name="ayah_end" id="ayah_end" class="form-control bg-light fw-medium" required min="1" disabled placeholder="Pilih Surah">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold text-secondary">Tanggal</label>
                        <input type="date" name="activity_date" id="activity_date" class="form-control bg-light fw-medium" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold text-secondary">Jam</label>
                        <input type="time" name="activity_time" id="activity_time" class="form-control bg-light fw-medium" value="<?= date('H:i') ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control bg-light fw-medium" rows="2" placeholder="Bagaimana kelancaran hafalanmu?"></textarea>
                </div>

                <button type="submit" class="btn btn-success w-100 py-3 rounded-4 fw-bold shadow-sm" id="btn-submit-ajax">
                    <i class="fas fa-save me-2"></i> Simpan Catatan
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL / BOTTOM SHEET: Detail Harian -->
    <div class="modal-backdrop-custom" id="modalDetail">
        <div class="modal-content-custom border-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark" id="detail-date-title"><i class="fas fa-calendar-day text-primary me-2"></i>Aktivitas Harian</h5>
                <button type="button" class="btn-close shadow-none" onclick="closeModal('modalDetail')"></button>
            </div>
            <div id="daily-timeline-container" class="d-flex flex-column gap-3"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // =====================================
        // VARIABEL GLOBAL SPA (SINGLE PAGE)
        // =====================================
        const ITEMS_PER_PAGE = 7;
        let currentPage = 1;
        let currentCategory = 'all';
        let allItems = Array.from(document.querySelectorAll('.tl-item'));

        let streakData = JSON.parse(document.getElementById('app-data').textContent).streakData;
        let currentStreakIdx = 0;

        // =====================================
        // FUNGSI RE-RENDER UI TANPA REFRESH
        // =====================================
        async function refreshUIWithoutReload() {
            try {
                // Fetch isi halaman terbaru dari server secara background
                const response = await fetch(window.location.href);
                const htmlText = await response.text();

                // Parsing HTML menjadi DOM bayangan (Virtual DOM sederhana)
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(htmlText, 'text/html');

                // 1. Ganti konten Statistik
                document.getElementById('stats-grid').innerHTML = newDoc.getElementById('stats-grid').innerHTML;

                // 2. Ganti konten Kalender
                document.getElementById('calendar-grid').innerHTML = newDoc.getElementById('calendar-grid').innerHTML;

                // 3. Ganti konten Timeline
                document.getElementById('timeline-wrapper').innerHTML = newDoc.getElementById('timeline-wrapper').innerHTML;

                // 4. Update data JavaScript untuk Streak
                const newAppData = JSON.parse(newDoc.getElementById('app-data').textContent);
                streakData = newAppData.streakData;
                updateStreakUI(); // Refresh tampilan streak

                // 5. Inisialisasi ulang item timeline untuk fitur filter & pagination
                allItems = Array.from(document.querySelectorAll('.tl-item'));
                runSearchAndFilter();

                // 6. Tutup modal form 
                closeModal('modalForm');

            } catch (error) {
                console.error("Gagal refresh UI, fallback ke reload biasa.", error);
                window.location.reload();
            }
        }

        // =====================================
        // MANUAL STREAK SLIDER & EFEK API
        // =====================================
        function updateStreakUI() {
            const sd = streakData[currentStreakIdx];
            const titleEl = document.getElementById('streak-title');
            const iconEl = document.getElementById('streak-icon');
            const valEl = document.getElementById('streak-val');
            const animEl = document.getElementById('streak-anim-wrapper');

            animEl.style.opacity = 0; // Fade out

            setTimeout(() => {
                if (titleEl) titleEl.innerText = sd.title;
                if (iconEl) iconEl.className = sd.icon + ' streak-icon-anim fs-1 mb-2';
                if (valEl) valEl.innerText = sd.val;

                let fireLevel = Math.floor(sd.val / 5);
                if (iconEl) {
                    iconEl.style.textShadow = getFireShadow(fireLevel);
                    iconEl.style.color = fireLevel > 0 ? '#fef08a' : '#ffffff';
                }

                animEl.style.opacity = 1; // Fade in
            }, 250);
        }

        function getFireShadow(level) {
            if (level === 0) return 'none';
            if (level === 1) return '0 0 10px #fef08a';
            if (level === 2) return '0 0 10px #fef08a, 0 0 20px #ef4444';
            if (level >= 3) return '0 0 15px #fef08a, 0 0 30px #ef4444, 0 0 40px #fef08a';
            return 'none';
        }

        function nextStreak() {
            currentStreakIdx = (currentStreakIdx + 1) % streakData.length;
            updateStreakUI();
        }

        function prevStreak() {
            currentStreakIdx = (currentStreakIdx - 1 + streakData.length) % streakData.length;
            updateStreakUI();
        }
        setTimeout(updateStreakUI, 100);

        // =====================================
        // FUNGSI BUKA MODAL (TAMBAH & EDIT)
        // =====================================
        function openAddModal() {
            document.getElementById('modalFormTitle').innerHTML = '<i class="fas fa-plus-circle text-primary me-2"></i>Catat Aktivitas';
            document.getElementById('form_action').value = 'simpan_aktivitas';
            document.getElementById('form_id').value = '';

            document.getElementById('activity_type').value = 'tilawah';
            document.getElementById('search_surah').value = '';
            document.getElementById('hidden_surah').value = '';
            document.getElementById('ayah_start').value = '';
            document.getElementById('ayah_end').value = '';
            document.getElementById('ayah_start').disabled = true;
            document.getElementById('ayah_end').disabled = true;
            document.getElementById('notes').value = '';
            updateCatIcon();

            openModal('modalForm');
        }

        function editAktivitas(id, type, date, time, surah, start, end, notes) {
            document.getElementById('modalFormTitle').innerHTML = '<i class="fas fa-edit text-primary me-2"></i>Edit Aktivitas';
            document.getElementById('form_action').value = 'edit_aktivitas';
            document.getElementById('form_id').value = id;

            document.getElementById('activity_type').value = type;
            document.getElementById('activity_date').value = date;
            document.getElementById('activity_time').value = time;
            document.getElementById('search_surah').value = surah;
            document.getElementById('hidden_surah').value = surah;

            let aStart = document.getElementById('ayah_start');
            let aEnd = document.getElementById('ayah_end');
            aStart.disabled = false;
            aStart.value = start;
            aEnd.disabled = false;
            aEnd.value = end;

            document.getElementById('notes').value = notes;
            updateCatIcon();

            openModal('modalForm');
        }

        // =====================================
        // AJAX: SUBMIT FORM (TAMBAH/EDIT)
        // =====================================
        document.getElementById('formMutabaah').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-ajax');
            const oriBtnText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            // Memanggil fungsi re-render (TANPA RELOAD)
                            refreshUIWithoutReload();
                            btn.innerHTML = oriBtnText;
                            btn.disabled = false;
                        });
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                        btn.innerHTML = oriBtnText;
                        btn.disabled = false;
                    }
                }).catch(err => {
                    Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                    btn.innerHTML = oriBtnText;
                    btn.disabled = false;
                });
        });

        // =====================================
        // AJAX: HAPUS AKTIVITAS
        // =====================================
        function deleteAktivitas(id) {
            Swal.fire({
                title: 'Hapus Catatan?',
                text: "Catatan yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('ajax_action', 'delete_aktivitas');
                    formData.append('id', id);

                    fetch(window.location.href, {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: data.message,
                                    timer: 1200,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Memanggil fungsi re-render (TANPA RELOAD)
                                    refreshUIWithoutReload();
                                });
                            } else {
                                Swal.fire('Gagal', data.message, 'error');
                            }
                        });
                }
            })
        }

        // =====================================
        // FILTER, PENCARIAN, & PAGINATION PER 7
        // =====================================
        function filterCategory(type, btn) {
            document.querySelectorAll('.filter-btn-custom').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = type;
            currentPage = 1;
            runSearchAndFilter();
        }

        function runSearchAndFilter() {
            const sDate = document.getElementById('search_date').value;
            const sNotes = document.getElementById('search_notes').value.toLowerCase();

            let filteredItems = allItems.filter(item => {
                let matchCat = (currentCategory === 'all' || item.getAttribute('data-type') === currentCategory);
                let matchDate = (sDate === '' || item.getAttribute('data-date') === sDate);
                let matchNotes = (sNotes === '' || item.getAttribute('data-notes').toLowerCase().includes(sNotes));
                return matchCat && matchDate && matchNotes;
            });

            const totalPages = Math.ceil(filteredItems.length / ITEMS_PER_PAGE) || 1;
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            allItems.forEach(item => item.style.display = 'none');
            let startIdx = (currentPage - 1) * ITEMS_PER_PAGE;
            let endIdx = startIdx + ITEMS_PER_PAGE;

            for (let i = startIdx; i < Math.min(endIdx, filteredItems.length); i++) {
                filteredItems[i].style.display = 'block';
            }

            const emptyState = document.getElementById('empty-state');
            if (emptyState) {
                emptyState.style.display = filteredItems.length === 0 ? 'block' : 'none';
            }
            renderPagination(totalPages);
        }

        function renderPagination(totalPages) {
            const nav = document.getElementById('pagination-nav');
            if (!nav) return;
            nav.innerHTML = '';
            if (totalPages <= 1) return;

            nav.innerHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" onclick="goToPage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></button>
            </li>`;

            for (let p = 1; p <= totalPages; p++) {
                nav.innerHTML += `<li class="page-item ${p === currentPage ? 'active' : ''}">
                    <button class="page-link" onclick="goToPage(${p})">${p}</button>
                </li>`;
            }

            nav.innerHTML += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" onclick="goToPage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></button>
            </li>`;
        }

        function goToPage(pageNum) {
            currentPage = pageNum;
            runSearchAndFilter();
        }

        runSearchAndFilter(); // Init

        // ICON KATEGORI DINAMIS
        function updateCatIcon() {
            const val = document.getElementById('activity_type').value;
            const icon = document.getElementById('cat_icon');
            icon.className = 'fas text-primary ' +
                (val === 'tilawah' ? 'fa-book-open' :
                    val === 'murojaah' ? 'fa-sync-alt' :
                    val === 'hafalan_baru' ? 'fa-star' : 'fa-microphone');
        }

        // =====================================
        // MODAL HANDLER GLOBAL
        // =====================================
        function openModal(id) {
            document.getElementById(id).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function(e) {
            document.querySelectorAll('.modal-backdrop-custom').forEach(backdrop => {
                if (e.target === backdrop) closeModal(backdrop.id);
            });
            if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
                dropdownList.classList.remove('show');
            }
        });

        // DETAIL HARIAN KALENDER
        function showDaily(dateStr) {
            const dateObj = new Date(dateStr);
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            document.getElementById('detail-date-title').innerHTML = `<i class="fas fa-calendar-day text-primary me-2"></i> ${dateObj.toLocaleDateString('id-ID', options)}`;

            const container = document.getElementById('daily-timeline-container');
            container.innerHTML = "";
            let found = false;

            allItems.forEach(item => {
                if (item.getAttribute('data-date') === dateStr) {
                    const clone = item.cloneNode(true);
                    clone.style.display = 'block';
                    clone.style.animation = 'none';
                    const actionDiv = clone.querySelector('.d-flex.justify-content-end.gap-2');
                    if (actionDiv) actionDiv.remove();
                    container.appendChild(clone);
                    found = true;
                }
            });

            if (!found) {
                container.innerHTML = `<div class="text-center py-5 text-muted bg-light rounded-4 border border-dashed"><i class="fas fa-bed fa-2x mb-2 opacity-50"></i><p class="mb-0 fw-medium">Tidak ada catatan aktivitas.</p></div>`;
            }
            openModal('modalDetail');
        }

        // =====================================
        // SURAH SEARCH LOGIC & BUG FIX (')
        // =====================================
        let surahData = [];
        const searchInput = document.getElementById('search_surah');
        const dropdownList = document.getElementById('surah_dropdown');
        const hiddenSurah = document.getElementById('hidden_surah');
        const inputAyahStart = document.getElementById('ayah_start');
        const inputAyahEnd = document.getElementById('ayah_end');

        async function fetchSurahList() {
            try {
                const response = await fetch('https://equran.id/api/v2/surat');
                const json = await response.json();
                surahData = json.data;
                renderDropdown(surahData);
            } catch (error) {
                dropdownList.innerHTML = `<div class="p-3 text-center text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data surah.</div>`;
            }
        }

        function renderDropdown(dataList) {
            if (dataList.length === 0) {
                dropdownList.innerHTML = `<div class="p-3 text-center text-muted">Surah tidak ditemukan.</div>`;
                return;
            }
            let html = '';
            dataList.forEach(surah => {
                const safeName = surah.namaLatin.replace(/'/g, "\\'");
                html += `
                <div class="dropdown-item-custom" onclick="selectSurah('${safeName}', ${surah.jumlahAyat})">
                    <span class="s-name"><i class="fas fa-quran me-2 opacity-50"></i>${surah.nomor}. ${surah.namaLatin}</span>
                    <span class="s-ayat">${surah.jumlahAyat} Ayat</span>
                </div>`;
            });
            dropdownList.innerHTML = html;
        }

        function selectSurah(namaLatin, jumlahAyat) {
            searchInput.value = namaLatin;
            hiddenSurah.value = namaLatin;
            inputAyahStart.disabled = false;
            inputAyahEnd.disabled = false;
            inputAyahStart.max = jumlahAyat;
            inputAyahEnd.max = jumlahAyat;
            inputAyahStart.placeholder = `Maks: ${jumlahAyat}`;
            inputAyahEnd.placeholder = `Maks: ${jumlahAyat}`;

            if (document.getElementById('form_action').value !== 'edit_aktivitas') {
                inputAyahStart.value = '';
                inputAyahEnd.value = '';
            }
            dropdownList.classList.remove('show');
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const filtered = surahData.filter(surah => surah.namaLatin.toLowerCase().includes(query));
            renderDropdown(filtered);
            dropdownList.classList.add('show');
            if (hiddenSurah.value !== query) {
                inputAyahStart.disabled = true;
                inputAyahEnd.disabled = true;
                inputAyahStart.placeholder = "Pilih Surah";
                inputAyahEnd.placeholder = "Pilih Surah";
                hiddenSurah.value = '';
            }
        });

        searchInput.addEventListener('focus', () => {
            if (surahData.length > 0) dropdownList.classList.add('show');
        });
        inputAyahEnd.addEventListener('change', function() {
            if (parseInt(this.value) < parseInt(inputAyahStart.value)) {
                Swal.fire('Peringatan', 'Ayat Akhir tidak boleh lebih kecil dari Ayat Awal.', 'warning');
                this.value = inputAyahStart.value;
            }
        });

        fetchSurahList();
    </script>
</body>

</html>