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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.css">

    <style>
        :root {
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

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html.lenis { scroll-behavior: auto; }
        html.lenis { height: auto; }
        .lenis.lenis-smooth { scroll-behavior: auto; }
        .lenis.lenis-smooth [data-lenis-prevent] { overscroll-behavior: contain; }
        .lenis.lenis-stopped { overflow: hidden; }
        .lenis.lenis-scrolling iframe { pointer-events: none; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(165deg, #eaf7f1 0%, #fdfcf9 28%, #fdfcf9 68%, #eef6f0 100%);
            color: var(--ink);
            line-height: 1.65;
            overflow-x: hidden;
        }

        h1, h2, h3, .display { font-family: 'Amiri', serif; }

        a { text-decoration: none; }

        ::selection { background: var(--gold-soft); color: var(--emerald-deep); }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        .route-veil {
            position: fixed; inset: 0; z-index: 9998;
            background: var(--emerald-deep);
            transform: translateY(100%);
            transition: transform 0.55s var(--ease);
            pointer-events: none;
        }
        .route-veil.active { transform: translateY(0); pointer-events: all; }

        .arabesque-bg {
            position: fixed; inset: -10%; z-index: 0;
            pointer-events: none; opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Cg fill='none' stroke='%23073b2c' stroke-width='1'%3E%3Cpath d='M60 6 L96 30 L96 90 L60 114 L24 90 L24 30 Z'/%3E%3Ccircle cx='60' cy='60' r='30'/%3E%3C/g%3E%3C/svg%3E");
            animation: drift 80s linear infinite;
        }
        @keyframes drift {
            0% { transform: translate(0, 0); }
            100% { transform: translate(120px, 120px); }
        }

        .glow-blob {
            position: fixed; border-radius: 50%;
            filter: blur(90px); z-index: 0;
            pointer-events: none; opacity: 0.25;
        }
        .glow-blob.b1 {
            width: 420px; height: 420px;
            background: radial-gradient(circle, var(--emerald-bright), transparent 70%);
            top: -120px; left: -100px;
            animation: floatBlob 16s ease-in-out infinite;
        }
        .glow-blob.b2 {
            width: 360px; height: 360px;
            background: radial-gradient(circle, var(--gold-soft), transparent 70%);
            top: 40%; right: -140px;
            animation: floatBlob 20s ease-in-out infinite reverse;
        }
        .glow-blob.b3 {
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(43,171,130,0.3), transparent 70%);
            bottom: 10%; left: -80px;
            animation: floatBlob 18s ease-in-out infinite;
        }

        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, -40px) scale(1.08); }
        }

        #particles-canvas {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
        }

        .scroll-progress {
            position: fixed; top: 0; left: 0; width: 0; height: 3px;
            background: linear-gradient(90deg, var(--emerald-bright), var(--gold));
            z-index: 9999; transition: width 0.1s linear;
        }

        .navbar-hz {
            background: rgba(253, 252, 249, 0.78);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0; width: 100%; z-index: 1000;
            border-bottom: 1px solid var(--line);
            transition: padding 0.4s var(--ease), box-shadow 0.4s var(--ease), background 0.4s var(--ease);
        }
        .navbar-hz.scrolled {
            padding: 11px 6%;
            box-shadow: 0 10px 30px rgba(7, 59, 44, 0.07);
            background: rgba(253, 252, 249, 0.92);
        }
        .nav-brand {
            font-family: 'Amiri', serif;
            font-size: 1.5rem; font-weight: 700;
            color: var(--emerald-deep);
            display: flex; align-items: center; gap: 12px;
            letter-spacing: 0.3px;
        }
        .logo-mark {
            background: var(--emerald-deep);
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            padding: 7px;
        }
        .logo-mark img { width: 100%; height: 100%; object-fit: contain; filter: brightness(0) invert(1); }
        .nav-brand .logo-mark { width: 38px; height: 38px; }

        .btn-back {
            background: var(--emerald-deep);
            color: var(--ivory);
            padding: 11px 24px;
            border-radius: 999px;
            font-weight: 600; font-size: 0.92rem;
            letter-spacing: 0.2px;
            transition: 0.35s var(--ease);
            display: inline-flex; align-items: center; gap: 8px;
            position: relative; overflow: hidden;
        }
        .btn-back span { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 8px; }
        .btn-back::before {
            content: "";
            position: absolute; inset: 0;
            background: linear-gradient(120deg, var(--emerald-bright), var(--gold));
            transform: translateX(-100%);
            transition: transform 0.45s var(--ease);
        }
        .btn-back:hover::before { transform: translateX(0); }
        .btn-back:hover { box-shadow: 0 10px 26px rgba(7, 59, 44, 0.3); transform: translateY(-2px); }

        .hero {
            position: relative;
            padding: clamp(140px, 20vw, 180px) 6% clamp(80px, 10vw, 120px);
            text-align: center;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }
        .hero-bg-mesh {
            position: absolute; inset: 0; z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 30% 20%, rgba(43,171,130,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 70% 80%, rgba(189,154,75,0.06) 0%, transparent 50%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(20,104,79,0.04) 0%, transparent 50%);
            animation: meshShift 12s ease-in-out infinite alternate;
        }
        @keyframes meshShift {
            0% { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1.1) rotate(2deg); }
        }

        .hero-floating {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            opacity: 0.1;
        }
        .hero-floating.f1 {
            width: 60px; height: 60px;
            border: 2px solid var(--emerald-bright);
            top: 15%; left: 8%;
            animation: floatShape 8s ease-in-out infinite;
        }
        .hero-floating.f2 {
            width: 40px; height: 40px;
            background: var(--gold-soft);
            top: 25%; right: 12%;
            animation: floatShape 10s ease-in-out infinite reverse;
        }
        .hero-floating.f3 {
            width: 24px; height: 24px;
            border: 2px solid var(--gold);
            bottom: 20%; left: 15%;
            animation: floatShape 7s ease-in-out infinite;
        }
        .hero-floating.f4 {
            width: 80px; height: 80px;
            border: 1px solid rgba(43,171,130,0.2);
            bottom: 15%; right: 10%;
            animation: floatShape 12s ease-in-out infinite reverse;
        }
        @keyframes floatShape {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(15px, -20px) rotate(120deg); }
            66% { transform: translate(-10px, 15px) rotate(240deg); }
        }

        .hero-mark {
            width: clamp(56px, 8vw, 72px);
            height: clamp(56px, 8vw, 72px);
            margin: 0 auto 28px;
            border-radius: 20px;
            padding: 13px;
            box-shadow: 0 16px 40px rgba(7, 59, 44, 0.2);
            animation: heroPulse 3s ease-in-out infinite;
        }
        @keyframes heroPulse {
            0%, 100% { box-shadow: 0 16px 40px rgba(7, 59, 44, 0.2); }
            50% { box-shadow: 0 16px 60px rgba(7, 59, 44, 0.35), 0 0 80px rgba(43, 171, 130, 0.15); }
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
            to { opacity: 1; transform: translateY(0); }
        }
        .hero h1 em {
            font-style: italic;
            background: linear-gradient(135deg, var(--emerald), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
            gap: 12px;
            color: var(--ink-muted);
            font-size: 0.78rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .scroll-mouse {
            width: 24px; height: 36px;
            border: 2px solid var(--gold);
            border-radius: 12px;
            position: relative;
            opacity: 0.7;
        }
        .scroll-mouse-dot {
            width: 4px; height: 8px;
            background: var(--gold);
            border-radius: 2px;
            position: absolute;
            top: 6px; left: 50%;
            transform: translateX(-50%);
            animation: scrollDot 1.8s ease-in-out infinite;
        }
        @keyframes scrollDot {
            0% { transform: translateX(-50%) translateY(0); opacity: 1; }
            100% { transform: translateX(-50%) translateY(14px); opacity: 0; }
        }

        .section-divider {
            position: relative;
            z-index: 1;
            height: 80px;
            overflow: hidden;
            pointer-events: none;
        }
        .section-divider svg {
            position: absolute;
            width: 100%; height: 100%;
            fill: var(--emerald-deep);
        }
        .section-divider.top-emerald svg { fill: var(--emerald-deep); }
        .section-divider.top-light svg { fill: rgba(253,252,249,0.6); }

        .vision-section {
            position: relative;
            z-index: 1;
            padding: clamp(60px, 8vw, 100px) 6%;
        }
        .vision-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(30px, 4vw, 60px);
            max-width: 1080px;
            margin: 0 auto;
        }
        .vision-card {
            background: rgba(253, 252, 249, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            padding: clamp(32px, 4vw, 44px);
            border: 1px solid var(--line);
            transition: 0.5s var(--ease);
            position: relative;
            overflow: hidden;
        }
        .vision-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--emerald-bright), var(--gold));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.6s var(--ease);
        }
        .vision-card:hover::before { transform: scaleX(1); }
        .vision-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 60px rgba(7, 59, 44, 0.12);
            background: rgba(253, 252, 249, 0.9);
        }
        .vision-card::after {
            content: "";
            position: absolute;
            top: -50%; right: -50%;
            width: 100%; height: 100%;
            background: radial-gradient(circle, var(--mint-50), transparent 70%);
            opacity: 0;
            transition: opacity 0.6s var(--ease);
            pointer-events: none;
        }
        .vision-card:hover::after { opacity: 1; }
        .vision-icon {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--mint-50), #ffffff);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            color: var(--emerald-deep);
            margin-bottom: 22px;
            border: 1px solid var(--line);
            position: relative;
            z-index: 1;
            transition: transform 0.4s var(--ease), box-shadow 0.4s var(--ease);
        }
        .vision-card:hover .vision-icon {
            transform: scale(1.05) rotate(-3deg);
            box-shadow: 0 8px 24px rgba(7,59,44,0.1);
        }
        .vision-card h3 {
            font-size: 1.3rem;
            color: var(--emerald-deep);
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }
        .vision-card p {
            color: var(--ink-muted);
            font-size: 0.95rem;
            line-height: 1.8;
            position: relative;
            z-index: 1;
        }
        .vision-card ul {
            list-style: none;
            padding: 0; margin: 0;
            position: relative;
            z-index: 1;
        }
        .vision-card ul li {
            padding: 8px 0;
            color: var(--ink-muted);
            font-size: 0.95rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .vision-card ul li::before {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: var(--emerald-bright);
            font-size: 0.85rem;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .team-section {
            position: relative;
            z-index: 1;
            padding: clamp(60px, 9vw, 100px) 6% clamp(50px, 7vw, 80px);
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
            margin-bottom: clamp(48px, 7vw, 80px);
        }

        .dev-row {
            display: flex;
            align-items: center;
            gap: clamp(36px, 6vw, 90px);
            max-width: 1080px;
            margin: 0 auto clamp(70px, 10vw, 120px);
        }
        .dev-row:last-child { margin-bottom: 0; }
        .dev-row.reverse { flex-direction: row-reverse; }

        .dev-photo-wrap {
            position: relative;
            width: clamp(230px, 27vw, 320px);
            aspect-ratio: 1 / 1;
            flex-shrink: 0;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 16px 40px rgba(7, 59, 44, 0.08);
            transition: box-shadow 0.5s var(--ease);
        }
        .dev-photo-wrap:hover { box-shadow: 0 24px 60px rgba(7, 59, 44, 0.18); }
        .dev-photo-wrap .dev-photo-frame {
            width: 100%; height: 100%;
            overflow: hidden;
            border-radius: 24px;
        }
        .dev-photo-wrap .dev-photo-frame img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.7s var(--ease);
        }
        .dev-row:hover .dev-photo-frame img { transform: scale(1.05); }

        .dev-photo-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(0deg, rgba(20, 104, 79, 0.92) 0%, rgba(20, 104, 79, 0.15) 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 8px;
            opacity: 0;
            transition: opacity 0.5s var(--ease);
            border-radius: 24px;
            padding: 20px;
            text-align: center;
        }
        .dev-photo-wrap:hover .dev-photo-overlay { opacity: 1; }
        .dev-photo-overlay i { font-size: 2rem; color: var(--gold); }
        .dev-photo-overlay .overlay-quote {
            color: var(--ivory);
            font-size: 0.9rem;
            font-style: italic;
            line-height: 1.6;
        }

        .avatar-fallback {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            font-size: 3rem; color: var(--emerald);
            background: var(--mint-50);
        }

        .dev-info { flex: 1; min-width: 280px; }
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
            width: 22px; height: 1px;
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
        .dev-row.reverse .dev-bio { margin-left: auto; }
        .dev-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
        }
        .dev-row.reverse .dev-skills { justify-content: flex-end; }
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
            cursor: default;
        }
        .skill-chip i { color: var(--gold); font-size: 0.95rem; }
        .skill-chip:hover {
            background: linear-gradient(135deg, var(--emerald-deep), var(--emerald));
            color: var(--ivory);
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(7, 59, 44, 0.22);
        }
        .skill-chip:hover i { color: var(--gold-soft); }

        .social-links {
            display: flex;
            gap: 12px;
        }
        .dev-row.reverse .social-links { justify-content: flex-end; }
        .social-btn {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: var(--mint-50);
            color: var(--emerald);
            display: flex; justify-content: center; align-items: center;
            font-size: 1.02rem;
            transition: 0.3s var(--ease);
            border: 1px solid transparent;
            position: relative;
        }
        .social-btn:hover {
            background: var(--emerald-deep);
            color: var(--ivory);
            transform: translateY(-3px) scale(1.06);
        }
        .social-btn .tooltip-text {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%) scale(0.9);
            background: var(--ink);
            color: var(--ivory);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: 0.25s var(--ease);
        }
        .social-btn .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%; left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: var(--ink);
        }
        .social-btn:hover .tooltip-text { opacity: 1; transform: translateX(-50%) scale(1); }

        .tech-section {
            background: var(--emerald-deep);
            padding: clamp(64px, 9vw, 100px) 6%;
            text-align: center;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        .tech-section::before {
            content: "";
            position: absolute; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140' viewBox='0 0 140 140'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1'%3E%3Cpath d='M70 8 L112 35 L112 105 L70 132 L28 105 L28 35 Z'/%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.04;
        }
        .tech-section::after {
            content: "";
            position: absolute; inset: 0;
            background: radial-gradient(circle at 50% 0%, rgba(29, 157, 117, 0.28), transparent 55%);
        }
        .tech-section h2 {
            position: relative;
            font-size: clamp(1.5rem, 3vw, 2.15rem);
            font-weight: 700;
            color: var(--ivory);
            margin-bottom: 10px;
        }
        .tech-subtitle {
            position: relative;
            color: rgba(253, 252, 249, 0.62);
            font-size: 0.95rem;
            margin-bottom: 50px;
        }
        .tech-categories {
            position: relative;
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 48px;
        }
        .tech-cat-btn {
            padding: 12px 28px;
            border-radius: 999px;
            border: 1px solid rgba(253, 252, 249, 0.15);
            background: transparent;
            color: rgba(253, 252, 249, 0.6);
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: 0.35s var(--ease);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .tech-cat-btn:hover {
            border-color: rgba(189, 154, 75, 0.5);
            color: var(--ivory);
            background: rgba(253,252,249,0.05);
        }
        .tech-cat-btn.active {
            background: var(--gold);
            color: var(--ink);
            border-color: var(--gold);
        }

        .tech-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .tech-item {
            position: relative;
            background: linear-gradient(160deg, rgba(253, 252, 249, 0.08), rgba(253, 252, 249, 0.02));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 28px 16px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(253, 252, 249, 0.08);
            transition: transform 0.5s var(--ease), border-color 0.5s var(--ease), box-shadow 0.5s var(--ease), opacity 0.4s var(--ease);
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .tech-item.hidden {
            opacity: 0;
            transform: translateY(12px) scale(0.95);
            pointer-events: none;
            position: absolute;
        }
        .tech-item:hover {
            transform: translateY(-8px);
            border-color: rgba(189, 154, 75, 0.5);
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.3);
            background: linear-gradient(160deg, rgba(253, 252, 249, 0.14), rgba(253, 252, 249, 0.04));
        }
        .tech-icon-badge {
            position: relative;
            width: 56px; height: 56px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: rgba(7, 59, 44, 0.5);
        }
        .tech-icon-badge::before {
            content: "";
            position: absolute; inset: -3px;
            border-radius: 50%;
            padding: 2px;
            background: conic-gradient(from 0deg, var(--gold), var(--emerald-bright), var(--gold));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: ringSpin 5s linear infinite;
        }
        @keyframes ringSpin { to { transform: rotate(360deg); } }
        .tech-icon-badge i {
            font-size: 1.5rem;
            position: relative;
            transition: transform 0.5s var(--ease);
        }
        .tech-item:hover .tech-icon-badge i { transform: rotateY(180deg) scale(1.1); }
        .tech-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--ivory);
            letter-spacing: 0.3px;
        }
        .tech-cat-label {
            display: inline-block;
            background: rgba(189, 154, 75, 0.2);
            color: var(--gold-soft);
            font-size: 0.6rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 999px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: 1px solid rgba(189, 154, 75, 0.15);
        }

        .cta-section {
            position: relative;
            z-index: 1;
            padding: clamp(64px, 9vw, 110px) 6%;
            text-align: center;
            background: linear-gradient(135deg, var(--mint-50), #ffffff, var(--mint-50));
        }
        .cta-section h2 {
            font-size: clamp(1.6rem, 3.5vw, 2.4rem);
            color: var(--emerald-deep);
            margin-bottom: 16px;
        }
        .cta-section p {
            color: var(--ink-muted);
            font-size: 1rem;
            max-width: 560px;
            margin: 0 auto 32px;
        }
        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, var(--emerald-deep), var(--emerald-bright));
            color: var(--ivory);
            padding: 16px 44px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 1.05rem;
            transition: 0.4s var(--ease);
            position: relative;
            overflow: hidden;
        }
        .btn-cta::before {
            content: "";
            position: absolute; inset: 0;
            background: linear-gradient(120deg, var(--emerald-bright), var(--gold));
            opacity: 0;
            transition: opacity 0.4s var(--ease);
        }
        .btn-cta:hover::before { opacity: 1; }
        .btn-cta span { position: relative; z-index: 1; }
        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 40px rgba(7, 59, 44, 0.3);
        }

        footer {
            background: var(--ink);
            color: rgba(253, 252, 249, 0.6);
            text-align: center;
            padding: 34px;
            font-size: 0.88rem;
            position: relative;
            z-index: 1;
        }
        footer i { color: var(--gold); }
        footer .footer-brand {
            font-family: 'Amiri', serif;
            font-size: 1.1rem;
            color: var(--ivory);
            margin-bottom: 6px;
        }
        footer .footer-links {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        footer .footer-links a {
            color: rgba(253, 252, 249, 0.5);
            font-size: 0.82rem;
            transition: color 0.3s;
        }
        footer .footer-links a:hover { color: var(--gold); }

        [data-aos] { will-change: transform, opacity; }

        @media (max-width: 992px) {
            .tech-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 860px) {
            .dev-row, .dev-row.reverse {
                flex-direction: column;
                text-align: center;
            }
            .dev-bio, .dev-row.reverse .dev-bio {
                margin-left: auto; margin-right: auto;
            }
            .dev-skills, .dev-row.reverse .dev-skills,
            .social-links, .dev-row.reverse .social-links { justify-content: center; }
            .dev-index::before { display: none; }
            .vision-grid { grid-template-columns: 1fr; }
            .section-heading { margin-bottom: clamp(32px, 5vw, 48px); }
            .tech-grid { grid-template-columns: repeat(3, 1fr); gap: 16px; }
            .tech-item { padding: 24px 12px 16px; }
            .hero-floating { display: none; }
        }

        @media (max-width: 768px) {
            .navbar-hz { padding: 15px 5%; }
            .navbar-hz.scrolled { padding: 10px 5%; }
            .hero { padding: 128px 6% 50px; }
            .hero p { max-width: 100%; }
            .dev-photo-wrap { width: clamp(180px, 55vw, 240px); }
            .vision-card { padding: clamp(24px, 3vw, 32px); }
            .cta-section { padding: clamp(48px, 8vw, 72px) 5%; }
            .btn-cta { padding: 14px 32px; font-size: 0.95rem; }
            footer { padding: 28px 5%; }
            footer .footer-links { gap: 16px; }
            .tech-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
            .tech-item { padding: 20px 12px 14px; }
            .tech-icon-badge { width: 48px; height: 48px; }
            .tech-icon-badge i { font-size: 1.2rem; }
            .section-divider { height: 50px; }
        }

        @media (max-width: 480px) {
            .navbar-hz { padding: 12px 4%; }
            .navbar-hz.scrolled { padding: 8px 4%; }
            .nav-brand { font-size: 1.2rem; }
            .nav-brand .logo-mark { width: 32px; height: 32px; }
            .btn-back { padding: 8px 16px; font-size: 0.82rem; }
            .hero { padding: 112px 5% 44px; }
            .hero h1 { font-size: clamp(1.6rem, 7vw, 2rem); }
            .hero p { font-size: 0.92rem; }
            .hero-scrollcue { margin-top: 36px; font-size: 0.7rem; }
            .hero-eyebrow { font-size: 0.7rem; letter-spacing: 2px; }
            .hero-eyebrow::before, .hero-eyebrow::after { width: 18px; }
            .hero-mark { width: 48px; height: 48px; padding: 10px; margin-bottom: 20px; }
            .section-label { font-size: 0.7rem; letter-spacing: 2px; }
            .section-heading { font-size: clamp(1.2rem, 5vw, 1.5rem); }
            .dev-photo-wrap { width: clamp(160px, 60vw, 200px); }
            .dev-name { font-size: clamp(1.2rem, 5vw, 1.4rem); }
            .dev-bio { font-size: 0.9rem; }
            .dev-skills { gap: 8px; }
            .skill-chip { padding: 7px 14px 7px 10px; font-size: 0.76rem; }
            .social-btn { width: 38px; height: 38px; font-size: 0.9rem; }
            .vision-card { padding: 22px; border-radius: 20px; }
            .vision-icon { width: 48px; height: 48px; font-size: 1.2rem; margin-bottom: 16px; }
            .vision-card h3 { font-size: 1.1rem; }
            .vision-card p, .vision-card ul li { font-size: 0.88rem; }
            .tech-section { padding: clamp(48px, 8vw, 64px) 5%; }
            .tech-section h2 { font-size: clamp(1.2rem, 5vw, 1.5rem); }
            .tech-subtitle { font-size: 0.85rem; margin-bottom: 28px; }
            .tech-cat-btn { padding: 8px 18px; font-size: 0.78rem; }
            .tech-categories { gap: 8px; margin-bottom: 32px; }
            .tech-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .tech-item { padding: 18px 10px 12px; gap: 10px; }
            .tech-icon-badge { width: 42px; height: 42px; }
            .tech-icon-badge i { font-size: 1rem; }
            .tech-name { font-size: 0.78rem; }
            .tech-cat-label { font-size: 0.55rem; padding: 3px 10px; }
            .cta-section h2 { font-size: clamp(1.2rem, 5vw, 1.5rem); }
            .cta-section p { font-size: 0.9rem; }
            .btn-cta { padding: 12px 28px; font-size: 0.88rem; }
            footer { padding: 24px 4%; font-size: 0.8rem; }
            footer .footer-brand { font-size: 1rem; }
            footer .footer-links a { font-size: 0.75rem; }
            .section-divider { height: 40px; }
        }

        @media (min-width: 1400px) {
            .hero p { max-width: 720px; }
            .dev-bio { max-width: 520px; }
            .tech-grid { max-width: 1100px; gap: 24px; }
        }
    </style>
</head>

<body>

    <div class="route-veil" id="routeVeil"></div>
    <div class="scroll-progress" id="scrollProgress"></div>

    <div class="arabesque-bg"></div>
    <div class="glow-blob b1"></div>
    <div class="glow-blob b2"></div>
    <div class="glow-blob b3"></div>

    <canvas id="particles-canvas"></canvas>

    <nav class="navbar-hz" id="navbar">
        <a href="index.php" class="nav-brand" data-route>
            <span class="logo-mark"><img src="assets/icon/logo.png" alt="Logo Hifzhly"></span>
            Hifzhly
        </a>
        <a href="index.php" class="btn-back" data-route><span><i class="fas fa-arrow-left"></i> Beranda</span></a>
    </nav>

    <section class="hero" id="hero">
        <div class="hero-bg-mesh"></div>
        <div class="hero-floating f1"></div>
        <div class="hero-floating f2"></div>
        <div class="hero-floating f3"></div>
        <div class="hero-floating f4"></div>
        <div class="hero-inner">
            <div class="logo-mark hero-mark" data-aos="zoom-in">
                <img src="assets/icon/logo.png" alt="Logo Hifzhly">
            </div>
            <div class="hero-eyebrow" data-aos="fade-up" data-aos-delay="100">Tim Pengembang</div>
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
                <span>Jelajahi</span>
                <div class="scroll-mouse"><div class="scroll-mouse-dot"></div></div>
            </div>
        </div>
    </section>

    <div class="section-divider top-light">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0,40 C240,0 480,80 720,40 C960,0 1200,80 1440,40 L1440,80 L0,80 Z"/>
        </svg>
    </div>

    <section class="vision-section" id="visi">
        <div class="section-label" data-aos="fade-up">Fundasi</div>
        <h2 class="section-heading" data-aos="fade-up" data-aos-delay="80">Visi &amp; Misi Kami</h2>
        <div class="vision-grid">
            <div class="vision-card" data-aos="fade-right" data-aos-delay="100">
                <div class="vision-icon"><i class="fas fa-eye"></i></div>
                <h3>Visi</h3>
                <p>Menjadi platform digital terdepan yang memberdayakan setiap muslim untuk istiqomah dalam menghafal, memahami, dan mengamalkan Al-Qur'an melalui teknologi yang humanis, intuitif, dan penuh keberkahan.</p>
            </div>
            <div class="vision-card" data-aos="fade-left" data-aos-delay="150">
                <div class="vision-icon"><i class="fas fa-list-check"></i></div>
                <h3>Misi</h3>
                <ul>
                    <li>Menyediakan fitur hafalan interaktif yang mudah digunakan oleh semua kalangan usia.</li>
                    <li>Membangun sistem murojaah cerdas yang menyesuaikan ritme belajar setiap pengguna.</li>
                    <li>Menghadirkan pengalaman belajar yang menyenangkan melalui gamifikasi dan target harian.</li>
                    <li>Terus berinovasi berdasarkan masukan dari komunitas penghafal Al-Qur'an.</li>
                </ul>
            </div>
        </div>
    </section>

    <div class="section-divider top-emerald">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M1440,40 C1200,0 960,80 720,40 C480,0 240,80 0,40 L0,80 L1440,80 Z" style="transform: scaleY(-1); transform-origin: center;"/>
        </svg>
    </div>

    <section class="team-section" id="tim">
        <div class="section-label" data-aos="fade-up">Sang Perancang</div>
        <h2 class="section-heading" data-aos="fade-up" data-aos-delay="80">Dua Tangan di Balik Layar</h2>

        <div class="dev-row">
            <div class="dev-photo-wrap" data-aos="fade-right" data-aos-delay="80">
                <div class="dev-photo-frame">
                    <img src="assets/images/pija.webp" alt="Faeyza Ardellein Yaradhitya"
                        onerror="this.parentElement.innerHTML='<div class=&quot;avatar-fallback&quot;><i class=&quot;fas fa-user-graduate&quot;></i></div>';">
                </div>
                <div class="dev-photo-overlay">
                    <i class="fas fa-quote-right"></i>
                    <p class="overlay-quote">"Membangun Hifzhly bukan sekadar coding—ini ibadah yang kami tulis dengan baris-baris kode."</p>
                </div>
            </div>
            <div class="dev-info" data-aos="fade-left" data-aos-delay="160">
                <div class="dev-index">Pijaaa <i class="fas fa-heart"></i></div>
                <h3 class="dev-name">Faeyza Ardellein Yaradhitya</h3>
                <div class="dev-role">Full-Stack Developer</div>
                <p class="dev-bio">Bertanggung jawab merancang pengalaman pengguna yang nyaman dan intuitif, serta memastikan alur sistem Hifzhly berjalan sesuai kebutuhan para penghafal Al-Qur'an. Spesialis dalam menghadirkan harmoni antara estetika visual dan fungsionalitas yang mulus.</p>
                <div class="dev-skills">
                    <span class="skill-chip"><i class="fas fa-pen-nib"></i> UI/UX Design</span>
                    <span class="skill-chip"><i class="fab fa-html5"></i> HTML5</span>
                    <span class="skill-chip"><i class="fab fa-css3-alt"></i> CSS3</span>
                    <span class="skill-chip"><i class="fab fa-js"></i> JavaScript</span>
                    <span class="skill-chip"><i class="fab fa-bootstrap"></i> Bootstrap 5</span>
                </div>
                <div class="social-links">
                    <a href="https://www.instagram.com/fyzardell" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-instagram"></i><span class="tooltip-text">Instagram</span></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i><span class="tooltip-text">GitHub</span></a>
                    <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i><span class="tooltip-text">LinkedIn</span></a>
                    <a href="#" class="social-btn"><i class="fas fa-envelope"></i><span class="tooltip-text">Email</span></a>
                </div>
            </div>
        </div>

        <div class="dev-row reverse">
            <div class="dev-photo-wrap" data-aos="fade-left" data-aos-delay="80">
                <div class="dev-photo-frame">
                    <img src="assets/images/firdy.webp" alt="Firdy Ridho Fillah"
                        onerror="this.parentElement.innerHTML='<div class=&quot;avatar-fallback&quot;><i class=&quot;fas fa-laptop-code&quot;></i></div>';">
                </div>
                <div class="dev-photo-overlay">
                    <i class="fas fa-quote-right"></i>
                    <p class="overlay-quote">"Setiap fitur yang lahir adalah jawaban atas doa dan kebutuhan para penghafal Qur'an di seluruh dunia."</p>
                </div>
            </div>
            <div class="dev-info" data-aos="fade-right" data-aos-delay="160">
                <div class="dev-index">Firdy</div>
                <h3 class="dev-name">Firdy Ridho Fillah</h3>
                <div class="dev-role">Full-Stack Developer</div>
                <p class="dev-bio">Mengeksekusi logika pemrograman dari sisi server hingga tampilan antarmuka, serta merancang arsitektur database Hifzhly agar aplikasi berjalan cepat dan aman. Berdedikasi dalam membangun fondasi teknis yang kokoh dan skalabel.</p>
                <div class="dev-skills">
                    <span class="skill-chip"><i class="fab fa-php"></i> PHP 8</span>
                    <span class="skill-chip"><i class="fas fa-database"></i> MySQL</span>
                    <span class="skill-chip"><i class="fas fa-code-branch"></i> REST API</span>
                    <span class="skill-chip"><i class="fas fa-shield-halved"></i> Session Auth</span>
                    <span class="skill-chip"><i class="fas fa-server"></i> Server Architecture</span>
                </div>
                <div class="social-links">
                    <a href="https://instagram.com/firdyfillaa_" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-instagram"></i><span class="tooltip-text">Instagram</span></a>
                    <a href="#" class="social-btn"><i class="fab fa-github"></i><span class="tooltip-text">GitHub</span></a>
                    <a href="#" class="social-btn"><i class="fab fa-linkedin-in"></i><span class="tooltip-text">LinkedIn</span></a>
                    <a href="#" class="social-btn"><i class="fas fa-envelope"></i><span class="tooltip-text">Email</span></a>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider top-emerald">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M0,40 C240,0 480,80 720,40 C960,0 1200,80 1440,40 L1440,80 L0,80 Z"/>
        </svg>
    </div>

    <section class="tech-section" id="teknologi">
        <h2 data-aos="fade-up">Teknologi di Balik Hifzhly</h2>
        <p class="tech-subtitle" data-aos="fade-up" data-aos-delay="80">Ditenagai oleh perangkat yang teruji dan andal</p>
        <div class="tech-categories" data-aos="fade-up" data-aos-delay="120">
            <button class="tech-cat-btn active" data-cat="all">Semua</button>
            <button class="tech-cat-btn" data-cat="frontend">Frontend</button>
            <button class="tech-cat-btn" data-cat="backend">Backend</button>
            <button class="tech-cat-btn" data-cat="infra">Infrastruktur</button>
        </div>
        <div class="tech-grid" id="techGrid">
            <div class="tech-item" data-cat="frontend" data-aos="fade-up" data-aos-delay="50">
                <span class="tech-icon-badge"><i class="fab fa-html5" style="color:#fca5a5;"></i></span>
                <span class="tech-name">HTML5</span>
                <span class="tech-cat-label">Frontend</span>
            </div>
            <div class="tech-item" data-cat="frontend" data-aos="fade-up" data-aos-delay="100">
                <span class="tech-icon-badge"><i class="fab fa-css3-alt" style="color:#93c5fd;"></i></span>
                <span class="tech-name">CSS3</span>
                <span class="tech-cat-label">Frontend</span>
            </div>
            <div class="tech-item" data-cat="frontend" data-aos="fade-up" data-aos-delay="150">
                <span class="tech-icon-badge"><i class="fab fa-js" style="color:#fde68a;"></i></span>
                <span class="tech-name">JavaScript</span>
                <span class="tech-cat-label">Frontend</span>
            </div>
            <div class="tech-item" data-cat="frontend" data-aos="fade-up" data-aos-delay="200">
                <span class="tech-icon-badge"><i class="fab fa-bootstrap" style="color:#c4b5fd;"></i></span>
                <span class="tech-name">Bootstrap 5</span>
                <span class="tech-cat-label">Frontend</span>
            </div>
            <div class="tech-item" data-cat="infra" data-aos="fade-up" data-aos-delay="250">
                <span class="tech-icon-badge"><i class="fas fa-mobile-screen" style="color:#86efac;"></i></span>
                <span class="tech-name">Responsive</span>
                <span class="tech-cat-label">Infra</span>
            </div>
            <div class="tech-item" data-cat="backend" data-aos="fade-up" data-aos-delay="50">
                <span class="tech-icon-badge"><i class="fab fa-php" style="color:#a5b4fc;"></i></span>
                <span class="tech-name">PHP 8</span>
                <span class="tech-cat-label">Backend</span>
            </div>
            <div class="tech-item" data-cat="backend" data-aos="fade-up" data-aos-delay="100">
                <span class="tech-icon-badge"><i class="fas fa-database" style="color:#7dd3fc;"></i></span>
                <span class="tech-name">MySQL</span>
                <span class="tech-cat-label">Backend</span>
            </div>
            <div class="tech-item" data-cat="backend" data-aos="fade-up" data-aos-delay="150">
                <span class="tech-icon-badge"><i class="fas fa-code-branch" style="color:#86efac;"></i></span>
                <span class="tech-name">REST API</span>
                <span class="tech-cat-label">Backend</span>
            </div>
            <div class="tech-item" data-cat="infra" data-aos="fade-up" data-aos-delay="200">
                <span class="tech-icon-badge"><i class="fas fa-server" style="color:var(--gold);"></i></span>
                <span class="tech-name">Al Quran Cloud</span>
                <span class="tech-cat-label">Infra</span>
            </div>
            <div class="tech-item" data-cat="infra" data-aos="fade-up" data-aos-delay="250">
                <span class="tech-icon-badge"><i class="fas fa-shield-halved" style="color:#86efac;"></i></span>
                <span class="tech-name">Session Auth</span>
                <span class="tech-cat-label">Infra</span>
            </div>
            <div class="tech-item" data-cat="infra" data-aos="fade-up" data-aos-delay="300">
                <span class="tech-icon-badge"><i class="fas fa-cloud" style="color:#93c5fd;"></i></span>
                <span class="tech-name">Cloud Hosting</span>
                <span class="tech-cat-label">Infra</span>
            </div>
        </div>
    </section>

    <div class="section-divider top-light">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none">
            <path d="M1440,40 C1200,0 960,80 720,40 C480,0 240,80 0,40 L0,80 L1440,80 Z" style="transform: scaleY(-1); transform-origin: center;"/>
        </svg>
    </div>

    <section class="cta-section">
        <h2 data-aos="fade-up">Siap Memulai Perjalanan Hafalanmu?</h2>
        <p data-aos="fade-up" data-aos-delay="80">Bergabunglah dengan ribuan penghafal Al-Qur'an yang telah merasakan kemudahan murojaah bersama Hifzhly. Gratis, selamanya.</p>
        <a href="register.php" class="btn-cta" data-route><span><i class="fas fa-rocket"></i> Mulai Sekarang</span></a>
    </section>

    <footer>
        <div class="footer-brand">Hifzhly</div>
        <div class="footer-links">
            <a href="index.php">Beranda</a>
            <a href="privacy.php">Kebijakan Privasi</a>
            <a href="terms.php">Syarat &amp; Ketentuan</a>
            <a href="developer.php">Developer</a>
        </div>
        <p>&copy; <?= date('Y') ?> Hifzhly App &mdash; Dibuat dengan <i class="fas fa-heart"></i> untuk umat.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
    <script>
        // Lenis smooth scroll
        const lenis = new Lenis({
            duration: 1.8,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            orientation: 'vertical',
            smoothWheel: true,
            wheelMultiplier: 1,
            touchMultiplier: 1.5,
        });

        lenis.on('scroll', (e) => {
            const pct = (e.progress) * 100;
            document.getElementById('scrollProgress').style.width = pct + '%';
            const navbar = document.getElementById('navbar');
            navbar.classList.toggle('scrolled', e.scroll > 30);
        });

        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 80,
        });

        document.getElementById('scrollProgress').style.width = '0%';

        // Tech category filter
        const grid = document.getElementById('techGrid');
        const allItems = grid.querySelectorAll('.tech-item');
        const catBtns = document.querySelectorAll('.tech-cat-btn');

        function filterTech(category) {
            catBtns.forEach(b => b.classList.remove('active'));
            document.querySelector(`.tech-cat-btn[data-cat="${category}"]`).classList.add('active');

            allItems.forEach((item, index) => {
                const cat = item.dataset.cat;
                const match = category === 'all' || cat === category;
                if (match) {
                    item.classList.remove('hidden');
                    item.style.position = 'relative';
                    item.style.transitionDelay = (index * 40) + 'ms';
                } else {
                    item.classList.add('hidden');
                    item.style.position = 'absolute';
                    item.style.transitionDelay = '0ms';
                }
            });
        }

        catBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterTech(btn.dataset.cat);
            });
        });

        // SPA-like route transition
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

        // Floating particles
        const canvas = document.getElementById('particles-canvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        let animId;

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        class Particle {
            constructor() { this.reset(); }
            reset() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2.2 + 0.6;
                this.speedX = (Math.random() - 0.5) * 0.35;
                this.speedY = (Math.random() - 0.5) * 0.35;
                this.opacity = Math.random() * 0.4 + 0.1;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x < 0 || this.x > canvas.width || this.y < 0 || this.y > canvas.height) this.reset();
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(43, 171, 130, ${this.opacity})`;
                ctx.fill();
            }
        }

        const pCount = Math.min(60, Math.floor(window.innerWidth / 20));
        for (let i = 0; i < pCount; i++) particles.push(new Particle());

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => { p.update(); p.draw(); });
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(43, 171, 130, ${0.06 * (1 - dist / 120)})`;
                        ctx.lineWidth = 0.5;
                        ctx.stroke();
                    }
                }
            }
            animId = requestAnimationFrame(drawParticles);
        }
        drawParticles();

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) { cancelAnimationFrame(animId); }
            else { drawParticles(); }
        });
    </script>

</body>
</html>
