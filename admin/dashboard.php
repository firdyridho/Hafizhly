<?php
// Pastikan tidak ada spasi atau baris kosong sebelum tag <?php ini!
session_start();

// 1. CEK FILE KONEKSI DATABASE
if (file_exists('../config/database.php')) {
    include '../config/database.php';
} else {
    die("<h1>ERROR:</h1> File koneksi database tidak ditemukan. Pastikan ada file di '../config/database.php'");
}

// 2. CEK VARIABEL KONEKSI
if (!isset($conn)) {
    die("<h1>ERROR:</h1> Variabel koneksi (\$conn) tidak ditemukan di dalam database.php");
}

// 3. CEK LOGIN ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_name = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Admin';

// 4. MENGAMBIL DATA DENGAN SANGAT AMAN
$total_users = 0;
$q_users = mysqli_query($conn, "SELECT COUNT(id) AS total FROM users WHERE role = 'user'");
if ($q_users) {
    $row = mysqli_fetch_assoc($q_users);
    $total_users = $row ? (int)$row['total'] : 0;
}

$total_materi = 0;
$cek_materi = mysqli_query($conn, "SHOW TABLES LIKE 'tajwid_materi'");
if ($cek_materi && mysqli_num_rows($cek_materi) > 0) {
    $q_materi = mysqli_query($conn, "SELECT COUNT(id) AS total FROM tajwid_materi");
    if ($q_materi) {
        $row = mysqli_fetch_assoc($q_materi);
        $total_materi = $row ? (int)$row['total'] : 0;
    }
}

