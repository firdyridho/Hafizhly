<?php
session_start();
// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hifzly - AI Quran Companion</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- AOS (scroll reveal) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #059669;
            --primary-dark: #04785a;
            --primary-light: #34d399;
            --primary-glow: rgba(5, 150, 105, 0.35);
            --gold: #c9a227;
            --gold-light: #e8c85f;
            --dark: #0b1120;
            --ink: #0f172a;
            --muted: #64748b;
            --bg: #f7faf8;
            --card-bg: rgba(255, 255, 255, 0.72);
            --border-soft: rgba(15, 23, 42, 0.06);
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
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: var(--bg);
            color: var(--ink);
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        .display-font {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            letter-spacing: -0.02em;
        }

        ::selection {
            background: var(--primary-light);
            color: white;
        }

        /* ===== Preloader ===== */
        #preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: var(--dark);
            display: flex;
            flex-direction: column;
            gap: 18px;
            align-items: center;
            justify-content: center;
            transition: opacity 0.6s ease, visibility 0.6s ease;
        }

        #preloader.hide {
            opacity: 0;
            visibility: hidden;
        }

        .preloader-mark {
            width: 64px;
            height: 64px;
            border-radius: 18px 6px 18px 18px;
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: var(--dark);
            animation: markPulse 1.1s ease-in-out infinite;
            box-shadow: 0 0 40px rgba(201, 162, 39, 0.4);
        }

        .preloader-text {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.8rem;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        @keyframes markPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(0.88);
            }
        }

        /* ===== Ambient background ===== */
        .aurora {
            position: fixed;
            inset: 0;
            z-index: -2;
            overflow: hidden;
            pointer-events: none;
        }

        .aurora span {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.35;
            animation: drift 18s ease-in-out infinite;
        }

        .aurora span:nth-child(1) {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, var(--primary-light), transparent 70%);
            top: -120px;
            left: -100px;
        }

        .aurora span:nth-child(2) {
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, var(--gold-light), transparent 70%);
            top: 30%;
            right: -160px;
            opacity: 0.22;
            animation-delay: -6s;
        }

        .aurora span:nth-child(3) {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, var(--primary), transparent 70%);
            bottom: -140px;
            left: 20%;
            opacity: 0.18;
            animation-delay: -12s;
        }

        @keyframes drift {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            33% {
                transform: translate(30px, -40px) scale(1.08);
            }

            66% {
                transform: translate(-25px, 25px) scale(0.95);
            }
        }

        /* ===== Navbar ===== */
        .navbar-custom {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            padding: 18px 0;
            background: rgba(247, 250, 248, 0.6);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid transparent;
            transition: all 0.35s ease;
        }

        .navbar-custom.scrolled {
            padding: 12px 0;
            background: rgba(247, 250, 248, 0.92);
            border-bottom: 1px solid var(--border-soft);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 11px 4px 11px 11px;
            background: linear-gradient(135deg, var(--gold-light), var(--gold));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            font-size: 1.05rem;
            box-shadow: 0 6px 16px rgba(201, 162, 39, 0.35);
        }

        .brand-text {
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--dark);
        }

        .nav-link-custom {
            color: var(--ink) !important;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 18px !important;
        }

        .nav-link-custom:hover {
            color: var(--primary) !important;
        }

        .btn-gold {
            background: var(--dark);
            color: #fff;
            font-weight: 600;
            padding: 9px 22px;
            border-radius: 30px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ===== Hero ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 130px 0 70px;
            position: relative;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(5, 150, 105, 0.09);
            color: var(--primary-dark);
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(2.4rem, 5vw, 3.6rem);
            font-weight: 800;
            line-height: 1.15;
            margin: 22px 0 20px;
            color: var(--dark);
        }

        .hero h1 .text-gradient {
            background: linear-gradient(100deg, var(--primary), var(--primary-light) 60%, var(--gold));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p.lead-custom {
            font-size: 1.08rem;
            color: var(--muted);
            max-width: 520px;
            line-height: 1.7;
            margin-bottom: 34px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 30px;
            border: none;
            box-shadow: 0 14px 28px var(--primary-glow);
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 32px var(--primary-glow);
            color: #fff;
        }

        .btn-outline-custom {
            background: rgba(255, 255, 255, 0.7);
            color: var(--dark);
            font-weight: 600;
            padding: 14px 26px;
            border-radius: 30px;
            border: 1px solid var(--border-soft);
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            border-color: var(--primary);
            color: var(--primary-dark);
            background: #fff;
        }

        /* Arabic decorative script */
        .arabic-deco {
            position: absolute;
            font-family: 'Amiri', serif;
            font-size: 7rem;
            color: var(--primary);
            opacity: 0.06;
            top: 90px;
            left: -10px;
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
        }

        /* ===== Listening card (hero visual / "video" signature) ===== */
        .listening-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 28px;
            padding: 32px 28px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.1);
            position: relative;
        }

        .listening-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 22px;
        }

        .mic-badge {
            position: relative;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        .mic-badge::before,
        .mic-badge::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid var(--primary);
            animation: ringPulse 2.2s ease-out infinite;
        }

        .mic-badge::after {
            animation-delay: 1.1s;
        }

        @keyframes ringPulse {
            0% {
                transform: scale(1);
                opacity: 0.7;
            }

            100% {
                transform: scale(2.1);
                opacity: 0;
            }
        }

        .listening-status {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--dark);
        }

        .listening-sub {
            font-size: 0.78rem;
            color: var(--muted);
        }

        .waveform {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            height: 46px;
            margin: 8px 0 24px;
        }

        .waveform span {
            width: 4px;
            border-radius: 3px;
            background: linear-gradient(180deg, var(--primary-light), var(--primary));
            animation: bar 1.2s ease-in-out infinite;
        }

        @keyframes bar {

            0%,
            100% {
                height: 10px;
            }

            50% {
                height: 42px;
            }
        }

        .ayat-box {
            background: rgba(5, 150, 105, 0.05);
            border: 1px solid rgba(5, 150, 105, 0.12);
            border-radius: 16px;
            padding: 20px;
            text-align: right;
            font-family: 'Amiri', serif;
            font-size: 1.55rem;
            line-height: 2.4;
            color: var(--ink);
            direction: rtl;
        }

        .ayat-box .word {
            transition: color 0.35s ease, opacity 0.35s ease;
            opacity: 0.32;
        }

        .ayat-box .word.active {
            color: var(--primary-dark);
            opacity: 1;
        }

        .ayat-box .word.done {
            color: var(--gold);
            opacity: 0.9;
        }

        .score-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .score-pill {
            background: var(--dark);
            color: var(--gold-light);
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        /* ===== Features ===== */
        .section-pad {
            padding: 100px 0;
        }

        .section-eyebrow {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .section-title {
            font-size: clamp(1.9rem, 3.5vw, 2.6rem);
            font-weight: 800;
            color: var(--dark);
            margin-top: 10px;
        }

        .feature-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: 34px 28px;
            height: 100%;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.03);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        }

        .feature-icon {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: rgba(5, 150, 105, 0.1);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 18px;
        }

        .feature-card.accent .feature-icon {
            background: rgba(201, 162, 39, 0.14);
            color: var(--gold);
        }

        .feature-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.65;
            margin: 0;
        }

        /* ===== How it works ===== */
        .how-section {
            background: linear-gradient(180deg, rgba(5, 150, 105, 0.04), transparent);
        }

        .step-item {
            display: flex;
            gap: 18px;
            padding: 22px 0;
            border-bottom: 1px solid var(--border-soft);
        }

        .step-item:last-child {
            border-bottom: none;
        }

        .step-num {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--primary);
            opacity: 0.5;
            min-width: 46px;
        }

        .step-item h4 {
            font-weight: 700;
            font-size: 1.05rem;
            margin-bottom: 4px;
        }

        .step-item p {
            color: var(--muted);
            font-size: 0.92rem;
            margin: 0;
        }

        .phone-mock {
            background: var(--dark);
            border-radius: 34px;
            padding: 14px;
            max-width: 300px;
            margin: 0 auto;
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.28);
        }

        .phone-mock-screen {
            background: linear-gradient(160deg, #ffffff, #f1faf6);
            border-radius: 24px;
            padding: 22px 18px;
            min-height: 460px;
            display: flex;
            flex-direction: column;
        }

        .phone-mock-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .phone-mock-header i {
            color: var(--primary);
        }

        .phone-progress {
            height: 6px;
            border-radius: 4px;
            background: rgba(5, 150, 105, 0.12);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .phone-progress-fill {
            height: 100%;
            width: 42%;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 4px;
            animation: progressGrow 6s ease-in-out infinite;
        }

        @keyframes progressGrow {
            0% {
                width: 15%;
            }

            50% {
                width: 78%;
            }

            100% {
                width: 15%;
            }
        }

        .phone-mock .ayat-box {
            font-size: 1.15rem;
            flex-grow: 1;
        }

        /* ===== CTA ===== */
        .cta-section {
            background: linear-gradient(120deg, var(--dark), #10281f 60%, var(--dark));
            border-radius: 32px;
            padding: 70px 40px;
            text-align: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 20%, rgba(5, 150, 105, 0.35), transparent 55%),
                radial-gradient(circle at 80% 80%, rgba(201, 162, 39, 0.25), transparent 50%);
        }

        .cta-section>* {
            position: relative;
            z-index: 1;
        }

        .cta-section h2 {
            font-size: clamp(1.8rem, 3.5vw, 2.4rem);
            font-weight: 800;
            margin-bottom: 14px;
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.7);
            max-width: 480px;
            margin: 0 auto 30px;
        }

        /* ===== Footer ===== */
        footer {
            padding: 50px 0 30px;
            border-top: 1px solid var(--border-soft);
            color: var(--muted);
            font-size: 0.9rem;
        }

        .footer-social a {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(5, 150, 105, 0.08);
            color: var(--primary-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
            transition: all 0.3s ease;
        }

        .footer-social a:hover {
            background: var(--primary);
            color: #fff;
        }

        @media (max-width: 767px) {
            .arabic-deco {
                display: none;
            }

            .hero {
                padding-top: 110px;
            }
        }
    </style>
</head>

<body>

    <div id="preloader">
        <div class="preloader-mark"><i class="fa-solid fa-book-quran"></i></div>
        <div class="preloader-text">Menyiapkan Hifzly</div>
    </div>

    <div class="aurora"><span></span><span></span><span></span></div>

    <!-- Navbar -->
    <nav class="navbar navbar-custom navbar-expand-lg" id="mainNav">
        <div class="container d-flex align-items-center justify-content-between">
            <a class="d-flex align-items-center gap-2 text-decoration-none" href="#">
                <span class="brand-mark"><i class="fa-solid fa-book-quran"></i></span>
                <span class="brand-text">Hifzly</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="fa-solid fa-bars fs-4"></i>
            </button>
            <div class="collapse navbar-collapse flex-grow-0" id="navMenu">
                <ul class="navbar-nav align-items-lg-center gap-lg-1 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#cara-kerja">Cara Kerja</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="login.php" class="btn btn-gold">
                            <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Masuk
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="arabic-deco">القرآن الكريم</div>
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="hero-badge"><i class="fa-solid fa-sparkles"></i>Teknologi AI Generasi Baru</span>
                    <h1>Revolusi Cara Kamu <span class="text-gradient">Menjaga Hafalan</span></h1>
                    <p class="lead-custom">
                        Hifzly menggunakan teknologi Voice Recognition dan Smart AI Coach untuk mendengarkan, mengoreksi, dan membantu murajaahmu menjadi lebih interaktif setiap hari.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="register.php" class="btn btn-primary-custom">
                            Mulai Gratis <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                        <a href="#fitur" class="btn btn-outline-custom">
                            <i class="fa-solid fa-circle-play me-2"></i>Pelajari Fitur
                        </a>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="150">
                    <div class="listening-card">
                        <div class="listening-header">
                            <span class="mic-badge"><i class="fa-solid fa-microphone"></i></span>
                            <div>
                                <div class="listening-status">Sedang Mendengarkan...</div>
                                <div class="listening-sub">QS. An-Naba &middot; Ayat 1-2</div>
                            </div>
                        </div>
                        <div class="waveform" id="waveform"></div>
                        <div class="ayat-box" id="ayatBox">
                            <span class="word" data-w="1">عَمَّ</span>
                            <span class="word" data-w="2">يَتَسَاءَلُونَ</span>
                            <span class="word" data-w="3">عَنِ</span>
                            <span class="word" data-w="4">النَّبَإِ</span>
                            <span class="word" data-w="5">الْعَظِيمِ</span>
                        </div>
                        <div class="score-row">
                            <span><i class="fa-solid fa-chart-simple me-1"></i>Akurasi bacaan real-time</span>
                            <span class="score-pill">98% Tepat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="fitur" class="section-pad">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-eyebrow">Fitur Unggulan</span>
                <h2 class="section-title">Semua yang Kamu Butuhkan untuk Menjaga Hafalan</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-6" data-aos="fade-up">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-microphone-lines"></i></div>
                        <h3>Smart Murojaah dengan Suara</h3>
                        <p>Ayat di layar akan disembunyikan dan hanya muncul saat sistem mendeteksi bacaanmu benar. Teknologi Voice Recognition Hifzly memastikan hafalanmu akurat dan lancar secara real-time.</p>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card accent">
                        <div class="feature-icon"><i class="fa-solid fa-book-open-reader"></i></div>
                        <h3>E-Qur'an Interaktif Terpadu</h3>
                        <p>Membaca 114 surat lengkap dengan terjemahan, tafsir, dan audio. Jika kamu bertilawah dari sini, data akan langsung terekam otomatis ke jurnal Mutabaah-mu.</p>
                    </div>
                </div>

                <div class="col-md-6" data-aos="fade-up" data-aos-delay="150">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-robot"></i></div>
                        <h3>AI Coach Pribadi</h3>
                        <p>Dapatkan skor dari setiap bacaanmu. AI akan menganalisis progresmu dan memberikan rekomendasi surah mana yang perlu lebih sering diulang.</p>
                    </div>
                </div>

                <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card accent">
                        <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                        <h3>Mutabaah Yaumi</h3>
                        <p>Catatan amal digital komprehensif ala Github Contribution. Pantau streak, hafalan baru, dan konsistensimu hari demi hari.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="cara-kerja" class="section-pad how-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 order-lg-2" data-aos="fade-left">
                    <div class="phone-mock">
                        <div class="phone-mock-screen">
                            <div class="phone-mock-header">
                                <i class="fa-solid fa-microphone fa-lg"></i>
                                <div>
                                    <div class="fw-bold small">Murojaah - QS. An-Naba</div>
                                    <div class="text-muted" style="font-size:0.72rem;">Ayat 1 dari 40</div>
                                </div>
                            </div>
                            <div class="phone-progress">
                                <div class="phone-progress-fill"></div>
                            </div>
                            <div class="ayat-box" id="ayatBoxPhone">
                                <span class="word" data-w="1">عَمَّ</span>
                                <span class="word" data-w="2">يَتَسَاءَلُونَ</span>
                                <span class="word" data-w="3">عَنِ</span>
                                <span class="word" data-w="4">النَّبَإِ</span>
                                <span class="word" data-w="5">الْعَظِيمِ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 order-lg-1" data-aos="fade-right">
                    <span class="section-eyebrow">Cara Kerja</span>
                    <h2 class="section-title mb-4">Tiga Langkah Menuju Hafalan yang Terjaga</h2>

                    <div class="step-item">
                        <div class="step-num">01</div>
                        <div>
                            <h4><i class="fa-solid fa-microphone me-2 text-success"></i>Bacakan Ayat</h4>
                            <p>Aktifkan mikrofon dan mulai murojaah seperti biasa, tanpa melihat mushaf.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">02</div>
                        <div>
                            <h4><i class="fa-solid fa-wand-magic-sparkles me-2 text-success"></i>AI Mengoreksi Real-time</h4>
                            <p>Sistem mendeteksi setiap kata yang kamu ucapkan dan menandai bacaan yang kurang tepat.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">03</div>
                        <div>
                            <h4><i class="fa-solid fa-chart-simple me-2 text-success"></i>Lihat Progres di Mutabaah</h4>
                            <p>Skor dan konsistensi harianmu otomatis tercatat, lengkap dengan rekomendasi ulangan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section-pad pt-0">
        <div class="container">
            <div class="cta-section" data-aos="zoom-in">
                <h2>Mulai Jaga Hafalanmu Hari Ini</h2>
                <p>Gratis untuk memulai. Tidak perlu kartu kredit, cukup niat dan konsistensi.</p>
                <a href="register.php" class="btn btn-primary-custom">
                    <i class="fa-solid fa-book-quran me-2"></i>Daftar Sekarang
                </a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <span class="brand-mark" style="width:32px;height:32px;font-size:0.9rem;"><i class="fa-solid fa-book-quran"></i></span>
                <span>&copy; <?= date('Y'); ?> Hifzly. Pendamping Murojaah Al-Qur'an Berbasis AI.</span>
            </div>
            <div class="footer-social">
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                <a href="#"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <script>
        // Preloader
        window.addEventListener('load', function() {
            document.getElementById('preloader').classList.add('hide');
        });

        // AOS init
        AOS.init({
            duration: 700,
            once: true,
            offset: 60
        });

        // Navbar scroll state
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', function() {
            nav.classList.toggle('scrolled', window.scrollY > 30);
        });

        // Waveform bars generator
        const waveform = document.getElementById('waveform');
        const barCount = 24;
        for (let i = 0; i < barCount; i++) {
            const bar = document.createElement('span');
            bar.style.animationDelay = (i * 0.06) + 's';
            bar.style.animationDuration = (0.9 + Math.random() * 0.6) + 's';
            waveform.appendChild(bar);
        }

        // "Live caption" word-by-word highlight loop (the signature demo animation)
        function runCaptionLoop(boxId, interval) {
            const box = document.getElementById(boxId);
            if (!box) return;
            const words = box.querySelectorAll('.word');
            let i = 0;
            setInterval(() => {
                words.forEach((w, idx) => {
                    w.classList.remove('active');
                    if (idx < i) w.classList.add('done');
                    else w.classList.remove('done');
                });
                if (words[i]) words[i].classList.add('active');
                i = (i + 1) % words.length;
                if (i === 0) words.forEach(w => w.classList.remove('done'));
            }, interval);
        }
        runCaptionLoop('ayatBox', 850);
        runCaptionLoop('ayatBoxPhone', 700);
    </script>
</body>

</html>