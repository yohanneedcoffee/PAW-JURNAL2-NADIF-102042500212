<?php
// index.php (Beranda)
// Menampilkan jumlah data lewat COUNT(*) dari database, bukan angka tetap.
require_once __DIR__ . '/includes/init.php';

$jml_mk     = (int) query_satu($conn, 'SELECT COUNT(*) AS n FROM mata_kuliah')['n'];
$jml_materi = (int) query_satu($conn, 'SELECT COUNT(*) AS n FROM materi')['n'];
$jml_tugas  = (int) query_satu($conn, 'SELECT COUNT(*) AS n FROM tugas')['n'];
$daftar_mk  = semua_mk($conn);

$judul_halaman = 'Beranda';
$halaman_aktif = 'beranda';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Learning Management System</h1>
    <p class="sub">Pengelolaan mata kuliah, materi pembelajaran, dan tugas.</p>

    <div class="ringkasan">
        <a href="daftar_mata_kuliah.php"><span>Mata kuliah</span><strong><?= $jml_mk ?></strong></a>
        <a href="daftar_materi.php"><span>Materi</span><strong><?= $jml_materi ?></strong></a>
        <a href="daftar_tugas.php"><span>Tugas</span><strong><?= $jml_tugas ?></strong></a>
    </div>

    <h2>Mata kuliah tersedia</h2>
    <?php if (count($daftar_mk) === 0): ?>
        <div class="kosong">Belum ada mata kuliah. <a href="tambah_mata_kuliah.php">Tambah mata kuliah</a> terlebih dahulu.</div>
    <?php else: ?>
        <ul>
            <?php foreach ($daftar_mk as $mk): ?>
                <li>
                    <a href="detail_mata_kuliah.php?id=<?= (int) $mk['id'] ?>"><?= e($mk['kode']) ?> - <?= e($mk['nama']) ?></a>
                    (<a href="daftar_materi.php?mk=<?= (int) $mk['id'] ?>">materi</a>,
                     <a href="daftar_tugas.php?mk=<?= (int) $mk['id'] ?>">tugas</a>)
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
