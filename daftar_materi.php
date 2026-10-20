<?php
// daftar_materi.php (READ: daftar materi + pencarian judul + filter mata kuliah)
require_once __DIR__ . '/includes/init.php';

$q    = get_teks('q');
$f_mk = id_valid(get_teks('mk')); // null = semua mata kuliah (ID tidak valid dianggap tanpa filter)

// Nama tabel tertulis tetap di sini; hanya nilai pencarian yang di-bind.
$sql = 'SELECT m.id, m.judul, mk.kode, mk.nama AS mk_nama
        FROM materi m
        INNER JOIN mata_kuliah mk ON mk.id = m.mata_kuliah_id';
$kondisi = [];
$tipe    = '';
$param   = [];

if ($q !== '') {
    $kondisi[] = 'm.judul LIKE ?';
    $tipe     .= 's';
    $param[]   = like_aman($q);
}
if ($f_mk !== null) {
    $kondisi[] = 'm.mata_kuliah_id = ?';
    $tipe     .= 'i';
    $param[]   = $f_mk;
}
if ($kondisi) {
    $sql .= ' WHERE ' . implode(' AND ', $kondisi); // kata kunci dan filter dipakai bersamaan
}
$sql .= ' ORDER BY m.judul';

$data       = query_semua($conn, $sql, $tipe, $param);
$pilihan_mk = semua_mk($conn);

$judul_halaman = 'Materi';
$halaman_aktif = 'materi';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <div class="bar">
        <div>
            <h1>Materi</h1>
            <p class="sub">Menampilkan <?= count($data) ?> data.</p>
        </div>
        <a class="tombol tombol-utama" href="tambah_materi.php<?= $f_mk !== null ? '?mk=' . $f_mk : '' ?>">Tambah materi</a>
    </div>

    <form method="get" class="cari">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari judul materi">
        <select name="mk"><?= opsi_mk($pilihan_mk, $f_mk, 'Semua mata kuliah') ?></select>
        <button type="submit" class="tombol tombol-utama">Cari</button>
        <a class="tombol" href="daftar_materi.php">Reset</a>
    </form>

    <?php if (count($data) === 0): ?>
        <div class="kosong">
            <?= ($q !== '' || $f_mk !== null) ? 'Data tidak ditemukan.' : 'Belum ada data materi.' ?>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Judul</th><th>Mata kuliah</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($data as $r): ?>
                <tr>
                    <td><?= e($r['judul']) ?></td>
                    <td><?= e($r['kode']) ?> - <?= e($r['mk_nama']) ?></td>
                    <td class="aksi">
                        <a class="tombol tombol-kecil" href="detail_materi.php?id=<?= (int) $r['id'] ?>">Detail</a>
                        <a class="tombol tombol-kecil" href="edit_materi.php?id=<?= (int) $r['id'] ?>">Edit</a>
                        <a class="tombol tombol-kecil tombol-bahaya" href="hapus_materi.php?id=<?= (int) $r['id'] ?>">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
