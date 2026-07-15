<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer - Hifzhly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <!-- Google Fonts: Amiri (display) + Plus Jakarta Sans (body/UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --emerald-deep: #064e3b;
            --primary: #0d7a5f;
            --primary-soft: #e7f5ef;
            --ivory: #fbfaf6;
            --ivory-dim: #f3f1ea;
            --ink: #10241d;
            --ink-muted: #5b6b64;
            --gold: #b6934a;
            --gold-soft: #eadfc4;
            --line: rgba(16, 36, 29, 0.09);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--ivory);
            color: var(--ink);
            line-height: 1.65;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        .display {
            font-family: 'Amiri', serif;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ============ ARABESQUE BACKDROP ============ */
        .arabesque-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: 0.05;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cg fill='none' stroke='%23064e3b' stroke-width='1'%3E%3Cpath d='M60 6 L96 30 L96 90 L60 114 L24 90 L24 30 Z'/%3E%3Ccircle cx='60' cy='60' r='30'/%3E%3C/g%3E%3C/svg%3E");
        }

        /* ============ NAVBAR ============ */
        .navbar {
            background: rgba(251, 250, 246, 0.85);
            backdrop-filter: blur(14px);
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid var(--line);
        }

        .nav-brand {
            font-family: 'Amiri', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--emerald-deep);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: 0.3px;
        }

        .nav-brand i {
            color: var(--gold);
            font-size: 1.2rem;
        }

        .btn-back {
            background: var(--emerald-deep);
            color: var(--ivory);
            padding: 11px 24px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.92rem;
            letter-spacing: 0.2px;
            transition: 0.35s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: var(--primary);
            box-shadow: 0 8px 22px rgba(6, 78, 59, 0.28);
            transform: translateY(-1px);
        }

        /* ============ HERO ============ */
        .hero {
            position: relative;
            padding: 168px 6% 100px;
            text-align: center;
            z-index: 1;
        }

        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 1px;
            height: 90px;
            background: linear-gradient(to bottom, transparent, var(--gold));
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--gold);
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 26px;
        }

        .hero-eyebrow::before,
        .hero-eyebrow::after {
            content: "";
            width: 28px;
            height: 1px;
            background: var(--gold);
        }

        .hero h1 {
            font-size: clamp(2.3rem, 5vw, 3.6rem);
            font-weight: 700;
            color: var(--emerald-deep);
            margin-bottom: 24px;
            line-height: 1.25;
        }

        .hero h1 em {
            font-style: italic;
            color: var(--primary);
        }

        .hero p {
            font-size: 1.08rem;
            color: var(--ink-muted);
            max-width: 620px;
            margin: 0 auto;
        }

        /* ============ TEAM SECTION ============ */
        .team-section {
            position: relative;
            z-index: 1;
            padding: 0 6% 110px;
        }

        .section-label {
            text-align: center;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .team-container {
            max-width: 980px;
            margin: 40px auto 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 44px;
        }

        .team-card {
            background: #ffffff;
            border-radius: 26px;
            padding: 46px 38px 38px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(6, 78, 59, 0.05);
            border: 1px solid var(--line);
            transition: 0.45s cubic-bezier(.2, .8, .2, 1);
            position: relative;
        }

        .team-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 26px 60px rgba(6, 78, 59, 0.13);
            border-color: var(--gold-soft);
        }

        /* Arch-shaped photo frame — nods to mihrab architecture */
        .avatar-frame {
            width: 160px;
            height: 190px;
            margin: 0 auto 26px;
            border-radius: 80px 80px 12px 12px;
            padding: 6px;
            background: linear-gradient(160deg, var(--gold), var(--primary) 60%);
            box-shadow: 0 14px 30px rgba(6, 78, 59, 0.18);
        }

        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 76px 76px 8px 8px;
            overflow: hidden;
            background: var(--primary-soft);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .avatar-fallback {
            font-size: 3.2rem;
            color: var(--primary);
        }

        .name {
            font-family: 'Amiri', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .role {
            font-size: 0.82rem;
            color: var(--gold);
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .bio {
            color: var(--ink-muted);
            font-size: 0.96rem;
            margin-bottom: 28px;
            line-height: 1.75;
        }

        .divider-dot {
            width: 30px;
            height: 1px;
            background: var(--line);
            margin: 0 auto 22px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .social-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            font-size: 1.05rem;
            transition: 0.3s;
            border: 1px solid transparent;
        }

        .social-btn:hover {
            background: var(--emerald-deep);
            color: var(--ivory);
            transform: translateY(-3px);
        }

        /* ============ TECH STACK ============ */
        .tech-section {
            background: var(--emerald-deep);
            padding: 90px 6%;
            text-align: center;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .tech-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140' viewBox='0 0 140 140'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1'%3E%3Cpath d='M70 8 L112 35 L112 105 L70 132 L28 105 L28 35 Z'/%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.05;
        }

        .tech-section h2 {
            position: relative;
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 700;
            color: var(--ivory);
            margin-bottom: 12px;
        }

        .tech-subtitle {
            position: relative;
            color: rgba(251, 250, 246, 0.65);
            font-size: 0.95rem;
            margin-bottom: 46px;
        }

        .tech-grid {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 16px;
            max-width: 900px;
            margin: 0 auto;
        }

        .tech-item {
            background: rgba(251, 250, 246, 0.06);
            padding: 16px 26px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--ivory);
            border: 1px solid rgba(251, 250, 246, 0.14);
            transition: 0.3s;
        }

        .tech-item:hover {
            border-color: var(--gold);
            background: rgba(182, 147, 74, 0.14);
            transform: translateY(-3px);
        }

        .tech-item i {
            font-size: 1.4rem;
        }

        /* ============ FOOTER ============ */
        footer {
            background: var(--ink);
            color: rgba(251, 250, 246, 0.6);
            text-align: center;
            padding: 34px;
            font-size: 0.88rem;
            position: relative;
            z-index: 1;
        }

        footer i {
            color: var(--gold);
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 16px 5%;
            }

            .hero {
                padding: 140px 6% 70px;
            }

            .team-card {
                padding: 38px 26px 32px;
            }
        }
    </style>
