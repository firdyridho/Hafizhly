<?php
session_start();

// 1. NYALAKAN DETEKSI ERROR (Biar kita tahu error-nya di mana)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. PASTIKAN PATH DATABASE BENAR
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_name = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'Admin';

// --- MENGAMBIL DATA STATISTIK DENGAN AMAN (Mencegah Fatal Error PHP 8) ---

// A. Total Users
$total_users = 0;
$q_users = mysqli_query($conn, "SELECT COUNT(id) AS total FROM users WHERE role = 'user'");
if ($q_users) {
    $row = mysqli_fetch_assoc($q_users);
    $total_users = $row ? (int)$row['total'] : 0;
}

// B. Total Materi Tajwid (Cek tabelnya ada atau tidak dulu)
$total_materi = 0;
$cek_materi = mysqli_query($conn, "SHOW TABLES LIKE 'tajwid_materi'");
if ($cek_materi && mysqli_num_rows($cek_materi) > 0) {
    $q_materi = mysqli_query($conn, "SELECT COUNT(id) AS total FROM tajwid_materi");
    if ($q_materi) {
        $row = mysqli_fetch_assoc($q_materi);
        $total_materi = $row ? (int)$row['total'] : 0;
    }
}

// C. Total Hafalan Mutabaah (Cek tabelnya ada atau tidak dulu)
$total_hafalan = 0;
$cek_mutabaah = mysqli_query($conn, "SHOW TABLES LIKE 'mutabaah'");
if ($cek_mutabaah && mysqli_num_rows($cek_mutabaah) > 0) {
    $q_hafalan = mysqli_query($conn, "SELECT SUM(ayah_end - ayah_start + 1) AS total FROM mutabaah");
    if ($q_hafalan) {
        $row = mysqli_fetch_assoc($q_hafalan);
        $total_hafalan = ($row && $row['total']) ? (int)$row['total'] : 0;
    }
}
