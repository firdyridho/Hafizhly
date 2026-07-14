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

// --- PROSES UPDATE PROFIL (Jika Form Disubmit) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password']; // Opsional

    if (!empty($password)) {
        // Jika password diisi, update beserta passwordnya
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET nama_lengkap='$nama', email='$email', password='$hashed' WHERE id=$user_id";
    } else {
        // Jika tidak, biarkan password lama
        $query = "UPDATE users SET nama_lengkap='$nama', email='$email' WHERE id=$user_id";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['nama_lengkap'] = $nama; // Update session
        $pesan_sukses = "Profil berhasil diperbarui!";
    }
}

// Ambil data user terbaru untuk diisi ke form
$q_user = mysqli_query($conn, "SELECT nama_lengkap, email FROM users WHERE id=$user_id");
$user_data = mysqli_fetch_assoc($q_user);
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

    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
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
        }

        /* --- HEADER --- */
        .header {
            background: white;
            padding: max(15px, env(safe-area-inset-top)) 20px 15px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid var(--border);
        }

        .back-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: center;
            align-items: center;
            background: white;
            color: var(--text-dark);
            cursor: pointer;
            text-decoration: none;
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

        /* --- VIEWS (Section yang akan disembunyikan/dimunculkan) --- */
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

        /* --- MENU UTAMA PENGATURAN --- */
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
            transition: 0.2s;
        }

        .setting-item:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
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

        .si-icon.quran {
            background: #d1fae5;
            color: var(--primary-dark);
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
            background: var(--bg-color);
        }

        .form-control:focus {
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-location {
            background: #e2e8f0;
            color: var(--text-dark);
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        /* --- RADIO CARD (Untuk Audio) --- */
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
            background: white;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: 0.2s;
        }

        .rc-content:hover {
            background: #f8fafc;
        }

        .radio-card input:checked+.rc-content {
            border-color: var(--primary);
            background: #f0fdf4;
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
    </style>
</head>

<body>

    <!-- Header Dinamis -->
    <div class="header">
        <button class="back-btn" id="btnBack" onclick="goBack()"><i class="fas fa-arrow-left"></i></button>
        <h1 id="headerTitle">Pengaturan</h1>
    </div>

    <div class="container">

        <!-- 1. VIEW MENU UTAMA -->
        <div id="view-menu" class="view-section active">
            <div class="setting-list">
                <div class="setting-item" onclick="switchView('profile')">
                    <div class="si-icon profile"><i class="fas fa-user-edit"></i></div>
                    <div class="si-text">
                        <div class="si-title">Profil Akun</div>
                        <div class="si-desc">Ubah nama, email, dan password</div>
                    </div>
                    <i class="fas fa-chevron-right si-arrow"></i>
                </div>

                <div class="setting-item" onclick="switchView('quran')">
                    <div class="si-icon quran"><i class="fas fa-volume-up"></i></div>
                    <div class="si-text">
                        <div class="si-title">Audio Al-Qur'an</div>
                        <div class="si-text si-desc" id="lbl-qari">Mishary Rashid Alafasy</div>
                    </div>
                    <i class="fas fa-chevron-right si-arrow"></i>
                </div>

                <div class="setting-item" onclick="switchView('location')">
                    <div class="si-icon location"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="si-text">
                        <div class="si-title">Lokasi Saya</div>
                        <div class="si-desc" id="lbl-location">Jakarta, Indonesia</div>
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

                <div class="form-group">
                    <label class="form-label">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Profil</button>
            </form>
        </div>

        <!-- 3. VIEW AUDIO QUR'AN -->
        <div id="view-quran" class="view-section">
            <p style="margin-bottom:15px; color:var(--text-muted); font-size:0.9rem;">Pilih suara Qari yang akan digunakan saat memutar ayat Al-Qur'an.</p>

            <label class="radio-card">
                <input type="radio" name="qari" value="ar.alafasy" onchange="saveQari('ar.alafasy', 'Mishary Rashid Alafasy')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Mishary Rashid Alafasy</div>
                </div>
            </label>

            <label class="radio-card">
                <input type="radio" name="qari" value="ar.abdulbasitmurattal" onchange="saveQari('ar.abdulbasitmurattal', 'Abdul Basit (Murattal)')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Abdul Basit (Murattal)</div>
                </div>
            </label>

            <label class="radio-card">
                <input type="radio" name="qari" value="ar.sudais" onchange="saveQari('ar.sudais', 'Abdurrahmaan As-Sudais')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Abdurrahmaan As-Sudais</div>
                </div>
            </label>

            <label class="radio-card">
                <input type="radio" name="qari" value="ar.husary" onchange="saveQari('ar.husary', 'Mahmoud Khalil Al-Husary')">
                <div class="rc-content">
                    <div class="rc-circle"></div>
                    <div class="rc-title">Mahmoud Khalil Al-Husary</div>
                </div>
            </label>
        </div>

        <!-- 4. VIEW LOKASI -->
        <div id="view-location" class="view-section">
            <p style="margin-bottom:15px; color:var(--text-muted); font-size:0.9rem;">Lokasi ini digunakan untuk menyesuaikan Jadwal Sholat di beranda.</p>

            <div class="form-group">
                <label class="form-label">Nama Kota/Lokasi Saat Ini</label>
                <input type="text" id="lokasi-input" class="form-control" placeholder="Contoh: Jakarta">
            </div>

            <button type="button" class="btn btn-primary" onclick="saveManualLocation()">Simpan Lokasi</button>

            <div style="text-align:center; margin:15px 0; color:var(--text-muted); font-size:0.9rem;">ATAU</div>

            <button type="button" class="btn btn-location" onclick="autoDetectLocation()">
                <i class="fas fa-location-arrow"></i> Deteksi Lokasi Otomatis
            </button>
        </div>

    </div>

    <!-- Panggil Navigasi Bawah (Jika kamu pakai komponen nav) -->
    <?php include '../components/nav.php'; ?>

    <script>
        // --- LOGIKA PERPINDAHAN TAMPILAN (SATU FILE) ---
        let currentView = 'menu';

        function switchView(viewId) {
            // Sembunyikan semua view
            document.querySelectorAll('.view-section').forEach(el => el.classList.remove('active'));

            // Tampilkan view yang dipilih
            document.getElementById('view-' + viewId).classList.add('active');
            currentView = viewId;

            // Ubah judul dan fungsi tombol back
            const headerTitle = document.getElementById('headerTitle');
            if (viewId === 'menu') {
                headerTitle.innerText = "Pengaturan";
            } else if (viewId === 'profile') {
                headerTitle.innerText = "Profil Akun";
            } else if (viewId === 'quran') {
                headerTitle.innerText = "Audio Al-Qur'an";
            } else if (viewId === 'location') {
                headerTitle.innerText = "Lokasi Saya";
            }
        }

        function goBack() {
            if (currentView === 'menu') {
                // Jika sedang di menu utama, kembali ke index.php
                window.location.href = 'index.php';
            } else {
                // Jika sedang di dalam form, kembali ke menu pengaturan
                switchView('menu');
            }
        }

        // --- CEK PHP UPDATE PROFIL ---
        <?php if (!empty($pesan_sukses)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= $pesan_sukses ?>',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>

        // --- INISIALISASI DATA LOCALSTORAGE ---
        window.onload = function() {
            // Load Audio Qari
            const savedQari = localStorage.getItem('hifzly_qari_id') || 'ar.alafasy';
            const savedQariName = localStorage.getItem('hifzly_qari_name') || 'Mishary Rashid Alafasy';
            document.getElementById('lbl-qari').innerText = savedQariName;

            // Set radio button aktif
            const radios = document.getElementsByName('qari');
            radios.forEach(r => {
                if (r.value === savedQari) r.checked = true;
            });

            // Load Lokasi
            const savedCity = localStorage.getItem('hifzly_city') || 'Belum diatur';
            document.getElementById('lbl-location').innerText = savedCity;
            document.getElementById('lokasi-input').value = (savedCity !== 'Belum diatur') ? savedCity : '';
        }

        // --- FUNGSI SIMPAN AUDIO ---
        function saveQari(id, name) {
            localStorage.setItem('hifzly_qari_id', id);
            localStorage.setItem('hifzly_qari_name', name);
            document.getElementById('lbl-qari').innerText = name;

            Swal.fire({
                icon: 'success',
                title: 'Tersimpan',
                text: 'Audio Qari berhasil diubah ke ' + name,
                timer: 1500,
                showConfirmButton: false
            });
            setTimeout(() => switchView('menu'), 1500);
        }

        // --- FUNGSI LOKASI ---
        function saveManualLocation() {
            const val = document.getElementById('lokasi-input').value;
            if (!val) return Swal.fire('Oops', 'Nama lokasi tidak boleh kosong', 'warning');

            // Simpan nama kotanya saja. (Untuk fungsi jadwal sholat, di index.php nantinya butuh koordinat lat/lon, 
            // tapi kita bisa andalkan sistem default jika hanya kota).
            localStorage.setItem('hifzly_city', val);
            document.getElementById('lbl-location').innerText = val;

            Swal.fire({
                icon: 'success',
                title: 'Tersimpan',
                text: 'Lokasi manual berhasil disimpan',
                timer: 1500,
                showConfirmButton: false
            });
            setTimeout(() => switchView('menu'), 1500);
        }

        function autoDetectLocation() {
            Swal.fire({
                title: 'Mencari Lokasi...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
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
                                Swal.fire('Error', 'Gagal menerjemahkan lokasi', 'error');
                            }
                        },
                        () => {
                            Swal.fire('Gagal', 'Izin akses lokasi ditolak oleh browser Anda.', 'error');
                        }, {
                            enableHighAccuracy: true,
                            timeout: 5000
                        }
                );
            } else {
                Swal.fire('Gagal', 'Browser tidak mendukung pelacakan lokasi', 'error');
            }
        }
    </script>
</body>

</html>