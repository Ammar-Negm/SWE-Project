-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 02:38 PM
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
-- Database: `hameed_warehouse`
--

-- --------------------------------------------------------

--
-- Table structure for table `bin`
--

CREATE TABLE `bin` (
  `bin_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `currentWeight` float DEFAULT 0,
  `maxWeight` float NOT NULL,
  `shelfLocation` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bin`
--

INSERT INTO `bin` (`bin_id`, `zone_id`, `currentWeight`, `maxWeight`, `shelfLocation`) VALUES
(1, 1, 0, 500, 'A1'),
(2, 2, 0, 500, 'A1');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `loyalty_points` int(11) DEFAULT 0,
  `total_orders_placed` int(11) DEFAULT 0,
  `client_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`client_id`, `name`, `email`, `password`, `shipping_address`, `loyalty_points`, `total_orders_placed`, `client_type`) VALUES
(1, 'Ahmed', 'ahmedsaleem12345@gmail.com', '12345', 'N/A', 5, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `floorstaff`
--

CREATE TABLE `floorstaff` (
  `staff_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `shift_start` time NOT NULL,
  `shift_end` time NOT NULL,
  `productivity_score` float NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `floorstaff`
--

INSERT INTO `floorstaff` (`staff_id`, `name`, `email`, `password`, `shift_start`, `shift_end`, `productivity_score`, `user_id`) VALUES
(1, 'zizo', 'staff_1777825586@hameed.com', '123456', '09:00:00', '17:00:00', 98.2, NULL),
(4, 'heeed', 'ahmedhammmeed1212@gmail.com', 'FWC1nF1.)%', '08:00:00', '14:00:00', 1.6, NULL),
(6, 'pipo', 'pipo@gmail.com', '222', '08:00:00', '14:00:00', 1.3, NULL),
(7, 'Acdemy', 'ahmedhammmeed1212@gmail.com', '$2y$10$iCR7R/Bo49j3zzcgQ732F.skHeLsBTS2SB6KCITQ5qPMAstE2axpa', '00:00:00', '00:00:00', 0, NULL),
(8, 'tester', 'test@test.com', '123', '08:00:00', '16:00:00', 90, NULL),
(9, 'staff', 's@s.com', '123', '08:00:00', '16:00:00', 90, NULL),
(10, 'Ahmed', 'ahmed@test.com', '$2y$10$5FF5ZRva1AXA/twIKUVU1.gmijcRHJaSQV/2LqdMkGIh.MqYALZHS', '08:00:00', '16:00:00', 90, NULL),
(11, 'Ahmed', 'ahmed@test.com', '$2y$10$Sr1k/dIU1SN9/NxQFtsIW.OO8Nz.S3qeMwDhzW/SbjWVvswyRQNTW', '08:00:00', '16:00:00', 90, NULL),
(12, 'Ahmed', 'ahmed@test.com', '$2y$10$uxNVbeDrkv18Y7hOfvARC.fZkDv/BpuOqTjk9OtzpZZ8v2ouuy0a6', '08:00:00', '16:00:00', 90, NULL),
(13, 'Ahmd Selim', 'ahmedsaleem12345@gmail.com', '12345', '08:00:00', '16:00:00', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_audit_log`
--

CREATE TABLE `inventory_audit_log` (
  `log_id` int(11) NOT NULL,
  `inv_item_id` int(11) NOT NULL,
  `action_type` enum('SUPPLY','PICKING') NOT NULL,
  `change_amount` int(11) NOT NULL,
  `performer_id` int(11) NOT NULL,
  `performer_role` enum('supplier','staff') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_audit_log`
--

INSERT INTO `inventory_audit_log` (`log_id`, `inv_item_id`, `action_type`, `change_amount`, `performer_id`, `performer_role`, `reference_id`, `quantity_before`, `quantity_after`, `created_at`) VALUES
(0, 1, 'PICKING', -5, 1, 'staff', 11, 85, 80, '2026-05-09 13:58:35'),
(1, 1, 'SUPPLY', 50, 1, 'supplier', 101, 135, 185, '2026-05-07 19:27:35'),
(2, 1, 'PICKING', -10, 1, 'staff', 501, 125, 115, '2026-05-07 19:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_item`
--

CREATE TABLE `inventory_item` (
  `inv_item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `bin_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `status` enum('Available','Reserved','Picked','Damaged') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_item`
--

INSERT INTO `inventory_item` (`inv_item_id`, `product_id`, `bin_id`, `quantity`, `status`) VALUES
(1, 1, 1, 85, 'Available'),
(5, 1, 1, 100, 'Available'),
(6, 1, 1, 100, 'Available'),
(7, 5, 1, 7, 'Available'),
(8, 6, 2, 70, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `pdf_path` varchar(255) DEFAULT NULL,
  `match_status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `total_weight` float DEFAULT 0,
  `total_cost` decimal(10,2) DEFAULT 0.00,
  `client_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `date`, `status`, `total_weight`, `total_cost`, `client_id`) VALUES
(1, '2026-05-09 14:21:02', 'Pending', 0, 0.00, 1),
(4, '2026-05-08 00:48:06', 'Pending', 1, 65.00, 1),
(5, '2026-05-08 00:49:10', 'Pending', 4, 40.00, 1),
(6, '2026-05-08 00:49:31', 'Pending', 4, 40.00, 1),
(7, '2026-05-08 00:49:34', 'Pending', 4, 40.00, 1),
(8, '2026-05-08 00:56:03', 'Pending', 0, 0.00, 1),
(9, '2026-05-08 01:00:54', 'Pending', 5, 250.00, 1),
(10, '2026-05-09 14:29:08', 'Pending', 2, 138.00, 1),
(11, '2026-05-09 14:29:17', 'Pending', 4, 260.00, 1),
(12, '2026-05-09 14:29:53', 'Pending', 4, 260.00, 1),
(13, '2026-05-09 14:33:47', 'Pending', 4, 260.00, 1),
(14, '2026-05-09 14:33:52', 'Pending', 5, 50.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `packing_material`
--

CREATE TABLE `packing_material` (
  `material_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `max_weight` decimal(10,2) NOT NULL,
  `max_volume` decimal(10,2) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `picklist_order`
--

CREATE TABLE `picklist_order` (
  `pick_list_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `picklist_order`
--

INSERT INTO `picklist_order` (`pick_list_id`, `order_id`) VALUES
(1, 10),
(6, 4),
(7, 5),
(8, 6),
(9, 7),
(10, 8),
(11, 9),
(12, 13),
(13, 14);

-- --------------------------------------------------------

--
-- Table structure for table `pick_list`
--

CREATE TABLE `pick_list` (
  `pick_list_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Open',
  `optimized_route` text DEFAULT NULL,
  `assigned_staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pick_list`
--

INSERT INTO `pick_list` (`pick_list_id`, `created_at`, `status`, `optimized_route`, `assigned_staff_id`) VALUES
(1, '2026-05-09 14:29:08', 'Open', NULL, NULL),
(3, '2026-05-04 02:17:57', 'Open', NULL, 1),
(4, '2026-05-04 02:19:56', 'Open', NULL, 1),
(5, '2026-05-04 19:02:06', 'Open', NULL, 1),
(6, '2026-05-08 00:48:06', 'Open', NULL, NULL),
(7, '2026-05-08 00:49:10', 'Open', NULL, NULL),
(8, '2026-05-08 00:49:31', 'Open', NULL, NULL),
(9, '2026-05-08 00:49:34', 'Open', NULL, NULL),
(10, '2026-05-08 00:56:03', 'Open', NULL, NULL),
(11, '2026-05-08 01:00:54', 'Open', NULL, 1),
(12, '2026-05-09 14:33:47', 'Open', NULL, NULL),
(13, '2026-05-09 14:33:52', 'Open', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pick_task`
--

CREATE TABLE `pick_task` (
  `picktask_id` int(11) NOT NULL,
  `pick_list_id` int(11) NOT NULL,
  `quantity_to_pick` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `inv_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pick_task`
--

INSERT INTO `pick_task` (`picktask_id`, `pick_list_id`, `quantity_to_pick`, `status`, `inv_item_id`) VALUES
(4, 4, 5, 'Picked', 1),
(5, 5, 5, 'Picked', 1),
(11, 11, 5, 'Picked', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `SKU` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `basePrice` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `minStockLevel` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `SKU`, `name`, `basePrice`, `category`, `minStockLevel`) VALUES
(1, 'TESTSKU', 'TestProduct', 50.00, NULL, 0),
(2, 'T1', 'Test', 10.00, NULL, 0),
(5, 'T5', 'low', 61.00, 'Lowstck', 10),
(6, 'SKU-1', 'Test999', 9.00, 'Electro', 20);

-- --------------------------------------------------------

--
-- Table structure for table `purchaseorder`
--

CREATE TABLE `purchaseorder` (
  `po_id` int(11) NOT NULL,
  `status` enum('pending','shipped','delivered','cancelled') DEFAULT 'pending',
  `total_cost` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `expected_delivery_date` datetime DEFAULT NULL,
  `supplier_id` int(11) NOT NULL,
  `po_number` varchar(50) DEFAULT NULL,
  `total_value` decimal(10,2) DEFAULT 0.00,
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchaseorder`
--

INSERT INTO `purchaseorder` (`po_id`, `status`, `total_cost`, `created_at`, `expected_delivery_date`, `supplier_id`, `po_number`, `total_value`, `order_date`) VALUES
(1, 'pending', 0.00, '2026-05-09 15:33:12', '2026-05-09 00:00:00', 1, 'PO-00001', 50.00, '2026-05-09 15:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipment`
--

CREATE TABLE `shipment` (
  `shipment_id` int(11) NOT NULL,
  `status` enum('Expected','AtDock','BeingInspected','Stored') DEFAULT 'Expected',
  `items_received` int(11) DEFAULT 0,
  `po_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_label`
--

CREATE TABLE `shipping_label` (
  `label_id` int(11) NOT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `generated_at` datetime DEFAULT current_timestamp(),
  `tracking_number` varchar(100) DEFAULT NULL,
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_shift_record`
--

CREATE TABLE `staff_shift_record` (
  `shift_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `logout_time` datetime DEFAULT NULL,
  `hours_worked` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `perf_score` float DEFAULT 0,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `name`, `email`, `password`, `perf_score`, `user_id`) VALUES
(1, 'Ahmd Selim', 'ahmedsaleem12345@gmail.com', '12345', 100, NULL),
(3, 'Selim Ltd', 'ahmedsaleem@gmail.com', '', 0, NULL),
(1, 'Ahmd Selim', 'ahmedsaleem12345@gmail.com', '12345', 100, NULL),
(3, 'Selim Ltd', 'ahmedsaleem@gmail.com', '', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `userType` enum('staff','supplier','manager') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_config`
--

CREATE TABLE `warehouse_config` (
  `config_key` varchar(100) NOT NULL,
  `config_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_manager`
--

CREATE TABLE `warehouse_manager` (
  `manager_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warehouse_manager`
--

INSERT INTO `warehouse_manager` (`manager_id`, `name`, `email`, `password`) VALUES
(1, 'heeed', 'ahmedhammmeed1212@gmail.com', '1234'),
(1, 'heeed', 'ahmedhammmeed1212@gmail.com', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `zone_id` int(11) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `max_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `zone`
--

INSERT INTO `zone` (`zone_id`, `zone_name`, `max_capacity`) VALUES
(1, 'TestZone', 100),
(2, 'Z', 100),
(1, 'TestZone', 100),
(2, 'Z', 100);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bin`
--
ALTER TABLE `bin`
  ADD PRIMARY KEY (`bin_id`),
  ADD KEY `fk_bin_zone` (`zone_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `floorstaff`
--
ALTER TABLE `floorstaff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `inventory_audit_log`
--
ALTER TABLE `inventory_audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_audit_item_ref` (`inv_item_id`);

--
-- Indexes for table `inventory_item`
--
ALTER TABLE `inventory_item`
  ADD PRIMARY KEY (`inv_item_id`),
  ADD KEY `fk_inv_product` (`product_id`),
  ADD KEY `fk_inv_bin` (`bin_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_order_client` (`client_id`);

--
-- Indexes for table `packing_material`
--
ALTER TABLE `packing_material`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `picklist_order`
--
ALTER TABLE `picklist_order`
  ADD PRIMARY KEY (`pick_list_id`,`order_id`),
  ADD KEY `fk_plo_order` (`order_id`);

--
-- Indexes for table `pick_list`
--
ALTER TABLE `pick_list`
  ADD PRIMARY KEY (`pick_list_id`),
  ADD KEY `fk_picklist_staff` (`assigned_staff_id`);

--
-- Indexes for table `pick_task`
--
ALTER TABLE `pick_task`
  ADD PRIMARY KEY (`picktask_id`),
  ADD KEY `fk_task_inventory` (`inv_item_id`),
  ADD KEY `fk_task_picklist` (`pick_list_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `purchaseorder`
--
ALTER TABLE `purchaseorder`
  ADD PRIMARY KEY (`po_id`);

--
-- Indexes for table `warehouse_config`
--
ALTER TABLE `warehouse_config`
  ADD PRIMARY KEY (`config_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory_item`
--
ALTER TABLE `inventory_item`
  MODIFY `inv_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `packing_material`
--
ALTER TABLE `packing_material`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `picklist_order`
--
ALTER TABLE `picklist_order`
  MODIFY `pick_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pick_list`
--
ALTER TABLE `pick_list`
  MODIFY `pick_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pick_task`
--
ALTER TABLE `pick_task`
  MODIFY `picktask_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchaseorder`
--
ALTER TABLE `purchaseorder`
  MODIFY `po_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
