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

# Laporan Praktikum Jobsheet 09: CRUD Penuh, Pencarian, & Pagination

## 1. Deskripsi Tugas
Pada Jobsheet 09, fokus pembelajaran adalah menyempurnakan operasi CRUD (*Create, Read, Update, Delete*) pada sistem agar lebih aman, skalabel, dan ramah pengguna. 

Untuk studi kasus **Sistem Informasi Apotek (SIAFARMA)**, saya telah menerapkan *Server-Side Pagination* (pembatasan data halaman di tingkat *database*), *Server-Side Search* (pencarian data dinamis menggunakan klausa `ILIKE`), serta mengamankan rute penghapusan data menggunakan metode `POST`. Selain itu, saya juga menyelesaikan instruksi latihan tambahan berupa pencarian multi-kolom, pengubahan batas baris data, dan konfirmasi ekstra pada proses *Update*.

## 2. Implementasi & Penjelasan Kode

### 2.1. Server-Side Pagination & Multi-Column Search (`list.php`)
Fitur pencarian dan penomoran halaman tidak lagi menggunakan filter JavaScript lokal, melainkan langsung ditangani oleh PostgreSQL. Hal ini membuat aplikasi tetap ringan meskipun data obat, kategori, atau supplier mencapai ribuan baris.

```php
<?php
// Menangkap parameter dari URL (Method GET)
$q = trim($_GET['q'] ?? '');
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));
$perHalaman = 10; // Latihan Tambahan: Mengubah jumlah baris per halaman menjadi 10

// Latihan Tambahan: Pencarian di banyak kolom menggunakan logika OR
$hitung = $pdo->prepare("
    SELECT COUNT(*) FROM kategori 
    WHERE nama_kategori ILIKE :kw OR deskripsi ILIKE :kw
");
$hitung->execute(['kw' => "%$q%"]);
$totalData = (int) $hitung->fetchColumn();

// Kalkulasi Pagination
$totalHalaman = max(1, (int) ceil($totalData / $perHalaman));
$halaman = min($halaman, $totalHalaman);
$offset = ($halaman - 1) * $perHalaman;

// Query pengambilan data utama
$stmt = $pdo->prepare("
    SELECT * FROM kategori
    WHERE nama_kategori ILIKE :kw OR deskripsi ILIKE :kw
    ORDER BY id DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':kw', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
?>
```
**Penjelasan Kode:**
*   `ILIKE`: Klausa pencarian bawaan PostgreSQL yang *case-insensitive* (tidak membedakan huruf besar/kecil).
*   `OR`: Diterapkan pada parameter pencarian untuk mencocokkan kata kunci ke lebih dari satu kolom (misalnya: mencari di kolom nama ATAU deskripsi).
*   `LIMIT` & `OFFSET`: Menginstruksikan database untuk memotong hasil *query*. `LIMIT 10` berarti hanya 10 baris yang diambil, dan `OFFSET` menentukan dari baris ke berapa pengambilan data dimulai (dihitung berdasarkan halaman aktif).
*   `bindValue()`: Digunakan secara eksplisit karena parameter `LIMIT` dan `OFFSET` wajib disuntikkan ke query sebagai integer (`PDO::PARAM_INT`), bukan string.

### 2.2. Pengamanan Metode Delete (`hapus.php`)
Sesuai instruksi Jobsheet, fitur hapus data yang sebelumnya menggunakan URL GET (sangat rentan tereksekusi tanpa sengaja) telah diubah sepenuhnya menggunakan form tersembunyi dengan metode POST.

```php
<?php
// 1. Validasi metode pengiriman (hanya menerima POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit('Akses ditolak. Metode tidak diizinkan.');
}

// 2. Filter input ID dari POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID tidak valid.'];
    header('Location: list.php');
    exit;
}

// 3. Eksekusi Hapus dengan Try-Catch untuk mengatasi relasi Foreign Key
try {
    $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data berhasil dihapus.'];
} catch (PDOException $e) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Data tidak dapat dihapus karena sedang berelasi.'];
}
?>
```
**Implementasi UI Hapus (`list.php`):**
```html
<!-- Tombol hapus bukan lagi tag <a>, melainkan form HTML utuh -->
<form class="form-hapus-inline" method="post" action="hapus.php" style="display: inline-block;">
    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
    <button type="submit" class="btn-action delete">Hapus</button>
</form>
```
**Penjelasan Kode:**
*   `$_SERVER['REQUEST_METHOD'] !== 'POST'`: Pencegahan akses langsung via *address bar*. Sistem akan melempar kode status HTTP 405.
*   `filter_input(..., FILTER_VALIDATE_INT)`: Memastikan nilai ID yang ditangkap benar-benar berupa angka bilangan bulat (mencegah SQL Injection tipe union).
*   Blok `catch (PDOException $e)`: Jika pengguna mencoba menghapus Kategori/Supplier yang masih terhubung dengan data Obat, database akan menolak (*Foreign Key Constraint*), dan aplikasi akan menangkap error tersebut menjadi *flash message* tanpa merusak halaman.

### 2.3. Latihan Tambahan: Konfirmasi Ekstra pada Update (`app.js`)
Mengingat aplikasi SIAFARMA berkaitan dengan stok dan harga, proses pengubahan (Edit) memerlukan kehati-hatian. Saya menambahkan validasi Javascript berbasis *event* `submit` sebelum form edit dikirim ke server.

```javascript
function initEditConfirm() {
    const editForms = document.querySelectorAll('form[action="proses_edit.php"]');
    
    editForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const yakin = confirm("Apakah kamu yakin ingin menyimpan perubahan data ini?");
            if (!yakin) {
                e.preventDefault(); // Membatalkan submit jika user memilih Cancel
            }
        });
    });
}
```

## 3. Kesimpulan
Implementasi Jobsheet 09 berhasil meningkatkan stabilitas dan keamanan SIAFARMA. Pencarian berbasis *database* dengan `ILIKE` digabungkan dengan fungsi *Pagination* berhasil mendistribusikan beban kerja secara efisien. Selain itu, penggantian metode *Delete* ke `POST` dan penambahan *Foreign Key protection* menjadikan aplikasi tidak mudah rusak oleh interaksi pengguna yang tidak disengaja. Seluruh pola 4-file CRUD (`list.php`, `tambah.php`, `edit.php`, `hapus.php`) telah direplikasi ke entitas Kategori, Supplier, dan Obat.