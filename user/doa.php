<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kumpulan Doa - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
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

        /* ============ Page header (search + filter) ============ */
        .page-head {
            background: var(--card-bg);
            padding: 18px 20px 14px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .page-head-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .page-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i {
            color: var(--primary);
        }

        .page-count {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-light);
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
        }

        .search-box {
            margin-top: 14px;
            display: flex;
            align-items: center;
            background: var(--bg);
            border-radius: 12px;
            padding: 0 15px;
            border: 1.5px solid var(--border);
            transition: 0.2s;
        }

        .search-box:focus-within {
            border-color: var(--primary);
            background: #fff;
        }

        .search-box i {
            color: var(--text-muted);
        }

        .search-box input {
            border: none;
            background: transparent;
            padding: 12px 10px;
            width: 100%;
            font-size: 1rem;
            outline: none;
        }

        .chip-row {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 4px;
            scrollbar-width: none;
        }

        .chip-row::-webkit-scrollbar {
            display: none;
        }

        .chip {
            flex-shrink: 0;
            padding: 7px 16px;
            border-radius: 20px;
            background: var(--bg);
            border: 1.5px solid var(--border);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.15s;
            white-space: nowrap;
        }

        .chip:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .chip.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        /* ============ Content ============ */
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .doa-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .doa-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 18px 15px;
            border: 1px solid var(--border);
            transition: 0.2s;
            text-decoration: none;
            color: var(--dark);
            display: block;
            opacity: 0;
            animation: cardIn 0.45s ease forwards;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .doa-card:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.12);
            transform: translateY(-3px);
        }

        .doa-card .doa-nama {
            font-weight: 700;
            font-size: 1.02rem;
            color: var(--primary);
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .doa-card .doa-arab {
            font-family: 'Scheherazade New', serif;
            font-size: 1.25rem;
            direction: rtl;
            color: var(--dark);
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .doa-card .doa-arti {
            font-size: 0.83rem;
            color: var(--text-muted);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .doa-card .doa-grup {
            display: inline-block;
            margin-top: 10px;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-light);
            padding: 3px 9px;
            border-radius: 20px;
        }

        /* ============ Skeleton loading ============ */
        .skeleton-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 18px 15px;
            border: 1px solid var(--border);
        }

        .sk-line {
            height: 12px;
            border-radius: 6px;
            background: linear-gradient(90deg, #eef2f6 25%, #e2e8f0 37%, #eef2f6 63%);
            background-size: 400% 100%;
            animation: shimmer 1.4s ease infinite;
            margin-bottom: 10px;
        }

        @keyframes shimmer {
            0% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0 50%;
            }
        }

        /* ============ States ============ */
        .state-box {
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
        }

        .state-box i {
            font-size: 2.2rem;
            margin-bottom: 12px;
            display: block;
            color: var(--border);
        }

        .state-box.is-error i {
            color: #ef4444;
        }

        .retry-btn {
            margin-top: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .retry-btn:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 500px) {
            .doa-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <?php
    // TODO: sesuaikan path ini kalau lokasi nav.php di project kamu beda.
    // nav.php diasumsikan langsung mengeluarkan header atas + bottom nav (auto responsive).
    @include '../includes/nav.php';
    ?>

    <div class="page-head">
        <div class="page-head-top">
            <div class="page-title"><i class="fas fa-hands-praying"></i> Kumpulan Doa</div>
            <div class="page-count" id="doaCount">...</div>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchDoa" placeholder="Cari nama doa atau arti..." oninput="onSearchInput()">
        </div>
        <div class="chip-row" id="chipRow">
            <!-- Chip kategori/grup diisi otomatis lewat JS setelah data doa berhasil diambil -->
        </div>
    </div>

    <div class="container">
        <div class="doa-grid" id="doaGrid"></div>
    </div>

    <script>
        // ============================================================
        //  SUMBER DATA DOA
        //  1) EQuran.id  -> sumber utama (lebih lengkap & stabil)
        //  2) API lama (fly.dev) -> fallback otomatis kalau EQuran.id gagal
        // ============================================================
        const API_EQURAN = 'https://equran.id/api/doa';
        const API_FALLBACK = 'https://doa-doa-api-ahmadramadhan.fly.dev/api/doa';

        let semuaDoa = [];
        let activeGroup = 'Semua';
        let searchTimer = null;
        let sourceUsed = 'equran'; // dikirim ke baca-doa.php lewat query string biar detail konsisten

        // Ambil array data doa dari berbagai kemungkinan bentuk response
        function extractArray(json) {
            if (Array.isArray(json)) return json;
            if (json && Array.isArray(json.data)) return json.data;
            if (json && json.data && Array.isArray(json.data.doa)) return json.data.doa;
            if (json && Array.isArray(json.doa)) return json.doa;
            return [];
        }

        function pick(obj, keys, fallback = '') {
            for (const k of keys) {
                if (obj && obj[k] !== undefined && obj[k] !== null && obj[k] !== '') return obj[k];
            }
            return fallback;
        }

        // Menyeragamkan field dari API manapun (nama field belum tentu sama persis)
        function normalizeDoa(item, index) {
            return {
                id: pick(item, ['id', 'ID', 'no'], index + 1),
                judul: pick(item, ['judul', 'nama', 'doa', 'title'], 'Doa'),
                arab: pick(item, ['arab', 'teks_arab', 'ar', 'lafadz', 'lafaz', 'ayat']),
                latin: pick(item, ['latin', 'teks_latin', 'transliterasi']),
                arti: pick(item, ['artinya', 'arti', 'terjemah', 'terjemahan', 'terjemahan_id', 'idn']),
                grup: pick(item, ['grup', 'kategori', 'category'], 'Umum'),
                tag: Array.isArray(item.tag) ? item.tag : (Array.isArray(item.tags) ? item.tags : []),
                sumber: pick(item, ['sumber', 'referensi', 'source'])
            };
        }

        async function fetchJson(url) {
            const res = await fetch(url);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        }

        async function fetchDoaList() {
            renderSkeleton();
            try {
                let json;
                try {
                    json = await fetchJson(API_EQURAN);
                    sourceUsed = 'equran';
                } catch (e) {
                    console.warn('EQuran.id gagal, coba fallback...', e);
                    json = await fetchJson(API_FALLBACK);
                    sourceUsed = 'legacy';
                }

                const arr = extractArray(json);
                if (!arr.length) throw new Error('Data doa kosong');

                semuaDoa = arr.map(normalizeDoa);
                buildChips();
                renderDoa(semuaDoa);
                document.getElementById('doaCount').innerText = semuaDoa.length + ' doa';
            } catch (error) {
                console.error('Error fetchDoaList:', error);
                renderError();
            }
        }

        function buildChips() {
            const groups = ['Semua', ...new Set(semuaDoa.map(d => d.grup).filter(Boolean))];
            const chipRow = document.getElementById('chipRow');
            chipRow.innerHTML = groups.map(g => `
                <div class="chip ${g === activeGroup ? 'active' : ''}" data-group="${g}" onclick="setActiveGroup('${g.replace(/'/g, "\\'")}')">${g}</div>
            `).join('');
        }

        function setActiveGroup(group) {
            activeGroup = group;
            document.querySelectorAll('.chip').forEach(c => {
                c.classList.toggle('active', c.dataset.group === group);
            });
            applyFilter();
        }

        function onSearchInput() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applyFilter, 250);
        }

        function applyFilter() {
            const keyword = document.getElementById('searchDoa').value.toLowerCase().trim();
            const filtered = semuaDoa.filter(d => {
                const matchGroup = activeGroup === 'Semua' || d.grup === activeGroup;
                const matchKeyword = !keyword ||
                    d.judul.toLowerCase().includes(keyword) ||
                    d.arti.toLowerCase().includes(keyword) ||
                    d.tag.join(' ').toLowerCase().includes(keyword);
                return matchGroup && matchKeyword;
            });
            renderDoa(filtered);
        }

        function renderSkeleton() {
            const grid = document.getElementById('doaGrid');
            let html = '';
            for (let i = 0; i < 6; i++) {
                html += `
                <div class="skeleton-card">
                    <div class="sk-line" style="width:70%;height:16px;"></div>
                    <div class="sk-line" style="width:90%;height:20px;"></div>
                    <div class="sk-line" style="width:60%;"></div>
                </div>`;
            }
            grid.innerHTML = html;
        }

        function renderError() {
            document.getElementById('doaGrid').innerHTML = `
                <div class="state-box is-error">
                    <i class="fas fa-triangle-exclamation"></i>
                    Gagal memuat daftar doa.<br>Periksa koneksi internet kamu.
                    <br><button class="retry-btn" onclick="fetchDoaList()"><i class="fas fa-rotate-right"></i> Coba Lagi</button>
                </div>`;
            document.getElementById('doaCount').innerText = '0 doa';
        }

        function renderDoa(list) {
            const grid = document.getElementById('doaGrid');

            if (!list.length) {
                grid.innerHTML = `
                    <div class="state-box">
                        <i class="fas fa-magnifying-glass"></i>
                        Tidak ada doa yang cocok dengan pencarianmu.
                    </div>`;
                return;
            }

            grid.innerHTML = list.map((doa, i) => `
                <a href="baca-doa.php?id=${encodeURIComponent(doa.id)}&src=${sourceUsed}" class="doa-card" style="animation-delay:${Math.min(i, 10) * 0.04}s">
                    <div class="doa-nama">${escapeHtml(doa.judul)}</div>
                    <div class="doa-arab">${escapeHtml(doa.arab)}</div>
                    <div class="doa-arti">${escapeHtml(doa.arti)}</div>
                    <span class="doa-grup">${escapeHtml(doa.grup)}</span>
                </a>
            `).join('');
        }

        function escapeHtml(str) {
            return String(str || '').replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [m]));
        }

        fetchDoaList();
    </script>

</body>

</html>