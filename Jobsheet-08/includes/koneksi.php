<?php
$host = "localhost";
$port = "5432";
$db   = "simpus_mini"; // Sesuaikan nama database kamu
$user = "postgres";
$pass = "postgres";    // Sesuaikan password database kamu

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}