<?php
$page_title = "Registrasi Petugas";
include __DIR__ . '/../includes/header.php';
?>
<div class="form-card" style="max-width: 400px; margin: 40px auto;">
    <div class="form-section">
        <h3>Daftar Petugas SIAFARMA</h3>
        <p>Buat akun untuk mengakses fitur manajemen apotek.</p>
    </div>
    <form action="proses_register.php" method="POST">
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" required>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Daftar</button>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>