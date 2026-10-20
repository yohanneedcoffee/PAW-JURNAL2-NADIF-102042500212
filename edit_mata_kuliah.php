<?php
// edit_mata_kuliah.php (UPDATE)
// ID selalu dari URL (?id=) dan divalidasi sebelum dipakai membaca data.
// Form berisi data lama; disimpan dengan ID yang sama (UPDATE ... WHERE id = ?).
require_once __DIR__ . '/includes/init.php';

$id   = id_valid($_GET['id'] ?? null);
$lama = $id !== null
    ? query_satu($conn, 'SELECT id, kode, nama, dosen, semester FROM mata_kuliah WHERE id = ?', 'i', [$id])
    : null;

if ($lama === null) {
    set_flash('error', 'Data tidak ditemukan.');
    redirect('daftar_mata_kuliah.php');
}

$nilai = [
    'kode'     => $lama['kode'],
    'nama'     => $lama['nama'],
    'dosen'    => $lama['dosen'],
    'semester' => (string) $lama['semester'],
];
$galat = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nilai = ambil_input_mk();

    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $galat['umum'] = 'Token tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        // $id dikirim sebagai pengecualian supaya kode milik sendiri tidak dianggap kembar.
        $galat = validasi_mk($conn, $nilai, $id);

        if (!$galat) {
            $nilai['semester'] = (string) (int) $nilai['semester']; // "03" -> "3"

            // Bandingkan dengan data lama: kalau sama persis, tidak perlu UPDATE.
            $berubah = false;
            foreach (['kode', 'nama', 'dosen', 'semester'] as $k) {
                if ((string) $lama[$k] !== (string) $nilai[$k]) {
                    $berubah = true;
                }
            }

            if (!$berubah) {
                set_flash('info', 'Tidak ada perubahan data.');
                redirect('daftar_mata_kuliah.php');
            }

            try {
                query_ubah(
                    $conn,
                    'UPDATE mata_kuliah SET kode = ?, nama = ?, dosen = ?, semester = ? WHERE id = ?',
                    'sssii',
                    [$nilai['kode'], $nilai['nama'], $nilai['dosen'], (int) $nilai['semester'], $id]
                );
                set_flash('sukses', 'Mata kuliah "' . $nilai['kode'] . '" berhasil diubah.');
                redirect('daftar_mata_kuliah.php');
            } catch (mysqli_sql_exception $ex) {
                if ((int) $ex->getCode() === 1062) {
                    $galat['kode'] = 'Kode mata kuliah sudah digunakan.';
                } else {
                    throw $ex;
                }
            }
        }
    }
}

$judul_halaman = 'Edit mata kuliah';
$halaman_aktif = 'mk';
$aksi   = 'edit_mata_kuliah.php?id=' . $id;
$tombol = 'Simpan perubahan';
require __DIR__ . '/includes/header.php';
?>
<section class="kartu">
    <h1>Edit mata kuliah</h1>
    <?php if (isset($galat['umum'])): ?>
        <div class="pesan pesan-error"><?= e($galat['umum']) ?></div>
    <?php elseif ($galat): ?>
        <div class="pesan pesan-error">Data belum tersimpan. Periksa isian yang bertanda merah.</div>
    <?php endif; ?>
    <?php require __DIR__ . '/includes/form_mata_kuliah.php'; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
