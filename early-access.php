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

        /* ============ PAGE LOADER ============ */
        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: linear-gradient(160deg, var(--hero-top), var(--hero-bottom));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 22px;
            transition: opacity 0.7s ease, visibility 0.7s ease;
        }

        .page-loader.hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .loader-mark {
            font-family: 'Amiri', serif;
            font-size: 3.2rem;
            color: #fff;
            opacity: 0.92;
            animation: loaderPulse 1.6s ease-in-out infinite;
        }

        @keyframes loaderPulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.75;
            }

            50% {
                transform: scale(1.08);
                opacity: 1;
            }
        }

        .loader-bar {
            width: 160px;
            height: 3px;
            border-radius: 3px;
            background: rgba(255, 255, 255, 0.25);
            overflow: hidden;
        }

        .loader-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--gold-light), var(--gold));
            border-radius: 3px;
            transition: width 0.9s cubic-bezier(.65, 0, .35, 1);
        }

        /* Staggered hero entrance */
        .hero-copy,
        .phone-stage,
        .hero-sub,
        .store-buttons {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.9s cubic-bezier(.16, 1, .3, 1), transform 0.9s cubic-bezier(.16, 1, .3, 1);
        }

        body.loaded .hero-copy {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.05s;
        }

        body.loaded .phone-stage {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.2s;
        }

        body.loaded .hero-sub {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.35s;
        }

        body.loaded .store-buttons {
            opacity: 1;
            transform: translateY(0);
            transition-delay: 0.48s;
        }

        body.reveal-done .hero-copy,
        body.reveal-done .phone-stage {
            transition: none;
        }

        @media (prefers-reduced-motion: reduce) {

            .hero-copy,
            .phone-stage,
            .hero-sub,
            .store-buttons {
                opacity: 1;
                transform: none;
                transition: none;
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

        @media (prefers-reduced-motion: reduce) {
            .reveal-up {
                opacity: 1;
                transform: none;
            }
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

        /* ============ HERO BOTTOM FADE — progressive layered blur ============ */
        .hero-bottom-fade {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 300px;
            pointer-events: none;
            z-index: 6;
        }

        .hero-bottom-fade .blur-layer {
            position: absolute;
            inset: 0;
        }

        .hero-bottom-fade .l1 {
            backdrop-filter: blur(2px);
            -webkit-backdrop-filter: blur(2px);
            -webkit-mask-image: linear-gradient(to bottom, transparent 0%, #000 35%, #000 100%);
            mask-image: linear-gradient(to bottom, transparent 0%, #000 35%, #000 100%);
        }

        .hero-bottom-fade .l2 {
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            -webkit-mask-image: linear-gradient(to bottom, transparent 20%, #000 48%, #000 100%);
            mask-image: linear-gradient(to bottom, transparent 20%, #000 48%, #000 100%);
        }

        .hero-bottom-fade .l3 {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            -webkit-mask-image: linear-gradient(to bottom, transparent 38%, #000 60%, #000 100%);
            mask-image: linear-gradient(to bottom, transparent 38%, #000 60%, #000 100%);
        }

        .hero-bottom-fade .l4 {
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            -webkit-mask-image: linear-gradient(to bottom, transparent 55%, #000 74%, #000 100%);
            mask-image: linear-gradient(to bottom, transparent 55%, #000 74%, #000 100%);
        }

        .hero-bottom-fade .l5 {
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            -webkit-mask-image: linear-gradient(to bottom, transparent 72%, #000 88%, #000 100%);
            mask-image: linear-gradient(to bottom, transparent 72%, #000 88%, #000 100%);
        }

        .hero-bottom-fade .fade-color {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom,
                    rgba(52, 211, 153, 0) 0%,
                    rgba(46, 197, 140, 0.45) 45%,
                    var(--hero-bottom) 92%);
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

        .highlights .row>div:nth-child(1) {
            transition-delay: 0.05s;
        }

        .highlights .row>div:nth-child(2) {
            transition-delay: 0.15s;
        }

        .highlights .row>div:nth-child(3) {
            transition-delay: 0.25s;
        }

        .highlights .row>div:nth-child(4) {
            transition-delay: 0.35s;
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

        /* ============ FEATURES — 3D pinned zigzag scroll ============ */
        .features {
            padding: 70px 0 40px;
            background: var(--paper);
            position: relative;
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
            height: 420vh;
        }

        .features-canvas-sticky {
            position: sticky;
            top: 0;
            height: 100vh;
            height: 100dvh;
            width: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        #phoneCanvas {
            width: 100%;
            height: 100%;
            display: block;
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
            background: rgba(255, 255, 255, 0.68);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            padding: 30px 28px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.12);
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
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.1);
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
            color: var(--gold);
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

        /* No-3D / fallback path */
        .features-3d-wrap.no-3d {
            height: auto;
        }

        .features-3d-wrap.no-3d .features-canvas-sticky {
            position: relative;
            height: auto;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 0;
        }

        .features-3d-wrap.no-3d #phoneCanvas {
            display: none;
        }

        .features-3d-wrap.no-3d .features-canvas-sticky::after {
            content: '\f10b';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 4rem;
            color: var(--primary-dark);
            opacity: 0.5;
        }

        .features-3d-wrap.no-3d .features-text-track {
            position: relative;
        }

        .features-3d-wrap.no-3d .feature-slide {
            height: auto;
            padding: 16px 6% 46px;
            justify-content: center !important;
        }

        .features-3d-wrap.no-3d .slide-card {
            opacity: 1;
            transform: none;
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
                margin-top: 20px;
            }

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

            .features-3d-wrap {
                height: 360vh;
            }

            .feature-slide {
                justify-content: center !important;
                padding: 0 8%;
            }

            .slide-card {
                max-width: 88vw;
                padding: 24px 22px;
            }

            .loader-mark {
                font-size: 2.4rem;
            }
        }

        /* Ekstra Kecil (Misal HP Layar Sempit) */
        @media (max-width: 480px) {
            .container {
                padding: 0 20px;
            }

            .hero h1 {
                font-size: clamp(1.7rem, 8vw, 2rem);
            }

            .hero-bottom-fade {
                height: 240px;
            }
        }
    </style>
</head>

<body>

    <!-- ============ PAGE LOADER ============ -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-mark">اقرأ</div>
        <div class="loader-bar">
            <div class="loader-fill" id="loaderFill"></div>
        </div>
    </div>

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

        <!-- Progressive layered blur fade -->
        <div class="hero-bottom-fade">
            <div class="blur-layer l1"></div>
            <div class="blur-layer l2"></div>
            <div class="blur-layer l3"></div>
            <div class="blur-layer l4"></div>
            <div class="blur-layer l5"></div>
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
        <div class="container">
            <div class="section-head reveal-up">
                <div class="section-eyebrow">Kenapa Hifzhly</div>
                <h2>Dibangun untuk konsistensi hafalanmu</h2>
                <p>Empat kebiasaan kecil yang dijaga aplikasinya — scroll untuk melihat langsung simulasinya lewat ponsel 3D di sebelah penjelasan tiap fitur.</p>
            </div>
        </div>

        <div class="features-3d-wrap" id="features3DWrap">
            <div class="features-canvas-sticky">
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

    <!-- Three.js + Lenis -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.14/dist/lenis.min.js"></script>

    <script>
        (function() {
            var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var isMobile = window.matchMedia('(max-width: 768px)').matches;

            /* ---------- THEME COLORS ---------- */
            var rootStyles = getComputedStyle(document.documentElement);
            var COLOR = {
                primary: (rootStyles.getPropertyValue('--primary') || '#10b981').trim(),
                primaryDark: (rootStyles.getPropertyValue('--primary-dark') || '#059669').trim(),
                gold: (rootStyles.getPropertyValue('--gold') || '#c9a227').trim(),
                goldLight: (rootStyles.getPropertyValue('--gold-light') || '#e8c85f').trim(),
                ink: (rootStyles.getPropertyValue('--ink') || '#0f172a').trim(),
                muted: (rootStyles.getPropertyValue('--muted') || '#64748b').trim(),
                border: (rootStyles.getPropertyValue('--border') || '#e6ebe8').trim()
            };

            /* ---------- PAGE LOADER ---------- */
            function initLoader() {
                var loader = document.getElementById('pageLoader');
                var fill = document.getElementById('loaderFill');
                if (!loader) return;

                function done() {
                    loader.classList.add('hide');
                    document.body.classList.add('loaded');
                    setTimeout(function() {
                        document.body.classList.add('reveal-done');
                    }, 1450);
                    setTimeout(function() {
                        if (loader.parentNode) loader.parentNode.removeChild(loader);
                    }, 800);
                }

                if (prefersReduced) {
                    done();
                    return;
                }
                requestAnimationFrame(function() {
                    if (fill) fill.style.width = '100%';
                });
                setTimeout(done, 900);
            }

            /* ---------- LENIS SMOOTH SCROLL ---------- */
            function initLenis() {
                if (prefersReduced || typeof Lenis === 'undefined') return null;
                var lenis = new Lenis({
                    duration: 1.15,
                    easing: function(t) {
                        return Math.min(1, 1.001 - Math.pow(2, -10 * t));
                    },
                    smoothWheel: true,
                    touchMultiplier: 1.1
                });

                function raf(time) {
                    lenis.raf(time);
                    requestAnimationFrame(raf);
                }
                requestAnimationFrame(raf);
                return lenis;
            }

            /* ---------- HERO PHONE TILT ---------- */
            function initHeroTilt() {
                var stage = document.getElementById('phoneStage');
                var tilt = document.getElementById('phoneTilt');
                if (stage && tilt && !prefersReduced && window.matchMedia('(hover: hover)').matches) {
                    stage.addEventListener('mousemove', function(e) {
                        var rect = stage.getBoundingClientRect();
                        var x = (e.clientX - rect.left) / rect.width - 0.5;
                        var y = (e.clientY - rect.top) / rect.height - 0.5;
                        tilt.style.transform = 'rotateY(' + (x * 14) + 'deg) rotateX(' + (-y * 14) + 'deg)';
                    });
                    stage.addEventListener('mouseleave', function() {
                        tilt.style.transform = 'rotateY(0deg) rotateX(0deg)';
                    });
                }
            }

            /* ---------- HERO PARALLAX ---------- */
            function initHeroParallax() {
                if (prefersReduced) return;
                var hero = document.querySelector('.hero');
                var copy = document.querySelector('.hero-copy');
                var stage = document.getElementById('phoneStage');
                if (!hero || !copy || !stage) return;
                var ticking = false;

                function update() {
                    var h = hero.offsetHeight;
                    var p = Math.min(Math.max(window.scrollY / h, 0), 1);
                    copy.style.transform = 'translateY(' + (p * -30) + 'px)';
                    copy.style.opacity = String(1 - p * 0.9);
                    stage.style.transform = 'translateY(' + (p * 40) + 'px)';
                    ticking = false;
                }
                window.addEventListener('scroll', function() {
                    if (!ticking) {
                        requestAnimationFrame(update);
                        ticking = true;
                    }
                }, {
                    passive: true
                });
            }

            /* ---------- GENERIC SCROLL REVEAL ---------- */
            function initReveal() {
                var els = document.querySelectorAll('.reveal-up');
                if ('IntersectionObserver' in window && !prefersReduced) {
                    var io = new IntersectionObserver(function(entries) {
                        entries.forEach(function(en) {
                            if (en.isIntersecting) {
                                en.target.classList.add('in-view');
                                io.unobserve(en.target);
                            }
                        });
                    }, {
                        threshold: 0.2
                    });
                    els.forEach(function(el) {
                        io.observe(el);
                    });
                } else {
                    els.forEach(function(el) {
                        el.classList.add('in-view');
                    });
                }
            }

            /* ---------- FEATURE SLIDE TEXT REVEAL ---------- */
            function initSlideReveal() {
                var slides = document.querySelectorAll('.feature-slide');
                if (!slides.length) return;
                if ('IntersectionObserver' in window) {
                    var io = new IntersectionObserver(function(entries) {
                        entries.forEach(function(en) {
                            en.target.classList.toggle('in-view', en.isIntersecting);
                        });
                    }, {
                        threshold: 0.45
                    });
                    slides.forEach(function(s) {
                        io.observe(s);
                    });
                } else {
                    slides.forEach(function(s) {
                        s.classList.add('in-view');
                    });
                }
            }

            /* ---------- 3D PHONE (THREE.JS) ---------- */
            var FEATURE_COUNT = 4;
            var X_POS = [-1.15, 1.15, -1.15, 1.15];

            function hexToInt(hex) {
                if (!hex) return 0xffffff;
                hex = hex.trim().replace('#', '');
                if (hex.length === 3) hex = hex.split('').map(function(c) {
                    return c + c;
                }).join('');
                return parseInt(hex, 16);
            }

            function roundedRectShape(w, h, r) {
                var shape = new THREE.Shape();
                var x = -w / 2,
                    y = -h / 2;
                shape.moveTo(x, y + r);
                shape.lineTo(x, y + h - r);
                shape.quadraticCurveTo(x, y + h, x + r, y + h);
                shape.lineTo(x + w - r, y + h);
                shape.quadraticCurveTo(x + w, y + h, x + w, y + h - r);
                shape.lineTo(x + w, y + r);
                shape.quadraticCurveTo(x + w, y, x + w - r, y);
                shape.lineTo(x + r, y);
                shape.quadraticCurveTo(x, y, x, y + r);
                return shape;
            }

            function roundRectPath(ctx, x, y, w, h, r) {
                ctx.beginPath();
                ctx.moveTo(x + r, y);
                ctx.arcTo(x + w, y, x + w, y + h, r);
                ctx.arcTo(x + w, y + h, x, y + h, r);
                ctx.arcTo(x, y + h, x, y, r);
                ctx.arcTo(x, y, x + w, y, r);
                ctx.closePath();
            }

            function drawFeatureScreen(ctx, w, h, index, t) {
                var pad = 34;
                ctx.textAlign = 'left';
                ctx.font = '700 15px "Plus Jakarta Sans", sans-serif';
                ctx.fillStyle = COLOR.ink;

                if (index === 0) {
                    ctx.fillText("Mutaba'ah Hari Ini", pad, 78);
                    for (var i = 0; i < 3; i++) {
                        var y = 118 + i * 58;
                        var cyc = (t * 0.35 + i * 0.33) % 1;
                        var active = cyc > 0.28 && cyc < 0.85;
                        ctx.lineWidth = 2;
                        ctx.strokeStyle = active ? COLOR.primaryDark : COLOR.border;
                        ctx.fillStyle = active ? COLOR.primaryDark : 'transparent';
                        roundRectPath(ctx, pad, y, 26, 26, 7);
                        ctx.fill();
                        ctx.stroke();
                        if (active) {
                            ctx.strokeStyle = '#ffffff';
                            ctx.lineWidth = 2.4;
                            ctx.beginPath();
                            ctx.moveTo(pad + 6, y + 13);
                            ctx.lineTo(pad + 11, y + 19);
                            ctx.lineTo(pad + 20, y + 7);
                            ctx.stroke();
                        }
                        ctx.fillStyle = '#e2edea';
                        roundRectPath(ctx, pad + 40, y + 4, (w - pad * 2 - 40) * (0.5 + 0.12 * Math.sin(i + 1)), 8, 4);
                        ctx.fill();
                        ctx.fillStyle = '#cfe3d9';
                        roundRectPath(ctx, pad + 40, y + 16, (w - pad * 2 - 40) * 0.32, 6, 3);
                        ctx.fill();
                    }
                } else if (index === 1) {
                    ctx.textAlign = 'center';
                    var pulse = 1 + Math.sin(t * 3) * 0.06;
                    ctx.font = Math.round(64 * pulse) + 'px sans-serif';
                    ctx.fillStyle = '#f59e0b';
                    ctx.fillText('\uD83D\uDD25', w / 2, h * 0.42);
                    ctx.font = '800 46px "Plus Jakarta Sans", sans-serif';
                    ctx.fillStyle = COLOR.ink;
                    ctx.fillText('7', w / 2, h * 0.42 + 64);
                    ctx.font = '600 14px Inter, sans-serif';
                    ctx.fillStyle = COLOR.muted;
                    ctx.fillText('hari beruntun', w / 2, h * 0.42 + 92);
                    var dots = 5,
                        dotY = h * 0.42 + 140,
                        startX = w / 2 - ((dots - 1) * 22) / 2;
                    for (var d = 0; d < dots; d++) {
                        var dc = (t * 0.6 + d * 0.16) % 1;
                        ctx.fillStyle = (dc > 0.15 && dc < 0.9) ? COLOR.primaryDark : '#e2edea';
                        ctx.beginPath();
                        ctx.arc(startX + d * 22, dotY, 7, 0, Math.PI * 2);
                        ctx.fill();
                    }
                    ctx.textAlign = 'left';
                } else if (index === 2) {
                    ctx.textAlign = 'center';
                    ctx.fillStyle = COLOR.primaryDark;
                    roundRectPath(ctx, w / 2 - 30, h * 0.36, 60, 60, 15);
                    ctx.fill();
                    ctx.fillStyle = '#ffffff';
                    ctx.font = '700 13px Inter, sans-serif';
                    ctx.fillText('PDF', w / 2, h * 0.36 + 37);
                    ctx.font = '700 15px "Plus Jakarta Sans", sans-serif';
                    ctx.fillStyle = COLOR.ink;
                    ctx.fillText('Laporan_Juli.pdf', w / 2, h * 0.36 + 96);
                    var trackW = w * 0.62,
                        trackX = w / 2 - trackW / 2,
                        trackY = h * 0.36 + 118;
                    ctx.fillStyle = '#e2edea';
                    roundRectPath(ctx, trackX, trackY, trackW, 8, 4);
                    ctx.fill();
                    var prog = (Math.sin(t * 1.1) * 0.5 + 0.5);
                    ctx.fillStyle = COLOR.primaryDark;
                    roundRectPath(ctx, trackX, trackY, trackW * prog, 8, 4);
                    ctx.fill();
                    ctx.textAlign = 'left';
                } else {
                    ctx.textAlign = 'center';
                    var cy = h * 0.46;
                    ctx.fillStyle = '#eef7f2';
                    roundRectPath(ctx, w * 0.22 - 24, cy - 24, 48, 48, 13);
                    ctx.fill();
                    roundRectPath(ctx, w * 0.78 - 24, cy - 24, 48, 48, 13);
                    ctx.fill();
                    ctx.fillStyle = COLOR.primaryDark;
                    ctx.font = '700 20px Inter, sans-serif';
                    ctx.fillText('\u{1F5A5}', w * 0.22, cy + 7);
                    ctx.fillText('\u{1F4F1}', w * 0.78, cy + 7);
                    ctx.strokeStyle = '#cfe3d9';
                    ctx.lineWidth = 2;
                    ctx.setLineDash([5, 6]);
                    ctx.beginPath();
                    ctx.moveTo(w * 0.22 + 28, cy);
                    ctx.lineTo(w * 0.78 - 28, cy);
                    ctx.stroke();
                    ctx.setLineDash([]);
                    var travel = (t * 0.5) % 1;
                    var px = (w * 0.22 + 28) + (w * 0.78 - 28 - (w * 0.22 + 28)) * travel;
                    ctx.fillStyle = COLOR.primary;
                    ctx.beginPath();
                    ctx.arc(px, cy, 5, 0, Math.PI * 2);
                    ctx.fill();
                    ctx.font = '600 14px Inter, sans-serif';
                    ctx.fillStyle = COLOR.muted;
                    ctx.fillText('Tersinkron otomatis', w / 2, cy + 70);
                    ctx.textAlign = 'left';
                }
            }

            function drawScreen(ctx, w, h, index, blendIndex, blendAmt, t) {
                ctx.clearRect(0, 0, w, h);
                var grad = ctx.createLinearGradient(0, 0, 0, h);
                grad.addColorStop(0, '#ffffff');
                grad.addColorStop(1, '#f4faf7');
                ctx.fillStyle = grad;
                roundRectPath(ctx, 0, 0, w, h, 46);
                ctx.fill();

                ctx.save();
                ctx.globalAlpha = 1 - blendAmt;
                drawFeatureScreen(ctx, w, h, index, t);
                ctx.restore();

                if (blendAmt > 0.001 && blendIndex !== index) {
                    ctx.save();
                    ctx.globalAlpha = blendAmt;
                    drawFeatureScreen(ctx, w, h, blendIndex, t);
                    ctx.restore();
                }

                ctx.fillStyle = '#060a08';
                roundRectPath(ctx, w / 2 - 46, 14, 92, 24, 12);
                ctx.fill();
            }

            function initPhone3D() {
                var wrap = document.getElementById('features3DWrap');
                var stickyEl = document.querySelector('.features-canvas-sticky');
                var canvas = document.getElementById('phoneCanvas');
                if (!wrap || !stickyEl || !canvas) return;
                if (typeof THREE === 'undefined') {
                    wrap.classList.add('no-3d');
                    return;
                }

                var renderer, scene, camera, phoneGroup;
                var screenCanvas, screenCtx, screenTexture;
                var running = false,
                    rafId = null;
                var startTime = performance.now();

                try {
                    renderer = new THREE.WebGLRenderer({
                        canvas: canvas,
                        alpha: true,
                        antialias: true
                    });
                } catch (e) {
                    wrap.classList.add('no-3d');
                    return;
                }

                scene = new THREE.Scene();
                camera = new THREE.PerspectiveCamera(32, 1, 0.1, 100);
                camera.position.set(0, 0, 6.4);

                scene.add(new THREE.AmbientLight(0xffffff, 0.65));
                var key = new THREE.DirectionalLight(0xffffff, 1.15);
                key.position.set(3, 4, 5);
                scene.add(key);
                var rim = new THREE.DirectionalLight(hexToInt(COLOR.primary), 0.9);
                rim.position.set(-4, -1, -3);
                scene.add(rim);
                var goldLight = new THREE.PointLight(hexToInt(COLOR.gold), 0.7, 12);
                goldLight.position.set(-2.5, 2, 3.5);
                scene.add(goldLight);

                screenCanvas = document.createElement('canvas');
                screenCanvas.width = 420;
                screenCanvas.height = 860;
                screenCtx = screenCanvas.getContext('2d');
                screenTexture = new THREE.CanvasTexture(screenCanvas);
                screenTexture.minFilter = THREE.LinearFilter;

                phoneGroup = new THREE.Group();

                var frameShape = roundedRectShape(1.72, 3.44, 0.34);
                var frameGeo = new THREE.ExtrudeGeometry(frameShape, {
                    depth: 0.2,
                    bevelEnabled: true,
                    bevelThickness: 0.02,
                    bevelSize: 0.02,
                    bevelSegments: 3
                });
                var frameMat = new THREE.MeshPhysicalMaterial({
                    color: hexToInt(COLOR.gold),
                    metalness: 0.75,
                    roughness: 0.3,
                    clearcoat: 0.4
                });
                var frameMesh = new THREE.Mesh(frameGeo, frameMat);
                frameMesh.position.z = -0.13;
                phoneGroup.add(frameMesh);

                var bodyShape = roundedRectShape(1.6, 3.3, 0.3);
                var bodyGeo = new THREE.ExtrudeGeometry(bodyShape, {
                    depth: 0.18,
                    bevelEnabled: true,
                    bevelThickness: 0.015,
                    bevelSize: 0.015,
                    bevelSegments: 3
                });
                var bodyMat = new THREE.MeshPhysicalMaterial({
                    color: 0x14231d,
                    metalness: 0.55,
                    roughness: 0.35,
                    clearcoat: 0.5,
                    clearcoatRoughness: 0.25
                });
                var bodyMesh = new THREE.Mesh(bodyGeo, bodyMat);
                phoneGroup.add(bodyMesh);

                var screenGeo = new THREE.PlaneGeometry(1.42, 3.05);
                var screenMat = new THREE.MeshBasicMaterial({
                    map: screenTexture
                });
                var screenMesh = new THREE.Mesh(screenGeo, screenMat);
                screenMesh.position.z = 0.2;
                phoneGroup.add(screenMesh);

                scene.add(phoneGroup);

                function size() {
                    var w = stickyEl.clientWidth,
                        h = stickyEl.clientHeight;
                    if (!w || !h) return;
                    renderer.setSize(w, h, false);
                    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, isMobile ? 1.5 : 2));
                    camera.aspect = w / h;
                    camera.updateProjectionMatrix();
                }

                function getProgress() {
                    var rect = wrap.getBoundingClientRect();
                    var total = wrap.offsetHeight - window.innerHeight;
                    if (total <= 0) return {
                        index: 0,
                        frac: 0,
                        scaled: 0
                    };
                    var scrolled = -rect.top;
                    var progress = Math.min(Math.max(scrolled / total, 0), 1);
                    var scaled = progress * (FEATURE_COUNT - 1);
                    var index = Math.min(Math.floor(scaled), FEATURE_COUNT - 1);
                    var frac = scaled - index;
                    return {
                        index: index,
                        frac: frac,
                        scaled: scaled
                    };
                }

                function tick(now) {
                    rafId = requestAnimationFrame(tick);
                    var t = (now - startTime) / 1000;
                    var prog = getProgress();
                    var index = prog.index,
                        frac = prog.frac,
                        scaled = prog.scaled;
                    var nextIndex = Math.min(index + 1, FEATURE_COUNT - 1);
                    var targetX = X_POS[index] + (X_POS[nextIndex] - X_POS[index]) * frac;

                    var easeAmt = prefersReduced ? 1 : 0.08;
                    phoneGroup.position.x += (targetX - phoneGroup.position.x) * easeAmt;

                    if (prefersReduced) {
                        phoneGroup.rotation.y = scaled * Math.PI * 0.5;
                        phoneGroup.rotation.x = 0;
                        phoneGroup.position.y = 0;
                    } else {
                        phoneGroup.rotation.y = t * 0.22 + scaled * Math.PI * 1.15;
                        phoneGroup.rotation.x = Math.sin(t * 0.6) * 0.06 + (frac - 0.5) * 0.12;
                        phoneGroup.position.y = Math.sin(t * 0.75) * 0.09;
                    }

                    drawScreen(screenCtx, screenCanvas.width, screenCanvas.height, index, nextIndex, frac, t);
                    screenTexture.needsUpdate = true;

                    renderer.render(scene, camera);
                }

                function start() {
                    if (!running) {
                        running = true;
                        startTime = performance.now();
                        rafId = requestAnimationFrame(tick);
                    }
                }

                function stop() {
                    if (running) {
                        running = false;
                        cancelAnimationFrame(rafId);
                    }
                }

                size();
                window.addEventListener('resize', function() {
                    size();
                }, {
                    passive: true
                });

                if ('IntersectionObserver' in window) {
                    var io = new IntersectionObserver(function(entries) {
                        entries.forEach(function(en) {
                            if (en.isIntersecting) {
                                start();
                            } else {
                                stop();
                            }
                        });
                    }, {
                        threshold: 0
                    });
                    io.observe(wrap);
                } else {
                    start();
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                initLoader();
                initLenis();
                initHeroTilt();
                initHeroParallax();
                initReveal();
                initSlideReveal();
                initPhone3D();
            });
        })();
    </script>

</body>

</html>