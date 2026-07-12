<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) \vert{}\vert{}$_SESSION['role'] !== 'admin') { exit('Unauthorized'); }

// 1. UPDATE TABEL OTOMATIS (Menambahkan fitur Cover & Waktu)
$check_cover = mysqli_query($conn, "SHOW COLUMNS FROM tajwid_materi LIKE 'cover_image'");
if(mysqli_num_rows($check_cover) == 0) {
    mysqli_query($conn, "ALTER TABLE tajwid_materi ADD cover_image VARCHAR(255) AFTER judul");
    mysqli_query($conn, "ALTER TABLE tajwid_materi ADD waktu_kuis INT DEFAULT 0 AFTER youtube_url");
}

// Buat folder uploads jika belum ada
if (!file_exists('../uploads')) { mkdir('../uploads', 0777, true); }

// 2. HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_materi'])) {$judul = mysqli_real_escape_string($conn,$_POST['judul']);
        $konten = mysqli_real_escape_string($conn, $_POST['konten']); // Dari Quill Editor$youtube = mysqli_real_escape_string($conn,$_POST['youtube_url']);
        $waktu_kuis = (int)$_POST['waktu_kuis'];
        
        // Upload Cover
        $cover = "";
        if(isset($_FILES['cover']) &&$_FILES['cover']['error'] == 0){
            $ext = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
            $cover = time() . '_' . rand(100,999) . '.' . $ext;
            move_uploaded_file($_FILES['cover']['tmp_name'], '../uploads/' .$cover);
        }

        mysqli_query($conn, "INSERT INTO tajwid_materi (judul, cover_image, konten, youtube_url, waktu_kuis) VALUES ('$judul', '$cover', '$konten', '$youtube', '$waktu_kuis')");
        header("Location: materi_tajwid.php"); exit();
    }
    
    if (isset($_POST['add_kuis'])) {
        $materi_id = (int)$_POST['materi_id'];
        $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);$opsi_a = mysqli_real_escape_string($conn,$_POST['opsi_a']);
        $opsi_b = mysqli_real_escape_string($conn, $_POST['opsi_b']);$opsi_c = mysqli_real_escape_string($conn,$_POST['opsi_c']);
        $opsi_d = mysqli_real_escape_string($conn, $_POST['opsi_d']);$jawaban = mysqli_real_escape_string($conn,$_POST['jawaban_benar']);
        
        mysqli_query($conn, "INSERT INTO tajwid_kuis (materi_id, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar) 
                             VALUES ('$materi_id', '$pertanyaan', '$opsi_a', '$opsi_b', '$opsi_c', '$opsi_d', '$jawaban')");
        header("Location: materi_tajwid.php"); exit();
    }

    if (isset($_POST['delete_materi'])) {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "DELETE FROM tajwid_materi WHERE id='$id'");
        header("Location: materi_tajwid.php"); exit();
    }
}

