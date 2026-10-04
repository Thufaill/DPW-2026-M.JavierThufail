<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Registrasi Petugas";
include __DIR__ . '/../includes/header.php';
?>
<div class="form-card" style="max-width: 400px; margin: 40px auto;">
    <div class="form-section">
        <h3>Daftar Petugas SIAFARMA</h3>
        <p>Buat akun untuk mengakses fitur manajemen apotek.</p>
    </div>
    <form action="proses_register.php" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" name="nama" id="nama" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required minlength="6">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Daftar</button>
        </div>
        <p style="text-align: center; margin-top: 15px; font-size: 12px;">
            Sudah punya akun? <a href="login.php" style="color: #18a77a;">Login di sini</a>
        </p>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>