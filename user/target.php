<?php
session_start();
require_once '../config/database.php';

/** @var mysqli $conn */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$current_date = date('Y-m-d');

// --- 1. AUTO-CREATE TABEL YANG DIBUTUHKAN JIKA BELUM ADA ---
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS user_targets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    tipe_target VARCHAR(20) NOT NULL, -- harian, mingguan, bulanan
    jumlah_target INT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_kat (user_id, kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS user_todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    task_time TIME NOT NULL,
    task_date DATE NOT NULL,
    is_completed TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Memastikan tabel bookmark ada (untuk sinkronisasi bacaan)
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    surah INT NOT NULL,
    ayat INT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_bm (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// --- 2. AJAX HANDLERS ---
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'save_target') {
        $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
        $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
        $jumlah = (int) $_POST['jumlah'];

        $cek = mysqli_query($conn, "SELECT id FROM user_targets WHERE user_id='$user_id' AND kategori='$kategori'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE user_targets SET tipe_target='$tipe', jumlah_target='$jumlah' WHERE user_id='$user_id' AND kategori='$kategori'");
        } else {
            mysqli_query($conn, "INSERT INTO user_targets (user_id, kategori, tipe_target, jumlah_target) VALUES ('$user_id', '$kategori', '$tipe', '$jumlah')");
        }
        exit('ok');
    }
    if ($_POST['action'] == 'add_todo') {
        $task = mysqli_real_escape_string($conn, $_POST['task']);
        $time = mysqli_real_escape_string($conn, $_POST['time']);
        mysqli_query($conn, "INSERT INTO user_todos (user_id, task_name, task_time, task_date) VALUES ('$user_id', '$task', '$time', '$current_date')");
        exit('ok');
    }
    if ($_POST['action'] == 'toggle_todo') {
        $todo_id = (int) $_POST['todo_id'];
        $status = (int) $_POST['status'];
        mysqli_query($conn, "UPDATE user_todos SET is_completed='$status' WHERE id='$todo_id' AND user_id='$user_id'");
        exit('ok');
    }
    if ($_POST['action'] == 'delete_todo') {
        $todo_id = (int) $_POST['todo_id'];
        mysqli_query($conn, "DELETE FROM user_todos WHERE id='$todo_id' AND user_id='$user_id'");
        exit('ok');
    }
}

// --- 3. AMBIL DATA TARGET & SINKRONISASI BOOKMARK (TILAWAH) ---
// Ambil pengaturan target user (Default: Harian 10 Ayat)
$q_target = mysqli_query($conn, "SELECT * FROM user_targets WHERE user_id='$user_id' AND kategori='tilawah'");
if(mysqli_num_rows($q_target) > 0) {
    $row_tgt = mysqli_fetch_assoc($q_target);
    $tipe_target = $row_tgt['tipe_target'];
    $jumlah_target = (int) $row_tgt['jumlah_target'];
} else {
    $tipe_target = 'harian';
    $jumlah_target = 10;
}

// Logika Pintar: Cari START (Dari Mana) dan END (Sampai Mana)
// Start = Ayat setelah record Mutabaah terakhir. End = Bookmark saat ini.
$q_last_mutabaah = mysqli_query($conn, "SELECT surah, ayah_end FROM mutabaah WHERE user_id='$user_id' ORDER BY activity_date DESC, activity_time DESC LIMIT 1");
if(mysqli_num_rows($q_last_mutabaah) > 0) {
    $row_lm = mysqli_fetch_assoc($q_last_mutabaah);
    $start_surah = $row_lm['surah'];
    $start_ayat = $row_lm['ayah_end'] + 1; // Mulai dari ayat berikutnya
} else {
    $start_surah = 1; $start_ayat = 1;
}

$q_bookmark = mysqli_query($conn, "SELECT surah, ayat FROM bookmarks WHERE user_id='$user_id' LIMIT 1");
if(mysqli_num_rows($q_bookmark) > 0) {
    $row_bm = mysqli_fetch_assoc($q_bookmark);
    $end_surah = $row_bm['surah'];
    $end_ayat = $row_bm['ayat'];
} else {
    // Jika belum ada bookmark, set sama dengan start
    $end_surah = $start_surah; $end_ayat = $start_ayat - 1;
}

