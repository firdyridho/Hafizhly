<?php
session_start();
require_once '../config/database.php';

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
    <title>Doa Harian - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669; --primary-light: #d1fae5;
            --dark: #1e293b; --text-muted: #64748b;
            --bg: #f8fafc; --card-bg: #ffffff; --border: #e2e8f0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg); color: var(--dark); padding-bottom: 90px; }

        .container { padding: 20px; max-width: 900px; margin: 0 auto; }
        
        .page-header { text-align: center; margin-bottom: 25px; }
        .page-title { font-size: 1.8rem; font-weight: 700; color: var(--primary); margin-bottom: 8px; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; }

        .search-box { position: relative; margin-bottom: 30px; }
        .search-box input {
            width: 100%; padding: 15px 20px 15px 50px; border-radius: 16px;
            border: 1px solid var(--border); font-size: 1rem; outline: none; transition: 0.3s;
        }
        .search-box input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }
        .search-box i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--text-muted); }

        .doa-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        
        .doa-card {
            background: var(--card-bg); border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--border);
            text-decoration: none; display: block; transition: 0.3s; position: relative;
        }
        .doa-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(5,150,105,0.1); border-color: var(--primary-light); }
        .doa-img { width: 100%; height: 140px; object-fit: cover; background-color: var(--border); }
        .doa-title-card { padding: 18px; font-weight: 700; font-size: 1.1rem; color: var(--dark); text-align: center; }

        #loading-state, #empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
        #empty-state { display: none; }
    </style>
</head>
<body>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Doa Harian</h1>
            <p class="page-subtitle">Senjata utama seorang mukmin</p>
        </div>

        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari doa (contoh: makan, tidur)..." autocomplete="off">
        </div>

        <div id="loading-state"><i class="fas fa-spinner fa-spin"></i> Mengambil daftar doa...</div>
        <div class="doa-grid" id="doa-container"></div>
        
        <div id="empty-state">
            <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:10px; color:#cbd5e1;"></i><br>
            Doa tidak ditemukan.
        </div>
    </div>

    <!-- Panggil Navigasi Bawah -->
    <?php include '../components/nav.php'; ?>

    <script>
        const API_URL = 'https://equran.id/api/doa';
        let allDoaList = [];

        const container = document.getElementById('doa-container');
        const loadingState = document.getElementById('loading-state');
        const emptyState = document.getElementById('empty-state');
        const searchInput = document.getElementById('searchInput');

        async function fetchAllDoa() {
            try {
                const response = await fetch(API_URL); 
                const json = await response.json();
                
                // Menyesuaikan struktur balasan API
                allDoaList = json.data || json; 
                
                loadingState.style.display = 'none';
                renderList(allDoaList);
            } catch (error) {
                loadingState.innerHTML = '<span style="color:red;">Gagal terhubung ke API EQuran.id. Periksa koneksi internet Anda.</span>';
            }
        }

        function renderList(data) {
            container.innerHTML = '';
            
            if(data.length === 0) {
                emptyState.style.display = 'block';
                return;
            }
            emptyState.style.display = 'none';

            data.forEach((doa, index) => {
                // Ekstrak data yang fleksibel
                const judul = doa.doa || doa.judul || doa.nama || 'Doa Harian';
                const doaId = doa.id ? doa.id : (index + 1); // Fallback ID jika tidak ada properti ID
                
                // Generate gambar acak untuk visual
                const randomImg = `https://images.unsplash.com/photo-1564507004663-b6dfb3c824d5?q=80&w=400&auto=format&fit=crop&sig=${doaId}`;

                const card = document.createElement('a'); // Gunakan A tag agar langsung pindah halaman
                card.href = `baca-doa.php?id=${doaId}`;
                card.className = 'doa-card';
                card.innerHTML = `
                    <img src="${randomImg}" alt="Visual Doa" class="doa-img" loading="lazy">
                    <div class="doa-title-card">${judul}</div>
                `;
                container.appendChild(card);
            });
        }

        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const filtered = allDoaList.filter(doa => {
                const judul = (doa.doa || doa.judul || doa.nama || '').toLowerCase();
                return judul.includes(query);
            });
            renderList(filtered);
        });

        fetchAllDoa();
    </script>
</body>
</html>