<?php
session_start();
require_once '../config/database.php'; // Pastikan koneksi database tersedia

// Cek apakah yang masuk benar-benar admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_name = $_SESSION['nama_lengkap'] ?? 'Admin';

// --- MENGAMBIL DATA STATISTIK UNTUK DASHBOARD ---
// (Menggunakan @ agar tidak error jika tabel belum dibuat di tahap awal)
$total_users = 0;
$q_users = @mysqli_query($conn, "SELECT COUNT(id) AS total FROM users WHERE role = 'user'");
if ($q_users) $total_users = mysqli_fetch_assoc($q_users)['total'];

$total_materi = 0;
$q_materi = @mysqli_query($conn, "SELECT COUNT(id) AS total FROM tajwid_materi");
if ($q_materi) $total_materi = mysqli_fetch_assoc($q_materi)['total'];

$total_hafalan = 0;
$q_hafalan = @mysqli_query($conn, "SELECT SUM(ayah_end - ayah_start + 1) AS total FROM mutabaah");
if ($q_hafalan) $total_hafalan = mysqli_fetch_assoc($q_hafalan)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --primary-light: #d1fae5;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
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
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            z-index: 1000;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 25px 20px;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            flex-grow: 1;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu-item {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
            border-left: 4px solid transparent;
        }

        .menu-item:hover,
        .menu-item.active {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--primary);
        }

        .menu-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #f87171;
            text-decoration: none;
            padding: 12px;
            border-radius: 8px;
            transition: 0.2s;
            font-weight: 600;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
            height: 100vh;
            overflow-y: auto;
        }

        /* Top Navigation */
        .top-nav {
            background: var(--card-bg);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-main);
            cursor: pointer;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .admin-info {
            display: flex;
            flex-direction: column;
        }

        .admin-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .admin-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Content Area */
        .content-area {
            padding: 30px;
        }

        .welcome-box {
            margin-bottom: 30px;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        .welcome-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .icon-blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        .icon-green {
            background: #d1fae5;
            color: #059669;
        }

        .icon-purple {
            background: #f3e8ff;
            color: #9333ea;
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 2px;
            color: var(--text-main);
        }

        .stat-info p {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Quick Access / Section */
        .section-header {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quick-access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .action-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: var(--text-main);
            transition: 0.2s;
            display: block;
        }

        .action-card:hover {
            border-color: var(--primary);
            background: #f8fafc;
        }

        .action-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .action-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }

        .action-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: 100vh;
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
                box-shadow: 5px 0 25px rgba(0, 0, 0, 0.5);
            }

            .main-content {
                width: 100%;
            }

            .menu-toggle {
                display: block;
            }

            .content-area {
                padding: 20px;
            }

            /* Overlay untuk menutup sidebar di mobile */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.active {
                display: block;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR NAVIGATION -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-leaf" style="color: var(--primary-light);"></i> Hifzly Admin
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="#" class="menu-item" onclick="alert('Kelola Pengguna segera hadir!')"><i class="fas fa-users"></i> Kelola Pengguna</a>
            <a href="materi_tajwid.php" class="menu-item"><i class="fas fa-book-quran"></i> Materi Tajwid</a>
            <a href="#" class="menu-item" onclick="alert('Laporan segera hadir!')"><i class="fas fa-chart-bar"></i> Laporan Aktivitas</a>
            <a href="#" class="menu-item" onclick="alert('Pengaturan segera hadir!')"><i class="fas fa-cog"></i> Pengaturan</a>
        </div>
        <div class="sidebar-footer">
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Keluar (Logout)
            </a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- Top Navigation -->
        <header class="top-nav">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div style="flex-grow: 1;"></div> <!-- Spacer -->
            <div class="admin-profile">
                <div class="admin-info" style="text-align: right;">
                    <span class="admin-name"><?= htmlspecialchars($admin_name) ?></span>
                    <span class="admin-role">Administrator Utama</span>
                </div>
                <div class="admin-avatar">
                    <?= strtoupper(substr($admin_name, 0, 1)) ?>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">

            <div class="welcome-box">
                <h1 class="welcome-title">Assalamu'alaikum, <?= htmlspecialchars($admin_name) ?> 👋</h1>
                <p class="welcome-subtitle">Berikut adalah ringkasan data aplikasi Hifzly hari ini.</p>
            </div>

            <!-- STATISTIC CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($total_users, 0, ',', '.') ?></h3>
                        <p>Total Pengguna</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-purple"><i class="fas fa-book-open"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($total_materi, 0, ',', '.') ?></h3>
                        <p>Materi Tajwid</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fas fa-quran"></i></div>
                    <div class="stat-info">
                        <h3><?= number_format($total_hafalan, 0, ',', '.') ?></h3>
                        <p>Total Ayat Terbaca</p>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <h2 class="section-header"><i class="fas fa-bolt" style="color: #eab308;"></i> Akses Cepat</h2>
            <div class="quick-access-grid">
                <a href="materi_tajwid.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-clapperboard"></i></div>
                    <div class="action-title">Kelola Tajwid & Video</div>
                    <div class="action-desc">Tambah materi tajwid baru, tautkan video YouTube, atau buat soal kuis evaluasi.</div>
                </a>

                <a href="#" class="action-card" onclick="alert('Fitur segera hadir!')">
                    <div class="action-icon"><i class="fas fa-user-shield"></i></div>
                    <div class="action-title">Manajemen User</div>
                    <div class="action-desc">Pantau progres pengguna, reset password, atau berikan pengumuman ke semua santri.</div>
                </a>
            </div>

        </div>
    </main>

    <script>
        // Fungsi untuk membuka/menutup Sidebar di mode Mobile (HP)
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
    </script>
</body>

</html>