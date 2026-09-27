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
| **Jobsheet Ke-** | : 8 |

---

# Laporan Praktikum Jobsheet 10: Autentikasi & Manajemen Sesi

## 1. Deskripsi Tugas
Pada Jobsheet 10, fokus pembelajaran adalah mengimplementasikan sistem keamanan berlapis melalui Autentikasi (Login/Register) dan Manajemen Sesi berbasis peran. 

Dalam pengembangan **Sistem Informasi Apotek (SIAFARMA)**, saya telah menambahkan tabel `users` untuk menyimpan data petugas/admin. Selain memenuhi instruksi dasar, saya juga mengembangkan struktur *database* lebih jauh dengan menambahkan relasi (*Foreign Key*) antara tabel `penjualan` dan `users`. Hal ini memungkinkan sistem untuk melacak petugas (kasir) mana yang bertanggung jawab atas setiap transaksi.

## 2. Implementasi & Penjelasan Kode

### 2.1. Penambahan Tabel Users & Relasi di Database (Supabase)
Sebelum membuat antarmuka, struktur data disiapkan terlebih dahulu. Tabel `users` dibuat untuk menampung nama, *username*, *password*, dan peran (*role*).

```sql
-- Pembuatan tabel users
CREATE TABLE public.users (
  id integer NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  nama character varying NOT NULL,
  username character varying NOT NULL UNIQUE,
  password character varying NOT NULL,
  role character varying DEFAULT 'admin'::character varying,
  CONSTRAINT users_pkey PRIMARY KEY (id)
);

-- Penambahan relasi petugas (kasir) ke transaksi penjualan
ALTER TABLE public.penjualan ADD COLUMN user_id integer;
ALTER TABLE public.penjualan ADD CONSTRAINT fk_penjualan_users FOREIGN KEY (user_id) REFERENCES public.users(id);
```
**Penjelasan:** Penggunaan `UNIQUE` pada kolom `username` mencegah adanya duplikasi akun. Penambahan `user_id` pada tabel `penjualan` memastikan integritas data transaksi agar selalu terhubung dengan petugas yang melayaninya.

### 2.2. Registrasi Akun & Enkripsi Kata Sandi (`auth/proses_register.php`)
Ketika petugas baru didaftarkan, kata sandi (password) tidak disimpan dalam bentuk teks biasa (plain text), melainkan dienkripsi menggunakan fungsi bawaan PHP.

```php
// Cek username duplikat
$cek = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$cek->execute(['username' => $username]);
if ($cek->fetch()) {
    // Redirect dengan error
}

// Enkripsi password
$hashPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (nama, username, password) VALUES (:nama, :username, :password)");
$stmt->execute(['nama' => $nama, 'username' => $username, 'password' => $hashPassword]);
```
**Penjelasan:** Fungsi `password_hash()` menggunakan algoritma enkripsi (secara *default* BCRYPT) yang menghasilkan string acak searah (hash). Bahkan jika *database* diretas, kata sandi asli pengguna tetap aman dan tidak bisa dibaca.

### 2.3. Verifikasi Login & Pembuatan Sesi (`auth/proses_login.php`)
Proses pencocokan kata sandi dilakukan secara aman tanpa perlu melakukan dekripsi pada hash.

```php
$stmt = $pdo->prepare("SELECT id, nama, password, role FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

// Validasi hash menggunakan password_verify
if ($user && password_verify($password, $user['password'])) {
    // Inisiasi data sesi
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nama'] = $user['nama'];
    $_SESSION['user_role'] = $user['role'];
    
    header('Location: ../index.php');
}
```
**Penjelasan:** `password_verify()` membandingkan *password* mentah yang diinputkan di formulir dengan *hash* yang tersimpan di *database*. Jika cocok, variabel superglobal `$_SESSION` diisi dengan data pengguna sebagai tanda bahwa mereka telah memiliki akses sah.

### 2.4. Penjaga Sesi / Guard Clause (`includes/auth.php`)
File ini berfungsi sebagai gerbang keamanan (middleware) yang dipanggil di baris paling pertama pada setiap halaman yang bersifat rahasia (seperti modul Tambah, Edit, Hapus, dan Penjualan).

```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Silakan login terlebih dahulu.'];
    header('Location: ../auth/login.php');
    exit;
}
?>
```
**Penjelasan:** Jika `$_SESSION['user_id']` tidak ditemukan (berarti pengguna belum login atau sesi telah berakhir), sistem akan segera menghentikan eksekusi script dengan `exit` dan melakukan `redirect` paksa kembali ke halaman login.

### 2.5. Implementasi ID Petugas pada Transaksi (`penjualan/proses_tambah.php`)
Berkat relasi yang ditambahkan di langkah 2.1, sekarang aplikasi SIAFARMA menyisipkan data petugas secara otomatis setiap kali ada transaksi penjualan obat baru.

```php
// Mengambil ID petugas dari session (Jobsheet 10)
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    INSERT INTO penjualan (total, user_id)
    VALUES (:total, :user_id)
    RETURNING id
");
$stmt->execute([
    'total' => $subtotal,
    'user_id' => $userId
]);
```
**Penjelasan:** Identitas pencatat transaksi tidak perlu diketik manual. Data secara instan diambil dari `$_SESSION['user_id']` yang aman dan tidak bisa dimanipulasi oleh pengguna (client).

## 3. Kesimpulan
Sistem Informasi Apotek (SIAFARMA) kini telah memiliki sistem autentikasi yang tangguh. Melalui penerapan `password_hash()` dan `password_verify()`, kredensial pengguna terjamin keamanannya. Penggunaan *Guard Clause* berhasil memisahkan ruang lingkup halaman publik (seperti Katalog Obat yang bisa dilihat Tamu) dan halaman administratif yang hanya boleh diakses oleh petugas. Penambahan logika *Foreign Key* pada tabel transaksi juga membuktikan bahwa integrasi antara sesi pengguna dan *database* dapat dimanfaatkan untuk menyempurnakan alur kerja sistem informasi.