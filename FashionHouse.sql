-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 24, 2025 at 05:49 AM
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
-- Database: `FashionHouse`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Men', 'Mens clothing and accessories', '2025-11-18 17:41:45'),
(2, 'Women', 'Womens clothing and accessories', '2025-11-18 17:41:45');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_status` varchar(50) DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Address` varchar(255) NOT NULL,
  `Payment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_status`, `total_amount`, `order_date`, `updated_at`, `Address`, `Payment`) VALUES
(1, 4, 'completed', 56.69, '2025-11-23 08:23:01', '2025-11-23 08:23:01', '', ''),
(2, 2, 'pending', 79.99, '2025-11-18 17:58:43', '2025-11-18 17:58:43', '', ''),
(5, 4, 'pending', 20.99, '2025-11-23 15:28:06', '2025-11-23 15:28:06', 'sjdskdj@gmail.com', 'paypal'),
(6, 4, 'pending', 59.99, '2025-11-23 16:25:44', '2025-11-23 16:25:44', 'djdkjfdkd', 'card'),
(7, 4, 'pending', 83.96, '2025-11-23 16:26:51', '2025-11-23 16:26:51', 'djdkfjdkfjdkf', 'card'),
(8, 4, 'pending', 19.99, '2025-11-24 00:26:29', '2025-11-24 00:26:29', 'sjdkjskdsjds', 'card');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `selected_variations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_variations`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `selected_variations`, `created_at`) VALUES
(4, 5, 13, 1, 20.99, '[]', '2025-11-23 15:28:06'),
(5, 6, 6, 1, 59.99, '[]', '2025-11-23 16:25:44'),
(6, 7, 13, 4, 20.99, '[]', '2025-11-23 16:26:51'),
(7, 8, 12, 1, 19.99, '[]', '2025-11-24 00:26:29');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `aesthetic` varchar(100) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `description`, `price`, `aesthetic`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 'Blue Cotton T-Shirt', 'Comfortable casual t-shirt', 29.99, 'casual', 50, '2025-11-18 17:57:28', '2025-11-18 17:57:28'),
(3, 2, 'Red Summer Dress', 'Light and breezy summer dress', 49.99, 'elegant', 25, '2025-11-18 17:57:28', '2025-11-18 17:57:28'),
(6, 2, 'Black Jeans', 'Slim fit denim jeans', 59.99, 'casual', 30, '2025-11-18 17:57:52', '2025-11-18 18:28:48'),
(12, 1, 'Black Summer Shorts', 'Comfortable brezzy summer shorts', 19.99, 'casual', 50, '2025-11-18 18:50:38', '2025-11-18 18:50:38'),
(13, 1, 'Black Work Pants', 'Slim fit work pants', 20.99, 'casual', 30, '2025-11-18 18:50:38', '2025-11-18 19:04:55'),
(14, 2, 'Printed Cotton Top', 'Light and breezy summer top', 10.99, 'casual', 25, '2025-11-18 18:50:38', '2025-11-18 18:50:38');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`, `alt_text`, `is_primary`, `display_order`, `created_at`) VALUES
(1, 1, './images/products/men/tops/blueshirt-front.webp', 'Blue T-Shirt Front View', 1, 1, '2025-11-18 17:58:06'),
(2, 1, './images/products/men/tops/blueshirt-back.webp', 'Blue T-Shirt Back View', 0, 2, '2025-11-18 17:58:06'),
(5, 3, './images/products/women/dresses/reddress-front.webp', 'Red Dress Full View', 1, 1, '2025-11-18 17:58:06'),
(6, 3, './images/products/women/dresses/reddress-back.webp', 'Red Dress Close Up', 0, 2, '2025-11-18 17:58:06'),
(7, 6, './images/products/women/bottoms/blackjeans-back.webp', 'Black Jeans Front', 1, 1, '2025-11-18 18:33:22'),
(8, 6, './images/products/women/bottoms/blackjeans-back.webp', 'Black Jeans Back', 0, 2, '2025-11-18 18:33:22'),
(9, 12, './images/products/men/bottoms/summershorts-front.webp', 'Black Summer Shorts Front View', 1, 1, '2025-11-18 18:51:39'),
(10, 13, './images/products/men/bottoms/workpants-front.webp', 'Black Work Pants Front View', 1, 1, '2025-11-18 18:51:39'),
(11, 13, './images/products/men/bottoms/workpants-back.webp.jpg', 'Black Work Pants Back View', 0, 2, '2025-11-18 18:51:39'),
(12, 14, 'images/products/women/tops/cottontop-front.webp', 'Printed Cotton Top Front View', 1, 1, '2025-11-18 18:51:39'),
(13, 14, 'images/products/women/tops/cottontop-back.webp', 'Printed Cotton Top Back View', 0, 2, '2025-11-18 18:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `name`, `rating`, `review`, `created_at`) VALUES
(1, 12, 'nmnmn', 5, 'm,m,', '2025-11-23 15:08:53'),
(2, 12, 'dsjdksjd', 5, 'smkdmsdk', '2025-11-23 15:13:03');

-- --------------------------------------------------------

--
-- Table structure for table `product_variations`
--

CREATE TABLE `product_variations` (
  `variation_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_type` varchar(50) NOT NULL,
  `variation_value` varchar(100) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variations`
--

INSERT INTO `product_variations` (`variation_id`, `product_id`, `variation_type`, `variation_value`, `stock_quantity`, `created_at`) VALUES
(1, 1, 'size', 'S', 15, '2025-11-18 17:58:23'),
(2, 1, 'size', 'M', 20, '2025-11-18 17:58:23'),
(3, 1, 'size', 'L', 15, '2025-11-18 17:58:23');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `selected_variations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_variations`)),
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shopping_cart`
--

INSERT INTO `shopping_cart` (`cart_id`, `user_id`, `product_id`, `quantity`, `selected_variations`, `added_at`, `updated_at`) VALUES
(23, 4, 12, 1, '[]', '2025-11-23 14:03:43', '2025-11-23 14:03:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` int(9) DEFAULT NULL,
  `dateofbirth` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `first_name`, `last_name`, `phone`, `dateofbirth`, `created_at`, `updated_at`) VALUES
(2, 'john@example.com', 'hashed_password_123', 'John', 'Doe', 555123412, NULL, '2025-11-18 17:43:16', '2025-11-18 17:43:16'),
(3, 'sarah@example.com', 'hashed_password_456', 'Sarah', 'Smith', 555567812, NULL, '2025-11-18 17:43:16', '2025-11-18 17:43:16'),
(4, 'test@example.com', '$2y$10$qPhkA2IONit.X6nRKUis2ugA4zPy/l14HUIDjvho34Qy8hbr6Ot9q', 'Test1', 'User', 16276273, '2000-11-05', '2025-11-23 08:10:14', '2025-11-23 08:16:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_aesthetic` (`aesthetic`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `idx_product_images` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`variation_id`),
  ADD UNIQUE KEY `unique_variation` (`product_id`,`variation_type`,`variation_value`),
  ADD KEY `idx_product_variations_product` (`product_id`);

--
-- Indexes for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_cart_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_variations`
--
ALTER TABLE `product_variations`
  MODIFY `variation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD CONSTRAINT `product_variations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shopping_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
