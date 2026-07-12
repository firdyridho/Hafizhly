<?php
// Hubungkan ke database
require_once 'config/database.php';

// --- ATUR AKUN ADMIN KAMU DI SINI ---
$nama_lengkap = "Admin Utama";
$email = "admin@hifzly.com";
$password = "admin123"; // Ganti jika ingin password lain
$role = "admin";

// Enkripsi password (Wajib agar bisa login)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah email sudah ada di database
$cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");

if (mysqli_num_rows($cek_email) > 0) {
    echo "<h1>Gagal!</h1>";
    echo "<p>Akun dengan email <b>$email</b> sudah terdaftar.</p>";
    echo "<a href='login.php'>Pergi ke halaman Login</a>";
} else {
    // Masukkan ke tabel users
    $query = "INSERT INTO users (nama_lengkap, email, password, role) 
              VALUES ('$nama_lengkap', '$email', '$hashed_password', '$role')";

    if (mysqli_query($conn, $query)) {
        echo "<h1>Berhasil! 🎉</h1>";
        echo "<p>Akun Admin berhasil dibuat!</p>";
        echo "<ul>";
        echo "<li>Email: <b>$email</b></li>";
        echo "<li>Password: <b>$password</b></li>";
        echo "</ul>";
        echo "<a href='login.php'>Silakan Login di sini</a>";
        echo "<br><br><strong style='color:red;'>PENTING: Segera hapus file <code>buat_admin.php</code> ini agar tidak disalahgunakan orang lain!</strong>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
