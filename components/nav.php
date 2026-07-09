<?php
// Mengambil nama file yang sedang dibuka (contoh: 'dashboard.php' atau 'mutabaah.php')
// Ini berguna agar ikon yang aktif bisa menyala otomatis
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Font Awesome (dihapus komentarnya biar ikon pasti muncul, aman walau ke-load 2x di halaman yang sudah punya) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
    :root {
        --nav-primary: #059669;
        --nav-primary-light: #34d399;
        --nav-primary-dark: #04785a;
        --nav-gold: #c9a227;
        --nav-muted: #9ca3af;
        --nav-glow: rgba(5, 150, 105, 0.35);
    }

    /* =========================================
       DEFAULT: MOBILE FIRST (BOTTOM NAVIGATION)
       ========================================= */
    body {
        padding-bottom: calc(78px + env(safe-area-inset-bottom));
    }

    .app-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 -8px 30px rgba(15, 23, 42, 0.08);
        border-top: 1px solid rgba(15, 23, 42, 0.05);
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 6px calc(12px + env(safe-area-inset-bottom));
        z-index: 1000;
        animation: navRise 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes navRise {
        from {
            transform: translateY(100%);
        }

        to {
            transform: translateY(0);
        }
    }

    /* Sembunyikan brand di mobile (menu ada di bawah) */
    .nav-brand {
        display: none;
    }

    .nav-links {
        display: flex;
        width: 100%;
        justify-content: space-around;
        align-items: center;
    }

    .nav-item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        text-decoration: none;
        color: var(--nav-muted);
        font-size: 0.68rem;
        font-weight: 600;
        gap: 3px;
        min-width: 56px;
        padding: 4px 0;
        -webkit-tap-highlight-color: transparent;
    }

    .nav-icon-wrap {
        position: relative;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.35s ease, box-shadow 0.35s ease;
    }

    .nav-icon {
        font-size: 1.2rem;
        color: var(--nav-muted);
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .nav-text {
        transition: color 0.3s ease, opacity 0.3s ease;
        opacity: 0.75;
    }

    /* State aktif: ikon "melayang" dalam bubble gradient */
    .nav-item.active .nav-icon-wrap {
        background: linear-gradient(135deg, var(--nav-primary-light), var(--nav-primary));
        transform: translateY(-14px) scale(1.06);
        box-shadow: 0 12px 22px var(--nav-glow);
    }

    .nav-item.active .nav-icon {
        color: #fff;
        transform: scale(1.05);
    }

    .nav-item.active .nav-text {
        color: var(--nav-primary-dark);
        font-weight: 700;
        opacity: 1;
    }

    .nav-item:not(.active):active .nav-icon-wrap {
        background: rgba(5, 150, 105, 0.08);
        transform: scale(0.92);
    }

    /* Item logout diberi aksen merah halus saat disentuh */
    .nav-item.nav-logout:active .nav-icon-wrap {
        background: rgba(239, 68, 68, 0.1);
    }

    .nav-item.nav-logout:active .nav-icon {
        color: #ef4444;
    }

    /* =========================================
       RESPONSIVE: DESKTOP (TOP HEADER)
       ========================================= */
    @media (min-width: 768px) {
        body {
            padding-bottom: 0 !important;
            padding-top: 82px !important;
        }

        .app-nav {
            top: 0;
            bottom: auto;
            left: 0;
            right: 0;
            padding: 0 40px;
            height: 74px;
            justify-content: space-between;
            box-shadow: 0 4px 24px rgba(15, 23, 42, 0.05);
            border-top: none;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
            animation: navDrop 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes navDrop {
            from {
                transform: translateY(-100%);
            }

            to {
                transform: translateY(0);
            }
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-decoration: none;
        }

        .nav-brand span.dot {
            color: var(--nav-gold);
        }

        .nav-links {
            width: auto;
            gap: 6px;
        }

        .nav-item {
            position: relative;
            flex-direction: row;
            font-size: 0.92rem;
            gap: 9px;
            min-width: auto;
            padding: 9px 18px;
            border-radius: 30px;
            color: #475569;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .nav-icon-wrap {
            width: auto;
            height: auto;
            border-radius: 0;
            background: none !important;
            box-shadow: none !important;
            transform: none !important;
        }

        .nav-icon {
            font-size: 1rem;
        }

        .nav-item .nav-text {
            opacity: 1;
        }

        .nav-item:hover {
            background: #f1f5f9;
            color: var(--nav-primary-dark);
        }

        .nav-item:hover .nav-icon {
            color: var(--nav-primary-dark);
        }

        .nav-item.active {
            background: rgba(5, 150, 105, 0.1);
            color: var(--nav-primary-dark);
        }

        .nav-item.active .nav-icon {
            color: var(--nav-primary-dark);
            transform: none;
        }

        .nav-item.active .nav-text {
            font-weight: 700;
        }

        .nav-item.nav-logout {
            margin-left: 8px;
            border-left: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 0 30px 30px 0;
            padding-left: 20px;
        }

        .nav-item.nav-logout:hover {
            background: rgba(239, 68, 68, 0.08);
            color: #ef4444;
        }

        .nav-item.nav-logout:hover .nav-icon {
            color: #ef4444;
        }
    }
</style>

<nav class="app-nav">
    <a href="dashboard.php" class="nav-brand">
        Hafizhly<span class="dot">.</span>
    </a>

    <div class="nav-links">
        <a href="dashboard.php" class="nav-item <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
            <span class="nav-icon-wrap"><i class="fa-solid fa-house nav-icon"></i></span>
            <span class="nav-text">Beranda</span>
        </a>
        <a href="alquran.php" class="nav-item <?= $current_page == 'alquran.php' ? 'active' : '' ?>">
            <span class="nav-icon-wrap"><i class="fa-solid fa-book-open-reader nav-icon"></i></span>
            <span class="nav-text">Qur'an</span>
        </a>
        <a href="mutabaah.php" class="nav-item <?= $current_page == 'mutabaah.php' ? 'active' : '' ?>">
            <span class="nav-icon-wrap"><i class="fa-solid fa-chart-simple nav-icon"></i></span>
            <span class="nav-text">Mutabaah</span>
        </a>
        <a href="../logout.php" class="nav-item nav-logout" onclick="return confirm('Yakin ingin keluar?')">
            <span class="nav-icon-wrap"><i class="fa-solid fa-right-from-bracket nav-icon"></i></span>
            <span class="nav-text">Keluar</span>
        </a>
    </div>
</nav>