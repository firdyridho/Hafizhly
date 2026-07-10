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

        .header {
            background: var(--card-bg);
            padding: 20px 20px 15px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title i {
            color: var(--primary);
        }

        .back-btn {
            color: var(--text-muted);
            font-size: 1.3rem;
            text-decoration: none;
        }

        .back-btn:hover {
            color: var(--primary);
        }

        .search-box {
            margin-top: 15px;
            display: flex;
            align-items: center;
            background: var(--bg);
            border-radius: 12px;
            padding: 0 15px;
            border: 1px solid var(--border);
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

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .doa-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
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
        }

        .doa-card:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.08);
            transform: translateY(-2px);
        }

        .doa-card .doa-nama {
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .doa-card .doa-arab {
            font-family: 'Scheherazade New', serif;
            font-size: 1.2rem;
            direction: rtl;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .doa-card .doa-arti {
            font-size: 0.85rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #loading {
            text-align: center;
            padding: 50px 0;
            color: var(--primary);
            font-weight: 600;
        }

        .not-found {
            text-align: center;
            color: var(--text-muted);
            padding: 30px 0;
        }

        @media (max-width: 500px) {
            .doa-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-top">
            <a href="../dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="header-title"><i class="fas fa-hands-praying"></i> Kumpulan Doa</div>
            <div style="width: 28px;"></div>
        </div>
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchDoa" placeholder="Cari doa..." onkeyup="filterDoa()">
        </div>
    </div>

    <div class="container">
        <div id="loading"><i class="fas fa-spinner fa-spin"></i> Memuat daftar doa...</div>
        <div class="doa-grid" id="doaGrid"></div>
    </div>

    <script>
        let semuaDoa = [];

        async function fetchDoaList() {
            try {
                const res = await fetch('https://doa-doa-api-ahmadramadhan.fly.dev/api/doa');
                if (!res.ok) throw new Error('Gagal mengambil data');
                const data = await res.json();
                semuaDoa = data; // array of doa objects
                renderDoa(semuaDoa);
            } catch (error) {
                document.getElementById('loading').innerHTML = 'Gagal memuat doa. Periksa koneksi.';
            }
        }

        function renderDoa(list) {
            const grid = document.getElementById('doaGrid');
            const loading = document.getElementById('loading');
            loading.style.display = 'none';

            if (list.length === 0) {
                grid.innerHTML = `<div class="not-found">Tidak ada doa yang cocok</div>`;
                return;
            }

            grid.innerHTML = list.map(doa => `
            <a href="baca-doa.php?id=${doa.id}" class="doa-card">
                <div class="doa-nama">${doa.judul || doa.nama || 'Doa'}</div>
                <div class="doa-arab">${doa.arab || ''}</div>
                <div class="doa-arti">${doa.artinya || doa.arti || ''}</div>
            </a>
        `).join('');
        }

        function filterDoa() {
            const keyword = document.getElementById('searchDoa').value.toLowerCase();
            const filtered = semuaDoa.filter(doa => {
                const judul = (doa.judul || doa.nama || '').toLowerCase();
                const arti = (doa.artinya || doa.arti || '').toLowerCase();
                return judul.includes(keyword) || arti.includes(keyword);
            });
            renderDoa(filtered);
        }

        fetchDoaList();
    </script>
</body>

</html>