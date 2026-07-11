<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// --- 1. MENGAMBIL DATA PROGRESS HARI INI ---
// Menghitung jumlah ayat yang diinput hari ini
$q_today = mysqli_query($conn, "SELECT SUM(ayah_end - ayah_start + 1) as total_today FROM mutabaah WHERE user_id = '$user_id' AND activity_date = CURDATE()");
$row_today = mysqli_fetch_assoc($q_today);
$progress_ayat = (int)$row_today['total_today'];

// Target default (Bisa dikembangkan nanti jika ada tabel setting khusus user)
$target_ayat = 5; 
$sisa_ayat = $target_ayat - $progress_ayat;

// Kalkulasi persentase
$persentase = ($progress_ayat / $target_ayat) * 100;
if ($persentase > 100) $persentase = 100;

// Logika Pesan Motivasi
if ($sisa_ayat > 0) {
    $pesan_motivasi = "Tinggal $sisa_ayat ayat lagi untuk mencapai target hari ini. Semangat! 🔥";
    $pesan_color = "#f59e0b"; // Oranye
    $pesan_bg = "#fef3c7";
} else {
    $pesan_motivasi = "Alhamdulillah, target hari ini tercapai! Luar biasa! 🎉";
    $pesan_color = "#059669"; // Hijau
    $pesan_bg = "#d1fae5";
}

// --- 2. MENGAMBIL DATA STATISTIK GLOBAL (ALL TIME) ---
// Menghitung total ayat keseluruhan dan jumlah hari aktif unik (Streak)
$q_all = mysqli_query($conn, "SELECT SUM(ayah_end - ayah_start + 1) as total_all, COUNT(DISTINCT activity_date) as active_days FROM mutabaah WHERE user_id = '$user_id'");
$row_all = mysqli_fetch_assoc($q_all);

$total_ayat_hafal = (int)$row_all['total_all'];
$hari_aktif = (int)$row_all['active_days'];

// Estimasi Halaman & Juz (Standar: 1 Hal = ~15 Ayat, 1 Juz = ~20 Hal / 300 Ayat)
$total_halaman = floor($total_ayat_hafal / 15);
$total_juz = floor($total_ayat_hafal / 300);

