<?php
// tambah_tugas.php (CREATE)
// Tugas di sini hanya data instruksi (bukan tempat mengumpulkan jawaban).
require_once __DIR__ . '/includes/init.php';

$pilihan_mk = semua_mk($conn);
$mk_awal    = id_valid(get_teks('mk'));

$nilai = [
    'mata_kuliah_id' => $mk_awal !== null ? (string) $mk_awal : '',
    'judul'          => '',
    'instruksi'      => '',
    'tenggat'        => '',
    'status'         => 'Draf', // status awal sesuai contoh form di jurnal
];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = ambil_input_tugas();

    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat['umum'] = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $galat = validasi_tugas($conn, $nilai);

        if (!$galat) {
            query_ubah(
                $conn,
                'INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)',
                'issss',
                [(int) $nilai['mata_kuliah_id'], $nilai['judul'], $nilai['instruksi'], $nilai['tenggat'], $nilai['status']]
            );
            set_flash('sukses', 'Tugas "' . $nilai['judul'] . '" berhasil ditambahkan.');
            redirect('daftar_tugas.php');
        }
    }
}

$judul_halaman = 'Tambah tugas';
$halaman_aktif = 'tugas';
$aksi   = 'tambah_tugas.php';
$tombol = 'Simpan data';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Tambah tugas</h1>
    <?php if (isset($galat['umum'])): ?>
        <div class="pesan pesan-error"><?= e($galat['umum']) ?></div>
    <?php elseif ($galat): ?>
        <div class="pesan pesan-error">Data belum tersimpan. Periksa isian yang bertanda merah.</div>
    <?php endif; ?>
    <?php require __DIR__ . '/includes/form_tugas.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
