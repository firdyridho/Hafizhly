<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - Hafizhly</title>
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --white: #ffffff;
            --paper: #f5faf7;
            --ink: #0b1f17;
            --ink-soft: #46605a;
            --muted: #7a9188;
            --green-50: #eaf6f0;
            --green-100: #d7ede1;
            --green-500: #0e9463;
            --green-600: #0a7a51;
            --green-700: #085f40;
            --green-900: #063f2b;
            --line: #dbeae1;
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
            font-family: 'Inter', sans-serif;
            background: var(--paper);
            color: var(--ink);
            line-height: 1.75;
            -webkit-font-smoothing: antialiased;
        }

        ::selection {
            background: var(--green-100);
            color: var(--green-900);
        }

        /* ---------- Header ---------- */
        .header {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 16px 24px;
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .back-btn {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--green-700);
            text-decoration: none;
            font-size: 1rem;
            border: 1px solid var(--line);
            flex-shrink: 0;
            transition: 0.2s ease;
        }

        .back-btn:hover {
            background: var(--green-50);
            border-color: var(--green-500);
            transform: translateX(-2px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand img {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            object-fit: cover;
            background: var(--green-50);
        }

        .brand-name {
            font-family: 'Amiri', serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--green-700);
            letter-spacing: 0.2px;
        }

        .header-spacer {
            flex: 1;
        }

        .header-pill {
            display: none;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--green-700);
            background: var(--green-50);
            border: 1px solid var(--green-100);
            padding: 6px 14px;
            border-radius: 100px;
        }

        @media (min-width: 640px) {
            .header-pill {
                display: inline-flex;
            }
        }

        /* ---------- Hero ---------- */
        .hero {
            position: relative;
            overflow: hidden;
            padding: 72px 24px 56px;
            text-align: center;
            background: linear-gradient(180deg, var(--green-50) 0%, var(--paper) 100%);
            border-bottom: 1px solid var(--line);
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.5;
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            max-width: 720px;
            margin: 0 auto;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--green-600);
            text-transform: uppercase;
            letter-spacing: 1.4px;
            margin-bottom: 20px;
        }

        .eyebrow .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green-500);
        }

        h1 {
            font-family: 'Amiri', serif;
            font-weight: 700;
            font-size: clamp(2.1rem, 5vw, 3rem);
            color: var(--ink);
            margin-bottom: 16px;
            letter-spacing: 0.2px;
        }

        .hero-sub {
            color: var(--ink-soft);
            font-size: 1.05rem;
            max-width: 520px;
            margin: 0 auto 28px;
        }

        .updated-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            border: 1px solid var(--line);
            padding: 9px 18px;
            border-radius: 100px;
            font-size: 0.85rem;
            color: var(--ink-soft);
            box-shadow: 0 1px 2px rgba(11, 31, 23, 0.04);
        }

        .updated-pill i {
            color: var(--green-500);
        }

        .updated-pill strong {
            color: var(--ink);
            font-weight: 600;
        }

        /* ---------- Layout ---------- */
        .layout {
            max-width: 980px;
            margin: 0 auto;
            padding: 56px 24px 90px;
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 48px;
            align-items: start;
        }

        @media (max-width: 860px) {
            .layout {
                grid-template-columns: 1fr;
                padding-top: 28px;
            }
        }

        /* ---------- TOC ---------- */
        .toc-wrap {
            position: sticky;
            top: 90px;
        }

        .toc-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 14px;
            padding-left: 2px;
        }

        .toc {
            list-style: none;
            border-left: 2px solid var(--line);
        }

        .toc li {
            margin: 0;
        }

        .toc a {
            display: flex;
            align-items: baseline;
            gap: 10px;
            padding: 9px 0 9px 16px;
            margin-left: -2px;
            border-left: 2px solid transparent;
            text-decoration: none;
            color: var(--ink-soft);
            font-size: 0.92rem;
            font-weight: 500;
            transition: 0.15s ease;
        }

        .toc a .num {
            font-family: 'Amiri', serif;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .toc a:hover {
            color: var(--green-700);
        }

        .toc a.active {
            color: var(--green-700);
            border-left-color: var(--green-600);
            font-weight: 600;
        }

        .toc a.active .num {
            color: var(--green-600);
        }

        @media (max-width: 860px) {
            .toc-wrap {
                position: sticky;
                top: 66px;
                background: var(--paper);
                z-index: 50;
                padding: 12px 0;
                margin: 0 -24px;
                padding-left: 24px;
                padding-right: 24px;
                border-bottom: 1px solid var(--line);
            }

            .toc-label {
                display: none;
            }

            .toc {
                border-left: none;
                display: flex;
                gap: 8px;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .toc::-webkit-scrollbar {
                display: none;
            }

            .toc li {
                flex-shrink: 0;
            }

            .toc a {
                border: 1px solid var(--line);
                border-radius: 100px;
                padding: 7px 16px;
                margin: 0;
                white-space: nowrap;
            }

            .toc a.active {
                background: var(--green-600);
                border-color: var(--green-600);
                color: var(--white);
            }

            .toc a.active .num {
                color: var(--green-100);
            }
        }

        /* ---------- Intro note ---------- */
        .intro-note {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 22px 26px;
            margin-bottom: 32px;
            font-size: 0.98rem;
            color: var(--ink-soft);
            display: flex;
            gap: 16px;
        }

        .intro-note i {
            color: var(--green-500);
            font-size: 1.2rem;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .intro-note strong {
            color: var(--ink);
        }

        /* ---------- Section cards ---------- */
        section.term {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 32px;
            margin-bottom: 22px;
            scroll-margin-top: 100px;
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 0.55s ease, transform 0.55s ease;
        }

        section.term.in-view {
            opacity: 1;
            transform: translateY(0);
        }

        .term-head {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .term-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            background: var(--green-50);
            color: var(--green-600);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .term-head-text .num-label {
            font-family: 'Amiri', serif;
            font-size: 0.85rem;
            color: var(--green-500);
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        h2 {
            font-family: 'Amiri', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--ink);
        }

        p,
        li {
            font-size: 0.98rem;
            color: var(--ink-soft);
            margin-bottom: 13px;
        }

        p:last-child,
        li:last-child {
            margin-bottom: 0;
        }

        ul {
            list-style: none;
            margin: 0;
        }

        ul li {
            position: relative;
            padding-left: 24px;
        }

        ul li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 9px;
            width: 7px;
            height: 7px;
            border-radius: 2px;
            background: var(--green-500);
            transform: rotate(45deg);
        }

        strong {
            color: var(--ink);
            font-weight: 600;
        }

        /* ---------- Footer ---------- */
        .footer-note {
            text-align: center;
            padding: 40px 24px 60px;
            color: var(--muted);
            font-size: 0.85rem;
        }

        .footer-note .brand-name {
            font-size: 1rem;
        }

        .footer-note .fa-seedling {
            color: var(--green-500);
        }
    </style>
</head>

<body>

    <div class="header">
        <a href="index.php" class="back-btn" aria-label="Kembali"><i class="fas fa-arrow-left"></i></a>
        <div class="brand">
            <img src="assets/icon/logo.png" alt="Logo Hafizhly" onerror="this.style.display='none'">
            <span class="brand-name">Hafizhly</span>
        </div>
        <div class="header-spacer"></div>
        <div class="header-pill"><i class="fas fa-shield-halved"></i> Dokumen resmi</div>
    </div>

    <section class="hero">
        <svg class="hero-pattern" viewBox="0 0 800 300" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="star8" width="80" height="80" patternUnits="userSpaceOnUse">
                    <g fill="none" stroke="#0a7a51" stroke-width="1">
                        <path d="M40 6 L52 28 L74 28 L57 43 L64 65 L40 51 L16 65 L23 43 L6 28 L28 28 Z" opacity="0.25"></path>
                    </g>
                </pattern>
            </defs>
            <rect width="800" height="300" fill="url(#star8)"></rect>
        </svg>
        <div class="hero-inner">
            <div class="eyebrow"><span class="dot"></span> Legal &middot; Hafizhly</div>
            <h1>Syarat dan Ketentuan</h1>
            <p class="hero-sub">Panduan penggunaan aplikasi Hafizhly agar ibadah, hafalan, dan aktivitasmu di dalamnya berjalan nyaman, aman, dan sesuai kesepakatan bersama.</p>
            <div class="updated-pill">
                <i class="fas fa-clock-rotate-left"></i>
                Terakhir diperbarui: <strong><?= date('d F Y') ?></strong>
            </div>
        </div>
    </section>

    <div class="layout">
        <aside class="toc-wrap">
            <div class="toc-label">Daftar isi</div>
            <ul class="toc" id="toc">
                <li><a href="#s1" class="active"><span class="num">01</span> Penggunaan layanan</a></li>
                <li><a href="#s2"><span class="num">02</span> Hak cipta & konten</a></li>
                <li><a href="#s3"><span class="num">03</span> Batasan tanggung jawab</a></li>
                <li><a href="#s4"><span class="num">04</span> Penghentian akses</a></li>
                <li><a href="#s5"><span class="num">05</span> Hukum yang berlaku</a></li>
            </ul>
        </aside>

        <main>
            <div class="intro-note">
                <i class="fas fa-book-quran"></i>
                <div>Dengan mengakses dan menggunakan aplikasi <strong>Hafizhly</strong>, Anda menyetujui untuk terikat oleh Syarat dan Ketentuan ini. Jika Anda tidak setuju dengan bagian mana pun dari persyaratan ini, Anda tidak diperkenankan menggunakan aplikasi kami.</div>
            </div>

            <section class="term" id="s1">
                <div class="term-head">
                    <div class="term-icon"><i class="fas fa-hand-holding-heart"></i></div>
                    <div class="term-head-text">
                        <span class="num-label">Pasal 01</span>
                        <h2>Penggunaan Layanan</h2>
                    </div>
                </div>
                <ul>
                    <li>Anda setuju untuk menggunakan aplikasi Hafizhly hanya untuk tujuan ibadah, pembelajaran, dan pencatatan yang sah (legal).</li>
                    <li>Anda bertanggung jawab untuk menjaga kerahasiaan kredensial akun Anda (email dan kata sandi).</li>
                    <li>Dilarang keras melakukan tindakan yang dapat merusak, membebani, atau mengganggu server dan jaringan Hafizhly.</li>
                </ul>
            </section>

            <section class="term" id="s2">
                <div class="term-head">
                    <div class="term-icon"><i class="fas fa-copyright"></i></div>
                    <div class="term-head-text">
                        <span class="num-label">Pasal 02</span>
                        <h2>Hak Cipta dan Konten</h2>
                    </div>
                </div>
                <p>Antarmuka pengguna, desain, dan struktur basis data Hafizhly adalah hak milik kami. Namun, teks Al-Qur'an, Tafsir, Audio Murottal, dan data Jadwal Sholat bersumber dari API publik pihak ketiga. Hafizhly tidak mengklaim hak cipta atas teks suci Al-Qur'an.</p>
            </section>

            <section class="term" id="s3">
                <div class="term-head">
                    <div class="term-icon"><i class="fas fa-circle-exclamation"></i></div>
                    <div class="term-head-text">
                        <span class="num-label">Pasal 03</span>
                        <h2>Batasan Tanggung Jawab (Disclaimer)</h2>
                    </div>
                </div>
                <p>Kami berusaha menyajikan data seakurat mungkin. Namun, Hafizhly tidak memberikan jaminan mutlak atas keakuratan jadwal sholat, arah kiblat, atau terjemahan. Pengguna diharapkan menggunakan kebijaksanaannya sendiri (melakukan cross-check dengan otoritas agama setempat) jika terdapat keraguan.</p>
                <p>Kami tidak bertanggung jawab atas kerugian langsung maupun tidak langsung yang timbul dari penggunaan atau ketidakmampuan menggunakan layanan kami.</p>
            </section>

            <section class="term" id="s4">
                <div class="term-head">
                    <div class="term-icon"><i class="fas fa-user-lock"></i></div>
                    <div class="term-head-text">
                        <span class="num-label">Pasal 04</span>
                        <h2>Penghentian Akses</h2>
                    </div>
                </div>
                <p>Kami berhak untuk menangguhkan atau mengakhiri akun Anda secara sepihak jika Anda terbukti melanggar Syarat dan Ketentuan ini, melakukan spamming, atau tindakan peretasan.</p>
            </section>

            <section class="term" id="s5">
                <div class="term-head">
                    <div class="term-icon"><i class="fas fa-scale-balanced"></i></div>
                    <div class="term-head-text">
                        <span class="num-label">Pasal 05</span>
                        <h2>Hukum yang Berlaku</h2>
                    </div>
                </div>
                <p>Syarat dan Ketentuan ini diatur dan ditafsirkan sesuai dengan hukum yang berlaku di Indonesia.</p>
            </section>
        </main>
    </div>

    <div class="footer-note">
        <i class="fas fa-seedling"></i> <span class="brand-name" style="font-family:'Amiri',serif;color:var(--green-700);font-weight:700;">Hafizhly</span> &middot; Dibuat dengan niat baik untuk menemani ibadahmu.
    </div>

    <script>
        const sections = document.querySelectorAll('section.term');
        const tocLinks = document.querySelectorAll('.toc a');

        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                }
            });
        }, {
            threshold: 0.1
        });
        sections.forEach(sec => revealObserver.observe(sec));

        const spyObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.getAttribute('id');
                    tocLinks.forEach(link => {
                        link.classList.toggle('active', link.getAttribute('href') === '#' + id);
                    });
                }
            });
        }, {
            rootMargin: '-40% 0px -50% 0px',
            threshold: 0
        });
        sections.forEach(sec => spyObserver.observe(sec));
    </script>

</body>

</html>