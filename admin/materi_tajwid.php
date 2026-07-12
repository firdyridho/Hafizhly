<?php
session_start();
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
} else {
    die("Error: File database.php tidak ditemukan!");
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// 1. AUTO-UPDATE TABEL (Menambahkan PDF & Gambar Kuis jika belum ada)
$cek_pdf = mysqli_query($conn, "SHOW COLUMNS FROM tajwid_materi LIKE 'pdf_file'");
if ($cek_pdf && mysqli_num_rows($cek_pdf) == 0) {
    @mysqli_query($conn, "ALTER TABLE tajwid_materi ADD pdf_file VARCHAR(255) NULL AFTER cover_image");
}
$cek_gbr = mysqli_query($conn, "SHOW COLUMNS FROM tajwid_kuis LIKE 'gambar'");
if ($cek_gbr && mysqli_num_rows($cek_gbr) == 0) {
    @mysqli_query($conn, "ALTER TABLE tajwid_kuis ADD gambar VARCHAR(255) NULL AFTER pertanyaan");
}
if (!file_exists('../uploads')) {
    @mkdir('../uploads', 0777, true);
}

$alert = "";

// 2. HANDLE SIMPAN (TAMBAH / EDIT)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save') {
    $id_materi = isset($_POST['id_materi']) ? (int)$_POST['id_materi'] : 0;
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $konten = mysqli_real_escape_string($conn, $_POST['konten']);
    $youtube = mysqli_real_escape_string($conn, $_POST['youtube_url']);
    $waktu_kuis = isset($_POST['waktu_kuis']) ? (int)$_POST['waktu_kuis'] : 0;

    // Upload Cover & PDF
    $cover = "";
    $pdf = "";
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
        $cover = time() . '_cov.' . pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
        @move_uploaded_file($_FILES['cover']['tmp_name'], '../uploads/' . $cover);
    }
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $pdf = time() . '_doc.' . pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
        @move_uploaded_file($_FILES['pdf_file']['tmp_name'], '../uploads/' . $pdf);
    }

    if ($id_materi > 0) {
        // EDIT MATERI
        $q_upd = "UPDATE tajwid_materi SET judul='$judul', konten='$konten', youtube_url='$youtube', waktu_kuis='$waktu_kuis'";
        if ($cover != "") $q_upd .= ", cover_image='$cover'";
        if ($pdf != "") $q_upd .= ", pdf_file='$pdf'";
        $q_upd .= " WHERE id='$id_materi'";
        mysqli_query($conn, $q_upd);

        // Hapus Kuis Lama, ganti yang baru
        mysqli_query($conn, "DELETE FROM tajwid_kuis WHERE materi_id='$id_materi'");
    } else {
        // TAMBAH MATERI
        mysqli_query($conn, "INSERT INTO tajwid_materi (judul, cover_image, pdf_file, konten, youtube_url, waktu_kuis) VALUES ('$judul', '$cover', '$pdf', '$konten', '$youtube', '$waktu_kuis')");
        $id_materi = mysqli_insert_id($conn);
    }

    // PROSES SOAL KUIS (Jika ada)
    if (isset($_POST['pertanyaan']) && is_array($_POST['pertanyaan'])) {
        for ($i = 0; $i < count($_POST['pertanyaan']); $i++) {
            $tanya = mysqli_real_escape_string($conn, $_POST['pertanyaan'][$i]);
            if (trim($tanya) == "") continue;

            $oa = mysqli_real_escape_string($conn, $_POST['opsi_a'][$i]);
            $ob = mysqli_real_escape_string($conn, $_POST['opsi_b'][$i]);
            $oc = mysqli_real_escape_string($conn, $_POST['opsi_c'][$i]);
            $od = mysqli_real_escape_string($conn, $_POST['opsi_d'][$i]);
            $jb = mysqli_real_escape_string($conn, $_POST['jawaban_benar'][$i]);

            // Gambar Kuis
            $gbr_kuis = "";
            if (isset($_FILES['gambar_kuis']['name'][$i]) && $_FILES['gambar_kuis']['error'][$i] == 0) {
                $gbr_kuis = time() . '_q' . $i . '.' . pathinfo($_FILES['gambar_kuis']['name'][$i], PATHINFO_EXTENSION);
                @move_uploaded_file($_FILES['gambar_kuis']['tmp_name'][$i], '../uploads/' . $gbr_kuis);
            } else if (isset($_POST['old_gambar_kuis'][$i])) {
                $gbr_kuis = mysqli_real_escape_string($conn, $_POST['old_gambar_kuis'][$i]); // Pertahankan gambar lama
            }

            mysqli_query($conn, "INSERT INTO tajwid_kuis (materi_id, pertanyaan, gambar, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar) VALUES ('$id_materi', '$tanya', '$gbr_kuis', '$oa', '$ob', '$oc', '$od', '$jb')");
        }
    }
    $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Materi berhasil disimpan!'];
    header("Location: materi_tajwid.php");
    exit();
}

