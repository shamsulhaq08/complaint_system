-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2025 at 01:47 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `complaint_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `staff_ids` varchar(255) DEFAULT NULL,
  `complaint_desc` text NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_email` varchar(100) NOT NULL,
  `client_phone` varchar(20) DEFAULT NULL,
  `preferred_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `staff_ids`, `complaint_desc`, `client_name`, `client_email`, `client_phone`, `preferred_date`) VALUES
(1, '1,6', 'Dolorem dolorem aspe', 'Lois Marshall', 'nypu@mailinator.com', '+1 (149) 161-9229', '2016-03-28 00:00:00'),
(2, '1,6', 'Dolorem dolorem aspe', 'Lois Marshall', 'nypu@mailinator.com', '+1 (149) 161-9229', '2016-03-28 00:00:00'),
(3, '1,6', 'Dolorem dolorem aspe', 'Lois Marshall', 'nypu@mailinator.com', '+1 (149) 161-9229', '2016-03-28 00:00:00'),
(4, '1,6', 'Ducimus voluptas qu', 'Cathleen Bruce', 'kusefovu@mailinator.com', '+1 (928) 569-5998', '1985-07-01 00:00:00'),
(5, '1,6', 'Ducimus voluptas qu', 'Cathleen Bruce', 'kusefovu@mailinator.com', '+1 (928) 569-5998', '1985-07-01 00:00:00'),
(6, '1,6', 'Ducimus voluptas qu', 'Cathleen Bruce', 'kusefovu@mailinator.com', '+1 (928) 569-5998', '1985-07-01 00:00:00'),
(7, '1,6', 'Ducimus voluptas qu', 'Cathleen Bruce', 'kusefovu@mailinator.com', '+1 (928) 569-5998', '1985-07-01 00:00:00'),
(8, '1,6,9', 'Et magnam qui deseru', 'Yetta Gillespie', 'cika@mailinator.com', '+1 (928) 749-3029', '2008-09-08 00:00:00'),
(9, '1,6,9', 'Itaque proident dol', 'Hashim Hansen', 'mety@mailinator.com', '+1 (701) 811-5849', '2004-03-29 00:00:00'),
(10, '1', 'Nisi ex proident pe', 'Nevada Hart', 'gozi@mailinator.com', '+1 (211) 857-2182', '1995-06-01 00:00:00'),
(11, '1,6', 'Tempor veniam sit e', 'Adam Huff', 'kavo@mailinator.com', '+1 (899) 845-9209', '2015-07-12 00:00:00'),
(12, '9', 'Aut magnam et sunt v', 'Whitney Perkins', 'jomuhunij@mailinator.com', '+1 (619) 623-6057', '2024-12-23 00:00:00'),
(13, '9', 'Aut magnam et sunt v', 'Whitney Perkins', 'jomuhunij@mailinator.com', '+1 (619) 623-6057', '2024-12-23 00:00:00'),
(14, '9', 'Aut magnam et sunt v', 'Whitney Perkins', 'jomuhunij@mailinator.com', '+1 (619) 623-6057', '2024-12-23 00:00:00'),
(15, '6', 'Quas ut nihil itaque', 'Wendy Wynn', '', '+1 (295) 321-5355', '2025-05-02 00:00:00'),
(16, '1', 'Rerum laboriosam od wfsdgasdfasdfasdf', 'Joelle Williamson', 'voser@mailinator.com', '+1 (474) 563-6832', '2021-03-01 17:39:00'),
(17, '6', 'ABc', 'Muhammad Ijaz', 'ijaz@chapter2.com.pk', '0616222637', '2025-05-02 18:50:00'),
(18, '1,6,9', 'gikhk', 'Mahmood Ali', '', '03457345522', '0000-00-00 00:00:00'),
(19, '', 'Optio excepturi off', 'shams ulhaq', 'demo@gmail.com', '55646456', '2023-10-20 15:23:00'),
(20, '', 'Optio excepturi off', 'shams ulhaq', 'demo@gmail.com', '55646456', '2023-10-20 15:23:00'),
(21, '1', 'Excepteur quia numqu', 'Nola Anderson', 'zeqaku@mailinator.com', '+1 (924) 652-5421', '1971-09-10 16:19:00'),
(22, '1', 'Excepteur quia numqu', 'Nola Anderson', 'zeqaku@mailinator.com', '+1 (924) 652-5421', '1971-09-10 16:19:00'),
(23, '6', 'sdfsdf', 'shams ulhaq', 'shamsulhaq08@gmail.com', '03427084016', '2025-05-30 03:47:00'),
(24, '', 'Voluptate consequatu', 'Regina Wiley', 'remehyt@mailinator.com', '+1 (348) 899-3115', '2004-06-25 03:17:00'),
(25, '', 'Recusandae Optio s', 'Tamekah Gilliam', 'qigaxafyn@mailinator.com', '+1 (484) 398-5341', '1986-10-04 10:04:00'),
(26, '1,6', 'Ea earum reiciendis ', 'Carter Skinner', 'dusiwapy@mailinator.com', '+1 (623) 483-5356', '1972-02-09 04:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_files`
--

