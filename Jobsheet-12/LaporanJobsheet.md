| | |
| :--- | :--- |
| **Mata Kuliah** | : Desain dan Pemrograman Web |
| **Program Studi** | : D4 – Teknik Informatika |
| **Semester** | : 3 |

---

| | |
| :--- | :--- |
| **Kelas** | : TI-2D |
| **NIM** | : 254107020019 |
| **Nama** | : M. Javier Thufail |
| **Jobsheet Ke-** | : 12 |

---

# Laporan Praktikum Jobsheet 12: Integrasi Modul Transaksi & Keamanan Sesi/Stok

## 1. Deskripsi Tugas
Pada Jobsheet 12, fokus utama pembelajaran adalah mengintegrasikan seluruh modul *front-end* dan *back-end* yang telah dibangun sejak Jobsheet 8 hingga Jobsheet 11 menjadi satu kesatuan sistem transaksi yang utuh.

Dalam pengembangan **Sistem Informasi Apotek (SIAFARMA)**, modul transaksi direalisasikan pada pengelolaan **Penjualan Obat** dan **Pembatalan Transaksi / Retur**. Pengembangan ini menerapkan mekanisme *Database Transaction* (`beginTransaction`, `commit`, `rollBack`) untuk menjamin konsistensi data, serta penguncian baris (`SELECT ... FOR UPDATE`) guna mencegah *Race Condition* pada stok obat ketika diproses oleh beberapa petugas (kasir) secara bersamaan.

---

## 2. Implementasi & Penjelasan Kode

### 2.1. Skema Database Relasional Penjualan (`sql/03_penjualan.sql`)
Tabel `penjualan` dan `detail_penjualan` dibuat untuk menghubungkan data petugas (`users`) dengan data obat (`obat`).

```sql
-- Pembuatan tabel header penjualan
CREATE TABLE IF NOT EXISTS public.penjualan (
    id SERIAL PRIMARY KEY,
    tanggal TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    total NUMERIC NOT NULL DEFAULT 0,
    user_id INTEGER REFERENCES public.users(id) ON DELETE SET NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'selesai'
);

-- Pembuatan tabel detail penjualan (relasi obat & penjualan)
CREATE TABLE IF NOT EXISTS public.detail_penjualan (
    id SERIAL PRIMARY KEY,
    penjualan_id INTEGER NOT NULL REFERENCES public.penjualan(id) ON DELETE CASCADE,
    obat_id INTEGER NOT NULL REFERENCES public.obat(id) ON DELETE RESTRICT,
    jumlah INTEGER NOT NULL CHECK (jumlah > 0),
    harga NUMERIC NOT NULL,
    subtotal NUMERIC NOT NULL
);
```
Penjelasan: Penggunaan REFERENCES memastikan integritas data (Foreign Key). Jika data obat digunakan dalam transaksi, maka obat tersebut dibatasi agar tidak dapat dihapus secara tidak sengaja (ON DELETE RESTRICT).

### 2.2. Transaksi Penjualan & Pencegahan Race Condition (penjualan/proses_tambah.php)
Proses pencatatan transaksi dilakukan di dalam blok Database Transaction untuk memastikan bahwa pencatatan nota dan pemotongan stok obat terjadi secara atomik (semua berhasil atau dibatalkan total).

```PHP
try {
    // 1. Mulai Database Transaction
    $pdo->beginTransaction();

    /*
     * 2. Kunci baris data obat dengan FOR UPDATE
     * Mencegah Race Condition saat 2 kasir memproses obat yang sama secara bersamaan
     */
    $stmt = $pdo->prepare("SELECT id, nama_obat, harga_jual, stok FROM obat WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => (int)$obatId]);
    $obat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$obat) {
        throw new Exception('Data obat tidak ditemukan.');
    }

    if ($jumlah > $obat['stok']) {
        throw new Exception('Stok obat tidak mencukupi. Sisa stok: ' . $obat['stok']);
    }

    $harga = $obat['harga_jual'];
    $subtotal = $harga * $jumlah;
    $userId = $_SESSION['user_id'];

    // 3. Simpan Header Penjualan
    $stmt = $pdo->prepare("INSERT INTO penjualan (total, user_id, status) VALUES (:total, :user_id, 'selesai') RETURNING id");
    $stmt->execute([
        'total' => $subtotal,
        'user_id' => $userId
    ]);
    $penjualanId = $stmt->fetchColumn();

    // 4. Simpan Detail Penjualan
    $stmt = $pdo->prepare("INSERT INTO detail_penjualan (penjualan_id, obat_id, jumlah, harga, subtotal) VALUES (:penjualan_id, :obat_id, :jumlah, :harga, :subtotal)");
    $stmt->execute([
        'penjualan_id' => $penjualanId,
        'obat_id' => $obatId,
        'jumlah' => $jumlah,
        'harga' => $harga,
        'subtotal' => $subtotal
    ]);

    // 5. Potong Stok Obat
    $stmt = $pdo->prepare("UPDATE obat SET stok = stok - :jumlah WHERE id = :id");
    $stmt->execute([
        'jumlah' => $jumlah,
        'id' => $obatId
    ]);

    // 6. Commit seluruh perubahan jika tidak ada kesalahan
    $pdo->commit();

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => 'Transaksi berhasil disimpan! Invoice: INV-' . str_pad($penjualanId, 3, '0', STR_PAD_LEFT)
    ];
    header('Location: list.php');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal mencatat transaksi: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}
```
Penjelasan: Penggunaan FOR UPDATE mengunci baris obat yang sedang dibaca. Jika ada transaksi lain yang ingin mengakses data obat yang sama di saat bersamaan, transaksi kedua akan menunggu hingga transaksi pertama selesai (commit atau rollBack), sehingga stok obat tidak akan pernah bernilai negatif.

