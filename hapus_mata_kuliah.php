<?php
// hapus_mata_kuliah.php (DELETE)
// GET  : hanya menampilkan halaman konfirmasi (tidak menghapus apa pun).
// POST : hapus hanya jika token CSRF cocok dan ID valid/ada.
// Foreign key ON DELETE RESTRICT menolak penghapusan selama masih ada materi/tugas (error 1451).
require_once __DIR__ . '/includes/init.php';

$galat_hapus = '';
$adalah_post = $_SERVER['REQUEST_METHOD'] === 'POST';

// ID diambil dari POST saat konfirmasi, dari URL saat pertama dibuka. Keduanya divalidasi.
$id   = $adalah_post ? id_valid($_POST['id'] ?? null) : id_valid($_GET['id'] ?? null);
$data = $id !== null
    ? query_satu($conn, 'SELECT id, kode, nama FROM mata_kuliah WHERE id = ?', 'i', [$id])
    : null;

if ($data === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_mata_kuliah.php');
}

if ($adalah_post) {
    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat_hapus = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        try {
            query_ubah($conn, 'DELETE FROM mata_kuliah WHERE id = ?', 'i', [$id]);
            set_flash('sukses', 'Mata kuliah "' . $data['nama'] . '" berhasil dihapus.');
            redirect('daftar_mata_kuliah.php');
        } catch (mysqli_sql_exception $ex) {
            if ((int) $ex->getCode() === 1451) {
                // Masih dipakai materi/tugas: data tetap ada, tampilkan konfirmasi lagi dengan pesan.
                $galat_hapus = 'Data belum tersimpan. Mata kuliah masih memiliki materi atau tugas. Hapus data terkait terlebih dahulu.';
            } else {
                throw $ex;
            }
        }
    }
}

$judul_halaman = 'Hapus mata kuliah';
$halaman_aktif = 'mk';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Mata kuliah</h1>
    <?php if ($galat_hapus !== ''): ?>
        <div class="pesan pesan-error"><?= e($galat_hapus) ?></div>
    <?php endif; ?>
    <p>Hapus <strong><?= e($data['nama']) ?></strong>?</p>
    <p class="sub">Data baru dihapus setelah tombol konfirmasi dikirim.</p>
    <form method="post" action="hapus_mata_kuliah.php" class="form-aksi">
        <?= csrf_input() ?>
        <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">
        <button type="submit" class="tombol tombol-bahaya">Ya, hapus data</button>
        <a class="tombol" href="daftar_mata_kuliah.php">Batal</a>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
