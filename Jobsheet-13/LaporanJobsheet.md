|                   |                              |
| :---------------- | :--------------------------- |
| **Mata Kuliah**   | : Desain dan Pemrograman Web |
| **Program Studi** | : D4 – Teknik Informatika    |
| **Semester**      | : 3                          |

---

|                  |                     |
| :--------------- | :------------------ |
| **Kelas**        | : TI-2D             |
| **NIM**          | : 254107020019      |
| **Nama**         | : M. Javier Thufail |
| **Jobsheet Ke-** | : 13                |

---

# Laporan Praktikum Jobsheet 13: Deployment & Dokumentasi SIMPUS-Mini

## 1. Deskripsi Tugas

Pada Jobsheet 13, fokus utama pembelajaran adalah mempersiapkan aplikasi web yang telah dibangun sejak Jobsheet 01 hingga Jobsheet 12 menjadi sebuah _Final Snapshot_ yang siap dideploy ke server produksi (Cloud Hosting) dan terdokumentasi secara profesional untuk didemokan pada Ujian Akhir Semester (UAS).

Berbeda dengan praktikum sebelumnya yang berfokus pada penambahan fitur CRUD dan transaksi, Jobsheet 13 berfokus pada dua pilar utama rekayasa perangkat lunak web modern:

1. **Deployment & Pemisahan Konfigurasi Lingkungan (_Environment Variables_):** Memisahkan kredensial sensitif database dari kode sumber aplikasi (_source code_) agar tidak terekspos ke repositori publik, serta memastikan aplikasi dapat berjalan mulus di berbagai lingkungan (_development_ lokal maupun _production_ di Vercel/Cloud).
2. **Dokumentasi Proyek Menyeluruh:** Menyusun dokumentasi komprehensif yang berfungsi sebagai "kartu identitas" sistem, mencakup diagram relasi database final (ERD), matriks hak akses pengguna per peran (_role matrix_), panduan manual operasional pengguna (_user manual_), dan audit keamanan berlapis (_security checklist_).

Dalam konteks studi kasus yang saya kembangkan, yaitu **Sistem Informasi Apotek (SIAFARMA)** (sebagai pengembangan dari tema dasar SIMPUS-Mini), seluruh modul master data (obat, kategori, supplier), autentikasi petugas berkeamanan tinggi, transaksi kasir dengan database transaction ACID, dan pembatalan transaksi telah diintegrasikan secara utuh dengan cloud database **Supabase PostgreSQL** dan siap dirilis.

---

## 2. Implementasi & Penjelasan Kode

### 2.1. Pemisahan Konfigurasi Kredensial & Environment Variables (`includes/koneksi.php`)

Kredensial database tidak lagi ditulis langsung secara statis (_hardcoded_) di dalam kode PHP. Sebagai gantinya, aplikasi membaca konfigurasi dari _Environment Variables_ menggunakan fungsi bawaan `getenv()`, didukung oleh mekanisme _native .env loader_ otomatis untuk pengujian lokal.

```php
// 1. Loader Native File .env untuk Lingkungan Lokal
$envCandidates = [
    __DIR__ . '/../.env',      // Jobsheet-13/.env
    __DIR__ . '/../../.env',   // root workspace/.env
];

foreach ($envCandidates as $envFile) {
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (str_contains($line, '=')) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val, " \t\n\r\0\x0B\"'");
                if (getenv($key) === false) {
                    putenv("$key=$val");
                    $_ENV[$key] = $val;
                    $_SERVER[$key] = $val;
                }
            }
        }
        break;
    }
}

// 2. Pembacaan Variabel Lingkungan dengan Nilai Cadangan (Fallback)
$host    = getenv('DB_HOST');
$port    = getenv('DB_PORT') ?: '5432';
$db      = getenv('DB_NAME') ?: 'postgres';
$user    = getenv('DB_USER');
$pass    = getenv('DB_PASSWORD');
$sslmode = getenv('DB_SSLMODE') ?: 'require';
```

**Penjelasan:** Pemisahan ini menjamin prinsip _Twelve-Factor App_ (konfigurasi dipisahkan ketat dari kode). Ketika diunggah ke GitHub, file `.env` dicegah agar tidak terunggah melalui aturan `.gitignore`, sehingga informasi sensitif seperti kata sandi database tetap terjaga kerahasiaannya.

### 2.2. Integrasi Cloud Database PostgreSQL Supabase & Connection Pooling

Aplikasi terhubung ke database PostgreSQL yang di-host di cloud provider **Supabase**. Mengingat karakteristik jaringan lokal di Indonesia yang umumnya berbasis IPv4, koneksi dikonfigurasikan menggunakan **Supabase Connection Pooler** pada port 5432 / 6543 dengan enkripsi SSL wajib (`sslmode=require`).

```php
try {
    $dsn = "pgsql:host={$host};port={$port};dbname={$db};sslmode={$sslmode}";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT            => 10,
    ]);

} catch (PDOException $e) {
    die(
        "<div style='font-family: sans-serif; max-width: 650px; margin: 50px auto; padding: 25px; border: 1px solid #f5c2c7; background: #f8d7da; border-radius: 8px; color: #842029;'>" .
        "<h3 style='margin-top: 0;'>❌ Gagal Terhubung ke Database Supabase (PostgreSQL)</h3>" .
        "<p><strong>Pesan Error:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>" .
        "</div>"
    );
}
```

