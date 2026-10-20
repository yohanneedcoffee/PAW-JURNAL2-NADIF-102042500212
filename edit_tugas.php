<?php
// edit_tugas.php (UPDATE)
require_once __DIR__ . '/includes/init.php';

$id   = id_valid($_GET['id'] ?? null);
$lama = $id !== null
    ? query_satu($conn, 'SELECT id, mata_kuliah_id, judul, instruksi, tenggat, status FROM tugas WHERE id = ?', 'i', [$id])
    : null;

if ($lama === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_tugas.php');
}

$pilihan_mk = semua_mk($conn);
$nilai = [
    'mata_kuliah_id' => (string) $lama['mata_kuliah_id'],
    'judul'          => $lama['judul'],
    'instruksi'      => $lama['instruksi'],
    'tenggat'        => $lama['tenggat'], // format YYYY-MM-DD, cocok dengan <input type="date">
    'status'         => $lama['status'],
];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = ambil_input_tugas();

    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat['umum'] = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $galat = validasi_tugas($conn, $nilai);

        if (!$galat) {
            $nilai['mata_kuliah_id'] = (string) (int) $nilai['mata_kuliah_id'];

            $berubah = false;
            foreach (['mata_kuliah_id', 'judul', 'instruksi', 'tenggat', 'status'] as $k) {
                if ((string) $lama[$k] !== (string) $nilai[$k]) {
                    $berubah = true;
                }
            }

            if (!$berubah) {
                set_flash('info', 'Tidak ada perubahan data.');
                redirect('daftar_tugas.php');
            }

            query_ubah(
                $conn,
                'UPDATE tugas SET mata_kuliah_id = ?, judul = ?, instruksi = ?, tenggat = ?, status = ? WHERE id = ?',
                'issssi',
                [(int) $nilai['mata_kuliah_id'], $nilai['judul'], $nilai['instruksi'], $nilai['tenggat'], $nilai['status'], $id]
            );
            set_flash('sukses', 'Tugas "' . $nilai['judul'] . '" berhasil diubah.');
            redirect('daftar_tugas.php');
        }
    }
}

$judul_halaman = 'Edit tugas';
$halaman_aktif = 'tugas';
$aksi   = 'edit_tugas.php?id=' . $id;
$tombol = 'Simpan perubahan';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Edit tugas</h1>
    <?php if (isset($galat['umum'])): ?>
        <div class="pesan pesan-error"><?= e($galat['umum']) ?></div>
    <?php elseif ($galat): ?>
        <div class="pesan pesan-error">Data belum tersimpan. Periksa isian yang bertanda merah.</div>
    <?php endif; ?>
    <?php require __DIR__ . '/includes/form_tugas.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
