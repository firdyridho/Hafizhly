<?php
session_start();

// Cek session login yang sesuai dengan project kita sebelumnya
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
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

        .kitab-cover {
            height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .kitab-cover .cover-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.1;
            background-image:
                radial-gradient(circle at 20% 50%, #fff 1px, transparent 1px),
                radial-gradient(circle at 80% 50%, #fff 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .kitab-cover .cover-icon {
            font-size: 2.2rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .kitab-cover .cover-arab {
            font-family: 'Amiri', serif;
            font-size: 1.3rem;
            color: rgba(255,255,255,0.95);
            position: relative;
            z-index: 1;
            text-align: center;
            line-height: 1.5;
            padding: 0 12px;
        }

        .kitab-cover .cover-label {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }

        .kitab-info {
            padding: 18px 20px 20px;
        }

        .kitab-info h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .kitab-info p {
            font-size: 0.8rem;
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

        .hadis-actions {
            display: flex;
            gap: 10px;
            padding: 0 24px 20px;
            flex-wrap: wrap;
        }

        .btn-toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 30px;
            border: 1.5px solid var(--border);
            background: white;
            font-family: inherit;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.25s;
        }

        .btn-toggle:hover {
            border-color: var(--primary-light);
            background: #f0fdf4;
        }

        .btn-toggle.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .hadis-section {
            display: none;
            padding: 20px 24px;
            border-top: 1px dashed var(--border);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .hadis-section.open {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hadis-section .section-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            color: var(--muted);
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .hadis-section .section-label.sanad-label {
            background: #fff7ed;
            color: #c2410c;
        }

        .hadis-section .section-label.arti-label {
            background: #ecfdf5;
            color: #047857;
        }

        .sanad-text {
            color: #9a3412;
        }

        .sanad-text .narrator {
            color: #c2410c;
            font-weight: 700;
        }

        .sanad-text .arrow {
            color: #fdba74;
            margin: 0 4px;
        }

        .topik-pills {
            display: flex;
            flex-wrap: nowrap;
            gap: 8px;
            margin-bottom: 20px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            padding-bottom: 4px;
        }

        .topik-pills::-webkit-scrollbar {
            display: none;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border-radius: 30px;
            border: 1.5px solid var(--border);
            background: white;
            font-family: inherit;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--muted);
            cursor: pointer;
            transition: all 0.2s;
        }

        .pill:hover {
            border-color: var(--primary-light);
            background: #f0fdf4;
            color: var(--primary-dark);
        }

        .pill.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .pill i {
            font-size: 0.7rem;
        }

        .highlight {
            background-color: #fef08a;
            padding: 2px 4px;
            border-radius: 4px;
            font-weight: 600;
        }

        .arabic-text {
            cursor: pointer;
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

    <?php include '../components/nav.php'; ?>

    <div id="ajax-content">

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
                <input type="text" id="inputPencarian" placeholder="Cari nomor atau kata kunci..." onkeypress="handleEnter(event)">
                <button class="btn-cari" onclick="prosesPencarian()">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>

            <div class="topik-pills" id="topikPills"></div>

            <div class="loader" id="loader">
                <i class="fas fa-circle-notch"></i>
                <p style="margin-top: 10px; font-weight: 600;" id="loaderText">Memuat data...</p>
            </div>

            <!-- List Hadis -->
            <div class="hadis-list" id="hadisList"></div>
        </div>
    </div>

    </div>

    <!-- ============ JAVASCRIPT ============ -->
    <script>
        const API_BASE = 'https://hadis-api-id.vercel.app';

        const dataKitab = [{
                id: 'bukhari',
                name: 'Sahih Bukhari',
                arab: 'صحيح البخاري',
                total: 6638,
                icon: 'fa-book-open',
                gradient: 'linear-gradient(135deg, #059669, #047857)'
            },
            {
                id: 'muslim',
                name: 'Sahih Muslim',
                arab: 'صحيح مسلم',
                total: 4930,
                icon: 'fa-book',
                gradient: 'linear-gradient(135deg, #2563eb, #1d4ed8)'
            },
            {
                id: 'abu-dawud',
                name: 'Sunan Abu Dawud',
                arab: 'سنن أبي داود',
                total: 4419,
                icon: 'fa-book-quran',
                gradient: 'linear-gradient(135deg, #7c3aed, #6d28d9)'
            },
            {
                id: 'tirmidzi',
                name: 'Sunan Tirmidzi',
                arab: 'جامع الترمذي',
                total: 3625,
                icon: 'fa-book-bookmark',
                gradient: 'linear-gradient(135deg, #d97706, #b45309)'
            },
            {
                id: 'nasai',
                name: 'Sunan An-Nasai',
                arab: 'سنن النسائي',
                total: 5364,
                icon: 'fa-scroll',
                gradient: 'linear-gradient(135deg, #dc2626, #b91c1c)'
            },
            {
                id: 'ibnu-majah',
                name: 'Sunan Ibnu Majah',
                arab: 'سنن ابن ماجه',
                total: 4285,
                icon: 'fa-book-open-reader',
                gradient: 'linear-gradient(135deg, #0891b2, #0e7490)'
            },
            {
                id: 'ahmad',
                name: 'Musnad Ahmad',
                arab: 'مسند أحمد',
                total: 4305,
                icon: 'fa-layer-group',
                gradient: 'linear-gradient(135deg, #65a30d, #4d7c0f)'
            },
            {
                id: 'malik',
                name: 'Muwatta Malik',
                arab: 'موطأ مالك',
                total: 1587,
                icon: 'fa-scale-balanced',
                gradient: 'linear-gradient(135deg, #0d9488, #0f766e)'
            },
            {
                id: 'darimi',
                name: 'Sunan Ad-Darimi',
                arab: 'سنن الدارمي',
                total: 2949,
                icon: 'fa-star-and-crescent',
                gradient: 'linear-gradient(135deg, #db2777, #be185d)'
            }
        ];

        const TOPIK_LIST = [
            { label: 'Semua', icon: 'fa-list', keyword: '' },
            { label: 'Iman', icon: 'fa-star', keyword: 'iman' },
            { label: 'Shalat', icon: 'fa-mosque', keyword: 'shalat' },
            { label: 'Puasa', icon: 'fa-moon', keyword: 'puasa' },
            { label: 'Zakat', icon: 'fa-hand-holding-heart', keyword: 'zakat' },
            { label: 'Haji', icon: 'fa-kaaba', keyword: 'haji' },
            { label: 'Ilmu', icon: 'fa-graduation-cap', keyword: 'ilmu' },
            { label: 'Nikah', icon: 'fa-ring', keyword: 'nikah' },
            { label: 'Jual Beli', icon: 'fa-store', keyword: 'jual' },
            { label: 'Akhlak', icon: 'fa-handshake', keyword: 'akhlak' },
            { label: 'Doa', icon: 'fa-hands-praying', keyword: 'doa' },
            { label: 'Hukum', icon: 'fa-scale-balanced', keyword: 'hukum' },
            { label: 'Surga', icon: 'fa-tree', keyword: 'surga' },
            { label: 'Neraka', icon: 'fa-fire', keyword: 'neraka' },
            { label: 'Malaikat', icon: 'fa-feather', keyword: 'malaikat' }
        ];

        let currentKitabId = '';
        let currentKitabName = '';
        let dataKitabCache = {}; // cache full hadis per kitab

        function renderTopikPills() {
            const container = document.getElementById('topikPills');
            if (!container) return;
            container.innerHTML = '';
            TOPIK_LIST.forEach(t => {
                const pill = document.createElement('button');
                pill.className = 'pill' + (t.keyword === '' ? ' active' : '');
                pill.innerHTML = `<i class="fas ${t.icon}"></i> ${t.label}`;
                pill.onclick = () => {
                    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
                    pill.classList.add('active');
                    document.getElementById('inputPencarian').value = t.keyword;
                    if (t.keyword) {
                        prosesPencarian();
                    } else {
                        // Semua: reload initial
                        document.getElementById('hadisList').innerHTML = '';
                        loadBanyakHadis(1, 10, false);
                    }
                };
                container.appendChild(pill);
            });
        }

        (function initKitab() {
            const grid = document.getElementById('kitabGrid');
            if (!grid) return;
            dataKitab.forEach(kitab => {
                const card = document.createElement('div');
                card.className = 'kitab-card';
                card.onclick = () => bukaKitab(kitab.id, kitab.name, kitab.total);
                card.innerHTML = `
                    <div class="kitab-cover" style="background: ${kitab.gradient};">
                        <div class="cover-pattern"></div>
                        <div class="cover-icon"><i class="fas ${kitab.icon}"></i></div>
                        <div class="cover-arab">${kitab.arab}</div>
                        <div class="cover-label">${kitab.total} Hadis</div>
                    </div>
                    <div class="kitab-info">
                        <h3>${kitab.name}</h3>
                        <p><i class="fas fa-bookmark" style="color: var(--primary);"></i> ${kitab.total} hadis tersedia</p>
                    </div>
                `;
                grid.appendChild(card);
            });
        })();

        function bukaKitab(id, name, total) {
            currentKitabId = id;
            currentKitabName = name;

            document.getElementById('detail-title').innerText = name;
            document.getElementById('detail-subtitle').innerText = `Total ${total} hadis tersedia`;
            document.getElementById('inputPencarian').value = '';
            document.getElementById('hadisList').innerHTML = '';

            document.getElementById('view-home').style.display = 'none';
            document.getElementById('view-detail').style.display = 'block';

            renderTopikPills();
            // Reset pills ke "Semua"
            document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
            const pillSemua = document.querySelector('.pill');
            if (pillSemua) pillSemua.classList.add('active');

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

            if (/^\d+$/.test(input)) {
                loadSatuHadis(input);
            } else {
                loadBanyakHadis(1, 10, true, input.toLowerCase());
            }
        }

        // --- FUNGSI AMBIL 1 HADIS (BY NOMOR) ---
        async function loadSatuHadis(nomor) {
            setLoading(true, "Mencari hadis nomor " + nomor + "...");
            try {
                const res = await fetch(`${API_BASE}/hadith/${currentKitabId}/${nomor}`);
                const data = await res.json();

                if (!data || !data.number) throw new Error("Gagal");

                renderHadis([data], false, '');
            } catch (err) {
                tampilError("Nomor hadis tidak ditemukan atau server sedang sibuk.");
            } finally {
                setLoading(false);
            }
        }

        async function ambilSemuaHadis() {
            if (dataKitabCache[currentKitabId]) {
                return dataKitabCache[currentKitabId];
            }
            // Cari total dari dataKitab
            const kitab = dataKitab.find(k => k.id === currentKitabId);
            const total = kitab ? kitab.total : 1000;
            setLoading(true, `Memuat ${total} hadis...`);

            try {
                const res = await fetch(`${API_BASE}/hadith/${currentKitabId}?page=1&limit=${total}`);
                const data = await res.json();
                if (!data || !data.items) throw new Error("Gagal");
                dataKitabCache[currentKitabId] = data.items;
                return data.items;
            } catch (e) {
                tampilError("Gagal memuat data dari server.");
                return null;
            } finally {
                setLoading(false);
            }
        }

        // --- FUNGSI AMBIL BANYAK HADIS ---
        async function loadBanyakHadis(start, end, isSearch, keyword = '') {
            if (isSearch) {
                // Search: ambil semua hadis, filter client-side
                setLoading(true, `Mencari "${keyword}" dari semua hadis...`);
                const semua = await ambilSemuaHadis();
                if (!semua) { setLoading(false); return; }

                const hasil = semua.filter(h =>
                    (h.id && h.id.toLowerCase().includes(keyword)) ||
                    (h.arab && h.arab.toLowerCase().includes(keyword))
                );

                if (hasil.length === 0) {
                    document.getElementById('hadisList').innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-search" style="font-size: 3rem; color: #cbd5e1; margin-bottom:15px; display:block;"></i>
                            Tidak ada hadis dengan kata "<b>${keyword}</b>" di kitab ini.
                        </div>`;
                    setLoading(false);
                    return;
                }

                renderHadis(hasil, true, keyword);
                setLoading(false);
                return;
            }

            // Initial load: ambil 20 hadis pertama
            setLoading(true, "Memuat hadis...");
            try {
                const res = await fetch(`${API_BASE}/hadith/${currentKitabId}?page=1&limit=20`);
                const data = await res.json();
                if (!data || !data.items || data.items.length === 0) {
                    tampilError("Gagal memuat data dari server.");
                    setLoading(false);
                    return;
                }
                renderHadis(data.items, false, '');
            } catch (e) {
                tampilError("Gagal terhubung ke server. Coba beberapa saat lagi.");
            } finally {
                setLoading(false);
            }
        }

        function pisahSanadMatan(teks) {
            // Coba pisah sanad dari matan di teks Indonesia
            const pola = /(Rasulullah\s*(?:shallallahu\s*['\u2018\u2019]alaihi\s*wasallam|SAW|saw|shalallahu\s*['\u2018\u2019]alaihi\s*wasallam)?\s*(?:shallallahu\s*['\u2018\u2019]alaihi\s*wasallam)?\s*bersabda\s*[:.,]?)/i;
            const cocok = teks.match(pola);
            if (cocok) {
                const idx = cocok.index + cocok[0].length;
                const sanad = teks.substring(0, idx).trim();
                const matan = teks.substring(idx).trim().replace(/^["\u201C\u201D\s]+|["\u201C\u201D\s]+$/g, '');
                return { sanad, matan };
            }
            // Fallback: cari "Nabi" atau "beliau bersabda"
            const pola2 = /(Nabi\s*(?:Muhammad)?\s*(?:SAW|saw)?\s*bersabda\s*[:.,]?)/i;
            const cocok2 = teks.match(pola2);
            if (cocok2) {
                const idx = cocok2.index + cocok2[0].length;
                const sanad = teks.substring(0, idx).trim();
                const matan = teks.substring(idx).trim().replace(/^["\u201C\u201D\s]+|["\u201C\u201D\s]+$/g, '');
                return { sanad, matan };
            }
            return { sanad: '', matan: teks.trim() };
        }

        function formatSanadHTML(sanad) {
            if (!sanad) return '';
            let html = sanad;
            // Highlight narrator names in brackets
            html = html.replace(/\[([^\]]+)\]/g, '<span class="narrator">$1</span>');
            // Add arrows between narrators
            html = html.replace(/;\s*/g, '; <span class="arrow">→</span> ');
            // Highlight "Rasulullah" and "Nabi"
            html = html.replace(/(Rasulullah|Nabi|beliau)/gi, '<span class="narrator">$1</span>');
            return html;
        }

        // --- RENDER HADIS KE HTML ---
        function renderHadis(dataArray, isSearch, keyword) {
            const listArea = document.getElementById('hadisList');
            listArea.innerHTML = '';

            if (isSearch) {
                const info = document.createElement('div');
                info.style.cssText = 'background: #ecfdf5; border-radius: 12px; padding: 12px 18px; font-size: 0.85rem; font-weight: 600; color: var(--primary-dark); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;';
                info.innerHTML = `<i class="fas fa-search"></i> Ditemukan ${dataArray.length} hadis untuk "<b>${keyword}</b>"`;
                listArea.appendChild(info);
            }

            dataArray.forEach(hadis => {
                const { sanad, matan } = pisahSanadMatan(hadis.id);

                let teksMatan = matan;
                let teksSanad = sanad;
                if (isSearch && keyword) {
                    const regex = new RegExp(`(${keyword})`, 'gi');
                    teksMatan = teksMatan.replace(regex, `<span class="highlight">$1</span>`);
                    teksSanad = teksSanad.replace(regex, `<span class="highlight">$1</span>`);
                }

                const card = document.createElement('div');
                card.className = 'hadis-item';
                card.innerHTML = `
                    <div class="hadis-header">
                        <span><i class="fas fa-quote-right" style="margin-right: 6px;"></i> Hadis No. ${hadis.number}</span>
                        <span class="badge" style="background: var(--gold); color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">${currentKitabName}</span>
                    </div>
                    <div class="hadis-content">
                        <div class="arabic-text">${hadis.arab}</div>
                    </div>
                    <div class="hadis-actions">
                        <button class="btn-toggle toggle-terjemah" onclick="toggleSection(this, 'terjemah-${hadis.number}')">
                            <i class="fas fa-language"></i> Terjemah
                        </button>
                        ${teksSanad ? `<button class="btn-toggle toggle-sanad" onclick="toggleSection(this, 'sanad-${hadis.number}')">
                            <i class="fas fa-sitemap"></i> Sanad
                        </button>` : ''}
                    </div>
                    ${teksSanad ? `
                    <div class="hadis-section" id="sanad-${hadis.number}">
                        <span class="section-label sanad-label"><i class="fas fa-chain"></i> Sanad (Rantai Perawi)</span>
                        <div class="sanad-text">${formatSanadHTML(teksSanad)}</div>
                    </div>` : ''}
                    <div class="hadis-section" id="terjemah-${hadis.number}">
                        <span class="section-label arti-label"><i class="fas fa-book-open"></i> Arti / Kandungan</span>
                        <div>${teksMatan}</div>
                    </div>
                `;
                listArea.appendChild(card);
            });
        }

        function toggleSection(btn, sectionId) {
            const section = document.getElementById(sectionId);
            if (!section) return;
            const isOpen = section.classList.toggle('open');
            btn.classList.toggle('active', isOpen);
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