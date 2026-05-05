-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 02, 2025 lúc 12:25 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `product_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--
CREATE DATABASE IF NOT EXISTS product_management;
USE product_management;

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(20) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 'IPHONE14', 'iPhone 14 Series', 'iPhone 14 với thiết kế mới, camera cải tiến và hiệu năng vượt trội', 'assets/images/thumbnails/iphone14.jpg', '2026-05-05 10:00:00', '2026-05-05 10:00:00'),
(2, 'SAMSUNGS23', 'Samsung Galaxy S23', 'Samsung Galaxy S23 với màn hình Dynamic AMOLED và camera đa ống kính', 'assets/images/thumbnails/samsung-s23.jpg', '2026-05-05 09:30:00', '2026-05-05 09:30:00'),
(3, 'MACBOOKAIR', 'MacBook Air M2', 'MacBook Air với chip M2, thiết kế siêu mỏng nhẹ và pin lâu dài', 'assets/images/thumbnails/macbook-air-m2.jpg', '2026-05-05 14:20:00', '2026-05-05 14:20:00'),
(4, 'IPADPRO12', 'iPad Pro 12.9 inch', 'iPad Pro 12.9 inch với chip M2, màn hình Liquid Retina XDR', 'assets/images/thumbnails/ipad-pro-12.jpg', '2026-05-05 11:00:00', '2026-05-05 11:00:00'),
(5, 'AIRPODSPRO3', 'AirPods Pro 3', 'AirPods Pro thế hệ 3 với chống ồn chủ động và chất âm sống động', 'assets/images/thumbnails/airpods-pro-3.jpg', '2026-05-05 14:30:00', '2026-05-05 14:30:00'),
(6, 'GALAXYZFOLD5', 'Samsung Galaxy Z Fold5', 'Điện thoại màn hình gập cao cấp với công nghệ mới nhất', 'assets/images/thumbnails/galaxy-z-fold5.jpg', '2026-05-05 09:15:00', '2026-05-05 09:15:00'),
(7, 'SURFACEPRO9', 'Microsoft Surface Pro 9', 'Surface Pro 9 với thiết kế 2-in-1, hiệu năng mạnh mẽ', 'assets/images/thumbnails/surface-pro-9.jpg', '2026-05-05 16:45:00', '2026-05-05 16:45:00'),
(8, 'XIAOMI13PRO', 'Xiaomi 13 Pro', 'Xiaomi 13 Pro với camera Leica và hiệu năng flagship', 'assets/images/thumbnails/xiaomi-13-pro.jpg', '2026-05-05 13:20:00', '2026-05-05 13:20:00'),
(9, 'OPPOFINDX6', 'OPPO Find X6 Pro', 'OPPO Find X6 Pro với thiết kế sang trọng và camera ấn tượng', 'assets/images/thumbnails/oppo-find-x6.jpg', '2026-05-05 10:10:00', '2026-05-05 10:10:00'),
(10, 'VIVOX90PRO', 'vivo X90 Pro', 'vivo X90 Pro với hệ thống camera Zeiss và chip Dimensity', 'assets/images/thumbnails/vivo-x90-pro.jpg', '2026-05-05 15:30:00', '2026-05-05 15:30:00'),
(11, 'XIAOMI13', 'Xiaomi 13', 'Xiaomi 13 với camera Leica cao cấp và hiệu năng mạnh mẽ', 'assets/images/thumbnails/xiaomi-13-pro.jpg', '2026-05-05 09:20:00', '2026-05-05 09:20:00'),
(12, 'SAMSUNGGALAXYS22', 'Samsung Galaxy S22', 'Samsung Galaxy S22 nhỏ gọn với chip Snapdragon mạnh mẽ và camera chất lượng', 'assets/images/thumbnails/galaxy-z-fold5.jpg', '2026-05-05 14:45:00', '2026-05-05 14:45:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `created_at`) VALUES
(1, 1, 'assets/images/products/iphone14-black-front.jpg', '2026-05-05 10:00:00'),
(2, 1, 'assets/images/products/iphone14-black-back.jpg', '2026-05-05 10:00:00'),
(3, 1, 'assets/images/products/iphone14-black-side.jpg', '2026-05-05 10:00:00'),
(4, 1, 'assets/images/products/iphone14-pink-front.jpg', '2026-05-05 10:00:00'),
(5, 1, 'assets/images/products/iphone14-pink-back.jpg', '2026-05-05 10:00:00'),
(6, 1, 'assets/images/products/iphone14-blue-front.jpg', '2026-05-05 10:00:00'),
(7, 1, 'assets/images/products/iphone14-blue-back.jpg', '2026-05-05 10:00:00'),
(8, 2, 'assets/images/products/s23-green-front.jpg', '2026-05-05 09:30:00'),
(9, 2, 'assets/images/products/s23-green-back.jpg', '2026-05-05 09:30:00'),
(10, 2, 'assets/images/products/s23-black-front.jpg', '2026-05-05 09:30:00'),
(11, 2, 'assets/images/products/s23-black-back.jpg', '2026-05-05 09:30:00'),
(12, 2, 'assets/images/products/s23-cream-front.jpg', '2026-05-05 09:30:00'),
(13, 2, 'assets/images/products/s23-cream-back.jpg', '2026-05-05 09:30:00'),
(14, 3, 'assets/images/products/macbook-silver-front.jpg', '2026-05-05 14:20:00'),
(15, 3, 'assets/images/products/macbook-silver-keyboard.jpg', '2026-05-05 14:20:00'),
(16, 3, 'assets/images/products/macbook-spacegray-front.jpg', '2026-05-05 14:20:00'),
(17, 3, 'assets/images/products/macbook-spacegray-keyboard.jpg', '2026-05-05 14:20:00'),
(18, 3, 'assets/images/products/macbook-midnight-front.jpg', '2026-05-05 14:20:00'),
(19, 3, 'assets/images/products/macbook-midnight-keyboard.jpg', '2026-05-05 14:20:00'),
(20, 4, 'assets/images/products/ipad-silver-front.jpg', '2026-05-05 11:00:00'),
(21, 4, 'assets/images/products/ipad-silver-back.jpg', '2026-05-05 11:00:00'),
(22, 4, 'assets/images/products/ipad-spacegray-front.jpg', '2026-05-05 11:00:00'),
(23, 4, 'assets/images/products/ipad-spacegray-back.jpg', '2026-05-05 11:00:00'),
(24, 5, 'assets/images/products/airpods-white-case.jpg', '2026-05-05 14:30:00'),
(25, 5, 'assets/images/products/airpods-white-earpieces.jpg', '2026-05-05 14:30:00'),
(26, 5, 'assets/images/products/airpods-white-charging.jpg', '2026-05-05 14:30:00'),
(27, 6, 'assets/images/products/zfold5-black-front.jpg', '2026-05-05 09:15:00'),
(28, 6, 'assets/images/products/zfold5-black-folded.jpg', '2026-05-05 09:15:00'),
(29, 6, 'assets/images/products/zfold5-beige-front.jpg', '2026-05-05 09:15:00'),
(30, 6, 'assets/images/products/zfold5-beige-folded.jpg', '2026-05-05 09:15:00'),
(31, 6, 'assets/images/products/zfold5-silver-front.jpg', '2026-05-05 09:15:00'),
(32, 6, 'assets/images/products/zfold5-silver-folded.jpg', '2026-05-05 09:15:00'),
(33, 7, 'assets/images/products/surface-platinum-front.jpg', '2026-05-05 16:45:00'),
(34, 7, 'assets/images/products/surface-platinum-keyboard.jpg', '2026-05-05 16:45:00'),
(35, 7, 'assets/images/products/surface-black-front.jpg', '2026-05-05 16:45:00'),
(36, 7, 'assets/images/products/surface-black-keyboard.jpg', '2026-05-05 16:45:00'),
(37, 7, 'assets/images/products/surface-green-front.jpg', '2026-05-05 16:45:00'),
(38, 7, 'assets/images/products/surface-green-keyboard.jpg', '2026-05-05 16:45:00'),
(39, 8, 'assets/images/products/xiaomi-black-front.jpg', '2026-05-05 13:20:00'),
(40, 8, 'assets/images/products/xiaomi-black-back.jpg', '2026-05-05 13:20:00'),
(41, 8, 'assets/images/products/xiaomi-green-front.jpg', '2026-05-05 13:20:00'),
(42, 8, 'assets/images/products/xiaomi-green-back.jpg', '2026-05-05 13:20:00'),
(43, 8, 'assets/images/products/xiaomi-white-front.jpg', '2026-05-05 13:20:00'),
(44, 8, 'assets/images/products/xiaomi-white-back.jpg', '2026-05-05 13:20:00'),
(45, 9, 'assets/images/products/oppo-black-front.jpg', '2026-05-05 10:10:00'),
(46, 9, 'assets/images/products/oppo-black-back.jpg', '2026-05-05 10:10:00'),
(47, 9, 'assets/images/products/oppo-gold-front.jpg', '2026-05-05 10:10:00'),
(48, 9, 'assets/images/products/oppo-gold-back.jpg', '2026-05-05 10:10:00'),
(49, 9, 'assets/images/products/oppo-green-front.jpg', '2026-05-05 10:10:00'),
(50, 9, 'assets/images/products/oppo-green-back.jpg', '2026-05-05 10:10:00'),
(51, 10, 'assets/images/products/vivo-black-front.jpg', '2026-05-05 15:30:00'),
(52, 10, 'assets/images/products/vivo-black-back.jpg', '2026-05-05 15:30:00'),
(53, 10, 'assets/images/products/vivo-red-front.jpg', '2026-05-05 15:30:00'),
(54, 10, 'assets/images/products/vivo-red-back.jpg', '2026-05-05 15:30:00'),
(55, 10, 'assets/images/products/vivo-blue-front.jpg', '2026-05-05 15:30:00'),
(56, 10, 'assets/images/products/vivo-blue-back.jpg', '2026-05-05 15:30:00'),
(57, 11, 'assets/images/products/xiaomi-black-back.jpg', '2026-05-05 13:20:00'),
(58, 12, 'assets/images/products/xiaomi-green-front.jpg', '2026-05-05 13:20:00'),
(59, 12, 'assets/images/products/s23-black-front.jpg', '2026-05-05 09:30:00'),
(60, 12, 'assets/images/products/s23-black-back.jpg', '2026-05-05 09:30:00'),
(61, 12, 'assets/images/products/s23-cream-front.jpg', '2026-05-05 09:30:00');
-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `sku` varchar(30) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `storage` varchar(10) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `sku`, `color`, `storage`, `price`, `stock`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'IPHONE14-BLACK-128', 'Đen', '128GB', 21990000.00, 50, 'assets/images/products/iphone14-black-front.jpg', '2026-05-05 10:00:00', '2026-05-05 10:00:00'),
(2, 1, 'IPHONE14-BLACK-256', 'Đen', '256GB', 24990000.00, 30, 'assets/images/products/iphone14-black-front.jpg', '2026-05-05 10:00:00', '2026-05-05 10:00:00'),
(3, 1, 'IPHONE14-PINK-128', 'Hồng', '128GB', 21990000.00, 25, 'assets/images/products/iphone14-pink-front.jpg', '2026-05-05 10:00:00', '2026-05-05 10:00:00'),
(4, 1, 'IPHONE14-PINK-256', 'Hồng', '256GB', 24990000.00, 20, 'assets/images/products/iphone14-pink-front.jpg', '2026-05-05 10:00:00', '2026-05-05 10:00:00'),
(5, 1, 'IPHONE14-BLUE-128', 'Xanh dương', '128GB', 21990000.00, 18, 'assets/images/products/iphone14-blue-front.jpg', '2026-05-05 10:00:00', '2026-05-05 10:00:00'),
(6, 2, 'S23-GREEN-256', 'Xanh lá', '256GB', 18990000.00, 40, 'assets/images/products/s23-green-front.jpg', '2026-05-05 09:30:00', '2026-05-05 09:30:00'),
(7, 2, 'S23-BLACK-256', 'Đen', '256GB', 18990000.00, 35, 'assets/images/products/s23-black-front.jpg', '2026-05-05 09:30:00', '2026-05-05 09:30:00'),
(8, 2, 'S23-CREAM-256', 'Kem', '256GB', 18990000.00, 22, 'assets/images/products/s23-cream-front.jpg', '2026-05-05 09:30:00', '2026-05-05 09:30:00'),
(9, 3, 'MBA-SILVER-256', 'Bạc', '256GB', 28990000.00, 15, 'assets/images/products/macbook-silver-front.jpg', '2026-05-05 14:20:00', '2026-05-05 14:20:00'),
(10, 3, 'MBA-SPACEGRAY-256', 'Space Gray', '256GB', 28990000.00, 12, 'assets/images/products/macbook-spacegray-front.jpg', '2026-05-05 14:20:00', '2026-05-05 14:20:00'),
(11, 3, 'MBA-MIDNIGHT-256', 'Midnight', '256GB', 28990000.00, 18, 'assets/images/products/macbook-midnight-front.jpg', '2026-05-05 14:20:00', '2026-05-05 14:20:00'),
(12, 4, 'IPAD-SILVER-128', 'Bạc', '128GB', 22990000.00, 25, 'assets/images/products/ipad-silver-front.jpg', '2026-05-05 11:00:00', '2026-05-05 11:00:00'),
(13, 4, 'IPAD-SPACEGRAY-128', 'Space Gray', '128GB', 22990000.00, 20, 'assets/images/products/ipad-spacegray-front.jpg', '2026-05-05 11:00:00', '2026-05-05 11:00:00'),
(14, 5, 'AIRPODS-WHITE', 'Trắng', 'N/A', 6990000.00, 100, 'assets/images/products/airpods-white-case.jpg', '2026-05-05 14:30:00', '2026-05-05 14:30:00'),
(15, 6, 'ZFOLD5-BLACK-256', 'Đen', '256GB', 38990000.00, 12, 'assets/images/products/zfold5-black-front.jpg', '2026-05-05 09:15:00', '2026-05-05 09:15:00'),
(16, 6, 'ZFOLD5-BEIGE-256', 'Beige', '256GB', 38990000.00, 10, 'assets/images/products/zfold5-beige-front.jpg', '2026-05-05 09:15:00', '2026-05-05 09:15:00'),
(17, 6, 'ZFOLD5-SILVER-256', 'Bạc', '256GB', 38990000.00, 8, 'assets/images/products/zfold5-silver-front.jpg', '2026-05-05 09:15:00', '2026-05-05 09:15:00'),
(18, 7, 'SURFACE-PLATINUM-256', 'Platinum', '256GB', 25990000.00, 18, 'assets/images/products/surface-platinum-front.jpg', '2026-05-05 16:45:00', '2026-05-05 16:45:00'),
(19, 7, 'SURFACE-BLACK-256', 'Đen', '256GB', 25990000.00, 15, 'assets/images/products/surface-black-front.jpg', '2026-05-05 16:45:00', '2026-05-05 16:45:00'),
(20, 7, 'SURFACE-GREEN-256', 'Xanh lá', '256GB', 25990000.00, 12, 'assets/images/products/surface-green-front.jpg', '2026-05-05 16:45:00', '2026-05-05 16:45:00'),
(21, 8, 'XIAOMI-BLACK-256', 'Đen', '256GB', 17990000.00, 25, 'assets/images/products/xiaomi-black-front.jpg', '2026-05-05 13:20:00', '2026-05-05 13:20:00'),
(22, 8, 'XIAOMI-GREEN-256', 'Xanh lá', '256GB', 17990000.00, 20, 'assets/images/products/xiaomi-green-front.jpg', '2026-05-05 13:20:00', '2026-05-05 13:20:00'),
(23, 8, 'XIAOMI-WHITE-256', 'Trắng', '256GB', 17990000.00, 18, 'assets/images/products/xiaomi-white-front.jpg', '2026-05-05 13:20:00', '2026-05-05 13:20:00'),
(24, 9, 'OPPO-BLACK-256', 'Đen', '256GB', 19990000.00, 20, 'assets/images/products/oppo-black-front.jpg', '2026-05-05 10:10:00', '2026-05-05 10:10:00'),
(25, 9, 'OPPO-GOLD-256', 'Vàng', '256GB', 19990000.00, 15, 'assets/images/products/oppo-gold-front.jpg', '2026-05-05 10:10:00', '2026-05-05 10:10:00'),
(26, 9, 'OPPO-GREEN-256', 'Xanh lá', '256GB', 19990000.00, 16, 'assets/images/products/oppo-green-front.jpg', '2026-05-05 10:10:00', '2026-05-05 10:10:00'),
(27, 10, 'VIVO-BLACK-256', 'Đen', '256GB', 18990000.00, 18, 'assets/images/products/vivo-black-front.jpg', '2026-05-05 15:30:00', '2026-05-05 15:30:00'),
(28, 10, 'VIVO-RED-256', 'Đỏ', '256GB', 18990000.00, 16, 'assets/images/products/vivo-red-front.jpg', '2026-05-05 15:30:00', '2026-05-05 15:30:00'),
(29, 10, 'VIVO-BLUE-256', 'Xanh dương', '256GB', 18990000.00, 14, 'assets/images/products/vivo-blue-front.jpg', '2026-05-05 15:30:00', '2026-05-05 15:30:00');



--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- 1) Tạo bảng categories
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categories_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- 2) Tạo bảng suppliers
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_vietnamese_ci;

-- 3) Thêm cột category_id và supplier_id vào bảng products
ALTER TABLE `products`
  ADD COLUMN `category_id` int(11) DEFAULT NULL AFTER `description`,
  ADD COLUMN `supplier_id` int(11) DEFAULT NULL AFTER `category_id`;

-- 4) Tạo index cho 2 cột mới
ALTER TABLE `products`
  ADD KEY `idx_products_category_id` (`category_id`),
  ADD KEY `idx_products_supplier_id` (`supplier_id`);

-- 5) Tạo khóa ngoại
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_products_supplier`
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