### 2.3. Pembatalan Transaksi / Retur (penjualan/proses_batal.php)
Fitur ini memungkinkan pembatalan transaksi yang sudah selesai dengan mengembalikan jumlah obat ke stok gudang secara otomatis.

```PHP
try {
    $pdo->beginTransaction();

    // Lock baris transaksi penjualan
    $stmt = $pdo->prepare("SELECT id, status FROM penjualan WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $id]);
    $trx = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trx || $trx['status'] !== 'selesai') {
        throw new Exception('Transaksi tidak ditemukan atau sudah dibatalkan.');
    }

    // Ambil detail item obat yang dibeli
    $stmtDetail = $pdo->prepare("SELECT obat_id, jumlah FROM detail_penjualan WHERE penjualan_id = :id");
    $stmtDetail->execute(['id' => $id]);
    $details = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);

    // Kembalikan stok obat ke tabel obat
    foreach ($details as $item) {
        $updateBuku = $pdo->prepare("UPDATE obat SET stok = stok + :jumlah WHERE id = :obat_id");
        $updateBuku->execute([
            'jumlah' => $item['jumlah'],
            'obat_id' => $item['obat_id']
        ]);
    }

    // Ubah status transaksi menjadi dibatalkan
    $updateTrx = $pdo->prepare("UPDATE penjualan SET status = 'dibatalkan' WHERE id = :id");
    $updateTrx->execute(['id' => $id]);

    $pdo->commit();
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Transaksi berhasil dibatalkan dan stok obat dikembalikan.'];

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal membatalkan transaksi: ' . $e->getMessage()];
}
```
Penjelasan: Pengembalian stok dilakukan di dalam looping detail transaksi sebelum me-UPDATE status penjualan menjadi 'dibatalkan'. Seluruh proses dibungkus transaksi agar tidak terjadi ketidakcocokan data jika terjadi galat server.

### 2.4. Riwayat Penjualan Lanjutan dengan Multi-JOIN (penjualan/riwayat.php)
Menampilkan histori transaksi secara transparan dengan menggabungkan 4 tabel sekaligus.

```PHP
$stmt = $pdo->query("
    SELECT 
        p.id AS invoice_id,
        p.tanggal,
        p.total,
        p.status,
        u.nama AS nama_kasir,
        o.nama_obat,
        dp.jumlah,
        dp.harga,
        dp.subtotal
    FROM penjualan p
    JOIN detail_penjualan dp ON dp.penjualan_id = p.id
    JOIN obat o ON dp.obat_id = o.id
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY p.id DESC
");
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
```
Penjelasan: Perintah JOIN menggabungkan data transaksi, detail transaksi, data spesifik obat, serta identitas kasir yang bertugas sehingga laporan penjualan disajikan dengan rinci.

### 2.5. Pembaruan Ringkasan Statistik Beranda (index.php)
Kartu statistik total penjualan pada dashboard kini dihitung secara dinamis dari database PostgreSQL.

```PHP
// Mengambil data ringkasan dinamis dari database
$totalObat      = $pdo->query("SELECT COUNT(*) FROM obat")->fetchColumn();
$totalKategori  = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$totalSupplier  = $pdo->query("SELECT COUNT(*) FROM supplier")->fetchColumn();
$totalPenjualan = $pdo->query("SELECT COUNT(*) FROM penjualan WHERE status = 'selesai'")->fetchColumn();
```
Penjelasan: Statistik tidak lagi bersifat statis. Angka total penjualan langsung memperhitungkan transaksi yang berstatus 'selesai'.

---

## 3. Kesimpulan
Integrasi modul transaksi pada Sistem Informasi Apotek (SIAFARMA) telah berhasil diimplementasikan. Penerapan Database Transaction menjamin bahwa pengoperasian tabel header, detail transaksi, dan perubahan stok obat berjalan secara penuh atau dibatalkan sama sekali ketika terjadi kesalahan. Dukungan penguncian klausa FOR UPDATE berhasil mengamankan stok dari ancaman Race Condition, sementara penggunaan perintah JOIN menyempurnakan penyajian riwayat transaksi dan dashboard analisis secara akurat.