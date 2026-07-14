<?php

/**
 * Endpoint upload gambar untuk CKEditor (SimpleUploadAdapter).
 * Dipanggil otomatis saat admin drag-drop / sisip gambar di dalam editor "Isi Materi".
 * Taruh file ini satu folder dengan materi_tajwid.php.
 */
session_start();
header('Content-Type: application/json');

if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
} else {
    http_response_code(500);
    echo json_encode(['error' => ['message' => 'Konfigurasi database tidak ditemukan.']]);
    exit();
}

// Hanya admin yang sedang login yang boleh upload
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => ['message' => 'Akses ditolak. Silakan login ulang.']]);
    exit();
}

// Validasi CSRF token yang dikirim lewat header X-CSRF-Token
$sentToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sentToken)) {
    http_response_code(403);
    echo json_encode(['error' => ['message' => 'Sesi tidak valid, silakan muat ulang halaman.']]);
    exit();
}

if (!isset($_FILES['upload']) || $_FILES['upload']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'Tidak ada file terkirim atau upload gagal.']]);
    exit();
}

$allowedExt  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$maxSizeMB   = 5;

$tmp  = $_FILES['upload']['tmp_name'];
$name = $_FILES['upload']['name'];
$size = $_FILES['upload']['size'];

$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt, true)) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => "Format .$ext tidak diizinkan. Gunakan JPG, PNG, WEBP, atau GIF."]]);
    exit();
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $tmp);
finfo_close($finfo);
if (!in_array($mime, $allowedMime, true)) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => 'Jenis file tidak valid.']]);
    exit();
}

if ($size > $maxSizeMB * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => ['message' => "Ukuran file melebihi {$maxSizeMB}MB."]]);
    exit();
}

if (!file_exists('../uploads')) {
    @mkdir('../uploads', 0777, true);
}

$newName = time() . '_' . bin2hex(random_bytes(4)) . '_content.' . $ext;
if (!@move_uploaded_file($tmp, '../uploads/' . $newName)) {
    http_response_code(500);
    echo json_encode(['error' => ['message' => 'Gagal menyimpan file ke server.']]);
    exit();
}

// CKEditor SimpleUploadAdapter expects: { "url": "..." }
echo json_encode(['url' => '../uploads/' . $newName]);
