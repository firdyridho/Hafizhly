<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mengambil data Terakhir Baca (Bookmark terakhir) dari database
$q_last = mysqli_query($conn, "SELECT * FROM bookmark WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1");
$last_read = mysqli_fetch_assoc($q_last);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al-Qur'an - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8b5cf6;
            /* Mengikuti referensi desain warna ungu modern */
            --primary-light: #ede9fe;
            --dark: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 90px;
        }

        .container {
            padding: 20px;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Header & Last Read Card */
        .page-header {
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
        }

        .page-subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .last-read-card {
            background: linear-gradient(135deg, var(--primary), #6d28d9);
            border-radius: 20px;
            padding: 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .lrc-content {
            position: relative;
            z-index: 2;
        }

        .lrc-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            opacity: 0.8;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .lrc-surah {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .lrc-ayat {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .lrc-icon {
            font-size: 4rem;
            opacity: 0.2;
            position: absolute;
            right: -10px;
            bottom: -10px;
            z-index: 1;
        }

        /* Search Bar */
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border-radius: 16px;
            border: 1px solid var(--border);
            font-size: 0.95rem;
            outline: none;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
        }

        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .search-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border);
            padding-bottom: 10px;
            overflow-x: auto;
        }

        .tab-btn {
            background: none;
            border: none;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            padding: 5px 10px;
            position: relative;
            white-space: nowrap;
        }

        .tab-btn.active {
            color: var(--primary);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }

        /* List Surah */
        .surah-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .surah-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border);
            transition: 0.2s;
        }

        .surah-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-color: var(--primary-light);
        }

        .s-number {
            width: 45px;
            height: 45px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
            position: relative;
        }

        /* Motif bintang oktagon kecil */
        .s-number::before {
            content: '۞';
            position: absolute;
            font-size: 2.5rem;
            opacity: 0.1;
        }

        .s-details {
            flex-grow: 1;
        }

        .s-name-id {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--dark);
            margin-bottom: 3px;
        }

        .s-info {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .s-name-ar {
            font-family: 'Amiri', serif;
            font-size: 1.5rem;
            color: var(--primary);
            font-weight: bold;
        }

        #loading {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Al-Qur'an</h1>
            <p class="page-subtitle">Lanjutkan perjalanan spiritualmu</p>
        </div>

        <?php if ($last_read): ?>
            <div class="last-read-card" onclick="window.location.href='baca.php?nomor=<?= $last_read['surah_nomor'] ?>#ayat-<?= $last_read['ayat'] ?>'">
                <div class="lrc-content">
                    <div class="lrc-label"><i class="fas fa-book-open"></i> Terakhir Baca</div>
                    <div class="lrc-surah">Surah ke-<?= $last_read['surah_nomor'] ?></div>
                    <div class="lrc-ayat">Ayat No: <?= $last_read['ayat'] ?></div>
                </div>
                <i class="fas fa-quran lrc-icon"></i>
            </div>
        <?php else: ?>
            <div class="last-read-card" onclick="window.location.href='baca.php?nomor=1'">
                <div class="lrc-content">
                    <div class="lrc-label"><i class="fas fa-book-open"></i> Mulai Membaca</div>
                    <div class="lrc-surah">Al-Fatihah</div>
                    <div class="lrc-ayat">Ayat No: 1</div>
                </div>
                <i class="fas fa-quran lrc-icon"></i>
            </div>
        <?php endif; ?>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama surah (contoh: Mulk)..." autocomplete="off">
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="filterData('all', this)">Semua</button>
            <button class="tab-btn" onclick="filterData('Makkiyah', this)">Makiyyah</button>
            <button class="tab-btn" onclick="filterData('Madaniyah', this)">Madaniyyah</button>
        </div>

        <div id="loading"><i class="fas fa-spinner fa-spin"></i> Memuat Al-Qur'an...</div>
        <div class="surah-list" id="surahList"></div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script>
        let allSurah = [];

        async function fetchSurah() {
            try {
                const response = await fetch('https://equran.id/api/v2/surat');
                const data = await response.json();
                allSurah = data.data;
                document.getElementById('loading').style.display = 'none';
                renderList(allSurah);
            } catch (error) {
                document.getElementById('loading').innerHTML = "Gagal memuat data. Periksa koneksi internet.";
            }
        }

        function renderList(data) {
            const container = document.getElementById('surahList');
            container.innerHTML = '';

            if (data.length === 0) {
                container.innerHTML = '<div style="text-align:center; padding:20px; color:#64748b;">Surah tidak ditemukan.</div>';
                return;
            }

            data.forEach(surah => {
                const card = document.createElement('div');
                card.className = 'surah-card';
                card.onclick = () => {
                    window.location.href = 'baca.php?nomor=' + surah.nomor;
                };
                card.innerHTML = `
                    <div class="s-number">${surah.nomor}</div>
                    <div class="s-details">
                        <div class="s-name-id">${surah.namaLatin}</div>
                        <div class="s-info">${surah.tempatTurun} • ${surah.jumlahAyat} AYAT</div>
                    </div>
                    <div class="s-name-ar">${surah.nama}</div>
                `;
                container.appendChild(card);
            });
        }

        // Live Search
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const activeTab = document.querySelector('.tab-btn.active').innerText;

            let filtered = allSurah.filter(s => s.namaLatin.toLowerCase().includes(query));

            if (activeTab === 'Makiyyah') {
                filtered = filtered.filter(s => s.tempatTurun === 'Makkiyah');
            } else if (activeTab === 'Madaniyyah') {
                filtered = filtered.filter(s => s.tempatTurun === 'Madaniyah');
            }

            renderList(filtered);
        });

        // Filter Tabs
        function filterData(type, btn) {
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');

            const query = document.getElementById('searchInput').value.toLowerCase();
            let filtered = allSurah.filter(s => s.namaLatin.toLowerCase().includes(query));

            if (type !== 'all') {
                filtered = filtered.filter(s => s.tempatTurun === type);
            }
            renderList(filtered);
        }

        fetchSurah();
    </script>
</body>

</html>