<?php
session_start();
if (file_exists('../config/database.php')) {
    require_once '../config/database.php';
}

// Cek login (sesuaikan dengan sistem kamu)
$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['role'] === 'user';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Pencapaian — Hifzly</title>
    <link rel="icon" type="image/png" href="../assets/icon/logo.png">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #059669;
            --bg-color: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --line-color: #e2e8f0;
            --gold: #f59e0b;
            --silver: #94a3b8;
            --bronze: #b45309;
            --fire: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            padding-bottom: 100px;
        }

        /* HEADER */
        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding-top: env(safe-area-inset-top);
        }

        .btn-back {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid var(--line-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 1.1rem;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: #f1f5f9;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
        }

        /* TABS */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            overflow-x: auto;
            padding-bottom: 5px;
            scrollbar-width: none;
            /* Firefox */
        }

        .tabs::-webkit-scrollbar {
            display: none;
        }

        /* Chrome */

        .tab-btn {
            padding: 10px 20px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid var(--line-color);
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            white-space: nowrap;
            transition: 0.2s;
        }

        .tab-btn.active {
            background: var(--text-dark);
            color: white;
            border-color: var(--text-dark);
        }

        /* ACHIEVEMENT CARDS */
        .achievements-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .achievement-card {
            display: flex;
            align-items: center;
            padding: 18px;
            border-radius: 16px;
            background: white;
            border: 1px solid var(--line-color);
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .achievement-card.locked {
            opacity: 0.6;
            background: #f8fafc;
            filter: grayscale(100%);
        }

        .achievement-card.locked::after {
            content: '\f023';
            /* Lock icon */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--line-color);
            font-size: 1.5rem;
        }

        .icon-box {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-right: 15px;
            flex-shrink: 0;
            background: #f8fafc;
            border: 1px solid var(--line-color);
        }

        /* Icon Colors */
        .icon-gold {
            color: var(--gold);
            background: #fef3c7;
            border-color: #fde68a;
        }

        .icon-silver {
            color: var(--silver);
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .icon-bronze {
            color: var(--bronze);
            background: #ffedd5;
            border-color: #fed7aa;
        }

        .icon-emerald {
            color: var(--primary);
            background: #d1fae5;
            border-color: #a7f3d0;
        }

        .icon-fire {
            color: var(--fire);
            background: #fee2e2;
            border-color: #fecaca;
        }

        .ach-info {
            flex-grow: 1;
        }

        .ach-title {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--text-dark);
        }

        .ach-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .ach-date {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary);
            margin-top: 8px;
            display: inline-block;
            background: #d1fae5;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .locked .ach-date {
            background: var(--line-color);
            color: var(--text-muted);
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i></a>
            <h1 class="page-title">Pencapaian</h1>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="filterTab('semua')">Semua</button>
            <button class="tab-btn" onclick="filterTab('quran')">Al-Qur'an</button>
            <button class="tab-btn" onclick="filterTab('game')">Kompetisi</button>
            <button class="tab-btn" onclick="filterTab('mutabaah')">Mutabaah</button>
        </div>

        <!-- Grid Pencapaian -->
        <div class="achievements-grid" id="achievementList">

            <!-- KATEGORI AL-QUR'AN -->
            <div class="achievement-card item-quran">
                <div class="icon-box icon-emerald">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="ach-info">
                    <div class="ach-title">Penakluk Juz 30</div>
                    <div class="ach-desc">Berhasil menyetorkan hafalan penuh 1 Juz (Juz 30).</div>
                    <div class="ach-date"><i class="fas fa-check-circle"></i> Diraih: 12 Ags 2026</div>
                </div>
            </div>

            <div class="achievement-card item-quran">
                <div class="icon-box icon-gold">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div class="ach-info">
                    <div class="ach-title">Penjaga Kuat Juz 30</div>
                    <div class="ach-desc">Menyelesaikan Murojaah Juz 30 tanpa kesalahan fatal.</div>
                    <div class="ach-date"><i class="fas fa-check-circle"></i> Diraih: 14 Ags 2026</div>
                </div>
            </div>

            <div class="achievement-card item-quran locked">
                <div class="icon-box icon-emerald">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="ach-info">
                    <div class="ach-title">Penakluk Juz 29</div>
                    <div class="ach-desc">Hafal penuh 1 Juz (Juz 29).</div>
                    <div class="ach-date"><i class="fas fa-lock"></i> Belum diraih</div>
                </div>
            </div>

            <!-- KATEGORI GAME / KOMPETISI -->
            <div class="achievement-card item-game">
                <div class="icon-box icon-gold">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="ach-info">
                    <div class="ach-title">Raja Sambung Ayat</div>
                    <div class="ach-desc">Meraih Juara 1 Game Sambung Ayat di bulan Juli 2026.</div>
                    <div class="ach-date"><i class="fas fa-check-circle"></i> Diraih: 31 Jul 2026</div>
                </div>
            </div>

            <div class="achievement-card item-game">
                <div class="icon-box icon-silver">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="ach-info">
                    <div class="ach-title">Runner-Up Tebak Surah</div>
                    <div class="ach-desc">Meraih Juara 2 Game Tebak Surah di bulan Juni 2026.</div>
                    <div class="ach-date"><i class="fas fa-check-circle"></i> Diraih: 30 Jun 2026</div>
                </div>
            </div>

            <!-- KATEGORI MUTABAAH -->
            <div class="achievement-card item-mutabaah">
                <div class="icon-box icon-fire">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="ach-info">
                    <div class="ach-title">Pejuang Harian (10x)</div>
                    <div class="ach-desc">Menyelesaikan mutabaah 10 kali dalam sehari.</div>
                    <div class="ach-date"><i class="fas fa-check-circle"></i> Diraih: 05 Ags 2026</div>
                </div>
            </div>

            <div class="achievement-card item-mutabaah">
                <div class="icon-box icon-fire">
                    <i class="fas fa-fire-flame-curved"></i>
                </div>
                <div class="ach-info">
                    <div class="ach-title">Konsistensi Mingguan (50x)</div>
                    <div class="ach-desc">Menyelesaikan mutabaah 50 kali dalam satu minggu.</div>
                    <div class="ach-date"><i class="fas fa-check-circle"></i> Diraih: 10 Ags 2026</div>
                </div>
            </div>

            <div class="achievement-card item-mutabaah locked">
                <div class="icon-box icon-fire">
                    <i class="fas fa-fire-extinguisher"></i>
                </div>
                <div class="ach-info">
                    <div class="ach-title">Legenda Mutabaah (100x)</div>
                    <div class="ach-desc">Menyelesaikan mutabaah 100 kali dalam satu bulan.</div>
                    <div class="ach-date"><i class="fas fa-lock"></i> Progres: 85 / 100</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Fungsi simpel untuk Filter Tab Kategori
        function filterTab(category) {
            // Update styling tombol tab
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Filter cards
            const cards = document.querySelectorAll('.achievement-card');
            cards.forEach(card => {
                if (category === 'semua') {
                    card.style.display = 'flex';
                } else {
                    if (card.classList.contains('item-' + category)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        }
    </script>

</body>

</html>