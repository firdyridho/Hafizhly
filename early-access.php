<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#059669">
    <title>Get Early Access - Hifzhly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #6ee7b7;
            --hero-top: #0ea371;
            --hero-bottom: #34d399;
            --gold: #c9a227;
            --ink: #0f172a;
            --muted: #64748b;
            --paper: #f7faf8;
            --white: #ffffff;
            --border: #e6ebe8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--paper);
            color: var(--ink);
            overflow-x: hidden;
            /* Instant display variables for removed splash screen */
            opacity: 1 !important;
        }

        h1,
        h2,
        h3,
        .display-font {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.001ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.001ms !important;
            }
        }

        a {
            text-decoration: none;
        }

        .container {
            width: 100%;
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 32px;
        }

        /* Staggered hero entrance (Adjusted for no splash screen) */
        .hero-copy,
        .phone-stage,
        .hero-sub,
        .store-buttons {
            opacity: 0;
            transform: translateY(28px);
            animation: fadeUpIn 0.9s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .hero-copy {
            animation-delay: 0.1s;
        }

        .phone-stage {
            animation-delay: 0.25s;
        }

        .hero-sub {
            animation-delay: 0.4s;
        }

        .store-buttons {
            animation-delay: 0.5s;
        }

        @keyframes fadeUpIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Generic scroll reveal */
        .reveal-up {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity 0.7s cubic-bezier(.16, 1, .3, 1), transform 0.7s cubic-bezier(.16, 1, .3, 1);
        }

        .reveal-up.in-view {
            opacity: 1;
            transform: translateY(0);
        }

        /* ============ HERO ============ */
        .hero {
            position: relative;
            background: linear-gradient(180deg, var(--hero-top) 0%, var(--hero-bottom) 100%);
            padding-bottom: 0;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.09) 1px, transparent 1px);
            background-size: 26px 26px;
            opacity: 0.5;
            pointer-events: none;
        }

        .hero-arabic-mark {
            position: absolute;
            font-family: 'Amiri', serif;
            font-size: 11rem;
            color: rgba(255, 255, 255, 0.06);
            top: -10px;
            right: -10px;
            line-height: 1;
            pointer-events: none;
            user-select: none;
        }

        .navbar {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 28px 0 0;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--white);
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.3px;
        }

        .brand-mark {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .navbar .nav-cta {
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 30px;
            transition: all 0.25s ease;
        }

        .navbar .nav-cta:hover {
            border-color: rgba(255, 255, 255, 0.6);
        }

        .hero-copy {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 680px;
            margin: 40px auto 0;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #fff;
            padding: 7px 16px 7px 12px;
            border-radius: 30px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .hero h1 {
            color: var(--white);
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1.8px;
        }

        /* Static Mockup (Behind 3D if needed, but we use mostly HTML here) */
        .phone-stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 430px;
            margin-top: 37px;
            perspective: 1400px;
            z-index: 5;
        }

        .phone-aura {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.28), rgba(255, 255, 255, 0) 70%);
            filter: blur(6px);
            animation: auraPulse 5s ease-in-out infinite;
        }

        @keyframes auraPulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.1);
                opacity: 1;
            }
        }



        .phone-float {
            animation: floatY 5.5s ease-in-out infinite;
            position: relative;
            z-index: 5;
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-14px);
            }
        }

        .phone-tilt {
            transition: transform 0.15s ease-out;
            transform-style: preserve-3d;
        }

        .phone-frame {
            position: relative;
            width: 250px;
            height: 508px;
            background: linear-gradient(155deg, #1e293b 0%, #0f172a 45%, #020617 100%);
            border-radius: 48px;
            padding: 12px;
            box-shadow: 0 34px 64px rgba(4, 30, 20, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.12) inset, 0 1px 0 rgba(255, 255, 255, 0.18) inset;
        }

        .phone-notch {
            position: absolute;
            top: 16px;
            left: 50%;
            transform: translateX(-50%);
            width: 88px;
            height: 25px;
            background: #060a08;
            border-radius: 20px;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
            z-index: 4;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 9px;
        }

        .phone-notch::after {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #1c2b26;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.08) inset;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #ffffff, #f4faf7);
            border-radius: 36px;
            overflow: hidden;
            position: relative;
            padding: 32px 17px 17px;
            display: flex;
            flex-direction: column;
        }

        .screen-status {
            display: flex;
            justify-content: space-between;
            font-size: 0.6rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 14px;
        }

        .screen-greet {
            font-size: 0.66rem;
            color: var(--muted);
            font-weight: 600;
        }

        .screen-greet strong {
            display: block;
            color: var(--ink);
            font-size: 0.98rem;
            font-weight: 800;
            margin-top: 2px;
        }

        .juz-ring-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 14px 0 12px;
            background: #eef7f2;
            border-radius: 16px;
            padding: 9px 11px;
        }

        .juz-ring {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-dark) 0deg, var(--primary-dark) var(--pct, 220deg), #dbe9e2 var(--pct, 220deg));
            display: flex;
            align-items: center;
            justify-content: center;
            animation: ringGrow 6s ease-in-out infinite;
            flex-shrink: 0;
        }

        @keyframes ringGrow {

            0%,
            100% {
                --pct: 210deg;
            }

            50% {
                --pct: 265deg;
            }
        }

        .juz-ring::after {
            content: '';
            width: 26px;
            height: 26px;
            background: #eef7f2;
            border-radius: 50%;
        }

        .juz-text small {
            display: block;
            font-size: 0.56rem;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .juz-text strong {
            font-size: 0.75rem;
            color: var(--ink);
        }

        .ayat-list {
            display: flex;
            flex-direction: column;
            gap: 7px;
            list-style: none;
            flex: 1;
        }

        .ayat-row {
            display: flex;
            align-items: center;
            gap: 9px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 8px 9px;
        }

        .ayat-check {
            width: 17px;
            height: 17px;
            border-radius: 6px;
            border: 1.5px solid #cfe3d9;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.52rem;
            color: #fff;
        }

        .ayat-row .name {
            font-size: 0.64rem;
            font-weight: 700;
            color: var(--ink);
        }

        .ayat-row .sub {
            font-size: 0.54rem;
            color: var(--muted);
        }

        .ayat-row.r1 .ayat-check {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .ayat-row.r2 .ayat-check {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* ============ HERO BOTTOM FADE ============ */
        .hero-bottom-fade {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 300px;
            pointer-events: none;
            z-index: 6;
        }

        .hero-bottom-fade .fade-color {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(52, 211, 153, 0) 0%, rgba(46, 197, 140, 0.45) 45%, var(--paper) 92%);
        }

        .hero-sub {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 480px;
            margin: -20px auto 0;
        }

        .hero-sub p.lede {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.05rem;
            line-height: 1.6;
        }

        .store-buttons {
            position: relative;
            z-index: 10;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin: 24px auto 0;
            padding-bottom: 60px;
            max-width: 640px;
        }

        /* Animated background for white sections */
        .features::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(16, 185, 129, 0.06) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(52, 211, 153, 0.04) 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 80%, rgba(110, 231, 183, 0.03) 0%, transparent 50%);
            animation: bgDrift 12s ease-in-out infinite alternate;
            pointer-events: none;
        }

        @keyframes bgDrift {
            0% { transform: scale(1) translate(0, 0); }
            100% { transform: scale(1.1) translate(2%, -1%); }
        }

        .features .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.04);
            pointer-events: none;
            z-index: 0;
        }

        .features .floating-shape:nth-child(1) {
            width: 300px; height: 300px;
            top: 10%; left: -5%;
            animation: floatShape 8s ease-in-out infinite alternate;
        }

        .features .floating-shape:nth-child(2) {
            width: 200px; height: 200px;
            bottom: 15%; right: -3%;
            animation: floatShape 10s ease-in-out infinite alternate-reverse;
        }

        .features .floating-shape:nth-child(3) {
            width: 150px; height: 150px;
            top: 50%; left: 60%;
            animation: floatShape 7s ease-in-out infinite alternate;
        }

        @keyframes floatShape {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, -20px) scale(1.15); }
        }

        .btn-store {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            border-radius: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-store.primary {
            background: #fff;
            color: var(--primary-dark);
            box-shadow: 0 14px 28px rgba(4, 30, 20, 0.2);
        }

        .btn-store.primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(4, 30, 20, 0.28);
        }

        .btn-store.ghost {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: rgba(255, 255, 255, 0.9);
            cursor: not-allowed;
            position: relative;
        }

        .btn-store .icon {
            font-size: 1.4rem;
            width: 20px;
            text-align: center;
        }

        .btn-store .txt {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.25;
        }

        .btn-store .txt small {
            font-size: 0.6rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.6px;
            opacity: 0.85;
        }

        .btn-store .txt span {
            font-size: 0.95rem;
        }

        .soon-chip {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--gold);
            color: #1a1400;
            font-size: 0.55rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 20px;
        }

        /* ============ QUICK HIGHLIGHTS (Enhanced White Background) ============ */
        .highlights {
            position: relative;
            z-index: 10;
            padding: 26px 0 0;
            background: linear-gradient(180deg, var(--paper) 0%, #ffffff 100%);
        }

        .highlight-chip {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 16px;
            height: 100%;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
        }

        .highlight-chip:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.08);
            border-color: var(--primary-light);
        }

        .highlight-chip .hi-icon {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            background: rgba(16, 185, 129, 0.12);
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .highlight-chip strong {
            display: block;
            font-size: 0.85rem;
            color: var(--ink);
            font-weight: 700;
        }

        .highlight-chip span {
            font-size: 0.74rem;
            color: var(--muted);
        }

        /* ============ FEATURES (Enhanced Background + 3D Canvas) ============ */
        .features {
            padding: 70px 0 40px;
            position: relative;
            background: radial-gradient(circle at top right, rgba(110, 231, 183, 0.1), transparent 50%), radial-gradient(circle at bottom left, rgba(52, 211, 153, 0.05), transparent 40%), #ffffff;
        }

        .section-head {
            text-align: center;
            max-width: 560px;
            margin: 0 auto 30px;
        }

        .section-eyebrow {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--primary-dark);
            margin-bottom: 12px;
        }

        .section-head h2 {
            font-size: 2.3rem;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--ink);
            margin-bottom: 14px;
        }

        .section-head p {
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.6;
        }

        .features-3d-wrap {
            position: relative;
            height: 350vh;
        }

        .features-canvas-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            height: 100dvh;
            width: 100%;
            overflow: hidden;
            z-index: 1;
            cursor: grab;
            touch-action: pan-y;
        }

        .features-canvas-sticky:active {
            cursor: grabbing;
        }

        /* Hint to rotate */
        .rotate-hint {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.8);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: pulseHint 2s infinite;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .features-canvas-sticky:hover .rotate-hint {
            opacity: 1;
        }

        @keyframes pulseHint {

            0%,
            100% {
                transform: translate(-50%, 0);
            }

            50% {
                transform: translate(-50%, -5px);
            }
        }

        #phoneCanvas {
            width: 100%;
            height: 100%;
            display: block;
            outline: none;
        }

        .features-text-track {
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
        }

        .feature-slide {
            height: 100vh;
            height: 100dvh;
            display: flex;
            align-items: center;
            padding: 0 7%;
        }

        .feature-slide.align-left {
            justify-content: flex-start;
        }

        .feature-slide.align-right {
            justify-content: flex-end;
        }

        .slide-card {
            pointer-events: auto;
            max-width: 380px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            padding: 30px 28px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
            opacity: 0;
            transform: translateY(34px);
            transition: opacity 0.8s cubic-bezier(.16, 1, .3, 1), transform 0.8s cubic-bezier(.16, 1, .3, 1);
        }

        .feature-slide.in-view .slide-card {
            opacity: 1;
            transform: translateY(0);
        }

        .slide-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 7px 12px;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--ink);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
            margin-bottom: 16px;
        }

        .slide-badge i {
            color: var(--primary-dark);
        }

        .slide-index {
            display: block;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: var(--primary-dark);
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .slide-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            background: rgba(16, 185, 129, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 1.1rem;
            margin-bottom: 14px;
        }

        .slide-card h3 {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: 8px;
            letter-spacing: -0.4px;
        }

        .slide-card p {
            color: var(--muted);
            font-size: 0.94rem;
            line-height: 1.7;
        }

        /* ============ CLOSING CTA ============ */
        .closing-cta {
            position: relative;
            background: linear-gradient(135deg, var(--hero-top), var(--hero-bottom));
            padding: 64px 0;
            text-align: center;
            overflow: hidden;
        }

        .closing-cta h2 {
            position: relative;
            color: #fff;
            font-size: 1.9rem;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .closing-cta p {
            position: relative;
            color: rgba(255, 255, 255, 0.88);
            max-width: 460px;
            margin: 0 auto 26px;
            line-height: 1.6;
        }

        .site-footer {
            background: var(--ink);
            color: rgba(255, 255, 255, 0.6);
            padding: 30px 0;
            text-align: center;
            font-size: 0.82rem;
        }

        @media (max-width: 980px) {
            .hero h1 {
                font-size: 2.8rem;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .phone-stage {
                min-height: 380px;
                margin-top: 20px;
            }

            .phone-frame {
                width: 220px;
                height: 460px;
                padding: 10px;
            }

            .store-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-store {
                width: 100%;
                justify-content: center;
            }

            .features-3d-wrap {
                height: 280vh;
            }

            .feature-slide {
                justify-content: center !important;
                padding: 0 8%;
            }

            .slide-card {
                max-width: 88vw;
                padding: 24px 22px;
            }

            .rotate-hint {
                font-size: 0.75rem;
                bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: clamp(1.7rem, 8vw, 2rem);
            }
        }
    </style>
</head>

<body>

    <!-- ============ HERO ============ -->
    <section class="hero">
        <div class="hero-arabic-mark">اقرأ</div>
        <div class="container">
            <nav class="navbar">
                <a class="brand" href="index.php">
                    <span class="brand-mark"><img src="assets/icon/logo.png" alt="Logo"></span> Hifzhly
                </a>
                <a class="nav-cta" href="login.php">Sudah punya akun? Masuk <i class="fa-solid fa-arrow-right"></i></a>
            </nav>

            <div class="hero-copy">
                <div class="hero-eyebrow"><i class="fa-solid fa-sparkles"></i> Segera hadir di ponselmu</div>
                <h1>Genggam hafalanmu, ke mana pun melangkah.</h1>
            </div>

            <!-- Static Phone mockup (First view) -->
            <div class="phone-stage" id="phoneStage">
                <div class="phone-aura"></div>
                <div class="phone-float">
                    <div class="phone-tilt" id="phoneTilt">
                        <div class="phone-frame">
                            <div class="phone-notch"></div>
                            <div class="phone-screen">
                                <div class="screen-status"><span>9:41</span><span><i class="fa-solid fa-signal"></i> <i class="fa-solid fa-wifi"></i> <i class="fa-solid fa-battery-three-quarters"></i></span></div>
                                <div class="screen-greet">Assalamu'alaikum,<strong>Aisyah <i class="fa-solid fa-hand"></i></strong></div>
                                <div class="juz-ring-wrap">
                                    <div class="juz-ring"></div>
                                    <div class="juz-text"><small>Progres</small><strong>Juz 5 &middot; sedang berjalan</strong></div>
                                </div>
                                <ul class="ayat-list">
                                    <li class="ayat-row r1">
                                        <div class="ayat-check"><i class="fa-solid fa-check"></i></div>
                                        <div>
                                            <div class="name">QS. Al-Baqarah 1-5</div>
                                            <div class="sub">Murojaah pagi</div>
                                        </div>
                                    </li>
                                    <li class="ayat-row r2">
                                        <div class="ayat-check"><i class="fa-solid fa-check"></i></div>
                                        <div>
                                            <div class="name">QS. Al-Baqarah 6-10</div>
                                            <div class="sub">Hafalan baru</div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hero-sub">
                <p class="lede">Aplikasi mobile Hifzhly sedang di tahap penyempurnaan akhir. Murojaah, mutaba'ah, dan progres hafalanmu, kini bisa kamu bawa ke mana saja.</p>
            </div>

            <div class="store-buttons">
                <a href="#" class="btn-store primary">
                    <span class="icon"><i class="fa-solid fa-desktop"></i></span>
                    <span class="txt"><small>Tersedia sekarang</small><span>Buka Versi Web</span></span>
                </a>
                <a href="#" class="btn-store ghost" onclick="event.preventDefault()">
                    <span class="soon-chip">Soon</span><span class="icon"><i class="fa-brands fa-google-play"></i></span>
                    <span class="txt"><small>Get it on</small><span>Google Play</span></span>
                </a>
                <a href="#" class="btn-store ghost" onclick="event.preventDefault()">
                    <span class="soon-chip">Soon</span><span class="icon"><i class="fa-brands fa-apple"></i></span>
                    <span class="txt"><small>Download on the</small><span>App Store</span></span>
                </a>
            </div>
        </div>
        <div class="hero-bottom-fade">
            <div class="fade-color"></div>
        </div>
    </section>

    <!-- ============ QUICK HIGHLIGHTS ============ -->
    <section class="highlights">
        <div class="container">
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-3 reveal-up">
                    <div class="highlight-chip">
                        <span class="hi-icon"><i class="fa-solid fa-cloud"></i></span>
                        <span><strong>Auto-sync</strong><span>Realtime ke semua device</span></span>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 reveal-up">
                    <div class="highlight-chip">
                        <span class="hi-icon"><i class="fa-solid fa-fire"></i></span>
                        <span><strong>Streak harian</strong><span>Pengingat tepat waktu</span></span>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 reveal-up">
                    <div class="highlight-chip">
                        <span class="hi-icon"><i class="fa-solid fa-file-export"></i></span>
                        <span><strong>Laporan siap pakai</strong><span>Ekspor PDF & Excel</span></span>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 reveal-up">
                    <div class="highlight-chip">
                        <span class="hi-icon"><i class="fa-solid fa-shield-heart"></i></span>
                        <span><strong>Ramah musyrif</strong><span>Pantau progres santri</span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FEATURES — 3D pinned zigzag scroll ============ -->
    <section class="features">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="container">
            <div class="section-head reveal-up">
                <div class="section-eyebrow">Kenapa Hifzhly</div>
                <h2>Dibangun untuk konsistensi hafalanmu</h2>
                <p>Empat kebiasaan kecil yang dijaga aplikasinya — kamu bisa memutar HP 3D di bawah ini untuk melihat detail layarnya.</p>
            </div>
        </div>

        <div class="features-3d-wrap" id="features3DWrap">
            <div class="features-canvas-sticky">
                <div class="rotate-hint"><i class="fa-solid fa-hand-pointer"></i> Geser/Drag untuk putar HP</div>
                <canvas id="phoneCanvas" aria-hidden="true"></canvas>
            </div>
            <div class="features-text-track">

                <div class="feature-slide align-right">
                    <div class="slide-card">
                        <div class="slide-badge"><i class="fa-solid fa-check"></i> Tersimpan otomatis</div>
                        <span class="slide-index">Fitur 01</span>
                        <div class="slide-icon"><i class="fa-solid fa-list-check"></i></div>
                        <h3>Mutaba'ah Cerdas</h3>
                        <p>Catat setiap aktivitas tilawah, murojaah, dan hafalan barumu dengan cepat dan rapi, langsung tercentang otomatis begitu selesai.</p>
                    </div>
                </div>

                <div class="feature-slide align-left">
                    <div class="slide-card">
                        <div class="slide-badge"><i class="fa-solid fa-fire"></i> 7 hari beruntun</div>
                        <span class="slide-index">Fitur 02</span>
                        <div class="slide-icon"><i class="fa-solid fa-fire"></i></div>
                        <h3>Konsistensi &amp; Streak</h3>
                        <p>Pertahankan api semangatmu setiap hari. Jangan biarkan streak-mu terputus, aplikasi akan mengingatkanmu tepat waktu.</p>
                    </div>
                </div>

                <div class="feature-slide align-right">
                    <div class="slide-card">
                        <div class="slide-badge"><i class="fa-solid fa-file-arrow-down"></i> Laporan_Juli.pdf</div>
                        <span class="slide-index">Fitur 03</span>
                        <div class="slide-icon"><i class="fa-solid fa-file-export"></i></div>
                        <h3>Ekspor Laporan</h3>
                        <p>Unduh rekap aktivitas bulananmu ke dalam format PDF atau Excel, siap dibagikan ke musyrif atau orang tua.</p>
                    </div>
                </div>

                <div class="feature-slide align-left">
                    <div class="slide-card">
                        <div class="slide-badge"><i class="fa-solid fa-check"></i> Tersinkron</div>
                        <span class="slide-index">Fitur 04</span>
                        <div class="slide-icon"><i class="fa-solid fa-arrows-rotate"></i></div>
                        <h3>Multi-Platform</h3>
                        <p>Sinkronisasi sempurna. Akses data hafalanmu dari Desktop, Android, maupun iOS, selalu terbarui otomatis.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============ CLOSING CTA ============ -->
    <section class="closing-cta">
        <div class="container reveal-up">
            <h2>Siap jaga hafalanmu setiap hari?</h2>
            <p>Coba versi web-nya sekarang, gratis, sambil menunggu aplikasi mobile-nya rilis.</p>
            <a href="#" class="btn-store primary">
                <span class="icon"><i class="fa-solid fa-desktop"></i></span>
                <span class="txt"><small>Mulai sekarang</small><span>Buka Versi Web</span></span>
            </a>
        </div>
    </section>

    <footer class="site-footer">
        <div class="container">&copy; <?= date('Y') ?> Hifzhly. Pendamping Murojaah Al-Qur'an Berbasis AI.</div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>

    <script>
        (function() {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;

            // Simple Reveal animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.15
            });
            document.querySelectorAll('.reveal-up, .feature-slide').forEach(el => observer.observe(el));

            // Hero Phone 3D Tilt (Mouse Move only, not touch)
            const stage = document.getElementById('phoneStage');
            const tilt = document.getElementById('phoneTilt');
            if (stage && tilt && !isMobile) {
                stage.addEventListener('mousemove', (e) => {
                    const rect = stage.getBoundingClientRect();
                    const x = (e.clientX - rect.left) / rect.width - 0.5;
                    const y = (e.clientY - rect.top) / rect.height - 0.5;
                    tilt.style.transform = `rotateY(${x * 12}deg) rotateX(${-y * 12}deg)`;
                }, { passive: true });
                stage.addEventListener('mouseleave', () => tilt.style.transform = 'rotateY(0deg) rotateX(0deg)');
            }

            /* ---------- 3D PHONE (OPTIMIZED) ---------- */
            function initPhone3D() {
                const wrap = document.getElementById('features3DWrap');
                const stickyEl = document.querySelector('.features-canvas-sticky');
                const canvas = document.getElementById('phoneCanvas');

                if (!wrap || !stickyEl || !canvas || typeof THREE === 'undefined') return;

                const renderer = new THREE.WebGLRenderer({
                    canvas,
                    alpha: true,
                    antialias: false
                });
                renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1));

                const scene = new THREE.Scene();
                const camera = new THREE.PerspectiveCamera(30, 1, 0.1, 100);
                camera.position.set(0, 0, 7.5);

                scene.add(new THREE.AmbientLight(0xffffff, 0.8));
                const dirLight = new THREE.DirectionalLight(0xffffff, 1.2);
                dirLight.position.set(5, 5, 5);
                scene.add(dirLight);

                const screenCanvas = document.createElement('canvas');
                screenCanvas.width = 200;
                screenCanvas.height = 400;
                const screenCtx = screenCanvas.getContext('2d');
                const screenTexture = new THREE.CanvasTexture(screenCanvas);
                screenTexture.minFilter = THREE.LinearFilter;

                const phoneGroup = new THREE.Group();
                phoneGroup.rotation.y = -0.25;

                const shape = new THREE.Shape();
                const w = 1.6, h = 3.3, r = 0.25;
                shape.moveTo(-w / 2, -h / 2 + r);
                shape.lineTo(-w / 2, h / 2 - r);
                shape.quadraticCurveTo(-w / 2, h / 2, -w / 2 + r, h / 2);
                shape.lineTo(w / 2 - r, h / 2);
                shape.quadraticCurveTo(w / 2, h / 2, w / 2, h / 2 - r);
                shape.lineTo(w / 2, -h / 2 + r);
                shape.quadraticCurveTo(w / 2, -h / 2, w / 2 - r, -h / 2);
                shape.lineTo(-w / 2 + r, -h / 2);
                shape.quadraticCurveTo(-w / 2, -h / 2, -w / 2, -h / 2 + r);

                const bodyGeo = new THREE.ExtrudeGeometry(shape, {
                    depth: 0.18,
                    bevelEnabled: true,
                    bevelThickness: 0.02,
                    bevelSize: 0.02,
                    bevelSegments: 1
                });

                const bodyMat = new THREE.MeshStandardMaterial({
                    color: 0x0f172a,
                    metalness: 0.6,
                    roughness: 0.3
                });
                const bodyMesh = new THREE.Mesh(bodyGeo, bodyMat);
                bodyMesh.position.z = -0.09;
                phoneGroup.add(bodyMesh);

                const screenGeo = new THREE.PlaneGeometry(1.48, 3.12);
                const screenMat = new THREE.MeshBasicMaterial({ map: screenTexture });
                const screenMesh = new THREE.Mesh(screenGeo, screenMat);
                screenMesh.position.z = 0.12;
                phoneGroup.add(screenMesh);

                scene.add(phoneGroup);

                // --- INTERACTIVE DRAG ---
                let targetRotationX = 0;
                let targetRotationY = -0.25;
                let isDragging = false;
                let prevMouse = { x: 0, y: 0 };

                function onDown(e) {
                    isDragging = true;
                    prevMouse = {
                        x: e.touches ? e.touches[0].clientX : e.clientX,
                        y: e.touches ? e.touches[0].clientY : e.clientY
                    };
                }

                function onMove(e) {
                    if (!isDragging) return;
                    const cx = e.touches ? e.touches[0].clientX : e.clientX;
                    const cy = e.touches ? e.touches[0].clientY : e.clientY;
                    targetRotationY += (cx - prevMouse.x) * 0.01;
                    targetRotationX += (cy - prevMouse.y) * 0.01;
                    targetRotationX = Math.max(-0.5, Math.min(0.5, targetRotationX));
                    prevMouse = { x: cx, y: cy };
                }

                function onUp() {
                    isDragging = false;
                }

                stickyEl.addEventListener('mousedown', onDown);
                stickyEl.addEventListener('mousemove', onMove);
                window.addEventListener('mouseup', onUp);

                // Touch: only on direct touch (not scroll) via threshold
                let touchStartY = 0;
                stickyEl.addEventListener('touchstart', (e) => {
                    touchStartY = e.touches[0].clientY;
                    onDown(e);
                }, { passive: true });

                stickyEl.addEventListener('touchmove', (e) => {
                    const dy = Math.abs(e.touches[0].clientY - touchStartY);
                    if (dy > 10) { isDragging = false; return; }
                    onMove(e);
                }, { passive: true });

                window.addEventListener('touchend', onUp);

                function resize() {
                    const width = stickyEl.clientWidth;
                    const height = stickyEl.clientHeight;
                    renderer.setSize(width, height, false);
                    camera.aspect = width / height;
                    camera.updateProjectionMatrix();
                }
                window.addEventListener('resize', resize);
                resize();

                function drawScreenUI(index) {
                    const cw = screenCanvas.width, ch = screenCanvas.height;
                    screenCtx.fillStyle = '#ffffff';
                    screenCtx.fillRect(0, 0, cw, ch);

                    screenCtx.fillStyle = '#059669';
                    screenCtx.font = 'bold 18px sans-serif';
                    screenCtx.textAlign = 'center';

                    if (index === 0) {
                        screenCtx.fillText("Mutaba'ah Harian", cw / 2, ch / 2 - 20);
                        screenCtx.fillStyle = '#e2edea';
                        screenCtx.fillRect(30, ch / 2 + 10, cw - 60, 16);
                        screenCtx.fillRect(30, ch / 2 + 36, cw - 100, 16);
                    } else if (index === 1) {
                        screenCtx.fillStyle = '#f59e0b';
                        screenCtx.font = 'bold 48px sans-serif';
                        screenCtx.fillText("7", cw / 2, ch / 2);
                        screenCtx.fillStyle = '#64748b';
                        screenCtx.font = '13px sans-serif';
                        screenCtx.fillText("Hari Beruntun", cw / 2, ch / 2 + 28);
                    } else if (index === 2) {
                        screenCtx.fillText("Export Laporan", cw / 2, ch / 2 - 20);
                        screenCtx.fillStyle = '#e2edea';
                        screenCtx.fillRect(50, ch / 2 + 20, cw - 100, 30);
                    } else {
                        screenCtx.fillText("Sinkronisasi Aktif", cw / 2, ch / 2 - 20);
                        screenCtx.fillStyle = '#10b981';
                        screenCtx.beginPath();
                        screenCtx.arc(cw / 2, ch / 2 + 24, 20, 0, Math.PI * 2);
                        screenCtx.fill();
                    }

                    screenCtx.fillStyle = '#000';
                    screenCtx.beginPath();
                    screenCtx.roundRect(cw / 2 - 40, 8, 80, 20, 10);
                    screenCtx.fill();

                    screenTexture.needsUpdate = true;
                }

                let lastIndex = -1;
                let animId = null;
                let isVisible = false;
                const X_POS = [-1.2, 1.2, -1.2, 1.2];

                function animate() {
                    if (!isVisible) { animId = null; return; }
                    animId = requestAnimationFrame(animate);

                    const rect = wrap.getBoundingClientRect();
                    const total = wrap.offsetHeight - window.innerHeight;
                    let progress = total > 0 ? Math.min(Math.max(-rect.top / total, 0), 1) : 0;

                    const scaled = progress * 3;
                    const index = Math.min(Math.floor(scaled), 3);
                    const frac = scaled - index;
                    const nextIndex = Math.min(index + 1, 3);

                    const targetX = isMobile ? 0 : X_POS[index] + (X_POS[nextIndex] - X_POS[index]) * frac;
                    phoneGroup.position.x += (targetX - phoneGroup.position.x) * 0.1;

                    phoneGroup.rotation.y += (targetRotationY - phoneGroup.rotation.y) * 0.1;
                    phoneGroup.rotation.x += (targetRotationX - phoneGroup.rotation.x) * 0.1;

                    if (index !== lastIndex) {
                        drawScreenUI(index);
                        lastIndex = index;
                    }

                    renderer.render(scene, camera);
                }

                const io = new IntersectionObserver((entries) => {
                    isVisible = entries[0].isIntersecting;
                    if (isVisible && !animId) animate();
                }, { threshold: 0.05 });
                io.observe(wrap);
            }

            initPhone3D();
        })();
    </script>
</body>

</html>