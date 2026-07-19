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

    <!-- Bootstrap 5 (grid & utilities) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts: Amiri (display) + Plus Jakarta Sans (body/UI) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS (scroll reveal) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.css">

    <style>
        :root {
            /* Balanced emerald palette — not neon-young, not near-black */
            --emerald-deep: #14684f;
            --emerald: #187a5e;
            --emerald-bright: #2bab82;
            --mint-50: #eaf7f1;
            --ivory: #fdfcf9;
            --ivory-dim: #f4f2ea;
            --ink: #0f231c;
            --ink-muted: #5f7168;
            --gold: #bd9a4b;
            --gold-soft: #eee0bd;
            --line: rgba(15, 35, 28, 0.09);
            --shadow-soft: 0 4px 24px rgba(7, 59, 44, 0.06);
            --ease: cubic-bezier(.22, .82, .27, 1);
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
            background: linear-gradient(165deg, #eaf7f1 0%, #fdfcf9 28%, #fdfcf9 68%, #eef6f0 100%);
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

        a {
            text-decoration: none;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        ::selection {
            background: var(--gold-soft);
            color: var(--emerald-deep);
        }

        /* ============ PAGE TRANSITION VEIL (SPA-like nav) ============ */
        .route-veil {
            position: fixed;
            inset: 0;
            z-index: 9998;
            background: var(--emerald-deep);
            transform: translateY(100%);
            transition: transform 0.55s var(--ease);
            pointer-events: none;
        }

        .route-veil.active {
            transform: translateY(0);
            pointer-events: all;
        }

        /* ============ ARABESQUE BACKDROP ============ */
        .arabesque-bg {
            position: fixed;
            inset: -10%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.045;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cg fill='none' stroke='%23073b2c' stroke-width='1'%3E%3Cpath d='M60 6 L96 30 L96 90 L60 114 L24 90 L24 30 Z'/%3E%3Ccircle cx='60' cy='60' r='30'/%3E%3C/g%3E%3C/svg%3E");
            animation: drift 60s linear infinite;
        }

        @keyframes drift {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(120px, 120px);
            }
        }

        .glow-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(90px);
            z-index: 0;
            pointer-events: none;
            opacity: 0.28;
        }

        .glow-blob.b1 {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, var(--emerald-bright), transparent 70%);
            top: -120px;
            left: -100px;
            animation: floatBlob 16s ease-in-out infinite;
        }

        .glow-blob.b2 {
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, var(--gold-soft), transparent 70%);
            top: 40%;
            right: -140px;
            animation: floatBlob 20s ease-in-out infinite reverse;
        }

        @keyframes floatBlob {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(30px, -40px) scale(1.08);
            }
        }

        /* ============ NAVBAR ============ */
        .navbar-hz {
            background: rgba(253, 252, 249, 0.78);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid var(--line);
            transition: padding 0.4s var(--ease), box-shadow 0.4s var(--ease);
        }

        .navbar-hz.scrolled {
            padding: 11px 6%;
            box-shadow: 0 10px 30px rgba(7, 59, 44, 0.07);
        }

        .nav-brand {
            font-family: 'Amiri', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--emerald-deep);
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: 0.3px;
        }

        .logo-mark {
            background: var(--emerald-deep);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 7px;
        }

        .logo-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .nav-brand .logo-mark {
            width: 38px;
            height: 38px;
        }

        .btn-back {
            background: var(--emerald-deep);
            color: var(--ivory);
            padding: 11px 24px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.92rem;
            letter-spacing: 0.2px;
            transition: 0.35s var(--ease);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }

        .btn-back span {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, var(--emerald-bright), var(--gold));
            transform: translateX(-100%);
            transition: transform 0.45s var(--ease);
        }

        .btn-back:hover::before {
            transform: translateX(0);
        }

        .btn-back:hover {
            box-shadow: 0 10px 26px rgba(7, 59, 44, 0.3);
            transform: translateY(-2px);
        }

        /* ============ HERO ============ */
        .hero {
            position: relative;
            padding: clamp(140px, 20vw, 172px) 6% clamp(70px, 10vw, 100px);
            text-align: center;
            z-index: 1;
        }

        .hero-mark {
            width: clamp(56px, 8vw, 72px);
            height: clamp(56px, 8vw, 72px);
            margin: 0 auto 28px;
            border-radius: 20px;
            padding: 13px;
            box-shadow: 0 16px 40px rgba(7, 59, 44, 0.2);
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
            font-size: clamp(2.1rem, 5.4vw, 3.7rem);
            font-weight: 700;
            color: var(--emerald-deep);
            margin-bottom: 24px;
            line-height: 1.28;
        }

        .hero h1 .word {
            display: inline-block;
            opacity: 0;
            transform: translateY(22px);
            animation: wordUp 0.8s var(--ease) forwards;
        }

        @keyframes wordUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 em {
            font-style: italic;
            color: var(--emerald);
        }

        .hero p {
            font-size: clamp(0.98rem, 1.6vw, 1.08rem);
            color: var(--ink-muted);
            max-width: 620px;
            margin: 0 auto;
        }

        .hero-scrollcue {
            margin-top: 54px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--ink-muted);
            font-size: 0.78rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .hero-scrollcue i {
            animation: cueBounce 1.8s ease-in-out infinite;
            color: var(--gold);
        }

        @keyframes cueBounce {

            0%,
            100% {
                transform: translateY(0);
                opacity: 0.5;
            }

            50% {
                transform: translateY(7px);
                opacity: 1;
            }
        }

        /* ============ TEAM SECTION — zig-zag rows ============ */
        .team-section {
            position: relative;
            z-index: 1;
            padding: 0 6% clamp(80px, 11vw, 130px);
        }

        .section-label {
            text-align: center;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--emerald);
            margin-bottom: 12px;
        }

        .section-heading {
            text-align: center;
            font-size: clamp(1.5rem, 3vw, 2rem);
            color: var(--emerald-deep);
            margin-bottom: clamp(56px, 8vw, 90px);
        }

        .dev-row {
            display: flex;
            align-items: center;
            gap: clamp(36px, 6vw, 90px);
            max-width: 1080px;
            margin: 0 auto clamp(70px, 10vw, 120px);
        }

        .dev-row:last-child {
            margin-bottom: 0;
        }

        .dev-row.reverse {
            flex-direction: row-reverse;
        }

        /* --- clean, unadorned photo --- */
        .dev-photo-wrap {
            position: relative;
            width: clamp(230px, 27vw, 320px);
            aspect-ratio: 1 / 1;
            flex-shrink: 0;
        }

        .dev-photo-frame {
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: 24px;
        }

        .dev-photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.7s var(--ease);
        }

        .dev-row:hover .dev-photo-frame img {
            transform: scale(1.05);
        }

        .avatar-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--emerald);
            background: var(--mint-50);
        }

        /* --- info side --- */
        .dev-info {
            flex: 1;
            min-width: 280px;
        }

        .dev-index {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--gold);
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .dev-index::before {
            content: "";
            width: 22px;
            height: 1px;
            background: var(--gold);
        }

        .dev-name {
            font-size: clamp(1.5rem, 2.6vw, 1.9rem);
            color: var(--ink);
            margin-bottom: 6px;
        }

        .dev-role {
            font-size: 0.82rem;
            color: var(--emerald);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 18px;
        }

        .dev-bio {
            color: var(--ink-muted);
            font-size: 0.98rem;
            line-height: 1.8;
            margin-bottom: 24px;
            max-width: 480px;
        }

        .dev-row.reverse .dev-bio {
            margin-left: auto;
        }

        .dev-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }

        .dev-row.reverse .dev-skills {
            justify-content: flex-end;
        }

        .skill-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--mint-50), #ffffff);
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 9px 18px 9px 14px;
            font-size: 0.83rem;
            font-weight: 600;
            color: var(--emerald-deep);
            transition: 0.3s var(--ease);
        }

        .skill-chip i {
            color: var(--gold);
            font-size: 0.95rem;
        }

        .skill-chip:hover {
            background: linear-gradient(135deg, var(--emerald-deep), var(--emerald));
            color: var(--ivory);
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(7, 59, 44, 0.22);
        }

        .skill-chip:hover i {
            color: var(--gold-soft);
        }

        .social-links {
            display: flex;
            gap: 12px;
        }

        .dev-row.reverse .social-links {
            justify-content: flex-end;
        }

        .social-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--mint-50);
            color: var(--emerald);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.02rem;
            transition: 0.3s var(--ease);
            border: 1px solid transparent;
        }

        .social-btn:hover {
            background: var(--emerald-deep);
            color: var(--ivory);
            transform: translateY(-3px) scale(1.06);
        }

        /* ============ TECH STACK — modern glass chips ============ */
        .tech-section {
            background: var(--emerald-deep);
            padding: clamp(64px, 9vw, 90px) 0;
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

        .tech-section::after {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 0%, rgba(29, 157, 117, 0.28), transparent 55%);
        }

        .tech-section h2 {
            position: relative;
            font-size: clamp(1.5rem, 3vw, 2.15rem);
            font-weight: 700;
            color: var(--ivory);
            margin-bottom: 10px;
            padding: 0 6%;
        }

        .tech-subtitle {
            position: relative;
            color: rgba(253, 252, 249, 0.62);
            font-size: 0.95rem;
            margin-bottom: 50px;
            padding: 0 6%;
        }

        .tech-belt-wrap {
            position: relative;
            width: 100%;
            -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
            mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
        }

        .tech-belt {
            display: flex;
            width: max-content;
            gap: 20px;
            animation: beltScroll 28s linear infinite;
        }

        .tech-belt-wrap:hover .tech-belt {
            animation-play-state: paused;
        }

        @keyframes beltScroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .tech-item {
            position: relative;
            background: linear-gradient(160deg, rgba(253, 252, 249, 0.09), rgba(253, 252, 249, 0.02));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 24px 30px 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            min-width: 128px;
            border: 1px solid rgba(253, 252, 249, 0.12);
            transition: transform 0.4s var(--ease), border-color 0.4s var(--ease), box-shadow 0.4s var(--ease);
        }

        .tech-item:hover {
            transform: translateY(-7px);
            border-color: rgba(189, 154, 75, 0.55);
            box-shadow: 0 20px 42px rgba(0, 0, 0, 0.28);
        }

        .tech-icon-badge {
            position: relative;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(7, 59, 44, 0.4);
        }

        .tech-icon-badge::before {
            content: "";
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            padding: 2px;
            background: conic-gradient(from 0deg, var(--gold), var(--emerald-bright), var(--gold));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: ringSpin 5s linear infinite;
        }

        @keyframes ringSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .tech-icon-badge i {
            font-size: 1.4rem;
            position: relative;
            transition: transform 0.5s var(--ease);
        }

        .tech-item:hover .tech-icon-badge i {
            transform: rotateY(180deg);
        }

        .tech-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--ivory);
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        /* ============ FOOTER ============ */
        footer {
            background: var(--ink);
            color: rgba(253, 252, 249, 0.6);
            text-align: center;
            padding: 34px;
            font-size: 0.88rem;
            position: relative;
            z-index: 1;
        }

        footer i {
            color: var(--gold);
        }

        [data-aos] {
            will-change: transform, opacity;
        }

        @media (max-width: 860px) {

            .dev-row,
            .dev-row.reverse {
                flex-direction: column;
                text-align: center;
            }

            .dev-bio,
            .dev-row.reverse .dev-bio {
                margin-left: auto;
                margin-right: auto;
            }

            .dev-skills,
            .dev-row.reverse .dev-skills,
            .social-links,
            .dev-row.reverse .social-links {
                justify-content: center;
            }

            .dev-index::before {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .navbar-hz {
                padding: 15px 5%;
            }

            .navbar-hz.scrolled {
                padding: 10px 5%;
            }

            .hero {
                padding: 128px 6% 60px;
            }

            .dev-photo-wrap {
                width: clamp(190px, 55vw, 250px);
            }

            .tech-belt {
                gap: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="route-veil" id="routeVeil"></div>

    <div class="arabesque-bg"></div>
    <div class="glow-blob b1"></div>
    <div class="glow-blob b2"></div>

    <!-- NAVBAR -->
    <nav class="navbar-hz" id="navbar">
        <a href="index.php" class="nav-brand" data-route>
            <span class="logo-mark"><img src="assets/icon/logo.png" alt="Logo Hifzhly"></span>
            Hifzhly
        </a>
        <a href="index.php" class="btn-back" data-route><span><i class="fas fa-arrow-left"></i> Beranda</span></a>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="logo-mark hero-mark"><img src="assets/icon/logo.png" alt="Logo Hifzhly"></div>
        <div class="hero-eyebrow">Tim Pengembang</div>
        <h1>
            <span class="word" style="animation-delay:.05s">Membangun</span>
            <span class="word" style="animation-delay:.15s">Hifzhly</span>
            <span class="word" style="animation-delay:.25s">dengan</span><br>
            <em class="word" style="animation-delay:.35s">Cinta</em>
            <span class="word" style="animation-delay:.42s">&amp;</span>
            <em class="word" style="animation-delay:.5s">Dedikasi</em>
        </h1>
        <p data-aos="fade-up" data-aos-delay="300">Hifzhly lahir dari keinginan untuk membantu umat Islam menjaga hafalannya dengan teknologi yang cerdas, mudah, dan menyenangkan. Kenali sosok di balik layar aplikasi ini.</p>
        <div class="hero-scrollcue" data-aos="fade-up" data-aos-delay="450">
            Gulir <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <!-- PROFIL TIM -->
    <section class="team-section">
        <div class="section-label" data-aos="fade-up">Sang Perancang</div>
        <h2 class="section-heading" data-aos="fade-up" data-aos-delay="80">Dua Tangan di Balik Layar</h2>

        <!-- PROFIL 1: FAEYZA — photo left, info right -->
        <div class="dev-row">
            <div class="dev-photo-wrap" data-aos="fade-right" data-aos-delay="80">
                <div class="dev-photo-frame">
                    <img src="assets/images/pija.webp" alt="Foto Faeyza Ardellein Yaradhitya"
                        onerror="this.parentElement.innerHTML='<div class=&quot;avatar-fallback&quot;><i class=&quot;fas fa-user-graduate&quot;></i></div>';">
                </div>
            </div>
            <div class="dev-info" data-aos="fade-left" data-aos-delay="160">
                <div class="dev-index">Pijaaa <i class="fas fa-heart"></i></div>
                <h3 class="dev-name">Faeyza Ardellein Yaradhitya</h3>
                <div class="dev-role">Full-Stack Developer</div>
                <p class="dev-bio">Bertanggung jawab merancang pengalaman pengguna yang nyaman dan intuitif, serta memastikan alur sistem Hifzhly berjalan sesuai kebutuhan para penghafal Al-Qur'an.</p>
                <div class="dev-skills">
                    <span class="skill-chip"><i class="fas fa-pen-nib"></i> UI/UX Design</span>
                    <span class="skill-chip"><i class="fab fa-html5"></i> HTML5</span>
                    <span class="skill-chip"><i class="fab fa-css3-alt"></i> CSS3</span>
                    <span class="skill-chip"><i class="fab fa-js"></i> JavaScript</span>
                    <span class="skill-chip"><i class="fab fa-bootstrap"></i> Bootstrap 5</span>
                </div>
                <div class="social-links">
                    <a href="https://www.instagram.com/fyzardell" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                    <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-btn"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>

        <!-- PROFIL 2: FIRDY — mirrored, info left, photo right -->
        <div class="dev-row reverse">
            <div class="dev-photo-wrap" data-aos="fade-left" data-aos-delay="80">
                <div class="dev-photo-frame">
                    <img src="assets/images/firdy.webp" alt="Foto Firdy Ridho Fillah"
                        onerror="this.parentElement.innerHTML='<div class=&quot;avatar-fallback&quot;><i class=&quot;fas fa-laptop-code&quot;></i></div>';">
                </div>
            </div>
            <div class="dev-info" data-aos="fade-right" data-aos-delay="160">
                <div class="dev-index">Firdy</div>
                <h3 class="dev-name">Firdy Ridho Fillah</h3>
                <div class="dev-role">Full-Stack Developer</div>
                <p class="dev-bio">Mengeksekusi logika pemrograman dari sisi server hingga tampilan antarmuka, serta merancang arsitektur database Hifzhly agar aplikasi berjalan cepat dan aman.</p>
                <div class="dev-skills">
                    <span class="skill-chip"><i class="fab fa-php"></i> PHP 8</span>
                    <span class="skill-chip"><i class="fas fa-database"></i> MySQL</span>
                    <span class="skill-chip"><i class="fas fa-code-branch"></i> REST API</span>
                    <span class="skill-chip"><i class="fas fa-shield-halved"></i> Session Auth</span>
                    <span class="skill-chip"><i class="fas fa-server"></i> Server Architecture</span>
                </div>
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
        <h2 data-aos="fade-up">Teknologi di Balik Hifzhly</h2>
        <p class="tech-subtitle" data-aos="fade-up" data-aos-delay="80">Ditenagai oleh perangkat yang teruji dan andal</p>
        <div class="tech-belt-wrap" data-aos="fade-up" data-aos-delay="150">
            <div class="tech-belt" id="techBelt">
                <div class="tech-item">
                    <span class="tech-icon-badge"><i class="fab fa-php" style="color:#a5b4fc;"></i></span>
                    <span class="tech-name">PHP 8</span>
                </div>
                <div class="tech-item">
                    <span class="tech-icon-badge"><i class="fas fa-database" style="color:#7dd3fc;"></i></span>
                    <span class="tech-name">MySQL</span>
                </div>
                <div class="tech-item">
                    <span class="tech-icon-badge"><i class="fab fa-html5" style="color:#fca5a5;"></i></span>
                    <span class="tech-name">HTML5</span>
                </div>
                <div class="tech-item">
                    <span class="tech-icon-badge"><i class="fab fa-css3-alt" style="color:#93c5fd;"></i></span>
                    <span class="tech-name">CSS3</span>
                </div>
                <div class="tech-item">
                    <span class="tech-icon-badge"><i class="fab fa-js" style="color:#fde68a;"></i></span>
                    <span class="tech-name">JavaScript</span>
                </div>
                <div class="tech-item">
                    <span class="tech-icon-badge"><i class="fab fa-bootstrap" style="color:#c4b5fd;"></i></span>
                    <span class="tech-name">Bootstrap 5</span>
                </div>
                <div class="tech-item">
                    <span class="tech-icon-badge"><i class="fas fa-server" style="color:var(--gold);"></i></span>
                    <span class="tech-name">Al Quran Cloud API</span>
                </div>
                <div class="tech-item">
                    <span class="tech-icon-badge"><i class="fas fa-shield-halved" style="color:#86efac;"></i></span>
                    <span class="tech-name">Session Auth</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; <?= date('Y') ?> Hifzhly App &mdash; Dibuat dengan <i class="fas fa-heart"></i> untuk umat.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 700,
            easing: 'ease-out-cubic',
            once: true,
            offset: 60
        });

        // Navbar shrink on scroll
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 30);
        });

        // Duplicate tech belt content for seamless infinite loop
        const belt = document.getElementById('techBelt');
        belt.innerHTML += belt.innerHTML;

        // SPA-like route transition veil for internal links
        const veil = document.getElementById('routeVeil');
        document.querySelectorAll('[data-route]').forEach(link => {
            link.addEventListener('click', (e) => {
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#')) return;
                e.preventDefault();
                veil.classList.add('active');
                setTimeout(() => {
                    window.location.href = href;
                }, 500);
            });
        });
    </script>

</body>

</html>