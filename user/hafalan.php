<?php
session_start();
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
}

$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['role'] === 'user';

// Jika ingin diwajibkan login, aktifkan blok ini:
// if (!$is_logged_in) {
//     header("Location: ../login.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Hafalan Al-Qur'an — Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Menggunakan Font Standar Mushaf Madinah */
        @font-face {
            font-family: 'Uthmani';
            src: url('https://cdn.jsdelivr.net/gh/fawazahmed0/quran-api@1/fonts/KFGQPC_Uthmanic_Script_HAFS_Regular.ttf') format('truetype');
        }

        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --primary-light: #d1fae5;
            --dark: #0f172a;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --ease: cubic-bezier(.22, 1, .36, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 100px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: clamp(14px, 4vw, 20px);
            width: 100%;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .back-btn {
            background: white;
            width: 45px;
            height: 45px;
            flex-shrink: 0;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            transition: 0.2s;
        }

        .back-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-title {
            font-size: clamp(1.2rem, 4.5vw, 1.5rem);
            font-weight: 800;
        }

        /* ---------- KONTROL HAFALAN ---------- */
        .controls-card {
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            margin-bottom: 25px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .control-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .control-group label {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--dark);
            display: flex;
            justify-content: space-between;
        }

        select {
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--bg);
            font-weight: 600;
            color: var(--dark);
            outline: none;
            cursor: pointer;
        }

        select:focus {
            border-color: var(--primary);
        }

        /* Slider Tingkat Kesulitan */
        .slider-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        input[type=range] {
            flex-grow: 1;
            -webkit-appearance: none;
            width: 100%;
            background: transparent;
        }

        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 24px;
            width: 24px;
            border-radius: 50%;
            background: var(--primary);
            cursor: pointer;
            margin-top: -8px;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.3);
        }

        input[type=range]::-webkit-slider-runnable-track {
            width: 100%;
            height: 8px;
            cursor: pointer;
            background: var(--primary-light);
            border-radius: 4px;
        }

        .level-badge {
            background: var(--dark);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 0.85rem;
            min-width: 65px;
            text-align: center;
        }

        /* ---------- TAMPILAN MUSHAF ---------- */
        .mushaf-container {
            background: #fffdf5;
            /* Warna kertas kuning gading khas mushaf */
            padding: clamp(20px, 5vw, 40px);
            border-radius: 24px;
            box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.02), 0 20px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e0c8;
            position: relative;
            overflow: hidden;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .mushaf-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 0.9rem;
            color: #857a55;
            border-bottom: 2px solid #e5e0c8;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .quran-page {
            width: 100%;
            font-family: 'Uthmani', serif;
            font-size: clamp(1.8rem, 4.5vw, 2.4rem);
            line-height: 1.8;
            direction: rtl;
            text-align: justify;
            text-justify: inter-word;
            color: #1e1e1e;
        }

        .ayah-word {
            display: inline-block;
            transition: color 0.3s var(--ease), border-color 0.3s var(--ease);
            padding: 0 2px;
        }

        /* State saat kata dihilangkan (Hafalan) */
        .ayah-word.hidden-word {
            color: transparent;
            border-bottom: 2px dashed #d4d4d8;
            user-select: none;
        }

        .ayah-word.hidden-word:hover {
            color: rgba(15, 23, 42, 0.1);
            /* Hint tipis saat dihover */
            border-bottom-color: var(--primary);
        }

        .ayah-end {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="none" stroke="%23b4a269" stroke-width="4"/><circle cx="50" cy="50" r="38" fill="none" stroke="%23b4a269" stroke-width="1" stroke-dasharray="2,2"/></svg>') no-repeat center;
            background-size: contain;
            font-size: 1rem;
            color: #b4a269;
            margin: 0 5px;
            transform: translateY(-5px);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            display: none;
            margin: 50px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ---------- PAGINASI ---------- */
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 25px;
            gap: 10px;
            width: 100%;
        }

        .btn-page {
            background: white;
            border: 1px solid var(--border);
            padding: 12px 20px;
            border-radius: 14px;
            font-weight: 700;
            color: var(--dark);
            cursor: pointer;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-page:hover:not(:disabled) {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-page:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <h1 class="page-title">Hafalan Mushaf</h1>
        </div>

        <div class="controls-card">
            <div class="control-group">
                <label>Pilih Surah (Lompat ke Halaman)</label>
                <select id="surahSelect" onchange="jumpToSurah()">
                    <option value="">Memuat daftar surah...</option>
                </select>
            </div>

            <div class="control-group">
                <label><span>Tingkat Hafalan (Sensor Kata)</span> <span id="lblPercent">0% Hilang</span></label>
                <div class="slider-container">
                    <span style="font-size: 1.2rem; color: var(--text-muted);"><i class="fas fa-eye"></i></span>
                    <input type="range" id="levelSlider" min="0" max="100" step="25" value="0" oninput="applyMemorizationLevel()">
                    <div class="level-badge" id="lvlBadge">Tampil</div>
                </div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">
                    *Geser ke kanan untuk menghilangkan teks perlahan. Layout halaman akan tetap dipertahankan.
                </div>
            </div>
        </div>

        <div class="mushaf-container">
            <div class="mushaf-header">
                <span id="pageSurahName">Juz 1 • Al-Fatihah</span>
                <span id="pageNumberLabel">Halaman 1</span>
            </div>

            <div class="loader" id="loader"></div>
            <div class="quran-page" id="quranPage">
                <!-- Teks Al-Qur'an akan dirender di sini -->
            </div>

            <div class="pagination">
                <button class="btn-page" id="btnPrev" onclick="changePage(-1)"><i class="fas fa-chevron-right"></i> Halaman Sebelumnya</button>
                <button class="btn-page" id="btnNext" onclick="changePage(1)">Halaman Berikutnya <i class="fas fa-chevron-left"></i></button>
            </div>
        </div>
    </div>

    <?php if ($is_logged_in) include '../components/nav.php'; ?>

    <script>
        let currentPage = 1;
        const totalPages = 604;
        let wordElements = []; // Menyimpan semua span elemen kata
        let shuffledIndices = []; // Array urutan acak untuk menyembunyikan kata

        // Daftar Halaman Awal Tiap Surah (Standar Mushaf Madinah)
        const surahStartPages = [
            1, 2, 50, 77, 106, 128, 151, 177, 187, 208, 221, 235, 249, 255, 262, 267, 282, 293, 305, 312,
            322, 332, 342, 350, 359, 367, 377, 385, 396, 404, 411, 415, 418, 428, 434, 440, 446, 453, 458, 467,
            477, 483, 489, 496, 499, 502, 507, 511, 515, 518, 520, 523, 526, 528, 531, 534, 537, 542, 545, 549,
            551, 553, 554, 556, 558, 560, 562, 564, 566, 568, 570, 572, 574, 575, 577, 578, 580, 582, 583, 585,
            586, 587, 587, 589, 590, 591, 591, 592, 593, 594, 595, 595, 596, 596, 597, 597, 598, 598, 599, 599,
            600, 600, 601, 601, 601, 602, 602, 602, 603, 603, 603, 604, 604, 604
        ];
        const surahNames = [
            "Al-Fatihah", "Al-Baqarah", "Ali 'Imran", "An-Nisa'", "Al-Ma'idah", "Al-An'am", "Al-A'raf", "Al-Anfal", "At-Taubah", "Yunus", "Hud", "Yusuf", "Ar-Ra'd", "Ibrahim", "Al-Hijr", "An-Nahl", "Al-Isra'", "Al-Kahf", "Maryam", "Taha", "Al-Anbiya'", "Al-Hajj", "Al-Mu'minun", "An-Nur", "Al-Furqan", "Asy-Syu'ara'", "An-Naml", "Al-Qasas", "Al-'Ankabut", "Ar-Rum", "Luqman", "As-Sajdah", "Al-Ahzab", "Saba'", "Fatir", "Yasin", "As-Saffat", "Sad", "Az-Zumar", "Ghafir", "Fussilat", "Asy-Syura", "Az-Zukhruf", "Ad-Dukhan", "Al-Jasiyah", "Al-Ahqaf", "Muhammad", "Al-Fath", "Al-Hujurat", "Qaf", "Az-Zariyat", "At-Tur", "An-Najm", "Al-Qamar", "Ar-Rahman", "Al-Waqi'ah", "Al-Hadid", "Al-Mujadilah", "Al-Hasyr", "Al-Mumtahanah", "As-Saff", "Al-Jumu'ah", "Al-Munafiqun", "At-Tagabun", "At-Talaq", "At-Tahrim", "Al-Mulk", "Al-Qalam", "Al-Haqqah", "Al-Ma'arij", "Nuh", "Al-Jinn", "Al-Muzzammil", "Al-Muddassir", "Al-Qiyamah", "Al-Insan", "Al-Mursalat", "An-Naba'", "An-Nazi'at", "'Abasa", "At-Takwir", "Al-Infitar", "Al-Mutaffifin", "Al-Insyiqaq", "Al-Buruj", "At-Tariq", "Al-A'la", "Al-Gasyiyah", "Al-Fajr", "Al-Balad", "Asy-Syams", "Al-Lail", "Ad-Duha", "Asy-Syarh", "At-Tin", "Al-'Alaq", "Al-Qadr", "Al-Bayyinah", "Az-Zalzalah", "Al-'Adiyat", "Al-Qari'ah", "At-Takasur", "Al-'Asr", "Al-Humazah", "Al-Fil", "Quraisy", "Al-Ma'un", "Al-Kausar", "Al-Kafirun", "An-Nasr", "Al-Lahab", "Al-Ikhlas", "Al-Falaq", "An-Nas"
        ];

        document.addEventListener('DOMContentLoaded', () => {
            // Populate Surah Dropdown
            const select = document.getElementById('surahSelect');
            select.innerHTML = '<option value="">-- Pilih Surah --</option>';
            surahNames.forEach((name, i) => {
                select.innerHTML += `<option value="${surahStartPages[i]}">${i + 1}. ${name} (Hal. ${surahStartPages[i]})</option>`;
            });

            loadQuranPage(currentPage);
        });

        function jumpToSurah() {
            const val = document.getElementById('surahSelect').value;
            if (val) {
                currentPage = parseInt(val);
                loadQuranPage(currentPage);
            }
        }

        function changePage(direction) {
            let newPage = currentPage + direction;
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                loadQuranPage(currentPage);
            }
        }

        async function loadQuranPage(page) {
            document.getElementById('quranPage').innerHTML = '';
            document.getElementById('loader').style.display = 'block';
            document.getElementById('btnPrev').disabled = (page === 1);
            document.getElementById('btnNext').disabled = (page === totalPages);

            // Set slider to 0 automatically when switching pages
            document.getElementById('levelSlider').value = 0;
            updateSliderUI();

            try {
                // Fetch dari API Al-Quran Cloud (Format Uthmani Mushaf Madinah)
                const response = await fetch(`https://api.alquran.cloud/v1/page/${page}/quran-uthmani`);
                const data = await response.json();

                if (data.code === 200) {
                    renderQuranText(data.data);
                }
            } catch (error) {
                document.getElementById('quranPage').innerHTML = '<div style="text-align:center; color:var(--danger); font-size:1rem;">Gagal memuat ayat. Periksa koneksi internet Anda.</div>';
            } finally {
                document.getElementById('loader').style.display = 'none';
            }
        }

        function renderQuranText(pageData) {
            const container = document.getElementById('quranPage');

            // Update Header Info
            const firstAyah = pageData.ayahs[0];
            document.getElementById('pageSurahName').innerText = `Juz ${firstAyah.juz} • ${firstAyah.surah.name}`;
            document.getElementById('pageNumberLabel').innerText = `Halaman ${pageData.number}`;

            wordElements = [];
            let htmlContent = '';
            let globalWordIndex = 0;

            pageData.ayahs.forEach(ayah => {
                // Hapus Bismillah di awal ayat pertama surah (karena Bismillah tidak selalu dihitung kata demi kata untuk hafalan, kecuali surah Al-Fatihah)
                let text = ayah.text;
                if (ayah.numberInSurah === 1 && ayah.surah.number !== 1 && ayah.surah.number !== 9) {
                    text = text.replace('بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ', '');
                    // Render Bismillah sebagai teks statis di tengah
                    htmlContent += `<div style="text-align:center; display:block; font-size:1.8rem; margin:15px 0;">بِسْمِ ٱللَّهِ ٱلرَّحْمَٰنِ ٱلرَّحِيمِ</div>`;
                }

                const words = text.trim().split(' ');

                words.forEach(word => {
                    if (word) {
                        htmlContent += `<span class="ayah-word" id="word-${globalWordIndex}">${word}</span> `;
                        globalWordIndex++;
                    }
                });

                // Tambahkan tanda akhir ayat
                const arabicNumber = convertToArabicNumber(ayah.numberInSurah);
                htmlContent += `<span class="ayah-end">${arabicNumber}</span> `;
            });

            container.innerHTML = htmlContent;

            // Koleksi elemen ke dalam array
            for (let i = 0; i < globalWordIndex; i++) {
                wordElements.push(document.getElementById(`word-${i}`));
            }

            // Buat urutan acak untuk algoritma hilangnya kata
            shuffledIndices = Array.from({
                length: globalWordIndex
            }, (_, i) => i);
            shuffleArray(shuffledIndices);
        }

        function convertToArabicNumber(num) {
            const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            return num.toString().split('').map(digit => arabicNumbers[parseInt(digit)]).join('');
        }

        // Algoritma Fisher-Yates Shuffle
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
        }

        function updateSliderUI() {
            const val = document.getElementById('levelSlider').value;
            const badge = document.getElementById('lvlBadge');
            document.getElementById('lblPercent').innerText = `${val}% Hilang`;

            if (val == 0) badge.innerText = "Tampil";
            else if (val == 25) badge.innerText = "Mudah";
            else if (val == 50) badge.innerText = "Sedang";
            else if (val == 75) badge.innerText = "Sulit";
            else badge.innerText = "Kosong";
        }

        function applyMemorizationLevel() {
            updateSliderUI();
            const percentage = parseInt(document.getElementById('levelSlider').value);

            // Reset semua kata menjadi terlihat
            wordElements.forEach(el => el.classList.remove('hidden-word'));

            // Hitung berapa jumlah kata yang harus dihilangkan berdasarkan persentase
            const wordsToHideCount = Math.floor((percentage / 100) * wordElements.length);

            // Hilangkan kata berdasarkan urutan acak yang sudah disimpan di awal halaman
            for (let i = 0; i < wordsToHideCount; i++) {
                const targetIndex = shuffledIndices[i];
                wordElements[targetIndex].classList.add('hidden-word');
            }
        }
    </script>
</body>

</html>