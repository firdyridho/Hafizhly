<?php
// Pengaturan Database
$host = "localhost"; // Di InfinityFree biasanya berbentuk "sqlxxx.epizy.com"
$user = "root";      // Di InfinityFree biasanya "epiz_xxxxxxx"
$pass = "";          // Password database InfinityFree kamu
$db   = "hafizhly_db"; // Nama database di InfinityFree

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
