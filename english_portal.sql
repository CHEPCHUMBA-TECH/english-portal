-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 05:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `english_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `question` text NOT NULL,
  `keywords` text NOT NULL,
  `max_score` int(11) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `module`, `question`, `keywords`, `max_score`, `date_added`) VALUES
(1, 'Vocabulary', 'Give a synonym for significant  ', 'important, crucial', 2, '2026-03-28 15:30:15');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `date_taken` datetime DEFAULT current_timestamp(),
  `maximum_score` int(11) NOT NULL DEFAULT 10,
  `total_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`id`, `user_id`, `module`, `score`, `date_taken`, `maximum_score`, `total_score`) VALUES
(52, NULL, 'Reading', 0, '2026-03-19 20:58:56', 20, NULL),
(53, NULL, 'Vocabulary', 0, '2026-03-19 21:08:02', 20, NULL),
(54, NULL, 'Vocabulary', 0, '2026-03-19 21:11:07', 20, NULL),
(55, 4, 'Vocabulary', 0, '2026-03-19 21:23:02', 20, NULL),
(56, 4, 'Grammar', 42, '2026-03-19 21:28:15', 50, NULL),
(57, 4, 'Writing', 0, '2026-03-19 21:35:37', 20, NULL),
(58, 4, 'Vocabulary', 0, '2026-03-19 21:36:56', 20, NULL),
(59, 4, 'Vocabulary', 0, '2026-03-19 21:40:33', 20, NULL),
(60, 3, 'Vocabulary', 0, '2026-03-21 13:35:05', 20, NULL),
(61, 4, 'Reading', 0, '2026-03-21 14:20:49', 20, NULL),
(62, 3, 'Listening & Speaking', 0, '2026-03-21 14:23:39', 18, NULL),
(63, 3, 'Reading', 0, '2026-03-21 14:25:22', 20, NULL),
(64, 3, 'Reading', 0, '2026-03-21 15:35:09', 20, NULL),
(65, 3, 'Vocabulary', 0, '2026-03-21 15:36:44', 20, NULL),
(66, 4, 'Listening & Speaking', 4, '2026-03-21 15:39:33', 18, NULL),
(67, 3, 'Reading', 0, '2026-03-21 15:46:08', 20, NULL),
(68, 3, 'Reading', 0, '2026-03-21 15:46:13', 20, NULL),
(69, 4, 'Listening & Speaking', 0, '2026-03-21 15:47:34', 18, NULL),
(70, 3, 'Listening & Speaking', 0, '2026-03-21 16:09:02', 18, NULL),
(71, 3, 'Grammar', 20, '2026-03-22 15:15:43', 20, NULL),
(72, 3, 'Writing', 17, '2026-03-22 15:23:30', 50, NULL),
(73, 3, 'Vocabulary', 20, '2026-03-22 15:31:15', 20, NULL),
(74, 4, 'Listening & Speaking', 11, '2026-03-22 15:38:28', 18, NULL),
(75, 3, 'Listening & Speaking', 0, '2026-03-22 16:08:46', 18, NULL),
(76, 3, 'Vocabulary', 0, '2026-03-28 15:31:52', 20, NULL),
(77, 6, 'Reading', 10, '2026-04-02 17:17:18', 20, NULL),
(78, 6, 'Writing', 34, '2026-04-02 17:18:21', 50, NULL),
(79, 6, 'Grammar', 20, '2026-04-02 17:19:35', 20, NULL),
(80, 6, 'Listening & Speaking', 6, '2026-04-02 17:23:51', 18, NULL),
(81, 6, 'Listening & Speaking', 0, '2026-04-02 17:24:11', 18, NULL),
(82, 6, 'Vocabulary', 0, '2026-04-02 17:24:37', 20, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(3, 'student 1', '12345', 'student'),
(4, 'Teacher 1  ', '678910', 'teacher'),
(5, 'Claudia', '39482061', 'teacher'),
(6, 'vincent', '0717110505', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
