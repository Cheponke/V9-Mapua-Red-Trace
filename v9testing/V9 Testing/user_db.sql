-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2026 at 10:06 AM
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
-- Database: `user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `InventoryID` int(6) UNSIGNED ZEROFILL NOT NULL,
  `DonationID` int(6) UNSIGNED ZEROFILL NOT NULL,
  `Inventory_BloodType` varchar(3) NOT NULL,
  `Inventory_Volume` decimal(5,2) NOT NULL,
  `Inventory_ExpDate` date DEFAULT (curdate() + interval 35 day),
  `Inventory_Status` enum('Available','Reserved','Expired') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`InventoryID`, `DonationID`, `Inventory_BloodType`, `Inventory_Volume`, `Inventory_ExpDate`, `Inventory_Status`) VALUES
(000002, 000026, 'A+', 450.00, '2026-04-17', 'Available'),
(000003, 000028, 'B-', 450.00, '2026-04-17', 'Available'),
(000004, 000027, 'A+', 470.00, '2026-04-17', 'Available'),
(000005, 000029, 'B-', 460.00, '2026-04-17', 'Available'),
(000006, 000033, 'B-', 450.00, '2026-04-17', 'Available');

--
-- Triggers `inventory`
--
DELIMITER $$
CREATE TRIGGER `set_inventory_expiry` BEFORE INSERT ON `inventory` FOR EACH ROW BEGIN
    IF NEW.Inventory_ExpDate IS NULL OR NEW.Inventory_ExpDate = '0000-00-00' THEN
        SET NEW.Inventory_ExpDate = DATE_ADD(CURDATE(), INTERVAL 35 DAY);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `priority` varchar(10) DEFAULT NULL,
  `recipients` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `title`, `message`, `priority`, `recipients`, `created_at`) VALUES
(000001, 'urgent', 'Help', 'Need Blodd', 'high', 'ab-neg', '2026-03-12 10:12:07'),
(000002, 'event', 'Donation Drive', 'There will be a donation drive soon.', 'medium', 'all', '2026-03-13 14:50:36'),
(000003, 'urgent', 'Emergency', 'help', 'high', 'all', '2026-03-13 14:52:35');

-- --------------------------------------------------------

--
-- Table structure for table `screenings`
--

CREATE TABLE `screenings` (
  `id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `blood_pressure` varchar(7) NOT NULL,
  `temperature` decimal(4,1) NOT NULL,
  `pulse_rate` int(3) UNSIGNED NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `hemoglobin_level` decimal(4,1) NOT NULL,
  `donor_id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `screenings`
--

INSERT INTO `screenings` (`id`, `blood_pressure`, `temperature`, `pulse_rate`, `weight`, `hemoglobin_level`, `donor_id`, `date`, `location`) VALUES
(000026, '110/90', 36.9, 76, 175.00, 15.7, 000007, '2026-03-11', 'Room 211'),
(000027, '110/90', 34.9, 78, 164.00, 15.0, 000017, '2026-03-12', 'Room 211'),
(000028, '120/80', 36.5, 72, 165.00, 13.5, 000018, '2026-03-13', '201'),
(000029, '120/80', 36.5, 72, 165.00, 13.5, 000018, '2026-03-13', '201'),
(000030, '', 0.0, 0, 0.00, 0.0, 000018, '2026-03-13', ''),
(000031, '', 0.0, 0, 0.00, 0.0, 000018, '2026-03-13', ''),
(000032, '', 0.0, 0, 0.00, 0.0, 000018, '2026-03-13', ''),
(000033, '120/80', 36.5, 72, 165.00, 13.5, 000018, '2026-03-13', '201');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(6) UNSIGNED ZEROFILL NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('donor','staff') NOT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `medical_condition` text DEFAULT NULL,
  `current_medication` text DEFAULT NULL,
  `phone_number` varchar(20) NOT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `birthday`, `gender`, `blood_type`, `weight`, `medical_condition`, `current_medication`, `phone_number`, `street_address`, `city`, `zip`, `contact_name`, `contact_phone`, `status`) VALUES
(000001, 'Godwin', 'Ona', 'godwinona922@gmail.com', '$2y$10$yq2/XamFjBv6TY/.i9E2pOi3oQ8OwQKmlXe.wNLo52T3xBGQ2kOp.', 'donor', '0000-00-00', '', '', 0.00, '', '', '+639668099143', '', '', '', '', '', 'active'),
(000005, 'May', 'Ona', 'may@gmail.com', '$2y$10$kbNROA3J2EMChrsJk4Bv5.P0Qw/l1bhi5UupvLfaXtalsNDJxVz42', 'donor', '0000-00-00', '', '', 0.00, '', '', '+639668099144', '', '', '', '', '', 'pending'),
(000006, 'cat', 'cat', 'cat@gmail.com', '$2y$10$qbhH/gLJDWIUq4.65oaN1.nkaZVLzO/K4mjs.u4lOw33BdXEj6c56', 'donor', '0000-00-00', '', '', 0.00, '', '', '+639668099145', '', '', '', '', '', 'pending'),
(000007, 'ken', 'ken', 'ken@gmail.com', '$2y$10$SadSyfn/7mmZjdPSNLk5f.FuC03/rY/GsqgMbh/4ohsDWDo2UghOC', 'donor', '0000-00-00', '', '', 0.00, '', '', '+639668099148', '', '', '', '', '', 'pending'),
(000008, 'kat', 'tak', 'kat@gmail.com', '$2y$10$/Ebqq5Qsz6B2uPUgKCu6MO/EhoyuSj4sRSUbbLxZ068eZopQP7r.O', 'donor', '2021-11-11', 'Female', 'B+', 121.00, 'no', 'no', '+639668099141', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668099143', 'pending'),
(000009, 'jor', 'caye', 'jor@gmail.com', '$2y$10$cbriOfW7BvphiheGe967e.Mb9rpNlYCI6krRzOhPDzV2tqL2q1Qym', 'donor', '2025-12-10', 'Male', 'B+', 555.00, 'no', 'no', '+639668099143', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668099143', 'pending'),
(000011, 'ken', 'han', 'kenn@gmail.com', '$2y$10$f.ayIm08yfr6m.MRuMuuUeGGueIYRGjrRkPPsKDkie1h9im5l1GSG', 'donor', '2025-07-10', 'Male', 'A+', 555.00, 'no', 'no', '+639668099143', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668099143', 'pending'),
(000012, 'lan', 'nan', 'lan@gmail.com', '$2y$10$sBbFsTIzuW/IkwTH.oYOaO9mTa.lpP6x1wMr8TLGCeNx4FcVXMFVu', 'donor', '2025-10-10', 'Male', 'B+', 555.00, 'none', 'none', '+639668099155', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668099143', 'pending'),
(000013, 'Jordana', 'Ca', 'jordi@gmail.com', '$2y$10$Kee4oKdMFLZN26aLfZmg8OKOnQPSe8s5qno4F6HUW7WM1dv1H67Eu', 'staff', '2016-01-28', 'Female', 'AB+', 566.00, 'none', 'none', '+639668066666', 'BRGY.San Buting', 'Makatijbd', '4027', 'Mondae', '+639668666666AFWETWE', 'pending'),
(000014, 'may', 'nay', 'may@hospital.com', '$2y$10$2PMFWxAp/o7hq5KgAnoTGeH8FsgNQ2UCm3D8tiAmfkGkKLlGpAk9a', 'donor', '2025-12-02', 'Female', 'A-', 555.00, 'no', 'no', '+639668555555', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668099143', 'pending'),
(000015, 'Pao', 'Pa', 'pao@gmail.com', '$2y$10$Q5CBW7mvFPmCgr6BntKB4.xABVqXWEUJKTu2aY2hfQo9cHGr.eV72', 'donor', '2025-12-03', 'Male', 'A+', 134.00, 'no', 'no', '+639668066666', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668565656', 'pending'),
(000016, 'Han', 'han', 'han@gmail.com', '$2y$10$EF9bp.YlvVj9eBg.oiC3oe3Lt5.Rr68nubBxyEzUrorPuSnsq6Cwi', 'donor', '2026-01-28', 'Male', 'A-', 565.00, 'no', 'no', '+639668099666', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668095555', 'pending'),
(000017, 'Monday', 'Day', 'mon@gmail.com', '$2y$10$22qcx2RB4wwqpy/JAgoq4uTrRVLv6kEGzgMg1KBxLUBwN8py3Qy6G', 'donor', '2025-11-26', 'Female', 'A+', 164.00, 'No', 'No', '+639668099656', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668099143', 'inactive'),
(000018, 'Blase Maree', 'Handayan', 'bmrhandayan@mymail.mapua.edu.ph', '$2y$10$OXLUogg4S0JadbBWasoxzOq1QRLZXCs3TAKIR/nCKEbzGcsZneu5q', 'donor', '2017-02-03', 'Female', 'B-', 115.00, '', '', '+639151800728', 'P. Ocampo', 'Makati', '1200', 'HANDAYAN, Blase Maree Regencia', '+639569911944', 'inactive'),
(000019, 'Mani', 'San', 'mani@gmail.com', '$2y$10$dC6iM4Ug9hHQgPR2wpW11u2k/sQFWQbzejIQTEZQ1QuamEJlCIlhW', 'donor', '2025-10-08', 'Male', 'AB+', 145.00, 'None', 'None', '+639665656123', 'BRGY.Sta Cruz', 'Quezon', '4026', 'Godwin Oma', '+639668099155', 'pending'),
(000021, 'RN. May', 'Yam', 'mayy@hospital.com', '$2y$10$sZHIVm8V.Xegk54lPZtgie2GD3fd4TTgEOPOl8PwNYn2H90AWebIe', 'staff', '2026-01-21', 'Female', 'O-', 123.00, 'None', 'None', '+639668099555', 'BRGY.BUROL', 'Calamba', '4027', 'Godwin Ona', '+639668099143', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`InventoryID`),
  ADD KEY `DonationID` (`DonationID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `screenings`
--
ALTER TABLE `screenings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_donor_user` (`donor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `InventoryID` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `screenings`
--
ALTER TABLE `screenings`
  MODIFY `id` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `DonationID` FOREIGN KEY (`DonationID`) REFERENCES `screenings` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `screenings`
--
ALTER TABLE `screenings`
  ADD CONSTRAINT `DonorID` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donor_user` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `auto_expire_inventory` ON SCHEDULE EVERY 1 DAY STARTS '2026-03-13 14:46:43' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE inventory
SET Inventory_Status = 'Expired'
WHERE Inventory_ExpDate < CURDATE()
AND Inventory_Status != 'Expired'$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
