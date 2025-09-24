-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2025 at 02:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parking_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `parking_spots`
--

CREATE TABLE `parking_spots` (
  `id` int(11) NOT NULL,
  `spot_name` varchar(10) NOT NULL,
  `status` enum('available','occupied') NOT NULL DEFAULT 'available',
  `booked_by_user` varchar(255) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 50.00,
  `spot_type` varchar(50) NOT NULL DEFAULT 'car'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_spots`
--

INSERT INTO `parking_spots` (`id`, `spot_name`, `status`, `booked_by_user`, `zone_id`, `price`, `spot_type`) VALUES
(1, 'A01', 'available', 'Filmza00789', 1, 50.00, 'car'),
(2, 'A02', 'available', 'Filmza00789', 1, 50.00, 'car'),
(3, 'A03', 'available', 'Filmza00789', 1, 50.00, 'car'),
(4, 'A04', 'occupied', 'Filmza00789', 1, 50.00, 'car'),
(5, 'A05', 'available', 'Filmza00789', 1, 50.00, 'car'),
(6, 'A06', 'available', NULL, 1, 50.00, 'car'),
(7, 'A07', 'available', NULL, 1, 50.00, 'car'),
(8, 'A08', 'occupied', NULL, 1, 50.00, 'car'),
(9, 'A09', 'occupied', 'Filmza00789', 1, 50.00, 'car'),
(10, 'A10', 'occupied', NULL, 1, 50.00, 'car'),
(11, 'A11', 'available', NULL, 1, 50.00, 'car'),
(12, 'A12', 'occupied', NULL, 1, 50.00, 'car'),
(13, 'A13', 'occupied', 'Filmza00789', 1, 50.00, 'car'),
(14, 'A14', 'available', 'Filmza00789', 1, 50.00, 'car'),
(15, 'A15', 'available', NULL, 1, 50.00, 'car'),
(16, 'A16', 'available', NULL, 1, 50.00, 'car'),
(17, 'A17', 'available', NULL, 1, 50.00, 'car'),
(18, 'A18', 'occupied', NULL, 1, 50.00, 'car'),
(19, 'A19', 'available', NULL, 1, 50.00, 'car'),
(20, 'A20', 'available', NULL, 1, 50.00, 'car'),
(21, 'A21', 'available', 'Filmza00789', 1, 50.00, 'car'),
(22, 'A22', 'occupied', NULL, 1, 50.00, 'car'),
(23, 'A23', 'available', 'Filmza00789', 1, 50.00, 'car'),
(24, 'A24', 'occupied', NULL, 1, 50.00, 'car'),
(25, 'A25', 'occupied', NULL, 1, 50.00, 'car');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'Filmza00789', '$2y$10$8KletU/RITepZcVJOyiP.u0FAC/VafcVf51gLxCfWLbaNE1r/NYD6', 'user'),
(2, 'momily145@gmail.com', '$2y$10$QHPTDq5LVhjYRbA3JHcwKeWZ6x7Iq9Gixe79T4KPc.05f.J.DV/jK', 'user'),
(3, 'FF', '$2y$10$/Uh6L/AD8WSbTcl8g0BDLOa2DDHUrXCqZD4nXR0bZ2sNMzv3P8NgK', 'user'),
(4, 'admin', '$2y$10$Earu5ZQB8Ip.20EqwiJ3zu3/SoRQoq3zrmRB7U8CaE26TV1HMqhEW', 'admin'),
(5, 'FF1', '$2y$10$PcDAiDo78apZyJj2.bnUH.NPtEVNRXA1ZpgQnB/9qjbYXToYQJ/Vi', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `name`) VALUES
(1, 'เซ็นทรัล'),
(2, 'บิ๊กซี');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `parking_spots`
--
ALTER TABLE `parking_spots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `spot_name` (`spot_name`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `parking_spots`
--
ALTER TABLE `parking_spots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
