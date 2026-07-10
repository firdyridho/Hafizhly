<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - Hifzly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
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
            <h1>Kebijakan Privasi</h1>
            <div class="last-updated">Terakhir diperbarui: <?= date('d F Y') ?></div>

            <p>Selamat datang di <strong>Hifzly</strong>. Kami sangat menghargai privasi Anda dan berkomitmen untuk melindungi informasi pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data Anda saat menggunakan layanan kami.</p>

            <h2>1. Informasi yang Kami Kumpulkan</h2>
            <ul>
                <li><strong>Informasi Akun:</strong> Saat Anda mendaftar, kami mengumpulkan nama, alamat email, dan kata sandi yang dienkripsi.</li>
                <li><strong>Data Aktivitas:</strong> Kami menyimpan catatan aktivitas ibadah Anda (Mutabaah), bookmark Al-Qur'an, dan riwayat hafalan untuk menampilkan progres Anda.</li>
                <li><strong>Data Lokasi (GPS):</strong> Kami meminta izin akses lokasi hanya untuk menghitung jadwal sholat dan arah kiblat secara akurat (melalui API pihak ketiga). Data lokasi ini diproses secara <i>real-time</i> dan <strong>tidak kami simpan</strong> di database kami.</li>
            </ul>

            <h2>2. Penggunaan Informasi</h2>
            <p>Informasi yang kami kumpulkan digunakan semata-mata untuk:</p>
            <ul>
                <li>Menyediakan dan memelihara layanan aplikasi Hifzly.</li>
                <li>Personalisasi pengalaman pengguna (seperti nama di Dashboard dan target hafalan).</li>
                <li>Menganalisis penggunaan aplikasi untuk meningkatkan fitur di masa mendatang.</li>
            </ul>

            <h2>3. Keamanan Data</h2>
            <p>Kami menggunakan standar keamanan industri untuk melindungi data pribadi Anda dari akses yang tidak sah. Kata sandi Anda dienkripsi menggunakan algoritma hashing yang aman. Namun, perlu diingat bahwa tidak ada metode transmisi data melalui internet yang 100% aman.</p>

            <h2>4. Pihak Ketiga</h2>
            <p>Hifzly menggunakan API publik (seperti EQuran.id dan Aladhan) untuk menyediakan teks Al-Qur'an, Tafsir, dan Jadwal Sholat. Kami tidak pernah menjual, menyewakan, atau menukar data pribadi Anda kepada pihak ketiga untuk tujuan pemasaran.</p>

            <h2>5. Perubahan Kebijakan</h2>
            <p>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Kami akan memberi tahu pengguna tentang perubahan material melalui pengumuman di dalam aplikasi.</p>
        </div>
    </div>

</body>

</html>