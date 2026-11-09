
<?php
$nama = '';
$email = '';
$hp = '';
$umur = '';
$github = '';
$kategori = '';
$peserta = '';

$errors = [];
$success = false;

// Daftar pilihan yang diizinkan
$kategoriList = [
    'Web Development',
    'Mobile Development',
    'UI/UX Design',
    'Data Science'
];

$pesertaList = [
    'Individu',
    'Tim'
];

// Fungsi keamanan output
function aman($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil data formulir
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $hp = trim($_POST['hp'] ?? '');
    $umur = trim($_POST['umur'] ?? '');
    $github = trim($_POST['github'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $peserta = $_POST['peserta'] ?? '';

    // Validasi nama
    $panjangNama = function_exists('mb_strlen')
        ? mb_strlen($nama, 'UTF-8')
        : strlen($nama);

    if ($nama === '') {
        $errors['nama'] = 'Nama wajib diisi.';
    } elseif ($panjangNama < 3 || $panjangNama > 50) {
        $errors['nama'] = 'Nama harus 3-50 karakter.';
    }

    // Validasi email
    if ($email === '') {
        $errors['email'] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }

    // Validasi nomor HP menggunakan REGEX
    if ($hp === '') {
        $errors['hp'] = 'Nomor HP wajib diisi.';
    } elseif (!preg_match('/^(?:\+62|62|0)8[1-9][0-9]{7,11}$/', $hp)) {
        $errors['hp'] = 'Nomor HP tidak valid. Contoh: 081234567890.';
    }

    // Validasi umur
    if ($umur === '') {
        $errors['umur'] = 'Umur wajib diisi.';
    } elseif (!ctype_digit($umur) || (int)$umur < 17 || (int)$umur > 19) {
        $errors['umur'] = 'Peringatan! Umur harus antara 17-19 tahun.';
    }

    // Validasi URL GitHub (opsional)
    if ($github !== '') {
        $urlValid = filter_var($github, FILTER_VALIDATE_URL);
        $host = parse_url($github, PHP_URL_HOST);

        if (
            !$urlValid ||
            !in_array(strtolower($host ?? ''), ['github.com', 'www.github.com']) ||
            empty(parse_url($github, PHP_URL_PATH)) ||
            parse_url($github, PHP_URL_SCHEME) !== 'https'
        ) {
            $errors['github'] = 'Masukkan URL repositori GitHub yang valid.';
        }
    }

    // Validasi kategori dengan whitelist
    if (!in_array($kategori, $kategoriList, true)) {
        $errors['kategori'] = 'Silakan pilih kategori yang tersedia.';
    }

    // Validasi peserta dengan whitelist
    if (!in_array($peserta, $pesertaList, true)) {
        $errors['peserta'] = 'Silakan pilih jenis peserta.';
    }

    // Jika tidak ada error
    if (empty($errors)) {
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Lomba Coding</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px 15px;
            color: #1e293b;
        }

        .container {
            max-width: 550px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #2563eb;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #2563eb;
        }

        .radio-group {
            display: flex;
            gap: 20px;
        }

        .radio-group label {
            font-weight: normal;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .radio-group input {
            width: auto;
        }

        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 15px;
            border-radius: 7px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .warning {
            color: #b45309;
            font-size: 13px;
            margin-top: 6px;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .required {
            color: red;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Registrasi Lomba Coding</h2>
    <p class="subtitle">Silakan lengkapi formulir pendaftaran berikut.</p>

    <?php if ($success): ?>
        <div class="success">
            <strong>Registrasi berhasil!</strong><br>
            Nama: <?= aman($nama) ?><br>
            Email: <?= aman($email) ?><br>
            Nomor HP: <?= aman($hp) ?><br>
            Umur: <?= aman($umur) ?> tahun<br>
            GitHub: <?= $github !== '' ? aman($github) : 'Tidak diisi' ?><br>
            Kategori: <?= aman($kategori) ?><br>
            Peserta: <?= aman($peserta) ?>
            <br><br>
            <small>Data berhasil divalidasi, tetapi belum disimpan ke database.</small>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <div class="form-group">
            <label>Nama <span class="required">*</span></label>
            <input type="text" name="nama"
                value="<?= aman($nama) ?>"
                minlength="3" maxlength="50"
                placeholder="Masukkan nama lengkap">

            <?php if (isset($errors['nama'])): ?>
                <p class="error"><?= aman($errors['nama']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Email <span class="required">*</span></label>
            <input type="email" name="email"
                value="<?= aman($email) ?>"
                placeholder="contoh@email.com">

            <?php if (isset($errors['email'])): ?>
                <p class="error"><?= aman($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Nomor HP <span class="required">*</span></label>
            <input type="text" name="hp"
                value="<?= aman($hp) ?>"
                placeholder="081234567890">

            <?php if (isset($errors['hp'])): ?>
                <p class="error"><?= aman($errors['hp']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Umur <span class="required">*</span></label>
            <input type="number" name="umur"
                value="<?= aman($umur) ?>"
                placeholder="Masukkan umur"
                min="17" max="19">

            <?php if (isset($errors['umur'])): ?>
                <p class="error"><?= aman($errors['umur']) ?></p>
            <?php else: ?>
                <p class="warning">Umur peserta harus 17-19 tahun.</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>URL Repositori GitHub (Opsional)</label>
            <input type="url" name="github"
                value="<?= aman($github) ?>"
                placeholder="https://github.com/username/repository">

            <?php if (isset($errors['github'])): ?>
                <p class="error"><?= aman($errors['github']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Kategori Lomba <span class="required">*</span></label>
            <select name="kategori">
                <option value="">-- Pilih Kategori --</option>

                <?php foreach ($kategoriList as $item): ?>
                    <option value="<?= aman($item) ?>"
                        <?= $kategori === $item ? 'selected' : '' ?>>
                        <?= aman($item) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <?php if (isset($errors['kategori'])): ?>
                <p class="error"><?= aman($errors['kategori']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Jenis Peserta <span class="required">*</span></label>

            <div class="radio-group">
                <?php foreach ($pesertaList as $item): ?>
                    <label>
                        <input type="radio" name="peserta"
                            value="<?= aman($item) ?>"
                            <?= $peserta === $item ? 'checked' : '' ?>>
                        <?= aman($item) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <?php if (isset($errors['peserta'])): ?>
                <p class="error"><?= aman($errors['peserta']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit">Daftar Sekarang</button>

    </form>

</div>

</body>
</html>
