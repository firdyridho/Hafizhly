<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// FUNGSI KONVERSI LINK YOUTUBE KE EMBED
function getYouTubeEmbedUrl($url)
{
    if (empty($url)) return false;
    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';
    if (preg_match($longUrlRegex, $url, $matches)) {
        return "https://www.youtube.com/embed/" . $matches[3];
    }
    if (preg_match($shortUrlRegex, $url, $matches)) {
        return "https://www.youtube.com/embed/" . $matches[1];
    }
    return $url;
}

// Cek apakah user sedang melihat detail materi atau daftar materi
$view_materi_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$materi_detail = null;
$soal_kuis = [];

if ($view_materi_id > 0) {
    $q_detail = mysqli_query($conn, "SELECT * FROM tajwid_materi WHERE id='$view_materi_id'");
    $materi_detail = mysqli_fetch_assoc($q_detail);

    // Ambil soal kuis
    $q_kuis = mysqli_query($conn, "SELECT * FROM tajwid_kuis WHERE materi_id='$view_materi_id'");
    while ($row = mysqli_fetch_assoc($q_kuis)) {
        $soal_kuis[] = $row;
    }
} else {
    // Ambil semua daftar materi
    $materi_q = mysqli_query($conn, "SELECT * FROM tajwid_materi ORDER BY created_at DESC");
    $daftar_materi = [];
    while ($row = mysqli_fetch_assoc($materi_q)) {
        $daftar_materi[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Belajar Tajwid - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Library untuk Convert HTML to PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary: #059669;
            --primary-light: #d1fae5;
            --dark: #1e293b;
            --text-muted: #64748b;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --spacing: 24px;
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
            padding-bottom: 100px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .back-btn {
            color: var(--text-muted);
            font-size: 1.2rem;
            text-decoration: none;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
            flex-grow: 1;
        }

        .card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            margin-bottom: var(--spacing);
        }

        /* List Materi */
        .materi-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .materi-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 16px;
            text-decoration: none;
            color: var(--dark);
            transition: 0.2s;
            background: white;
        }

        .materi-item:hover {
            border-color: var(--primary);
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.1);
            transform: translateY(-2px);
        }

        .m-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.3rem;
        }

        /* Detail Materi */
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 16px;
            margin-bottom: 20px;
            background: #000;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .materi-content {
            font-size: 1rem;
            line-height: 1.7;
            color: #334155;
            margin-bottom: 20px;
            white-space: pre-line;
        }

        .action-bar {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            border-top: 1px solid var(--border);
            padding-top: 20px;
        }

        .btn {
            flex: 1;
            text-align: center;
            padding: 14px;
            border-radius: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }

        .btn-pdf {
            background: #fee2e2;
            color: #dc2626;
        }

        .btn-quiz {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
        }

        /* Area Kuis (Tersembunyi Awalnya) */
        #quizArea {
            display: none;
            margin-top: 20px;
        }

        .question-box {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .q-text {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1.05rem;
        }

        .option-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border: 1px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            margin-bottom: 10px;
            background: white;
            transition: 0.2s;
        }

        .option-label:hover {
            border-color: var(--primary);
        }

        .option-label input[type="radio"] {
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
        }

        /* Custom Modal Hasil */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 24px;
            text-align: center;
            width: 90%;
            max-width: 350px;
        }

        .score-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 800;
            margin: 0 auto 20px auto;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">

        <?php if (!$view_materi_id): ?>
            <!-- HALAMAN 1: DAFTAR MATERI -->
            <div class="header">
                <a href="target.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title">📚 Belajar Tajwid</h1>
            </div>
            <p style="color:var(--text-muted); margin-bottom:20px;">Pilih materi tajwid yang ingin kamu pelajari hari ini.</p>

            <div class="materi-list">
                <?php if (empty($daftar_materi)): ?>
                    <div style="text-align:center; padding:30px; color:var(--text-muted);">Belum ada materi tajwid tersedia.</div>
                <?php endif; ?>

                <?php foreach ($daftar_materi as $m): ?>
                    <a href="tajwid.php?id=<?= $m['id'] ?>" class="materi-item">
                        <div style="display:flex; align-items:center; gap:15px;">
                            <div class="m-icon"><i class="fas fa-book-quran"></i></div>
                            <div>
                                <div style="font-weight:700; font-size:1.1rem;"><?= htmlspecialchars($m['judul']) ?></div>
                                <div style="font-size:0.85rem; color:var(--text-muted); margin-top:4px;">
                                    <?= $m['youtube_url'] ? '<i class="fab fa-youtube" style="color:#ef4444;"></i> Video Tersedia' : 'Teks Materi' ?>
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right" style="color:#cbd5e1;"></i>
                    </a>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- HALAMAN 2: DETAIL MATERI & KUIS -->
            <div class="header">
                <a href="tajwid.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title"><?= htmlspecialchars($materi_detail['judul']) ?></h1>
            </div>

            <!-- Area ini yang akan diekspor ke PDF -->
            <div class="card" id="materiToPdf">

                <?php
                $embed_url = getYouTubeEmbedUrl($materi_detail['youtube_url']);
                if ($embed_url):
                ?>
                    <div class="video-container" data-html2canvas-ignore="true">
                        <iframe src="<?= $embed_url ?>" allowfullscreen></iframe>
                    </div>
                <?php endif; ?>

                <div style="font-size:1.5rem; font-weight:700; margin-bottom:15px; color:var(--dark);" id="pdf-title">
                    <?= htmlspecialchars($materi_detail['judul']) ?>
                </div>

                <div class="materi-content">
                    <?= htmlspecialchars($materi_detail['konten']) ?>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="action-bar" id="actionBar">
                <button class="btn btn-pdf" onclick="exportPDF()"><i class="fas fa-file-pdf"></i> Ekspor PDF</button>
                <?php if (count($soal_kuis) > 0): ?>
                    <button class="btn btn-quiz" onclick="mulaiKuis()"><i class="fas fa-pen-alt"></i> Mulai Kuis (<?= count($soal_kuis) ?> Soal)</button>
                <?php endif; ?>
            </div>

            <!-- Formulir Kuis -->
            <div id="quizArea">
                <h2 style="font-size:1.2rem; margin-bottom:20px;"><i class="fas fa-tasks"></i> Kuis Evaluasi</h2>
                <form id="quizForm" onsubmit="submitKuis(event)">
                    <?php foreach ($soal_kuis as $index => $soal): ?>
                        <div class="question-box">
                            <div class="q-text"><?= ($index + 1) . '. ' . htmlspecialchars($soal['pertanyaan']) ?></div>
                            <input type="hidden" id="ans_<?= $index ?>" value="<?= $soal['jawaban_benar'] ?>">

                            <label class="option-label"><input type="radio" name="q_<?= $index ?>" value="a" required> A. <?= htmlspecialchars($soal['opsi_a']) ?></label>
                            <label class="option-label"><input type="radio" name="q_<?= $index ?>" value="b"> B. <?= htmlspecialchars($soal['opsi_b']) ?></label>
                            <label class="option-label"><input type="radio" name="q_<?= $index ?>" value="c"> C. <?= htmlspecialchars($soal['opsi_c']) ?></label>
                            <label class="option-label"><input type="radio" name="q_<?= $index ?>" value="d"> D. <?= htmlspecialchars($soal['opsi_d']) ?></label>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-quiz" style="width:100%; margin-bottom:30px;"><i class="fas fa-paper-plane"></i> Kumpulkan Jawaban</button>
                </form>
            </div>

            <!-- Modal Hasil Kuis -->
            <div class="modal-overlay" id="scoreModal">
                <div class="modal-content">
                    <div id="scoreCircle" class="score-circle">100</div>
                    <h3 id="scoreTitle" style="margin-bottom:10px;">Luar Biasa!</h3>
                    <p id="scoreMessage" style="color:var(--text-muted); font-size:0.9rem; margin-bottom:20px; line-height:1.5;"></p>
                    <button class="btn btn-quiz" style="width:100%;" onclick="location.reload()">Tutup & Selesai</button>
                </div>
            </div>

            <script>
                // Fitur Export ke PDF menggunakan HTML2PDF
                function exportPDF() {
                    const element = document.getElementById('materiToPdf');
                    const opt = {
                        margin: 15,
                        filename: 'Materi_Tajwid_Hifzly.pdf',
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        }
                    };

                    // Tambahkan class loading ke tombol
                    const btn = document.querySelector('.btn-pdf');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

                    html2pdf().set(opt).from(element).save().then(() => {
                        btn.innerHTML = originalText; // Kembalikan teks tombol
                    });
                }

                // Fitur Munculkan Kuis
                function mulaiKuis() {
                    document.getElementById('actionBar').style.display = 'none';
                    document.getElementById('quizArea').style.display = 'block';
                    // Scroll perlahan ke bagian kuis
                    window.scrollTo({
                        top: document.getElementById('quizArea').offsetTop - 20,
                        behavior: 'smooth'
                    });
                }

                // Fitur Skoring Kuis Otomatis
                function submitKuis(e) {
                    e.preventDefault();
                    const totalSoal = <?= count($soal_kuis) ?>;
                    let skorBenar = 0;

                    for (let i = 0; i < totalSoal; i++) {
                        const jawabanUser = document.querySelector(`input[name="q_${i}"]:checked`).value;
                        const jawabanAsli = document.getElementById(`ans_${i}`).value;

                        if (jawabanUser === jawabanAsli) {
                            skorBenar++;
                        }
                    }

                    // Kalkulasi Skor 0 - 100
                    const nilaiAkhir = Math.round((skorBenar / totalSoal) * 100);

                    tampilkanHasil(nilaiAkhir, skorBenar, totalSoal);
                }

                function tampilkanHasil(nilai, benar, total) {
                    const modal = document.getElementById('scoreModal');
                    const circle = document.getElementById('scoreCircle');
                    const title = document.getElementById('scoreTitle');
                    const msg = document.getElementById('scoreMessage');

                    circle.innerText = nilai;

                    if (nilai >= 80) {
                        circle.style.background = 'linear-gradient(135deg, #059669, #10b981)';
                        title.innerText = "Luar Biasa! 🎉";
                        msg.innerText = `Kamu berhasil menjawab ${benar} dari ${total} soal dengan benar. Pemahaman tajwidmu sangat baik!`;
                    } else if (nilai >= 50) {
                        circle.style.background = 'linear-gradient(135deg, #f59e0b, #fbbf24)';
                        title.innerText = "Cukup Bagus! 👍";
                        msg.innerText = `Kamu menjawab ${benar} dari ${total} soal dengan benar. Yuk pelajari lagi materinya.`;
                    } else {
                        circle.style.background = 'linear-gradient(135deg, #ef4444, #f87171)';
                        title.innerText = "Jangan Menyerah! 💪";
                        msg.innerText = `Kamu menjawab ${benar} dari ${total} soal dengan benar. Tonton videonya lagi dan coba lagi ya.`;
                    }

                    modal.style.display = 'flex';
                }
            </script>
        <?php endif; ?>

    </div>

    <!-- Panggil Bottom Navigation Hifzly di luar Container Halaman Detail -->
    <?php if (!$view_materi_id) include '../components/nav.php'; ?>
</body>

</html>