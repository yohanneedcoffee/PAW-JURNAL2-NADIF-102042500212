<?php
// includes/form_materi.php
// Form bersama untuk tambah dan edit materi.
// Variabel yang dibutuhkan: $nilai, $galat, $aksi, $tombol, $pilihan_mk (daftar mata kuliah).
?>
<form method="post" action="<?= e($aksi) ?>" novalidate>
    <?= csrf_input() ?>
    <div class="grid2">
        <div class="field <?= isset($galat['mata_kuliah_id']) ? 'ada-galat' : '' ?>">
            <label for="mata_kuliah_id">Mata kuliah</label>
            <select id="mata_kuliah_id" name="mata_kuliah_id">
                <?= opsi_mk($pilihan_mk, $nilai['mata_kuliah_id'], 'Pilih Mata kuliah') ?>
            </select>
            <?= pesan_galat_field($galat, 'mata_kuliah_id') ?>
        </div>
        <div class="field <?= isset($galat['judul']) ? 'ada-galat' : '' ?>">
            <label for="judul">Judul materi</label>
            <input type="text" id="judul" name="judul" value="<?= e($nilai['judul']) ?>">
            <?= pesan_galat_field($galat, 'judul') ?>
        </div>
        <div class="field penuh <?= isset($galat['isi']) ? 'ada-galat' : '' ?>">
            <label for="isi">Isi materi</label>
            <textarea id="isi" name="isi"><?= e($nilai['isi']) ?></textarea>
            <?= pesan_galat_field($galat, 'isi') ?>
        </div>
        <div class="field penuh <?= isset($galat['tautan']) ? 'ada-galat' : '' ?>">
            <label for="tautan">Tautan referensi</label>
            <input type="text" id="tautan" name="tautan" value="<?= e($nilai['tautan']) ?>" placeholder="https://...">
            <?= pesan_galat_field($galat, 'tautan') ?>
        </div>
    </div>
    <div class="form-aksi">
        <button type="submit" class="tombol tombol-utama"><?= e($tombol) ?></button>
        <a class="tombol" href="daftar_materi.php">Batal</a>
    </div>
</form>
