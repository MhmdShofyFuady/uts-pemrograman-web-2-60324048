-- ============================================================
-- Database Export: uts_perpustakaan_NIM
-- Ganti "NIM" dengan NIM Anda sebelum import
-- ============================================================

CREATE DATABASE IF NOT EXISTS `uts_perpustakaan_60324048`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `uts_perpustakaan_60324048`;

-- -------------------------------------------------------
-- Struktur Tabel `kategori`
-- -------------------------------------------------------

DROP TABLE IF EXISTS `kategori`;

CREATE TABLE `kategori` (
  `id_kategori`   INT           NOT NULL AUTO_INCREMENT,
  `kode_kategori` VARCHAR(10)   NOT NULL UNIQUE,
  `nama_kategori` VARCHAR(50)   NOT NULL,
  `deskripsi`     TEXT,
  `status`        ENUM('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- Sample Data (minimal 3 record)
-- -------------------------------------------------------

INSERT INTO `kategori`
  (`kode_kategori`, `nama_kategori`, `deskripsi`, `status`)
VALUES
  ('KAT-001', 'Pemrograman', 'Buku-buku tentang bahasa pemrograman dan pengembangan perangkat lunak', 'Aktif'),
  ('KAT-002', 'Database',    'Buku-buku tentang sistem basis data, SQL, dan NoSQL',                   'Aktif'),
  ('KAT-003', 'Jaringan',    'Buku-buku tentang jaringan komputer dan keamanan siber',                'Aktif'),
  ('KAT-004', 'UI/UX Design','Buku-buku tentang desain antarmuka dan pengalaman pengguna',            'Aktif'),
  ('KAT-005', 'Algoritma',   'Buku-buku tentang struktur data dan algoritma pemrograman',             'Nonaktif');
