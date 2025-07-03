-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2025 at 03:00 PM
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
-- Database: `shop_mam`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `rating` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `image`, `price`, `rating`) VALUES
(1, 'Gaming Mouse', 'High precision RGB gaming mouse with 6 programmable buttons.', 'gaming_mouse.jpg', 1500.00, 8),
(2, 'Mechanical Keyboard', 'RGB mechanical keyboard with blue switches for fast typing.', 'mech_keyboard.jpg', 3500.00, 10),
(3, 'Gaming Headset', 'Surround sound gaming headset with noise-canceling microphone.', 'gaming_headset.jpg', 1800.00, 6),
(4, 'Gaming Chair', 'Ergonomic gaming chair with adjustable lumbar support.', 'gaming_chair.jpg', 5000.00, 8),
(5, 'Mouse Pad', 'Large anti-slip mouse pad for gaming.', 'mouse_pad.jpg', 300.00, 4),
(6, 'USB Hub', '4-port USB 3.0 hub for your peripherals.', 'usb_hub.jpg', 450.00, 2),
(7, 'Webcam', '1080p HD webcam with built-in microphone.', 'webcam.jpg', 1200.00, 6),
(8, 'External Hard Drive', '1TB USB 3.0 external hard drive.', 'hard_drive.jpg', 3500.00, 7),
(9, 'Microphone', 'USB condenser microphone for streaming.', 'microphone.jpg', 2500.00, 9),
(10, 'Speakers', '2.1 channel speakers with deep bass.', 'speaker.jpg', 2000.00, 5),
(11, 'Monitor 24-inch', '24-inch Full HD monitor with 75Hz refresh rate.', '24_inch.jpg', 7000.00, 10),
(12, 'Graphics Tablet', 'Drawing tablet with pressure sensitivity.', 'graphics_tablet.jpg', 4500.00, 6),
(13, 'VR Headset', 'Virtual reality headset for immersive gaming.', 'vr_headset.jpg', 15000.00, 8),
(14, 'Cooling Pad', 'Laptop cooling pad with adjustable fan speed.', 'cooling_pad.jpg', 600.00, 3),
(15, 'Cable Organizer', 'Magnetic cable organizer for your desk.', 'cable_organizer.jpg', 250.00, 1),
(16, 'Surge Protector', '6-outlet surge protector with USB charging ports.', 'surge_protector.jpg', 950.00, 4),
(17, 'Game Controller', 'Wireless game controller compatible with PC.', 'game_controller.jpg', 1800.00, 7),
(18, 'LED Strip', 'RGB LED strip lights for your setup.', 'led_strip.jpg', 350.00, 2),
(19, 'Router', 'Dual-band WiFi router for stable connections.', 'router.jpg', 2500.00, 5),
(20, 'Headphone Stand', 'Aluminum headphone stand with cable management.', 'headphone_stand.jpg', 700.00, 3);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `created_at`) VALUES
(1, 1, '2025-07-02 11:53:27'),
(2, 1, '2025-07-02 11:54:13'),
(3, 1, '2025-07-02 11:54:20'),
(4, 1, '2025-07-02 12:21:42'),
(5, 1, '2025-07-02 12:34:31'),
(6, 1, '2025-07-02 12:42:10'),
(7, 1, '2025-07-02 12:55:23');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `quantity`) VALUES
(1, 1, 14, 1),
(2, 1, 11, 5),
(3, 2, 2, 1),
(4, 4, 11, 1),
(5, 5, 20, 1),
(6, 5, 1, 1),
(7, 6, 11, 7),
(8, 7, 8, 1),
(9, 7, 9, 1),
(10, 7, 19, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `province` varchar(100) NOT NULL,
  `zipcode` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gmail` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `dob`, `address`, `street`, `province`, `zipcode`, `country`, `phone`, `gmail`, `username`, `password`) VALUES
(1, 'Lebron James', '2001-12-12', '12 London, Bridge', '12 London', 'Metro Manila', '1002', 'PH', '09432617283', 'lebronjames@gmial.com', 'lebron', '$2y$10$ZxBtiQWl.lRrD3.TEgOKUeSjIF1/XWnMd5QZKfp5RP8drZO.flMpq');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
