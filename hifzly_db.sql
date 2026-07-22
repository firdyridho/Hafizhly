-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql303.infinityfree.com
-- Waktu pembuatan: 21 Jul 2026 pada 23.10
-- Versi server: 11.4.12-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_42360001_hafizhly_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `achievements`
--

CREATE TABLE `achievements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_penghargaan` varchar(100) NOT NULL,
  `tanggal_diraih` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `achievements`
--

INSERT INTO `achievements` (`id`, `user_id`, `nama_penghargaan`, `tanggal_diraih`) VALUES
(1, 1, 'Master Tebak Surah', '2026-07-12 13:24:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookmark`
--

CREATE TABLE `bookmark` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `surah_nomor` int(11) NOT NULL,
  `ayat` int(11) NOT NULL,
  `catatan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookmark`
--

INSERT INTO `bookmark` (`id`, `user_id`, `surah_nomor`, `ayat`, `catatan`) VALUES
(1, 1, 1, 2, 'Disimpan otomatis'),
(2, 1, 4, 3, 'Disimpan otomatis'),
(3, 1, 4, 4, 'Disimpan otomatis'),
(4, 1, 4, 5, 'Disimpan otomatis'),
(5, 1, 23, 1, 'Disimpan otomatis'),
(6, 1, 2, 9, 'Disimpan otomatis'),
(7, 1, 7, 2, 'Disimpan otomatis'),
(8, 1, 78, 1, 'Disimpan otomatis'),
(9, 1, 3, 3, 'Disimpan otomatis');

-- --------------------------------------------------------

--
-- Struktur dari tabel `game_history`
--

CREATE TABLE `game_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_type` varchar(50) NOT NULL,
  `juz_start` int(11) DEFAULT NULL,
  `juz_end` int(11) DEFAULT NULL,
  `total_q` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `game_history`
--

INSERT INTO `game_history` (`id`, `user_id`, `game_type`, `juz_start`, `juz_end`, `total_q`, `score`, `created_at`) VALUES
(1, 1, 'tebak_surah', 1, 2, 10, 10, '2026-07-12 06:24:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `murojaah_progress`
--

CREATE TABLE `murojaah_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `surah_nomor` int(11) NOT NULL,
  `last_ayat` int(11) NOT NULL,
  `last_page` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `murojaah_progress`
--

INSERT INTO `murojaah_progress` (`id`, `user_id`, `surah_nomor`, `last_ayat`, `last_page`, `updated_at`) VALUES
(2, 1, 2, 1, 2, '2026-07-10 07:43:59'),
(4, 1, 0, 1, 1, '2026-07-10 09:47:20'),
(5, 2, 2, 186, 28, '2026-07-10 15:26:58'),
(6, 1, 78, 5, 582, '2026-07-10 15:58:26'),
(7, 2, 1, 1, 1, '2026-07-11 05:48:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `murojaah_srs`
--

CREATE TABLE `murojaah_srs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `surah_nomor` int(11) NOT NULL,
  `status` enum('learning','reviewing') DEFAULT 'learning',
  `interval_hari` int(11) DEFAULT 1,
  `next_review` date NOT NULL,
  `last_reviewed` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `murojaah_srs`
--

INSERT INTO `murojaah_srs` (`id`, `user_id`, `surah_nomor`, `status`, `interval_hari`, `next_review`, `last_reviewed`) VALUES
(0, 1, 1, 'learning', 7, '2026-07-20', '2026-07-13 20:50:29'),
(1, 1, 114, 'learning', 7, '2026-07-17', '2026-07-10 07:43:07');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mutabaah`
--

CREATE TABLE `mutabaah` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_type` enum('tilawah','murojaah','hafalan_baru','setoran') NOT NULL,
  `activity_date` date NOT NULL,
  `activity_time` time NOT NULL,
  `surah` varchar(100) NOT NULL,
  `ayah_start` int(11) NOT NULL,
  `ayah_end` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `mutabaah`
--

INSERT INTO `mutabaah` (`id`, `user_id`, `activity_type`, `activity_date`, `activity_time`, `surah`, `ayah_start`, `ayah_end`, `notes`, `created_at`) VALUES
(1, 2, 'hafalan_baru', '2026-07-10', '09:02:00', 'Al-Fatihah', 1, 7, 'Bedain makhraj Ù‡ dan Ø­', '2026-07-09 13:05:30'),
(2, 2, 'tilawah', '2026-07-10', '09:43:00', 'Al-Baqarah', 1, 286, 'Perhatikan tajwidnya', '2026-07-09 13:46:25'),
(3, 1, 'hafalan_baru', '2026-07-09', '11:53:00', 'Yasin', 1, 83, 'itu ayat 3 harus sering di baca soalnya sering lupa', '2026-07-09 15:59:44'),
(4, 1, 'murojaah', '2026-07-09', '16:59:00', 'Yasin', 50, 83, 'oke lah', '2026-07-09 16:00:27'),
(5, 1, 'setoran', '2026-07-10', '07:30:00', 'Al-Fatihah', 1, 7, 'lancar banget', '2026-07-10 02:27:47'),
(6, 1, 'tilawah', '2026-07-10', '07:20:00', 'Al-Baqarah', 1, 50, 'Harus belajar huruf lagi', '2026-07-10 03:03:58'),
(7, 2, 'murojaah', '2026-07-10', '03:50:00', 'An-Nahl', 1, 100, '', '2026-07-10 07:52:12'),
(8, 2, 'murojaah', '2026-07-11', '06:52:00', 'Yusuf', 1, 100, 'masyaallah lancarr', '2026-07-10 07:53:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `surah_nomor` int(11) NOT NULL,
  `ayat` int(11) NOT NULL,
  `skor` int(11) DEFAULT 0,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tajwid_kuis`
--

CREATE TABLE `tajwid_kuis` (
  `id` int(11) NOT NULL,
  `materi_id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `opsi_a` varchar(255) NOT NULL,
  `opsi_b` varchar(255) NOT NULL,
  `opsi_c` varchar(255) NOT NULL,
  `opsi_d` varchar(255) NOT NULL,
  `jawaban_benar` enum('a','b','c','d') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tajwid_kuis`
--

INSERT INTO `tajwid_kuis` (`id`, `materi_id`, `pertanyaan`, `gambar`, `opsi_a`, `opsi_b`, `opsi_c`, `opsi_d`, `jawaban_benar`) VALUES
(7, 2, 'Hukum nun mati atau tanwin dibagi menjadi berapa macam?', '', '3', '4', '5', '6', 'c'),
(8, 2, 'Apabila ada nun mati atau tanwin bertemu dengan huruf Ba (Ø¨), maka hukum bacaannya disebut...', '', 'Ikhfa\'', 'Iqlab', 'Idzhar', 'Idgham Bighunnah', 'b'),
(9, 2, 'Secara bahasa, arti dari Izhar adalah..', '', 'Samar', 'Jelas', 'Memasukkan', 'Menukar', 'b'),
(10, 2, 'Huruf-huruf berikut yang merupakan huruf dari Izhar Halqi adalah..', '', ' ÙŠØŒ Ù†ØŒ Ù…ØŒ Ùˆ', ' Ø£ØŒ Ù‡Ù€ØŒ Ø­ØŒ Ø®ØŒ Ø¹ØŒ Øº', ' Ø¨', ' Ø·ØŒ Ø¯ØŒ Øª', 'b'),
(11, 2, 'Jika nun mati bertemu dengan huruf Lam (Ù„), maka hukum bacaannya adalah...', '', 'Idgham Bighunnah', 'Idgham Bilaghunnah', 'Iqlab', 'Izhar Halqi', 'b'),
(12, 2, 'Hukum nun mati atau tanwin dibagi menjadi berapa macam?', '', '3', '4', '5', '6', 'c'),
(13, 2, 'Apabila ada nun mati atau tanwin bertemu dengan huruf Ba (Ø¨), maka hukum bacaannya disebut...', '', 'Ikhfa\'', 'Iqlab', 'Idzhar', 'Idgham Bighunnah', 'b'),
(14, 2, 'Secara bahasa, arti dari Izhar adalah..', '', 'Samar', 'Jelas', 'Memasukkan', 'Menukar', 'b'),
(15, 2, 'Huruf-huruf berikut yang merupakan huruf dari Izhar Halqi adalah..', '', ' ÙŠØŒ Ù†ØŒ Ù…ØŒ Ùˆ', ' Ø£ØŒ Ù‡Ù€ØŒ Ø­ØŒ Ø®ØŒ Ø¹ØŒ Øº', ' Ø¨', ' Ø·ØŒ Ø¯ØŒ Øª', 'b'),
(16, 2, 'Jika nun mati bertemu dengan huruf Lam (Ù„), maka hukum bacaannya adalah...', '', 'Idgham Bighunnah', 'Idgham Bilaghunnah', 'Iqlab', 'Izhar Halqi', 'b');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tajwid_materi`
--

CREATE TABLE `tajwid_materi` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `konten` longtext DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `waktu_kuis` int(11) DEFAULT 5,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tajwid_materi`
--

INSERT INTO `tajwid_materi` (`id`, `judul`, `cover_image`, `pdf_file`, `konten`, `youtube_url`, `waktu_kuis`, `created_at`) VALUES
(2, 'Hukum Nun Mati dan Tanwin Beserta Contohnya', '1783866790_00de477f_cov.webp', '1783836445_3eebc3e1_doc.pdf', '<h2><strong>Pengertian Hukum Nun Mati dan Tanwin</strong></h2><p>Hukum nun mati dan tanwin adalah <strong>hukum bacaan dalam Al-Qurâ€™an</strong> <strong>apabila terdapat huruf nun mati maupun tanwin yang bertemu dengan huruf-huruf hijaiyah tertentu</strong>.&nbsp;</p><p><strong>Nun mati</strong> disebut juga <strong>sukun</strong> (Ù†Ù’) adalah <strong>huruf nun yang tidak berharakat fathah, kasrah, ataupun dhammah</strong>. Nun mati biasanya diberi <strong>tanda bulatan kecil di atasnya</strong>. Huruf ini dibaca menjadi akhiran -n.</p><p>Sedangkan <strong>tanwin</strong> ( Ù€ÙŽÙ€Ù€Ù‹Ù€ , Ù€ÙÙ€Ù€ÙÙ€ , Ù€ÙÙ€Ù€ÙŒÙ€ ) adalah tanda harakat dua atau <strong>harakat ganda</strong> yang bunyinya mirip seperti nun mati, yakni dibaca akhiran -n.&nbsp;</p><p>Untuk mengetahui macam-macam beserta contoh hukum nun mati dan tanwin, kamu bisa membaca penjelasan berikut.</p><p>&nbsp;</p><h2><strong>Macam-Macam Hukum Nun Mati dan Tanwin Beserta Contohnya</strong></h2><p>Terdapat empat hukum bacaan nun mati atau sukun dan tanwin , yaitu <strong>izhar, idgham, iqlab, dan ikhfa</strong>. Masing-masing hukum bacaan ini memiliki ketentuan serta cara baca yang berbeda-beda. Seperti apa perbedaannya? Yuk, simak penjelasan berikut ini!</p><p>&nbsp;</p><h3><strong>1. Hukum Bacaan Izhar</strong></h3><p>Hukum nun mati dan tanwin yang pertama bernama izhar. Secara bahasa, <strong>izhar artinya jelas atau nampak</strong>. Sesuai artinya, izhar adalah hukum bacaan yang <strong>dibaca jelas apabila nun mati atau tanwin bertemu dengan salah satu dari enam huruf halqi (tenggorokan)</strong>.</p><p>Jadi, cara membaca hukum izhar yaitu nun mati atau tanwin harus dilafalkan dengan jelas, tidak boleh terpengaruh oleh huruf halqi yang terletak setelahnya.</p><p><strong>Huruf izhar</strong> atau huruf halqi ada enam, yaitu:</p><ul><li>ha (Ø­)</li><li>kha (Ø®)</li><li>ain (Ø¹)</li><li>ghain (Øº)</li><li>ha (Ù‡)</li><li>hamzah (Ø¡)</li></ul><p>&nbsp;</p><p><strong>Contoh izhar</strong> salah satunya terdapat pada potongan QS. Yasin: 9 sebagai berikut:</p><p>ÙˆÙŽÙ…ÙÙ†Ù’ Ø®ÙŽÙ„Ù’ÙÙÙ‡ÙÙ…Ù’ Ø³ÙŽØ¯Ù‘Ù‹</p><p>&nbsp;</p><p>Cara membacanya yaitu: wa min khalfihim saddan.&nbsp;</p><p>Karena terdapat nun mati (Ù†Ù’) bertemu dengan huruf kha (Ø®), maka nun mati tersebut harus dibaca jelas.</p><p>&nbsp;</p><h3><strong>2. Hukum Bacaan Idgham</strong></h3><p>Hukum nun mati dan tanwin yang kedua yaitu idgham. Secara bahasa, <strong>idgham artinya memasukkan atau melebur</strong>. Sementara secara istilah, idgham artinya memasukkan huruf mati pada huruf yang berharakat, sehingga keduanya akan menjadi huruf bertasydid yang diucapkan dengan satu kali ucapan.&nbsp;</p><p>&nbsp;</p><p>Jika disimpulkan, maka arti dari idgham adalah memasukkan bacaan nun mati atau tanwin ke huruf sesudahnya, yang merupakan huruf-huruf idgham.&nbsp;</p><p>Idgham dibagi menjadi dua jenis hukum bacaan, yaitu idgham bighunnah dan idgham bilaghunnah. Berikut penjelasan mengenai idgham bighunnah, serta idgham bilaghunnah dan contohnya.</p><p>&nbsp;</p><h4><strong>a. Idgham Bighunnah</strong></h4><p><strong>Idgham bighunnah</strong> artinya hukum bacaan yang dibaca mendengung. Idgham bighunnah dibaca dengan dengung selama 2 harakat atau 1 alif. Idgham bighunnah hurufnya ada empat, yaitu:</p><ul><li>wau (Ùˆ)</li><li>mim (Ù…)</li><li>nun (Ù†)</li><li>ya (ÙŠ)</li></ul><p>&nbsp;</p><p>Hukum ini hanya berlaku ketika <strong>huruf-huruf tersebut bertemu tidak dalam satu kata</strong>. Jika nun sukun atau tanwin bertemu dengan huruf idgham bigunnah dalam satu kata, maka harus dibaca jelas, bukan berdengung.</p><p><strong>Contoh idgham bighunnah</strong> antara lain terdapat pada potongan QS. Al-Hasyr: 6 sebagai berikut:</p><p>ÙˆÙŽÙ„ÙŽÙ°ÙƒÙÙ†ÙŽÙ‘ Ù±Ù„Ù„ÙŽÙ‘Ù‡ÙŽ ÙŠÙØ³ÙŽÙ„ÙÙ‘Ø·Ù Ø±ÙØ³ÙÙ„ÙŽÙ‡ÙÛ¥ Ø¹ÙŽÙ„ÙŽÙ‰Ù° Ù…ÙŽÙ† ÙŠÙŽØ´ÙŽØ¢Ø¡Ù</p><p>&nbsp;</p><p>Cara membacanya yaitu: wa lÄkinnallÄha yusalliá¹­u rusulahá»¥ â€˜alÄ may yasyÄ`.&nbsp;</p><p>Karena nun mati (Ù†Ù’) bertemu huruf (ya) ÙŠ tidak dalam satu kata, maka harus dibaca dengung selama selama 2 harakat atau 1 alif (kurang lebih 3 ketukan).</p><p>&nbsp;</p><h4><strong>b. Idgham Bilaghunnah</strong></h4><p>Idgham bilaghunnah adalah hukum bacaan yang membunyikan nun mati atau tanwin dengan memasukkannya ke dalam huruf setelahnya tanpa dengungan. Idgham bilaghunnah hurufnya ada dua, yaitu:</p><ul><li>la (Ù„)</li><li>ra (Ø±)</li></ul><p>&nbsp;</p><p><strong>Contoh idgham bilaghunnah</strong> terdapat pada potongan QS. Al-Kahfi: 2 sebagai berikut:</p><p>Ù‚ÙŽÙŠÙ‘ÙÙ…Ù‹Ø§ Ù„Ù‘ÙÙŠÙÙ†Ø°ÙØ±ÙŽ Ø¨ÙŽØ£Ù’Ø³Ù‹Ø§</p><p>&nbsp;</p><p>Cara membacanya yaitu: qayyimal liyundÅ¼ira baâ€™san.&nbsp;</p><p>Karena fathatain (Ù€Ù‹Ù€) bertemu dengan huruf la (Ù„), maka tanwin dileburkan ke dalam huruf setelahnya.</p><p>&nbsp;</p><h3><strong>3. Hukum Bacaan Iqlab</strong></h3><p>Secara bahasa, <strong>Iqlab artinya mengubah huruf asli</strong>. Sedangkan secara istilah, iqlab berarti menukar atau mengganti suatu huruf menjadi huruf lain. Oleh karena itu, hukum bacaan iqlab adalah menukar atau <strong>mengganti nun mati atau tanwin menjadi huruf mim mati (Ù…) dengan disertai dengungan</strong>.</p><p>Iqlab terjadi ketika <strong>nun mati atau tanwin bertemu dengan huruf iqlab, yaitu ba (Ø¨)</strong>. Iqlab dibaca dengan merapatkan bibir atas dengan bawah, serta diiringi dengan suara dengung selama kurang lebih 2 ketukan.</p><p><strong>Contoh hukum bacaan iqlab</strong> terdapat pada potongan QS. Al-Maidah: 39 sebagai berikut:</p><p>ÙÙŽÙ…ÙŽÙ† ØªÙŽØ§Ø¨ÙŽ Ù…ÙÙ†Ù’ Ø¨ÙŽØ¹Ù’Ø¯Ù Ø¸ÙÙ„Ù’Ù…ÙÙ‡Ù</p><p>&nbsp;</p><p>Cara membacanya yaitu: fa man tÄba mim baâ€˜di áº“ulmihÄ«.&nbsp;</p><p>Karena nun mati (Ù†Ù’) bertemu dengan huruf ba (Ø¨), maka nun sukun diganti menjadi mim sukun dan dibaca dengung.</p><p>&nbsp;</p><h3><strong>4. Hukum Bacaan Ikhfa</strong></h3><p>Secara bahasa, ikhfa artinya adalah menutup atau tersembunyi. Oleh karena itu, ikhfa adalah hukum bacaan yang <strong>dibaca dengan menyamarkan nun mati atau tanwin menjadi samar-samar</strong>, antara jelas dan dengung sepanjang 2 harakat.&nbsp;</p><p>Hukum ini terjadi apabila nun mati atau tanwin bertemu dengan salah satu dari huruf ikhfa. Huruf ikhfa ada berapa? Jawabannya ada 15! Kelima belas huruf ikhfa tersebut yaitu sebagai berikut:</p><ul><li>kaf ( Ùƒ )</li><li>qaf ( Ù‚ )</li><li>faâ€™ ( Ù )</li><li>zha ( Ø¸ )</li><li>tha ( Ø· )</li><li>dhad ( Ø¶ )</li><li>shad ( Øµ )</li><li>syin ( Ø´ )</li><li>sin ( Ø³ )</li><li>zaâ€™ ( Ø² )</li><li>dzal ( Ø° )</li><li>dal ( Ø¯ )</li><li>jim ( Ø¬ )</li><li>tsaâ€™ ( Ø« )</li><li>taâ€™ ( Øª )</li></ul><p>&nbsp;</p><p><strong>Contoh hukum bacaan ikhfa</strong> terdapat pada potongan QS. An-Nisa: 2 sebagai berikut:</p><p>Ø¥ÙÙ†ÙŽÙ‘Ù‡ÙÛ¥ ÙƒÙŽØ§Ù†ÙŽ Ø­ÙÙˆØ¨Ù‹Ø§ ÙƒÙŽØ¨ÙÙŠØ±Ù‹Ø§</p><p>Cara membacanya yaitu: innahá»¥ kÄna á¸¥á»¥bang kabÄ«rÄ.&nbsp;</p><p>Karena fathatain (Ù€Ù‹Ù€) bertemu dengan huruf kaf ( Ùƒ ), maka tanwin harus dibaca samar-samar.</p>', 'https://www.youtube.com/watch?v=7NFQBn5s8mU', 10, '2026-07-11 23:07:25'),
(3, 'Hukum Min Sukun Beserta Contohnya', '1784090733_3e611442_cov.webp', '', '<p>&nbsp;</p><p>&nbsp;</p><p style=\"text-align:justify;\">Hukum mim sukun dibagi menjadi 3: &nbsp;Ikhfa Syafawi, Idhgham &nbsp;Mitslain (Mimi), dan Idzhar Syafawi</p><h3 style=\"text-align:justify;\">&nbsp;</h3><p style=\"text-align:justify;\"><strong><u>1.Ikhfa\' Syafawi</u></strong></p><p style=\"text-align:justify;\">Apabila Mim Sukun bertemu dengan huruf Ba (Ø¨ ), maka cara membacanya mendengungkan huruf Mim dengan tempo selama 2 harakat atau 1 alif.</p><p style=\"text-align:justify;\">Contoh Ikhfa\' Syafawi :</p><p style=\"text-align:justify;\"><span style=\"font-size:24px;\">ØªÙŽØ±Ù’Ù…ÙÙŠÙ’Ù‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:24px;\">Ù…Ù’ Ø¨Ù</span><span style=\"font-size:24px;\">Ø­ÙØ¬ÙŽØ§Ø±ÙŽØ©Ù - ÙŠÙŽØ¹Ù’Ù„ÙŽ</span><span style=\"color:hsl(0,100%,50%);font-size:24px;\">Ù…Ù’ Ø¨Ù</span><span style=\"font-size:24px;\">Ø§ÙŽÙ†Ù‘ÙŽ</span></p><p style=\"text-align:justify;\">&nbsp;</p><h4 style=\"text-align:justify;\"><strong><u>2.Idgham Mitslain</u></strong></h4><p style=\"text-align:justify;\">Apabila Mim Sukun bertemu dengan huruf Mim (Ù…), maka cara membacanya Mim pertama dimasukkan ke dalam Mim kedua dengan tempo dengung selama &nbsp;2 harakat atau 1 alif.&nbsp;</p><p style=\"text-align:justify;\">Contoh Idhgam Mimi:</p><p style=\"text-align:justify;\"><span style=\"font-size:24px;\">ÙÙÙŠÙ’ Ù‚ÙÙ„ÙÙˆÙ’Ø¨ÙÙ‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:24px;\">Ù…Ù’ Ù…ÙŽ</span><span style=\"font-size:24px;\">Ø±ÙŽØ¶ÙŒ - Ø§ÙŽØ¬Ù’Ø±ÙŽÙ‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:24px;\">Ù…Ù’ Ù…ÙŽ</span><span style=\"font-size:24px;\">Ø±Ù‘ÙŽØªÙŽÙŠÙ’Ù†Ù</span></p><p style=\"text-align:justify;\">&nbsp;</p><h4 style=\"text-align:justify;\"><strong><u>3.Idzhar Syafawi</u></strong></h4><p style=\"text-align:justify;\">Apabila Mim Sukum bertemu dengan huruf Hijaiyah selain Ba (Ø¨ ) dan Mim (Ù…), maka cara membacanya jelas</p><p style=\"text-align:justify;\">Contoh Idzhar Syafawi:</p><figure class=\"table\"><table><thead><tr><th style=\"text-align:center;\">Huruf</th><th align=\"left\">Mim Sukun</th><th style=\"text-align:center;\">Huruf</th><th align=\"left\">Mim Sukun</th></tr></thead><tbody><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø£</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø£ÙŽÙŠÙ‘ÙÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø£ÙŽ</span><span style=\"font-size:22px;\">Ø­Ù’Ø³ÙŽÙ†Ù</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø¶</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ù‚ÙŽØ±ÙŽØ£ÙŽ Ø¹ÙŽÙ„ÙŽÙŠÙ’Ù‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø¶ÙŽ</span><span style=\"font-size:22px;\">Ø±Ù’Ø¨Ù‹Ø§</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Øª</span></td><td align=\"left\"><span style=\"font-size:22px;\">ÙƒÙÙ†Ù’ØªÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ ØªÙŽ</span><span style=\"font-size:22px;\">Ø¯Ù’Ø±ÙØ³ÙÙˆÙ†ÙŽ</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø·</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ù„ÙŽÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø·ÙŽ</span><span style=\"font-size:22px;\">Ø§Ù„ÙÙˆØªÙ</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø«</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø£ÙŽ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’Ø«ÙŽ</span><span style=\"font-size:22px;\">Ø§Ù„ÙÙƒÙÙ…Ù’</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø¸</span></td><td align=\"left\"><span style=\"font-size:22px;\">ÙˆÙŽÙ‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø¸ÙŽ</span><span style=\"font-size:22px;\">Ø§Ù„ÙÙ…ÙÙˆÙ†ÙŽ</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø¬</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø£ÙŽÙ†Ù’ Ù„ÙŽÙ‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø¬ÙŽ</span><span style=\"font-size:22px;\">Ù†Ù‘ÙŽØ§ØªÙ</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø¹</span></td><td align=\"left\"><span style=\"font-size:22px;\">ÙˆÙŽÙŠÙŽÙ†Ù’ØµÙØ±Ù’ÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø¹ÙŽ</span><span style=\"font-size:22px;\">Ù„ÙŽÙŠÙ’Ù‡ÙÙ…Ù’</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø­</span></td><td align=\"left\"><span style=\"font-size:22px;\">ÙÙÙŠ Ø£ÙŽÙ…Ù’ÙˆÙŽØ§Ù„ÙÙ‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø­ÙŽ</span><span style=\"font-size:22px;\">Ù‚Ù‘ÙŒ</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Øº</span></td><td align=\"left\"><span style=\"font-size:22px;\">ÙÙŽØ¥ÙÙ†Ù‘ÙŽÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ ØºÙŽ</span><span style=\"font-size:22px;\">Ø§Ù„ÙØ¨ÙÙˆÙ†ÙŽ</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø®</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ù‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø®ÙŽ</span><span style=\"font-size:22px;\">ÙŠÙ’Ø±Ù Ø§Ù„Ù’Ø¨ÙŽØ±ÙÙŠÙ‘ÙŽØ©Ù</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ù</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ù„ÙŽÙ‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ ÙÙ</span><span style=\"font-size:22px;\">ÙŠÙ‡ÙŽØ§</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø¯</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ù„ÙŽÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø¯Ù</span><span style=\"font-size:22px;\">ÙŠÙ†ÙÙƒÙÙ…Ù’</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ù‚</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø¨ÙØ£ÙŽÙ†Ù‘ÙŽÙ‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ù‚ÙŽ</span><span style=\"font-size:22px;\">ÙˆÙ’Ù…ÙŒ</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø°</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø±ÙŽØ¨Ù‘ÙÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø°Ù</span><span style=\"font-size:22px;\">Ùˆ Ø±ÙŽØ­Ù’Ù…ÙŽØ©Ù</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ùƒ</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ù…ÙŽØ§ Ù„ÙŽÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ ÙƒÙŽ</span><span style=\"font-size:22px;\">ÙŠÙ’ÙÙŽ</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø±</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø¬ÙŽØ§Ø¡ÙŽÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø±ÙŽ</span><span style=\"font-size:22px;\">Ø³ÙÙˆÙ„ÙŒ</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ù„</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø£ÙŽ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ù„ÙŽ</span><span style=\"font-size:22px;\">ÙƒÙÙ…Ù’</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø²</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ù…ÙÙ†Ù’Ù‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø²ÙŽ</span><span style=\"font-size:22px;\">Ù‡Ù’Ø±ÙŽØ©ÙŒ</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ù†</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø¥ÙÙ„ÙŽÙŠÙ’ÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ù†Ù</span><span style=\"font-size:22px;\">ÙˆØ±Ù‹Ø§</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø³</span></td><td align=\"left\"><span style=\"font-size:22px;\">ÙÙŽÙˆÙ’Ù‚ÙŽÙƒÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø³ÙŽ</span><span style=\"font-size:22px;\">Ø¨Ù’Ø¹Ù‹Ø§</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ù‡</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø£ÙŽØ®ÙŽØ§Ù‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ù‡Ù</span><span style=\"font-size:22px;\">ÙˆØ¯Ù‹Ø§</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ø´</span></td><td align=\"left\"><span style=\"font-size:22px;\">ÙƒÙÙ†Ù’ØªÙ</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ Ø´Ù</span><span style=\"font-size:22px;\">Ù‡ÙŽØ¯ÙŽØ§Ø¡ÙŽ</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Ùˆ</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø¹ÙŽÙ„ÙŽÙ‰Ù° Ù‚ÙÙ„ÙÙˆØ¨ÙÙ‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ ÙˆÙŽ</span><span style=\"font-size:22px;\">Ø¹ÙŽÙ„ÙŽÙ‰Ù°</span></td></tr><tr><td style=\"text-align:center;\"><span style=\"font-size:22px;\">Øµ</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ø¹ÙŽÙ„ÙŽÙŠÙ’Ù‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ ØµÙŽ</span><span style=\"font-size:22px;\">Ù„ÙŽÙˆÙŽØ§ØªÙŒ</span></td><td style=\"text-align:center;\"><span style=\"font-size:22px;\">ÙŠ</span></td><td align=\"left\"><span style=\"font-size:22px;\">Ù…ÙÙ…Ù‘ÙŽØ§ Ø±ÙŽØ²ÙŽÙ‚Ù’Ù†ÙŽØ§Ù‡Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…Ù’ ÙŠÙ</span><span style=\"font-size:22px;\">Ù†Ù’ÙÙÙ‚ÙÙˆÙ†ÙŽ</span></td></tr></tbody></table></figure><p>&nbsp;</p>', 'https://www.youtube.com/watch?v=JTiiio3jeYA', 5, '2026-07-12 20:51:36');
INSERT INTO `tajwid_materi` (`id`, `judul`, `cover_image`, `pdf_file`, `konten`, `youtube_url`, `waktu_kuis`, `created_at`) VALUES
(4, 'Hukum Nun dan Mim yang Bertasydid', '1784090837_cfd157a5_cov.webp', '', '<p>Apabila Nun yang bertasydid<strong> ( </strong><span style=\"font-family:Georgia, serif;font-size:22px;\"><strong>Ù†Ù‘</strong></span><span style=\"font-family:Georgia, serif;\"><strong> </strong></span><strong>) </strong>dan Mim yang bertasydid <strong>(</strong><span style=\"font-size:22px;\"><strong> </strong></span><span style=\"font-family:Georgia, serif;font-size:22px;\"><strong>Ù…Ù‘</strong></span><span style=\"font-size:20px;\"><strong> </strong></span><strong>)</strong>, maka dibaca dengung 2 harakat atau 1 alif dan disebut bacaan <i>Ghunnah</i> &nbsp;( <span style=\"font-size:22px;\"><strong>ØºÙÙ†ÙŽÙ‘Ø© </strong></span>).</p><p>Contoh:&nbsp;<br>&nbsp;</p><figure class=\"table\"><table><tbody><tr><td>Contoh <span style=\"font-family:Georgia, serif;font-size:22px;\"><strong>Ù†Ù‘</strong></span><span style=\"font-family:Georgia, serif;\"><strong>&nbsp;</strong></span>&nbsp;</td><td><span style=\"font-size:22px;\">Ù…ÙÙ†ÙŽ Ø§Ù„Ù’Ø¬Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù†ÙŽÙ‘</span><span style=\"font-size:22px;\">Ø©Ù ÙˆÙŽØ§Ù„</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù†ÙŽÙ‘</span><span style=\"font-size:22px;\">Ø§Ø³Ù</span></td><td><span style=\"color:rgb(51,51,51);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">ÙˆÙŽØ¥Ù</span></span><span style=\"color:hsl(0,100%,50%);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">Ù†ÙŽÙ‘</span></span><span style=\"color:rgb(51,51,51);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\"> Ø§Ù„ÙŽÙ‘Ø°ÙÙŠÙ†ÙŽ</span></span></td><td><span style=\"color:rgb(51,51,51);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">Ø¥Ù</span></span><span style=\"color:hsl(0,100%,50%);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">Ù†ÙŽÙ‘</span></span><span style=\"color:rgb(51,51,51);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">Ù‡Ù ÙƒÙŽØ§Ù†ÙŽ</span></span></td></tr><tr><td>Contoh <span style=\"font-family:Georgia, serif;font-size:22px;\"><strong>Ù…Ù‘</strong></span></td><td><span style=\"font-size:22px;\">ÙˆÙŽÙ…Ù</span><span style=\"color:hsl(0,100%,50%);font-size:22px;\">Ù…ÙŽÙ‘</span><span style=\"font-size:22px;\">Ø§ Ø±ÙŽØ²ÙŽÙ‚Ù’Ù†ÙŽÙ°Ù‡ÙÙ…Ù’</span></td><td><span style=\"color:rgb(51,51,51);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">ÙÙŽÙ„ÙØ£Ù</span></span><span style=\"color:hsl(0,100%,50%);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">Ù…Ù‘Ù</span></span><span style=\"color:rgb(51,51,51);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">Ù‡Ù Ù±Ù„Ø³Ù‘ÙØ¯ÙØ³Ù</span></span></td><td><span style=\"color:rgb(51,51,51);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">Ø«Ù</span></span><span style=\"color:hsl(0,100%,50%);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\">Ù…Ù‘ÙŽ</span></span><span style=\"color:rgb(51,51,51);font-family:CNNsans, sans-serif;font-size:22px;\"><span style=\"-webkit-text-stroke-width:0px;display:inline !important;float:none;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;\"> Ù„ÙŽØ§ ÙŠÙÙ†Ø¸ÙŽØ±ÙÙˆÙ†ÙŽ</span></span></td></tr></tbody></table></figure>', 'https://www.youtube.com/watch?v=7jIEs8TzD50', 5, '2026-07-13 00:35:23'),
(5, 'Hukum Bacaan Alif Lam', '1784090854_9b575ea2_cov.webp', '', '<p>&nbsp;</p><p>Hukum Alif Lam (Ø§Ù„Ù€) dibagi menjadi 2, yaitu <strong>Alif Lam al-Qamariyyah (Ø§Ù„Ù’Ù‚ÙŽÙ…ÙŽØ±ÙÙŠÙ‘ÙŽØ©Ù)</strong> dan <strong>Alif Lam asy-Syamsiyyah (Ø§Ù„Ø´Ù‘ÙŽÙ…Ù’Ø³ÙÙŠÙ‘ÙŽØ©Ù).</strong></p><h4><strong><u>1. Alif Lam al-Qamariyyah (Ø§Ù„Ù’Ù‚ÙŽÙ…ÙŽØ±ÙÙŠÙ‘ÙŽØ©Ù)</u></strong></h4><p>Apabila al-taâ€˜rÄ«f (Ø§Ù„Ù’) bertemu dengan salah satu huruf-huruf qamariyyah yang berjumlah 14, yaitu: Hamzah (Ø¡), BÄ\' (Ø¨), Ghain (Øº), á¸¤Ä\' (Ø­), JÄ«m (Ø¬), KÄf (Ùƒ), WÄw (Ùˆ), KhÄ\' (Ø®), FÄ\' (Ù), \'Ain (Ø¹), QÄf (Ù‚), YÄ\' (ÙŠ), MÄ«m (Ù…), dan HÄ\' (Ù‡Ù€).</p><p>Terkumpul dalam kalimat: <span style=\"font-size:20px;\"><strong>Ø§ÙŽØ¨Ù’ØºÙ Ø­ÙŽØ¬Ù‘ÙŽÙƒÙŽ ÙˆÙŽØ®ÙŽÙÙ’ Ø¹ÙŽÙ‚ÙÙŠÙ…ÙŽÙ‡Ù</strong></span></p><p>Cara membacanya harus terang dan jelas. Hukumnya disebut <strong>Iáº“hÄr Qamariyyah (Ø¥ÙØ¸Ù’Ù‡ÙŽØ§Ø±Ù Ø§Ù„Ù’Ù‚ÙŽÙ…ÙŽØ±ÙÙŠÙ‘ÙŽØ©Ù).</strong></p><h4><strong><u>2. Alif Lam asy-Syamsiyyah (Ø§Ù„Ø´Ù‘ÙŽÙ…Ù’Ø³ÙÙŠÙ‘ÙŽØ©Ù)</u></strong></h4><p>Apabila al-taâ€˜rÄ«f (Ø§Ù„Ù’) bertemu dengan salah satu huruf-huruf syamsiyyah yang berjumlah 14, yaitu:TÄ\' (Øª), TsÄ\' (Ø«), DÄl (Ø¯), DzÄl (Ø°), RÄ\' (Ø±), ZÄy (Ø²), SÄ«n (Ø³), SyÄ«n (Ø´), á¹¢Äd (Øµ), á¸ŒÄd (Ø¶), á¹¬Ä\' (Ø·), áº’Ä\' (Ø¸), LÄm (Ù„), dan NÅ«n (Ù†).</p><p>Terkumpul dalam kalimat:<span style=\"font-size:20px;\"> <strong>Ø·ÙØ¨Ù’ Ø«ÙÙ…Ù‘ÙŽ ØµÙÙ„Ù’ Ø±ÙŽØ­ÙÙ…Ù‹Ø§ ØªÙŽÙÙØ²Ù’ Ø¶ÙÙÙ’ Ø°ÙŽØ§ Ù†ÙØ¹ÙŽÙ…Ù Ø¯ÙŽØ¹Ù’ Ø³ÙÙˆØ¡ÙŽ Ø¸ÙŽÙ†Ù‘Ù Ø²ÙØ±Ù’ Ø´ÙŽØ±ÙÙŠÙÙ‹Ø§ Ù„ÙÙ„Ù’ÙƒÙŽØ±ÙŽÙ…Ù</strong></span></p><p>Cara membacanya adalah memasukkan (mengidgamkan) huruf Lam (Ù„) ke dalam huruf syamsiyyah yang mengikutinya, sehingga bunyi lam tidak terdengar. Hukumnya disebut IdghÄm Syamsiyyah (Ø¥ÙØ¯Ù’ØºÙŽØ§Ù…Ù Ø§Ù„Ø´Ù‘ÙŽÙ…Ù’Ø³ÙÙŠÙ‘ÙŽØ©Ù).</p>', 'https://www.youtube.com/watch?v=DWeserchaJs', 5, '2026-07-13 01:47:40'),
(6, 'Hukum Bacaan Idgham', '', '', '', '', 5, '2026-07-14 03:48:39'),
(7, 'Pembagian Mad', '', '', '', '', 5, '2026-07-14 03:49:25'),
(8, 'Hukum Mad', '', '', '', '', 5, '2026-07-14 03:49:46'),
(9, 'Hukum Membaca Ra', '', '', '', '', 5, '2026-07-14 03:52:25'),
(10, 'Hukum Qalqalah', '', '', '', '', 5, '2026-07-14 03:52:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `targets`
--

CREATE TABLE `targets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `target_nama` varchar(100) NOT NULL,
  `status` enum('belum','selesai') DEFAULT 'belum',
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Firdy Ridho Fillah', 'firdyridho9@gmail.com', '$2y$10$689Xy04mhiDI64ItxD2fluv.rWrezG1XgKzvlYV.VBO3AJMCgTfie', 'user', '2026-07-09 01:02:35'),
(2, 'pijaa', 'cherr@gmail.com', '$2y$10$2q9uS30HYUf22Gn.ZsOfoO0fzpqu94dYCvMjihi2OdYzbyrkcw7zu', 'user', '2026-07-09 12:57:50'),
(3, 'akrom ganteng', 'akromfadhil234@gmail.com', '$2y$10$wOP8aqADsGa8UGQNYQHe.Oc.RY2GsVIoU24BkWdvCE6WFSZTb6JeG', 'user', '2026-07-10 15:17:57'),
(4, 'joko', 'jokoxprabs@gmail.com', '$2y$10$X0L248QvbV1Icq4ZamBXlOsZddWeMY7nwxR10MjedaetF.UTTL0xO', 'user', '2026-07-12 11:29:53'),
(5, 'Admin Utama', 'admin@hifzly.com', '$2y$10$n8ifzi9EC6XjOv1cuRI0qOEZA3eLfx4fynZ/oVfMxa1FWEA2fenwG', 'admin', '2026-07-15 04:41:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_targets`
--

CREATE TABLE `user_targets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `tipe_target` varchar(20) NOT NULL,
  `jumlah_target` int(11) NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_targets`
--

INSERT INTO `user_targets` (`id`, `user_id`, `kategori`, `tipe_target`, `jumlah_target`, `updated_at`) VALUES
(1, 1, 'tilawah', 'harian', 10, '2026-07-11 07:40:50'),
(2, 2, 'tilawah', 'harian', 10, '2026-07-12 03:18:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_todos`
--

CREATE TABLE `user_todos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `task_time` time NOT NULL,
  `task_date` date NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `kategori` varchar(50) DEFAULT 'Tilawah',
  `catatan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user_todos`
--

INSERT INTO `user_todos` (`id`, `user_id`, `task_name`, `task_time`, `task_date`, `is_completed`, `created_at`, `kategori`, `catatan`) VALUES
(2, 2, '2 halaman', '01:54:00', '2026-07-11', 1, '2026-07-11 08:52:01', 'Tilawah', 'sipp'),
(3, 2, '6 juz', '06:56:00', '2026-07-11', 1, '2026-07-11 08:52:42', 'Tilawah', ''),
(4, 2, '2 halaman', '19:17:00', '2026-07-12', 1, '2026-07-12 03:14:20', 'Tilawah', 'sipp');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `game_history`
--
ALTER TABLE `game_history`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `murojaah_progress`
--
ALTER TABLE `murojaah_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_surah` (`user_id`,`surah_nomor`);

--
-- Indeks untuk tabel `murojaah_srs`
--
ALTER TABLE `murojaah_srs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mutabaah`
--
ALTER TABLE `mutabaah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `tajwid_kuis`
--
ALTER TABLE `tajwid_kuis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `materi_id` (`materi_id`);

--
-- Indeks untuk tabel `tajwid_materi`
--
ALTER TABLE `tajwid_materi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `targets`
--
ALTER TABLE `targets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_targets`
--
ALTER TABLE `user_targets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_user_kat` (`user_id`,`kategori`);

--
-- Indeks untuk tabel `user_todos`
--
ALTER TABLE `user_todos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `achievements`
--
ALTER TABLE `achievements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `bookmark`
--
ALTER TABLE `bookmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `game_history`
--
ALTER TABLE `game_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `murojaah_progress`
--
ALTER TABLE `murojaah_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `murojaah_srs`
--
ALTER TABLE `murojaah_srs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `mutabaah`
--
ALTER TABLE `mutabaah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `targets`
--
ALTER TABLE `targets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `achievements`
--
ALTER TABLE `achievements`
  ADD CONSTRAINT `achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `targets`
--
ALTER TABLE `targets`
  ADD CONSTRAINT `targets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
