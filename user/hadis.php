<?php
session_start();

// Cek session login yang sesuai dengan project kita sebelumnya
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pustaka Hadis - Hifzhly</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #059669;
            --primary-light: #10b981;
            --primary-dark: #047857;
            --gold: #c9a227;
            --bg: #f6faf8;
            --ink: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --card-bg: #ffffff;
            --radius: 20px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
            min-height: 100vh;
            margin: 0;
            padding-bottom: 60px;
        }

        .container-hadis {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-section h1 {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .header-section p {
            color: var(--muted);
        }

        /* ============ VIEW 1: GRID KITAB ============ */
        #view-home {
            transition: opacity 0.3s;
        }

        .kitab-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }

        .kitab-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .kitab-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-light);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.1);
        }

        .kitab-card .icon {
            width: 50px;
            height: 50px;
            background: #ecfdf5;
            color: var(--primary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }

        .kitab-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .kitab-card p {
            font-size: 0.85rem;
            color: var(--muted);
        }

        /* ============ VIEW 2: DETAIL & PENCARIAN ============ */
        #view-detail {
            display: none;
            animation: slideIn 0.4s ease forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            border: 1px solid var(--border);
            padding: 10px 16px;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 24px;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: #f1f5f9;
        }

        .search-box {
            background: white;
            padding: 20px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .search-box input {
            flex: 1;
            padding: 14px 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 1rem;
            outline: none;
        }

        .search-box input:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px #d1fae5;
        }

        .btn-cari {
            padding: 0 24px;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            border: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-cari:hover {
            background: var(--primary-dark);
        }

        /* ============ LIST HADIS ============ */
        .hadis-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .hadis-item {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        }

        .hadis-header {
            background: #ecfdf5;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .hadis-content {
            padding: 30px 24px;
        }

        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 2.2rem;
            line-height: 2.2;
            text-align: right;
            margin-bottom: 24px;
            direction: rtl;
            color: #000;
        }

        .terjemahan {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--ink);
            padding-top: 20px;
            border-top: 1px dashed var(--border);
        }

        .terjemahan-label {
            display: inline-block;
            background: #f1f5f9;
            color: var(--muted);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .highlight {
            background-color: #fef08a;
            padding: 2px 4px;
            border-radius: 4px;
            font-weight: 600;
        }

        .loader {
            display: none;
            text-align: center;
            padding: 40px;
            color: var(--primary);
        }

        .loader i {
            font-size: 2.5rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        .error-msg {
            display: none;
            background: #fee2e2;
            color: #ef4444;
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--muted);
            font-weight: 500;
        }

        @media (max-width: 640px) {
            .search-box {
                flex-direction: column;
            }

            .btn-cari {
                height: 48px;
            }

            .arabic-text {
                font-size: 1.7rem;
            }
        }
    </style>
</head>

<body>

    <!-- Include Navbar dipanggil di dalam Body agar UI rapi -->
    <?php include '../components/nav.php'; ?>

    <div class="container-hadis">

        <!-- VIEW 1: HOME (GRID KITAB) -->
        <div id="view-home">
            <div class="header-section">
                <h1>Pustaka Hadis</h1>
                <p>Pilih kitab untuk membaca atau mencari berdasarkan nomor & topik kajian.</p>
            </div>
            <div class="kitab-grid" id="kitabGrid"></div>
        </div>

        <!-- VIEW 2: PENCARIAN & DAFTAR HADIS -->
        <div id="view-detail">
            <button class="btn-back" onclick="kembaliKeHome()">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kitab
            </button>

            <div class="header-section" style="text-align: left; margin-bottom: 24px;">
                <h1 id="detail-title">Sahih Bukhari</h1>
                <p id="detail-subtitle">Total Hadis: 7008</p>
            </div>

            <div class="error-msg" id="errorMsg"></div>

            <div class="search-box">
                <input type="text" id="inputPencarian" placeholder="Cari nomor (ex: 5) atau topik (ex: hukum, puasa)..." onkeypress="handleEnter(event)">
                <button class="btn-cari" onclick="prosesPencarian()">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>

            <div class="loader" id="loader">
                <i class="fas fa-circle-notch"></i>
                <p style="margin-top: 10px; font-weight: 600;" id="loaderText">Memuat data...</p>
            </div>

            <!-- List Hadis -->
            <div class="hadis-list" id="hadisList"></div>
        </div>
    </div>

    <!-- ============ JAVASCRIPT ============ -->
    <script>
        const dataKitab = [{
                id: 'bukhari',
                name: 'Sahih Bukhari',
                total: 7008,
                icon: 'fa-book-open'
            },
            {
                id: 'muslim',
                name: 'Sahih Muslim',
                total: 5362,
                icon: 'fa-book'
            },
            {
                id: 'abu-dawud',
                name: 'Sunan Abu Dawud',
                total: 4590,
                icon: 'fa-book-quran'
            },
            {
                id: 'tirmidzi',
                name: 'Sunan Tirmidzi',
                total: 3625,
                icon: 'fa-book-bookmark'
            },
            {
                id: 'nasai',
                name: 'Sunan An-Nasai',
                total: 5364,
                icon: 'fa-scroll'
            },
            {
                id: 'ibnu-majah',
                name: 'Sunan Ibnu Majah',
                total: 4341,
                icon: 'fa-book-open-reader'
            },
            {
                id: 'ahmad',
                name: 'Musnad Ahmad',
                total: 4305,
                icon: 'fa-layer-group'
            },
            {
                id: 'malik',
                name: 'Muwatta Malik',
                total: 1589,
                icon: 'fa-scale-balanced'
            },
            {
                id: 'darimi',
                name: 'Sunan Ad-Darimi',
                total: 2949,
                icon: 'fa-star-and-crescent'
            }
        ];

        let currentKitabId = '';
        let currentKitabName = '';

        window.onload = () => {
            const grid = document.getElementById('kitabGrid');
            dataKitab.forEach(kitab => {
                const card = document.createElement('div');
                card.className = 'kitab-card';
                card.onclick = () => bukaKitab(kitab.id, kitab.name, kitab.total);
                card.innerHTML = `
                    <div class="icon"><i class="fas ${kitab.icon}"></i></div>
                    <h3>${kitab.name}</h3>
                    <p>${kitab.total} Hadis tersedia</p>
                `;
                grid.appendChild(card);
            });
        };

        function bukaKitab(id, name, total) {
            currentKitabId = id;
            currentKitabName = name;

            document.getElementById('detail-title').innerText = name;
            document.getElementById('detail-subtitle').innerText = `Total ${total} hadis tersedia`;
            document.getElementById('inputPencarian').value = '';
            document.getElementById('hadisList').innerHTML = '';

            document.getElementById('view-home').style.display = 'none';
            document.getElementById('view-detail').style.display = 'block';

            // Load 10 hadis pertama secara paralel
            loadBanyakHadis(1, 10, false);
        }

        function kembaliKeHome() {
            document.getElementById('view-detail').style.display = 'none';
            document.getElementById('view-home').style.display = 'block';
        }

        function handleEnter(e) {
            if (e.key === 'Enter') prosesPencarian();
        }

        function prosesPencarian() {
            const input = document.getElementById('inputPencarian').value.trim();
            if (!input) {
                loadBanyakHadis(1, 10, false);
                return;
            }

            // Jika input berupa angka = cari nomor spesifik
            if (/^\d+$/.test(input)) {
                loadSatuHadis(input);
            } else {
                // Jika input berupa huruf/topik = ambil 30 hadis awal lalu saring lokal
                loadBanyakHadis(1, 30, true, input.toLowerCase());
            }
        }

        // --- FUNGSI AMBIL 1 HADIS (BY NOMOR) ---
        async function loadSatuHadis(nomor) {
            setLoading(true, "Mencari hadis nomor " + nomor + "...");
            try {
                const res = await fetch(`https://api.hadith.gading.dev/books/${currentKitabId}/${nomor}`);
                const data = await res.json();

                if (data.code !== 200) throw new Error("Gagal");

                renderHadis([data.data.contents], false, '');
            } catch (err) {
                tampilError("Nomor hadis tidak ditemukan atau server sedang sibuk.");
            } finally {
                setLoading(false);
            }
        }

        // --- FUNGSI AMBIL BANYAK HADIS (PARALEL) ---
        async function loadBanyakHadis(start, end, isSearch, keyword = '') {
            setLoading(true, isSearch ? "Mencari kata kunci..." : "Memuat data sanad & matan...");

            let kumpulanHadis = [];
            let promises = [];

            for (let i = start; i <= end; i++) {
                promises.push(
                    fetch(`https://api.hadith.gading.dev/books/${currentKitabId}/${i}`)
                    .then(res => res.json())
                    .catch(() => null)
                );
            }

            try {
                const results = await Promise.all(promises);

                results.forEach(res => {
                    if (res && res.code === 200 && res.data) {
                        kumpulanHadis.push(res.data.contents);
                    }
                });

                if (kumpulanHadis.length === 0) throw new Error("Gagal");

                // Filter Kata Kunci jika user sedang mencari topik
                if (isSearch) {
                    kumpulanHadis = kumpulanHadis.filter(h => h.id.toLowerCase().includes(keyword));
                    if (kumpulanHadis.length === 0) {
                        document.getElementById('hadisList').innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-search" style="font-size: 3rem; color: #cbd5e1; margin-bottom:15px; display:block;"></i>
                                Tidak ada hadis terkait kata kunci "<b>${keyword}</b>" pada urutan awal kitab ini.
                            </div>`;
                        setLoading(false);
                        return;
                    }
                }

                renderHadis(kumpulanHadis, isSearch, keyword);

            } catch (error) {
                tampilError("Koneksi ke server API terputus. Silakan coba beberapa saat lagi.");
            } finally {
                setLoading(false);
            }
        }

        // --- RENDER HADIS KE HTML ---
        function renderHadis(dataArray, isSearch, keyword) {
            const listArea = document.getElementById('hadisList');
            listArea.innerHTML = '';

            dataArray.forEach(hadis => {
                let teksArti = hadis.id;

                // Highlight kata kunci (Stabilo Kuning)
                if (isSearch && keyword) {
                    const regex = new RegExp(`(${keyword})`, 'gi');
                    teksArti = teksArti.replace(regex, `<span class="highlight">$1</span>`);
                }

                const card = document.createElement('div');
                card.className = 'hadis-item';
                card.innerHTML = `
                    <div class="hadis-header">
                        <span>Hadis No. ${hadis.number}</span>
                        <span class="badge" style="background: var(--gold); color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem;">Sahih</span>
                    </div>
                    <div class="hadis-content">
                        <div class="arabic-text">${hadis.arab}</div>
                        <div class="terjemahan">
                            <span class="terjemahan-label"><i class="fas fa-sitemap"></i> Sanad & Matan (Terjemahan)</span>
                            <div>${teksArti}</div>
                        </div>
                    </div>
                `;
                listArea.appendChild(card);
            });
        }

        // --- UTILITIES ---
        function setLoading(isLoading, text = "") {
            document.getElementById('loader').style.display = isLoading ? 'block' : 'none';
            document.getElementById('errorMsg').style.display = 'none';
            if (isLoading) document.getElementById('loaderText').innerText = text;
        }

        function tampilError(msg) {
            const errorMsg = document.getElementById('errorMsg');
            errorMsg.innerText = msg;
            errorMsg.style.display = 'block';
            document.getElementById('hadisList').innerHTML = '';
        }
    </script>
</body>

</html>