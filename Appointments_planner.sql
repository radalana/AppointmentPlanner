-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 02, 2024 at 11:39 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Appointments_planner`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `creator` varchar(200) NOT NULL,
  `location` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `duration_min` int(11) NOT NULL,
  `date_options_id` int(11) NOT NULL,
  `expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `title`, `creator`, `location`, `description`, `duration_min`, `date_options_id`, `expiration_date`, `created_at`) VALUES
(1, 'termin1', 'creator1', '1210', 'termin 1 desc', 90, 1, '2024-05-10 10:25:00', '0000-00-00 00:00:00'),
(2, 'termin 2', 'creator2', '2222', 'description 2', 60, 2, '2024-04-08 10:25:25', '0000-00-00 00:00:00'),
(3, 'termin 3', 'creator3', '3333', 'description termin 3', 120, 3, '2024-04-08 10:25:14', '0000-00-00 00:00:00'),
(4, 'Frischmalerei', 'Svetlana', 'Waldviertel', 'nimm dein Oil und Pinze', 180, 4, '2024-05-03 22:00:00', '2024-05-02 20:11:48');

-- --------------------------------------------------------

--
-- Table structure for table `date_options`
--

CREATE TABLE `date_options` (
  `id` int(11) NOT NULL,
  `option1` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `option2` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `option3` timestamp NULL DEFAULT NULL,
  `option4` timestamp NULL DEFAULT NULL,
  `option5` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `date_options`
--

INSERT INTO `date_options` (`id`, `option1`, `option2`, `option3`, `option4`, `option5`) VALUES
(1, '2024-04-25 18:04:22', '2024-04-03 13:04:22', '2024-05-03 05:33:22', '2024-04-17 02:15:22', '2024-04-10 06:26:00'),
(2, '2024-06-01 08:06:08', '2024-06-21 13:06:08', '2024-06-20 13:06:08', NULL, NULL),
(3, '2024-04-11 03:06:50', '2024-04-30 02:06:50', NULL, NULL, NULL),
(4, '2024-05-03 06:00:00', '2024-05-02 08:00:00', '2024-05-02 10:32:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nickname` varchar(50) NOT NULL COMMENT 'unique?'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nickname`) VALUES
(4, 'anna'),
(6, 'Barbara'),
(7, 'Cate'),
(5, 'ivan'),
(10, 'Panda'),
(11, 'Rabia'),
(2, 'Roma'),
(8, 'Spider Man'),
(1, 'Sveta'),
(9, 'Victor');

-- --------------------------------------------------------

--
-- Table structure for table `user_selections`
--

CREATE TABLE `user_selections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'user votes once - uniqe',
  `appointment_id` int(11) NOT NULL,
  `opt1_check` tinyint(1) NOT NULL,
  `opt2_check` tinyint(1) NOT NULL,
  `opt3_check` tinyint(1) NOT NULL,
  `opt4_check` tinyint(1) NOT NULL,
  `opt5_check` tinyint(1) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='к конкретному  app results';

--
-- Dumping data for table `user_selections`
--

INSERT INTO `user_selections` (`id`, `user_id`, `appointment_id`, `opt1_check`, `opt2_check`, `opt3_check`, `opt4_check`, `opt5_check`, `comment`, `created_at`) VALUES
(1, 4, 1, 0, 1, 1, 1, 0, 'i late', '2024-05-02 11:52:10'),
(3, 5, 3, 1, 1, 0, 0, 0, '', '2024-05-02 11:52:10'),
(4, 6, 2, 1, 0, 1, 0, 0, 'Can i take my dog?', '2024-05-02 11:52:10'),
(5, 6, 2, 1, 0, 1, 0, 0, 'Can i take my dog?', '2024-05-02 11:52:10'),
(6, 1, 3, 1, 0, 0, 0, 0, '', '2024-05-02 13:21:36'),
(7, 7, 1, 1, 0, 0, 0, 1, '', '2024-05-02 15:18:32'),
(8, 8, 1, 1, 0, 1, 0, 0, '', '2024-05-02 19:43:25'),
(9, 9, 4, 0, 1, 0, 0, 0, 'I can be late', '2024-05-02 21:21:19'),
(10, 10, 4, 1, 1, 1, 0, 0, '', '2024-05-02 21:29:12'),
(11, 11, 4, 1, 0, 0, 0, 0, '', '2024-05-02 21:37:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `date_options`
--
ALTER TABLE `date_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nickname` (`nickname`);

--
-- Indexes for table `user_selections`
--
ALTER TABLE `user_selections`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `date_options`
--
ALTER TABLE `date_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_selections`
--
ALTER TABLE `user_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
