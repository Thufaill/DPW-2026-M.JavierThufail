<?php
session_start();
session_destroy();

// Memulai sesi baru khusus untuk melempar pesan flash setelah logout
session_start();
$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anda berhasil logout.'];
header('Location: ../index.php');
exit;