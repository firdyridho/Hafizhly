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

/* =========================================================
   1. AUTO-MIGRATE TABEL (Menambahkan kolom PDF & Gambar Kuis jika belum ada)
   ========================================================= */
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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

/* =========================================================
   Helper: validasi & simpan file upload dengan aman
   ========================================================= */
function simpan_upload($fileArrKey, $allowedExt, $maxSizeMB, $suffix, $index = null)
{
    if ($index === null) {
        if (!isset($_FILES[$fileArrKey]) || $_FILES[$fileArrKey]['error'] != 0) return ['ok' => true, 'name' => ''];
        $tmp   = $_FILES[$fileArrKey]['tmp_name'];
        $name  = $_FILES[$fileArrKey]['name'];
        $size  = $_FILES[$fileArrKey]['size'];
    } else {
        if (!isset($_FILES[$fileArrKey]['name'][$index]) || $_FILES[$fileArrKey]['error'][$index] != 0) return ['ok' => true, 'name' => ''];
        $tmp   = $_FILES[$fileArrKey]['tmp_name'][$index];
        $name  = $_FILES[$fileArrKey]['name'][$index];
        $size  = $_FILES[$fileArrKey]['size'][$index];
    }

    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        return ['ok' => false, 'msg' => "Format file .$ext tidak diizinkan"];
    }
    if ($size > $maxSizeMB * 1024 * 1024) {
        return ['ok' => false, 'msg' => "Ukuran file melebihi {$maxSizeMB}MB"];
    }

    $newName = time() . '_' . bin2hex(random_bytes(4)) . '_' . $suffix . '.' . $ext;
    if (!@move_uploaded_file($tmp, '../uploads/' . $newName)) {
        return ['ok' => false, 'msg' => 'Gagal menyimpan file'];
    }
    return ['ok' => true, 'name' => $newName];
}

$alert = null;

