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
    <title>Dashboard - Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <!-- FontAwesome sudah ditarik otomatis dari nav.php nanti, tapi kita pastikan styling utamanya aman -->
    <style>
        :root {
            --primary: #064e3b;
            --primary-light: #059669;
            --accent: #fbbf24;
            --dark: #1f2937;
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-muted: #6b7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 90px;
        }

        /* HEADER HERO */
        .hero-section {
            background: linear-gradient(160deg, #022c22, var(--primary), var(--primary-light));
            color: white;
            padding: 30px 20px 80px 20px;
            position: relative;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            text-align: center;
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
        }

        .location-badge i {
            color: var(--accent);
        }

        .profile-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .bismillah {
            font-size: 1.8rem;
            font-family: 'Amiri', 'Traditional Arabic', serif;
            margin-bottom: 15px;
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
        }

        .date-box .value {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .realtime-clock {
            font-size: 2.2rem;
            font-weight: bold;
            letter-spacing: 2px;
        }

        /* Jadwal Sholat */
        .prayer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 0, 0.15);
            padding: 15px;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .prayer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            opacity: 0.6;
            transition: 0.3s;
        }

        .prayer-item.active {
            opacity: 1;
            transform: scale(1.1);
        }

        .prayer-item.active .p-time {
            color: var(--accent);
        }

        .prayer-item.active .p-icon {
            color: var(--accent);
        }

        .p-time {
            font-size: 0.85rem;
            font-weight: bold;
        }

        .p-icon {
            font-size: 1.3rem;
        }

        .p-name {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .countdown-text {
            font-size: 0.9rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .countdown-text span {
            color: var(--accent);
            font-weight: bold;
        }

        /* MAIN CONTENT */
        .main-content {
            padding: 0 20px;
            max-width: 600px;
            margin: -50px auto 0;
            position: relative;
            z-index: 5;
        }

        .floating-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            cursor: pointer;
            transition: 0.3s;
        }

        .fc-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .progress-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-light) 21%, #e5e7eb 0);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress-inner {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.75rem;
            font-weight: bold;
            color: var(--dark);
        }

        .fc-text h4 {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .fc-text h3 {
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 3px;
        }

        .fc-badges {
            display: flex;
            gap: 5px;
        }

        .fc-badge {
            font-size: 0.7rem;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 10px;
            color: var(--text-muted);
        }

        .fc-arrow {
            width: 30px;
            height: 30px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        /* Quick Access */
        .section-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px 10px;
            margin-bottom: 30px;
        }

        .q-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .q-icon {
            width: 55px;
            height: 55px;
            background: var(--card-bg);
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
            color: var(--primary);
            border: 1px solid #f3f4f6;
            transition: 0.2s;
        }

        .q-item:hover .q-icon {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
        }

        .q-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--dark);
            text-align: center;
        }

        @media (min-width: 768px) {
            .hero-section {
                padding-top: 100px;
                border-bottom-left-radius: 60px;
                border-bottom-right-radius: 60px;
            }

            .quick-grid {
                grid-template-columns: repeat(6, 1fr);
            }

            .main-content {
                max-width: 800px;
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
            <div style="font-size: 0.85rem; width: 100%; text-align: center;">Menyelaraskan jadwal sholat...</div>
        </div>

        <div class="countdown-text" id="countdown-text">--:--:-- menuju waktu sholat berikutnya</div>
    </div>

    <div class="main-content">
        <div class="floating-card" onclick="window.location.href='alquran.php'">
            <div class="fc-left">
                <div class="progress-circle">
                    <div class="progress-inner">21%</div>
                </div>
                <div class="fc-text">
                    <h4>Lanjutkan Hafalan</h4>
                    <h3>Surah Al-Baqarah</h3>
                    <div class="fc-badges">
                        <span class="fc-badge">Juz 1</span>
                        <span class="fc-badge">Ayat 60</span>
                    </div>
                </div>
            </div>
            <div class="fc-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>

        <h3 class="section-title">Menu</h3>
        <div class="quick-grid">
            <a href="alquran.php" class="q-item">
                <div class="q-icon" style="color: #059669;"><i class="fas fa-book-open"></i></div>
                <div class="q-text">Qur'an</div>
            </a>
            <a href="smart_murojaah.php" class="q-item">
                <div class="q-icon" style="color: #059669;"><i class="fas fa-microphone-alt"></i></div>
                <div class="q-text">Murojaah</div>
            </a>
            <a href="mutabaah.php" class="q-item">
                <div class="q-icon" style="color: #059669;"><i class="fas fa-chart-line"></i></div>
                <div class="q-text">Mutabaah</div>
            </a>
            <a href="doa.php" class="q-item">
                <div class="q-icon" style="color: #059669;"><i class="fas fa-hands-praying"></i></div>
                <div class="q-text">Doa</div>
            </a>
            <a href="target.php" class="q-item">
                <div class="q-icon" style="color: #059669;"><i class="fas fa-bullseye"></i></div>
                <div class="q-text">Target</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #059669;"><i class="fas fa-medal"></i></div>
                <div class="q-text">Pencapaian</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #059669;"><i class="fas fa-robot"></i></div>
                <div class="q-text">AI Coach</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #059669;"><i class="fas fa-cog"></i></div>
                <div class="q-text">Pengaturan</div>
            </a>
        </div>
    </div>

    <!-- Panggil Navigasi (Otomatis load FontAwesome) -->
    <?php include '../components/nav.php'; ?>

    <script>
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
            fetchPrayerAPI(-6.1824, 106.3351);
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

        // Konfigurasi Ikon FontAwesome untuk Sholat
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