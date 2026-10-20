<?php
// tambah_materi.php (CREATE)
// Materi menyimpan mata_kuliah_id (foreign key) dari dropdown. Nilai dropdown tetap
// divalidasi di server: harus angka dan benar-benar ada di tabel mata_kuliah.
require_once __DIR__ . '/includes/init.php';

$pilihan_mk = semua_mk($conn);
$mk_awal    = id_valid(get_teks('mk')); // dari tombol "Tambah materi" saat filter aktif

$nilai = [
    'mata_kuliah_id' => $mk_awal !== null ? (string) $mk_awal : '',
    'judul'          => '',
    'isi'            => '',
    'tautan'         => '',
];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = ambil_input_materi();

    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat['umum'] = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $galat = validasi_materi($conn, $nilai);

        if (!$galat) {
            query_ubah(
                $conn,
                'INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES (?, ?, ?, ?)',
                'isss',
                [(int) $nilai['mata_kuliah_id'], $nilai['judul'], $nilai['isi'], $nilai['tautan']]
            );
            set_flash('sukses', 'Materi "' . $nilai['judul'] . '" berhasil ditambahkan.');
            redirect('daftar_materi.php');
        }
    }
}

$judul_halaman = 'Tambah materi';
$halaman_aktif = 'materi';
$aksi   = 'tambah_materi.php';
$tombol = 'Simpan data';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Tambah materi</h1>
    <?php if (isset($galat['umum'])): ?>
        <div class="pesan pesan-error"><?= e($galat['umum']) ?></div>
    <?php elseif ($galat): ?>
        <div class="pesan pesan-error">Data belum tersimpan. Periksa isian yang bertanda merah.</div>
    <?php endif; ?>
    <?php require __DIR__ . '/includes/form_materi.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
