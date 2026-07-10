<?php
session_start();

// Cek apakah yang masuk benar-benar admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Tendang balik ke halaman login
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Admin</title>
</head>

<body>
    <h1>Panel Admin - Hifzly</h1>
    <p>Halo Admin <?= $_SESSION['nama_lengkap']; ?>, di sini tempat mengelola aplikasi.</p>
    <a href="../logout.php">Keluar</a>
</body>

</html>