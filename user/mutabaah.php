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
        $pesan = "<div class='alert alert-success'>Aktivitas berhasil dicatat!</div>";
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
    <style>
        :root {
            --primary: #059669;
            --primary-light: #d1fae5;
            --dark: #1f2937;
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Padding untuk mobile (memberi ruang footer nav) */
        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 90px;
        }

        /* Header Navigasi Bulan */
        .month-header {
            background: var(--card-bg);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .month-header h2 {
            font-size: 1.2rem;
            color: var(--primary);
            font-weight: bold;
        }

        .month-nav a {
            text-decoration: none;
            color: var(--dark);
            font-weight: bold;
            padding: 8px 15px;
            background: var(--bg);
            border-radius: 8px;
            transition: 0.2s;
        }

        .month-nav a:hover {
            background: var(--border);
        }

        .container {
            padding: 0 20px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.95rem;
            background-color: var(--primary-light);
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        /* Grid Layout Utama */
        .mutabaah-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Gaya Card Umum */
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--dark);
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }

        /* Summary Grid */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-item {
            background: var(--bg);
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }

        .streak {
            grid-column: span 2;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .streak .stat-label {
            color: rgba(255, 255, 255, 0.8);
        }

        .streak .stat-value {
            color: white;
        }

        /* Calendar */
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .cal-day-label {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .cal-box {
            aspect-ratio: 1;
            border-radius: 6px;
            background-color: #ebedf0;
            position: relative;
            cursor: pointer;
            transition: 0.2s;
            border: 1px solid rgba(27, 31, 35, 0.06);
        }

        .cal-box.active {
            background-color: var(--primary);
            border-color: rgba(27, 31, 35, 0.1);
        }

        .cal-box:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .cal-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.65rem;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* Timeline & Filter */
        .filter-scroll {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .filter-scroll::-webkit-scrollbar {
            height: 4px;
        }

        .filter-scroll::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .filter-btn {
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--bg);
            font-size: 0.85rem;
            cursor: pointer;
            white-space: nowrap;
            transition: 0.3s;
            color: var(--text-muted);
            font-weight: 600;
        }

        .filter-btn:hover {
            background: #e5e7eb;
        }

        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .tl-card {
            background: var(--bg);
            padding: 15px;
            border-radius: 12px;
            border-left: 5px solid var(--primary);
            transition: 0.2s;
        }

        .tl-card:hover {
            transform: translateX(5px);
        }

        .tl-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .tl-title {
            font-weight: bold;
            font-size: 1.05rem;
            color: var(--dark);
            margin-bottom: 4px;
        }

        .tl-type {
            font-size: 0.7rem;
            padding: 3px 10px;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary);
            text-transform: uppercase;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 6px;
        }

        /* FAB */
        .fab {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 28px;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.4);
            cursor: pointer;
            z-index: 100;
            transition: 0.3s;
        }

        .fab:hover {
            transform: scale(1.05);
        }

        /* Export Button */
        .btn-export {
            display: block;
            width: 100%;
            padding: 12px;
            background: white;
            border: 2px dashed var(--primary);
            color: var(--primary);
            border-radius: 12px;
            font-weight: bold;
            margin-bottom: 20px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
        }

        .btn-export:hover {
            background: var(--primary-light);
        }

        /* RESPONSIVE DESKTOP (Grid 2 Kolom) */
        @media (min-width: 768px) {
            .month-header {
                border-radius: 16px;
                margin-top: 20px;
            }

            .mutabaah-grid {
                display: grid;
                grid-template-columns: 1fr 1.2fr;
                gap: 30px;
                align-items: start;
            }

            .fab {
                bottom: 40px;
                right: 40px;
            }

            /* Sesuaikan FAB karena tidak ada footer nav */
            .filter-scroll {
                flex-wrap: wrap;
            }
        }

        /* Modal Settings */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1050;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: var(--card-bg);
            width: 100%;
            max-width: 500px;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 1.2rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }

        .close-btn {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-muted);
            transition: 0.2s;
        }

        .close-btn:hover {
            color: red;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 10px;
            cursor: pointer;
            font-size: 1rem;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #047857;
        }

        @media print {
            body {
                padding: 0 !important;
                background: white;
            }

            .app-nav,
            .fab,
            .filter-scroll,
            .btn-export,
            .month-nav {
                display: none !important;
            }

            .container {
                max-width: 100%;
                padding: 0;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ccc;
                margin-bottom: 10px;
                break-inside: avoid;
            }

            .tl-card {
                border-left: 2px solid #ccc;
            }
        }
    </style>
</head>

