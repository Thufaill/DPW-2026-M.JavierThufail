<?php

$profile = [
    'nama' => 'M. Javier Thufail',
    'nim'  => '254107020019',
    'kelas' => 'TI-2D',
    'absen' => '18',
    'initials' => 'JT'
];

$jobsheets = [
    [
        'nomor' => '01',
        'judul' => 'Jobsheet 01',
        'deskripsi' => 'HTML5 Semantic Skeleton',
        'link' => 'Jobsheet-01/index.html'
    ],
    [
        'nomor' => '02',
        'judul' => 'Jobsheet 02',
        'deskripsi' => 'CSS3 Styling Dasar',
        'link' => 'Jobsheet-02/index.html'
    ],
    [
        'nomor' => '03',
        'judul' => 'Jobsheet 03',
        'deskripsi' => 'Responsive Design',
        'link' => 'Jobsheet-03/index.html'
    ],
    [
        'nomor' => '04',
        'judul' => 'Jobsheet 04',
        'deskripsi' => 'UI/UX Design',
        'link' => 'Jobsheet-04/index.html'
    ],
    [
        'nomor' => '05',
        'judul' => 'Jobsheet 05',
        'deskripsi' => 'JavaScript DOM & Event',
        'link' => 'Jobsheet-05/index.html'
    ],
    [
        'nomor' => '06',
        'judul' => 'Jobsheet 06',
        'deskripsi' => 'Fetch API & JSON',
        'link' => 'Jobsheet-06/index.html'
    ],
    [
        'nomor' => '07',
        'judul' => 'Jobsheet 07',
        'deskripsi' => 'PHP Dasar & Form Handling',
        'link' => 'Jobsheet-07/index.php'
    ],
    [
        'nomor' => '08',
        'judul' => 'Jobsheet 08',
        'deskripsi' => 'Koneksi PostgreSQL',
        'link' => 'Jobsheet-08/index.php'
    ],
    [
        'nomor' => '09',
        'judul' => 'Jobsheet 09',
        'deskripsi' => 'CRUD Penuh',
        'link' => 'Jobsheet-09/index.html'
    ],
    [
        'nomor' => '10',
        'judul' => 'Jobsheet 10',
        'deskripsi' => 'Autentikasi & Manajemen Sesi',
        'link' => 'Jobsheet-10/index.html'
    ],
    [
        'nomor' => '11',
        'judul' => 'Jobsheet 11',
        'deskripsi' => 'Keamanan Web Dasar',
        'link' => 'Jobsheet-11/index.html'
    ],
    [
        'nomor' => '12',
        'judul' => 'Jobsheet 12',
        'deskripsi' => 'Integrasi Modul Peminjaman',
        'link' => 'Jobsheet-12/index.html'
    ],
    [
        'nomor' => '13',
        'judul' => 'Jobsheet 13',
        'deskripsi' => 'Deployment & Dokumentasi (SIMPUS-Mini)',
        'link' => 'Jobsheet-13/index.html'
    ],
];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DPW | Kumpulan Jobsheet</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <main class="container">
        <!-- Profile Section -->
        <div class="profile-hero">
            <div class="avatar"><?= htmlspecialchars($profile['initials']); ?></div>
            <div class="profile-details">
                <div class="profile-name">
                    <?= htmlspecialchars($profile['nama']); ?>
                    <span class="status-dot" title="Aktif"></span>
                </div>
                <div class="profile-grid">
                    <div class="info-pill">
                        <span class="info-label">NIM</span>
                        <span class="info-value"><?= htmlspecialchars($profile['nim']); ?></span>
                    </div>
                    <div class="info-pill">
                        <span class="info-label">Kelas</span>
                        <span class="info-value"><?= htmlspecialchars($profile['kelas']); ?></span>
                    </div>
                    <div class="info-pill">
                        <span class="info-label">Absen</span>
                        <span class="info-value"><?= htmlspecialchars($profile['absen']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-title">
            <span>Daftar Jobsheet Desain & Pemrograman Web</span>
            <span class="total-badge"><?= count($jobsheets); ?> Total</span>
        </div>

        <ul class="jobsheet-list">
            <?php foreach ($jobsheets as $jobsheet): ?>
                <li class="jobsheet-item">
                    <a href="<?= htmlspecialchars($jobsheet['link']); ?>">
                        <div class="jobsheet-info">
                            <span class="jobsheet-title">
                                <span class="badge"><?= htmlspecialchars($jobsheet['nomor']); ?></span>
                                <?= htmlspecialchars($jobsheet['judul']); ?>
                            </span>
                            <span class="jobsheet-desc"><?= htmlspecialchars($jobsheet['deskripsi']); ?></span>
                        </div>
                        <span class="arrow-icon">→</span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <footer class="footer">
            <p>&copy; <?= date('Y'); ?> Desain & Pemrograman Web — Politeknik Negeri Malang</p>
        </footer>
    </main>

</body>

</html>