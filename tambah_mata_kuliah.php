<?php
// tambah_mata_kuliah.php (CREATE)
// GET  : tampilkan form kosong.
// POST : cek token CSRF -> validasi isian -> INSERT -> redirect ke daftar.
require_once __DIR__ . '/includes/init.php';

$nilai = ['kode' => '', 'nama' => '', 'dosen' => '', 'semester' => ''];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = ambil_input_mk(); // isian tetap terisi kalau ada galat

    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat['umum'] = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $galat = validasi_mk($conn, $nilai);

        if (!$galat) {
            try {
                query_ubah(
                    $conn,
                    'INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)',
                    'sssi',
                    [$nilai['kode'], $nilai['nama'], $nilai['dosen'], (int) $nilai['semester']]
                );
                set_flash('sukses', 'Mata kuliah "' . $nilai['kode'] . '" berhasil ditambahkan.');
                redirect('daftar_mata_kuliah.php');
            } catch (mysqli_sql_exception $ex) {
                // 1062 = duplicate entry (jaga-jaga kalau dua orang kirim kode sama bersamaan).
                if ((int) $ex->getCode() === 1062) {
                    $galat['kode'] = 'Kode mata kuliah sudah digunakan.';
                } else {
                    throw $ex;
                }
            }
        }
    }
}

$judul_halaman = 'Tambah mata kuliah';
$halaman_aktif = 'mk';
$aksi   = 'tambah_mata_kuliah.php';
$tombol = 'Simpan data';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Tambah mata kuliah</h1>
    <?php if (isset($galat['umum'])): ?>
        <div class="pesan pesan-error"><?= e($galat['umum']) ?></div>
    <?php elseif ($galat): ?>
        <div class="pesan pesan-error">Data belum tersimpan. Periksa isian yang bertanda merah.</div>
    <?php endif; ?>
    <?php require __DIR__ . '/includes/form_mata_kuliah.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
