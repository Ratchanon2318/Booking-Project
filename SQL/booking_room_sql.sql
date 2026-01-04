-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2024 at 08:31 PM
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
-- Database: `booking_room_sql`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `user_department` varchar(255) DEFAULT NULL,
  `booker_name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `desired_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `booking_verify` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `booking_date`, `user_id`, `user_department`, `booker_name`, `phone_number`, `room_id`, `desired_date`, `start_time`, `end_time`, `booking_verify`) VALUES
(1, '2024-07-04 18:22:33', 6, 'งานประกัน', 'ธีรภัทร งานประกัน', '0931945999', 1, '2024-07-07', '08:30:00', '11:30:00', 'Approved'),
(2, '2024-07-04 18:23:52', 5, 'พยาบาล', 'ธีรภัทร พยาบาล', '0931945999', 2, '2024-07-10', '09:00:00', '12:00:00', 'Pending'),
(4, '2024-07-04 18:25:27', 3, 'บริหาร', 'ธีรภัทร บริหาร', '0931945999', 1, '2024-07-16', '08:00:00', '11:00:00', 'Pending'),
(5, '2024-07-04 18:26:34', 6, 'งานประกัน', 'ธีรภัทร งานประกัน', '0931945999', 2, '2024-07-09', '08:30:00', '11:30:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `booking_equipment`
--

CREATE TABLE `booking_equipment` (
  `id_booking_equipment` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `equipment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_equipment`
--

INSERT INTO `booking_equipment` (`id_booking_equipment`, `booking_id`, `equipment_id`) VALUES
(1, 1, 3),
(2, 1, 4),
(3, 2, 6),
(4, 2, 7),
(5, 2, 9),
(6, 2, 12),
(9, 4, 3),
(10, 4, 4),
(11, 5, 6),
(12, 5, 9),
(13, 5, 11),
(14, 5, 14);

-- --------------------------------------------------------

--
-- Table structure for table `cancelled_booking`
--

CREATE TABLE `cancelled_booking` (
  `cancelled_id` int(11) NOT NULL,
  `booking_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_department` varchar(255) NOT NULL,
  `room_id` int(11) NOT NULL,
  `desired_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `cancelled_by` int(11) NOT NULL,
  `cancelled_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cancelled_booking`
--

INSERT INTO `cancelled_booking` (`cancelled_id`, `booking_date`, `user_id`, `user_department`, `room_id`, `desired_date`, `start_time`, `end_time`, `cancelled_by`, `cancelled_at`) VALUES
(1, '2024-07-05 01:25:09', 3, 'บริหาร', 1, '2024-07-15', '08:00:00', '11:00:00', 3, '2024-07-05 01:25:38'),
(2, '2024-07-05 01:28:09', 6, 'งานประกัน', 1, '2024-07-30', '06:00:00', '11:30:00', 6, '2024-07-05 01:28:20'),
(3, '2024-07-05 01:27:13', 6, 'งานประกัน', 2, '2024-07-23', '08:30:00', '12:30:00', 2, '2024-07-05 01:29:23');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `room_id` int(11) DEFAULT NULL,
  `equipment_id` int(11) NOT NULL,
  `equipment_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`room_id`, `equipment_id`, `equipment_name`) VALUES
(1, 1, 'คอมพิวเตอร์'),
(1, 2, 'ไมโครโฟน 2'),
(1, 3, 'โน๊ตบุ๊ค'),
(1, 4, 'โปรเจคเตอร์'),
(1, 5, 'ลำโพง 2'),
(2, 6, 'โน๊ตบุ๊ค'),
(2, 7, 'โปรเจคเตอร์'),
(2, 8, 'คอมพิวเตอร์'),
(2, 9, 'ลำโพง 2'),
(2, 10, 'ลำโพง 4'),
(2, 11, 'ไมโครโฟน 2'),
(2, 12, 'ไมโครโฟน 4'),
(2, 13, 'ไมโครโฟน 8'),
(2, 14, 'TV'),
(3, 15, 'โน๊ตบุ๊ค'),
(3, 16, 'โปรเจคเตอร์'),
(3, 17, 'TV');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `user_id` int(24) NOT NULL,
  `user_username` varchar(255) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `user_firstname` varchar(255) DEFAULT NULL,
  `user_lastname` varchar(255) DEFAULT NULL,
  `user_tel` varchar(10) DEFAULT NULL,
  `user_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(255) NOT NULL,
  `room_detail` text DEFAULT NULL,
  `room_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `room_name`, `room_detail`, `room_status`) VALUES
(1, 'ห้องประชุม OPD', 'ห้องประชุม OPD ชั้น 2 ความจุ 30 คน', NULL),
(2, 'ห้องประชุม ตึกส่งเสริม', 'ห้องประชุม ตึกส่งเสริม ชั้น 2 จำนวนจุ 100 คน', NULL),
(3, 'ห้องประชุมที่ 3 (003)', 'ห้องประชุมที่ 3 (003) ', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_username` varchar(50) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_department` varchar(50) DEFAULT NULL,
  `user_status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_username`, `user_password`, `user_department`, `user_status`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'IT', 'Admin'),
(2, 'ไอที', 'e10adc3949ba59abbe56e057f20f883e', 'ไอที', 'Admin'),
(3, 'บริหาร', 'e10adc3949ba59abbe56e057f20f883e', 'บริหาร', 'User'),
(4, 'เภสัชกรรม', 'e10adc3949ba59abbe56e057f20f883e', 'เภสัชกรรม', 'User'),
(5, 'พยาบาล', 'e10adc3949ba59abbe56e057f20f883e', 'พยาบาล', 'User'),
(6, 'งานประกัน', 'e10adc3949ba59abbe56e057f20f883e', 'งานประกัน', 'User'),
(7, 'สุขภาพดิจิทัล', 'e10adc3949ba59abbe56e057f20f883e', 'สุขภาพดิจิทัล', 'User'),
(8, 'ทันตกรรม', 'e10adc3949ba59abbe56e057f20f883e', 'ทันตกรรม', 'User'),
(9, 'กายภาพบําบัด', 'e10adc3949ba59abbe56e057f20f883e', 'กายภาพบําบัด', 'User'),
(10, 'แพทย์', 'e10adc3949ba59abbe56e057f20f883e', 'แพทย์', 'User'),
(11, 'Lab', 'e10adc3949ba59abbe56e057f20f883e', 'Lab', 'User'),
(12, 'Er', 'e10adc3949ba59abbe56e057f20f883e', 'Er', 'User'),
(13, 'IPD', 'e10adc3949ba59abbe56e057f20f883e', 'IPD', 'User'),
(14, 'OPD', 'e10adc3949ba59abbe56e057f20f883e', 'OPD', 'User'),
(15, 'สุภาพจิต', 'e10adc3949ba59abbe56e057f20f883e', 'สุภาพจิต', 'User'),
(16, 'เวชกรรม', 'e10adc3949ba59abbe56e057f20f883e', 'เวชกรรม', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`);

--
-- Indexes for table `booking_equipment`
--
ALTER TABLE `booking_equipment`
  ADD PRIMARY KEY (`id_booking_equipment`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `booking_equipment_ibfk_1` (`booking_id`);

--
-- Indexes for table `cancelled_booking`
--
ALTER TABLE `cancelled_booking`
  ADD PRIMARY KEY (`cancelled_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`),
  ADD KEY `equipment_ibfk_1` (`room_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `booking_equipment`
--
ALTER TABLE `booking_equipment`
  MODIFY `id_booking_equipment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `cancelled_booking`
--
ALTER TABLE `cancelled_booking`
  MODIFY `cancelled_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `user_id` int(24) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_equipment`
--
ALTER TABLE `booking_equipment`
  ADD CONSTRAINT `booking_equipment_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_equipment_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`);

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_room_id` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
