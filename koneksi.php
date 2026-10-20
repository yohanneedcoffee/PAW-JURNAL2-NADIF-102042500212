<?php
// koneksi.php
// Membuat koneksi ke database LMS. Dipanggil lewat includes/init.php.

// Mode strict: error MySQL dilempar sebagai exception (mysqli_sql_exception).
// Ini dipakai untuk menangkap error 1062 (kode kembar) dan 1451 (FK RESTRICT).
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';          // XAMPP default kosong; sesuaikan dengan perangkat kamu
$db_nama = 'lms_paw';
$db_port = 3306;        // ganti kalau MySQL kamu pakai port lain (mis. 3307)

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_nama, $db_port);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    // Pesan asli MySQL sengaja tidak ditampilkan ke pengguna (bisa membocorkan info server).
    error_log('Koneksi LMS gagal: ' . $ex->getMessage());
    http_response_code(500);
    echo '<h1>Koneksi database gagal</h1>';
    echo '<p>Periksa: MySQL sudah menyala, port benar, database <code>lms_paw</code> sudah di-import, '
       . 'dan username/password di <code>koneksi.php</code> sesuai.</p>';
    exit;
}
