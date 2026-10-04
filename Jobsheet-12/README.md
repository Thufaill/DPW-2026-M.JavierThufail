## Ide Latihan Tambahan (Opsional)

1. **Terapkan validasi bisnis yang belum ada** — sesuai catatan di
   [README.md](../README.md) jobsheet ini: anggota dengan peminjaman
   **terlambat lebih dari 14 hari** tidak boleh meminjam buku baru.
   Petunjuk: hitung selisih `CURRENT_DATE` dengan `tanggal_pinjam` pada
   transaksi aktif anggota tersebut sebelum mengizinkan `INSERT` baru
   di `proses_tambah.php`.  
   **Jawaban:**  
   Validasi bisnis ini bertujuan memastikan integritas aturan operasional sebelum transaksi baru disimpan ke database. Penerapannya dilakukan di dalam blok transaksi sebelum operasi `INSERT` dijalankan.

   1. **Logika Query Pengecekan:**
      Sistem memeriksa apakah pengguna/anggota memiliki riwayat transaksi aktif yang selisih tanggalnya melebihi batas toleransi (14 hari):
      ```sql
      SELECT COUNT(*) FROM peminjaman 
      WHERE anggota_id = :anggota_id 
        AND status = 'dipinjam' 
        AND (CURRENT_DATE - tanggal_pinjam::date) > 14;
      ```

   2. **Implementasi pada Script Transaksi (`proses_tambah.php`):**
      ```php
      // 1. Validasi Aturan Bisnis: Cek keterlambatan peminjaman aktif
      $stmtCek = $pdo->prepare("
          SELECT COUNT(*) 
          FROM peminjaman 
          WHERE anggota_id = :anggota_id 
            AND status = 'dipinjam' 
            AND (CURRENT_DATE - tanggal_pinjam::date) > 14
      ");
      $stmtCek->execute(['anggota_id' => $anggotaId]);
      $adaTerlambat = (int) $stmtCek->fetchColumn();

      if ($adaTerlambat > 0) {
          throw new Exception('Peminjaman ditolak: Anggota masih memiliki tanggungan peminjaman yang terlambat lebih dari 14 hari.');
      }
      ```
   *(Pada konteks studi kasus apotek **SIAFARMA**, validasi bisnis ini diterapkan dalam bentuk pembatasan batas maksimum pembelian obat keras/terbatas per transaksi untuk mencegah penimbunan, serta verifikasi hak akses kasir yang aktif).*

---

2. **Tambah kolom "jatuh tempo"** — misalnya `tanggal_jatuh_tempo DATE`
   yang otomatis diisi 14 hari setelah `tanggal_pinjam` (petunjuk: cari
   fungsi tanggal PostgreSQL seperti `tanggal_pinjam + INTERVAL '14 days'`),
   lalu tampilkan di `kembali.php` untuk membantu Petugas melihat
   transaksi mana yang sudah lewat jatuh tempo.  
   **Jawaban:**  
   1. **Modifikasi Skema Tabel di PostgreSQL:**
      Menambahkan kolom baru `tanggal_jatuh_tempo` bertipe `DATE` pada tabel:
      ```sql
      ALTER TABLE peminjaman ADD COLUMN tanggal_jatuh_tempo DATE;
      ```

   2. **Pengisian Otomatis Saat Transaksi Dibuat (`proses_tambah.php`):**
      Menggunakan ekspresi interval waktu PostgreSQL `CURRENT_DATE + INTERVAL '14 days'`:
      ```sql
      INSERT INTO peminjaman (anggota_id, buku_id, tanggal_pinjam, tanggal_jatuh_tempo, status)
      VALUES (:anggota_id, :buku_id, CURRENT_DATE, CURRENT_DATE + INTERVAL '14 days', 'dipinjam');
      ```

   3. **Penyajian Data & Indikator Keterlambatan di Antarmuka:**
      Pada file tampilan transaksi aktif (`kembali.php` / `batal.php`), tambahkan penanda visual badge merah jika transaksi sudah melewati tanggal jatuh tempo:
      ```php
      $isTerlambat = strtotime(date('Y-m-d')) > strtotime($trx['tanggal_jatuh_tempo']);
      ?>
      <td>
          <?= date('d M Y', strtotime($trx['tanggal_jatuh_tempo'])); ?>
          <?php if ($isTerlambat): ?>
              <span class="stock-empty" style="font-size: 11px; padding: 2px 6px;">Lewat Jatuh Tempo</span>
          <?php endif; ?>
      </td>
      ```

---

