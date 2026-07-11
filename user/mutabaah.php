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
        $pesan = "<div class='alert alert-success d-flex align-items-center'><i class='fas fa-check-circle me-2'></i> Aktivitas berhasil dicatat!</div>";
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
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            padding-bottom: 80px;
        }

        /* HEADER BULAN */
        .month-header {
            background: var(--card-bg);
            padding: 1rem 1.5rem;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .month-header h2 {
            font-weight: 700;
            color: var(--dark);
        }

        .month-nav-link {
            color: var(--text-muted);
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            background: var(--bg);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .month-nav-link:hover {
            background: var(--border);
            color: var(--dark);
        }

        .alert {
            border-radius: 12px;
            font-weight: 500;
        }

        /* STATISTIK */
        .stat-card {
            background: var(--bg);
            border-radius: 16px;
            padding: 1.2rem;
            border: 1px solid var(--border);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
        }

        .streak-card {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
        }

        .streak-card .stat-label,
        .streak-card .stat-value {
            color: white;
        }

        /* KALENDER */
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .cal-day-label {
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 4px;
        }

        .cal-box {
            aspect-ratio: 1;
            border-radius: 10px;
            background-color: #f1f5f9;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.95rem;
            color: rgba(0, 0, 0, 0.25);
            user-select: none;
        }

        .cal-box.active {
            background-color: var(--primary);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
            color: white;
            font-weight: 700;
        }

        .cal-box:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            color: var(--dark);
        }

        .cal-box.active:hover {
            border-color: transparent;
            transform: scale(1.08);
            color: white;
        }

        .cal-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            font-size: 0.7rem;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.5);
            border: 2px solid white;
            z-index: 5;
        }

        /* TIMELINE */
        .tl-card {
            background: var(--card-bg);
            padding: 1.2rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            border-left: 6px solid var(--primary);
            transition: all 0.3s ease;
        }

        .tl-card:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .tl-type {
            font-size: 0.75rem;
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary-hover);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .tl-notes {
            font-style: italic;
            background: var(--bg);
            padding: 0.8rem;
            border-radius: 10px;
            margin-top: 0.8rem;
            color: #475569;
        }

        /* FAB */
        .fab {
            position: fixed;
            bottom: 100px;
            right: 25px;
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
            cursor: pointer;
            z-index: 1000;
            transition: all 0.3s ease;
            border: none;
        }

        .fab:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 12px 30px rgba(5, 150, 105, 0.5);
        }

        /* MODAL & BOTTOM SHEET */
        .modal-backdrop-custom {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
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
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal-backdrop-custom.show .modal-content-custom {
            transform: translateY(0);
        }

        /* BOTTOM SHEET UNTUK MOBILE */
        @media (max-width: 767.98px) {
            .modal-backdrop-custom {
                align-items: flex-end;
            }

            .modal-content-custom {
                max-width: 100%;
                border-radius: 24px 24px 0 0;
                max-height: 85vh;
                transform: translateY(100%);
            }

            .modal-backdrop-custom.show .modal-content-custom {
                transform: translateY(0);
            }

            .fab {
                bottom: 90px;
                right: 20px;
            }
        }

        /* FILTER BUTTONS */
        .filter-scroll {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .filter-scroll::-webkit-scrollbar {
            height: 0;
        }

        .filter-btn-custom {
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            font-size: 0.9rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s ease;
            color: var(--text-muted);
            font-weight: 500;
        }

        .filter-btn-custom.active,
        .filter-btn-custom:hover {
            background: var(--dark);
            color: white;
            border-color: var(--dark);
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

        .dropdown-item-custom:last-child {
            border-bottom: none;
        }

        .dropdown-item-custom:hover {
            background: var(--primary-light);
            color: var(--primary-hover);
        }

        .dropdown-item-custom .s-name {
            font-weight: 600;
        }

        .dropdown-item-custom .s-ayat {
            font-size: 0.8rem;
            color: var(--text-muted);
         background: var(--bg);
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <?php include '../components/nav.php'; ?>

    <div class="container">
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
            <a href="?m=<?= $prev_m ?>&y=<?= $prev_y ?>" class="month-nav-link">
                <i class="fas fa-chevron-left me-1"></i> Sebelumnya
            </a>
            <h2 class="mb-0 fs-4"><?= $month_name ?></h2>
            <a href="?m=<?= $next_m ?>&y=<?= $next_y ?>" class="month-nav-link">
                Selanjutnya <i class="fas fa-chevron-right ms-1"></i>
            </a>
        </div>

        <!-- PESAN ALERT -->
        <?php if ($pesan): ?>
            <?= $pesan ?>
        <?php endif; ?>

        <!-- GRID UTAMA -->
        <div class="row g-4">
            <!-- KOLOM KIRI: STATS + KALENDER + EXPORT -->
            <div class="col-lg-5">
                <!-- Ringkasan Aktivitas -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2"></i>Ringkasan Aktivitas</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Hari Aktif</div>
                                    <div class="stat-value"><?= $hari_aktif ?></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Tilawah</div>
                                    <div class="stat-value"><?= $stats['tilawah'] ?></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Murojaah</div>
                                    <div class="stat-value"><?= $stats['murojaah'] ?></div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card">
                                    <div class="stat-label">Setoran</div>
                                    <div class="stat-value"><?= $stats['setoran'] ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="stat-card streak-card d-flex justify-content-between align-items-center">
                                    <span class="stat-label"><i class="fas fa-fire me-1"></i> Streak Saat Ini</span>
                                    <span class="stat-value"><?= $streak ?> Hari</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kalender Kontribusi -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="fas fa-calendar-check me-2"></i>Kalender Kontribusi</h5>
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
                                // Angka tanggal dengan opasitas semi transparan
                                echo "<div class='cal-box $class' onclick='showDaily(\"$date_str\")'>
                                        <span style='opacity: 0.7;'>$d</span>
                                        $badge
                                      </div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Tombol Download Laporan -->
                <button class="btn btn-outline-secondary w-100 rounded-3 py-3 fw-semibold" onclick="window.print()">
                    <i class="fas fa-file-pdf me-2"></i> Download Laporan Bulanan
                </button>
            </div>

            <!-- KOLOM KANAN: TIMELINE -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="fas fa-history me-2"></i>Jejak Langkah (Timeline)</h5>

                        <!-- Filter -->
                        <div class="filter-scroll">
                            <button class="filter-btn-custom active" onclick="filterTimeline('all', this)">Semua</button>
                            <button class="filter-btn-custom" onclick="filterTimeline('tilawah', this)">Tilawah</button>
                            <button class="filter-btn-custom" onclick="filterTimeline('murojaah', this)">Murojaah</button>
                            <button class="filter-btn-custom" onclick="filterTimeline('hafalan_baru', this)">Hafalan Baru</button>
                            <button class="filter-btn-custom" onclick="filterTimeline('setoran', this)">Setoran</button>
                        </div>

                        <!-- Timeline -->
                        <div id="timeline-container" class="d-flex flex-column gap-3">
                            <?php foreach ($timeline as $tl):
                                $type_label = str_replace('_', ' ', $tl['activity_type']);
                            ?>
                                <div class="tl-card tl-item" data-type="<?= $tl['activity_type'] ?>" data-date="<?= $tl['activity_date'] ?>">
                                    <div class="d-flex justify-content-between text-muted small mb-2">
                                        <span><i class="fas fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($tl['activity_date'])) ?></span>
                                        <span><i class="fas fa-clock me-1"></i> <?= date('H:i', strtotime($tl['activity_time'])) ?></span>
                                    </div>
                                    <span class="tl-type mb-2"><?= $type_label ?></span>
                                    <div class="fw-bold fs-6">Surah <?= htmlspecialchars($tl['surah']) ?></div>
                                    <div class="text-muted small fw-medium">
                                        Ayat <?= $tl['ayah_start'] ?> - <?= $tl['ayah_end'] ?>
                                    </div>
                                    <?php if ($tl['notes']): ?>
                                        <div class="tl-notes">"<?= htmlspecialchars($tl['notes']) ?>"</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($timeline)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-seedling fa-3x mb-3 opacity-50"></i>
                                    <h5>Belum ada aktivitas</h5>
                                    <p>Yuk, mulai catat perjalanan hafalanmu hari ini!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAB -->
    <button class="fab" onclick="openModal('modalAdd')">
        <i class="fas fa-plus"></i>
    </button>

    <!-- MODAL / BOTTOM SHEET: Tambah Aktivitas -->
    <div class="modal-backdrop-custom" id="modalAdd">
        <div class="modal-content-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Catat Aktivitas</h5>
                <button type="button" class="btn-close" onclick="closeModal('modalAdd')"></button>
            </div>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori Aktivitas</label>
                    <select name="activity_type" class="form-select rounded-3" required>
                        <option value="tilawah">📖 Tilawah</option>
                        <option value="murojaah">🔄 Murojaah</option>
                        <option value="hafalan_baru">✨ Hafalan Baru</option>
                        <option value="setoran">🎙️ Setoran</option>
                    </select>
                </div>

                <div class="mb-3 custom-select-wrapper">
                    <label class="form-label fw-semibold">Nama Surah</label>
                    <input type="hidden" name="surah" id="hidden_surah" required>
                    <input type="text" id="search_surah" class="form-control rounded-3" placeholder="🔍 Ketik & cari nama surah..." autocomplete="off" required>
                    <div class="dropdown-list" id="surah_dropdown">
                        <div class="p-3 text-center text-muted small">Memuat data surah...</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Ayat Awal</label>
                        <input type="number" name="ayah_start" id="ayah_start" class="form-control rounded-3" required min="1" disabled placeholder="Pilih Surah">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Ayat Akhir</label>
                        <input type="number" name="ayah_end" id="ayah_end" class="form-control rounded-3" required min="1" disabled placeholder="Pilih Surah">
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="activity_date" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Jam</label>
                        <input type="time" name="activity_time" class="form-control rounded-3" value="<?= date('H:i') ?>" required>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label fw-semibold">Catatan (Opsional)</label>
                    <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Bagaimana kelancaran hafalanmu?"></textarea>
                </div>
                <button type="submit" name="simpan_aktivitas" class="btn btn-success w-100 py-3 rounded-3 fw-bold mt-2">
                    <i class="fas fa-save me-2"></i> Simpan Catatan
                </button>
            </form>
        </div>
    </div>

    <!-- MODAL / BOTTOM SHEET: Detail Harian -->
    <div class="modal-backdrop-custom" id="modalDetail">
        <div class="modal-content-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0" id="detail-date-title">Aktivitas Harian</h5>
                <button type="button" class="btn-close" onclick="closeModal('modalDetail')"></button>
            </div>
            <div id="daily-timeline-container" class="d-flex flex-column gap-3"></div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // MODAL / BOTTOM SHEET LOGIC
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
        // Tutup modal jika klik di luar area konten
        document.querySelectorAll('.modal-backdrop-custom').forEach(backdrop => {
            backdrop.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // FILTER TIMELINE
        function filterTimeline(type, btn) {
            document.querySelectorAll('.filter-btn-custom').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('.tl-item').forEach(item => {
                item.style.display = (type === 'all' || item.getAttribute('data-type') === type) ? 'block' : 'none';
            });
        }

        // DETAIL HARIAN DARI KALENDER
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
                container.innerHTML = `<div class="text-center py-4 text-muted bg-light rounded-3">Belum ada aktivitas pada tanggal ini.</div>`;
            }
            openModal('modalDetail');
        }

        // SURAH SEARCH LOGIC
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
                dropdownList.innerHTML = `<div class="p-3 text-center text-danger">Gagal memuat data surah. Periksa koneksi internet.</div>`;
            }
        }

        function renderDropdown(dataList) {
            if (dataList.length === 0) {
                dropdownList.innerHTML = `<div class="p-3 text-center text-muted">Surah tidak ditemukan.</div>`;
                return;
            }
            let html = '';
            dataList.forEach(surah => {
                html += `
                <div class="dropdown-item-custom" onclick="selectSurah('${surah.namaLatin}', ${surah.jumlahAyat})">
                    <span class="s-name">${surah.nomor}. ${surah.namaLatin}</span>
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
            inputAyahStart.value = '';
            inputAyahEnd.value = '';
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

        searchInput.addEventListener('focus', () => dropdownList.classList.add('show'));

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
                dropdownList.classList.remove('show');
            }
        });

        inputAyahEnd.addEventListener('change', function() {
            if (parseInt(this.value) < parseInt(inputAyahStart.value)) {
                alert("Ayat Akhir tidak boleh lebih kecil dari Ayat Awal.");
                this.value = inputAyahStart.value;
            }
        });

        fetchSurahList();
    </script>
</body>

</html>