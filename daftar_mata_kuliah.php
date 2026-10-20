<?php
// daftar_mata_kuliah.php (READ: daftar + pencarian kode/nama)
require_once __DIR__ . '/includes/init.php';

$q = get_teks('q');

$sql   = 'SELECT id, kode, nama, dosen, semester FROM mata_kuliah';
$tipe  = '';
$param = [];
if ($q !== '') {
    // Pencarian dari kode ATAU nama. Kata kunci di-bind, bukan digabung ke SQL.
    $sql  .= ' WHERE kode LIKE ? OR nama LIKE ?';
    $tipe  = 'ss';
    $like  = like_aman($q);
    $param = [$like, $like];
}
$sql .= ' ORDER BY nama';
$data = query_semua($conn, $sql, $tipe, $param);

$judul_halaman = 'Mata kuliah';
$halaman_aktif = 'mk';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <div class="bar">
        <div>
            <h1>Mata kuliah</h1>
            <p class="sub">Menampilkan <?= count($data) ?> data.</p>
        </div>
        <a class="tombol tombol-utama" href="tambah_mata_kuliah.php">Tambah mata kuliah</a>
    </div>

    <form method="get" class="cari">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari kode atau nama mata kuliah">
        <button type="submit" class="tombol tombol-utama">Cari</button>
        <a class="tombol" href="daftar_mata_kuliah.php">Reset</a>
    </form>

    <?php if (count($data) === 0): ?>
        <div class="kosong">
            <?= $q !== '' ? 'Data tidak ditemukan.' : 'Belum ada data mata kuliah.' ?>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Mata kuliah</th><th>Dosen / semester</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($data as $r): ?>
                <tr>
                    <td><?= e($r['kode']) ?> - <?= e($r['nama']) ?></td>
                    <td><?= e($r['dosen']) ?> / Semester <?= (int) $r['semester'] ?></td>
                    <td class="aksi">
                        <a class="tombol tombol-kecil" href="detail_mata_kuliah.php?id=<?= (int) $r['id'] ?>">Detail</a>
                        <a class="tombol tombol-kecil" href="edit_mata_kuliah.php?id=<?= (int) $r['id'] ?>">Edit</a>
                        <a class="tombol tombol-kecil tombol-bahaya" href="hapus_mata_kuliah.php?id=<?= (int) $r['id'] ?>">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
