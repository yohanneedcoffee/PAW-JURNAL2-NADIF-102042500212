<?php
// includes/init.php
// Dipanggil paling atas di setiap halaman: koneksi, fungsi bantu, dan validasi.
// fungsi.php memulai session (dibutuhkan untuk token CSRF dan pesan flash),
// jadi harus jalan sebelum ada output HTML apa pun.
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/fungsi.php';
require_once __DIR__ . '/validasi.php';
