<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit('Unauthorized');
}

$user_id = $_SESSION['user_id'];

// --- AJAX HANDLER UNTUK BOOKMARK (Berjalan tanpa reload) ---
if (isset($_POST['action']) && $_POST['action'] == 'bookmark') {
    $surah = (int)$_POST['surah'];
    $ayat = (int)$_POST['ayat'];

    // Cek apakah sudah ada
    $cek = mysqli_query($conn, "SELECT id FROM bookmark WHERE user_id='$user_id' AND surah_nomor='$surah' AND ayat='$ayat'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($conn, "INSERT INTO bookmark (user_id, surah_nomor, ayat, catatan) VALUES ('$user_id', '$surah', '$ayat', 'Disimpan otomatis')");
        echo "saved";
    } else {
        echo "exist";
    }
    exit(); // Hentikan eksekusi PHP agar tidak merender HTML
}

$nomor_surat = isset($_GET['nomor']) ? (int)$_GET['nomor'] : 1;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Al-Qur'an</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #8b5cf6;
            --primary-light: #ede9fe;
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

        /* CUSTOM HEADER (TIDAK PAKAI nav.php) */
        .read-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-btn {
            color: var(--dark);
            font-size: 1.2rem;
            cursor: pointer;
            text-decoration: none;
        }

        .surah-title-head {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
        }

        .info-btn {
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.2rem;
        }

        /* Audio Player Full Surah */
        .audio-player-full {
            display: flex;
            align-items: center;
            gap: 15px;
            background: var(--bg);
            padding: 10px 15px;
            border-radius: 12px;
            border: 1px solid var(--border);
        }

        .play-full-btn {
            width: 35px;
            height: 35px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .audio-progress {
            flex-grow: 1;
            height: 6px;
            background: #cbd5e1;
            border-radius: 3px;
            position: relative;
            overflow: hidden;
        }

        .progress-bar {
            width: 0%;
            height: 100%;
            background: var(--primary);
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Bismillah */
        .bismillah-img {
            text-align: center;
            font-family: 'Amiri', serif;
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
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
            position: relative;
            transition: background 0.3s;
            user-select: none;
            /* Penting untuk long press */
        }

        .ayat-card.playing {
            border: 1px solid var(--primary);
            background: var(--primary-light);
        }

        /* Barisan atas Ayat: Nomor & Aksi */
        .ayat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .ayat-number-badge {
            width: 35px;
            height: 35px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
        }

        .ayat-actions {
            display: flex;
            gap: 15px;
        }

        .ayat-action-btn {
            color: var(--text-muted);
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .ayat-action-btn:hover {
            color: var(--primary);
        }

        /* Tipografi Arab & Waqaf */
        .teks-arab {
            font-family: 'Amiri', 'Traditional Arabic', serif;
            font-size: 2.4rem;
            text-align: right;
            line-height: 2.2;
            color: var(--quran-text);
            margin-bottom: 20px;
            direction: rtl;
        }

        /* Simbol End of Ayah */
        .ayah-end {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            font-size: 1rem;
            color: var(--primary);
            background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="%238b5cf6" stroke-width="4" d="M50 5 L60 20 L80 20 L85 40 L100 50 L85 60 L80 80 L60 80 L50 95 L40 80 L20 80 L15 60 L0 50 L15 40 L20 20 L40 20 Z"/></svg>') no-repeat center;
            background-size: contain;
            margin-right: 10px;
            font-family: 'Inter', sans-serif;
            font-weight: bold;
            position: relative;
            top: -5px;
        }

        .teks-latin {
            font-size: 1rem;
            color: var(--primary);
            margin-bottom: 10px;
            font-weight: 500;
            line-height: 1.5;
        }

        .teks-indo {
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.6;
        }

        /* HUKUM TAJWID & TOOLTIP (Bubble Chat) */
        .tajwid {
            position: relative;
            display: inline-block;
            cursor: pointer;
            border-bottom: 2px dashed;
        }

        .t-ikhfa {
            color: #d97706;
            border-color: #d97706;
        }

        .t-idgham {
            color: #059669;
            border-color: #059669;
        }

        .t-qalqalah {
            color: #2563eb;
            border-color: #2563eb;
        }

        .t-ghunnah {
            color: #db2777;
            border-color: #db2777;
        }

        /* Tooltip Bubble */
        .tajwid::after {
            content: attr(data-hukum);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-10px);
            background: var(--dark);
            color: white;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            white-space: nowrap;
            font-family: 'Inter', sans-serif;
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 10;
        }

        .tajwid::before {
            /* Segitiga panah */
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: var(--dark);
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 10;
        }

        .tajwid:hover::after,
        .tajwid:hover::before {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* ALERT ISLAMI */
        .islamic-alert {
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--card-bg);
            border-left: 5px solid var(--primary);
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 9999;
            transition: 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }

        .islamic-alert.show {
            top: 30px;
        }

        .ia-icon {
            font-size: 1.5rem;
            color: var(--primary);
        }

        /* MODAL TAFSIR */
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
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--primary);
        }

        #loading {
            text-align: center;
            margin-top: 50px;
            font-size: 1.2rem;
            color: var(--primary);
        }
    </style>
