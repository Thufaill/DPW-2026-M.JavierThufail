# Manual Pengguna - SIAFARMA (Sistem Informasi Apotek)

Dokumen ini berisi panduan operasional penggunaan aplikasi SIAFARMA untuk Petugas Kasir dan Administrator.

---

## 1. Hak Akses Sistem

| Modul / Halaman | Pengunjung Publik (Tamu) | Petugas / Admin (Login) |
|---|---|---|
| Dashboard Ringkasan & Stok Rendah | Ya | Ya |
| Katalog Obat & Pencarian Obat | Ya | Ya |
| Tambah, Edit, Hapus Data Obat | Tidak | Ya |
| Kelola Data Kategori Obat (CRUD) | Tidak | Ya |
| Kelola Data Supplier (CRUD) | Tidak | Ya |
| Transaksi Penjualan Kasir Baru | Tidak | Ya |
| Retur / Pembatalan Transaksi Penjualan | Tidak | Ya |
| Riwayat Penjualan Lengkap (Multi-JOIN) | Tidak | Ya |

---

## 2. Alur Penggunaan Aplikasi

### 2.1 Autentikasi (Login & Register)
1. **Login Petugas:**
   - Akses menu **Login** di pojok kanan atas.
   - Masukkan *username* dan *password* yang terdaftar.
   - Sistem dilengkapi proteksi brute-force: jika gagal 3 kali berturut-turut, akun akan dikunci selama 60 detik.
2. **Registrasi Akun Baru:**
   - Buka menu *Daftar Akun Baru* pada halaman login.
   - Isi nama lengkap, username unik, dan password. Password akan dienkripsi secara otomatis menggunakan algoritma BCRYPT (`password_hash`).

### 2.2 Pengelolaan Data Master (Obat, Kategori, Supplier)
1. **Tambah Obat Baru:**
   - Masuk ke menu **Katalog Obat** -> klik **+ Tambah Obat**.
   - Masukkan Kode Obat (wajib unik), Nama Obat, Kategori, Supplier, Harga Beli, Harga Jual, Stok awal, dan Satuan.
2. **Pencarian Data Obat:**
   - Gunakan bilah pencarian pada halaman katalog untuk mencari nama atau kode obat secara *real-time*.

### 2.3 Transaksi Penjualan Obat (Kasir)
1. Klik menu **Transaksi Baru**.
2. Pilih item obat dari dropdown (hanya menampilkan obat dengan stok > 0).
3. Masukkan **Jumlah Beli**. Sistem akan otomatis mengunci stok dengan `FOR UPDATE` untuk mencegah penjualan melebihi sisa stok di gudang.
4. Klik **Simpan Transaksi**.
5. Sistem akan menerbitkan nomor invoice (contoh: `INV-001`) dan stok obat langsung terpotong secara atomik.

### 2.4 Pembatalan Transaksi / Retur Obat
1. Masuk ke menu **Retur/Batal**.
2. Cari transaksi yang ingin dibatalkan berdasarkan nomor invoice atau nama kasir.
3. Klik tombol **Batalkan Transaksi**.
4. Konfirmasi dialog yang muncul. Sistem akan mengembalikan stok obat ke gudang dan memperbarui status nota menjadi *Dibatalkan*.

### 2.5 Laporan Riwayat Penjualan
- Buka menu **Riwayat** untuk melihat rekapitulasi penjualan lengkap dengan detail obat, kuantitas, harga satuan, subtotal, status transaksi, serta nama kasir penanggung jawab.
