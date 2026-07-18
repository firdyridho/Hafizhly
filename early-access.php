<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#04140f">
    <title>Get Early Access - Hifzhly</title>
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
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 32px;
        }

        /* ============ HERO ============ */
        .hero {
            position: relative;
            background: radial-gradient(120% 90% at 50% 0%, #113d2c 0%, var(--night-mid) 45%, var(--night) 100%);
            padding-bottom: 70px;
            overflow: hidden;
            border-bottom-left-radius: 42px;
            border-bottom-right-radius: 42px;
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
            font-size: 12rem;
            color: rgba(255, 255, 255, 0.035);
            top: -20px;
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
            font-size: 0.85rem;
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

        /* Centered hero copy, matching reference layout */
        .hero-copy {
            position: relative;
            z-index: 3;
            text-align: center;
            max-width: 700px;
            margin: 44px auto 0;
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
            font-size: 3.6rem;
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -1.8px;
            margin-bottom: 18px;
        }

        .hero h1 em {
            font-style: normal;
            display: block;
            background: linear-gradient(100deg, var(--gold-light), var(--primary-light) 75%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero p.lede {
            color: rgba(255, 255, 255, 0.68);
            font-size: 1.05rem;
            line-height: 1.7;
            max-width: 480px;
            margin: 0 auto 34px;
        }

        /* Store buttons, centered */
        .store-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
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
            color: var(--night);
            font-size: 0.55rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 20px;
        }

        /* ===== Phone stage — large, centered below hero copy (signature element) ===== */
        .phone-stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 480px;
            margin-top: 26px;
            perspective: 1400px;
        }

        .phone-aura {
            position: absolute;
            width: 420px;
            height: 420px;
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
            width: 264px;
            height: 536px;
            background: linear-gradient(160deg, #10241c, #04140f);
            border-radius: 46px;
            padding: 12px;
            box-shadow: 0 40px 70px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.06) inset;
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
            gap: 24px;
        }

        .feature-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 26px 26px 30px;
            transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 46px rgba(5, 150, 105, 0.1);
            border-color: rgba(5, 150, 105, 0.28);
        }

        /* ---- device simulation area ---- */
        .device-sim {
            position: relative;
            height: 210px;
            border-radius: 18px;
            background: linear-gradient(165deg, #eef7f2, #e4f0e9);
            margin-bottom: 22px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
        }

        .mini-phone {
            width: 118px;
            height: 190px;
            margin-bottom: -14px;
            background: linear-gradient(160deg, #10241c, #04140f);
            border-radius: 20px;
            padding: 7px;
            box-shadow: 0 20px 34px rgba(4, 20, 15, 0.28);
            animation: floatY 5.5s ease-in-out infinite;
        }

        .mini-screen {
            width: 100%;
            height: 100%;
            background: #ffffff;
            border-radius: 14px;
            position: relative;
            overflow: hidden;
            padding: 14px 10px;
            display: flex;
            flex-direction: column;
        }

        .floating-chip {
            position: absolute;
            display: flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 0.66rem;
            font-weight: 700;
            color: var(--ink);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.14);
            animation: chipFloat 4s ease-in-out infinite;
            z-index: 3;
        }

        .floating-chip i {
            color: var(--primary);
            font-size: 0.72rem;
        }

        @keyframes chipFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        /* Card 1: mutaba'ah mini list */
        .sim-checklist {
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-top: 4px;
        }

        .sim-checklist .row {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sim-checklist .box {
            width: 12px;
            height: 12px;
            border-radius: 4px;
            border: 1.4px solid #cfe3d9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.42rem;
            color: #fff;
            flex-shrink: 0;
        }

        .sim-checklist .bar {
            height: 5px;
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

        .chip-1 {
            top: 14px;
            left: -8px;
        }

        /* Card 2: streak mini counter */
        .sim-streak {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sim-streak .flame-big {
            font-size: 1.6rem;
            color: #f59e0b;
            animation: flameFlicker 1.4s ease-in-out infinite;
        }

        .sim-streak .count {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--ink);
        }

        .sim-streak .row {
            display: flex;
            gap: 4px;
        }

        .sim-streak .dot {
            width: 10px;
            height: 10px;
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
                background: var(--primary);
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

        .chip-2 {
            top: 10px;
            right: -10px;
        }

        /* Card 3: export mini */
        .sim-export {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
        }

        .sim-export .file-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
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
            width: 78%;
            height: 5px;
            border-radius: 4px;
            background: #e2edea;
            overflow: hidden;
        }

        .sim-export .track .fill {
            height: 100%;
            width: 0%;
            background: var(--primary);
            border-radius: 4px;
            animation: fillBar 3.6s ease-in-out infinite;
        }

        @keyframes fillBar {
            0% {
                width: 0%;
            }

            60% {
                width: 100%;
            }

            100% {
                width: 100%;
            }
        }

        .chip-3 {
            top: 12px;
            left: -10px;
        }

        /* Card 4: sync mini */
        .sim-sync {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            position: relative;
        }

        .sim-sync .node {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #eef7f2;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }

        .sim-sync .track2 {
            position: absolute;
            left: 36px;
            right: 36px;
            top: 50%;
            height: 2px;
            background: repeating-linear-gradient(90deg, #cfe3d9 0 5px, transparent 5px 10px);
        }

        .sim-sync .pulse2 {
            position: absolute;
            top: 50%;
            left: 36px;
            width: 6px;
            height: 6px;
            margin-top: -3px;
            border-radius: 50%;
            background: var(--primary-light);
            box-shadow: 0 0 6px 2px rgba(52, 211, 153, 0.6);
            animation: pulseMove2 2.2s linear infinite;
        }

        @keyframes pulseMove2 {
            0% {
                left: 36px;
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                left: calc(100% - 42px);
                opacity: 0;
            }
        }

        .chip-4 {
            top: 8px;
            right: -10px;
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
            .hero h1 {
                font-size: 2.7rem;
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
                font-size: 2.1rem;
                letter-spacing: -1px;
            }

            .store-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-store {
                width: 100%;
                justify-content: center;
            }

            .phone-frame {
                width: 220px;
                height: 448px;
            }

            .floating-chip {
                font-size: 0.6rem;
                padding: 6px 9px;
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
                    Hifzhly
                </a>
                <a class="nav-cta" href="login.php">Sudah punya akun? Masuk <i class="fa-solid fa-arrow-right"></i></a>
            </nav>

            <div class="hero-copy">
                <div class="hero-eyebrow"><i class="fa-solid fa-sparkles"></i> Segera hadir di ponselmu</div>
                <h1>Genggam hafalanmu<em>ke mana pun melangkah.</em></h1>
                <p class="lede">Aplikasi mobile Hifzhly sedang di tahap penyempurnaan akhir. Murojaah, mutaba'ah, dan progres hafalanmu, kini bisa kamu bawa ke mana saja.</p>

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

            <!-- Large centered phone mockup below hero copy, matching reference layout -->
            <div class="phone-stage" id="phoneStage">
                <div class="phone-aura"></div>
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
    </section>

    <!-- ============ FEATURES ============ -->
    <section class="features">
        <div class="container">
            <div class="section-head">
                <div class="section-eyebrow">Kenapa Hifzhly</div>
                <h2>Dibangun untuk konsistensi hafalanmu</h2>
                <p>Empat kebiasaan kecil yang dijaga aplikasinya, dilihat langsung lewat simulasi tampilan appnya di bawah ini.</p>
            </div>

            <div class="feature-grid">
                <!-- Feature 1: Mutaba'ah Cerdas -->
                <div class="feature-card">
                    <div class="device-sim">
                        <div class="floating-chip chip-1"><i class="fa-solid fa-check"></i> Tersimpan otomatis</div>
                        <div class="mini-phone">
                            <div class="mini-screen">
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
                    <h3 class="feature-title">Mutaba'ah Cerdas</h3>
                    <p class="feature-desc">Catat setiap aktivitas tilawah, murojaah, dan hafalan barumu dengan cepat dan rapi, langsung tercentang otomatis begitu selesai.</p>
                </div>

                <!-- Feature 2: Streak -->
                <div class="feature-card">
                    <div class="device-sim">
                        <div class="floating-chip chip-2"><i class="fa-solid fa-fire"></i> 7 hari beruntun</div>
                        <div class="mini-phone">
                            <div class="mini-screen">
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
                    <h3 class="feature-title">Konsistensi &amp; Streak</h3>
                    <p class="feature-desc">Pertahankan api semangatmu setiap hari. Jangan biarkan streak-mu terputus, aplikasi akan mengingatkanmu tepat waktu.</p>
                </div>

                <!-- Feature 3: Ekspor Laporan -->
                <div class="feature-card">
                    <div class="device-sim">
                        <div class="floating-chip chip-3"><i class="fa-solid fa-file-arrow-down"></i> Laporan_Juli.pdf</div>
                        <div class="mini-phone">
                            <div class="mini-screen">
                                <div class="sim-export">
                                    <div class="file-icon"><i class="fa-solid fa-file-lines"></i></div>
                                    <div class="track">
                                        <div class="fill"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h3 class="feature-title">Ekspor Laporan</h3>
                    <p class="feature-desc">Unduh rekap aktivitas bulananmu ke dalam format PDF atau Excel, siap dibagikan ke musyrif atau orang tua.</p>
                </div>

                <!-- Feature 4: Multi-Platform -->
                <div class="feature-card">
                    <div class="device-sim">
                        <div class="floating-chip chip-4"><i class="fa-solid fa-check"></i> Tersinkron</div>
                        <div class="mini-phone">
                            <div class="mini-screen">
                                <div class="sim-sync">
                                    <div class="node"><i class="fa-solid fa-desktop"></i></div>
                                    <div class="track2"></div>
                                    <div class="pulse2"></div>
                                    <div class="node"><i class="fa-solid fa-mobile-screen"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <h3 class="feature-title">Multi-Platform</h3>
                    <p class="feature-desc">Sinkronisasi sempurna. Akses data hafalanmu dari Desktop, Android, maupun iOS, selalu terbarui otomatis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FOOTER ============ -->
    <footer class="site-footer">
        <div class="container">&copy; <?= date('Y') ?> Hifzhly. Pendamping Murojaah Al-Qur'an Berbasis AI.</div>
    </footer>

    <script>
        // Gentle cursor-follow tilt on the hero phone mockup (desktop only, disabled for reduced-motion users)
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