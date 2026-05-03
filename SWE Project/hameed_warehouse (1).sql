-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 08:19 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `client_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `shipping_address` text NOT NULL,
  `loyalty_points` int(11) DEFAULT 0,
  `total_orders_placed` int(11) DEFAULT 0,
  `client_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `floorstaff`
--

CREATE TABLE `floorstaff` (
  `staff_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(10) NOT NULL,
  `shift_start` time NOT NULL,
  `shift_end` time NOT NULL,
  `productivity_score` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `floorstaff`
--

INSERT INTO `floorstaff` (`staff_id`, `name`, `email`, `password`, `shift_start`, `shift_end`, `productivity_score`) VALUES
(1, 'fifo', '', '', '00:00:00', '00:00:00', 0),
(2, 'zizo', 'staff_1777825586@hameed.com', '123456', '09:00:00', '17:00:00', 98.2),
(4, 'heeed', 'ahmedhammmeed1212@gmail.com', 'FWC1nF1.)%', '08:00:00', '14:00:00', 1.6),
(6, 'pipo', 'pipo@gmail.com', '222', '08:00:00', '14:00:00', 1.3);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_item`
--

CREATE TABLE `inventory_item` (
  `inv_item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `bin_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
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

-- --------------------------------------------------------

--
-- Table structure for table `picklist_order`
--

CREATE TABLE `picklist_order` (
  `pick_list_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `perf_score` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `floorstaff`
--
ALTER TABLE `floorstaff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `inventory_item`
--
ALTER TABLE `inventory_item`
  ADD PRIMARY KEY (`inv_item_id`),
  ADD KEY `fk_inv_product` (`product_id`),
  ADD KEY `fk_inv_bin` (`bin_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_order_client` (`client_id`);

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
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `SKU` (`SKU`);

--
-- Indexes for table `shipping_label`
--
ALTER TABLE `shipping_label`
  ADD PRIMARY KEY (`label_id`),
  ADD KEY `fk_label_order` (`order_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `warehouse_manager`
--
ALTER TABLE `warehouse_manager`
  ADD PRIMARY KEY (`manager_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`zone_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bin`
--
ALTER TABLE `bin`
  MODIFY `bin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floorstaff`
--
ALTER TABLE `floorstaff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inventory_item`
--
ALTER TABLE `inventory_item`
  MODIFY `inv_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pick_list`
--
ALTER TABLE `pick_list`
  MODIFY `pick_list_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pick_task`
--
ALTER TABLE `pick_task`
  MODIFY `picktask_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipping_label`
--
ALTER TABLE `shipping_label`
  MODIFY `label_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse_manager`
--
ALTER TABLE `warehouse_manager`
  MODIFY `manager_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `zone`
--
ALTER TABLE `zone`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bin`
--
ALTER TABLE `bin`
  ADD CONSTRAINT `fk_bin_zone` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`zone_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventory_item`
--
ALTER TABLE `inventory_item`
  ADD CONSTRAINT `fk_inv_bin` FOREIGN KEY (`bin_id`) REFERENCES `bin` (`bin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inv_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_client` FOREIGN KEY (`client_id`) REFERENCES `client` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `picklist_order`
--
ALTER TABLE `picklist_order`
  ADD CONSTRAINT `fk_plo_order` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_plo_picklist` FOREIGN KEY (`pick_list_id`) REFERENCES `pick_list` (`pick_list_id`) ON DELETE CASCADE;

--
-- Constraints for table `pick_list`
--
ALTER TABLE `pick_list`
  ADD CONSTRAINT `fk_picklist_staff` FOREIGN KEY (`assigned_staff_id`) REFERENCES `floorstaff` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pick_task`
--
ALTER TABLE `pick_task`
  ADD CONSTRAINT `fk_task_inventory` FOREIGN KEY (`inv_item_id`) REFERENCES `inventory_item` (`inv_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_task_picklist` FOREIGN KEY (`pick_list_id`) REFERENCES `pick_list` (`pick_list_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shipping_label`
--
ALTER TABLE `shipping_label`
  ADD CONSTRAINT `fk_label_order` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
