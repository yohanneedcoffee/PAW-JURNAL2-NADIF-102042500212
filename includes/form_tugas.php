<?php
// includes/form_tugas.php
// Form bersama untuk tambah dan edit tugas.
// Variabel yang dibutuhkan: $nilai, $galat, $aksi, $tombol, $pilihan_mk.
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
            <label for="judul">Judul tugas</label>
            <input type="text" id="judul" name="judul" value="<?= e($nilai['judul']) ?>">
            <?= pesan_galat_field($galat, 'judul') ?>
        </div>
        <div class="field penuh <?= isset($galat['instruksi']) ? 'ada-galat' : '' ?>">
            <label for="instruksi">Instruksi tugas</label>
            <textarea id="instruksi" name="instruksi"><?= e($nilai['instruksi']) ?></textarea>
            <?= pesan_galat_field($galat, 'instruksi') ?>
        </div>
        <div class="field <?= isset($galat['tenggat']) ? 'ada-galat' : '' ?>">
            <label for="tenggat">Tenggat</label>
            <input type="date" id="tenggat" name="tenggat" value="<?= e($nilai['tenggat']) ?>">
            <?= pesan_galat_field($galat, 'tenggat') ?>
        </div>
        <div class="field <?= isset($galat['status']) ? 'ada-galat' : '' ?>">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (STATUS_TUGAS as $s): ?>
                    <option value="<?= e($s) ?>" <?= $nilai['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                <?php endforeach; ?>
            </select>
            <?= pesan_galat_field($galat, 'status') ?>
        </div>
    </div>
    <div class="form-aksi">
        <button type="submit" class="tombol tombol-utama"><?= e($tombol) ?></button>
        <a class="tombol" href="daftar_tugas.php">Batal</a>
    </div>
</form>