3. **Uji race condition secara manual** — buka dua tab browser
   berbeda (atau dua sesi terpisah), coba pinjamkan buku dengan stok
   tersisa 1 dari **kedua** tab hampir bersamaan. Amati hanya **satu**
   yang berhasil, yang lain mendapat pesan "Stok buku tidak tersedia."
   (bukti `FOR UPDATE` dari [bab 3 §3.5](03-peminjaman-baru-dan-transaction.md#35-mencegah-race-condition-select--for-update)
   bekerja).  
   **Jawaban:**  
   1. **Skenario Langkah Pengujian:**
      - **Persiapan Data:** Atur salah satu item (obat/buku) pada database hingga memiliki sisa stok tepat **1 item**.
      - **Buka Dua Sesi Terpisah:** Buka Browser Utama (misal Google Chrome) dan Tab Incognito / Browser Kedua (misal Microsoft Edge) dengan login akun petugas berbeda (`Thufail` dan `Ibni`).
      - **Buka Form Transaksi:** Pada kedua jendela browser, buka halaman transaksi baru (`penjualan/tambah.php`), pilih item obat/buku yang tersisa stok 1, dan isikan jumlah pemesanan 1 item.
      - **Eksekusi Serentak:** Tekan tombol **Simpan Transaksi** pada kedua browser dalam waktu yang hampir bersamaan (selisih sepersekian detik).

   2. **Hasil Pengamatan & Analisis Mekanisme `FOR UPDATE`:**
      - **Tab Pertama (Request Tiba Lebih Dulu):** Berhasil memproses transaksi, stok terpotong dari 1 menjadi 0, dan sistem menampilkan flash message sukses: *"Transaksi berhasil disimpan"*.
      - **Tab Kedua (Request Menunggu Antrean):** Tertahan sesaat (*waiting lock*), lalu transaksi digagalkan dan menampilkan flash message error: *"Stok obat tidak mencukupi. Sisa stok: 0"*.

   3. **Penjelasan Teknis:**
      Klausa `SELECT ... FOR UPDATE` memberikan penguncian baris eksklusif (*row-level exclusive lock*) pada PostgreSQL. Sesi kedua dipaksa menunggu hingga sesi pertama mengeksekusi `COMMIT` atau `ROLLBACK`. Begitu sesi pertama selesai memotong stok menjadi 0 dan melakukan `COMMIT`, sesi kedua baru membaca data stok terbaru (yang sudah 0), sehingga kondisi `if ($jumlah > $stok)` langsung terpenuhi dan mencegah stok bernilai negatif.

---

4. **Tambah `JOIN` tambahan di `kembali.php`/`riwayat.php`** — misalnya
   sertakan juga kolom `no_hp` anggota (dari tabel `anggota`) di daftar
   transaksi aktif, sebagai latihan menambah kolom ke query `JOIN` yang
   sudah ada.  
   **Jawaban:**  
   1. **Modifikasi Query Multi-Table JOIN:**
      Untuk memperkaya informasi transaksi pada `riwayat.php` / `kembali.php`, kita dapat menambahkan tabel relasi lain ke dalam query SQL. Misalnya pada SIAFARMA, menambahkan relasi ke tabel `kategori` dan `supplier` obat:
      ```sql
      SELECT 
          p.id AS invoice_id,
          p.tanggal,
          p.total,
          p.status,
          u.nama AS nama_kasir,
          o.nama_obat,
          k.nama_kategori,
          s.nama_supplier,
          dp.jumlah,
          dp.harga,
          dp.subtotal
      FROM penjualan p
      JOIN detail_penjualan dp ON dp.penjualan_id = p.id
      JOIN obat o ON dp.obat_id = o.id
      LEFT JOIN kategori k ON o.kategori_id = k.id
      LEFT JOIN supplier s ON o.supplier_id = s.id
      LEFT JOIN users u ON p.user_id = u.id
      ORDER BY p.id DESC;
      ```

   2. **Penyesuaian Antarmuka Tabel HTML:**
      Menambahkan kolom baru pada bagian `<thead>` dan baris data `<tbody>`:
      ```html
      <!-- Header Tabel -->
      <th>KATEGORI</th>
      <th>SUPPLIER</th>

      <!-- Data Tabel -->
      <td><span class="count-badge"><?= e($item['nama_kategori'] ?? '-'); ?></span></td>
      <td><?= e($item['nama_supplier'] ?? '-'); ?></td>
      ```
      *Penggunaan `LEFT JOIN` memastikan data transaksi tetap tampil dengan baik meskipun data kategori atau supplier dari obat tersebut belum terisi atau telah dihapus (`NULL`).*
