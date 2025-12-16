-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Okt 2025 pada 16.24
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
-- Database: `netmon`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$9dBQlQxr/3QYLE0qMKt0k.iOUzAt5jx4fRtapVY.1nUB8e5gfpL4a', '2025-10-19 05:43:13');

-- --------------------------------------------------------

--
-- Struktur dari tabel `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `device_name` varchar(150) NOT NULL,
  `device_brand` varchar(150) DEFAULT NULL,
  `device_ip` varchar(45) DEFAULT NULL,
  `bandwidth` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `devices`
--

INSERT INTO `devices` (`id`, `student_id`, `device_name`, `device_brand`, `device_ip`, `bandwidth`, `status`, `created_at`) VALUES
(24, 6, 'Laptop-Asus', 'Asus', '192.168.1.3', NULL, 0, '2025-10-20 13:43:44'),
(25, 7, 'HP-Samsung', 'Samsung', '192.168.1.4', 1, 1, '2025-10-20 13:46:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dummy_devices`
--

CREATE TABLE `dummy_devices` (
  `id` int(11) NOT NULL,
  `device_name` varchar(150) NOT NULL,
  `device_brand` varchar(150) DEFAULT NULL,
  `device_ip` varchar(45) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `used` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dummy_devices`
--

INSERT INTO `dummy_devices` (`id`, `device_name`, `device_brand`, `device_ip`, `status`, `used`) VALUES
(1, 'Laptop-Acer', 'Acer', '192.168.1.2', 1, 1),
(2, 'Laptop-Asus', 'Asus', '192.168.1.3', 0, 1),
(3, 'HP-Samsung', 'Samsung', '192.168.1.4', 1, 1),
(4, 'HP-Xiaomi', 'Xiaomi', '192.168.1.5', 1, 1),
(5, 'Laptop-Lenovo', 'Lenovo', '192.168.1.6', 0, 0),
(6, 'HP-Oppo', 'Oppo', '192.168.1.7', 1, 0),
(7, 'Laptop-HP', 'HP', '192.168.1.8', 0, 0),
(8, 'HP-Vivo', 'Vivo', '192.168.1.9', 1, 0),
(9, 'Laptop-Dell', 'Dell', '192.168.1.10', 0, 0),
(10, 'HP-Infinix', 'Infinix', '192.168.1.11', 1, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `can_login` tinyint(1) NOT NULL DEFAULT 0,
  `requested_device_name` varchar(150) DEFAULT NULL,
  `requested_device_brand` varchar(150) DEFAULT NULL,
  `requested_device_ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `password`, `can_login`, `requested_device_name`, `requested_device_brand`, `requested_device_ip`, `created_at`) VALUES
(6, 'r', 'r@r.com', '$2y$10$mCm8wHWqg.nBu.HK2Xp4te/1zkShvYlMK7TIlXEm8GBrRdsoiu3dK', 1, NULL, NULL, NULL, '2025-10-20 13:43:37'),
(7, 'pli', 'pli@pli.com', '$2y$10$YVM0iifIj3yzc5gLom9wo.7jMxJdOly8E7t6fkQ8JDM4n9RThWVTa', 1, NULL, NULL, NULL, '2025-10-20 13:45:59');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indeks untuk tabel `dummy_devices`
--
ALTER TABLE `dummy_devices`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `dummy_devices`
--
ALTER TABLE `dummy_devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
