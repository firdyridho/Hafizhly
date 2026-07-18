<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Early Access - Hifzly</title>
    <!-- Gunakan path logo yang sesuai dengan folder index -->
    <link rel="icon" type="image/png" href="assets/icon/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #059669;
            --primary-hover: #047857;
            --primary-light: #d1fae5;
            --dark: #0f172a;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
        }

        /* HERO SECTION */
        .hero-section {
            background: linear-gradient(135deg, #047857 0%, #10b981 100%);
            min-height: 85vh;
            position: relative;
            overflow: hidden;
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            padding-bottom: 5rem;
        }

        /* Efek cahaya di background */
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 60%);
            z-index: 0;
        }

        .navbar {
            padding: 1.5rem 0;
            position: relative;
            z-index: 10;
        }

        .navbar-brand img {
            height: 40px;
            /* Filter ini mengubah gambar logo berwarna menjadi putih murni */
            filter: brightness(0) invert(1);
        }

        .navbar-brand span {
            color: var(--white);
            font-weight: 800;
            font-size: 1.5rem;
            margin-left: 10px;
            vertical-align: middle;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: var(--white);
            margin-top: 4rem;
        }

        .badge-coming-soon {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: var(--white);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.4);
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1rem;
            letter-spacing: -1.5px;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            font-weight: 400;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2.5rem auto;
            line-height: 1.6;
        }

        /* STORE BUTTONS */
        .store-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 3rem;
        }

        .btn-store {
            background: var(--white);
            color: var(--dark);
            border-radius: 12px;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .btn-store:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            background: var(--bg);
        }

        .btn-store i {
            font-size: 1.8rem;
        }

        .btn-store.coming-soon {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            cursor: not-allowed;
        }

        .btn-store.coming-soon:hover {
            transform: none;
            box-shadow: none;
        }

        .btn-store.coming-soon .soon-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #ef4444;
            color: white;
            font-size: 0.55rem;
            padding: 2px 6px;
            border-radius: 10px;
            text-transform: uppercase;
            font-weight: 800;
        }

        .store-text {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .store-text small {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            opacity: 0.8;
            line-height: 1;
        }

        .store-text span {
            font-size: 1.1rem;
            line-height: 1.2;
        }

        /* COUNTDOWN SECTION */
        .countdown-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 2rem;
            position: relative;
            z-index: 10;
        }

        .countdown-box {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
            padding: 15px 25px;
            min-width: 100px;
            color: var(--white);
        }

        .countdown-number {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 5px;
        }

        .countdown-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            opacity: 0.8;
        }

        .countdown-separator {
            font-size: 2rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
        }

        /* FEATURES SECTION */
        .features-section {
            padding: 5rem 0;
            background-color: var(--bg);
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
            letter-spacing: -1px;
        }

        .section-header p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--white);
            border-radius: 24px;
            padding: 2.5rem;
            height: 100%;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(5, 150, 105, 0.08);
            border-color: var(--primary-light);
        }

        .feature-icon-wrapper {
            width: 60px;
            height: 60px;
            background: var(--bg);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            transition: 0.3s;
        }

        .feature-card:hover .feature-icon-wrapper {
            background: var(--primary);
        }

        .feature-card:hover .feature-icon-wrapper i {
            color: var(--white) !important;
        }

        .feature-icon-wrapper i {
            font-size: 1.5rem;
            color: var(--primary);
            transition: 0.3s;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 0.8rem;
        }

        .feature-desc {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* MOCKUP VISUAL PLACEHOLDER */
        .feature-visual {
            margin-top: 2rem;
            background: var(--bg);
            border-radius: 16px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #cbd5e1;
            position: relative;
            overflow: hidden;
        }

        .feature-visual i {
            font-size: 4rem;
            color: #e2e8f0;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 3rem;
            }

            .countdown-box {
                min-width: 70px;
                padding: 10px 15px;
            }

            .countdown-number {
                font-size: 1.5rem;
            }

            .countdown-separator {
                font-size: 1.5rem;
            }

            .store-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-store {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container">
            <!-- Navbar -->
            <nav class="navbar">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img src="assets/icon/logo.png" alt="Hifzly Logo">
                    <span>Hifzly</span>
                </a>
            </nav>

            <!-- Main Hero Content -->
            <div class="hero-content">
                <div class="badge-coming-soon">Coming Soon</div>
                <h1 class="hero-title">Get early<br>access</h1>
                <p class="hero-subtitle">
                    Bersiaplah untuk pengalaman menghafal Al-Qur'an yang lebih revolusioner.
                    Aplikasi mobile kami sedang dalam tahap penyempurnaan akhir!
                </p>

                <!-- Store Buttons -->
                <div class="store-buttons">
                    <!-- Desktop (Available) -->
                    <a href="#" class="btn-store" title="Gunakan Versi Desktop/Web Sekarang">
                        <i class="fas fa-desktop text-primary"></i>
                        <div class="store-text">
                            <small>Gunakan di</small>
                            <span>Web / Desktop</span>
                        </div>
                    </a>

                    <!-- Google Play (Coming Soon) -->
                    <a href="#" class="btn-store coming-soon" onclick="event.preventDefault()">
                        <span class="soon-badge">Soon</span>
                        <i class="fab fa-google-play"></i>
                        <div class="store-text">
                            <small>Get it on</small>
                            <span>Google Play</span>
                        </div>
                    </a>

                    <!-- App Store (Coming Soon) -->
                    <a href="#" class="btn-store coming-soon" onclick="event.preventDefault()">
                        <span class="soon-badge">Soon</span>
                        <i class="fab fa-apple"></i>
                        <div class="store-text">
                            <small>Download on the</small>
                            <span>App Store</span>
                        </div>
                    </a>
                </div>

                <!-- Countdown -->
                <div class="countdown-container">
                    <div class="countdown-box">
                        <div class="countdown-number" id="days">00</div>
                        <div class="countdown-label">Days</div>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-box">
                        <div class="countdown-number" id="hours">00</div>
                        <div class="countdown-label">Hours</div>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-box">
                        <div class="countdown-number" id="minutes">00</div>
                        <div class="countdown-label">Minutes</div>
                    </div>
                    <div class="countdown-separator">:</div>
                    <div class="countdown-box">
                        <div class="countdown-number" id="seconds">00</div>
                        <div class="countdown-label">Seconds</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2>Features designed<br>for your success.</h2>
                <p>Jelajahi fitur-fitur yang dirancang khusus untuk menjaga hafalanmu tetap terorganisir dan konsisten.</p>
            </div>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="feature-title">Mutaba'ah Cerdas</h3>
                        <p class="feature-desc">Catat setiap aktivitas tilawah, murojaah, dan hafalan barumu dengan cepat dan rapi.</p>
                        <div class="feature-visual">
                            <i class="fas fa-list-check"></i>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-fire"></i>
                        </div>
                        <h3 class="feature-title">Konsistensi & Streak</h3>
                        <p class="feature-desc">Pertahankan api semangatmu setiap hari. Jangan biarkan streak-mu terputus!</p>
                        <div class="feature-visual">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <h3 class="feature-title">Ekspor Laporan</h3>
                        <p class="feature-desc">Unduh rekap aktivitas bulananmu ke dalam format PDF atau Excel dengan satu klik.</p>
                        <div class="feature-visual">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                    </div>
                </div>

                <!-- Feature 4 -->
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card">
                        <div class="feature-icon-wrapper">
                            <i class="fas fa-mobile-screen"></i>
                        </div>
                        <h3 class="feature-title">Multi-Platform</h3>
                        <p class="feature-desc">Sinkronisasi sempurna. Akses data hafalanmu dari Desktop, Android, maupun iOS.</p>
                        <div class="feature-visual">
                            <i class="fas fa-cloud-arrow-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-4 text-center text-muted" style="background-color: var(--bg);">
        <div class="container">
            <small>&copy; <?= date('Y') ?> Hifzly. All rights reserved.</small>
        </div>
    </footer>

    <!-- SCRIPT UNTUK COUNTDOWN -->
    <script>
        // Set tanggal rilis (Contoh: 30 hari dari sekarang)
        let releaseDate = new Date();
        releaseDate.setDate(releaseDate.getDate() + 30);

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = releaseDate - now;

            // Perhitungan waktu
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Tampilkan hasil
            document.getElementById("days").innerHTML = days < 10 ? '0' + days : days;
            document.getElementById("hours").innerHTML = hours < 10 ? '0' + hours : hours;
            document.getElementById("minutes").innerHTML = minutes < 10 ? '0' + minutes : minutes;
            document.getElementById("seconds").innerHTML = seconds < 10 ? '0' + seconds : seconds;
        }

        // Jalankan setiap 1 detik
        setInterval(updateCountdown, 1000);
        updateCountdown(); // Panggilan pertama agar tidak nunggu 1 detik
    </script>
</body>

</html>