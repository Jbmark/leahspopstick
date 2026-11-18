-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql102.infinityfree.com
-- Generation Time: Nov 18, 2026 at 01:37 AM
-- Server version: 11.4.13-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_43185150_popstickmanufacturer`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `contact_number`, `email`, `address`, `created_at`, `updated_at`, `password`, `email_verified`) VALUES
(1, 'ABC Ice Drop Factory', '09123456789', NULL, NULL, '2026-08-28 23:15:22', '2026-08-28 23:15:22', NULL, 0),
(2, 'Aki', '09111222333', '4ki@gmail.com', 'csu, ampayon', '2026-10-07 18:10:54', '2026-10-27 20:23:12', '$2y$12$z/isSP8EXR4vs2x1EwvIpeq4Al6NZKsxSoDzBTRCuUbbdD/5p6M3G', 0),
(3, 'hello', '09000000000', NULL, 'butuan', '2026-10-07 18:15:50', '2026-10-07 18:15:50', NULL, 0),
(4, 'nmnmnmnmn', '097868678686', NULL, 'asdasd', '2026-10-14 13:22:11', '2026-10-14 13:22:11', NULL, 0),
(5, 'mnmnmnmnm', '09090909099', NULL, 'mmmmm', '2026-10-15 18:55:10', '2026-10-15 18:55:10', NULL, 0),
(6, 'Juan', '09123', NULL, 'basag', '2026-10-16 17:09:23', '2026-10-16 17:09:23', NULL, 0),
(7, 'Boboy', '09456', NULL, 'tiniwisan', '2026-10-16 17:20:56', '2026-10-16 17:20:56', NULL, 0),
(8, 'caloy', '09789', NULL, 'antongalon', '2026-10-17 08:26:38', '2026-10-17 08:26:38', NULL, 0),
(9, 'Jeah Suico', '09467671297', NULL, 'Villa Kananga, Butuan City', '2026-10-17 11:03:09', '2026-10-17 11:03:09', NULL, 0),
(10, 'alola', '9995', NULL, 'saa', '2026-10-17 14:53:13', '2026-10-17 14:53:13', NULL, 0),
(11, 'Jeb', '09467671292', NULL, 'Butuan City', '2026-10-19 09:33:27', '2026-10-19 09:33:27', NULL, 0),
(12, 'Jeah', '09467671292', 'suicojeah@gmail.com', 'Villa Kananga, Butuan City', '2026-11-04 22:09:45', '2026-11-04 22:09:45', '$2y$12$MWJTosMgXIb9NYiTziLF4ua6JXBQPPoN4as6uh2IWMmDTcPpmzo1S', 0),
(13, 'Camotes', '09467671297', 'gilbert1968suico@gmail.com', 'Cebu City', '2026-11-09 10:05:50', '2026-11-09 10:05:50', NULL, 0),
(14, 'GVSS Inc.', '09089017844', 'GVSSinc@gmail.com', 'Baan Three', '2026-11-11 13:55:30', '2026-11-11 13:55:30', NULL, 0),
(15, 'Gilbert C.Suico', '0946-767-1297', 'Gilbert1968suico@gmail.com', 'Purok 3, Villa Kananga', '2026-11-11 15:20:56', '2026-11-11 15:20:56', NULL, 0),
(16, 'Lou Reales', '09916038241', 'louribethmreales@gmail.com', 'Purok 5, Villa Kananga, Butuan City', '2026-11-12 17:23:19', '2026-11-12 17:23:19', '$2y$12$k7qvp.uTcdJGMjTVyC7EUOAUEdmubfXOIyesHIMbxjPzTPMiZ3PK.', 0),
(17, 'Haej Deer', '09062367687', NULL, 'CCF Butuan', '2026-11-13 12:27:30', '2026-11-13 12:27:30', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `expense_name` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Uncategorized',
  `description` text DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_date` date NOT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_test` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `expense_name`, `category`, `description`, `amount`, `expense_date`, `recorded_by`, `created_at`, `updated_at`, `is_test`) VALUES
(1, 'Falaca Wood', 'Uncategorized', '', '10000.00', '2026-11-09', 1, '2026-11-09 21:31:08', '2026-11-16 03:52:02', 1),
(2, 'labor cost', 'Uncategorized', 'Salary Expense for the week', '45000.00', '2026-11-16', 1, '2026-11-16 08:11:36', '2026-11-16 08:11:36', 0),
(3, 'Boxes and Fillers Purchase', 'Packaging Materials', 'Purchase of boxes and fillers for packaging finished wooden products.', '3000.00', '2026-11-17', 3, '2026-11-17 14:34:04', '2026-11-17 14:34:04', 0);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_batches`
--