-- 6) Dữ liệu mẫu cho categories
INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Điện thoại', 'Các dòng điện thoại thông minh'),
(2, 'Laptop', 'Các dòng máy tính xách tay'),
(3, 'Tablet', 'Các dòng máy tính bảng'),
(4, 'Phụ kiện', 'Tai nghe, phụ kiện công nghệ');

-- 7) Dữ liệu mẫu cho suppliers
INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`) VALUES
(1, 'Apple Vietnam', '0901000001', 'apple@example.com', 'TP.HCM'),
(2, 'Samsung Vietnam', '0901000002', 'samsung@example.com', 'Hà Nội'),
(3, 'Microsoft Vietnam', '0901000003', 'microsoft@example.com', 'TP.HCM'),
(4, 'Xiaomi Distributor', '0901000004', 'xiaomi@example.com', 'Đà Nẵng'),
(5, 'OPPO Vietnam', '0901000005', 'oppo@example.com', 'Hà Nội'),
(6, 'Vivo Vietnam', '0901000006', 'vivo@example.com', 'TP.HCM');

-- 8) Gán category_id và supplier_id cho dữ liệu products hiện có
UPDATE `products` SET `category_id` = 1, `supplier_id` = 1 WHERE `id` = 1; -- iPhone 14
UPDATE `products` SET `category_id` = 1, `supplier_id` = 2 WHERE `id` = 2; -- Samsung Galaxy S23
UPDATE `products` SET `category_id` = 2, `supplier_id` = 1 WHERE `id` = 3; -- MacBook Air M2
UPDATE `products` SET `category_id` = 3, `supplier_id` = 1 WHERE `id` = 4; -- iPad Pro
UPDATE `products` SET `category_id` = 4, `supplier_id` = 1 WHERE `id` = 5; -- AirPods Pro
UPDATE `products` SET `category_id` = 1, `supplier_id` = 2 WHERE `id` = 6; -- Galaxy Z Fold5
UPDATE `products` SET `category_id` = 2, `supplier_id` = 3 WHERE `id` = 7; -- Surface Pro 9
UPDATE `products` SET `category_id` = 1, `supplier_id` = 4 WHERE `id` = 8; -- Xiaomi 13 Pro
UPDATE `products` SET `category_id` = 1, `supplier_id` = 5 WHERE `id` = 9; -- OPPO Find X6 Pro
UPDATE `products` SET `category_id` = 1, `supplier_id` = 6 WHERE `id` = 10; -- vivo X90 Pro
UPDATE `products` SET `category_id` = 1, `supplier_id` = 4 WHERE `id` = 11; -- Xiaomi 13 
UPDATE `products` SET `category_id` = 1, `supplier_id` = 2 WHERE `id` = 12; -- Galaxy