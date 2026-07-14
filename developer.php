<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer - Hifzly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #059669;
            --primary-light: #d1fae5;
            --dark: #0f172a;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* NAVBAR */
        .navbar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid var(--border);
        }

        .nav-brand {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-back {
            background: var(--dark);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: var(--primary);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }

        /* HERO SECTION */
        .hero {
            padding: 150px 5% 80px;
            text-align: center;
            background: linear-gradient(to bottom, #ecfdf5, var(--bg));
        }

        .hero-badge {
            display: inline-block;
            background: var(--primary-light);
            color: var(--primary);
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        /* PROFIL TIM */
        .team-container {
            max-width: 1000px;
            margin: 0 auto 80px auto;
            padding: 0 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .team-card {
            background: white;
            border-radius: 30px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border);
            transition: 0.4s;
            position: relative;
            overflow: hidden;
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(5, 150, 105, 0.1);
            border-color: var(--primary);
        }

        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 25px auto;
            border: 5px solid white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }

        /* Avatar Khusus Cewek (Faeyza) */
        .avatar-female {
            background: linear-gradient(135deg, #fce7f3, #fbcfe8);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 3.5rem;
            color: #db2777;
        }

        /* Avatar Khusus Cowok (Firdy) */
        .avatar-male {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 3.5rem;
            color: #0284c7;
        }

        .name {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .role {
            font-size: 0.95rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .bio {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 25px;
            line-height: 1.7;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-btn {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            background: var(--bg);
            color: var(--text-muted);
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            font-size: 1.2rem;
            transition: 0.3s;
            border: 1px solid var(--border);
        }

        .social-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        /* TECH STACK SECTION */
        .tech-section {
            background: white;
            padding: 80px 5%;
            text-align: center;
            border-top: 1px solid var(--border);
        }

        .tech-section h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 40px;
        }

        .tech-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }

        .tech-item {
            background: var(--bg);
            padding: 20px 30px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 700;
            color: var(--dark);
            border: 1px solid var(--border);
            transition: 0.3s;
        }

        .tech-item:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary);
        }

        .tech-item i {
            font-size: 1.8rem;
        }

        /* FOOTER */
        footer {
            background: var(--dark);
            color: white;
            text-align: center;
            padding: 30px;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }

            .team-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            <i class="fas fa-leaf"></i> Hifzly
        </a>
        <a href="index.php" class="btn-back"><i class="fas fa-home"></i> Beranda</a>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-badge">Tim Pengembang</div>
        <h1>Membangun Hifzly dengan<br><span style="color: var(--primary);">Cinta & Dedikasi</span></h1>
        <p>Hifzly lahir dari keinginan untuk membantu umat Islam menjaga hafalannya dengan teknologi cerdas, mudah, dan menyenangkan. Kenali sosok di balik layar aplikasi ini.</p>
    </section>

    <!-- PROFIL TIM -->
    <section class="team-container">

        <!-- PROFIL 1: FAEYZA (CEWEK) -->
        <div class="team-card">
            <!-- Bisa diganti dengan tag <img> jika sudah punya foto asli -->
            <div class="avatar avatar-female">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3 class="name">Faeyza Ardellein Y.</h3>
            <div class="role">System Analyst & UI/UX</div>
            <p class="bio">Bertanggung jawab dalam merancang pengalaman pengguna (User Experience) yang nyaman dan intuitif, serta memastikan alur sistem Hifzly berjalan sesuai dengan kebutuhan para penghafal Al-Qur'an.</p>
            <div class="social-links">
                <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-btn"><i class="fas fa-envelope"></i></a>
            </div>
        </div>

        <!-- PROFIL 2: FIRDY (COWOK) -->
        <div class="team-card">
            <!-- Bisa diganti dengan tag <img> jika sudah punya foto asli -->
            <div class="avatar avatar-male">
                <i class="fas fa-laptop-code"></i>
            </div>
            <h3 class="name">Firdy Ridho Fillah</h3>
            <div class="role">Full-Stack Developer</div>
            <p class="bio">Mengeksekusi logika pemrograman dari sisi server (Backend) hingga tampilan antarmuka (Frontend), serta merancang arsitektur database Hifzly agar aplikasi berjalan cepat dan aman.</p>
            <div class="social-links">
                <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-btn"><i class="fas fa-envelope"></i></a>
            </div>
        </div>

    </section>

    <!-- TECH STACK -->
    <section class="tech-section">
        <h2>Teknologi di Balik Hifzly</h2>
        <div class="tech-grid">
            <div class="tech-item"><i class="fab fa-php" style="color: #777bb4;"></i> PHP 8</div>
            <div class="tech-item"><i class="fas fa-database" style="color: #4479a1;"></i> MySQL</div>
            <div class="tech-item"><i class="fab fa-html5" style="color: #e34f26;"></i> HTML5</div>
            <div class="tech-item"><i class="fab fa-css3-alt" style="color: #1572b6;"></i> CSS3</div>
            <div class="tech-item"><i class="fab fa-js" style="color: #f7df1e;"></i> JavaScript</div>
            <div class="tech-item"><i class="fas fa-server" style="color: var(--primary);"></i> Al Quran Cloud API</div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; <?= date('Y') ?> Hifzly App. Dibuat dengan <i class="fas fa-heart" style="color: #ef4444;"></i> untuk umat.</p>
    </footer>

</body>

</html>