/* =========================================================
   2. HANDLE SIMPAN (TAMBAH / EDIT) — prepared statements
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrf_token) {
        $_SESSION['alert'] = ['type' => 'error', 'msg' => 'Sesi tidak valid, silakan coba lagi.'];
        header("Location: materi_tajwid.php");
        exit();
    }

    $id_materi = isset($_POST['id_materi']) ? (int)$_POST['id_materi'] : 0;
    $judul     = trim($_POST['judul'] ?? '');
    $konten    = $_POST['konten'] ?? '';
    $youtube   = trim($_POST['youtube_url'] ?? '');
    $waktu_kuis = isset($_POST['waktu_kuis']) ? (int)$_POST['waktu_kuis'] : 0;

    if ($judul === '') {
        $_SESSION['alert'] = ['type' => 'error', 'msg' => 'Judul materi wajib diisi!'];
        header("Location: materi_tajwid.php");
        exit();
    }

    $upCover = simpan_upload('cover', ['jpg', 'jpeg', 'png', 'webp'], 5, 'cov');
    $upPdf   = simpan_upload('pdf_file', ['pdf'], 15, 'doc');

    if (!$upCover['ok'] || !$upPdf['ok']) {
        $_SESSION['alert'] = ['type' => 'error', 'msg' => ($upCover['msg'] ?? $upPdf['msg'])];
        header("Location: materi_tajwid.php");
        exit();
    }
    $cover = $upCover['name'];
    $pdf   = $upPdf['name'];

    if ($id_materi > 0) {
        // EDIT MATERI
        if ($cover !== '' && $pdf !== '') {
            $stmt = mysqli_prepare($conn, "UPDATE tajwid_materi SET judul=?, konten=?, youtube_url=?, waktu_kuis=?, cover_image=?, pdf_file=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssissi", $judul, $konten, $youtube, $waktu_kuis, $cover, $pdf, $id_materi);
        } elseif ($cover !== '') {
            $stmt = mysqli_prepare($conn, "UPDATE tajwid_materi SET judul=?, konten=?, youtube_url=?, waktu_kuis=?, cover_image=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssisi", $judul, $konten, $youtube, $waktu_kuis, $cover, $id_materi);
        } elseif ($pdf !== '') {
            $stmt = mysqli_prepare($conn, "UPDATE tajwid_materi SET judul=?, konten=?, youtube_url=?, waktu_kuis=?, pdf_file=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssisi", $judul, $konten, $youtube, $waktu_kuis, $pdf, $id_materi);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE tajwid_materi SET judul=?, konten=?, youtube_url=?, waktu_kuis=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssii", $judul, $konten, $youtube, $waktu_kuis, $id_materi);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Hapus soal kuis lama (beserta file gambar lama yang tidak dipertahankan) sebelum insert ulang
        $stmtDel = mysqli_prepare($conn, "DELETE FROM tajwid_kuis WHERE materi_id=?");
        mysqli_stmt_bind_param($stmtDel, "i", $id_materi);
        mysqli_stmt_execute($stmtDel);
        mysqli_stmt_close($stmtDel);
    } else {
        // TAMBAH MATERI BARU
        $stmt = mysqli_prepare($conn, "INSERT INTO tajwid_materi (judul, cover_image, pdf_file, konten, youtube_url, waktu_kuis) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssssi", $judul, $cover, $pdf, $konten, $youtube, $waktu_kuis);
        mysqli_stmt_execute($stmt);
        $id_materi = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
    }

    // PROSES SOAL KUIS (Jika ada)
    if (isset($_POST['pertanyaan']) && is_array($_POST['pertanyaan'])) {
        $stmtQ = mysqli_prepare($conn, "INSERT INTO tajwid_kuis (materi_id, pertanyaan, gambar, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        for ($i = 0; $i < count($_POST['pertanyaan']); $i++) {
            $tanya = trim($_POST['pertanyaan'][$i]);
            if ($tanya === '') continue;

            $oa = $_POST['opsi_a'][$i] ?? '';
            $ob = $_POST['opsi_b'][$i] ?? '';
            $oc = $_POST['opsi_c'][$i] ?? '';
            $od = $_POST['opsi_d'][$i] ?? '';
            $jb = $_POST['jawaban_benar'][$i] ?? 'a';

            $upGbr = simpan_upload('gambar_kuis', ['jpg', 'jpeg', 'png', 'webp'], 5, 'q' . $i, $i);
            $gbr_kuis = $upGbr['ok'] ? $upGbr['name'] : '';
            if ($gbr_kuis === '' && isset($_POST['old_gambar_kuis'][$i])) {
                $gbr_kuis = $_POST['old_gambar_kuis'][$i]; // pertahankan gambar lama
            }

            mysqli_stmt_bind_param($stmtQ, "isssssss", $id_materi, $tanya, $gbr_kuis, $oa, $ob, $oc, $od, $jb);
            mysqli_stmt_execute($stmtQ);
        }
        mysqli_stmt_close($stmtQ);
    }

    $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Materi berhasil disimpan!'];
    header("Location: materi_tajwid.php");
    exit();
}

/* =========================================================
   HANDLE HAPUS — cascade hapus kuis + file terkait
   ========================================================= */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $files = [];
    $stmtF = mysqli_prepare($conn, "SELECT cover_image, pdf_file FROM tajwid_materi WHERE id=?");
    mysqli_stmt_bind_param($stmtF, "i", $id);
    mysqli_stmt_execute($stmtF);
    $resF = mysqli_stmt_get_result($stmtF);
    if ($rowF = mysqli_fetch_assoc($resF)) {
        if (!empty($rowF['cover_image'])) $files[] = $rowF['cover_image'];
        if (!empty($rowF['pdf_file'])) $files[] = $rowF['pdf_file'];
    }
    mysqli_stmt_close($stmtF);

    $stmtQF = mysqli_prepare($conn, "SELECT gambar FROM tajwid_kuis WHERE materi_id=?");
    mysqli_stmt_bind_param($stmtQF, "i", $id);
    mysqli_stmt_execute($stmtQF);
    $resQF = mysqli_stmt_get_result($stmtQF);
    while ($rq = mysqli_fetch_assoc($resQF)) {
        if (!empty($rq['gambar'])) $files[] = $rq['gambar'];
    }
    mysqli_stmt_close($stmtQF);

    $stmtDK = mysqli_prepare($conn, "DELETE FROM tajwid_kuis WHERE materi_id=?");
    mysqli_stmt_bind_param($stmtDK, "i", $id);
    mysqli_stmt_execute($stmtDK);
    mysqli_stmt_close($stmtDK);

    $stmtDM = mysqli_prepare($conn, "DELETE FROM tajwid_materi WHERE id=?");
    mysqli_stmt_bind_param($stmtDM, "i", $id);
    mysqli_stmt_execute($stmtDM);
    mysqli_stmt_close($stmtDM);

    foreach ($files as $f) {
        $path = '../uploads/' . $f;
        if (file_exists($path)) @unlink($path);
    }

    $_SESSION['alert'] = ['type' => 'success', 'msg' => 'Materi berhasil dihapus!'];
    header("Location: materi_tajwid.php");
    exit();
}