// --- 3. MENGAMBIL MILESTONE (Surah & Ayat Terakhir) ---
$q_last = mysqli_query($conn, "SELECT surah, ayah_end FROM mutabaah WHERE user_id = '$user_id' ORDER BY activity_date DESC, activity_time DESC LIMIT 1");
if(mysqli_num_rows($q_last) > 0) {
    $row_last = mysqli_fetch_assoc($q_last);
    $next_surah_no = $row_last['surah'];
    $next_ayat = $row_last['ayah_end'] + 1;
} else {
    $next_surah_no = 1; // Default
    $next_ayat = 1;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target Hafalan - Hifzly</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #059669; --primary-light: #d1fae5; --dark: #1e293b;
            --text-muted: #64748b; --bg: #f8fafc; --card-bg: #ffffff;
            --border: #e2e8f0; --spacing: 24px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg); color: var(--dark); padding-bottom: 100px; -webkit-tap-highlight-color: transparent; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }

        /* 1. Header */
        .page-header { text-align: center; margin-bottom: var(--spacing); padding-top: 10px; }
        .page-title { font-size: 1.6rem; font-weight: 700; color: var(--dark); margin-bottom: 6px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .page-subtitle { font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; padding: 0 15px; }

        /* 2. Segmented Control */
        .segmented-control {
            display: flex; background: #f1f5f9; padding: 4px; border-radius: 14px;
            margin-bottom: var(--spacing); position: relative;
        }
        .segment-btn {
            flex: 1; padding: 10px 0; text-align: center; font-size: 0.9rem; font-weight: 600;
            color: var(--text-muted); cursor: pointer; border-radius: 10px; transition: 0.3s ease; z-index: 2;
        }
        .segment-btn.active { color: white; background: var(--primary); box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2); }

        /* Card Global Style */
        .card {
            background: var(--card-bg); border-radius: 20px; padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); border: 1px solid var(--border);
            margin-bottom: var(--spacing);
        }

        /* 3. Current Target Card */
        .target-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .th-title { font-size: 1.1rem; font-weight: 700; color: var(--dark); }
        .th-value { font-size: 1.4rem; font-weight: 700; color: var(--primary); }
        
        .progress-meta { display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 600; margin-bottom: 10px; color: var(--text-muted); }
        .progress-bar-bg { width: 100%; height: 10px; background: #e2e8f0; border-radius: 10px; overflow: hidden; margin-bottom: 15px; }
        .progress-bar-fill { height: 100%; background: var(--primary); border-radius: 10px; width: <?= $persentase ?>%; transition: 1s cubic-bezier(0.4, 0, 0.2, 1); }
        
        .target-footer { display: flex; flex-direction: column; gap: 15px; align-items: flex-start; }
        .tf-text { font-size: 0.85rem; font-weight: 600; padding: 8px 12px; border-radius: 12px; width: 100%; line-height: 1.5; }
        
        .btn-outline { 
            border: 1.5px solid var(--primary); color: var(--primary); background: transparent; 
            padding: 10px 20px; border-radius: 12px; font-weight: 600; font-size: 0.9rem; 
            cursor: pointer; transition: 0.2s; text-decoration: none; width: 100%; text-align: center;
            display: inline-block;
        }
        .btn-outline:hover { background: var(--primary-light); }

        /* 4. Progress Chart */
        .chart-card { padding: 20px; }
        .chart-header { font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 15px; }
        .chart-container { position: relative; height: 220px; width: 100%; }

        /* 5. Statistics Grid */
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: var(--spacing); }
        .stat-card {
            background: var(--card-bg); border-radius: 18px; padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02); border: 1px solid var(--border);
            display: flex; flex-direction: column; gap: 8px;
        }
        .stat-icon { width: 40px; height: 40px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; justify-content: center; align-items: center; font-size: 1.2rem; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--dark); }
        .stat-label { font-size: 0.85rem; font-weight: 500; color: var(--text-muted); }

        /* 6. Next Goal / Milestone */
        .milestone-card { position: relative; overflow: hidden; border: 1.5px solid var(--primary-light); }
        .milestone-card::before {
            content: '\f100'; font-family: 'Font Awesome 6 Free'; font-weight: 900;
            position: absolute; right: -20px; top: -10px; font-size: 8rem; color: var(--primary-light);
            opacity: 0.3; transform: rotate(-15deg); pointer-events: none;
        }
        .ms-title { font-size: 0.9rem; font-weight: 600; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .ms-surah { font-size: 1.4rem; font-weight: 700; color: var(--dark); margin-bottom: 5px; }
        .ms-desc { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 20px; }
        .btn-solid { width: 100%; background: var(--primary); color: white; border: none; padding: 14px; border-radius: 14px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(5,150,105,0.3); transition: 0.2s; text-decoration: none;}
        .btn-solid:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5,150,105,0.4); }

    </style>
