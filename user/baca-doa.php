<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$doa_id = isset($_GET['id']) ? $_GET['id'] : '1';
$doa_id = htmlspecialchars($doa_id, ENT_QUOTES, 'UTF-8');
// src dikirim dari doa.php supaya baca-doa.php tau harus coba sumber mana duluan
// (biar id-nya konsisten dengan list yang barusan dibuka user)
$src = isset($_GET['src']) && $_GET['src'] === 'legacy' ? 'legacy' : 'equran';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Doa - Hifzly</title>
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

        .h-btn {
            color: var(--text-muted);
            font-size: 1.3rem;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }

        .h-btn:hover {
            color: var(--primary);
        }

        .header-title {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.1rem;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* ============ Skeleton loading ============ */
        .skeleton-detail {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border);
            padding: 25px;
        }

        .sk-line {
            height: 14px;
            border-radius: 6px;
            background: linear-gradient(90deg, #eef2f6 25%, #e2e8f0 37%, #eef2f6 63%);
            background-size: 400% 100%;
            animation: shimmer 1.4s ease infinite;
            margin: 0 auto 14px;
        }

        @keyframes shimmer {
            0% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0 50%;
            }
        }

        /* ============ Detail card ============ */
        .doa-detail-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            display: none;
            opacity: 0;
            animation: fadeUp 0.45s ease forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .doa-body {
            padding: 25px;
        }

        .d-grup {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-light);
            padding: 4px 12px;
            border-radius: 20px;
            margin: 0 auto 14px;
            display: table;
        }

        .d-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 25px;
            text-align: center;
            border-bottom: 2px dashed var(--border);
            padding-bottom: 15px;
        }

        .d-arab {
            font-family: 'Scheherazade New', serif;
            font-size: 2.5rem;
            color: #111827;
            text-align: center;
            line-height: 2.2;
            margin-bottom: 20px;
            direction: rtl;
        }

        .d-latin {
            font-size: 1.1rem;
            color: var(--primary);
            margin-bottom: 15px;
            font-style: italic;
            font-weight: 500;
            line-height: 1.6;
            text-align: center;
        }

        .d-arti {
            font-size: 1rem;
            color: #475569;
            margin-bottom: 10px;
            line-height: 1.7;
            text-align: center;
        }

        .d-sumber {
            font-size: 0.82rem;
            color: var(--text-muted);
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
        }

        .doa-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-action {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
            transition: 0.2s;
        }

        .btn-copy {
            background: var(--primary-light);
            color: var(--primary);
        }

        .btn-copy:hover {
            background: var(--primary);
            color: white;
        }

        .btn-share {
            background: var(--dark);
            color: white;
        }

        .btn-share:hover {
            opacity: 0.9;
        }

        /* ============ Error state ============ */
        .state-box {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .state-box i {
            font-size: 2.2rem;
            margin-bottom: 12px;
            display: block;
            color: #ef4444;
        }

        .retry-btn,
        .back-list-btn {
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
            text-decoration: none;
            margin-right: 8px;
        }

        .back-list-btn {
            background: var(--dark);
        }

        .retry-btn:hover {
            background: var(--primary-dark);
        }

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
            color: #fbbf24;
            font-size: 1.2rem;
        }
    </style>
</head>

