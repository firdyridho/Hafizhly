<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Sapaan dinamis berdasarkan jam server
$jam = (int) date('H');
if ($jam < 11) {
    $sapaan = 'Selamat Pagi';
    $sapaanIcon = 'fa-solid fa-sun';
} elseif ($jam < 15) {
    $sapaan = 'Selamat Siang';
    $sapaanIcon = 'fa-solid fa-sun';
} elseif ($jam < 18) {
    $sapaan = 'Selamat Sore';
    $sapaanIcon = 'fa-solid fa-cloud-sun';
} else {
    $sapaan = 'Selamat Malam';
    $sapaanIcon = 'fa-solid fa-moon';
}

// Tanggal hari ini dalam Bahasa Indonesia
$hariIndo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$bulanIndo = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
$tanggalHariIni = $hariIndo[date('l')] . ', ' . date('d') . ' ' . $bulanIndo[(int) date('n')] . ' ' . date('Y');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#059669">
    <title>Dashboard - Hafizhly</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --primary: #059669;
            --primary-dark: #04785a;
            --primary-light: #34d399;
            --gold: #c9a227;
            --gold-light: #e8c85f;
            --dark: #0f172a;
            --muted: #6b7280;
            --bg: #f5f8f6;
            --card-bg: #ffffff;
            --border-soft: rgba(15, 23, 42, 0.06);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        h1,
        h2,
        h3,
        .display-font {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
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

        .fade-in {
            opacity: 0;
            animation: fadeUp 0.6s ease forwards;
        }

        /* ===== Header Profil ===== */
        .header-profile {
            position: relative;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary) 55%, var(--primary-light));
            padding: 28px 20px 46px;
            border-bottom-left-radius: 32px;
            border-bottom-right-radius: 32px;
            box-shadow: 0 12px 30px rgba(5, 150, 105, 0.28);
            overflow: hidden;
        }

        .header-profile::before {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201, 162, 39, 0.35), transparent 70%);
            top: -90px;
            right: -60px;
            filter: blur(10px);
        }

        .header-profile::after {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.18), transparent 70%);
            bottom: -80px;
            left: -40px;
        }

        .header-inner {
            position: relative;
            z-index: 2;
            max-width: 640px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }

        .greeting-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            background: rgba(255, 255, 255, 0.14);
            padding: 5px 12px;
            border-radius: 30px;
            margin-bottom: 10px;
        }

        .greeting h1 {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .greeting .date-text {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 4px;
        }

        .avatar {
            width: 52px;
            height: 52px;
            flex-shrink: 0;
            background: #fff;
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.3rem;
            color: var(--primary);
            font-weight: 800;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        /* ===== Container ===== */
        .container {
            padding: 0 20px 20px;
            max-width: 640px;
            margin: -26px auto 0;
            position: relative;
            z-index: 3;
        }

        /* ===== Widget Jadwal Sholat ===== */
        .prayer-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            border: 1px solid var(--border-soft);
            margin-bottom: 26px;
        }

        .prayer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-soft);
        }

        .prayer-title {
            font-weight: 700;
            color: var(--dark);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .prayer-title i {
            color: var(--primary);
            font-size: 0.95rem;
        }

        .prayer-location {
            font-size: 0.78rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .prayer-times {
            display: flex;
            justify-content: space-between;
            gap: 6px;
        }

        .prayer-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 10px 4px;
            border-radius: 14px;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .prayer-item i {
            font-size: 0.95rem;
            color: var(--muted);
        }

        .prayer-name {
            font-size: 0.7rem;
            color: var(--muted);
            font-weight: 600;
        }

        .prayer-time {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .prayer-item.next {
            background: linear-gradient(160deg, #ecfdf5, #f0fdf4);
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.15);
        }

        .prayer-item.next i,
        .prayer-item.next .prayer-name {
            color: var(--primary-dark);
        }

        .prayer-item.next .prayer-time {
            color: var(--primary-dark);
        }

        .prayer-item.next::after {
            content: 'Selanjutnya';
            font-size: 0.55rem;
            font-weight: 700;
            color: #fff;
            background: var(--primary);
            padding: 2px 6px;
            border-radius: 20px;
            margin-top: 2px;
        }

        /* Shimmer loading state */
        .prayer-skeleton {
            display: flex;
            gap: 8px;
            width: 100%;
        }

        .skeleton-item {
            flex: 1;
            height: 58px;
            border-radius: 12px;
            background: linear-gradient(90deg, #eef2f0 25%, #f7f9f8 37%, #eef2f0 63%);
            background-size: 400% 100%;
            animation: shimmer 1.4s ease infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* ===== Menu Grid ===== */
        .section-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 14px;
            color: var(--dark);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }

        .menu-card {
            position: relative;
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 20px 14px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.08);
        }

        .menu-card:active {
            transform: scale(0.96);
        }

        .menu-icon {
            width: 50px;
            height: 50px;
            background: #ecfdf5;
            border-radius: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.35rem;
            color: var(--primary);
            transition: transform 0.3s ease;
        }

        .menu-card:hover .menu-icon {
            transform: scale(1.08) rotate(-4deg);
        }

        .menu-text {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--dark);
        }

        .menu-card.highlight {
            border: 1px solid rgba(5, 150, 105, 0.25);
            background: linear-gradient(160deg, #f0fdf4, #ffffff);
        }

        .menu-card.highlight .menu-icon {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: #fff;
            box-shadow: 0 8px 18px rgba(5, 150, 105, 0.3);
        }

        .menu-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.6rem;
            font-weight: 700;
            color: #fff;
            background: var(--gold);
            padding: 3px 8px;
            border-radius: 20px;
            letter-spacing: 0.4px;
        }

        /* ===== Desktop ===== */
        @media (min-width: 768px) {
            .header-profile {
                padding: 36px 40px 56px;
                border-bottom-left-radius: 36px;
                border-bottom-right-radius: 36px;
            }

            .header-inner {
                max-width: 900px;
            }

            .greeting h1 {
                font-size: 1.7rem;
            }

            .avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
                border-radius: 18px;
            }

            .container {
                max-width: 900px;
                margin-top: -30px;
                padding: 0 40px 40px;
            }

            .prayer-card {
                padding: 26px 30px;
            }

            .prayer-item {
                padding: 14px 6px;
            }

            .prayer-time {
                font-size: 1.05rem;
            }

            .menu-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 18px;
            }

            .menu-card {
                padding: 26px 16px;
            }

            .menu-icon {
                width: 58px;
                height: 58px;
                font-size: 1.5rem;
            }
        }

        @media (max-width: 360px) {
            .prayer-times {
                flex-wrap: wrap;
                row-gap: 10px;
            }

            .prayer-item {
                flex: 1 1 30%;
            }
        }
    </style>