// HANDLE HAPUS
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM tajwid_materi WHERE id='$id'");
    $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Materi berhasil dihapus!'];
    header("Location: materi_tajwid.php");
    exit();
}

// 3. AMBIL DATA UNTUK DITAMPILKAN
$materi_list = [];
$q_m = mysqli_query($conn, "SELECT * FROM tajwid_materi ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($q_m)) {
    $q_s = mysqli_query($conn, "SELECT COUNT(id) as total FROM tajwid_kuis WHERE materi_id = " . $row['id']);
    $row['total_soal'] = mysqli_fetch_assoc($q_s)['total'] ?? 0;

    // Ambil detail soal untuk fitur Edit
    $soal = [];
    $q_soal_detail = mysqli_query($conn, "SELECT * FROM tajwid_kuis WHERE materi_id = " . $row['id']);
    while ($s = mysqli_fetch_assoc($q_soal_detail)) {
        $soal[] = $s;
    }
    $row['soal'] = $soal;

    $materi_list[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Tajwid - Admin Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 & CKEditor 5 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <style>
        :root {
            --primary: #059669;
            --dark: #0f172a;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--dark);
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.2);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: var(--dark);
        }

        /* CARD DAFTAR MATERI */
        .grid-materi {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .materi-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
            transition: 0.3s;
        }

        .materi-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.1);
        }

        .mc-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .mc-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
        }

        /* MULTI-STEP FORM (SPA) */
        #formArea {
            display: none;
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
        }

        .stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border);
            z-index: 1;
        }

        .step-item {
            background: white;
            z-index: 2;
            padding: 0 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .step-item.active {
            color: var(--primary);
        }

        .step-num {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--border);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .step-item.active .step-num {
            background: var(--primary);
            box-shadow: 0 0 0 4px #d1fae5;
        }

        .step-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            outline: none;
            font-family: inherit;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px #d1fae5;
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #cbd5e1;
            padding: 30px;
            text-align: center;
            border-radius: 16px;
            cursor: pointer;
            transition: 0.2s;
            background: #f8fafc;
        }

        .upload-area:hover {
            border-color: var(--primary);
            background: #f0fdf4;
        }

        /* CKEditor Custom Height */
        .ck-editor__editable {
            min-height: 400px;
            font-family: inherit;
            font-size: 1.05rem;
        }

        /* Quiz Box */
        .quiz-box {
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fafafa;
            position: relative;
        }

        .btn-remove-quiz {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #fee2e2;
            color: #ef4444;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 10px;
            cursor: pointer;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }
    </style>
</head>

