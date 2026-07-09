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
    <title>E-Qur'an - Hifzly</title>
    <style>
        :root { --primary: #059669; --dark: #1f2937; --bg: #f9fafb; --card-bg: #ffffff; --text-muted: #6b7280; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: var(--bg); color: var(--dark); padding-bottom: 90px; }
        
        .header-title { background: var(--card-bg); padding: 20px; text-align: center; font-size: 1.2rem; font-weight: bold; color: var(--primary); box-shadow: 0 1px 3px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 10; }
        .container { padding: 20px; max-width: 600px; margin: 0 auto; }
        
        .surah-card { background: var(--card-bg); padding: 15px; margin-bottom: 12px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: 0.2s; }
        .surah-card:active { transform: scale(0.98); }
        .surah-info h3 { font-size: 1.1rem; color: var(--primary); }
        .surah-info p { font-size: 0.85rem; color: var(--text-muted); margin-top: 5px; }
        .surah-arab { font-size: 1.5rem; font-weight: bold; color: var(--primary); }
        #loading { text-align: center; color: var(--text-muted); margin: 20px 0; }
    </style>
</head>
<body>

    <div class="header-title">E-Qur'an & Murojaah</div>

    <div class="container">
        <div id="loading">Memuat daftar surat dari API...</div>
        <div id="surah-list"></div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script>
        async function fetchSurah() {
            try {
                const response = await fetch('https://equran.id/api/v2/surat');
                const data = await response.json();
                document.getElementById('loading').style.display = 'none';
                const container = document.getElementById('surah-list');

                data.data.forEach(surah => {
                    const card = document.createElement('div');
                    card.className = 'surah-card';
                    card.innerHTML = `
                        <div class="surah-info">
                            <h3>${surah.nomor}. ${surah.namaLatin}</h3>
                            <p>${surah.arti} • ${surah.jumlahAyat} Ayat</p>
                        </div>
                        <div class="surah-arab">${surah.nama}</div>
                    `;
                    card.onclick = () => { window.location.href = 'baca.php?nomor=' + surah.nomor; };
                    container.appendChild(card);
                });
            } catch (error) {
                document.getElementById('loading').innerText = "Gagal memuat data.";
            }
        }
        fetchSurah();
    </script>
</body>
</html>