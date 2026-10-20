<?php
// includes/form_mata_kuliah.php
// Form bersama untuk tambah dan edit mata kuliah.
// Variabel yang dibutuhkan: $nilai (isian), $galat (pesan per field), $aksi (URL tujuan), $tombol (teks tombol).
// novalidate: validasi dilakukan server supaya pesan galat konsisten dan bisa diuji.
?>
<form method="post" action="<?= e($aksi) ?>" novalidate>
    <?= csrf_input() ?>
    <div class="grid2">
        <div class="field <?= isset($galat['kode']) ? 'ada-galat' : '' ?>">
            <label for="kode">Kode mata kuliah</label>
            <input type="text" id="kode" name="kode" value="<?= e($nilai['kode']) ?>">
            <?= pesan_galat_field($galat, 'kode') ?>
        </div>
        <div class="field <?= isset($galat['nama']) ? 'ada-galat' : '' ?>">
            <label for="nama">Nama mata kuliah</label>
            <input type="text" id="nama" name="nama" value="<?= e($nilai['nama']) ?>">
            <?= pesan_galat_field($galat, 'nama') ?>
        </div>
        <div class="field <?= isset($galat['dosen']) ? 'ada-galat' : '' ?>">
            <label for="dosen">Dosen pengampu</label>
            <input type="text" id="dosen" name="dosen" value="<?= e($nilai['dosen']) ?>">
            <?= pesan_galat_field($galat, 'dosen') ?>
        </div>
        <div class="field <?= isset($galat['semester']) ? 'ada-galat' : '' ?>">
            <label for="semester">Semester</label>
            <input type="text" id="semester" name="semester" inputmode="numeric" value="<?= e($nilai['semester']) ?>">
            <?= pesan_galat_field($galat, 'semester') ?>
        </div>
    </div>
    <div class="form-aksi">
        <button type="submit" class="tombol tombol-utama"><?= e($tombol) ?></button>
        <a class="tombol" href="daftar_mata_kuliah.php">Batal</a>
    </div>
</form>
