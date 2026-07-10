<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Ambil parameter ID dari URL (default 1 jika kosong)
$doa_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Doa - Hifzly</title>
    <!-- Font Arab -->
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

        /* Custom Header Baca */
        .read-header {
            background: var(--card-bg); position: sticky; top: 0; z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 15px 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .h-btn { color: var(--text-muted); font-size: 1.3rem; cursor: pointer; text-decoration: none; transition: 0.2s; }
        .h-btn:hover { color: var(--primary); }
        .header-title { font-weight: 700; color: var(--dark); font-size: 1.1rem; }

        .container { padding: 20px; max-width: 800px; margin: 0 auto; }

        /* Card Visual & Detail Doa */
        .doa-detail-card {
            background: var(--card-bg); border-radius: 20px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--border);
            display: none; /* Sembunyikan sebelum API terload */
        }
        
        .doa-hero-img { width: 100%; height: 250px; object-fit: cover; background: #e2e8f0; }
        
        .doa-body { padding: 25px; }
        .d-title { font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 25px; text-align: center; border-bottom: 2px dashed var(--border); padding-bottom: 15px; }
        
        .d-arab { font-family: 'Scheherazade New', serif; font-size: 2.5rem; color: #111827; text-align: center; line-height: 2.2; margin-bottom: 20px; direction: rtl; }
        .d-latin { font-size: 1.1rem; color: var(--primary); margin-bottom: 15px; font-style: italic; font-weight: 500; line-height: 1.6; text-align: center; }
        .d-arti { font-size: 1rem; color: #475569; margin-bottom: 25px; line-height: 1.7; text-align: center; }
        
        /* Tombol Aksi */
        .doa-actions { display: flex; gap: 15px; margin-top: 30px; }
        .btn-action {
            flex: 1; padding: 15px; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;
            display: flex; justify-content: center; align-items: center; gap: 8px; font-size: 1rem; transition: 0.2s;
        }
        .btn-copy { background: var(--primary-light); color: var(--primary); }
        .btn-copy:hover { background: var(--primary); color: white; }
        .btn-share { background: var(--dark); color: white; }
        .btn-share:hover { opacity: 0.9; }

        /* Loading & Alert */
        #loading { text-align: center; padding: 60px 20px; color: var(--primary); font-size: 1.1rem; font-weight: 600; }
        
        .islamic-alert {
            position: fixed; top: -100px; left: 50%; transform: translateX(-50%);
            background: var(--primary); color: white; padding: 14px 24px; border-radius: 50px;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4); display: flex; align-items: center; gap: 12px;
            z-index: 9999; transition: 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55); width: max-content;
        }
        .islamic-alert.show { top: 30px; }
        .ia-icon { color: #fbbf24; font-size: 1.2rem; }
    </style>
</head>
<body>

    <div class="read-header">
        <div class="header-left">
            <a href="doa.php" class="h-btn"><i class="fas fa-arrow-left"></i></a>
            <div class="header-title">Detail Doa</div>
        </div>
    </div>

    <div class="container">
        <div id="loading"><i class="fas fa-spinner fa-spin"></i> Menarik doa dari server...</div>
        
        <div class="doa-detail-card" id="doaCard">
            <!-- Gambar Visual Dinamis -->
            <img src="" alt="Visual Doa" class="doa-hero-img" id="d-img">
            
            <div class="doa-body">
                <div class="d-title" id="d-title">Memuat...</div>
                <div class="d-arab" id="d-arab"></div>
                <div class="d-latin" id="d-latin"></div>
                <div class="d-arti" id="d-arti"></div>
                
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

    <!-- Alert Kapsul -->
    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-check-circle ia-icon"></i>
        <div style="font-size:0.95rem; font-weight:600;" id="alertMsg">Berhasil disalin!</div>
    </div>

    <script>
        const doaId = <?= $doa_id ?>;
        const API_URL_DETAIL = `https://equran.id/api/doa/${doaId}`;
        
        let currentDoa = null;

        async function fetchDoaDetail() {
            try {
                const response = await fetch(API_URL_DETAIL);
                const json = await response.json();
                
                currentDoa = json.data || json; // Antisipasi format json
                
                document.getElementById('loading').style.display = 'none';
                
                // Jika API tidak menemukan doa (kosong atau error)
                if(!currentDoa || Object.keys(currentDoa).length === 0) {
                    document.querySelector('.container').innerHTML = `
                        <div style="text-align:center; padding: 50px; color:#64748b;">
                            <i class="fas fa-box-open" style="font-size:3rem; margin-bottom:15px;"></i><br>
                            Maaf, doa tidak ditemukan di server API.
                        </div>`;
                    return;
                }
                
                // Menerapkan data ke HTML
                const judul = currentDoa.doa || currentDoa.judul || currentDoa.nama || 'Doa Harian';
                document.getElementById('d-title').innerText = judul;
                document.getElementById('d-arab').innerText = currentDoa.ayat || currentDoa.arab || '';
                document.getElementById('d-latin').innerText = currentDoa.latin || '';
                document.getElementById('d-arti').innerText = `"${currentDoa.artinya || currentDoa.arti || ''}"`;
                
                // Set gambar (menggunakan Unsplash dengan ID sebagai penentu seed image agar konsisten)
                document.getElementById('d-img').src = `https://images.unsplash.com/photo-1564507004663-b6dfb3c824d5?q=80&w=800&auto=format&fit=crop&sig=${doaId}`;
                
                document.getElementById('doaCard').style.display = 'block';

            } catch (error) {
                document.getElementById('loading').innerHTML = '<span style="color:red;">Gagal menarik data doa. Pastikan koneksi internet stabil.</span>';
            }
        }

        function copyDoa() {
            if(!currentDoa) return;
            const judul = document.getElementById('d-title').innerText;
            const arab = document.getElementById('d-arab').innerText;
            const latin = document.getElementById('d-latin').innerText;
            const arti = document.getElementById('d-arti').innerText;

            const textToCopy = `*${judul}*\n\n${arab}\n\n_${latin}_\n\nArtinya:\n${arti}\n\nDibagikan dari aplikasi Hifzly`;
            
            navigator.clipboard.writeText(textToCopy).then(() => {
                showAlert("Teks doa berhasil disalin!");
            });
        }

        function shareDoa() {
            if(!currentDoa) return;
            const judul = document.getElementById('d-title').innerText;
            const arab = document.getElementById('d-arab').innerText;
            const latin = document.getElementById('d-latin').innerText;
            const arti = document.getElementById('d-arti').innerText;

            const textToShare = `*${judul}*\n\n${arab}\n\n_${latin}_\n\nArtinya:\n${arti}\n\nDibagikan via Hifzly`;

            if (navigator.share) {
                navigator.share({
                    title: judul,
                    text: textToShare,
                }).catch(err => console.log('Share gagal', err));
            } else {
                copyDoa(); // Fallback jika browser laptop
                showAlert("Disalin! (Fitur Share tidak didukung di browser ini)");
            }
        }

        function showAlert(msg) {
            document.getElementById('alertMsg').innerText = msg;
            const alertEl = document.getElementById('customAlert');
            alertEl.classList.add('show');
            setTimeout(() => alertEl.classList.remove('show'), 3000);
        }

        // Panggil fungsi
        fetchDoaDetail();
    </script>
</body>
</html>