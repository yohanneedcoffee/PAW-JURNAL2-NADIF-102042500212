<?php
// edit_materi.php (UPDATE)
require_once __DIR__ . '/includes/init.php';

$id   = id_valid($_GET['id'] ?? null);
$lama = $id !== null
    ? query_satu($conn, 'SELECT id, mata_kuliah_id, judul, isi, tautan FROM materi WHERE id = ?', 'i', [$id])
    : null;

if ($lama === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_materi.php');
}

$pilihan_mk = semua_mk($conn);
$nilai = [
    'mata_kuliah_id' => (string) $lama['mata_kuliah_id'],
    'judul'          => $lama['judul'],
    'isi'            => $lama['isi'],
    'tautan'         => $lama['tautan'],
];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = ambil_input_materi();

    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat['umum'] = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $galat = validasi_materi($conn, $nilai);

        if (!$galat) {
            $nilai['mata_kuliah_id'] = (string) (int) $nilai['mata_kuliah_id'];

            $berubah = false;
            foreach (['mata_kuliah_id', 'judul', 'isi', 'tautan'] as $k) {
                if ((string) $lama[$k] !== (string) $nilai[$k]) {
                    $berubah = true;
                }
            }

            if (!$berubah) {
                set_flash('info', 'Tidak ada perubahan data.');
                redirect('daftar_materi.php');
            }

            query_ubah(
                $conn,
                'UPDATE materi SET mata_kuliah_id = ?, judul = ?, isi = ?, tautan = ? WHERE id = ?',
                'isssi',
                [(int) $nilai['mata_kuliah_id'], $nilai['judul'], $nilai['isi'], $nilai['tautan'], $id]
            );
            set_flash('sukses', 'Materi "' . $nilai['judul'] . '" berhasil diubah.');
            redirect('daftar_materi.php');
        }
    }
}

$judul_halaman = 'Edit materi';
$halaman_aktif = 'materi';
$aksi   = 'edit_materi.php?id=' . $id;
$tombol = 'Simpan perubahan';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Edit materi</h1>
    <?php if (isset($galat['umum'])): ?>
        <div class="pesan pesan-error"><?= e($galat['umum']) ?></div>
    <?php elseif ($galat): ?>
        <div class="pesan pesan-error">Data belum tersimpan. Periksa isian yang bertanda merah.</div>
    <?php endif; ?>
    <?php require __DIR__ . '/includes/form_materi.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
