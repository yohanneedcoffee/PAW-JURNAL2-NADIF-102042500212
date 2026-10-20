<?php
// daftar_tugas.php (READ: daftar tugas + pencarian judul + filter mata kuliah)
require_once __DIR__ . '/includes/init.php';

$q    = get_teks('q');
$f_mk = id_valid(get_teks('mk'));

$sql = 'SELECT t.id, t.judul, t.tenggat, t.status, mk.kode, mk.nama AS mk_nama
        FROM tugas t
        INNER JOIN mata_kuliah mk ON mk.id = t.mata_kuliah_id';
$kondisi = [];
$tipe    = '';
$param   = [];

if ($q !== '') {
    $kondisi[] = 't.judul LIKE ?';
    $tipe     .= 's';
    $param[]   = like_aman($q);
}
if ($f_mk !== null) {
    $kondisi[] = 't.mata_kuliah_id = ?';
    $tipe     .= 'i';
    $param[]   = $f_mk;
}
if ($kondisi) {
    $sql .= ' WHERE ' . implode(' AND ', $kondisi);
}
$sql .= ' ORDER BY t.tenggat, t.judul';

$data       = query_semua($conn, $sql, $tipe, $param);
$pilihan_mk = semua_mk($conn);

$judul_halaman = 'Tugas';
$halaman_aktif = 'tugas';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <div class="bar">
        <div>
            <h1>Tugas</h1>
            <p class="sub">Menampilkan <?= count($data) ?> data.</p>
        </div>
        <a class="tombol tombol-utama" href="tambah_tugas.php<?= $f_mk !== null ? '?mk=' . $f_mk : '' ?>">Tambah tugas</a>
    </div>

    <form method="get" class="cari">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari judul tugas">
        <select name="mk"><?= opsi_mk($pilihan_mk, $f_mk, 'Semua mata kuliah') ?></select>
        <button type="submit" class="tombol tombol-utama">Cari</button>
        <a class="tombol" href="daftar_tugas.php">Reset</a>
    </form>

    <?php if (count($data) === 0): ?>
        <div class="kosong">
            <?= ($q !== '' || $f_mk !== null) ? 'Data tidak ditemukan.' : 'Belum ada data tugas.' ?>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Judul</th><th>Mata kuliah</th><th>Tenggat</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php foreach ($data as $r): ?>
                <tr>
                    <td><?= e($r['judul']) ?></td>
                    <td><?= e($r['kode']) ?> - <?= e($r['mk_nama']) ?></td>
                    <td><?= e(tgl_indo($r['tenggat'])) ?></td>
                    <td><span class="lencana lencana-<?= e(strtolower($r['status'])) ?>"><?= e($r['status']) ?></span></td>
                    <td class="aksi">
                        <a class="tombol tombol-kecil" href="detail_tugas.php?id=<?= (int) $r['id'] ?>">Detail</a>
                        <a class="tombol tombol-kecil" href="edit_tugas.php?id=<?= (int) $r['id'] ?>">Edit</a>
                        <a class="tombol tombol-kecil tombol-bahaya" href="hapus_tugas.php?id=<?= (int) $r['id'] ?>">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
