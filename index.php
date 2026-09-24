<?php
// Data Mahasiswa
$profile = [
    'nama' => 'M. Javier Thufail',
    'nim'  => '254107020019',
    'kelas' => 'TI-2D',
    'absen' => '18',
    'initials' => 'JT'
];

// Data List Jobsheet
$jobsheets = [
    ['no' => '01', 'title' => 'Jobsheet 01', 'desc' => 'HTML5 Semantic Skeleton', 'url' => 'Jobsheet-01/index.html'],
    ['no' => '02', 'title' => 'Jobsheet 02', 'desc' => 'CSS3 Styling Dasar', 'url' => 'Jobsheet-02/index.html'],
    ['no' => '03', 'title' => 'Jobsheet 03', 'desc' => 'Responsive Design', 'url' => 'Jobsheet-03/index.html'],
    ['no' => '04', 'title' => 'Jobsheet 04', 'desc' => 'UI/UX Design', 'url' => 'Jobsheet-04/index.html'],
    ['no' => '05', 'title' => 'Jobsheet 05', 'desc' => 'JavaScript DOM & Event', 'url' => 'Jobsheet-05/index.html'],
    ['no' => '06', 'title' => 'Jobsheet 06', 'desc' => 'Fetch API & JSON', 'url' => 'Jobsheet-06/index.html'],
    ['no' => '07', 'title' => 'Jobsheet 07', 'desc' => 'PHP Dasar & Form Handling', 'url' => 'Jobsheet-07/index.php'],
    ['no' => '08', 'title' => 'Jobsheet 08', 'desc' => 'Koneksi PostgreSQL', 'url' => 'Jobsheet-08/index.php'],
    ['no' => '09', 'title' => 'Jobsheet 09', 'desc' => 'CRUD Penuh', 'url' => 'Jobsheet-09/index.html'],
    ['no' => '10', 'title' => 'Jobsheet 10', 'desc' => 'Autentikasi & Manajemen Sesi', 'url' => 'Jobsheet-10/index.html'],
    ['no' => '11', 'title' => 'Jobsheet 11', 'desc' => 'Keamanan Web Dasar', 'url' => 'Jobsheet-11/index.html'],
    ['no' => '12', 'title' => 'Jobsheet 12', 'desc' => 'Integrasi Modul Peminjaman', 'url' => 'Jobsheet-12/index.html'],
    ['no' => '13', 'title' => 'Jobsheet 13', 'desc' => 'Deployment & Dokumentasi (SIMPUS-Mini)', 'url' => 'Jobsheet-13/index.html'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jobsheet Desain dan Pemrograman Web</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #090d16;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(147, 51, 234, 0.15) 0px, transparent 50%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 1rem;
        }

        .container {
            width: 100%;
            max-width: 680px;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 2.5rem;
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* --- KARTU PROFIL HASIL DESAIN --- */
        .profile-hero {
            position: relative;
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.6) 0%, rgba(15, 23, 42, 0.8) 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 1.5rem 1.75rem;
            margin-bottom: 2rem;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .profile-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .avatar {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.5rem;
            color: #ffffff;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
            flex-shrink: 0;
        }

        .profile-details {
            flex-grow: 1;
            z-index: 1;
        }

        .profile-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.02em;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px #10b981;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .info-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 8px 12px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 0.85rem;
            font-weight: 600;
            color: #38bdf8;
        }

        /* --- STYLING LIST JOBSHEET --- */
        .section-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1rem;
        }

        .jobsheet-list {
            list-style: none;
            display: grid;
            gap: 10px;
            max-height: 520px;
            overflow-y: auto;
            padding-right: 6px;
        }

        /* Custom Scrollbar */
        .jobsheet-list::-webkit-scrollbar {
            width: 6px;
        }
        .jobsheet-list::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
        }
        .jobsheet-list::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
        }
        .jobsheet-list::-webkit-scrollbar-thumb:hover {
            background: rgba(56, 189, 248, 0.4);
        }

        .jobsheet-item a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1.2rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .jobsheet-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .jobsheet-title {
            color: #f1f5f9;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .jobsheet-desc {
            color: #94a3b8;
            font-size: 0.82rem;
        }

        .badge {
            padding: 2px 6px;
            background: rgba(56, 189, 248, 0.1);
            color: #38bdf8;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            min-width: 26px;
            text-align: center;
        }

        .jobsheet-item a:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateX(4px);
        }

        .jobsheet-item a:hover .jobsheet-title {
            color: #38bdf8;
        }

        .arrow-icon {
            color: #475569;
            font-size: 1.1rem;
            transition: transform 0.25s ease, color 0.25s ease;
        }

        .jobsheet-item a:hover .arrow-icon {
            color: #38bdf8;
            transform: translateX(3px);
        }

        @media (max-width: 520px) {
            .profile-hero {
                flex-direction: column;
                align-items: flex-start;
            }
            .profile-grid {
                grid-template-columns: 1fr;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <main class="container">
        <!-- Profile Section -->
        <div class="profile-hero">
            <div class="avatar"><?= htmlspecialchars($profile['initials']) ?></div>
            <div class="profile-details">
                <div class="profile-name">
                    <?= htmlspecialchars($profile['nama']) ?>
                    <span class="status-dot" title="Aktif"></span>
                </div>
                <div class="profile-grid">
                    <div class="info-pill">
                        <span class="info-label">NIM</span>
                        <span class="info-value"><?= htmlspecialchars($profile['nim']) ?></span>
                    </div>
                    <div class="info-pill">
                        <span class="info-label">Kelas</span>
                        <span class="info-value"><?= htmlspecialchars($profile['kelas']) ?></span>
                    </div>
                    <div class="info-pill">
                        <span class="info-label">Absen</span>
                        <span class="info-value"><?= htmlspecialchars($profile['absen']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-title">Daftar Jobsheet Desain & Pemograman Web</div>

        <ul class="jobsheet-list">
            <?php foreach ($jobsheets as $item): ?>
                <li class="jobsheet-item">
                    <a href="<?= htmlspecialchars($item['url']) ?>">
                        <div class="jobsheet-info">
                            <span class="jobsheet-title">
                                <span class="badge"><?= htmlspecialchars($item['no']) ?></span>
                                <?= htmlspecialchars($item['title']) ?>
                            </span>
                            <span class="jobsheet-desc"><?= htmlspecialchars($item['desc']) ?></span>
                        </div>
                        <span class="arrow-icon">→</span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>

</body>
</html>