CREATE TABLE `inventory_batches` (
  `batch_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_number` varchar(100) DEFAULT NULL,
  `quantity_received` int(11) NOT NULL DEFAULT 0,
  `quantity_remaining` int(11) NOT NULL DEFAULT 0,
  `date_received` datetime NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `status` enum('Available','Depleted') DEFAULT 'Available',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_batches`
--

INSERT INTO `inventory_batches` (`batch_id`, `product_id`, `batch_number`, `quantity_received`, `quantity_remaining`, `date_received`, `expiration_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'B001', 100, 0, '2026-08-28 00:00:00', NULL, 'Depleted', '2026-08-28 22:33:14', '2026-10-07 13:03:12'),
(2, 1, 'B002', 150, 89, '2026-08-28 00:00:00', NULL, 'Available', '2026-08-28 22:33:59', '2026-11-18 11:10:59'),
(3, 1, 'WS001', 50, 50, '2026-08-28 00:00:00', NULL, 'Available', '2026-08-28 22:34:47', '2026-08-28 22:34:47'),
(4, 2, 'WS001', 50, 40, '2026-08-28 00:00:00', NULL, 'Available', '2026-08-28 22:35:13', '2026-08-28 23:33:49'),
(5, 2, 'B001', 20, 0, '2026-01-01 00:00:00', NULL, 'Depleted', '2026-08-30 09:42:13', '2026-11-09 21:32:25'),
(6, 2, 'B002', 30, 8, '2026-01-05 00:00:00', NULL, 'Available', '2026-08-30 09:43:16', '2026-11-17 18:36:56'),
(7, 2, 'B003', 50, 50, '2026-01-10 00:00:00', NULL, 'Available', '2026-08-30 09:44:01', '2026-08-30 09:44:01'),
(8, 6, 'B001', 100, 93, '2026-09-07 00:00:00', NULL, 'Available', '2026-10-07 13:15:41', '2026-11-16 08:51:25'),
(9, 6, 'B002', 50, 50, '2026-10-07 00:00:00', NULL, 'Available', '2026-10-07 13:16:26', '2026-10-07 13:21:34'),
(10, 3, 'WF01', 300, 271, '2026-10-15 00:00:00', NULL, 'Available', '2026-10-15 18:25:56', '2026-11-16 08:13:56'),
(11, 8, 'S001', 300, 274, '2026-10-15 00:00:00', NULL, 'Available', '2026-10-15 18:26:17', '2026-11-12 16:14:38'),
(12, 5, 'C001', 300, 152, '2026-10-15 00:00:00', NULL, 'Available', '2026-10-15 18:26:42', '2026-11-18 11:10:59'),
(13, 5, 'C002', 200, 200, '2026-10-15 00:00:00', NULL, 'Available', '2026-10-15 18:27:35', '2026-10-15 18:27:35'),
(14, 8, 'S002', 300, 300, '2026-10-17 00:00:00', NULL, 'Available', '2026-10-17 14:30:51', '2026-10-17 14:30:51'),
(15, 6, 'BATCH-20261017-001', 50, 50, '2026-10-17 00:00:00', NULL, 'Available', '2026-10-17 15:28:00', '2026-10-17 15:28:00'),
(16, 1, 'BATCH-20261017-002', 50, 50, '2026-10-17 00:00:00', NULL, 'Available', '2026-10-17 15:29:16', '2026-10-17 15:29:16'),
(17, 2, 'WS-20261017-001', 3, 3, '2026-10-17 00:00:00', NULL, 'Available', '2026-10-17 15:39:52', '2026-10-17 15:39:52'),
(18, 2, 'WS-20261017-002', 100, 100, '2026-10-17 00:00:00', NULL, 'Available', '2026-10-17 15:47:06', '2026-10-17 15:47:06'),
(19, 1, 'PS-20261017-001', 5, 5, '2026-10-17 00:00:00', NULL, 'Available', '2026-10-17 16:02:58', '2026-10-17 16:02:58'),
(20, 1, 'PS-20261017-002', 5, 5, '2026-10-17 00:00:00', NULL, 'Available', '2026-10-17 16:02:59', '2026-10-17 16:02:59');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `transaction_type` enum('Stock In','Stock Out','Adjustment','Return') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `transaction_date` datetime DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_transactions`
--

INSERT INTO `inventory_transactions` (`transaction_id`, `product_id`, `batch_id`, `transaction_type`, `quantity`, `reference_id`, `transaction_date`, `remarks`, `created_by`) VALUES
(1, 1, 1, 'Stock In', 100, NULL, '2026-08-28 22:33:15', 'Initial batch entry', 1),
(2, 1, 2, 'Stock In', 150, NULL, '2026-08-28 22:33:59', 'Initial batch entry', 1),
(3, 1, 3, 'Stock In', 50, NULL, '2026-08-28 22:34:48', 'Initial batch entry', 1),
(4, 2, 4, 'Stock In', 50, NULL, '2026-08-28 22:35:14', 'Initial batch entry', 1),
(5, 1, 1, 'Stock Out', 100, 1, '2026-08-28 23:33:49', 'FIFO deduction', 4),
(6, 1, 2, 'Stock Out', 20, 1, '2026-08-28 23:33:49', 'FIFO deduction', 4),
(7, 2, 4, 'Stock Out', 10, 1, '2026-08-28 23:33:50', 'FIFO deduction', 4),
(8, 2, 5, 'Stock In', 20, NULL, '2026-08-30 09:42:14', 'Initial batch entry', 1),
(9, 2, 6, 'Stock In', 30, NULL, '2026-08-30 09:43:17', 'Initial batch entry', 1),
(10, 2, 7, 'Stock In', 50, NULL, '2026-08-30 09:44:01', 'Initial batch entry', 1),
(11, 2, 5, 'Stock Out', 20, 2, '2026-08-30 09:48:01', 'FIFO deduction after completed order', 1),
(12, 2, 6, 'Stock Out', 15, 2, '2026-08-30 09:48:01', 'FIFO deduction after completed order', 1),
(13, 2, 6, 'Return', 15, 2, '2026-08-30 09:49:54', 'Inventory restored after order cancellation', 1),
(14, 2, 5, 'Return', 20, 2, '2026-08-30 09:49:54', 'Inventory restored after order cancellation', 1),
(15, 6, 8, 'Stock In', 100, NULL, '2026-10-07 13:15:42', 'Initial batch entry', 1),
(16, 6, 9, 'Stock In', 50, NULL, '2026-10-07 13:16:27', 'Initial batch entry', 1),
(17, 6, 8, 'Stock Out', 100, 4, '2026-10-07 13:20:53', 'FIFO deduction after completed order', 1),
(18, 6, 9, 'Stock Out', 20, 4, '2026-10-07 13:20:53', 'FIFO deduction after completed order', 1),
(19, 6, 9, 'Return', 20, 4, '2026-10-07 13:21:34', 'Inventory restored after order cancellation', 1),
(20, 6, 8, 'Return', 100, 4, '2026-10-07 13:21:34', 'Inventory restored after order cancellation', 1),
(21, 1, 2, 'Stock Out', 10, 5, '2026-10-07 18:19:19', 'FIFO deduction after completed order', 1),
(22, 3, 10, 'Stock In', 300, NULL, '2026-10-15 18:25:56', 'Initial batch entry', 1),
(23, 8, 11, 'Stock In', 300, NULL, '2026-10-15 18:26:17', 'Initial batch entry', 1),
(24, 5, 12, 'Stock In', 300, NULL, '2026-10-15 18:26:43', 'Initial batch entry', 1),
(25, 5, 13, 'Stock In', 200, NULL, '2026-10-15 18:27:35', 'Initial batch entry', 1),
(26, 1, 2, 'Stock Out', 20, 9, '2026-10-16 15:36:39', 'FIFO deduction after completed order', 1),
(27, 5, 12, 'Stock Out', 1, 3, '2026-10-16 16:58:20', 'FIFO deduction after completed order', 1),
(28, 2, 5, 'Stock Out', 10, 10, '2026-10-16 17:09:50', 'FIFO deduction after completed order', 1),
(29, 5, 12, 'Stock Out', 9, 12, '2026-10-17 08:27:26', 'FIFO deduction after completed order', 1),
(30, 2, 5, 'Stock Out', 2, 13, '2026-10-17 09:10:05', 'FIFO deduction after completed order', 1),
(31, 5, 12, 'Stock Out', 2, 13, '2026-10-17 09:10:05', 'FIFO deduction after completed order', 1),
(32, 3, 10, 'Stock Out', 5, 14, '2026-10-17 11:04:50', 'FIFO deduction after completed order', 1),
(33, 2, 5, 'Stock Out', 1, 17, '2026-10-17 14:17:52', 'FIFO deduction after completed order', 1),
(34, 5, 12, 'Stock Out', 1, 17, '2026-10-17 14:17:52', 'FIFO deduction after completed order', 1),
(35, 8, 14, 'Stock In', 300, NULL, '2026-10-17 14:30:51', 'Initial batch entry', 1),
(36, 6, 15, 'Stock In', 50, NULL, '2026-10-17 15:28:00', 'Initial batch entry', 1),
(37, 1, 16, 'Stock In', 50, NULL, '2026-10-17 15:29:16', 'Initial batch entry', 1),
(38, 2, 17, 'Stock In', 3, NULL, '2026-10-17 15:39:52', 'Initial batch entry', 1),
(39, 2, 18, 'Stock In', 100, NULL, '2026-10-17 15:47:06', 'Initial batch entry', 1),
(40, 1, 19, 'Stock In', 5, NULL, '2026-10-17 16:02:58', 'Initial batch entry', 1),
(41, 1, 20, 'Stock In', 5, NULL, '2026-10-17 16:02:59', 'Initial batch entry', 1),
(42, 2, 5, 'Stock Out', 5, 19, '2026-10-19 09:36:29', 'FIFO deduction after completed order', 1),
(43, 3, 10, 'Stock Out', 5, 19, '2026-10-19 09:36:29', 'FIFO deduction after completed order', 1),
(44, 8, 11, 'Stock Out', 5, 19, '2026-10-19 09:36:29', 'FIFO deduction after completed order', 1),
(45, 1, 2, 'Stock Out', 2, 20, '2026-10-22 14:25:01', 'FIFO deduction after completed order', 1),
(46, 1, 2, 'Return', 2, 20, '2026-10-22 14:25:38', 'Inventory restored after order cancellation', 1),
(47, 2, 5, 'Stock Out', 1, 28, '2026-11-09 21:32:08', 'FIFO deduction after completed order', 1),
(48, 5, 12, 'Stock Out', 5, 28, '2026-11-09 21:32:08', 'FIFO deduction after completed order', 1),
(49, 2, 5, 'Stock Out', 1, 27, '2026-11-09 21:32:25', 'FIFO deduction after completed order', 1),
(50, 3, 10, 'Stock Out', 1, 27, '2026-11-09 21:32:25', 'FIFO deduction after completed order', 1),
(51, 5, 12, 'Stock Out', 1, 27, '2026-11-09 21:32:25', 'FIFO deduction after completed order', 1),
(52, 6, 8, 'Stock Out', 1, 27, '2026-11-09 21:32:25', 'FIFO deduction after completed order', 1),
(53, 8, 11, 'Stock Out', 1, 27, '2026-11-09 21:32:25', 'FIFO deduction after completed order', 1),
(54, 5, 12, 'Stock Out', 5, 15, '2026-11-09 21:36:58', 'FIFO deduction after completed order', 1),
(55, 6, 8, 'Stock Out', 5, 15, '2026-11-09 21:36:58', 'FIFO deduction after completed order', 1),
(56, 8, 11, 'Stock Out', 1, 15, '2026-11-09 21:36:58', 'FIFO deduction after completed order', 1),
(57, 2, 6, 'Stock Out', 10, 32, '2026-11-09 21:39:10', 'FIFO deduction after completed order', 1),
(58, 3, 10, 'Stock Out', 10, 32, '2026-11-09 21:39:10', 'FIFO deduction after completed order', 1),
(59, 5, 12, 'Stock Out', 10, 32, '2026-11-09 21:39:10', 'FIFO deduction after completed order', 1),
(60, 8, 11, 'Stock Out', 15, 32, '2026-11-09 21:39:10', 'FIFO deduction after completed order', 1),
(61, 5, 12, 'Stock Out', 4, 36, '2026-11-12 16:05:33', 'FIFO deduction after completed order', 1),
(62, 8, 11, 'Stock Out', 1, 36, '2026-11-12 16:05:33', 'FIFO deduction after completed order', 1),
(63, 2, 6, 'Stock Out', 2, 37, '2026-11-12 16:14:38', 'FIFO deduction after completed order', 1),
(64, 3, 10, 'Stock Out', 8, 37, '2026-11-12 16:14:38', 'FIFO deduction after completed order', 1),
(65, 8, 11, 'Stock Out', 3, 37, '2026-11-12 16:14:38', 'FIFO deduction after completed order', 1),
(66, 5, 12, 'Stock Out', 100, 34, '2026-11-13 11:56:38', 'FIFO deduction after completed order', 1),
(67, 3, 10, 'Stock Out', 3, 41, '2026-11-16 08:13:04', 'FIFO deduction after completed order', 4),
(68, 5, 12, 'Stock Out', 2, 41, '2026-11-16 08:13:04', 'FIFO deduction after completed order', 4),
(69, 5, 12, 'Return', 2, 41, '2026-11-16 08:13:56', 'Inventory restored after order cancellation', 4),
(70, 3, 10, 'Return', 3, 41, '2026-11-16 08:13:56', 'Inventory restored after order cancellation', 4),
(71, 1, 2, 'Stock Out', 1, 42, '2026-11-16 08:51:25', 'FIFO deduction after completed order', 1),
(72, 2, 6, 'Stock Out', 10, 42, '2026-11-16 08:51:25', 'FIFO deduction after completed order', 1),
(73, 6, 8, 'Stock Out', 1, 42, '2026-11-16 08:51:25', 'FIFO deduction after completed order', 1),
(74, 2, 6, 'Stock Out', 1, 44, '2026-11-17 18:32:28', 'FIFO deduction after completed order', 1),
(75, 2, 6, 'Return', 1, 44, '2026-11-17 18:36:56', 'Inventory restored after order cancellation', 1),
(76, 1, 2, 'Stock Out', 10, 45, '2026-11-18 11:10:59', 'FIFO deduction after completed order', 1),
(77, 5, 12, 'Stock Out', 10, 45, '2026-11-18 11:10:59', 'FIFO deduction after completed order', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `encoded_by` int(11) DEFAULT NULL,
  `order_source` enum('Website','Messenger','Viber','Phone','Email','Walk-in') NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Processing','Completed','Cancelled') DEFAULT 'Pending',
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `completed_at` datetime DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_name` varchar(150) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `encoded_by`, `order_source`, `order_date`, `status`, `total_amount`, `completed_at`, `delivery_address`, `remarks`, `created_at`, `updated_at`, `customer_name`, `contact_number`) VALUES
(1, 1, 4, 'Messenger', '2026-08-28 23:25:36', 'Completed', '16200.00', '2026-08-28 23:33:50', 'Talacogon', 'FIFO engine defense simulation test.', '2026-08-28 23:25:36', '2026-08-28 23:33:50', NULL, NULL),
(2, 1, 1, 'Messenger', '2026-08-30 09:46:42', 'Cancelled', '6300.00', NULL, 'Zamboanga City Warehouse Storage', '', '2026-08-30 09:46:42', '2026-08-30 09:49:54', NULL, NULL),
(3, 1, NULL, 'Website', '2026-09-07 14:16:01', 'Completed', '100.00', '2026-10-16 16:58:20', 'san franz', ' [Web Guest Name: jj]', '2026-09-07 14:16:01', '2026-10-16 16:58:20', NULL, NULL),
(4, 1, NULL, 'Website', '2026-10-07 13:18:33', 'Cancelled', '18000.00', NULL, '', '', '2026-10-07 13:18:33', '2026-10-07 13:21:34', NULL, NULL),
(5, 2, NULL, 'Website', '2026-10-07 18:13:05', 'Completed', '1200.00', '2026-10-07 18:19:19', 'ampayon', 'i like it', '2026-10-07 18:13:05', '2026-10-07 18:19:19', NULL, NULL),
(6, 3, NULL, 'Website', '2026-10-07 18:15:51', 'Cancelled', '100.00', NULL, 'butuan', '', '2026-10-07 18:15:51', '2026-10-07 18:18:25', NULL, NULL),
(9, 2, NULL, 'Website', '2026-10-16 15:35:35', 'Completed', '2400.00', '2026-10-16 15:36:39', 'Ampayon', '', '2026-10-16 15:35:35', '2026-10-16 15:36:39', NULL, NULL),
(10, 6, NULL, 'Website', '2026-10-16 17:09:23', 'Completed', '1800.00', '2026-10-16 17:09:50', 'basag', '', '2026-10-16 17:09:23', '2026-10-16 17:09:50', NULL, NULL),
(11, 7, NULL, 'Website', '2026-10-16 17:20:56', 'Pending', '1800.00', NULL, 'tiniwisan', '', '2026-10-16 17:20:56', '2026-10-16 17:20:56', NULL, NULL),
(12, 8, NULL, 'Website', '2026-10-17 08:26:38', 'Completed', '900.00', '2026-10-17 08:27:26', 'antongalon', '', '2026-10-17 08:26:38', '2026-10-17 08:27:26', NULL, NULL),
(13, 2, NULL, 'Website', '2026-10-17 09:09:31', 'Completed', '560.00', '2026-10-17 09:10:05', 'diri paluyo unya straight lang unya balik', '', '2026-10-17 09:09:31', '2026-10-17 09:10:05', NULL, NULL),
(14, 9, NULL, 'Website', '2026-10-17 11:03:09', 'Completed', '900.00', '2026-10-17 11:04:50', 'Villa Kananga, Butuan City', '', '2026-10-17 11:03:09', '2026-10-17 11:04:50', NULL, NULL),
(15, 9, NULL, 'Website', '2026-10-17 11:06:45', 'Completed', '1450.00', '2026-11-09 21:36:58', 'Davao City', '', '2026-10-17 11:06:45', '2026-11-09 21:36:58', NULL, NULL),
(16, 9, NULL, 'Website', '2026-10-17 11:07:09', 'Cancelled', '1450.00', NULL, 'Davao City', '', '2026-10-17 11:07:09', '2026-10-17 11:14:27', NULL, NULL),
(17, 2, NULL, 'Website', '2026-10-17 14:16:46', 'Completed', '280.00', '2026-10-17 14:17:52', 'csu', '', '2026-10-17 14:16:46', '2026-10-17 14:17:52', NULL, NULL),
(19, 11, NULL, 'Website', '2026-10-19 09:33:27', 'Completed', '2800.00', '2026-10-19 09:36:29', 'Butuan City', '', '2026-10-19 09:33:27', '2026-10-19 09:36:29', NULL, NULL),
(20, 2, 1, 'Walk-in', '2026-10-22 14:22:39', 'Cancelled', '240.00', NULL, 'walkin', 'aus', '2026-10-22 14:22:39', '2026-10-22 14:25:38', NULL, NULL),
(21, 6, NULL, 'Website', '2026-10-22 14:56:27', 'Pending', '400.00', NULL, 'kkkkk', '', '2026-10-22 14:56:27', '2026-10-22 14:56:27', NULL, NULL),
(22, 6, NULL, 'Website', '2026-10-22 15:07:29', 'Pending', '200.00', NULL, 'agusan', '', '2026-10-22 15:07:29', '2026-10-22 15:07:29', NULL, NULL),
(23, 6, NULL, 'Website', '2026-10-22 15:29:07', 'Processing', '400.00', NULL, 'csu', '', '2026-10-22 15:29:07', '2026-10-23 14:42:01', 'apollo', '09123'),
(24, 2, NULL, 'Website', '2026-10-23 23:09:10', 'Pending', '580.00', NULL, 'CSU', '', '2026-10-23 23:09:10', '2026-10-23 23:09:10', 'Aki', '09111222333'),
(25, 2, NULL, 'Website', '2026-10-27 20:23:12', 'Pending', '360.00', NULL, 'csu, ampayon', '', '2026-10-27 20:23:12', '2026-10-27 20:23:12', 'Aki', '09111222333'),
(26, 2, NULL, 'Website', '2026-10-28 17:47:38', 'Pending', '270.00', NULL, 'csu, ampayon', '', '2026-10-28 17:47:38', '2026-10-28 17:47:38', 'Aki', '09111222333'),
(27, 12, NULL, 'Website', '2026-11-04 22:12:51', 'Completed', '810.00', '2026-11-09 21:32:25', 'Villa Kananga, Butuan City', '', '2026-11-04 22:12:51', '2026-11-09 21:32:25', 'Jeah', '09467671292'),
(28, 12, NULL, 'Website', '2026-11-09 09:54:44', 'Completed', '680.00', '2026-11-09 21:32:08', 'Villa Kananga, Butuan City', '', '2026-11-09 09:54:44', '2026-11-09 21:32:08', 'Jeah', '09467671292'),
(30, 13, 1, 'Viber', '2026-11-09 10:06:30', 'Pending', '600.00', NULL, 'Cebu City', '', '2026-11-09 10:06:30', '2026-11-09 10:06:30', NULL, NULL),
(31, 11, 1, 'Email', '2026-11-09 10:48:51', 'Pending', '700.00', NULL, 'Davao City', '', '2026-11-09 10:48:51', '2026-11-09 10:48:51', NULL, NULL),
(32, 12, NULL, 'Website', '2026-11-09 21:38:39', 'Completed', '7600.00', '2026-11-09 21:39:10', 'Villa Kananga, Butuan City', '', '2026-11-09 21:38:39', '2026-11-09 21:39:10', 'Jeah', '09467671292'),
(33, 14, NULL, 'Website', '2026-11-11 13:55:30', 'Pending', '100.00', NULL, 'Baan Three', 'Grey Gate hehe', '2026-11-11 13:55:30', '2026-11-11 13:55:30', 'GVSS Inc.', '09089017844'),
(34, 15, NULL, 'Website', '2026-11-11 15:20:56', 'Completed', '10000.00', '2026-11-13 11:56:38', 'Purok 3, Villa Kananga', 'Camella homes', '2026-11-11 15:20:56', '2026-11-13 11:56:38', 'Gilbert C.Suico', '0946-767-1297'),
(35, 12, NULL, 'Website', '2026-11-12 02:51:46', 'Pending', '3740.00', NULL, 'Villa Kananga, Butuan City', '', '2026-11-12 02:51:46', '2026-11-12 02:51:46', 'Jeah', '09467671292'),
(36, 12, NULL, 'Website', '2026-11-12 16:04:37', 'Completed', '600.00', '2026-11-12 16:05:33', 'Villa Kananga, Butuan City', '', '2026-11-12 16:04:37', '2026-11-12 16:05:33', 'Jeah', '09467671292'),
(37, 12, NULL, 'Website', '2026-11-12 16:13:57', 'Completed', '2400.00', '2026-11-12 16:14:38', 'Villa Kananga, Butuan City', '', '2026-11-12 16:13:57', '2026-11-12 16:14:38', 'Jeah', '09467671292'),
(38, 12, NULL, 'Website', '2026-11-12 16:43:50', 'Pending', '100.00', NULL, 'Villa Kananga, Butuan City', '', '2026-11-12 16:43:50', '2026-11-12 16:43:50', 'Jeah', '09467671292'),
(39, 16, NULL, 'Website', '2026-11-12 17:25:31', 'Processing', '300.00', NULL, 'Purok 5, Villa Kananga, Butuan City', '', '2026-11-12 17:25:31', '2026-11-13 11:56:13', 'Lou Reales', '09916038241'),
(40, 17, NULL, 'Website', '2026-11-13 12:27:30', 'Pending', '1600.00', NULL, 'CCF Butuan', 'Please wrap it meticulously.', '2026-11-13 12:27:30', '2026-11-13 12:27:30', 'Haej Deer', '09062367687'),
(41, 12, NULL, 'Website', '2026-11-14 10:47:25', 'Cancelled', '2940.00', NULL, 'Villa Kananga, Butuan City', '', '2026-11-14 10:47:25', '2026-11-16 08:13:56', 'Jeah', '09467671292'),
(42, 12, NULL, 'Website', '2026-11-16 08:51:09', 'Completed', '20400.00', '2026-11-16 08:51:25', 'Villa Kananga, Butuan City', '', '2026-11-16 08:51:09', '2026-11-16 08:51:25', 'Jeah', '09467671292'),
(43, 2, NULL, 'Website', '2026-11-17 18:30:01', 'Cancelled', '1800.00', NULL, 'csu, ampayon', '', '2026-11-17 18:30:01', '2026-11-17 18:31:17', 'Aki', '09111222333'),
(44, 2, NULL, 'Website', '2026-11-17 18:31:34', 'Cancelled', '1800.00', NULL, 'csu, ampayon', '', '2026-11-17 18:31:34', '2026-11-17 18:36:56', 'Aki', '09111222333'),
(45, 12, NULL, 'Website', '2026-11-18 11:10:42', 'Completed', '24000.00', '2026-11-18 11:10:59', 'Villa Kananga, Butuan City', '', '2026-11-18 11:10:42', '2026-11-18 11:10:59', 'Jeah', '09467671292'),
(46, 12, NULL, 'Website', '2026-11-18 11:16:40', 'Pending', '32000.00', NULL, 'Villa Kananga, Butuan City', '', '2026-11-18 11:16:40', '2026-11-18 11:16:40', 'Jeah', '09467671292');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 120, '120.00', '14400.00', '2026-08-28 23:25:38', '2026-08-28 23:25:38'),
(2, 1, 2, 10, '180.00', '1800.00', '2026-08-28 23:25:43', '2026-08-28 23:25:43'),
(3, 2, 2, 35, '180.00', '6300.00', '2026-08-30 09:46:43', '2026-08-30 09:46:43'),
(4, 3, 5, 1, '100.00', '100.00', '2026-09-07 14:16:13', '2026-09-07 14:16:13'),
(5, 4, 6, 120, '150.00', '18000.00', '2026-10-07 13:18:33', '2026-10-07 13:18:33'),
(6, 5, 1, 10, '120.00', '1200.00', '2026-10-07 18:13:05', '2026-10-07 18:13:05'),
(7, 6, 5, 1, '100.00', '100.00', '2026-10-07 18:15:51', '2026-10-07 18:15:51'),
(8, 9, 1, 20, '120.00', '2400.00', '2026-10-16 15:35:35', '2026-10-16 15:35:35'),
(9, 10, 2, 10, '180.00', '1800.00', '2026-10-16 17:09:23', '2026-10-16 17:09:23'),
(10, 11, 2, 10, '180.00', '1800.00', '2026-10-16 17:20:56', '2026-10-16 17:20:56'),
(11, 12, 5, 9, '100.00', '900.00', '2026-10-17 08:26:38', '2026-10-17 08:26:38'),
(12, 13, 5, 2, '100.00', '200.00', '2026-10-17 09:09:31', '2026-10-17 09:09:31'),
(13, 13, 2, 2, '180.00', '360.00', '2026-10-17 09:09:31', '2026-10-17 09:09:31'),
(14, 14, 3, 5, '180.00', '900.00', '2026-10-17 11:03:09', '2026-10-17 11:03:09'),
(15, 15, 5, 5, '100.00', '500.00', '2026-10-17 11:06:45', '2026-10-17 11:06:45'),
(16, 15, 6, 5, '150.00', '750.00', '2026-10-17 11:06:45', '2026-10-17 11:06:45'),
(17, 15, 8, 1, '200.00', '200.00', '2026-10-17 11:06:45', '2026-10-17 11:06:45'),
(18, 16, 5, 5, '100.00', '500.00', '2026-10-17 11:07:09', '2026-10-17 11:07:09'),
(19, 16, 6, 5, '150.00', '750.00', '2026-10-17 11:07:09', '2026-10-17 11:07:09'),
(20, 16, 8, 1, '200.00', '200.00', '2026-10-17 11:07:09', '2026-10-17 11:07:09'),
(21, 17, 5, 1, '100.00', '100.00', '2026-10-17 14:16:46', '2026-10-17 14:16:46'),
(22, 17, 2, 1, '180.00', '180.00', '2026-10-17 14:16:46', '2026-10-17 14:16:46'),
(23, 19, 8, 5, '200.00', '1000.00', '2026-10-19 09:33:27', '2026-10-19 09:33:27'),
(24, 19, 3, 5, '180.00', '900.00', '2026-10-19 09:33:27', '2026-10-19 09:33:27'),
(25, 19, 2, 5, '180.00', '900.00', '2026-10-19 09:33:27', '2026-10-19 09:33:27'),
(26, 20, 1, 2, '120.00', '240.00', '2026-10-22 14:22:39', '2026-10-22 14:22:39'),
(27, 21, 8, 2, '200.00', '400.00', '2026-10-22 14:56:27', '2026-10-22 14:56:27'),
(28, 22, 5, 2, '100.00', '200.00', '2026-10-22 15:07:29', '2026-10-22 15:07:29'),
(29, 23, 8, 2, '200.00', '400.00', '2026-10-22 15:29:07', '2026-10-22 15:29:07'),
(30, 24, 2, 1, '180.00', '180.00', '2026-10-23 23:09:10', '2026-10-23 23:09:10'),
(31, 24, 8, 2, '200.00', '400.00', '2026-10-23 23:09:10', '2026-10-23 23:09:10'),
(32, 25, 3, 2, '180.00', '360.00', '2026-10-27 20:23:12', '2026-10-27 20:23:12'),
(33, 26, 1, 1, '120.00', '120.00', '2026-10-28 17:47:38', '2026-10-28 17:47:38'),
(34, 26, 6, 1, '150.00', '150.00', '2026-10-28 17:47:38', '2026-10-28 17:47:38'),
(35, 27, 5, 1, '100.00', '100.00', '2026-11-04 22:12:51', '2026-11-04 22:12:51'),
(36, 27, 2, 1, '180.00', '180.00', '2026-11-04 22:12:51', '2026-11-04 22:12:51'),
(37, 27, 8, 1, '200.00', '200.00', '2026-11-04 22:12:51', '2026-11-04 22:12:51'),
(38, 27, 3, 1, '180.00', '180.00', '2026-11-04 22:12:51', '2026-11-04 22:12:51'),
(39, 27, 6, 1, '150.00', '150.00', '2026-11-04 22:12:51', '2026-11-04 22:12:51'),
(40, 28, 5, 5, '100.00', '500.00', '2026-11-09 09:54:44', '2026-11-09 09:54:44'),
(41, 28, 2, 1, '180.00', '180.00', '2026-11-09 09:54:44', '2026-11-09 09:54:44'),
(45, 30, 2, 2, '180.00', '360.00', '2026-11-09 10:06:30', '2026-11-09 10:06:30'),
(46, 30, 1, 2, '120.00', '240.00', '2026-11-09 10:06:30', '2026-11-09 10:06:30'),
(47, 31, 5, 7, '100.00', '700.00', '2026-11-09 10:48:51', '2026-11-09 10:48:51'),
(48, 32, 8, 15, '200.00', '3000.00', '2026-11-09 21:38:39', '2026-11-09 21:38:39'),
(49, 32, 3, 10, '180.00', '1800.00', '2026-11-09 21:38:39', '2026-11-09 21:38:39'),
(50, 32, 2, 10, '180.00', '1800.00', '2026-11-09 21:38:39', '2026-11-09 21:38:39'),
(51, 32, 5, 10, '100.00', '1000.00', '2026-11-09 21:38:39', '2026-11-09 21:38:39'),
(52, 33, 5, 1, '100.00', '100.00', '2026-11-11 13:55:30', '2026-11-11 13:55:30'),
(53, 34, 5, 100, '100.00', '10000.00', '2026-11-11 15:20:56', '2026-11-11 15:20:56'),
(54, 35, 8, 2, '200.00', '400.00', '2026-11-12 02:51:46', '2026-11-12 02:51:46'),
(55, 35, 2, 13, '180.00', '2340.00', '2026-11-12 02:51:46', '2026-11-12 02:51:46'),
(56, 35, 5, 4, '100.00', '400.00', '2026-11-12 02:51:46', '2026-11-12 02:51:46'),
(57, 35, 6, 4, '150.00', '600.00', '2026-11-12 02:51:46', '2026-11-12 02:51:46'),
(58, 36, 5, 4, '100.00', '400.00', '2026-11-12 16:04:37', '2026-11-12 16:04:37'),
(59, 36, 8, 1, '200.00', '200.00', '2026-11-12 16:04:37', '2026-11-12 16:04:37'),
(60, 37, 8, 3, '200.00', '600.00', '2026-11-12 16:13:57', '2026-11-12 16:13:57'),
(61, 37, 3, 8, '180.00', '1440.00', '2026-11-12 16:13:57', '2026-11-12 16:13:57'),
(62, 37, 2, 2, '180.00', '360.00', '2026-11-12 16:13:57', '2026-11-12 16:13:57'),
(63, 38, 5, 1, '100.00', '100.00', '2026-11-12 16:43:50', '2026-11-12 16:43:50'),
(64, 39, 8, 1, '200.00', '200.00', '2026-11-12 17:25:31', '2026-11-12 17:25:31'),
(65, 39, 5, 1, '100.00', '100.00', '2026-11-12 17:25:31', '2026-11-12 17:25:31'),
(66, 40, 8, 8, '200.00', '1600.00', '2026-11-13 12:27:30', '2026-11-13 12:27:30'),
(67, 41, 3, 3, '180.00', '540.00', '2026-11-14 10:47:25', '2026-11-14 10:47:25'),
(68, 41, 5, 2, '1200.00', '2400.00', '2026-11-14 10:47:25', '2026-11-14 10:47:25'),
(69, 42, 6, 1, '1200.00', '1200.00', '2026-11-16 08:51:09', '2026-11-16 08:51:09'),
(70, 42, 1, 1, '1200.00', '1200.00', '2026-11-16 08:51:09', '2026-11-16 08:51:09'),
(71, 42, 2, 10, '1800.00', '18000.00', '2026-11-16 08:51:09', '2026-11-16 08:51:09'),
(72, 43, 3, 1, '1800.00', '1800.00', '2026-11-17 18:30:01', '2026-11-17 18:30:01'),
(73, 44, 2, 1, '1800.00', '1800.00', '2026-11-17 18:31:34', '2026-11-17 18:31:34'),
(74, 45, 1, 10, '1200.00', '12000.00', '2026-11-18 11:10:42', '2026-11-18 11:10:42'),
(75, 45, 5, 10, '1200.00', '12000.00', '2026-11-18 11:10:42', '2026-11-18 11:10:42'),
(76, 46, 8, 10, '2000.00', '20000.00', '2026-11-18 11:16:40', '2026-11-18 11:16:40'),
(77, 46, 5, 10, '1200.00', '12000.00', '2026-11-18 11:16:40', '2026-11-18 11:16:40');

