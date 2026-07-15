<?php
session_start();
require_once '../config/database.php';
$user_id = (int) $_SESSION['user_id'];
$current_month = date('Y-m');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_score') {
    $juz_start = (int)$_POST['j_start'];
    $juz_end = (int)$_POST['j_end'];
    $total_q = (int)$_POST['total_q'];
    $score = (int)$_POST['score'];

    mysqli_query($conn, "INSERT INTO game_history (user_id, game_type, juz_start, juz_end, total_q, score) VALUES ('$user_id', 'tebak_surah', '$juz_start', '$juz_end', '$total_q', '$score')");

    if ($score == $total_q && $total_q >= 5) {
        $cek_ach = mysqli_query($conn, "SELECT id FROM achievements WHERE user_id='$user_id' AND nama_penghargaan='Master Tebak Surah'");
        if (mysqli_num_rows($cek_ach) == 0) {
            mysqli_query($conn, "INSERT INTO achievements (user_id, nama_penghargaan) VALUES ('$user_id', 'Master Tebak Surah')");
        }
    }
    exit(json_encode(['status' => 'ok']));
}

$q_leaderboard = mysqli_query($conn, "
    SELECT u.nama_lengkap, SUM(g.score) as total_benar 
    FROM game_history g JOIN users u ON g.user_id = u.id 
    WHERE g.game_type = 'tebak_surah' AND DATE_FORMAT(g.created_at, '%Y-%m') = '$current_month' 
    GROUP BY g.user_id ORDER BY total_benar DESC LIMIT 10
");
$leaderboard = [];
while ($r = mysqli_fetch_assoc($q_leaderboard)) {
    $leaderboard[] = $r;
}

$q_my = mysqli_query($conn, "SELECT SUM(score) as my_score, SUM(total_q - score) as my_wrong, COUNT(id) as play_count FROM game_history WHERE user_id='$user_id' AND game_type='tebak_surah' AND DATE_FORMAT(created_at, '%Y-%m') = '$current_month'");
$my_stats = mysqli_fetch_assoc($q_my);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tebak Surah - Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Gaya CSS sama persis dengan lanjut_ayat.php */
        :root {
            --primary: #3b82f6;
            --dark: #0f172a;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--dark);
            margin: 0;
            padding-bottom: 50px;
        }

        .container {
            max-width: 600px;
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
            background: white;
            width: 45px;
            height: 45px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            text-decoration: none;
            border: 1px solid var(--border);
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border);
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-family: inherit;
            font-size: 1rem;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        .btn-start {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            transition: 0.2s;
        }

        .rank-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid var(--border);
        }

        .rank-num {
            width: 30px;
            height: 30px;
            background: var(--dark);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .rank-1 {
            background: #fbbf24;
        }

        .rank-2 {
            background: #94a3b8;
        }

        .rank-3 {
            background: #b45309;
        }

        .my-stats-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px;
            border-radius: 16px;
            margin-bottom: 20px;
            display: none;
        }

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
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 800;
        }

        .quiz-card {
            background: white;
            padding: 30px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
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
            font-size: 0.95rem;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .audio-player {
            width: 100%;
            margin-bottom: 20px;
            outline: none;
        }

        .q-text-ar {
            font-family: 'Amiri', serif;
            font-size: 2rem;
            text-align: center;
            line-height: 1.8;
            margin-bottom: 30px;
            color: var(--dark);
        }

        .gemini-option {
            display: block;
            padding: 15px 20px;
            border: 2px solid var(--border);
            border-radius: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 700;
            font-size: 1.05rem;
            text-align: center;
            color: #334155;
        }

        .gemini-option:hover {
            border-color: var(--primary);
            background: #f8fafc;
        }

        .gemini-option input[type="radio"] {
            display: none;
        }

        .gemini-option.selected {
            border-color: var(--primary);
            background: #eff6ff;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
        }

        .quiz-footer {
            padding: 20px;
            display: flex;
            justify-content: center;
            background: white;
            position: sticky;
            bottom: 0;
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
        }

        .btn-next.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        .review-card {
            background: white;
            padding: 30px;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            width: 90%;
            max-width: 600px;
            margin: 40px auto;
            display: none;
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

        .ri-item {
            padding: 15px;
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 15px;
            background: #f8fafc;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .btn-share {
            background: #25D366;
            color: white;
            padding: 14px;
            border-radius: 16px;
            width: 100%;
            border: none;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 10px;
            font-family: 'Plus Jakarta Sans';
        }
    </style>
</head>

<body>
    <div class="container" id="mainScreen">
        <div class="header">
            <a href="game.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <h1 style="font-size:1.5rem; font-weight:800; margin:0;">Tebak Surah</h1>
        </div>

        <div class="card">
            <h2 style="font-size:1.2rem; margin-bottom:15px;"><i class="fas fa-sliders-h"></i> Kriteria Permainan</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label>Juz Awal</label>
                    <input type="number" id="f_juz_start" class="form-control" value="30" min="1" max="30">
                </div>
                <div class="form-group">
                    <label>Juz Akhir</label>
                    <input type="number" id="f_juz_end" class="form-control" value="30" min="1" max="30">
                </div>
                <div class="form-group">
                    <label>Jumlah Soal</label>
                    <select id="f_total" class="form-control">
                        <option value="5">5 Soal</option>
                        <option value="10" selected>10 Soal</option>
                        <option value="20">20 Soal</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Waktu (Menit)</label>
                    <select id="f_time" class="form-control">
                        <option value="0">Santai (Tanpa Batas)</option>
                        <option value="3">3 Menit</option>
                        <option value="5">5 Menit</option>
                    </select>
                </div>
            </div>
            <button class="btn-start" id="startBtn" onclick="initGame()">Mulai Bermain <i class="fas fa-play"></i></button>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <h2 style="font-size:1.2rem;"><i class="fas fa-trophy" style="color:#fbbf24;"></i> Top 10 Bulan Ini</h2>
            <button onclick="document.getElementById('myStats').style.display='block'" style="background:none; border:none; color:var(--primary); font-weight:700; cursor:pointer;">Akumulasi Saya</button>
        </div>

        <div class="my-stats-box" id="myStats">
            <h3 style="font-size:1rem; margin-bottom:10px; color:#1e3a8a;">Statistik Kamu (Bulan Ini)</h3>
            <div style="display:flex; gap:15px; font-size:0.9rem; font-weight:600;">
                <div><i class="fas fa-gamepad"></i> Main: <?= $my_stats['play_count'] ?? 0 ?>x</div>
                <div style="color:#3b82f6;"><i class="fas fa-check"></i> Benar: <?= $my_stats['my_score'] ?? 0 ?></div>
                <div style="color:#ef4444;"><i class="fas fa-times"></i> Salah: <?= $my_stats['my_wrong'] ?? 0 ?></div>
            </div>
            <button onclick="this.parentElement.style.display='none'" style="margin-top:10px; padding:5px 10px; border-radius:8px; border:1px solid #3b82f6; background:white; color:#3b82f6; cursor:pointer; font-weight:600;">Tutup</button>
        </div>

        <div class="card" style="padding:15px;">
            <?php if (empty($leaderboard)): ?>
                <div style="text-align:center; padding:20px; color:var(--text-muted);">Belum ada yang bermain bulan ini.</div>
            <?php else: ?>
                <?php foreach ($leaderboard as $idx => $lb): ?>
                    <div class="rank-item">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div class="rank-num <?= $idx < 3 ? 'rank-' . ($idx + 1) : '' ?>"><?= $idx + 1 ?></div>
                            <div style="font-weight:700;"><?= htmlspecialchars($lb['nama_lengkap']) ?></div>
                        </div>
                        <div style="font-weight:800; color:var(--primary);"><?= $lb['total_benar'] ?> <i class="fas fa-star" style="font-size:0.8rem;"></i></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- QUIZ OVERLAY -->
    <div id="quizOverlay">
        <div class="quiz-header">
            <button onclick="location.reload()" style="background:none; border:none; font-size:1.5rem; color:var(--text-muted); cursor:pointer;"><i class="fas fa-times"></i></button>
            <div class="timer-badge" id="timerDisplay">--:--</div>
        </div>
        <div id="slideContainer"></div>
        <div class="review-card" id="reviewArea"></div>
        <div class="quiz-footer" id="quizFooter">
            <button class="btn-next" id="nextBtn" onclick="nextSlide()">Pilih Jawaban Dulu</button>
        </div>
    </div>

    <script>
        let questions = [];
        let currentSlide = 0;
        let userAnswers = [];
        let timerInterval;
        let juzCache = {};

        async function fetchJuzData(juz) {
            if (juzCache[juz]) return juzCache[juz];
            const res = await fetch(`https://api.alquran.cloud/v1/juz/${juz}/ar.alafasy`);
            const data = await res.json();
            juzCache[juz] = data.data.ayahs;
            return data.data.ayahs;
        }

        async function initGame() {
            const startBtn = document.getElementById('startBtn');
            const jStart = parseInt(document.getElementById('f_juz_start').value);
            const jEnd = parseInt(document.getElementById('f_juz_end').value);
            const totalQ = parseInt(document.getElementById('f_total').value);
            const timeLmt = parseInt(document.getElementById('f_time').value);

            if (jStart > jEnd) return Swal.fire('Error', 'Juz awal tidak boleh lebih besar dari Juz akhir', 'error');

            startBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyiapkan Soal...';
            startBtn.disabled = true;

            try {
                questions = [];
                for (let i = 0; i < totalQ; i++) {
                    const rJuz = Math.floor(Math.random() * (jEnd - jStart + 1)) + jStart;
                    const ayahs = await fetchJuzData(rJuz);

                    let rIdx = Math.floor(Math.random() * ayahs.length);
                    const qAyah = ayahs[rIdx];
                    const cAns = `${qAyah.surah.englishName} : Ayat ${qAyah.numberInSurah}`;

                    let wrongAns = [];
                    while (wrongAns.length < 3) {
                        let wIdx = Math.floor(Math.random() * ayahs.length);
                        let wText = `${ayahs[wIdx].surah.englishName} : Ayat ${ayahs[wIdx].numberInSurah}`;
                        if (wText !== cAns && !wrongAns.includes(wText)) wrongAns.push(wText);
                    }

                    let options = [{
                        t: cAns,
                        v: 'benar'
                    }, {
                        t: wrongAns[0],
                        v: 's1'
                    }, {
                        t: wrongAns[1],
                        v: 's2'
                    }, {
                        t: wrongAns[2],
                        v: 's3'
                    }];
                    options.sort(() => Math.random() - 0.5);

                    questions.push({
                        q_audio: qAyah.audio,
                        q_text: qAyah.text,
                        correct_text: cAns,
                        options: options
                    });
                }

                document.getElementById('mainScreen').style.display = 'none';
                document.getElementById('quizOverlay').style.display = 'flex';
                buildSlides();
                if (timeLmt > 0) startTimer(timeLmt * 60);
                else document.getElementById('timerDisplay').innerHTML = '<i class="fas fa-infinity"></i>';

            } catch (error) {
                Swal.fire('Koneksi Gagal', 'Pastikan internet stabil untuk mengunduh soal', 'error');
                startBtn.innerHTML = 'Mulai Bermain <i class="fas fa-play"></i>';
                startBtn.disabled = false;
            }
        }

        function buildSlides() {
            const container = document.getElementById('slideContainer');
            container.innerHTML = '';

            questions.forEach((q, i) => {
                const slide = document.createElement('div');
                slide.className = `quiz-card ${i === 0 ? 'active' : ''}`;
                slide.id = `slide-${i}`;

                let optHtml = '';
                q.options.forEach((opt) => {
                    optHtml += `<label class="gemini-option" onclick="selectOpt(this, ${i}, '${opt.v}')">
                                    <input type="radio" name="q_${i}" value="${opt.v}"> ${opt.t}
                                </label>`;
                });

                slide.innerHTML = `
                    <div class="q-number">Soal ${i + 1} / ${questions.length}</div>
                    <audio controls class="audio-player" controlsList="nodownload"><source src="${q.q_audio}" type="audio/mpeg"></audio>
                    <div class="q-text-ar">${q.q_text} ۝</div>
                    ${optHtml}
                `;
                container.appendChild(slide);
            });
        }

        function selectOpt(label, qIndex, val) {
            const slide = document.getElementById(`slide-${qIndex}`);
            slide.querySelectorAll('.gemini-option').forEach(el => el.classList.remove('selected'));
            label.classList.add('selected');
            label.querySelector('input').checked = true;
            userAnswers[qIndex] = val;

            const btn = document.getElementById('nextBtn');
            btn.classList.add('enabled');
            btn.innerHTML = currentSlide === questions.length - 1 ? 'Selesai <i class="fas fa-check"></i>' : 'Lanjut <i class="fas fa-arrow-right"></i>';
        }

        function nextSlide() {
            const audio = document.getElementById(`slide-${currentSlide}`).querySelector('audio');
            if (audio) audio.pause();

            document.getElementById(`slide-${currentSlide}`).classList.remove('active');
            currentSlide++;

            const btn = document.getElementById('nextBtn');
            btn.classList.remove('enabled');
            btn.innerHTML = 'Pilih Jawaban Dulu';

            if (currentSlide < questions.length) {
                document.getElementById(`slide-${currentSlide}`).classList.add('active');
                const nextAudio = document.getElementById(`slide-${currentSlide}`).querySelector('audio');
                if (nextAudio) nextAudio.play().catch(e => {});
            } else {
                finishGame();
            }
        }

        function startTimer(duration) {
            let timer = duration;
            const display = document.getElementById('timerDisplay');
            timerInterval = setInterval(() => {
                let m = parseInt(timer / 60, 10);
                let s = parseInt(timer % 60, 10);
                display.innerHTML = `<i class="fas fa-stopwatch"></i> ${m<10?'0'+m:m}:${s<10?'0'+s:s}`;
                if (--timer < 0) {
                    clearInterval(timerInterval);
                    alert("Waktu Habis!");
                    finishGame();
                }
            }, 1000);
        }

        function finishGame() {
            if (timerInterval) clearInterval(timerInterval);
            document.getElementById('slideContainer').style.display = 'none';
            document.getElementById('quizFooter').style.display = 'none';

            let skorBenar = 0;
            let reviewHtml = '';

            questions.forEach((q, i) => {
                const isBenar = userAnswers[i] === 'benar';
                if (isBenar) skorBenar++;
                reviewHtml += `
                    <div class="ri-item" style="border-color:${isBenar ? '#60a5fa' : '#f87171'};">
                        <div style="font-size:0.9rem; color:${isBenar ? '#3b82f6' : '#ef4444'}; margin-bottom:10px;">
                            ${isBenar ? '<i class="fas fa-check-circle"></i> Tepat!' : '<i class="fas fa-times-circle"></i> Keliru'}
                        </div>
                        <div style="font-family:'Amiri'; font-size:1.5rem; margin-bottom:10px;">${q.q_text}</div>
                        <div style="color:var(--dark); font-size:1.1rem;">Benar: ${q.correct_text}</div>
                    </div>
                `;
            });

            const nilai = Math.round((skorBenar / questions.length) * 100);
            const reviewArea = document.getElementById('reviewArea');
            reviewArea.innerHTML = `
                <div class="score-circle" style="background: ${nilai>=80 ? '#3b82f6' : (nilai>=50 ? '#fbbf24' : '#ef4444')}">${nilai}</div>
                <h2 style="font-size:1.8rem; text-align:center; margin-bottom:10px;">${nilai>=80 ? 'Masya Allah! 🎉' : 'Tetap Semangat! 💪'}</h2>
                <p style="text-align:center; color:var(--text-muted); margin-bottom:20px;">Kamu berhasil menebak ${skorBenar} surah dengan tepat dari total ${questions.length} soal.</p>
                
                <button class="btn-share" onclick="shareWA(${skorBenar}, ${questions.length})"><i class="fab fa-whatsapp"></i> Bagikan ke Status WA</button>
                <button class="btn-start" style="margin-top:10px; background:var(--dark);" onclick="location.reload()"><i class="fas fa-home"></i> Papan Peringkat</button>
                
                <h3 style="margin-top:30px; font-size:1.1rem; text-align:center; border-bottom:1px solid #e2e8f0; padding-bottom:10px;">Review Tebakan</h3>
                ${reviewHtml}
            `;
            reviewArea.style.display = 'block';

            const fd = new FormData();
            fd.append('action', 'save_score');
            fd.append('j_start', document.getElementById('f_juz_start').value);
            fd.append('j_end', document.getElementById('f_juz_end').value);
            fd.append('total_q', questions.length);
            fd.append('score', skorBenar);
            fetch('tebak_surah.php', {
                method: 'POST',
                body: fd
            });
        }

        function shareWA(benar, total) {
            const text = `Alhamdulillah! Saya baru saja menyelesaikan Tantangan Tebak Surah di aplikasi Hifzly dan berhasil menjawab benar ${benar} dari ${total} soal. Yuk uji hafalanmu juga! 🌟`;
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`);
        }
    </script>
</body>

</html>