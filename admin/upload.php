<?php
// upload.php - Endpoint upload gambar untuk Jodit Editor
// Simpan di folder yang sama dengan materi_tajwid.php

// Cek apakah ada file yang diunggah
if (!isset($_FILES['files']) && !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tidak ada file']);
    exit;
}

$file = isset($_FILES['files']) ? $_FILES['files'] : $_FILES['file'];

// Validasi error
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Upload error']);
    exit;
}

// Validasi tipe file
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Format file tidak diizinkan']);
    exit;
}

// Validasi ukuran (maks 5MB)
$maxSize = 5 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar (maks 5MB)']);
    exit;
}

// Folder tujuan (relatif terhadap posisi upload.php)
$uploadDir = '/uploads';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Buat nama unik
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$destination = $uploadDir . $newName;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    // URL file yang bisa diakses (sesuaikan dengan struktur folder)
    $fileUrl = '../uploads/' . $newName;
    echo json_encode([
        'success' => true,
        'data' => [
            'baseurl' => '',
            'files' => [$fileUrl],
            'isImages' => [true]
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan file']);
}
