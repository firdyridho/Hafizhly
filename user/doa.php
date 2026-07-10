<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
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

        .doa-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        
        .doa-card {
            background: var(--card-bg); border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--border);
            cursor: pointer; transition: 0.3s; position: relative;
        }
        .doa-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(5,150,105,0.1); border-color: var(--primary-light); }
        .doa-img { width: 100%; height: 140px; object-fit: cover; background-color: var(--border); }
        .doa-title-card { padding: 15px; font-weight: 700; font-size: 1.1rem; color: var(--dark); text-align: center; }

        #loading-state, #empty-state { text-align: center; padding: 40px; color: var(--text-muted); }
        #empty-state { display: none; }

        .modal {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px); z-index: 1050;
            align-items: center; justify-content: center; padding: 20px;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .modal.show { display: flex; opacity: 1; }
        .modal-content {
            background: var(--card-bg); width: 100%; max-width: 600px; border-radius: 24px;
            padding: 30px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-height: 85vh; overflow-y: auto; transform: translateY(20px); transition: 0.3s ease;
        }
        .modal.show .modal-content { transform: translateY(0); }
        
        .modal-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 2px dashed var(--border); padding-bottom: 15px; }
        .m-title { font-size: 1.3rem; font-weight: 700; color: var(--primary); }
        .close-btn { font-size: 1.5rem; cursor: pointer; color: var(--text-muted); background: none; border: none; }
        
        .doa-arab { font-family: 'Scheherazade New', serif; font-size: 2.2rem; color: #111827; text-align: right; line-height: 2.2; margin-bottom: 15px; direction: rtl; }
        .doa-latin { font-size: 1rem; color: var(--primary); margin-bottom: 10px; font-style: italic; font-weight: 500; line-height: 1.5; }
        .doa-arti { font-size: 0.95rem; color: #475569; margin-bottom: 25px; line-height: 1.6; }
        
        .doa-actions { display: flex; gap: 10px; }
        .btn-action {
            flex: 1; padding: 14px; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;
            display: flex; justify-content: center; align-items: center; gap: 8px; font-size: 0.95rem; transition: 0.2s;
        }
        .btn-copy { background: var(--primary-light); color: var(--primary); }
        .btn-copy:hover { background: var(--primary); color: white; }
        .btn-share { background: var(--dark); color: white; }
        .btn-share:hover { opacity: 0.9; }

        .islamic-alert {
            position: fixed; top: -100px; left: 50%; transform: translateX(-50%);
            background: var(--primary); color: white; padding: 14px 24px; border-radius: 50px;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4); display: flex; align-items: center; gap: 12px;
            z-index: 9999; transition: 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55); width: max-content;
        }
        .islamic-alert.show { top: 30px; }
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
            <input type="text" id="searchInput" placeholder="Cari doa..." autocomplete="off">
        </div>

        <div id="loading-state"><i class="fas fa-spinner fa-spin"></i> Mengambil daftar doa...</div>
        <div class="doa-grid" id="doa-container"></div>
        <div id="empty-state"><i class="fas fa-box-open" style="font-size:3rem; margin-bottom:10px;"></i><br>Doa tidak ditemukan.</div>
    </div>

    <!-- Alert Kapsul -->
    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-check-circle" style="color: #fbbf24;"></i>
        <div style="font-size:0.95rem; font-weight:600;" id="alertMsg">Teks disalin!</div>
    </div>

    <!-- Modal Detail Doa -->
    <div class="modal" id="modalDetail" onclick="closeModal(event)">
        <div class="modal-content">
            <div class="modal-header">
                <div class="m-title" id="m-title">Memuat...</div>
                <button class="close-btn" onclick="closeModalBtn()"><i class="fas fa-times"></i></button>
            </div>
            
            <div id="m-loading" style="text-align:center; padding: 20px; color:var(--primary);"><i class="fas fa-spinner fa-spin"></i> Menarik detail doa...</div>
            
            <div id="m-body" style="display:none;">
                <div class="doa-arab" id="m-arab"></div>
                <div class="doa-latin" id="m-latin"></div>
                <div class="doa-arti" id="m-arti"></div>
                
                <div class="doa-actions">
                    <button class="btn-action btn-copy" onclick="copyDoaData()">
                        <i class="fas fa-copy"></i> Salin
                    </button>
                    <button class="btn-action btn-share" onclick="shareDoaData()">
                        <i class="fas fa-share-nodes"></i> Bagikan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script>
        // Set Base URL dari EQuran ID
        const API_BASE_URL = 'https://equran.id';
        
        let allDoaList = [];
        let currentDetailDoa = null;

        const container = document.getElementById('doa-container');
        const loadingState = document.getElementById('loading-state');
        const emptyState = document.getElementById('empty-state');
        const searchInput = document.getElementById('searchInput');

        // Mengambil semua doa
        async function fetchAllDoa() {
            try {
                const response = await fetch(`${API_BASE_URL}/api/doa`); 
                const json = await response.json();
                
                // Menyesuaikan jika API equran me-return array langsung atau di dalam object 'data'
                allDoaList = json.data || json; 
                
                loadingState.style.display = 'none';
                renderList(allDoaList);
            } catch (error) {
                loadingState.innerHTML = '<span style="color:red;">Gagal terhubung ke server API. Periksa koneksi internet Anda.</span>';
            }
        }

        // Render List Grid
        function renderList(data) {
            container.innerHTML = '';
            
            if(data.length === 0) {
                emptyState.style.display = 'block';
                return;
            }
            emptyState.style.display = 'none';

            data.forEach((doa, index) => {
                // Gambar Acak Islami berdasarkan ID agar selalu sama untuk doa yang sama
                const doaId = doa.id || index;
                const randomImg = `https://images.unsplash.com/photo-1564507004663-b6dfb3c824d5?q=80&w=400&auto=format&fit=crop&sig=${doaId}`;
                
                // Biasanya judul di equran.id menggunakan key 'doa'
                const judul = doa.doa || doa.judul || doa.nama || 'Doa Harian';

                const card = document.createElement('div');
                card.className = 'doa-card';
                // Jika API punya ID spesifik, gunakan itu. Jika tidak, gunakan index.
                const detailParam = doa.id ? doa.id : (index + 1); 
                card.onclick = () => openDetailModal(detailParam, judul);
                
                card.innerHTML = `
                    <img src="${randomImg}" alt="Visual Doa" class="doa-img" loading="lazy">
                    <div class="doa-title-card">${judul}</div>
                `;
                container.appendChild(card);
            });
        }

        // Search Realtime
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const filtered = allDoaList.filter(doa => {
                const judul = (doa.doa || doa.judul || doa.nama || '').toLowerCase();
                return judul.includes(query);
            });
            renderList(filtered);
        });

        // Fetch Detail Spesifik
        async function openDetailModal(id, judul) {
            const modal = document.getElementById('modalDetail');
            document.getElementById('m-title').innerText = judul;
            document.getElementById('m-body').style.display = 'none';
            document.getElementById('m-loading').style.display = 'block';
            
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('show'), 10);

            try {
                const response = await fetch(`${API_BASE_URL}/api/doa/${id}`);
                const json = await response.json();
                
                currentDetailDoa = json.data || json;

                // Memasukkan teks Arab, Latin, dan Arti (Terjemahan)
                // Disesuaikan dengan key umum dari equran.id
                document.getElementById('m-arab').innerText = currentDetailDoa.ayat || currentDetailDoa.arab || '';
                document.getElementById('m-latin').innerText = currentDetailDoa.latin || '';
                document.getElementById('m-arti').innerText = `"${currentDetailDoa.artinya || currentDetailDoa.arti || currentDetailDoa.terjemah || ''}"`;
                
                document.getElementById('m-loading').style.display = 'none';
                document.getElementById('m-body').style.display = 'block';
            } catch(e) {
                document.getElementById('m-loading').innerHTML = '<span style="color:red;">Gagal menarik detail dari server.</span>';
            }
        }

        function closeModalBtn() {
            const modal = document.getElementById('modalDetail');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }
        function closeModal(e) {
            if(e.target.id === 'modalDetail') closeModalBtn();
        }

        // Fungsi Salin
        function copyDoaData() {
            if(!currentDetailDoa) return;
            const judul = document.getElementById('m-title').innerText;
            const arab = document.getElementById('m-arab').innerText;
            const latin = document.getElementById('m-latin').innerText;
            const arti = document.getElementById('m-arti').innerText;

            const textToCopy = `*${judul}*\n\n${arab}\n\n_${latin}_\n\nArtinya:\n${arti}\n\nDibagikan dari Hifzly`;
            
            navigator.clipboard.writeText(textToCopy).then(() => {
                showAlert("Teks doa berhasil disalin!");
            });
        }

        // Fungsi Bagikan
        function shareDoaData() {
            if(!currentDetailDoa) return;
            const judul = document.getElementById('m-title').innerText;
            const arab = document.getElementById('m-arab').innerText;
            const latin = document.getElementById('m-latin').innerText;
            const arti = document.getElementById('m-arti').innerText;

            const textToShare = `*${judul}*\n\n${arab}\n\n_${latin}_\n\nArtinya:\n${arti}\n\nDibagikan via Hifzly`;

            if (navigator.share) {
                navigator.share({
                    title: judul,
                    text: textToShare,
                }).catch(err => console.log('Error sharing', err));
            } else {
                copyDoaData();
                showAlert("Disalin! (Fitur Share tidak didukung di browser ini)");
            }
        }

        function showAlert(msg) {
            document.getElementById('alertMsg').innerText = msg;
            const alertEl = document.getElementById('customAlert');
            alertEl.classList.add('show');
            setTimeout(() => alertEl.classList.remove('show'), 3000);
        }

        fetchAllDoa();
    </script>
</body>
</html>