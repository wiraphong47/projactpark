-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2025 at 03:34 PM
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
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`id`, `user_id`, `login_time`, `logout_time`) VALUES
(1, 8, '2025-09-25 19:36:25', '2025-09-25 19:36:32'),
(2, 8, '2025-09-25 19:38:48', '2025-09-25 19:39:15'),
(3, 8, '2025-09-25 19:42:41', '2025-09-25 19:44:18'),
(4, 8, '2025-09-25 19:44:26', '2025-09-25 19:44:34');

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
(4, 'A04', 'available', NULL, 1, 50.00, 'car'),
(5, 'A05', 'available', 'Filmza00789', 1, 50.00, 'car'),
(6, 'A06', 'available', NULL, 1, 50.00, 'car'),
(7, 'A07', 'occupied', NULL, 1, 50.00, 'car'),
(8, 'A08', 'occupied', NULL, 1, 50.00, 'car'),
(9, 'A09', 'available', NULL, 1, 50.00, 'car'),
(10, 'A10', 'available', NULL, 1, 50.00, 'car'),
(11, 'A11', 'available', NULL, 1, 50.00, 'car'),
(12, 'A12', 'occupied', NULL, 1, 50.00, 'car'),
(13, 'A13', 'occupied', 'FF', 1, 50.00, 'car'),
(14, 'A14', 'available', 'Filmza00789', 1, 50.00, 'car'),
(15, 'A15', 'available', NULL, 1, 50.00, 'car'),
(16, 'A16', 'available', NULL, 1, 50.00, 'car'),
(17, 'A17', 'occupied', NULL, 1, 50.00, 'car'),
(18, 'A18', 'occupied', NULL, 1, 50.00, 'car'),
(19, 'A19', 'available', NULL, 1, 50.00, 'car'),
(20, 'A20', 'occupied', 'Filmza00789', 1, 50.00, 'car'),
(21, 'A21', 'occupied', 'FF', 1, 50.00, 'car'),
(22, 'A22', 'available', NULL, 1, 50.00, 'car'),
(23, 'A23', 'available', 'Filmza00789', 1, 50.00, 'car'),
(24, 'A24', 'available', NULL, 1, 50.00, 'car'),
(25, 'A25', 'occupied', NULL, 1, 50.00, 'car'),
(26, 'B01', 'available', NULL, 2, 50.00, 'car'),
(27, 'B02', 'available', NULL, 2, 50.00, 'car'),
(28, 'B03', 'available', NULL, 2, 50.00, 'car'),
(29, 'B04', 'occupied', 'FF', 2, 50.00, 'car'),
(30, 'B05', 'occupied', NULL, 2, 50.00, 'car'),
(31, 'B06', 'available', NULL, 2, 50.00, 'car'),
(32, 'B07', 'available', NULL, 2, 50.00, 'car'),
(33, 'B08', 'occupied', NULL, 2, 50.00, 'car'),
(34, 'B09', 'available', NULL, 2, 50.00, 'car'),
(35, 'B10', 'available', NULL, 2, 50.00, 'car'),
(36, 'B11', 'occupied', NULL, 2, 50.00, 'car'),
(37, 'B12', 'available', NULL, 2, 50.00, 'car'),
(38, 'B13', 'available', NULL, 2, 50.00, 'car'),
(39, 'B14', 'occupied', NULL, 2, 50.00, 'car'),
(40, 'B15', 'available', NULL, 2, 50.00, 'car'),
(41, 'B16', 'available', NULL, 2, 50.00, 'car'),
(42, 'B17', 'occupied', NULL, 2, 50.00, 'car'),
(43, 'B18', 'available', NULL, 2, 50.00, 'car'),
(44, 'B19', 'available', NULL, 2, 50.00, 'car'),
(45, 'B20', 'occupied', NULL, 2, 50.00, 'car'),
(46, 'B21', 'occupied', 'FF', 2, 50.00, 'car'),
(47, 'B22', 'occupied', 'FF', 2, 50.00, 'car'),
(48, 'B23', 'occupied', NULL, 2, 50.00, 'car'),
(49, 'B24', 'available', NULL, 2, 50.00, 'car'),
(50, 'B25', 'available', NULL, 2, 50.00, 'car');

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
  `member_id` varchar(255) DEFAULT NULL,
  `employee_id` varchar(255) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin','employee') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `member_id`, `employee_id`, `position`, `username`, `password`, `full_name`, `phone_number`, `address`, `role`) VALUES
(1, NULL, NULL, NULL, 'Filmza00789', '$2y$10$8KletU/RITepZcVJOyiP.u0FAC/VafcVf51gLxCfWLbaNE1r/NYD6', NULL, NULL, NULL, 'user'),
(2, NULL, NULL, NULL, 'momily145@gmail.com', '$2y$10$QHPTDq5LVhjYRbA3JHcwKeWZ6x7Iq9Gixe79T4KPc.05f.J.DV/jK', NULL, NULL, NULL, 'user'),
(3, NULL, NULL, NULL, 'FF', '$2y$10$/Uh6L/AD8WSbTcl8g0BDLOa2DDHUrXCqZD4nXR0bZ2sNMzv3P8NgK', NULL, NULL, NULL, 'user'),
(4, NULL, NULL, NULL, 'admin', '$2y$10$Earu5ZQB8Ip.20EqwiJ3zu3/SoRQoq3zrmRB7U8CaE26TV1HMqhEW', NULL, NULL, NULL, 'admin'),
(5, NULL, NULL, NULL, 'FF1', '$2y$10$PcDAiDo78apZyJj2.bnUH.NPtEVNRXA1ZpgQnB/9qjbYXToYQJ/Vi', NULL, NULL, NULL, 'user'),
(6, 'MEM-68d52fc6dd29b', NULL, NULL, 'FF2', '$2y$10$imdMzvyfJFDy8G5gD.vcI..Pw7m55gMZdHs7/6UzRBwhtQwV/mypy', 'wiraphong wongchare', '0829695360', 'house number114 Ban Nong Na Kham, Udon Thani, Thailand', 'user'),
(7, 'A07', NULL, NULL, 'FF3', '$2y$10$P3iRxjBQ8wkID/hwjsMLzeqWIK65F9g6F/sEwZG/JzqqXh34Enj1q', 'wiraphong wongchare', '0829695360', 'house number114 Ban Nong Na Kham, Udon Thani, Thailand', 'user'),
(8, NULL, 'EMP-008', 'employee', 'EE1', '$2y$10$1aciU/gXKeMOChXhwBziYef52sret5ePHRyxKOUBad.N0qv2GAbfi', 'wiraphong wongchare', '0829695360', 'house number114 Ban Nong Na Kham, Udon Thani, Thailand', 'employee');

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
(2, 'บิ๊กซี'),
(4, 'บิ๊กซี2');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `parking_spots`
--
ALTER TABLE `parking_spots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
