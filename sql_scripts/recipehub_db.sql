-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2025 at 10:31 AM
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
-- Database: `recipehub_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `meal_plans`
--

CREATE TABLE `meal_plans` (
  `meal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meal_name` varchar(255) NOT NULL,
  `meal_date` date NOT NULL,
  `meal_time` enum('Breakfast','Lunch','Dinner') NOT NULL,
  `meal_type` enum('recipe','custom') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `recipe_id` int(11) DEFAULT NULL,
  `custom_meal` varchar(255) DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 1 COMMENT 'Number of days this meal is planned for',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `meal_plans`
--

INSERT INTO `meal_plans` (`meal_id`, `user_id`, `meal_name`, `meal_date`, `meal_time`, `meal_type`, `created_at`, `recipe_id`, `custom_meal`, `duration`, `updated_at`) VALUES
(4, 6, 'Fish', '2025-03-28', 'Lunch', 'recipe', '2025-03-12 13:49:16', 4, NULL, 5, '2025-03-26 08:02:14'),
(5, 6, 'Bear', '2025-03-26', 'Dinner', 'custom', '2025-03-12 13:56:35', NULL, 'Bear and biscuit', 9, '2025-03-26 07:43:05'),
(6, 6, 'Fish and me ', '2025-04-12', 'Breakfast', 'recipe', '2025-04-12 13:54:04', 3, NULL, 9, NULL),
(7, 6, 'The cube', '2025-04-07', 'Lunch', 'custom', '2025-04-12 13:54:32', NULL, 'Eat rubic cube', 21, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `meal_template`
--

CREATE TABLE `meal_template` (
  `template_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_template`
--

INSERT INTO `meal_template` (`template_id`, `user_id`, `template_name`, `description`, `created_at`, `updated_at`) VALUES
(4, 6, 'B', 'B', '2025-03-27 05:20:52', NULL),
(7, 6, 'Sushi mentai', 'Sushi mentai super good ', '2025-04-12 09:06:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `meal_template_details`
--

CREATE TABLE `meal_template_details` (
  `template_detail_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `meal_time` enum('Breakfast','Lunch','Dinner') NOT NULL,
  `meal_name` varchar(255) NOT NULL,
  `meal_type` varchar(255) NOT NULL,
  `recipe_id` int(11) DEFAULT NULL,
  `custom_meal` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_template_details`
--

INSERT INTO `meal_template_details` (`template_detail_id`, `template_id`, `day_of_week`, `meal_time`, `meal_name`, `meal_type`, `recipe_id`, `custom_meal`) VALUES
(85, 4, 'Monday', 'Breakfast', 'B', 'custom', NULL, 'dadawdaw'),
(86, 4, 'Monday', 'Lunch', 'B', 'recipe', 4, NULL),
(87, 4, 'Monday', 'Dinner', 'B', 'recipe', 2, NULL),
(88, 4, 'Tuesday', 'Breakfast', 'B', 'recipe', 2, NULL),
(89, 4, 'Tuesday', 'Lunch', 'B', 'recipe', 1, NULL),
(90, 4, 'Tuesday', 'Dinner', 'B', 'recipe', 1, NULL),
(91, 4, 'Wednesday', 'Breakfast', 'B', 'recipe', 4, NULL),
(92, 4, 'Wednesday', 'Lunch', 'B', 'recipe', 4, NULL),
(93, 4, 'Wednesday', 'Dinner', 'B', 'recipe', 2, NULL),
(94, 4, 'Thursday', 'Breakfast', 'B', 'recipe', 1, NULL),
(95, 4, 'Thursday', 'Lunch', 'B', 'recipe', 2, NULL),
(96, 4, 'Thursday', 'Dinner', 'B', 'recipe', 2, NULL),
(97, 4, 'Friday', 'Breakfast', 'B', 'recipe', 2, NULL),
(98, 4, 'Friday', 'Lunch', 'B', 'recipe', 3, NULL),
(99, 4, 'Friday', 'Dinner', 'B', 'recipe', 3, NULL),
(100, 4, 'Saturday', 'Breakfast', 'B', 'recipe', 2, NULL),
(101, 4, 'Saturday', 'Lunch', 'B', 'recipe', 2, NULL),
(102, 4, 'Saturday', 'Dinner', 'B', 'recipe', 3, NULL),
(103, 4, 'Sunday', 'Breakfast', 'B', 'recipe', 2, NULL),
(104, 4, 'Sunday', 'Lunch', 'B', 'recipe', 1, NULL),
(105, 4, 'Sunday', 'Dinner', 'B', 'recipe', 3, NULL),
(148, 7, 'Monday', 'Breakfast', 'A', 'recipe', 3, NULL),
(149, 7, 'Monday', 'Lunch', 'A', 'recipe', 1, NULL),
(150, 7, 'Monday', 'Dinner', 'A', 'recipe', 4, NULL),
(151, 7, 'Tuesday', 'Breakfast', 'A', 'recipe', 3, NULL),
(152, 7, 'Tuesday', 'Lunch', 'A', 'recipe', 3, NULL),
(153, 7, 'Tuesday', 'Dinner', 'A', 'recipe', 3, NULL),
(154, 7, 'Wednesday', 'Breakfast', 'A', 'recipe', 1, NULL),
(155, 7, 'Wednesday', 'Lunch', 'A', 'recipe', 2, NULL),
(156, 7, 'Wednesday', 'Dinner', 'A', 'recipe', 5, NULL),
(157, 7, 'Thursday', 'Breakfast', 'A', 'recipe', 1, NULL),
(158, 7, 'Thursday', 'Lunch', 'A', 'recipe', 3, NULL),
(159, 7, 'Thursday', 'Dinner', 'A', 'recipe', 4, NULL),
(160, 7, 'Friday', 'Breakfast', 'A', 'recipe', 3, NULL),
(161, 7, 'Friday', 'Lunch', 'A', 'recipe', 2, NULL),
(162, 7, 'Friday', 'Dinner', 'A', 'recipe', 3, NULL),
(163, 7, 'Saturday', 'Breakfast', 'A', 'recipe', 3, NULL),
(164, 7, 'Saturday', 'Lunch', 'A', 'recipe', 2, NULL),
(165, 7, 'Saturday', 'Dinner', 'A', 'recipe', 5, NULL),
(166, 7, 'Sunday', 'Breakfast', 'A', 'recipe', 4, NULL),
(167, 7, 'Sunday', 'Lunch', 'A', 'recipe', 4, NULL),
(168, 7, 'Sunday', 'Dinner', 'A', 'recipe', 3, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD PRIMARY KEY (`meal_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `meal_template`
--
ALTER TABLE `meal_template`
  ADD PRIMARY KEY (`template_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `meal_template_details`
--
ALTER TABLE `meal_template_details`
  ADD PRIMARY KEY (`template_detail_id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `meal_plans`
--
ALTER TABLE `meal_plans`
  MODIFY `meal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meal_template`
--
ALTER TABLE `meal_template`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `meal_template_details`
--
ALTER TABLE `meal_template_details`
  MODIFY `template_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD CONSTRAINT `recipe_id` FOREIGN KEY (`recipe_id`) REFERENCES `recipe` (`recipe_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meal_template`
--
ALTER TABLE `meal_template`
  ADD CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meal_template_details`
--
ALTER TABLE `meal_template_details`
  ADD CONSTRAINT `template` FOREIGN KEY (`template_id`) REFERENCES `meal_template` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
