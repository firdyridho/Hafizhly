<?php
// Mengambil nama file yang sedang dibuka (contoh: 'dashboard.php' atau 'mutabaah.php')
// Ini berguna agar warna ikon yang aktif bisa menyala otomatis
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* =========================================
       DEFAULT: MOBILE FIRST (BOTTOM NAVIGATION) 
       ========================================= */
    .app-nav {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: #ffffff;
        box-shadow: 0 -2px 15px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 0 15px 0; /* Padding bawah lebih besar untuk area swipe HP */
        z-index: 1000;
        transition: all 0.3s ease;
    }

    /* Sembunyikan Logo Brand di Mobile (karena menu ada di bawah) */
    .nav-brand { display: none; }

    .nav-links {
        display: flex;
        width: 100%;
        justify-content: space-around;
    }

    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #6b7280; /* Warna abu-abu redup */
        font-size: 0.75rem;
        font-weight: 600;
        gap: 4px;
        transition: 0.3s;
    }

    .nav-item.active { color: #059669; /* Warna Hijau Primary saat aktif */ }
    .nav-icon { font-size: 1.4rem; }

    /* =========================================
       RESPONSIVE: DESKTOP (TOP HEADER)
       Jika lebar layar di atas 768px (Tablet/PC)
       ========================================= */
    @media (min-width: 768px) {
        body {
            /* Hapus jarak bawah, pindahkan jaraknya ke atas untuk Header */
            padding-bottom: 0 !important;
            padding-top: 80px !important; 
        }

        .app-nav {
            top: 0; bottom: auto; /* Pindah ke atas */
            padding: 0 50px;
            height: 70px;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        /* Munculkan Logo Brand di Kiri */
        .nav-brand {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: #059669;
            letter-spacing: -0.5px;
        }

        .nav-brand span { color: #f97316; /* Aksen oranye AI */ }

        .nav-links {
            width: auto;
            gap: 20px;
        }

        .nav-item {
            flex-direction: row; /* Ikon dan teks sejajar menyamping */
            font-size: 0.95rem;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
        }

        .nav-item:hover { background: #f3f4f6; color: #059669; }
        .nav-item.active { background: #d1fae5; color: #059669; }
    }
</style>

<div class="app-nav">
    <div class="nav-brand">Hifzly<span>.</span></div>

    <div class="nav-links">
        <a href="dashboard.php" class="nav-item <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Beranda</span>
        </a>
        <a href="alquran.php" class="nav-item <?= $current_page == 'alquran.php' ? 'active' : '' ?>">
            <span class="nav-icon">📖</span>
            <span class="nav-text">Qur'an</span>
        </a>
        <a href="mutabaah.php" class="nav-item <?= $current_page == 'mutabaah.php' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Mutabaah</span>
        </a>
        <a href="../logout.php" class="nav-item" onclick="return confirm('Yakin ingin keluar?')">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Keluar</span>
        </a>
    </div>
</div>