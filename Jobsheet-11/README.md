## Ide Latihan Tambahan (Opsional)

1. **Tambah proteksi CSRF ke form pencarian** — form `method="get"` di
   `buku/list.php`
   [dokumentasi jobsheet-09 §5.8] **sengaja tidak** diberi token CSRF — diskusikan sendiri kenapa: apa
   bedanya risiko form `GET` (yang hanya membaca data) dengan form
   `POST` (yang mengubah data) dalam konteks serangan CSRF?  
   **Jawaban:**  
    1. Perbedaan Risiko Method GET vs POST:
    
    - Method POST (Data Modification): Digunakan untuk operasi yang mengubah state database (Create, Update, Delete). Jika tidak dilindungi CSRF, penyerang bisa secara tersembunyi memicu server menghapus data atau menambah transaksi tanpa persetujuan user.
    
    - Method GET (Read Only / Idempotent): Form pencarian berbasis GET hanya bersifat membaca/menampilkan data (safe method). Mengirimkan request GET tidak mengubah state database, sehingga tidak membahayakan integritas data sistem.
    
    2. Masalah Kepraktisan UX:
    
    - Parameter URL pada GET dirancang agar dapat di-bookmark, dibagikan (shareable link), atau di-refresh dengan mudah oleh pengguna (misal: list.php?q=paracetamol&halaman=1).
    
    - Jika diberi token CSRF yang dinamis per-sesi, link hasil pencarian tersebut tidak akan bisa dibuka kembali di masa mendatang atau di-share ke petugas lain karena tokennya sudah tidak valid/kedaluwarsa.  

2. **Tambah baris baru ke `security-checklist.md`** — audit satu
   bagian aplikasi yang belum eksplisit disebutkan (misalnya:
   "Apakah pesan error PHP mentah pernah bocor ke pengguna, membocorkan
   detail struktur database/server?"), lengkap dengan kolom Sebelum/
   Sesudah seperti baris-baris lainnya.    
   **Jawaban:** 
   | # | Kerentanan | Ditemukan di | Sebelum | Sesudah (Perbaikan) | 
   |---|---|---|---|---|
   | 6 | **Pesan Error / Information Disclosure** | `includes/koneksi.php` & blok `try-catch` | Potensi pesan fatal error database PostgreSQL mentah bocor ke pengguna saat database mati | **Ditangani:** Pesan `PDOException` ditangkap oleh `try-catch` dan disajikan kembali sebagai pesan *Flash Message* yang bersih tanpa mengekspos kredensial server. |
   | 7 | **Content Security Policy (CSP)** | `includes/header.php` | Tidak ada pembatasan eksekusi sumber daya browser | **Ditambahkan Header HTTP:** `header("Content-Security-Policy: default-src 'self'...")` untuk mencegah browser memuat skrip berbahaya pihak ketiga secara terselubung. |


3. **Terapkan `e()` di halaman yang belum diperiksa** — telusuri
   sendiri apakah ada tempat lain di aplikasi (di luar yang disebutkan
   di [README.md](../README.md)) yang mencetak data dari database/
   `$_GET`/`$_POST` tanpa dibungkus `e()`.  
   **Jawaban:**  
   ```bash
      <script src="<?php echo e($base ?? ''); ?>assets/js/app.js"></script>
      <?php if (!empty($extra_scripts)): ?>
          <?php foreach ($extra_scripts as $src): ?>
              <script src="<?php echo e($src); ?>"></script>
          <?php endforeach; ?>
      <?php endif; ?>
   ```
4. **Pelajari `Content-Security-Policy` (CSP)** — cari tahu lewat
   dokumentasi web resmi bagaimana header HTTP ini bisa menjadi
   **lapisan pertahanan tambahan** terhadap XSS, bahkan seandainya ada
   satu tempat yang lolos dari `e()` tanpa sengaja.  
   **Jawaban:**
   ```bash
      // LAPISAN KEAMANAN TAMBAHAN: Content Security Policy (CSP) Header
      header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
   ```
   