# Checklist Keamanan — SIAFARMA (Jobsheet 11)

Dokumen ini berisi hasil audit keamanan menyeluruh dan mitigasi yang telah diterapkan pada aplikasi Sistem Informasi Apotek (SIAFARMA).

| # | Kerentanan | Ditemukan di | Sebelum | Sesudah (Perbaikan) |
|---|---|---|---|---|
| 1 | **SQL Injection** | Semua query di `obat/`, `kategori/`, `supplier/`, `penjualan/`, `auth/` | Menggunakan PDO Prepared Statements (`:parameter`) sejak Jobsheet 8 | **Diaudit Ulang (Aman):** Tidak ada string SQL yang digabung langsung dengan `$_POST`/`$_GET`. Input `' OR '1'='1` pada login tidak berhasil melakukan bypass. |
| 2 | **XSS (Cross-Site Scripting)** | `obat/list.php`, `kategori/list.php`, `supplier/list.php`, `includes/header.php`, `includes/footer.php` | Output data dicetak langsung menggunakan `echo` tanpa escaping | Dibungkus fungsi `e()` (`htmlspecialchars` dengan `ENT_QUOTES`). Uji coba input judul `<script>alert(1)</script>` tampil sebagai teks biasa. |
| 3 | **CSRF (Cross-Site Request Forgery)** | Form Tambah/Edit/Hapus (Obat, Kategori, Supplier, Penjualan), Login, Register | Form `POST` tidak memiliki verifikasi token | Ditambahkan `csrf_field()` pada form dan `csrf_verify()` pada file `proses_*.php` & `hapus.php`. Akses eksternal via `curl` tanpa token menghasilkan HTTP 403. |
| 4 | **Validasi & Sanitasi Input** | All `proses_tambah.php` & `proses_edit.php` | Sudah divalidasi tipe dan nilainya sejak Jobsheet 7-9 | **Diaudit Ulang (Aman):** Ditambahkan type casting `(int)` secara eksplisit pada parameter ID hidden input form edit. |
| 5 | **Session Fixation** | `auth/proses_login.php` | ID sesi tidak diperbarui saat transisi login | `session_regenerate_id(true)` dipanggil tepat setelah `password_verify()` berhasil. |
| 6 | **Pesan Error / Information Disclosure** | `includes/koneksi.php` & blok `try-catch` | Potensi pesan fatal error database PostgreSQL mentah bocor ke pengguna saat database mati | **Ditangani:** Pesan `PDOException` ditangkap oleh `try-catch` dan disajikan kembali sebagai pesan *Flash Message* yang bersih tanpa mengekspos kredensial server. |
| 7 | **Content Security Policy (CSP)** | `includes/header.php` | Tidak ada pembatasan eksekusi sumber daya browser | **Ditambahkan Header HTTP:** `header("Content-Security-Policy: default-src 'self'...")` untuk mencegah browser memuat skrip berbahaya pihak ketiga secara terselubung. |

## Catatan Implementasi
- Guard `includes/auth.php` selalu dijalankan **sebelum** `includes/csrf.php` di halaman proses untuk memisahkan otorisasi dan validasi form.
- Form pencarian (`GET`) di `list.php` sengaja tidak memakai token CSRF karena bersifat *read-only* (tidak mengubah database) dan untuk menjaga fungsi *bookmark/share URL*.