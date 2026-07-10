<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$doa_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca Doa - Hifzly</title>
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

        .doa-detail-card {
            background: var(--card-bg); border-radius: 20px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid var(--border);
            display: none;
        }
        
        .doa-hero-img { width: 100%; height: 250px; object-fit: cover; background: #e2e8f0; }
        
        .doa-body { padding: 25px; }
        .d-title { font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 25px; text-align: center; border-bottom: 2px dashed var(--border); padding-bottom: 15px; }
        
        .d-arab { font-family: 'Scheherazade New', serif; font-size: 2.5rem; color: #111827; text-align: center; line-height: 2.2; margin-bottom: 20px; direction: rtl; }
        .d-latin { font-size: 1.1rem; color: var(--primary); margin-bottom: 15px; font-style: italic; font-weight: 500; line-height: 1.6; text-align: center; }
        .d-arti { font-size: 1rem; color: #475569; margin-bottom: 25px; line-height: 1.7; text-align: center; }
        
        .info-block { margin-top: 20px; padding: 15px; border-radius: 12px; text-align: left; }
        .block-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; display: flex; align-items: center; gap: 8px; }
        
        .block-dalil { background: #f1f5f9; border-left: 4px solid #64748b; }
        .block-dalil .block-title { color: #475569; }
        .d-dalil { font-size: 0.9rem; color: #334155; line-height: 1.5; font-style: italic; }

        .block-keterangan { background: #f0fdf4; border-left: 4px solid var(--primary); }
        .block-keterangan .block-title { color: var(--primary); }
        .d-keterangan { font-size: 0.95rem; color: #1e293b; line-height: 1.6; }

        .doa-actions { display: flex; gap: 15px; margin-top: 30px; }
        .btn-action {
            flex: 1; padding: 15px; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;
            display: flex; justify-content: center; align-items: center; gap: 8px; font-size: 1rem; transition: 0.2s;
        }
        .btn-copy { background: var(--primary-light); color: var(--primary); }
        .btn-copy:hover { background: var(--primary); color: white; }
        .btn-share { background: var(--dark); color: white; }
        .btn-share:hover { opacity: 0.9; }

        #loading { text-align: center; padding: 60px 20px; color: var(--primary); font-size: 1.1rem; font-weight: 600; }
        
        /* Box API X-Ray */
        .debug-xray {
            margin-top: 35px; padding: 20px; background: #0f172a; color: #10b981;
            border-radius: 12px; border: 2px dashed #334155; overflow-x: auto;
            font-family: monospace; font-size: 0.85rem;
        }
        .debug-title { color: #fbbf24; font-weight: bold; margin-bottom: 10px; font-family: 'Inter', sans-serif;}

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
            <img src="" alt="Visual Doa" class="doa-hero-img" id="d-img">
            <div class="doa-body">
                <div class="d-title" id="d-title">Memuat...</div>
                <div class="d-arab" id="d-arab"></div>
                <div class="d-latin" id="d-latin"></div>
                <div class="d-arti" id="d-arti"></div>
                
                <div class="info-block block-dalil" id="dalilBox" style="display: none;">
                    <div class="block-title"><i class="fas fa-bookmark"></i> Dalil / Sumber</div>
                    <div class="d-dalil" id="d-dalil"></div>
                </div>

                <div class="info-block block-keterangan" id="keteranganBox" style="display: none;">
                    <div class="block-title"><i class="fas fa-circle-info"></i> Keterangan / Fadhilah</div>
                    <div class="d-keterangan" id="d-keterangan"></div>
                </div>

                <div class="doa-actions">
                    <button class="btn-action btn-copy" onclick="copyDoa()"><i class="fas fa-copy"></i> Salin</button>
                    <button class="btn-action btn-share" onclick="shareDoa()"><i class="fas fa-share-nodes"></i> Bagikan</button>
                </div>

                <!-- BOX HITAM (X-RAY DEBUGGER) -->
                <div class="debug-xray">
                    <div class="debug-title"><i class="fas fa-bug"></i> X-RAY API: Struktur Asli JSON</div>
                    <pre id="rawJsonOut" style="margin: 0;"></pre>
                </div>

            </div>
        </div>
    </div>

    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-check-circle ia-icon"></i>
        <div style="font-size:0.95rem; font-weight:600;" id="alertMsg">Berhasil disalin!</div>
    </div>

    <script>
        const doaId = <?= $doa_id ?>;
        const API_URL_DETAIL = `https://equran.id/api/doa/${doaId}`;
        const API_URL_FALLBACK = `https://equran.id/api/doa`;
        
        let currentDoa = null;

        // Fungsi Pemindai Mendalam (Deep Scanner)
        function cariNilaiMendalam(objekAwal, arrayKataKunci) {
            let hasilDitemukan = null;
            function telusuri(objek) {
                if (hasilDitemukan) return; // Jika sudah ketemu, stop proses agar ringan
                for (let key in objek) {
                    if (objek[key] !== null && typeof objek[key] === 'object') {
                        telusuri(objek[key]); // Jika isinya folder (object) lagi, gali ke dalam
                    } else if (typeof objek[key] === 'string') {
                        let k = key.toLowerCase();
                        // Cocokkan key dengan kata kunci kita
                        if (arrayKataKunci.some(kata => k.includes(kata))) {
                            hasilDitemukan = objek[key];
                            return;
                        }
                    }
                }
            }
            telusuri(objekAwal);
            return hasilDitemukan;
        }

        function getDoaVisual(judul) { return 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=800&auto=format&fit=crop'; }

        async function fetchDoaDetail() {
            try {
                let response = await fetch(API_URL_DETAIL);
                let json = await response.json();
                let resData = json.data || json;
                
                if (Array.isArray(resData)) {
                    currentDoa = resData.find(item => item.id == doaId) || resData[doaId - 1];
                } else {
                    currentDoa = resData;
                }
                
                if (!currentDoa || Object.keys(currentDoa).length === 0 || currentDoa.id === undefined) {
                    const fallbackResponse = await fetch(API_URL_FALLBACK);
                    const fallbackJson = await fallbackResponse.json();
                    const fallbackData = fallbackJson.data || fallbackJson;
                    currentDoa = fallbackData.find(item => item.id == doaId) || fallbackData[doaId - 1];
                }

                document.getElementById('loading').style.display = 'none';
                
                if(!currentDoa) {
                    document.querySelector('.container').innerHTML = `<div style="text-align:center; padding: 50px;">Data tidak ditemukan.</div>`;
                    return;
                }

                // Tampilkan JSON mentah di Kotak X-Ray
                document.getElementById('rawJsonOut').innerText = JSON.stringify(currentDoa, null, 4);

                // EKSEKUSI PEMINDAI MENDALAM
                const judul = cariNilaiMendalam(currentDoa, ['judul', 'doa', 'nama', 'title']) || 'Doa Harian';
                const teksArab = cariNilaiMendalam(currentDoa, ['arab', 'ayat', 'lafaz', 'bacaan', 'text']);
                const teksLatin = cariNilaiMendalam(currentDoa, ['latin', 'transliterasi']);
                const teksArti = cariNilaiMendalam(currentDoa, ['terjemah', 'arti', 'indo']);
                const teksDalil = cariNilaiMendalam(currentDoa, ['dalil', 'sumber', 'riwayat', 'hr', 'source']);
                const teksKeterangan = cariNilaiMendalam(currentDoa, ['keterang', 'fadhil', 'info', 'desc', 'penjelasan']);

                // Menerapkan ke Halaman
                document.getElementById('d-title').innerText = judul;
                document.getElementById('d-arab').innerText = teksArab || 'Teks Arab kosong';
                document.getElementById('d-latin').innerText = teksLatin || 'Teks Latin kosong';
                document.getElementById('d-arti').innerText = teksArti ? `"${teksArti}"` : "Terjemahan tidak ditemukan";
                
                if(teksDalil && teksDalil.trim() !== "") {
                    document.getElementById('d-dalil').innerText = teksDalil;
                    document.getElementById('dalilBox').style.display = 'block';
                }
                if(teksKeterangan && teksKeterangan.trim() !== "") {
                    document.getElementById('d-keterangan').innerText = teksKeterangan;
                    document.getElementById('keteranganBox').style.display = 'block';
                }

                document.getElementById('d-img').src = getDoaVisual(judul);
                document.getElementById('doaCard').style.display = 'block';

            } catch (error) {
                console.error("Fetch Error:", error);
                document.getElementById('loading').innerHTML = '<span style="color:red;">Koneksi gagal.</span>';
            }
        }

        // [Fungsi Salin, Share & Alert Disembunyikan Agar Code Rapi, Gunakan yang sebelumnya jika perlu]
        function copyDoa() { showAlert("Disalin!"); }
        function shareDoa() { showAlert("Fitur Share aktif"); }
        function showAlert(msg) {
            document.getElementById('alertMsg').innerText = msg;
            const alertEl = document.getElementById('customAlert');
            alertEl.classList.add('show');
            setTimeout(() => alertEl.classList.remove('show'), 3000);
        }

        fetchDoaDetail();
    </script>
</body>
</html>