<body>
    <div class="container">

        <?php if (isset($_SESSION['alert'])): ?>
            <script>
                Swal.fire({
                    icon: '<?= $_SESSION['alert']['type'] ?>',
                    title: '<?= $_SESSION['alert']['msg'] ?>',
                    timer: 2500,
                    showConfirmButton: false
                });
            </script>
        <?php unset($_SESSION['alert']);
        endif; ?>

        <!-- HEADER DAFTAR MATERI -->
        <div id="listView">
            <div class="header-top">
                <div>
                    <h1 style="font-size:1.8rem; font-weight:800;"><i class="fas fa-layer-group" style="color:var(--primary);"></i> Studio Tajwid</h1>
                    <p style="color:var(--text-muted); margin-top:5px;">Kelola materi, PDF, dan evaluasi santri.</p>
                </div>
                <div style="display:flex; gap:10px;">
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-home"></i></a>
                    <button class="btn btn-primary" onclick="openForm()"><i class="fas fa-plus"></i> Buat Materi Baru</button>
                </div>
            </div>

            <!-- DAFTAR KARTU -->
            <div class="grid-materi">
                <?php if (empty($materi_list)): ?>
                    <div style="grid-column:1/-1; text-align:center; padding:50px; background:white; border-radius:16px; border:1px solid var(--border); color:var(--text-muted);">Belum ada materi. Klik "Buat Materi Baru" untuk memulai.</div>
                <?php endif; ?>

                <?php foreach ($materi_list as $m): ?>
                    <div class="materi-card">
                        <div class="mc-title"><?= htmlspecialchars($m['judul']) ?></div>
                        <div class="mc-meta">
                            <span><i class="far fa-clock"></i> <?= $m['waktu_kuis'] ?>m Kuis</span>
                            <span><i class="fas fa-list-ol"></i> <?= $m['total_soal'] ?> Soal</span>
                            <?php if ($m['pdf_file']): ?><span><i class="fas fa-file-pdf" style="color:#ef4444;"></i> PDF</span><?php endif; ?>
                        </div>
                        <div style="display:flex; gap:10px;">
                            <!-- Data lemparan ke JS untuk mode Edit -->
                            <button class="btn btn-secondary" style="flex:1; padding:8px; font-size:0.85rem;" onclick='editMateri(<?= json_encode($m) ?>)'><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn" style="background:#fee2e2; color:#ef4444; padding:8px 15px;" onclick="confirmDelete(<?= $m['id'] ?>)"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- AREA FORM SPA (MULTI-STEP) -->
        <div id="formArea">
            <div class="header-top" style="margin-bottom:20px;">
                <h2 id="formTitle" style="font-size:1.5rem; font-weight:800;">Buat Materi Baru</h2>
                <button class="btn btn-secondary" onclick="closeForm()"><i class="fas fa-times"></i> Batal</button>
            </div>

            <div class="stepper">
                <div class="step-item active" id="stepIndicator1">
                    <div class="step-num">1</div> Info Dasar
                </div>
                <div class="step-item" id="stepIndicator2">
                    <div class="step-num">2</div> Tulis Materi
                </div>
                <div class="step-item" id="stepIndicator3">
                    <div class="step-num">3</div> Buat Kuis
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" id="mainForm">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id_materi" id="id_materi" value="0">

                <!-- STEP 1: INFO DASAR -->
                <div class="step-content active" id="step1">
                    <div class="form-group">
                        <label>Judul Materi</label>
                        <input type="text" name="judul" id="f_judul" class="form-control" placeholder="Contoh: Hukum Bacaan Nun Mati..." required>
                    </div>
                    <div class="form-group">
                        <label>Link Video YouTube (Opsional)</label>
                        <input type="url" name="youtube_url" id="f_youtube" class="form-control" placeholder="https://youtube.com/watch?v=...">
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Foto Sampul / Cover (Opsional)</label>
                            <label class="upload-area" onclick="document.getElementById('f_cover').click()">
                                <i class="fas fa-image" style="font-size:2rem; color:var(--primary); margin-bottom:10px;"></i>
                                <div id="lbl_cover">Klik untuk unggah foto (JPG/PNG)</div>
                                <input type="file" name="cover" id="f_cover" accept="image/*" style="display:none;" onchange="document.getElementById('lbl_cover').innerText = this.files[0].name">
                            </label>
                        </div>
                        <div class="form-group">
                            <label>Lampiran Modul PDF (Opsional)</label>
                            <label class="upload-area" onclick="document.getElementById('f_pdf').click()">
                                <i class="fas fa-file-pdf" style="font-size:2rem; color:#ef4444; margin-bottom:10px;"></i>
                                <div id="lbl_pdf">Klik untuk unggah file (.pdf)</div>
                                <input type="file" name="pdf_file" id="f_pdf" accept=".pdf" style="display:none;" onchange="document.getElementById('lbl_pdf').innerText = this.files[0].name">
                            </label>
                        </div>
                    </div>
                    <div class="form-footer" style="justify-content:flex-end;">
                        <button type="button" class="btn btn-primary" onclick="goToStep(2)">Lanjut Tulis Materi <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- STEP 2: EDITOR WORD -->
                <div class="step-content" id="step2">
                    <div class="form-group">
                        <label>Isi Materi (Bisa tambahkan tabel, gambar, link, dll)</label>
                        <textarea name="konten" id="editor"></textarea>
                    </div>
                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(1)"><i class="fas fa-arrow-left"></i> Kembali</button>
                        <button type="button" class="btn btn-primary" onclick="goToStep(3)">Buat Kuis Evaluasi (Opsional) <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                <!-- STEP 3: KUIS -->
                <div class="step-content" id="step3">
                    <div class="form-group" style="background:#f0fdf4; padding:20px; border-radius:12px; border:1px solid #bbf7d0;">
                        <label><i class="fas fa-stopwatch"></i> Pengaturan Waktu Kuis</label>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="number" name="waktu_kuis" id="f_waktu" class="form-control" value="5" min="0" style="width:100px;">
                            <span>Menit (Biarkan 0 jika tanpa batas waktu)</span>
                        </div>
                    </div>

                    <div id="quizContainer">
                        <!-- Kotak Soal akan digenerate ke sini -->
                    </div>

                    <button type="button" class="btn btn-secondary" style="width:100%; border:2px dashed var(--primary); color:var(--primary); background:transparent;" onclick="addQuizBox()"><i class="fas fa-plus"></i> Tambah Pertanyaan Baru</button>

                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="goToStep(2)"><i class="fas fa-arrow-left"></i> Kembali</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan & Terbitkan Materi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let myEditor;
        // Inisialisasi CKEditor 5
        ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'insertTable', 'blockQuote', '|', 'undo', 'redo']
        }).then(editor => {
            myEditor = editor;
        }).catch(error => console.error(error));

        // Navigasi Multi-Step
        function goToStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));

            document.getElementById('step' + step).classList.add('active');
            for (let i = 1; i <= step; i++) {
                document.getElementById('stepIndicator' + i).classList.add('active');
            }
        }

        function openForm() {
            document.getElementById('listView').style.display = 'none';
            document.getElementById('formArea').style.display = 'block';
            document.getElementById('formTitle').innerText = 'Buat Materi Baru';
            document.getElementById('mainForm').reset();
            document.getElementById('id_materi').value = 0;
            myEditor.setData('');
            document.getElementById('quizContainer').innerHTML = '';
            goToStep(1);
        }

        function closeForm() {
            document.getElementById('formArea').style.display = 'none';
            document.getElementById('listView').style.display = 'block';
        }

        function editMateri(data) {
            openForm();
            document.getElementById('formTitle').innerText = 'Edit Materi';
            document.getElementById('id_materi').value = data.id;
            document.getElementById('f_judul').value = data.judul;
            document.getElementById('f_youtube').value = data.youtube_url;
            document.getElementById('f_waktu').value = data.waktu_kuis;
            myEditor.setData(data.konten);

            if (data.soal && data.soal.length > 0) {
                data.soal.forEach(s => addQuizBox(s));
            }
        }

        // Generator UI Kotak Soal
        let quizCounter = 0;

        function addQuizBox(data = null) {
            quizCounter++;
            const div = document.createElement('div');
            div.className = 'quiz-box';
            div.id = 'qbox_' + quizCounter;

            const p = data ? data.pertanyaan : '';
            const oa = data ? data.opsi_a : '';
            const ob = data ? data.opsi_b : '';
            const oc = data ? data.opsi_c : '';
            const od = data ? data.opsi_d : '';
            const jb = data ? data.jawaban_benar : '';
            const imgOld = data && data.gambar ? `<input type="hidden" name="old_gambar_kuis[]" value="${data.gambar}"><div style="font-size:0.8rem; color:var(--primary); margin-top:5px;"><i class="fas fa-check-circle"></i> Gambar tersimpan: ${data.gambar}</div>` : '';

            div.innerHTML = `
                <button type="button" class="btn-remove-quiz" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>
                <div class="form-group">
                    <label>Pertanyaan</label>
                    <textarea name="pertanyaan[]" class="form-control" rows="2" required>${p}</textarea>
                </div>
                <div class="form-group">
                    <label>Sisipkan Gambar (Opsional)</label>
                    <input type="file" name="gambar_kuis[]" class="form-control" accept="image/*" style="padding:10px;">
                    ${imgOld}
                </div>
                <div class="grid-2">
                    <div><label>Opsi A</label><input type="text" name="opsi_a[]" class="form-control" value="${oa}" required></div>
                    <div><label>Opsi B</label><input type="text" name="opsi_b[]" class="form-control" value="${ob}" required></div>
                    <div><label>Opsi C</label><input type="text" name="opsi_c[]" class="form-control" value="${oc}" required></div>
                    <div><label>Opsi D</label><input type="text" name="opsi_d[]" class="form-control" value="${od}" required></div>
                </div>
                <div class="form-group" style="margin-top:15px;">
                    <label>Jawaban Benar</label>
                    <select name="jawaban_benar[]" class="form-control" required style="background:#f0fdf4; font-weight:bold; color:var(--primary);">
                        <option value="a" ${jb=='a'?'selected':''}>A</option>
                        <option value="b" ${jb=='b'?'selected':''}>B</option>
                        <option value="c" ${jb=='c'?'selected':''}>C</option>
                        <option value="d" ${jb=='d'?'selected':''}>D</option>
                    </select>
                </div>
            `;
            document.getElementById('quizContainer').appendChild(div);
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus materi ini?',
                text: "Semua data kuis di dalamnya juga akan terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `materi_tajwid.php?delete=${id}`;
                }
            });
        }
    </script>
</body>

</html>