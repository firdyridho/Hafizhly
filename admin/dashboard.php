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
    <title>Dashboard Admin - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lexend:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-glow: rgba(16, 185, 129, 0.35);
            --gold: #e8c366;
            --gold-light: #f7e3a1;
            --gold-glow: rgba(232, 195, 102, 0.35);

            --bg: #0b1120;
            --bg-soft: #0f172a;
            --sidebar-bg: linear-gradient(180deg, #0c1424 0%, #0a0f1c 100%);

            --glass: rgba(255, 255, 255, 0.04);
            --glass-strong: rgba(255, 255, 255, 0.06);
            --glass-border: rgba(255, 255, 255, 0.08);

            --text-main: #eef2f7;
            --text-muted: #8b96a8;
            --danger: #f87171;

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
                radial-gradient(circle at 15% 0%, rgba(16, 185, 129, 0.08), transparent 40%),
                radial-gradient(circle at 85% 20%, rgba(232, 195, 102, 0.06), transparent 35%),
                var(--bg);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        ::selection {
            background: var(--primary-glow);
            color: #fff;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(232, 195, 102, 0.4);
        }

        /* --- Page loader (SPA-ish first paint) --- */
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
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 3px solid rgba(232, 195, 102, 0.15);
            border-top-color: var(--gold);
            animation: spin 0.85s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: clamp(230px, 22vw, 270px);
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            transition: transform 0.45s var(--ease);
            z-index: 1000;
            flex-shrink: 0;
            border-right: 1px solid var(--glass-border);
            position: relative;
        }

        .sidebar::after {
            content: '';
            position: absolute;
            top: 0;
            right: -1px;
            width: 1px;
            height: 100%;
            background: linear-gradient(180deg, transparent, rgba(232, 195, 102, 0.25), transparent);
        }

        .sidebar-header {
            padding: clamp(20px, 3vw, 26px) 22px;
            font-family: 'Lexend', sans-serif;
            font-size: clamp(1.15rem, 2vw, 1.4rem);
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--glass-border);
            letter-spacing: 0.2px;
        }

        .sidebar-header .logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 18px var(--primary-glow);
            flex-shrink: 0;
        }

        .sidebar-menu {
            flex-grow: 1;
            padding: 18px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .menu-item {
            padding: 13px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #9aa5b8;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.92rem;
            border-radius: 12px;
            position: relative;
            transition: color 0.3s var(--ease-soft), background 0.3s var(--ease-soft), transform 0.25s var(--ease-soft);
            overflow: hidden;
        }

        .menu-item i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            transition: transform 0.3s var(--ease-soft);
        }

        .menu-item:hover {
            background: var(--glass-strong);
            color: #fff;
            transform: translateX(3px);
        }

        .menu-item:hover i {
            transform: scale(1.12);
        }

        .menu-item.active {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.18), rgba(16, 185, 129, 0.02));
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(16, 185, 129, 0.25);
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10%;
            width: 3px;
            height: 80%;
            border-radius: 3px;
            background: linear-gradient(180deg, var(--gold), var(--primary));
            box-shadow: 0 0 10px var(--gold-glow);
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--glass-border);
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
            background: rgba(248, 113, 113, 0.1);
            transform: translateX(3px);
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            width: calc(100% - 270px);
            height: 100vh;
            overflow-y: auto;
        }

        /* Top Navigation */
        .top-nav {
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(16px) saturate(160%);
            -webkit-backdrop-filter: blur(16px) saturate(160%);
            padding: 14px clamp(16px, 3vw, 30px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .menu-toggle {
            display: none;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            font-size: 1.1rem;
            color: var(--text-main);
            cursor: pointer;
            transition: 0.25s var(--ease-soft);
        }

        .menu-toggle:hover {
            background: var(--glass-strong);
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
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            color: #1a1305;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 800;
            font-size: 1.15rem;
            box-shadow: 0 0 0 3px rgba(232, 195, 102, 0.12), 0 4px 14px var(--gold-glow);
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

        /* Content Area */
        .content-area {
            padding: clamp(18px, 3vw, 34px);
        }

        .welcome-box {
            margin-bottom: clamp(20px, 3vw, 32px);
            opacity: 0;
            animation: fadeUp 0.7s var(--ease) forwards;
        }

        .welcome-title {
            font-family: 'Lexend', sans-serif;
            font-size: clamp(1.25rem, 2.4vw, 1.7rem);
            font-weight: 700;
            color: #fff;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .wave {
            display: inline-block;
            transform-origin: 70% 70%;
            animation: wave 2.2s ease-in-out infinite;
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(200px, 30vw, 240px), 1fr));
            gap: clamp(14px, 2vw, 20px);
            margin-bottom: clamp(20px, 3vw, 32px);
        }

        .stat-card {
            background: var(--glass);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--glass-border);
            padding: clamp(18px, 2.2vw, 25px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            overflow: hidden;
            transition: transform 0.4s var(--ease), box-shadow 0.4s var(--ease), border-color 0.4s var(--ease-soft);
            opacity: 0;
            animation: fadeUp 0.7s var(--ease) forwards;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(232, 195, 102, 0.06), transparent 60%);
            opacity: 0;
            transition: opacity 0.4s var(--ease-soft);
        }

        .stat-card:hover {
            transform: translateY(-6px);
            border-color: rgba(232, 195, 102, 0.35);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(232, 195, 102, 0.08);
        }

        .stat-card:hover::before {
            opacity: 1;
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
            background: rgba(56, 189, 248, 0.14);
            color: #38bdf8;
        }

        .icon-green {
            background: rgba(16, 185, 129, 0.14);
            color: var(--primary);
        }

        .icon-gold {
            background: rgba(232, 195, 102, 0.14);
            color: var(--gold);
        }

        .stat-info h3 {
            font-family: 'Lexend', sans-serif;
            font-size: clamp(1.4rem, 2.4vw, 1.8rem);
            font-weight: 700;
            margin-bottom: 2px;
            color: #fff;
            font-variant-numeric: tabular-nums;
        }

        .stat-info p {
            font-size: clamp(0.8rem, 1.2vw, 0.88rem);
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Section header */
        .section-header {
            font-family: 'Lexend', sans-serif;
            font-size: clamp(1.02rem, 1.6vw, 1.15rem);
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            opacity: 0;
            animation: fadeUp 0.7s var(--ease) forwards;
        }

        .quick-access-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(clamp(240px, 40vw, 300px), 1fr));
            gap: clamp(14px, 2vw, 20px);
        }

        .action-card {
            background: var(--glass);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: clamp(20px, 2.4vw, 26px);
            text-align: center;
            text-decoration: none;
            color: var(--text-main);
            transition: transform 0.4s var(--ease), border-color 0.4s var(--ease-soft), box-shadow 0.4s var(--ease);
            display: block;
            position: relative;
            overflow: hidden;
            opacity: 0;
            animation: fadeUp 0.7s var(--ease) forwards;
        }

        .action-card:hover {
            transform: translateY(-6px);
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.35);
        }

        .action-icon {
            font-size: clamp(2rem, 3vw, 2.5rem);
            background: linear-gradient(135deg, var(--gold-light), var(--primary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 14px;
            transition: transform 0.4s var(--ease);
        }

        .action-card:hover .action-icon {
            transform: scale(1.12);
        }

        .action-title {
            font-weight: 700;
            font-size: clamp(1rem, 1.4vw, 1.1rem);
            margin-bottom: 8px;
            color: #fff;
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

        /* stagger delays */
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

        /* --- TOAST / ALERT SYSTEM --- */
        #toast-stack {
            position: fixed;
            top: clamp(14px, 2vw, 24px);
            right: clamp(14px, 2vw, 24px);
            left: clamp(14px, 2vw, 24px);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 12px;
            pointer-events: none;
        }

        .toast {
            pointer-events: auto;
            width: min(360px, 100%);
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(18px) saturate(180%);
            -webkit-backdrop-filter: blur(18px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-left: 3px solid var(--gold);
            border-radius: 16px;
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(232, 195, 102, 0.06);
            transform: translateX(120%) scale(0.9);
            opacity: 0;
            animation: toastIn 0.55s var(--ease) forwards;
            position: relative;
            overflow: hidden;
        }

        .toast.leaving {
            animation: toastOut 0.4s var(--ease-soft) forwards;
        }

        @keyframes toastIn {
            0% {
                transform: translateX(120%) scale(0.9);
                opacity: 0;
            }

            60% {
                transform: translateX(-6px) scale(1.01);
                opacity: 1;
            }

            100% {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes toastOut {
            to {
                transform: translateX(110%) scale(0.92);
                opacity: 0;
            }
        }

        .toast-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.95rem;
            animation: iconPop 0.5s var(--ease) 0.15s backwards;
        }

        @keyframes iconPop {
            0% {
                transform: scale(0);
            }

            70% {
                transform: scale(1.15);
            }

            100% {
                transform: scale(1);
            }
        }

        .toast.info .toast-icon {
            background: rgba(232, 195, 102, 0.16);
            color: var(--gold);
        }

        .toast.success .toast-icon {
            background: rgba(16, 185, 129, 0.16);
            color: var(--primary);
        }

        .toast.error .toast-icon {
            background: rgba(248, 113, 113, 0.16);
            color: var(--danger);
        }

        .toast-body {
            flex-grow: 1;
            min-width: 0;
        }

        .toast-title {
            font-weight: 700;
            font-size: 0.88rem;
            color: #fff;
            margin-bottom: 2px;
        }

        .toast-msg {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.85rem;
            padding: 4px;
            transition: color 0.2s, transform 0.2s;
            flex-shrink: 0;
        }

        .toast-close:hover {
            color: #fff;
            transform: rotate(90deg);
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--primary));
            animation: progressShrink linear forwards;
        }

        @keyframes progressShrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: 100vh;
                transform: translateX(-100%);
                box-shadow: 20px 0 60px rgba(0, 0, 0, 0.5);
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
                left: 12px;
                right: 12px;
                align-items: stretch;
            }

            .toast {
                width: 100%;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(5, 8, 15, 0.6);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
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
            <span class="logo-icon"><i class="fas fa-leaf" style="color:#fff;"></i></span> Hifzly Admin
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="#" class="menu-item" onclick="showToast('info','Segera Hadir','Fitur Kelola Pengguna sedang dalam pengembangan.'); return false;"><i class="fas fa-users"></i> Kelola Pengguna</a>
            <a href="materi_tajwid.php" class="menu-item"><i class="fas fa-book-quran"></i> Materi Tajwid</a>
            <a href="#" class="menu-item" onclick="showToast('info','Segera Hadir','Laporan Aktivitas sedang dalam pengembangan.'); return false;"><i class="fas fa-chart-bar"></i> Laporan Aktivitas</a>
            <a href="#" class="menu-item" onclick="showToast('info','Segera Hadir','Halaman Pengaturan sedang dalam pengembangan.'); return false;"><i class="fas fa-cog"></i> Pengaturan</a>
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
                <h1 class="welcome-title">Assalamu'alaikum, <?= htmlspecialchars($admin_name) ?> <span class="wave">👋</span></h1>
                <p class="welcome-subtitle">Berikut adalah ringkasan data aplikasi Hifzly hari ini.</p>
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
            <h2 class="section-header"><i class="fas fa-bolt" style="color: var(--gold);"></i> Akses Cepat</h2>
            <div class="quick-access-grid">
                <a href="materi_tajwid.php" class="action-card">
                    <div class="action-icon"><i class="fas fa-clapperboard"></i></div>
                    <div class="action-title">Kelola Tajwid & Video</div>
                    <div class="action-desc">Tambah materi tajwid baru, tautkan video YouTube, atau buat soal kuis evaluasi.</div>
                </a>

                <a href="#" class="action-card" onclick="showToast('info','Segera Hadir','Fitur Manajemen User sedang dalam pengembangan.'); return false;">
                    <div class="action-icon"><i class="fas fa-user-shield"></i></div>
                    <div class="action-title">Manajemen User</div>
                    <div class="action-desc">Pantau progres pengguna, reset password, atau berikan pengumuman ke semua santri.</div>
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