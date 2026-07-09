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

// Hitung Streak (Berjalan mundur dari hari ini)
$streak = 0;
$cek_tgl = date('Y-m-d');
while (true) {
    $cek_q = mysqli_query($conn, "SELECT id FROM mutabaah WHERE user_id = '$user_id' AND activity_date = '$cek_tgl' LIMIT 1");
    if (mysqli_num_rows($cek_q) > 0) {
        $streak++;
        $cek_tgl = date('Y-m-d', strtotime("-1 day", strtotime($cek_tgl)));
    } else {
        // Cek jika kemarin ada aktivitas, tapi hari ini belum (streak belum putus total)
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
            --bg: #f9fafb;
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

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 90px;
        }

        .header {
            background: var(--card-bg);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .header h2 {
            font-size: 1.2rem;
            color: var(--primary);
        }

        .month-nav a {
            text-decoration: none;
            color: var(--dark);
            font-weight: bold;
            padding: 5px 10px;
            background: var(--bg);
            border-radius: 6px;
        }

        .container {
            padding: 15px;
            max-width: 600px;
            margin: 0 auto;
        }

        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.9rem;
            background-color: var(--primary-light);
            color: #065f46;
        }

        /* Summary Card */
        .summary-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .summary-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 0.9rem;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            background: var(--bg);
            border-radius: 6px;
        }

        .streak {
            grid-column: span 2;
            background: #fffbeb;
            color: #b45309;
            text-align: center;
            font-weight: bold;
            font-size: 1rem;
        }

        /* Calendar GitHub Style */
        .calendar-section {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .cal-day-label {
            text-align: center;
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .cal-box {
            aspect-ratio: 1;
            border-radius: 4px;
            background-color: var(--border);
            position: relative;
            cursor: pointer;
            transition: 0.2s;
        }

        .cal-box.active {
            background-color: var(--primary);
        }

        .cal-box:hover {
            transform: scale(1.1);
        }

        .cal-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            font-size: 0.6rem;
            background: white;
            color: var(--primary);
            border-radius: 50%;
            width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Export Button */
        .btn-export {
            width: 100%;
            padding: 12px;
            background: white;
            border: 1px solid var(--primary);
            color: var(--primary);
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .btn-export:hover {
            background: var(--primary-light);
        }

        /* Filter & Timeline */
        .filter-scroll {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            margin-bottom: 15px;
            padding-bottom: 5px;
        }

        .filter-btn {
            padding: 6px 15px;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            font-size: 0.85rem;
            cursor: pointer;
            white-space: nowrap;
        }

        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .tl-card {
            background: var(--card-bg);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid var(--primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .tl-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 8px;
            border-bottom: 1px dashed var(--border);
            padding-bottom: 5px;
        }

        .tl-title {
            font-weight: bold;
            font-size: 1rem;
            color: var(--dark);
            margin-bottom: 3px;
        }

        .tl-type {
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 10px;
            background: var(--primary-light);
            color: var(--primary);
            text-transform: uppercase;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 5px;
        }

        /* FAB */
        .fab {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 56px;
            height: 56px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.4);
            cursor: pointer;
            z-index: 100;
            transition: 0.3s;
        }

        .fab:active {
            transform: scale(0.9);
        }

        /* Modal Base */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: var(--card-bg);
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            padding: 20px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .close-btn {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-muted);
        }

        /* Form elements */
        .form-group {
            margin-bottom: 12px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 10px;
            cursor: pointer;
        }

        /* Bottom Nav */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--card-bg);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 90;
        }

        .nav-item {
            text-align: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .nav-item.active {
            color: var(--primary);
        }

        .nav-icon {
            font-size: 1.4rem;
        }

        /* Print Media for Report Export */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .header,
            .bottom-nav,
            .fab,
            .filter-scroll,
            .btn-export,
            .calendar-section {
                display: none !important;
            }

            .container {
                max-width: 100%;
                padding: 0;
            }

            .tl-card {
                border: 1px solid #ccc;
                break-inside: avoid;
                margin-bottom: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="header">
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
        <a href="?m=<?= $prev_m ?>&y=<?= $prev_y ?>" class="month-nav">❮</a>
        <h2><?= $month_name ?></h2>
        <a href="?m=<?= $next_m ?>&y=<?= $next_y ?>" class="month-nav">❯</a>
    </div>

    <div class="container">
        <?= $pesan; ?>

        <!-- 1. Monthly Summary -->
        <div class="summary-card">
            <div class="summary-header">Ringkasan Aktivitas</div>
            <div class="stat-grid">
                <div class="stat-item"><span>Hari Aktif</span> <strong><?= $hari_aktif ?> Hari</strong></div>
                <div class="stat-item"><span>Tilawah</span> <strong><?= $stats['tilawah'] ?>x</strong></div>
                <div class="stat-item"><span>Murojaah</span> <strong><?= $stats['murojaah'] ?>x</strong></div>
                <div class="stat-item"><span>Hafalan Baru</span> <strong><?= $stats['hafalan_baru'] ?>x</strong></div>
                <div class="stat-item"><span>Setoran</span> <strong><?= $stats['setoran'] ?>x</strong></div>
                <div class="stat-item streak">🔥 Streak: <?= $streak ?> Hari</div>
            </div>
        </div>

        <!-- 2. Activity Calendar -->
        <div class="calendar-section">
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
                $first_day = date('w', mktime(0, 0, 0, $m, 1, $y)); // 0 (Sun) to 6 (Sat)

                // Empty slots before first day
                for ($i = 0; $i < $first_day; $i++) echo "<div></div>";

                // Days of the month
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

        <!-- Export Button -->
        <button class="btn-export" onclick="window.print()">📥 Export Monthly Report (PDF)</button>

        <!-- 4 & 5. Filter & Timeline -->
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
                        <span><?= date('d M Y', strtotime($tl['activity_date'])) ?></span>
                        <span><?= date('H:i', strtotime($tl['activity_time'])) ?> WIB</span>
                    </div>
                    <div class="tl-type"><?= $type_label ?></div>
                    <div class="tl-title">Surah <?= htmlspecialchars($tl['surah']) ?></div>
                    <div style="font-size:0.85rem; color:#4b5563;">Ayat <?= $tl['ayah_start'] ?> - <?= $tl['ayah_end'] ?></div>
                    <?php if ($tl['notes']): ?>
                        <div style="font-size:0.8rem; margin-top:5px; font-style:italic;">"<?= htmlspecialchars($tl['notes']) ?>"</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php if (empty($timeline)): ?>
                <p style="text-align:center; color:#6b7280; font-size:0.9rem; padding: 20px;">Belum ada aktivitas di bulan ini.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add Activity FAB -->
    <div class="fab" onclick="document.getElementById('modalAdd').style.display='flex'">+</div>

    <!-- Modal: Add Activity -->
    <div class="modal" id="modalAdd">
        <div class="modal-content">
            <div class="modal-header">
                <span>Tambah Aktivitas Manual</span>
                <span class="close-btn" onclick="document.getElementById('modalAdd').style.display='none'">&times;</span>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Jenis Aktivitas</label>
                    <select name="activity_type" class="form-control" required>
                        <option value="tilawah">Tilawah</option>
                        <option value="murojaah">Murojaah</option>
                        <option value="hafalan_baru">Hafalan Baru</option>
                        <option value="setoran">Setoran</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px;">
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
                <div style="display: flex; gap: 10px;">
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
                    <textarea name="notes" class="form-control" rows="2" placeholder="Catatan progress..."></textarea>
                </div>
                <button type="submit" name="simpan_aktivitas" class="btn-submit">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Modal: Daily Detail (Dipicu dari klik Kalender) -->
    <div class="modal" id="modalDetail">
        <div class="modal-content">
            <div class="modal-header">
                <span id="detail-date-title">Detail Aktivitas</span>
                <span class="close-btn" onclick="document.getElementById('modalDetail').style.display='none'">&times;</span>
            </div>
            <div class="timeline" id="daily-timeline-container">
                <!-- Diisi via JS -->
            </div>
        </div>
    </div>

    <div class="bottom-nav">
        <a href="dashboard.php" class="nav-item"><span class="nav-icon">📖</span><span>Qur'an</span></a>
        <a href="mutabaah.php" class="nav-item active"><span class="nav-icon">📊</span><span>Mutabaah</span></a>
        <a href="#" class="nav-item"><span class="nav-icon">🏆</span><span>Target</span></a>
        <a href="../logout.php" class="nav-item"><span class="nav-icon">🚪</span><span>Keluar</span></a>
    </div>

    <script>
        // 5. Activity Filter Logic
        function filterTimeline(type, btn) {
            // Update Active Button Style
            document.querySelectorAll('.filter-btn').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');

            // Filter Timeline Cards
            const items = document.querySelectorAll('.tl-item');
            items.forEach(item => {
                if (type === 'all' || item.getAttribute('data-type') === type) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // 3. Daily Activity Detail Logic (klik dari Kalender)
        function showDaily(dateStr) {
            document.getElementById('detail-date-title').innerText = "Aktivitas: " + dateStr;
            const container = document.getElementById('daily-timeline-container');
            container.innerHTML = ""; // Bersihkan kontainer

            let found = false;
            // Ambil semua data dari timeline yang ada di DOM dan filter berdasarkan tanggal kalender
            document.querySelectorAll('.tl-item').forEach(item => {
                if (item.getAttribute('data-date') === dateStr) {
                    container.appendChild(item.cloneNode(true));
                    found = true;
                }
            });

            if (!found) {
                container.innerHTML = "<p style='text-align:center; font-size:0.9rem;'>Tidak ada aktivitas pada tanggal ini.</p>";
            } else {
                // Pastikan yang di-clone terlihat (berjaga-jaga jika tersembunyi karena filter)
                container.querySelectorAll('.tl-item').forEach(el => el.style.display = 'block');
            }

            document.getElementById('modalDetail').style.display = 'flex';
        }
    </script>
</body>

</html>