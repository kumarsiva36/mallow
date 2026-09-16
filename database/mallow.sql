-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2026 at 05:49 AM
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
-- Database: `mallow`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-86d53dde56565bb021cf333dc13decc8', 'i:2;', 1789530456),
('laravel-cache-86d53dde56565bb021cf333dc13decc8:timer', 'i:1789530456;', 1789530456),
('laravel-cache-plans:merchant:92', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:15:\"App\\Models\\Plan\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"plans\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:15:{s:2:\"id\";i:113;s:11:\"merchant_id\";i:92;s:4:\"name\";s:22:\"Model API Professional\";s:4:\"code\";s:13:\"model-api-pro\";s:11:\"description\";s:55:\"Inference tokens metering plan. Includes 50,000 tokens.\";s:10:\"base_price\";s:5:\"75.00\";s:13:\"billing_cycle\";s:7:\"monthly\";s:10:\"cycle_days\";i:30;s:20:\"included_usage_units\";i:50000;s:21:\"overage_rate_per_unit\";s:6:\"0.0025\";s:17:\"prorate_allowance\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-09-15 15:34:49\";s:10:\"updated_at\";s:19:\"2026-09-15 15:34:49\";s:19:\"subscriptions_count\";i:2;}s:11:\"\0*\0original\";a:15:{s:2:\"id\";i:113;s:11:\"merchant_id\";i:92;s:4:\"name\";s:22:\"Model API Professional\";s:4:\"code\";s:13:\"model-api-pro\";s:11:\"description\";s:55:\"Inference tokens metering plan. Includes 50,000 tokens.\";s:10:\"base_price\";s:5:\"75.00\";s:13:\"billing_cycle\";s:7:\"monthly\";s:10:\"cycle_days\";i:30;s:20:\"included_usage_units\";i:50000;s:21:\"overage_rate_per_unit\";s:6:\"0.0025\";s:17:\"prorate_allowance\";i:1;s:9:\"is_active\";i:1;s:10:\"created_at\";s:19:\"2026-09-15 15:34:49\";s:10:\"updated_at\";s:19:\"2026-09-15 15:34:49\";s:19:\"subscriptions_count\";i:2;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:6:{s:10:\"base_price\";s:9:\"decimal:2\";s:10:\"cycle_days\";s:7:\"integer\";s:20:\"included_usage_units\";s:7:\"integer\";s:21:\"overage_rate_per_unit\";s:9:\"decimal:4\";s:17:\"prorate_allowance\";s:7:\"boolean\";s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:11:{i:0;s:11:\"merchant_id\";i:1;s:4:\"name\";i:2;s:4:\"code\";i:3;s:11:\"description\";i:4;s:10:\"base_price\";i:5;s:13:\"billing_cycle\";i:6;s:10:\"cycle_days\";i:7;s:20:\"included_usage_units\";i:8;s:21:\"overage_rate_per_unit\";i:9;s:17:\"prorate_allowance\";i:10;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1789533276);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `merchant_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `external_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `merchant_id`, `name`, `email`, `password`, `external_id`, `created_at`, `updated_at`) VALUES
(76, 91, 'Acme Corporation', 'devops@acme.corp', '$2y$12$BKSXuYfqBsrjX6ElLgb2quGc0KXDLgy.RWPu1RdLnH6ojkNGDD5cW', 'cust_acme_001', '2026-09-15 10:04:47', '2026-09-15 10:04:47'),
(77, 91, 'Globex Logistics', 'api@globex.io', '$2y$12$WhcYzq9LKbHTGsBt048lCO.BmrqDCD5t98A49adhsSWHQnbN6/e32', 'cust_globex_002', '2026-09-15 10:04:47', '2026-09-15 10:04:47'),
(78, 91, 'Initech Systems', 'admin@initech.net', '$2y$12$/wXAxexPv0B1Zx5uN6cLreKDTKkbcLntWaEOq.vl0yd4VFQSC4zg.', 'cust_initech_003', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(79, 91, 'Apex Cloud Systems', 'ops@apexcloud.io', '$2y$12$oSSaJ2y7Puysc/QhyCR.kuerHusR7dinUzlMt8/oF6h4EJ9eQrlTa', 'cust_apex_004', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(80, 91, 'Stale Innovations', 'accounts@staleinno.com', '$2y$12$x/xcTtEuB2PtvdzYGQwwAuXdy/8TTQeyj5en74K/3IbHwglbEt.KG', 'cust_stale_005', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(81, 92, 'Soylent Technologies GmbH', 'billing@soylent-tech.de', '$2y$12$dS5ktaD3BlhHCzwNvP5o8ugxm2Oyrztnypy18mUPFEpKE8fYj6GZ.', 'cust_soylent_de', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(82, 92, 'Smart Corporation', 'skcorp@smart.corp', '$2y$12$zx54S9wOqv8Qm0JGF748LubEh9AprvajmXh21THUHcJnBBRfEDuf6', 'cust_6zgdevqf', '2026-09-15 22:04:36', '2026-09-15 22:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `daily_usages`
--

CREATE TABLE `daily_usages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `merchant_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `metric` varchar(50) NOT NULL DEFAULT 'api_calls',
  `usage_date` date NOT NULL,
  `total_units` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `event_count` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `last_recorded_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `daily_usages`
--

INSERT INTO `daily_usages` (`id`, `merchant_id`, `customer_id`, `metric`, `usage_date`, `total_units`, `event_count`, `last_recorded_at`, `created_at`, `updated_at`) VALUES
(28, 91, 76, 'api_calls', '2026-09-01', 489, 5, '2026-09-01 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(29, 91, 76, 'api_calls', '2026-09-02', 915, 10, '2026-09-02 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(30, 91, 76, 'api_calls', '2026-09-03', 898, 10, '2026-09-03 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(31, 91, 76, 'api_calls', '2026-09-04', 913, 10, '2026-09-04 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(32, 91, 76, 'api_calls', '2026-09-05', 908, 10, '2026-09-05 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(33, 91, 76, 'api_calls', '2026-09-06', 941, 10, '2026-09-06 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(34, 91, 76, 'api_calls', '2026-09-07', 919, 10, '2026-09-07 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(35, 91, 76, 'api_calls', '2026-09-08', 952, 10, '2026-09-08 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(36, 91, 76, 'api_calls', '2026-09-09', 947, 10, '2026-09-09 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(37, 91, 76, 'api_calls', '2026-09-10', 930, 10, '2026-09-10 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(38, 91, 76, 'api_calls', '2026-09-11', 951, 10, '2026-09-11 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(39, 91, 76, 'api_calls', '2026-09-12', 945, 10, '2026-09-12 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(40, 91, 76, 'api_calls', '2026-09-13', 929, 10, '2026-09-13 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(41, 91, 76, 'api_calls', '2026-09-14', 914, 10, '2026-09-14 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(42, 91, 76, 'api_calls', '2026-09-15', 939, 10, '2026-09-15 23:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(43, 91, 76, 'api_calls', '2026-09-16', 457, 5, '2026-09-16 09:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(44, 91, 77, 'api_calls', '2026-08-18', 245, 1, '2026-08-18 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(45, 91, 77, 'api_calls', '2026-08-20', 190, 1, '2026-08-20 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(46, 91, 77, 'api_calls', '2026-08-22', 229, 1, '2026-08-22 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(47, 91, 77, 'api_calls', '2026-08-24', 201, 1, '2026-08-24 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(48, 91, 77, 'api_calls', '2026-08-26', 256, 1, '2026-08-26 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(49, 91, 77, 'api_calls', '2026-08-28', 225, 1, '2026-08-28 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(50, 91, 77, 'api_calls', '2026-08-30', 233, 1, '2026-08-30 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(51, 91, 77, 'api_calls', '2026-09-01', 243, 1, '2026-09-01 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(52, 91, 77, 'api_calls', '2026-09-03', 219, 1, '2026-09-03 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(53, 91, 77, 'api_calls', '2026-09-05', 254, 1, '2026-09-05 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(54, 91, 77, 'api_calls', '2026-09-07', 208, 1, '2026-09-07 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(55, 91, 77, 'api_calls', '2026-09-09', 256, 1, '2026-09-09 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(56, 91, 77, 'api_calls', '2026-09-11', 187, 1, '2026-09-11 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(57, 91, 77, 'api_calls', '2026-09-13', 220, 1, '2026-09-13 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(58, 91, 77, 'api_calls', '2026-09-15', 237, 1, '2026-09-15 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(59, 91, 78, 'api_calls', '2026-09-11', 900, 1, '2026-09-11 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(60, 91, 78, 'api_calls', '2026-09-12', 900, 1, '2026-09-12 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(61, 91, 78, 'api_calls', '2026-09-13', 900, 1, '2026-09-13 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(62, 91, 78, 'api_calls', '2026-09-14', 900, 1, '2026-09-14 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(63, 91, 78, 'api_calls', '2026-09-15', 900, 1, '2026-09-15 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(64, 91, 79, 'api_calls', '2026-08-18', 120, 1, '2026-08-18 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(65, 91, 79, 'api_calls', '2026-08-20', 120, 1, '2026-08-20 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(66, 91, 79, 'api_calls', '2026-08-22', 120, 1, '2026-08-22 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(67, 91, 79, 'api_calls', '2026-08-24', 120, 1, '2026-08-24 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(68, 91, 79, 'api_calls', '2026-08-26', 120, 1, '2026-08-26 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(69, 91, 79, 'api_calls', '2026-08-29', 1400, 1, '2026-08-29 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(70, 91, 79, 'api_calls', '2026-08-31', 1400, 1, '2026-08-31 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(71, 91, 79, 'api_calls', '2026-09-02', 1400, 1, '2026-09-02 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(72, 91, 79, 'api_calls', '2026-09-04', 1400, 1, '2026-09-04 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(73, 91, 79, 'api_calls', '2026-09-06', 1400, 1, '2026-09-06 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(74, 91, 79, 'api_calls', '2026-09-08', 1400, 1, '2026-09-08 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(75, 91, 79, 'api_calls', '2026-09-10', 1400, 1, '2026-09-10 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(76, 91, 79, 'api_calls', '2026-09-12', 1400, 1, '2026-09-12 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(77, 91, 79, 'api_calls', '2026-09-14', 1400, 1, '2026-09-14 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(78, 91, 80, 'api_calls', '2026-07-22', 1150, 1, '2026-07-22 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(79, 91, 80, 'api_calls', '2026-07-24', 1150, 1, '2026-07-24 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(80, 91, 80, 'api_calls', '2026-07-26', 1150, 1, '2026-07-26 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(81, 91, 80, 'api_calls', '2026-07-28', 1150, 1, '2026-07-28 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(82, 91, 80, 'api_calls', '2026-07-30', 1150, 1, '2026-07-30 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(83, 91, 80, 'api_calls', '2026-08-01', 1150, 1, '2026-08-01 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(84, 91, 80, 'api_calls', '2026-08-03', 1150, 1, '2026-08-03 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(85, 91, 80, 'api_calls', '2026-08-05', 1150, 1, '2026-08-05 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(86, 91, 80, 'api_calls', '2026-08-07', 1150, 1, '2026-08-07 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(87, 91, 80, 'api_calls', '2026-08-09', 1150, 1, '2026-08-09 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(88, 91, 80, 'api_calls', '2026-08-11', 1150, 1, '2026-08-11 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(89, 91, 80, 'api_calls', '2026-08-13', 1150, 1, '2026-08-13 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(90, 91, 80, 'api_calls', '2026-08-21', 450, 1, '2026-08-21 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(91, 91, 80, 'api_calls', '2026-08-27', 450, 1, '2026-08-27 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(92, 91, 80, 'api_calls', '2026-09-02', 450, 1, '2026-09-02 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(93, 91, 80, 'api_calls', '2026-09-08', 450, 1, '2026-09-08 15:34:46', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(94, 92, 81, 'api_calls', '2026-09-10', 4500, 1, '2026-09-10 14:30:00', '2026-09-15 10:07:36', '2026-09-15 10:07:36'),
(95, 92, 82, 'api_calls', '2026-09-16', 4500, 1, '2026-09-16 08:30:00', '2026-09-15 22:16:49', '2026-09-15 22:16:49');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `merchant_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `period_start` datetime NOT NULL,
  `period_end` datetime NOT NULL,
  `base_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `overage_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `proration_ratio` decimal(6,4) NOT NULL DEFAULT 1.0000,
  `status` varchar(20) NOT NULL DEFAULT 'issued',
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `issued_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_number`, `merchant_id`, `customer_id`, `subscription_id`, `period_start`, `period_end`, `base_amount`, `overage_amount`, `total_amount`, `proration_ratio`, `status`, `currency`, `issued_at`, `created_at`, `updated_at`) VALUES
(8, 'INV-1-202607-HIST01', 91, 77, 32, '2026-07-17 00:00:00', '2026-08-15 23:59:59', 29.00, 45.50, 74.50, 1.0000, 'paid', 'USD', '2026-08-16 00:04:59', '2026-09-15 10:04:49', '2026-09-15 10:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `quantity` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `unit_price` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `type`, `description`, `quantity`, `unit_price`, `amount`, `created_at`, `updated_at`) VALUES
(20, 8, 'base_fee', 'Starter Tier Base Subscription (Full billing cycle)', 1, 29.0000, 29.00, '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(21, 8, 'allowance', 'Included allowance: 1,000 units. Recorded usage: 1,910 units.', 1000, 0.0000, 0.00, '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(22, 8, 'overage', 'Usage Overage: 910 units @ USD 0.0500 / unit', 910, 0.0500, 45.50, '2026-09-15 10:04:49', '2026-09-15 10:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

CREATE TABLE `merchants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `merchants`
--

INSERT INTO `merchants` (`id`, `name`, `slug`, `email`, `password`, `currency`, `timezone`, `created_at`, `updated_at`) VALUES
(91, 'Nexus Cloud Platform', 'nexus-cloud', 'billing@nexuscloud.io', '$2y$12$DI.Qjk9VREvatjROOKYP2OBqGveZnIZ/NGOHiEm2r9O2Q0RQD3pLK', 'USD', 'UTC', '2026-09-15 10:04:47', '2026-09-15 10:04:47'),
(92, 'Apex AI Solutions', 'apex-ai', 'finance@apex-ai.de', '$2y$12$3TAPKmPQ8RIFhU3LqlsWvOqzVjzkyD1LEPcyyZ3vHtWQWT0iJuIu.', 'EUR', 'Europe/Berlin', '2026-09-15 10:04:49', '2026-09-15 10:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_13_000001_create_merchants_table', 1),
(5, '2026_09_13_000002_create_plans_table', 1),
(6, '2026_09_13_000003_create_customers_table', 1),
(7, '2026_09_13_000004_create_subscriptions_table', 1),
(8, '2026_09_13_000005_create_usage_tables', 1),
(9, '2026_09_13_000006_create_invoices_table', 1),
(10, '2026_09_13_000007_create_subscription_segments_table', 1),
(11, '2026_09_13_000008_add_password_to_merchants_and_customers_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `merchant_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `billing_cycle` varchar(20) NOT NULL DEFAULT 'monthly',
  `cycle_days` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `included_usage_units` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `overage_rate_per_unit` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `prorate_allowance` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `merchant_id`, `name`, `code`, `description`, `base_price`, `billing_cycle`, `cycle_days`, `included_usage_units`, `overage_rate_per_unit`, `prorate_allowance`, `is_active`, `created_at`, `updated_at`) VALUES
(110, 91, 'Starter Tier', 'starter-tier', 'Great for prototypes and microservices with up to 1,000 API calls included.', 29.00, 'monthly', 30, 1000, 0.0500, 1, 1, '2026-09-15 10:04:47', '2026-09-15 10:04:47'),
(111, 91, 'Growth Tier', 'growth-tier', 'For fast growing apps. Includes 10,000 API calls with low $0.02 overage.', 99.00, 'monthly', 30, 10000, 0.0200, 1, 1, '2026-09-15 10:04:47', '2026-09-15 10:04:47'),
(112, 91, 'Enterprise Scale', 'enterprise-scale', 'Dedicated infrastructure with 100,000 calls included and volume pricing.', 499.00, 'monthly', 30, 100000, 0.0100, 1, 1, '2026-09-15 10:04:47', '2026-09-15 10:04:47'),
(113, 92, 'Model API Professional', 'model-api-pro', 'Inference tokens metering plan. Includes 50,000 tokens.', 75.00, 'monthly', 30, 50000, 0.0025, 1, 1, '2026-09-15 10:04:49', '2026-09-15 10:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('DmJjZ4CcKZqFkrlY4iTScKQm6wr9rwL99ChNONOk', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUVg0amJRZTdiMklSU2QzYlJRS0dodnJRODVaZlJhdUFvcnY3YWpEOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC8/bWVyY2hhbnRfaWQ9OTEiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1789488294),
('m491CxUajJzIRUs7T03z9Fb2AchBAZCnZudWvm1U', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRFVoaXp3NDVlV29Kb01qVUFDanY0elZrdXVGMUJkOHA1dUFmRWZQQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wb3J0YWwiO3M6NToicm91dGUiO3M6MTY6InBvcnRhbC5kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjExOiJjdXN0b21lcl9pZCI7aTo4Mjt9', 1789530418),
('UI5Xgo11elZZNLUK9AEq9PzSwh9oCcwXGI9eE4cy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMTVrN3UyNzIzc25vMzdLTlZCVnlDVmlLSk9FcWN2UnBoUzJFUWlJYiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tZXJjaGFudCI7czo1OiJyb3V0ZSI7czoxODoibWVyY2hhbnQuZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMToibWVyY2hhbnRfaWQiO2k6OTI7fQ==', 1789501357);

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `merchant_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `starts_at` datetime NOT NULL,
  `current_cycle_start` datetime NOT NULL,
  `current_cycle_end` datetime NOT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `merchant_id`, `customer_id`, `plan_id`, `status`, `starts_at`, `current_cycle_start`, `current_cycle_end`, `cancelled_at`, `created_at`, `updated_at`) VALUES
(31, 91, 76, 111, 'active', '2026-08-31 00:00:00', '2026-08-16 00:00:00', '2026-09-15 14:34:46', NULL, '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(32, 91, 77, 110, 'active', '2026-08-16 00:00:00', '2026-08-16 00:00:00', '2026-09-15 14:34:46', NULL, '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(33, 91, 78, 112, 'active', '2026-09-10 15:34:46', '2026-09-10 15:34:46', '2026-10-10 23:59:59', NULL, '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(34, 91, 79, 111, 'active', '2026-08-16 00:00:00', '2026-08-16 00:00:00', '2026-09-15 14:34:46', NULL, '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(35, 91, 80, 110, 'active', '2026-07-17 00:00:00', '2026-08-16 00:00:00', '2026-09-15 14:34:46', NULL, '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(36, 92, 81, 113, 'active', '2026-09-05 15:34:46', '2026-09-05 00:00:00', '2026-10-05 23:59:59', NULL, '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(37, 92, 82, 113, 'active', '2026-09-16 03:34:36', '2026-09-16 00:00:00', '2026-10-16 23:59:59', NULL, '2026-09-15 22:04:36', '2026-09-15 22:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_segments`
--

CREATE TABLE `subscription_segments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime NOT NULL,
  `billing_cycle_start` datetime NOT NULL,
  `billing_cycle_end` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_segments`
--

INSERT INTO `subscription_segments` (`id`, `subscription_id`, `plan_id`, `starts_at`, `ends_at`, `billing_cycle_start`, `billing_cycle_end`, `created_at`, `updated_at`) VALUES
(36, 31, 111, '2026-08-31 00:00:00', '2026-09-15 14:34:46', '2026-08-16 00:00:00', '2026-09-15 14:34:46', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(37, 32, 110, '2026-08-16 00:00:00', '2026-09-15 14:34:46', '2026-08-16 00:00:00', '2026-09-15 14:34:46', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(38, 33, 112, '2026-09-10 15:34:46', '2026-10-10 23:59:59', '2026-09-10 15:34:46', '2026-10-10 23:59:59', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(39, 34, 110, '2026-08-16 00:00:00', '2026-08-28 00:00:00', '2026-08-16 00:00:00', '2026-09-15 14:34:46', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(40, 34, 111, '2026-08-28 00:00:00', '2026-09-15 14:34:46', '2026-08-16 00:00:00', '2026-09-15 14:34:46', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(41, 35, 110, '2026-07-17 00:00:00', '2026-09-15 14:34:46', '2026-08-16 00:00:00', '2026-09-15 14:34:46', '2026-09-15 10:04:48', '2026-09-15 10:04:48'),
(42, 36, 113, '2026-09-05 15:34:46', '2026-10-05 23:59:59', '2026-09-05 00:00:00', '2026-10-05 23:59:59', '2026-09-15 10:04:49', '2026-09-15 10:04:49'),
(43, 37, 113, '2026-09-16 03:34:36', '2026-10-16 23:59:59', '2026-09-16 00:00:00', '2026-10-16 23:59:59', '2026-09-15 22:04:36', '2026-09-15 22:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `usage_events`
--

CREATE TABLE `usage_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `merchant_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `metric` varchar(50) NOT NULL DEFAULT 'api_calls',
  `units` bigint(20) UNSIGNED NOT NULL,
  `idempotency_key` varchar(128) DEFAULT NULL,
  `recorded_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usage_events`
--

INSERT INTO `usage_events` (`id`, `merchant_id`, `customer_id`, `metric`, `units`, `idempotency_key`, `recorded_at`, `created_at`) VALUES
(22, 91, 76, 'api_calls', 103, 'seed_acme_hbqeS5ssp6PW', '2026-09-01 15:34:46', '2026-09-15 10:04:49'),
(23, 91, 76, 'api_calls', 103, 'seed_acme_dpAsAbaXu6zE', '2026-09-01 17:34:46', '2026-09-15 10:04:49'),
(24, 91, 76, 'api_calls', 88, 'seed_acme_bhugzI1Y9JJ3', '2026-09-01 19:34:46', '2026-09-15 10:04:49'),
(25, 91, 76, 'api_calls', 102, 'seed_acme_r2GmoTAebvNB', '2026-09-01 21:34:46', '2026-09-15 10:04:49'),
(26, 91, 76, 'api_calls', 93, 'seed_acme_YfhR9QMVMAZO', '2026-09-01 23:34:46', '2026-09-15 10:04:49'),
(27, 91, 76, 'api_calls', 95, 'seed_acme_kbD0joi6VQMF', '2026-09-02 01:34:46', '2026-09-15 10:04:49'),
(28, 91, 76, 'api_calls', 97, 'seed_acme_ZKxWJxhcnKKv', '2026-09-02 03:34:46', '2026-09-15 10:04:49'),
(29, 91, 76, 'api_calls', 98, 'seed_acme_3CNwO0JsbT5i', '2026-09-02 05:34:46', '2026-09-15 10:04:49'),
(30, 91, 76, 'api_calls', 83, 'seed_acme_Zp4ogK27cDeI', '2026-09-02 07:34:46', '2026-09-15 10:04:49'),
(31, 91, 76, 'api_calls', 92, 'seed_acme_uNjOh8DVyyE4', '2026-09-02 09:34:46', '2026-09-15 10:04:49'),
(32, 91, 76, 'api_calls', 88, 'seed_acme_EKjaq0pFmDUh', '2026-09-02 15:34:46', '2026-09-15 10:04:49'),
(33, 91, 76, 'api_calls', 100, 'seed_acme_5mFYDc799ADg', '2026-09-02 17:34:46', '2026-09-15 10:04:49'),
(34, 91, 76, 'api_calls', 83, 'seed_acme_Kso4bB2VlH6h', '2026-09-02 19:34:46', '2026-09-15 10:04:49'),
(35, 91, 76, 'api_calls', 93, 'seed_acme_UHeIW3dpHUXk', '2026-09-02 21:34:46', '2026-09-15 10:04:49'),
(36, 91, 76, 'api_calls', 86, 'seed_acme_SXSR50Xzx1uq', '2026-09-02 23:34:46', '2026-09-15 10:04:49'),
(37, 91, 76, 'api_calls', 85, 'seed_acme_uyGl0GlyrTiL', '2026-09-03 01:34:46', '2026-09-15 10:04:49'),
(38, 91, 76, 'api_calls', 99, 'seed_acme_kMqLvUvXsHSH', '2026-09-03 03:34:46', '2026-09-15 10:04:49'),
(39, 91, 76, 'api_calls', 83, 'seed_acme_PfYNroorqjX2', '2026-09-03 05:34:46', '2026-09-15 10:04:49'),
(40, 91, 76, 'api_calls', 87, 'seed_acme_DRqZFyernIDt', '2026-09-03 07:34:46', '2026-09-15 10:04:49'),
(41, 91, 76, 'api_calls', 91, 'seed_acme_TBlfhR3jq85z', '2026-09-03 09:34:46', '2026-09-15 10:04:49'),
(42, 91, 76, 'api_calls', 86, 'seed_acme_ocKPU6BtZhIz', '2026-09-03 15:34:46', '2026-09-15 10:04:49'),
(43, 91, 76, 'api_calls', 94, 'seed_acme_eWrdnffQc1dN', '2026-09-03 17:34:46', '2026-09-15 10:04:49'),
(44, 91, 76, 'api_calls', 96, 'seed_acme_bnx91MgzYGnM', '2026-09-03 19:34:46', '2026-09-15 10:04:49'),
(45, 91, 76, 'api_calls', 93, 'seed_acme_KPHLYHB3mO7Q', '2026-09-03 21:34:46', '2026-09-15 10:04:49'),
(46, 91, 76, 'api_calls', 84, 'seed_acme_pbMJYM45OP2A', '2026-09-03 23:34:46', '2026-09-15 10:04:49'),
(47, 91, 76, 'api_calls', 99, 'seed_acme_K44buTVDnc3A', '2026-09-04 01:34:46', '2026-09-15 10:04:49'),
(48, 91, 76, 'api_calls', 84, 'seed_acme_fBDVr484jkKd', '2026-09-04 03:34:46', '2026-09-15 10:04:49'),
(49, 91, 76, 'api_calls', 100, 'seed_acme_k18sC0Dyc7VF', '2026-09-04 05:34:46', '2026-09-15 10:04:49'),
(50, 91, 76, 'api_calls', 86, 'seed_acme_UeMTdvnle23s', '2026-09-04 07:34:46', '2026-09-15 10:04:49'),
(51, 91, 76, 'api_calls', 87, 'seed_acme_EGs1azN8mAP2', '2026-09-04 09:34:46', '2026-09-15 10:04:49'),
(52, 91, 76, 'api_calls', 98, 'seed_acme_KfHKTT0YYdE8', '2026-09-04 15:34:46', '2026-09-15 10:04:49'),
(53, 91, 76, 'api_calls', 89, 'seed_acme_AVYFFret8wY1', '2026-09-04 17:34:46', '2026-09-15 10:04:49'),
(54, 91, 76, 'api_calls', 100, 'seed_acme_N2DkmHAmVHI5', '2026-09-04 19:34:46', '2026-09-15 10:04:49'),
(55, 91, 76, 'api_calls', 85, 'seed_acme_L2rYKbQXZNbR', '2026-09-04 21:34:46', '2026-09-15 10:04:49'),
(56, 91, 76, 'api_calls', 85, 'seed_acme_BwQqDsTi7Ek0', '2026-09-04 23:34:46', '2026-09-15 10:04:49'),
(57, 91, 76, 'api_calls', 89, 'seed_acme_mFIaxJnTDTWw', '2026-09-05 01:34:46', '2026-09-15 10:04:49'),
(58, 91, 76, 'api_calls', 86, 'seed_acme_nhVj48I38kAy', '2026-09-05 03:34:46', '2026-09-15 10:04:49'),
(59, 91, 76, 'api_calls', 85, 'seed_acme_68yRPL0QhBjX', '2026-09-05 05:34:46', '2026-09-15 10:04:49'),
(60, 91, 76, 'api_calls', 98, 'seed_acme_OQ7XMKMuqGqy', '2026-09-05 07:34:46', '2026-09-15 10:04:49'),
(61, 91, 76, 'api_calls', 97, 'seed_acme_ZpnpE1C9UuEy', '2026-09-05 09:34:46', '2026-09-15 10:04:49'),
(62, 91, 76, 'api_calls', 94, 'seed_acme_gWzyA88wwCK3', '2026-09-05 15:34:46', '2026-09-15 10:04:49'),
(63, 91, 76, 'api_calls', 98, 'seed_acme_nrVxltfMDDf9', '2026-09-05 17:34:46', '2026-09-15 10:04:49'),
(64, 91, 76, 'api_calls', 88, 'seed_acme_G3prm3qeeiw1', '2026-09-05 19:34:46', '2026-09-15 10:04:49'),
(65, 91, 76, 'api_calls', 87, 'seed_acme_JcGoUrvJJu5p', '2026-09-05 21:34:46', '2026-09-15 10:04:49'),
(66, 91, 76, 'api_calls', 86, 'seed_acme_2l9Q0Ck2S9q3', '2026-09-05 23:34:46', '2026-09-15 10:04:49'),
(67, 91, 76, 'api_calls', 101, 'seed_acme_MeX7NJaCGd3I', '2026-09-06 01:34:46', '2026-09-15 10:04:49'),
(68, 91, 76, 'api_calls', 97, 'seed_acme_IwwTRYK1EZNg', '2026-09-06 03:34:46', '2026-09-15 10:04:49'),
(69, 91, 76, 'api_calls', 84, 'seed_acme_CRk0NMoKvAId', '2026-09-06 05:34:46', '2026-09-15 10:04:49'),
(70, 91, 76, 'api_calls', 103, 'seed_acme_nQivnWJAegqT', '2026-09-06 07:34:46', '2026-09-15 10:04:49'),
(71, 91, 76, 'api_calls', 102, 'seed_acme_tYhCtHchbzFK', '2026-09-06 09:34:46', '2026-09-15 10:04:49'),
(72, 91, 76, 'api_calls', 92, 'seed_acme_RYX9Iqrk3t5R', '2026-09-06 15:34:46', '2026-09-15 10:04:49'),
(73, 91, 76, 'api_calls', 86, 'seed_acme_jvGgqCMn4QLQ', '2026-09-06 17:34:46', '2026-09-15 10:04:49'),
(74, 91, 76, 'api_calls', 89, 'seed_acme_AiHbsQrFRw4X', '2026-09-06 19:34:46', '2026-09-15 10:04:49'),
(75, 91, 76, 'api_calls', 99, 'seed_acme_lbR9YsqfSsmK', '2026-09-06 21:34:46', '2026-09-15 10:04:49'),
(76, 91, 76, 'api_calls', 88, 'seed_acme_9P8dh6FezDx6', '2026-09-06 23:34:46', '2026-09-15 10:04:49'),
(77, 91, 76, 'api_calls', 86, 'seed_acme_LFNrr6chaPkI', '2026-09-07 01:34:46', '2026-09-15 10:04:49'),
(78, 91, 76, 'api_calls', 101, 'seed_acme_Xh5mWTGNYkrp', '2026-09-07 03:34:46', '2026-09-15 10:04:49'),
(79, 91, 76, 'api_calls', 91, 'seed_acme_KQHQIyHWmffl', '2026-09-07 05:34:46', '2026-09-15 10:04:49'),
(80, 91, 76, 'api_calls', 86, 'seed_acme_nUZFhrPu10Rz', '2026-09-07 07:34:46', '2026-09-15 10:04:49'),
(81, 91, 76, 'api_calls', 95, 'seed_acme_OjKXoQnKH3YW', '2026-09-07 09:34:46', '2026-09-15 10:04:49'),
(82, 91, 76, 'api_calls', 100, 'seed_acme_5OVzIwCXNIYo', '2026-09-07 15:34:46', '2026-09-15 10:04:49'),
(83, 91, 76, 'api_calls', 84, 'seed_acme_WNoNXTCq1qgg', '2026-09-07 17:34:46', '2026-09-15 10:04:49'),
(84, 91, 76, 'api_calls', 90, 'seed_acme_Tr7Qm1QxOZWt', '2026-09-07 19:34:46', '2026-09-15 10:04:49'),
(85, 91, 76, 'api_calls', 93, 'seed_acme_faVnAGV3rtVb', '2026-09-07 21:34:46', '2026-09-15 10:04:49'),
(86, 91, 76, 'api_calls', 93, 'seed_acme_gZpwbwqVmKsK', '2026-09-07 23:34:46', '2026-09-15 10:04:49'),
(87, 91, 76, 'api_calls', 103, 'seed_acme_TzSbmhGsYnas', '2026-09-08 01:34:46', '2026-09-15 10:04:49'),
(88, 91, 76, 'api_calls', 90, 'seed_acme_vfhIXJo5zFYr', '2026-09-08 03:34:46', '2026-09-15 10:04:49'),
(89, 91, 76, 'api_calls', 96, 'seed_acme_wAgnVA2bQpGg', '2026-09-08 05:34:46', '2026-09-15 10:04:49'),
(90, 91, 76, 'api_calls', 99, 'seed_acme_1eVA1lj8PDoy', '2026-09-08 07:34:46', '2026-09-15 10:04:49'),
(91, 91, 76, 'api_calls', 98, 'seed_acme_LnccpWTlTMAE', '2026-09-08 09:34:46', '2026-09-15 10:04:49'),
(92, 91, 76, 'api_calls', 98, 'seed_acme_qwPkQ43kE8g0', '2026-09-08 15:34:46', '2026-09-15 10:04:49'),
(93, 91, 76, 'api_calls', 93, 'seed_acme_nZ3kgsrIpW0L', '2026-09-08 17:34:46', '2026-09-15 10:04:49'),
(94, 91, 76, 'api_calls', 90, 'seed_acme_mZ0ANy3KZRyK', '2026-09-08 19:34:46', '2026-09-15 10:04:49'),
(95, 91, 76, 'api_calls', 96, 'seed_acme_183WaKQKU7E1', '2026-09-08 21:34:46', '2026-09-15 10:04:49'),
(96, 91, 76, 'api_calls', 89, 'seed_acme_JE4lnNz1e5C8', '2026-09-08 23:34:46', '2026-09-15 10:04:49'),
(97, 91, 76, 'api_calls', 89, 'seed_acme_kzhHJvz82FNV', '2026-09-09 01:34:46', '2026-09-15 10:04:49'),
(98, 91, 76, 'api_calls', 88, 'seed_acme_BZakPQ6nqUI6', '2026-09-09 03:34:46', '2026-09-15 10:04:49'),
(99, 91, 76, 'api_calls', 100, 'seed_acme_hZlqXwr9jSUM', '2026-09-09 05:34:46', '2026-09-15 10:04:49'),
(100, 91, 76, 'api_calls', 97, 'seed_acme_qcirfXEHeE0I', '2026-09-09 07:34:46', '2026-09-15 10:04:49'),
(101, 91, 76, 'api_calls', 102, 'seed_acme_1cuu5ULSQQQq', '2026-09-09 09:34:46', '2026-09-15 10:04:49'),
(102, 91, 76, 'api_calls', 94, 'seed_acme_blZytvRbZiHN', '2026-09-09 15:34:46', '2026-09-15 10:04:49'),
(103, 91, 76, 'api_calls', 85, 'seed_acme_ILXR2AoVz3cx', '2026-09-09 17:34:46', '2026-09-15 10:04:49'),
(104, 91, 76, 'api_calls', 92, 'seed_acme_J56S5kb5wpyR', '2026-09-09 19:34:46', '2026-09-15 10:04:49'),
(105, 91, 76, 'api_calls', 98, 'seed_acme_fk7nLjc0q3qL', '2026-09-09 21:34:46', '2026-09-15 10:04:49'),
(106, 91, 76, 'api_calls', 102, 'seed_acme_gdHAj2ugYyKK', '2026-09-09 23:34:46', '2026-09-15 10:04:49'),
(107, 91, 76, 'api_calls', 87, 'seed_acme_p38ZlZIxM2gg', '2026-09-10 01:34:46', '2026-09-15 10:04:49'),
(108, 91, 76, 'api_calls', 96, 'seed_acme_btfzO6gGiHcI', '2026-09-10 03:34:46', '2026-09-15 10:04:49'),
(109, 91, 76, 'api_calls', 95, 'seed_acme_Rwi6ksnUJv1L', '2026-09-10 05:34:46', '2026-09-15 10:04:49'),
(110, 91, 76, 'api_calls', 90, 'seed_acme_ozZ6qOKwTy03', '2026-09-10 07:34:46', '2026-09-15 10:04:49'),
(111, 91, 76, 'api_calls', 89, 'seed_acme_fdaDxkxQeW47', '2026-09-10 09:34:46', '2026-09-15 10:04:49'),
(112, 91, 76, 'api_calls', 83, 'seed_acme_1QCCUWrfFhnD', '2026-09-10 15:34:46', '2026-09-15 10:04:49'),
(113, 91, 76, 'api_calls', 96, 'seed_acme_defneRLNrWt2', '2026-09-10 17:34:46', '2026-09-15 10:04:49'),
(114, 91, 76, 'api_calls', 98, 'seed_acme_Wob9SmEAdCla', '2026-09-10 19:34:46', '2026-09-15 10:04:49'),
(115, 91, 76, 'api_calls', 100, 'seed_acme_xQBIhyYEo6xH', '2026-09-10 21:34:46', '2026-09-15 10:04:49'),
(116, 91, 76, 'api_calls', 96, 'seed_acme_0PGn4Nvx4lpk', '2026-09-10 23:34:46', '2026-09-15 10:04:49'),
(117, 91, 76, 'api_calls', 84, 'seed_acme_nQQDGS2sPfle', '2026-09-11 01:34:46', '2026-09-15 10:04:49'),
(118, 91, 76, 'api_calls', 103, 'seed_acme_0HFsnGQdo9c1', '2026-09-11 03:34:46', '2026-09-15 10:04:49'),
(119, 91, 76, 'api_calls', 95, 'seed_acme_70TMmnzDXQLz', '2026-09-11 05:34:46', '2026-09-15 10:04:49'),
(120, 91, 76, 'api_calls', 102, 'seed_acme_j2QUsBhbEqQd', '2026-09-11 07:34:46', '2026-09-15 10:04:49'),
(121, 91, 76, 'api_calls', 98, 'seed_acme_9pQlseosA2CE', '2026-09-11 09:34:46', '2026-09-15 10:04:49'),
(122, 91, 76, 'api_calls', 101, 'seed_acme_zpSOeJrtn4Hl', '2026-09-11 15:34:46', '2026-09-15 10:04:49'),
(123, 91, 76, 'api_calls', 103, 'seed_acme_PMhNT4XHJAeN', '2026-09-11 17:34:46', '2026-09-15 10:04:49'),
(124, 91, 76, 'api_calls', 86, 'seed_acme_X57VxnpuCUi4', '2026-09-11 19:34:46', '2026-09-15 10:04:49'),
(125, 91, 76, 'api_calls', 96, 'seed_acme_jZObV8jj3c4I', '2026-09-11 21:34:46', '2026-09-15 10:04:49'),
(126, 91, 76, 'api_calls', 83, 'seed_acme_d56nlVVO3XJF', '2026-09-11 23:34:46', '2026-09-15 10:04:49'),
(127, 91, 76, 'api_calls', 103, 'seed_acme_56sOjdtvn229', '2026-09-12 01:34:46', '2026-09-15 10:04:49'),
(128, 91, 76, 'api_calls', 85, 'seed_acme_c3Jj66vnt9zQ', '2026-09-12 03:34:46', '2026-09-15 10:04:49'),
(129, 91, 76, 'api_calls', 90, 'seed_acme_hnGdapeYSv94', '2026-09-12 05:34:46', '2026-09-15 10:04:49'),
(130, 91, 76, 'api_calls', 98, 'seed_acme_7hMokvJuzu7c', '2026-09-12 07:34:46', '2026-09-15 10:04:49'),
(131, 91, 76, 'api_calls', 86, 'seed_acme_qgZb9qkxUxfB', '2026-09-12 09:34:46', '2026-09-15 10:04:49'),
(132, 91, 76, 'api_calls', 99, 'seed_acme_jAjHfm9iRvND', '2026-09-12 15:34:46', '2026-09-15 10:04:49'),
(133, 91, 76, 'api_calls', 103, 'seed_acme_6EMqYg7wFZ0L', '2026-09-12 17:34:46', '2026-09-15 10:04:49'),
(134, 91, 76, 'api_calls', 94, 'seed_acme_jY4jnMvCx6Fm', '2026-09-12 19:34:46', '2026-09-15 10:04:49'),
(135, 91, 76, 'api_calls', 88, 'seed_acme_J1eW6ERkOaX7', '2026-09-12 21:34:46', '2026-09-15 10:04:49'),
(136, 91, 76, 'api_calls', 99, 'seed_acme_hFiadQKJMgR0', '2026-09-12 23:34:46', '2026-09-15 10:04:49'),
(137, 91, 76, 'api_calls', 100, 'seed_acme_7RcrEmFedaAI', '2026-09-13 01:34:46', '2026-09-15 10:04:49'),
(138, 91, 76, 'api_calls', 93, 'seed_acme_nZIWScQIGtD4', '2026-09-13 03:34:46', '2026-09-15 10:04:49'),
(139, 91, 76, 'api_calls', 85, 'seed_acme_za5kLNwDwKMv', '2026-09-13 05:34:46', '2026-09-15 10:04:49'),
(140, 91, 76, 'api_calls', 98, 'seed_acme_VwEwzBS1q1y5', '2026-09-13 07:34:46', '2026-09-15 10:04:49'),
(141, 91, 76, 'api_calls', 87, 'seed_acme_mZbMATlyXXKt', '2026-09-13 09:34:46', '2026-09-15 10:04:49'),
(142, 91, 76, 'api_calls', 89, 'seed_acme_mjmTu00QkUa6', '2026-09-13 15:34:46', '2026-09-15 10:04:49'),
(143, 91, 76, 'api_calls', 89, 'seed_acme_gTCoOPZfcaZz', '2026-09-13 17:34:46', '2026-09-15 10:04:49'),
(144, 91, 76, 'api_calls', 83, 'seed_acme_kWmdBadDoo6b', '2026-09-13 19:34:46', '2026-09-15 10:04:49'),
(145, 91, 76, 'api_calls', 103, 'seed_acme_lxVEmEPT5OKE', '2026-09-13 21:34:46', '2026-09-15 10:04:49'),
(146, 91, 76, 'api_calls', 102, 'seed_acme_gAnSL2EEf9nd', '2026-09-13 23:34:46', '2026-09-15 10:04:49'),
(147, 91, 76, 'api_calls', 86, 'seed_acme_stC6f1BSVLf2', '2026-09-14 01:34:46', '2026-09-15 10:04:49'),
(148, 91, 76, 'api_calls', 94, 'seed_acme_zOR7r9rhEgoO', '2026-09-14 03:34:46', '2026-09-15 10:04:49'),
(149, 91, 76, 'api_calls', 83, 'seed_acme_Jt0O8Rp7PTZK', '2026-09-14 05:34:46', '2026-09-15 10:04:49'),
(150, 91, 76, 'api_calls', 94, 'seed_acme_8xDgHDJD65nH', '2026-09-14 07:34:46', '2026-09-15 10:04:49'),
(151, 91, 76, 'api_calls', 100, 'seed_acme_34McSlAbUsu0', '2026-09-14 09:34:46', '2026-09-15 10:04:49'),
(152, 91, 76, 'api_calls', 91, 'seed_acme_T8BC315WwxH7', '2026-09-14 15:34:46', '2026-09-15 10:04:49'),
(153, 91, 76, 'api_calls', 94, 'seed_acme_YeBKZ0rrrzgP', '2026-09-14 17:34:46', '2026-09-15 10:04:49'),
(154, 91, 76, 'api_calls', 97, 'seed_acme_GUdQugd3gSI3', '2026-09-14 19:34:46', '2026-09-15 10:04:49'),
(155, 91, 76, 'api_calls', 89, 'seed_acme_SE9y2yyLhpZw', '2026-09-14 21:34:46', '2026-09-15 10:04:49'),
(156, 91, 76, 'api_calls', 86, 'seed_acme_6nI3XL7SS4Vt', '2026-09-14 23:34:46', '2026-09-15 10:04:49'),
(157, 91, 76, 'api_calls', 94, 'seed_acme_kJmrhsesTRwx', '2026-09-15 01:34:46', '2026-09-15 10:04:49'),
(158, 91, 76, 'api_calls', 97, 'seed_acme_nugD8k2yi2E2', '2026-09-15 03:34:46', '2026-09-15 10:04:49'),
(159, 91, 76, 'api_calls', 87, 'seed_acme_C9EWNWsfAIMr', '2026-09-15 05:34:46', '2026-09-15 10:04:49'),
(160, 91, 76, 'api_calls', 98, 'seed_acme_oJx5lKCe53QI', '2026-09-15 07:34:46', '2026-09-15 10:04:49'),
(161, 91, 76, 'api_calls', 99, 'seed_acme_23BRleNr5wQt', '2026-09-15 09:34:46', '2026-09-15 10:04:49'),
(162, 91, 76, 'api_calls', 95, 'seed_acme_3f31p8agNogT', '2026-09-15 15:34:46', '2026-09-15 10:04:49'),
(163, 91, 76, 'api_calls', 88, 'seed_acme_gkPNfFd5cqCk', '2026-09-15 17:34:46', '2026-09-15 10:04:49'),
(164, 91, 76, 'api_calls', 94, 'seed_acme_FKwkVEa4RoGP', '2026-09-15 19:34:46', '2026-09-15 10:04:49'),
(165, 91, 76, 'api_calls', 91, 'seed_acme_fdMDNGxumhVh', '2026-09-15 21:34:46', '2026-09-15 10:04:49'),
(166, 91, 76, 'api_calls', 96, 'seed_acme_Fm4tEhTpXHIm', '2026-09-15 23:34:46', '2026-09-15 10:04:49'),
(167, 91, 76, 'api_calls', 85, 'seed_acme_rkR6fJ9NOUQl', '2026-09-16 01:34:46', '2026-09-15 10:04:49'),
(168, 91, 76, 'api_calls', 93, 'seed_acme_iUHlcmHd5LmM', '2026-09-16 03:34:46', '2026-09-15 10:04:49'),
(169, 91, 76, 'api_calls', 94, 'seed_acme_mFuTmaL52Xi0', '2026-09-16 05:34:46', '2026-09-15 10:04:49'),
(170, 91, 76, 'api_calls', 100, 'seed_acme_mQOqO27xtLEc', '2026-09-16 07:34:46', '2026-09-15 10:04:49'),
(171, 91, 76, 'api_calls', 85, 'seed_acme_3e3rJHrWkiIc', '2026-09-16 09:34:46', '2026-09-15 10:04:49'),
(172, 91, 77, 'api_calls', 245, 'seed_globex_MuREMLEg1Bh1', '2026-08-18 15:34:46', '2026-09-15 10:04:49'),
(173, 91, 77, 'api_calls', 190, 'seed_globex_Tr0PHo235vNI', '2026-08-20 15:34:46', '2026-09-15 10:04:49'),
(174, 91, 77, 'api_calls', 229, 'seed_globex_q7uv2yBLqjZb', '2026-08-22 15:34:46', '2026-09-15 10:04:49'),
(175, 91, 77, 'api_calls', 201, 'seed_globex_QicYa0RIqfyC', '2026-08-24 15:34:46', '2026-09-15 10:04:49'),
(176, 91, 77, 'api_calls', 256, 'seed_globex_ScfaudizeTgN', '2026-08-26 15:34:46', '2026-09-15 10:04:49'),
(177, 91, 77, 'api_calls', 225, 'seed_globex_s7YnCwYgC1Mg', '2026-08-28 15:34:46', '2026-09-15 10:04:49'),
(178, 91, 77, 'api_calls', 233, 'seed_globex_xpQKWNpVDZFa', '2026-08-30 15:34:46', '2026-09-15 10:04:49'),
(179, 91, 77, 'api_calls', 243, 'seed_globex_slrKuaviklKl', '2026-09-01 15:34:46', '2026-09-15 10:04:49'),
(180, 91, 77, 'api_calls', 219, 'seed_globex_vzOzHjGH7m6o', '2026-09-03 15:34:46', '2026-09-15 10:04:49'),
(181, 91, 77, 'api_calls', 254, 'seed_globex_aZsiu5nw1ZeB', '2026-09-05 15:34:46', '2026-09-15 10:04:49'),
(182, 91, 77, 'api_calls', 208, 'seed_globex_bpdTf3Tev1eh', '2026-09-07 15:34:46', '2026-09-15 10:04:49'),
(183, 91, 77, 'api_calls', 256, 'seed_globex_teuuTuqdfE0f', '2026-09-09 15:34:46', '2026-09-15 10:04:49'),
(184, 91, 77, 'api_calls', 187, 'seed_globex_f72Hj8Mw6eH0', '2026-09-11 15:34:46', '2026-09-15 10:04:49'),
(185, 91, 77, 'api_calls', 220, 'seed_globex_D8ZKKn5ePvIK', '2026-09-13 15:34:46', '2026-09-15 10:04:49'),
(186, 91, 77, 'api_calls', 237, 'seed_globex_0ISnTxf7VSpQ', '2026-09-15 15:34:46', '2026-09-15 10:04:49'),
(187, 91, 78, 'api_calls', 900, 'seed_initech_tEvSOzcuFoho', '2026-09-11 15:34:46', '2026-09-15 10:04:49'),
(188, 91, 78, 'api_calls', 900, 'seed_initech_eqw5ozjnKJpx', '2026-09-12 15:34:46', '2026-09-15 10:04:49'),
(189, 91, 78, 'api_calls', 900, 'seed_initech_Tifo4q6nVtT9', '2026-09-13 15:34:46', '2026-09-15 10:04:49'),
(190, 91, 78, 'api_calls', 900, 'seed_initech_N1YyH8ZGPbf6', '2026-09-14 15:34:46', '2026-09-15 10:04:49'),
(191, 91, 78, 'api_calls', 900, 'seed_initech_0SW6OPbZsHuH', '2026-09-15 15:34:46', '2026-09-15 10:04:49'),
(192, 91, 79, 'api_calls', 120, 'seed_apex_seg1_3oP4rZu3ixdI', '2026-08-18 15:34:46', '2026-09-15 10:04:49'),
(193, 91, 79, 'api_calls', 120, 'seed_apex_seg1_p7w9vUUglIxQ', '2026-08-20 15:34:46', '2026-09-15 10:04:49'),
(194, 91, 79, 'api_calls', 120, 'seed_apex_seg1_Q1fdX04wAmUo', '2026-08-22 15:34:46', '2026-09-15 10:04:49'),
(195, 91, 79, 'api_calls', 120, 'seed_apex_seg1_lr3I84iRUt7p', '2026-08-24 15:34:46', '2026-09-15 10:04:49'),
(196, 91, 79, 'api_calls', 120, 'seed_apex_seg1_wzh8wH9ZadWc', '2026-08-26 15:34:46', '2026-09-15 10:04:49'),
(197, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_pLVj7YpM7pxU', '2026-08-29 15:34:46', '2026-09-15 10:04:49'),
(198, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_6FyFPOgQSYky', '2026-08-31 15:34:46', '2026-09-15 10:04:49'),
(199, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_ebYk7YtNt788', '2026-09-02 15:34:46', '2026-09-15 10:04:49'),
(200, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_ApjcAlbYYDzL', '2026-09-04 15:34:46', '2026-09-15 10:04:49'),
(201, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_iRmjf1OdJMyD', '2026-09-06 15:34:46', '2026-09-15 10:04:49'),
(202, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_pGGTyKFBzzze', '2026-09-08 15:34:46', '2026-09-15 10:04:49'),
(203, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_KsfIDMR5HBtl', '2026-09-10 15:34:46', '2026-09-15 10:04:49'),
(204, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_WGSHCxQaFRhf', '2026-09-12 15:34:46', '2026-09-15 10:04:49'),
(205, 91, 79, 'api_calls', 1400, 'seed_apex_seg2_9U2V6YAeYHZG', '2026-09-14 15:34:46', '2026-09-15 10:04:49'),
(206, 91, 80, 'api_calls', 1150, 'seed_stale_prev_lESnYOzoNlzQ', '2026-07-22 15:34:46', '2026-09-15 10:04:49'),
(207, 91, 80, 'api_calls', 1150, 'seed_stale_prev_InxDnFSjpVc4', '2026-07-24 15:34:46', '2026-09-15 10:04:49'),
(208, 91, 80, 'api_calls', 1150, 'seed_stale_prev_6FXLXLqfMPMf', '2026-07-26 15:34:46', '2026-09-15 10:04:49'),
(209, 91, 80, 'api_calls', 1150, 'seed_stale_prev_f4HLtrTNukBP', '2026-07-28 15:34:46', '2026-09-15 10:04:49'),
(210, 91, 80, 'api_calls', 1150, 'seed_stale_prev_oHuT2hRW8Xp2', '2026-07-30 15:34:46', '2026-09-15 10:04:49'),
(211, 91, 80, 'api_calls', 1150, 'seed_stale_prev_HWCOR16Sy3Z2', '2026-08-01 15:34:46', '2026-09-15 10:04:49'),
(212, 91, 80, 'api_calls', 1150, 'seed_stale_prev_uPku3KUOLM0N', '2026-08-03 15:34:46', '2026-09-15 10:04:49'),
(213, 91, 80, 'api_calls', 1150, 'seed_stale_prev_dEtoL8QBYffA', '2026-08-05 15:34:46', '2026-09-15 10:04:49'),
(214, 91, 80, 'api_calls', 1150, 'seed_stale_prev_KP5pLfYBPIJ7', '2026-08-07 15:34:46', '2026-09-15 10:04:49'),
(215, 91, 80, 'api_calls', 1150, 'seed_stale_prev_Nu0PduP5q7T3', '2026-08-09 15:34:46', '2026-09-15 10:04:49'),
(216, 91, 80, 'api_calls', 1150, 'seed_stale_prev_XAHU92HPoM0g', '2026-08-11 15:34:46', '2026-09-15 10:04:49'),
(217, 91, 80, 'api_calls', 1150, 'seed_stale_prev_f3TgdN2vvAkM', '2026-08-13 15:34:46', '2026-09-15 10:04:49'),
(218, 91, 80, 'api_calls', 450, 'seed_stale_curr_hsFFbB6XVrx7', '2026-08-21 15:34:46', '2026-09-15 10:04:49'),
(219, 91, 80, 'api_calls', 450, 'seed_stale_curr_KhfOxEF7hwkY', '2026-08-27 15:34:46', '2026-09-15 10:04:49'),
(220, 91, 80, 'api_calls', 450, 'seed_stale_curr_vgVbtuShkqfD', '2026-09-02 15:34:46', '2026-09-15 10:04:49'),
(221, 91, 80, 'api_calls', 450, 'seed_stale_curr_C9GmNe9S3UTO', '2026-09-08 15:34:46', '2026-09-15 10:04:49'),
(222, 92, 81, 'api_calls', 4500, 'req_test_0067', '2026-09-10 14:30:00', '2026-09-15 15:37:36'),
(223, 92, 82, 'api_calls', 4500, 'req_test_0046', '2026-09-16 08:30:00', '2026-09-16 03:46:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_merchant_id_email_unique` (`merchant_id`,`email`),
  ADD KEY `customers_merchant_id_external_id_index` (`merchant_id`,`external_id`);

--
-- Indexes for table `daily_usages`
--
ALTER TABLE `daily_usages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `daily_usages_tenant_cust_metric_date_unique` (`merchant_id`,`customer_id`,`metric`,`usage_date`),
  ADD KEY `daily_usages_customer_id_usage_date_index` (`customer_id`,`usage_date`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoices_invoice_number_unique` (`invoice_number`),
  ADD KEY `invoices_subscription_id_foreign` (`subscription_id`),
  ADD KEY `invoices_merchant_id_status_index` (`merchant_id`,`status`),
  ADD KEY `invoices_customer_id_period_end_index` (`customer_id`,`period_end`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_items_invoice_id_type_index` (`invoice_id`,`type`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `merchants`
--
ALTER TABLE `merchants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `merchants_slug_unique` (`slug`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plans_merchant_id_code_unique` (`merchant_id`,`code`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_plan_id_foreign` (`plan_id`),
  ADD KEY `subscriptions_merchant_id_status_index` (`merchant_id`,`status`),
  ADD KEY `subscriptions_customer_id_status_index` (`customer_id`,`status`),
  ADD KEY `subscriptions_current_cycle_end_status_index` (`current_cycle_end`,`status`);

--
-- Indexes for table `subscription_segments`
--
ALTER TABLE `subscription_segments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_segments_plan_id_foreign` (`plan_id`),
  ADD KEY `subscription_segments_subscription_id_starts_at_ends_at_index` (`subscription_id`,`starts_at`,`ends_at`),
  ADD KEY `subscription_segments_subscription_id_billing_cycle_start_index` (`subscription_id`,`billing_cycle_start`);

--
-- Indexes for table `usage_events`
--
ALTER TABLE `usage_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usage_events_idempotency_key_unique` (`idempotency_key`),
  ADD KEY `usage_events_customer_id_foreign` (`customer_id`),
  ADD KEY `usage_events_merchant_id_customer_id_recorded_at_index` (`merchant_id`,`customer_id`,`recorded_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `daily_usages`
--
ALTER TABLE `daily_usages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `merchants`
--
ALTER TABLE `merchants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `subscription_segments`
--
ALTER TABLE `subscription_segments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `usage_events`
--
ALTER TABLE `usage_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_usages`
--
ALTER TABLE `daily_usages`
  ADD CONSTRAINT `daily_usages_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_usages_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plans`
--
ALTER TABLE `plans`
  ADD CONSTRAINT `plans_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_segments`
--
ALTER TABLE `subscription_segments`
  ADD CONSTRAINT `subscription_segments_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_segments_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `usage_events`
--
ALTER TABLE `usage_events`
  ADD CONSTRAINT `usage_events_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usage_events_merchant_id_foreign` FOREIGN KEY (`merchant_id`) REFERENCES `merchants` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
