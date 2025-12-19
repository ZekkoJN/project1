-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Des 2025 pada 11.02
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
-- Database: `universitas`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `mhs`
--

CREATE TABLE `mhs` (
  `id` int(8) NOT NULL,
  `nim` varchar(14) NOT NULL,
  `nama` varchar(40) NOT NULL,
  `tempatLahir` varchar(25) NOT NULL,
  `tanggalLahir` date NOT NULL,
  `jmlSaudara` int(2) NOT NULL,
  `alamat` varchar(50) NOT NULL,
  `kota` varchar(20) NOT NULL,
  `jenisKelamin` varchar(2) NOT NULL,
  `statusKeluarga` varchar(1) NOT NULL,
  `hobi` varchar(100) NOT NULL,
  `email` varchar(30) NOT NULL,
  `pass` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mhs`
--

INSERT INTO `mhs` (`id`, `nim`, `nama`, `tempatLahir`, `tanggalLahir`, `jmlSaudara`, `alamat`, `kota`, `jenisKelamin`, `statusKeluarga`, `hobi`, `email`, `pass`) VALUES
(1, 'A12.2022.06860', 'Zekko Jotty Nugroho', 'Blora', '2015-12-23', 2, 'blora', 'Semarang', 'L', 'B', 'Membaca, Musik', '112202206860@mhs.dinus.ac.id', '$2y$10$5aJIkYnKVkCqh6KM2Nu2P.abZ1D.tsUEbUDP7DZ3nOa/6n5LZrXz2');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `mhs`
--
ALTER TABLE `mhs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `mhs`
--
ALTER TABLE `mhs`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
