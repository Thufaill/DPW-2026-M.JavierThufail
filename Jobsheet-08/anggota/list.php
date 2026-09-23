<?php
session_start();
$page_title = "Daftar Anggota";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarAnggota = $pdo->query("SELECT * FROM anggota ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Anggota</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <div>
                    <label for="search-input">Cari Nama Anggota</label>
                    <input type="text" id="search-input" placeholder="Ketik nama anggota...">
                </div>
                <a href="tambah.php" class="btn-reload" style="text-decoration: none;">+ Tambah Anggota</a>

                <!-- Tombol Reset Data Session -->
                <a href="../includes/reset_session.php" 
                    class="btn-reload" 
                    style="background-color: #a72828; text-decoration: none;"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus seluruh data session?');">
                    🗑️ Reset Data
                    </a>
            </div>

            <p id="table-counter" class="table-counter">Menampilkan <?php echo count($daftarAnggota); ?> dari <?php echo count($daftarAnggota); ?> anggota</p>
            
            <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No. Anggota</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Umur</th>
                        <th>No. HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarAnggota)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Belum ada data anggota. Silakan tambah lewat menu "Tambah Anggota".</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($daftarAnggota as $index => $anggota): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($anggota['no_anggota'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['nama'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['alamat'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['umur'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['no_hp'] ?? '-'); ?></td>
                            <td>
                                <button type="button">Edit</button>
                                <button type="button">Detail</button>
                                <button type="button" class="btn-hapus">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>