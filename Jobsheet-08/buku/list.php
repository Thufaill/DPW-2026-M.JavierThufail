<?php
session_start();
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$daftarBuku = $pdo->query("SELECT * FROM buku ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <section>
            <h2>Daftar Buku</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <div class="search-box">
                <div>
                    <label for="search-input">Cari Judul Buku</label>
                    <input type="text" id="search-input" placeholder="Ketik judul buku...">
                </div>
                <a href="tambah.php" class="btn-reload" style="text-decoration: none;">+ Tambah Buku</a>
                
                <!-- Tombol Reset Data Session -->
                <a href="../includes/reset_session.php" 
                    class="btn-reload" 
                    style="background-color: #a72828; text-decoration: none;"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus seluruh data session?');">
                    🗑️ Reset Data
                </a>
            </div>

            <p id="table-counter" class="table-counter">Menampilkan <?php echo count($daftarBuku); ?> dari <?php echo count($daftarBuku); ?> buku</p>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarBuku)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($daftarBuku as $buku): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($buku['judul'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($buku['pengarang'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($buku['tahun'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($buku['stok'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($buku['kategori'] ?? '-'); ?></td>
                                <td>
                                    <button type="button">Edit</button>
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