$total_hafalan = 0;
$cek_mutabaah = mysqli_query($conn, "SHOW TABLES LIKE 'mutabaah'");
if ($cek_mutabaah && mysqli_num_rows($cek_mutabaah) > 0) {
    $q_hafalan = mysqli_query($conn, "SELECT SUM(ayah_end - ayah_start + 1) AS total FROM mutabaah");
    if ($q_hafalan) {
        $row = mysqli_fetch_assoc($q_hafalan);
        $total_hafalan = ($row && $row['total']) ? (int)$row['total'] : 0;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard Admin - Hafizhly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lexend:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #d1fae5;
            --primary-glow: rgba(16, 185, 129, 0.22);

            --accent: #f0b429;
            --accent-light: #fef3c7;

            --bg: #f4f9f6;
            --sidebar-bg: #ffffff;

            --card-bg: rgba(255, 255, 255, 0.85);
            --card-border: rgba(16, 185, 129, 0.12);

            --text-main: #0f2b22;
            --text-muted: #6b8579;
            --danger: #ef4444;
            --border: #e6f0ea;

            --ease: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-soft: cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background:
                radial-gradient(circle at 10% 0%, rgba(16, 185, 129, 0.07), transparent 40%),
                radial-gradient(circle at 90% 10%, rgba(240, 180, 41, 0.06), transparent 35%),
                var(--bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        ::selection {
            background: var(--primary-glow);
            color: var(--text-main);
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.25);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(16, 185, 129, 0.45);
        }

        /* --- Page loader --- */
        #page-loader {
            position: fixed;
            inset: 0;
            background: var(--bg);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s var(--ease), visibility 0.5s var(--ease);
        }

        #page-loader.hide {
            opacity: 0;
            visibility: hidden;
        }

        .loader-ring {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 3px solid var(--primary-light);
            border-top-color: var(--primary);
            animation: spin 0.85s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: clamp(230px, 22vw, 268px);
            background: var(--sidebar-bg);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            transition: transform 0.45s var(--ease);
            z-index: 1000;
            flex-shrink: 0;
            border-right: 1px solid var(--border);
            box-shadow: 4px 0 24px rgba(16, 185, 129, 0.04);
        }

        .sidebar-header {
            padding: clamp(20px, 3vw, 26px) 22px;
            display: flex;
            align-items: center;
            gap: 13px;
            border-bottom: 1px solid var(--border);
        }

        .logo-badge {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 16px var(--primary-glow);
            overflow: hidden;
        }

        .logo-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .logo-badge i {
            color: #fff;
            font-size: 1.2rem;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .brand-text .brand-name {
            font-family: 'Lexend', sans-serif;
            font-size: clamp(1.1rem, 2vw, 1.3rem);
            font-weight: 700;
            color: var(--text-main);
        }

        .brand-text .brand-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .sidebar-menu {
            flex-grow: 1;
            padding: 18px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .menu-item {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.92rem;
            border-radius: 12px;
            position: relative;
            transition: color 0.3s var(--ease-soft), background 0.3s var(--ease-soft), transform 0.25s var(--ease-soft);
        }

        .menu-item i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            color: var(--text-muted);
            transition: transform 0.3s var(--ease-soft), color 0.3s var(--ease-soft);
        }

        .menu-item:hover {
            background: var(--primary-light);
            color: var(--primary-dark);
            transform: translateX(3px);
        }

        .menu-item:hover i {
            color: var(--primary-dark);
            transform: scale(1.12);
        }

        .menu-item.active {
            background: linear-gradient(90deg, var(--primary-light), rgba(209, 250, 229, 0.2));
            color: var(--primary-dark);
            font-weight: 600;
        }

        .menu-item.active i {
            color: var(--primary);
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 10%;
            width: 4px;
            height: 80%;
            border-radius: 3px;
            background: linear-gradient(180deg, var(--primary), var(--accent));
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--danger);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 12px;
            transition: 0.3s var(--ease-soft);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .logout-btn:hover {
            background: #fef2f2;
            transform: translateX(3px);
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            width: calc(100% - 268px);
            height: 100vh;
            overflow-y: auto;
        }

        .top-nav {
            background: rgba(244, 249, 246, 0.7);
            backdrop-filter: blur(14px) saturate(160%);
            -webkit-backdrop-filter: blur(14px) saturate(160%);
            padding: 14px clamp(16px, 3vw, 30px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .menu-toggle {
            display: none;
            background: #fff;
            border: 1px solid var(--border);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            font-size: 1.1rem;
            color: var(--text-main);
            cursor: pointer;
            transition: 0.25s var(--ease-soft);
        }

        .menu-toggle:hover {
            background: var(--primary-light);
        }

        .menu-toggle:active {
            transform: scale(0.92);
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .admin-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #d99a1f);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 0 0 3px var(--accent-light);
        }

        .admin-info {
            display: flex;
            flex-direction: column;
        }

        .admin-name {
            font-weight: 600;
            font-size: 0.92rem;
            color: var(--text-main);
        }

        .admin-role {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        .content-area {
            padding: clamp(18px, 3vw, 34px);
        }

        .welcome-box {
            margin-bottom: clamp(20px, 3vw, 32px);
            opacity: 0;
            animation: fadeUp 0.7s var(--ease) forwards;
            position: relative;
            padding-left: 18px;
        }

        .welcome-box::before {
            content: '';
            position: absolute;
            left: 0;
            top: 4px;
            bottom: 4px;
            width: 4px;
            background: linear-gradient(180deg, var(--primary), var(--accent));
            border-radius: 3px;
        }

        .welcome-title {
            font-family: 'Lexend', sans-serif;
            font-size: clamp(1.25rem, 2.4vw, 1.7rem);
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .welcome-title i.fa-hand-wave {
            transform-origin: 70% 70%;
            animation: wave 2.2s ease-in-out infinite;
            display: inline-block;
        }

        @keyframes wave {

            0%,
            60%,
            100% {
                transform: rotate(0deg);
            }

            10% {
                transform: rotate(16deg);
            }

            20% {
                transform: rotate(-8deg);
            }

            30% {
                transform: rotate(16deg);
            }

            40% {
                transform: rotate(-4deg);
            }

            50% {
                transform: rotate(10deg);
            }
        }

        .welcome-subtitle {
            color: var(--text-muted);
            font-size: clamp(0.85rem, 1.3vw, 0.95rem);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(200px, 30vw, 240px), 1fr));
            gap: clamp(14px, 2vw, 20px);
            margin-bottom: clamp(20px, 3vw, 32px);
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            padding: clamp(18px, 2.2vw, 25px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(16, 185, 129, 0.05);
            transition: transform 0.4s var(--ease), box-shadow 0.4s var(--ease), border-color 0.4s var(--ease-soft);
            opacity: 0;
            animation: fadeUp 0.7s var(--ease) forwards;
        }

        .stat-card:hover {
            transform: translateY(-6px);
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 18px 34px rgba(16, 185, 129, 0.14);
        }

        .stat-icon {
            width: clamp(52px, 6vw, 60px);
            height: clamp(52px, 6vw, 60px);
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: clamp(1.4rem, 2vw, 1.7rem);
            flex-shrink: 0;
            transition: transform 0.4s var(--ease);
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.08) rotate(-4deg);
        }

        .icon-blue {
            background: #e0f2fe;
            color: #0284c7;
        }

        .icon-green {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .icon-gold {
            background: var(--accent-light);
            color: #b7791f;
        }

        .stat-info h3 {
            font-family: 'Lexend', sans-serif;
            font-size: clamp(1.4rem, 2.4vw, 1.8rem);
            font-weight: 800;
            margin-bottom: 2px;
            background: linear-gradient(135deg, var(--text-main), #1a4738);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-variant-numeric: tabular-nums;
        }

        .stat-info p {
            font-size: clamp(0.8rem, 1.2vw, 0.88rem);
            color: var(--text-muted);
            font-weight: 500;
        }

        .section-header {
            font-family: 'Lexend', sans-serif;
            font-size: clamp(1.02rem, 1.6vw, 1.15rem);
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
            opacity: 0;
            animation: fadeUp 0.7s var(--ease) forwards;
        }

        .quick-access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(240px, 40vw, 300px), 1fr));
            gap: clamp(14px, 2vw, 20px);
        }

        .action-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: clamp(20px, 2.4vw, 26px);
            text-align: center;
            text-decoration: none;
            color: var(--text-main);
            transition: transform 0.4s var(--ease), border-color 0.4s var(--ease-soft), box-shadow 0.4s var(--ease);
            display: block;
            box-shadow: 0 4px 18px rgba(16, 185, 129, 0.05);
            opacity: 0;
            animation: fadeUp 0.7s var(--ease) forwards;
        }

        .action-card:hover {
            transform: translateY(-6px);
            border-color: rgba(16, 185, 129, 0.35);
            box-shadow: 0 18px 34px rgba(16, 185, 129, 0.14);
        }

        .action-icon {
            font-size: clamp(2rem, 3vw, 2.5rem);
            color: var(--primary);
            margin-bottom: 14px;
            transition: transform 0.4s var(--ease);
        }

        .action-card:hover .action-icon {
            transform: scale(1.12);
            color: var(--primary-dark);
        }

        .action-title {
            font-weight: 700;
            font-size: clamp(1rem, 1.4vw, 1.1rem);
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .action-desc {
            font-size: clamp(0.8rem, 1.1vw, 0.85rem);
            color: var(--text-muted);
            line-height: 1.55;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card:nth-child(1),
        .action-card:nth-child(1) {
            animation-delay: 0.05s;
        }

        .stat-card:nth-child(2),
        .action-card:nth-child(2) {
            animation-delay: 0.15s;
        }

        .stat-card:nth-child(3) {
            animation-delay: 0.25s;
        }

        .section-header {
            animation-delay: 0.3s;
        }

        .quick-access-grid .action-card:nth-child(1) {
            animation-delay: 0.35s;
        }

        .quick-access-grid .action-card:nth-child(2) {
            animation-delay: 0.45s;
        }

        /* --- TOAST MINIMALIS --- */
        #toast-stack {
            position: fixed;
            top: 18px;
            right: 18px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            pointer-events: none;
            max-width: 340px;
        }

        .toast {
            pointer-events: auto;
            width: 100%;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.12);
            transform: translateX(120%);
            opacity: 0;
            animation: toastIn 0.45s cubic-bezier(.16,1,.3,1) forwards;
            position: relative;
            overflow: hidden;
        }

        .toast.leaving {
            animation: toastOut 0.3s ease-in forwards;
        }

        @keyframes toastIn {
            0% { transform: translateX(120%); opacity: 0; }
            100% { transform: translateX(0); opacity: 1; }
        }

        @keyframes toastOut {
            to { transform: translateX(110%); opacity: 0; }
        }

        .toast-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.75rem;
        }

        .toast.info .toast-icon {
            background: var(--accent-light);
            color: #b7791f;
        }
        .toast.success .toast-icon {
            background: var(--primary-light);
            color: var(--primary-dark);
        }
        .toast.error .toast-icon {
            background: #fee2e2;
            color: var(--danger);
        }

        .toast-body {
            flex: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 700;
            font-size: 0.82rem;
            color: var(--text-main);
        }

        .toast-msg {
            font-size: 0.74rem;
            color: var(--text-muted);
            line-height: 1.35;
            margin-top: 1px;
        }

        .toast-close {
            background: none;
            border: none;
            color: #cbd5e1;
            cursor: pointer;
            font-size: 0.75rem;
            padding: 4px;
            transition: color 0.2s;
            flex-shrink: 0;
        }

        .toast-close:hover {
            color: var(--text-main);
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            background: var(--primary);
            animation: progressShrink linear forwards;
        }

        @keyframes progressShrink {
            from { width: 100%; }
            to { width: 0%; }
        }

        @media (max-width: 1024px) and (min-width: 769px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                width: calc(100% - 200px);
            }
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
            }

            .main-content {
                width: 100%;
            }

            .menu-toggle {
                display: block;
            }

            #toast-stack {
                right: 12px;
                max-width: calc(100% - 24px);
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 43, 34, 0.35);
                backdrop-filter: blur(3px);
                -webkit-backdrop-filter: blur(3px);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.35s var(--ease-soft);
            }

            .sidebar-overlay.active {
                display: block;
                opacity: 1;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.001ms !important;
                transition-duration: 0.001ms !important;
            }
        }
    </style>
