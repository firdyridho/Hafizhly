<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$pesan = '';

// Jika tombol 'Simpan' ditekan
if (isset($_POST['simpan_mutabaah'])) {
    $jenis = mysqli_real_escape_string($conn, $_POST['jenis']);
    $juz = (int)$_POST['juz'];
    $durasi = (int)$_POST['durasi_menit'];
    $tanggal = date('Y-m-d'); // Mengambil tanggal hari ini secara otomatis

    // Masukkan data ke tabel mutabaah
    $query = "INSERT INTO mutabaah (user_id, jenis, juz, durasi_menit, tanggal) 
              VALUES ('$user_id', '$jenis', '$juz', '$durasi', '$tanggal')";

    if (mysqli_query($conn, $query)) {
        $pesan = "<div class='alert alert-success'>Alhamdulillah, catatan berhasil disimpan!</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal menyimpan: " . mysqli_error($conn) . "</div>";
    }
}

// Mengambil riwayat mutabaah pengguna (10 data terakhir)
$history_query = mysqli_query($conn, "SELECT * FROM mutabaah WHERE user_id = '$user_id' ORDER BY tanggal DESC, id DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mutabaah - Hifzly</title>

    <style>
        :root {
            --primary: #059669;
            --dark: #1f2937;
            --bg: #f3f4f6;
            --card-bg: #ffffff;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 80px;
        }

        /* Header */
        .header {
            background: var(--primary);
            color: white;
            padding: 30px 20px 20px 20px;
            text-align: center;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h2 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .header p {
            font-size: 0.9rem;
            margin-top: 5px;
            opacity: 0.9;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 1.1rem;
            margin-bottom: 15px;
            margin-top: 25px;
            color: var(--dark);
            font-weight: 600;
        }

        /* Form Input */
        .card-form {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            outline: none;
            transition: 0.3s;
        }

        .form-group select:focus,
        .form-group input:focus {
            border-color: var(--primary);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #047857;
        }

        /* Riwayat Card */
        .history-card {
            background: var(--card-bg);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid var(--primary);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .history-info h4 {
            color: var(--dark);
            font-size: 1rem;
            margin-bottom: 3px;
            text-transform: capitalize;
        }

        .history-info p {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .history-date {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* Menu Navigasi Bawah */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--card-bg);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 1000;
            max-width: 600px;
            margin: 0 auto;
        }

        .nav-item {
            text-align: center;
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .nav-item.active {
            color: var(--primary);
        }

        .nav-icon {
            font-size: 1.4rem;
        }

        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Mutabaah Yaumi</h2>
        <p>Catat amalan harianmu di sini</p>
    </div>

    <div class="container">
        <?= $pesan; ?>

        <div class="card-form">
            <form action="" method="POST">
                <div class="form-group">
                    <label>Jenis Amalan</label>
                    <select name="jenis" required>
                        <option value="tilawah">Tilawah (Membaca)</option>
                        <option value="murojaah">Murojaah (Mengulang Hafalan)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Juz ke-</label>
                    <input type="number" name="juz" min="1" max="30" placeholder="Misal: 30" required>
                </div>

                <div class="form-group">
                    <label>Durasi (Menit)</label>
                    <input type="number" name="durasi_menit" min="1" placeholder="Misal: 15" required>
                </div>

                <button type="submit" name="simpan_mutabaah" class="btn">Simpan Catatan</button>
            </form>
        </div>

        <h3 class="section-title">Riwayat Terakhir</h3>

        <?php if (mysqli_num_rows($history_query) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($history_query)): ?>
                <div class="history-card">
                    <div class="history-info">
                        <h4><?= $row['jenis']; ?></h4>
                        <p>Juz <?= $row['juz']; ?> • <?= $row['durasi_menit']; ?> Menit</p>
                    </div>
                    <div class="history-date">
                        <?= date('d M Y', strtotime($row['tanggal'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:#6b7280; font-size:0.9rem;">Belum ada catatan mutabaah. Yuk mulai hari ini!</p>
        <?php endif; ?>

    </div>

    <div class="bottom-nav">
        <a href="dashboard.php" class="nav-item">
            <span class="nav-icon">📖</span>
            <span>Qur'an</span>
        </a>
        <a href="mutabaah.php" class="nav-item active">
            <span class="nav-icon">📊</span>
            <span>Mutabaah</span>
        </a>
        <a href="#" class="nav-item">
            <span class="nav-icon">🏆</span>
            <span>Target</span>
        </a>
        <a href="../logout.php" class="nav-item" onclick="return confirm('Yakin ingin keluar?')">
            <span class="nav-icon">🚪</span>
            <span>Keluar</span>
        </a>
    </div>

</body>

</html>