</head>

<body>

    <audio id="audioFull"></audio>
    <audio id="audioAyat"></audio>

    <div class="read-header">
        <div class="header-top">
            <a href="alquran.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="surah-title-head" id="head-title">Memuat...</div>
            <div class="info-btn" onclick="openInfoModal()"><i class="fas fa-info-circle"></i></div>
        </div>
        <div class="audio-player-full">
            <button class="play-full-btn" id="btn-play-full" onclick="togglePlayFull()"><i class="fas fa-play"></i></button>
            <div class="audio-progress">
                <div class="progress-bar" id="progress-full"></div>
            </div>
            <div style="font-size:0.75rem; color:var(--text-muted);" id="time-full">00:00</div>
        </div>
    </div>

    <div class="container">
        <div class="bismillah-img" id="bismillah" style="display:none;">
            بِسْمِ اللَّهِ <span class="tajwid t-idgham" data-hukum="Alif Lam Syamsiyah">الرَّحْمَنِ</span> <span class="tajwid t-idgham" data-hukum="Alif Lam Syamsiyah">الرَّحِيمِ</span>
        </div>

        <div id="loading"><i class="fas fa-spinner fa-spin"></i> Menyiapkan mushaf...</div>
        <div class="ayat-list" id="ayatList"></div>
    </div>

    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-bookmark ia-icon"></i>
        <div>
            <div style="font-weight:700; color:var(--dark);">Masya Allah!</div>
            <div style="font-size:0.85rem; color:var(--text-muted);" id="alertMsg">Ayat berhasil disimpan.</div>
        </div>
    </div>

    <div class="modal" id="infoModal" onclick="closeInfoModal(event)">
        <div class="modal-content" id="infoContent">
            <h2 class="modal-title" id="m-title">Info Surah</h2>
            <div style="font-size:0.95rem; line-height:1.6; color:#475569;" id="m-desc"></div>
        </div>
    </div>

    <script>
        const noSurat = <?= $nomor_surat ?>;
        let surahData = null;
        let audioFullEl = document.getElementById('audioFull');
        let audioAyatEl = document.getElementById('audioAyat');

        async function fetchBacaan() {
            try {
                const res = await fetch(`https://equran.id/api/v2/surat/${noSurat}`);
                const json = await res.json();
                surahData = json.data;

                document.getElementById('loading').style.display = 'none';
                document.getElementById('head-title').innerText = surahData.namaLatin;

                // Audio Full Setup
                audioFullEl.src = surahData.audioFull['05']; // Pilih Qari Misyari Rasyid Al-Afasi

                // Tampilkan Bismillah jika bukan surah 1 dan 9
                if (noSurat !== 1 && noSurat !== 9) {
                    document.getElementById('bismillah').style.display = 'block';
                }

                // Render Deskripsi (Asbabun Nuzul) ke Modal
                document.getElementById('m-title').innerText = `Tafsir Singkat & Asbabun Nuzul: ${surahData.namaLatin}`;
                document.getElementById('m-desc').innerHTML = surahData.deskripsi; // API return HTML string

                renderAyat(surahData.ayat);
            } catch (e) {
                document.getElementById('loading').innerHTML = "Gagal memuat ayat.";
            }
        }

        function renderAyat(ayatList) {
            const container = document.getElementById('ayatList');
            let html = '';

            ayatList.forEach(a => {
                html += `
                <div class="ayat-card" id="ayat-${a.nomorAyat}" 
                     onmousedown="startPress(${a.nomorAyat})" onmouseup="endPress()" onmouseleave="endPress()"
                     ontouchstart="startPress(${a.nomorAyat})" ontouchend="endPress()">
                    <div class="ayat-header">
                        <div class="ayat-number-badge">${a.nomorAyat}</div>
                        <div class="ayat-actions">
                            <i class="fas fa-play-circle ayat-action-btn" onclick="playAyat('${a.audio['05']}', ${a.nomorAyat})"></i>
                            <i class="far fa-bookmark ayat-action-btn" onclick="saveBookmark(${a.nomorAyat})"></i>
                        </div>
                    </div>
                    <div class="teks-arab">${a.teksArab} <span class="ayah-end">${a.nomorAyat}</span></div>
                    <div class="teks-latin">${a.teksLatin}</div>
                    <div class="teks-indo">${a.teksIndonesia}</div>
                </div>`;
            });
            container.innerHTML = html;

            // Check if URL has hash (e.g. #ayat-5) and scroll to it
            if (window.location.hash) {
                setTimeout(() => {
                    const el = document.querySelector(window.location.hash);
                    if (el) {
                        el.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                        el.style.background = "var(--primary-light)";
                        setTimeout(() => el.style.background = "var(--card-bg)", 2000);
                    }
                }, 500);
            }
        }

        // --- AUDIO PLAYER FULL ---
        let isFullPlaying = false;

        function togglePlayFull() {
            const btn = document.getElementById('btn-play-full');
            if (audioFullEl.paused) {
                // Pause ayat jika sedang jalan
                audioAyatEl.pause();
                audioFullEl.play();
                btn.innerHTML = '<i class="fas fa-pause"></i>';
            } else {
                audioFullEl.pause();
                btn.innerHTML = '<i class="fas fa-play"></i>';
            }
        }
        audioFullEl.ontimeupdate = () => {
            const progress = (audioFullEl.currentTime / audioFullEl.duration) * 100;
            document.getElementById('progress-full').style.width = progress + '%';

            let curMins = Math.floor(audioFullEl.currentTime / 60);
            let curSecs = Math.floor(audioFullEl.currentTime - curMins * 60);
            document.getElementById('time-full').innerText = `${curMins < 10 ? '0'+curMins : curMins}:${curSecs < 10 ? '0'+curSecs : curSecs}`;
        };
        audioFullEl.onended = () => document.getElementById('btn-play-full').innerHTML = '<i class="fas fa-play"></i>';

        // --- AUDIO PER AYAT ---
        let currentAyatCard = null;

        function playAyat(url, nomor) {
            // Pause full audio
            audioFullEl.pause();
            document.getElementById('btn-play-full').innerHTML = '<i class="fas fa-play"></i>';

            if (currentAyatCard) currentAyatCard.classList.remove('playing');

            audioAyatEl.src = url;
            audioAyatEl.play();

            currentAyatCard = document.getElementById(`ayat-${nomor}`);
            currentAyatCard.classList.add('playing');
        }
        audioAyatEl.onended = () => {
            if (currentAyatCard) currentAyatCard.classList.remove('playing');
        };

        // --- LONG PRESS TO BOOKMARK ---
        let pressTimer;

        function startPress(ayatNo) {
            pressTimer = window.setTimeout(() => {
                saveBookmark(ayatNo);
                // Get haptic feedback if supported
                if (navigator.vibrate) navigator.vibrate(50);
            }, 800); // Tahan 0.8 detik
        }

        function endPress() {
            clearTimeout(pressTimer);
        }

        // --- AJAX SAVE BOOKMARK ---
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
                        showAlert(`Ayat ${ayatNo} berhasil ditandai!`);
                    } else {
                        showAlert(`Ayat ${ayatNo} sudah ada di penanda.`);
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

        // --- MODAL INFO ---
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

        // Load data
        fetchBacaan();
    </script>
</body>

</html>