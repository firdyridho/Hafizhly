<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// --- MENGAMBIL DATA BOOKMARK TILAWAH TERAKHIR ---
$q_bm = mysqli_query($conn, "SELECT surah, ayat FROM bookmarks WHERE user_id='$user_id' LIMIT 1");
if ($q_bm && mysqli_num_rows($q_bm) > 0) {
    $bm = mysqli_fetch_assoc($q_bm);
    $bm_surah = (int) $bm['surah'];
    $bm_ayat = (int) $bm['ayat'];
} else {
    // Default jika belum pernah bookmark
    $bm_surah = 1;
    $bm_ayat = 1;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #064e3b;
            --primary-light: #059669;
            --accent: #fbbf24;
            --dark: #0f172a;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 100px;
            overflow-x: hidden;
        }

        /* HEADER HERO */
        .hero-section {
            background: linear-gradient(160deg, #022c22, var(--primary), var(--primary-light));
            color: white;
            padding: 30px 20px 85px 20px;
            position: relative;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(5, 150, 105, 0.2);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .location-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: 0.2s;
        }

        .location-badge:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .location-badge i {
            color: var(--accent);
        }

        .profile-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 800;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .bismillah {
            font-size: 2rem;
            font-family: 'Amiri', 'Traditional Arabic', serif;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .date-time-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .date-box {
            text-align: center;
        }

        .date-box .label {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
            font-weight: 700;
        }

        .date-box .value {
            font-size: 0.9rem;
            font-weight: 700;
        }

        .realtime-clock {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Jadwal Sholat */
        .prayer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 20px;
            border-radius: 20px;
            margin-bottom: 15px;
            backdrop-filter: blur(5px);
        }

        .prayer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            opacity: 0.5;
            transition: 0.3s;
        }

        .prayer-item.active {
            opacity: 1;
            transform: scale(1.15);
        }

        .prayer-item.active .p-time,
        .prayer-item.active .p-icon {
            color: var(--accent);
        }

        .p-time {
            font-size: 0.85rem;
            font-weight: 800;
        }

        .p-icon {
            font-size: 1.3rem;
            margin: 3px 0;
        }

        .p-name {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .countdown-text {
            font-size: 0.95rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .countdown-text span {
            color: var(--accent);
            font-weight: 800;
        }

        /* MAIN CONTENT & SLIDER KARTU */
        .main-content {
            padding: 0;
            max-width: 600px;
            margin: -55px auto 0;
            position: relative;
            z-index: 5;
        }

        .slider-container {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            padding: 0 20px 20px 20px;
            scrollbar-width: none;
            /* Firefox */
        }

        .slider-container::-webkit-scrollbar {
            display: none;
            /* Chrome */
        }

        .floating-card {
            min-width: 90%;
            scroll-snap-align: center;
            background: var(--card-bg);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: var(--dark);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .floating-card:active {
            transform: scale(0.98);
        }

        .fc-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .progress-circle {
            width: 55px;
            height: 55px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-light) 100%, #e5e7eb 0);
            /* Full circle default */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress-inner {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
            color: var(--primary);
            font-weight: 800;
        }

        .fc-text h4 {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 4px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .fc-text h3 {
            font-size: 1.15rem;
            color: var(--dark);
            margin-bottom: 6px;
            font-weight: 800;
        }

        .fc-badges {
            display: flex;
            gap: 8px;
        }

        .fc-badge {
            font-size: 0.75rem;
            font-weight: 700;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 12px;
            color: var(--text-muted);
        }

        .fc-arrow {
            width: 35px;
            height: 35px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--dark);
            font-size: 0.9rem;
        }

        /* Coming Soon Overlay */
        .coming-soon-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(2px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 2;
        }

        .cs-badge {
            background: var(--dark);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Quick Access */
        .section-title {
            font-size: 1.15rem;
            font-weight: 800;
            margin: 10px 20px 15px;
            color: var(--dark);
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px 10px;
            padding: 0 20px 30px;
        }

        .q-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .q-icon {
            width: 60px;
            height: 60px;
            background: var(--card-bg);
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.6rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            color: var(--primary-light);
            border: 1px solid var(--border);
            transition: 0.3s;
        }

        .q-item:hover .q-icon {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.15);
            border-color: var(--primary-light);
        }

        .q-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--dark);
            text-align: center;
        }

        /* Dot Indicators */
        .slider-dots {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: -10px;
            margin-bottom: 20px;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: 0.3s;
        }

        .dot.active {
            background: var(--primary-light);
            width: 20px;
            border-radius: 10px;
        }

        @media (min-width: 768px) {
            .hero-section {
                padding-top: 50px;
                border-bottom-left-radius: 60px;
                border-bottom-right-radius: 60px;
            }

            .quick-grid {
                grid-template-columns: repeat(8, 1fr);
            }

            .main-content {
                max-width: 800px;
            }

            .floating-card {
                min-width: 48%;
            }
        }
    </style>
</head>

<body>

    <div class="hero-section">
        <div class="top-bar">
            <div class="location-badge" onclick="getLocation()">
                <i class="fas fa-map-marker-alt"></i>
                <span id="location-text">Mencari Lokasi...</span>
            </div>
            <div class="profile-btn"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?></div>
        </div>

        <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>

        <div class="date-time-row">
            <div class="date-box">
                <div class="label">Hijriah</div>
                <div class="value" id="hijri-date">--</div>
            </div>
            <div class="realtime-clock" id="clock">00:00:00</div>
            <div class="date-box">
                <div class="label">Masehi</div>
                <div class="value" id="masehi-date">--</div>
            </div>
        </div>

        <div class="prayer-row" id="prayer-container">
            <div style="font-size: 0.85rem; width: 100%; text-align: center; font-weight:600;">Menyelaraskan jadwal sholat...</div>
        </div>

        <div class="countdown-text" id="countdown-text">--:--:-- menuju waktu sholat berikutnya</div>
    </div>

    <div class="main-content">

        <!-- SLIDER KARTU PROGRESS -->
        <div class="slider-container" id="cardSlider" onscroll="updateDots()">

            <!-- 1. KARTU TILAWAH (Data Dinamis dari Bookmark) -->
            <a href="baca.php?surah=<?= $bm_surah ?>&ayat=<?= $bm_ayat ?>" class="floating-card">
                <div class="fc-left">
                    <div class="progress-circle">
                        <div class="progress-inner"><i class="fas fa-book-open"></i></div>
                    </div>
                    <div class="fc-text">
                        <h4>Lanjutkan Baca</h4>
                        <h3 id="bm-surah-title">Memuat...</h3>
                        <div class="fc-badges">
                            <span class="fc-badge" id="bm-surah-no">Surah Ke-<?= $bm_surah ?></span>
                            <span class="fc-badge">Ayat <?= $bm_ayat ?></span>
                        </div>
                    </div>
                </div>
                <div class="fc-arrow"><i class="fas fa-play"></i></div>
            </a>

            <!-- 2. KARTU HAFALAN (Coming Soon) -->
            <div class="floating-card" style="opacity: 0.8;">
                <div class="coming-soon-overlay">
                    <div class="cs-badge">Segera Hadir</div>
                </div>
                <div class="fc-left">
                    <div class="progress-circle" style="background: conic-gradient(#94a3b8 100%, #e5e7eb 0);">
                        <div class="progress-inner" style="color: #64748b;"><i class="fas fa-brain"></i></div>
                    </div>
                    <div class="fc-text">
                        <h4>Target Hafalan</h4>
                        <h3>Surah Al-Mulk</h3>
                        <div class="fc-badges">
                            <span class="fc-badge">Ayat 1-10</span>
                        </div>
                    </div>
                </div>
                <div class="fc-arrow"><i class="fas fa-lock"></i></div>
            </div>

            <!-- 3. KARTU MUROJAAH (Coming Soon) -->
            <div class="floating-card" style="opacity: 0.8;">
                <div class="coming-soon-overlay">
                    <div class="cs-badge">Segera Hadir</div>
                </div>
                <div class="fc-left">
                    <div class="progress-circle" style="background: conic-gradient(#94a3b8 100%, #e5e7eb 0);">
                        <div class="progress-inner" style="color: #64748b;"><i class="fas fa-sync-alt"></i></div>
                    </div>
                    <div class="fc-text">
                        <h4>Jadwal Murojaah</h4>
                        <h3>Surah Yasin</h3>
                        <div class="fc-badges">
                            <span class="fc-badge">Waktunya Review</span>
                        </div>
                    </div>
                </div>
                <div class="fc-arrow"><i class="fas fa-lock"></i></div>
            </div>

        </div>

        <!-- Indikator Slide -->
        <div class="slider-dots">
            <div class="dot active" id="dot1"></div>
            <div class="dot" id="dot2"></div>
            <div class="dot" id="dot3"></div>
        </div>

        <h3 class="section-title">Menu Eksplorasi</h3>
        <div class="quick-grid">
            <a href="alquran.php" class="q-item">
                <div class="q-icon"><i class="fas fa-book-quran"></i></div>
                <div class="q-text">Qur'an</div>
            </a>
            <a href="smart_murojaah.php" class="q-item">
                <div class="q-icon"><i class="fas fa-microphone-alt"></i></div>
                <div class="q-text">Murojaah</div>
            </a>
            <a href="mutabaah.php" class="q-item">
                <div class="q-icon"><i class="fas fa-chart-line"></i></div>
                <div class="q-text">Mutabaah</div>
            </a>
            <!-- MENU BARU: TAJWID -->
            <a href="tajwid.php" class="q-item">
                <div class="q-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="q-text">Tajwid</div>
            </a>
            <a href="doa.php" class="q-item">
                <div class="q-icon"><i class="fas fa-hands-praying"></i></div>
                <div class="q-text">Doa</div>
            </a>
            <a href="target.php" class="q-item">
                <div class="q-icon"><i class="fas fa-bullseye"></i></div>
                <div class="q-text">Target</div>
            </a>
            <a href="#" class="q-item" onclick="alert('Fitur segera hadir!')">
                <div class="q-icon"><i class="fas fa-medal"></i></div>
                <div class="q-text">Pencapaian</div>
            </a>
            <a href="#" class="q-item" onclick="alert('Fitur segera hadir!')">
                <div class="q-icon"><i class="fas fa-robot"></i></div>
                <div class="q-text">AI Coach</div>
            </a>
            <a href="game.php" class="q-item">
                <div class="q-icon"><i class="fas fa-gamepad"></i></div>
                <div class="q-text">AI Coach</div>
            </a>
        </div>
    </div>

    <!-- Panggil Navigasi -->
    <?php include '../components/nav.php'; ?>

    <script>
        // Tarik Nama Surah Bookmark lewat API agar tidak perlu hardcode array
        const bmSurahNo = <?= $bm_surah ?>;
        fetch(`https://equran.id/api/v2/surat/${bmSurahNo}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('bm-surah-title').innerText = 'Surah ' + data.data.namaLatin;
            }).catch(e => {
                document.getElementById('bm-surah-title').innerText = 'Lanjutkan Tilawah';
            });

        // Logika Titik Slider (Dots Indicator)
        function updateDots() {
            const slider = document.getElementById('cardSlider');
            const scrollLeft = slider.scrollLeft;
            const cardWidth = slider.offsetWidth;
            const activeIndex = Math.round(scrollLeft / cardWidth);

            document.querySelectorAll('.dot').forEach((dot, index) => {
                if (index === activeIndex) dot.classList.add('active');
                else dot.classList.remove('active');
            });
        }

        // --- SISTEM WAKTU & JADWAL SHOLAT ---
        let prayerTimesData = null;

        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').innerText = `${h}:${m}:${s}`;
            if (prayerTimesData) updateCountdown(now);
        }
        setInterval(updateClock, 1000);
        updateClock();

        function getLocation() {
            document.getElementById('location-text').innerText = "Melacak...";
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                fallbackLocation();
            }
        }

        function showPosition(position) {
            fetchPrayerAPI(position.coords.latitude, position.coords.longitude);
            fetchCityName(position.coords.latitude, position.coords.longitude);
        }

        function showError() {
            fallbackLocation();
        }

        function fallbackLocation() {
            document.getElementById('location-text').innerText = "Cikande, Banten";
            fetchPrayerAPI(-6.1824, 106.3351); // Koordinat Cikande
        }

        async function fetchCityName(lat, lon) {
            try {
                const res = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=id`);
                const data = await res.json();
                document.getElementById('location-text').innerText = data.city || data.locality || "Lokasi Anda";
            } catch (e) {
                document.getElementById('location-text').innerText = "Lokasi Ditemukan";
            }
        }

        async function fetchPrayerAPI(lat, lon) {
            try {
                const res = await fetch(`https://api.aladhan.com/v1/timings?latitude=${lat}&longitude=${lon}&method=11`);
                const result = await res.json();
                const data = result.data;
                prayerTimesData = data.timings;

                document.getElementById('masehi-date').innerText = data.date.gregorian.date;
                document.getElementById('hijri-date').innerText = `${data.date.hijri.day} ${data.date.hijri.month.en} ${data.date.hijri.year}`;

                renderPrayerTimes();
            } catch (e) {
                document.getElementById('prayer-container').innerHTML = "<div style='color:red;'>Gagal memuat jadwal.</div>";
            }
        }

        const prayerConfig = [{
                id: 'Fajr',
                name: 'Subuh',
                icon: '<i class="fas fa-cloud-moon"></i>'
            },
            {
                id: 'Dhuhr',
                name: 'Dzuhur',
                icon: '<i class="fas fa-sun"></i>'
            },
            {
                id: 'Asr',
                name: 'Ashar',
                icon: '<i class="fas fa-cloud-sun"></i>'
            },
            {
                id: 'Maghrib',
                name: 'Maghrib',
                icon: '<i class="fas fa-moon"></i>'
            },
            {
                id: 'Isha',
                name: 'Isya',
                icon: '<i class="fas fa-star"></i>'
            }
        ];

        function renderPrayerTimes() {
            let html = '';
            prayerConfig.forEach(p => {
                html += `
                <div class="prayer-item" id="pr-${p.id}">
                    <div class="p-time">${prayerTimesData[p.id]}</div>
                    <div class="p-icon">${p.icon}</div>
                    <div class="p-name">${p.name}</div>
                </div>`;
            });
            document.getElementById('prayer-container').innerHTML = html;
        }

        function updateCountdown(now) {
            let nextPrayerName = "";
            let nextPrayerTimeDate = null;
            let activeId = "";

            for (let i = 0; i < prayerConfig.length; i++) {
                const p = prayerConfig[i];
                const [h, m] = prayerTimesData[p.id].split(':');
                let pTime = new Date();
                pTime.setHours(h, m, 0, 0);

                if (pTime > now) {
                    nextPrayerName = p.name;
                    nextPrayerTimeDate = pTime;
                    activeId = p.id;
                    break;
                }
            }

            if (!nextPrayerTimeDate) {
                const [fh, fm] = prayerTimesData['Fajr'].split(':');
                nextPrayerTimeDate = new Date();
                nextPrayerTimeDate.setDate(now.getDate() + 1);
                nextPrayerTimeDate.setHours(fh, fm, 0, 0);
                nextPrayerName = 'Subuh';
                activeId = 'Fajr';
            }

            document.querySelectorAll('.prayer-item').forEach(el => el.classList.remove('active'));
            if (document.getElementById(`pr-${activeId}`)) {
                document.getElementById(`pr-${activeId}`).classList.add('active');
            }

            const diffMs = nextPrayerTimeDate - now;
            const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
            const diffMins = Math.floor((diffMs % 3600000) / 60000);
            const diffSecs = Math.floor((diffMs % 60000) / 1000);
            const format = (num) => String(num).padStart(2, '0');

            document.getElementById('countdown-text').innerHTML = `<span>${format(diffHrs)}:${format(diffMins)}:${format(diffSecs)}</span> menuju ${nextPrayerName}`;
        }

        window.onload = () => {
            getLocation();
        };
    </script>
</body>

</html>