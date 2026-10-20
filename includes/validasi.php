<?php
// includes/validasi.php
// Pengambilan dan pemeriksaan isian form untuk mata kuliah, materi, dan tugas.
// Semua fungsi validasi mengembalikan array galat: kosong = isian benar.
// Pemeriksaan dilakukan di server karena validasi di browser mudah dilewati.

/* ---------------------------------------------------------------
 * Mata kuliah
 * ------------------------------------------------------------- */

function ambil_input_mk(): array
{
    return [
        'kode'     => post_teks('kode'),
        'nama'     => post_teks('nama'),
        'dosen'    => post_teks('dosen'),
        'semester' => post_teks('semester'),
    ];
}

// $id_sendiri dipakai saat edit supaya kode milik baris itu sendiri tidak dianggap kembar.
function validasi_mk(mysqli $conn, array $d, int $id_sendiri = 0): array
{
    $g = [];

    // Kode: 3-20 karakter huruf kapital, angka, atau tanda -. \z = akhir string
    // (tidak mengizinkan newline di belakang seperti $).
    if (!preg_match('/^[A-Z0-9-]{3,20}\z/', $d['kode'])) {
        $g['kode'] = 'Kode harus 3-20 karakter: huruf kapital, angka, atau tanda -.';
    } elseif (query_satu($conn, 'SELECT id FROM mata_kuliah WHERE kode = ? AND id <> ?', 'si', [$d['kode'], $id_sendiri])) {
        // Cek kode kembar sebelum INSERT/UPDATE agar pesannya ramah.
        $g['kode'] = 'Kode mata kuliah sudah digunakan.';
    }

    $p = panjang($d['nama']);
    if ($p < 3 || $p > 100) {
        $g['nama'] = 'Nama harus 3-100 karakter.';
    }

    $p = panjang($d['dosen']);
    if ($p < 3 || $p > 100) {
        $g['dosen'] = 'Dosen harus 3-100 karakter.';
    }

    // Semester: hanya angka, 1 atau 2 digit, nilai 1-14.
    if (!preg_match('/^\d{1,2}\z/', $d['semester']) || (int) $d['semester'] < 1 || (int) $d['semester'] > 14) {
        $g['semester'] = 'Semester harus bilangan bulat 1-14.';
    }

    return $g;
}

/* ---------------------------------------------------------------
 * Pilihan mata kuliah (dipakai materi dan tugas)
 * ------------------------------------------------------------- */

// Mengembalikan pesan galat, atau null kalau mata kuliah valid dan ada di database.
function validasi_mk_id(mysqli $conn, string $nilai): ?string
{
    $id = id_valid($nilai);
    if ($id === null) {
        return 'Pilih mata kuliah.';
    }
    if (!query_satu($conn, 'SELECT id FROM mata_kuliah WHERE id = ?', 'i', [$id])) {
        return 'Mata kuliah tidak ditemukan.';
    }
    return null;
}

/* ---------------------------------------------------------------
 * Materi
 * ------------------------------------------------------------- */

function ambil_input_materi(): array
{
    return [
        'mata_kuliah_id' => post_teks('mata_kuliah_id'),
        'judul'          => post_teks('judul'),
        'isi'            => post_teks('isi'),
        'tautan'         => post_teks('tautan'),
    ];
}

function validasi_materi(mysqli $conn, array $d): array
{
    $g = [];

    $galat_mk = validasi_mk_id($conn, $d['mata_kuliah_id']);
    if ($galat_mk !== null) {
        $g['mata_kuliah_id'] = $galat_mk;
    }

    $p = panjang($d['judul']);
    if ($p < 3 || $p > 150) {
        $g['judul'] = 'Judul harus 3-150 karakter.';
    }

    $p = panjang($d['isi']);
    if ($p < 3 || $p > 2000) {
        $g['isi'] = 'Isi materi harus 3-2000 karakter.';
    }

    // Tautan: maksimal 255 karakter dan HARUS diawali http:// atau https://.
    // Skema lain seperti javascript: ditolak (mencegah XSS lewat href).
    if ($d['tautan'] === '') {
        $g['tautan'] = 'Tautan referensi wajib diisi.';
    } elseif (panjang($d['tautan']) > 255) {
        $g['tautan'] = 'Tautan maksimal 255 karakter.';
    } elseif (!preg_match('#^https?://#i', $d['tautan']) || filter_var($d['tautan'], FILTER_VALIDATE_URL) === false) {
        $g['tautan'] = 'Gunakan tautan http/https yang valid.';
    }

    return $g;
}

/* ---------------------------------------------------------------
 * Tugas
 * ------------------------------------------------------------- */

function ambil_input_tugas(): array
{
    return [
        'mata_kuliah_id' => post_teks('mata_kuliah_id'),
        'judul'          => post_teks('judul'),
        'instruksi'      => post_teks('instruksi'),
        'tenggat'        => post_teks('tenggat'),
        'status'         => post_teks('status'),
    ];
}

// Tanggal valid: format YYYY-MM-DD dan benar-benar ada di kalender (2026-02-31 ditolak).
function tanggal_valid(string $tanggal): bool
{
    $dt = DateTime::createFromFormat('!Y-m-d', $tanggal);
    return $dt !== false && $dt->format('Y-m-d') === $tanggal;
}

function validasi_tugas(mysqli $conn, array $d): array
{
    $g = [];

    $galat_mk = validasi_mk_id($conn, $d['mata_kuliah_id']);
    if ($galat_mk !== null) {
        $g['mata_kuliah_id'] = $galat_mk;
    }

    $p = panjang($d['judul']);
    if ($p < 3 || $p > 150) {
        $g['judul'] = 'Judul harus 3-150 karakter.';
    }

    $p = panjang($d['instruksi']);
    if ($p < 3 || $p > 2000) {
        $g['instruksi'] = 'Instruksi harus 3-2000 karakter.';
    }

    if (!tanggal_valid($d['tenggat'])) {
        $g['tenggat'] = 'Tenggat harus berupa tanggal yang valid.';
    }

    // Status dibandingkan ketat dengan daftar yang diizinkan.
    if (!in_array($d['status'], STATUS_TUGAS, true)) {
        $g['status'] = 'Status hanya boleh Draf, Aktif, atau Selesai.';
    }

    return $g;
}
