<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#04140f">
    <title>Get Early Access - Hifzly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Amiri:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #059669;
            --primary-dark: #04785a;
            --primary-light: #34d399;
            --gold: #c9a227;
            --gold-light: #e8c85f;
            --ink: #0f172a;
            --night: #041711;
            --night-mid: #0f2e22;
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

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: var(--paper);
            color: var(--ink);
            overflow-x: hidden;
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
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 32px;
        }

        /* ============ HERO ============ */
        .hero {
            position: relative;
            background: radial-gradient(120% 90% at 15% 0%, #113d2c 0%, var(--night-mid) 42%, var(--night) 100%);
            padding-bottom: 90px;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 26px 26px;
            opacity: 0.5;
            pointer-events: none;
        }

        .hero-arabic-mark {
            position: absolute;
            font-family: 'Amiri', serif;
            font-size: 13rem;
            color: rgba(255, 255, 255, 0.035);
            top: -30px;
            right: -10px;
            line-height: 1;
            pointer-events: none;
            user-select: none;
        }

        .navbar {
            position: relative;
            z-index: 5;
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
            width: 34px;
            height: 34px;
            border-radius: 10px 4px 10px 10px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: #fff;
            box-shadow: 0 8px 18px rgba(5, 150, 105, 0.4);
        }

        .navbar .nav-cta {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 30px;
            transition: all 0.25s ease;
        }

        .navbar .nav-cta:hover {
            border-color: rgba(255, 255, 255, 0.4);
            color: #fff;
        }

        .hero-grid {
            position: relative;
            z-index: 3;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            align-items: center;
            gap: 40px;
            margin-top: 48px;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(201, 162, 39, 0.14);
            border: 1px solid rgba(201, 162, 39, 0.4);
            color: var(--gold-light);
            padding: 7px 16px 7px 12px;
            border-radius: 30px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1.6px;
            text-transform: uppercase;
            margin-bottom: 22px;
        }

        .hero-eyebrow i {
            font-size: 0.65rem;
        }

        .hero h1 {
            color: var(--white);
            font-size: 3.35rem;
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -1.6px;
            margin-bottom: 20px;
        }

        .hero h1 em {
            font-style: normal;
            background: linear-gradient(100deg, var(--gold-light), var(--primary-light) 75%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero p.lede {
            color: rgba(255, 255, 255, 0.68);
            font-size: 1.06rem;
            line-height: 1.7;
            max-width: 460px;
            margin-bottom: 34px;
        }

        /* Store buttons */
        .store-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
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
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: #fff;
            box-shadow: 0 14px 28px rgba(5, 150, 105, 0.35);
        }

        .btn-store.primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(5, 150, 105, 0.45);
        }

        .btn-store.ghost {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.16);
            color: rgba(255, 255, 255, 0.55);
            cursor: not-allowed;
            position: relative;
        }

        .btn-store .icon {
            font-size: 1.5rem;
            width: 22px;
            text-align: center;
        }

        .btn-store .txt {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.25;
        }

        .btn-store .txt small {
            font-size: 0.62rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.6px;
            opacity: 0.85;
        }

        .btn-store .txt span {
            font-size: 0.98rem;
        }

        .soon-chip {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--gold);
            color: var(--night);
            font-size: 0.55rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 20px;
        }

        /* ===== Phone stage (signature element) ===== */
        .phone-stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 560px;
            perspective: 1400px;
        }

        .phone-aura {
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.35), rgba(52, 211, 153, 0) 70%);
            filter: blur(10px);
            animation: auraPulse 5s ease-in-out infinite;
        }

        @keyframes auraPulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.12);
                opacity: 1;
            }
        }

        .mote {
            position: absolute;
            border-radius: 50%;
            background: var(--gold-light);
            box-shadow: 0 0 8px 2px rgba(232, 200, 95, 0.6);
            opacity: 0;
            animation: moteRise linear infinite;
        }

        @keyframes moteRise {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            12% {
                opacity: 0.9;
            }

            85% {
                opacity: 0.5;
            }

            100% {
                transform: translateY(-260px) translateX(var(--drift, 20px));
                opacity: 0;
            }
        }

        .phone-float {
            animation: floatY 5.5s ease-in-out infinite;
        }

        @keyframes floatY {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-16px);
            }
        }

        .phone-tilt {
            transition: transform 0.15s ease-out;
            transform-style: preserve-3d;
        }

        .phone-frame {
            position: relative;
            width: 252px;
            height: 512px;
            background: linear-gradient(160deg, #10241c, #04140f);
            border-radius: 46px;
            padding: 12px;
            box-shadow:
                0 40px 70px rgba(0, 0, 0, 0.55),
                0 0 0 1px rgba(255, 255, 255, 0.06) inset;
        }

        .phone-notch {
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 90px;
            height: 20px;
            background: #04140f;
            border-radius: 0 0 16px 16px;
            z-index: 4;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, #ffffff, #f4faf7);
            border-radius: 36px;
            overflow: hidden;
            position: relative;
            padding: 34px 18px 18px;
            display: flex;
            flex-direction: column;
        }

        .screen-status {
            display: flex;
            justify-content: space-between;
            font-size: 0.62rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 16px;
        }

        .screen-greet {
            font-size: 0.68rem;
            color: var(--muted);
            font-weight: 600;
        }

        .screen-greet strong {
            display: block;
            color: var(--ink);
            font-size: 1rem;
            font-weight: 800;
            margin-top: 2px;
        }

        .juz-ring-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 16px 0 14px;
            background: #eef7f2;
            border-radius: 16px;
            padding: 10px 12px;
        }

        .juz-ring {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: conic-gradient(var(--primary) 0deg, var(--primary) var(--pct, 220deg), #dbe9e2 var(--pct, 220deg));
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
            width: 28px;
            height: 28px;
            background: #eef7f2;
            border-radius: 50%;
        }

        .juz-text small {
            display: block;
            font-size: 0.58rem;
            color: var(--muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .juz-text strong {
            font-size: 0.78rem;
            color: var(--ink);
        }

        .ayat-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            list-style: none;
            flex: 1;
        }

        .ayat-row {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 9px 10px;
        }

        .ayat-check {
            width: 18px;
            height: 18px;
            border-radius: 6px;
            border: 1.5px solid #cfe3d9;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.55rem;
            color: #fff;
        }

        .ayat-row .name {
            font-size: 0.66rem;
            font-weight: 700;
            color: var(--ink);
        }

        .ayat-row .sub {
            font-size: 0.56rem;
            color: var(--muted);
        }

        .ayat-row.r1 .ayat-check {
            animation: checkFill 6s ease-in-out infinite;
        }

        .ayat-row.r2 .ayat-check {
            animation: checkFill 6s ease-in-out infinite 2s;
        }

        .ayat-row.r3 .ayat-check {
            animation: checkFill 6s ease-in-out infinite 4s;
        }

        @keyframes checkFill {

            0%,
            28% {
                background: transparent;
                border-color: #cfe3d9;
            }

            35%,
            90% {
                background: var(--primary);
                border-color: var(--primary);
            }

            96%,
            100% {
                background: transparent;
                border-color: #cfe3d9;
            }
        }

        .ayat-row.r1 {
            animation: rowGlow 6s ease-in-out infinite;
        }

        .ayat-row.r2 {
            animation: rowGlow 6s ease-in-out infinite 2s;
        }

        .ayat-row.r3 {
            animation: rowGlow 6s ease-in-out infinite 4s;
        }

        @keyframes rowGlow {

            0%,
            28% {
                box-shadow: none;
                border-color: var(--border);
            }

            35%,
            90% {
                box-shadow: 0 4px 14px rgba(5, 150, 105, 0.14);
                border-color: rgba(5, 150, 105, 0.35);
            }

            96%,
            100% {
                box-shadow: none;
                border-color: var(--border);
            }
        }

        /* ============ FEATURES ============ */
        .features {
            padding: 92px 0 100px;
            background: var(--paper);
        }

        .section-head {
            text-align: center;
            max-width: 560px;
            margin: 0 auto 56px;
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
            font-size: 2.35rem;
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

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 22px;
        }

        .feature-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 28px 28px 0;
            overflow: hidden;
            transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 46px rgba(5, 150, 105, 0.1);
            border-color: rgba(5, 150, 105, 0.28);
        }

        .feature-icon-wrapper {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            background: rgba(5, 150, 105, 0.09);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .feature-title {
            font-size: 1.12rem;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: 8px;
        }

        .feature-desc {
            color: var(--muted);
            font-size: 0.88rem;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .feature-visual {
            height: 128px;
            background: linear-gradient(180deg, #f4faf7, #eef5f1);
            border-radius: 16px 16px 0 0;
            margin: 0 -28px;
            padding: 18px 28px 0;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
        }

        /* --- Visual 1: mutaba'ah checklist ticking --- */
        .v-checklist {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 7px;
            padding-bottom: 16px;
        }

        .v-checklist .line {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .v-checklist .box {
            width: 14px;
            height: 14px;
            border-radius: 4px;
            border: 1.5px solid #cfe3d9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            color: #fff;
            flex-shrink: 0;
        }

        .v-checklist .bar {
            height: 6px;
            border-radius: 4px;
            background: #dfeae4;
            flex: 1;
        }

        .v-checklist .line1 .box {
            animation: checkFill 4.5s ease-in-out infinite;
        }

        .v-checklist .line2 .box {
            animation: checkFill 4.5s ease-in-out infinite 1.5s;
        }

        .v-checklist .line3 .box {
            animation: checkFill 4.5s ease-in-out infinite 3s;
        }

        .v-checklist .line1 .bar {
            width: 70%;
        }

        .v-checklist .line2 .bar {
            width: 55%;
        }

        .v-checklist .line3 .bar {
            width: 62%;
        }

        /* --- Visual 2: streak days --- */
        .v-streak {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding-bottom: 22px;
        }

        .v-streak .dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #dfeae4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            color: transparent;
        }

        .v-streak .dot:nth-child(1) {
            animation: dotLight 5.6s ease-in-out infinite 0s;
        }

        .v-streak .dot:nth-child(2) {
            animation: dotLight 5.6s ease-in-out infinite 0.35s;
        }

        .v-streak .dot:nth-child(3) {
            animation: dotLight 5.6s ease-in-out infinite 0.7s;
        }

        .v-streak .dot:nth-child(4) {
            animation: dotLight 5.6s ease-in-out infinite 1.05s;
        }

        .v-streak .dot:nth-child(5) {
            animation: dotLight 5.6s ease-in-out infinite 1.4s;
        }

        .v-streak .flame {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f59e0b;
            font-size: 0.85rem;
            box-shadow: 0 6px 14px rgba(245, 158, 11, 0.28);
            animation: flameFlicker 1.4s ease-in-out infinite;
        }

        @keyframes dotLight {

            0%,
            8% {
                background: #dfeae4;
            }

            16%,
            92% {
                background: var(--primary);
            }

            100% {
                background: #dfeae4;
            }
        }

        @keyframes flameFlicker {

            0%,
            100% {
                transform: scale(1) rotate(-2deg);
            }

            50% {
                transform: scale(1.12) rotate(2deg);
            }
        }

        /* --- Visual 3: export sliding into tray --- */
        .v-export {
            width: 100%;
            position: relative;
            height: 100%;
            padding-bottom: 0;
        }

        .v-export .doc {
            position: absolute;
            left: 50%;
            top: 0;
            width: 46px;
            height: 32px;
            background: #fff;
            border: 1.5px solid #cfe3d9;
            border-radius: 6px;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 0.7rem;
            animation: docDrop 3.6s ease-in-out infinite;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
        }

        @keyframes docDrop {
            0% {
                top: 0;
                opacity: 0;
            }

            18% {
                opacity: 1;
            }

            45%,
            65% {
                top: 46px;
                opacity: 1;
            }

            85% {
                opacity: 0;
                top: 60px;
            }

            100% {
                top: 0;
                opacity: 0;
            }
        }

        .v-export .tray {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 84px;
            height: 34px;
            border: 2px dashed #b9d8ca;
            border-top: none;
            border-radius: 0 0 14px 14px;
        }

        .v-export .pct {
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.58rem;
            font-weight: 800;
            color: var(--primary-dark);
            animation: pctShow 3.6s ease-in-out infinite;
            opacity: 0;
        }

        @keyframes pctShow {

            0%,
            45% {
                opacity: 0;
            }

            55%,
            80% {
                opacity: 1;
            }

            95%,
            100% {
                opacity: 0;
            }
        }

        /* --- Visual 4: multi-platform sync --- */
        .v-sync {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 26px;
            padding-bottom: 22px;
            position: relative;
        }

        .v-sync .node {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #fff;
            border: 1.5px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 0.85rem;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.06);
            z-index: 2;
        }

        .v-sync .track {
            position: absolute;
            left: 42px;
            right: 42px;
            top: 50%;
            height: 2px;
            background: repeating-linear-gradient(90deg, #cfe3d9 0 6px, transparent 6px 12px);
        }

        .v-sync .pulse {
            position: absolute;
            top: 50%;
            left: 42px;
            width: 8px;
            height: 8px;
            margin-top: -4px;
            border-radius: 50%;
            background: var(--primary-light);
            box-shadow: 0 0 8px 2px rgba(52, 211, 153, 0.6);
            animation: pulseMove 2.4s linear infinite;
        }

        @keyframes pulseMove {
            0% {
                left: 42px;
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                left: calc(100% - 50px);
                opacity: 0;
            }
        }

        /* ============ FOOTER ============ */
        .site-footer {
            background: var(--night);
            color: rgba(255, 255, 255, 0.55);
            padding: 30px 0;
            text-align: center;
            font-size: 0.82rem;
        }

        @media (max-width: 980px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }

            .phone-stage {
                min-height: 420px;
                margin-top: 20px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .container {
                padding: 0 20px;
            }

            .hero h1 {
                font-size: 2.05rem;
                letter-spacing: -1px;
            }

            .store-buttons {
                flex-direction: column;
            }

            .btn-store {
                width: 100%;
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
                    <span class="brand-mark"><i class="fa-solid fa-book-open-reader"></i></span>
                    Hifzly
                </a>
                <a class="nav-cta" href="login.php">Sudah punya akun? Masuk <i class="fa-solid fa-arrow-right"></i></a>
            </nav>

            <div class="hero-grid">
                <div class="hero-copy">
                    <div class="hero-eyebrow"><i class="fa-solid fa-sparkles"></i> Segera hadir di ponselmu</div>
                    <h1>Bawa hafalanmu, <em>ke mana pun</em> melangkah.</h1>
                    <p class="lede">Aplikasi mobile Hifzly sedang di tahap penyempurnaan akhir. Murojaah, mutaba'ah, dan progres hafalanmu, kini bisa kamu genggam kapan saja.</p>

                    <div class="store-buttons">
                        <a href="#" class="btn-store primary" title="Gunakan versi Web/Desktop sekarang">
                            <span class="icon"><i class="fa-solid fa-desktop"></i></span>
                            <span class="txt"><small>Tersedia sekarang</small><span>Buka Versi Web</span></span>
                        </a>
                        <a href="#" class="btn-store ghost" onclick="event.preventDefault()">
                            <span class="soon-chip">Soon</span>
                            <span class="icon"><i class="fa-brands fa-google-play"></i></span>
                            <span class="txt"><small>Get it on</small><span>Google Play</span></span>
                        </a>
                        <a href="#" class="btn-store ghost" onclick="event.preventDefault()">
                            <span class="soon-chip">Soon</span>
                            <span class="icon"><i class="fa-brands fa-apple"></i></span>
                            <span class="txt"><small>Download on the</small><span>App Store</span></span>
                        </a>
                    </div>
                </div>

                <div class="phone-stage" id="phoneStage">
                    <div class="phone-aura"></div>
                    <!-- light motes -->
                    <span class="mote" style="left:38%; width:4px; height:4px; animation-duration:4.2s; animation-delay:0.2s; --drift:14px;"></span>
                    <span class="mote" style="left:58%; width:3px; height:3px; animation-duration:5.1s; animation-delay:1.4s; --drift:-18px;"></span>
                    <span class="mote" style="left:48%; width:5px; height:5px; animation-duration:4.7s; animation-delay:2.1s; --drift:10px;"></span>
                    <span class="mote" style="left:66%; width:3px; height:3px; animation-duration:3.9s; animation-delay:0.8s; --drift:-8px;"></span>
                    <span class="mote" style="left:30%; width:3px; height:3px; animation-duration:5.6s; animation-delay:2.8s; --drift:16px;"></span>

                    <div class="phone-float">
                        <div class="phone-tilt" id="phoneTilt">
                            <div class="phone-frame">
                                <div class="phone-notch"></div>
                                <div class="phone-screen">
                                    <div class="screen-status"><span>9:41</span><span><i class="fa-solid fa-signal"></i> <i class="fa-solid fa-wifi"></i> <i class="fa-solid fa-battery-three-quarters"></i></span></div>
                                    <div class="screen-greet">Assalamu'alaikum,<strong>Aisyah 👋</strong></div>

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
                                        <li class="ayat-row r3">
                                            <div class="ayat-check"><i class="fa-solid fa-check"></i></div>
                                            <div>
                                                <div class="name">QS. Al-Baqarah 11-15</div>
                                                <div class="sub">Target sore ini</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FEATURES ============ -->
    <section class="features">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Kenapa Hifzly</div>
                <h2>Dibangun untuk konsistensi hafalanmu</h2>
                <p>Empat kebiasaan kecil yang dijaga aplikasinya, supaya hafalanmu tumbuh setiap hari tanpa terasa berat.</p>
            </div>

            <div class="feature-grid">
                <!-- Feature 1 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper"><i class="fa-solid fa-list-check"></i></div>
                    <h3 class="feature-title">Mutaba'ah Cerdas</h3>
                    <p class="feature-desc">Catat setiap aktivitas tilawah, murojaah, dan hafalan barumu dengan cepat dan rapi, langsung tercentang otomatis.</p>
                    <div class="feature-visual">
                        <div class="v-checklist">
                            <div class="line line1">
                                <div class="box"><i class="fa-solid fa-check"></i></div>
                                <div class="bar"></div>
                            </div>
                            <div class="line line2">
                                <div class="box"><i class="fa-solid fa-check"></i></div>
                                <div class="bar"></div>
                            </div>
                            <div class="line line3">
                                <div class="box"><i class="fa-solid fa-check"></i></div>
                                <div class="bar"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper"><i class="fa-solid fa-fire"></i></div>
                    <h3 class="feature-title">Konsistensi &amp; Streak</h3>
                    <p class="feature-desc">Pertahankan api semangatmu setiap hari. Jangan biarkan streak-mu terputus, aplikasi akan mengingatkanmu.</p>
                    <div class="feature-visual">
                        <div class="v-streak">
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="flame"><i class="fa-solid fa-fire"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper"><i class="fa-solid fa-file-export"></i></div>
                    <h3 class="feature-title">Ekspor Laporan</h3>
                    <p class="feature-desc">Unduh rekap aktivitas bulananmu ke dalam format PDF atau Excel, siap dibagikan ke musyrif atau orang tua.</p>
                    <div class="feature-visual">
                        <div class="v-export">
                            <div class="doc"><i class="fa-solid fa-file-lines"></i></div>
                            <div class="pct">100%</div>
                            <div class="tray"></div>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card">
                    <div class="feature-icon-wrapper"><i class="fa-solid fa-arrows-rotate"></i></div>
                    <h3 class="feature-title">Multi-Platform</h3>
                    <p class="feature-desc">Sinkronisasi sempurna. Akses data hafalanmu dari Desktop, Android, maupun iOS, selalu terbarui otomatis.</p>
                    <div class="feature-visual">
                        <div class="v-sync">
                            <div class="node"><i class="fa-solid fa-desktop"></i></div>
                            <div class="track"></div>
                            <div class="pulse"></div>
                            <div class="node"><i class="fa-solid fa-mobile-screen"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FOOTER ============ -->
    <footer class="site-footer">
        <div class="container">&copy; <?= date('Y') ?> Hifzly. Pendamping Murojaah Al-Qur'an Berbasis AI.</div>
    </footer>

    <script>
        // Gentle cursor-follow tilt on the phone mockup (desktop only, disabled for reduced-motion users)
        const stage = document.getElementById('phoneStage');
        const tilt = document.getElementById('phoneTilt');
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (stage && tilt && !prefersReduced && window.matchMedia('(hover: hover)').matches) {
            stage.addEventListener('mousemove', (e) => {
                const rect = stage.getBoundingClientRect();
                const x = (e.clientX - rect.left) / rect.width - 0.5;
                const y = (e.clientY - rect.top) / rect.height - 0.5;
                tilt.style.transform = `rotateY(${x * 14}deg) rotateX(${-y * 14}deg)`;
            });
            stage.addEventListener('mouseleave', () => {
                tilt.style.transform = 'rotateY(0deg) rotateX(0deg)';
            });
        }
    </script>

</body>

</html>