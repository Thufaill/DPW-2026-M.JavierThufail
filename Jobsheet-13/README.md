## Ide Latihan Tambahan (Opsional)

1. **Tambah `.env.example`** — buat file baru bernama `.env.example`
   berisi daftar nama environment variable yang dibutuhkan (`DB_HOST`,
   `DB_PORT`, dst.) **tanpa** nilai sungguhan, sebagai contoh/template
   untuk siapa pun yang men-deploy aplikasi ini — pola yang sangat
   umum dipakai di proyek sungguhan (cari tahu sendiri lewat referensi
   umum tentang file `.env` di proyek web).  
   **Jawaban:**  
   Penyediaan berkas `.env.example` adalah praktik standar industri (_best practice_) dalam rekayasa perangkat lunak web. Berkas ini berfungsi sebagai **cetak biru konfigurasi** tanpa memuat kredensial rahasia, sehingga aman untuk di-commit ke Git dan dijadikan acuan oleh developer lain maupun pipeline deployment cloud (Vercel/CI-CD).

   Berikut adalah isi berkas `.env.example` yang disiapkan untuk aplikasi:

   ```bash
   # ==============================================================================
   # TEMPLATE KONFIGURASI DATABASE POSTGRESQL (SUPABASE / LOKAL)
   # Salin file ini menjadi ".env" lalu isi dengan kredensial database Anda.
   # ==============================================================================

   # Opsi 1: Supabase Connection Pooler (Sangat Direkomendasikan untuk Jaringan IPv4)
   DB_HOST=aws-0-ap-southeast-1.pooler.supabase.com
   DB_PORT=5432
   DB_NAME=postgres
   DB_USER=postgres.xxxxxxxxxxxxxxxxxxxx
   DB_PASSWORD=masukkan_password_database_disini
   DB_SSLMODE=require

   # Opsi 2: Direct Connection (Memerlukan Jaringan yang Mendukung IPv6)
   # DB_HOST=db.xxxxxxxxxxxxxxxxxxxx.supabase.co
   # DB_PORT=5432
   # DB_NAME=postgres
   # DB_USER=postgres
   # DB_PASSWORD=masukkan_password_database_disini
   # DB_SSLMODE=require
   ```

---

2. **Lengkapi validasi bisnis yang masih tertunda** — ingat dari
   [dokumentasi jobsheet-12],
   aturan "anggota terlambat >14 hari tidak boleh meminjam" masih
   belum diterapkan — coba selesaikan sebagai latihan akhir yang
   menggabungkan hampir semua konsep yang sudah kamu pelajari (query
   SQL, transaction, validasi, flash message).  
   **Jawaban:**  
   Validasi bisnis ini mengintegrasikan seluruh pilar backend yang telah dipelajari: query PostgreSQL, eksekusi dalam Database Transaction ACID, validasi kondisi bersyarat, dan pengiriman pesan umpan balik (_Flash Message_).
   1. **Logika Query PostgreSQL:**
      Menghitung jumlah transaksi pinjaman aktif milik anggota yang telah melewati batas 14 hari:

      ```sql
      SELECT COUNT(*)
      FROM peminjaman
      WHERE anggota_id = :anggota_id
        AND status = 'dipinjam'
        AND (CURRENT_DATE - tanggal_pinjam::date) > 14;
      ```

   2. **Penerapan pada Skrip Transaksi (`proses_tambah.php`):**
      Diletakkan di dalam blok transaksi sebelum perintah `INSERT` dijalankan:

      ```php
      try {
          $pdo->beginTransaction();

          // Validasi Aturan Bisnis: Cek apakah anggota memiliki tunggakan > 14 hari
          $stmtCek = $pdo->prepare("
              SELECT COUNT(*)
              FROM peminjaman
              WHERE anggota_id = :anggota_id
                AND status = 'dipinjam'
                AND (CURRENT_DATE - tanggal_pinjam::date) > 14
          ");
          $stmtCek->execute(['anggota_id' => $anggotaId]);

          if ((int)$stmtCek->fetchColumn() > 0) {
              throw new Exception('Transaksi ditolak: Anggota masih memiliki pinjaman aktif yang terlambat lebih dari 14 hari.');
          }

          // Lanjutkan proses insert transaksi dan commit...
          $pdo->commit();

      } catch (Exception $e) {
          if ($pdo->inTransaction()) {
              $pdo->rollBack();
          }
          $_SESSION['flash'] = ['type' => 'error', 'pesan' => $e->getMessage()];
          header('Location: tambah.php');
          exit;
      }
      ```

      _(Pada studi kasus apotek **SIAFARMA**, validasi bisnis sejenis diterapkan pada pembatasan kuota maksimal pembelian obat keras/terbatas per transaksi untuk mencegah penyalahgunaan obat)._

---