</head>

<body>

    <!-- Page loader -->
    <div id="page-loader">
        <div class="loader-ring"></div>
    </div>

    <!-- Toast stack -->
    <div id="toast-stack" aria-live="polite"></div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR NAVIGATION -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="logo-badge">
                <!-- Ganti src di bawah ini dengan logo Hafizhly kamu, contoh: assets/img/logo.png -->
                <img src="assets/img/logo.png" alt="Logo Hafizhly" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <i class="fas fa-book-quran" style="display:none;"></i>
            </span>
            <div class="brand-text">
                <span class="brand-name">Hafizhly</span>
                <span class="brand-sub">Admin Panel</span>
            </div>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active"><i class="fas fa-house"></i> Dashboard</a>
            <a href="#" class="menu-item" onclick="showToast('info','Segera Hadir','Fitur Kelola Pengguna sedang dalam pengembangan.'); return false;"><i class="fas fa-users"></i> Kelola Pengguna</a>
            <a href="materi_tajwid.php" class="menu-item"><i class="fas fa-book-quran"></i> Materi Tajwid</a>
            <a href="#" class="menu-item" onclick="showToast('info','Segera Hadir','Laporan Aktivitas sedang dalam pengembangan.'); return false;"><i class="fas fa-chart-column"></i> Laporan Aktivitas</a>
            <a href="#" class="menu-item" onclick="showToast('info','Segera Hadir','Halaman Pengaturan sedang dalam pengembangan.'); return false;"><i class="fas fa-gear"></i> Pengaturan</a>
        </div>
        <div class="sidebar-footer">
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-arrow-right-from-bracket"></i> Keluar (Logout)
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
                <h1 class="welcome-title">Assalamu'alaikum, <?= htmlspecialchars($admin_name) ?> <i class="fa-solid fa-hand-wave"></i></h1>
                <p class="welcome-subtitle">Berikut adalah ringkasan data aplikasi Hafizhly hari ini.</p>
            </div>

            <!-- STATISTIC CARDS -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3 class="count-up" data-target="<?= (int)$total_users ?>">0</h3>
                        <p>Total Pengguna</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-gold"><i class="fas fa-book-open"></i></div>
                    <div class="stat-info">
                        <h3 class="count-up" data-target="<?= (int)$total_materi ?>">0</h3>
                        <p>Materi Tajwid</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon icon-green"><i class="fas fa-quran"></i></div>
                    <div class="stat-info">
                        <h3 class="count-up" data-target="<?= (int)$total_hafalan ?>">0</h3>
                        <p>Total Ayat Terbaca</p>
                    </div>
                </div>
            </div>

            <!-- QUICK ACTIONS -->
            <h2 class="section-header"><i class="fas fa-gauge-high" style="color: var(--primary);"></i> Akses Cepat</h2>
            <div class="quick-access-grid">
                <a href="materi_tajwid.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-book-quran"></i></div>
                    <div class="action-title">Kelola Materi Tajwid</div>
                    <div class="action-desc">Tambah materi tajwid baru, tautkan video pembelajaran, dan buat soal kuis untuk evaluasi santri.</div>
                </a>

                <a href="#" class="action-card" onclick="showToast('info','Segera Hadir','Fitur Manajemen User sedang dalam pengembangan.'); return false;">
                    <div class="action-icon"><i class="fas fa-users-gear"></i></div>
                    <div class="action-title">Manajemen Pengguna</div>
                    <div class="action-desc">Pantau progres santri, reset akun, kelola izin akses, dan kirim pengumuman ke semua pengguna.</div>
                </a>
            </div>

        </div>
    </main>

    <script>
        // ---------- Page loader ----------
        window.addEventListener('load', () => {
            const loader = document.getElementById('page-loader');
            setTimeout(() => loader.classList.add('hide'), 250);
        });

        // ---------- Sidebar toggle (mobile) ----------
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }

        // ---------- Custom toast/alert system (replaces native alert()) ----------
        const TOAST_ICONS = {
            info: 'fa-solid fa-sparkles',
            success: 'fa-solid fa-check',
            error: 'fa-solid fa-triangle-exclamation'
        };

        function showToast(type = 'info', title = '', message = '', duration = 4200) {
            const stack = document.getElementById('toast-stack');

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="toast-icon"><i class="${TOAST_ICONS[type] || TOAST_ICONS.info}"></i></div>
                <div class="toast-body">
                    <div class="toast-title">${title}</div>
                    <div class="toast-msg">${message}</div>
                </div>
                <button class="toast-close" aria-label="Tutup"><i class="fas fa-xmark"></i></button>
                <div class="toast-progress" style="animation-duration:${duration}ms;"></div>
            `;

            const remove = () => {
                toast.classList.add('leaving');
                toast.addEventListener('animationend', () => toast.remove(), {
                    once: true
                });
            };

            toast.querySelector('.toast-close').addEventListener('click', remove);
            const timer = setTimeout(remove, duration);
            toast.addEventListener('mouseenter', () => clearTimeout(timer));

            stack.appendChild(toast);
        }

        // ---------- Count-up animation for stat numbers ----------
        function animateCount(el) {
            const target = parseInt(el.dataset.target, 10) || 0;
            const duration = 1100;
            const start = performance.now();

            function tick(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const value = Math.floor(eased * target);
                el.textContent = value.toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target.toLocaleString('id-ID');
            }
            requestAnimationFrame(tick);
        }

        document.querySelectorAll('.count-up').forEach(el => {
            setTimeout(() => animateCount(el), 350);
        });
    </script>
</body>

</html>