-- --------------------------------------------------------

--
-- Table structure for table `order_stock_snapshots`
--

CREATE TABLE `order_stock_snapshots` (
  `snapshot_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL,
  `stock_before` int(11) NOT NULL,
  `stock_after` int(11) NOT NULL,
  `recorded_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_stock_snapshots`
--

INSERT INTO `order_stock_snapshots` (`snapshot_id`, `order_id`, `product_id`, `quantity_ordered`, `stock_before`, `stock_after`, `recorded_at`) VALUES
(1, 3, 5, 1, 500, 499, '2026-10-16 16:58:20'),
(2, 10, 2, 10, 140, 130, '2026-10-16 17:09:50'),
(3, 12, 5, 9, 499, 490, '2026-10-17 08:27:26'),
(4, 13, 2, 2, 130, 128, '2026-10-17 09:10:05'),
(5, 13, 5, 2, 490, 488, '2026-10-17 09:10:05'),
(6, 14, 3, 5, 300, 295, '2026-10-17 11:04:50'),
(7, 17, 2, 1, 128, 127, '2026-10-17 14:17:52'),
(8, 17, 5, 1, 488, 487, '2026-10-17 14:17:52'),
(9, 19, 2, 5, 230, 225, '2026-10-19 09:36:29'),
(10, 19, 3, 5, 295, 290, '2026-10-19 09:36:29'),
(11, 19, 8, 5, 600, 595, '2026-10-19 09:36:29'),
(12, 20, 1, 2, 210, 208, '2026-10-22 14:25:01'),
(13, 28, 2, 1, 225, 224, '2026-11-09 21:32:08'),
(14, 28, 5, 5, 487, 482, '2026-11-09 21:32:08'),
(15, 27, 2, 1, 224, 223, '2026-11-09 21:32:25'),
(16, 27, 3, 1, 290, 289, '2026-11-09 21:32:25'),
(17, 27, 5, 1, 482, 481, '2026-11-09 21:32:25'),
(18, 27, 6, 1, 200, 199, '2026-11-09 21:32:25'),
(19, 27, 8, 1, 595, 594, '2026-11-09 21:32:25'),
(20, 15, 5, 5, 481, 476, '2026-11-09 21:36:58'),
(21, 15, 6, 5, 199, 194, '2026-11-09 21:36:58'),
(22, 15, 8, 1, 594, 593, '2026-11-09 21:36:58'),
(23, 32, 2, 10, 223, 213, '2026-11-09 21:39:10'),
(24, 32, 3, 10, 289, 279, '2026-11-09 21:39:10'),
(25, 32, 5, 10, 476, 466, '2026-11-09 21:39:10'),
(26, 32, 8, 15, 593, 578, '2026-11-09 21:39:10'),
(27, 36, 5, 4, 466, 462, '2026-11-12 16:05:33'),
(28, 36, 8, 1, 578, 577, '2026-11-12 16:05:33'),
(29, 37, 2, 2, 213, 211, '2026-11-12 16:14:38'),
(30, 37, 3, 8, 279, 271, '2026-11-12 16:14:38'),
(31, 37, 8, 3, 577, 574, '2026-11-12 16:14:38'),
(32, 34, 5, 100, 462, 362, '2026-11-13 11:56:38'),
(33, 41, 3, 3, 271, 268, '2026-11-16 08:13:04'),
(34, 41, 5, 2, 362, 360, '2026-11-16 08:13:04'),
(35, 42, 1, 1, 210, 209, '2026-11-16 08:51:25'),
(36, 42, 2, 10, 211, 201, '2026-11-16 08:51:25'),
(37, 42, 6, 1, 194, 193, '2026-11-16 08:51:25'),
(38, 44, 2, 1, 201, 200, '2026-11-17 18:32:28'),
(39, 45, 1, 10, 209, 199, '2026-11-18 11:10:59'),
(40, 45, 5, 10, 362, 352, '2026-11-18 11:10:59');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(50) NOT NULL,
  `selling_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reorder_level` int(11) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(100) NOT NULL DEFAULT 'Uncategorized',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `unit`, `selling_price`, `reorder_level`, `status`, `created_at`, `updated_at`, `category`, `image`) VALUES
(1, 'Popsicle Stick', 'Finished Product', 'box', '1200.00', 20, 'Active', '2026-08-27 09:42:10', '2026-11-16 08:40:17', 'Food Accessories', 'shop/images/70414bd896170c0ab8144a9e02ebcdc7.webp'),
(2, 'Wooden Spoon', 'Finished Product', 'box', '1800.00', 15, 'Active', '2026-08-27 09:42:10', '2026-11-16 08:39:54', 'Wooden Utensils', 'shop/images/1e184938383525a3eba814c478c2679f.webp'),
(3, 'Wooden Fork', 'Finished Product', 'box', '1800.00', 15, 'Active', '2026-08-27 09:42:11', '2026-11-16 08:39:37', 'Wooden Utensils', 'shop/images/336ccebb0a3efc00881806196dac37c6.webp'),
(4, 'Spork', 'Finished Product', 'box', '200.00', 15, 'Inactive', '2026-08-27 09:42:11', '2026-08-27 10:02:17', 'Uncategorized', NULL),
(5, 'Coffee Stirrer', 'Finished Product', 'box', '1200.00', 50, 'Active', '2026-08-27 09:42:11', '2026-11-13 07:19:17', 'Wooden Sticks', 'shop/images/f2125bfdea70d4e4aff63f7ed5f9644f.jpg'),
(6, 'Ice Cream Spoon', 'Finished Product', 'box', '1200.00', 100, 'Active', '2026-08-27 09:42:11', '2026-11-13 07:19:46', 'Food Accessories', 'shop/images/d2980128540557651af5743a21517313.jpg'),
(7, 'Popsicle Stick', 'finished product', 'box', '100.00', 5, 'Inactive', '2026-08-28 22:23:01', '2026-10-15 18:26:55', 'Wooden Sticks', NULL),
(8, 'Spork', 'Finished Product', 'box', '2000.00', 10, 'Active', '2026-10-15 17:59:50', '2026-11-16 08:33:57', 'Wooden Utensils', 'shop/images/5a30309a64e5ab6314e39e65b57d10c5.jpg'),
(9, 'Ice Cream Spoon', 'finished product', 'box', '100.00', 20, 'Inactive', '2026-10-16 06:43:22', '2026-10-16 06:44:07', 'Wooden Utensils', 'shop/images/1b3a5f053c7707c8150f6bd1a72c702e.jpg'),
(10, 'S-Fork', 'Finished Product', 'box', '2000.00', 100, 'Inactive', '2026-11-16 08:26:34', '2026-11-16 08:45:12', 'Wooden Utensils', 'shop/images/55573eac9cbaea73ac09c3ecf2b55aa0.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `raw_material_id` int(11) NOT NULL,
  `material_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(50) NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reorder_level` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`raw_material_id`, `material_name`, `description`, `unit`, `quantity`, `reorder_level`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Falcata Wood', '', '100', '10.00', '3.00', 'Active', '2026-10-17 11:16:25', '2026-11-13 11:55:34'),
(2, 'falcata wood', 'new', '100', '5.00', '2.00', 'Active', '2026-10-23 14:04:08', '2026-11-09 23:25:07');

-- --------------------------------------------------------

--
-- Table structure for table `raw_material_transactions`
--

CREATE TABLE `raw_material_transactions` (
  `transaction_id` int(11) NOT NULL,
  `raw_material_id` int(11) NOT NULL,
  `transaction_type` enum('Stock In','Stock Out','Adjustment') NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `raw_material_transactions`
--

INSERT INTO `raw_material_transactions` (`transaction_id`, `raw_material_id`, `transaction_type`, `quantity`, `transaction_date`, `remarks`, `created_by`) VALUES
(1, 1, 'Stock In', '0.05', '2026-10-17 11:16:25', 'Initial stock', 1),
(2, 1, 'Adjustment', '10.00', '2026-10-20 10:35:39', 'wrong quantity', 1),
(3, 2, 'Stock In', '5.00', '2026-10-23 14:04:08', 'Initial stock', 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` enum('Owner','Bookkeeper','Office Staff') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `created_at`, `updated_at`) VALUES
(1, 'Owner', '2026-08-26 10:16:43', '2026-08-26 10:16:43'),
(2, 'Bookkeeper', '2026-08-26 10:16:43', '2026-08-26 10:16:43'),
(3, 'Office Staff', '2026-08-26 10:16:43', '2026-08-26 10:16:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `full_name`, `username`, `password`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Leah Administrator', 'admin', '$2y$10$ZuT0a1VYdnHuolpEOPdFAe0txhvVf9TFMZKSN0JVDc2V0am8ZmnpS', 'admin@leahspopstick.com', 'Active', '2026-08-26 10:23:27', '2026-08-26 10:23:27'),
(3, 2, 'Juan Dela Cruz', 'bookkeeper', '$2y$10$TrMw71Duh4SpAOMMOqgknODn0262w3kdmtYooCPYwYNkl30pY8dEO', 'bookkeeper@leah.com', 'Active', '2026-08-27 09:41:54', '2026-08-27 09:41:54'),
(4, 3, 'Maria Santos', 'staff', '$2y$10$hoMEVPNj4Uo44NupE0JPTOcZjOKvh3jLWRCcU/8sUYiETCk92ARQW', 'staff@leah.com', 'Active', '2026-08-27 09:41:57', '2026-08-27 09:41:57'),
(5, 3, 'Mark Hermie Bongales', 'mark', '$2y$10$zUGo.gbU4JOnUqLF7f0YL.8d4Ynd74IaIbmg4Zhtdf5pnnHI.SPpG', 'markhermie02@gmail.com', 'Inactive', '2026-10-07 18:00:36', '2026-10-29 09:52:30'),
(6, 3, 'Jeah Suico', 'jeah', '$2y$12$J95rMMpSp1KG5ws0.PtEbuDpl6TwtnRiEUIIAKDqXYAwTJgLJX10y', 'suicojeah@gmail.com', 'Active', '2026-10-19 09:40:47', '2026-10-19 09:40:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `fk_expenses_recorded_by` (`recorded_by`);

--
-- Indexes for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `fk_batches_product` (`product_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `fk_inventory_transaction_product` (`product_id`),
  ADD KEY `fk_inventory_transaction_batch` (`batch_id`),
  ADD KEY `fk_inventory_transaction_user` (`created_by`),
  ADD KEY `fk_inventory_transaction_order` (`reference_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_customer` (`customer_id`),
  ADD KEY `fk_orders_encoded_by` (`encoded_by`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `fk_order_details_order` (`order_id`),
  ADD KEY `fk_order_details_product` (`product_id`);

--
-- Indexes for table `order_stock_snapshots`
--
ALTER TABLE `order_stock_snapshots`
  ADD PRIMARY KEY (`snapshot_id`),
  ADD UNIQUE KEY `unique_order_product` (`order_id`,`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`raw_material_id`);

--
-- Indexes for table `raw_material_transactions`
--
ALTER TABLE `raw_material_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `raw_material_id` (`raw_material_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `order_stock_snapshots`
--
ALTER TABLE `order_stock_snapshots`
  MODIFY `snapshot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `raw_material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `raw_material_transactions`
--
ALTER TABLE `raw_material_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expenses_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  ADD CONSTRAINT `fk_batches_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `fk_inventory_transaction_batch` FOREIGN KEY (`batch_id`) REFERENCES `inventory_batches` (`batch_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_transaction_order` FOREIGN KEY (`reference_id`) REFERENCES `orders` (`order_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_transaction_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_transaction_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_encoded_by` FOREIGN KEY (`encoded_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_order_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_details_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON UPDATE CASCADE;

--
-- Constraints for table `raw_material_transactions`
--
ALTER TABLE `raw_material_transactions`
  ADD CONSTRAINT `raw_material_transactions_ibfk_1` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`raw_material_id`),
  ADD CONSTRAINT `raw_material_transactions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
