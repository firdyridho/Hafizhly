<?php
// Mengambil nama file yang sedang dibuka (contoh: 'dashboard.php' atau 'mutabaah.php')
// Ini berguna agar ikon yang aktif bisa menyala otomatis
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Font Awesome (dihapus komentarnya biar ikon pasti muncul, aman walau ke-load 2x di halaman yang sudah punya) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
    :root {
        --nav-primary: #059669;
        --nav-primary-light: #34d399;
        --nav-primary-dark: #04785a;
        --nav-gold: #c9a227;
        --nav-muted: #9ca3af;
        --nav-glow: rgba(5, 150, 105, 0.35);
    }

    /* =========================================
       TOP PROGRESS BAR (AJAX loading indicator)
       ========================================= */
    #ajax-progress {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        width: 0%;
        z-index: 2000;
        background: linear-gradient(90deg, var(--nav-primary-light), var(--nav-primary), var(--nav-gold));
        box-shadow: 0 0 10px var(--nav-glow), 0 0 4px var(--nav-gold);
        opacity: 0;
        transition: width 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
        border-radius: 0 3px 3px 0;
    }

    #ajax-progress.active {
        opacity: 1;
    }

    /* =========================================
       PAGE CONTENT TRANSITION
       ========================================= */
    #ajax-content {
        animation: contentIn 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }

    #ajax-content.ajax-fade-out {
        opacity: 0;
        transform: translateY(6px) scale(0.995);
        filter: blur(2px);
        transition: opacity 0.18s ease, transform 0.18s ease, filter 0.18s ease;
        pointer-events: none;
    }

    #ajax-content.ajax-fade-in {
        animation: contentIn 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes contentIn {
        from {
            opacity: 0;
            transform: translateY(10px);
            filter: blur(3px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }
    }

    body.ajax-loading {
        cursor: progress;
    }

    /* =========================================
       DEFAULT: MOBILE FIRST (BOTTOM NAVIGATION)
       ========================================= */
    body {
        padding-bottom: calc(78px + env(safe-area-inset-bottom));
    }

    .app-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.78);
        backdrop-filter: blur(22px) saturate(160%);
        -webkit-backdrop-filter: blur(22px) saturate(160%);
        box-shadow: 0 -10px 34px rgba(15, 23, 42, 0.1), 0 -1px 0 rgba(255, 255, 255, 0.6) inset;
        border-top: 1px solid rgba(255, 255, 255, 0.5);
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 6px calc(12px + env(safe-area-inset-bottom));
        z-index: 1000;
        animation: navRise 0.55s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .app-nav::before {
        content: '';
        position: absolute;
        top: 0;
        left: 14%;
        right: 14%;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--nav-gold), transparent);
        opacity: 0.55;
    }

    .app-nav::after {
        content: '';
        position: absolute;
        top: -16px;
        left: 50%;
        transform: translateX(-50%);
        width: 66px;
        height: 36px;
        background: transparent;
        border-radius: 50%;
        box-shadow: 0 0 0 1.5px rgba(255,255,255,0.5), 0 -4px 12px rgba(0,0,0,0.04);
        z-index: 0;
        pointer-events: none;
    }

    .app-nav {
        -webkit-mask-image: radial-gradient(circle at 50% -8px, transparent 31px, black 31px);
        mask-image: radial-gradient(circle at 50% -8px, transparent 31px, black 31px);
    }

    @keyframes navRise {
        from {
            transform: translateY(110%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Sembunyikan brand di mobile (menu ada di bawah) */
    .nav-brand {
        display: none;
    }

    .nav-links {
        display: flex;
        width: 100%;
        justify-content: space-around;
        align-items: center;
    }

    .nav-item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        text-decoration: none;
        color: var(--nav-muted);
        font-size: 0.68rem;
        font-weight: 600;
        gap: 3px;
        min-width: 56px;
        padding: 4px 0;
        -webkit-tap-highlight-color: transparent;
    }

    .nav-icon-wrap {
        position: relative;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), background 0.35s ease, box-shadow 0.35s ease;
    }

    /* Cincin halo lembut di belakang ikon aktif */
    .nav-icon-wrap::after {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: 18px;
        background: radial-gradient(circle, var(--nav-glow), transparent 70%);
        opacity: 0;
        transform: scale(0.6);
        transition: opacity 0.4s ease, transform 0.4s ease;
        z-index: -1;
    }

    .nav-icon {
        font-size: 1.2rem;
        color: var(--nav-muted);
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .nav-text {
        transition: color 0.3s ease, opacity 0.3s ease;
        opacity: 0.75;
        letter-spacing: 0.1px;
    }

    /* State aktif: ikon "melayang" dalam bubble gradient */
    .nav-item.active .nav-icon-wrap {
        background: linear-gradient(135deg, var(--nav-primary-light), var(--nav-primary));
        transform: translateY(-14px) scale(1.06);
        box-shadow: 0 12px 24px var(--nav-glow), 0 2px 0 rgba(255, 255, 255, 0.4) inset;
    }

    .nav-item.active .nav-icon-wrap::after {
        opacity: 1;
        transform: scale(1);
    }

    .nav-item.active .nav-icon {
        color: #fff;
        transform: scale(1.05);
    }

    .nav-item.active .nav-text {
        color: var(--nav-primary-dark);
        font-weight: 700;
        opacity: 1;
    }

    /* Titik indikator kecil di bawah item aktif (mobile) */
    .nav-item.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: var(--nav-gold);
        box-shadow: 0 0 6px var(--nav-gold);
    }

    .nav-item:not(.active):active .nav-icon-wrap {
        background: rgba(5, 150, 105, 0.08);
        transform: scale(0.9);
    }

    /* Ripple halus saat item ditekan */
    .nav-item .nav-icon-wrap {
        overflow: hidden;
    }

    .nav-item .nav-ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(5, 150, 105, 0.25);
        transform: scale(0);
        animation: rippleAnim 0.55s ease-out;
        pointer-events: none;
    }

    @keyframes rippleAnim {
        to {
            transform: scale(2.6);
            opacity: 0;
        }
    }

    /* Beranda di tengah & spesial (mobile) */
    .nav-item:nth-child(1) { order: 3; }
    .nav-item:nth-child(2) { order: 1; }
    .nav-item:nth-child(3) { order: 2; }
    .nav-item:nth-child(4) { order: 4; }
    .nav-item:nth-child(5) { order: 5; }

    .nav-item.nav-home {
        z-index: 2;
    }

    .nav-item.nav-home .nav-icon-wrap {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: linear-gradient(145deg, #6ee7b7 0%, #059669 40%, #047857 100%);
        box-shadow:
            0 10px 28px rgba(5, 150, 105, 0.45),
            inset 0 -6px 8px rgba(0,0,0,0.15),
            inset 0 4px 6px rgba(255,255,255,0.25);
        transform: translateY(-14px);
        transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .nav-item.nav-home .nav-icon {
        color: #fff;
        font-size: 1.4rem;
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.15));
    }

    .nav-item.nav-home .nav-text {
        color: var(--nav-primary-dark);
        font-weight: 800;
        opacity: 1;
        margin-top: -4px;
        font-size: 0.65rem;
    }

    .nav-item.nav-home:active .nav-icon-wrap {
        transform: translateY(-8px) scale(0.92);
    }

    .nav-item.nav-home.active .nav-icon-wrap {
        transform: translateY(-20px) scale(1.06);
        box-shadow:
            0 16px 35px rgba(5, 150, 105, 0.55),
            inset 0 -6px 8px rgba(0,0,0,0.18),
            inset 0 4px 6px rgba(255,255,255,0.3);
    }

    .nav-item.nav-home.active::after {
        display: none;
    }

    /* Item logout diberi aksen merah halus saat disentuh */
    .nav-item.nav-logout:active .nav-icon-wrap {
        background: rgba(239, 68, 68, 0.1);
    }

    .nav-item.nav-logout:active .nav-icon {
        color: #ef4444;
    }

    /* =========================================
       RESPONSIVE: DESKTOP (TOP HEADER)
       ========================================= */
    @media (min-width: 768px) {
        body {
            padding-bottom: 0 !important;
            padding-top: 82px !important;
        }

        .app-nav {
            top: 0;
            bottom: auto;
            left: 0;
            right: 0;
            padding: 0 40px;
            height: 74px;
            justify-content: space-between;
            box-shadow: 0 4px 30px rgba(15, 23, 42, 0.06);
            border-top: none;
            border-bottom: 1px solid rgba(15, 23, 42, 0.05);
            animation: navDrop 0.55s cubic-bezier(0.16, 1, 0.3, 1);
            -webkit-mask-image: none;
            mask-image: none;
        }

        .app-nav::before {
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            opacity: 1;
            background: linear-gradient(90deg, transparent, var(--nav-gold) 20%, var(--nav-primary) 50%, var(--nav-gold) 80%, transparent);
            opacity: 0.35;
        }

        .app-nav::after {
            display: none;
        }

        @keyframes navDrop {
            from {
                transform: translateY(-110%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .nav-brand:hover {
            transform: translateY(-1px);
        }

        .nav-brand span.dot {
            color: var(--nav-gold);
            text-shadow: 0 0 12px rgba(201, 162, 39, 0.5);
        }

        .nav-links {
            width: auto;
            gap: 6px;
        }

        .nav-item {
            position: relative;
            flex-direction: row;
            font-size: 0.92rem;
            gap: 9px;
            min-width: auto;
            padding: 9px 18px;
            border-radius: 30px;
            color: #475569;
            overflow: hidden;
            transition: background 0.35s cubic-bezier(0.16, 1, 0.3, 1), color 0.3s ease, transform 0.25s ease;
        }

        .nav-item:not(.active):hover {
            transform: translateY(-1px);
        }

        .nav-icon-wrap {
            width: auto;
            height: auto;
            border-radius: 0;
            background: none !important;
            box-shadow: none !important;
            transform: none !important;
        }

        .nav-icon-wrap::after {
            display: none;
        }

        .nav-icon {
            font-size: 1rem;
        }

        .nav-item .nav-text {
            opacity: 1;
        }

        .nav-item:hover {
            background: #f1f5f9;
            color: var(--nav-primary-dark);
        }

        .nav-item:hover .nav-icon {
            color: var(--nav-primary-dark);
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(52, 211, 153, 0.16), rgba(5, 150, 105, 0.12));
            color: var(--nav-primary-dark);
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.5) inset, 0 4px 14px rgba(5, 150, 105, 0.12);
        }

        .nav-item.active::after {
            display: none;
        }

        .nav-item.active .nav-icon {
            color: var(--nav-primary-dark);
            transform: none;
        }

        .nav-item.active .nav-text {
            font-weight: 700;
        }

        .nav-item:nth-child(1),
        .nav-item:nth-child(2),
        .nav-item:nth-child(3),
        .nav-item:nth-child(4),
        .nav-item:nth-child(5) { order: unset; }

        .nav-item.nav-home {
            z-index: auto;
        }

        .nav-item.nav-home .nav-icon-wrap {
            width: auto;
            height: auto;
            border-radius: 0;
            background: none;
            box-shadow: none;
            transform: none;
        }

        .nav-item.nav-home .nav-icon {
            color: inherit;
            font-size: 1rem;
            filter: none;
        }

        .nav-item.nav-home .nav-text {
            color: inherit;
            font-weight: 600;
            opacity: 0.75;
            margin-top: 0;
            font-size: 0.92rem;
        }

        .nav-item.nav-home.active .nav-icon-wrap {
            transform: none;
            box-shadow: none;
        }

        .nav-item.nav-home.active .nav-text {
            font-weight: 700;
            opacity: 1;
        }

        .nav-item.nav-logout {
            margin-left: 8px;
            border-left: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 0 30px 30px 0;
            padding-left: 20px;
        }

        .nav-item.nav-logout:hover {
            background: rgba(239, 68, 68, 0.08);
            color: #ef4444;
        }

        .nav-item.nav-logout:hover .nav-icon {
            color: #ef4444;
        }
    }

    /* =========================================
       MODAL LOGOUT PREMIUM
       ========================================= */
    .logout-overlay {
        position: fixed;
        inset: 0;
        background: rgba(8, 15, 13, 0.55);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 3000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 20px;
    }

    .logout-overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    .logout-card {
        position: relative;
        width: 100%;
        max-width: 340px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(24px) saturate(180%);
        -webkit-backdrop-filter: blur(24px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 22px;
        padding: 30px 26px 24px;
        text-align: center;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.4) inset;
        transform: translateY(24px) scale(0.94);
        opacity: 0;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.35s ease;
    }

    .logout-overlay.show .logout-card {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .logout-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 16%;
        right: 16%;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--nav-gold), transparent);
        opacity: 0.6;
    }

    .logout-icon {
        width: 62px;
        height: 62px;
        margin: 0 auto 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.16), rgba(239, 68, 68, 0.06));
        box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.06);
        animation: iconPulse 2.2s ease-in-out infinite;
    }

    @keyframes iconPulse {

        0%,
        100% {
            box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.06);
        }

        50% {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0.1);
        }
    }

    .logout-icon i {
        font-size: 1.5rem;
        color: #ef4444;
    }

    .logout-title {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
        letter-spacing: -0.2px;
    }

    .logout-desc {
        font-size: 0.86rem;
        color: #64748b;
        line-height: 1.5;
        margin: 0 0 24px;
    }

    .logout-actions {
        display: flex;
        gap: 10px;
    }

    .logout-btn {
        flex: 1;
        border: none;
        border-radius: 14px;
        padding: 12px 14px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease, background 0.25s ease;
        -webkit-tap-highlight-color: transparent;
    }

    .logout-btn:active {
        transform: scale(0.96);
    }

    .logout-btn-cancel {
        background: #f1f5f9;
        color: #334155;
    }

    .logout-btn-cancel:hover {
        background: #e2e8f0;
    }

    .logout-btn-confirm {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .logout-btn-confirm:hover {
        box-shadow: 0 12px 24px rgba(239, 68, 68, 0.4);
    }

    .logout-btn-confirm .fa-spinner {
        display: none;
    }

    .logout-btn-confirm.loading .fa-spinner {
        display: inline-block;
        animation: spin 0.7s linear infinite;
    }

    .logout-btn-confirm.loading .btn-label {
        display: none;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .app-nav,
        .nav-icon-wrap,
        #ajax-content,
        .logout-card,
        .logout-overlay {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<div id="ajax-progress"></div>

<nav class="app-nav">
    <a href="dashboard.php" class="nav-brand" data-ajax-link>
        Hifzhly<span class="dot">.</span>
    </a>

    <div class="nav-links">
        <a href="dashboard.php" class="nav-item nav-home <?= $current_page == 'dashboard.php' ? 'active' : '' ?>" data-ajax-link>
            <span class="nav-icon-wrap"><i class="fa-solid fa-house nav-icon"></i></span>
            <span class="nav-text">Beranda</span>
        </a>
        <a href="alquran.php" class="nav-item <?= $current_page == 'alquran.php' ? 'active' : '' ?>" data-ajax-link>
            <span class="nav-icon-wrap"><i class="fa-solid fa-book-open-reader nav-icon"></i></span>
            <span class="nav-text">Qur'an</span>
        </a>
        <a href="mutabaah.php" class="nav-item <?= $current_page == 'mutabaah.php' ? 'active' : '' ?>" data-ajax-link>
            <span class="nav-icon-wrap"><i class="fa-solid fa-chart-simple nav-icon"></i></span>
            <span class="nav-text">Mutabaah</span>
        </a>
        <a href="hadis.php" class="nav-item <?= $current_page == 'hadis.php' ? 'active' : '' ?>" data-ajax-link>
            <span class="nav-icon-wrap"><i class="fa-solid fa-book-quran nav-icon"></i></span>
            <span class="nav-text">Hadis</span>
        </a>
        <a href="../logout.php" class="nav-item nav-logout" id="logout-trigger">
            <span class="nav-icon-wrap"><i class="fa-solid fa-right-from-bracket nav-icon"></i></span>
            <span class="nav-text">Keluar</span>
        </a>
    </div>
</nav>

<!-- Modal konfirmasi logout premium -->
<div class="logout-overlay" id="logout-overlay">
    <div class="logout-card">
        <div class="logout-icon"><i class="fa-solid fa-right-from-bracket"></i></div>
        <h3 class="logout-title">Yakin ingin keluar?</h3>
        <p class="logout-desc">Sesi kamu akan diakhiri dan kamu perlu masuk kembali untuk melanjutkan hafalan.</p>
        <div class="logout-actions">
            <button type="button" class="logout-btn logout-btn-cancel" id="logout-cancel">Batal</button>
            <button type="button" class="logout-btn logout-btn-confirm" id="logout-confirm">
                <i class="fa-solid fa-spinner"></i>
                <span class="btn-label">Ya, Keluar</span>
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        /* =========================================
           MODAL LOGOUT
           ========================================= */
        const logoutTrigger = document.getElementById('logout-trigger');
        const logoutOverlay = document.getElementById('logout-overlay');
        const logoutCancel = document.getElementById('logout-cancel');
        const logoutConfirm = document.getElementById('logout-confirm');
        const logoutHref = logoutTrigger ? logoutTrigger.getAttribute('href') : '../logout.php';

        function openLogoutModal() {
            logoutOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeLogoutModal() {
            logoutOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (logoutTrigger) {
            logoutTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                openLogoutModal();
            });
        }

        if (logoutCancel) {
            logoutCancel.addEventListener('click', closeLogoutModal);
        }

        if (logoutOverlay) {
            logoutOverlay.addEventListener('click', function(e) {
                if (e.target === logoutOverlay) closeLogoutModal();
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && logoutOverlay.classList.contains('show')) closeLogoutModal();
        });

        if (logoutConfirm) {
            logoutConfirm.addEventListener('click', function() {
                logoutConfirm.classList.add('loading');
                logoutConfirm.disabled = true;
                setTimeout(function() {
                    window.location.href = logoutHref;
                }, 350);
            });
        }

        /* =========================================
           AJAX PAGE TRANSITION (SPA-like)
           ========================================= */
        const CONTENT_SELECTOR = '#ajax-content';
        const progressBar = document.getElementById('ajax-progress');
        let progressTimer = null;
        let isNavigating = false;

        function startProgress() {
            clearTimeout(progressTimer);
            progressBar.style.transition = 'none';
            progressBar.style.width = '0%';
            // force reflow so the transition below actually animates
            void progressBar.offsetWidth;
            progressBar.style.transition = '';
            progressBar.classList.add('active');
            requestAnimationFrame(function() {
                progressBar.style.width = '78%';
            });
        }

        function finishProgress() {
            progressBar.style.width = '100%';
            progressTimer = setTimeout(function() {
                progressBar.classList.remove('active');
                progressBar.style.width = '0%';
            }, 300);
        }

        function wait(ms) {
            return new Promise(function(resolve) {
                setTimeout(resolve, ms);
            });
        }

        function updateActiveNav(url) {
            const page = url.split('/').pop().split('?')[0].split('#')[0] || 'dashboard.php';
            document.querySelectorAll('.nav-item[data-ajax-link]').forEach(function(item) {
                const itemPage = item.getAttribute('href').split('/').pop();
                item.classList.toggle('active', itemPage === page || (page === '' && itemPage === 'dashboard.php'));
            });
        }

        function reAttachScripts(container) {
            container.querySelectorAll('script').forEach(function(oldScript) {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(function(attr) {
                    newScript.setAttribute(attr.name, attr.value);
                });
                newScript.textContent = oldScript.textContent;
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });
        }

        async function navigateTo(url, pushHistory) {
            if (isNavigating) return;
            const content = document.querySelector(CONTENT_SELECTOR);

            // Kalau halaman belum punya wrapper #ajax-content, fallback ke navigasi normal
            if (!content) {
                window.location.href = url;
                return;
            }

            isNavigating = true;
            document.body.classList.add('ajax-loading');
            startProgress();
            content.classList.add('ajax-fade-out');

            try {
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error('Gagal memuat halaman');
                const html = await res.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newContent = doc.querySelector(CONTENT_SELECTOR);

                if (!newContent) {
                    window.location.href = url;
                    return;
                }

                await wait(180);

                document.title = doc.title || document.title;
                content.innerHTML = newContent.innerHTML;
                reAttachScripts(content);

                content.classList.remove('ajax-fade-out');
                content.classList.add('ajax-fade-in');
                setTimeout(function() {
                    content.classList.remove('ajax-fade-in');
                }, 450);

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                if (pushHistory) history.pushState({
                    ajax: true
                }, '', url);
                updateActiveNav(url);
            } catch (err) {
                window.location.href = url;
                return;
            } finally {
                document.body.classList.remove('ajax-loading');
                finishProgress();
                isNavigating = false;
            }
        }

        document.querySelectorAll('[data-ajax-link]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || link.target === '_blank') return;
                e.preventDefault();
                if (link.classList.contains('active') && !document.querySelector(CONTENT_SELECTOR + ' .force-reload')) {
                    return; // sudah di halaman ini, tidak perlu fetch ulang
                }
                navigateTo(href, true);
            });
        });

        window.addEventListener('popstate', function() {
            navigateTo(location.pathname + location.search, false);
        });
    })();
</script>