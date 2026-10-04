<?php
// Membungkus output data untuk mencegah serangan XSS (Cross-Site Scripting)
function e($value) {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}