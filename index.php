<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hifzly - Pendamping Murojaah</title>

    <style>
        /* RESET & VARIABEL WARNA */
        :root {
            --primary: #059669;
            --dark: #1f2937;
            --bg: #f9fafb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            text-align: center;
        }

        /* NAVBAR */
        nav {
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        nav h1 {
            color: var(--primary);
        }

        /* HERO SECTION */
        .hero {
            padding: 40px 20px;
            max-width: 600px;
            margin: 0 auto;
        }

        .hero h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .hero p {
            color: #4b5563;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* BUTTONS */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #047857;
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        /* RESPONSIVE DESKTOP */
        @media (min-width: 600px) {
            .btn-group {
                flex-direction: row;
                justify-content: center;
            }

            .btn {
                width: 150px;
            }
        }
    </style>
</head>

<body>

    <nav>
        <!-- Nanti gambar logonya taruh di assets/images/logo.png -->
        <img src="assets/images/logo.png" alt="Logo" style="width: 40px; height: 40px; border-radius: 50%; background: #ccc;">
        <h1>Hifzly</h1>
    </nav>

    <div class="hero">
        <h2>Jaga Hafalanmu dengan Baik</h2>
        <p>Aplikasi pendamping murojaah Al-Qur'an interaktif untuk membantumu tetap istiqomah setiap hari.</p>

        <div class="btn-group">
            <a href="register.php" class="btn btn-primary" onclick="showLoading()">Sign Up</a>
            <a href="login.php" class="btn btn-outline" onclick="showLoading()">Sign In</a>
        </div>

        <p id="loading-text" style="display:none; color:var(--primary); margin-top:20px;">Memuat halaman...</p>
    </div>

    <script>
        function showLoading() {
            document.getElementById('loading-text').style.display = 'block';
        }
    </script>

</body>

</html>