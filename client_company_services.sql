-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 26, 2026 at 11:23 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `client_company_services`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `second_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `mobile_number` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `status` int NOT NULL COMMENT '0 : Disabled ,  1: Enabled , 2: Delete',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Truncate table before insert `clients`
--

TRUNCATE TABLE `clients`;
-- --------------------------------------------------------

--
-- Table structure for table `client_company_map`
--

DROP TABLE IF EXISTS `client_company_map`;
CREATE TABLE IF NOT EXISTS `client_company_map` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL COMMENT 'reference from the clients table',
  `company_id` int NOT NULL COMMENT 'reference from the companies table',
  `status` int NOT NULL DEFAULT '0' COMMENT '0 : Disabled , 1:Enabled',
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Truncate table before insert `client_company_map`
--

TRUNCATE TABLE `client_company_map`;
--
-- Dumping data for table `client_company_map`
--

INSERT INTO `client_company_map` (`id`, `client_id`, `company_id`, `status`, `created_at`, `updated_at`) VALUES
(4, 24, 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 24, 1, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 27, 7, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 27, 6, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 25, 2, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 26, 5, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 26, 1, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 25, 6, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 25, 5, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `client_service_map`
--

DROP TABLE IF EXISTS `client_service_map`;
CREATE TABLE IF NOT EXISTS `client_service_map` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL COMMENT 'reference from the client table',
  `service_id` int NOT NULL COMMENT 'reference from the service table',
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Truncate table before insert `client_service_map`
--

TRUNCATE TABLE `client_service_map`;
--
-- Dumping data for table `client_service_map`
--

INSERT INTO `client_service_map` (`id`, `client_id`, `service_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 27, 4, 0, '2026-01-26 10:27:50', '2026-01-26 10:27:50'),
(2, 27, 5, 0, '2026-01-26 10:27:50', '2026-01-26 10:27:50'),
(3, 27, 1, 0, '2026-01-26 10:27:50', '2026-01-26 10:27:50'),
(4, 27, 3, 0, '2026-01-26 10:27:50', '2026-01-26 10:27:50');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0 : Disabled , 1: Enabled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Truncate table before insert `companies`
--

TRUNCATE TABLE `companies`;
-- --------------------------------------------------------

--
-- Table structure for table `company_services`
--

DROP TABLE IF EXISTS `company_services`;
CREATE TABLE IF NOT EXISTS `company_services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `sort_order` int NOT NULL,
  `status` int NOT NULL DEFAULT '0' COMMENT '0:Enabled , 1: Disabled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Truncate table before insert `company_services`
--

TRUNCATE TABLE `company_services`;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
