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

# Laporan Praktikum Jobsheet 08: Koneksi PostgreSQL

## 1. Deskripsi Tugas & Pemilihan Tema
Fokus utama pada Jobsheet 08 adalah mengimplementasikan koneksi antara aplikasi web berbasis PHP dan database PostgreSQL menggunakan ekstensi PDO (*PHP Data Objects*). 

Sesuai dengan instruksi praktikum untuk merombak dan mengubah konten web bawaan menjadi studi kasus pilihan, saya memilih untuk membangun **Sistem Informasi Apotek (SIAFARMA)**. Aplikasi ini mendigitalisasi pengelolaan inventaris obat, pencatatan kategori, data supplier, dan riwayat penjualan. Seluruh kerangka antarmuka (UI) telah disesuaikan dengan identitas visual apotek.

## 2. Implementasi & Penjelasan Kode

### 2.1. Konfigurasi Koneksi Database (`includes/koneksi.php`)
File ini merupakan jembatan utama yang menghubungkan aplikasi SIAFARMA dengan database PostgreSQL. Pendekatan yang digunakan adalah memanfaatkan *Environment Variables* untuk menjaga keamanan kredensial.

```php
<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'postgres';
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

try {
    // Membuka koneksi menggunakan driver pgsql
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$db;sslmode=require",
        $user,
        $pass
    );
    
    // Mengatur mode error handling menjadi Exception
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $e) {
    // Menghentikan eksekusi dan menampilkan pesan jika koneksi gagal
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
```

**Penjelasan Kode:**
*   `getenv()`: Mengambil variabel lingkungan seperti host, nama database, pengguna, dan kata sandi. Praktik ini sangat aman untuk proses *deployment* (misalnya ke Vercel/Railway) karena *password* tidak ditulis langsung (di-*hardcode*) di dalam *source code*.
*   `new PDO("pgsql:...")`: Membuat *instance* (objek) koneksi baru. *String* pertama adalah *Data Source Name* (DSN) yang secara spesifik memanggil driver `pgsql` untuk PostgreSQL, dilengkapi konfigurasi *port* dan pengaturan `sslmode=require`.
*   `setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)`: Menginstruksikan PDO untuk melemparkan pengecualian (*exception*) setiap kali terjadi kesalahan query SQL, sehingga pelacakan *bug* (*debugging*) menjadi jauh lebih mudah.
*   `try...catch`: Struktur kontrol untuk menangkap kegagalan. Jika database mati atau kredensial salah, script akan melompat ke blok `catch` dan mengeksekusi `die()` untuk menghentikan halaman.

### 2.2. Penggunaan Koneksi pada Aplikasi (`index.php`)
Setelah koneksi terbentuk, objek `$pdo` dipanggil menggunakan `require_once` dan digunakan untuk mengambil data dari PostgreSQL untuk ditampilkan di halaman Dashboard SIAFARMA.

```php
<?php
$page_title = "Dashboard";
require_once __DIR__ . '/includes/koneksi.php';
include __DIR__ . '/includes/header.php';

// Menghitung total data menggunakan fetchColumn()
$totalObat = $pdo->query("SELECT COUNT(*) FROM obat")->fetchColumn();

// Mengambil data spesifik dengan klausa ORDER BY dan LIMIT
$stokRendah = $pdo->query("SELECT nama_obat, stok, satuan FROM obat ORDER BY stok ASC LIMIT 5")->fetchAll();
?>
```

**Penjelasan Kode:**
*   `require_once __DIR__ . '/includes/koneksi.php'`: Memastikan file koneksi dimuat sebelum query dijalankan. `__DIR__` memastikan pencarian rute file selalu akurat meskipun dipanggil dari folder yang berbeda.
*   `$pdo->query(...)`: Menjalankan instruksi SQL langsung ke database.
*   `fetchColumn()`: Mengambil hanya satu nilai kolom dari hasil *query*. Sangat efisien untuk *query* agregat seperti `COUNT(*)` yang bertujuan mencari total baris.
*   `fetchAll()`: Mengambil seluruh baris hasil *query* (dalam bentuk *array* asosiatif multidiimensi) agar dapat diulang (di-*looping*) menggunakan `foreach` di antarmuka HTML untuk menampilkan daftar obat yang stoknya menipis.

## 3. Kesimpulan
Melalui Jobsheet 08, aplikasi SIAFARMA telah berhasil terhubung dengan PostgreSQL. Penggunaan PDO memberikan fleksibilitas keamanan tingkat lanjut (seperti *exception handling*) dan kemampuan untuk mengambil data dinamis yang siap dikembangkan lebih jauh menjadi sistem CRUD penuh.