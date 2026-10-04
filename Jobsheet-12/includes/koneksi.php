<?php

// ====================================================================
// LOAD CONFIG DARI FILE .env (UNTUK DEBUG LOKAL & PRODUKSI)
// ====================================================================
$envCandidates = [
    __DIR__ . '/../.env',      // Jobsheet-12/.env
    __DIR__ . '/../../.env',   // root workspace/.env
    dirname($_SERVER['DOCUMENT_ROOT'] ?? '') . '/.env',
];

foreach ($envCandidates as $envFile) {
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, '=')) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val, " \t\n\r\0\x0B\"'");
                // Hanya isi jika belum ada di environment
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

// ====================================================================
// PARAMETER KONEKSI DATABASE SUPABASE POSTGRESQL
// ====================================================================
$host    = getenv('DB_HOST');
$port    = getenv('DB_PORT') ?: '5432';
$db      = getenv('DB_NAME') ?: 'postgres';
$user    = getenv('DB_USER');
$pass    = getenv('DB_PASSWORD');
$sslmode = getenv('DB_SSLMODE') ?: 'require';

if (empty($host) || empty($user)) {
    die(
        "<div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 25px; border: 1px solid #f5c2c7; background: #f8d7da; border-radius: 8px; color: #842029;'>" .
        "<h3 style='margin-top: 0;'>⚠️ Konfigurasi Database Belum Lengkap</h3>" .
        "<p>Variabel lingkungan (Environment Variables) untuk database Supabase belum diatur.</p>" .
        "<p><strong>Solusi:</strong> Buat file <code>.env</code> di dalam folder <code>Jobsheet-12/</code> dengan menyalin <code>.env.example</code>:</p>" .
        "<pre style='background: #fff; padding: 12px; border-radius: 4px; overflow-x: auto;'>DB_HOST=db.xxxxxxxx.supabase.co\nDB_PORT=5432\nDB_NAME=postgres\nDB_USER=postgres\nDB_PASSWORD=password_supabase_anda\nDB_SSLMODE=require</pre>" .
        "<p style='margin-bottom: 0;'>Atau gunakan Connection Pooler Supabase jika menggunakan jaringan IPv4 biasa.</p>" .
        "</div>"
    );
}

// ====================================================================
// INISIALISASI PDO POSTGRESQL
// ====================================================================
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
        "<hr style='border: none; border-top: 1px solid #f5c2c7; margin: 15px 0;'>" .
        "<strong>Tips Debugging Lokal:</strong>" .
        "<ul style='margin-top: 8px; padding-left: 20px; line-height: 1.6;'>" .
        "<li>Pastikan ekstensi <code>pdo_pgsql</code> telah aktif di PHP Anda.</li>" .
        "<li>Periksa apakah <code>DB_HOST</code>, <code>DB_USER</code>, dan <code>DB_PASSWORD</code> di file <code>.env</code> sudah sesuai dengan dashboard Supabase.</li>" .
        "<li>Jika menggunakan host direct (<code>db.xxxx.supabase.co</code>) dan mengalami timeout/koneksi gagal, coba gunakan <strong>Supabase Connection Pooler</strong> (host: <code>aws-0-[region].pooler.supabase.com</code>, port: <code>6543</code> atau <code>5432</code>, user: <code>postgres.[project-ref]</code>).</li>" .
        "<li>Pastikan koneksi internet aktif karena database di-host di Supabase Cloud.</li>" .
        "</ul>" .
        "</div>"
    );
}