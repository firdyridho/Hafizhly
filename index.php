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
    <title>Hafizhly - AI Quran Companion</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
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
            --primary-dark: #046354;
            --primary-darker: #033b30;
            --primary-light: #34d399;
            --primary-lighter: #6ee7b7;
            --mint: #ecfdf5;
            --mint-soft: #d7f6e9;
            --secondary: #0d9488;
            --danger-soft: #e05252;
            --primary-glow: rgba(5, 150, 105, 0.35);
            --dark: #06231b;
            --ink: #0b241c;
            --muted: #5c7268;
            --bg: #f6faf8;
            --white: #ffffff;
            --card-bg: rgba(255, 255, 255, 0.78);
            --border-soft: rgba(6, 35, 27, 0.08);
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

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.001ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.001ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* ===== Preloader ===== */
        #preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            width: 100vw;
            height: 100dvh;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            gap: clamp(14px, 4vw, 22px);
            align-items: center;
            justify-content: center;
            padding: 0 24px;
            box-sizing: border-box;
            transition: opacity 0.6s ease, visibility 0.6s ease;
            overflow: hidden;
        }

        #preloader.hide {
            opacity: 0;
            visibility: hidden;
        }

        .preloader-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(clamp(36px, 8vw, 60px));
            animation: drift 7s ease-in-out infinite;
        }

        .preloader-orb.o1 {
            width: clamp(160px, 45vw, 280px);
            height: clamp(160px, 45vw, 280px);
            background: radial-gradient(circle, rgba(52, 211, 153, 0.4), transparent 70%);
            top: -15%;
            left: -12%;
        }

        .preloader-orb.o2 {
            width: clamp(150px, 42vw, 260px);
            height: clamp(150px, 42vw, 260px);
            background: radial-gradient(circle, rgba(5, 150, 105, 0.3), transparent 70%);
            bottom: -18%;
            right: -12%;
            animation-delay: -3.5s;
        }

        .preloader-ring-wrap {
            position: relative;
            width: clamp(68px, 18vw, 92px);
            height: clamp(68px, 18vw, 92px);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .preloader-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid rgba(5, 150, 105, 0.14);
            border-top-color: var(--primary);
            border-right-color: var(--primary-light);
            animation: spin 1s linear infinite;
        }

        .preloader-mark {
            width: clamp(44px, 12vw, 60px);
            height: clamp(44px, 12vw, 60px);
            border-radius: 17px 6px 17px 17px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 9px;
            animation: markPulse 1.3s ease-in-out infinite;
            box-shadow: 0 12px 30px rgba(5, 150, 105, 0.3);
        }

        .preloader-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .preloader-text {
            color: var(--primary-dark);
            font-size: clamp(0.65rem, 2.4vw, 0.78rem);
            font-weight: 700;
            letter-spacing: clamp(1.5px, 0.6vw, 3px);
            text-transform: uppercase;
            text-align: center;
            white-space: nowrap;
        }

        .preloader-bar {
            width: clamp(120px, 40vw, 160px);
            height: 4px;
            border-radius: 4px;
            background: rgba(5, 150, 105, 0.12);
            overflow: hidden;
        }

        .preloader-bar-fill {
            height: 100%;
            width: 40%;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            animation: loadBar 1.4s ease-in-out infinite;
        }

        @keyframes loadBar {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(250%);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes markPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(0.9);
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
            will-change: transform;
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
            background: radial-gradient(circle, var(--primary-lighter), transparent 70%);
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

        /* ===== Scroll progress ===== */
        #scrollProgress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            z-index: 1101;
            transition: width 0.1s linear;
        }

        /* ===== Ripple ===== */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.55);
            transform: scale(0);
            animation: rippleAnim 0.6s ease-out;
            pointer-events: none;
        }

        @keyframes rippleAnim {
            to {
                transform: scale(3);
                opacity: 0;
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
            box-shadow: 0 8px 24px rgba(6, 35, 27, 0.05);
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 11px 4px 11px 11px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.35);
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .brand-text {
            font-weight: 800;
            font-size: 1.35rem;
            color: var(--dark);
        }

        .nav-link-custom {
            position: relative;
            color: var(--ink) !important;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 8px 18px !important;
        }

        .nav-link-custom::after {
            content: '';
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 2px;
            height: 2px;
            border-radius: 2px;
            background: var(--primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            color: var(--primary) !important;
        }

        .nav-link-custom:hover::after,
        .nav-link-custom.active::after {
            transform: scaleX(1);
        }

        .btn-gold {
            position: relative;
            overflow: hidden;
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
            animation: badgeFloat 3.4s ease-in-out infinite;
        }

        @keyframes badgeFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-4px);
            }
        }

        .hero h1 {
            font-size: clamp(2.4rem, 5vw, 3.6rem);
            font-weight: 800;
            line-height: 1.15;
            margin: 22px 0 20px;
            color: var(--dark);
        }

        .hero h1 .text-gradient {
            background: linear-gradient(100deg, var(--primary), var(--primary-light) 60%, var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: gradientShift 6s ease-in-out infinite;
        }

        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% center;
            }

            50% {
                background-position: 100% center;
            }
        }

        .hero p.lead-custom {
            font-size: clamp(0.95rem, 2.2vw, 1.08rem);
            color: var(--muted);
            max-width: 520px;
            line-height: 1.7;
            margin-bottom: 34px;
        }

        .btn-primary-custom {
            position: relative;
            overflow: hidden;
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
            position: relative;
            overflow: hidden;
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
            will-change: transform;
        }

        .listening-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 28px;
            padding: 32px 28px;
            box-shadow: 0 24px 60px rgba(6, 35, 27, 0.12);
            position: relative;
            transition: transform 0.25s ease;
            will-change: transform;
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
            color: var(--secondary);
            opacity: 0.9;
        }

        .ayat-box .word.wrong {
            color: var(--danger-soft) !important;
            opacity: 1 !important;
            text-decoration: wavy underline;
            text-decoration-color: var(--danger-soft);
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
            color: var(--primary-lighter);
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        /* ===== Section basics ===== */
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
            box-shadow: 0 10px 30px rgba(6, 35, 27, 0.04);
            transition: transform 0.3s ease, box-shadow 0.35s ease;
            will-change: transform;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 45px rgba(6, 35, 27, 0.1);
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
            background: rgba(13, 148, 136, 0.14);
            color: var(--secondary);
        }

        .feature-card h3 {
            font-size: clamp(1.05rem, 2.4vw, 1.2rem);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.65;
            margin: 0;
        }

        /* ===== 3D Mushaf page-flip carousel (signature) ===== */
        .book-scroll-section {
            position: relative;
            height: 260vh;
        }

        .book-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .book-glow {
            position: absolute;
            width: 620px;
            height: 620px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.22), transparent 70%);
            top: 50%;
            left: 62%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            filter: blur(20px);
        }

        .book-3d-wrap {
            perspective: 2400px;
            perspective-origin: 50% 40%;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .book {
            position: relative;
            width: min(300px, 68vw);
            height: min(400px, 55vh);
            transform-style: preserve-3d;
            will-change: transform;
        }

        .book-page-card {
            position: absolute;
            inset: 0;
            background: linear-gradient(155deg, #ffffff, var(--mint));
            border-radius: 6px 16px 16px 6px;
            box-shadow: 0 30px 70px rgba(6, 35, 27, 0.22), inset 0 0 0 1px rgba(5, 150, 105, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: clamp(16px, 3vw, 28px);
            text-align: center;
            transform: rotateY(90deg) scale(0.94);
            opacity: 0;
            pointer-events: none;
            transition: transform 0.65s cubic-bezier(.45, .1, .2, 1), opacity 0.45s ease;
            backface-visibility: hidden;
        }

        .book-page-card.is-active {
            transform: rotateY(0deg) scale(1);
            opacity: 1;
            pointer-events: auto;
            z-index: 5;
        }

        .book-page-card.is-prev {
            transform: rotateY(-110deg) scale(0.92);
            opacity: 0;
        }

        .book-page-card .mushaf-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--primary-dark);
            margin-bottom: 12px;
        }

        .book-page-card .ayat-box {
            font-size: clamp(1rem, 2.4vw, 1.35rem);
            background: transparent;
            border: none;
            padding: 0;
        }

        .book-page-card .mushaf-juz {
            margin-top: 14px;
            font-size: 0.72rem;
            color: var(--muted);
            font-weight: 600;
        }

        .book-cover {
            position: absolute;
            inset: 0;
            transform-origin: left center;
            background: linear-gradient(135deg, var(--primary-light), var(--primary) 55%, var(--primary-darker));
            border-radius: 6px 16px 16px 6px;
            box-shadow: 0 26px 60px rgba(6, 35, 27, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            backface-visibility: hidden;
            will-change: transform;
            z-index: 10;
            transition: transform 0.1s linear;
        }

        .cover-inner {
            text-align: center;
            color: #fff;
        }

        .cover-mark {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            backdrop-filter: blur(4px);
        }

        .cover-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .cover-title {
            font-family: 'Amiri', serif;
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1.4;
        }

        .book-dots {
            display: flex;
            gap: 8px;
            margin-top: 22px;
        }

        .book-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: rgba(5, 150, 105, 0.25);
            transition: all 0.35s ease;
        }

        .book-dots span.active {
            background: var(--primary);
            width: 22px;
            border-radius: 4px;
        }

        .book-progress-track {
            height: 6px;
            border-radius: 4px;
            background: rgba(5, 150, 105, 0.14);
            overflow: hidden;
            margin-top: 26px;
            max-width: 320px;
        }

        .book-progress-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            border-radius: 4px;
        }

        .book-progress-label {
            margin-top: 10px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        /* ===== Cara Kerja - scrollytelling ===== */
        .how-section {
            background: linear-gradient(180deg, rgba(5, 150, 105, 0.04), transparent);
        }

        .step-track {
            position: relative;
        }

        .step-item-scroll {
            min-height: 58vh;
            display: flex;
            align-items: center;
            opacity: 0.32;
            transition: opacity 0.45s ease;
        }

        .step-item-scroll:first-child {
            min-height: 40vh;
        }

        .step-item-scroll.active {
            opacity: 1;
        }

        .step-item-scroll .step-inner {
            display: flex;
            gap: 18px;
        }

        .step-num {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 1.7rem;
            color: var(--primary);
            opacity: 0.45;
            min-width: 46px;
            transition: all 0.35s ease;
        }

        .step-item-scroll.active .step-num {
            opacity: 1;
            transform: scale(1.12);
        }

        .step-item-scroll h4 {
            font-weight: 700;
            font-size: 1.12rem;
            margin-bottom: 6px;
        }

        .step-item-scroll p {
            color: var(--muted);
            font-size: 0.94rem;
            margin: 0;
            max-width: 420px;
        }

        .step-note {
            margin-top: 10px;
            padding: 40px 0 10px;
            text-align: left;
        }

        .step-note-card {
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 22px 24px;
            max-width: 480px;
        }

        .step-note-card i {
            color: var(--primary);
        }

        .phone-sticky-col {
            position: sticky;
            top: 14vh;
        }

        .phone-mock {
            background: var(--dark);
            border-radius: 34px;
            padding: 14px;
            max-width: 300px;
            margin: 0 auto;
            box-shadow: 0 30px 70px rgba(6, 35, 27, 0.3);
        }

        .phone-mock-screen {
            background: linear-gradient(160deg, #ffffff, #eefbf4);
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            min-height: 480px;
        }

        .phone-scene {
            position: absolute;
            inset: 0;
            padding: 22px 18px;
            display: flex;
            flex-direction: column;
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.45s ease, transform 0.45s ease;
            pointer-events: none;
        }

        .phone-scene.active {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .phone-mock-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
        }

        .phone-mock-header i {
            color: var(--primary);
        }

        .scene-mic-wrap {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 18px;
        }

        .scene-mic-big {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            position: relative;
        }

        .scene-mic-big::before,
        .scene-mic-big::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid var(--primary);
            animation: ringPulse 2.2s ease-out infinite;
        }

        .scene-mic-big::after {
            animation-delay: 1.1s;
        }

        .scene-waveform-mini {
            display: flex;
            align-items: center;
            gap: 3px;
            height: 30px;
        }

        .scene-waveform-mini span {
            width: 3px;
            border-radius: 3px;
            background: linear-gradient(180deg, var(--primary-light), var(--primary));
            animation: bar 1s ease-in-out infinite;
        }

        .scene-hint-tag {
            margin-top: 12px;
            background: rgba(224, 82, 82, 0.1);
            color: var(--danger-soft);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .contrib-grid {
            display: grid;
            grid-template-columns: repeat(9, 1fr);
            gap: 4px;
            margin: 18px 0;
        }

        .contrib-grid span {
            aspect-ratio: 1;
            border-radius: 3px;
            background: rgba(5, 150, 105, 0.12);
        }

        .streak-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--mint);
            border-radius: 14px;
            padding: 12px 16px;
            margin-top: auto;
        }

        .streak-badge .num {
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--primary-dark);
        }

        .streak-badge .lbl {
            font-size: 0.72rem;
            color: var(--muted);
        }

        .scene-badge-wrap {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 10px;
        }

        .scene-trophy {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            box-shadow: 0 14px 30px rgba(5, 150, 105, 0.35);
        }

        .scene-badge-wrap h5 {
            font-weight: 800;
            font-size: 1rem;
            margin: 0;
        }

        .scene-badge-wrap p {
            font-size: 0.8rem;
            color: var(--muted);
            margin: 0;
        }

        .scene-reco-list {
            width: 100%;
            margin-top: 8px;
            text-align: left;
        }

        .scene-reco-list div {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.78rem;
            color: var(--ink);
            padding: 7px 10px;
            background: var(--mint);
            border-radius: 10px;
            margin-bottom: 6px;
        }

        .scene-reco-list i {
            color: var(--primary);
            font-size: 0.7rem;
        }

        /* ===== Game / interactive features ===== */
        .game-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-soft);
            border-radius: 22px;
            padding: 32px 26px;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.35s ease;
        }

        .game-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 45px rgba(6, 35, 27, 0.1);
        }

        .game-card::after {
            content: '';
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.1), transparent 70%);
            top: -50px;
            right: -50px;
        }

        .game-badge-new {
            position: absolute;
            top: 18px;
            right: 18px;
            background: var(--primary);
            color: #fff;
            font-size: 0.66rem;
            font-weight: 700;
            padding: 4px 11px;
            border-radius: 20px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .game-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(5, 150, 105, 0.1);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 18px;
            position: relative;
            z-index: 1;
        }

        .game-card h3 {
            font-size: 1.12rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .game-card p {
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.6;
            margin: 0 0 14px;
            position: relative;
            z-index: 1;
        }

        .game-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--primary-dark);
            position: relative;
            z-index: 1;
        }

        /* ===== How-it-works old classes reused for step icon lines ===== */
        .phone-mock .ayat-box {
            font-size: 1.1rem;
        }

        /* ===== CTA ===== */
        .cta-section {
            background: linear-gradient(120deg, var(--dark), var(--primary-darker) 60%, var(--dark));
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
            background: radial-gradient(circle at 30% 20%, rgba(5, 150, 105, 0.35), transparent 55%), radial-gradient(circle at 80% 80%, rgba(52, 211, 153, 0.28), transparent 50%);
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

        footer a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.85rem;
        }

        footer a:hover {
            color: var(--primary-dark);
        }

        /* ===== Mobile ===== */
        @media (max-width: 991px) {
            #navMenu {
                background: rgba(255, 255, 255, 0.97);
                backdrop-filter: blur(10px);
                border-radius: 18px;
                padding: 14px;
                margin-top: 10px;
                box-shadow: 0 16px 40px rgba(6, 35, 27, 0.1);
                border: 1px solid var(--border-soft);
            }

            .nav-link-custom::after {
                display: none;
            }

            .nav-item.ms-lg-3 {
                margin-top: 8px;
            }

            .nav-item.ms-lg-3 .btn-gold {
                display: block;
                text-align: center;
            }

            .phone-sticky-col {
                position: static;
                top: auto;
                margin-bottom: 30px;
            }
        }

        @media (max-width: 767px) {
            .arabic-deco {
                display: none;
            }

            .hero {
                min-height: auto;
                padding: 100px 0 50px;
                text-align: center;
            }

            .hero .lead-custom {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }

            .hero .btn-group,
            .hero .d-flex.flex-wrap {
                justify-content: center;
            }

            .listening-card {
                padding: 24px 20px;
                margin-top: 10px;
            }

            .ayat-box {
                font-size: 1.2rem;
                padding: 16px;
                line-height: 2.1;
            }

            .waveform {
                height: 38px;
            }

            .score-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .section-pad {
                padding: 64px 0;
            }

            .feature-card,
            .game-card {
                padding: 26px 22px;
            }

            .cta-section {
                padding: 46px 22px;
                border-radius: 24px;
            }

            .phone-mock {
                max-width: 260px;
            }

            footer .container {
                text-align: center;
            }

            .book-scroll-section {
                height: 220vh;
            }

            .book-3d-wrap {
                perspective: 1400px;
            }

            .book-glow {
                width: 420px;
                height: 420px;
                left: 50%;
            }

            .book-progress-track {
                margin-left: auto;
                margin-right: auto;
            }

            .step-item-scroll {
                min-height: 42vh;
            }

            .step-item-scroll:first-child {
                min-height: 26vh;
            }

            .step-note-card {
                max-width: 100%;
            }
        }

        @media (max-width: 575px) {
            .hero h1 {
                font-size: 2.1rem;
            }

            .btn-primary-custom,
            .btn-outline-custom {
                width: 100%;
                justify-content: center;
                padding: 13px 22px;
            }

            .hero .d-flex.flex-wrap {
                flex-direction: column;
            }

            .ayat-box {
                font-size: 1.05rem;
            }
        }

        @media (max-width: 380px) {
            .container {
                padding-left: 14px;
                padding-right: 14px;
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .hero-badge {
                font-size: 0.7rem;
                padding: 7px 14px;
            }

            .listening-card {
                padding: 18px 14px;
                border-radius: 20px;
            }

            .ayat-box {
                font-size: 0.92rem;
                padding: 12px;
            }

            .section-title {
                font-size: 1.55rem;
            }

            .feature-card,
            .game-card {
                padding: 20px 16px;
            }

            .cta-section h2 {
                font-size: 1.5rem;
            }
        }

        @media (max-height: 560px) and (orientation: landscape) {
            .hero {
                min-height: auto;
                padding: 110px 0 40px;
            }

            .book-scroll-section {
                height: 240vh;
            }

            .phone-sticky-col {
                position: static;
            }

            #preloader {
                gap: 10px;
            }
        }

        @media (hover: none) {

            .feature-card:hover,
            .listening-card:hover,
            .game-card:hover {
                transform: none;
            }
        }

        /* ===== Preloader — mo.js powered logo reveal ===== */
        .preloader-brandrow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: clamp(12px, 3.2vw, 22px);
            flex-wrap: wrap;
        }

        .preloader-stage {
            position: relative;
            width: clamp(60px, 16vw, 92px);
            height: clamp(60px, 16vw, 92px);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .mojs-ring,
        .mojs-burst {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            overflow: visible;
        }

        .preloader-stage .preloader-mark {
            position: relative;
            z-index: 2;
            animation: none;
            overflow: hidden;
            clip-path: none;
            transition: clip-path 0.9s cubic-bezier(.16, 1, .3, 1);
        }

        .preloader-stage .preloader-mark.reveal-init {
            clip-path: circle(0% at 50% 50%);
        }

        .preloader-stage .preloader-mark.revealed {
            clip-path: circle(75% at 50% 50%);
        }

        .preloader-stage .preloader-mark::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(115deg, transparent 35%, rgba(255, 255, 255, 0.65) 50%, transparent 65%);
            transform: translateX(-140%);
            pointer-events: none;
        }

        .preloader-stage .preloader-mark.shine::after {
            animation: shineSweep 0.9s ease forwards;
        }

        @keyframes shineSweep {
            to {
                transform: translateX(140%);
            }
        }

        .preloader-brand {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: clamp(1.55rem, 6.5vw, 2.15rem);
            color: var(--dark);
            letter-spacing: 0.01em;
        }

        .preloader-brand .pb-letter {
            display: inline-block;
            opacity: 0;
            will-change: transform, opacity;
        }

        .no-mojs .preloader-brand .pb-letter {
            opacity: 1;
            transform: none !important;
        }

        .no-mojs .preloader-stage .preloader-mark {
            clip-path: none !important;
        }

        .no-mojs .preloader-stage .preloader-mark::after {
            display: none;
        }

        .no-mojs .preloader-stage .preloader-ring-wrap {
            display: flex;
        }

        .preloader-stage .preloader-ring-wrap {
            display: none;
            position: absolute;
            inset: 0;
            margin: auto;
        }

        /* ===== Celebration popup — Spain 2026 World Cup ===== */
        .celebration-overlay {
            position: fixed;
            inset: 0;
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(6, 20, 15, 0.6);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.45s ease, visibility 0.45s ease;
        }

        .celebration-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .celebration-confetti {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .celebration-confetti span {
            position: absolute;
            top: -20px;
            width: 7px;
            height: 12px;
            border-radius: 2px;
            opacity: 0.9;
            animation: confettiFall linear infinite;
        }

        @keyframes confettiFall {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.9;
            }

            100% {
                transform: translateY(105vh) rotate(540deg);
                opacity: 0;
            }
        }

        .celebration-card {
            position: relative;
            z-index: 2;
            width: min(400px, 100%);
            background: linear-gradient(165deg, rgba(255, 255, 255, 0.97), rgba(255, 249, 235, 0.97));
            border: 1px solid rgba(255, 196, 0, 0.35);
            border-radius: 26px;
            padding: clamp(28px, 5vw, 38px) clamp(22px, 5vw, 32px) 30px;
            text-align: center;
            box-shadow: 0 40px 90px rgba(6, 35, 27, 0.35);
            transform: scale(0.85) translateY(20px);
            opacity: 0;
            transition: transform 0.5s cubic-bezier(.2, 1.5, .4, 1), opacity 0.4s ease;
        }

        .celebration-overlay.show .celebration-card {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        .celebration-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: rgba(6, 35, 27, 0.06);
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.25s ease, color 0.25s ease;
        }

        .celebration-close:hover {
            background: rgba(6, 35, 27, 0.12);
            color: var(--dark);
        }

        .celebration-flag {
            width: clamp(96px, 24vw, 128px);
            height: clamp(64px, 16vw, 85px);
            margin: 0 auto 18px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 14px 30px rgba(200, 16, 46, 0.28), 0 0 0 1px rgba(6, 35, 27, 0.08);
        }

        .celebration-flag svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .celebration-trophy {
            width: 44px;
            height: 44px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffd873, #d4af37);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            box-shadow: 0 10px 22px rgba(212, 175, 55, 0.45);
            animation: trophyBounce 2.4s ease-in-out infinite;
        }

        @keyframes trophyBounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .celebration-card h3 {
            font-weight: 800;
            font-size: clamp(1.2rem, 4vw, 1.45rem);
            color: var(--dark);
            margin-bottom: 8px;
        }

        .celebration-card p {
            color: var(--muted);
            font-size: 0.88rem;
            line-height: 1.65;
            max-width: 320px;
            margin: 0 auto 18px;
        }

        .celebration-score {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 700;
            font-size: 0.82rem;
            color: var(--dark);
            background: rgba(212, 175, 55, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 30px;
            padding: 9px 16px;
        }

        .celebration-score b {
            font-size: 1rem;
            color: #b8860b;
        }

        .celebration-score .sep {
            color: var(--muted);
            font-weight: 400;
        }

        @media (max-width: 380px) {
            .celebration-card {
                padding: 24px 18px 26px;
            }
        }
    </style>
</head>

<body>

    <div id="preloader">
        <div class="preloader-orb o1"></div>
        <div class="preloader-orb o2"></div>
        <div class="preloader-brandrow">
            <div class="preloader-stage">
                <div class="mojs-ring" id="mojsRing"></div>
                <div class="preloader-ring-wrap">
                    <div class="preloader-ring"></div>
                </div>
                <div class="preloader-mark" id="preloaderMark"><img src="assets/icon/logo.png" alt="Hafizhly"></div>
                <div class="mojs-burst" id="mojsBurst"></div>
            </div>
            <div class="preloader-brand" id="preloaderBrand" data-text="Hafizhly">Hafizhly</div>
        </div>
        <div class="preloader-text" id="preloaderTagline">Menyiapkan Hafizhly</div>
        <div class="preloader-bar">
            <div class="preloader-bar-fill"></div>
        </div>
    </div>

    <!-- Celebration popup: Spain — 2026 World Cup Champions -->
    <div class="celebration-overlay" id="celebrationOverlay">
        <div class="celebration-confetti" id="celebrationConfetti"></div>
        <div class="celebration-card">
            <button class="celebration-close" id="celebrationClose" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>

            <div class="celebration-flag">
                <svg viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
                    <filter id="flagWave">
                        <feTurbulence type="fractalNoise" baseFrequency="0.015 0.04" numOctaves="2" seed="4" result="noise">
                            <animate attributeName="baseFrequency" dur="5s" values="0.015 0.04;0.025 0.06;0.015 0.04" repeatCount="indefinite" />
                        </feTurbulence>
                        <feDisplacementMap in="SourceGraphic" in2="noise" scale="7" xChannelSelector="R" yChannelSelector="G" />
                    </filter>
                    <g filter="url(#flagWave)">
                        <rect width="120" height="80" fill="#C60B1E"></rect>
                        <rect y="20" width="120" height="40" fill="#FFC400"></rect>
                    </g>
                </svg>
            </div>

            <div class="celebration-trophy"><i class="fa-solid fa-trophy"></i></div>

            <h3>¡Campeones del Mundo!</h3>
            <p>Spanyol juara Piala Dunia 2026 usai menang atas Argentina di final. Gol tunggal Ferran Torres di menit ke-106 memastikan trofi kedua La Roja.</p>

            <div class="celebration-score">
                <span>🇪🇸 SPAIN <b>1</b></span>
                <span class="sep">—</span>
                <span><b>0</b> ARGENTINA 🇦🇷</span>
            </div>
        </div>
    </div>

    <div id="scrollProgress"></div>
    <div class="aurora"><span></span><span></span><span></span></div>

    <!-- Navbar -->
    <nav class="navbar navbar-custom navbar-expand-lg" id="mainNav">
        <div class="container d-flex align-items-center justify-content-between">
            <a class="d-flex align-items-center gap-2 text-decoration-none" href="#">
                <span class="brand-mark"><img src="assets/icon/logo.png" alt="Hafizhly"></span>
                <span class="brand-text">Hafizhly</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="fa-solid fa-bars fs-4"></i>
            </button>
            <div class="collapse navbar-collapse flex-grow-0" id="navMenu">
                <ul class="navbar-nav align-items-lg-center gap-lg-1 mt-3 mt-lg-0">
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#beranda">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#fitur">Fitur</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#jelajah-quran">Jelajah Al-Qur'an</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#cara-kerja">Cara Kerja</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="#belajar-seru">Belajar Seru</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-custom" href="early-access.php">Dapatkan Akses Awal</a></li>
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
    <section class="hero" id="beranda">
        <div class="arabic-deco" id="arabicDeco">القرآن الكريم</div>
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="hero-badge"><i class="fa-solid fa-sparkles"></i>Teknologi AI Generasi Baru</span>
                    <h1>Revolusi Cara Kamu <span class="text-gradient">Menjaga Hafalan</span></h1>
                    <p class="lead-custom">
                        Hafizhly menggunakan teknologi Voice Recognition dan Smart AI Coach untuk mendengarkan, mengoreksi, dan membantu murajaahmu menjadi lebih interaktif setiap hari.
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
                        <p>Ayat di layar akan disembunyikan dan hanya muncul saat sistem mendeteksi bacaanmu benar. Teknologi Voice Recognition Hafizhly memastikan hafalanmu akurat dan lancar secara real-time.</p>
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

    <!-- 3D Mushaf page-flip carousel -->
    <section class="book-scroll-section" id="jelajah-quran">
        <div class="book-sticky">
            <div class="book-glow"></div>
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-5 d-flex justify-content-center order-lg-2">
                        <div class="book-3d-wrap">
                            <div class="book" id="book3d">
                                <div class="book-page-card" data-page="1">
                                    <div class="mushaf-label">QS. Al-Fatihah &middot; Ayat 1-3</div>
                                    <div class="ayat-box"><span class="word">بِسْمِ</span> <span class="word">اللَّهِ</span> <span class="word">الرَّحْمَٰنِ</span> <span class="word">الرَّحِيمِ</span></div>
                                    <div class="mushaf-juz">Juz 1 &middot; Al-Fatihah</div>
                                </div>
                                <div class="book-page-card" data-page="2">
                                    <div class="mushaf-label">QS. Al-Ikhlas &middot; Ayat 1-2</div>
                                    <div class="ayat-box"><span class="word">قُلْ</span> <span class="word">هُوَ</span> <span class="word">اللَّهُ</span> <span class="word">أَحَدٌ</span></div>
                                    <div class="mushaf-juz">Juz 30 &middot; Al-Ikhlas</div>
                                </div>
                                <div class="book-page-card" data-page="3">
                                    <div class="mushaf-label">QS. An-Naba &middot; Ayat 1-2</div>
                                    <div class="ayat-box"><span class="word">عَمَّ</span> <span class="word">يَتَسَاءَلُونَ</span> <span class="word">عَنِ</span> <span class="word">النَّبَإِ</span> <span class="word">الْعَظِيمِ</span></div>
                                    <div class="mushaf-juz">Juz 30 &middot; An-Naba</div>
                                </div>
                                <div class="book-cover" id="bookCover">
                                    <div class="cover-inner">
                                        <span class="cover-mark"><img src="assets/icon/logo.png" alt="Hafizhly"></span>
                                        <div class="cover-title">Al-Qur'an<br>Digital</div>
                                    </div>
                                </div>
                            </div>
                            <div class="book-dots" id="bookDots">
                                <span></span><span></span><span></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 order-lg-1" data-aos="fade-right">
                        <span class="section-eyebrow">114 Surat Dalam Genggaman</span>
                        <h2 class="section-title mb-3">Terus Scroll, Terus Berpindah Surah</h2>
                        <p class="lead-custom">Ini gambaran koleksi Al-Qur'an digital di Hafizhly. Setiap scroll membuka lembar baru dan berpindah ke surah berikutnya, lengkap dengan penanda juz agar kamu tetap tahu posisi bacaanmu.</p>
                        <div class="book-progress-track">
                            <div class="book-progress-fill" id="bookProgressFill"></div>
                        </div>
                        <div class="book-progress-label" id="bookProgressLabel">Membuka mushaf...</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cara Kerja - scrollytelling -->
    <section id="cara-kerja" class="section-pad how-section">
        <div class="container">
            <div class="text-center mb-4" data-aos="fade-up">
                <span class="section-eyebrow">Cara Kerja</span>
                <h2 class="section-title">Empat Langkah Menuju Hafalan yang Terjaga</h2>
                <p class="lead-custom mx-auto" style="text-align:center;">Scroll ke bawah, dan lihat tampilan HP berubah mengikuti setiap tahap prosesnya.</p>
            </div>

            <div class="row g-5">
                <div class="col-lg-5 order-lg-2">
                    <div class="phone-sticky-col">
                        <div class="phone-mock">
                            <div class="phone-mock-screen">
                                <div class="phone-scene active" data-scene="1">
                                    <div class="phone-mock-header">
                                        <i class="fa-solid fa-microphone fa-lg"></i>
                                        <div>
                                            <div class="fw-bold small">Murojaah - QS. An-Naba</div>
                                            <div class="text-muted" style="font-size:0.72rem;">Menunggu suaramu</div>
                                        </div>
                                    </div>
                                    <div class="scene-mic-wrap">
                                        <div class="scene-mic-big"><i class="fa-solid fa-microphone"></i></div>
                                        <div class="scene-waveform-mini" id="sceneWaveform1"></div>
                                        <div class="text-muted" style="font-size:0.8rem;">Sedang mendengarkan...</div>
                                    </div>
                                </div>

                                <div class="phone-scene" data-scene="2">
                                    <div class="phone-mock-header">
                                        <i class="fa-solid fa-wand-magic-sparkles fa-lg"></i>
                                        <div>
                                            <div class="fw-bold small">AI Mengoreksi</div>
                                            <div class="text-muted" style="font-size:0.72rem;">Analisis real-time</div>
                                        </div>
                                    </div>
                                    <div class="ayat-box">
                                        <span class="word done">عَمَّ</span> <span class="word wrong">يَتَسَاءَلُونَ</span> <span class="word done">عَنِ</span> <span class="word">النَّبَإِ</span> <span class="word">الْعَظِيمِ</span>
                                    </div>
                                    <div class="scene-hint-tag"><i class="fa-solid fa-triangle-exclamation"></i>Kata ke-2 kurang tepat, coba ulangi</div>
                                </div>

                                <div class="phone-scene" data-scene="3">
                                    <div class="phone-mock-header">
                                        <i class="fa-solid fa-chart-simple fa-lg"></i>
                                        <div>
                                            <div class="fw-bold small">Mutabaah Yaumi</div>
                                            <div class="text-muted" style="font-size:0.72rem;">Progres harianmu</div>
                                        </div>
                                    </div>
                                    <div class="contrib-grid" id="contribGrid"></div>
                                    <div class="streak-badge"><span class="num">12</span><span class="lbl">Hari beruntun<br>tanpa putus</span></div>
                                </div>

                                <div class="phone-scene" data-scene="4">
                                    <div class="phone-mock-header">
                                        <i class="fa-solid fa-award fa-lg"></i>
                                        <div>
                                            <div class="fw-bold small">Rekomendasi & Lencana</div>
                                            <div class="text-muted" style="font-size:0.72rem;">Hasil akhir sesi</div>
                                        </div>
                                    </div>
                                    <div class="scene-badge-wrap">
                                        <div class="scene-trophy"><i class="fa-solid fa-award"></i></div>
                                        <h5>Lencana Juz 30 Diraih!</h5>
                                        <p>AI merekomendasikan surah berikut untuk diulang:</p>
                                        <div class="scene-reco-list">
                                            <div><i class="fa-solid fa-circle"></i>QS. An-Naba (perlu diulang)</div>
                                            <div><i class="fa-solid fa-circle"></i>QS. Al-Ikhlas (lancar)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 order-lg-1">
                    <div class="step-track">
                        <div class="step-item-scroll" data-step="1">
                            <div class="step-inner">
                                <div class="step-num">01</div>
                                <div>
                                    <h4><i class="fa-solid fa-microphone me-2 text-success"></i>Bacakan Ayat</h4>
                                    <p>Aktifkan mikrofon dan mulai murojaah seperti biasa, tanpa melihat mushaf. Hafizhly mendengarkan lewat suara HP-mu, tidak perlu alat tambahan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="step-item-scroll" data-step="2">
                            <div class="step-inner">
                                <div class="step-num">02</div>
                                <div>
                                    <h4><i class="fa-solid fa-wand-magic-sparkles me-2 text-success"></i>AI Mengoreksi Real-time</h4>
                                    <p>Sistem mendeteksi setiap kata yang kamu ucapkan, membandingkannya dengan mushaf asli, dan menandai kata yang kurang tepat saat itu juga.</p>
                                </div>
                            </div>
                        </div>
                        <div class="step-item-scroll" data-step="3">
                            <div class="step-inner">
                                <div class="step-num">03</div>
                                <div>
                                    <h4><i class="fa-solid fa-chart-simple me-2 text-success"></i>Lihat Progres di Mutabaah</h4>
                                    <p>Skor dan konsistensi harianmu otomatis tercatat, lengkap dengan grafik streak seperti kontribusi Github, biar kamu tetap semangat konsisten.</p>
                                </div>
                            </div>
                        </div>
                        <div class="step-item-scroll" data-step="4">
                            <div class="step-inner">
                                <div class="step-num">04</div>
                                <div>
                                    <h4><i class="fa-solid fa-award me-2 text-success"></i>Dapatkan Rekomendasi & Lencana</h4>
                                    <p>Selesai murojaah, AI Coach memberi rekomendasi surah yang perlu diulang dan lencana pencapaian untuk memotivasi hafalanmu terus bertambah.</p>
                                </div>
                            </div>
                        </div>
                        <div class="step-note">
                            <div class="step-note-card">
                                <p><i class="fa-solid fa-circle-info me-2"></i>Semua proses ini berjalan langsung di HP kamu, hasilnya tersimpan otomatis, dan bisa diakses kapan saja lewat dashboard Mutabaah.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Belajar Seru / Game features -->
    <section id="belajar-seru" class="section-pad">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-eyebrow">Belajar Sambil Bermain</span>
                <h2 class="section-title">Hafalan Jadi Seru dengan Mode Interaktif</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="game-card">
                        <span class="game-badge-new">Baru</span>
                        <div class="game-icon"><i class="fa-solid fa-puzzle-piece"></i></div>
                        <h3>Tebak Ayat</h3>
                        <p>Sepotong ayat ditampilkan tanpa nama surah, kamu tebak sebelum waktu habis. Cocok buat asah ingatan sambil seru-seruan bareng teman.</p>
                        <span class="game-tag"><i class="fa-solid fa-clock"></i>Mode cepat, ada leaderboard</span>
                    </div>
                </div>

                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="game-card">
                        <span class="game-badge-new">Baru</span>
                        <div class="game-icon"><i class="fa-solid fa-forward"></i></div>
                        <h3>Lanjutkan Ayat</h3>
                        <p>Sistem menampilkan potongan awal ayat, kamu ucapkan atau ketik lanjutannya. Latihan ini melatih transisi hafalan antar ayat yang sering jadi titik lupa.</p>
                        <span class="game-tag"><i class="fa-solid fa-microphone"></i>Bisa pakai suara atau ketik</span>
                    </div>
                </div>

                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="game-card">
                        <span class="game-badge-new">Baru</span>
                        <div class="game-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                        <h3>Belajar Tajwid</h3>
                        <p>Modul interaktif mengenal hukum tajwid lengkap dengan warna kode huruf, contoh audio, dan latihan singkat supaya bacaanmu makin sesuai kaidah.</p>
                        <span class="game-tag"><i class="fa-solid fa-palette"></i>Visual warna-kode, ada kuis</span>
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
                <span class="brand-mark" style="width:32px;height:32px;"><img src="assets/icon/logo.png" alt="Hafizhly"></span>
                <span>&copy; <?= date('Y'); ?> Hafizhly. Pendamping Murojaah Al-Qur'an Berbasis AI.</span>
            </div>
            <div style="display: flex; justify-content: center; gap: 15px;">
                <a href="privacy.php">Kebijakan Privasi</a>
                <span style="color: #cbd5e1;">|</span>
                <a href="terms.php">Syarat & Ketentuan</a>
                <span style="color: #cbd5e1;">|</span>
                <a href="developer.php">Developer</a>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@mojs/core/dist/mo.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
    <script>
        /* ===== Preloader entrance, powered by mo.js ===== */
        (function() {
            const preloaderEl = document.getElementById('preloader');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const hasMojs = typeof mojs !== 'undefined';

            if (!hasMojs || reduceMotion) {
                preloaderEl.classList.add('no-mojs');
            } else {
                try {
                    const markEl = document.getElementById('preloaderMark');

                    // Split brand text into individually animatable letters
                    const brandEl = document.getElementById('preloaderBrand');
                    const text = brandEl.getAttribute('data-text') || brandEl.textContent;
                    brandEl.innerHTML = '';
                    Array.from(text).forEach(function(ch) {
                        const span = document.createElement('span');
                        span.className = 'pb-letter';
                        span.textContent = ch === ' ' ? '\u00A0' : ch;
                        brandEl.appendChild(span);
                    });
                    const letterEls = brandEl.querySelectorAll('.pb-letter');

                    // Drawn ring that spins open around the mark
                    const ringDraw = new mojs.Shape({
                        parent: '#mojsRing',
                        shape: 'circle',
                        radius: 34,
                        stroke: '#059669',
                        strokeWidth: {
                            4: 0
                        },
                        strokeDasharray: '214',
                        strokeDashoffset: {
                            214: 0
                        },
                        fill: 'none',
                        opacity: {
                            1: 0.85
                        },
                        duration: 950,
                        easing: 'cubic.out',
                        isShowStart: true
                    });

                    const ringDraw2 = new mojs.Shape({
                        parent: '#mojsRing',
                        shape: 'circle',
                        radius: 29,
                        stroke: '#6ee7b7',
                        strokeWidth: 2,
                        strokeDasharray: '182',
                        strokeDashoffset: {
                            182: 0
                        },
                        fill: 'none',
                        rotate: {
                            0: 180
                        },
                        duration: 1150,
                        delay: 100,
                        easing: 'elastic.out',
                        isShowStart: true
                    });

                    // Radiating particle burst — the "flash" behind the logo reveal
                    const burst = new mojs.Burst({
                        parent: '#mojsBurst',
                        radius: {
                            0: 56
                        },
                        count: 8,
                        angle: 20,
                        children: {
                            shape: 'circle',
                            radius: {
                                5: 0
                            },
                            fill: ['#059669', '#34d399', '#6ee7b7'],
                            duration: 700,
                            easing: 'cubic.out'
                        },
                        delay: 180
                    });

                    // Logo mark: elastic pop-in, paired with the iris mask + shine below
                    const markPop = new mojs.Html({
                        target: '#preloaderMark',
                        scale: {
                            0.25: 1
                        },
                        rotate: {
                            '-40': 0
                        },
                        duration: 700,
                        delay: 160,
                        easing: 'elastic.out'
                    });

                    // --- Logo reveal: iris mask opens + a light sweep crosses the mark ---
                    markEl.classList.add('reveal-init');
                    setTimeout(function() {
                        markEl.classList.add('revealed');
                        markEl.classList.add('shine');
                    }, 160);

                    // Brand letters fly in from random directions around the page,
                    // then settle into their normal reading position, staggered.
                    const letterAnims = [];
                    letterEls.forEach(function(el, i) {
                        const angle = Math.random() * Math.PI * 2;
                        const dist = 70 + Math.random() * 110;
                        const dx = Math.round(Math.cos(angle) * dist);
                        const dy = Math.round(Math.sin(angle) * dist);
                        const rot = Math.round((Math.random() - 0.5) * 260);
                        const startScale = (Math.random() > 0.5 ? 0.25 : 1.9).toFixed(2);
                        const anim = new mojs.Html({
                            target: el,
                            x: {
                                [dx]: 0
                            },
                            y: {
                                [dy]: 0
                            },
                            rotate: {
                                [rot]: 0
                            },
                            scale: {
                                [startScale]: 1
                            },
                            opacity: {
                                0: 1
                            },
                            duration: 750,
                            delay: 480 + i * 40 + Math.random() * 60,
                            easing: 'elastic.out'
                        });
                        letterAnims.push(anim);
                    });

                    // Tagline fades up last
                    const taglineIn = new mojs.Html({
                        target: '#preloaderTagline',
                        y: {
                            12: 0
                        },
                        opacity: {
                            0: 1
                        },
                        duration: 550,
                        delay: 480 + letterEls.length * 40 + 380,
                        easing: 'cubic.out'
                    });

                    const timeline = new mojs.Timeline();
                    timeline.add.apply(timeline, [ringDraw, ringDraw2, burst, markPop].concat(letterAnims).concat([taglineIn]));
                    timeline.play();

                    // Idle breathing pulse on the mark while assets keep loading
                    const idlePulse = new mojs.Html({
                        target: '#preloaderMark',
                        scale: {
                            1: 1.06
                        },
                        duration: 1000,
                        delay: 480 + letterEls.length * 40 + 900,
                        easing: 'sin.inOut',
                        repeat: 999,
                        yoyo: true
                    });
                    idlePulse.play();
                    window.__hafizhlyIdlePulse = idlePulse;
                } catch (e) {
                    preloaderEl.classList.add('no-mojs');
                }
            }

            // Safety net: guarantee everything is visible even if animation setup fails
            setTimeout(function() {
                preloaderEl.classList.add('no-mojs');
            }, 1600);

            window.addEventListener('load', function() {
                if (window.__hafizhlyIdlePulse) {
                    try {
                        window.__hafizhlyIdlePulse.stop();
                    } catch (e) {}
                }
                if (hasMojs && !reduceMotion) {
                    try {
                        new mojs.Html({
                            target: '.preloader-brandrow, #preloaderTagline, .preloader-bar',
                            scale: {
                                1: 0.92
                            },
                            opacity: {
                                1: 0
                            },
                            duration: 380,
                            easing: 'cubic.in'
                        }).play();
                    } catch (e) {}
                }
                setTimeout(function() {
                    preloaderEl.classList.add('hide');
                }, 260);
            });
        })();

        // Show the Spain 2026 celebration popup shortly after the page is ready.
        // Runs on DOMContentLoaded (not window.load) so it doesn't wait on slow CDN assets.
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(showCelebrationPopup, 1200);
        });

        /* ===== Celebration popup: Spain 2026 World Cup ===== */
        function showCelebrationPopup() {
            const overlay = document.getElementById('celebrationOverlay');
            if (!overlay) return;

            // TESTING: shows on every reload for now.
            // To limit it to once per browser session, uncomment the two lines below.
            // if (sessionStorage.getItem('hafizhly_wc2026_seen')) return;
            // sessionStorage.setItem('hafizhly_wc2026_seen', '1');

            const confettiHost = document.getElementById('celebrationConfetti');
            const colors = ['#C60B1E', '#FFC400', '#ffffff', '#d4af37'];
            const pieceCount = window.innerWidth < 480 ? 22 : 36;
            for (let i = 0; i < pieceCount; i++) {
                const piece = document.createElement('span');
                piece.style.left = Math.random() * 100 + '%';
                piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                piece.style.animationDuration = (2.4 + Math.random() * 2.2) + 's';
                piece.style.animationDelay = (Math.random() * 1.6) + 's';
                piece.style.transform = 'rotate(' + Math.floor(Math.random() * 360) + 'deg)';
                confettiHost.appendChild(piece);
            }

            overlay.classList.add('show');

            function closePopup() {
                overlay.classList.remove('show');
            }
            document.getElementById('celebrationClose').addEventListener('click', closePopup);
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closePopup();
            });
        }

        AOS.init({
            duration: 700,
            once: true,
            offset: 60
        });

        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', function() {
            nav.classList.toggle('scrolled', window.scrollY > 30);
        });

        const scrollProgress = document.getElementById('scrollProgress');

        function updateScrollProgress() {
            const h = document.documentElement;
            const percent = (h.scrollTop / (h.scrollHeight - h.clientHeight)) * 100;
            scrollProgress.style.width = percent + '%';
        }

        const navLinks = document.querySelectorAll('.nav-link-custom');
        const trackedSections = document.querySelectorAll('section[id]');
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    navLinks.forEach((l) => l.classList.remove('active'));
                    const activeLink = document.querySelector('.nav-link-custom[href="#' + entry.target.id + '"]');
                    if (activeLink) activeLink.classList.add('active');
                }
            });
        }, {
            rootMargin: '-35% 0px -55% 0px'
        });
        trackedSections.forEach((s) => sectionObserver.observe(s));

        const navMenuEl = document.getElementById('navMenu');
        navMenuEl.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (navMenuEl.classList.contains('show')) {
                    new bootstrap.Collapse(navMenuEl).hide();
                }
            });
        });

        if (window.matchMedia('(hover: hover)').matches) {
            const addTilt = (selector, intensity) => {
                document.querySelectorAll(selector).forEach((card) => {
                    card.addEventListener('mousemove', (e) => {
                        const r = card.getBoundingClientRect();
                        const x = e.clientX - r.left;
                        const y = e.clientY - r.top;
                        const rotateX = ((y / r.height) - 0.5) * -intensity;
                        const rotateY = ((x / r.width) - 0.5) * intensity;
                        card.style.transform = 'perspective(900px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-4px)';
                    });
                    card.addEventListener('mouseleave', () => {
                        card.style.transform = '';
                    });
                });
            };
            addTilt('.feature-card', 6);
            addTilt('.listening-card', 4);
            addTilt('.game-card', 5);
        }

        document.querySelectorAll('.btn-primary-custom, .btn-outline-custom, .btn-gold').forEach((btn) => {
            btn.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const ripple = document.createElement('span');
                ripple.className = 'ripple';
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 650);
            });
        });

        function buildWaveform(el, count) {
            if (!el) return;
            for (let i = 0; i < count; i++) {
                const bar = document.createElement('span');
                bar.style.animationDelay = (i * 0.06) + 's';
                bar.style.animationDuration = (0.9 + Math.random() * 0.6) + 's';
                el.appendChild(bar);
            }
        }
        buildWaveform(document.getElementById('waveform'), 24);
        buildWaveform(document.getElementById('sceneWaveform1'), 16);

        function buildContribGrid(el) {
            if (!el) return;
            for (let i = 0; i < 27; i++) {
                const cell = document.createElement('span');
                const shade = Math.random();
                if (shade > 0.8) cell.style.background = 'var(--primary)';
                else if (shade > 0.55) cell.style.background = 'var(--primary-light)';
                else if (shade > 0.3) cell.style.background = 'rgba(5,150,105,0.3)';
                el.appendChild(cell);
            }
        }
        buildContribGrid(document.getElementById('contribGrid'));

        function runCaptionLoop(box, interval) {
            if (!box) return null;
            const words = box.querySelectorAll('.word');
            if (!words.length) return null;
            let i = 0;
            return setInterval(() => {
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
        runCaptionLoop(document.getElementById('ayatBox'), 850);

        /* Parallax on hero ambient elements */
        const arabicDeco = document.getElementById('arabicDeco');
        const auroraSpans = document.querySelectorAll('.aurora span');

        function updateParallax() {
            const y = window.scrollY;
            if (arabicDeco) arabicDeco.style.transform = 'translateY(' + (y * 0.15) + 'px)';
            auroraSpans.forEach((span, idx) => {
                const speed = 0.04 + idx * 0.015;
                span.style.transform = 'translateY(' + (y * speed) + 'px)';
            });
        }

        /* ===== 3D Mushaf page-flip carousel, scroll-driven ===== */
        const bookSection = document.getElementById('jelajah-quran');
        const bookEl = document.getElementById('book3d');
        const bookCover = document.getElementById('bookCover');
        const pageCards = Array.from(document.querySelectorAll('.book-page-card'));
        const bookProgressFill = document.getElementById('bookProgressFill');
        const bookProgressLabel = document.getElementById('bookProgressLabel');
        const bookDots = document.querySelectorAll('#bookDots span');
        let activeCardIndex = -1;
        let cardCaptionTimer = null;

        function setActiveCard(newIndex) {
            if (newIndex === activeCardIndex) return;
            pageCards.forEach((card, idx) => {
                card.classList.remove('is-active', 'is-prev');
                if (idx < newIndex) card.classList.add('is-prev');
                if (idx === newIndex) card.classList.add('is-active');
            });
            bookDots.forEach((dot, idx) => dot.classList.toggle('active', idx === newIndex));
            if (cardCaptionTimer) clearInterval(cardCaptionTimer);
            if (newIndex >= 0 && pageCards[newIndex]) {
                cardCaptionTimer = runCaptionLoop(pageCards[newIndex].querySelector('.ayat-box'), 700);
            }
            activeCardIndex = newIndex;
        }

        function updateBookScroll() {
            if (!bookSection) return;
            const rect = bookSection.getBoundingClientRect();
            const total = bookSection.offsetHeight - window.innerHeight;
            const scrolled = -rect.top;
            let progress = total > 0 ? scrolled / total : 0;
            progress = Math.min(Math.max(progress, 0), 1);

            const coverOpenPhase = 0.12;
            const coverAngle = -155 * Math.min(progress / coverOpenPhase, 1);
            bookCover.style.transform = 'rotateY(' + coverAngle + 'deg)';

            const tilt = 5 * Math.sin(progress * Math.PI);
            bookEl.style.transform = 'rotateX(' + (4 - progress * 4) + 'deg) rotateY(' + tilt + 'deg)';

            if (progress <= coverOpenPhase) {
                setActiveCard(-1);
                bookProgressLabel.textContent = 'Membuka mushaf...';
            } else {
                const cardsProgress = (progress - coverOpenPhase) / (1 - coverOpenPhase);
                const idx = Math.min(pageCards.length - 1, Math.floor(cardsProgress * pageCards.length));
                setActiveCard(idx);
                const card = pageCards[idx];
                const label = card ? card.querySelector('.mushaf-label').textContent : '';
                bookProgressLabel.textContent = label;
            }

            bookProgressFill.style.width = (progress * 100) + '%';
        }

        /* ===== Cara Kerja scrollytelling ===== */
        const stepItems = document.querySelectorAll('.step-item-scroll');
        const phoneScenes = document.querySelectorAll('.phone-scene');
        const stepObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const stepNum = entry.target.getAttribute('data-step');
                    stepItems.forEach((item) => item.classList.remove('active'));
                    entry.target.classList.add('active');
                    phoneScenes.forEach((scene) => {
                        scene.classList.toggle('active', scene.getAttribute('data-scene') === stepNum);
                    });
                }
            });
        }, {
            rootMargin: '-40% 0px -40% 0px',
            threshold: 0
        });
        stepItems.forEach((item) => stepObserver.observe(item));

        let ticking = false;

        function onScroll() {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    updateParallax();
                    updateBookScroll();
                    ticking = false;
                });
                ticking = true;
            }
        }
        window.addEventListener('scroll', onScroll);
        window.addEventListener('scroll', updateScrollProgress);
        updateBookScroll();
    </script>
</body>

</html>