3. **Sisipkan screenshot sungguhan** ke `manual-pengguna.md` — jalankan
   aplikasi di lab dengan PostgreSQL aktif, ambil tangkapan layar
   nyata di setiap langkah bertanda `[Screenshot]`, lengkapi dokumen
   itu sesuai catatannya sendiri.  
   **Jawaban:**  
   Penyisipan tangkapan layar nyata (_real screenshots_) pada manual pengguna sangat krusial untuk memberikan panduan visual yang jelas kepada petugas/kasir apotek.
   1. **Struktur Penyimpanan Aset Gambar:**
      Tangkapan layar disimpan di dalam folder gambar proyek (misal: `docs/img/` atau `img/`):

      ```
      Jobsheet-13/
      ├── docs/
      │   ├── img/
      │   │   ├── 01_dashboard.png
      │   │   ├── 02_katalog_obat.png
      │   │   ├── 03_transaksi_kasir.png
      │   │   ├── 04_retur_pembatalan.png
      │   │   └── 05_riwayat_penjualan.png
      │   └── manual-pengguna.md
      ```

   2. **Format Penulisan Markdown di `manual-pengguna.md`:**

      ```markdown
      ### 2.1 Alur Transaksi Kasir Baru

      1. Buka menu **Transaksi Baru**, pilih obat dan tentukan jumlah beli.
         ![Form Transaksi Kasir](img/03_transaksi_kasir.png)

      ### 2.2 Alur Pembatalan / Retur Obat

      1. Masuk ke menu **Retur/Batal**, cari nomor invoice, lalu klik tombol **Batalkan Transaksi**.
         ![Antarmuka Pembatalan Transaksi](img/04_retur_pembatalan.png)
      ```

      _Gambar-gambar tersebut memvalidasi bahwa sistem telah teruji dan berjalan normal pada server lokal dan cloud database Supabase._

---

4. **Tulis "lessons learned"-mu sendiri** — coba tulis 1 halaman
   ringkasan (terpisah dari dokumentasi ini) tentang konsep pemrograman
   web apa yang menurutmu paling menantang dari seluruh 13 jobsheet
   ini, dan kenapa — latihan reflektif yang bagus sebelum presentasi
   UAS.  
   **Jawaban:**

   ### Refleksi Pembelajaran (Lessons Learned): Perjalanan Jobsheet 01 s.d. 13

   Sepanjang 13 jobsheet mata kuliah Desain dan Pemrograman Web (DPW), aplikasi dikembangkan secara inkremental dari sekadar dokumen HTML statis hingga menjadi aplikasi apotek **SIAFARMA** yang utuh dan aman. Dari seluruh proses tersebut, tiga konsep berikut merupakan yang **paling menantang dan berharga**:
   1. **Pencegahan Race Condition & Transaksi ACID (Jobsheet 12):**
      - **Tantangan:** Memahami bahwa aplikasi web diakses oleh banyak pengguna (kasir) secara bersamaan (_concurrent_). Jika dua kasir memproses obat yang tersisa 1 secara serentak, tanpa penanganan khusus stok bisa bernilai negatif (-1).
      - **Pelajaran yang Didapat:** Menguasai mekanisme `beginTransaction`, `commit`, dan `rollBack`, serta penggunaan klausa `SELECT ... FOR UPDATE` untuk mengunci baris stok obat secara atomik. Ini mengubah pola pikir saya dari sekadar "membuat program yang jalan" menjadi "membangun sistem yang tahan konkurensi".

   2. **Penguatan Keamanan Web Berlapis (Jobsheet 10 & 11):**
      - **Tantangan:** Mengamankan web dari berbagai vektor serangan umum (OWASP Top 10) seperti SQL Injection, XSS (_Cross-Site Scripting_), CSRF (_Cross-Site Request Forgery_), Session Fixation, dan Brute Force.
      - **Pelajaran yang Didapat:** Keamanan bukan fitur opsional, melainkan fondasi. Penggunaan wrapper sanitasi `e()`, tokenisasi CSRF dinamis per form `POST`, pengacakan ID sesi via `session_regenerate_id()`, serta header CSP (_Content Security Policy_) memberikan pemahaman mendalam tentang konsep _defense-in-depth_.

   3. **Pemisahan Konfigurasi Lingkungan & Cloud Database Supabase (Jobsheet 13):**
      - **Tantangan:** Menghubungkan PHP native lokal dengan cloud database modern (Supabase PostgreSQL) yang memerlukan penanganan jaringan IPv4/IPv6 melalui Connection Pooler dan koneksi SSL terenkripsi, serta memastikan password tidak bocor ke Git.
      - **Pelajaran yang Didapat:** Menerapkan prinsip _Twelve-Factor App_ dengan memisahkan konfigurasi database ke dalam variabel lingkungan (`.env` dan `getenv()`). Hal ini membuat kode bersifat bersih, portabel, dan siap dirilis ke platform cloud hosting seperti Vercel tanpa perlu mengubah baris kode program.