<body>

    <?php
    // TODO: sesuaikan path ini kalau lokasi nav.php di project kamu beda.
    @include '../includes/nav.php';
    ?>

    <div class="read-header">
        <div class="header-left">
            <a href="doa.php" class="h-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="header-title">Detail Doa</div>
        </div>
    </div>

    <div class="container">

        <div id="skeletonBox" class="skeleton-detail">
            <div class="sk-line" style="width:50%;height:18px;"></div>
            <div class="sk-line" style="width:80%;height:40px;"></div>
            <div class="sk-line" style="width:60%;height:40px;"></div>
            <div class="sk-line" style="width:70%;"></div>
            <div class="sk-line" style="width:65%;"></div>
        </div>

        <div id="errorBox"></div>

        <div class="doa-detail-card" id="doaCard">
            <div class="doa-body">
                <div class="d-grup" id="d-grup" style="display:none;"></div>
                <div class="d-title" id="d-title">Memuat...</div>
                <div class="d-arab" id="d-arab"></div>
                <div class="d-latin" id="d-latin"></div>
                <div class="d-arti" id="d-arti"></div>
                <div class="d-sumber" id="d-sumber" style="display:none;"></div>
                <div class="doa-actions">
                    <button class="btn-action btn-copy" onclick="copyDoa()">
                        <i class="fas fa-copy"></i> Salin
                    </button>
                    <button class="btn-action btn-share" onclick="shareDoa()">
                        <i class="fas fa-share-nodes"></i> Bagikan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-check-circle ia-icon"></i>
        <div style="font-size:0.95rem; font-weight:600;" id="alertMsg">Berhasil disalin!</div>
    </div>

    <script>
        const doaId = <?= json_encode($doa_id) ?>;
        const preferredSrc = <?= json_encode($src) ?>; // 'equran' atau 'legacy', dikirim dari doa.php

        // ============================================================
        //  SUMBER DATA DOA (urutan coba mengikuti sumber yang dipakai
        //  di halaman daftar doa, supaya ID-nya nyambung ke doa yang benar)
        // ============================================================
        const API_EQURAN_DETAIL = `https://equran.id/api/doa/${encodeURIComponent(doaId)}`;
        const API_EQURAN_LIST = `https://equran.id/api/doa`;
        const API_LEGACY_LIST = `https://doa-doa-api-ahmadramadhan.fly.dev/api/doa`;

        let currentDoa = null;

        function extractArray(json) {
            if (Array.isArray(json)) return json;
            if (json && Array.isArray(json.data)) return json.data;
            if (json && json.data && Array.isArray(json.data.doa)) return json.data.doa;
            if (json && Array.isArray(json.doa)) return json.doa;
            return [];
        }

        function extractObject(json) {
            if (json && json.data && !Array.isArray(json.data)) return json.data;
            if (json && typeof json === 'object' && !Array.isArray(json)) return json;
            return null;
        }

        function pick(obj, keys, fallback = '') {
            for (const k of keys) {
                if (obj && obj[k] !== undefined && obj[k] !== null && obj[k] !== '') return obj[k];
            }
            return fallback;
        }

        function normalizeDoa(item) {
            return {
                judul: pick(item, ['judul', 'nama', 'doa', 'title'], 'Doa'),
                arab: pick(item, ['arab', 'teks_arab', 'ar', 'lafadz', 'lafaz', 'ayat']),
                latin: pick(item, ['latin', 'teks_latin', 'transliterasi']),
                arti: pick(item, ['artinya', 'arti', 'terjemah', 'terjemahan', 'terjemahan_id', 'idn']),
                grup: pick(item, ['grup', 'kategori', 'category']),
                sumber: pick(item, ['sumber', 'referensi', 'source'])
            };
        }

        async function fetchJson(url) {
            const res = await fetch(url);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        }

        async function findInList(url) {
            const json = await fetchJson(url);
            const arr = extractArray(json);
            const found = arr.find(item => String(pick(item, ['id', 'ID', 'no'], '')) === String(doaId));
            return found || arr[parseInt(doaId, 10) - 1] || null;
        }

        async function fetchDoaDetail() {
            try {
                let raw = null;

                const attempts = preferredSrc === 'legacy' ?
                    [() => findInList(API_LEGACY_LIST), () => findInList(API_EQURAN_LIST)] :
                    [async () => {
                        const json = await fetchJson(API_EQURAN_DETAIL);
                        return extractObject(json) || (extractArray(json)[0] ?? null);
                    }, () => findInList(API_EQURAN_LIST), () => findInList(API_LEGACY_LIST)];

                for (const attempt of attempts) {
                    try {
                        raw = await attempt();
                        if (raw) break;
                    } catch (e) {
                        console.warn('Percobaan sumber gagal, lanjut ke sumber berikutnya...', e);
                    }
                }

                if (!raw) throw new Error('Doa tidak ditemukan');

                currentDoa = normalizeDoa(raw);

                document.getElementById('skeletonBox').style.display = 'none';
                const card = document.getElementById('doaCard');
                card.style.display = 'block';

                document.getElementById('d-title').innerText = currentDoa.judul;
                document.getElementById('d-arab').innerText = currentDoa.arab;
                document.getElementById('d-latin').innerText = currentDoa.latin;
                document.getElementById('d-arti').innerText = currentDoa.arti;

                if (currentDoa.grup) {
                    const g = document.getElementById('d-grup');
                    g.innerText = currentDoa.grup;
                    g.style.display = 'table';
                }
                if (currentDoa.sumber) {
                    const s = document.getElementById('d-sumber');
                    s.innerText = 'Sumber: ' + currentDoa.sumber;
                    s.style.display = 'block';
                }

            } catch (error) {
                console.error('Error fetchDoaDetail:', error);
                document.getElementById('skeletonBox').style.display = 'none';
                document.getElementById('errorBox').innerHTML = `
                    <div class="state-box">
                        <i class="fas fa-triangle-exclamation"></i>
                        ${error.message === 'Doa tidak ditemukan' ? 'Doa tidak ditemukan.' : 'Gagal memuat doa. Periksa koneksi internet kamu.'}
                        <br>
                        <a href="doa.php" class="back-list-btn"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
                        <button class="retry-btn" onclick="location.reload()"><i class="fas fa-rotate-right"></i> Coba Lagi</button>
                    </div>`;
            }
        }

        function copyDoa() {
            if (!currentDoa) return;
            const judul = document.getElementById('d-title').innerText;
            const arab = document.getElementById('d-arab').innerText;
            const latin = document.getElementById('d-latin').innerText;
            const arti = document.getElementById('d-arti').innerText;

            const text = `*${judul}*\n\n${arab}\n\n_${latin}_\n\nArtinya:\n${arti}\n\nDibagikan dari Hifzly`;
            navigator.clipboard.writeText(text).then(() => showAlert('Teks doa berhasil disalin!'))
                .catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    showAlert('Teks doa berhasil disalin!');
                });
        }

        function shareDoa() {
            if (!currentDoa) return;
            const judul = document.getElementById('d-title').innerText;
            const arab = document.getElementById('d-arab').innerText;
            const latin = document.getElementById('d-latin').innerText;
            const arti = document.getElementById('d-arti').innerText;

            const text = `*${judul}*\n\n${arab}\n\n_${latin}_\n\nArtinya:\n${arti}\n\nDibagikan via Hifzly`;

            if (navigator.share) {
                navigator.share({
                    title: judul,
                    text: text
                }).catch(() => {});
            } else {
                copyDoa();
                showAlert('Disalin! (Browser tidak mendukung share)');
            }
        }

        function showAlert(msg) {
            document.getElementById('alertMsg').innerText = msg;
            const el = document.getElementById('customAlert');
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 3000);
        }

        fetchDoaDetail();
    </script>
</body>

</html>