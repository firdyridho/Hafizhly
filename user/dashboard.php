<?php
session_start();

// Keamanan: Cek apakah user sudah login dan rolenya 'user'
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
            --primary: #059669;
            /* Hijau Emerald */
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

        /* padding-bottom untuk memberi ruang pada menu bawah */
        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 80px;
        }

        /* Header Melengkung */
        .header {
            background: var(--primary);
            color: white;
            padding: 30px 20px 20px 20px;
            text-align: center;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h2 {
            font-size: 1.2rem;
            font-weight: 500;
        }

        .header p {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 5px;
        }

        /* Container Konten */
        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 1.1rem;
            margin-bottom: 15px;
            color: var(--dark);
            font-weight: 600;
        }

        /* Card Daftar Surat */
        .surah-card {
            background: var(--card-bg);
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .surah-card:active {
            transform: scale(0.98);
        }

        .surah-info h3 {
            font-size: 1.1rem;
            color: var(--primary);
        }

        .surah-info p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .surah-arab {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        /* Menu Navigasi Bawah (Bottom Nav) */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--card-bg);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 1000;
            max-width: 600px;
            margin: 0 auto;
        }

        .nav-item {
            text-align: center;
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .nav-item.active {
            color: var(--primary);
        }

        .nav-icon {
            font-size: 1.4rem;
        }

        #loading {
            text-align: center;
            color: var(--text-muted);
            margin: 20px 0;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Assalamu'alaikum,</h2>
        <p><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></p>
    </div>

    <div class="container">
        <h3 class="section-title">E-Qur'an (Murojaah)</h3>
        <div id="loading">Memuat daftar surat dari API...</div>
        <div id="surah-list"></div>
    </div>

    <div class="bottom-nav">
        <a href="dashboard.php" class="nav-item active">
            <span class="nav-icon">📖</span>
            <span>Qur'an</span>
        </a>
        <a href="mutabaah.php" class="nav-item">
            <span class="nav-icon">📊</span>
            <span>Mutabaah</span>
        </a>
        <a href="#" class="nav-item">
            <span class="nav-icon">🏆</span>
            <span>Target</span>
        </a>
        <a href="../logout.php" class="nav-item" onclick="return confirm('Yakin ingin keluar?')">
            <span class="nav-icon">🚪</span>
            <span>Keluar</span>
        </a>
    </div>

    <script>
        async function fetchSurah() {
            try {
                const response = await fetch('https://equran.id/api/v2/surat');
                const data = await response.json();

                document.getElementById('loading').style.display = 'none';
                const container = document.getElementById('surah-list');

                data.data.forEach(surah => {
                    const card = document.createElement('div');
                    card.className = 'surah-card';
                    card.innerHTML = `
                        <div class="surah-info">
                            <h3>${surah.nomor}. ${surah.namaLatin}</h3>
                            <p>${surah.arti} • ${surah.jumlahAyat} Ayat</p>
                        </div>
                        <div class="surah-arab">
                            ${surah.nama}
                        </div>
                    `;

                    // Aksi ketika surat di klik (untuk tahap selanjutnya)
                    card.onclick = () => {
                        window.location.href = 'baca.php?nomor=' + surah.nomor;
                    };

                    container.appendChild(card);
                });
            } catch (error) {
                console.error("Error:", error);
                document.getElementById('loading').innerText = "Gagal memuat data. Periksa koneksi internet.";
            }
        }

        // Panggil fungsi saat halaman terbuka
        fetchSurah();
    </script>

</body>

</html>