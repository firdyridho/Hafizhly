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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <!-- FontAwesome sudah ditarik otomatis dari nav.php nanti -->
    <style>
        :root {
            --ink: #16241d;
            --primary: #0f5132;
            --primary-deep: #06281f;
            --primary-light: #1f9d68;
            --gold: #c9a227;
            --gold-soft: #e7cd77;
            --parchment: #faf7f0;
            --parchment-deep: #f0ead9;
            --muted: #8a8378;
            --sans: 'Plus Jakarta Sans', sans-serif;
            --serif: 'Fraunces', serif;
            --mono: 'JetBrains Mono', monospace;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: var(--sans);
        }

        body {
            background-color: var(--parchment);
            color: var(--ink);
            padding-bottom: 60px;
            -webkit-font-smoothing: antialiased;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            font-family: inherit;
            border: none;
            background: none;
            cursor: pointer;
        }

        /* ===== HERO ===== */
        .hero-section {
            position: relative;
            overflow: hidden;
            background: radial-gradient(120% 140% at 15% 0%, var(--primary-light) 0%, var(--primary) 45%, var(--primary-deep) 100%);
            color: #fdfdf9;
            padding: 26px 20px 34px;
            text-align: center;
            border-bottom-left-radius: 34px;
            border-bottom-right-radius: 34px;
        }

        .hero-ornament {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0.18;
            pointer-events: none;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            position: relative;
            z-index: 1;
        }

        .location-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(5px);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.82rem;
            color: inherit;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .location-badge i {
            color: var(--gold-soft);
        }

        .profile-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: var(--serif);
            font-weight: 700;
            border: 2px solid var(--gold-soft);
        }

        .bismillah {
            font-family: 'Amiri', serif;
            font-size: 1.8rem;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .date-time-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 22px;
            margin-bottom: 22px;
            position: relative;
            z-index: 1;
        }

        .date-box {
            text-align: center;
            min-width: 74px;
        }

        .date-box .label {
            font-size: 0.63rem;
            color: var(--gold-soft);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 3px;
        }

        .date-box .value {
            font-size: 0.83rem;
            font-weight: 600;
        }

        .realtime-clock {
            font-family: var(--mono);
            font-size: 2.1rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* Jadwal Sholat */
        .prayer-row {
            display: flex;
            justify-content: space-between;
            gap: 4px;
            background: rgba(0, 0, 0, 0.16);
            padding: 14px 10px;
            border-radius: 18px;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .prayer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex: 1;
            opacity: 0.55;
            transition: 0.25s;
        }

        .prayer-item.active {
            opacity: 1;
            transform: scale(1.08);
        }

        .prayer-item.active .p-time,
        .prayer-item.active .p-icon {
            color: var(--gold-soft);
        }

        .p-time {
            font-family: var(--mono);
            font-size: 0.8rem;
            font-weight: 700;
        }

        .p-icon {
            font-size: 1.2rem;
        }

        .p-name {
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .countdown-text {
            font-size: 0.88rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .countdown-text span {
            font-family: var(--mono);
            color: var(--gold-soft);
            font-weight: 700;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            padding: 0 20px;
            max-width: 640px;
            margin: -34px auto 0;
            position: relative;
            z-index: 5;
        }

        .floating-card {
            position: relative;
            background: #fff;
            border-radius: 20px;
            padding: 18px 20px 18px 26px;
            box-shadow: 0 12px 28px rgba(6, 40, 31, 0.09);
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .fc-ribbon {
            position: absolute;
            left: 16px;
            top: -2px;
            background: var(--gold);
            color: var(--primary-deep);
            font-family: var(--mono);
            font-size: 0.6rem;
            font-weight: 700;
            padding: 3px 9px 8px;
            clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 78%, 0 100%);
        }

        .fc-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .progress-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-light) 21%, var(--parchment-deep) 0);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress-inner {
            width: 38px;
            height: 38px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .fc-text h4 {
            font-size: 0.68rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .fc-text h3 {
            font-family: var(--serif);
            font-size: 1.08rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .fc-badges {
            display: flex;
            gap: 5px;
        }

        .fc-badge {
            font-size: 0.66rem;
            background: var(--parchment-deep);
            padding: 2px 9px;
            border-radius: 10px;
            color: var(--muted);
        }

        .fc-arrow {
            width: 30px;
            height: 30px;
            background: var(--parchment-deep);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--muted);
            font-size: 0.8rem;
        }

        /* Quick Access */
        .section-title {
            font-family: var(--serif);
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--ink);
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px 8px;
            margin-bottom: 10px;
        }

        .q-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 9px;
        }

        .q-icon {
            width: 54px;
            height: 54px;
            background: #fff;
            border-radius: 16px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.4rem;
            box-shadow: 0 4px 10px rgba(6, 40, 31, 0.05);
            color: var(--primary);
            border: 1px solid var(--parchment-deep);
            transition: 0.2s;
        }

        .q-item:hover .q-icon {
            transform: translateY(-3px);
            border-color: var(--gold-soft);
            box-shadow: 0 6px 16px rgba(6, 40, 31, 0.08);
        }

        .q-text {
            font-size: 0.74rem;
            font-weight: 600;
            color: var(--ink);
            text-align: center;
        }

        /* ===== DESKTOP LAYOUT (auto-detect via media query) ===== */
        @media (min-width: 900px) {
            body {
                padding-bottom: 40px;
            }

            .hero-section {
                padding: 34px 48px 42px;
                border-bottom-left-radius: 48px;
                border-bottom-right-radius: 48px;
                text-align: left;
            }

            .top-bar {
                max-width: 1080px;
                margin: 0 auto 26px;
            }

            .hero-inner {
                max-width: 1080px;
                margin: 0 auto;
                display: grid;
                grid-template-columns: 1fr 1.3fr;
                gap: 44px;
                align-items: center;
            }

            .bismillah {
                font-size: 2rem;
            }

            .date-time-row {
                justify-content: flex-start;
            }

            .prayer-row {
                margin-bottom: 0;
            }

            .main-content {
                max-width: 1080px;
                margin: -30px auto 0;
                display: grid;
                grid-template-columns: 1.4fr 1fr 1fr;
                gap: 18px;
                align-items: start;
            }

            .floating-card {
                margin-bottom: 0;
                grid-row: 1;
            }

            .stat-card {
                background: #fff;
                border-radius: 18px;
                padding: 18px 20px;
                display: flex;
                align-items: center;
                gap: 14px;
                box-shadow: 0 10px 24px rgba(6, 40, 31, 0.06);
                color: var(--primary);
                grid-row: 1;
            }

            .stat-card h4 {
                font-size: 0.66rem;
                text-transform: uppercase;
                color: var(--muted);
                font-weight: 600;
                margin-bottom: 3px;
            }

            .stat-card h3 {
                font-family: var(--serif);
                font-size: 1rem;
                color: var(--ink);
                font-weight: 600;
            }

            .stat-card i {
                font-size: 1.3rem;
            }

            .menu-section {
                grid-column: 1 / -1;
            }

            .quick-grid {
                grid-template-columns: repeat(8, 1fr);
                gap: 18px 12px;
            }
        }
    </style>
</head>

<body>

    <div class="hero-section">
        <svg class="hero-ornament" viewBox="0 0 400 200" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <g fill="none" stroke="#c9a227" stroke-width="0.6">
                <g transform="translate(40 30)">
                    <polygon points="0,-16 4.7,-4.7 16,0 4.7,4.7 0,16 -4.7,4.7 -16,0 -4.7,-4.7" />
                    <polygon points="0,-11 3.3,-3.3 11,0 3.3,3.3 0,11 -3.3,3.3 -11,0 -3.3,-3.3" transform="rotate(22.5)" />
                </g>
                <g transform="translate(120 150)">
                    <polygon points="0,-16 4.7,-4.7 16,0 4.7,4.7 0,16 -4.7,4.7 -16,0 -4.7,-4.7" />
                    <polygon points="0,-11 3.3,-3.3 11,0 3.3,3.3 0,11 -3.3,3.3 -11,0 -3.3,-3.3" transform="rotate(22.5)" />
                </g>
                <g transform="translate(200 30)">
                    <polygon points="0,-16 4.7,-4.7 16,0 4.7,4.7 0,16 -4.7,4.7 -16,0 -4.7,-4.7" />
                    <polygon points="0,-11 3.3,-3.3 11,0 3.3,3.3 0,11 -3.3,3.3 -11,0 -3.3,-3.3" transform="rotate(22.5)" />
                </g>
                <g transform="translate(280 150)">
                    <polygon points="0,-16 4.7,-4.7 16,0 4.7,4.7 0,16 -4.7,4.7 -16,0 -4.7,-4.7" />
                    <polygon points="0,-11 3.3,-3.3 11,0 3.3,3.3 0,11 -3.3,3.3 -11,0 -3.3,-3.3" transform="rotate(22.5)" />
                </g>
                <g transform="translate(360 30)">
                    <polygon points="0,-16 4.7,-4.7 16,0 4.7,4.7 0,16 -4.7,4.7 -16,0 -4.7,-4.7" />
                    <polygon points="0,-11 3.3,-3.3 11,0 3.3,3.3 0,11 -3.3,3.3 -11,0 -3.3,-3.3" transform="rotate(22.5)" />
                </g>
            </g>
        </svg>

        <div class="top-bar">
            <div class="location-badge" id="location-badge" onclick="Hifzly.location.detect()">
                <i class="fas fa-map-marker-alt"></i>
                <span id="location-text">Mencari Lokasi...</span>
            </div>
            <div class="profile-btn"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?></div>
        </div>

        <div class="hero-inner">
            <div>
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

                <div class="countdown-text" id="countdown-text">--:--:-- menuju waktu sholat berikutnya</div>
            </div>

            <div>
                <div class="prayer-row" id="prayer-container">
                    <div style="font-size: 0.85rem; width: 100%; text-align: center;">Menyelaraskan jadwal sholat...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <a class="floating-card" href="alquran.php">
            <span class="fc-ribbon">Juz 1</span>
            <div class="fc-left">
                <div class="progress-circle">
                    <div class="progress-inner">21%</div>
                </div>
                <div class="fc-text">
                    <h4>Lanjutkan Hafalan</h4>
                    <h3>Surah Al-Baqarah</h3>
                    <div class="fc-badges">
                        <span class="fc-badge">Ayat 60</span>
                    </div>
                </div>
            </div>
            <div class="fc-arrow"><i class="fas fa-chevron-right"></i></div>
        </a>

        <div class="stat-card">
            <i class="fas fa-medal"></i>
            <div>
                <h4>Pencapaian</h4>
                <h3>3 lencana bulan ini</h3>
            </div>
        </div>

        <div class="stat-card">
            <i class="fas fa-chart-line"></i>
            <div>
                <h4>Mutaba'ah</h4>
                <h3>5 hari beruntun</h3>
            </div>
        </div>

        <div class="menu-section">
            <h3 class="section-title">Menu</h3>
            <div class="quick-grid">
                <a href="alquran.php" class="q-item">
                    <div class="q-icon"><i class="fas fa-book-open"></i></div>
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
                <a href="doa.php" class="q-item">
                    <div class="q-icon"><i class="fas fa-hands-praying"></i></div>
                    <div class="q-text">Doa</div>
                </a>
                <a href="target.php" class="q-item">
                    <div class="q-icon"><i class="fas fa-bullseye"></i></div>
                    <div class="q-text">Target</div>
                </a>
                <a href="#" class="q-item">
                    <div class="q-icon"><i class="fas fa-medal"></i></div>
                    <div class="q-text">Pencapaian</div>
                </a>
                <a href="#" class="q-item">
                    <div class="q-icon"><i class="fas fa-robot"></i></div>
                    <div class="q-text">AI Coach</div>
                </a>
                <a href="#" class="q-item">
                    <div class="q-icon"><i class="fas fa-cog"></i></div>
                    <div class="q-text">Pengaturan</div>
                </a>
            </div>
        </div>
    </div>

    <!-- Panggil Navigasi (Otomatis load FontAwesome) -->
    <?php include '../components/nav.php'; ?>

    <script>
        /**
         * Struktur JS dipecah jadi modul-modul kecil (Clock, Location, PrayerTimes)
         * biar rapi dan gampang dikembangkan, walau tetap vanilla JS di dalam PHP.
         */
        const Hifzly = (() => {
            const FALLBACK = {
                lat: -6.1824,
                lon: 106.3351,
                label: "Cikande, Banten"
            };

            const PRAYER_CONFIG = [{
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

            let prayerTimesData = null;

            const pad = (n) => String(n).padStart(2, '0');

            const Clock = {
                start() {
                    this.tick();
                    setInterval(() => this.tick(), 1000);
                },
                tick() {
                    const now = new Date();
                    document.getElementById('clock').innerText =
                        `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
                    if (prayerTimesData) PrayerTimes.updateCountdown(now);
                }
            };

            const PrayerTimes = {
                async fetch(lat, lon) {
                    try {
                        const res = await fetch(`https://api.aladhan.com/v1/timings?latitude=${lat}&longitude=${lon}&method=11`);
                        const result = await res.json();
                        const data = result.data;
                        prayerTimesData = data.timings;

                        document.getElementById('masehi-date').innerText = data.date.gregorian.date;
                        document.getElementById('hijri-date').innerText =
                            `${data.date.hijri.day} ${data.date.hijri.month.en} ${data.date.hijri.year}`;

                        this.render();
                    } catch (e) {
                        document.getElementById('prayer-container').innerHTML =
                            "<div style='color:#ffb4b4;'>Gagal memuat jadwal.</div>";
                    }
                },
                render() {
                    let html = '';
                    PRAYER_CONFIG.forEach(p => {
                        html += `
                        <div class="prayer-item" id="pr-${p.id}">
                            <div class="p-time">${prayerTimesData[p.id]}</div>
                            <div class="p-icon">${p.icon}</div>
                            <div class="p-name">${p.name}</div>
                        </div>`;
                    });
                    document.getElementById('prayer-container').innerHTML = html;
                },
                updateCountdown(now) {
                    let nextPrayerName = "";
                    let nextPrayerTimeDate = null;
                    let activeId = "";

                    for (const p of PRAYER_CONFIG) {
                        const [h, m] = prayerTimesData[p.id].split(':');
                        const pTime = new Date();
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
                    document.getElementById(`pr-${activeId}`)?.classList.add('active');

                    const diffMs = nextPrayerTimeDate - now;
                    const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
                    const diffMins = Math.floor((diffMs % 3600000) / 60000);
                    const diffSecs = Math.floor((diffMs % 60000) / 1000);

                    document.getElementById('countdown-text').innerHTML =
                        `<span>${pad(diffHrs)}:${pad(diffMins)}:${pad(diffSecs)}</span> menuju ${nextPrayerName}`;
                }
            };

            const Location = {
                detect() {
                    document.getElementById('location-text').innerText = "Melacak...";
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (pos) => this.onFound(pos.coords.latitude, pos.coords.longitude),
                            () => this.onFallback()
                        );
                    } else {
                        this.onFallback();
                    }
                },
                onFound(lat, lon) {
                    PrayerTimes.fetch(lat, lon);
                    this.fetchCityName(lat, lon);
                },
                onFallback() {
                    document.getElementById('location-text').innerText = FALLBACK.label;
                    PrayerTimes.fetch(FALLBACK.lat, FALLBACK.lon);
                },
                async fetchCityName(lat, lon) {
                    try {
                        const res = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=id`);
                        const data = await res.json();
                        document.getElementById('location-text').innerText = data.city || data.locality || "Lokasi Anda";
                    } catch (e) {
                        document.getElementById('location-text').innerText = "Lokasi Ditemukan";
                    }
                }
            };

            return {
                Clock,
                PrayerTimes,
                location: Location
            };
        })();

        window.onload = () => {
            Hifzly.Clock.start();
            Hifzly.location.detect();
        };
    </script>
</body>

</html>