CREATE TABLE `complaint_files` (
  `id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `complaint_files`
--

INSERT INTO `complaint_files` (`id`, `complaint_id`, `file_path`, `file_type`, `uploaded_at`) VALUES
(1, 23, 'uploads/6815bc058ef62_275817757_04d756be-0553-4b5d-91ed-ff97c57dfaf0.jpg', 'image/jpeg', '2025-05-03 06:47:33'),
(2, 23, 'uploads/6815bc059f711_voice_recording.webm', 'audio/webm', '2025-05-03 06:47:33'),
(3, 24, 'uploads/6815c22fb4375_28016747_qx8e_qt0s_220513.jpg', 'image/jpeg', '2025-05-03 07:13:51'),
(4, 24, 'uploads/6815c22fbefaf_voice_recording.webm', 'audio/webm', '2025-05-03 07:13:51'),
(5, 26, 'uploads/6815c899e0fae_275817757_04d756be-0553-4b5d-91ed-ff97c57dfaf0.jpg', 'image/jpeg', '2025-05-03 07:41:13'),
(6, 26, 'uploads/6815c89a0082c_voice_recording.webm', 'audio/webm', '2025-05-03 07:41:14');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_email` varchar(100) NOT NULL,
  `feedback_title` varchar(150) NOT NULL,
  `feedback_desc` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `client_name`, `client_email`, `feedback_title`, `feedback_desc`, `submitted_at`, `phone`) VALUES
(1, 'Isabelle Atkins', 'juvil@mailinator.com', 'Sint labore ducimus', 'Ea beatae maiores eo', '2025-05-01 09:01:42', ''),
(2, 'Bethany Schroeder', 'gewysuvabo@mailinator.com', 'Similique commodo fu', 'Et accusamus est ul', '2025-05-02 06:50:16', ''),
(3, 'Rhiannon Ellis', 'nedeny@mailinator.com', 'Officiis dolorem com', 'Ipsam excepturi in m', '2025-05-02 06:53:17', '+1 (903) 847-4847'),
(4, 'Mahmood', 'drmahmudali@gmail.com', 'F', 'Good', '2025-05-03 06:21:16', '03457345522');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `designation`, `image`, `active`) VALUES
(1, 'Noman', 'demo@gmail.com', 'Gift Maker', 'uploads/1746008300_360_F_236992283_sNOxCVQeFLd5pdqaKGh8DRGMZy7P4XKm.jpg', 1),
(6, 'Umar', 'fugix@mailinator.com', 'In aliqua Anim sed', 'uploads/68149740d181c_13272.jpg', 1),
(9, 'Salman', 'bydiwase@mailinator.com', 'Labore et molestiae', 'uploads/1746182159_103964.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'shamsulhaq08@gmail.com', '$2y$10$rBv22EBn0nJuCw4Ks3OxruJipW540sr18hWCgq9gOsuDDuPc.CjkG', 'admin', '2025-05-01 09:29:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaint_files`
--
ALTER TABLE `complaint_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_id` (`complaint_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `complaint_files`
--
ALTER TABLE `complaint_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaint_files`
--
ALTER TABLE `complaint_files`
  ADD CONSTRAINT `complaint_files_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
