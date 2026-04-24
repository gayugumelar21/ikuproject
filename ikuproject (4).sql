-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 24, 2026 at 02:05 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ikuproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('sistem-iku-kabupaten-pringsewu-cache-1c31ecdcf43a4c45335e125fdd661c66', 'i:1;', 1776983277),
('sistem-iku-kabupaten-pringsewu-cache-1c31ecdcf43a4c45335e125fdd661c66:timer', 'i:1776983277;', 1776983277),
('sistem-iku-kabupaten-pringsewu-cache-admin@gmail.com|127.0.0.1', 'i:1;', 1776951614),
('sistem-iku-kabupaten-pringsewu-cache-admin@gmail.com|127.0.0.1:timer', 'i:1776951614;', 1776951614),
('sistem-iku-kabupaten-pringsewu-cache-admin@ikuproject.test|127.0.0.1', 'i:1;', 1776951705),
('sistem-iku-kabupaten-pringsewu-cache-admin@ikuproject.test|127.0.0.1:timer', 'i:1776951705;', 1776951705),
('sistem-iku-kabupaten-pringsewu-cache-c525a5357e97fef8d3db25841c86da1a', 'i:1;', 1776951613),
('sistem-iku-kabupaten-pringsewu-cache-c525a5357e97fef8d3db25841c86da1a:timer', 'i:1776951613;', 1776951613),
('sistem-iku-kabupaten-pringsewu-cache-ddf34b6f0a8ee8f7bdac8387a4d1d290', 'i:1;', 1776951705),
('sistem-iku-kabupaten-pringsewu-cache-ddf34b6f0a8ee8f7bdac8387a4d1d290:timer', 'i:1776951705;', 1776951705),
('sistem-iku-kabupaten-pringsewu-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:23:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:10:\"kelola-opd\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:15:\"kelola-pengguna\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:14:\"buat-indikator\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:4;i:2;i:6;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"edit-indikator\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:4;i:2;i:6;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"hapus-indikator\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"lihat-indikator\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:16:\"ajukan-indikator\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;i:4;i:6;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:23:\"setujui-indikator-kabag\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:25:\"setujui-indikator-asisten\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:23:\"setujui-indikator-sekda\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:24:\"setujui-indikator-bupati\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:15:\"input-realisasi\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:6;i:2;i:7;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:14:\"edit-realisasi\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:6;i:2;i:7;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:15:\"lihat-realisasi\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:20:\"verifikasi-realisasi\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:3;i:2;i:5;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:10:\"skoring-ai\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:10:\"skoring-ta\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:14:\"skoring-bupati\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:13:\"lihat-skoring\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:7;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:17:\"lihat-laporan-opd\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:4;i:2;i:6;i:3;i:7;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:21:\"lihat-laporan-asisten\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:19:\"lihat-laporan-sekda\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:19:\"lihat-laporan-semua\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}}s:5:\"roles\";a:7:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"admin_super\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:5:\"sekda\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:5:\"kabag\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:12:\"kepala_dinas\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:6:\"bupati\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:7:\"asisten\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:13:\"kepala_bidang\";s:1:\"c\";s:3:\"web\";}}}', 1777038336);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iku_skorings`
--

CREATE TABLE `iku_skorings` (
  `id` bigint UNSIGNED NOT NULL,
  `indikator_id` bigint UNSIGNED NOT NULL,
  `realisasi_id` bigint UNSIGNED DEFAULT NULL,
  `bulan` tinyint NOT NULL,
  `tahun` smallint NOT NULL,
  `skor_ai` tinyint DEFAULT NULL,
  `ai_reasoning` text COLLATE utf8mb4_unicode_ci,
  `ai_generated_at` timestamp NULL DEFAULT NULL,
  `skor_ta` tinyint DEFAULT NULL,
  `ta_notes` text COLLATE utf8mb4_unicode_ci,
  `ta_scored_by` bigint UNSIGNED DEFAULT NULL,
  `ta_scored_at` timestamp NULL DEFAULT NULL,
  `skor_bupati` tinyint DEFAULT NULL,
  `bupati_notes` text COLLATE utf8mb4_unicode_ci,
  `bupati_scored_at` timestamp NULL DEFAULT NULL,
  `is_final` tinyint(1) NOT NULL DEFAULT '0',
  `finalized_by` bigint UNSIGNED DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','ai_done','ta_done','final') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `iku_skorings`
--

INSERT INTO `iku_skorings` (`id`, `indikator_id`, `realisasi_id`, `bulan`, `tahun`, `skor_ai`, `ai_reasoning`, `ai_generated_at`, `skor_ta`, `ta_notes`, `ta_scored_by`, `ta_scored_at`, `skor_bupati`, `bupati_notes`, `bupati_scored_at`, `is_final`, `finalized_by`, `finalized_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 12, 4, 2026, 9, 'Simulasi AI (Fallback): API limit atau error pada model yang dipilih (gemini).', '2026-04-22 05:16:09', 9, '', 1, '2026-04-22 05:16:19', 9, NULL, '2026-04-22 05:16:32', 1, 1, '2026-04-22 05:16:32', 'final', '2026-04-22 05:16:09', '2026-04-22 05:16:32'),
(2, 15, NULL, 4, 2026, 0, NULL, NULL, 0, 'Otomatis tersinkronisasi dari rata-rata capaian Dinas Pendidikan dan Kebudayaan', NULL, NULL, 0, 'Otomatis tersinkronisasi dari rata-rata capaian Dinas Pendidikan dan Kebudayaan', NULL, 1, NULL, '2026-04-22 05:20:16', 'final', '2026-04-22 05:20:16', '2026-04-22 05:20:16'),
(3, 16, NULL, 4, 2026, 1, NULL, NULL, 1, 'Otomatis tersinkronisasi dari rata-rata capaian Dinas Komunikasi dan Informatika', NULL, NULL, 1, 'Otomatis tersinkronisasi dari rata-rata capaian Dinas Komunikasi dan Informatika', NULL, 1, NULL, '2026-04-22 05:20:16', 'final', '2026-04-22 05:20:16', '2026-04-22 05:20:16'),
(4, 2, 8, 4, 2026, 7, 'Simulasi AI (Fallback): API limit atau error pada model yang dipilih (gemini).', '2026-04-22 05:31:18', 7, '', 1, '2026-04-22 05:31:23', 8, NULL, '2026-04-22 05:31:34', 1, 1, '2026-04-22 05:31:34', 'final', '2026-04-22 05:31:18', '2026-04-22 05:31:34'),
(5, 5, 20, 4, 2026, 5, 'Realisasi 40% dari target 55% menunjukkan ketercapaian sekitar 72.7% dari target. Meskipun ada upaya positif berupa pelatihan pengelola data sektoral di 8 OPD yang menunjukkan langkah konkret untuk meningkatkan kualitas data, namun capaian masih di bawah target dengan gap 15 persen poin. Aktivitas pelatihan yang dilakukan memberikan nilai tambah sebagai fondasi peningkatan ke depan, namun belum cukup untuk mendongkrak realisasi mendekati target bulan April.', '2026-04-22 05:33:19', 5, '', 1, '2026-04-22 05:33:38', NULL, NULL, NULL, 0, NULL, NULL, 'ta_done', '2026-04-22 05:33:19', '2026-04-22 05:33:38'),
(6, 4, 16, 4, 2026, 9, 'Simulasi AI (Fallback): API limit atau error pada model yang dipilih (gemini).', '2026-04-22 05:36:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'ai_done', '2026-04-22 05:36:05', '2026-04-22 05:36:05'),
(7, 12, 44, 4, 2026, 6, 'Simulasi AI (Fallback): API limit atau error pada model yang dipilih (gemini).', '2026-04-22 19:09:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 'ai_done', '2026-04-22 19:09:40', '2026-04-22 19:09:40');

-- --------------------------------------------------------

--
-- Table structure for table `indikators`
--

CREATE TABLE `indikators` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun_anggaran_id` bigint UNSIGNED NOT NULL,
  `sekda_id` bigint UNSIGNED NOT NULL,
  `kabag_id` bigint UNSIGNED DEFAULT NULL,
  `asisten_id` bigint UNSIGNED DEFAULT NULL,
  `opd_id` bigint UNSIGNED DEFAULT NULL,
  `bidang_id` bigint UNSIGNED DEFAULT NULL,
  `parent_indikator_id` bigint UNSIGNED DEFAULT NULL,
  `source_indikator_id` bigint UNSIGNED DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('utama','kerjasama') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'utama',
  `definisi` text COLLATE utf8mb4_unicode_ci,
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `measurement_type` enum('kuantitatif','kualitatif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kuantitatif',
  `target` decimal(15,2) NOT NULL DEFAULT '0.00',
  `bobot` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','diajukan','disetujui','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `dibuat_oleh` bigint UNSIGNED NOT NULL,
  `owner_user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `indikators`
--

INSERT INTO `indikators` (`id`, `tahun_anggaran_id`, `sekda_id`, `kabag_id`, `asisten_id`, `opd_id`, `bidang_id`, `parent_indikator_id`, `source_indikator_id`, `nama`, `category`, `definisi`, `satuan`, `measurement_type`, `target`, `bobot`, `status`, `dibuat_oleh`, `owner_user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 6, 3, 5, NULL, NULL, 'Indeks SPBE (Sistem Pemerintahan Berbasis Elektronik)', 'utama', 'Nilai indeks SPBE yang diperoleh dari evaluasi Kemenpan-RB terhadap implementasi sistem pemerintahan berbasis elektronik di lingkungan Pemda.', 'indeks', 'kuantitatif', 2.60, 20.00, 'disetujui', 1, NULL, '2026-04-21 23:46:59', '2026-04-22 02:54:21'),
(2, 1, 1, NULL, 6, 3, 5, NULL, NULL, 'Persentase Layanan Publik Berbasis Digital yang Aktif', 'utama', 'Persentase jumlah layanan publik yang sudah berbasis digital dan aktif digunakan masyarakat dari total layanan publik yang ada.', 'persen', 'kuantitatif', 85.00, 25.00, 'disetujui', 1, NULL, '2026-04-21 23:46:59', '2026-04-22 02:54:21'),
(3, 1, 1, NULL, 6, 3, 4, NULL, NULL, 'Cakupan Desa/Kelurahan dengan Akses Internet Memadai', 'utama', 'Persentase desa/kelurahan yang memiliki infrastruktur jaringan internet dengan kecepatan minimal 25 Mbps untuk mendukung layanan publik digital.', 'persen', 'kuantitatif', 90.00, 20.00, 'disetujui', 1, NULL, '2026-04-21 23:46:59', '2026-04-22 02:54:21'),
(4, 1, 1, NULL, 6, 3, 4, NULL, NULL, 'Persentase Aduan Masyarakat yang Ditindaklanjuti Tepat Waktu', 'utama', 'Persentase pengaduan masyarakat melalui semua kanal (SP4N-LAPOR!, media sosial, website) yang mendapat respons dan tindak lanjut dalam SLA ≤ 5 hari kerja.', 'persen', 'kuantitatif', 90.00, 15.00, 'disetujui', 1, NULL, '2026-04-21 23:46:59', '2026-04-22 02:54:21'),
(5, 1, 1, NULL, 6, 3, 4, NULL, NULL, 'Persentase Data Statistik Sektoral yang Tersedia dan Terpublikasi', 'utama', 'Persentase jenis data statistik sektoral OPD yang telah dikumpulkan, divalidasi, dan dipublikasikan melalui portal data daerah dari total data yang wajib tersedia.', 'persen', 'kuantitatif', 80.00, 10.00, 'disetujui', 1, NULL, '2026-04-21 23:46:59', '2026-04-22 02:54:21'),
(6, 1, 1, NULL, 6, 7, 8, NULL, NULL, 'Persentase Lembaga PAUD yang Melaksanakan Kelas Orang Tua Dini', 'utama', 'Persentase lembaga PAUD (TK/KB/TPA) di Kabupaten Pringsewu yang menyelenggarakan kelas parenting/kelas orang tua minimal 4 kali setahun.', '%', 'kuantitatif', 75.00, 30.00, 'disetujui', 5, 6, '2026-04-21 23:47:00', '2026-04-22 00:27:19'),
(7, 1, 1, NULL, 6, 7, 8, NULL, NULL, 'Angka Partisipasi Kasar (APK) PAUD', 'utama', 'Rasio jumlah siswa PAUD terhadap total penduduk usia 3–6 tahun.', '%', 'kuantitatif', 82.00, 35.00, 'disetujui', 5, 6, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(8, 1, 1, NULL, 6, 7, 9, NULL, NULL, 'Nilai Rata-rata Hasil Asesmen Nasional SD', 'utama', 'Rata-rata nilai Asesmen Nasional jenjang SD/MI se-Kabupaten Pringsewu.', 'Nilai', 'kuantitatif', 72.00, 25.00, 'disetujui', 5, 6, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(9, 1, 1, NULL, 6, 7, 10, NULL, NULL, 'Persentase Guru Bersertifikat Pendidik', 'utama', 'Persentase guru PNS yang telah memiliki sertifikat pendidik.', '%', 'kuantitatif', 88.00, 10.00, 'disetujui', 5, 6, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(10, 1, 1, NULL, 6, 3, 4, NULL, 6, 'Kerjasama: Pelaksanaan Kelas Orang Tua PAUD (Disdik)', 'kerjasama', 'Kominfo berperan dalam sosialisasi dan promosi digital pelaksanaan Kelas Orang Tua di lembaga PAUD. Skor mengikuti capaian Disdik (Bidang PAUD) secara otomatis.', '%', 'kuantitatif', 75.00, 10.00, 'disetujui', 3, 3, '2026-04-21 23:47:00', '2026-04-22 02:54:21'),
(11, 1, 1, 11, 6, 11, NULL, NULL, NULL, 'Persentase Penyelenggaraan Administrasi Pemerintahan Desa yang Tertib', 'utama', 'Persentase desa/kelurahan di Kabupaten Pringsewu yang menyelenggarakan administrasi pemerintahan sesuai ketentuan peraturan perundang-undangan (laporan tepat waktu, APBDes tersusun, monografi desa terbarui).', '%', 'kuantitatif', 90.00, 10.00, 'disetujui', 1, 8, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(12, 1, 1, 12, 6, 12, NULL, NULL, NULL, 'Persentase Usulan Bantuan Sosial yang Diproses Sesuai SOP', 'utama', 'Persentase usulan bantuan sosial (beasiswa, bansos, PKH, dll.) yang masuk ke Bagian Kesra dan diproses verifikasi-rekomendasi tepat waktu sesuai SOP ≤ 10 hari kerja.', '%', 'kuantitatif', 88.00, 10.00, 'disetujui', 1, 9, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(13, 1, 1, 13, 6, 13, NULL, NULL, NULL, 'Persentase Produk Hukum Daerah yang Ditetapkan Tepat Waktu', 'utama', 'Persentase rancangan produk hukum daerah (Perda, Perbup, SK Bupati) yang berhasil ditetapkan sesuai jadwal Program Legislasi Daerah tahun berjalan.', '%', 'kuantitatif', 85.00, 10.00, 'disetujui', 1, 10, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(14, 1, 1, NULL, 6, 6, NULL, NULL, NULL, 'Indeks Koordinasi Asisten I dengan OPD Binaan', 'utama', 'Indeks koordinasi yang diukur berdasarkan frekuensi rapat koordinasi, tindak lanjut rekomendasi, dan tingkat responsivitas OPD binaan terhadap arahan Asisten I.', 'indeks', 'kualitatif', 80.00, 10.00, 'disetujui', 1, 7, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(15, 1, 1, NULL, 6, 6, NULL, NULL, NULL, '[Kontribusi] Capaian Rata-rata IKU Dinas Pendidikan dan Kebudayaan', 'utama', 'Rata-rata persentase capaian seluruh IKU Dinas Pendidikan dan Kebudayaan yang menjadi tanggung jawab koordinasi Asisten I. Skor ini otomatis diambil dari rata-rata tertimbang IKU Disdik yang telah difinalisasi.', '%', 'kuantitatif', 90.00, 35.00, 'disetujui', 1, 7, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(16, 1, 1, NULL, 6, 6, NULL, NULL, NULL, '[Kontribusi] Capaian Program Digitalisasi Pemerintahan & Kesra (Diskominfo)', 'utama', 'Persentase capaian program digitalisasi layanan pemerintahan dan kesejahteraan rakyat yang dikoordinasikan Asisten I bersama Dinas Komunikasi dan Informatika, mencakup e-government desa, sistem data sosial terpadu, dan digitalisasi administrasi kependudukan.', '%', 'kuantitatif', 85.00, 25.00, 'disetujui', 1, 7, '2026-04-22 00:42:18', '2026-04-22 00:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `indikator_kerjasamas`
--

CREATE TABLE `indikator_kerjasamas` (
  `id` bigint UNSIGNED NOT NULL,
  `indikator_id` bigint UNSIGNED NOT NULL,
  `sekda_id` bigint UNSIGNED DEFAULT NULL,
  `kabag_id` bigint UNSIGNED DEFAULT NULL,
  `asisten_id` bigint UNSIGNED DEFAULT NULL,
  `opd_id` bigint UNSIGNED NOT NULL,
  `bidang_id` bigint UNSIGNED DEFAULT NULL,
  `owner_user_id` bigint UNSIGNED DEFAULT NULL,
  `peran` text COLLATE utf8mb4_unicode_ci,
  `bobot` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','diajukan','disetujui','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `dibuat_oleh` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `indikator_kerjasamas`
--

INSERT INTO `indikator_kerjasamas` (`id`, `indikator_id`, `sekda_id`, `kabag_id`, `asisten_id`, `opd_id`, `bidang_id`, `owner_user_id`, `peran`, `bobot`, `status`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES
(1, 6, 1, NULL, 2, 3, 4, 3, 'Kominfo berperan dalam sosialisasi dan promosi digital pelaksanaan Kelas Orang Tua di lembaga PAUD. Skor mengikuti capaian Disdik (Bidang PAUD) secara otomatis.', 10.00, 'disetujui', 3, '2026-04-21 23:47:00', '2026-04-21 23:47:00');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_14_170933_add_two_factor_columns_to_users_table', 1),
(5, '2026_04_22_030004_create_opds_table', 1),
(6, '2026_04_22_030004_create_tahun_anggaran_table', 1),
(7, '2026_04_22_030013_add_role_opd_to_users_table', 1),
(8, '2026_04_22_030014_create_indikators_table', 1),
(9, '2026_04_22_030014_create_realisasi_table', 1),
(10, '2026_04_22_030014_create_target_indikators_table', 1),
(11, '2026_04_22_030015_create_persetujuan_table', 1),
(12, '2026_04_22_030210_create_rekap_capaian_table', 1),
(13, '2026_04_22_032552_create_permission_tables', 1),
(14, '2026_04_22_033146_add_username_to_users_table', 1),
(15, '2026_04_22_044924_add_measurement_type_and_owner_to_indikators_table', 1),
(16, '2026_04_22_044925_add_target_description_to_target_indikators_table', 1),
(17, '2026_04_22_044925_create_iku_skorings_table', 1),
(18, '2026_04_22_053131_create_monthly_summaries_table', 1),
(19, '2026_04_22_053131_create_settings_table', 1),
(20, '2026_04_22_053131_create_wa_logs_table', 1),
(21, '2026_04_22_053132_add_category_source_to_indikators_table', 1),
(22, '2026_04_22_054850_create_notifications_table', 1),
(23, '2026_04_22_060100_create_indikator_kerjasamas_table', 2),
(24, '2026_04_22_080000_add_bukti_dukung_to_realisasi_table', 3),
(25, '2026_04_22_102937_add_phone_to_users_table', 4),
(26, '2026_04_23_132425_add_must_change_password_to_users_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(6, 'App\\Models\\User', 2),
(7, 'App\\Models\\User', 3),
(7, 'App\\Models\\User', 4),
(6, 'App\\Models\\User', 5),
(7, 'App\\Models\\User', 6),
(5, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 8),
(4, 'App\\Models\\User', 9),
(4, 'App\\Models\\User', 10),
(1, 'App\\Models\\User', 11),
(2, 'App\\Models\\User', 12);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_summaries`
--

CREATE TABLE `monthly_summaries` (
  `id` bigint UNSIGNED NOT NULL,
  `opd_id` bigint UNSIGNED NOT NULL,
  `bulan` tinyint NOT NULL,
  `tahun` smallint NOT NULL,
  `skor_utama` decimal(5,2) DEFAULT NULL,
  `skor_kerjasama` decimal(5,2) DEFAULT NULL,
  `skor_total` decimal(5,2) DEFAULT NULL,
  `is_complete` tinyint(1) NOT NULL DEFAULT '0',
  `calculated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monthly_summaries`
--

INSERT INTO `monthly_summaries` (`id`, `opd_id`, `bulan`, `tahun`, `skor_utama`, `skor_kerjasama`, `skor_total`, `is_complete`, `calculated_at`, `created_at`, `updated_at`) VALUES
(1, 3, 4, 2026, 2.00, 0.00, 1.40, 0, '2026-04-22 05:20:16', '2026-04-22 00:06:41', '2026-04-22 05:20:16'),
(2, 7, 4, 2026, 0.00, NULL, 0.00, 0, '2026-04-22 05:20:16', '2026-04-22 00:06:41', '2026-04-22 05:20:16'),
(3, 6, 4, 2026, 0.36, NULL, 0.36, 0, '2026-04-22 05:20:16', '2026-04-22 00:45:01', '2026-04-22 05:20:16'),
(4, 11, 4, 2026, 0.00, NULL, 0.00, 0, '2026-04-22 05:20:16', '2026-04-22 00:45:01', '2026-04-22 05:20:16'),
(5, 12, 4, 2026, 0.00, NULL, 0.00, 0, '2026-04-22 05:20:16', '2026-04-22 00:45:01', '2026-04-22 05:20:16'),
(6, 13, 4, 2026, 0.00, NULL, 0.00, 0, '2026-04-22 05:20:16', '2026-04-22 00:45:01', '2026-04-22 05:20:16'),
(7, 1, 4, 2026, 0.36, NULL, 0.36, 0, '2026-04-22 05:20:16', '2026-04-22 02:42:04', '2026-04-22 05:20:16');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('15fc7585-3e21-44e2-81eb-8f12185d8a71', 'App\\Notifications\\SkorBupatiFinalisasi', 'App\\Models\\User', 6, '{\"type\":\"bupati_finalize\",\"indikator_id\":6,\"indikator_nama\":\"Persentase Lembaga PAUD yang Melaksanakan Kelas Orang Tua Dini\",\"skor_final\":9,\"bulan\":4,\"tahun\":2026,\"message\":\"Bupati memfinalisasi skor: Persentase Lembaga PAUD yang Melaksanakan Kelas Orang Tua Dini \\u2014 Skor 9\\/10\"}', NULL, '2026-04-22 01:14:08', '2026-04-22 01:14:08'),
('19f49454-d16d-47eb-aee4-31f4115d0e3a', 'App\\Notifications\\SkorBupatiFinalisasi', 'App\\Models\\User', 3, '{\"type\":\"bupati_finalize\",\"indikator_id\":6,\"indikator_nama\":\"Persentase Lembaga PAUD yang Melaksanakan Kelas Orang Tua Dini\",\"skor_final\":9,\"bulan\":4,\"tahun\":2026,\"message\":\"Bupati memfinalisasi skor: Persentase Lembaga PAUD yang Melaksanakan Kelas Orang Tua Dini \\u2014 Skor 9\\/10\"}', NULL, '2026-04-22 01:14:08', '2026-04-22 01:14:08'),
('229aabe4-b978-4ffd-bc66-e77540707238', 'App\\Notifications\\SkorBupatiFinalisasi', 'App\\Models\\User', 4, '{\"type\":\"bupati_finalize\",\"indikator_id\":2,\"indikator_nama\":\"Persentase Layanan Publik Berbasis Digital yang Aktif\",\"skor_final\":8,\"bulan\":4,\"tahun\":2026,\"message\":\"Bupati memfinalisasi skor: Persentase Layanan Publik Berbasis Digital yang Aktif \\u2014 Skor 8\\/10\"}', NULL, '2026-04-22 05:31:34', '2026-04-22 05:31:34'),
('4ff80508-51b8-480f-a0a3-d4df520336b2', 'App\\Notifications\\SkorBupatiFinalisasi', 'App\\Models\\User', 2, '{\"type\":\"bupati_finalize\",\"indikator_id\":2,\"indikator_nama\":\"Persentase Layanan Publik Berbasis Digital yang Aktif\",\"skor_final\":8,\"bulan\":4,\"tahun\":2026,\"message\":\"Bupati memfinalisasi skor: Persentase Layanan Publik Berbasis Digital yang Aktif \\u2014 Skor 8\\/10\"}', NULL, '2026-04-22 05:31:34', '2026-04-22 05:31:34'),
('94f8c999-1607-440a-9309-baad855c7344', 'App\\Notifications\\SkorBupatiFinalisasi', 'App\\Models\\User', 1, '{\"type\":\"bupati_finalize\",\"indikator_id\":3,\"indikator_nama\":\"Cakupan Desa\\/Kelurahan dengan Akses Internet Memadai\",\"skor_final\":9,\"bulan\":4,\"tahun\":2026,\"message\":\"Bupati memfinalisasi skor: Cakupan Desa\\/Kelurahan dengan Akses Internet Memadai \\u2014 Skor 9\\/10\"}', NULL, '2026-04-22 05:16:32', '2026-04-22 05:16:32'),
('a1b08e04-74ec-4fd6-8990-cf4a07cf7d50', 'App\\Notifications\\SkorBupatiFinalisasi', 'App\\Models\\User', 1, '{\"type\":\"bupati_finalize\",\"indikator_id\":1,\"indikator_nama\":\"Indeks SPBE (Sistem Pemerintahan Berbasis Elektronik)\",\"skor_final\":9,\"bulan\":4,\"tahun\":2026,\"message\":\"Bupati memfinalisasi skor: Indeks SPBE (Sistem Pemerintahan Berbasis Elektronik) \\u2014 Skor 9\\/10\"}', NULL, '2026-04-22 04:46:46', '2026-04-22 04:46:46'),
('e6d92213-3d43-48b4-bfd0-416c01931b29', 'App\\Notifications\\SkorBupatiFinalisasi', 'App\\Models\\User', 6, '{\"type\":\"bupati_finalize\",\"indikator_id\":7,\"indikator_nama\":\"Angka Partisipasi Kasar (APK) PAUD\",\"skor_final\":7,\"bulan\":4,\"tahun\":2026,\"message\":\"Bupati memfinalisasi skor: Angka Partisipasi Kasar (APK) PAUD \\u2014 Skor 7\\/10\"}', NULL, '2026-04-22 05:13:52', '2026-04-22 05:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `opds`
--

CREATE TABLE `opds` (
  `id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('sekda','asisten','kabag','opd','bidang') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opds`
--

INSERT INTO `opds` (`id`, `parent_id`, `name`, `code`, `type`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Sekretariat Daerah', 'SEKDA', 'sekda', '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(2, 1, 'Asisten III - Administrasi Umum', 'ASISTEN-III', 'asisten', '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(3, 6, 'Dinas Komunikasi dan Informatika', 'DISKOMINFO', 'opd', '2026-04-21 23:46:58', '2026-04-22 00:44:40'),
(4, 3, 'Bidang IKP dan Statistik Sektoral', 'DISKOMINFO-IKP', 'bidang', '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(5, 3, 'Bidang Tata Kelola SPBE dan Persandian', 'DISKOMINFO-SPBE', 'bidang', '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(6, 1, 'Asisten I (Pemerintahan & Kesra)', 'ASISTEN-I', 'asisten', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(7, 6, 'Dinas Pendidikan dan Kebudayaan', 'DISDIK', 'opd', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(8, 7, 'Bidang PAUD dan Dikmas', 'DISDIK-PAUD', 'bidang', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(9, 7, 'Bidang Pembinaan Dikdas & Dikmen', 'DISDIK-DASMEN', 'bidang', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(10, 7, 'Bidang Pendidik & Tenaga Kependidikan', 'DISDIK-GTK', 'bidang', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(11, 6, 'Bagian Tata Pemerintahan', 'KABAG-TAPEM', 'kabag', '2026-04-22 00:42:17', '2026-04-22 00:42:17'),
(12, 6, 'Bagian Kesejahteraan Rakyat', 'KABAG-KESRA', 'kabag', '2026-04-22 00:42:17', '2026-04-22 00:42:17'),
(13, 6, 'Bagian Hukum', 'KABAG-HUKUM', 'kabag', '2026-04-22 00:42:17', '2026-04-22 00:42:17');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'kelola-opd', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(2, 'kelola-pengguna', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(3, 'buat-indikator', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(4, 'edit-indikator', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(5, 'hapus-indikator', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(6, 'lihat-indikator', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(7, 'ajukan-indikator', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(8, 'setujui-indikator-kabag', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(9, 'setujui-indikator-asisten', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(10, 'setujui-indikator-sekda', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(11, 'setujui-indikator-bupati', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(12, 'input-realisasi', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(13, 'edit-realisasi', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(14, 'lihat-realisasi', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(15, 'verifikasi-realisasi', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(16, 'skoring-ai', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(17, 'skoring-ta', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(18, 'skoring-bupati', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(19, 'lihat-skoring', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(20, 'lihat-laporan-opd', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(21, 'lihat-laporan-asisten', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(22, 'lihat-laporan-sekda', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(23, 'lihat-laporan-semua', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57');

-- --------------------------------------------------------

--
-- Table structure for table `persetujuan`
--

CREATE TABLE `persetujuan` (
  `id` bigint UNSIGNED NOT NULL,
  `indikator_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `level` enum('kabag','asisten','sekda','bupati') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','disetujui','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `realisasi`
--

CREATE TABLE `realisasi` (
  `id` bigint UNSIGNED NOT NULL,
  `indikator_id` bigint UNSIGNED NOT NULL,
  `bulan` tinyint NOT NULL,
  `nilai` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `deskripsi_progres` text COLLATE utf8mb4_unicode_ci,
  `bukti_link` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto_bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `status` enum('draft','diajukan','diverifikasi') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `realisasi`
--

INSERT INTO `realisasi` (`id`, `indikator_id`, `bulan`, `nilai`, `keterangan`, `deskripsi_progres`, `bukti_link`, `foto_bukti`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1.82, 'Evaluasi internal SPBE Q1 selesai, skor meningkat dari baseline 1.75.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(2, 1, 2, 1.95, 'Implementasi SSO untuk 3 aplikasi daerah berhasil.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(3, 1, 3, 2.05, 'Integrasi data kependudukan dengan aplikasi layanan publik rampung.', NULL, NULL, NULL, 3, 'diajukan', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(4, 1, 4, 2.08, 'Pembangunan data center tahap I selesai, integrasi layanan masih berjalan.', NULL, NULL, NULL, 3, 'diajukan', '2026-04-21 23:46:59', '2026-04-22 00:24:11'),
(5, 2, 1, 57.00, 'Digitalisasi 12 layanan baru selesai, total 57% dari 89 layanan aktif digital.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(6, 2, 2, 61.00, 'Penambahan 4 layanan digital baru termasuk izin usaha dan akta kelahiran online.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(7, 2, 3, 64.00, 'Layanan pengaduan SP4N-LAPOR! terintegrasi, 3 layanan lama masih migrasi.', NULL, NULL, NULL, 3, 'diajukan', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(8, 2, 4, 30.00, 'Terdapat 1 layanan digital yang mengalami gangguan teknis sehingga realisasi turun tipis.', NULL, NULL, NULL, 3, 'draft', '2026-04-21 23:46:59', '2026-04-22 00:20:18'),
(9, 3, 1, 66.00, 'Pemasangan tower BTS baru di 5 desa terpencil selesai.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(10, 3, 2, 70.00, 'Program BAKTI Kominfo berhasil menjangkau 8 desa tambahan.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(11, 3, 3, 69.00, 'Kerusakan kabel optik di 2 kecamatan menyebabkan sedikit penurunan cakupan.', NULL, NULL, NULL, 3, 'diajukan', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(12, 3, 4, 60.00, 'Perbaikan dan perluasan jaringan serat optik Q2 selesai lebih awal dari jadwal.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-22 00:49:13'),
(13, 4, 1, 77.00, '154 dari 200 aduan ditindaklanjuti tepat waktu, 46 tertunda karena koordinasi lintas OPD.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(14, 4, 2, 82.00, 'Implementasi sistem tiket otomatis berhasil mempercepat distribusi aduan.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(15, 4, 3, 88.00, 'Pencapaian terbaik — koordinasi lintas OPD semakin efektif pasca rapat evaluasi.', NULL, NULL, NULL, 3, 'diajukan', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(16, 4, 4, 70.00, 'Lonjakan aduan terkait layanan kependudukan pasca lebaran, respons sedikit melambat.', NULL, NULL, NULL, 3, 'diajukan', '2026-04-21 23:46:59', '2026-04-22 00:24:18'),
(17, 5, 1, 38.00, 'Pengumpulan data sektoral Q1 masih berjalan, beberapa OPD belum melengkapi formulir.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(18, 5, 2, 46.00, 'Rapat koordinasi data sektoral dengan 12 OPD berhasil mendorong penyerahan data.', NULL, NULL, NULL, 3, 'diverifikasi', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(19, 5, 3, 52.00, 'Portal open data diluncurkan, 52% data sudah terpublikasi.', NULL, NULL, NULL, 3, 'diajukan', '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(20, 5, 4, 40.00, 'Pelatihan pengelola data sektoral di 8 OPD meningkatkan kualitas dan kelengkapan data.', NULL, NULL, NULL, 3, 'diajukan', '2026-04-21 23:46:59', '2026-04-22 00:24:22'),
(21, 6, 1, 47.50, NULL, NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(22, 6, 2, 51.20, NULL, NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(23, 6, 3, 54.00, NULL, NULL, NULL, NULL, 5, 'diajukan', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(24, 6, 4, 50.00, '', NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-22 01:12:49'),
(25, 7, 1, 71.10, NULL, NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(26, 7, 2, 72.80, NULL, NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(27, 7, 3, 73.50, NULL, NULL, NULL, NULL, 5, 'diajukan', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(28, 7, 4, 50.00, '', NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-22 00:49:08'),
(29, 8, 1, 64.50, NULL, NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(30, 8, 2, 66.10, NULL, NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(31, 8, 3, 67.00, NULL, NULL, NULL, NULL, 5, 'diajukan', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(32, 8, 4, 68.00, NULL, NULL, NULL, NULL, 5, 'diajukan', '2026-04-21 23:47:00', '2026-04-22 00:24:15'),
(33, 9, 1, 81.00, NULL, NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(34, 9, 2, 82.00, NULL, NULL, NULL, NULL, 5, 'diverifikasi', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(35, 9, 3, 83.00, NULL, NULL, NULL, NULL, 5, 'diajukan', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(36, 9, 4, 80.00, '', NULL, NULL, NULL, 5, 'diajukan', '2026-04-21 23:47:00', '2026-04-22 00:51:27'),
(37, 11, 1, 76.50, 'Monitoring administrasi 126 desa selesai, 97 desa tertib laporan.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(38, 11, 2, 79.20, 'APBDes 2026 seluruh desa selesai ditetapkan, 3 desa terlambat.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(39, 11, 3, 81.00, 'Bimtek administrasi pemerintahan desa diikuti 134 perangkat desa.', NULL, NULL, NULL, 7, 'diajukan', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(40, 11, 4, 83.50, 'Persentase naik signifikan pasca evaluasi Q1, 105 dari 126 desa tertib.', NULL, NULL, NULL, 7, 'draft', '2026-04-22 00:42:18', '2026-04-22 02:23:26'),
(41, 12, 1, 72.00, '144 dari 200 usulan bansos diproses tepat waktu. 56 terlambat karena verifikasi lapangan.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(42, 12, 2, 75.50, 'Implementasi sistem digitalisasi usulan bansos mempercepat proses verifikasi.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(43, 12, 3, 78.00, 'Sinkronisasi data dengan Dinsos berhasil mengurangi duplikasi penerima.', NULL, NULL, NULL, 7, 'diajukan', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(44, 12, 4, 80.50, 'Pencairan PKH Q1 selesai 100%, usulan beasiswa PPDB 2026 sudah diproses.', NULL, NULL, NULL, 7, 'draft', '2026-04-22 00:42:18', '2026-04-22 02:23:26'),
(45, 13, 1, 61.00, '11 dari 18 Perda target Q1 sudah masuk tahap pembahasan DPRD.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(46, 13, 2, 66.00, '3 Perda berhasil ditetapkan, harmonisasi 5 Perbup selesai.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(47, 13, 3, 69.50, 'Produk hukum meningkat, meski 2 Perda ditunda karena revisi substansi.', NULL, NULL, NULL, 7, 'diajukan', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(48, 13, 4, 72.00, 'Target Q2 on-track: 4 Perda lagi dijadwalkan Mei 2026.', NULL, NULL, NULL, 7, 'draft', '2026-04-22 00:42:18', '2026-04-22 02:23:26'),
(49, 14, 1, 66.00, '8 rapat koordinasi terlaksana, tindak lanjut 72% rekomendasi.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(50, 14, 2, 68.50, 'Rapat koordinasi rutin 2x/bulan berjalan. Responsivitas OPD meningkat.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(51, 14, 3, 70.00, 'Evaluasi triwulan: semua OPD binaan hadir, tindak lanjut 78% rekomendasi.', NULL, NULL, NULL, 7, 'diajukan', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(52, 14, 4, 72.50, 'Koordinasi persiapan PPDB dan penyaluran bansos berjalan baik.', NULL, NULL, NULL, 7, 'draft', '2026-04-22 00:42:18', '2026-04-22 02:23:26'),
(53, 15, 1, 76.80, 'Rata-rata capaian IKU Disdik Jan 2026 — APK PAUD dan nilai UN menunjukkan tren positif.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(54, 15, 2, 78.30, 'Capaian Disdik meningkat, program PAUD berkualitas berjalan baik.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(55, 15, 3, 79.50, 'Bimtek guru GTK selesai, dampak positif pada sertifikasi.', NULL, NULL, NULL, 7, 'diajukan', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(56, 15, 4, 81.10, 'Rata-rata capaian 4 IKU Disdik Apr 2026: APK 74.2%, Kelas Ortu 57.3%, Nilai UN 68.0, Guru Sertif 83.5%.', NULL, NULL, NULL, 7, 'draft', '2026-04-22 00:42:18', '2026-04-22 02:23:26'),
(57, 16, 1, 57.50, 'Digitalisasi layanan desa baru dimulai. e-Monografi desa 62% sudah online.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(58, 16, 2, 62.00, 'Sistem aduan digital desa aktif di 45 desa (36%). Integrasi data sosial berjalan.', NULL, NULL, NULL, 7, 'diverifikasi', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(59, 16, 3, 65.00, 'Pelatihan PPID desa selesai, 65% desa sudah punya web desa aktif.', NULL, NULL, NULL, 7, 'diajukan', '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(60, 16, 4, 67.50, 'Target Q2 digitalisasi desa on-track. SPBE mendukung layanan bansos digital.', NULL, NULL, NULL, 7, 'draft', '2026-04-22 00:42:18', '2026-04-22 02:23:26');

-- --------------------------------------------------------

--
-- Table structure for table `rekap_capaian`
--

CREATE TABLE `rekap_capaian` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun_anggaran_id` bigint UNSIGNED NOT NULL,
  `opd_id` bigint UNSIGNED NOT NULL,
  `level` enum('bidang','opd','asisten','kabag','sekda') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bulan` tinyint NOT NULL,
  `total_target` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_realisasi` decimal(15,2) NOT NULL DEFAULT '0.00',
  `persentase` decimal(5,2) NOT NULL DEFAULT '0.00',
  `jumlah_indikator` int NOT NULL DEFAULT '0',
  `indikator_tercapai` int NOT NULL DEFAULT '0',
  `dihitung_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rekap_capaian`
--

INSERT INTO `rekap_capaian` (`id`, `tahun_anggaran_id`, `opd_id`, `level`, `bulan`, `total_target`, `total_realisasi`, `persentase`, `jumlah_indikator`, `indikator_tercapai`, `dihitung_pada`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'sekda', 4, 1008.10, 907.18, 89.99, 15, 0, '2026-04-23 07:07:09', '2026-04-22 00:18:49', '2026-04-23 07:07:09'),
(2, 1, 2, 'asisten', 4, 277.10, 202.08, 72.93, 5, 0, '2026-04-22 01:17:55', '2026-04-22 00:18:49', '2026-04-22 01:17:55'),
(3, 1, 3, 'opd', 4, 277.10, 202.08, 72.93, 5, 0, '2026-04-23 07:07:09', '2026-04-22 00:18:49', '2026-04-23 07:07:09'),
(4, 1, 4, 'bidang', 4, 210.00, 170.00, 80.95, 3, 0, '2026-04-22 00:25:47', '2026-04-22 00:18:49', '2026-04-22 00:25:47'),
(5, 1, 5, 'bidang', 4, 67.10, 32.08, 47.81, 2, 0, '2026-04-22 00:25:47', '2026-04-22 00:18:49', '2026-04-22 00:25:47'),
(6, 1, 6, 'asisten', 4, 1008.10, 907.18, 89.99, 15, 0, '2026-04-23 07:07:09', '2026-04-22 00:18:49', '2026-04-23 07:07:09'),
(7, 1, 7, 'opd', 4, 283.00, 248.00, 87.63, 4, 0, '2026-04-23 07:07:09', '2026-04-22 00:18:49', '2026-04-23 07:07:09'),
(8, 1, 8, 'bidang', 4, 132.00, 100.00, 75.76, 2, 0, '2026-04-22 00:25:47', '2026-04-22 00:18:49', '2026-04-22 00:25:47'),
(9, 1, 9, 'bidang', 4, 68.00, 68.00, 100.00, 1, 0, '2026-04-22 00:25:47', '2026-04-22 00:18:49', '2026-04-22 00:25:47'),
(10, 1, 10, 'bidang', 4, 83.00, 80.00, 96.39, 1, 0, '2026-04-22 00:25:47', '2026-04-22 00:18:49', '2026-04-22 00:25:47'),
(11, 1, 11, 'kabag', 4, 82.00, 83.50, 101.83, 1, 0, '2026-04-23 07:07:09', '2026-04-22 00:46:03', '2026-04-23 07:07:09'),
(12, 1, 12, 'kabag', 4, 78.00, 80.50, 103.21, 1, 0, '2026-04-23 07:07:09', '2026-04-22 00:46:03', '2026-04-23 07:07:09'),
(13, 1, 13, 'kabag', 4, 70.00, 72.00, 102.86, 1, 0, '2026-04-23 07:07:09', '2026-04-22 00:46:03', '2026-04-23 07:07:09');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin_super', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(2, 'bupati', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(3, 'sekda', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(4, 'kabag', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(5, 'asisten', 'web', '2026-04-21 23:46:57', '2026-04-21 23:46:57'),
(6, 'kepala_dinas', 'web', '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(7, 'kepala_bidang', 'web', '2026-04-21 23:46:58', '2026-04-21 23:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(6, 2),
(11, 2),
(14, 2),
(18, 2),
(19, 2),
(23, 2),
(1, 3),
(6, 3),
(7, 3),
(10, 3),
(14, 3),
(15, 3),
(22, 3),
(23, 3),
(3, 4),
(4, 4),
(6, 4),
(7, 4),
(8, 4),
(14, 4),
(20, 4),
(6, 5),
(7, 5),
(9, 5),
(14, 5),
(15, 5),
(21, 5),
(3, 6),
(4, 6),
(6, 6),
(7, 6),
(12, 6),
(13, 6),
(14, 6),
(20, 6),
(6, 7),
(12, 7),
(13, 7),
(14, 7),
(19, 7),
(20, 7);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4m67Gp0sraHhFyC7NwLx7BjxGXdz6nyCJNfYjcjd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJDRXEyZVlzN2dmUHNxOEc0Vm5yUG96M1VRNmdCa3lEenNGUk44SEU4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1776910629),
('exxHXNd2bpsmyWv0K0w7rpgMnWrRVkkE8Hk5XIl6', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJsTmpoMXBCNHZ0MFlwa2dEOUp0QnV0Qk5LMUY4dlZvUmxmYzZnTlBwIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1776953457),
('gvwrh5Rou1WsD4YcFMbhx1lT4QYozLfBX6tWifC8', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJOZUVSN09pTktxTGtBTHlVR0ZDcTlRTW9MTHBWcG9aY2U2bHgyMUh6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9yZWFsaXNhc2kiLCJyb3V0ZSI6InJlYWxpc2FzaS5pbmRleCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxfQ==', 1776983860),
('PS7JScetKImt5nlYHoX3F8U4NYU2vqii3sXiK2U5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJLODZhbjJzZHYxdFhMdWhYTzA1aWhVaTM5UzNhWFBSZ2pvVk9Oek8zIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImhvbWUifX0=', 1776910475),
('vr8BSvb1rAUPEh2aandWNNJZzblaIPSdpGe3ltHT', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', 'eyJfdG9rZW4iOiJmYXh6VEpldmk4TnZzdFZ4N043OXVZckdhMWJLaGVCWVBUN2h4VFQ5IiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9za29yaW5nLWJ1cGF0aSIsInJvdXRlIjoic2tvcmluZy1idXBhdGkuaW5kZXgifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MX0=', 1776861767),
('yEHPNTBA5KgyWhKhkaAFrny3TK3FMxTbbXCJoLQj', 11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIzSDdvbVh0WDZ4eWoyUmRYekZ6anllek5rV0twQ0RWTTBDbjN0dkJnIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sInVybCI6W10sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxMX0=', 1776854175);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` enum('boolean','string','integer','json','encrypted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'ai_enabled', '1', 'boolean', 'ai', 'AI Scoring Aktif', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(2, 'ai_api_key', 'eyJpdiI6Im5XZHJpa2Ruc2ZYV21WaWxENDJOWHc9PSIsInZhbHVlIjoiQm5WZWZPc3A2V2tra2FDcG1jRm9TT2NwenpLQjBQOTk4N2lyTWQ3WXZVcUVkZ3ZFV29WbVdaSW9NMkluNmhXY1JQTS9ja3lyS1RuMGlrUEVMQmhmUmswUFgvNmRXalZkdFFtMk5hNzFYY3pxSldaQUZUUGE1VkoxeWxqZ1dPQTVzam82clRGeGRxWWQ4WkdFRS9pQkZRPT0iLCJtYWMiOiI0ZjY0MTA0ZWNmMmE3MmU1NjRlY2IwZmJkMGEyYTIzZjljOTg1ZTlkZGNmMmYwNjk3Mjc3ZWU3NGIzZDZiYThkIiwidGFnIjoiIn0=', 'encrypted', 'ai', 'Anthropic API Key', NULL, '2026-04-21 23:46:58', '2026-04-22 01:10:22'),
(3, 'ai_model', 'claude-sonnet-4-6', 'string', 'ai', 'Model AI', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(4, 'ai_auto_trigger', '1', 'boolean', 'ai', 'AI Auto Trigger', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(5, 'wa_enabled', '1', 'boolean', 'whatsapp', 'WhatsApp Aktif', NULL, '2026-04-21 23:46:58', '2026-04-22 00:30:33'),
(6, 'wa_api_key', 'eyJpdiI6IlJtOXlaZVAwTmZsYnVKclZnR21QRUE9PSIsInZhbHVlIjoiUEZVWWE4b3B5akJzV3ROQm8rcWFHU1h4amRTbExtY1cvQ2FWRnJUNEtKTT0iLCJtYWMiOiJiYzI4ZmZkYzliOWMyNzRjYTk3YWI1Zjg5OWMxZDc2ZjQyNzhmNzIwOWVmZDcwYTBiNmZhZWE5YjJmZDE0YjA4IiwidGFnIjoiIn0=', 'encrypted', 'whatsapp', 'Fonnte API Key', NULL, '2026-04-21 23:46:58', '2026-04-22 01:10:22'),
(7, 'wa_sender_number', '6285600832836', 'string', 'whatsapp', 'Nomor Pengirim WA', NULL, '2026-04-21 23:46:58', '2026-04-22 00:30:33'),
(8, 'wa_reminder_enabled', '1', 'boolean', 'whatsapp', 'Reminder WA Aktif', NULL, '2026-04-21 23:46:58', '2026-04-22 00:30:33'),
(9, 'wa_reminder_day', '25', 'integer', 'whatsapp', 'Hari Kirim Reminder', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(10, 'active_year', '2026', 'integer', 'general', 'Tahun Aktif', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(11, 'current_scoring_month', '4', 'integer', 'general', 'Bulan Skoring Aktif', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(12, 'submission_deadline_day', '5', 'integer', 'general', 'Deadline Input (tgl)', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(13, 'app_name', 'Sistem IKU Pringsewu', 'string', 'general', 'Nama Aplikasi', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(14, 'opd_can_see_own_score', '0', 'boolean', 'general', 'OPD Dapat Lihat Skor Sendiri', NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `tahun_anggaran`
--

CREATE TABLE `tahun_anggaran` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun` smallint NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tahun_anggaran`
--

INSERT INTO `tahun_anggaran` (`id`, `tahun`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2026, 1, '2026-04-21 23:46:58', '2026-04-21 23:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `target_indikators`
--

CREATE TABLE `target_indikators` (
  `id` bigint UNSIGNED NOT NULL,
  `indikator_id` bigint UNSIGNED NOT NULL,
  `bulan` tinyint NOT NULL,
  `target` decimal(15,2) NOT NULL DEFAULT '0.00',
  `target_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `target_indikators`
--

INSERT INTO `target_indikators` (`id`, `indikator_id`, `bulan`, `target`, `target_description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1.80, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(2, 1, 2, 1.90, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(3, 1, 3, 2.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(4, 1, 4, 2.10, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(5, 1, 5, 2.20, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(6, 1, 6, 2.30, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(7, 1, 7, 2.35, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(8, 1, 8, 2.40, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(9, 1, 9, 2.45, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(10, 1, 10, 2.50, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(11, 1, 11, 2.55, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(12, 1, 12, 2.60, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(13, 2, 1, 55.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(14, 2, 2, 58.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(15, 2, 3, 62.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(16, 2, 4, 65.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(17, 2, 5, 68.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(18, 2, 6, 70.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(19, 2, 7, 73.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(20, 2, 8, 75.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(21, 2, 9, 78.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(22, 2, 10, 80.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(23, 2, 11, 83.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(24, 2, 12, 85.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(25, 3, 1, 65.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(26, 3, 2, 68.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(27, 3, 3, 70.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(28, 3, 4, 73.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(29, 3, 5, 75.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(30, 3, 6, 78.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(31, 3, 7, 80.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(32, 3, 8, 82.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(33, 3, 9, 84.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(34, 3, 10, 86.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(35, 3, 11, 88.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(36, 3, 12, 90.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(37, 4, 1, 75.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(38, 4, 2, 78.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(39, 4, 3, 80.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(40, 4, 4, 82.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(41, 4, 5, 83.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(42, 4, 6, 85.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(43, 4, 7, 86.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(44, 4, 8, 87.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(45, 4, 9, 88.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(46, 4, 10, 89.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(47, 4, 11, 90.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(48, 4, 12, 90.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(49, 5, 1, 40.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(50, 5, 2, 45.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(51, 5, 3, 50.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(52, 5, 4, 55.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(53, 5, 5, 60.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(54, 5, 6, 63.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(55, 5, 7, 65.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(56, 5, 8, 68.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(57, 5, 9, 70.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(58, 5, 10, 73.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(59, 5, 11, 77.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(60, 5, 12, 80.00, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(61, 6, 1, 45.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(62, 6, 2, 50.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(63, 6, 3, 55.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(64, 6, 4, 58.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(65, 6, 5, 60.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(66, 6, 6, 62.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(67, 6, 7, 62.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(68, 6, 8, 65.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(69, 6, 9, 68.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(70, 6, 10, 70.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(71, 6, 11, 72.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(72, 6, 12, 75.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(73, 7, 1, 70.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(74, 7, 2, 72.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(75, 7, 3, 73.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(76, 7, 4, 74.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(77, 7, 5, 75.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(78, 7, 6, 76.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(79, 7, 7, 76.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(80, 7, 8, 77.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(81, 7, 9, 78.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(82, 7, 10, 79.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(83, 7, 11, 80.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(84, 7, 12, 82.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(85, 8, 1, 65.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(86, 8, 2, 66.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(87, 8, 3, 67.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(88, 8, 4, 68.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(89, 8, 5, 69.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(90, 8, 6, 70.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(91, 8, 7, 70.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(92, 8, 8, 70.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(93, 8, 9, 71.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(94, 8, 10, 71.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(95, 8, 11, 72.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(96, 8, 12, 72.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(97, 9, 1, 80.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(98, 9, 2, 81.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(99, 9, 3, 82.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(100, 9, 4, 83.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(101, 9, 5, 84.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(102, 9, 6, 85.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(103, 9, 7, 85.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(104, 9, 8, 86.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(105, 9, 9, 86.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(106, 9, 10, 87.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(107, 9, 11, 87.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(108, 9, 12, 88.00, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(109, 10, 1, 45.00, 'Mengikuti target Disdik: 45%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(110, 10, 2, 50.00, 'Mengikuti target Disdik: 50%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(111, 10, 3, 55.00, 'Mengikuti target Disdik: 55%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(112, 10, 4, 58.00, 'Mengikuti target Disdik: 58%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(113, 10, 5, 60.00, 'Mengikuti target Disdik: 60%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(114, 10, 6, 62.00, 'Mengikuti target Disdik: 62%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(115, 10, 7, 62.00, 'Mengikuti target Disdik: 62%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(116, 10, 8, 65.00, 'Mengikuti target Disdik: 65%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(117, 10, 9, 68.00, 'Mengikuti target Disdik: 68%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(118, 10, 10, 70.00, 'Mengikuti target Disdik: 70%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(119, 10, 11, 72.00, 'Mengikuti target Disdik: 72%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(120, 10, 12, 75.00, 'Mengikuti target Disdik: 75%', '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(121, 11, 1, 75.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(122, 11, 2, 78.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(123, 11, 3, 80.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(124, 11, 4, 82.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(125, 11, 5, 83.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(126, 11, 6, 84.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(127, 11, 7, 85.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(128, 11, 8, 86.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(129, 11, 9, 87.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(130, 11, 10, 88.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(131, 11, 11, 89.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(132, 11, 12, 90.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(133, 12, 1, 70.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(134, 12, 2, 73.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(135, 12, 3, 76.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(136, 12, 4, 78.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(137, 12, 5, 80.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(138, 12, 6, 82.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(139, 12, 7, 83.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(140, 12, 8, 84.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(141, 12, 9, 85.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(142, 12, 10, 86.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(143, 12, 11, 87.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(144, 12, 12, 88.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(145, 13, 1, 60.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(146, 13, 2, 65.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(147, 13, 3, 68.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(148, 13, 4, 70.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(149, 13, 5, 72.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(150, 13, 6, 74.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(151, 13, 7, 75.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(152, 13, 8, 78.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(153, 13, 9, 80.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(154, 13, 10, 82.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(155, 13, 11, 83.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(156, 13, 12, 85.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(157, 14, 1, 65.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(158, 14, 2, 67.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(159, 14, 3, 69.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(160, 14, 4, 71.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(161, 14, 5, 73.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(162, 14, 6, 75.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(163, 14, 7, 75.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(164, 14, 8, 76.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(165, 14, 9, 77.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(166, 14, 10, 78.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(167, 14, 11, 79.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(168, 14, 12, 80.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(169, 15, 1, 75.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(170, 15, 2, 77.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(171, 15, 3, 79.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(172, 15, 4, 81.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(173, 15, 5, 82.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(174, 15, 6, 83.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(175, 15, 7, 84.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(176, 15, 8, 85.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(177, 15, 9, 86.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(178, 15, 10, 87.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(179, 15, 11, 88.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(180, 15, 12, 90.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(181, 16, 1, 55.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(182, 16, 2, 60.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(183, 16, 3, 63.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(184, 16, 4, 66.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(185, 16, 5, 68.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(186, 16, 6, 70.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(187, 16, 7, 72.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(188, 16, 8, 74.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(189, 16, 9, 76.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(190, 16, 10, 78.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(191, 16, 11, 81.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(192, 16, 12, 85.00, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `opd_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `opd_id`, `name`, `username`, `email`, `phone`, `must_change_password`, `is_active`, `last_login_at`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Admin Super', 'admin', 'admin@ikuproject.test', NULL, 0, 1, '2026-04-23 15:26:59', '2026-04-21 23:46:58', '$2y$12$UbaJpogPyrPamjZySPnPf.HPK0C7KdvxZR4U1LimHMTNhZOdfkK7.', NULL, NULL, NULL, 'QppXCrjZImLNBvudF4wrmPrdhVdE5qj7RWQkZgf7WZjaW1brkh5zRb5WZpjR', '2026-04-21 23:46:58', '2026-04-23 15:26:59'),
(2, 3, 'Kepala Dinas Kominfo', 'kadis_kominfo', 'kadis@diskominfo.test', '6282178535114', 0, 1, NULL, '2026-04-21 23:46:58', '$2y$12$211UW1N835QncrpPXEvQ.uIKJmRsuFgazn78fl9mTnCvanYeU5xze', NULL, NULL, NULL, NULL, '2026-04-21 23:46:58', '2026-04-21 23:46:58'),
(3, 3, 'Kabid IKP dan Statistik', 'kabid_ikp', 'kabid.ikp@diskominfo.test', NULL, 0, 1, NULL, '2026-04-21 23:46:59', '$2y$12$aMN8C37LgiV4kvMtMoCC7.a7gBojidFUGRcd4BebhNmdNXrsvjEOS', NULL, NULL, NULL, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(4, 3, 'Kabid SPBE dan Persandian', 'kabid_spbe', 'kabid.spbe@diskominfo.test', NULL, 0, 1, NULL, '2026-04-21 23:46:59', '$2y$12$BVzv5vDeA66JuSty5AIY3uMa6dZpGTmicCSZUZGvCIwt3326yfrXu', NULL, NULL, NULL, NULL, '2026-04-21 23:46:59', '2026-04-21 23:46:59'),
(5, 7, 'Kepala Dinas Disdik', 'kadis_disdik', 'kadis.disdik@pringsewu.go.id', NULL, 0, 1, NULL, NULL, '$2y$12$dxAAizYb1rfWdnqjCUSpcOJLZPa6pmHke7Q2PBFZpLxOyaMIZUk36', NULL, NULL, NULL, NULL, '2026-04-21 23:46:59', '2026-04-22 04:16:37'),
(6, 8, 'Kabid PAUD Disdik', 'kabid_paud', 'kabid.paud@pringsewu.go.id', NULL, 0, 1, NULL, NULL, '$2y$12$7ksvXp1g7Rt/yuIV9hREr.oQZdedTwSuBxD/Ci6AkDpOD9IE8fwCK', NULL, NULL, NULL, NULL, '2026-04-21 23:47:00', '2026-04-21 23:47:00'),
(7, 6, 'Asisten I Pemerintahan & Kesra', 'asisten1', 'asisten1@pringsewu.go.id', NULL, 0, 1, NULL, '2026-04-22 00:42:17', '$2y$12$NWU147mioMq4ynpQ0SH5U.MKV/MTnTbCA7.gvL2CjnuFx7JAXIC3G', NULL, NULL, NULL, NULL, '2026-04-22 00:42:17', '2026-04-22 00:42:17'),
(8, 11, 'Kabag Tata Pemerintahan', 'kabag_tapem', 'kabag.tapem@pringsewu.go.id', NULL, 0, 1, NULL, '2026-04-22 00:42:17', '$2y$12$iF7Y1i0krSg4zpMi4TOBVeFe3TiRi4Y5hseyvXts7.WN3GcN.VVhy', NULL, NULL, NULL, NULL, '2026-04-22 00:42:17', '2026-04-22 00:42:17'),
(9, 12, 'Kabag Kesejahteraan Rakyat', 'kabag_kesra', 'kabag.kesra@pringsewu.go.id', NULL, 0, 1, NULL, '2026-04-22 00:42:18', '$2y$12$9B5PJQAHztZFhfe3.NhUbOJ1dZG6ZPOC774Fdx3/m7KrdjzyHrjUe', NULL, NULL, NULL, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(10, 13, 'Kabag Hukum', 'kabag_hukum', 'kabag.hukum@pringsewu.go.id', NULL, 0, 1, NULL, '2026-04-22 00:42:18', '$2y$12$3QF3DSn3A73Ui0O7rr1ea.VtRXslICJF4x9vMoAP6/p7Q7QvsExse', NULL, NULL, NULL, NULL, '2026-04-22 00:42:18', '2026-04-22 00:42:18'),
(11, NULL, 'Super Admin', 'superadmin', 'superadmin@example.com', NULL, 0, 1, NULL, NULL, '$2y$12$PNYQNNpY23sUIkffuuH1j.P0jciwSMbdnHR6wSEQik3aA42b.XgHe', NULL, NULL, NULL, NULL, '2026-04-22 01:14:58', '2026-04-22 01:14:58'),
(12, NULL, 'Bupati', 'bupati', 'bupati@gmail.com', NULL, 0, 1, NULL, NULL, '$2y$12$YhfFracQLwIOHiTrStdxBuNj09txo9kHsS./8SN4032NoyzAcGcWq', NULL, NULL, NULL, NULL, '2026-04-22 05:33:02', '2026-04-22 05:33:02');

-- --------------------------------------------------------

--
-- Table structure for table `wa_logs`
--

CREATE TABLE `wa_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `recipient_user_id` bigint UNSIGNED DEFAULT NULL,
  `recipient_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_type` enum('report','reminder','blast','notification') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'notification',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','sent','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `provider_response` json DEFAULT NULL,
  `sent_by` bigint UNSIGNED DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `iku_skorings`
--
ALTER TABLE `iku_skorings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iku_skorings_indikator_id_bulan_tahun_unique` (`indikator_id`,`bulan`,`tahun`),
  ADD KEY `iku_skorings_realisasi_id_foreign` (`realisasi_id`),
  ADD KEY `iku_skorings_ta_scored_by_foreign` (`ta_scored_by`),
  ADD KEY `iku_skorings_finalized_by_foreign` (`finalized_by`);

--
-- Indexes for table `indikators`
--
ALTER TABLE `indikators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `indikators_kabag_id_foreign` (`kabag_id`),
  ADD KEY `indikators_opd_id_foreign` (`opd_id`),
  ADD KEY `indikators_bidang_id_foreign` (`bidang_id`),
  ADD KEY `indikators_parent_indikator_id_foreign` (`parent_indikator_id`),
  ADD KEY `indikators_dibuat_oleh_foreign` (`dibuat_oleh`),
  ADD KEY `indikators_tahun_anggaran_id_index` (`tahun_anggaran_id`),
  ADD KEY `indikators_sekda_id_index` (`sekda_id`),
  ADD KEY `indikators_asisten_id_opd_id_index` (`asisten_id`,`opd_id`),
  ADD KEY `indikators_owner_user_id_foreign` (`owner_user_id`),
  ADD KEY `indikators_source_indikator_id_foreign` (`source_indikator_id`);

--
-- Indexes for table `indikator_kerjasamas`
--
ALTER TABLE `indikator_kerjasamas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `indikator_kerjasama_unique` (`indikator_id`,`opd_id`,`bidang_id`),
  ADD KEY `indikator_kerjasamas_sekda_id_foreign` (`sekda_id`),
  ADD KEY `indikator_kerjasamas_kabag_id_foreign` (`kabag_id`),
  ADD KEY `indikator_kerjasamas_asisten_id_foreign` (`asisten_id`),
  ADD KEY `indikator_kerjasamas_bidang_id_foreign` (`bidang_id`),
  ADD KEY `indikator_kerjasamas_owner_user_id_foreign` (`owner_user_id`),
  ADD KEY `indikator_kerjasamas_dibuat_oleh_foreign` (`dibuat_oleh`),
  ADD KEY `indikator_kerjasamas_opd_id_status_index` (`opd_id`,`status`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `monthly_summaries`
--
ALTER TABLE `monthly_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `monthly_summaries_opd_id_bulan_tahun_unique` (`opd_id`,`bulan`,`tahun`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `opds`
--
ALTER TABLE `opds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `opds_code_unique` (`code`),
  ADD KEY `opds_parent_id_index` (`parent_id`),
  ADD KEY `opds_type_index` (`type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `persetujuan`
--
ALTER TABLE `persetujuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `persetujuan_user_id_foreign` (`user_id`),
  ADD KEY `persetujuan_indikator_id_level_index` (`indikator_id`,`level`);

--
-- Indexes for table `realisasi`
--
ALTER TABLE `realisasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `realisasi_indikator_id_bulan_unique` (`indikator_id`,`bulan`),
  ADD KEY `realisasi_user_id_foreign` (`user_id`),
  ADD KEY `realisasi_indikator_id_bulan_index` (`indikator_id`,`bulan`);

--
-- Indexes for table `rekap_capaian`
--
ALTER TABLE `rekap_capaian`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rekap_capaian_tahun_anggaran_id_opd_id_bulan_level_unique` (`tahun_anggaran_id`,`opd_id`,`bulan`,`level`),
  ADD KEY `rekap_capaian_opd_id_foreign` (`opd_id`),
  ADD KEY `rekap_capaian_tahun_anggaran_id_level_bulan_index` (`tahun_anggaran_id`,`level`,`bulan`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `tahun_anggaran`
--
ALTER TABLE `tahun_anggaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tahun_anggaran_tahun_unique` (`tahun`);

--
-- Indexes for table `target_indikators`
--
ALTER TABLE `target_indikators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `target_indikators_indikator_id_bulan_unique` (`indikator_id`,`bulan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_opd_id_foreign` (`opd_id`);

--
-- Indexes for table `wa_logs`
--
ALTER TABLE `wa_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wa_logs_recipient_user_id_foreign` (`recipient_user_id`),
  ADD KEY `wa_logs_sent_by_foreign` (`sent_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iku_skorings`
--
ALTER TABLE `iku_skorings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `indikators`
--
ALTER TABLE `indikators`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `indikator_kerjasamas`
--
ALTER TABLE `indikator_kerjasamas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `monthly_summaries`
--
ALTER TABLE `monthly_summaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `opds`
--
ALTER TABLE `opds`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `persetujuan`
--
ALTER TABLE `persetujuan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `realisasi`
--
ALTER TABLE `realisasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `rekap_capaian`
--
ALTER TABLE `rekap_capaian`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tahun_anggaran`
--
ALTER TABLE `tahun_anggaran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `target_indikators`
--
ALTER TABLE `target_indikators`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wa_logs`
--
ALTER TABLE `wa_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `iku_skorings`
--
ALTER TABLE `iku_skorings`
  ADD CONSTRAINT `iku_skorings_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `iku_skorings_indikator_id_foreign` FOREIGN KEY (`indikator_id`) REFERENCES `indikators` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `iku_skorings_realisasi_id_foreign` FOREIGN KEY (`realisasi_id`) REFERENCES `realisasi` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `iku_skorings_ta_scored_by_foreign` FOREIGN KEY (`ta_scored_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `indikators`
--
ALTER TABLE `indikators`
  ADD CONSTRAINT `indikators_asisten_id_foreign` FOREIGN KEY (`asisten_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikators_bidang_id_foreign` FOREIGN KEY (`bidang_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikators_dibuat_oleh_foreign` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `indikators_kabag_id_foreign` FOREIGN KEY (`kabag_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikators_opd_id_foreign` FOREIGN KEY (`opd_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikators_owner_user_id_foreign` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikators_parent_indikator_id_foreign` FOREIGN KEY (`parent_indikator_id`) REFERENCES `indikators` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikators_sekda_id_foreign` FOREIGN KEY (`sekda_id`) REFERENCES `opds` (`id`),
  ADD CONSTRAINT `indikators_source_indikator_id_foreign` FOREIGN KEY (`source_indikator_id`) REFERENCES `indikators` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikators_tahun_anggaran_id_foreign` FOREIGN KEY (`tahun_anggaran_id`) REFERENCES `tahun_anggaran` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `indikator_kerjasamas`
--
ALTER TABLE `indikator_kerjasamas`
  ADD CONSTRAINT `indikator_kerjasamas_asisten_id_foreign` FOREIGN KEY (`asisten_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikator_kerjasamas_bidang_id_foreign` FOREIGN KEY (`bidang_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikator_kerjasamas_dibuat_oleh_foreign` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikator_kerjasamas_indikator_id_foreign` FOREIGN KEY (`indikator_id`) REFERENCES `indikators` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `indikator_kerjasamas_kabag_id_foreign` FOREIGN KEY (`kabag_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikator_kerjasamas_opd_id_foreign` FOREIGN KEY (`opd_id`) REFERENCES `opds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `indikator_kerjasamas_owner_user_id_foreign` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `indikator_kerjasamas_sekda_id_foreign` FOREIGN KEY (`sekda_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `monthly_summaries`
--
ALTER TABLE `monthly_summaries`
  ADD CONSTRAINT `monthly_summaries_opd_id_foreign` FOREIGN KEY (`opd_id`) REFERENCES `opds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `opds`
--
ALTER TABLE `opds`
  ADD CONSTRAINT `opds_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `persetujuan`
--
ALTER TABLE `persetujuan`
  ADD CONSTRAINT `persetujuan_indikator_id_foreign` FOREIGN KEY (`indikator_id`) REFERENCES `indikators` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `persetujuan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `realisasi`
--
ALTER TABLE `realisasi`
  ADD CONSTRAINT `realisasi_indikator_id_foreign` FOREIGN KEY (`indikator_id`) REFERENCES `indikators` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `realisasi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rekap_capaian`
--
ALTER TABLE `rekap_capaian`
  ADD CONSTRAINT `rekap_capaian_opd_id_foreign` FOREIGN KEY (`opd_id`) REFERENCES `opds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rekap_capaian_tahun_anggaran_id_foreign` FOREIGN KEY (`tahun_anggaran_id`) REFERENCES `tahun_anggaran` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `target_indikators`
--
ALTER TABLE `target_indikators`
  ADD CONSTRAINT `target_indikators_indikator_id_foreign` FOREIGN KEY (`indikator_id`) REFERENCES `indikators` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_opd_id_foreign` FOREIGN KEY (`opd_id`) REFERENCES `opds` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wa_logs`
--
ALTER TABLE `wa_logs`
  ADD CONSTRAINT `wa_logs_recipient_user_id_foreign` FOREIGN KEY (`recipient_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `wa_logs_sent_by_foreign` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
