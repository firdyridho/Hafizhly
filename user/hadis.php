<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pustaka Hadis - Hifzhly</title>
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
            --card-bg: rgba(255, 255, 255, 0.95);
            --radius: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            background-image: radial-gradient(circle at top right, #d1fae5 0%, transparent 40%),
                radial-gradient(circle at bottom left, #ecfdf5 0%, transparent 40%);
            color: var(--ink);
            min-height: 100vh;
            padding: 40px 20px;
            overflow-x: hidden;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .header p {
            color: var(--muted);
            font-size: 1rem;
        }

        /* ============ VIEW 1: GRID KITAB ============ */
        #view-home {
            transition: opacity 0.3s ease;
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
            font-weight: 500;
        }

        .kitab-card .arrow {
            position: absolute;
            right: 24px;
            bottom: 24px;
            color: var(--border);
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .kitab-card:hover .arrow {
            color: var(--primary);
            transform: translateX(5px);
        }

        /* ============ VIEW 2: PENCARIAN HADIS ============ */
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
            color: var(--ink);
            cursor: pointer;
            margin-bottom: 24px;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: #f1f5f9;
        }

        .search-box {
            background: var(--card-bg);
            padding: 24px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
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
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-cari:hover {
            background: var(--primary-dark);
        }

        /* Hasil Hadis */
        #resultArea {
            display: none;
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .hadith-header {
            background: #ecfdf5;
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .hadith-content {
            padding: 30px 24px;
        }

        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 2.2rem;
            line-height: 2.2;
            text-align: right;
            margin-bottom: 24px;
            direction: rtl;
        }

        .terjemahan {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--muted);
            padding-top: 20px;
            border-top: 1px dashed var(--border);
        }

        .terjemahan strong {
            color: var(--ink);
            display: block;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
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

    <div class="container">
        <!-- VIEW 1: HOME (PILIH KITAB) -->
        <div id="view-home">
            <div class="header">
                <h1>Pustaka Hadis</h1>
                <p>Pilih kitab untuk mulai menjelajahi ribuan hadis dan sanadnya.</p>
            </div>
            <div class="kitab-grid" id="kitabGrid">
                <!-- Card di-generate via JavaScript -->
            </div>
        </div>

        <!-- VIEW 2: DETAIL & PENCARIAN -->
        <div id="view-detail">
            <button class="btn-back" onclick="kembaliKeHome()">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kitab
            </button>

            <div class="header" style="text-align: left; margin-bottom: 24px;">
                <h1 id="detail-title">Sahih Bukhari</h1>
                <p id="detail-subtitle">Total Hadis: 7008</p>
            </div>

            <div class="error-msg" id="errorMsg"></div>

            <div class="search-box">
                <input type="number" id="nomorHadis" placeholder="Masukkan nomor hadis (Contoh: 1)" min="1">
                <button class="btn-cari" onclick="cariHadis()">
                    <i class="fas fa-search"></i> Cari Hadis
                </button>
            </div>

            <div class="loader" id="loader">
                <i class="fas fa-circle-notch"></i>
                <p style="margin-top: 10px; font-weight: 600;">Mengambil sanad & matan...</p>
            </div>

            <div id="resultArea">
                <div class="hadith-header">
                    <span id="judulHadis">Hadis No. 1</span>
                    <span class="badge" style="background: var(--gold); color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem;">Sahih</span>
                </div>
                <div class="hadith-content">
                    <div class="arabic-text" id="teksArab"></div>
                    <div class="terjemahan">
                        <strong><i class="fas fa-language"></i> Terjemahan Bahasa Indonesia</strong>
                        <div id="teksArti"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data Kitab dan total hadisnya
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

        // Generate Card Kitab saat halaman diload
        function initGrid() {
            const grid = document.getElementById('kitabGrid');
            dataKitab.forEach(kitab => {
                const card = document.createElement('div');
                card.className = 'kitab-card';
                card.onclick = () => bukaKitab(kitab.id, kitab.name, kitab.total);
                card.innerHTML = `
                    <div class="icon"><i class="fas ${kitab.icon}"></i></div>
                    <h3>${kitab.name}</h3>
                    <p>${kitab.total} Hadis tersedia</p>
                    <i class="fas fa-chevron-right arrow"></i>
                `;
                grid.appendChild(card);
            });
        }

        // Navigasi Buka Kitab
        function bukaKitab(id, name, total) {
            currentKitabId = id;
            currentKitabName = name;

            // Set UI Teks
            document.getElementById('detail-title').innerText = name;
            document.getElementById('detail-subtitle').innerText = `Jelajahi dari 1 hingga ${total} hadis`;

            // Reset Area Pencarian
            document.getElementById('nomorHadis').value = '';
            document.getElementById('resultArea').style.display = 'none';
            document.getElementById('errorMsg').style.display = 'none';

            // Ganti View
            document.getElementById('view-home').style.display = 'none';
            document.getElementById('view-detail').style.display = 'block';
        }

        // Navigasi Kembali
        function kembaliKeHome() {
            document.getElementById('view-detail').style.display = 'none';
            document.getElementById('view-home').style.display = 'block';
        }

        // Fungsi Cari Hadis (Dengan Fallback / Cadangan API)
        async function cariHadis() {
            const nomor = document.getElementById('nomorHadis').value;
            const loader = document.getElementById('loader');
            const resultArea = document.getElementById('resultArea');
            const errorMsg = document.getElementById('errorMsg');

            if (!nomor) {
                tampilError("Masukkan nomor hadis terlebih dahulu!");
                return;
            }

            errorMsg.style.display = 'none';
            resultArea.style.display = 'none';
            loader.style.display = 'block';

            try {
                // API UTAMA: Vercel Hadis API Indonesia
                let response = await fetch(`https://hadis-api-id.vercel.app/hadith/${currentKitabId}/${nomor}`);
                let resData = await response.json();

                // Cek jika API utama gagal atau format berbeda, pakai API KEDUA (Gading)
                if (!response.ok || !resData.arab) {
                    const fallbackId = currentKitabId === 'abu-dawud' ? 'abu-dawud' : currentKitabId;
                    response = await fetch(`https://api.hadith.gading.dev/books/${fallbackId}/${nomor}`);
                    const fallbackData = await response.json();

                    if (fallbackData.code !== 200 || fallbackData.error) {
                        throw new Error("Hadis tidak ditemukan di database.");
                    }
                    // Mapping format API kedua agar sama
                    resData = {
                        arab: fallbackData.data.contents.arab,
                        id: fallbackData.data.contents.id
                    };
                }

                loader.style.display = 'none';

                // Tampilkan ke layar
                document.getElementById('judulHadis').innerText = `Hadis Riwayat ${currentKitabName} No. ${nomor}`;
                document.getElementById('teksArab').innerText = resData.arab;
                document.getElementById('teksArti').innerText = resData.id;

                resultArea.style.display = 'block';

            } catch (error) {
                loader.style.display = 'none';
                tampilError("Tidak dapat memuat hadis. Nomor mungkin melebihi batas atau server API sedang sibuk.");
            }
        }

        function tampilError(msg) {
            const errorMsg = document.getElementById('errorMsg');
            errorMsg.innerText = msg;
            errorMsg.style.display = 'block';
        }

        // Jalankan saat pertama kali dibuka
        window.onload = initGrid;
    </script>
</body>

</html>