<?php
// detail_tugas.php (READ: detail satu tugas + nama mata kuliahnya lewat JOIN)
require_once __DIR__ . '/includes/init.php';

$id = id_valid($_GET['id'] ?? null);
$t  = $id !== null
    ? query_satu(
        $conn,
        'SELECT t.id, t.judul, t.instruksi, t.tenggat, t.status, mk.kode, mk.nama AS mk_nama
         FROM tugas t
         INNER JOIN mata_kuliah mk ON mk.id = t.mata_kuliah_id
         WHERE t.id = ?',
        'i',
        [$id]
    )
    : null;

if ($t === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_tugas.php');
}

$judul_halaman = 'Detail tugas';
$halaman_aktif = 'tugas';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Tugas</h1>
    <h2><?= e($t['judul']) ?></h2>
    <dl class="detail">
        <dt>Mata kuliah</dt><dd><?= e($t['kode']) ?> - <?= e($t['mk_nama']) ?></dd>
        <dt>Judul tugas</dt><dd><?= e($t['judul']) ?></dd>
        <dt>Instruksi</dt><dd><?= e($t['instruksi']) ?></dd>
        <dt>Tenggat</dt><dd><?= e(tgl_indo($t['tenggat'])) ?></dd>
        <dt>Status</dt><dd><?= e($t['status']) ?></dd>
    </dl>
    <div class="form-aksi">
        <a class="tombol tombol-utama" href="edit_tugas.php?id=<?= (int) $t['id'] ?>">Edit data</a>
        <a class="tombol" href="daftar_tugas.php">Kembali</a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
