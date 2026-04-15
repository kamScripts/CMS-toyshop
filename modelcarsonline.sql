-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 15, 2026 at 11:27 AM
-- Server version: 8.0.43
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `modelcarsonline`
--

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int NOT NULL,
  `brand_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `brand_name`) VALUES
(1, 'Pro Models'),
(3, 'Hot Wheels'),
(6, 'testBrand');

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE `collection` (
  `collection_id` int NOT NULL,
  `category_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collection`
--

INSERT INTO `collection` (`collection_id`, `category_name`) VALUES
(1, 'Die-cast Cars'),
(2, 'New collection');

-- --------------------------------------------------------

--
-- Table structure for table `model`
--

CREATE TABLE `model` (
  `model_id` int NOT NULL,
  `model_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `collection_id` int DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  `scale_id` int DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model`
--

INSERT INTO `model` (`model_id`, `model_name`, `collection_id`, `brand_id`, `scale_id`, `description`) VALUES
(1, 'Ford Mondeo', 1, 1, 1, 'Die-cast model Ford Mondeo'),
(2, 'Honda Civic', 1, 1, 1, 'Die-cast model Honda Civic Type R'),
(3, 'Hyundai i20', 1, 1, 1, 'Die-cast model Hyundai i20'),
(4, 'Volvo F12', 1, 1, 1, 'Die-cast model Volvo F12'),
(6, 'Suzuki Swift', 1, 1, 1, 'Suzuki Swift die-cast Model'),
(7, 'Ferrari', 1, 3, 1, 'Ferrari Sport Car'),
(8, 'FIAT', 1, 3, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scale`
--

CREATE TABLE `scale` (
  `scale_id` int NOT NULL,
  `scale_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scale`
--

INSERT INTO `scale` (`scale_id`, `scale_name`) VALUES
(1, '1:64'),
(2, '1:32'),
(4, '1:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(16) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`) VALUES
(4, 'admin', 'admin@gmail.com', '$2y$10$Q0XCl7tKahSnM0ObGJ5ajO3rGBmErBzqPyBe6X1guyqwxxtUY3g0e'),
(7, 'kamil1', 'kamil@gmail.com', '$2y$10$X9GQJSXipEexcI.eW8csuOxoDFdpwXdbQIuo5XxRqJBmTupjTgbVW');

-- --------------------------------------------------------

--
-- Table structure for table `variant`
--

CREATE TABLE `variant` (
  `variant_id` int NOT NULL,
  `model_id` int DEFAULT NULL,
  `variant` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sku` varchar(12) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `price` decimal(5,2) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `imagepath` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `variant`
--

INSERT INTO `variant` (`variant_id`, `model_id`, `variant`, `sku`, `price`, `stock`, `imagepath`) VALUES
(1, 1, 'Titanium X', 'MON-TIT-001', 19.99, 12, 'FordMondeoToyCar2013.png'),
(2, 2, 'Type R', 'HON-R-002', 24.99, 10, 'hondaCivicTypeR2020.png'),
(3, 3, 'Play', 'HYU-PL-003', 14.99, 12, 'HyundaiI20Play2020 .png'),
(4, 4, 'Hauler Edition', 'VOL-H-004', 29.99, 5, 'VolvoF122020uck model.png'),
(19, 6, 'Coupe', NULL, 9.99, 10, 'placeholder.png'),
(22, 8, '126P', NULL, 25.50, 10, 'placeholder.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `collection`
--
ALTER TABLE `collection`
  ADD PRIMARY KEY (`collection_id`);

--
-- Indexes for table `model`
--
ALTER TABLE `model`
  ADD PRIMARY KEY (`model_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `scale_id` (`scale_id`),
  ADD KEY `collection_id` (`collection_id`);

--
-- Indexes for table `scale`
--
ALTER TABLE `scale`
  ADD PRIMARY KEY (`scale_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `variant`
--
ALTER TABLE `variant`
  ADD PRIMARY KEY (`variant_id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `model_id` (`model_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `collection`
--
ALTER TABLE `collection`
  MODIFY `collection_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `model`
--
ALTER TABLE `model`
  MODIFY `model_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `scale`
--
ALTER TABLE `scale`
  MODIFY `scale_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `variant`
--
ALTER TABLE `variant`
  MODIFY `variant_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model`
--
ALTER TABLE `model`
  ADD CONSTRAINT `model_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `model_ibfk_2` FOREIGN KEY (`scale_id`) REFERENCES `scale` (`scale_id`),
  ADD CONSTRAINT `model_ibfk_3` FOREIGN KEY (`collection_id`) REFERENCES `collection` (`collection_id`) ON DELETE CASCADE;

--
-- Constraints for table `variant`
--
ALTER TABLE `variant`
  ADD CONSTRAINT `variant_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `model` (`model_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