</head>
<body>

    <div class="container">
        <!-- 1. Header -->
        <div class="page-header">
            <h1 class="page-title">🎯 Target</h1>
            <p class="page-subtitle">Tetapkan target hafalanmu dan pantau progresnya secara konsisten.</p>
        </div>

        <!-- 2. Segmented Control -->
        <div class="segmented-control">
            <div class="segment-btn active" onclick="switchTab('hari', this)">Hari</div>
            <div class="segment-btn" onclick="switchTab('minggu', this)">Minggu</div>
            <div class="segment-btn" onclick="switchTab('bulan', this)">Bulan</div>
            <div class="segment-btn" onclick="switchTab('tahun', this)">Tahun</div>
        </div>

        <!-- 3. Current Target Card -->
        <div class="card">
            <div class="target-header">
                <div>
                    <div class="th-title">Target Hari Ini</div>
                    <div style="font-size:0.85rem; color:var(--text-muted); margin-top:3px;">Hafal <?= $target_ayat ?> Ayat</div>
                </div>
                <div class="th-value"><?= $progress_ayat ?> <span style="font-size:1rem; color:var(--text-muted);">/ <?= $target_ayat ?></span></div>
            </div>

            <div class="progress-meta">
                <span>Progress</span>
                <span><?= round($persentase) ?>%</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill"></div>
            </div>

            <div class="target-footer">
                <div class="tf-text" style="color: <?= $pesan_color ?>; background: <?= $pesan_bg ?>;">
                    <i class="fas fa-info-circle"></i> <?= $pesan_motivasi ?>
                </div>
                <!-- Tombol diarahkan ke mutabaah.php -->
                <a href="mutabaah.php" class="btn-outline"><i class="fas fa-plus"></i> Tambah Hafalan Baru</a>
            </div>
        </div>

        <!-- 4. Progress Chart -->
        <div class="card chart-card">
            <div class="chart-header">Progress Hafalan</div>
            <div class="chart-container">
                <canvas id="targetChart"></canvas>
            </div>
        </div>

        <!-- 5. Statistics Grid (Data Asli DB) -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-value"><?= number_format($total_ayat_hafal, 0, ',', '.') ?></div>
                <div class="stat-label">Total Ayat</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-value"><?= $total_halaman ?></div>
                <div class="stat-label">Est. Halaman</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                <div class="stat-value"><?= $total_juz ?></div>
                <div class="stat-label">Est. Juz</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-fire"></i></div>
                <div class="stat-value"><?= $hari_aktif ?></div>
                <div class="stat-label">Hari Aktif (Streak)</div>
            </div>
        </div>

        <!-- 6. Next Goal / Milestone -->
        <div class="card milestone-card">
            <div class="ms-title"><i class="fas fa-bullseye"></i> Target Berikutnya</div>
            <div class="ms-surah" id="ms-surah-name">Surah ke-<?= $next_surah_no ?></div>
            <div style="font-weight:600; color:var(--dark); margin-bottom:5px;">Lanjut ke Ayat <?= $next_ayat ?></div>
            <div class="ms-desc">Terus istiqomah melangkah ke ayat selanjutnya. Sedikit demi sedikit lama-lama menjadi bukit!</div>
            
            <a href="smart_murojaah.php" class="btn-solid">
                Mulai Hafalan <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Panggil Bottom Navigation Hifzly -->
    <?php include '../components/nav.php'; ?>

    <script>
        // Tarik nama surah dari API agar nama Surah di Milestone benar (bukan cuma angka)
        const nextSurahNo = <?= $next_surah_no ?>;
        fetch(`https://equran.id/api/v2/surat/${nextSurahNo}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('ms-surah-name').innerText = data.data.namaLatin;
            }).catch(e => console.log(e));

        // --- Inisialisasi Chart.js ---
        const ctx = document.getElementById('targetChart').getContext('2d');
        
        // Data Mock untuk masing-masing tab chart (Bagian ini bisa dikembangkan pakai query GROUP BY SQL jika diperlukan)
        const chartData = {
            hari: { labels: ['06:00', '09:00', '12:00', '15:00', '18:00', '21:00'], data: [0, 0, <?= $progress_ayat > 0 ? floor($progress_ayat/2) : 0 ?>, <?= $progress_ayat ?>, <?= $progress_ayat ?>, <?= $progress_ayat ?>] },
            minggu: { labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'], data: [4, 5, 2, 6, 8, 3, <?= $progress_ayat ?>] },
            bulan: { labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'], data: [15, 20, 18, 25] },
            tahun: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'], data: [50, 80, 60, 90, 120, 150] }
        };

        let progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.hari.labels,
                datasets: [{
                    label: 'Ayat Dihafal',
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
                    legend: { display: false },
                    tooltip: { backgroundColor: '#1e293b', padding: 10, cornerRadius: 8, displayColors: false, }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false }, ticks: { color: '#64748b', font: { family: 'Inter', size: 11 } } },
                    y: { grid: { color: '#f1f5f9', borderDash: [5, 5], drawBorder: false }, ticks: { color: '#64748b', font: { family: 'Inter', size: 11 }, stepSize: 1 } }
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
    </script>
</body>
</html>