-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Jun 2025 pada 13.17
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipemas`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_pengaduan_by_kategori` ()   BEGIN
    SELECT 
        kategori_pengaduan,
        COUNT(*) as jumlah,
        SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as selesai,
        ROUND((SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as persentase_selesai
    FROM tbl_pengaduan 
    GROUP BY kategori_pengaduan
    ORDER BY jumlah DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_statistik_pengaduan` (IN `bulan` INT, IN `tahun` INT)   BEGIN
    SELECT 
        COUNT(*) as total_pengaduan,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'process' THEN 1 ELSE 0 END) as process,
        SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as done,
        SUM(CASE WHEN prioritas = 'urgent' THEN 1 ELSE 0 END) as urgent,
        SUM(CASE WHEN prioritas = 'tinggi' THEN 1 ELSE 0 END) as tinggi,
        SUM(CASE WHEN prioritas = 'sedang' THEN 1 ELSE 0 END) as sedang,
        SUM(CASE WHEN prioritas = 'rendah' THEN 1 ELSE 0 END) as rendah
    FROM tbl_pengaduan 
    WHERE 
        (bulan IS NULL OR MONTH(tgl_pengaduan) = bulan) AND
        (tahun IS NULL OR YEAR(tgl_pengaduan) = tahun);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_admin`
--

INSERT INTO `tbl_admin` (`admin_id`, `username`, `password`, `nama`, `email`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin123', 'Administrator Utama', 'admin@desapalandan.go.id', 'active', '2024-01-15 10:30:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(2, 'operator1', 'operator123', 'Operator Pelayanan 1', 'operator1@desapalandan.go.id', 'active', '2024-01-14 14:20:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(3, 'kepdes', 'kepdes123', 'Kepala Desa Palandan', 'kepdes@desapalandan.go.id', 'active', '2024-01-13 09:15:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_kategori`
--

CREATE TABLE `tbl_kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `warna` varchar(7) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_kategori`
--

INSERT INTO `tbl_kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `icon`, `warna`, `status`, `created_at`) VALUES
(1, 'Infrastruktur', 'Jalan, jembatan, drainase, lampu jalan, dan fasilitas umum lainnya', 'fas fa-road', '#FF5722', 'active', '2025-06-09 14:07:47'),
(2, 'Pelayanan Publik', 'Administrasi kependudukan, perizinan, dan layanan pemerintahan', 'fas fa-file-alt', '#2196F3', 'active', '2025-06-09 14:07:47'),
(3, 'Keamanan', 'Ketertiban, keamanan lingkungan, dan penegakan peraturan desa', 'fas fa-shield-alt', '#F44336', 'active', '2025-06-09 14:07:47'),
(4, 'Kebersihan', 'Pengelolaan sampah, kebersihan lingkungan, dan sanitasi', 'fas fa-broom', '#4CAF50', 'active', '2025-06-09 14:07:47'),
(5, 'Kesehatan', 'Fasilitas kesehatan, pelayanan medis, dan kesehatan masyarakat', 'fas fa-heartbeat', '#E91E63', 'active', '2025-06-09 14:07:47'),
(6, 'Pendidikan', 'Fasilitas pendidikan, tenaga pengajar, dan program pendidikan', 'fas fa-graduation-cap', '#9C27B0', 'active', '2025-06-09 14:07:47'),
(7, 'Lainnya', 'Pengaduan yang tidak termasuk kategori di atas', 'fas fa-ellipsis-h', '#607D8B', 'active', '2025-06-09 14:07:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_logs`
--

CREATE TABLE `tbl_logs` (
  `id_log` int(11) NOT NULL,
  `user_type` enum('admin','user') NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_logs`
--

INSERT INTO `tbl_logs` (`id_log`, `user_type`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'admin', '1', 'LOGIN', 'Administrator login ke sistem', '127.0.0.1', NULL, '2024-01-15 02:30:00'),
(2, 'user', '7301234567890001', 'LOGIN', 'User Ahmad Budiman login', '192.168.1.100', NULL, '2024-01-15 00:30:00'),
(3, 'user', '7301234567890001', 'CREATE_PENGADUAN', 'Membuat pengaduan baru: Jalan Rusak di RT 02', '192.168.1.100', NULL, '2024-01-01 01:15:00'),
(4, 'admin', '1', 'VERIFY_PENGADUAN', 'Memverifikasi pengaduan ID: 1', '127.0.0.1', NULL, '2024-01-02 01:00:00'),
(5, 'admin', '1', 'ADD_TANGGAPAN', 'Menambah tanggapan untuk pengaduan ID: 1', '127.0.0.1', NULL, '2024-01-02 01:05:00'),
(6, 'user', '7301234567890002', 'LOGIN', 'User Siti Nurhaliza login', '192.168.1.101', NULL, '2024-01-14 08:45:00'),
(7, 'admin', '2', 'LOGIN', 'Operator 1 login ke sistem', '127.0.0.1', NULL, '2024-01-14 06:20:00'),
(8, 'admin', '1', 'UPDATE_STATUS', 'Mengubah status pengaduan ID: 1 menjadi done', '127.0.0.1', NULL, '2024-01-15 03:00:00'),
(9, 'user', '1234567891234567', 'CREATE_PENGADUAN', 'Membuat pengaduan baru: perkelahian', NULL, NULL, '2025-06-09 14:10:08'),
(10, 'admin', '1', 'ADD_TANGGAPAN', 'Menambah tanggapan untuk pengaduan ID: 16', NULL, NULL, '2025-06-09 14:10:38'),
(11, 'user', '1234567891234567', 'CREATE_PENGADUAN', 'Membuat pengaduan baru: perkelahian', NULL, NULL, '2025-06-09 14:10:52'),
(12, 'user', '1234567891234567', 'CREATE_PENGADUAN', 'Membuat pengaduan baru: perkelahian', NULL, NULL, '2025-06-20 09:43:16'),
(13, 'user', '1234567891234567', 'CREATE_PENGADUAN', 'Membuat pengaduan baru: kerusakan', NULL, NULL, '2025-06-20 10:47:22'),
(14, 'user', '1234567891234567', 'CREATE_PENGADUAN', 'Membuat pengaduan baru: kerusakan', NULL, NULL, '2025-06-20 10:47:46');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_masyarakat`
--

CREATE TABLE `tbl_masyarakat` (
  `NIK` varchar(16) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `telp` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_masyarakat`
--

INSERT INTO `tbl_masyarakat` (`NIK`, `nama`, `alamat`, `username`, `telp`, `password`, `email`, `jenis_kelamin`, `tanggal_lahir`, `pekerjaan`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
('1234567891234567', 'arya', 'plp', 'arya', '1234567890', '516117', NULL, NULL, NULL, NULL, 'active', NULL, '2025-06-09 14:09:38', '2025-06-11 08:44:29'),
('7301234567890001', 'Ahmad Budiman', 'Jl. Mawar No. 15, RT 02/RW 01, Desa Palandan', 'ahmad_budiman', '081234567890', 'password123', 'ahmad.budiman@gmail.com', 'L', '1985-05-20', 'Petani', 'active', '2024-01-15 08:30:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890002', 'Siti Nurhaliza', 'Jl. Melati No. 8, RT 01/RW 02, Desa Palandan', 'siti_nur', '081234567891', 'password123', 'siti.nur@gmail.com', 'P', '1990-03-12', 'Ibu Rumah Tangga', 'active', '2024-01-14 16:45:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890003', 'Budi Santoso', 'Jl. Anggrek No. 22, RT 03/RW 01, Desa Palandan', 'budi_santoso', '081234567892', 'password123', 'budi.santoso@yahoo.com', 'L', '1982-11-08', 'Wiraswasta', 'active', '2024-01-13 19:20:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890004', 'Dewi Sartika', 'Jl. Cempaka No. 5, RT 01/RW 03, Desa Palandan', 'dewi_sartika', '081234567893', 'password123', 'dewi.sartika@gmail.com', 'P', '1987-07-25', 'Guru', 'active', '2024-01-12 11:10:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890005', 'Ridwan Kamil', 'Jl. Kenanga No. 12, RT 02/RW 02, Desa Palandan', 'ridwan_kamil', '081234567894', 'password123', 'ridwan.kamil@hotmail.com', 'L', '1978-12-03', 'PNS', 'active', '2024-01-11 15:30:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890006', 'Rina Mutiara', 'Jl. Dahlia No. 18, RT 04/RW 01, Desa Palandan', 'rina_mutiara', '081234567895', 'password123', 'rina.mutiara@gmail.com', 'P', '1992-09-14', 'Pedagang', 'active', '2024-01-10 13:25:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890007', 'Joko Widodo', 'Jl. Tulip No. 7, RT 03/RW 02, Desa Palandan', 'joko_widodo', '081234567896', 'password123', 'joko.widodo@gmail.com', 'L', '1975-06-21', 'Buruh', 'active', '2024-01-09 20:15:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890008', 'Mega Wati', 'Jl. Flamboyan No. 25, RT 01/RW 04, Desa Palandan', 'mega_wati', '081234567897', 'password123', 'mega.wati@yahoo.com', 'P', '1988-02-17', 'Bidan', 'active', '2024-01-08 09:40:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890009', 'Bambang Sutrisno', 'Jl. Bougenville No. 9, RT 05/RW 01, Desa Palandan', 'bambang_sutrisno', '081234567898', 'password123', 'bambang.sutrisno@gmail.com', 'L', '1980-04-30', 'Sopir', 'active', '2024-01-07 17:55:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
('7301234567890010', 'Kartini Sari', 'Jl. Teratai No. 13, RT 02/RW 03, Desa Palandan', 'kartini_sari', '081234567899', 'password123', 'kartini.sari@hotmail.com', 'P', '1995-01-05', 'Mahasiswi', 'active', '2024-01-06 12:00:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pengaduan`
--

CREATE TABLE `tbl_pengaduan` (
  `id_pengaduan` int(11) NOT NULL,
  `tgl_pengaduan` date NOT NULL,
  `nama_pengaduan` varchar(150) NOT NULL,
  `NIK` varchar(16) NOT NULL,
  `kategori_pengaduan` varchar(50) NOT NULL,
  `detail_pengaduan` text NOT NULL,
  `telp` varchar(15) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('pending','process','done') DEFAULT 'pending',
  `prioritas` enum('rendah','sedang','tinggi','urgent') DEFAULT 'sedang',
  `admin_verifikator` int(11) DEFAULT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_pengaduan`
--

INSERT INTO `tbl_pengaduan` (`id_pengaduan`, `tgl_pengaduan`, `nama_pengaduan`, `NIK`, `kategori_pengaduan`, `detail_pengaduan`, `telp`, `foto`, `status`, `prioritas`, `admin_verifikator`, `tanggal_verifikasi`, `created_at`, `updated_at`) VALUES
(1, '2024-01-01', 'Jalan Rusak di RT 02', '7301234567890001', 'Infrastruktur', 'Jalan di RT 02 RW 01 sudah sangat rusak dan berlubang. Saat hujan air menggenang dan menyulitkan warga untuk lewat. Mohon segera diperbaiki karena mengganggu aktivitas sehari-hari warga.', '081234567890', NULL, 'done', 'tinggi', 1, '2024-01-02 09:00:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(2, '2024-01-03', 'Lampu Jalan Mati Total', '7301234567890002', 'Infrastruktur', 'Lampu penerangan jalan di Jl. Melati sudah mati total selama 1 minggu. Keadaan menjadi gelap gulita di malam hari dan rawan tindak kejahatan. Mohon segera diperbaiki.', '081234567891', NULL, 'done', 'tinggi', 2, '2024-01-04 10:30:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(3, '2024-01-05', 'Pelayanan KTP Lambat', '7301234567890003', 'Pelayanan Publik', 'Proses pembuatan KTP di kantor desa sangat lambat. Sudah menunggu 2 minggu belum selesai juga. Petugas juga kurang responsif dalam memberikan informasi status dokumen.', '081234567892', NULL, 'process', 'sedang', 1, '2024-01-06 14:20:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(4, '2024-01-07', 'Keamanan Lingkungan Menurun', '7301234567890004', 'Keamanan', 'Akhir-akhir ini sering terjadi pencurian motor di area RT 01. Ronda malam juga jarang dilakukan. Mohon ditingkatkan sistem keamanan lingkungan dan koordinasi dengan pihak kepolisian.', '081234567893', NULL, 'process', 'tinggi', 3, '2024-01-08 16:45:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(5, '2024-01-09', 'Sampah Menumpuk di TPS', '7301234567890005', 'Kebersihan', 'TPS (Tempat Pembuangan Sementara) di RT 02 sudah penuh dan menumpuk sampah dimana-mana. Bau tidak sedap menyebar ke rumah warga. Truk sampah jarang datang mengangkut.', '081234567894', NULL, 'process', 'sedang', 2, '2024-01-10 08:15:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(6, '2024-01-11', 'Fasilitas Puskesmas Kurang', '7301234567890006', 'Kesehatan', 'Puskesmas desa kekurangan obat-obatan dan peralatan medis. Banyak warga yang harus pergi ke kota untuk berobat. Mohon pengadaan fasilitas kesehatan yang lebih lengkap.', '081234567895', NULL, 'pending', 'tinggi', NULL, NULL, '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(7, '2024-01-12', 'Sekolah Kekurangan Guru', '7301234567890007', 'Pendidikan', 'SD Negeri 1 Palandan kekurangan tenaga pengajar. 1 guru harus mengajar 2 kelas sekaligus. Anak-anak jadi kurang mendapat perhatian dalam belajar. Mohon penambahan guru.', '081234567896', NULL, 'pending', 'sedang', NULL, NULL, '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(8, '2024-01-13', 'Drainase Tersumbat', '7301234567890008', 'Infrastruktur', 'Saluran drainase di Jl. Flamboyan tersumbat sampah dan lumpur. Saat hujan air menggenang tinggi dan masuk ke rumah warga. Perlu dibersihkan dan diperdalam salurannya.', '081234567897', NULL, 'pending', 'tinggi', NULL, NULL, '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(9, '2024-01-14', 'Surat Keterangan Usaha Sulit', '7301234567890009', 'Pelayanan Publik', 'Prosedur pembuatan surat keterangan usaha sangat berbelit-belit dan memakan waktu lama. Persyaratan juga tidak jelas. Mohon dipermudah prosedurnya untuk membantu usaha kecil warga.', '081234567898', NULL, 'pending', 'rendah', NULL, NULL, '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(10, '2024-01-15', 'Hewan Ternak Berkeliaran', '7301234567890010', 'Lainnya', 'Banyak hewan ternak (sapi, kambing) yang berkeliaran bebas di jalan dan merusak tanaman warga. Pemilik ternak tidak bertanggung jawab. Mohon ada aturan yang tegas tentang hal ini.', '081234567899', NULL, 'pending', 'rendah', NULL, NULL, '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(11, '2024-01-10', 'Jembatan Retak Berbahaya', '7301234567890001', 'Infrastruktur', 'Jembatan penghubung antar RT mengalami keretakan yang cukup parah. Sangat berbahaya jika dilewati kendaraan berat. Mohon segera dilakukan perbaikan sebelum terjadi kecelakaan.', '081234567890', NULL, 'done', 'urgent', 3, '2024-01-11 07:30:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(12, '2024-01-08', 'Air Bersih Keruh', '7301234567890003', 'Kesehatan', 'Air PAM yang mengalir ke rumah warga keruh dan berbau. Banyak warga yang sakit perut setelah menggunakan air tersebut. Mohon segera dicek kualitas air dan sumbernya.', '081234567892', NULL, 'done', 'urgent', 1, '2024-01-09 13:20:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(13, '2024-01-06', 'Pohon Tumbang Menghalangi Jalan', '7301234567890005', 'Infrastruktur', 'Pohon besar tumbang akibat angin kencang dan menghalangi akses jalan utama. Kendaraan tidak bisa lewat. Mohon segera dibersihkan karena mengganggu aktivitas warga.', '081234567894', NULL, 'done', 'urgent', 2, '2024-01-06 15:45:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(14, '2024-01-04', 'Posyandu Kekurangan Vaksin', '7301234567890008', 'Kesehatan', 'Posyandu desa kehabisan stok vaksin untuk balita dan ibu hamil. Program imunisasi jadi terganggu. Mohon segera pengadaan vaksin agar kesehatan anak-anak tetap terjaga.', '081234567897', NULL, 'process', 'tinggi', 1, '2024-01-05 11:10:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(15, '2024-01-02', 'Banjir Luapan Sungai', '7301234567890006', 'Infrastruktur', 'Sungai di belakang desa meluap dan menggenangi sawah warga. Tanaman padi banyak yang rusak. Perlu pengerukan sungai dan pembuatan tanggul pencegah banjir.', '081234567895', NULL, 'process', 'urgent', 3, '2024-01-03 06:00:00', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(16, '2025-06-09', 'perkelahian', '1234567891234567', 'Keamanan', 'pukull', '1234567890', 'Capture_1749478208_6846eb4030207.png', 'done', 'sedang', NULL, NULL, '2025-06-09 14:10:08', '2025-06-09 14:10:43'),
(17, '2025-06-09', 'perkelahian', '1234567891234567', 'Keamanan', 'pukull', '1234567890', 'Capture_1749478252_6846eb6ca07a3.png', 'done', 'sedang', NULL, NULL, '2025-06-09 14:10:52', '2025-06-11 08:43:19'),
(18, '2025-06-20', 'perkelahian', '1234567891234567', 'Pelayanan Publik', 'sadsadsfs', '1234567890', 'JAS_1750412596_68552d34b93ee.jpg', 'done', 'sedang', NULL, NULL, '2025-06-20 09:43:16', '2025-06-20 09:43:51'),
(19, '2025-06-20', 'kerusakan', '1234567891234567', 'Infrastruktur', 'tidak bisa', '1234567890', 'IMG20250419220008.jpg', 'pending', 'sedang', NULL, NULL, '2025-06-20 10:47:22', '2025-06-20 10:47:22'),
(20, '2025-06-20', 'kerusakan', '1234567891234567', 'Infrastruktur', 'jsoajoid', '1234567890', 'IMG20250419220008.jpg', 'done', 'sedang', NULL, NULL, '2025-06-20 10:47:46', '2025-06-20 10:48:56');

--
-- Trigger `tbl_pengaduan`
--
DELIMITER $$
CREATE TRIGGER `tr_pengaduan_after_insert` AFTER INSERT ON `tbl_pengaduan` FOR EACH ROW BEGIN
    INSERT INTO tbl_logs (user_type, user_id, action, description)
    VALUES ('user', NEW.NIK, 'CREATE_PENGADUAN', CONCAT('Membuat pengaduan baru: ', NEW.nama_pengaduan));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
(1, 'site_name', 'SIPEMAS Desa Palandan', 'text', 'Nama aplikasi/website', '2025-06-09 14:07:47'),
(2, 'site_description', 'Sistem Informasi Pelayanan Pengaduan Masyarakat Desa Palandan, Kabupaten Luwu Utara', 'text', 'Deskripsi aplikasi', '2025-06-09 14:07:47'),
(3, 'contact_phone', '(0421) 123456', 'text', 'Nomor telepon kontak', '2025-06-09 14:07:47'),
(4, 'contact_email', 'sipemas@desapalandan.go.id', 'text', 'Email kontak', '2025-06-09 14:07:47'),
(5, 'office_address', 'Jl. Desa Palandan, Kab. Luwu Utara, Sulawesi Selatan, Indonesia', 'text', 'Alamat kantor desa', '2025-06-09 14:07:47'),
(6, 'max_file_size', '5242880', 'number', 'Maksimal ukuran file upload dalam bytes (5MB)', '2025-06-09 14:07:47'),
(7, 'allowed_file_types', 'jpg,jpeg,png,gif', 'text', 'Tipe file yang diizinkan untuk upload', '2025-06-09 14:07:47'),
(8, 'auto_response_enabled', 'true', 'boolean', 'Aktifkan respon otomatis saat pengaduan diterima', '2025-06-09 14:07:47'),
(9, 'auto_response_message', 'Terima kasih atas pengaduan Anda. Laporan sedang kami proses dan akan segera ditindaklanjuti.', 'text', 'Pesan respon otomatis', '2025-06-09 14:07:47'),
(10, 'maintenance_mode', 'false', 'boolean', 'Mode maintenance aplikasi', '2025-06-09 14:07:47'),
(11, 'registration_enabled', 'true', 'boolean', 'Izinkan registrasi user baru', '2025-06-09 14:07:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_tanggapan`
--

CREATE TABLE `tbl_tanggapan` (
  `id_tanggapan` int(11) NOT NULL,
  `id_pengaduan` int(11) NOT NULL,
  `tanggal_tanggapan` date NOT NULL,
  `tanggapan` text NOT NULL,
  `admin_id` int(11) NOT NULL,
  `jenis_tanggapan` enum('informasi','tindak_lanjut','penyelesaian') DEFAULT 'informasi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tbl_tanggapan`
--

INSERT INTO `tbl_tanggapan` (`id_tanggapan`, `id_pengaduan`, `tanggal_tanggapan`, `tanggapan`, `admin_id`, `jenis_tanggapan`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-01-02', 'Terima kasih atas laporannya. Pengaduan Anda sedang kami verifikasi dan akan segera ditindaklanjuti oleh tim infrastruktur desa.', 1, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(2, 1, '2024-01-05', 'Tim teknis sudah melakukan survei lapangan. Kerusakan jalan memang cukup parah dan memerlukan perbaikan segera. Anggaran sedang diproses.', 1, 'tindak_lanjut', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(3, 1, '2024-01-15', 'Alhamdulillah perbaikan jalan di RT 02 RW 01 telah selesai dilaksanakan. Terima kasih atas laporan dan kesabarannya. Mohon laporkan jika ada kerusakan lainnya.', 1, 'penyelesaian', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(4, 2, '2024-01-04', 'Laporan lampu jalan mati telah kami terima. Tim listrik desa akan segera melakukan pengecekan dan perbaikan.', 2, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(5, 2, '2024-01-06', 'Kerusakan pada lampu jalan disebabkan oleh kabel yang putus. Perbaikan sedang dilakukan dan diperkirakan selesai dalam 2 hari.', 2, 'tindak_lanjut', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(6, 2, '2024-01-08', 'Lampu penerangan jalan di Jl. Melati sudah diperbaiki dan menyala normal kembali. Terima kasih atas laporannya.', 2, 'penyelesaian', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(7, 3, '2024-01-06', 'Pengaduan tentang pelayanan KTP telah kami terima. Kami akan evaluasi dan tingkatkan kualitas pelayanan administrasi kependudukan.', 1, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(8, 3, '2024-01-10', 'Proses KTP Anda sedang dalam tahap finalisasi. Diperkirakan akan selesai dalam 3 hari kerja. Mohon maaf atas keterlambatan.', 1, 'tindak_lanjut', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(9, 4, '2024-01-08', 'Laporan tentang keamanan lingkungan sangat penting. Kami akan koordinasi dengan Babinsa dan Bhabinkamtibmas untuk meningkatkan patroli.', 3, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(10, 4, '2024-01-12', 'Telah dibentuk tim ronda tambahan dan koordinasi dengan polsek setempat. Patroli akan ditingkatkan terutama di area rawan pencurian.', 3, 'tindak_lanjut', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(11, 5, '2024-01-10', 'TPS memang sudah overload. Kami akan koordinasi dengan dinas kebersihan untuk penambahan frekuensi pengangkutan sampah.', 2, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(12, 5, '2024-01-14', 'Truk sampah sudah dijadwalkan 2x seminggu untuk area RT 02. Kami juga akan sosialisasi pemilahan sampah kepada warga.', 2, 'tindak_lanjut', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(13, 11, '2024-01-11', 'Laporan jembatan retak telah kami terima. Tim akan segera melakukan inspeksi dan menutup sementara akses jembatan demi keamanan.', 3, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(14, 11, '2024-01-13', 'Jembatan telah diperbaiki dengan struktur yang lebih kuat. Akses sudah bisa digunakan kembali dengan aman.', 3, 'penyelesaian', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(15, 12, '2024-01-09', 'Air keruh sangat berbahaya bagi kesehatan. Tim akan segera cek instalasi PAM dan kualitas air bersama dinas kesehatan.', 1, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(16, 12, '2024-01-11', 'Masalah sudah ditemukan pada filter PAM yang kotor. Pembersihan telah dilakukan dan kualitas air sudah normal kembali.', 1, 'penyelesaian', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(17, 13, '2024-01-06', 'Tim emergency sudah disiapkan untuk membersihkan pohon tumbang. Terima kasih laporannya.', 2, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(18, 13, '2024-01-06', 'Pohon tumbang sudah dibersihkan dan jalan sudah bisa dilewati kembali. Terima kasih atas kesabarannya.', 2, 'penyelesaian', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(19, 14, '2024-01-05', 'Laporan kekurangan vaksin telah kami sampaikan ke dinas kesehatan kabupaten. Pengadaan sedang diproses.', 1, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(20, 14, '2024-01-09', 'Vaksin tambahan sudah datang dan program imunisasi bisa dilanjutkan. Jadwal posyandu tetap sesuai kalender.', 1, 'tindak_lanjut', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(21, 15, '2024-01-03', 'Banjir memang masalah serius. Tim akan survey lokasi dan koordinasi dengan dinas PU untuk solusi jangka panjang.', 3, 'informasi', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(22, 15, '2024-01-07', 'Pengerukan darurat sudah dilakukan. Untuk solusi permanen sedang diajukan proposal tanggul ke pemerintah kabupaten.', 3, 'tindak_lanjut', '2025-06-09 14:07:47', '2025-06-09 14:07:47'),
(23, 16, '2025-06-09', 'iyo', 1, 'informasi', '2025-06-09 14:10:38', '2025-06-09 14:10:38');

--
-- Trigger `tbl_tanggapan`
--
DELIMITER $$
CREATE TRIGGER `tr_tanggapan_after_insert` AFTER INSERT ON `tbl_tanggapan` FOR EACH ROW BEGIN
    INSERT INTO tbl_logs (user_type, user_id, action, description)
    VALUES ('admin', NEW.admin_id, 'ADD_TANGGAPAN', CONCAT('Menambah tanggapan untuk pengaduan ID: ', NEW.id_pengaduan));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `vw_pengaduan_lengkap`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `vw_pengaduan_lengkap` (
`id_pengaduan` int(11)
,`tgl_pengaduan` date
,`nama_pengaduan` varchar(150)
,`kategori_pengaduan` varchar(50)
,`detail_pengaduan` text
,`status` enum('pending','process','done')
,`prioritas` enum('rendah','sedang','tinggi','urgent')
,`foto` varchar(255)
,`NIK` varchar(16)
,`nama_pelapor` varchar(100)
,`alamat` text
,`telp` varchar(15)
,`email` varchar(100)
,`admin_verifikator` varchar(100)
,`tanggal_verifikasi` datetime
,`jumlah_tanggapan` bigint(21)
,`created_at` timestamp
,`updated_at` timestamp
);

-- --------------------------------------------------------

--
-- Struktur untuk view `vw_pengaduan_lengkap`
--
DROP TABLE IF EXISTS `vw_pengaduan_lengkap`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_pengaduan_lengkap`  AS SELECT `p`.`id_pengaduan` AS `id_pengaduan`, `p`.`tgl_pengaduan` AS `tgl_pengaduan`, `p`.`nama_pengaduan` AS `nama_pengaduan`, `p`.`kategori_pengaduan` AS `kategori_pengaduan`, `p`.`detail_pengaduan` AS `detail_pengaduan`, `p`.`status` AS `status`, `p`.`prioritas` AS `prioritas`, `p`.`foto` AS `foto`, `m`.`NIK` AS `NIK`, `m`.`nama` AS `nama_pelapor`, `m`.`alamat` AS `alamat`, `m`.`telp` AS `telp`, `m`.`email` AS `email`, `a`.`nama` AS `admin_verifikator`, `p`.`tanggal_verifikasi` AS `tanggal_verifikasi`, count(`t`.`id_tanggapan`) AS `jumlah_tanggapan`, `p`.`created_at` AS `created_at`, `p`.`updated_at` AS `updated_at` FROM (((`tbl_pengaduan` `p` join `tbl_masyarakat` `m` on(`p`.`NIK` = `m`.`NIK`)) left join `tbl_admin` `a` on(`p`.`admin_verifikator` = `a`.`admin_id`)) left join `tbl_tanggapan` `t` on(`p`.`id_pengaduan` = `t`.`id_pengaduan`)) GROUP BY `p`.`id_pengaduan` ORDER BY `p`.`created_at` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `tbl_kategori`
--
ALTER TABLE `tbl_kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `tbl_logs`
--
ALTER TABLE `tbl_logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `user_type` (`user_type`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action` (`action`),
  ADD KEY `idx_logs_created_at` (`created_at`);

--
-- Indeks untuk tabel `tbl_masyarakat`
--
ALTER TABLE `tbl_masyarakat`
  ADD PRIMARY KEY (`NIK`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_masyarakat_nama` (`nama`);

--
-- Indeks untuk tabel `tbl_pengaduan`
--
ALTER TABLE `tbl_pengaduan`
  ADD PRIMARY KEY (`id_pengaduan`),
  ADD KEY `NIK` (`NIK`),
  ADD KEY `admin_verifikator` (`admin_verifikator`),
  ADD KEY `status` (`status`),
  ADD KEY `kategori_pengaduan` (`kategori_pengaduan`),
  ADD KEY `idx_pengaduan_tanggal` (`tgl_pengaduan`),
  ADD KEY `idx_pengaduan_status_tanggal` (`status`,`tgl_pengaduan`);

--
-- Indeks untuk tabel `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indeks untuk tabel `tbl_tanggapan`
--
ALTER TABLE `tbl_tanggapan`
  ADD PRIMARY KEY (`id_tanggapan`),
  ADD KEY `id_pengaduan` (`id_pengaduan`),
  ADD KEY `admin_id` (`admin_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tbl_kategori`
--
ALTER TABLE `tbl_kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tbl_logs`
--
ALTER TABLE `tbl_logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `tbl_pengaduan`
--
ALTER TABLE `tbl_pengaduan`
  MODIFY `id_pengaduan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `tbl_tanggapan`
--
ALTER TABLE `tbl_tanggapan`
  MODIFY `id_tanggapan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tbl_pengaduan`
--
ALTER TABLE `tbl_pengaduan`
  ADD CONSTRAINT `tbl_pengaduan_ibfk_1` FOREIGN KEY (`NIK`) REFERENCES `tbl_masyarakat` (`NIK`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_pengaduan_ibfk_2` FOREIGN KEY (`admin_verifikator`) REFERENCES `tbl_admin` (`admin_id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `tbl_tanggapan`
--
ALTER TABLE `tbl_tanggapan`
  ADD CONSTRAINT `tbl_tanggapan_ibfk_1` FOREIGN KEY (`id_pengaduan`) REFERENCES `tbl_pengaduan` (`id_pengaduan`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_tanggapan_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `tbl_admin` (`admin_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
