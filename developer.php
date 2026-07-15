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
            --emerald-deep: #073b2c;
            --emerald: #0e7a5c;
            --emerald-bright: #14a37d;
            --mint-50: #f2faf6;
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
            background-color: var(--ivory);
            color: var(--ink);
            line-height: 1.65;
            overflow-x: hidden;
            cursor: default;
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

        /* ============ PRELOADER ============ */
        .preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: var(--emerald-deep);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.7s var(--ease), visibility 0.7s var(--ease);
        }

        .preloader.done {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .preloader-mark {
            width: 74px;
            height: 74px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(251, 250, 246, 0.06);
            border: 1px solid rgba(182, 147, 74, 0.35);
            animation: preloaderPulse 1.6s var(--ease) infinite;
        }

        .preloader-mark img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        @keyframes preloaderPulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(182, 147, 74, 0.28);
            }

            50% {
                transform: scale(1.08);
                box-shadow: 0 0 0 14px rgba(182, 147, 74, 0);
            }
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

        body:not(.loaded) .fade-in-page {
            opacity: 0;
        }

        .fade-in-page {
            opacity: 1;
            transition: opacity 0.6s var(--ease);
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
            opacity: 0.35;
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

        .nav-brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: linear-gradient(155deg, var(--gold), var(--emerald) 65%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            transition: transform 0.5s var(--ease);
        }

        .navbar-hz:hover .nav-brand-mark {
            transform: rotate(-8deg) scale(1.05);
        }

        .nav-brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
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
            background: linear-gradient(160deg, var(--gold), var(--emerald) 65%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            box-shadow: 0 16px 40px rgba(7, 59, 44, 0.2);
            animation: markFloat 5s ease-in-out infinite;
        }

        .hero-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        @keyframes markFloat {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-9px) rotate(3deg);
            }
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

        /* ============ TEAM SECTION ============ */
        .team-section {
            position: relative;
            z-index: 1;
            padding: 0 6% clamp(70px, 10vw, 110px);
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
            margin-bottom: 44px;
        }

        .team-container {
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: clamp(28px, 4vw, 44px);
        }

        /* --- Holographic 3D rare card --- */
        .card-stage {
            perspective: 1200px;
        }

        .team-card {
            --mx: 50%;
            --my: 50%;
            background: #ffffff;
            border-radius: 26px;
            padding: 44px 34px 36px;
            text-align: center;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--line);
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.25s var(--ease), box-shadow 0.4s var(--ease), border-color 0.4s var(--ease);
            will-change: transform;
        }

        .team-card:hover {
            box-shadow: 0 30px 70px rgba(7, 59, 44, 0.16);
            border-color: var(--gold-soft);
        }

        .team-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 26px;
            padding: 1px;
            background: conic-gradient(from calc(var(--my) * 3.6deg) at var(--mx) var(--my),
                    rgba(189, 154, 75, 0), rgba(189, 154, 75, 0.55), rgba(14, 122, 92, 0), rgba(189, 154, 75, 0));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s var(--ease);
            pointer-events: none;
        }

        .team-card:hover::before {
            opacity: 1;
        }

        .card-sheen {
            position: absolute;
            inset: 0;
            border-radius: 26px;
            background: radial-gradient(circle 220px at var(--mx) var(--my),
                    rgba(255, 255, 255, 0.85), rgba(189, 154, 75, 0.16) 40%, transparent 70%);
            opacity: 0;
            transition: opacity 0.35s var(--ease);
            pointer-events: none;
            mix-blend-mode: overlay;
        }

        .team-card:hover .card-sheen {
            opacity: 1;
        }

        .card-content {
            position: relative;
            transform: translateZ(28px);
        }

        /* Arch-shaped photo frame — nods to mihrab architecture */
        .avatar-frame {
            width: 156px;
            height: 186px;
            margin: 0 auto 26px;
            border-radius: 78px 78px 12px 12px;
            padding: 6px;
            background: linear-gradient(160deg, var(--gold), var(--emerald) 60%);
            box-shadow: 0 16px 34px rgba(7, 59, 44, 0.2);
            transition: transform 0.4s var(--ease);
        }

        .team-card:hover .avatar-frame {
            transform: translateZ(10px) scale(1.03);
        }

        .avatar-inner {
            width: 100%;
            height: 100%;
            border-radius: 74px 74px 8px 8px;
            overflow: hidden;
            background: var(--mint-50);
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
            font-size: 3.1rem;
            color: var(--emerald);
        }

        .name {
            font-family: 'Amiri', serif;
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .role {
            font-size: 0.8rem;
            color: var(--gold);
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .bio {
            color: var(--ink-muted);
            font-size: 0.95rem;
            margin-bottom: 26px;
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

        /* ============ TECH STACK — "tasbih belt" ============ */
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
            gap: 18px;
            animation: beltScroll 26s linear infinite;
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
            background: rgba(253, 252, 249, 0.06);
            padding: 16px 26px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--ivory);
            border: 1px solid rgba(253, 252, 249, 0.14);
            transition: 0.35s var(--ease);
            white-space: nowrap;
        }

        .tech-item:hover {
            border-color: var(--gold);
            background: rgba(189, 154, 75, 0.16);
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 14px 30px rgba(189, 154, 75, 0.18);
        }

        .tech-item i {
            font-size: 1.35rem;
            transition: transform 0.5s var(--ease);
        }

        .tech-item:hover i {
            transform: rotateY(180deg);
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

        /* ============ SCROLL REVEAL FALLBACK (no-JS AOS safety) ============ */
        [data-aos] {
            will-change: transform, opacity;
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

            .team-card {
                padding: 36px 24px 30px;
            }

            .tech-belt {
                gap: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="preloader" id="preloader">
        <div class="preloader-mark">
            <img src="assets/icon/logo.png" alt="Hifzhly">
        </div>
    </div>

    <div class="route-veil" id="routeVeil"></div>

    <div class="arabesque-bg"></div>
    <div class="glow-blob b1"></div>
    <div class="glow-blob b2"></div>

    <div class="fade-in-page">

        <!-- NAVBAR -->
        <nav class="navbar-hz" id="navbar">
            <a href="index.php" class="nav-brand" data-route>
                <span class="nav-brand-mark"><img src="assets/icon/logo.png" alt="Logo Hifzhly"></span>
                Hifzhly
            </a>
            <a href="index.php" class="btn-back" data-route><span><i class="fas fa-arrow-left"></i> Beranda</span></a>
        </nav>

        <!-- HERO SECTION -->
        <section class="hero">
            <div class="hero-mark"><img src="assets/icon/logo.png" alt="Logo Hifzhly"></div>
            <div class="hero-eyebrow">Tim Pengembang</div>
            <h1>
                <span class="word" style="animation-delay:.05s">Membangun</span>
                <span class="word" style="animation-delay:.15s">Hifzhly</span>
                <span class="word" style="animation-delay:.25s">dengan</span><br>
                <em class="word" style="animation-delay:.35s">Cinta</em>
                <span class="word" style="animation-delay:.42s">&amp;</span>
                <em class="word" style="animation-delay:.5s">Dedikasi</em>
            </h1>
            <p data-aos="fade-up" data-aos-delay="500">Hifzhly lahir dari keinginan untuk membantu umat Islam menjaga hafalannya dengan teknologi yang cerdas, mudah, dan menyenangkan. Kenali sosok di balik layar aplikasi ini.</p>
            <div class="hero-scrollcue" data-aos="fade-up" data-aos-delay="700">
                Gulir <i class="fas fa-chevron-down"></i>
            </div>
        </section>

        <!-- PROFIL TIM -->
        <section class="team-section">
            <div class="section-label" data-aos="fade-up">Sang Perancang</div>
            <h2 class="section-heading" data-aos="fade-up" data-aos-delay="80">Dua Tangan di Balik Layar</h2>
            <div class="team-container">

                <!-- PROFIL 1: FAEYZA -->
                <div class="card-stage" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-card" data-tilt>
                        <div class="card-sheen"></div>
                        <div class="card-content">
                            <div class="avatar-frame">
                                <div class="avatar-inner">
                                    <img src="assets/images/pija.webp"
                                        alt="Foto Faeyza Ardellein Yaradhitya"
                                        onerror="this.parentElement.innerHTML='<i class=\'fas fa-user-graduate avatar-fallback\'></i>';">
                                </div>
                            </div>
                            <h3 class="name">Faeyza Ardellein Yaradhitya</h3>
                            <div class="role">Full-Stack Developer</div>
                            <div class="divider-dot"></div>
                            <p class="bio">Bertanggung jawab merancang pengalaman pengguna yang nyaman dan intuitif, serta memastikan alur sistem Hifzhly berjalan sesuai kebutuhan para penghafal Al-Qur'an.</p>
                            <div class="social-links">
                                <a href="https://www.instagram.com/fyzardell" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                                <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                                <a href="#" class="social-btn"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PROFIL 2: FIRDY -->
                <div class="card-stage" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-card" data-tilt>
                        <div class="card-sheen"></div>
                        <div class="card-content">
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
                            <p class="bio">Mengeksekusi logika pemrograman dari sisi server hingga tampilan antarmuka, serta merancang arsitektur database Hifzhly agar aplikasi berjalan cepat dan aman.</p>
                            <div class="social-links">
                                <a href="https://instagram.com/firdyfillaa_" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-btn"><i class="fab fa-github"></i></a>
                                <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i></a>
                                <a href="#" class="social-btn"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
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
                    <div class="tech-item"><i class="fab fa-php" style="color:#a5b4fc;"></i> PHP 8</div>
                    <div class="tech-item"><i class="fas fa-database" style="color:#7dd3fc;"></i> MySQL</div>
                    <div class="tech-item"><i class="fab fa-html5" style="color:#fca5a5;"></i> HTML5</div>
                    <div class="tech-item"><i class="fab fa-css3-alt" style="color:#93c5fd;"></i> CSS3</div>
                    <div class="tech-item"><i class="fab fa-js" style="color:#fde68a;"></i> JavaScript</div>
                    <div class="tech-item"><i class="fab fa-bootstrap" style="color:#c4b5fd;"></i> Bootstrap 5</div>
                    <div class="tech-item"><i class="fas fa-server" style="color:var(--gold);"></i> Al Quran Cloud API</div>
                    <div class="tech-item"><i class="fas fa-shield-halved" style="color:#86efac;"></i> Session Auth</div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer>
            <p>&copy; <?= date('Y') ?> Hifzhly App &mdash; Dibuat dengan <i class="fas fa-heart"></i> untuk umat.</p>
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Preloader
        window.addEventListener('load', () => {
            document.getElementById('preloader').classList.add('done');
            document.body.classList.add('loaded');
        });

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

        // 3D tilt + holographic sheen for team cards
        const tiltCards = document.querySelectorAll('[data-tilt]');
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!reduceMotion) {
            tiltCards.forEach(card => {
                card.addEventListener('mousemove', (e) => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const px = (x / rect.width) * 100;
                    const py = (y / rect.height) * 100;
                    const rotateY = ((x / rect.width) - 0.5) * 14;
                    const rotateX = ((y / rect.height) - 0.5) * -14;

                    card.style.setProperty('--mx', px + '%');
                    card.style.setProperty('--my', py + '%');
                    card.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
                });

                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(1200px) rotateX(0deg) rotateY(0deg) translateY(0)';
                    card.style.setProperty('--mx', '50%');
                    card.style.setProperty('--my', '50%');
                });
            });
        }

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