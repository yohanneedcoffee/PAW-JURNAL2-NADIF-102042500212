<?php
// includes/fungsi.php
// Fungsi bantu: session, escape HTML, CSRF, flash message, dan query berparameter.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pilihan status tugas yang diizinkan (dipakai di form dan validasi).
const STATUS_TUGAS = ['Draf', 'Aktif', 'Selesai'];

/* ---------------------------------------------------------------
 * Output aman
 * ------------------------------------------------------------- */

// Semua teks dari pengguna/database yang ditampilkan lewat e() supaya
// karakter < > " ' menjadi entitas HTML (mencegah XSS).
function e($teks): string
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url)
{
    header('Location: ' . $url);
    exit;
}

/* ---------------------------------------------------------------
 * Ambil input
 * ------------------------------------------------------------- */

// Hanya terima string (kalau ada yang kirim array, dianggap kosong) lalu trim.
function post_teks(string $kunci): string
{
    return (isset($_POST[$kunci]) && is_string($_POST[$kunci])) ? trim($_POST[$kunci]) : '';
}

function get_teks(string $kunci): string
{
    return (isset($_GET[$kunci]) && is_string($_GET[$kunci])) ? trim($_GET[$kunci]) : '';
}

// Validasi ID: harus berupa angka bulat positif. Mengembalikan int atau null.
// Dipanggil SEBELUM ID dipakai untuk membaca data.
function id_valid($nilai): ?int
{
    if (is_int($nilai)) {
        $nilai = (string) $nilai;
    }
    if (!is_string($nilai) || !ctype_digit($nilai) || strlen($nilai) > 10) {
        return null;
    }
    $id = (int) $nilai;
    return $id > 0 ? $id : null;
}

function panjang(string $teks): int
{
    return mb_strlen($teks, 'UTF-8');
}

// Escape karakter wildcard LIKE (% dan _) agar kata kunci dicari apa adanya.
function like_aman(string $kata): string
{
    return '%' . addcslashes($kata, '%_\\') . '%';
}

/* ---------------------------------------------------------------
 * Pesan flash (tampil sekali setelah redirect)
 * ------------------------------------------------------------- */

function set_flash(string $tipe, string $isi): void
{
    $_SESSION['flash'] = ['tipe' => $tipe, 'isi' => $isi];
}

function ambil_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/* ---------------------------------------------------------------
 * CSRF token (disimpan di session, dicocokkan saat POST)
 * ------------------------------------------------------------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

// hash_equals membandingkan dengan waktu konstan (aman dari timing attack).
function csrf_valid($token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/* ---------------------------------------------------------------
 * Query berparameter (prepare + bind_param + execute)
 * Nilai dari pengguna TIDAK PERNAH digabung langsung ke string SQL;
 * hanya lewat tanda ? lalu di-bind. Nama tabel selalu tertulis tetap
 * di kode, bukan dari isian pengguna.
 * ------------------------------------------------------------- */

// SELECT: mengembalikan semua baris sebagai array.
function query_semua(mysqli $conn, string $sql, string $tipe = '', array $param = []): array
{
    $stmt = $conn->prepare($sql);
    if ($tipe !== '') {
        $stmt->bind_param($tipe, ...$param);
    }
    $stmt->execute();
    $hasil = $stmt->get_result();
    $baris = [];
    while ($r = $hasil->fetch_assoc()) {
        $baris[] = $r;
    }
    $stmt->close();
    return $baris;
}

// SELECT satu baris (atau null kalau tidak ada).
function query_satu(mysqli $conn, string $sql, string $tipe = '', array $param = []): ?array
{
    $baris = query_semua($conn, $sql, $tipe, $param);
    return $baris[0] ?? null;
}

// INSERT / UPDATE / DELETE: mengembalikan jumlah baris terpengaruh.
function query_ubah(mysqli $conn, string $sql, string $tipe, array $param): int
{
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($tipe, ...$param);
    $stmt->execute();
    $terpengaruh = $stmt->affected_rows;
    $stmt->close();
    return $terpengaruh;
}

/* ---------------------------------------------------------------
 * Bantuan tampilan
 * ------------------------------------------------------------- */

function semua_mk(mysqli $conn): array
{
    return query_semua($conn, 'SELECT id, kode, nama FROM mata_kuliah ORDER BY nama');
}

// Membuat <option> untuk dropdown mata kuliah.
function opsi_mk(array $daftar, $terpilih, string $placeholder): string
{
    $html = '<option value="">' . e($placeholder) . '</option>';
    foreach ($daftar as $mk) {
        $sel = ((string) $terpilih === (string) $mk['id']) ? ' selected' : '';
        $html .= '<option value="' . (int) $mk['id'] . '"' . $sel . '>'
              . e($mk['kode'] . ' - ' . $mk['nama']) . '</option>';
    }
    return $html;
}

function pesan_galat_field(array $galat, string $field): string
{
    return isset($galat[$field]) ? '<p class="galat">' . e($galat[$field]) . '</p>' : '';
}

function tgl_indo(string $ymd): string
{
    $waktu = strtotime($ymd);
    return $waktu ? date('d/m/Y', $waktu) : $ymd;
}
