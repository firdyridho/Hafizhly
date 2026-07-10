<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

// --- AJAX HANDLER UNTUK BOOKMARK (Berjalan di background) ---
if (isset($_POST['action']) && $_POST['action'] == 'bookmark') {
    $surah = (int)$_POST['surah'];
    $ayat = (int)$_POST['ayat'];

    $cek = mysqli_query($conn, "SELECT id FROM bookmark WHERE user_id='$user_id' AND surah_nomor='$surah' AND ayat='$ayat'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO bookmark (user_id, surah_nomor, ayat, catatan) VALUES ('$user_id', '$surah', '$ayat', 'Disimpan otomatis')");
        echo "saved";
    } else {
        echo "exist";
    }
    exit();
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

        /* CUSTOM HEADER BACA (Seragam dengan baca.php) */
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

        /* SURAH INFO CARD (MUNCUL SETIAP BERGANTI SURAH) */
        .surah-info-card {
            background: linear-gradient(135deg, var(--primary), #10b981);
            border-radius: 20px;
            padding: 30px 20px;
            color: white;
            text-align: center;
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.2);
            margin-top: 40px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .surah-info-card::before {
            content: '۞';
            position: absolute;
            font-size: 10rem;
            opacity: 0.1;
            right: -20px;
            bottom: -40px;
            color: white;
        }

        .sic-ar {
            font-family: 'Scheherazade New', serif;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .sic-la {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .sic-details {
            font-size: 0.85rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-flex;
            gap: 10px;
            align-items: center;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
        }

        .bismillah-img {
            text-align: center;
            font-family: 'Scheherazade New', serif;
            font-size: 2.2rem;
            color: var(--dark);
            margin: 10px 0 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
            line-height: 1.5;
        }

        /* AYAT CARD */
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

        .tafsir-box {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: var(--bg);
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--dark);
            line-height: 1.6;
            text-align: justify;
        }

        .tafsir-box.show {
            display: block;
        }

        .t-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        /* ALERT ISLAMI (Kapsul) */
        .islamic-alert {
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: white;
            padding: 14px 24px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            transition: 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            width: max-content;
        }

        .islamic-alert.show {
            top: 30px;
        }

        .ia-icon {
            font-size: 1.2rem;
            color: #fbbf24;
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

    <!-- Header Ikonik (Tanpa Asbabun Nuzul karena bacaan per Juz, tapi fitur lainnya sama) -->
    <div class="read-header">
        <div class="header-left">
            <a href="alquran.php" class="h-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="surah-name-mini">Juz <?= $juz_nomor ?></div>
        </div>
        <div class="header-right">
            <div class="h-btn active" id="btn-terjemah" onclick="toggleTerjemah()" title="Tampilkan/Sembunyikan Terjemahan"><i class="fas fa-language"></i></div>
        </div>
    </div>

    <div class="container">
        <div id="loading"><i class="fas fa-spinner fa-spin"></i> Menyiapkan Mushaf Juz <?= $juz_nomor ?>...</div>
        <div class="ayat-list" id="ayatList"></div>
    </div>

    <!-- Alert Custom -->
    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-check-circle ia-icon"></i>
        <div style="font-size:0.95rem; font-weight:600;" id="alertMsg">Berhasil!</div>
    </div>

    <script>
        const noJuz = <?= $juz_nomor ?>;
        let audioAyatEl = document.getElementById('audioAyat');
        let listSemuaSurah = [];
        let tafsirCache = {}; // Untuk menyimpan tafsir yang sudah diload agar hemat kuota

        async function fetchJuzData() {
            try {
                // Kombinasi 2 API untuk mendapatkan Ayat & Detail Surah
                const [resJuz, resSurah] = await Promise.all([
                    fetch(`https://api.quran.gading.dev/juz/${noJuz}`),
                    fetch(`https://equran.id/api/v2/surat`)
                ]);

                const jsonJuz = await resJuz.json();
                const jsonSurah = await resSurah.json();

                listSemuaSurah = jsonSurah.data;
                document.getElementById('loading').style.display = 'none';

                // Juz API gading mengembalikan `juzStartSurahNumber`
                renderAyat(jsonJuz.data.verses, jsonJuz.data.juzStartSurahNumber);
            } catch (e) {
                document.getElementById('loading').innerHTML = "Gagal memuat ayat. Periksa koneksi internet.";
            }
        }

        function renderAyat(ayatList, startSurahNo) {
            const container = document.getElementById('ayatList');
            let html = '';

            let currentSurahNo = startSurahNo;
            let isFirstVerseInLoop = true;

            ayatList.forEach(a => {
                // Deteksi jika ayat ini adalah awal surah baru (Ayat 1)
                // dan BUKAN iterasi pertama (karena iterasi pertama sudah ditangani oleh startSurahNo)
                if (a.number.inSurah === 1 && !isFirstVerseInLoop) {
                    currentSurahNo++;
                }

                // Cari data Surah dari listSemuaSurah API EQuran
                const surahData = listSemuaSurah.find(s => s.nomor === currentSurahNo);

                // TAMPILKAN CARD INFO SURAH JIKA: 
                // 1. Ini adalah ayat pertama yang dirender (awal halaman juz), ATAU
                // 2. Ini adalah ayat ke-1 dari sebuah Surah baru di tengah juz
                if (isFirstVerseInLoop || a.number.inSurah === 1) {
                    let tmpt = surahData.tempatTurun === 'Mekah' ? 'Makiyyah' : 'Madaniyyah';

                    html += `
                    <div class="surah-info-card">
                        <div class="sic-ar">${surahData.nama}</div>
                        <div class="sic-la">${surahData.namaLatin}</div>
                        <div class="sic-details"><span>${surahData.arti}</span> • <span>${tmpt}</span> • <span>${surahData.jumlahAyat} Ayat</span></div>
                    </div>`;

                    // Tampilkan Bismillah jika bukan Al-Fatihah(1) dan At-Tawbah(9)
                    if (a.number.inSurah === 1 && currentSurahNo !== 1 && currentSurahNo !== 9) {
                        html += `<div class="bismillah-img" style="display:block;">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</div>`;
                    }
                }

                isFirstVerseInLoop = false;

                // Render Ayat Card (dengan tombol Tafsir, Bookmark, Play)
                html += `
                <div class="ayat-card" id="ayat-${a.number.inQuran}">
                    <div class="ayat-header">
                        <div class="ayat-number-badge">${a.number.inSurah}</div>
                        <div class="ayat-actions">
                            <i class="fas fa-book-open ayat-action-btn" onclick="toggleTafsir(${currentSurahNo}, ${a.number.inSurah}, ${a.number.inQuran})" title="Baca Tafsir"></i>
                            <i class="fas fa-bookmark ayat-action-btn" onclick="saveBookmark(${currentSurahNo}, ${a.number.inSurah})" title="Tandai Terakhir Baca"></i>
                            <i class="fas fa-play ayat-action-btn" id="btn-play-ayat-${a.number.inQuran}" onclick="playAyat('${a.audio.primary}', ${a.number.inQuran})" title="Putar Audio"></i>
                        </div>
                    </div>
                    <div class="teks-arab">${a.text.arab}</div>
                    <div class="teks-container">
                        <div class="teks-latin">${a.text.transliteration.en}</div>
                        <div class="teks-indo">${a.translation.id}</div>
                        
                        <!-- Box Tafsir -->
                        <div class="tafsir-box" id="tafsir-${a.number.inQuran}">
                            <div class="t-title">Tafsir Kemenag RI</div>
                            <div id="tafsir-text-${a.number.inQuran}">Memuat tafsir... <i class="fas fa-spinner fa-spin"></i></div>
                        </div>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        }

        // --- TOGGLE TERJEMAHAN GLOBALLY ---
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

        // --- FITUR TAFSIR ON-DEMAND (LAZY LOAD AGAR CEPAT) ---
        async function toggleTafsir(surahNo, ayatNo, quranNo) {
            if (!isTerjemahTampil) toggleTerjemah(); // Munculkan terjemahan dulu

            const box = document.getElementById(`tafsir-${quranNo}`);
            const textBox = document.getElementById(`tafsir-text-${quranNo}`);

            box.classList.toggle('show');

            // Jika box terbuka dan tafsir belum ada di cache memori
            if (box.classList.contains('show') && !tafsirCache[surahNo]) {
                try {
                    const res = await fetch(`https://equran.id/api/v2/tafsir/${surahNo}`);
                    const data = await res.json();
                    tafsirCache[surahNo] = data.data.tafsir; // Simpan ke memori sementara

                    const findTafsir = tafsirCache[surahNo].find(t => t.ayat == ayatNo);
                    textBox.innerHTML = findTafsir ? findTafsir.teks : "Tafsir tidak tersedia.";
                } catch (e) {
                    textBox.innerHTML = "Gagal memuat tafsir. Periksa koneksi.";
                }
            } else if (box.classList.contains('show') && tafsirCache[surahNo]) {
                // Jika sudah ada di cache, langsung tampilkan
                const findTafsir = tafsirCache[surahNo].find(t => t.ayat == ayatNo);
                textBox.innerHTML = findTafsir ? findTafsir.teks : "Tafsir tidak tersedia.";
            }
        }

        // --- AUDIO PER AYAT ---
        let currentAyatCard = null;
        let currentAyatNo = null;

        function playAyat(url, quranNo) {
            if (currentAyatNo === quranNo && !audioAyatEl.paused) {
                audioAyatEl.pause();
                resetAyatIcons();
                return;
            }

            resetAyatIcons();

            audioAyatEl.src = url;
            audioAyatEl.play();

            currentAyatNo = quranNo;
            currentAyatCard = document.getElementById(`ayat-${quranNo}`);
            currentAyatCard.classList.add('playing');
            document.getElementById(`btn-play-ayat-${quranNo}`).className = "fas fa-pause ayat-action-btn playing";
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

        // --- AJAX SAVE BOOKMARK (TOMBOL) ---
        function saveBookmark(surahNo, ayatNo) {
            const formData = new URLSearchParams();
            formData.append('action', 'bookmark');
            formData.append('surah', surahNo);
            formData.append('ayat', ayatNo);

            fetch('baca-juz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                })
                .then(res => res.text())
                .then(res => {
                    if (res.trim() === 'saved') {
                        showAlert(`Ayat berhasil disimpan ke Terakhir Baca!`);
                    } else {
                        showAlert(`Ayat ini sudah ada di daftar.`);
                    }
                });
        }

        // --- CUSTOM ALERT ---
        function showAlert(msg) {
            document.getElementById('alertMsg').innerText = msg;
            const alertEl = document.getElementById('customAlert');
            alertEl.classList.add('show');
            setTimeout(() => alertEl.classList.remove('show'), 3000);
        }

        fetchJuzData();
    </script>
</body>

</html>