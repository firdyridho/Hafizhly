<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

// --- AJAX HANDLER UNTUK BOOKMARK ---
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

$nomor_surat = isset($_GET['nomor']) ? (int)$_GET['nomor'] : 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Al-Qur'an</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <!-- MENGGUNAKAN FONT SCHEHERAZADE NEW UNTUK WAQAF SEMPURNA -->
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

        .surah-info-card {
            background: linear-gradient(135deg, var(--primary), #10b981);
            border-radius: 20px;
            padding: 30px 20px;
            color: white;
            text-align: center;
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.2);
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

        /* Font Arab diganti ke Scheherazade New */
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
            display: none;
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

        /* ALERT DESAIN BARU (Kapsul Elegan) */
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

        /* Warna Emas */

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: flex-end;
        }

        .modal-content {
            background: var(--card-bg);
            width: 100%;
            max-width: 600px;
            border-top-left-radius: 25px;
            border-top-right-radius: 25px;
            padding: 30px 20px;
            max-height: 80vh;
            overflow-y: auto;
            transform: translateY(100%);
            transition: 0.3s;
        }

        .modal.show .modal-content {
            transform: translateY(0);
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary);
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
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

    <!-- Audio Elements tanpa autoplay/src di awal -->
    <audio id="audioFull"></audio>
    <audio id="audioAyat"></audio>

    <div class="read-header">
        <div class="header-left">
            <a href="alquran.php" class="h-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="surah-name-mini" id="mini-title">Memuat...</div>
        </div>
        <div class="header-right">
            <div class="h-btn active" id="btn-terjemah" onclick="toggleTerjemah()" title="Terjemahan"><i class="fas fa-language"></i></div>
            <div class="h-btn" id="btn-play-full" onclick="togglePlayFull()" title="Putar Murottal"><i class="fas fa-play-circle"></i></div>
            <div class="h-btn" onclick="openInfoModal()" title="Asbabun Nuzul"><i class="fas fa-info-circle"></i></div>
        </div>
    </div>

    <div class="container">
        <div class="surah-info-card" id="hero-card" style="display:none;">
            <div class="sic-ar" id="hero-ar">--</div>
            <div class="sic-la" id="hero-la">--</div>
            <div class="sic-details" id="hero-det">--</div>
        </div>

        <div class="bismillah-img" id="bismillah">
            بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ
        </div>

        <div id="loading"><i class="fas fa-spinner fa-spin"></i> Menyiapkan Mushaf...</div>
        <div class="ayat-list" id="ayatList"></div>
    </div>

    <!-- Alert Custom -->
    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-check-circle ia-icon"></i>
        <div style="font-size:0.95rem; font-weight:600;" id="alertMsg">Berhasil!</div>
    </div>

    <div class="modal" id="infoModal" onclick="closeInfoModal(event)">
        <div class="modal-content" id="infoContent">
            <h2 class="modal-title" id="m-title">Asbabun Nuzul</h2>
            <div style="font-size:0.95rem; line-height:1.7; color:#475569;" id="m-desc"></div>
        </div>
    </div>

    <script>
        const noSurat = <?= $nomor_surat ?>;
        let surahData = null;
        let tafsirData = null;
        let audioFullEl = document.getElementById('audioFull');
        let audioAyatEl = document.getElementById('audioAyat');

        async function fetchAlQuranData() {
            try {
                const [resSurat, resTafsir] = await Promise.all([
                    fetch(`https://equran.id/api/v2/surat/${noSurat}`),
                    fetch(`https://equran.id/api/v2/tafsir/${noSurat}`)
                ]);

                const jsonSurat = await resSurat.json();
                const jsonTafsir = await resTafsir.json();

                surahData = jsonSurat.data;
                tafsirData = jsonTafsir.data.tafsir;

                setupUI();
                renderAyat(surahData.ayat);
            } catch (e) {
                document.getElementById('loading').innerHTML = "Gagal memuat ayat. Periksa koneksi internet.";
            }
        }

        function setupUI() {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('mini-title').innerText = surahData.namaLatin;

            document.getElementById('hero-card').style.display = 'block';
            document.getElementById('hero-ar').innerText = surahData.nama;
            document.getElementById('hero-la').innerText = surahData.namaLatin;
            let tmpt = surahData.tempatTurun === 'Mekah' ? 'Makiyyah' : 'Madaniyyah';
            document.getElementById('hero-det').innerHTML = `<span>${surahData.arti}</span> • <span>${tmpt}</span> • <span>${surahData.jumlahAyat} Ayat</span>`;

            if (noSurat !== 1 && noSurat !== 9) {
                document.getElementById('bismillah').style.display = 'block';
            }

            document.getElementById('m-title').innerText = `Info & Asbabun Nuzul: ${surahData.namaLatin}`;
            document.getElementById('m-desc').innerHTML = surahData.deskripsi;
        }

        function renderAyat(ayatList) {
            const container = document.getElementById('ayatList');
            let html = '';

            ayatList.forEach(a => {
                let txtTafsir = "Tafsir tidak tersedia.";
                let findTafsir = tafsirData.find(t => t.ayat == a.nomorAyat);
                if (findTafsir) txtTafsir = findTafsir.teks;

                html += `
                <div class="ayat-card" id="ayat-${a.nomorAyat}">
                    <div class="ayat-header">
                        <div class="ayat-number-badge">${a.nomorAyat}</div>
                        <div class="ayat-actions">
                            <i class="fas fa-book-open ayat-action-btn" onclick="toggleTafsir(${a.nomorAyat})" title="Baca Tafsir"></i>
                            <i class="fas fa-bookmark ayat-action-btn" onclick="saveBookmark(${a.nomorAyat})" title="Tandai Terakhir Baca"></i>
                            <i class="fas fa-play ayat-action-btn" id="btn-play-ayat-${a.nomorAyat}" onclick="playAyat('${a.audio['05']}', ${a.nomorAyat})" title="Putar Audio"></i>
                        </div>
                    </div>
                    <div class="teks-arab">${a.teksArab}</div>
                    <div class="teks-container">
                        <div class="teks-latin">${a.teksLatin}</div>
                        <div class="teks-indo">${a.teksIndonesia}</div>
                        <div class="tafsir-box" id="tafsir-${a.nomorAyat}">
                            <div class="t-title">Tafsir Kemenag RI</div>
                            ${txtTafsir}
                        </div>
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

        function toggleTafsir(no) {
            if (!isTerjemahTampil) toggleTerjemah();
            document.getElementById(`tafsir-${no}`).classList.toggle('show');
        }

        let isFullAudioLoaded = false;

        function togglePlayFull() {
            const btn = document.getElementById('btn-play-full');

            // Muat audio saat pertama kali ditekan saja
            if (!isFullAudioLoaded) {
                audioFullEl.src = surahData.audioFull['05'];
                isFullAudioLoaded = true;
            }

            if (audioFullEl.paused) {
                audioAyatEl.pause();
                resetAyatIcons();
                audioFullEl.play();
                btn.innerHTML = '<i class="fas fa-pause-circle"></i>';
                btn.classList.add('active');
            } else {
                audioFullEl.pause();
                btn.innerHTML = '<i class="fas fa-play-circle"></i>';
                btn.classList.remove('active');
            }
        }
        audioFullEl.onended = () => {
            document.getElementById('btn-play-full').innerHTML = '<i class="fas fa-play-circle"></i>';
            document.getElementById('btn-play-full').classList.remove('active');
        };

        // --- BUG AUDIO PER AYAT DIPERBAIKI DI SINI ---
        let currentAyatCard = null;
        let currentAyatNo = null;

        function playAyat(url, nomor) {
            audioFullEl.pause();
            document.getElementById('btn-play-full').innerHTML = '<i class="fas fa-play-circle"></i>';
            document.getElementById('btn-play-full').classList.remove('active');

            if (currentAyatNo === nomor && !audioAyatEl.paused) {
                audioAyatEl.pause();
                resetAyatIcons();
                return;
            }

            resetAyatIcons(); // Bersihkan yang lama sebelum putar yang baru

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
                // PENTING: Keduanya harus di-null-kan agar tidak bentrok
                currentAyatNo = null;
                currentAyatCard = null;
            }
        }

        audioAyatEl.onended = () => {
            resetAyatIcons();
        };

        function saveBookmark(ayatNo) {
            const formData = new URLSearchParams();
            formData.append('action', 'bookmark');
            formData.append('surah', noSurat);
            formData.append('ayat', ayatNo);

            fetch('baca.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                })
                .then(res => res.text())
                .then(res => {
                    if (res.trim() === 'saved') {
                        showAlert(`Ayat ${ayatNo} berhasil disimpan!`);
                    } else {
                        showAlert(`Ayat ${ayatNo} sudah tersimpan.`);
                    }
                });
        }

        function showAlert(msg) {
            document.getElementById('alertMsg').innerText = msg;
            const alertEl = document.getElementById('customAlert');
            alertEl.classList.add('show');
            setTimeout(() => alertEl.classList.remove('show'), 3000);
        }

        function openInfoModal() {
            document.getElementById('infoModal').style.display = 'flex';
            setTimeout(() => document.getElementById('infoModal').classList.add('show'), 10);
        }

        function closeInfoModal(e) {
            if (e.target.id === 'infoModal' || e.target.classList.contains('close-btn')) {
                document.getElementById('infoModal').classList.remove('show');
                setTimeout(() => document.getElementById('infoModal').style.display = 'none', 300);
            }
        }

        fetchAlQuranData();
    </script>
</body>

</html>