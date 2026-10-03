<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = "Login";
include __DIR__ . '/../includes/header.php';
?>
<div class="form-card" style="max-width: 400px; margin: 40px auto;">
    <div class="form-section">
        <h3>Login SIAFARMA</h3>
        <p>Masukkan kredensial Anda untuk masuk.</p>
    </div>
    <form action="proses_login.php" method="POST">
        <?php echo csrf_field(); ?>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required autocomplete="off">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk</button>
        </div>
        <p style="text-align: center; margin-top: 15px; font-size: 12px;">
            Belum punya akun? <a href="register.php" style="color: #18a77a;">Daftar di sini</a>
        </p>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>