**Penjelasan:** Penggunaan PDO DSN dengan parameter `sslmode=require` memastikan seluruh jalur transmisi data antara aplikasi web dan database Supabase terenkripsi. Blok `try-catch` menangkap kegagalan koneksi dan menampilkan panel diagnostik yang ramah pengembang tanpa membocorkan kredensial rahasia ke publik (_Information Disclosure Prevention_).

### 2.3. Konfigurasi Deployment Container Berbasis FrankenPHP (`Dockerfile.vercel`)

Untuk mendukung proses deployment instan ke platform cloud hosting (seperti Vercel, Railway, atau VPS), disediakan konfigurasi kontainer Docker modern berbasis **FrankenPHP**.

```dockerfile
FROM dunglas/frankenphp:php8.3-bookworm

WORKDIR /app

RUN install-php-extensions pdo_pgsql

COPY . /app

CMD ["sh", "-c", "frankenphp php-server --listen :${PORT:-80} --root /app"]
```

**Penjelasan:** Image kontainer menggunakan runtime resmi PHP 8.3 dengan web server FrankenPHP bawaan Caddy yang sangat cepat. Skrip `install-php-extensions pdo_pgsql` secara otomatis mengompilasi driver PostgreSQL yang dibutuhkan PDO, dan variabel port lingkungan `${PORT:-80}` memungkinkan aplikasi beradaptasi dengan port dinamis yang dialokasikan oleh server cloud hosting.

### 2.4. Skema Database Relasional Utuh / Final ERD

Struktur data final SIAFARMA menghubungkan 6 entitas tabel yang saling berelasi dengan integritas referensial yang ketat:

```
kategori            supplier           users              penjualan
---------           ---------          ------             -----------
id (PK)             id (PK)            id (PK)             id (PK)
nama_kategori       nama_supplier      nama                tanggal
deskripsi           alamat             username (UQ)       total
                    no_hp              password (hash)     user_id (FK -> users.id)
                    email              role                status ('selesai'/'dibatalkan')
       │                   │
       └─────────┬─────────┘
                 ▼
                obat                               detail_penjualan
                ----                               ----------------
                id (PK)                            id (PK)
                kode_obat (UQ)                     penjualan_id (FK -> penjualan.id ON DELETE CASCADE)
                nama_obat                          obat_id (FK -> obat.id ON DELETE RESTRICT)
                kategori_id (FK -> kategori.id)    jumlah (CHECK > 0)
                supplier_id (FK -> supplier.id)    harga
                harga_beli                         subtotal
                harga_jual
                stok
                satuan
```

**Penjelasan:**

- Relasi `penjualan.user_id` ke `users.id` mencatat petugas kasir penanggung jawab transaksi.
- Relasi `detail_penjualan.obat_id` ke `obat.id` menggunakan batasan `ON DELETE RESTRICT`, menjamin data obat historis tidak dapat dihapus jika pernah terlibat dalam transaksi penjualan.
- Relasi `detail_penjualan.penjualan_id` ke `penjualan.id` menggunakan `ON DELETE CASCADE`, memastikan seluruh rincian barang otomatis terhapus jika record header penjualan dibersihkan.

### 2.5. Matriks Hak Akses Pengguna & Dokumentasi Panduan Sistem (`docs/manual-pengguna.md`)

Aplikasi membagi hak akses ke dalam dua kategori pengguna guna menjamin keamanan operasional apotek:

| Fitur / Halaman                                       | Pengunjung Publik (Tamu) | Petugas / Kasir (Login) |
| ----------------------------------------------------- | ------------------------ | ----------------------- |
| Dashboard Ringkasan & Widget Stok Menipis             | Ya (Read-only)           | Ya (Full)               |
| Katalog Obat & Pencarian Real-time (`ILIKE`)          | Ya                       | Ya                      |
| Kelola Data Master Obat (Tambah/Edit/Hapus)           | Tidak                    | Ya                      |
| Kelola Kategori & Data Supplier (CRUD)                | Tidak                    | Ya                      |
| Transaksi Kasir Baru & Penguncian Stok (`FOR UPDATE`) | Tidak                    | Ya                      |
| Retur / Pembatalan Penjualan & Pengembalian Stok      | Tidak                    | Ya                      |
| Laporan Riwayat Penjualan Lengkap (Multi-JOIN)        | Tidak                    | Ya                      |

**Penjelasan:** Pengunjung tanpa autentikasi hanya dapat memantau katalog obat publik. Seluruh aksi manipulasi data dan eksekusi kasir dilindungi oleh middleware auth (`includes/auth.php`) yang memverifikasi sesi aktif `$_SESSION['user_id']`, token anti-CSRF (`csrf_verify()`), serta sanitasi XSS (`e()`). Panduan langkah penggunaan lengkap telah didokumentasikan di berkas `docs/manual-pengguna.md`.

---

## 3. Kesimpulan

Melalui Jobsheet 13, siklus pengembangan Sistem Informasi Apotek (SIAFARMA) telah mencapai tahap akhir (_Final Snapshot_) yang matang. Pemisahan konfigurasi lingkungan menggunakan _Environment Variables_ dan native `.env loader` berhasil membebaskan kode sumber dari keterikatan kredensial statis, sehingga aplikasi portabel dan aman saat dideploy ke cloud Supabase maupun Vercel.

Kelengkapan dokumentasi yang mencakup ERD final, matriks fitur peran, manual operasional pengguna, serta rekapitulasi keamanan memastikan sistem transparan, mudah dipelihara (_maintainable_), dan siap dipresentasikan secara meyakinkan pada Ujian Akhir Semester (UAS).
