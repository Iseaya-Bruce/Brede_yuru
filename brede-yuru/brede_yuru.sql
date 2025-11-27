-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 06:32 PM
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
-- Database: `brede_yuru`
--

-- --------------------------------------------------------

--
-- Table structure for table `breads`
--

CREATE TABLE `breads` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `breads`
--

INSERT INTO `breads` (`id`, `name`, `description`, `price`, `image_path`, `is_active`, `created_at`) VALUES
(1, 'Broodje Hotdog (vegetaries)', NULL, 40.00, 'uploads/breads/68a879f596053_broodje gezond.jpeg', 1, '2025-06-18 12:01:16'),
(2, 'Broodje Hotdog', NULL, 50.00, 'uploads/breads/6867c91810aa0_hotdog.jpg', 1, '2025-06-18 12:01:50'),
(3, 'Broodje Gezond', NULL, 40.00, 'uploads/breads/686bcfd5a8074_Broodje gezond.jpg', 1, '2025-07-04 13:40:54'),
(4, 'Getoosde Brood', NULL, 30.00, 'uploads/breads/686bcfc990ec9_grilled-cheese-500x500.jpg', 1, '2025-07-07 12:02:09'),
(5, 'Burger', NULL, 90.00, 'uploads/breads/68a48ac437c2e_crispy-comte-cheesburgers-FT-RECIPE0921-6166c6552b7148e8a8561f7765ddf20b.jpg', 1, '2025-08-19 14:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `bread_ingredients`
--

CREATE TABLE `bread_ingredients` (
  `id` int(11) NOT NULL,
  `bread_id` int(11) DEFAULT NULL,
  `ingredient_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bread_ingredients`
--

INSERT INTO `bread_ingredients` (`id`, `bread_id`, `ingredient_id`) VALUES
(65, 4, 6),
(66, 4, 1),
(67, 4, 2),
(68, 4, 7),
(69, 1, 2),
(70, 1, 8),
(71, 1, 3),
(72, 3, 1),
(73, 3, 2),
(74, 3, 3),
(75, 2, 6),
(76, 2, 1),
(77, 2, 7),
(78, 2, 4),
(79, 2, 3),
(80, 2, 5),
(81, 5, 6),
(82, 5, 1),
(83, 5, 9),
(84, 5, 2),
(85, 5, 3),
(86, 5, 5);

-- --------------------------------------------------------

--
-- Table structure for table `bread_types`
--

CREATE TABLE `bread_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bread_types`
--

INSERT INTO `bread_types` (`id`, `name`, `image_path`, `is_active`) VALUES
(1, 'Fernandes Sneedjes', 'uploads/bread_types/6867d084b3b5c_sneetjes.jpeg', 1),
(2, 'Fernandes Puntjes', 'uploads/bread_types/6867d0a042260_fern puntjes.jpg', 1),
(3, 'Chinese Puntjes', 'uploads/bread_types/6867d0b2e9a60_sneezy puntjes.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `bread_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `bread_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_item_ingredients`
--

CREATE TABLE `cart_item_ingredients` (
  `id` int(11) NOT NULL,
  `cart_item_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `is_extra` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_standard` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `name`, `image_path`, `price`, `is_standard`, `is_active`, `created_at`) VALUES
(1, 'cheese', 'uploads/ingredients/68680f038fff0_images (2).jpg', 2.50, 1, 1, '2025-06-18 11:59:19'),
(2, 'lettuce', 'uploads/ingredients/6867cc8313487_sla.jpg', 1.00, 1, 1, '2025-06-18 11:59:55'),
(3, 'tomato', 'uploads/ingredients/68681d797da0a_tomato.jpeg', 1.00, 1, 1, '2025-06-18 12:00:16'),
(4, 'sla', 'uploads/ingredients/6867bb106e885_sla.jpg', 6.00, 0, 1, '2025-07-04 11:29:20'),
(5, 'worst', 'uploads/ingredients/6867c7b13dd28_worst.jpg', 10.00, 0, 1, '2025-07-04 12:23:13'),
(6, 'Boterham Worst', 'uploads/ingredients/686bb8af061ce_boterham worst.jpeg', 0.00, 1, 1, '2025-07-07 12:08:15'),
(7, 'Salami Worst', 'uploads/ingredients/686bb8cb8a3d7_salami worst.jpeg', 0.00, 1, 1, '2025-07-07 12:08:43'),
(8, 'peper', 'uploads/ingredients/686bbd718a790_peper.jpeg', 2.00, 0, 1, '2025-07-07 12:28:33'),
(9, 'ketchup', 'uploads/ingredients/686ea0641605d_tomato.jpeg', 1.00, 0, 1, '2025-07-09 17:01:24');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pickup_time` datetime NOT NULL,
  `status` enum('pending','preparing','ready','completed') DEFAULT 'pending',
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `pickup_time`, `status`, `total_price`, `created_at`) VALUES
(1, 6, '2025-07-09 12:00:00', 'ready', 58.00, '2025-07-08 14:55:22'),
(2, 6, '2025-07-17 20:51:00', 'pending', 58.00, '2025-07-08 18:51:30'),
(3, 6, '2025-07-10 03:55:00', 'pending', 0.00, '2025-07-08 18:56:06'),
(4, 6, '2025-07-10 17:00:00', 'pending', 58.00, '2025-07-09 12:04:10'),
(5, 6, '2025-07-10 15:43:00', 'pending', 0.00, '2025-07-09 18:43:30'),
(6, 6, '2025-07-11 10:00:00', 'completed', 0.00, '2025-07-10 12:08:33'),
(7, 6, '2025-07-11 10:00:00', 'ready', 0.00, '2025-07-10 12:08:58'),
(8, 6, '2025-07-11 10:00:00', 'preparing', 144.00, '2025-07-10 12:20:04'),
(9, 6, '2025-07-12 14:57:00', 'pending', 58.00, '2025-07-10 17:57:55'),
(10, 6, '2025-07-14 10:50:00', 'pending', 58.00, '2025-07-11 13:49:23'),
(11, 6, '2025-07-26 09:02:00', 'completed', 48.00, '2025-07-24 12:02:54'),
(12, 6, '2030-08-05 00:00:00', 'pending', 59.00, '2025-08-12 16:12:06'),
(13, 6, '2025-08-15 15:31:00', 'pending', 98.00, '2025-08-13 17:31:51'),
(14, 6, '2025-08-19 11:01:00', 'pending', 188.00, '2025-08-18 14:01:41'),
(15, 6, '2025-08-27 11:05:00', 'pending', 43.00, '2025-08-18 14:05:15'),
(16, 6, '2025-08-19 16:34:00', 'pending', 43.00, '2025-08-18 14:35:10'),
(17, 6, '2025-08-18 16:34:00', 'pending', 43.00, '2025-08-18 14:35:16'),
(18, 6, '2025-08-21 06:00:00', 'pending', 86.00, '2025-08-19 12:56:45'),
(19, 6, '2025-08-22 20:01:00', 'pending', 51.00, '2025-08-19 14:01:11'),
(20, 6, '2025-08-20 11:33:00', 'pending', 159.00, '2025-08-19 14:33:48'),
(21, 6, '2025-08-20 14:13:00', 'pending', 56.00, '2025-08-19 17:13:12'),
(22, 6, '2025-08-20 14:16:00', 'pending', 40.00, '2025-08-19 17:16:15'),
(23, 6, '2025-08-20 14:22:00', 'pending', 40.00, '2025-08-19 17:22:26'),
(24, 6, '2025-08-21 00:00:00', 'pending', 105.00, '2025-08-19 17:24:53'),
(25, 6, '2025-08-20 14:38:00', 'pending', 100.00, '2025-08-19 17:38:09'),
(26, 6, '2025-08-20 14:40:00', 'pending', 80.00, '2025-08-19 17:40:13'),
(27, 6, '2025-08-20 14:52:00', 'pending', 40.00, '2025-08-19 17:52:28'),
(28, 6, '2025-08-20 15:05:00', 'pending', 46.00, '2025-08-19 18:05:11'),
(29, 6, '2025-08-20 15:25:00', 'pending', 41.00, '2025-08-19 18:25:43'),
(30, 6, '2025-08-20 15:40:00', 'pending', 46.00, '2025-08-19 18:41:20'),
(31, 6, '2025-08-21 15:41:00', 'pending', 40.00, '2025-08-19 18:41:37'),
(32, 6, '2025-08-21 15:44:00', 'pending', 40.00, '2025-08-19 18:44:33'),
(33, 6, '2025-08-20 15:54:00', 'pending', 40.00, '2025-08-19 18:54:13'),
(34, 6, '2025-08-19 15:54:00', 'pending', 40.00, '2025-08-19 18:55:11'),
(35, 6, '2025-08-22 08:54:00', 'pending', 40.00, '2025-08-20 11:55:04'),
(36, 6, '2025-08-21 08:59:00', 'pending', 50.00, '2025-08-20 11:59:26'),
(37, 6, '2025-08-21 09:03:00', 'pending', 40.00, '2025-08-20 12:04:04'),
(38, 6, '2025-08-21 09:20:00', 'pending', 40.00, '2025-08-20 12:20:10'),
(39, 6, '2025-08-24 09:21:00', 'pending', 200.00, '2025-08-20 12:21:18'),
(40, 6, '2025-08-22 15:06:00', 'pending', 179.00, '2025-08-20 18:06:44'),
(41, 6, '2025-08-20 18:21:00', 'pending', 139.00, '2025-08-20 18:21:48'),
(42, 6, '2025-08-21 15:41:00', 'pending', 80.00, '2025-08-20 18:41:10'),
(43, 6, '2025-08-21 15:44:00', 'pending', 357.00, '2025-08-20 18:45:35'),
(44, 6, '2025-08-25 22:02:00', 'pending', 327.00, '2025-08-22 20:03:43'),
(45, 6, '2025-08-22 22:02:00', 'pending', 327.00, '2025-08-22 20:04:06'),
(46, 6, '2025-08-22 22:02:00', 'pending', 109.00, '2025-08-22 20:04:21'),
(47, 6, '2025-08-22 22:05:00', 'pending', 109.00, '2025-08-22 20:05:04'),
(48, 6, '2025-08-22 22:21:00', 'pending', 109.00, '2025-08-22 20:21:11'),
(49, 6, '2025-08-24 16:33:00', 'pending', 327.00, '2025-08-23 14:33:59'),
(50, 6, '2025-08-24 08:36:00', 'pending', 327.00, '2025-08-23 14:36:37'),
(51, 6, '2025-08-23 16:36:00', 'pending', 327.00, '2025-08-23 14:36:43'),
(52, 6, '2025-08-26 10:00:00', 'pending', 327.00, '2025-08-23 14:38:01'),
(53, 6, '2025-08-24 11:48:00', 'pending', 70.00, '2025-08-23 14:49:41'),
(54, 6, '2025-08-24 13:16:00', 'pending', 72.00, '2025-08-23 16:17:24'),
(55, 6, '0000-00-00 00:00:00', 'pending', 72.00, '2025-08-23 16:17:49'),
(56, 6, '2025-08-29 13:18:00', 'pending', 72.00, '2025-08-23 16:18:26'),
(57, 6, '2025-08-24 13:19:00', 'pending', 67.00, '2025-08-23 16:19:17'),
(58, 6, '2025-08-24 13:19:00', 'pending', 70.00, '2025-08-23 16:19:58');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `bread_id` int(11) NOT NULL,
  `bread_type_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `bread_id`, `bread_type_id`, `quantity`, `price`) VALUES
(1, 1, 3, NULL, 1, NULL),
(2, 2, 3, 1, 1, NULL),
(3, 4, 3, 2, 1, NULL),
(4, 8, 3, 2, 1, 40.00),
(5, 8, 3, 2, 1, 40.00),
(6, 8, 4, 1, 1, 40.00),
(7, 9, 1, 1, 1, 40.00),
(8, 10, 2, 2, 1, 40.00),
(9, 11, 4, 3, 1, 40.00),
(10, 12, 2, 3, 1, 40.00),
(11, 13, 2, 1, 1, 40.00),
(12, 13, 3, 2, 1, 40.00),
(13, 14, 1, 1, 1, 40.00),
(14, 14, 4, 2, 3, 40.00),
(15, 15, 1, 2, 1, 40.00),
(16, 16, 1, 1, 1, NULL),
(17, 17, 1, 1, 1, NULL),
(18, 18, 1, 3, 2, NULL),
(19, 19, 1, 1, 1, 40.00),
(20, 20, 1, 2, 1, 40.00),
(21, 20, 5, 1, 1, 90.00),
(22, 21, 2, 1, 1, 50.00),
(23, 22, 1, 3, 1, 40.00),
(24, 23, 1, 1, 1, 40.00),
(25, 24, 2, 2, 1, 50.00),
(26, 24, 4, 1, 1, 30.00),
(27, 25, 2, 1, 2, 50.00),
(28, 26, 1, 3, 2, 40.00),
(29, 27, 1, 3, 1, 40.00),
(30, 28, 1, 1, 1, 40.00),
(31, 29, 1, 1, 1, 40.00),
(32, 30, 1, 1, 1, 40.00),
(33, 31, 1, 3, 1, 40.00),
(34, 32, 1, 1, 1, 40.00),
(35, 33, 1, 1, 1, 40.00),
(36, 34, 1, 3, 1, 40.00),
(37, 35, 1, 1, 1, 40.00),
(38, 36, 1, 3, 1, 40.00),
(39, 37, 1, 1, 1, 40.00),
(40, 38, 1, 3, 1, 40.00),
(41, 39, 1, 1, 5, 40.00),
(42, 40, 1, 1, 1, 40.00),
(43, 40, 5, 1, 1, 90.00),
(44, 41, 5, 2, 1, 90.00),
(45, 42, 1, 1, 1, 40.00),
(46, 43, 5, 1, 3, 90.00),
(47, 44, 5, 1, 3, NULL),
(48, 45, 5, 1, 3, NULL),
(49, 46, 5, 1, 1, NULL),
(50, 47, 5, 1, 1, NULL),
(51, 48, 5, 1, 1, 90.00),
(52, 49, 5, 1, 3, NULL),
(53, 50, 5, 1, 3, NULL),
(54, 51, 5, 1, 3, NULL),
(55, 52, 5, 1, 3, NULL),
(56, 53, 1, 1, 1, 40.00),
(57, 54, 1, 1, 1, 40.00),
(58, 55, 1, 2, 1, 40.00),
(59, 56, 1, 1, 1, 40.00),
(60, 57, 1, 2, 1, 40.00),
(61, 58, 2, 1, 1, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_item_ingredients`
--

CREATE TABLE `order_item_ingredients` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `is_extra` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_item_ingredients`
--

INSERT INTO `order_item_ingredients` (`id`, `order_item_id`, `ingredient_id`, `is_extra`) VALUES
(1, 1, 4, 1),
(2, 1, 8, 1),
(3, 1, 5, 1),
(4, 2, 4, 1),
(5, 2, 8, 1),
(6, 2, 5, 1),
(7, 3, 4, 1),
(8, 3, 8, 1),
(9, 3, 5, 1),
(10, 4, 8, 1),
(11, 4, 9, 1),
(12, 5, 9, 1),
(13, 5, 8, 1),
(14, 6, 5, 1),
(15, 6, 4, 1),
(16, 6, 8, 1),
(17, 7, 4, 1),
(18, 7, 5, 1),
(19, 7, 8, 1),
(20, 8, 4, 1),
(21, 8, 5, 1),
(22, 8, 8, 1),
(23, 9, 4, 1),
(24, 9, 8, 1),
(25, 10, 5, 1),
(26, 10, 9, 1),
(27, 10, 8, 1),
(28, 10, 4, 1),
(29, 12, 5, 1),
(30, 12, 8, 1),
(31, 12, 4, 1),
(32, 13, 5, 1),
(33, 14, 4, 1),
(34, 15, 9, 1),
(35, 15, 8, 1),
(36, 16, 9, 1),
(37, 16, 8, 1),
(38, 17, 9, 1),
(39, 17, 8, 1),
(40, 18, 9, 1),
(41, 18, 8, 1),
(42, 19, 5, 1),
(43, 19, 9, 1),
(44, 20, 5, 1),
(45, 21, 5, 1),
(46, 21, 9, 1),
(47, 21, 8, 1),
(48, 21, 4, 1),
(49, 22, 4, 1),
(50, 25, 5, 1),
(51, 25, 9, 1),
(52, 25, 8, 1),
(53, 26, 5, 1),
(54, 26, 8, 1),
(55, 30, 4, 1),
(56, 31, 9, 1),
(57, 32, 4, 1),
(58, 38, 5, 1),
(59, 43, 9, 1),
(60, 43, 5, 1),
(61, 43, 8, 1),
(62, 43, 4, 1),
(63, 44, 5, 1),
(64, 44, 9, 1),
(65, 44, 8, 1),
(66, 44, 4, 1),
(67, 45, 5, 1),
(68, 46, 4, 1),
(69, 46, 5, 1),
(70, 46, 9, 1),
(71, 46, 8, 1),
(72, 47, 4, 1),
(73, 47, 5, 1),
(74, 47, 9, 1),
(75, 47, 8, 1),
(76, 48, 4, 1),
(77, 48, 5, 1),
(78, 48, 9, 1),
(79, 48, 8, 1),
(80, 49, 5, 1),
(81, 49, 9, 1),
(82, 49, 8, 1),
(83, 49, 4, 1),
(84, 50, 5, 1),
(85, 50, 9, 1),
(86, 50, 8, 1),
(87, 50, 4, 1),
(88, 51, 5, 1),
(89, 51, 9, 1),
(90, 51, 8, 1),
(91, 51, 4, 1),
(92, 52, 4, 1),
(93, 52, 5, 1),
(94, 52, 9, 1),
(95, 52, 8, 1),
(96, 53, 4, 1),
(97, 53, 5, 1),
(98, 53, 9, 1),
(99, 53, 8, 1),
(100, 54, 4, 1),
(101, 54, 5, 1),
(102, 54, 9, 1),
(103, 54, 8, 1),
(104, 55, 4, 1),
(105, 55, 5, 1),
(106, 55, 9, 1),
(107, 55, 8, 1),
(108, 57, 5, 1),
(109, 57, 8, 1),
(110, 58, 5, 1),
(111, 58, 8, 1),
(112, 59, 5, 1),
(113, 59, 8, 1),
(114, 60, 5, 1),
(115, 60, 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `password`, `is_admin`, `created_at`) VALUES
(6, 'iseaya', '8858034', '$2y$10$.ff5vgUn8qjEu0jg8HE.2.xPi6838o6UyW9Nr9sd8KbRF23.Rr3im', 0, '2025-07-02 13:34:44'),
(8, 'admin', '1234567890', '$2y$10$LEZcZr9CkrZKWtYW8NQ4g.GfIXJIA8b.Z7fdo0dTYR6zJTxMiRt5i', 1, '2025-07-03 14:53:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `breads`
--
ALTER TABLE `breads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bread_ingredients`
--
ALTER TABLE `bread_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bread_id` (`bread_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `bread_types`
--
ALTER TABLE `bread_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `bread_id` (`bread_id`);

--
-- Indexes for table `cart_item_ingredients`
--
ALTER TABLE `cart_item_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_item_id` (`cart_item_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `bread_id` (`bread_id`);

--
-- Indexes for table `order_item_ingredients`
--
ALTER TABLE `order_item_ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_item_id` (`order_item_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `breads`
--
ALTER TABLE `breads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bread_ingredients`
--
ALTER TABLE `bread_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `bread_types`
--
ALTER TABLE `bread_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `cart_item_ingredients`
--
ALTER TABLE `cart_item_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `order_item_ingredients`
--
ALTER TABLE `order_item_ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bread_ingredients`
--
ALTER TABLE `bread_ingredients`
  ADD CONSTRAINT `bread_ingredients_ibfk_1` FOREIGN KEY (`bread_id`) REFERENCES `breads` (`id`),
  ADD CONSTRAINT `bread_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`bread_id`) REFERENCES `breads` (`id`);

--
-- Constraints for table `cart_item_ingredients`
--
ALTER TABLE `cart_item_ingredients`
  ADD CONSTRAINT `cart_item_ingredients_ibfk_1` FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`id`),
  ADD CONSTRAINT `cart_item_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`bread_id`) REFERENCES `breads` (`id`);

--
-- Constraints for table `order_item_ingredients`
--
ALTER TABLE `order_item_ingredients`
  ADD CONSTRAINT `order_item_ingredients_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`),
  ADD CONSTRAINT `order_item_ingredients_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