<body>

    <!-- Navigasi Utama (Include) -->
    <?php include '../components/nav.php'; ?>

    <div class="container">
        <!-- Header Bulan -->
        <div class="month-header">
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
            <a href="?m=<?= $prev_m ?>&y=<?= $prev_y ?>" class="month-nav">❮ Prev</a>
            <h2><?= $month_name ?></h2>
            <a href="?m=<?= $next_m ?>&y=<?= $next_y ?>" class="month-nav">Next ❯</a>
        </div>

        <?= $pesan; ?>

        <!-- Grid Responsif -->
        <div class="mutabaah-grid">

            <!-- KOLOM KIRI (Summary & Calendar) -->
            <div class="left-panel">
                <div class="card">
                    <div class="card-title">Ringkasan Bulan Ini</div>
                    <div class="stat-grid">
                        <div class="stat-item"><span class="stat-label">Hari Aktif</span><span class="stat-value"><?= $hari_aktif ?></span></div>
                        <div class="stat-item"><span class="stat-label">Tilawah</span><span class="stat-value"><?= $stats['tilawah'] ?></span></div>
                        <div class="stat-item"><span class="stat-label">Murojaah</span><span class="stat-value"><?= $stats['murojaah'] ?></span></div>
                        <div class="stat-item"><span class="stat-label">Setoran</span><span class="stat-value"><?= $stats['setoran'] ?></span></div>
                        <div class="stat-item streak"><span class="stat-label">🔥 Streak Saat Ini</span><span class="stat-value"><?= $streak ?> Hari</span></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">Kalender Aktivitas</div>
                    <div class="cal-grid">
                        <div class="cal-day-label">Min</div>
                        <div class="cal-day-label">Sen</div>
                        <div class="cal-day-label">Sel</div>
                        <div class="cal-day-label">Rab</div>
                        <div class="cal-day-label">Kam</div>
                        <div class="cal-day-label">Jum</div>
                        <div class="cal-day-label">Sab</div>
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

                <button class="btn-export" onclick="window.print()">📥 Download Laporan (PDF)</button>
            </div>

            <!-- KOLOM KANAN (Filter & Timeline) -->
            <div class="right-panel">
                <div class="card">
                    <div class="card-title">Jejak Langkah (Timeline)</div>

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
                                    <span>📅 <?= date('d M Y', strtotime($tl['activity_date'])) ?></span>
                                    <span>⏱️ <?= date('H:i', strtotime($tl['activity_time'])) ?></span>
                                </div>
                                <div class="tl-type"><?= $type_label ?></div>
                                <div class="tl-title">Surah <?= htmlspecialchars($tl['surah']) ?></div>
                                <div style="font-size:0.9rem; color:#4b5563;">Ayat <?= $tl['ayah_start'] ?> - <?= $tl['ayah_end'] ?></div>
                                <?php if ($tl['notes']): ?>
                                    <div style="font-size:0.85rem; margin-top:8px; font-style:italic; border-top:1px dashed #d1d5db; padding-top:8px;">"<?= htmlspecialchars($tl['notes']) ?>"</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($timeline)): ?>
                            <div style="text-align:center; padding: 40px 20px; color:var(--text-muted); background:var(--bg); border-radius:12px;">
                                <div style="font-size:3rem; margin-bottom:10px;">🍃</div>
                                <p>Belum ada catatan aktivitas di bulan ini.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Activity FAB -->
    <div class="fab" onclick="document.getElementById('modalAdd').style.display='flex'">+</div>

    <!-- Modal: Add Activity -->
    <div class="modal" id="modalAdd">
        <div class="modal-content">
            <div class="modal-header">
                <span>Catat Aktivitas Manual</span>
                <span class="close-btn" onclick="document.getElementById('modalAdd').style.display='none'">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Kategori Aktivitas</label>
                    <select name="activity_type" class="form-control" required>
                        <option value="tilawah">Tilawah</option>
                        <option value="murojaah">Murojaah</option>
                        <option value="hafalan_baru">Hafalan Baru</option>
                        <option value="setoran">Setoran</option>
                    </select>
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
                    <label>Nama Surah</label>
                    <input type="text" name="surah" class="form-control" placeholder="Contoh: Al-Mulk" required>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex:1;">
                        <label>Ayat Awal</label>
                        <input type="number" name="ayah_start" class="form-control" required min="1">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Ayat Akhir</label>
                        <input type="number" name="ayah_end" class="form-control" required min="1">
                    </div>
                </div>
                <div class="form-group">
                    <label>Catatan (Opsional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Tuliskan catatan atau refleksi progresmu di sini..."></textarea>
                </div>
                <button type="submit" name="simpan_aktivitas" class="btn-submit">Simpan Catatan</button>
            </form>
        </div>
    </div>

    <!-- Modal: Daily Detail (Dipicu dari klik Kalender) -->
    <div class="modal" id="modalDetail">
        <div class="modal-content">
            <div class="modal-header">
                <span id="detail-date-title">Aktivitas Harian</span>
                <span class="close-btn" onclick="document.getElementById('modalDetail').style.display='none'">&times;</span>
            </div>
            <div class="timeline" id="daily-timeline-container">
                <!-- Konten akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>

    <script>
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

        function showDaily(dateStr) {
            document.getElementById('detail-date-title').innerText = "Catatan: " + dateStr;
            const container = document.getElementById('daily-timeline-container');
            container.innerHTML = "";

            let found = false;
            document.querySelectorAll('.tl-item').forEach(item => {
                if (item.getAttribute('data-date') === dateStr) {
                    const clone = item.cloneNode(true);
                    clone.style.display = 'block'; // Paksa tampil walau sedang difilter
                    container.appendChild(clone);
                    found = true;
                }
            });

            if (!found) {
                container.innerHTML = `
                    <div style="text-align:center; padding: 20px; color:#6b7280; background:#f9fafb; border-radius:8px;">
                        Tidak ada aktivitas yang tercatat pada tanggal ini.
                    </div>`;
            }

            document.getElementById('modalDetail').style.display = 'flex';
        }
    </script>
</body>

</html>