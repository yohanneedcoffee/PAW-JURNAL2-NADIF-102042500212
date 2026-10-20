-- skema_lms.sql
-- Database LMS sederhana: mata_kuliah (1) -> materi (N) dan tugas (N).
-- Import lewat phpMyAdmin (tab Import) atau: mysql -u root < skema_lms.sql
-- PERINGATAN: file ini menghapus tabel lama (DROP TABLE) supaya bisa di-import ulang.

CREATE DATABASE IF NOT EXISTS lms_paw
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE lms_paw;

-- Hapus dari tabel anak dulu, baru tabel induk (urutan wajib karena ada foreign key).
DROP TABLE IF EXISTS tugas;
DROP TABLE IF EXISTS materi;
DROP TABLE IF EXISTS mata_kuliah;

-- ---------------------------------------------------------------
-- 1. Mata kuliah (tabel induk)
-- kode dibuat UNIQUE supaya tidak ada dua mata kuliah dengan kode sama.
-- ---------------------------------------------------------------
CREATE TABLE mata_kuliah (
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode     VARCHAR(20)  NOT NULL UNIQUE,
  nama     VARCHAR(100) NOT NULL,
  dosen    VARCHAR(100) NOT NULL,
  semester TINYINT UNSIGNED NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- 2. Materi (tabel anak)
-- Menyimpan mata_kuliah_id. ON DELETE RESTRICT: mata kuliah yang
-- masih punya materi tidak bisa dihapus.
-- ---------------------------------------------------------------
CREATE TABLE materi (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mata_kuliah_id INT UNSIGNED NOT NULL,
  judul          VARCHAR(150) NOT NULL,
  isi            TEXT         NOT NULL,
  tautan         VARCHAR(255) NOT NULL,
  FOREIGN KEY (mata_kuliah_id)
    REFERENCES mata_kuliah(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- 3. Tugas (tabel anak)
-- status dijaga di sisi PHP (hanya Draf, Aktif, Selesai).
-- ---------------------------------------------------------------
CREATE TABLE tugas (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  mata_kuliah_id INT UNSIGNED NOT NULL,
  judul          VARCHAR(150) NOT NULL,
  instruksi      TEXT         NOT NULL,
  tenggat        DATE         NOT NULL,
  status         VARCHAR(20)  NOT NULL,
  FOREIGN KEY (mata_kuliah_id)
    REFERENCES mata_kuliah(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Data awal (masing-masing 2 baris)
-- ---------------------------------------------------------------
INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES
  ('ALP-101', 'Algoritma dan Pemrograman',       'Dosen ALP', 1),
  ('PAW-101', 'Pengembangan Aplikasi Website',   'Dosen PAW', 3);

INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES
  (1, 'Variabel dan Percabangan',
      'Materi membahas variabel, tipe data, dan struktur percabangan if-else.',
      'https://www.php.net/manual/en/language.variables.php'),
  (2, 'Dasar PHP',
      'Materi membahas sintaks dasar PHP, variabel, dan cara PHP berjalan di server.',
      'https://www.php.net/manual/en/langref.php');

INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES
  (1, 'Latihan Algoritma Dasar',
      'Buat flowchart dan pseudocode untuk menghitung rata-rata tiga nilai.',
      '2026-11-10', 'Aktif'),
  (2, 'Latihan CRUD',
      'Buat aplikasi CRUD sederhana dengan PHP native dan MySQL sesuai instruksi di modul.',
      '2026-11-20', 'Draf');
