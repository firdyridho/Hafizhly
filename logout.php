<?php
session_start();
session_unset();
session_destroy();
// Sesi sudah dihancurkan di sini juga (server-side), jadi aman —
// halaman di bawah cuma nampilin animasi sebelum redirect ke index.php
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Sampai Jumpa · Hifzhly</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --nav-primary: #059669;
            --nav-primary-light: #34d399;
            --nav-primary-dark: #04785a;
            --nav-gold: #c9a227;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, sans-serif;
            background: radial-gradient(circle at 50% 20%, #0a2e24 0%, #062018 45%, #030f0b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: 20px;
        }

        /* Orb cahaya ambient yang mengambang pelan */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            pointer-events: none;
        }

        .orb-1 {
            width: 280px;
            height: 280px;
            background: var(--nav-primary-light);
            top: -60px;
            left: -60px;
            animation: floatOrb 9s ease-in-out infinite;
        }

        .orb-2 {
            width: 220px;
            height: 220px;
            background: var(--nav-gold);
            bottom: -40px;
            right: -40px;
            animation: floatOrb 11s ease-in-out infinite reverse;
        }

        .orb-3 {
            width: 160px;
            height: 160px;
            background: var(--nav-primary);
            bottom: 10%;
            left: 8%;
            animation: floatOrb 7.5s ease-in-out infinite;
            animation-delay: -3s;
        }

        @keyframes floatOrb {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(24px, -20px) scale(1.08);
            }
        }

        /* Partikel kecil melayang naik */
        .particle {
            position: absolute;
            bottom: -10px;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--nav-gold);
            opacity: 0;
            animation: riseParticle linear infinite;
        }

        @keyframes riseParticle {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 0.8;
            }

            90% {
                opacity: 0.4;
            }

            100% {
                transform: translateY(-100vh) translateX(var(--drift, 20px));
                opacity: 0;
            }
        }

        .card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 380px;
            text-align: center;
            padding: 46px 34px 38px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(26px) saturate(160%);
            -webkit-backdrop-filter: blur(26px) saturate(160%);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.04) inset;
            opacity: 0;
            transform: translateY(26px) scale(0.96);
            animation: cardIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
        }

        @keyframes cardIn {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 18%;
            right: 18%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--nav-gold), transparent);
            opacity: 0.7;
        }

        /* Lingkaran ikon + ring animasi check */
        .icon-stage {
            position: relative;
            width: 108px;
            height: 108px;
            margin: 0 auto 26px;
        }

        .ring-pulse {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 1.5px solid var(--nav-primary-light);
            opacity: 0;
            animation: ringPulse 2.4s cubic-bezier(0.16, 1, 0.3, 1) infinite;
        }

        .ring-pulse.delay {
            animation-delay: 0.8s;
        }

        @keyframes ringPulse {
            0% {
                transform: scale(0.7);
                opacity: 0;
            }

            25% {
                opacity: 0.6;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .icon-circle {
            position: absolute;
            inset: 12px;
            border-radius: 50%;
            background: linear-gradient(150deg, var(--nav-primary-light), var(--nav-primary) 60%, var(--nav-primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 18px 40px rgba(5, 150, 105, 0.45), 0 0 0 6px rgba(52, 211, 153, 0.08);
        }

        .icon-circle svg {
            width: 46px;
            height: 46px;
        }

        .icon-circle .check-path {
            fill: none;
            stroke: #fff;
            stroke-width: 3.5;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 40;
            stroke-dashoffset: 40;
            animation: drawCheck 0.5s cubic-bezier(0.65, 0, 0.35, 1) 0.9s forwards;
        }

        @keyframes drawCheck {
            to {
                stroke-dashoffset: 0;
            }
        }

        .title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
            margin-bottom: 8px;
            opacity: 0;
            animation: fadeUp 0.6s ease 0.5s forwards;
        }

        .subtitle {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.6;
            margin-bottom: 30px;
            opacity: 0;
            animation: fadeUp 0.6s ease 0.65s forwards;
        }

        .subtitle b {
            color: var(--nav-gold);
            font-weight: 700;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Progress bar redirect */
        .redirect-track {
            position: relative;
            width: 100%;
            height: 4px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
            overflow: hidden;
            margin-bottom: 14px;
            opacity: 0;
            animation: fadeUp 0.6s ease 0.8s forwards;
        }

        .redirect-fill {
            position: absolute;
            inset: 0;
            width: 0%;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--nav-primary-light), var(--nav-gold));
            box-shadow: 0 0 10px rgba(52, 211, 153, 0.6);
            animation: fillBar 2.2s cubic-bezier(0.65, 0, 0.35, 1) 0.9s forwards;
        }

        @keyframes fillBar {
            to {
                width: 100%;
            }
        }

        .redirect-label {
            font-size: 0.76rem;
            color: rgba(255, 255, 255, 0.45);
            opacity: 0;
            animation: fadeUp 0.6s ease 0.85s forwards;
        }

        .manual-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--nav-primary-light);
            text-decoration: none;
            opacity: 0;
            animation: fadeUp 0.6s ease 1s forwards;
            transition: color 0.25s ease, gap 0.25s ease;
        }

        .manual-link:hover {
            color: var(--nav-gold);
            gap: 9px;
        }

        @media (prefers-reduced-motion: reduce) {

            .orb,
            .particle,
            .ring-pulse,
            .card {
                animation-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div id="particles"></div>

    <div class="card">
        <div class="icon-stage">
            <span class="ring-pulse"></span>
            <span class="ring-pulse delay"></span>
            <div class="icon-circle">
                <svg viewBox="0 0 24 24">
                    <path class="check-path" d="M4 12.5L9.5 18L20 6" />
                </svg>
            </div>
        </div>

        <h1 class="title">Sampai Jumpa!</h1>
        <p class="subtitle">Kamu sudah berhasil keluar. Sampai ketemu lagi di<br><b>hafalan berikutnya</b> ✨</p>

        <div class="redirect-track">
            <div class="redirect-fill"></div>
        </div>
        <div class="redirect-label">Mengalihkan ke halaman masuk...</div>

        <a href="index.php" class="manual-link">
            Kembali sekarang <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <script>
        // Partikel emas kecil melayang naik dari bawah
        (function() {
            const container = document.getElementById('particles');
            const total = 16;
            for (let i = 0; i < total; i++) {
                const p = document.createElement('span');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + 'vw';
                p.style.setProperty('--drift', (Math.random() * 60 - 30) + 'px');
                p.style.animationDuration = (5 + Math.random() * 5) + 's';
                p.style.animationDelay = (Math.random() * 5) + 's';
                container.appendChild(p);
            }
        })();

        // Redirect otomatis setelah animasi selesai
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 2300);
    </script>
</body>

</html>