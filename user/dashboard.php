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
        :root { --primary: #059669; --dark: #1f2937; --bg: #f3f4f6; --card-bg: #ffffff; --text-muted: #6b7280; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: var(--bg); color: var(--dark); padding-bottom: 90px; }
        
        /* Header Profil */
        .header-profile {
            background: linear-gradient(135deg, var(--primary), #10b981);
            color: white;
            padding: 30px 20px 40px 20px;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .greeting h2 { font-size: 1rem; font-weight: normal; opacity: 0.9; }
        .greeting h1 { font-size: 1.5rem; font-weight: bold; margin-top: 5px; }
        
        /* Avatar Placeholder dengan logo AI */
        .avatar {
            width: 50px; height: 50px;
            background: white; border-radius: 50%;
            display: flex; justify-content: center; align-items: center;
            font-size: 1.5rem; color: var(--primary); font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .container { padding: 0 20px; max-width: 600px; margin: -25px auto 0; position: relative; z-index: 2; }
        
        /* Widget Jadwal Sholat */
        .prayer-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        .prayer-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
        .prayer-title { font-weight: bold; color: var(--dark); font-size: 1rem; }
        .prayer-location { font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
        
        .prayer-times { display: flex; justify-content: space-between; text-align: center; }
        .prayer-item { display: flex; flex-direction: column; gap: 5px; }
        .prayer-name { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }
        .prayer-time { font-size: 0.95rem; font-weight: bold; color: var(--primary); }

        /* Menu Grid */
        .section-title { font-size: 1.1rem; font-weight: bold; margin-bottom: 15px; color: var(--dark); }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .menu-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px 15px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            display: flex; flex-direction: column; align-items: center; gap: 10px;
        }
        .menu-card:active { transform: scale(0.95); }
        .menu-icon {
            width: 50px; height: 50px;
            background: #ecfdf5; border-radius: 14px;
            display: flex; justify-content: center; align-items: center;
            font-size: 1.8rem; color: var(--primary);
        }
        .menu-text { font-size: 0.95rem; font-weight: 600; color: var(--dark); }
        
        /* Khusus Smart Murojaah kasih aksen beda */
        .menu-card.highlight { border: 1px solid var(--primary); background: #f0fdf4; }
        .menu-card.highlight .menu-icon { background: var(--primary); color: white; }
    </style>
</head>
<body>

    <div class="header-profile">
        <div class="greeting">
            <h2>Assalamu'alaikum,</h2>
            <h1><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></h1>
        </div>
        <div class="avatar">
            <?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?>
        </div>
    </div>

    <div class="container">
        <div class="prayer-card">
            <div class="prayer-header">
                <div class="prayer-title">Jadwal Sholat</div>
                <div class="prayer-location">📍 <span id="loc-text">Memuat...</span></div>
            </div>
            <div class="prayer-times" id="prayer-container">
                <div style="text-align:center; width:100%; font-size:0.85rem; color:#6b7280;">Mengambil jadwal hari ini...</div>
            </div>
        </div>

        <div class="section-title">Eksplorasi</div>
        <div class="menu-grid">
            <a href="alquran.php" class="menu-card">
                <div class="menu-icon">📖</div>
                <div class="menu-text">Al-Qur'an</div>
            </a>
            <a href="#" class="menu-card highlight">
                <div class="menu-icon">🎙️</div>
                <div class="menu-text">Smart Murojaah</div>
            </a>
            <a href="mutabaah.php" class="menu-card">
                <div class="menu-icon">📊</div>
                <div class="menu-text">Mutabaah</div>
            </a>
            <a href="#" class="menu-card">
                <div class="menu-icon">🤲</div>
                <div class="menu-text">Doa Harian</div>
            </a>
        </div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script>
        // Mengambil Jadwal Sholat dari API Aladhan (Default: Jakarta)
        async function fetchPrayerTimes() {
            try {
                // Kamu bisa ganti parameter city ini nanti dengan Geolocation API jika ingin lebih canggih
                const response = await fetch('https://api.aladhan.com/v1/timingsByCity?city=Jakarta&country=Indonesia&method=11');
                const result = await response.json();
                const timings = result.data.timings;
                
                document.getElementById('loc-text').innerText = "Jakarta, ID";
                
                // Menampilkan 5 waktu sholat wajib
                const prayerHTML = `
                    <div class="prayer-item"><span class="prayer-name">Subuh</span><span class="prayer-time">${timings.Fajr}</span></div>
                    <div class="prayer-item"><span class="prayer-name">Dzuhur</span><span class="prayer-time">${timings.Dhuhr}</span></div>
                    <div class="prayer-item"><span class="prayer-name">Ashar</span><span class="prayer-time">${timings.Asr}</span></div>
                    <div class="prayer-item"><span class="prayer-name">Maghrib</span><span class="prayer-time">${timings.Maghrib}</span></div>
                    <div class="prayer-item"><span class="prayer-name">Isya</span><span class="prayer-time">${timings.Isha}</span></div>
                `;
                document.getElementById('prayer-container').innerHTML = prayerHTML;
            } catch (error) {
                document.getElementById('prayer-container').innerHTML = "<div style='font-size:0.8rem; color:red;'>Gagal memuat jadwal sholat.</div>";
            }
        }

        fetchPrayerTimes();
    </script>
</body>
</html>