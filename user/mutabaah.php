<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pesan = '';

// Waktu saat ini untuk filter bulan
$m = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('m');
$y = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');
$month_name = date('F Y', mktime(0, 0, 0, $m, 1, $y));

// Menangani Form Tambah Aktivitas
if (isset($_POST['simpan_aktivitas'])) {
    $type = mysqli_real_escape_string($conn, $_POST['activity_type']);
    $date = mysqli_real_escape_string($conn, $_POST['activity_date']);
    $time = mysqli_real_escape_string($conn, $_POST['activity_time']);
    $surah = mysqli_real_escape_string($conn, $_POST['surah']);
    $a_start = (int)$_POST['ayah_start'];
    $a_end = (int)$_POST['ayah_end'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $q_insert = "INSERT INTO mutabaah (user_id, activity_type, activity_date, activity_time, surah, ayah_start, ayah_end, notes) 
                 VALUES ('$user_id', '$type', '$date', '$time', '$surah', '$a_start', '$a_end', '$notes')";
    if (mysqli_query($conn, $q_insert)) {
        $pesan = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Aktivitas berhasil dicatat!</div>";
    }
}

// 1. Hitung Statistik Bulanan
$stat_q = mysqli_query($conn, "SELECT activity_type, COUNT(id) as total FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m AND YEAR(activity_date) = $y GROUP BY activity_type");
$stats = ['tilawah' => 0, 'murojaah' => 0, 'hafalan_baru' => 0, 'setoran' => 0];
while ($row = mysqli_fetch_assoc($stat_q)) {
    $stats[$row['activity_type']] = $row['total'];
}

$hari_aktif_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT activity_date) as aktif FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m AND YEAR(activity_date) = $y"));
$hari_aktif = $hari_aktif_q['aktif'];

// Hitung Streak
$streak = 0;
$cek_tgl = date('Y-m-d');
while (true) {
    $cek_q = mysqli_query($conn, "SELECT id FROM mutabaah WHERE user_id = '$user_id' AND activity_date = '$cek_tgl' LIMIT 1");
    if (mysqli_num_rows($cek_q) > 0) {
        $streak++;
        $cek_tgl = date('Y-m-d', strtotime("-1 day", strtotime($cek_tgl)));
    } else {
        if ($streak == 0 && $cek_tgl == date('Y-m-d')) {
            $cek_tgl = date('Y-m-d', strtotime("-1 day", strtotime($cek_tgl)));
            continue;
        }
        break;
    }
}

// 2. Data Kalender
$cal_q = mysqli_query($conn, "SELECT activity_date, COUNT(id) as jml FROM mutabaah WHERE user_id = '$user_id' AND MONTH(activity_date) = $m AND YEAR(activity_date) = $y GROUP BY activity_date");
$cal_data = [];
while ($row = mysqli_fetch_assoc($cal_q)) {
    $cal_data[$row['activity_date']] = $row['jml'];
}

