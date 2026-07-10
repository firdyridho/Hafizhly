<?php
// Pengaturan Database
$host = "sql206.infinityfree.com"; // Di InfinityFree biasanya berbentuk "sqlxxx.epizy.com"
$user = "if0_42360028";      // Di InfinityFree biasanya "epiz_xxxxxxx"
$pass = "ZEQ85K2AIzup";          // Password database InfinityFree kamu
$db   = "if0_42360028_hifzly_db"; // Nama database di InfinityFree

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
