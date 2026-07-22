<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eksplorasi Hadis - Hifzhly</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #059669;
            --primary-light: #10b981;
            --primary-dark: #047857;
            --bg: #f6faf8;
            --ink: #0f172a;
            --muted: #64748b;
            --border: #e2e8f0;
            --card-bg: rgba(255, 255, 255, 0.9);
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #d1fae5, transparent 40%), var(--bg);
            color: var(--ink);
            min-height: 100vh;
            padding: 30px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--ink);
            margin-bottom: 10px;
        }

        .header p {
            color: var(--muted);
            font-size: 0.95rem;
        }

        .search-box {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            padding: 24px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--ink);
        }

        select,
        input {
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
        }

        select:focus,
        input:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 4px #d1fae5;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            color: white;
            border: none;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            align-self: end;
            height: 45px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
        }

        /* Result Card */
        #resultArea {
            display: none;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.05);
            overflow: hidden;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hadith-header {
            background: rgba(16, 185, 129, 0.1);
            padding: 16px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .hadith-content {
            padding: 30px 24px;
        }

        .arabic-text {
            font-family: 'Amiri', serif;
            font-size: 2rem;
            line-height: 2.2;
            text-align: right;
            color: var(--ink);
            margin-bottom: 24px;
            direction: rtl;
        }

        .terjemahan {
            font-size: 1rem;
            line-height: 1.7;
            color: var(--muted);
            padding-top: 20px;
            border-top: 1px dashed var(--border);
        }

        .terjemahan strong {
            color: var(--ink);
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Loading Spinner */
        .loader {
            display: none;
            text-align: center;
            padding: 40px;
            color: var(--primary);
        }

        .loader i {
            font-size: 2.5rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        /* Error Message */
        .error-msg {
            display: none;
            background: #fee2e2;
            color: #ef4444;
            padding: 16px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .search-box {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .arabic-text {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Eksplorasi Hadis</h1>
            <p>Pencarian hadis otomatis terhubung dengan API Publik</p>
        </div>

        <div class="error-msg" id="errorMsg"></div>

        <div class="search-box">
            <div class="form-group">
                <label>Pilih Kitab</label>
                <select id="pilihKitab">
                    <option value="bukhari">Sahih Bukhari</option>
                    <option value="muslim">Sahih Muslim</option>
                    <option value="abu-dawud">Sunan Abu Dawud</option>
                    <option value="tirmidzi">Sunan Tirmidzi</option>
                    <option value="nasai">Sunan An-Nasai</option>
                    <option value="ibnu-majah">Sunan Ibnu Majah</option>
                    <option value="ahmad">Musnad Ahmad</option>
                    <option value="malik">Muwatta Malik</option>
                    <option value="darimi">Sunan Ad-Darimi</option>
                </select>
            </div>

            <div class="form-group">
                <label>Nomor Hadis</label>
                <input type="number" id="nomorHadis" placeholder="Contoh: 1" value="1" min="1">
            </div>

            <button class="btn" onclick="cariHadis()">
                <i class="fas fa-search"></i> Cari
            </button>
        </div>

        <div class="loader" id="loader">
            <i class="fas fa-circle-notch"></i>
            <p style="margin-top: 10px; font-weight: 600; color: var(--muted);">Mengambil data dari API...</p>
        </div>

        <div id="resultArea">
            <div class="hadith-header">
                <span id="judulKitab"><i class="fas fa-book-open"></i> Sahih Bukhari</span>
                <span id="labelNomor">Hadis No. 1</span>
            </div>
            <div class="hadith-content">
                <!-- Teks Arab -->
                <div class="arabic-text" id="teksArab"></div>

                <!-- Terjemahan & Sanad -->
                <div class="terjemahan">
                    <strong><i class="fas fa-language"></i> Terjemahan & Sanad</strong>
                    <div id="teksArti"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gunakan Public API Indonesia (api.hadith.gading.dev)
        async function cariHadis() {
            const kitab = document.getElementById('pilihKitab').value;
            const nomor = document.getElementById('nomorHadis').value;
            const loader = document.getElementById('loader');
            const resultArea = document.getElementById('resultArea');
            const errorMsg = document.getElementById('errorMsg');

            if (!nomor) {
                showError("Nomor hadis tidak boleh kosong!");
                return;
            }

            // Reset UI
            errorMsg.style.display = 'none';
            resultArea.style.display = 'none';
            loader.style.display = 'block';

            try {
                // Proses Ambil Data via Fetch API
                const response = await fetch(`https://api.hadith.gading.dev/books/${kitab}/${nomor}`);
                const resData = await response.json();

                loader.style.display = 'none';

                if (resData.code !== 200 || resData.error) {
                    showError("Hadis tidak ditemukan atau nomor melebihi jumlah hadis dalam kitab ini.");
                    return;
                }

                const data = resData.data;
                const isiHadis = data.contents;

                // Tampilkan Data ke UI
                document.getElementById('judulKitab').innerHTML = `<i class="fas fa-book-open"></i> ${data.name}`;
                document.getElementById('labelNomor').innerText = `Hadis No. ${isiHadis.number}`;
                document.getElementById('teksArab').innerText = isiHadis.arab;
                document.getElementById('teksArti').innerText = isiHadis.id;

                // Tampilkan Card
                resultArea.style.display = 'block';

            } catch (error) {
                loader.style.display = 'none';
                showError("Gagal terhubung ke API. Pastikan koneksi internet stabil.");
            }
        }

        function showError(msg) {
            const errorMsg = document.getElementById('errorMsg');
            errorMsg.innerText = msg;
            errorMsg.style.display = 'block';
            setTimeout(() => errorMsg.style.display = 'none', 3000);
        }

        // Auto load hadis pertama kali dibuka
        window.onload = cariHadis;
    </script>
</body>

</html>