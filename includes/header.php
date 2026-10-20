<?php
// includes/header.php
// Kepala halaman + navigasi. Halaman mengisi dulu $judul_halaman dan $halaman_aktif.
require_once __DIR__ . '/fungsi.php';
$flash = ambil_flash();
$halaman_aktif = $halaman_aktif ?? '';
$judul_halaman = $judul_halaman ?? 'LMS';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul_halaman) ?> - LMS</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="atas">
    <nav class="menu">
        <a href="index.php"               class="<?= $halaman_aktif === 'beranda' ? 'aktif' : '' ?>">Beranda</a>
        <a href="daftar_mata_kuliah.php"  class="<?= $halaman_aktif === 'mk'      ? 'aktif' : '' ?>">Mata kuliah</a>
        <a href="daftar_materi.php"       class="<?= $halaman_aktif === 'materi'  ? 'aktif' : '' ?>">Materi</a>
        <a href="daftar_tugas.php"        class="<?= $halaman_aktif === 'tugas'   ? 'aktif' : '' ?>">Tugas</a>
    </nav>
</header>
<main class="wadah">
<?php if ($flash): ?>
    <div class="pesan pesan-<?= e($flash['tipe']) ?>" role="alert"><?= e($flash['isi']) ?></div>
<?php endif; ?>
