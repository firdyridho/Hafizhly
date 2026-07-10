<?php
// Pengaturan Database
$host = "sql303.infinityfree.com"; // Di InfinityFree biasanya berbentuk "sqlxxx.epizy.com"
$user = "if0_42360001";      // Di InfinityFree biasanya "epiz_xxxxxxx"
$pass = "s2px6nqvwFFa";          // Password database InfinityFree kamu
$db   = "if0_42360001_hafizhly_db"; // Nama database di InfinityFree

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
