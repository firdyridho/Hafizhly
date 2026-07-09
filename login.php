<?php
session_start(); // Wajib dipanggil pertama kali untuk memulai sesi
require_once 'config/database.php';

$pesan = '';

// Jika user sudah login, langsung arahkan ke dashboard masing-masing
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cari user berdasarkan email
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        // Cek kecocokan password
        if (password_verify($password, $user['password'])) {
            // Set session jika berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            // Arahkan berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $pesan = "<div class='alert alert-danger'>Password salah!</div>";
        }
    } else {
        $pesan = "<div class='alert alert-danger'>Email tidak ditemukan!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Hifzly</title>

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
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .logo-container img {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            /* Dibuat sedikit kotak melengkung */
            margin-bottom: 10px;
            object-fit: contain;
        }

        .card h2 {
            color: var(--primary);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
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
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #047857;
        }

        .btn:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
        }

        .link {
            display: block;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #6b7280;
            text-decoration: none;
        }

        .link a {
            color: var(--primary);
            font-weight: bold;
        }

        .alert {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>

    <div class="card">
        <div class="logo-container">
            <img src="assets/images/logo.png" alt="Logo Hifzly" id="app-logo">
        </div>
        <h2>Sign In</h2>

        <?= $pesan; ?>

        <form action="" method="POST" onsubmit="tampilkanLoading()">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="nama@email.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password">
            </div>

            <button type="submit" name="login" class="btn" id="btn-login">Masuk</button>
        </form>

        <div class="link">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </div>
    </div>

    <script>
        function tampilkanLoading() {
            var btn = document.getElementById("btn-login");
            btn.innerHTML = "Memeriksa...";

            // Beri jeda sedikit sebelum tombol dinonaktifkan agar data form tetap terkirim ke PHP
            setTimeout(function() {
                btn.disabled = true;
            }, 10);
        }
    </script>

</body>

</html>