</head>

<body>

    <div class="header-profile">
        <div class="header-inner fade-in">
            <div class="greeting">
                <span class="greeting-tag"><i class="<?= $sapaanIcon ?>"></i><?= $sapaan ?></span>
                <h1><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h1>
                <div class="date-text"><?= $tanggalHariIni ?></div>
            </div>
            <div class="avatar">
                <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="prayer-card fade-in" style="animation-delay:0.1s;">
            <div class="prayer-header">
                <div class="prayer-title"><i class="fa-solid fa-mosque"></i>Jadwal Sholat</div>
                <div class="prayer-location"><i class="fa-solid fa-location-dot"></i><span id="loc-text">Memuat...</span></div>
            </div>
            <div class="prayer-times" id="prayer-container">
                <div class="prayer-skeleton">
                    <div class="skeleton-item"></div>
                    <div class="skeleton-item"></div>
                    <div class="skeleton-item"></div>
                    <div class="skeleton-item"></div>
                    <div class="skeleton-item"></div>
                </div>
            </div>
        </div>

        <div class="section-title fade-in" style="animation-delay:0.15s;">Eksplorasi</div>
        <div class="menu-grid">
            <a href="alquran.php" class="menu-card fade-in" style="animation-delay:0.2s;">
                <div class="menu-icon"><i class="fa-solid fa-book-open-reader"></i></div>
                <div class="menu-text">Al-Qur'an</div>
            </a>
            <a href="#" class="menu-card highlight fade-in" style="animation-delay:0.25s;">
                <span class="menu-badge">AI</span>
                <div class="menu-icon"><i class="fa-solid fa-microphone-lines"></i></div>
                <div class="menu-text">Smart Murojaah</div>
            </a>
            <a href="mutabaah.php" class="menu-card fade-in" style="animation-delay:0.3s;">
                <div class="menu-icon"><i class="fa-solid fa-chart-simple"></i></div>
                <div class="menu-text">Mutabaah</div>
            </a>
            <a href="#" class="menu-card fade-in" style="animation-delay:0.35s;">
                <div class="menu-icon"><i class="fa-solid fa-hands-praying"></i></div>
                <div class="menu-text">Doa Harian</div>
            </a>
        </div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script>
        const prayerIcons = {
            Fajr: 'fa-solid fa-cloud-moon',
            Dhuhr: 'fa-solid fa-sun',
            Asr: 'fa-solid fa-cloud-sun',
            Maghrib: 'fa-solid fa-sunset',
            Isha: 'fa-solid fa-moon'
        };
        const prayerLabels = {
            Fajr: 'Subuh',
            Dhuhr: 'Dzuhur',
            Asr: 'Ashar',
            Maghrib: 'Maghrib',
            Isha: 'Isya'
        };

        function getNextPrayerKey(timings) {
            const now = new Date();
            const nowMinutes = now.getHours() * 60 + now.getMinutes();
            const order = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
            for (const key of order) {
                const [h, m] = timings[key].split(':').map(Number);
                if ((h * 60 + m) > nowMinutes) return key;
            }
            return order[0]; // semua sudah lewat, tandai Subuh besok
        }

        async function fetchPrayerTimes() {
            try {
                const response = await fetch('https://api.aladhan.com/v1/timingsByCity?city=Jakarta&country=Indonesia&method=11');
                const result = await response.json();
                const timings = result.data.timings;

                document.getElementById('loc-text').innerText = 'Jakarta, ID';

                const nextKey = getNextPrayerKey(timings);
                const order = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

                const prayerHTML = order.map((key) => `
                    <div class="prayer-item ${key === nextKey ? 'next' : ''}">
                        <i class="${prayerIcons[key]}"></i>
                        <span class="prayer-name">${prayerLabels[key]}</span>
                        <span class="prayer-time">${timings[key]}</span>
                    </div>
                `).join('');

                document.getElementById('prayer-container').innerHTML = prayerHTML;
            } catch (error) {
                document.getElementById('prayer-container').innerHTML =
                    "<div style='text-align:center;width:100%;font-size:0.8rem;color:#ef4444;'><i class='fa-solid fa-triangle-exclamation'></i> Gagal memuat jadwal sholat.</div>";
            }
        }

        fetchPrayerTimes();
    </script>
</body>

</html>