/* =========================================================
   3. AMBIL DATA UNTUK DITAMPILKAN
   ========================================================= */
$materi_list = [];
$q_m = mysqli_query($conn, "SELECT * FROM tajwid_materi ORDER BY created_at DESC");
while ($row = mysqli_fetch_assoc($q_m)) {
    $stmtC = mysqli_prepare($conn, "SELECT COUNT(id) as total FROM tajwid_kuis WHERE materi_id = ?");
    mysqli_stmt_bind_param($stmtC, "i", $row['id']);
    mysqli_stmt_execute($stmtC);
    $row['total_soal'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtC))['total'] ?? 0;
    mysqli_stmt_close($stmtC);

    $soal = [];
    $stmtS = mysqli_prepare($conn, "SELECT * FROM tajwid_kuis WHERE materi_id = ?");
    mysqli_stmt_bind_param($stmtS, "i", $row['id']);
    mysqli_stmt_execute($stmtS);
    $resS = mysqli_stmt_get_result($stmtS);
    while ($s = mysqli_fetch_assoc($resS)) {
        $soal[] = $s;
    }
    mysqli_stmt_close($stmtS);
    $row['soal'] = $soal;

    $materi_list[] = $row;
}

$alertData = null;
if (isset($_SESSION['alert'])) {
    $alertData = $_SESSION['alert'];
    unset($_SESSION['alert']);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Tajwid — Admin Hafizhly</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --primary-light: #10b981;
            --primary-50: #ecfdf5;
            --primary-100: #d1fae5;
            --ink: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --danger-50: #fee2e2;
            --bg: #f6faf8;
            --radius-lg: clamp(16px, 2vw, 22px);
            --radius-md: 14px;
            --ease: cubic-bezier(.22, 1, .36, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(1200px 500px at 100% -10%, var(--primary-100), transparent 60%),
                radial-gradient(900px 400px at -10% 10%, #f0fdf4, transparent 55%),
                var(--bg);
            color: var(--ink);
            margin: 0;
            padding: clamp(14px, 3vw, 28px);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ---------- Header ---------- */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: clamp(20px, 3vw, 32px);
            flex-wrap: wrap;
        }

        .header-title {
            font-size: clamp(1.4rem, 3vw, 1.9rem);
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-title .icon-badge {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(5, 150, 105, .3);
            flex-shrink: 0;
        }

        .header-sub {
            color: var(--muted);
            margin-top: 4px;
            font-size: .95rem;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ---------- Buttons ---------- */
        .btn {
            padding: 12px 22px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: transform .18s var(--ease), box-shadow .18s var(--ease), background .18s var(--ease);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .93rem;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn:active {
            transform: scale(.97);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            color: white;
            box-shadow: 0 6px 18px rgba(5, 150, 105, .28);
        }

        .btn-primary:hover {
            box-shadow: 0 10px 24px rgba(5, 150, 105, .38);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: white;
            color: var(--ink);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .btn-danger-soft {
            background: var(--danger-50);
            color: var(--danger);
        }

        .btn-danger-soft:hover {
            background: #fecaca;
        }

        .btn-icon {
            padding: 12px;
        }

        .btn[disabled] {
            opacity: .6;
            cursor: not-allowed;
        }

        /* ---------- Search ---------- */
        .search-bar {
            position: relative;
            margin-bottom: 22px;
            max-width: 380px;
        }

        .search-bar i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
        }

        .search-bar input {
            width: 100%;
            padding: 13px 16px 13px 44px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: white;
            font-family: inherit;
            font-size: .92rem;
            outline: none;
            transition: box-shadow .2s var(--ease), border-color .2s var(--ease);
        }

        .search-bar input:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px var(--primary-100);
        }

        /* ---------- Cards grid ---------- */
        .grid-materi {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(280px, 100%), 1fr));
            gap: 18px;
        }

        .materi-card {
            background: rgba(255, 255, 255, .85);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            padding: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 6px 20px rgba(15, 23, 42, .04);
            transition: transform .3s var(--ease), box-shadow .3s var(--ease), border-color .3s var(--ease);
            opacity: 0;
            animation: cardIn .5s var(--ease) forwards;
            display: flex;
            flex-direction: column;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(14px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .materi-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary-light);
            box-shadow: 0 18px 34px rgba(5, 150, 105, .14);
        }

        .mc-cover {
            width: 100%;
            height: 130px;
            border-radius: 12px;
            margin-bottom: 14px;
            background-size: cover;
            background-position: center;
            background-color: var(--primary-50);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-light);
            font-size: 1.8rem;
        }

        .mc-title {
            font-size: 1.08rem;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.4;
            flex-grow: 1;
        }

        .mc-meta {
            font-size: .82rem;
            color: var(--muted);
            margin-bottom: 18px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .mc-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .mc-actions {
            display: flex;
            gap: 10px;
        }

        .mc-actions .btn {
            flex: 1;
            justify-content: center;
            padding: 10px;
            font-size: .85rem;
        }

        .empty-state {
            grid-column: 1/-1;
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: var(--radius-lg);
            border: 1px dashed var(--border);
            color: var(--muted);
        }

        .empty-state i {
            font-size: 2.4rem;
            color: var(--primary-light);
            margin-bottom: 14px;
            display: block;
        }

        /* ---------- Form area (SPA) ---------- */
        #formArea {
            display: none;
            background: rgba(255, 255, 255, .9);
            backdrop-filter: blur(12px);
            border-radius: var(--radius-lg);
            padding: clamp(18px, 4vw, 34px);
            box-shadow: 0 20px 50px rgba(15, 23, 42, .08);
            border: 1px solid var(--border);
            opacity: 0;
            transform: translateY(12px);
            transition: opacity .35s var(--ease), transform .35s var(--ease);
        }

        #formArea.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Stepper */
        .stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: clamp(24px, 4vw, 36px);
            position: relative;
            gap: 6px;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 24px;
            right: 24px;
            height: 3px;
            background: var(--border);
            z-index: 1;
            border-radius: 3px;
        }

        .stepper-fill {
            position: absolute;
            top: 15px;
            left: 24px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-light), var(--primary));
            z-index: 1;
            border-radius: 3px;
            width: 0%;
            transition: width .4s var(--ease);
        }

        .step-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-weight: 600;
            font-size: clamp(.72rem, 2vw, .85rem);
            flex: 1;
            text-align: center;
        }

        .step-item.active,
        .step-item.done {
            color: var(--primary-dark);
        }

        .step-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--border);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            transition: all .3s var(--ease);
        }

        .step-item.active .step-num {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 0 0 6px var(--primary-100);
        }

        .step-item.done .step-num {
            background: var(--primary-light);
            border-color: var(--primary-light);
            color: white;
        }

        /* Step content with slide transition */
        .step-content-wrap {
            position: relative;
            overflow: hidden;
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: stepIn .4s var(--ease);
        }

        @keyframes stepIn {
            from {
                opacity: 0;
                transform: translateX(16px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: .88rem;
            color: var(--ink);
        }

        .form-hint {
            font-size: .78rem;
            color: var(--muted);
            margin-top: 6px;
        }

        .form-control {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            outline: none;
            font-family: inherit;
            font-size: .93rem;
            background: white;
            transition: border-color .2s var(--ease), box-shadow .2s var(--ease);
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px var(--primary-100);
        }

        .form-control.invalid {
            border-color: var(--danger);
            box-shadow: 0 0 0 4px var(--danger-50);
        }

        /* Upload */
        .upload-area {
            border: 2px dashed #cbd5e1;
            padding: 26px 14px;
            text-align: center;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all .25s var(--ease);
            background: #f8fafc;
            display: block;
        }

        .upload-area:hover,
        .upload-area.dragover {
            border-color: var(--primary-light);
            background: var(--primary-50);
        }

        .upload-area i {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .upload-area .lbl {
            font-size: .85rem;
            color: var(--muted);
            word-break: break-word;
        }

        .upload-preview {
            margin-top: 10px;
            border-radius: 10px;
            max-height: 110px;
            display: none;
        }

        .ck-editor__editable {
            min-height: 380px;
            font-family: inherit;
            font-size: 1.02rem;
            border-radius: 0 0 12px 12px !important;
        }

        .ck.ck-toolbar {
            border-radius: 12px 12px 0 0 !important;
        }

        .ck.ck-editor__main>.ck-editor__editable,
        .ck.ck-toolbar {
            border-color: var(--border) !important;
        }

        /* Quiz box */
        .quiz-box {
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 20px;
            margin-bottom: 18px;
            background: #fafcfb;
            position: relative;
            animation: cardIn .35s var(--ease);
        }

        .quiz-box-title {
            font-weight: 700;
            font-size: .82rem;
            color: var(--primary-dark);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-remove-quiz {
            position: absolute;
            top: 14px;
            right: 14px;
            background: var(--danger-50);
            color: var(--danger);
            border: none;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            cursor: pointer;
            transition: all .2s var(--ease);
        }

        .btn-remove-quiz:hover {
            background: var(--danger);
            color: white;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .quiz-empty {
            text-align: center;
            padding: 30px;
            color: var(--muted);
            font-size: .88rem;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Loader overlay for submit */
        .btn .fa-spinner {
            display: none;
        }

        .btn.loading .fa-spinner {
            display: inline-block;
            animation: spin .7s linear infinite;
        }

        .btn.loading .fa-save,
        .btn.loading .btn-label-text {
            display: none;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 640px) {

            .grid-2,
            .grid-4 {
                grid-template-columns: 1fr;
            }

            .step-item span.step-label {
                display: none;
            }

            .header-actions {
                width: 100%;
            }

            .header-actions .btn {
                flex: 1;
                justify-content: center;
            }

            .form-footer {
                flex-direction: column-reverse;
            }

            .form-footer .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- HEADER -->
        <div id="listView">
            <div class="header-top">
                <div class="header-title">
                    <div class="icon-badge"><i class="fas fa-layer-group"></i></div>
                    <div>
                        Studio Tajwid
                        <div class="header-sub">Kelola materi, PDF, dan evaluasi santri.</div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="dashboard.php" class="btn btn-secondary btn-icon"><i class="fas fa-home"></i></a>
                    <button class="btn btn-primary" onclick="openForm()"><i class="fas fa-plus"></i> Buat Materi Baru</button>
                </div>
            </div>

            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari materi..." oninput="filterMateri(this.value)">
            </div>

            <div class="grid-materi" id="materiGrid">
                <?php if (empty($materi_list)): ?>
                    <div class="empty-state">
                        <i class="fas fa-book-open"></i>
                        Belum ada materi. Klik "Buat Materi Baru" untuk memulai.
                    </div>
                <?php endif; ?>

                <?php foreach ($materi_list as $idx => $m): ?>
                    <div class="materi-card" data-title="<?= htmlspecialchars(strtolower($m['judul'])) ?>" style="animation-delay: <?= min($idx * 0.05, 0.5) ?>s">
                        <?php if (!empty($m['cover_image'])): ?>
                            <div class="mc-cover" style="background-image:url('../uploads/<?= htmlspecialchars($m['cover_image']) ?>')"></div>
                        <?php else: ?>
                            <div class="mc-cover"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                        <div class="mc-title"><?= htmlspecialchars($m['judul']) ?></div>
                        <div class="mc-meta">
                            <span><i class="far fa-clock"></i> <?= (int)$m['waktu_kuis'] ?>m Kuis</span>
                            <span><i class="fas fa-list-ol"></i> <?= (int)$m['total_soal'] ?> Soal</span>
                            <?php if (!empty($m['pdf_file'])): ?><span><i class="fas fa-file-pdf" style="color:#ef4444;"></i> PDF</span><?php endif; ?>
                        </div>
                        <div class="mc-actions">
                            <button class="btn btn-secondary" onclick='editMateri(<?= json_encode($m, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-danger-soft btn-icon" onclick="confirmDelete(<?= (int)$m['id'] ?>)"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- FORM SPA (MULTI-STEP) -->
        <div id="formArea">
            <div class="header-top" style="margin-bottom:8px;">
                <h2 id="formTitle" style="font-size:clamp(1.2rem,3vw,1.5rem); font-weight:800; margin:0;">Buat Materi Baru</h2>
                <button type="button" class="btn btn-secondary" onclick="closeForm()"><i class="fas fa-times"></i> Batal</button>
            </div>

            <div class="stepper">
                <div class="stepper-fill" id="stepperFill"></div>
                <div class="step-item active" id="stepIndicator1">
                    <div class="step-num">1</div> <span class="step-label">Info Dasar</span>
                </div>
                <div class="step-item" id="stepIndicator2">
                    <div class="step-num">2</div> <span class="step-label">Tulis Materi</span>
                </div>
                <div class="step-item" id="stepIndicator3">
                    <div class="step-num">3</div> <span class="step-label">Buat Kuis</span>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" id="mainForm" novalidate>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="id_materi" id="id_materi" value="0">

                <div class="step-content-wrap">
                    <!-- STEP 1 -->
                    <div class="step-content active" id="step1">
                        <div class="form-group">
                            <label>Judul Materi <span style="color:var(--danger)">*</span></label>
                            <input type="text" name="judul" id="f_judul" class="form-control" placeholder="Contoh: Hukum Bacaan Nun Mati...">
                        </div>
                        <div class="form-group">
                            <label>Link Video YouTube (Opsional)</label>
                            <input type="url" name="youtube_url" id="f_youtube" class="form-control" placeholder="https://youtube.com/watch?v=...">
                        </div>
                        <div class="grid-2">
                            <div class="form-group">
                                <label>Foto Sampul / Cover (Opsional)</label>
                                <label class="upload-area" id="dropCover" onclick="document.getElementById('f_cover').click()">
                                    <i class="fas fa-image" style="color:var(--primary);"></i>
                                    <div class="lbl" id="lbl_cover">Klik atau seret foto (JPG/PNG/WEBP, maks 5MB)</div>
                                    <input type="file" name="cover" id="f_cover" accept="image/*" style="display:none;">
                                    <img class="upload-preview" id="preview_cover">
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Lampiran Modul PDF (Opsional)</label>
                                <label class="upload-area" id="dropPdf" onclick="document.getElementById('f_pdf').click()">
                                    <i class="fas fa-file-pdf" style="color:#ef4444;"></i>
                                    <div class="lbl" id="lbl_pdf">Klik atau seret file (.pdf, maks 15MB)</div>
                                    <input type="file" name="pdf_file" id="f_pdf" accept=".pdf" style="display:none;">
                                </label>
                            </div>
                        </div>
                        <div class="form-footer" style="justify-content:flex-end;">
                            <button type="button" class="btn btn-primary" onclick="goToStep(2)">Lanjut Tulis Materi <i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- STEP 2 -->
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

                    <!-- STEP 3 -->
                    <div class="step-content" id="step3">
                        <div class="form-group" style="background:var(--primary-50); padding:18px; border-radius:var(--radius-md); border:1px solid var(--primary-100);">
                            <label><i class="fas fa-stopwatch"></i> Pengaturan Waktu Kuis</label>
                            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                <input type="number" name="waktu_kuis" id="f_waktu" class="form-control" value="5" min="0" style="width:110px;">
                                <span style="font-size:.85rem; color:var(--muted);">Menit (isi 0 jika tanpa batas waktu)</span>
                            </div>
                        </div>

                        <div id="quizContainer"></div>
                        <div class="quiz-empty" id="quizEmptyState"><i class="fas fa-list-check" style="font-size:1.6rem; display:block; margin-bottom:8px; color:var(--primary-light);"></i>Belum ada soal kuis. Soal bersifat opsional.</div>

                        <button type="button" class="btn btn-secondary" style="width:100%; border:2px dashed var(--primary); color:var(--primary-dark); background:transparent;" onclick="addQuizBox()"><i class="fas fa-plus"></i> Tambah Pertanyaan Baru</button>

                        <div class="form-footer">
                            <button type="button" class="btn btn-secondary" onclick="goToStep(2)"><i class="fas fa-arrow-left"></i> Kembali</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-spinner"></i>
                                <i class="fas fa-save"></i>
                                <span class="btn-label-text">Simpan &amp; Terbitkan Materi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const ALERT_DATA = <?= $alertData ? json_encode($alertData) : 'null' ?>;

        document.addEventListener('DOMContentLoaded', () => {
            if (ALERT_DATA) {
                Swal.fire({
                    icon: ALERT_DATA.type === 'error' ? 'error' : 'success',
                    title: ALERT_DATA.msg,
                    timer: 2500,
                    showConfirmButton: false
                });
            }
        });

        let myEditor;
        ClassicEditor.create(document.querySelector('#editor'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'insertTable', 'blockQuote', '|', 'undo', 'redo']
        }).then(editor => {
            myEditor = editor;
        }).catch(err => console.error(err));

        /* ---------- Stepper navigation ---------- */
        const stepFills = {
            1: '2%',
            2: '50%',
            3: '98%'
        };

        function validateStep(step) {
            if (step === 2) { // moving away from step1 into step2 -> validate step1
                const judul = document.getElementById('f_judul');
                if (judul.value.trim() === '') {
                    judul.classList.add('invalid');
                    judul.focus();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Judul materi wajib diisi',
                        timer: 1800,
                        showConfirmButton: false
                    });
                    return false;
                }
                judul.classList.remove('invalid');
            }
            return true;
        }

        function goToStep(step) {
            if (!validateStep(step)) return;

            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active', 'done'));

            document.getElementById('step' + step).classList.add('active');
            for (let i = 1; i <= 3; i++) {
                const ind = document.getElementById('stepIndicator' + i);
                if (i < step) ind.classList.add('done');
                if (i === step) ind.classList.add('active');
            }
            document.getElementById('stepperFill').style.width = stepFills[step];
        }

        function openForm() {
            document.getElementById('listView').style.display = 'none';
            const formArea = document.getElementById('formArea');
            formArea.style.display = 'block';
            requestAnimationFrame(() => formArea.classList.add('visible'));

            document.getElementById('formTitle').innerText = 'Buat Materi Baru';
            document.getElementById('mainForm').reset();
            document.getElementById('id_materi').value = 0;
            document.getElementById('lbl_cover').innerText = 'Klik atau seret foto (JPG/PNG/WEBP, maks 5MB)';
            document.getElementById('lbl_pdf').innerText = 'Klik atau seret file (.pdf, maks 15MB)';
            document.getElementById('preview_cover').style.display = 'none';
            if (myEditor) myEditor.setData('');
            document.getElementById('quizContainer').innerHTML = '';
            toggleQuizEmpty();
            goToStep(1);
        }

        function closeForm() {
            const formArea = document.getElementById('formArea');
            formArea.classList.remove('visible');
            setTimeout(() => {
                formArea.style.display = 'none';
                document.getElementById('listView').style.display = 'block';
            }, 250);
        }

        function editMateri(data) {
            openForm();
            document.getElementById('formTitle').innerText = 'Edit Materi';
            document.getElementById('id_materi').value = data.id;
            document.getElementById('f_judul').value = data.judul;
            document.getElementById('f_youtube').value = data.youtube_url || '';
            document.getElementById('f_waktu').value = data.waktu_kuis;
            if (data.cover_image) {
                document.getElementById('lbl_cover').innerText = 'Cover tersimpan: ' + data.cover_image;
                const prev = document.getElementById('preview_cover');
                prev.src = '../uploads/' + data.cover_image;
                prev.style.display = 'block';
            }
            if (data.pdf_file) {
                document.getElementById('lbl_pdf').innerText = 'PDF tersimpan: ' + data.pdf_file;
            }
            setTimeout(() => {
                if (myEditor) myEditor.setData(data.konten || '');
            }, 100);

            document.getElementById('quizContainer').innerHTML = '';
            if (data.soal && data.soal.length > 0) {
                data.soal.forEach(s => addQuizBox(s));
            }
            toggleQuizEmpty();
        }

        /* ---------- Upload preview + drag&drop ---------- */
        document.getElementById('f_cover').addEventListener('change', function() {
            if (this.files[0]) {
                document.getElementById('lbl_cover').innerText = this.files[0].name;
                const prev = document.getElementById('preview_cover');
                prev.src = URL.createObjectURL(this.files[0]);
                prev.style.display = 'block';
            }
        });
        document.getElementById('f_pdf').addEventListener('change', function() {
            if (this.files[0]) document.getElementById('lbl_pdf').innerText = this.files[0].name;
        });

        function setupDropZone(dropId, inputId) {
            const dz = document.getElementById(dropId);
            const input = document.getElementById(inputId);
            ['dragenter', 'dragover'].forEach(evt => dz.addEventListener(evt, e => {
                e.preventDefault();
                dz.classList.add('dragover');
            }));
            ['dragleave', 'drop'].forEach(evt => dz.addEventListener(evt, e => {
                e.preventDefault();
                dz.classList.remove('dragover');
            }));
            dz.addEventListener('drop', e => {
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
        setupDropZone('dropCover', 'f_cover');
        setupDropZone('dropPdf', 'f_pdf');

        /* ---------- Quiz box generator ---------- */
        let quizCounter = 0;

        function toggleQuizEmpty() {
            const empty = document.getElementById('quizEmptyState');
            empty.style.display = document.getElementById('quizContainer').children.length ? 'none' : 'block';
        }

        function addQuizBox(data = null) {
            quizCounter++;
            const div = document.createElement('div');
            div.className = 'quiz-box';
            div.id = 'qbox_' + quizCounter;

            const esc = (s) => (s || '').replace(/"/g, '&quot;');
            const p = esc(data ? data.pertanyaan : '');
            const oa = esc(data ? data.opsi_a : '');
            const ob = esc(data ? data.opsi_b : '');
            const oc = esc(data ? data.opsi_c : '');
            const od = esc(data ? data.opsi_d : '');
            const jb = data ? data.jawaban_benar : '';
            const imgOld = data && data.gambar ?
                `<input type="hidden" name="old_gambar_kuis[]" value="${esc(data.gambar)}"><div style="font-size:0.78rem; color:var(--primary-dark); margin-top:6px;"><i class="fas fa-check-circle"></i> Gambar tersimpan: ${esc(data.gambar)}</div>` :
                `<input type="hidden" name="old_gambar_kuis[]" value="">`;

            div.innerHTML = `
                <button type="button" class="btn-remove-quiz" onclick="removeQuizBox('qbox_${quizCounter}')"><i class="fas fa-trash"></i></button>
                <div class="quiz-box-title"><i class="fas fa-circle-question"></i> Soal #${quizCounter}</div>
                <div class="form-group">
                    <label>Pertanyaan</label>
                    <textarea name="pertanyaan[]" class="form-control" rows="2">${p}</textarea>
                </div>
                <div class="form-group">
                    <label>Sisipkan Gambar (Opsional)</label>
                    <input type="file" name="gambar_kuis[]" class="form-control" accept="image/*" style="padding:10px;">
                    ${imgOld}
                </div>
                <div class="grid-4">
                    <div><label>Opsi A</label><input type="text" name="opsi_a[]" class="form-control" value="${oa}"></div>
                    <div><label>Opsi B</label><input type="text" name="opsi_b[]" class="form-control" value="${ob}"></div>
                    <div><label>Opsi C</label><input type="text" name="opsi_c[]" class="form-control" value="${oc}"></div>
                    <div><label>Opsi D</label><input type="text" name="opsi_d[]" class="form-control" value="${od}"></div>
                </div>
                <div class="form-group" style="margin-top:15px;">
                    <label>Jawaban Benar</label>
                    <select name="jawaban_benar[]" class="form-control" style="background:var(--primary-50); font-weight:bold; color:var(--primary-dark);">
                        <option value="a" ${jb=='a'?'selected':''}>A</option>
                        <option value="b" ${jb=='b'?'selected':''}>B</option>
                        <option value="c" ${jb=='c'?'selected':''}>C</option>
                        <option value="d" ${jb=='d'?'selected':''}>D</option>
                    </select>
                </div>
            `;
            document.getElementById('quizContainer').appendChild(div);
            toggleQuizEmpty();
        }

        function removeQuizBox(id) {
            const el = document.getElementById(id);
            el.style.transition = 'opacity .2s, transform .2s';
            el.style.opacity = '0';
            el.style.transform = 'translateX(10px)';
            setTimeout(() => {
                el.remove();
                toggleQuizEmpty();
            }, 200);
        }

        /* ---------- Search / filter ---------- */
        function filterMateri(term) {
            term = term.trim().toLowerCase();
            document.querySelectorAll('#materiGrid .materi-card').forEach(card => {
                card.style.display = card.dataset.title.includes(term) ? '' : 'none';
            });
        }

        /* ---------- Delete confirm ---------- */
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus materi ini?',
                text: "Semua data kuis di dalamnya juga akan terhapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'materi_tajwid.php?delete=' + id;
                }
            });
        }

        /* ---------- Submit loading state ---------- */
        document.getElementById('mainForm').addEventListener('submit', function(e) {
            const judul = document.getElementById('f_judul');
            if (judul.value.trim() === '') {
                e.preventDefault();
                goToStep(1);
                judul.classList.add('invalid');
                Swal.fire({
                    icon: 'warning',
                    title: 'Judul materi wajib diisi',
                    timer: 1800,
                    showConfirmButton: false
                });
                return;
            }
            if (myEditor) document.getElementById('editor').value = myEditor.getData();
            document.getElementById('submitBtn').classList.add('loading');
            document.getElementById('submitBtn').setAttribute('disabled', 'true');
        });
    </script>
</body>

</html>