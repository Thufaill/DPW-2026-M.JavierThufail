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
| **Jobsheet Ke-** | : 2 |

# LAPORAN JOBSHEET
## Penjelasan Kode PHP

Laporan ini berisi penjelasan teknis mengenai struktur kode PHP pada aplikasi SIMPUS-Mini, mencakup antarmuka form, validasi server-side, manajemen session, serta penerapan konsep pemrosesan modular.

---

## 1. Halaman Tambah Anggota (anggota/tambah.php)

### Deskripsi Ringkas
Halaman ini berfungsi sebagai antarmuka (interface) bagi pengguna untuk menginputkan data anggota baru. Halaman ini memanfaatkan komponen layout modular (header.php dan footer.php), menampilkan nilai input sebelumnya (old input), serta menyajikan pesan kesalahan (error message) secara spesifik di bawah masing-masing kolom input.

### Penjelasan Kode Utama

1. **Inisialisasi Session & Modul Header**
   - **session_start()**: Mengaktifkan mekanisme sesi PHP agar halaman dapat mengakses data error, data input lama, serta pesan flash yang dikirimkan dari skrip pemrosesan.
   - **include __DIR__ . '/../includes/header.php'**: Memanggil file header secara modular agar komponen navbar dan konfigurasi `<head>` konsisten di setiap halaman.
   - **$errors =$_SESSION['errors'] ?? []**: Mengambil pesan kesalahan spesifik per kolom input menggunakan operator null coalescing (`??`).
   - **$old =$_SESSION['old_input'] ?? []**: Memulihkan isi teks yang sebelumnya pernah dimasukkan pengguna agar form tidak dikosongkan secara otomatis saat terjadi kesalahan validasi.
   - **unset(...)**: Menghapus data session sementara (flash data) setelah dipindahkan ke variabel lokal, sehingga pesan error tidak akan terus-menerus muncul saat halaman diperbarui (refresh).

2. **Atribut Form & Keamanan Input**
   - **method="post" & action="proses_tambah.php"**: Mengirimkan data inputan pengguna ke file `proses_tambah.php` dengan metode HTTP POST demi keamanan data.
   - **htmlspecialchars(...)**: Mencegah celah keamanan Cross-Site Scripting (XSS) dengan mengonversi karakter khusus HTML menjadi entitas aman saat mencetak kembali nilai input lama.
   - **isset($errors['no_hp'])**: Struktur pengondisian untuk mengecek ketersediaan pesan kesalahan pada field terkait. Jika ditemukan, pesan error dicetak di dalam elemen `<span class="error">` tepat di bawah kotak inputnya.

---

## 2. Pemrosesan & Validasi Data (anggota/proses_tambah.php)

### Deskripsi Ringkas
Skrip ini merupakan logika backend yang mengeksekusi proses sanitasi data, validasi format angka dan teks, serta penanganan alur penyimpanan data anggota ke dalam memori session.

### Penjelasan Kode Utama

1. **Sanitasi Data Input**
   - **trim()**: Memotong spasi kosong di awal dan akhir teks inputan untuk memastikan data yang diproses tidak berupa spasi kosong semata.

2. **Validasi Teks & Format RegEx**
   - **preg_match('/^[A-Za-z0-9\-]+$/',$noAnggota)**: Menggunakan Ekspresi Reguler (Regular Expression) untuk memastikan format kode Nomor Anggota hanya memuat karakter alfanumerik dan tanda hubung.
   - **ctype_digit($umur)**: Memeriksa apakah seluruh karakter pada string merupakan angka bulat positif.
   - **preg_match('/^[0-9]{9,15}$/',$noHp)**: Memvalidasi Nomor HP agar khusus menerima inputan digit angka (0-9) dengan rentang panjang antara 9 hingga 15 digit.

3. **Penanganan Error & Redirection**
   - **Penanganan Error**: Apabila array `$errors` terisi, data kesalahan dan isi form diisikan ke `$_SESSION`, lalu pengguna diarahkan kembali (redirect) ke `tambah.php` via `header('Location: ...')`.
   - **Penyimpanan Data**: Jika seluruh aturan validasi terpenuhi, data baru ditambahkan ke array multidimensi `$_SESSION['anggota']`.
   - **exit**: Menghentikan eksekusi skrip secara penuh agar baris kode setelah redirect tidak ikut dijalankan.

---

## 3. Halaman Daftar Anggota (anggota/list.php)

### Deskripsi Ringkas
Halaman ini menyajikan seluruh data anggota yang telah berhasil tersimpan di dalam session ke dalam bentuk tabel HTML dinamis.

### Penjelasan Kode Utama

1. **Pembacaan Session Anggota**
   - Membaca array `$_SESSION['anggota']`. Jika belum ada data yang dimasukkan, variabel `$daftarAnggota` diinisialisasi sebagai array kosong `[]`.

2. **Pencetakan Tabel Dinamis**
   - **empty($daftarAnggota)**: Kondisi untuk mendeteksi apakah data anggota masih kosong atau sudah terisi.
   - **foreach ($daftarAnggota as$anggota)**: Melakukan iterasi perulangan (looping) pada array data anggota untuk mencetak baris tabel (`<tr>`) secara otomatis.
   - **htmlspecialchars(...)**: Menjamin keamanan data saat ditampilkan ke dalam tabel HTML.

---

## 4. Komponen Layout Modular (Header & Footer)

### A. Header (includes/header.php)
- **Dynamic Path Resolution ($base)**: Perhitungan tautan (path) relatif secara otomatis agar direktori assets (CSS/JS) dan menu navigasi tetap dapat diakses dengan tepat dari tingkat kedalaman folder manapun.

### B. Footer (includes/footer.php)
- Berfungsi menutup struktur tag `<main>` dan `<body>`, menampilkan teks hak cipta pada bagian footer, serta menyambungkan berkas JavaScript utama (`app.js`).