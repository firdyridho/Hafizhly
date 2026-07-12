<?php
session_start();
require_once '../config/database.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    exit('Unauthorized: Khusus Admin');
}

// 1. CREATE TABLES OTOMATIS
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS tajwid_materi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    konten TEXT NOT NULL,
    youtube_url VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS tajwid_kuis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    materi_id INT NOT NULL,
    pertanyaan TEXT NOT NULL,
    opsi_a VARCHAR(255) NOT NULL,
    opsi_b VARCHAR(255) NOT NULL,
    opsi_c VARCHAR(255) NOT NULL,
    opsi_d VARCHAR(255) NOT NULL,
    jawaban_benar ENUM('a','b','c','d') NOT NULL,
    FOREIGN KEY (materi_id) REFERENCES tajwid_materi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 2. HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_materi'])) {
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $konten = mysqli_real_escape_string($conn, $_POST['konten']);
        $youtube = mysqli_real_escape_string($conn, $_POST['youtube_url']);
        mysqli_query($conn, "INSERT INTO tajwid_materi (judul, konten, youtube_url) VALUES ('$judul', '$konten', '$youtube')");
        header("Location: materi_tajwid.php");
        exit();
    }

    if (isset($_POST['add_kuis'])) {
        $materi_id = (int) $_POST['materi_id'];
        $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $opsi_a = mysqli_real_escape_string($conn, $_POST['opsi_a']);
        $opsi_b = mysqli_real_escape_string($conn, $_POST['opsi_b']);
        $opsi_c = mysqli_real_escape_string($conn, $_POST['opsi_c']);
        $opsi_d = mysqli_real_escape_string($conn, $_POST['opsi_d']);
        $jawaban = mysqli_real_escape_string($conn, $_POST['jawaban_benar']);

        mysqli_query($conn, "INSERT INTO tajwid_kuis (materi_id, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar) 
                             VALUES ('$materi_id', '$pertanyaan', '$opsi_a', '$opsi_b', '$opsi_c', '$opsi_d', '$jawaban')");
        header("Location: materi_tajwid.php");
        exit();
    }

    if (isset($_POST['delete_materi'])) {
        $id = (int) $_POST['id'];
        mysqli_query($conn, "DELETE FROM tajwid_materi WHERE id='$id'");
        header("Location: materi_tajwid.php");
        exit();
    }
}

// AMBIL DATA MATERI
$materi_q = mysqli_query($conn, "SELECT * FROM tajwid_materi ORDER BY created_at DESC");
$materi_list = [];
while ($row = mysqli_fetch_assoc($materi_q)) {
    // Hitung jumlah soal untuk materi ini
    $q_soal = mysqli_query($conn, "SELECT COUNT(id) as total FROM tajwid_kuis WHERE materi_id = " . $row['id']);
    $row['total_soal'] = mysqli_fetch_assoc($q_soal)['total'];
    $materi_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tajwid - Admin Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --dark: #1e293b;
            --bg: #f8fafc;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--dark);
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        h2 {
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-danger {
            background: #ef4444;
        }

        .list-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid var(--border);
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div style="max-width: 1000px; margin: 0 auto 20px auto;">
        <h1><i class="fas fa-book-quran"></i> Kelola Materi Tajwid</h1>
        <a href="dashboard.php" style="color:var(--text-muted);">Kembali ke Dashboard Admin</a>
    </div>

    <div class="container">
        <!-- FORM TAMBAH MATERI -->
        <div class="card">
            <h2>Tambah Materi Baru</h2>
            <form method="POST">
                <input type="text" name="judul" class="form-control" placeholder="Judul Materi (Misal: Hukum Nun Mati)" required>
                <input type="url" name="youtube_url" class="form-control" placeholder="Link YouTube (Opsional)">
                <textarea name="konten" class="form-control" rows="8" placeholder="Teks Materi Tajwid..." required></textarea>
                <button type="submit" name="add_materi" class="btn"><i class="fas fa-save"></i> Simpan Materi</button>
            </form>
        </div>

        <!-- FORM TAMBAH SOAL KUIS -->
        <div class="card">
            <h2>Tambah Soal Kuis</h2>
            <form method="POST">
                <select name="materi_id" class="form-control" required>
                    <option value="">-- Pilih Materi --</option>
                    <?php foreach ($materi_list as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['judul']) ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="pertanyaan" class="form-control" rows="3" placeholder="Pertanyaan Kuis..." required></textarea>
                <input type="text" name="opsi_a" class="form-control" placeholder="Opsi A" required>
                <input type="text" name="opsi_b" class="form-control" placeholder="Opsi B" required>
                <input type="text" name="opsi_c" class="form-control" placeholder="Opsi C" required>
                <input type="text" name="opsi_d" class="form-control" placeholder="Opsi D" required>

                <select name="jawaban_benar" class="form-control" required>
                    <option value="">-- Kunci Jawaban Benar --</option>
                    <option value="a">A</option>
                    <option value="b">B</option>
                    <option value="c">C</option>
                    <option value="d">D</option>
                </select>
                <button type="submit" name="add_kuis" class="btn" style="background:#0284c7;"><i class="fas fa-plus"></i> Tambah Soal</button>
            </form>
        </div>

        <!-- DAFTAR MATERI -->
        <div class="card" style="grid-column: 1 / -1;">
            <h2>Daftar Materi Tajwid</h2>
            <?php foreach ($materi_list as $m): ?>
                <div class="list-item">
                    <div>
                        <div style="font-weight:bold;"><?= htmlspecialchars($m['judul']) ?></div>
                        <div style="font-size:0.85rem; color:#64748b; margin-top:5px;">
                            <i class="fab fa-youtube" style="color:red;"></i> <?= $m['youtube_url'] ? 'Ada Video' : 'Tanpa Video' ?> |
                            <i class="fas fa-question-circle" style="color:var(--primary);"></i> <?= $m['total_soal'] ?> Soal Kuis
                        </div>
                    </div>
                    <form method="POST" onsubmit="return confirm('Hapus materi beserta kuisnya?');">
                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                        <button type="submit" name="delete_materi" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>