// AMBIL DATA MATERI
$materi_q = mysqli_query($conn, "SELECT * FROM tajwid_materi ORDER BY created_at DESC");
$materi_list = [];
while ($row = mysqli_fetch_assoc($materi_q)) {
    $q_soal = mysqli_query($conn, "SELECT COUNT(id) as total FROM tajwid_kuis WHERE materi_id = " . $row['id']);
    $row['total_soal'] = mysqli_fetch_assoc($q_soal)['total'];
    $materi_list[] =$row;
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
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <style>
        :root { --primary: #059669; --dark: #1e293b; --bg: #f8fafc; --border: #e2e8f0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--dark); padding: 20px; }
        
        /* Layout Kiri-Kanan (Split) */
        .workspace { display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px; max-width: 1200px; margin: 0 auto; }
        @media (max-width: 900px) { .workspace { grid-template-columns: 1fr; } }
        
        .card { background: white; padding: 25px; border-radius: 16px; border: 1px solid var(--border); box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 20px;}
        h2 { margin-bottom: 20px; font-size: 1.3rem; display: flex; align-items: center; gap: 8px; color: var(--dark); }
        
        .form-control { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid var(--border); border-radius: 10px; outline: none; font-size: 0.95rem; }
        .form-control:focus { border-color: var(--primary); }
        .form-row { display: flex; gap: 15px; }
        
        .btn { background: var(--primary); color: white; border: none; padding: 12px 20px; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.2s; }
        .btn:hover { opacity: 0.9; }
        
        /* Custom File Input */
        .file-upload { border: 2px dashed var(--border); padding: 20px; text-align: center; border-radius: 10px; margin-bottom: 15px; cursor: pointer; color: var(--text-muted); }
        .file-upload:hover { border-color: var(--primary); color: var(--primary); background: #f0fdf4; }
        
        /* Editor Container */
        #editor-container { height: 250px; margin-bottom: 15px; border-radius: 0 0 10px 10px; }
        .ql-toolbar { border-radius: 10px 10px 0 0; }

        .list-item { background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--border); }
    </style>
</head>
<body>
    <div style="max-width: 1200px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
        <h1><i class="fas fa-chalkboard-teacher" style="color:var(--primary);"></i> Ruang Kelas Tajwid</h1>
        <a href="dashboard.php" class="btn" style="background:#64748b; text-decoration:none;"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>

    <div class="workspace">
        <div class="card">
            <h2><i class="fas fa-file-signature"></i> Buat Materi Baru</h2>
            <form method="POST" enctype="multipart/form-data" id="materiForm">
                <input type="text" name="judul" class="form-control" placeholder="Judul Materi (Misal: Hukum Ikhfa)" required>
                
                <div class="form-row">
                    <div style="flex: 2;">
                        <input type="url" name="youtube_url" class="form-control" placeholder="Link Video YouTube (Opsional)">
                    </div>
                    <div style="flex: 1;">
                        <input type="number" name="waktu_kuis" class="form-control" placeholder="Waktu Kuis (Menit)" min="0" value="5" required title="Batas waktu pengerjaan kuis dalam menit">
                    </div>
                </div>

                <label class="file-upload" onclick="document.getElementById('coverImg').click()">
                    <i class="fas fa-image" style="font-size:2rem; margin-bottom:10px; display:block;"></i>
                    <span id="coverText">Pilih Foto Sampul / Cover Materi...</span>
                    <input type="file" id="coverImg" name="cover" accept="image/*" style="display:none;" onchange="document.getElementById('coverText').innerText = this.files[0].name">
                </label>

                <div id="toolbar">
                    <span class="ql-formats"><select class="ql-header"></select></span>
                    <span class="ql-formats"><button class="ql-bold"></button><button class="ql-italic"></button><button class="ql-underline"></button></span>
                    <span class="ql-formats"><button class="ql-list" value="ordered"></button><button class="ql-list" value="bullet"></button></span>
                    <span class="ql-formats"><button class="ql-link"></button><button class="ql-image"></button></span>
                </div>
                <div id="editor-container"></div>
                <textarea name="konten" id="hiddenArea" style="display:none;"></textarea>

                <button type="submit" name="add_materi" class="btn" style="width: 100%;"><i class="fas fa-save"></i> Terbitkan Materi</button>
            </form>
        </div>

        <div>
            <div class="card">
                <h2><i class="fas fa-puzzle-piece"></i> Tambah Kuis Interaktif</h2>
                <form method="POST">
                    <select name="materi_id" class="form-control" required>
                        <option value="">-- Kuis Untuk Materi Apa? --</option>
                        <?php foreach($materi_list as$m): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['judul']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <textarea name="pertanyaan" class="form-control" rows="3" placeholder="Tulis soal kuis di sini..." required></textarea>
                    
                    <div class="form-row">
                        <input type="text" name="opsi_a" class="form-control" placeholder="Pilihan A" required>
                        <input type="text" name="opsi_b" class="form-control" placeholder="Pilihan B" required>
                    </div>
                    <div class="form-row">
                        <input type="text" name="opsi_c" class="form-control" placeholder="Pilihan C" required>
                        <input type="text" name="opsi_d" class="form-control" placeholder="Pilihan D" required>
                    </div>
                    
                    <select name="jawaban_benar" class="form-control" required style="background: #f0fdf4; font-weight:bold; color:var(--primary);">
                        <option value="">-- Pilih Kunci Jawaban Benar --</option>
                        <option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option>
                    </select>
                    <button type="submit" name="add_kuis" class="btn" style="background:#0284c7; width:100%;"><i class="fas fa-plus"></i> Simpan Soal</button>
                </form>
            </div>

            <div class="card" style="max-height: 400px; overflow-y:auto;">
                <h2 style="font-size:1.1rem;">Daftar Materi Aktif</h2>
                <?php foreach($materi_list as$m): ?>
                    <div class="list-item">
                        <div>
                            <div style="font-weight:bold;"><?= htmlspecialchars($m['judul']) ?></div>
                            <div style="font-size:0.8rem; color:#64748b; margin-top:5px;">
                                <i class="far fa-clock"></i> <?= $m['waktu_kuis'] ?> Menit | 
                                <i class="fas fa-list-ol"></i> <?= $m['total_soal'] ?> Soal
                            </div>
                        </div>
                        <form method="POST" onsubmit="return confirm('Hapus materi ini?');">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <button type="submit" name="delete_materi" class="btn" style="background:#ef4444; padding:8px 12px;"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        var quill = new Quill('#editor-container', {
            modules: { toolbar: '#toolbar' },
            theme: 'snow',
            placeholder: 'Tulis artikel tajwid di sini (Bisa tambahkan foto juga)...'
        });

        // Saat form disubmit, pindahkan HTML dari Quill ke textarea tersembunyi
        document.getElementById('materiForm').onsubmit = function() {
            document.getElementById('hiddenArea').value = document.querySelector('.ql-editor').innerHTML;
        };
    </script>
</body>
</html>