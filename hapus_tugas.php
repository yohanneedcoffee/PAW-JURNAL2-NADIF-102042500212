<?php
// hapus_tugas.php (DELETE)
// GET : halaman konfirmasi saja. POST : hapus jika token CSRF cocok dan ID valid.
require_once __DIR__ . '/includes/init.php';

$galat_hapus = '';
$adalah_post = $_SERVER['REQUEST_METHOD'] === 'POST';

$id   = $adalah_post ? id_valid($_POST['id'] ?? null) : id_valid($_GET['id'] ?? null);
$data = $id !== null
    ? query_satu($conn, 'SELECT id, judul FROM tugas WHERE id = ?', 'i', [$id])
    : null;

if ($data === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_tugas.php');
}

if ($adalah_post) {
    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat_hapus = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        query_ubah($conn, 'DELETE FROM tugas WHERE id = ?', 'i', [$id]);
        set_flash('sukses', 'Tugas "' . $data['judul'] . '" berhasil dihapus.');
        redirect('daftar_tugas.php');
    }
}

$judul_halaman = 'Hapus tugas';
$halaman_aktif = 'tugas';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Tugas</h1>
    <?php if ($galat_hapus !== ''): ?>
        <div class="pesan pesan-error"><?= e($galat_hapus) ?></div>
    <?php endif; ?>
    <p>Hapus <strong><?= e($data['judul']) ?></strong>?</p>
    <p class="sub">Data baru dihapus setelah tombol konfirmasi dikirim.</p>
    <form method="post" action="hapus_tugas.php" class="form-aksi">
        <?= csrf_input() ?>
        <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">
        <button type="submit" class="tombol tombol-bahaya">Ya, hapus data</button>
        <a class="tombol" href="daftar_tugas.php">Batal</a>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
