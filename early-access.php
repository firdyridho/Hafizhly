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
            --gold-light: #e8c85f;
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
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 32px;
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

        .hero-eyebrow i {
            font-size: 0.65rem;
        }

        .hero h1 {
            color: var(--white);
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1.8px;
        }

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

        .mote {
            position: absolute;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 0 8px 2px rgba(255, 255, 255, 0.7);
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
                opacity: 0.4;
            }

            100% {
                transform: translateY(-240px) translateX(var(--drift, 20px));
                opacity: 0;
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
            background: linear-gradient(155deg, #33453d 0%, #16211c 45%, #0a1310 100%);
            border-radius: 48px;
            padding: 12px;
            box-shadow:
                0 34px 64px rgba(4, 30, 20, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.12) inset,
                0 1px 0 rgba(255, 255, 255, 0.18) inset;
        }

        .phone-frame::before {
            content: '';
            position: absolute;
            left: -3px;
            top: 116px;
            width: 3px;
            height: 30px;
            border-radius: 2px 0 0 2px;
            background: linear-gradient(180deg, #3d5148, #0c1712);
        }

        .phone-frame::after {
            content: '';
            position: absolute;
            right: -3px;
            top: 150px;
            width: 3px;
            height: 62px;
            border-radius: 0 2px 2px 0;
            background: linear-gradient(180deg, #3d5148, #0c1712);
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

        .phone-screen::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0) 26%);
            pointer-events: none;
            z-index: 5;
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
                background: var(--primary-dark);
                border-color: var(--primary-dark);
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

        /* FIXED FADE BLUR UNTUK MOBILE */
        /* Kita buang mask-image yang bikin blur hilang di HP, ganti dengan gradient transparan ke solid */
        .hero-bottom-fade {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 280px;
            background: linear-gradient(to bottom,
                    rgba(52, 211, 153, 0) 0%,
                    rgba(52, 211, 153, 0.6) 35%,
                    var(--hero-bottom) 90%);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            pointer-events: none;
            z-index: 6;
            /* Naikin z-index agar cover hp dengan baik */
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

        /* ============ QUICK HIGHLIGHTS STRIP ============ */
        .highlights {
            position: relative;
            z-index: 10;
            background: var(--paper);
            padding: 26px 0 0;
        }

        .highlight-chip {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 16px;
            height: 100%;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .highlight-chip:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
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

        /* ============ FEATURES (zig-zag) ============ */
        .features {
            padding: 70px 0 100px;
            background: var(--paper);
        }

        .section-head {
            text-align: center;
            max-width: 560px;
            margin: 0 auto 64px;
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

        .feature-row {
            display: flex;
            align-items: center;
            gap: 64px;
            margin-bottom: 96px;
            opacity: 0;
            transform: translateY(36px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }

        .feature-row.in-view {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-row:last-child {
            margin-bottom: 0;
        }

        .feature-row.reverse {
            flex-direction: row-reverse;
        }

        .feature-row-media {
            flex: 0 0 300px;
            display: flex;
            justify-content: center;
            position: relative;
        }

        .media-glow {
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.16), rgba(16, 185, 129, 0) 70%);
            filter: blur(2px);
            z-index: 0;
        }

        .feature-row:nth-child(even) .media-glow {
            background: radial-gradient(circle, rgba(201, 162, 39, 0.16), rgba(201, 162, 39, 0) 70%);
        }

        .row-mini-phone {
            position: relative;
            z-index: 1;
            width: 190px;
            height: 320px;
            background: linear-gradient(160deg, #1c2b26, #0c1712);
            border-radius: 32px;
            padding: 9px;
            box-shadow: 0 24px 44px rgba(4, 30, 20, 0.16);
            animation: floatY 5.5s ease-in-out infinite;
        }

        .row-mini-screen {
            width: 100%;
            height: 100%;
            background: #fff;
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
        }

        .row-mini-screen::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0) 24%);
            pointer-events: none;
            z-index: 5;
        }

        .row-chip {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 9px 13px;
            font-size: 0.72rem;
            font-weight: 700;
            color: var(--ink);
            box-shadow: 0 14px 26px rgba(15, 23, 42, 0.14);
            animation: chipFloat 4s ease-in-out infinite;
            z-index: 3;
        }

        .row-chip i {
            color: var(--primary-dark);
            font-size: 0.78rem;
        }

        @keyframes chipFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-9px);
            }
        }

        .feature-row-text {
            flex: 1;
        }

        .feature-index {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: var(--gold);
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .feature-row-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            background: rgba(16, 185, 129, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 1.1rem;
            margin-bottom: 18px;
        }

        .feature-row-text h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: 10px;
            letter-spacing: -0.4px;
        }

        .feature-row-text p {
            color: var(--muted);
            font-size: 0.97rem;
            line-height: 1.75;
            max-width: 400px;
        }

        .feature-row.reverse .feature-row-text p {
            margin-left: auto;
        }

        .feature-row.reverse .feature-index,
        .feature-row.reverse .feature-row-icon {
            margin-left: auto;
        }

        /* --- Simulation 1: checklist --- */
        .sim-checklist {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 8px;
        }

        .sim-checklist .row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sim-checklist .box {
            width: 15px;
            height: 15px;
            border-radius: 5px;
            border: 1.5px solid #cfe3d9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            color: #fff;
            flex-shrink: 0;
        }

        .sim-checklist .bar {
            height: 6px;
            border-radius: 4px;
            background: #e2edea;
            flex: 1;
        }

        .sim-checklist .row1 .box {
            animation: checkFill 4.5s ease-in-out infinite;
        }

        .sim-checklist .row2 .box {
            animation: checkFill 4.5s ease-in-out infinite 1.5s;
        }

        .sim-checklist .row3 .box {
            animation: checkFill 4.5s ease-in-out infinite 3s;
        }

        .sim-checklist .row1 .bar {
            width: 68%;
        }

        .sim-checklist .row2 .bar {
            width: 50%;
        }

        .sim-checklist .row3 .bar {
            width: 60%;
        }

        /* --- Simulation 2: streak --- */
        .sim-streak {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sim-streak .flame-big {
            font-size: 2rem;
            color: #f59e0b;
            animation: flameFlicker 1.4s ease-in-out infinite;
        }

        .sim-streak .count {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--ink);
        }

        .sim-streak .row {
            display: flex;
            gap: 5px;
        }

        .sim-streak .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e2edea;
        }

        .sim-streak .dot:nth-child(1) {
            animation: dotLight 5.6s ease-in-out infinite 0s;
        }

        .sim-streak .dot:nth-child(2) {
            animation: dotLight 5.6s ease-in-out infinite 0.35s;
        }

        .sim-streak .dot:nth-child(3) {
            animation: dotLight 5.6s ease-in-out infinite 0.7s;
        }

        .sim-streak .dot:nth-child(4) {
            animation: dotLight 5.6s ease-in-out infinite 1.05s;
        }

        .sim-streak .dot:nth-child(5) {
            animation: dotLight 5.6s ease-in-out infinite 1.4s;
        }

        @keyframes dotLight {

            0%,
            8% {
                background: #e2edea;
            }

            16%,
            92% {
                background: var(--primary-dark);
            }

            100% {
                background: #e2edea;
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

        /* --- Simulation 3: export --- */
        .sim-export {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }

        .sim-export .file-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--primary-dark);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            animation: pulseScale 3.6s ease-in-out infinite;
        }

        @keyframes pulseScale {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .sim-export .track {
            width: 80%;
            height: 6px;
            border-radius: 4px;
            background: #e2edea;
            overflow: hidden;
        }

        .sim-export .track .fill {
            height: 100%;
            width: 0%;
            background: var(--primary-dark);
            border-radius: 4px;
            animation: fillBar 3.6s ease-in-out infinite;
        }

        @keyframes fillBar {
            0% {
                width: 0%;
            }

            60%,
            100% {
                width: 100%;
            }
        }

        /* --- Simulation 4: sync --- */
        .sim-sync {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 26px;
            position: relative;
        }

        .sim-sync .node {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: #eef7f2;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
        }

        .sim-sync .track2 {
            position: absolute;
            left: 44px;
            right: 44px;
            top: 50%;
            height: 2px;
            background: repeating-linear-gradient(90deg, #cfe3d9 0 6px, transparent 6px 12px);
        }

        .sim-sync .pulse2 {
            position: absolute;
            top: 50%;
            left: 44px;
            width: 7px;
            height: 7px;
            margin-top: -3.5px;
            border-radius: 50%;
            background: var(--primary-light);
            box-shadow: 0 0 7px 2px rgba(110, 231, 183, 0.7);
            animation: pulseMove2 2.2s linear infinite;
        }

        @keyframes pulseMove2 {
            0% {
                left: 44px;
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                left: calc(100% - 52px);
                opacity: 0;
            }
        }

        /* ============ CLOSING CTA ============ */
        .closing-cta {
            position: relative;
            background: linear-gradient(135deg, var(--hero-top), var(--hero-bottom));
            padding: 64px 0;
            text-align: center;
            overflow: hidden;
        }

        .closing-cta::before {
            content: '\0627\0642\0631\0623';
            position: absolute;
            font-family: 'Amiri', serif;
            font-size: 9rem;
            color: rgba(255, 255, 255, 0.06);
            bottom: -30px;
            left: -10px;
            line-height: 1;
            pointer-events: none;
        }

        .closing-cta h2 {
            position: relative;
            color: #fff;
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -0.6px;
            margin-bottom: 12px;
        }

        .closing-cta p {
            position: relative;
            color: rgba(255, 255, 255, 0.88);
            max-width: 460px;
            margin: 0 auto 26px;
            font-size: 0.96rem;
            line-height: 1.6;
        }

        .closing-cta .btn-store {
            position: relative;
            display: inline-flex;
        }

        /* ============ FOOTER ============ */
        .site-footer {
            background: var(--primary-dark);
            color: rgba(255, 255, 255, 0.75);
            padding: 30px 0;
            text-align: center;
            font-size: 0.82rem;
        }

        /* ============ RESPONSIVE MEDIA QUERIES ============ */

        /* Tablet & Kecil */
        @media (max-width: 980px) {
            .hero h1 {
                font-size: 2.8rem;
            }

            .feature-row,
            .feature-row.reverse {
                flex-direction: column;
                gap: 40px;
            }

            .feature-row-text p,
            .feature-row.reverse .feature-row-text p {
                margin-left: 0;
            }

            .feature-row-text {
                text-align: center;
            }

            .feature-row-icon,
            .feature-index {
                margin-left: auto;
                margin-right: auto;
            }
        }

        /* HP (Mobile) */
        @media (max-width: 768px) {
            .hero-arabic-mark {
                font-size: 8rem;
                top: 0;
            }

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .hero h1 {
                font-size: 2.2rem;
                letter-spacing: -1px;
            }

            .hero-copy {
                margin-top: 25px;
            }

            .phone-stage {
                min-height: 380px;
                /* Kurangi space agar hp tidak terlalu jauh ke bawah */
                margin-top: 20px;
            }

            /* Sesuaikan ukuran mockup HP biar tidak terlalu besar/meluber */
            .phone-frame {
                width: 220px;
                height: 460px;
                padding: 10px;
            }

            .phone-notch {
                width: 70px;
                height: 20px;
            }

            .screen-status {
                margin-bottom: 10px;
            }

            .juz-ring-wrap {
                padding: 6px;
            }

            .hero-sub {
                margin-top: 10px;
            }

            .store-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-store {
                width: 100%;
                justify-content: center;
            }

            /* Perkecil bayangan HP mini di list fitur */
            .row-mini-phone {
                width: 160px;
                height: 270px;
            }
        }

        /* Ekstra Kecil (Misal HP Layar Sempit) */
        @media (max-width: 480px) {
            .container {
                padding: 0 20px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero-bottom-fade {
                height: 240px;
                /* Disesuaikan dengan tinggi sisa hp */
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
                    <span class="brand-mark"><img src="assets/icon/logo.png" alt="Logo Hifzhly"></span>
                    Hifzhly
                </a>
                <a class="nav-cta" href="login.php">Sudah punya akun? Masuk <i class="fa-solid fa-arrow-right"></i></a>
            </nav>

            <div class="hero-copy">
                <div class="hero-eyebrow"><i class="fa-solid fa-sparkles"></i> Segera hadir di ponselmu</div>
                <h1>Genggam hafalanmu, ke mana pun melangkah.</h1>
            </div>

            <!-- Phone mockup -->
            <div class="phone-stage" id="phoneStage">
                <div class="phone-aura"></div>
                <span class="mote" style="left:38%; width:4px; height:4px; animation-duration:4.2s; animation-delay:0.2s; --drift:14px;"></span>
                <span class="mote" style="left:58%; width:3px; height:3px; animation-duration:5.1s; animation-delay:1.4s; --drift:-18px;"></span>
                <span class="mote" style="left:48%; width:5px; height:5px; animation-duration:4.7s; animation-delay:2.1s; --drift:10px;"></span>
                <span class="mote" style="left:66%; width:3px; height:3px; animation-duration:3.9s; animation-delay:0.8s; --drift:-8px;"></span>

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

            <!-- Text under the phone -->
            <div class="hero-sub">
                <p class="lede">Aplikasi mobile Hifzhly sedang di tahap penyempurnaan akhir. Murojaah, mutaba'ah, dan progres hafalanmu, kini bisa kamu bawa ke mana saja.</p>
            </div>

            <!-- Download row under the text -->
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

        <!-- Full-bleed fade: now using safe robust gradients for Mobile -->
        <div class="hero-bottom-fade"></div>
    </section>

    <!-- ============ QUICK HIGHLIGHTS ============ -->
    <section class="highlights">
        <div class="container">
            <!-- Diubah ke sistem grid Bootstrap standar agar tidak menumpuk aneh di HP -->
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="highlight-chip">
                        <span class="hi-icon"><i class="fa-solid fa-cloud"></i></span>
                        <span><strong>Auto-sync</strong><span>Realtime ke semua device</span></span>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="highlight-chip">
                        <span class="hi-icon"><i class="fa-solid fa-fire"></i></span>
                        <span><strong>Streak harian</strong><span>Pengingat tepat waktu</span></span>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="highlight-chip">
                        <span class="hi-icon"><i class="fa-solid fa-file-export"></i></span>
                        <span><strong>Laporan siap pakai</strong><span>Ekspor PDF & Excel</span></span>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="highlight-chip">
                        <span class="hi-icon"><i class="fa-solid fa-shield-heart"></i></span>
                        <span><strong>Ramah musyrif</strong><span>Pantau progres santri</span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FEATURES (zig-zag) ============ -->
    <section class="features">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Kenapa Hifzhly</div>
                <h2>Dibangun untuk konsistensi hafalanmu</h2>
                <p>Empat kebiasaan kecil yang dijaga aplikasinya — lihat langsung simulasi tampilannya di sebelah penjelasan tiap fitur.</p>
            </div>

            <!-- Row 1 -->
            <div class="feature-row">
                <div class="feature-row-media">
                    <div class="media-glow"></div>
                    <div class="row-chip" style="top:6px; left:-10px;"><i class="fa-solid fa-check"></i> Tersimpan otomatis</div>
                    <div class="row-mini-phone">
                        <div class="row-mini-screen">
                            <div class="sim-checklist">
                                <div class="row row1">
                                    <div class="box"><i class="fa-solid fa-check"></i></div>
                                    <div class="bar"></div>
                                </div>
                                <div class="row row2">
                                    <div class="box"><i class="fa-solid fa-check"></i></div>
                                    <div class="bar"></div>
                                </div>
                                <div class="row row3">
                                    <div class="box"><i class="fa-solid fa-check"></i></div>
                                    <div class="bar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="feature-row-text">
                    <span class="feature-index">Fitur 01</span>
                    <div class="feature-row-icon"><i class="fa-solid fa-list-check"></i></div>
                    <h3>Mutaba'ah Cerdas</h3>
                    <p>Catat setiap aktivitas tilawah, murojaah, dan hafalan barumu dengan cepat dan rapi, langsung tercentang otomatis begitu selesai.</p>
                </div>
            </div>

            <!-- Row 2 (reversed) -->
            <div class="feature-row reverse">
                <div class="feature-row-media">
                    <div class="media-glow"></div>
                    <div class="row-chip" style="top:2px; right:-10px;"><i class="fa-solid fa-fire"></i> 7 hari beruntun</div>
                    <div class="row-mini-phone">
                        <div class="row-mini-screen">
                            <div class="sim-streak">
                                <div class="flame-big"><i class="fa-solid fa-fire"></i></div>
                                <div class="count">7</div>
                                <div class="row">
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                    <div class="dot"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="feature-row-text">
                    <span class="feature-index">Fitur 02</span>
                    <div class="feature-row-icon"><i class="fa-solid fa-fire"></i></div>
                    <h3>Konsistensi &amp; Streak</h3>
                    <p>Pertahankan api semangatmu setiap hari. Jangan biarkan streak-mu terputus, aplikasi akan mengingatkanmu tepat waktu.</p>
                </div>
            </div>

            <!-- Row 3 -->
            <div class="feature-row">
                <div class="feature-row-media">
                    <div class="media-glow"></div>
                    <div class="row-chip" style="top:8px; left:-14px;"><i class="fa-solid fa-file-arrow-down"></i> Laporan_Juli.pdf</div>
                    <div class="row-mini-phone">
                        <div class="row-mini-screen">
                            <div class="sim-export">
                                <div class="file-icon"><i class="fa-solid fa-file-lines"></i></div>
                                <div class="track">
                                    <div class="fill"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="feature-row-text">
                    <span class="feature-index">Fitur 03</span>
                    <div class="feature-row-icon"><i class="fa-solid fa-file-export"></i></div>
                    <h3>Ekspor Laporan</h3>
                    <p>Unduh rekap aktivitas bulananmu ke dalam format PDF atau Excel, siap dibagikan ke musyrif atau orang tua.</p>
                </div>
            </div>

            <!-- Row 4 (reversed) -->
            <div class="feature-row reverse">
                <div class="feature-row-media">
                    <div class="media-glow"></div>
                    <div class="row-chip" style="top:4px; right:-10px;"><i class="fa-solid fa-check"></i> Tersinkron</div>
                    <div class="row-mini-phone">
                        <div class="row-mini-screen">
                            <div class="sim-sync">
                                <div class="node"><i class="fa-solid fa-desktop"></i></div>
                                <div class="track2"></div>
                                <div class="pulse2"></div>
                                <div class="node"><i class="fa-solid fa-mobile-screen"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="feature-row-text">
                    <span class="feature-index">Fitur 04</span>
                    <div class="feature-row-icon"><i class="fa-solid fa-arrows-rotate"></i></div>
                    <h3>Multi-Platform</h3>
                    <p>Sinkronisasi sempurna. Akses data hafalanmu dari Desktop, Android, maupun iOS, selalu terbarui otomatis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ CLOSING CTA ============ -->
    <section class="closing-cta">
        <div class="container">
            <h2>Siap jaga hafalanmu setiap hari?</h2>
            <p>Coba versi web-nya sekarang, gratis, sambil menunggu aplikasi mobile-nya rilis di Google Play dan App Store.</p>
            <a href="#" class="btn-store primary">
                <span class="icon"><i class="fa-solid fa-desktop"></i></span>
                <span class="txt"><small>Mulai sekarang</small><span>Buka Versi Web</span></span>
            </a>
        </div>
    </section>

    <!-- ============ FOOTER ============ -->
    <footer class="site-footer">
        <div class="container">&copy; <?= date('Y') ?> Hifzhly. Pendamping Murojaah Al-Qur'an Berbasis AI.</div>
    </footer>

    <script>
        // Gentle cursor-follow tilt on the hero phone mockup
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

        // Reveal each zig-zag feature row as it scrolls into view
        const rows = document.querySelectorAll('.feature-row');
        if ('IntersectionObserver' in window && !prefersReduced) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in-view');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.25
            });
            rows.forEach((row) => observer.observe(row));
        } else {
            rows.forEach((row) => row.classList.add('in-view'));
        }
    </script>

</body>

</html>