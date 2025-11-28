-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2025 at 01:04 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `no_wa` varchar(20) NOT NULL,
  `mobil` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `tanggal_sewa` date NOT NULL,
  `durasi_hari` int(11) NOT NULL,
  `total_bayar` int(11) NOT NULL,
  `status` enum('pending','lunas','batal') DEFAULT 'pending',
  `bukti_transfer` blob NOT NULL,
  `username_user` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `nama_pelanggan`, `no_wa`, `mobil`, `harga`, `tanggal_sewa`, `durasi_hari`, `total_bayar`, `status`, `bukti_transfer`, `username_user`, `created_at`) VALUES
(1, 'Budi Santoso', '08123456789', 'Innova Zenix', 900000, '2025-11-20', 3, 2700000, 'lunas', '', '', '2025-11-27 19:57:10'),
(2, 'Siti Aminah', '08571234567', 'Grand New Avanza', 500000, '2025-11-15', 5, 2500000, 'lunas', '', '', '2025-11-27 19:57:10'),
(3, 'Ahmad Rizky', '08987654321', 'Mitsubishi Xpander', 550000, '2025-11-25', 2, 1100000, 'lunas', '', '', '2025-11-27 19:57:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