// 3. Data Timeline
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #059669; --primary-hover: #047857; --primary-light: #d1fae5;
            --dark: #0f172a; --text-muted: #64748b; 
            --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg); color: var(--dark); padding-bottom: 90px; }

        /* HEADER BULAN */
        .month-header {
            background: var(--card-bg); padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 25px;
        }
        .month-header h2 { font-size: 1.25rem; color: var(--dark); font-weight: 700; }
        .month-nav a {
            text-decoration: none; color: var(--text-muted); font-weight: 600; font-size: 0.9rem;
            padding: 8px 16px; background: var(--bg); border-radius: 12px; transition: all 0.3s ease;
        }
        .month-nav a:hover { background: var(--border); color: var(--dark); }

        .container { padding: 0 20px; max-width: 1100px; margin: 0 auto; }
        .alert {
            padding: 15px; border-radius: 12px; margin-bottom: 25px; display: flex; gap: 10px; align-items: center;
            font-size: 0.95rem; background-color: var(--primary-light); color: #065f46; font-weight: 500;
        }

        /* GRID LAYOUT */
        .mutabaah-grid { display: flex; flex-direction: column; gap: 25px; }

        /* KARTU MODERN (Next.js / Vue style) */
        .card {
            background: var(--card-bg); border-radius: 20px; padding: 25px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .card-title { font-size: 1.15rem; font-weight: 700; margin-bottom: 20px; color: var(--dark); display: flex; justify-content: space-between; align-items: center; }

        /* STATISTIK */
        .stat-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .stat-item { background: var(--bg); padding: 15px; border-radius: 16px; display: flex; flex-direction: column; gap: 8px; border: 1px solid var(--border); }
        .stat-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; }
        .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--primary); }
        .streak { grid-column: span 2; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; border: none; flex-direction: row; justify-content: space-between; align-items: center; }
        .streak .stat-label { color: rgba(255, 255, 255, 0.9); }
        .streak .stat-value { color: white; }

        /* KALENDER */
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
        .cal-day-label { text-align: center; font-size: 0.8rem; color: var(--text-muted); font-weight: 600; margin-bottom: 5px; }
        .cal-box {
            aspect-ratio: 1; border-radius: 8px; background-color: #f1f5f9; position: relative;
            cursor: pointer; transition: all 0.2s ease; border: 1px solid transparent;
        }
        .cal-box.active { background-color: var(--primary); box-shadow: 0 4px 10px rgba(5,150,105,0.3); }
        .cal-box:hover { transform: translateY(-2px); border-color: var(--text-muted); z-index: 2; }
        .cal-box.active:hover { border-color: transparent; transform: scale(1.1); }
        .cal-badge {
            position: absolute; top: -6px; right: -6px; font-size: 0.7rem; background: #ef4444; color: white;
            border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center;
            font-weight: 700; box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4); border: 2px solid white;
        }

        /* FILTER & TIMELINE */
        .filter-scroll { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 15px; margin-bottom: 15px; }
        .filter-scroll::-webkit-scrollbar { height: 0px; } /* Hide scrollbar for clean UI */
        .filter-btn {
            padding: 8px 18px; border-radius: 20px; border: 1px solid var(--border); background: var(--card-bg);
            font-size: 0.9rem; cursor: pointer; white-space: nowrap; transition: all 0.3s ease;
            color: var(--text-muted); font-weight: 500;
        }
        .filter-btn:hover { background: var(--bg); color: var(--dark); }
        .filter-btn.active { background: var(--dark); color: white; border-color: var(--dark); }

        .timeline { display: flex; flex-direction: column; gap: 15px; }
        .tl-card {
            background: var(--card-bg); padding: 18px; border-radius: 16px; border: 1px solid var(--border);
            border-left: 6px solid var(--primary); transition: all 0.3s ease;
        }
        .tl-card:hover { transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .tl-header { display: flex; justify-content: space-between; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px; font-weight: 500; }
        .tl-title { font-weight: 700; font-size: 1.1rem; color: var(--dark); margin-bottom: 5px; }
        .tl-type {
            font-size: 0.75rem; padding: 4px 12px; border-radius: 12px; background: var(--primary-light);
            color: var(--primary-hover); text-transform: uppercase; font-weight: 700; display: inline-block; margin-bottom: 8px; letter-spacing: 0.5px;
        }
        .tl-notes { font-size: 0.9rem; margin-top: 10px; font-style: italic; color: #475569; background: var(--bg); padding: 10px; border-radius: 8px; }

        /* FAB */
        .fab {
            position: fixed; bottom: 90px; right: 25px; width: 65px; height: 65px; background: var(--primary); color: white;
            border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 30px;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.5); cursor: pointer; z-index: 100; transition: all 0.3s ease;
        }
        .fab:hover { transform: translateY(-5px) scale(1.05); }

        /* TOMBOL EXPORT */
        .btn-export {
            display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; padding: 15px;
            background: white; border: 2px dashed var(--border); color: var(--text-muted); border-radius: 16px;
            font-weight: 600; font-size: 1rem; margin-bottom: 25px; cursor: pointer; transition: all 0.3s ease;
        }
        .btn-export:hover { border-color: var(--primary); color: var(--primary); background: #f0fdf4; }

        /* MODAL ANIMASI & STYLING */
        .modal {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(5px); z-index: 1050;
            align-items: center; justify-content: center; padding: 20px;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .modal.show { display: flex; opacity: 1; }
        .modal-content {
            background: var(--card-bg); width: 100%; max-width: 500px; border-radius: 24px; padding: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); max-height: 90vh; overflow-y: auto;
            transform: translateY(20px); transition: transform 0.3s ease;
        }
        .modal.show .modal-content { transform: translateY(0); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; font-weight: 700; font-size: 1.3rem; color: var(--dark); }
        .close-btn { font-size: 1.5rem; cursor: pointer; color: var(--text-muted); padding: 5px; line-height: 1; border-radius: 8px; transition: 0.2s; }
        .close-btn:hover { background: #fee2e2; color: #ef4444; }

        /* FORM INPUTS */
        .form-group { margin-bottom: 18px; position: relative; }
        .form-group label { display: block; font-size: 0.9rem; margin-bottom: 8px; font-weight: 600; color: var(--dark); }
        .form-control {
            width: 100%; padding: 14px 16px; border: 1.5px solid var(--border); border-radius: 12px;
            font-size: 1rem; outline: none; transition: all 0.3s ease; background: var(--bg); color: var(--dark);
        }
        .form-control:focus { border-color: var(--primary); background: white; box-shadow: 0 0 0 4px var(--primary-light); }
        .form-control:disabled { background: #e2e8f0; cursor: not-allowed; color: #94a3b8; }
        
        .btn-submit {
            width: 100%; padding: 16px; background: var(--primary); color: white; border: none; border-radius: 12px;
            font-weight: 700; margin-top: 10px; cursor: pointer; font-size: 1.05rem; transition: all 0.3s ease;
        }
        .btn-submit:hover { background: var(--primary-hover); box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3); }

        /* SEARCHABLE DROPDOWN SURAH */
        .custom-select-wrapper { position: relative; }
        .dropdown-list {
            position: absolute; top: 100%; left: 0; right: 0; background: white;
            border: 1px solid var(--border); border-radius: 12px; max-height: 220px; overflow-y: auto;
            z-index: 1060; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-top: 8px;
            display: none; flex-direction: column;
        }
        .dropdown-list.show { display: flex; }
        .dropdown-item {
            padding: 12px 16px; cursor: pointer; border-bottom: 1px solid var(--bg);
            display: flex; justify-content: space-between; align-items: center; transition: 0.2s;
        }
        .dropdown-item:last-child { border-bottom: none; }
        .dropdown-item:hover { background: var(--primary-light); color: var(--primary-hover); }
        .dropdown-item .s-name { font-weight: 600; }
        .dropdown-item .s-ayat { font-size: 0.8rem; color: var(--text-muted); background: var(--bg); padding: 2px 8px; border-radius: 10px; }

        /* RESPONSIVE DESKTOP */
        @media (min-width: 768px) {
            .month-header { border-radius: 20px; margin-top: 25px; }
            .mutabaah-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; align-items: start; }
            .fab { bottom: 40px; right: 40px; }
        }
    </style>
</head>
<body>

    <?php include '../components/nav.php'; ?>

    <div class="container">
        <div class="month-header">
            <?php
            $prev_m = $m - 1; $prev_y = $y;
            if ($prev_m < 1) { $prev_m = 12; $prev_y--; }
            $next_m = $m + 1; $next_y = $y;
            if ($next_m > 12) { $next_m = 1; $next_y++; }
            ?>
            <a href="?m=<?= $prev_m ?>&y=<?= $prev_y ?>" class="month-nav"><i class="fas fa-chevron-left"></i> Sebelumnya</a>
            <h2><?= $month_name ?></h2>
            <a href="?m=<?= $next_m ?>&y=<?= $next_y ?>" class="month-nav">Selanjutnya <i class="fas fa-chevron-right"></i></a>
        </div>

        <?= $pesan; ?>

        <div class="mutabaah-grid">
            <div class="left-panel">
                <div class="card">
                    <div class="card-title">Ringkasan Aktivitas</div>
                    <div class="stat-grid">
                        <div class="stat-item"><span class="stat-label">Hari Aktif</span><span class="stat-value"><?= $hari_aktif ?></span></div>
                        <div class="stat-item"><span class="stat-label">Tilawah</span><span class="stat-value"><?= $stats['tilawah'] ?></span></div>
                        <div class="stat-item"><span class="stat-label">Murojaah</span><span class="stat-value"><?= $stats['murojaah'] ?></span></div>
                        <div class="stat-item"><span class="stat-label">Setoran</span><span class="stat-value"><?= $stats['setoran'] ?></span></div>
                        <div class="stat-item streak">
                            <span class="stat-label"><i class="fas fa-fire"></i> Streak Saat Ini</span>
                            <span class="stat-value"><?= $streak ?> Hari</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">Kalender Kontribusi</div>
                    <div class="cal-grid">
                        <div class="cal-day-label">Min</div><div class="cal-day-label">Sen</div><div class="cal-day-label">Sel</div>
                        <div class="cal-day-label">Rab</div><div class="cal-day-label">Kam</div><div class="cal-day-label">Jum</div><div class="cal-day-label">Sab</div>
                        <?php
                        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $m, $y);
                        $first_day = date('w', mktime(0, 0, 0, $m, 1, $y));

                        for ($i = 0; $i < $first_day; $i++) echo "<div></div>";

                        for ($d = 1; $d <= $days_in_month; $d++) {
                            $date_str = sprintf("%04d-%02d-%02d", $y, $m, $d);
                            $count = isset($cal_data[$date_str]) ? $cal_data[$date_str] : 0;
                            $class = $count > 0 ? 'active' : '';
                            $badge = $count > 1 ? "<div class='cal-badge'>$count</div>" : "";

                            echo "<div class='cal-box $class' onclick='showDaily(\"$date_str\")'>$badge</div>";
                        }
                        ?>
                    </div>
                </div>

                <button class="btn-export" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i> Download Laporan Bulanan
                </button>
            </div>

            <div class="right-panel">
                <div class="card" style="background: transparent; box-shadow: none; padding: 0; border: none;">
                    <div class="card-title" style="padding-left: 5px;">Jejak Langkah (Timeline)</div>

                    <div class="filter-scroll">
                        <button class="filter-btn active" onclick="filterTimeline('all', this)">Semua</button>
                        <button class="filter-btn" onclick="filterTimeline('tilawah', this)">Tilawah</button>
                        <button class="filter-btn" onclick="filterTimeline('murojaah', this)">Murojaah</button>
                        <button class="filter-btn" onclick="filterTimeline('hafalan_baru', this)">Hafalan Baru</button>
                        <button class="filter-btn" onclick="filterTimeline('setoran', this)">Setoran</button>
                    </div>

                    <div class="timeline" id="timeline-container">
                        <?php foreach ($timeline as $tl):
                            $type_label = str_replace('_', ' ', $tl['activity_type']);
                        ?>
                            <div class="tl-card tl-item" data-type="<?= $tl['activity_type'] ?>" data-date="<?= $tl['activity_date'] ?>">
                                <div class="tl-header">
                                    <span><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($tl['activity_date'])) ?></span>
                                    <span><i class="far fa-clock"></i> <?= date('H:i', strtotime($tl['activity_time'])) ?></span>
                                </div>
                                <div class="tl-type"><?= $type_label ?></div>
                                <div class="tl-title">Surah <?= htmlspecialchars($tl['surah']) ?></div>
                                <div style="font-size:0.95rem; color:var(--text-muted); font-weight:500;">
                                    Ayat <?= $tl['ayah_start'] ?> - <?= $tl['ayah_end'] ?>
                                </div>
                                <?php if ($tl['notes']): ?>
                                    <div class="tl-notes">"<?= htmlspecialchars($tl['notes']) ?>"</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($timeline)): ?>
                            <div style="text-align:center; padding: 60px 20px; color:var(--text-muted); background:var(--card-bg); border-radius:20px; border:1px dashed var(--border);">
                                <div style="font-size:3.5rem; margin-bottom:15px; opacity:0.5;">🍃</div>
                                <h3 style="color:var(--dark); margin-bottom:5px;">Belum ada aktivitas</h3>
                                <p>Yuk, mulai catat perjalanan hafalanmu hari ini!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fab" onclick="openModal('modalAdd')"><i class="fas fa-plus"></i></div>

    <div class="modal" id="modalAdd">
        <div class="modal-content">
            <div class="modal-header">
                <span>Catat Aktivitas</span>
                <span class="close-btn" onclick="closeModal('modalAdd')"><i class="fas fa-times"></i></span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Kategori Aktivitas</label>
                    <select name="activity_type" class="form-control" required>
                        <option value="tilawah">📖 Tilawah</option>
                        <option value="murojaah">🔄 Murojaah</option>
                        <option value="hafalan_baru">✨ Hafalan Baru</option>
                        <option value="setoran">🎙️ Setoran</option>
                    </select>
                </div>
                
                <div class="form-group custom-select-wrapper">
                    <label>Nama Surah</label>
                    <input type="hidden" name="surah" id="hidden_surah" required>
                    <input type="text" id="search_surah" class="form-control" placeholder="🔍 Ketik & cari nama surah..." autocomplete="off" required>
                    <div class="dropdown-list" id="surah_dropdown">
                        <div style="padding:15px; text-align:center; color:#64748b; font-size:0.85rem;">Memuat data surah...</div>
                    </div>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex:1;">
                        <label>Ayat Awal</label>
                        <input type="number" name="ayah_start" id="ayah_start" class="form-control" required min="1" disabled placeholder="Pilih Surah">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Ayat Akhir</label>
                        <input type="number" name="ayah_end" id="ayah_end" class="form-control" required min="1" disabled placeholder="Pilih Surah">
                    </div>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex:1;">
                        <label>Tanggal</label>
                        <input type="date" name="activity_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Jam</label>
                        <input type="time" name="activity_time" class="form-control" value="<?= date('H:i') ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Catatan (Opsional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Bagaimana kelancaran hafalanmu?"></textarea>
                </div>
                <button type="submit" name="simpan_aktivitas" class="btn-submit">Simpan Catatan</button>
            </form>
        </div>
    </div>

    <div class="modal" id="modalDetail">
        <div class="modal-content">
            <div class="modal-header">
                <span id="detail-date-title">Aktivitas Harian</span>
                <span class="close-btn" onclick="closeModal('modalDetail')"><i class="fas fa-times"></i></span>
            </div>
            <div class="timeline" id="daily-timeline-container"></div>
        </div>
    </div>

    <script>
        // --- LOGIK MODAL ANIMASI ---
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);
        }
        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        // --- LOGIK FILTER TIMELINE ---
        function filterTimeline(type, btn) {
            document.querySelectorAll('.filter-btn').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');
            const items = document.querySelectorAll('.tl-item');
            items.forEach(item => {
                if (type === 'all' || item.getAttribute('data-type') === type) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // --- LOGIK DETAIL HARIAN KALENDER ---
        function showDaily(dateStr) {
            document.getElementById('detail-date-title').innerText = "Catatan: " + dateStr;
            const container = document.getElementById('daily-timeline-container');
            container.innerHTML = "";
            let found = false;
            document.querySelectorAll('.tl-item').forEach(item => {
                if (item.getAttribute('data-date') === dateStr) {
                    const clone = item.cloneNode(true);
                    clone.style.display = 'block'; 
                    container.appendChild(clone);
                    found = true;
                }
            });
            if (!found) {
                container.innerHTML = `<div style="text-align:center; padding: 25px; color:#64748b; background:#f8fafc; border-radius:12px; border:1px solid #e2e8f0;">Belum ada aktivitas pada tanggal ini.</div>`;
            }
            openModal('modalDetail');
        }

        // --- LOGIK PENCARIAN SURAH DARI API EQURAN.ID ---
        let surahData = [];
        const searchInput = document.getElementById('search_surah');
        const dropdownList = document.getElementById('surah_dropdown');
        const hiddenSurah = document.getElementById('hidden_surah');
        const inputAyahStart = document.getElementById('ayah_start');
        const inputAyahEnd = document.getElementById('ayah_end');

        // Mengambil data surah saat halaman dimuat
        async function fetchSurahList() {
            try {
                const response = await fetch('https://equran.id/api/v2/surat');
                const json = await response.json();
                surahData = json.data;
                renderDropdown(surahData);
            } catch (error) {
                dropdownList.innerHTML = `<div style="padding:15px; text-align:center; color:red;">Gagal memuat data surah. Cek internet.</div>`;
            }
        }

        // Render isi dropdown
        function renderDropdown(dataList) {
            if (dataList.length === 0) {
                dropdownList.innerHTML = `<div style="padding:15px; text-align:center; color:#64748b;">Surah tidak ditemukan.</div>`;
                return;
            }
            let html = '';
            dataList.forEach(surah => {
                html += `
                <div class="dropdown-item" onclick="selectSurah('${surah.namaLatin}', ${surah.jumlahAyat})">
                    <span class="s-name">${surah.nomor}. ${surah.namaLatin}</span>
                    <span class="s-ayat">${surah.jumlahAyat} Ayat</span>
                </div>`;
            });
            dropdownList.innerHTML = html;
        }

        // Ketika Surah Dipilih
        function selectSurah(namaLatin, jumlahAyat) {
            // Set input nilai
            searchInput.value = namaLatin;
            hiddenSurah.value = namaLatin;
            
            // Buka gembok (enable) input ayat
            inputAyahStart.disabled = false;
            inputAyahEnd.disabled = false;
            
            // Set batas MAX ayat sesuai surah yang dipilih
            inputAyahStart.max = jumlahAyat;
            inputAyahEnd.max = jumlahAyat;
            
            // Ubah placeholder untuk instruksi user
            inputAyahStart.placeholder = `Maks: ${jumlahAyat}`;
            inputAyahEnd.placeholder = `Maks: ${jumlahAyat}`;
            
            // Kosongkan nilai sebelumnya jika ada agar user tidak salah input
            inputAyahStart.value = '';
            inputAyahEnd.value = '';

            // Sembunyikan dropdown
            dropdownList.classList.remove('show');
        }

        // Event listener saat user mengetik di kotak pencarian
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            // Filter data surah berdasarkan teks latin
            const filtered = surahData.filter(surah => surah.namaLatin.toLowerCase().includes(query));
            renderDropdown(filtered);
            dropdownList.classList.add('show');
            
            // Jika user merubah teks (mengetik ulang), kunci lagi input ayatnya
            if (hiddenSurah.value !== query) {
                inputAyahStart.disabled = true;
                inputAyahEnd.disabled = true;
                inputAyahStart.placeholder = "Pilih Surah";
                inputAyahEnd.placeholder = "Pilih Surah";
                hiddenSurah.value = '';
            }
        });

        // Buka dropdown saat input diklik
        searchInput.addEventListener('focus', function() {
            dropdownList.classList.add('show');
        });

        // Sembunyikan dropdown jika user klik di luar area
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
                dropdownList.classList.remove('show');
            }
        });

        // Validasi form agar Ayat Akhir tidak lebih kecil dari Ayat Awal (Opsional tambahan UI/UX)
        inputAyahEnd.addEventListener('change', function() {
            if (parseInt(this.value) < parseInt(inputAyahStart.value)) {
                alert("Ayat Akhir tidak boleh lebih kecil dari Ayat Awal.");
                this.value = inputAyahStart.value;
            }
        });

        // Panggil fungsi saat web jalan
        fetchSurahList();
    </script>
</body>
</html>