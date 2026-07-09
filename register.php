<?php
// 1. Panggil koneksi database
require_once 'config/database.php';

$pesan = ''; // Variabel untuk menyimpan pesan sukses/gagal

// 2. Cek apakah tombol "Daftar" sudah ditekan
if (isset($_POST['register'])) {
    // Ambil data dari form dan bersihkan dari karakter berbahaya
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Enkripsi password agar aman di database
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Role default untuk pendaftar baru adalah 'user'
    $role = 'user';

    // Cek apakah email sudah pernah didaftarkan
    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $pesan = "<div class='alert alert-danger'>Email sudah terdaftar! Gunakan email lain.</div>";
    } else {
        // Jika email belum ada, masukkan data ke tabel users
        $query = "INSERT INTO users (nama_lengkap, email, password, role) 
                  VALUES ('$nama_lengkap', '$email', '$password_hashed', '$role')";

        if (mysqli_query($conn, $query)) {
            $pesan = "<div class='alert alert-success'>Pendaftaran berhasil! <a href='login.php'>Silakan Sign In</a></div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Terjadi kesalahan: " . mysqli_error($conn) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Hifzly</title>

    <style>
        :root {
            --primary: #059669;
            --dark: #1f2937;
            --bg: #f9fafb;
            --card-bg: #ffffff;
            --border: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .card {
            background-color: var(--card-bg);
            width: 100%;
            max-width: 400px;
            /* Cocok untuk mobile, tidak terlalu lebar di desktop */
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .card h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 6px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: var(--primary);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #047857;
        }

        .link {
            display: block;
            text-align: center;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #6b7280;
            text-decoration: none;
        }

        .link a {
            color: var(--primary);
            font-weight: bold;
        }

        /* Gaya untuk pesan notifikasi */
        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>Daftar Hifzly</h2>

        <?= $pesan; ?>

        <form action="" method="POST" onsubmit="return validasiPassword()">
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required placeholder="Masukkan nama lengkap">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="nama@email.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter">
            </div>

            <div class="form-group">
                <label for="konfirmasi_password">Konfirmasi Password</label>
                <input type="password" id="konfirmasi_password" required placeholder="Ketik ulang password">
            </div>

            <button type="submit" name="register" class="btn">Daftar Sekarang</button>
        </form>

        <div class="link">
            Sudah punya akun? <a href="login.php">Sign In di sini</a>
        </div>
    </div>

    <script>
        // Fungsi untuk mengecek apakah password dan konfirmasi password sama sebelum dikirim ke PHP
        function validasiPassword() {
            var password = document.getElementById("password").value;
            var konfirmasi = document.getElementById("konfirmasi_password").value;

            if (password !== konfirmasi) {
                alert("Pendaftaran Gagal: Password dan Konfirmasi Password tidak sama!");
                return false; // Mencegah form dikirim ke server
            }
            return true; // Lanjut kirim form
        }
    </script>

</body>

</html>