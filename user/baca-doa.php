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

        .doa-detail-card {
            <<<<<<< HEAD background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            display: none;
        }

        .doa-hero-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #e2e8f0;
        }

        .doa-body {
            padding: 25px;
        }

        .d-title {
            font-size: 1.5rem;
            font-weight: 700;
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
            margin-bottom: 25px;
            line-height: 1.7;
            text-align: center;
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
            =======background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            display: none;
        }

        .doa-hero-img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            background: #e2e8f0;
        }

        .doa-body {
            padding: 25px;
        }

        .d-title {
            font-size: 1.5rem;
            font-weight: 700;
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
            margin-bottom: 25px;
            line-height: 1.7;
            text-align: center;
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
            >>>>>>>9cdfc6796284d7c7ad208d74ce56f045a8b0297f
        }

        <<<<<<< HEAD=======<<<<<<< HEAD #loading {
            text-align: center;
            padding: 60px 20px;
            color: var(--primary);
            font-size: 1.1rem;
            font-weight: 600;
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
            =======

            /* Loading & Alert */
            >>>>>>>06eef282c22e831801132e0c89d1bf6eec6cdac2 #loading {
                text-align: center;
                padding: 60px 20px;
                color: var(--primary);
                font-size: 1.1rem;
                font-weight: 600;
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
                >>>>>>>9cdfc6796284d7c7ad208d74ce56f045a8b0297f
            }
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
            <<<<<<< HEAD
                <img src="" alt="Visual Doa" class="doa-hero-img" id="d-img">
                =======
                <<<<<<< HEAD
                    <img src="" alt="Visual Doa" class="doa-hero-img" id="d-img">
                    =======
                    <!-- Gambar Visual (lebih tematik) -->
                    <img src="" alt="Visual Doa" class="doa-hero-img" id="d-img">

                    >>>>>>> 9cdfc6796284d7c7ad208d74ce56f045a8b0297f
                    >>>>>>> 06eef282c22e831801132e0c89d1bf6eec6cdac2
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

    <div class="islamic-alert" id="customAlert">
        <i class="fas fa-check-circle ia-icon"></i>
        <div style="font-size:0.95rem; font-weight:600;" id="alertMsg">Berhasil disalin!</div>
    </div>

    <script>
        const doaId = <?= $doa_id ?>; <<
        << << < HEAD
        const API_URL = `https://doa-doa-api-ahmadramadhan.fly.dev/api/doa/${doaId}`; ===
        === =
        // Gunakan URL dasar jika sub-endpoint ID bermasalah pada API asal
        const API_URL_DETAIL = `https://equran.id/api/doa/${doaId}`; <<
        << << < HEAD
        const API_URL_FALLBACK = `https://equran.id/api/doa`;

        ===
        === =

        >>>
        >>> > 9 cdfc6796284d7c7ad208d74ce56f045a8b0297f
            >>>
            >>> > 06 eef282c22e831801132e0c89d1bf6eec6cdac2
        let currentDoa = null;

        // Fungsi sinkronisasi gambar yang sama dengan halaman list
        function getDoaVisual(judul) {
            const title = judul.toLowerCase();
            if (title.includes('makan') || title.includes('minum')) {
                return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=800&auto=format&fit=crop';
            } else if (title.includes('tidur') || title.includes('bangun')) {
                return 'https://images.unsplash.com/photo-1520201163981-8cc95007dd2a?q=80&w=800&auto=format&fit=crop';
            } else if (title.includes('masjid') || title.includes('shala') || title.includes('wudhu') || title.includes('adzan')) {
                return 'https://images.unsplash.com/photo-1564507004663-b6dfb3c824d5?q=80&w=800&auto=format&fit=crop';
            } else if (title.includes('keluar') || title.includes('masuk') || title.includes('perjalanan') || title.includes('kendaraan') || title.includes('bepergian')) {
                return 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?q=80&w=800&auto=format&fit=crop';
            } else if (title.includes('orang tua') || title.includes('ibu') || title.includes('bapak') || title.includes('keluarga')) {
                return 'https://images.unsplash.com/photo-1511895426328-dc8714191300?q=80&w=800&auto=format&fit=crop';
            } else if (title.includes('pakaian') || title.includes('baju') || title.includes('berhias')) {
                return 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?q=80&w=800&auto=format&fit=crop';
            } else if (title.includes('belajar') || title.includes('ilmu') || title.includes('cerdas')) {
                return 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?q=80&w=800&auto=format&fit=crop';
            } else if (title.includes('sakit') || title.includes('sehat') || title.includes('sembuh')) {
                return 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?q=80&w=800&auto=format&fit=crop';
            } else if (title.includes('rumah')) {
                return 'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=800&auto=format&fit=crop';
            }
            return 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=800&auto=format&fit=crop';
        }

        async function fetchDoaDetail() {
                try {
                    <<
                    << << < HEAD
                    const res = await fetch(API_URL);
                    if (!res.ok) throw new Error('Doa tidak ditemukan');
                    const data = await res.json(); ===
                    === = <<
                    << << < HEAD
                    let response = await fetch(API_URL_DETAIL);
                    let json = await response.json();
                    let resData = json.data || json;

                    // CRITICAL FIX: Jika API mengembalikan array utuh, kita filter secara lokal berdasarkan ID
                    if (Array.isArray(resData)) {
                        currentDoa = resData.find(item => item.id == doaId) || resData[doaId - 1];
                    } else {
                        currentDoa = resData;
                    }

                    // Skenario cadangan jika link /id mengembalikan 404/Error, kita tembak endpoint list global
                    if (!currentDoa || Object.keys(currentDoa).length === 0 || currentDoa.id === undefined) {
                        const fallbackResponse = await fetch(API_URL_FALLBACK);
                        const fallbackJson = await fallbackResponse.json();
                        const fallbackData = fallbackJson.data || fallbackJson;
                        currentDoa = fallbackData.find(item => item.id == doaId) || fallbackData[doaId - 1];
                    }

                    document.getElementById('loading').style.display = 'none';

                    if (!currentDoa) {
                        showErrorState();
                        return;
                    }

                    const judul = currentDoa.doa || currentDoa.judul || currentDoa.nama || 'Doa Harian';
                    document.getElementById('d-title').innerText = judul;
                    document.getElementById('d-arab').innerText = currentDoa.ayat || currentDoa.arab || currentDoa.lafaz || '';
                    document.getElementById('d-latin').innerText = currentDoa.latin || '';
                    document.getElementById('d-arti').innerText = `"${currentDoa.artinya || currentDoa.arti || ''}"`;

                    // Menerapkan gambar relevan
                    document.getElementById('d-img').src = getDoaVisual(judul); ===
                    === =
                    const response = await fetch(API_URL_DETAIL);
                    if (!response.ok) throw new Error('Gagal mengambil data (HTTP ' + response.status + ')');

                    const json = await response.json();
                    // Data bisa langsung atau terbungkus dalam properti 'data'
                    const data = json.data || json;

                    // Jika data kosong atau tidak ada properti yang diharapkan
                    if (!data || Object.keys(data).length === 0) {
                        throw new Error('Doa tidak ditemukan');
                    }

                    >>>
                    >>> > 06 eef282c22e831801132e0c89d1bf6eec6cdac2
                    currentDoa = data;

                    document.getElementById('loading').style.display = 'none'; >>>
                    >>> > 9 cdfc6796284d7c7ad208d74ce56f045a8b0297f
                    document.getElementById('doaCard').style.display = 'block';

                    document.getElementById('d-title').innerText = currentDoa.judul || currentDoa.nama || 'Doa';
                    document.getElementById('d-arab').innerText = currentDoa.arab || '';
                    document.getElementById('d-latin').innerText = currentDoa.latin || '';
                    document.getElementById('d-arti').innerText = currentDoa.artinya || currentDoa.arti || '';

                    // Gambar tematik dari Unsplash
                    document.getElementById('d-img').src =
                        `https://images.unsplash.com/photo-1582653291997-079a1c04e5a1?q=80&w=800&auto=format&fit=crop&sig=${doaId}`;

                } catch (error) {
                    <<
                    << << < HEAD
                    console.error(error);
                    document.getElementById('loading').innerHTML = '<span style="color:red;">Gagal menarik data doa. Pastikan koneksi internet stabil.</span>'; ===
                    === =
                    document.getElementById('loading').innerHTML = `
<<<<<<< HEAD
                <span style="color:#dc2626;">
                    <i class="fas fa-exclamation-circle"></i> ${error.message}
                    <br><small style="color:#64748b;">Pastikan ID doa benar dan koneksi internet stabil.</small>
                </span>
            `; ===
                    === = <
                    span style = "color:#dc2626;" >
                        <
                        i class = "fas fa-exclamation-circle" > < /i> ${error.message || 'Gagal memuat doa.'} <
                        br > < small style = "color:#64748b;" > Pastikan koneksi internet stabil dan ID doa tersedia. < /small> <
                        /span>
                    `;
                console.error('Error fetchDoaDetail:', error);
>>>>>>> 9cdfc6796284d7c7ad208d74ce56f045a8b0297f
>>>>>>> 06eef282c22e831801132e0c89d1bf6eec6cdac2
            }
        }

        function showErrorState() {
            document.querySelector('.container').innerHTML = ` <
                    div style = "text-align:center; padding: 50px; color:#64748b;" >
                        <
                        i class = "fas fa-box-open"
                    style = "font-size:3rem; margin-bottom:15px;" > < /i><br>
                    Maaf, data doa tidak ditemukan di server API. <
                        /div>`;
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
                            // Fallback
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
                        <<
                        << << < HEAD
                            ===
                            === = <<
                            << << < HEAD ===
                            === =
                            // Fallback untuk browser desktop
                            >>>
                            >>> > 9 cdfc6796284d7c7ad208d74ce56f045a8b0297f >>>
                            >>> > 06 eef282c22e831801132e0c89d1bf6eec6cdac2
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