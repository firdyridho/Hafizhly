<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// AUTO-CREATE TABEL GAME HISTORY & ACHIEVEMENTS JIKA BELUM ADA
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS game_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_type VARCHAR(50) NOT NULL,
    juz_start INT,
    juz_end INT,
    total_q INT,
    score INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_penghargaan VARCHAR(100) NOT NULL,
    tanggal_diraih DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qur'an Games - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --dark: #0f172a;
            --bg: #f8fafc;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--dark);
            padding-bottom: 100px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            margin-top: 20px;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--dark);
        }

        .header p {
            color: #64748b;
            margin-top: 5px;
            font-size: 0.95rem;
        }

        .game-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border);
            margin-bottom: 20px;
            text-decoration: none;
            color: var(--dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .game-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 15px 35px rgba(5, 150, 105, 0.15);
        }

        .gc-icon {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            background: #ecfdf5;
            color: var(--primary);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .gc-title {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .gc-desc {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .btn-play {
            background: var(--dark);
            color: white;
            padding: 12px 30px;
            border-radius: 16px;
            font-weight: 700;
            width: 100%;
            transition: 0.2s;
        }

        .game-card:hover .btn-play {
            background: var(--primary);
        }

        .gc-bg {
            position: absolute;
            top: -20px;
            right: -20px;
            font-size: 8rem;
            color: #f1f5f9;
            z-index: 0;
            transform: rotate(-15deg);
            opacity: 0.5;
        }

        .gc-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🎮 Arcade Qur'an</h1>
            <p>Uji kekuatan hafalanmu dan raih peringkat teratas!</p>
        </div>

        <a href="lanjut_ayat.php" class="game-card">
            <i class="fas fa-microphone-alt gc-bg"></i>
            <div class="gc-content">
                <div class="gc-icon"><i class="fas fa-headphones"></i></div>
                <div class="gc-title">Lanjut Ayat</div>
                <div class="gc-desc">Dengarkan lantunan ayat, lalu tebak potongan ayat berikutnya dengan tepat. Sangat cocok untuk menguji kelancaran (mutqin) hafalan.</div>
                <div class="btn-play">Main Sekarang <i class="fas fa-play"></i></div>
            </div>
        </a>

        <a href="tebak_surah.php" class="game-card">
            <i class="fas fa-book-open gc-bg"></i>
            <div class="gc-content">
                <div class="gc-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fas fa-question"></i></div>
                <div class="gc-title">Tebak Surah & Ayat</div>
                <div class="gc-desc">Dengarkan audio acak dari juz pilihanmu, lalu tebak dari surah apa dan ayat ke berapa audio tersebut berasal.</div>
                <div class="btn-play" style="background:#3b82f6;">Main Sekarang <i class="fas fa-play"></i></div>
            </div>
        </a>
    </div>
    <?php include '../components/nav.php'; ?>
</body>

</html>