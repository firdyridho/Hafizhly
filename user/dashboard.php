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
    <style>
        :root {
            --primary: #064e3b;
            /* Hijau sangat gelap ala referensi gambar */
            --primary-light: #059669;
            --accent: #fbbf24;
            /* Emas */
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

        /* HEADER HERO (Setengah Layar Atas) */
        .hero-section {
            background: linear-gradient(160deg, #022c22, var(--primary), var(--primary-light));
            color: white;
            padding: 30px 20px 80px 20px;
            position: relative;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            text-align: center;
        }

        /* Top Bar: Lokasi & Profil */
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
            gap: 6px;
            cursor: pointer;
            transition: 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .location-badge:hover {
            background: rgba(255, 255, 255, 0.25);
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

        /* Bismillah & Waktu */
        .bismillah {
            font-size: 1.8rem;
            font-family: 'Amiri', 'Traditional Arabic', serif;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
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
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        /* Jadwal Sholat Horizontal */
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
            gap: 5px;
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

        .p-time {
            font-size: 0.85rem;
            font-weight: bold;
        }

        .p-icon {
            font-size: 1.2rem;
        }

        .p-name {
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        /* Countdown */
        .countdown-text {
            font-size: 0.9rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .countdown-text span {
            color: var(--accent);
            font-weight: bold;
        }

        /* KONTEN UTAMA (Overlapping Hero) */
        .main-content {
            padding: 0 20px;
            max-width: 600px;
            margin: -50px auto 0;
            position: relative;
            z-index: 5;
        }

        /* Floating Card (Continue Reading) */
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

        .floating-card:active {
            transform: scale(0.98);
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
            color: var(--dark);
        }

        /* Quick Access Grid */
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
            gap: 8px;
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
            font-size: 1.6rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
            color: var(--primary);
            transition: 0.2s;
            border: 1px solid #f3f4f6;
        }

        .q-item:active .q-icon {
            transform: scale(0.9);
            background: #ecfdf5;
            border-color: var(--primary-light);
        }

        .q-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--dark);
            text-align: center;
        }

        /* Responsif Desktop */
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

    <!-- HERO SECTION -->
    <div class="hero-section">
        <!-- Top Bar -->
        <div class="top-bar">
            <!-- Tombol Lokasi -->
            <div class="location-badge" onclick="getLocation()">
                <span id="loc-icon">📍</span>
                <span id="location-text">Mencari Lokasi...</span>
            </div>
            <!-- Profil -->
            <div class="profile-btn">
                <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
            </div>
        </div>

        <div class="bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>

        <!-- Tanggal & Jam -->
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

        <!-- Jadwal Sholat -->
        <div class="prayer-row" id="prayer-container">
            <div style="font-size: 0.85rem; width: 100%; text-align: center;">Menyelaraskan jadwal sholat...</div>
        </div>

        <div class="countdown-text" id="countdown-text">--:--:-- menuju waktu sholat berikutnya</div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- Floating Card -->
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
            <div class="fc-arrow">❯</div>
        </div>

        <!-- Quick Access Grid -->
        <h3 class="section-title">Akses Cepat</h3>
        <div class="quick-grid">
            <a href="alquran.php" class="q-item">
                <div class="q-icon">📖</div>
                <div class="q-text">Qur'an</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #f59e0b;">🎙️</div>
                <div class="q-text">Smart Murojaah</div>
            </a>
            <a href="mutabaah.php" class="q-item">
                <div class="q-icon" style="color: #3b82f6;">📊</div>
                <div class="q-text">Mutabaah</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #8b5cf6;">🤲</div>
                <div class="q-text">Doa & Zikir</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #ef4444;">🎯</div>
                <div class="q-text">Target</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #10b981;">🏆</div>
                <div class="q-text">Pencapaian</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #6366f1;">🤖</div>
                <div class="q-text">AI Coach</div>
            </a>
            <a href="#" class="q-item">
                <div class="q-icon" style="color: #6b7280;">⚙️</div>
                <div class="q-text">Pengaturan</div>
            </a>
        </div>
    </div>

    <!-- Panggil Navigasi -->
    <?php include '../components/nav.php'; ?>

    <script>
        let prayerTimesData = null;

        // 1. FUNGSI JAM REAL-TIME
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').innerText = `${h}:${m}:${s}`;

            // Perbarui countdown jika jadwal sholat sudah dimuat
            if (prayerTimesData) updateCountdown(now);
        }
        setInterval(updateClock, 1000);
        updateClock(); // Panggil sekali di awal

        // 2. FUNGSI GEOLOCATION & JADWAL SHOLAT
        function getLocation() {
            document.getElementById('location-text').innerText = "Melacak...";
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                fallbackLocation();
            }
        }

        function showPosition(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            fetchPrayerAPI(lat, lon);
            fetchCityName(lat, lon);
        }

        // Jika GPS ditolak / error, gunakan default Cikande, Banten
        function showError(error) {
            console.log("GPS Error:", error);
            fallbackLocation();
        }

        function fallbackLocation() {
            document.getElementById('location-text').innerText = "Cikande, Banten";
            // Koordinat Cikande
            fetchPrayerAPI(-6.1824, 106.3351);
        }

        // Ambil Nama Kota dari Koordinat
        async function fetchCityName(lat, lon) {
            try {
                const res = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=id`);
                const data = await res.json();
                const city = data.city || data.locality || "Lokasi Anda";
                document.getElementById('location-text').innerText = city;
            } catch (e) {
                document.getElementById('location-text').innerText = "Lokasi Ditemukan";
            }
        }

        // Ambil Jadwal Sholat & Tanggal Hijriah dari Aladhan API
        async function fetchPrayerAPI(lat, lon) {
            try {
                const res = await fetch(`https://api.aladhan.com/v1/timings?latitude=${lat}&longitude=${lon}&method=11`);
                const result = await res.json();

                const data = result.data;
                prayerTimesData = data.timings;

                // Set Tanggal
                document.getElementById('masehi-date').innerText = data.date.gregorian.date;
                document.getElementById('hijri-date').innerText = `${data.date.hijri.day} ${data.date.hijri.month.en} ${data.date.hijri.year}`;

                renderPrayerTimes();
            } catch (e) {
                document.getElementById('prayer-container').innerHTML = "<div style='color:red;'>Gagal memuat jadwal.</div>";
            }
        }

        // 3. FUNGSI RENDER JADWAL & COUNTDOWN
        const prayerConfig = [{
                id: 'Fajr',
                name: 'Subuh',
                icon: '🌅'
            },
            {
                id: 'Dhuhr',
                name: 'Dzuhur',
                icon: '☀️'
            },
            {
                id: 'Asr',
                name: 'Ashar',
                icon: '⛅'
            },
            {
                id: 'Maghrib',
                name: 'Maghrib',
                icon: '🌇'
            },
            {
                id: 'Isha',
                name: 'Isya',
                icon: '🌙'
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

            // Cari sholat berikutnya di hari ini
            for (let i = 0; i < prayerConfig.length; i++) {
                const p = prayerConfig[i];
                const timeStr = prayerTimesData[p.id];
                const [h, m] = timeStr.split(':');

                let pTime = new Date();
                pTime.setHours(h, m, 0, 0);

                if (pTime > now) {
                    nextPrayerName = p.name;
                    nextPrayerTimeDate = pTime;
                    activeId = p.id;
                    break;
                }
            }

            // Jika semua sholat hari ini sudah lewat, maka sholat berikutnya adalah Subuh besok
            if (!nextPrayerTimeDate) {
                const fStr = prayerTimesData['Fajr'];
                const [fh, fm] = fStr.split(':');
                nextPrayerTimeDate = new Date();
                nextPrayerTimeDate.setDate(now.getDate() + 1);
                nextPrayerTimeDate.setHours(fh, fm, 0, 0);
                nextPrayerName = 'Subuh';
                activeId = 'Fajr';
            }

            // Reset highlight
            document.querySelectorAll('.prayer-item').forEach(el => el.classList.remove('active'));
            if (document.getElementById(`pr-${activeId}`)) {
                document.getElementById(`pr-${activeId}`).classList.add('active');
            }

            // Hitung selisih
            const diffMs = nextPrayerTimeDate - now;
            const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
            const diffMins = Math.floor((diffMs % 3600000) / 60000);
            const diffSecs = Math.floor((diffMs % 60000) / 1000);

            const format = (num) => String(num).padStart(2, '0');
            document.getElementById('countdown-text').innerHTML = `<span>${format(diffHrs)}:${format(diffMins)}:${format(diffSecs)}</span> menuju ${nextPrayerName}`;
        }

        // Inisialisasi Lokasi Pertama Kali
        window.onload = () => {
            getLocation();
        };
    </script>
</body>

</html>