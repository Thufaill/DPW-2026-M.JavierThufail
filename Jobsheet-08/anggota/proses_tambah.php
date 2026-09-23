<?php
session_start();

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$umur = trim($_POST['umur'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];

// 1. Validasi Field Wajib
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}

// 2. Validasi Format No. HP (Jika diisi)
if ($noHp !== '' && !preg_match('/^[0-9]{9,15}$/', $noHp)) {
    $errors['no_hp'] = "No. HP hanya boleh berisi angka dan berpanjang 9-15 digit.";
}

// 3. Validasi Umur (Jika diisi)
if ($umur !== '') {
    if (!ctype_digit($umur)) {
        $errors[] = "Umur harus berupa angka bulat.";
    } else {
        $umurInt = (int)$umur;
        if ($umurInt < 5 || $umurInt > 100) {
            $errors[] = "Umur harus berada dalam rentang 5 hingga 100 tahun.";
        }
    }
}

// 4. Validasi Cek Duplikasi No. Anggota dalam Session
if ($noAnggota !== '' && isset($_SESSION['anggota'])) {
    foreach ($_SESSION['anggota'] as $anggota) {
        if (strcasecmp($anggota['no_anggota'], $noAnggota) === 0) {
            $errors[] = "No. Anggota '$noAnggota' sudah terdaftar.";
            break;
        }
    }
}

// Jika terdapat error validasi, kirim pesan flash dan kembalikan ke form tambah
if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO anggota (nama, no_anggota, alamat, no_hp) 
    VALUES (:nama, :no_anggota, :alamat, :no_hp)"
    );

// Simpan data anggota baru ke session
$_SESSION['anggota'][] = [
    'nama' => $nama,
    'no_anggota' => $noAnggota,
    'alamat' => $alamat,
    'umur' => $umur,
    'no_hp' => $noHp,
];

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
header('Location: list.php');
exit;