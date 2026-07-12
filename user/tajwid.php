<?php
session_start();
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
}
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary: #059669;
            --primary-light: #d1fae5;
            --dark: #0f172a;
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
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--dark);
            padding-bottom: 100px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .back-btn {
            background: white;
            width: 45px;
            height: 45px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border);
            transition: 0.2s;
        }

        .back-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--dark);
            flex-grow: 1;
        }

        /* LIST MATERI */
        .grid-materi {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .materi-card {
            background: var(--card-bg);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            text-decoration: none;
            color: var(--dark);
            display: flex;
            flex-direction: column;
            transition: 0.3s;
        }

        .materi-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(5, 150, 105, 0.1);
            border-color: var(--primary);
        }

        .mc-cover {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #e2e8f0;
        }

        .mc-body {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .mc-title {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .mc-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-top: auto;
        }

        .badge {
            background: var(--bg);
            padding: 6px 14px;
            border-radius: 20px;
            color: var(--text-muted);
        }

        /* DETAIL MATERI */
        .detail-cover {
            width: 100%;
            height: 350px;
            object-fit: cover;
            border-radius: 24px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .detail-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
            border: 1px solid var(--border);
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .rich-content {
            line-height: 1.8;
            font-size: 1.1rem;
            color: #334155;
        }

        .rich-content img {
            max-width: 100%;
            height: auto;
            border-radius: 16px;
            margin: 20px 0;
        }

        .rich-content h2,
        .rich-content h3 {
            margin-top: 30px;
            margin-bottom: 15px;
            color: var(--dark);
            font-weight: 800;
        }

        .rich-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .rich-content th,
        .rich-content td {
            border: 1px solid var(--border);
            padding: 12px;
            text-align: left;
        }

        .rich-content th {
            background: #f8fafc;
            font-weight: 700;
        }

        .action-bar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }

        .btn {
            padding: 16px;
            border-radius: 16px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-size: 1rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-pdf {
            background: white;
            color: var(--dark);
            border: 2px solid var(--border);
        }

        .btn-pdf:hover {
            background: #f1f5f9;
        }

        .btn-quiz {
            background: var(--dark);
            color: white;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
        }

        .btn-quiz:hover {
            background: var(--primary);
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
        }

        /* QUIZ OVERLAY */
        #quizOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f1f5f9;
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow-y: auto;
        }

        .quiz-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }

        .timer-badge {
            background: #fee2e2;
            color: #ef4444;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quiz-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }

        .quiz-card {
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 600px;
            display: none;
            animation: slideIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        .quiz-card.active {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        .q-img {
            width: 100%;
            max-height: 250px;
            object-fit: contain;
            background: var(--bg);
            border-radius: 16px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
        }

        .q-number {
            font-size: 0.95rem;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 15px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .q-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .gemini-option {
            display: block;
            padding: 20px;
            border: 2px solid var(--border);
            border-radius: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 700;
            font-size: 1.05rem;
            color: #475569;
            position: relative;
        }

        .gemini-option:hover {
            border-color: var(--primary-light);
            background: #f8fafc;
            transform: translateY(-2px);
        }

        .gemini-option input[type="radio"] {
            display: none;
        }

        .gemini-option.selected {
            border-color: var(--primary);
            background: #f0fdf4;
            color: var(--primary-dark);
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.15);
            transform: translateY(-2px);
        }

        .quiz-footer {
            padding: 20px;
            display: flex;
            justify-content: center;
            background: white;
            position: sticky;
            bottom: 0;
            box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-next {
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px 40px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            opacity: 0.5;
            pointer-events: none;
            transition: 0.3s;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
        }

        .btn-next.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        /* REVIEW SCREEN */
        .review-card {
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            display: none;
        }

        .score-circle {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 800;
            margin: 0 auto 30px auto;
            color: white;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .review-item {
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 15px;
            text-align: left;
            background: #f8fafc;
        }

        .ri-q {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 15px;
            color: var(--dark);
        }

        .ri-ans {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .ri-correct {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #34d399;
        }

        .ri-wrong {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }
    </style>
</head>

<body>
    <div class="container">

        <?php if (!$view_materi_id): ?>
            <div class="header">
                <h1 class="page-title">📚 Modul Pembelajaran</h1>
            </div>
            <div class="grid-materi">
                <?php foreach ($daftar_materi as $m):
                    $img_src = !empty($m['cover_image']) ? '../uploads/' . $m['cover_image'] : 'https://via.placeholder.com/600x400/e2e8f0/64748b?text=Materi+Hifzly';
                ?>
                    <a href="tajwid.php?id=<?= $m['id'] ?>" class="materi-card">
                        <img src="<?= $img_src ?>" class="mc-cover" alt="Cover">
                        <div class="mc-body">
                            <div class="mc-title"><?= htmlspecialchars($m['judul']) ?></div>
                            <div class="mc-badges">
                                <?php if ($m['waktu_kuis'] > 0): ?><span class="badge"><i class="far fa-clock"></i> <?= $m['waktu_kuis'] ?>m Kuis</span><?php endif; ?>
                                <?php if ($m['youtube_url']): ?><span class="badge"><i class="fab fa-youtube" style="color:#ef4444;"></i> Video</span><?php endif; ?>
                                <?php if ($m['pdf_file']): ?><span class="badge"><i class="fas fa-file-pdf" style="color:#3b82f6;"></i> Modul PDF</span><?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="header">
                <a href="tajwid.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <h1 class="page-title" style="font-size:1.3rem;">Detail Modul</h1>
            </div>

            <div id="materiToPdf">
                <?php if (!empty($materi_detail['cover_image'])): ?>
                    <img src="../uploads/<?= $materi_detail['cover_image'] ?>" class="detail-cover" alt="Cover">
                <?php endif; ?>

                <div class="detail-card">
                    <h1 style="font-size:2.2rem; margin-bottom:25px; color:var(--dark); font-weight:800;"><?= htmlspecialchars($materi_detail['judul']) ?></h1>

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

            <div class="action-bar" id="materiActions">
                <?php if (!empty($materi_detail['pdf_file'])): ?>
                    <a href="../uploads/<?= $materi_detail['pdf_file'] ?>" target="_blank" class="btn btn-pdf"><i class="fas fa-file-download"></i> Unduh Modul (PDF)</a>
                <?php else: ?>
                    <button class="btn btn-pdf" onclick="exportPDF()"><i class="fas fa-print"></i> Cetak ke PDF</button>
                <?php endif; ?>

                <?php if (count($soal_kuis) > 0): ?>
                    <button class="btn btn-quiz" onclick="startQuiz()"><i class="fas fa-rocket"></i> Mulai Kuis Evaluasi</button>
                <?php endif; ?>
            </div>

            <!-- LAYAR KUIS INTERAKTIF -->
            <div id="quizOverlay">
                <div class="quiz-header">
                    <button onclick="location.reload()" style="background:none; border:none; font-size:1.5rem; color:var(--text-muted); cursor:pointer;"><i class="fas fa-times"></i></button>
                    <?php if ($materi_detail['waktu_kuis'] > 0): ?>
                        <div class="timer-badge" id="timerDisplay"><i class="fas fa-stopwatch"></i> --:--</div>
                    <?php else: ?>
                        <div class="timer-badge" style="background:#d1fae5; color:#059669;"><i class="fas fa-infinity"></i> Santai</div>
                    <?php endif; ?>
                </div>

                <div class="quiz-container">
                    <div id="slideContainer" style="width:100%; display:flex; justify-content:center;"></div>

                    <!-- LAYAR REVIEW HASIL (Disembunyikan di awal) -->
                    <div class="review-card" id="reviewArea">
                        <div id="scoreCircle" class="score-circle">100</div>
                        <h2 id="scoreTitle" style="margin-bottom:10px; color:var(--dark); font-size:2rem; text-align:center;">Luar Biasa!</h2>
                        <p id="scoreMessage" style="color:var(--text-muted); margin-bottom:30px; text-align:center; font-size:1.1rem;"></p>

                        <h3 style="margin-bottom:15px; border-bottom:2px solid var(--border); padding-bottom:10px;">Pembahasan Kuis:</h3>
                        <div id="reviewList"></div>

                        <button class="btn btn-quiz" style="width:100%; margin-top:20px;" onclick="location.reload()"><i class="fas fa-home"></i> Kembali ke Modul</button>
                    </div>
                </div>

                <div class="quiz-footer" id="quizFooter">
                    <button class="btn-next" id="nextBtn" onclick="nextSlide()">Pilih Jawaban Dulu</button>
                </div>
            </div>

            <script>
                // --- Fitur Export PDF Alternatif ---
                function exportPDF() {
                    const element = document.getElementById('materiToPdf');
                    html2pdf().set({
                        margin: 15,
                        filename: 'Materi_Hifzly.pdf',
                        html2canvas: {
                            scale: 2
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        }
                    }).from(element).save();
                }

                // --- QUIZ ENGINE ---
                const questions = <?= json_encode($soal_kuis) ?>;
                const timeLimit = <?= (int)$materi_detail['waktu_kuis'] ?>;
                let currentSlide = 0;
                let userAnswers = [];
                let timerInterval;

                function startQuiz() {
                    document.body.style.overflow = 'hidden'; // Kunci scroll layar utama
                    document.getElementById('quizOverlay').style.display = 'flex';
                    buildSlides();
                    if (timeLimit > 0) startTimer(timeLimit * 60);
                }

                function buildSlides() {
                    const container = document.getElementById('slideContainer');
                    container.innerHTML = '';

                    questions.forEach((q, index) => {
                        const slide = document.createElement('div');
                        slide.className = `quiz-card ${index === 0 ? 'active' : ''}`;
                        slide.id = `slide-${index}`;

                        const imgHtml = q.gambar ? `<img src="../uploads/${q.gambar}" class="q-img">` : '';

                        slide.innerHTML = `
                        <div class="q-number">Soal ${index + 1} dari ${questions.length}</div>
                        ${imgHtml}
                        <div class="q-text">${q.pertanyaan}</div>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'a')"><input type="radio" name="q_${index}" value="a"> A. ${q.opsi_a}</label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'b')"><input type="radio" name="q_${index}" value="b"> B. ${q.opsi_b}</label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'c')"><input type="radio" name="q_${index}" value="c"> C. ${q.opsi_c}</label>
                        <label class="gemini-option" onclick="selectOption(this, ${index}, 'd')"><input type="radio" name="q_${index}" value="d"> D. ${q.opsi_d}</label>
                    `;
                        container.appendChild(slide);
                    });
                }

                function selectOption(labelElement, qIndex, answer) {
                    const slide = document.getElementById(`slide-${qIndex}`);
                    slide.querySelectorAll('.gemini-option').forEach(el => el.classList.remove('selected'));
                    labelElement.classList.add('selected');
                    labelElement.querySelector('input').checked = true;
                    userAnswers[qIndex] = answer;

                    const nextBtn = document.getElementById('nextBtn');
                    nextBtn.classList.add('enabled');
                    nextBtn.innerHTML = currentSlide === questions.length - 1 ? 'Kumpulkan Jawaban <i class="fas fa-check-double"></i>' : 'Lanjut ke Soal Berikutnya <i class="fas fa-arrow-right"></i>';
                }

                function nextSlide() {
                    document.getElementById(`slide-${currentSlide}`).classList.remove('active');
                    currentSlide++;
                    const nextBtn = document.getElementById('nextBtn');
                    nextBtn.classList.remove('enabled');
                    nextBtn.innerHTML = 'Pilih Jawaban Dulu';

                    if (currentSlide < questions.length) {
                        document.getElementById(`slide-${currentSlide}`).classList.add('active');
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
                        display.innerHTML = `<i class="fas fa-stopwatch"></i> ${minutes < 10 ? '0'+minutes : minutes}:${seconds < 10 ? '0'+seconds : seconds}`;
                        if (--timer < 0) {
                            clearInterval(timerInterval);
                            alert("Waktu Habis!");
                            submitQuiz();
                        }
                    }, 1000);
                }

                function submitQuiz() {
                    if (timerInterval) clearInterval(timerInterval);
                    document.getElementById('slideContainer').style.display = 'none';
                    document.getElementById('quizFooter').style.display = 'none';

                    const reviewArea = document.getElementById('reviewArea');
                    const reviewList = document.getElementById('reviewList');
                    let skorBenar = 0;
                    let reviewHTML = '';

                    questions.forEach((q, i) => {
                        const uAns = userAnswers[i] || 'kosong';
                        const isBenar = uAns === q.jawaban_benar;
                        if (isBenar) skorBenar++;

                        // Mapping string jawaban
                        const opsiMap = {
                            'a': q.opsi_a,
                            'b': q.opsi_b,
                            'c': q.opsi_c,
                            'd': q.opsi_d,
                            'kosong': 'Tidak dijawab'
                        };
                        const textUser = opsiMap[uAns];
                        const textBenar = opsiMap[q.jawaban_benar];

                        reviewHTML += `
                        <div class="review-item">
                            <div class="ri-q">${i+1}. ${q.pertanyaan}</div>
                            ${isBenar 
                                ? `<div class="ri-ans ri-correct"><i class="fas fa-check-circle"></i> Jawabanmu Benar: ${textUser}</div>` 
                                : `<div class="ri-ans ri-wrong"><i class="fas fa-times-circle"></i> Jawabanmu: ${textUser}</div>
                                   <div class="ri-ans ri-correct"><i class="fas fa-check-circle"></i> Jawaban Tepat: ${textBenar}</div>`
                            }
                        </div>
                    `;
                    });

                    const nilaiAkhir = Math.round((skorBenar / questions.length) * 100);
                    const circle = document.getElementById('scoreCircle');
                    circle.innerText = nilaiAkhir;

                    if (nilaiAkhir >= 80) {
                        circle.style.background = 'linear-gradient(135deg, #059669, #10b981)';
                        document.getElementById('scoreTitle').innerText = "Hebat Sekali! 🎉";
                    } else if (nilaiAkhir >= 50) {
                        circle.style.background = 'linear-gradient(135deg, #f59e0b, #fbbf24)';
                        document.getElementById('scoreTitle').innerText = "Lumayan Bagus! 👍";
                    } else {
                        circle.style.background = 'linear-gradient(135deg, #ef4444, #f87171)';
                        document.getElementById('scoreTitle').innerText = "Belajar Lagi Ya! 💪";
                    }

                    document.getElementById('scoreMessage').innerText = `Kamu berhasil menjawab ${skorBenar} dari ${questions.length} soal dengan benar.`;
                    reviewList.innerHTML = reviewHTML;
                    reviewArea.style.display = 'block';

                    // Scroll ke atas overlay
                    document.getElementById('quizOverlay').scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            </script>
        <?php endif; ?>
    </div>
    <?php if (!$view_materi_id) include '../components/nav.php'; ?>
</body>

</html>