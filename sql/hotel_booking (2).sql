-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 11:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_booking`
--
CREATE DATABASE IF NOT EXISTS `hotel_booking` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hotel_booking`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `guests` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'pending',
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `full_name`, `email`, `phone`, `room_id`, `check_in`, `check_out`, `guests`, `payment_method`, `payment_status`, `status`, `created_at`) VALUES
(1, 1, 'phuong uyen', 'uyen@gmail.com', '0548334672', 1, '2025-08-15', '2025-08-16', 1, 'bank', 'pending', 'pending', '2025-08-14 20:15:20'),
(2, 1, 'phuong uyen', 'uyen@gmail.com', '0548334672', 1, '2025-08-16', '2025-08-19', 1, 'bank', 'pending', 'pending', '2025-08-14 20:40:29');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

DROP TABLE IF EXISTS `hotels`;
CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `address`, `description`, `created_at`, `price`, `image`) VALUES
(1, 'Vinpearl Resort Nha Trang', 'Hon Tre Island, Nha Trang, Khanh Hoa', 'Luxury island resort with private beach, water park and golf course.', '2025-08-14 11:15:16', 3500000.00, 'hotel1.jpg'),
(2, 'Ana Mandara Villas Dalat', 'Le Lai Street, Ward 5, Da Lat, Lam Dong', 'French colonial villa resort nestled in the pine hills of Dalat.', '2025-08-14 11:15:16', 2800000.00, 'hotel2.jpg'),
(3, 'Pullman Danang Beach Resort', 'Vo Nguyen Giap Street, Khue My Ward, Ngu Hanh Son, Da Nang', '5-star beachfront resort with world-class spa and multiple dining venues.', '2025-08-14 11:15:16', 4200000.00, 'hotel3.jpg'),
(4, 'Imperial Hotel Vung Tau', '159 Thuy Van Street, Vung Tau', 'Beachfront hotel with ocean views and convenient access to Vung Tau attractions.', '2025-08-14 11:15:16', 1800000.00, 'hotel4.jpg'),
(5, 'Anantara Hoi An Resort', '1 Pham Hong Thai Street, Hoi An, Quang Nam', 'Luxury riverside resort in UNESCO World Heritage town with traditional architecture.', '2025-08-14 11:15:16', 5200000.00, 'hotel5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_images`
--

DROP TABLE IF EXISTS `hotel_images`;
CREATE TABLE `hotel_images` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_images`
--

INSERT INTO `hotel_images` (`id`, `hotel_id`, `image_path`, `uploaded_at`) VALUES
(1, 1, 'hotel1.jpg', '2025-08-14 11:29:47'),
(2, 2, 'hotel2.jpg', '2025-08-14 11:29:47'),
(3, 3, 'hotel3.jpg', '2025-08-14 11:29:47'),
(4, 4, 'hotel4.jpg', '2025-08-14 11:29:47'),
(5, 5, 'hotel5.jpg', '2025-08-14 11:29:47');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hotel_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `room_id`, `image_path`, `uploaded_at`, `hotel_name`) VALUES
(17, 1, 'vinpearl_suite101_1.jpg', '2025-08-14 11:28:20', 'Vinpearl Resort Nha Trang'),
(18, 2, 'vinpearl_deluxe102_1.jpg', '2025-08-14 11:28:20', 'Vinpearl Resort Nha Trang'),
(19, 3, 'vinpearl_standard103_1.jpg', '2025-08-14 11:28:20', 'Vinpearl Resort Nha Trang'),
(20, 4, 'ana_suite201_1.jpg', '2025-08-14 11:28:20', 'Ana Mandara Villas Dalat'),
(21, 5, 'ana_deluxe202_1.jpg', '2025-08-14 11:28:20', 'Ana Mandara Villas Dalat'),
(22, 6, 'ana_standard203_1.jpg', '2025-08-14 11:28:20', 'Ana Mandara Villas Dalat'),
(23, 7, 'pullman_suite301_1.jpg', '2025-08-14 11:28:20', 'Pullman Danang Beach Resort'),
(24, 8, 'pullman_deluxe302_1.jpg', '2025-08-14 11:28:20', 'Pullman Danang Beach Resort'),
(25, 9, 'pullman_standard303_1.jpg', '2025-08-14 11:28:20', 'Pullman Danang Beach Resort'),
(26, 10, 'imperial_suite401_1.jpg', '2025-08-14 11:28:20', 'Imperial Hotel Vung Tau'),
(27, 11, 'imperial_deluxe402_1.jpg', '2025-08-14 11:28:20', 'Imperial Hotel Vung Tau'),
(28, 12, 'imperial_standard403_1.jpg', '2025-08-14 11:28:20', 'Imperial Hotel Vung Tau'),
(29, 13, 'anantara_suite501_1.jpg', '2025-08-14 11:28:20', 'Anantara Hoi An Resort'),
(30, 14, 'anantara_deluxe502_1.jpg', '2025-08-14 11:28:20', 'Anantara Hoi An Resort'),
(31, 15, 'anantara_standard503_1.jpg', '2025-08-14 11:28:20', 'Anantara Hoi An Resort');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('available','booked') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `hotel_id`, `room_number`, `type`, `price`, `description`, `status`) VALUES
(1, 1, '101', 'Suite', 5000000.00, 'Luxury suite with ocean view, king-size bed, and private balcony.', 'available'),
(2, 1, '102', 'Deluxe', 3500000.00, 'Deluxe room with partial sea view, modern furniture, and mini-bar.', 'available'),
(3, 1, '103', 'Standard', 2500000.00, 'Cozy standard room with city view, perfect for short stays.', 'available'),
(4, 2, '201', 'Suite', 4500000.00, 'Elegant suite with French colonial decor and mountain view.', 'available'),
(5, 2, '202', 'Deluxe', 2800000.00, 'Deluxe room with garden view, spacious layout, and balcony.', 'available'),
(6, 2, '203', 'Standard', 2000000.00, 'Comfortable standard room with classic furniture and cozy ambiance.', 'available'),
(7, 3, '301', 'Suite', 6000000.00, 'Executive suite with private terrace, oceanfront view, and luxury amenities.', 'available'),
(8, 3, '302', 'Deluxe', 4200000.00, 'Deluxe room with beach view, stylish interior, and modern facilities.', 'available'),
(9, 3, '303', 'Standard', 3000000.00, 'Standard room with comfortable bedding and city view.', 'available'),
(10, 4, '401', 'Suite', 4000000.00, 'Spacious suite with panoramic ocean view and elegant furnishings.', 'available'),
(11, 4, '402', 'Deluxe', 2200000.00, 'Deluxe room with modern amenities and partial sea view.', 'available'),
(12, 4, '403', 'Standard', 1800000.00, 'Standard room with essential facilities and cozy atmosphere.', 'available'),
(13, 5, '501', 'Suite', 7000000.00, 'Riverside suite with traditional Hoi An architecture and premium amenities.', 'available'),
(14, 5, '502', 'Deluxe', 5200000.00, 'Deluxe room with river view, modern decor, and balcony.', 'available'),
(15, 5, '503', 'Standard', 4000000.00, 'Standard room with garden view, ideal for relaxing stays.', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Uyen', 'uyen@gmail.com', '$2y$10$eCLeG/R5rOHuIKh9cd4CLOIiTz6s7oZEwoKdzyEFSgxZrShVxGYrW', 'user', '2025-08-14 18:47:45'),
(2, 'Nguyen Van A', 'nguyena@gmail.com', '$2y$10$saKTRenMujUyncySOufxxe/WqDRz/L.I0NpC/4wkvTEa7E/raJ1q6', 'user', '2025-08-14 20:43:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotel_images`
--
ALTER TABLE `hotel_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rooms_hotel_id` (`hotel_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hotel_images`
--
ALTER TABLE `hotel_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel_images`
--
ALTER TABLE `hotel_images`
  ADD CONSTRAINT `hotel_images_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_rooms_hotel_id` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
