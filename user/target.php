<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// --- MOCK DATA / SIMULASI UNTUK UI ---
// Di tahap produksi, data ini diambil dari tabel `mutabaah` dan `murojaah_progress`.
$target_ayat = 5;
$progress_ayat = 3;
$persentase = ($progress_ayat / $target_ayat) * 100;
$sisa_ayat = $target_ayat - $progress_ayat;

$total_ayat_hafal = 1250;
$total_halaman = 83;
$total_juz = 4;
$hari_aktif = 24;

// Milestone (Mock)
$next_surah = "Al-Baqarah";
$next_ayat = 61;
$sisa_milestone = 226;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target Hafalan - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #059669;        /* Hijau khas Hifzly */
            --primary-light: #d1fae5;
            --primary-dark: #047857;
            --dark: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --spacing: 24px;
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
        
        .target-footer { display: flex; justify-content: space-between; align-items: center; }
        .tf-text { font-size: 0.85rem; color: #f59e0b; font-weight: 600; background: #fef3c7; padding: 4px 12px; border-radius: 20px; }
        .btn-outline { border: 1.5px solid var(--primary); color: var(--primary); background: transparent; padding: 8px 16px; border-radius: 12px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: 0.2s; }
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
        .btn-solid { width: 100%; background: var(--primary); color: white; border: none; padding: 14px; border-radius: 14px; font-weight: 600; font-size: 1rem; cursor: pointer; display: flex; justify-content: center; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(5,150,105,0.3); transition: 0.2s; }
        .btn-solid:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5,150,105,0.4); }

    </style>
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🎯 Target</h1>
            <p class="page-subtitle">Tetapkan target hafalanmu dan pantau progresnya secara konsisten.</p>
        </div>

        <div class="segmented-control">
            <div class="segment-btn active" onclick="switchTab('hari', this)">Hari</div>
            <div class="segment-btn" onclick="switchTab('minggu', this)">Minggu</div>
            <div class="segment-btn" onclick="switchTab('bulan', this)">Bulan</div>
            <div class="segment-btn" onclick="switchTab('tahun', this)">Tahun</div>
        </div>

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
                <div class="tf-text">Sisa <?= $sisa_ayat ?> ayat lagi</div>
                <button class="btn-outline">Edit Target</button>
            </div>
        </div>

        <div class="card chart-card">
            <div class="chart-header">Progress Hafalan</div>
            <div class="chart-container">
                <canvas id="targetChart"></canvas>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-value"><?= number_format($total_ayat_hafal, 0, ',', '.') ?></div>
                <div class="stat-label">Total Ayat</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-value"><?= $total_halaman ?></div>
                <div class="stat-label">Total Halaman</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                <div class="stat-value"><?= $total_juz ?></div>
                <div class="stat-label">Total Juz</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-fire"></i></div>
                <div class="stat-value"><?= $hari_aktif ?></div>
                <div class="stat-label">Hari Aktif (Streak)</div>
            </div>
        </div>

        <div class="card milestone-card">
            <div class="ms-title"><i class="fas fa-bullseye"></i> Target Berikutnya</div>
            <div class="ms-surah">Surah <?= $next_surah ?></div>
            <div style="font-weight:600; color:var(--dark); margin-bottom:5px;">Ayat <?= $next_ayat ?></div>
            <div class="ms-desc">Tinggal <?= $sisa_milestone ?> ayat lagi untuk menyelesaikan surah ini.</div>
            
            <button class="btn-solid" onclick="window.location.href='smart_murojaah.php'">
                Lanjut Hafalan <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script>
        // --- Inisialisasi Chart.js ---
        const ctx = document.getElementById('targetChart').getContext('2d');
        
        // Data Mock untuk masing-masing tab
        const chartData = {
            hari: {
                labels: ['06:00', '09:00', '12:00', '15:00', '18:00', '21:00'],
                data: [0, 1, 1, 2, 3, 3]
            },
            minggu: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                data: [4, 5, 2, 6, 8, 3, 5]
            },
            bulan: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                data: [25, 30, 28, 40]
            },
            tahun: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                data: [120, 150, 130, 180, 200, 210]
            }
        };

        // Konfigurasi Modern Line Chart
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
                    tension: 0.4 // Membuat garis melengkung (smooth)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: '#64748b', font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9', borderDash: [5, 5], drawBorder: false },
                        ticks: { color: '#64748b', font: { family: 'Inter', size: 11 }, stepSize: 1 }
                    }
                }
            }
        });

        // --- Logika Tab Segmented Control ---
        function switchTab(period, element) {
            // Ubah class active pada tombol
            document.querySelectorAll('.segment-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');

            // Update data chart berdasarkan periode yang dipilih dengan animasi
            progressChart.data.labels = chartData[period].labels;
            progressChart.data.datasets[0].data = chartData[period].data;
            progressChart.update();
        }
    </script>
</body>
</html>