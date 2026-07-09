<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Ambil nomor surat dari URL. Contoh: baca.php?nomor=1
$nomor_surat = isset($_GET['nomor']) ? intval($_GET['nomor']) : 0;

// Jika tidak ada nomor surat di URL, kembalikan ke dashboard
if ($nomor_surat == 0) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Al-Qur'an - Hifzly</title>

    <style>
        :root {
            --primary: #059669;
            --dark: #1f2937;
            --bg: #f9fafb;
            --card-bg: #ffffff;
            --text-muted: #6b7280;
            --border: #e5e7eb;
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
            padding-bottom: 40px;
        }

        /* Navbar Atas */
        .navbar {
            background-color: var(--primary);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .back-btn {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            margin-right: 15px;
            font-weight: bold;
        }

        .navbar-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Info Surat */
        .surah-header {
            text-align: center;
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border-bottom: 4px solid var(--primary);
        }

        .surah-header h1 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .surah-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Card Ayat */
        .ayat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        .ayat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
        }

        .nomor-ayat {
            background-color: var(--primary);
            color: white;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .teks-arab {
            font-size: 2rem;
            text-align: right;
            line-height: 2.5;
            color: var(--dark);
            margin-bottom: 15px;
            /* Font khusus teks Arab agar lebih rapi */
            font-family: 'Amiri', 'Traditional Arabic', serif;
        }

        .teks-latin {
            font-size: 1rem;
            color: var(--primary);
            margin-bottom: 5px;
            font-style: italic;
        }

        .teks-indo {
            font-size: 0.95rem;
            color: #4b5563;
            line-height: 1.6;
        }

        #loading {
            text-align: center;
            color: var(--text-muted);
            margin: 30px 0;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <a href="dashboard.php" class="back-btn">←</a>
        <div class="navbar-title" id="nav-title">Memuat Surat...</div>
    </div>

    <div class="container">
        <!-- Tempat info surat akan dimunculkan -->
        <div class="surah-header" id="surah-header" style="display: none;">
            <h1 id="nama-surat"></h1>
            <p id="info-surat"></p>
        </div>

        <div id="loading">Mengambil ayat suci...</div>

        <!-- Tempat list ayat akan dimunculkan -->
        <div id="ayat-list"></div>
    </div>

    <script>
        // Mengambil variabel PHP nomor_surat ke dalam JavaScript
        const nomorSurat = <?= $nomor_surat; ?>;
        const apiUrl = `https://equran.id/api/v2/surat/${nomorSurat}`;

        async function fetchAyat() {
            try {
                const response = await fetch(apiUrl);
                const result = await response.json();
                const data = result.data; // Data surat spesifik

                // Sembunyikan tulisan loading
                document.getElementById('loading').style.display = 'none';

                // Isi Navbar & Header Surat
                document.getElementById('nav-title').innerText = data.namaLatin;
                document.getElementById('surah-header').style.display = 'block';
                document.getElementById('nama-surat').innerText = data.namaLatin;
                document.getElementById('info-surat').innerText = `${data.arti} • ${data.jumlahAyat} Ayat • ${data.tempatTurun}`;

                // Looping untuk memunculkan semua ayat
                const ayatListContainer = document.getElementById('ayat-list');

                data.ayat.forEach(ayat => {
                    const card = document.createElement('div');
                    card.className = 'ayat-card';
                    card.innerHTML = `
                        <div class="ayat-header">
                            <div class="nomor-ayat">${ayat.nomorAyat}</div>
                        </div>
                        <div class="teks-arab">${ayat.teksArab}</div>
                        <div class="teks-latin">${ayat.teksLatin}</div>
                        <div class="teks-indo">${ayat.teksIndonesia}</div>
                    `;
                    ayatListContainer.appendChild(card);
                });

            } catch (error) {
                console.error("Gagal mengambil data:", error);
                document.getElementById('loading').innerText = "Gagal memuat ayat. Periksa koneksi internet.";
            }
        }

        // Jalankan fungsi saat halaman dibuka
        fetchAyat();
    </script>

</body>

</html>