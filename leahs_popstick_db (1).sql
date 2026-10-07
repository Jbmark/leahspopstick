-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2026 at 09:18 AM
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
-- Database: `leahs_popstick_db`
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `contact_number`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'ABC Ice Drop Factory', '09123456789', NULL, NULL, '2026-08-28 23:15:22', '2026-08-28 23:15:22');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `expense_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_date` date NOT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(2, 1, 'B002', 150, 130, '2026-08-28 00:00:00', NULL, 'Available', '2026-08-28 22:33:59', '2026-08-28 23:33:49'),
(3, 1, 'WS001', 50, 50, '2026-08-28 00:00:00', NULL, 'Available', '2026-08-28 22:34:47', '2026-08-28 22:34:47'),
(4, 2, 'WS001', 50, 40, '2026-08-28 00:00:00', NULL, 'Available', '2026-08-28 22:35:13', '2026-08-28 23:33:49'),
(5, 2, 'B001', 20, 20, '2026-01-01 00:00:00', NULL, 'Available', '2026-08-30 09:42:13', '2026-08-30 09:49:54'),
(6, 2, 'B002', 30, 30, '2026-01-05 00:00:00', NULL, 'Available', '2026-08-30 09:43:16', '2026-08-30 09:49:54'),
(7, 2, 'B003', 50, 50, '2026-01-10 00:00:00', NULL, 'Available', '2026-08-30 09:44:01', '2026-08-30 09:44:01'),
(8, 6, 'B001', 100, 100, '2026-09-07 00:00:00', NULL, 'Available', '2026-10-07 13:15:41', '2026-10-07 13:21:34'),
(9, 6, 'B002', 50, 50, '2026-10-07 00:00:00', NULL, 'Available', '2026-10-07 13:16:26', '2026-10-07 13:21:34');

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
(20, 6, 8, 'Return', 100, 4, '2026-10-07 13:21:34', 'Inventory restored after order cancellation', 1);

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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `encoded_by`, `order_source`, `order_date`, `status`, `total_amount`, `completed_at`, `delivery_address`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'Messenger', '2026-08-28 23:25:36', 'Completed', 16200.00, '2026-08-28 23:33:50', 'Talacogon', 'FIFO engine defense simulation test.', '2026-08-28 23:25:36', '2026-08-28 23:33:50'),
(2, 1, 1, 'Messenger', '2026-08-30 09:46:42', 'Cancelled', 6300.00, NULL, 'Zamboanga City Warehouse Storage', '', '2026-08-30 09:46:42', '2026-08-30 09:49:54'),
(3, 1, NULL, 'Website', '2026-09-07 14:16:01', 'Pending', 100.00, NULL, 'san franz', ' [Web Guest Name: jj]', '2026-09-07 14:16:01', '2026-09-07 14:16:14'),
(4, 1, NULL, 'Website', '2026-10-07 13:18:33', 'Cancelled', 18000.00, NULL, '', '', '2026-10-07 13:18:33', '2026-10-07 13:21:34');

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
(1, 1, 1, 120, 120.00, 14400.00, '2026-08-28 23:25:38', '2026-08-28 23:25:38'),
(2, 1, 2, 10, 180.00, 1800.00, '2026-08-28 23:25:43', '2026-08-28 23:25:43'),
(3, 2, 2, 35, 180.00, 6300.00, '2026-08-30 09:46:43', '2026-08-30 09:46:43'),
(4, 3, 5, 1, 100.00, 100.00, '2026-09-07 14:16:13', '2026-09-07 14:16:13'),
(5, 4, 6, 120, 150.00, 18000.00, '2026-10-07 13:18:33', '2026-10-07 13:18:33');

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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `unit`, `selling_price`, `reorder_level`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Popsicle Stick', 'Finished Product', 'box', 120.00, 20, 'Active', '2026-08-27 09:42:10', '2026-08-27 09:42:10'),
(2, 'Wooden Spoon', 'Finished Product', 'box', 180.00, 15, 'Active', '2026-08-27 09:42:10', '2026-08-27 09:42:10'),
(3, 'Wooden Fork', 'Finished Product', 'box', 180.00, 15, 'Active', '2026-08-27 09:42:11', '2026-08-27 09:42:11'),
(4, 'Spork', 'Finished Product', 'box', 200.00, 15, 'Inactive', '2026-08-27 09:42:11', '2026-08-27 10:02:17'),
(5, 'Coffee Stirrer', 'Finished Product', 'box', 100.00, 20, 'Active', '2026-08-27 09:42:11', '2026-08-27 09:42:11'),
(6, 'Ice Cream Spoon', 'Finished Product', 'box', 150.00, 20, 'Active', '2026-08-27 09:42:11', '2026-08-27 09:42:11'),
(7, 'Popsicle Stick', 'finished product', 'box', 100.00, 5, 'Active', '2026-08-28 22:23:01', '2026-08-28 22:23:01');

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
(4, 3, 'Maria Santos', 'staff', '$2y$10$hoMEVPNj4Uo44NupE0JPTOcZjOKvh3jLWRCcU/8sUYiETCk92ARQW', 'staff@leah.com', 'Active', '2026-08-27 09:41:57', '2026-08-27 09:41:57');

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
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_batches`
--
ALTER TABLE `inventory_batches`
  MODIFY `batch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `raw_material_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `raw_material_transactions`
--
ALTER TABLE `raw_material_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
