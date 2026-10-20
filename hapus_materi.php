<?php
// hapus_materi.php (DELETE)
// GET : halaman konfirmasi saja. POST : hapus jika token CSRF cocok dan ID valid.
require_once __DIR__ . '/includes/init.php';

$galat_hapus = '';
$adalah_post = $_SERVER['REQUEST_METHOD'] === 'POST';

$id   = $adalah_post ? id_valid($_POST['id'] ?? null) : id_valid($_GET['id'] ?? null);
$data = $id !== null
    ? query_satu($conn, 'SELECT id, judul FROM materi WHERE id = ?', 'i', [$id])
    : null;

if ($data === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_materi.php');
}

if ($adalah_post) {
    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat_hapus = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        query_ubah($conn, 'DELETE FROM materi WHERE id = ?', 'i', [$id]);
        set_flash('sukses', 'Materi "' . $data['judul'] . '" berhasil dihapus.');
        redirect('daftar_materi.php');
    }
}

$judul_halaman = 'Hapus materi';
$halaman_aktif = 'materi';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Materi</h1>
    <?php if ($galat_hapus !== ''): ?>
        <div class="pesan pesan-error"><?= e($galat_hapus) ?></div>
    <?php endif; ?>
    <p>Hapus <strong><?= e($data['judul']) ?></strong>?</p>
    <p class="sub">Data baru dihapus setelah tombol konfirmasi dikirim.</p>
    <form method="post" action="hapus_materi.php" class="form-aksi">
        <?= csrf_input() ?>
        <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">
        <button type="submit" class="tombol tombol-bahaya">Ya, hapus data</button>
        <a class="tombol" href="daftar_materi.php">Batal</a>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