</head>

<body>

    <div class="arabesque-bg"></div>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            Hifzhly
        </a>
        <a href="index.php" class="btn-back"><i class="fas fa-home"></i> Beranda</a>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-eyebrow">Tim Pengembang</div>
        <h1>Membangun Hifzly dengan<br><em>Cinta &amp; Dedikasi</em></h1>
        <p>Hifzly lahir dari keinginan untuk membantu umat Islam menjaga hafalannya dengan teknologi yang cerdas, mudah, dan menyenangkan. Kenali sosok di balik layar aplikasi ini.</p>
    </section>

    <!-- PROFIL TIM -->
    <section class="team-section">
        <div class="section-label">Sang Perancang</div>
        <div class="team-container">

            <!-- PROFIL 1: FAEYZA -->
            <div class="team-card">
                <div class="avatar-frame">
                    <div class="avatar-inner">
                        <img src="assets/images/pija.webp"
                            alt="Foto Faeyza Ardellein Y."
                            onerror="this.parentElement.innerHTML='<i class=\'fas fa-user-graduate avatar-fallback\'></i>';">
                    </div>
                </div>
                <h3 class="name">Faeyza Ardellein Y.</h3>
                <div class="role">Full-Stack Developer</div>
                <div class="divider-dot"></div>
                <p class="bio">Bertanggung jawab merancang pengalaman pengguna yang nyaman dan intuitif, serta memastikan alur sistem Hifzly berjalan sesuai kebutuhan para penghafal Al-Qur'an.</p>
                <div class="social-links">
                    <a href="https://www.instagram.com/fyzardell" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-btn"><i class="fas fa-envelope"></i></a>
                </div>
            </div>

            <!-- PROFIL 2: FIRDY -->
            <div class="team-card">
                <div class="avatar-frame">
                    <div class="avatar-inner">
                        <img src="assets/images/firdy.webp"
                            alt="Foto Firdy Ridho Fillah"
                            onerror="this.parentElement.innerHTML='<i class=\'fas fa-laptop-code avatar-fallback\'></i>';">
                    </div>
                </div>
                <h3 class="name">Firdy Ridho Fillah</h3>
                <div class="role">Full-Stack Developer</div>
                <div class="divider-dot"></div>
                <p class="bio">Mengeksekusi logika pemrograman dari sisi server hingga tampilan antarmuka, serta merancang arsitektur database Hifzly agar aplikasi berjalan cepat dan aman.</p>
                <div class="social-links">
                    <a href="https://instagram.com/firdyfillaa_" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-btn"><i class="fas fa-envelope"></i></a>
                </div>
            </div>

        </div>
    </section>

    <!-- TECH STACK -->
    <section class="tech-section">
        <h2>Teknologi di Balik Hifzly</h2>
        <p class="tech-subtitle">Ditenagai oleh perangkat yang teruji dan andal</p>
        <div class="tech-grid">
            <div class="tech-item"><i class="fab fa-php" style="color:#a5b4fc;"></i> PHP 8</div>
            <div class="tech-item"><i class="fas fa-database" style="color:#7dd3fc;"></i> MySQL</div>
            <div class="tech-item"><i class="fab fa-html5" style="color:#fca5a5;"></i> HTML5</div>
            <div class="tech-item"><i class="fab fa-css3-alt" style="color:#93c5fd;"></i> CSS3</div>
            <div class="tech-item"><i class="fab fa-js" style="color:#fde68a;"></i> JavaScript</div>
            <div class="tech-item"><i class="fas fa-server" style="color:var(--gold);"></i> Al Quran Cloud API</div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; <?= date('Y') ?> Hifzhly App &mdash; Dibuat dengan <i class="fas fa-heart"></i> untuk umat.</p>
    </footer>

</body>

</html>