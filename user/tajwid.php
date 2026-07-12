<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    exit();
}

function getYouTubeEmbedUrl($url)
{
    if (empty($url)) return false;
    if (preg_match('/youtu.be\/([a-zA-Z0-9_-]+)\??/i', $url, $matches)) return "https://www.youtube.com/embed/" . $matches[1];
    if (preg_match('/youtube.com\/.*v=([a-zA-Z0-9_-]+)/i', $url, $matches)) return "https://www.youtube.com/embed/" . $matches[1];
    return $url;
}

$view_materi_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$materi_detail = null;
$soal_kuis = [];

if ($view_materi_id > 0) {
    $q_detail = mysqli_query($conn, "SELECT * FROM tajwid_materi WHERE id='$view_materi_id'");
    $materi_detail = mysqli_fetch_assoc($q_detail);

    $q_kuis = mysqli_query($conn, "SELECT * FROM tajwid_kuis WHERE materi_id='$view_materi_id'");
    while ($row = mysqli_fetch_assoc($q_kuis)) {
        $soal_kuis[] = $row;
    }
} else {
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

        /* LIST MATERI */
        .materi-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            margin-bottom: 20px;
            text-decoration: none;
            color: var(--dark);
            display: block;
            transition: 0.3s;
        }

        .materi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.1);
            border-color: var(--primary);
        }

        .mc-cover {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: #e2e8f0;
        }

        .mc-body {
            padding: 20px;
        }

        .mc-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .mc-badges {
            display: flex;
            gap: 10px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge {
            background: var(--bg);
            padding: 5px 12px;
            border-radius: 20px;
            color: var(--text-muted);
        }

        /* DETAIL MATERI */
        .detail-cover {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .detail-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            margin-bottom: 20px;
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 16px;
            margin-bottom: 25px;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Render hasil Quill Editor */
        .rich-content {
            line-height: 1.8;
            font-size: 1.05rem;
            color: #334155;
        }

        .rich-content img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin: 15px 0;
        }

        .rich-content h1,
        .rich-content h2,
        .rich-content h3 {
            margin-top: 20px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .action-bar {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            text-align: center;
            padding: 16px;
            border-radius: 16px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-pdf {
            background: #f1f5f9;
            color: var(--dark);
        }

        .btn-quiz {
            background: linear-gradient(135deg, var(--primary), #10b981);
            color: white;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
        }

        /* GEMINI STYLE QUIZ (SLIDE) */
        #quizOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg);
            z-index: 1000;
            display: none;
            flex-direction: column;
        }

        .quiz-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .timer-badge {
            background: #fee2e2;
            color: #ef4444;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quiz-slide-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: hidden;
        }

        .quiz-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 500px;
            display: none;
            animation: slideIn 0.4s ease forwards;
        }

        .quiz-card.active {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .q-number {
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 15px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .q-text {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .gemini-option {
            display: block;
            padding: 18px 20px;
            border: 2px solid var(--border);
            border-radius: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 600;
            font-size: 1.05rem;
            color: #475569;
            position: relative;
        }

        .gemini-option:hover {
            border-color: var(--primary-light);
            background: #f8fafc;
        }

        .gemini-option input[type="radio"] {
            display: none;
        }

        /* Style saat dipilih */
        .gemini-option.selected {
            border-color: var(--primary);
            background: #f0fdf4;
            color: var(--primary-dark);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.1);
        }

        .quiz-footer {
            padding: 20px;
            display: flex;
            justify-content: flex-end;
            background: white;
        }

        .btn-next {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            opacity: 0.5;
            pointer-events: none;
            transition: 0.3s;
        }

        .btn-next.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        /* Modal Hasil Akhir */
        .score-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .score-box {
            background: white;
            padding: 40px;
            border-radius: 30px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            animation: popUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes popUp {
            from {
                transform: scale(0.8);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0 auto 20px auto;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">

        <?php if (!$view_materi_id): ?>
            <div class="header">
                <h1 class="page-title">📚 Modul Tajwid</h1>
            </div>

            <?php foreach ($daftar_materi as $m):
                $img_src = !empty($m['cover_image']) ? '../uploads/' . $m['cover_image'] : 'https://via.placeholder.com/600x200/e2e8f0/64748b?text=Materi+Tajwid';
            ?>
                <a href="tajwid.php?id=<?= $m['id'] ?>" class="materi-card">
                    <img src="<?= $img_src ?>" class="mc-cover" alt="Cover Materi">
                    <div class="mc-body">
                        <div class="mc-title"><?= htmlspecialchars($m['judul']) ?></div>
                        <div class="mc-badges">
                            <span class="badge"><i class="far fa-clock"></i> Kuis <?= $m['waktu_kuis'] ?> Menit</span>
                            <?php if ($m['youtube_url']): ?><span class="badge"><i class="fab fa-youtube" style="color:#ef4444;"></i> Video</span><?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="header">
                <a href="tajwid.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title" style="font-size:1.2rem;">Materi Hifzly</h1>
            </div>

            <div id="materiToPdf">
                <?php if (!empty($materi_detail['cover_image'])): ?>
                    <img src="../uploads/<?= $materi_detail['cover_image'] ?>" class="detail-cover" alt="Cover">
                <?php endif; ?>

                <div class="detail-card">
                    <h1 style="font-size:1.8rem; margin-bottom:20px; color:var(--dark);"><?= htmlspecialchars($materi_detail['judul']) ?></h1>

                    <?php $embed_url = getYouTubeEmbedUrl($materi_detail['youtube_url']);
                    if ($embed_url): ?>
                        <div class="video-container" data-html2canvas-ignore="true">
                            <iframe src="<?= $embed_url ?>" allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>

                    <div class="rich-content">
                        <?= $materi_detail['konten'] ?>
                    </div>
                </div>
            </div>

            <div class="action-bar">
                <button class="btn btn-pdf" onclick="exportPDF()"><i class="fas fa-download"></i> Simpan PDF</button>
                <?php if (count($soal_kuis) > 0): ?>
                    <button class="btn btn-quiz" onclick="startQuiz()"><i class="fas fa-play"></i> Mulai Kuis Interaktif</button>
                <?php endif; ?>
            </div>

            <div id="quizOverlay">
                <div class="quiz-header">
                    <button onclick="location.reload()" style="background:none; border:none; font-size:1.5rem; color:var(--text-muted); cursor:pointer;"><i class="fas fa-times"></i></button>
                    <div class="timer-badge" id="timerDisplay"><i class="fas fa-stopwatch"></i> 00:00</div>
                </div>

                <div class="quiz-slide-container" id="slideContainer">
                </div>

                <div class="quiz-footer">
                    <button class="btn-next" id="nextBtn" onclick="nextSlide()">Selanjutnya <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <div class="score-modal" id="scoreModal">
                <div class="score-box">
                    <div id="scoreCircle" class="score-circle">100</div>
                    <h2 id="scoreTitle" style="margin-bottom:10px; color:var(--dark);">Luar Biasa!</h2>
                    <p id="scoreMessage" style="color:var(--text-muted); margin-bottom:25px; line-height:1.6;"></p>
                    <button class="btn btn-quiz" style="width:100%;" onclick="location.reload()">Selesai Belajar</button>
                </div>
            </div>

            <script>
                // --- Fitur Export PDF ---
                function exportPDF() {
                    const element = document.getElementById('materiToPdf');
                    const btn = document.querySelector('.btn-pdf');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

                    html2pdf().set({
                        margin: 15,
                        filename: 'Materi_Tajwid.pdf',
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2,
                            useCORS: true
                        }, // useCORS penting agar gambar cover ter-render di PDF
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        }
                    }).from(element).save().then(() => btn.innerHTML = '<i class="fas fa-download"></i> Simpan PDF');
                }

                // --- QUIZ ENGINE (Gemini Style) ---
                const questions = <?= json_encode($soal_kuis) ?>;
                const timeLimitMinutes = <?= (int)$materi_detail['waktu_kuis'] ?>;
                let currentSlide = 0;
                let userAnswers = [];
                let timerInterval;

                function startQuiz() {
                    document.getElementById('quizOverlay').style.display = 'flex';
                    buildSlides();
                    startTimer(timeLimitMinutes * 60);
                }

                function buildSlides() {
                    const container = document.getElementById('slideContainer');
                    container.innerHTML = '';

                    questions.forEach((q, index) => {
                        const slide = document.createElement('div');
                        slide.className = `quiz-card ${index === 0 ? 'active' : ''}`;
                        slide.id = `slide-${index}`;

                        slide.innerHTML = `
                        <div class="q-number">Pertanyaan ${index + 1} dari ${questions.length}</div>
                        <div class="q-text">${q.pertanyaan}</div>
                        
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'a')">
                            <input type="radio" name="q_${index}" value="a"> A. ${q.opsi_a}
                        </label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'b')">
                            <input type="radio" name="q_${index}" value="b"> B. ${q.opsi_b}
                        </label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'c')">
                            <input type="radio" name="q_${index}" value="c"> C. ${q.opsi_c}
                        </label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'd')">
                            <input type="radio" name="q_${index}" value="d"> D. ${q.opsi_d}
                        </label>
                    `;
                        container.appendChild(slide);
                    });
                }

                function selectOption(labelElement, qIndex, answer) {
                    // Hapus style selected dari semua opsi di slide ini
                    const slide = document.getElementById(`slide-${qIndex}`);
                    slide.querySelectorAll('.gemini-option').forEach(el => el.classList.remove('selected'));

                    // Tambahkan style ke opsi yang diklik
                    labelElement.classList.add('selected');
                    labelElement.querySelector('input').checked = true;

                    userAnswers[qIndex] = answer;

                    // Nyalakan tombol Next
                    document.getElementById('nextBtn').classList.add('enabled');
                }

                function nextSlide() {
                    // Pindah ke slide berikutnya atau submit
                    document.getElementById(`slide-${currentSlide}`).classList.remove('active');
                    currentSlide++;

                    const nextBtn = document.getElementById('nextBtn');
                    nextBtn.classList.remove('enabled'); // Disable tombol sampai user milih lagi

                    if (currentSlide < questions.length) {
                        document.getElementById(`slide-${currentSlide}`).classList.add('active');
                        if (currentSlide === questions.length - 1) {
                            nextBtn.innerHTML = 'Kumpulkan Jawaban <i class="fas fa-check"></i>';
                        }
                    } else {
                        submitQuiz();
                    }
                }

                function startTimer(durationSeconds) {
                    let timer = durationSeconds;
                    const display = document.getElementById('timerDisplay');

                    timerInterval = setInterval(() => {
                        let minutes = parseInt(timer / 60, 10);
                        let seconds = parseInt(timer % 60, 10);

                        minutes = minutes < 10 ? "0" + minutes : minutes;
                        seconds = seconds < 10 ? "0" + seconds : seconds;

                        display.innerHTML = `<i class="fas fa-stopwatch"></i> ${minutes}:${seconds}`;

                        if (--timer < 0) {
                            clearInterval(timerInterval);
                            alert("Waktu habis! Jawaban akan dikumpulkan otomatis.");
                            submitQuiz();
                        }
                    }, 1000);
                }

                function submitQuiz() {
                    clearInterval(timerInterval);
                    let skorBenar = 0;

                    questions.forEach((q, i) => {
                        if (userAnswers[i] === q.jawaban_benar) {
                            skorBenar++;
                        }
                    });

                    const nilaiAkhir = Math.round((skorBenar / questions.length) * 100);

                    // Tampilkan Modal
                    const modal = document.getElementById('scoreModal');
                    const circle = document.getElementById('scoreCircle');
                    const title = document.getElementById('scoreTitle');
                    const msg = document.getElementById('scoreMessage');

                    circle.innerText = nilaiAkhir;

                    if (nilaiAkhir >= 80) {
                        circle.style.background = 'linear-gradient(135deg, #059669, #10b981)';
                        title.innerText = "Luar Biasa! 🎉";
                        msg.innerText = `Kamu berhasil menjawab benar ${skorBenar} dari ${questions.length} soal.`;
                    } else if (nilaiAkhir >= 50) {
                        circle.style.background = 'linear-gradient(135deg, #f59e0b, #fbbf24)';
                        title.innerText = "Terus Belajar! 👍";
                        msg.innerText = `Kamu menjawab ${skorBenar} benar. Coba pahami lagi materinya ya.`;
                    } else {
                        circle.style.background = 'linear-gradient(135deg, #ef4444, #f87171)';
                        title.innerText = "Jangan Menyerah! 💪";
                        msg.innerText = `Skor kamu belum maksimal. Yuk tonton ulang video penjelasannya!`;
                    }

                    modal.style.display = 'flex';
                }
            </script>
        <?php endif; ?>

    </div>
    <?php if (!$view_materi_id) include '../components/nav.php'; ?>
</body>

</html>