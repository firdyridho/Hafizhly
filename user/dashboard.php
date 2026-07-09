<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Hifzly</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter + Amiri -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --emerald-50: #ecfdf5;
            --emerald-100: #d1fae5;
            --emerald-500: #10b981;
            --emerald-600: #059669;
            --emerald-700: #047857;
            --surface: #ffffff;
            --text-primary: #111827;
            --text-muted: #9ca3af;
            --border-light: #f3f4f6;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.08);
            --radius: 16px;
            --radius-lg: 20px;
            --transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafb;
            color: var(--text-primary);
            min-height: 100vh;
            padding-bottom: 90px;
            /* ruang untuk bottom nav di mobile */
            -webkit-font-smoothing: antialiased;
        }

        /* ========== NAVBAR DESKTOP (d-none d-md-flex) ========== */
        .navbar-desktop {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: var(--shadow-sm);
            padding: 0.6rem 1.2rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            display: none;
            /* default hidden, ditampilkan di md */
        }

        @media (min-width: 768px) {
            .navbar-desktop {
                display: flex;
            }

            body {
                padding-bottom: 20px;
                /* kurangi padding bawah karena pakai navbar atas */
            }
        }

        .navbar-desktop .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--emerald-700);
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .navbar-desktop .nav-link {
            font-weight: 500;
            color: var(--text-primary);
            margin: 0 0.15rem;
            padding: 0.5rem 0.9rem;
            border-radius: 10px;
            transition: var(--transition);
            font-size: 0.9rem;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .navbar-desktop .nav-link:hover {
            background: var(--emerald-50);
            color: var(--emerald-700);
        }

        .navbar-desktop .nav-link.active {
            background: var(--emerald-50);
            color: var(--emerald-700);
            font-weight: 600;
        }

        .navbar-desktop .navbar-nav {
            margin-left: auto;
        }

        /* ========== HEADER PREMIUM ========== */
        .header-wrapper {
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, #064e3b 0%, #047857 25%, #059669 55%, #10b981 100%);
            padding: 24px 20px 50px 20px;
            border-bottom-left-radius: 32px;
            border-bottom-right-radius: 32px;
            box-shadow: 0 10px 40px rgba(5, 150, 105, 0.25);
        }

        .header-wrapper::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
            pointer-events: none;
        }

        .header-wrapper::after {
            content: '';
            position: absolute;
            bottom: -40px;
            left: -40px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
            pointer-events: none;
        }

        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 650px;
            margin: 0 auto;
        }

        .greeting-salam {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 2px;
        }

        .greeting-name {
            font-size: 1.55rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.2;
        }

        .greeting-subtitle {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 4px;
        }

        .avatar-premium {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            border: 2.5px solid rgba(255, 255, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
        }

        .avatar-premium:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.7);
        }

        /* ========== MAIN CONTAINER ========== */
        .main-container {
            max-width: 650px;
            margin: -28px auto 0;
            padding: 0 16px;
            position: relative;
            z-index: 10;
        }

        /* ========== PRAYER CARD ========== */
        .prayer-card-premium {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 20px 16px 18px;
            box-shadow: var(--shadow-lg);
            margin-bottom: 22px;
            border: 1px solid var(--border-light);
        }

        .prayer-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-light);
        }

        .prayer-label {
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .prayer-label i {
            color: var(--emerald-500);
            font-size: 1.2rem;
        }

        .prayer-location-badge {
            font-size: 0.75rem;
            color: var(--text-muted);
            background: #f9fafb;
            padding: 6px 12px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .prayer-times-row {
            display: flex;
            justify-content: space-between;
            text-align: center;
            gap: 2px;
        }

        .prayer-time-block {
            flex: 1;
            padding: 6px 2px;
            border-radius: 10px;
            transition: var(--transition);
        }

        .prayer-time-block:hover {
            background: var(--emerald-50);
        }

        .prayer-time-block.active {
            background: var(--emerald-50);
        }

        .prayer-time-name {
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .prayer-time-value {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .prayer-time-block.active .prayer-time-name {
            color: var(--emerald-600);
        }

        .prayer-time-block.active .prayer-time-value {
            color: var(--emerald-700);
        }

        /* ========== SECTION TITLE ========== */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            margin-top: 4px;
        }

        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .section-link {
            font-size: 0.8rem;
            color: var(--emerald-600);
            text-decoration: none;
            font-weight: 600;
        }

        /* ========== MENU GRID ========== */
        .menu-grid-premium {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .menu-card-premium {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 18px 14px;
            text-decoration: none;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            border: 1.5px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .menu-card-premium:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--emerald-200);
        }

        .menu-card-premium.featured {
            background: linear-gradient(135deg, #f0fdf6 0%, #ecfdf5 100%);
            border-color: var(--emerald-300);
        }

        .menu-card-premium.featured .menu-icon-circle {
            background: linear-gradient(135deg, var(--emerald-500), var(--emerald-600));
            color: #fff;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35);
        }

        .menu-icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: var(--emerald-50);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: var(--emerald-600);
        }

        .menu-label {
            font-size: 0.88rem;
            font-weight: 600;
            text-align: center;
        }

        .menu-sub {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: -6px;
            text-align: center;
        }

        .badge-new {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--emerald-500);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
        }

        /* ========== STATS ========== */
        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 16px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .stat-icon.streak {
            background: #fff7ed;
            color: #f97316;
        }

        .stat-icon.progress {
            background: #eff6ff;
            color: #3b82f6;
        }

        .stat-value {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ========== BOTTOM NAV (MOBILE ONLY) ========== */
        .bottom-nav {
            position: fixed;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: #ffffff;
            border-radius: 20px;
            padding: 8px 10px;
            display: flex;
            gap: 4px;
            box-shadow: 0 8px 35px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(0, 0, 0, 0.04);
            width: fit-content;
            max-width: 95vw;
            display: none;
            /* default hidden, tampil di mobile */
        }

        @media (max-width: 767.98px) {
            .bottom-nav {
                display: flex;
            }
        }

        .bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 10px 16px;
            border-radius: 14px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 0.68rem;
            font-weight: 500;
            transition: var(--transition);
            white-space: nowrap;
            min-width: 56px;
        }

        .bottom-nav a i {
            font-size: 1.4rem;
        }

        .bottom-nav a.active {
            background: var(--emerald-50);
            color: var(--emerald-700);
            font-weight: 700;
        }

        .bottom-nav a.active i {
            color: var(--emerald-600);
        }

        .bottom-nav a:hover {
            color: var(--emerald-600);
            background: var(--emerald-50);
        }
    </style>
</head>

<body>

    <!-- ==================== NAVBAR DESKTOP (Header untuk desktop) ==================== -->
    <nav class="navbar-desktop navbar navbar-expand">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-bookmark-heart-fill"></i> Hifzly
            </a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php"><i class="bi bi-house-door-fill"></i> Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="alquran.php"><i class="bi bi-book"></i> Al-Qur'an</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-mic-fill"></i> Murojaah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="mutabaah.php"><i class="bi bi-bar-chart-fill"></i> Mutabaah</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profil.php"><i class="bi bi-person"></i> Profil</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ==================== HEADER PROFILE ==================== -->
    <div class="header-wrapper">
        <div class="header-content">
            <div>
                <div class="greeting-salam">Assalamu'alaikum wr. wb. 👋</div>
                <div class="greeting-name"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
                <div class="greeting-subtitle">Semoga hari ini penuh berkah ✨</div>
            </div>
            <a href="profil.php" class="avatar-premium">
                <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
            </a>
        </div>
    </div>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="main-container">
        <!-- Jadwal Sholat -->
        <div class="prayer-card-premium">
            <div class="prayer-top">
                <div class="prayer-label"><i class="bi bi-clock"></i> Jadwal Sholat</div>
                <div class="prayer-location-badge">
                    <i class="bi bi-geo-alt-fill"></i> <span id="loc-text">Memuat...</span>
                </div>
            </div>
            <div class="prayer-times-row" id="prayer-container">
                <div style="text-align:center;width:100%;font-size:0.82rem;color:#9ca3af;padding:10px 0;">
                    <span class="spinner-border spinner-border-sm text-emerald-500 me-2"></span>
                    Mengambil jadwal...
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon streak"><i class="bi bi-fire"></i></div>
                <div class="stat-info">
                    <div class="stat-value">12 Hari</div>
                    <div class="stat-label">Streak Mengaji</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon progress"><i class="bi bi-trophy"></i></div>
                <div class="stat-info">
                    <div class="stat-value">3 Juz</div>
                    <div class="stat-label">Progress Hafalan</div>
                </div>
            </div>
        </div>

        <!-- Menu Eksplorasi -->
        <div class="section-header">
            <div class="section-title">📌 Menu Utama</div>
            <a href="#" class="section-link">Lihat Semua →</a>
        </div>
        <div class="menu-grid-premium">
            <a href="alquran.php" class="menu-card-premium">
                <div class="menu-icon-circle"><i class="bi bi-book"></i></div>
                <div class="menu-label">Al-Qur'an</div>
                <div class="menu-sub">Baca & tadabbur</div>
            </a>
            <a href="#" class="menu-card-premium featured">
                <span class="badge-new">AI</span>
                <div class="menu-icon-circle"><i class="bi bi-mic"></i></div>
                <div class="menu-label">Smart Murojaah</div>
                <div class="menu-sub">Setor hafalan pakai AI</div>
            </a>
            <a href="mutabaah.php" class="menu-card-premium">
                <div class="menu-icon-circle"><i class="bi bi-graph-up"></i></div>
                <div class="menu-label">Mutabaah</div>
                <div class="menu-sub">Pantau progress harian</div>
            </a>
            <a href="#" class="menu-card-premium">
                <div class="menu-icon-circle"><i class="bi bi-heart"></i></div>
                <div class="menu-label">Doa Harian</div>
                <div class="menu-sub">Kumpulan doa pilihan</div>
            </a>
        </div>

        <!-- Lainnya -->
        <div class="section-header">
            <div class="section-title">🔧 Lainnya</div>
        </div>
        <div class="menu-grid-premium" style="margin-bottom: 10px;">
            <a href="#" class="menu-card-premium">
                <div class="menu-icon-circle"><i class="bi bi-gear"></i></div>
                <div class="menu-label">Pengaturan</div>
                <div class="menu-sub">Atur preferensi</div>
            </a>
            <a href="../logout.php" class="menu-card-premium" style="border-color: #fee2e2;">
                <div class="menu-icon-circle" style="background:#fef2f2;color:#ef4444;"><i class="bi bi-box-arrow-right"></i></div>
                <div class="menu-label" style="color:#dc2626;">Keluar</div>
                <div class="menu-sub">Logout akun</div>
            </a>
        </div>
    </div>

    <!-- ==================== BOTTOM NAV (Footer untuk mobile) ==================== -->
    <nav class="bottom-nav" id="bottomNav">
        <a href="dashboard.php" class="active"><i class="bi bi-house-door-fill"></i><span>Beranda</span></a>
        <a href="alquran.php"><i class="bi bi-book"></i><span>Al-Qur'an</span></a>
        <a href="#"><i class="bi bi-mic-fill"></i><span>Murojaah</span></a>
        <a href="mutabaah.php"><i class="bi bi-bar-chart-fill"></i><span>Mutabaah</span></a>
        <a href="profil.php"><i class="bi bi-person"></i><span>Profil</span></a>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fetch jadwal sholat
        async function fetchPrayerTimes() {
            const container = document.getElementById('prayer-container');
            const locText = document.getElementById('loc-text');
            try {
                const response = await fetch('https://api.aladhan.com/v1/timingsByCity?city=Jakarta&country=Indonesia&method=11');
                const result = await response.json();
                const timings = result.data.timings;
                locText.innerText = 'Jakarta, ID';
                const prayerData = [{
                        name: 'Subuh',
                        time: timings.Fajr,
                        key: 'fajr'
                    },
                    {
                        name: 'Dzuhur',
                        time: timings.Dhuhr,
                        key: 'dhuhr'
                    },
                    {
                        name: 'Ashar',
                        time: timings.Asr,
                        key: 'asr'
                    },
                    {
                        name: 'Maghrib',
                        time: timings.Maghrib,
                        key: 'maghrib'
                    },
                    {
                        name: 'Isya',
                        time: timings.Isha,
                        key: 'isha'
                    }
                ];
                const now = new Date();
                const currentTotal = now.getHours() * 60 + now.getMinutes();
                const prayerMinutes = prayerData.map(p => {
                    const [h, m] = p.time.split(':').map(Number);
                    return {
                        key: p.key,
                        total: h * 60 + m
                    };
                });
                let activeKey = prayerMinutes[prayerMinutes.length - 1].key;
                for (let i = prayerMinutes.length - 1; i >= 0; i--) {
                    if (currentTotal >= prayerMinutes[i].total) {
                        activeKey = prayerMinutes[i].key;
                        break;
                    }
                }
                let html = '';
                prayerData.forEach(p => {
                    const isActive = p.key === activeKey ? ' active' : '';
                    html += `<div class="prayer-time-block${isActive}">
                                <div class="prayer-time-name">${p.name}</div>
                                <div class="prayer-time-value">${p.time}</div>
                            </div>`;
                });
                container.innerHTML = html;
            } catch (error) {
                container.innerHTML = "<div style='font-size:0.78rem;color:#ef4444;text-align:center;'>⚠️ Gagal memuat jadwal</div>";
            }
        }
        fetchPrayerTimes();

        // Active state bottom nav (mobile)
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('#bottomNav a');
            const currentPath = window.location.pathname;
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') && currentPath.includes(link.getAttribute('href').replace(/\/$/, '').split('/').pop())) {
                    link.classList.add('active');
                }
                if (currentPath.includes('dashboard.php') && link.getAttribute('href') === 'dashboard.php') {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>