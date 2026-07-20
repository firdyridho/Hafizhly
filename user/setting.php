<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$pesan_sukses = '';
$pesan_error = '';

// Ambil data user saat ini (TERMASUK PASSWORD untuk verifikasi)
$q_user = mysqli_query($conn, "SELECT nama_lengkap, email, password FROM users WHERE id=$user_id");
$user_data = mysqli_fetch_assoc($q_user);

// --- PROSES UPDATE PROFIL ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Cek apakah user ingin mengubah password
    if (!empty($new_password) || !empty($old_password)) {
        if (empty($old_password)) {
            $pesan_error = "Harap masukkan password lama Anda.";
        } elseif (!password_verify($old_password, $user_data['password'])) {
            $pesan_error = "Password lama salah!";
        } elseif ($new_password !== $confirm_password) {
            $pesan_error = "Konfirmasi password baru tidak cocok!";
        } else {
            // Password valid, hash dan update
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE users SET nama_lengkap='$nama', email='$email', password='$hashed' WHERE id=$user_id";
            if (mysqli_query($conn, $query)) {
                $pesan_sukses = "Profil dan Password berhasil diperbarui!";
                // Update data array agar form langsung menampilkan data baru
                $user_data['nama_lengkap'] = $nama;
                $user_data['email'] = $email;
                $user_data['password'] = $hashed; // Update hash di memori lokal
                $_SESSION['nama_lengkap'] = $nama;
            } else {
                $pesan_error = "Terjadi kesalahan sistem saat memperbarui profil.";
            }
        }
    } else {
        // Update tanpa ganti password
        $query = "UPDATE users SET nama_lengkap='$nama', email='$email' WHERE id=$user_id";
        if (mysqli_query($conn, $query)) {
            $pesan_sukses = "Profil berhasil diperbarui!";
            $user_data['nama_lengkap'] = $nama;
            $user_data['email'] = $email;
            $_SESSION['nama_lengkap'] = $nama;
        } else {
            $pesan_error = "Terjadi kesalahan sistem saat memperbarui profil.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pengaturan - Ngajii</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SCRIPT APLIKASI TEMA SEGERA UNTUK MENCEGAH FLASHING -->
    <script>
        const savedTheme = localStorage.getItem('ngajii_theme') || 'system';
        const savedColor = localStorage.getItem('ngajii_primary_color') || '#10b981';
        const savedWidgetColor = localStorage.getItem('ngajii_widget_color') || '';

        function applyThemeMode(theme) {
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.removeAttribute('data-theme');
            }
        }
        applyThemeMode(savedTheme);
        document.documentElement.style.setProperty('--primary', savedColor);
        if (savedWidgetColor) document.documentElement.style.setProperty('--card-bg', savedWidgetColor);
    </script>

    <style>
        :root {
            /* Warna Default */
            --primary: #10b981;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --input-bg: #f8fafc;
        }

        /* TEMA GELAP (DARK MODE) */
        [data-theme="dark"] {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --input-bg: #0f172a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            overflow-x: hidden;
            transition: background-color 0.3s, color 0.3s;
        }

        /* --- HEADER --- */
        .header {
            background: var(--card-bg);
            padding: max(15px, env(safe-area-inset-top)) 20px 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid var(--border);
            transition: 0.3s;
        }

        .back-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--card-bg);
            color: var(--text-dark);
            cursor: pointer;
            transition: 0.2s;
        }

        .header h1 {
            font-size: 1.2rem;
            font-weight: 800;
        }

        .container {
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            padding-bottom: 100px;
        }

        /* --- VIEWS --- */
        .view-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .view-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- MENU SETTING --- */
        .setting-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .setting-item {
            background: var(--card-bg);
            padding: 18px 20px;
            border-radius: 16px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        .setting-item:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .si-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .si-icon.profile {
            background: #e0f2fe;
            color: #0284c7;
        }

        .si-icon.theme {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .si-icon.quran {
            background: #d1fae5;
            color: #047857;
        }

        .si-icon.location {
            background: #fef3c7;
            color: #d97706;
        }

        .si-icon.logout {
            background: #fee2e2;
            color: #dc2626;
        }

        .si-text {
            flex-grow: 1;
        }

        .si-title {
            font-weight: 700;
            font-size: 1rem;
            color: var(--text-dark);
            margin-bottom: 3px;
        }

        .si-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .si-arrow {
            color: var(--border);
            font-size: 1.2rem;
        }

        /* --- FORM & INPUT --- */
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-size: 0.95rem;
            outline: none;
            transition: 0.2s;
            background: var(--input-bg);
            color: var(--text-dark);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        /* Input Password dengan Icon Mata */
        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper .form-control {
            padding-right: 45px;
        }

        .eye-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 1.1rem;
            cursor: pointer;
            padding: 5px;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:active {
            transform: scale(0.98);
            opacity: 0.9;
        }

        .btn-location {
            background: var(--border);
            color: var(--text-dark);
            margin-top: 10px;
        }

        /* --- TEMA & COLOR PICKER --- */
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 25px;
        }

        .theme-card {
            background: var(--card-bg);
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 15px 10px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
            color: var(--text-dark);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .theme-card i {
            font-size: 1.5rem;
            margin-bottom: 8px;
            display: block;
            color: var(--text-muted);
        }

        .theme-card.active {
            border-color: var(--primary);
            background: rgba(16, 185, 129, 0.05);
        }

        .theme-card.active i {
            color: var(--primary);
        }

        .color-options {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 10px;
        }

        .color-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: 0.2s;
            position: relative;
        }

        .color-circle.active {
            border-color: var(--text-dark);
            transform: scale(1.1);
        }

        .color-picker-wrapper {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            border: 2px dashed var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            background: conic-gradient(red, yellow, lime, aqua, blue, magenta, red);
        }

        .color-picker-wrapper input[type="color"] {
            position: absolute;
            width: 200%;
            height: 200%;
            opacity: 0;
            cursor: pointer;
        }

        /* --- RADIO CARD AUDIO --- */
        .radio-card {
            display: block;
            margin-bottom: 12px;
            cursor: pointer;
        }

        .radio-card input {
            display: none;
        }

        .rc-content {
            padding: 16px;
            border: 2px solid var(--border);
            border-radius: 14px;
            background: var(--card-bg);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: 0.2s;
        }

        .radio-card input:checked+.rc-content {
            border-color: var(--primary);
        }

        .rc-circle {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .radio-card input:checked+.rc-content .rc-circle {
            border-color: var(--primary);
        }

        .rc-circle::after {
            content: '';
            width: 12px;
            height: 12px;
            background: var(--primary);
            border-radius: 50%;
            transform: scale(0);
            transition: 0.2s;
        }

        .radio-card input:checked+.rc-content .rc-circle::after {
            transform: scale(1);
        }

        .rc-title {
            font-weight: 700;
            color: var(--text-dark);
        }

        hr.divider {
            border: 0;
            border-top: 1px dashed var(--border);
            margin: 25px 0;
        }
    </style>
</head>

<body>

    <div class="header">
        <button class="back-btn" id="btnBack" onclick="goBack()"><i class="fas fa-arrow-left"></i></button>
        <h1 id="headerTitle">Pengaturan</h1>
    </div>

    <div class="container">

        <!-- 1. VIEW MENU UTAMA -->
        <div id="view-menu" class="view-section active">
            <div class="setting-list">

                <div class="setting-item" onclick="switchView('profile')">
                    <div class="si-icon profile"><i class="fas fa-user-shield"></i></div>
                    <div class="si-text">
                        <div class="si-title">Profil & Keamanan</div>
                        <div class="si-desc">Ubah nama, email, dan password</div>
                    </div>
                    <i class="fas fa-chevron-right si-arrow"></i>
                </div>

                <div class="setting-item" onclick="switchView('theme')">
                    <div class="si-icon theme"><i class="fas fa-paint-roller"></i></div>
                    <div class="si-text">
                        <div class="si-title">Tampilan & Warna</div>
                        <div class="si-desc">Mode gelap, warna aksen & widget</div>
                    </div>
                    <i class="fas fa-chevron-right si-arrow"></i>
                </div>

                <div class="setting-item" onclick="switchView('quran')">
                    <div class="si-icon quran"><i class="fas fa-volume-up"></i></div>
                    <div class="si-text">
                        <div class="si-title">Audio Al-Qur'an</div>
                        <div class="si-text si-desc" id="lbl-qari">Memuat...</div>
                    </div>
                    <i class="fas fa-chevron-right si-arrow"></i>
                </div>

                <div class="setting-item" onclick="switchView('location')">
                    <div class="si-icon location"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="si-text">
                        <div class="si-title">Lokasi Saya</div>
                        <div class="si-desc" id="lbl-location">Memuat...</div>
                    </div>
                    <i class="fas fa-chevron-right si-arrow"></i>
                </div>

                <a href="../logout.php" style="text-decoration:none;" class="setting-item">
                    <div class="si-icon logout"><i class="fas fa-sign-out-alt"></i></div>
                    <div class="si-text">
                        <div class="si-title" style="color:#dc2626;">Keluar Akun</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- 2. VIEW PROFIL -->
        <div id="view-profile" class="view-section">
            <form action="" method="POST">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user_data['nama_lengkap'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email / Username</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                </div>

                <hr class="divider">
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:15px;"><i class="fas fa-lock"></i> Biarkan kosong jika tidak ingin mengubah password.</p>

                <div class="form-group">
                    <label class="form-label">Password Lama</label>
                    <div class="input-icon-wrapper">
                        <input type="password" name="old_password" id="old_pw" class="form-control" placeholder="Masukkan password lama">
                        <button type="button" class="eye-btn" onclick="togglePassword('old_pw', this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <div class="input-icon-wrapper">
                        <input type="password" name="new_password" id="new_pw" class="form-control" placeholder="Masukkan password baru">
                        <button type="button" class="eye-btn" onclick="togglePassword('new_pw', this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-icon-wrapper">
                        <input type="password" name="confirm_password" id="conf_pw" class="form-control" placeholder="Ulangi password baru">
                        <button type="button" class="eye-btn" onclick="togglePassword('conf_pw', this)"><i class="fas fa-eye"></i></button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Profil</button>
            </form>
        </div>

        <!-- 3. VIEW TEMA & WARNA (BARU) -->
        <div id="view-theme" class="view-section">

            <label class="form-label">Tema Tampilan</label>
            <div class="theme-grid">
                <div class="theme-card" id="theme-light" onclick="setTheme('light')">
                    <i class="fas fa-sun"></i> Terang
                </div>
                <div class="theme-card" id="theme-dark" onclick="setTheme('dark')">
                    <i class="fas fa-moon"></i> Gelap
                </div>
                <div class="theme-card" id="theme-system" onclick="setTheme('system')">
                    <i class="fas fa-mobile-alt"></i> Sistem
                </div>
            </div>

            <hr class="divider">

            <label class="form-label">Warna Utama (Aksen)</label>
            <div class="color-options" id="primary-colors">
                <!-- Hijau Ngajii -->
                <div class="color-circle" style="background:#10b981;" onclick="setPrimaryColor('#10b981', this)"></div>
                <!-- Biru -->
                <div class="color-circle" style="background:#3b82f6;" onclick="setPrimaryColor('#3b82f6', this)"></div>
                <!-- Ungu -->
                <div class="color-circle" style="background:#8b5cf6;" onclick="setPrimaryColor('#8b5cf6', this)"></div>
                <!-- Oranye -->
                <div class="color-circle" style="background:#f97316;" onclick="setPrimaryColor('#f97316', this)"></div>
                <!-- Custom -->
                <div class="color-picker-wrapper" title="Warna Kustom">
                    <input type="color" id="customPrimary" oninput="setPrimaryColor(this.value, this.parentElement)">
                </div>
            </div>

            <hr class="divider">

            <label class="form-label">Warna Latar Widget/Card (Kustom)</label>
            <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:10px;">Sesuaikan warna kotak latar di aplikasi. Klik tombol Reset untuk mengembalikan warna bawaan tema.</p>
            <div class="color-options">
                <div class="color-picker-wrapper" title="Pilih Warna Widget">
                    <input type="color" id="customWidget" oninput="setWidgetColor(this.value)">
                </div>
                <button type="button" class="btn btn-location" style="width:auto; padding: 10px 15px; margin:0;" onclick="resetWidgetColor()">Reset Widget</button>
            </div>
        </div>

        <!-- 4. VIEW AUDIO QUR'AN -->
        <div id="view-quran" class="view-section">
            <p style="margin-bottom:15px; color:var(--text-muted); font-size:0.9rem;">Pilih suara Qari yang akan digunakan saat memutar ayat Al-Qur'an.</p>

            <label class="radio-card">
                <input type="radio" name="qari" value="01" onchange="saveQari('01', 'Abdullah Al-Juhany')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Abdullah Al-Juhany</div>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="qari" value="02" onchange="saveQari('02', 'Abdul Muhsin Al-Qasim')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Abdul Muhsin Al-Qasim</div>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="qari" value="03" onchange="saveQari('03', 'Abdurrahman As-Sudais')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Abdurrahman As-Sudais</div>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="qari" value="04" onchange="saveQari('04', 'Ibrahim Al-Dossari')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Ibrahim Al-Dossari</div>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="qari" value="05" onchange="saveQari('05', 'Misyari Rasyid Al-Afasy')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Misyari Rasyid Al-Afasy</div>
                </div>
            </label>
        </div>

        <!-- 5. VIEW LOKASI -->
        <div id="view-location" class="view-section">
            <p style="margin-bottom:15px; color:var(--text-muted); font-size:0.9rem;">Lokasi ini digunakan untuk menyesuaikan Jadwal Sholat di beranda.</p>

            <div class="form-group">
                <label class="form-label">Nama Kota/Lokasi Saat Ini</label>
                <input type="text" id="lokasi-input" class="form-control" placeholder="Contoh: Jakarta">
            </div>

            <button type="button" class="btn btn-primary" onclick="saveManualLocation()"><i class="fas fa-save"></i> Simpan Lokasi</button>

            <div style="text-align:center; margin:20px 0; color:var(--text-muted); font-size:0.9rem; font-weight:bold;">ATAU</div>

            <button type="button" class="btn btn-location" onclick="autoDetectLocation()">
                <i class="fas fa-location-arrow"></i> Deteksi Lokasi Otomatis (GPS)
            </button>
        </div>

    </div>

    <!-- Panggil Navigasi Bawah -->
    <?php include '../components/nav.php'; ?>

    <script>
        // --- ALERT PHP UNTUK PROFIL ---
        <?php if (!empty($pesan_sukses)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= $pesan_sukses ?>',
                timer: 2500,
                showConfirmButton: false
            });
        <?php endif; ?>
        <?php if (!empty($pesan_error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '<?= $pesan_error ?>'
            });
            switchView('profile'); // Buka tab profil lagi jika error
        <?php endif; ?>

        // --- NAVIGASI ---
        let currentView = 'menu';

        function switchView(viewId) {
            document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));
            document.getElementById('view-' + viewId).classList.add('active');
            currentView = viewId;

            const title = {
                'menu': 'Pengaturan',
                'profile': 'Profil & Keamanan',
                'theme': 'Tampilan & Warna',
                'quran': 'Audio Al-Qur\'an',
                'location': 'Lokasi Saya'
            };
            document.getElementById('headerTitle').innerText = title[viewId];
        }

        function goBack() {
            if (currentView === 'menu') window.location.href = 'dashboard.php';
            else switchView('menu');
        }

        // --- PASSWORD EYE TOGGLE ---
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // --- INIT DATA LOKAL ---
        window.onload = function() {
            // Audio
            const savedQari = localStorage.getItem('hifzly_qari_id') || '05';
            document.getElementById('lbl-qari').innerText = localStorage.getItem('hifzly_qari_name') || 'Misyari Rasyid Al-Afasy';
            document.getElementsByName('qari').forEach(r => {
                if (r.value === savedQari) r.checked = true;
            });

            // Lokasi
            const savedCity = localStorage.getItem('hifzly_city') || 'Belum diatur';
            document.getElementById('lbl-location').innerText = savedCity;
            if (savedCity !== 'Belum diatur') document.getElementById('lokasi-input').value = savedCity;

            // Tema & Warna Init UI
            document.getElementById('theme-' + (localStorage.getItem('ngajii_theme') || 'system')).classList.add('active');

            // Set active circle for color
            const currColor = localStorage.getItem('ngajii_primary_color') || '#10b981';
            let matched = false;
            document.querySelectorAll('#primary-colors .color-circle').forEach(el => {
                if (el.style.backgroundColor === rgb2hex(el.style.backgroundColor) === currColor.toLowerCase() || rgb2hex(el.style.backgroundColor) === currColor.toLowerCase()) {
                    el.classList.add('active');
                    matched = true;
                }
            });
            if (!matched) {
                document.getElementById('customPrimary').value = currColor;
                document.getElementById('customPrimary').parentElement.classList.add('active');
            }

            const currWidget = localStorage.getItem('ngajii_widget_color');
            if (currWidget) document.getElementById('customWidget').value = currWidget;
        }

        // --- TEMA & WARNA LOGIC ---
        function setTheme(theme) {
            document.querySelectorAll('.theme-card').forEach(el => el.classList.remove('active'));
            document.getElementById('theme-' + theme).classList.add('active');

            localStorage.setItem('ngajii_theme', theme);
            applyThemeMode(theme); // Fungsi ada di tag <head>

            // Reset widget color jika user ganti tema agar tidak aneh
            resetWidgetColor();
        }

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (localStorage.getItem('ngajii_theme') === 'system') applyThemeMode('system');
        });

        function setPrimaryColor(hex, el) {
            document.documentElement.style.setProperty('--primary', hex);
            localStorage.setItem('ngajii_primary_color', hex);

            document.querySelectorAll('#primary-colors .color-circle, #primary-colors .color-picker-wrapper').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
        }

        function setWidgetColor(hex) {
            document.documentElement.style.setProperty('--card-bg', hex);
            localStorage.setItem('ngajii_widget_color', hex);
        }

        function resetWidgetColor() {
            document.documentElement.style.removeProperty('--card-bg');
            localStorage.removeItem('ngajii_widget_color');
            document.getElementById('customWidget').value = "#ffffff";
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Warna widget direset',
                showConfirmButton: false,
                timer: 1500
            });
        }

        // Helper untuk match Hex
        const rgb2hex = (rgb) => `#${rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/).slice(1).map(n => parseInt(n, 10).toString(16).padStart(2, '0')).join('')}`;

        // --- AUDIO LOGIC ---
        function saveQari(id, name) {
            localStorage.setItem('hifzly_qari_id', id);
            localStorage.setItem('hifzly_qari_name', name);
            document.getElementById('lbl-qari').innerText = name;
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan',
                text: 'Audio diubah ke ' + name,
                timer: 1500,
                showConfirmButton: false
            });
            setTimeout(() => switchView('menu'), 1500);
        }

        // --- LOKASI LOGIC ---
        function saveManualLocation() {
            const val = document.getElementById('lokasi-input').value;
            if (!val) return Swal.fire('Oops', 'Nama lokasi tidak boleh kosong', 'warning');
            localStorage.setItem('hifzly_city', val);
            document.getElementById('lbl-location').innerText = val;
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan',
                text: 'Lokasi berhasil disimpan',
                timer: 1500,
                showConfirmButton: false
            });
            setTimeout(() => switchView('menu'), 1500);
        }

        function autoDetectLocation() {
            Swal.fire({
                title: 'Mencari Lokasi...',
                text: 'Pastikan GPS perangkat Anda menyala.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            try {
                                const res = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=id`);
                                const data = await res.json();
                                const city = (data.city || data.locality || "Lokasi Baru") + ", " + (data.countryName || "Indonesia");

                                localStorage.setItem('hifzly_lat', lat);
                                localStorage.setItem('hifzly_lon', lon);
                                localStorage.setItem('hifzly_city', city);

                                document.getElementById('lbl-location').innerText = city;
                                document.getElementById('lokasi-input').value = city;

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Lokasi Ditemukan',
                                    text: city,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                setTimeout(() => switchView('menu'), 2000);
                            } catch (e) {
                                Swal.fire('Error', 'Gagal menerjemahkan lokasi.', 'error');
                            }
                        },
                        (error) => {
                            // CEK ERROR GPS
                            if (error.code === error.PERMISSION_DENIED) {
                                Swal.fire('Akses Ditolak', 'Izin lokasi ditolak oleh browser. Harap izinkan akses lokasi di pengaturan.', 'error');
                            } else if (error.code === error.POSITION_UNAVAILABLE) {
                                Swal.fire('GPS Tidak Aktif', 'Harap nyalakan GPS / Location Services di HP Anda terlebih dahulu.', 'warning');
                            } else {
                                Swal.fire('Gagal', 'Pencarian lokasi memakan waktu terlalu lama (Timeout).', 'error');
                            }
                        }, {
                            enableHighAccuracy: true,
                            timeout: 7000
                        }
                );
            } else {
                Swal.fire('Gagal', 'Browser tidak mendukung pelacakan lokasi', 'error');
            }
        }
    </script>
</body>

</html>