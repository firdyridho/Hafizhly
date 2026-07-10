<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$juz_nomor = isset($_GET['juz']) ? (int)$_GET['juz'] : 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Juz <?= $juz_nomor ?> - Hifzly</title>
    <!-- Font Arab Scheherazade New untuk Waqaf Sempurna -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-light: #d1fae5;
            --dark: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --quran-text: #111827;
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
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        .read-header {
            background: var(--card-bg);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .h-btn {
            color: var(--text-muted);
            font-size: 1.3rem;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }

        .h-btn:hover,
        .h-btn.active {
            color: var(--primary);
        }

        .surah-name-mini {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.1rem;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .surah-divider {
            text-align: center;
            margin: 30px 0 15px;
            font-weight: 700;
            color: white;
            background: var(--primary);
            padding: 10px 20px;
            border-radius: 30px;
            display: inline-block;
            left: 50%;
            position: relative;
            transform: translateX(-50%);
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
        }

        .ayat-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .ayat-card {
            background: var(--card-bg);
            padding: 25px 20px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--border);
            transition: 0.3s;
        }

        .ayat-card.playing {
            border-color: var(--primary);
            background: #f0fdf4;
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.1);
        }

        .ayat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: var(--bg);
            padding: 8px 12px;
            border-radius: 12px;
        }

        .ayat-number-badge {
            width: 30px;
            height: 30px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .ayat-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .ayat-action-btn {
            color: var(--text-muted);
            font-size: 1.15rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .ayat-action-btn:hover {
            color: var(--primary);
            transform: scale(1.1);
        }

        .ayat-action-btn.playing {
            color: #f59e0b;
        }

        .teks-arab {
            font-family: 'Scheherazade New', serif;
            font-size: 2.3rem;
            text-align: right;
            line-height: 2.2;
            color: var(--quran-text);
            margin-bottom: 20px;
            direction: rtl;
        }

        .teks-container {
            transition: 0.3s;
        }

        .body-no-terjemah .teks-container {
            display: none;
        }

        .teks-latin {
            font-size: 1rem;
            color: var(--primary);
            margin-bottom: 8px;
            font-weight: 500;
            line-height: 1.5;
        }

        .teks-indo {
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.6;
        }

        #loading {
            text-align: center;
            margin-top: 50px;
            font-size: 1.1rem;
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>

<body id="baca-body">

    <audio id="audioAyat"></audio>

    <div class="read-header">
        <div class="header-left">
            <a href="alquran.php" class="h-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="surah-name-mini">Juz <?= $juz_nomor ?></div>
        </div>
        <div class="header-right">
            <div class="h-btn active" id="btn-terjemah" onclick="toggleTerjemah()" title="Terjemahan"><i class="fas fa-language"></i></div>
        </div>
    </div>

    <div class="container">
        <div id="loading"><i class="fas fa-spinner fa-spin"></i> Menyiapkan Mushaf Juz <?= $juz_nomor ?>...</div>
        <div class="ayat-list" id="ayatList"></div>
    </div>

    <script>
        const noJuz = <?= $juz_nomor ?>;
        let audioAyatEl = document.getElementById('audioAyat');
        let listSemuaSurah = [];

        async function fetchJuzData() {
            try {
                // Ambil daftar surah untuk mencocokkan nama
                const resSurah = await fetch('https://equran.id/api/v2/surat');
                const jsonSurah = await resSurah.json();
                listSemuaSurah = jsonSurah.data;

                // API Khusus Juz Gading Dev
                const res = await fetch(`https://api.quran.gading.dev/juz/${noJuz}`);
                const json = await res.json();

                document.getElementById('loading').style.display = 'none';
                renderAyat(json.data.verses);
            } catch (e) {
                document.getElementById('loading').innerHTML = "Gagal memuat ayat. Periksa koneksi internet.";
            }
        }

        function renderAyat(ayatList) {
            const container = document.getElementById('ayatList');
            let html = '';
            let currentSurahNo = 0;

            ayatList.forEach(a => {
                // Pembatas jika surah berganti di tengah Juz (misal Juz 30)
                if (a.meta.surah !== currentSurahNo) {
                    const surahObj = listSemuaSurah.find(s => s.nomor === a.meta.surah);
                    const namaSurah = surahObj ? surahObj.namaLatin : `Surah ${a.meta.surah}`;
                    html += `<div class="surah-divider">Surah ${namaSurah}</div>`;
                    currentSurahNo = a.meta.surah;
                }

                html += `
                <div class="ayat-card" id="ayat-${a.number.inQuran}">
                    <div class="ayat-header">
                        <div class="ayat-number-badge">${a.number.inSurah}</div>
                        <div class="ayat-actions">
                            <i class="fas fa-play ayat-action-btn" id="btn-play-ayat-${a.number.inQuran}" onclick="playAyat('${a.audio.primary}', ${a.number.inQuran})" title="Putar Audio"></i>
                        </div>
                    </div>
                    <div class="teks-arab">${a.text.arab}</div>
                    <div class="teks-container">
                        <div class="teks-latin">${a.text.transliteration.en}</div>
                        <div class="teks-indo">${a.translation.id}</div>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        }

        let isTerjemahTampil = true;

        function toggleTerjemah() {
            isTerjemahTampil = !isTerjemahTampil;
            const bodyEl = document.getElementById('baca-body');
            const btn = document.getElementById('btn-terjemah');
            if (isTerjemahTampil) {
                bodyEl.classList.remove('body-no-terjemah');
                btn.classList.add('active');
            } else {
                bodyEl.classList.add('body-no-terjemah');
                btn.classList.remove('active');
            }
        }

        let currentAyatCard = null;
        let currentAyatNo = null;

        function playAyat(url, nomor) {
            // Jika memutar ayat yang sedang dimainkan
            if (currentAyatNo === nomor && !audioAyatEl.paused) {
                audioAyatEl.pause();
                resetAyatIcons();
                return;
            }

            resetAyatIcons();

            audioAyatEl.src = url;
            audioAyatEl.play();

            currentAyatNo = nomor;
            currentAyatCard = document.getElementById(`ayat-${nomor}`);
            currentAyatCard.classList.add('playing');
            document.getElementById(`btn-play-ayat-${nomor}`).className = "fas fa-pause ayat-action-btn playing";
        }

        function resetAyatIcons() {
            if (currentAyatCard) {
                currentAyatCard.classList.remove('playing');
                if (currentAyatNo) {
                    const btn = document.getElementById(`btn-play-ayat-${currentAyatNo}`);
                    if (btn) btn.className = "fas fa-play ayat-action-btn";
                }
                currentAyatNo = null;
                currentAyatCard = null;
            }
        }

        audioAyatEl.onended = () => {
            resetAyatIcons();
        };

        fetchJuzData();
    </script>
</body>

</html>