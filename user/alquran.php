<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Mengambil data Terakhir Baca
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
            --primary: #059669;
            /* Hijau Utama */
            --primary-light: #d1fae5;
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

        .page-header {
            margin-bottom: 20px;
            text-align: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* Last Read Card */
        .last-read-card {
            background: linear-gradient(135deg, var(--primary), #10b981);
            border-radius: 20px;
            padding: 20px 25px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.2);
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
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
        }

        .lrc-surah {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .lrc-ayat {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .lrc-icon {
            font-size: 4.5rem;
            opacity: 0.15;
            position: absolute;
            right: -5px;
            bottom: -15px;
            z-index: 1;
        }

        /* Main Tabs (Surah / Juz) */
        .main-tabs {
            display: flex;
            background: var(--border);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 20px;
        }

        .m-tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 10px;
            transition: 0.3s;
        }

        .m-tab.active {
            background: var(--card-bg);
            color: var(--primary);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Search Bar */
        .search-box {
            position: relative;
            margin-bottom: 15px;
        }

        .search-box input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border-radius: 14px;
            border: 1px solid var(--border);
            font-size: 0.95rem;
            outline: none;
            transition: 0.3s;
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

        /* Sub Tabs Filter (Semua, Makiyyah, Madaniyyah) */
        .sub-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .s-tab {
            background: var(--card-bg);
            border: 1px solid var(--border);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px 16px;
            border-radius: 20px;
            white-space: nowrap;
            transition: 0.2s;
        }

        .s-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Lists */
        .surah-list,
        .juz-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .juz-list {
            display: none;
            /* Sembunyikan default */
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .list-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            border: 1px solid var(--border);
            transition: 0.2s;
        }

        .list-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.1);
        }

        .s-number {
            width: 42px;
            height: 42px;
            background: var(--bg);
            color: var(--dark);
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .list-card:hover .s-number {
            background: var(--primary-light);
            color: var(--primary);
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
            font-size: 1.4rem;
            color: var(--primary);
            font-weight: bold;
        }

        /* Style for Juz Card */
        .juz-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .juz-card:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
        }

        #loading {
            text-align: center;
            padding: 40px;
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Al-Qur'an</h1>
        </div>

        <?php if ($last_read): ?>
            <div class="last-read-card" onclick="window.location.href='baca.php?nomor=<?= $last_read['surah_nomor'] ?>#ayat-<?= $last_read['ayat'] ?>'">
                <div class="lrc-content">
                    <div class="lrc-label"><i class="fas fa-bookmark"></i> Terakhir Baca</div>
                    <div class="lrc-surah">Surah ke-<?= $last_read['surah_nomor'] ?></div>
                    <div class="lrc-ayat">Berhenti di Ayat <?= $last_read['ayat'] ?></div>
                </div>
                <i class="fas fa-book-open lrc-icon"></i>
            </div>
        <?php else: ?>
            <div class="last-read-card" onclick="window.location.href='baca.php?nomor=1'">
                <div class="lrc-content">
                    <div class="lrc-label"><i class="fas fa-play-circle"></i> Mulai Membaca</div>
                    <div class="lrc-surah">Al-Fatihah</div>
                    <div class="lrc-ayat">Ayat ke-1</div>
                </div>
                <i class="fas fa-book-open lrc-icon"></i>
            </div>
        <?php endif; ?>

        <div class="main-tabs">
            <div class="m-tab active" onclick="switchMainTab('surah')">Surah</div>
            <div class="m-tab" onclick="switchMainTab('juz')">Juz</div>
        </div>

        <div id="surah-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama surah..." autocomplete="off">
            </div>

            <div class="sub-tabs">
                <button class="s-tab active" onclick="filterSurah('all', this)">Semua</button>
                <button class="s-tab" onclick="filterSurah('Mekah', this)">Makiyyah</button>
                <button class="s-tab" onclick="filterSurah('Madinah', this)">Madaniyyah</button>
            </div>

            <div id="loading"><i class="fas fa-spinner fa-spin"></i> Memuat Data...</div>
            <div class="surah-list" id="surahList"></div>
        </div>

        <div class="juz-list" id="juz-section">
            <?php for ($i = 1; $i <= 30; $i++): ?>
                <div class="juz-card" onclick="window.location.href='baca-juz.php?juz=<?= $i ?>'">
                    Juz <?= $i ?>
                </div>
            <?php endfor; ?>
        </div>
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
                document.getElementById('loading').innerHTML = "Gagal memuat data.";
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
                card.className = 'list-card';
                card.onclick = () => {
                    window.location.href = 'baca.php?nomor=' + surah.nomor;
                };
                card.innerHTML = `
                    <div class="s-number">${surah.nomor}</div>
                    <div class="s-details">
                        <div class="s-name-id">${surah.namaLatin}</div>
                        <div class="s-info">${surah.tempatTurun === 'Mekah' ? 'Makiyyah' : 'Madaniyyah'} • ${surah.jumlahAyat} AYAT</div>
                    </div>
                    <div class="s-name-ar">${surah.nama}</div>
                `;
                container.appendChild(card);
            });
        }

        // Live Search
        document.getElementById('searchInput').addEventListener('input', function(e) {
            applyFilters();
        });

        // Filter Tabs
        let currentFilter = 'all';

        function filterSurah(type, btn) {
            document.querySelectorAll('.s-tab').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = type;
            applyFilters();
        }

        function applyFilters() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            let filtered = allSurah.filter(s => s.namaLatin.toLowerCase().includes(query));

            if (currentFilter !== 'all') {
                filtered = filtered.filter(s => s.tempatTurun === currentFilter);
            }
            renderList(filtered);
        }

        // Switch Surah / Juz
        function switchMainTab(tab) {
            document.querySelectorAll('.m-tab').forEach(el => el.classList.remove('active'));
            if (tab === 'surah') {
                document.querySelector('.m-tab:nth-child(1)').classList.add('active');
                document.getElementById('surah-section').style.display = 'block';
                document.getElementById('juz-section').style.display = 'none';
            } else {
                document.querySelector('.m-tab:nth-child(2)').classList.add('active');
                document.getElementById('surah-section').style.display = 'none';
                document.getElementById('juz-section').style.display = 'grid';
            }
        }

        fetchSurah();
    </script>
</body>

</html>