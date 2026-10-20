<?php
// detail_materi.php (READ: detail satu materi + nama mata kuliahnya lewat JOIN)
require_once __DIR__ . '/includes/init.php';

$id = id_valid($_GET['id'] ?? null);
$m  = $id !== null
    ? query_satu(
        $conn,
        'SELECT m.id, m.judul, m.isi, m.tautan, mk.kode, mk.nama AS mk_nama
         FROM materi m
         INNER JOIN mata_kuliah mk ON mk.id = m.mata_kuliah_id
         WHERE m.id = ?',
        'i',
        [$id]
    )
    : null;

if ($m === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_materi.php');
}

$judul_halaman = 'Detail materi';
$halaman_aktif = 'materi';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Materi</h1>
    <h2><?= e($m['judul']) ?></h2>
    <dl class="detail">
        <dt>Mata kuliah</dt><dd><?= e($m['kode']) ?> - <?= e($m['mk_nama']) ?></dd>
        <dt>Judul materi</dt><dd><?= e($m['judul']) ?></dd>
        <dt>Isi materi</dt><dd><?= e($m['isi']) ?></dd>
        <dt>Tautan referensi</dt>
        <dd>
            <?php // Tautan sudah divalidasi http/https saat disimpan; tetap di-escape saat ditampilkan. ?>
            <a href="<?= e($m['tautan']) ?>" target="_blank" rel="noopener noreferrer"><?= e($m['tautan']) ?></a>
        </dd>
    </dl>
    <div class="form-aksi">
        <a class="tombol tombol-utama" href="edit_materi.php?id=<?= (int) $m['id'] ?>">Edit data</a>
        <a class="tombol" href="daftar_materi.php">Kembali</a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