// Hitung Progres Ayat (Asumsi masih di surah yang sama untuk kemudahan logika dasar)
$progress_ayat = 0;
if($end_surah == $start_surah && $end_ayat >= $start_ayat) {
    $progress_ayat = ($end_ayat - $start_ayat) + 1;
} elseif ($end_surah > $start_surah) {
    // Jika pindah surah, kita asumsikan minimal progressnya adalah ayat dari surah baru
    $progress_ayat = $end_ayat + 5; // Estimasi jika beda surah
}

$persentase = ($jumlah_target > 0) ? ($progress_ayat / $jumlah_target) * 100 : 0;
if ($persentase > 100) $persentase = 100;
$target_tercapai = ($progress_ayat >= $jumlah_target && $jumlah_target > 0);

// --- 4. AMBIL DATA TODO LIST HARI INI ---
$q_todos = mysqli_query($conn, "SELECT * FROM user_todos WHERE user_id='$user_id' AND task_date='$current_date' ORDER BY task_time ASC");
$todos = [];
while($row = mysqli_fetch_assoc($q_todos)) { $todos[] = $row; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target & Jadwal - Hifzly</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669; --primary-light: #d1fae5; --dark: #1e293b;
            --text-muted: #64748b; --bg: #f8fafc; --card-bg: #ffffff;
            --border: #e2e8f0; --spacing: 24px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg); color: var(--dark); padding-bottom: 100px; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }

        .page-header { text-align: center; margin-bottom: 20px; }
        .page-title { font-size: 1.5rem; font-weight: 700; color: var(--dark); }

        /* KATEGORI SCROLL (Tilawah, Hafalan, dll) */
        .category-tabs { display: flex; overflow-x: auto; gap: 10px; padding-bottom: 10px; margin-bottom: 15px; scrollbar-width: none; }
        .category-tabs::-webkit-scrollbar { display: none; }
        .cat-btn { white-space: nowrap; padding: 10px 20px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; cursor: pointer; border: 1.5px solid var(--border); background: var(--card-bg); color: var(--text-muted); transition: 0.2s; }
        .cat-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        .cat-btn.disabled { opacity: 0.5; cursor: not-allowed; }

        /* Card Global */
        .card { background: var(--card-bg); border-radius: 20px; padding: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); border: 1px solid var(--border); margin-bottom: var(--spacing); }
        
        /* Progress Area */
        .target-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .th-title { font-size: 1.1rem; font-weight: 700; color: var(--dark); }
        .btn-edit-target { font-size: 0.8rem; color: var(--primary); background: var(--primary-light); padding: 5px 12px; border-radius: 12px; cursor: pointer; border: none; font-weight: 600; }
        
        .progress-bar-bg { width: 100%; height: 12px; background: #e2e8f0; border-radius: 10px; overflow: hidden; margin: 15px 0; }
        .progress-bar-fill { height: 100%; background: var(--primary); border-radius: 10px; width: <?= $persentase ?>%; transition: 1s ease-in-out; }

        /* Info Bookmark Connection */
        .bookmark-info { background: #f8fafc; border: 1px dashed #cbd5e1; padding: 12px; border-radius: 12px; font-size: 0.85rem; margin-bottom: 15px; }
        .bm-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .bm-row span { font-weight: 600; color: var(--dark); }

        /* Peringatan / Warning */
        .warning-box { background: #fef3c7; border: 1px solid #fde68a; padding: 15px; border-radius: 15px; text-align: center; margin-bottom: 15px; display: <?= $target_tercapai ? 'block' : 'none' ?>; }
        .warning-title { font-weight: 700; color: #d97706; margin-bottom: 10px; font-size: 1rem; }
        .btn-autofill { background: #d97706; color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 600; display: inline-block; font-size: 0.9rem; transition: 0.2s; }
        .btn-autofill:hover { background: #b45309; }

        /* TODO LIST (Jadwal) */
        .todo-header { font-size: 1.1rem; font-weight: 700; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .todo-input-group { display: flex; gap: 10px; margin-bottom: 20px; }
        .todo-input { flex: 1; padding: 10px 15px; border: 1px solid var(--border); border-radius: 12px; outline: none; }
        .todo-time { width: 110px; padding: 10px; border: 1px solid var(--border); border-radius: 12px; outline: none; }
        .btn-add-todo { background: var(--primary); color: white; border: none; padding: 0 20px; border-radius: 12px; cursor: pointer; font-weight: 600; }

        .todo-list { display: flex; flex-direction: column; gap: 10px; }
        .todo-item { display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--border); border-radius: 12px; background: #fafafa; }
        .todo-item.completed { opacity: 0.6; background: #f1f5f9; text-decoration: line-through; }
        .todo-checkbox { width: 22px; height: 22px; cursor: pointer; accent-color: var(--primary); }
        .todo-text { flex: 1; font-weight: 500; font-size: 0.95rem; }
        .todo-time-badge { font-size: 0.75rem; background: #e2e8f0; padding: 4px 8px; border-radius: 8px; font-weight: 600; }
        .btn-del-todo { color: #ef4444; background: none; border: none; cursor: pointer; font-size: 1.1rem; }

        /* Modal Overlay */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .modal-content { background: white; padding: 25px; border-radius: 20px; width: 90%; max-width: 400px; }
        .modal-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 15px; }
        .form-select, .form-input { width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 10px; margin-bottom: 15px; outline: none; }
        .modal-actions { display: flex; gap: 10px; }
        .btn-cancel { flex: 1; padding: 12px; background: #f1f5f9; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; }
        .btn-save { flex: 1; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🎯 Target & Jadwal</h1>
        </div>

        <!-- TABS KATEGORI -->
        <div class="category-tabs">
            <div class="cat-btn active">Tilawah</div>
            <div class="cat-btn disabled" onclick="alert('Fitur segera hadir!')">Hafalan</div>
            <div class="cat-btn disabled" onclick="alert('Fitur segera hadir!')">Murojaah</div>
            <div class="cat-btn disabled" onclick="alert('Fitur segera hadir!')">Belajar Tajwid</div>
        </div>

        <!-- PROGRESS TILAWAH -->
        <div class="card">
            <div class="target-header">
                <div>
                    <div class="th-title">Target Tilawah <span style="text-transform:capitalize;">(<?= $tipe_target ?>)</span></div>
                    <div style="font-size:0.85rem; color:var(--text-muted); margin-top:3px;">Konsisten baca <?= $jumlah_target ?> Ayat</div>
                </div>
                <button class="btn-edit-target" onclick="openTargetModal()"><i class="fas fa-cog"></i> Atur</button>
            </div>

            <!-- Bookmark Tracker Info -->
            <div class="bookmark-info">
                <div class="bm-row">Mulai dari: <span id="start-surah-label">Surah <?= $start_surah ?></span> : <?= $start_ayat ?></div>
                <div class="bm-row">Sampai (Bookmark): <span id="end-surah-label">Surah <?= $end_surah ?></span> : <?= $end_ayat ?></div>
                <div style="margin-top:8px; font-size:0.8rem; color:var(--primary);"><i class="fas fa-sync-alt"></i> Tersinkronisasi otomatis dengan bookmark terakhir.</div>
            </div>

            <div style="display:flex; justify-content:space-between; font-weight:600; font-size:0.9rem;">
                <span>Progres Hari Ini</span>
                <span style="color:var(--primary);"><?= $progress_ayat ?> / <?= $jumlah_target ?> Ayat</span>
            </div>
            
            <div class="progress-bar-bg">
                <div class="progress-bar-fill"></div>
            </div>

            <!-- WARNING: CATAT KE MUTABAAH -->
            <div class="warning-box">
                <div class="warning-title"><i class="fas fa-bell"></i> Alhamdulillah, Target Tercapai!</div>
                <div style="font-size:0.85rem; color:#b45309; margin-bottom:12px;">Kamu sudah membaca sesuai target. Jangan lupa catat ke jurnal Mutabaah agar datamu tersimpan.</div>
                <!-- URL Auto-fill ke Mutabaah -->
                <a href="mutabaah.php?auto=1&kategori=Tilawah&surah=<?= $start_surah ?>&start=<?= $start_ayat ?>&end=<?= $end_ayat ?>" class="btn-autofill">
                    <i class="fas fa-pen"></i> Catat ke Mutabaah Sekarang
                </a>
            </div>
        </div>

        <!-- JADWAL / TODO LIST -->
        <div class="card">
            <div class="todo-header">
                <div><i class="far fa-calendar-check" style="color:var(--primary);"></i> Jadwal Hari Ini</div>
                <div style="font-size:0.8rem; font-weight:500; color:var(--text-muted);"><?= date('d M Y') ?></div>
            </div>

            <form class="todo-input-group" id="todoForm" onsubmit="addTodo(event)">
                <input type="text" id="todoTask" class="todo-input" placeholder="Rencana baca/murojaah..." required>
                <input type="time" id="todoTime" class="todo-time" required>
                <button type="submit" class="btn-add-todo"><i class="fas fa-plus"></i></button>
            </form>

            <div class="todo-list" id="todoList">
                <?php if(empty($todos)): ?>
                    <div style="text-align:center; padding:20px; color:var(--text-muted); font-size:0.9rem;">Belum ada jadwal untuk hari ini.</div>
                <?php else: ?>
                    <?php foreach($todos as $t): ?>
                        <div class="todo-item <?= $t['is_completed'] ? 'completed' : '' ?>" id="todo-<?= $t['id'] ?>">
                            <input type="checkbox" class="todo-checkbox" <?= $t['is_completed'] ? 'checked' : '' ?> onchange="toggleTodo(<?= $t['id'] ?>, this.checked)">
                            <div class="todo-text"><?= htmlspecialchars($t['task_name']) ?></div>
                            <div class="todo-time-badge"><?= date('H:i', strtotime($t['task_time'])) ?></div>
                            <button class="btn-del-todo" onclick="deleteTodo(<?= $t['id'] ?>)"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- MODAL ATUR TARGET -->
    <div class="modal-overlay" id="targetModal">
        <div class="modal-content">
            <div class="modal-title">Atur Target Konsisten</div>
            <label style="font-size:0.85rem; font-weight:600; margin-bottom:5px; display:block;">Tipe Target</label>
            <select id="modalTipe" class="form-select">
                <option value="harian" <?= $tipe_target=='harian'?'selected':'' ?>>Harian</option>
                <option value="mingguan" <?= $tipe_target=='mingguan'?'selected':'' ?>>Mingguan</option>
                <option value="bulanan" <?= $tipe_target=='bulanan'?'selected':'' ?>>Bulanan</option>
            </select>
            
            <label style="font-size:0.85rem; font-weight:600; margin-bottom:5px; display:block;">Jumlah Ayat</label>
            <input type="number" id="modalJumlah" class="form-input" value="<?= $jumlah_target ?>" min="1">
            
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeTargetModal()">Batal</button>
                <button class="btn-save" onclick="saveTarget()">Simpan Target</button>
            </div>
        </div>
    </div>

    <?php include '../components/nav.php'; ?>

    <script>
        // Fetch Nama Surah API agar tampilan lebih cantik
        fetch(`https://equran.id/api/v2/surat/<?= $start_surah ?>`).then(r=>r.json()).then(d=> { document.getElementById('start-surah-label').innerText = d.data.namaLatin; });
        fetch(`https://equran.id/api/v2/surat/<?= $end_surah ?>`).then(r=>r.json()).then(d=> { document.getElementById('end-surah-label').innerText = d.data.namaLatin; });

        // --- TARGET SETTINGS ---
        function openTargetModal() { document.getElementById('targetModal').style.display = 'flex'; }
        function closeTargetModal() { document.getElementById('targetModal').style.display = 'none'; }
        function saveTarget() {
            const tipe = document.getElementById('modalTipe').value;
            const jumlah = document.getElementById('modalJumlah').value;
            
            const fd = new URLSearchParams();
            fd.append('action', 'save_target'); fd.append('kategori', 'tilawah');
            fd.append('tipe', tipe); fd.append('jumlah', jumlah);

            fetch('target.php', { method:'POST', body: fd })
            .then(() => location.reload());
        }

        // --- TODO LIST / JADWAL AJAX ---
        function addTodo(e) {
            e.preventDefault();
            const task = document.getElementById('todoTask').value;
            const time = document.getElementById('todoTime').value;
            
            const fd = new URLSearchParams();
            fd.append('action', 'add_todo'); fd.append('task', task); fd.append('time', time);
            
            fetch('target.php', { method:'POST', body: fd }).then(() => location.reload());
        }

        function toggleTodo(id, isChecked) {
            const fd = new URLSearchParams();
            fd.append('action', 'toggle_todo'); fd.append('todo_id', id); fd.append('status', isChecked ? 1 : 0);
            fetch('target.php', { method:'POST', body: fd })
            .then(() => {
                const item = document.getElementById('todo-'+id);
                if(isChecked) item.classList.add('completed');
                else item.classList.remove('completed');
            });
        }

        function deleteTodo(id) {
            if(!confirm("Hapus jadwal ini?")) return;
            const fd = new URLSearchParams();
            fd.append('action', 'delete_todo'); fd.append('todo_id', id);
            fetch('target.php', { method:'POST', body: fd }).then(() => location.reload());
        }
    </script>
</body>
</html>