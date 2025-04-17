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
