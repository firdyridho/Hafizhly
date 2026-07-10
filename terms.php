<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
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
            line-height: 1.7;
        }

        .header {
            background: var(--card-bg);
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-btn {
            color: var(--text-muted);
            font-size: 1.2rem;
            text-decoration: none;
            transition: 0.2s;
        }

        .back-btn:hover {
            color: var(--primary);
        }

        .header-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary);
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .document-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
        }

        h1 {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .last-updated {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
        }

        h2 {
            font-size: 1.2rem;
            color: var(--primary);
            margin-top: 30px;
            margin-bottom: 15px;
        }

        p,
        li {
            font-size: 1rem;
            color: #475569;
            margin-bottom: 15px;
        }

        ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .document-card {
                padding: 25px;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div class="header-title">Hifzly.</div>
    </div>

    <div class="container">
        <div class="document-card">
            <h1>Syarat dan Ketentuan</h1>
            <div class="last-updated">Terakhir diperbarui: <?= date('d F Y') ?></div>

            <p>Dengan mengakses dan menggunakan aplikasi <strong>Hifzly</strong>, Anda menyetujui untuk terikat oleh Syarat dan Ketentuan ini. Jika Anda tidak setuju dengan bagian mana pun dari persyaratan ini, Anda tidak diperkenankan menggunakan aplikasi kami.</p>

            <h2>1. Penggunaan Layanan</h2>
            <ul>
                <li>Anda setuju untuk menggunakan aplikasi Hifzly hanya untuk tujuan ibadah, pembelajaran, dan pencatatan yang sah (legal).</li>
                <li>Anda bertanggung jawab untuk menjaga kerahasiaan kredensial akun Anda (email dan kata sandi).</li>
                <li>Dilarang keras melakukan tindakan yang dapat merusak, membebani, atau mengganggu server dan jaringan Hifzly.</li>
            </ul>

            <h2>2. Hak Cipta dan Konten</h2>
            <p>Antarmuka pengguna, desain, dan struktur basis data Hifzly adalah hak milik kami. Namun, teks Al-Qur'an, Tafsir, Audio Murottal, dan data Jadwal Sholat bersumber dari API publik pihak ketiga. Hifzly tidak mengklaim hak cipta atas teks suci Al-Qur'an.</p>

            <h2>3. Batasan Tanggung Jawab (Disclaimer)</h2>
            <p>Kami berusaha menyajikan data seakurat mungkin. Namun, Hifzly tidak memberikan jaminan mutlak atas keakuratan jadwal sholat, arah kiblat, atau terjemahan. Pengguna diharapkan menggunakan kebijaksanaannya sendiri (melakukan *cross-check* dengan otoritas agama setempat) jika terdapat keraguan.</p>
            <p>Kami tidak bertanggung jawab atas kerugian langsung maupun tidak langsung yang timbul dari penggunaan atau ketidakmampuan menggunakan layanan kami.</p>

            <h2>4. Penghentian Akses</h2>
            <p>Kami berhak untuk menangguhkan atau mengakhiri akun Anda secara sepihak jika Anda terbukti melanggar Syarat dan Ketentuan ini, melakukan *spamming*, atau tindakan peretasan.</p>

            <h2>5. Hukum yang Berlaku</h2>
            <p>Syarat dan Ketentuan ini diatur dan ditafsirkan sesuai dengan hukum yang berlaku di Indonesia.</p>
        </div>
    </div>

</body>

</html>