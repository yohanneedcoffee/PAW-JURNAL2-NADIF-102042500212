<?php
// detail_mata_kuliah.php (READ: detail satu mata kuliah + materi dan tugas miliknya)
require_once __DIR__ . '/includes/init.php';

$id = id_valid($_GET['id'] ?? null);
$mk = $id !== null
    ? query_satu($conn, 'SELECT id, kode, nama, dosen, semester FROM mata_kuliah WHERE id = ?', 'i', [$id])
    : null;

if ($mk === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_mata_kuliah.php');
}

$daftar_materi = query_semua($conn, 'SELECT id, judul FROM materi WHERE mata_kuliah_id = ? ORDER BY judul', 'i', [$id]);
$daftar_tugas  = query_semua($conn, 'SELECT id, judul, status FROM tugas WHERE mata_kuliah_id = ? ORDER BY judul', 'i', [$id]);

$judul_halaman = 'Detail mata kuliah';
$halaman_aktif = 'mk';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Mata kuliah</h1>
    <dl class="detail">
        <dt>Kode</dt><dd><?= e($mk['kode']) ?></dd>
        <dt>Nama</dt><dd><?= e($mk['nama']) ?></dd>
        <dt>Dosen</dt><dd><?= e($mk['dosen']) ?></dd>
        <dt>Semester</dt><dd><?= (int) $mk['semester'] ?></dd>
    </dl>
    <div class="form-aksi">
        <a class="tombol tombol-utama" href="edit_mata_kuliah.php?id=<?= (int) $mk['id'] ?>">Edit data</a>
        <a class="tombol" href="daftar_mata_kuliah.php">Kembali</a>
    </div>
</section>

<section class="kartu">
    <h2>Materi (<?= count($daftar_materi) ?>)</h2>
    <?php if (!$daftar_materi): ?>
        <p class="sub">Belum ada materi untuk mata kuliah ini.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($daftar_materi as $m): ?>
                <li><a href="detail_materi.php?id=<?= (int) $m['id'] ?>"><?= e($m['judul']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Tugas (<?= count($daftar_tugas) ?>)</h2>
    <?php if (!$daftar_tugas): ?>
        <p class="sub">Belum ada tugas untuk mata kuliah ini.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($daftar_tugas as $t): ?>
                <li><a href="detail_tugas.php?id=<?= (int) $t['id'] ?>"><?= e($t['judul']) ?></a> (<?= e($t['status']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
