-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for my_store
CREATE DATABASE IF NOT EXISTS `my_store` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `my_store`;

-- Dumping structure for table my_store.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.category: ~5 rows (approximately)
INSERT INTO `category` (`id`, `name`, `description`) VALUES
	(1, 'Điện thoại', 'Danh mục các loại điện thoại'),
	(2, 'Laptop', 'Danh mục các loại laptop'),
	(3, 'Máy tính bảng', 'Danh mục các loại máy tính bảng'),
	(4, 'Phụ kiện', 'Danh mục phụ kiện điện tử'),
	(5, 'Thiết bị âm thanh', 'Danh mục loa, tai nghe, micro');

-- Dumping structure for table my_store.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.orders: ~0 rows (approximately)

-- Dumping structure for table my_store.order_details
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.order_details: ~0 rows (approximately)

-- Dumping structure for table my_store.product
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.product: ~8 rows (approximately)
INSERT INTO `product` (`id`, `name`, `description`, `price`, `image`, `category_id`) VALUES
	(10, 'Laptop gaming ASUS ROG Strix G16 G614PH-TS118W', 'ASUS TUF Gaming là dòng laptop gaming bền bỉ theo tiêu chuẩn quân đội, trang bị GPU RTX 50 Series, RAM từ 16GB, màn hình 100% sRGB, tần số quét cao, laptop gaming cho học sinh sinh viên với mức giá tốt nhất phân khúc phổ thông.', 48000000.00, 'uploads/ASUS ROG Strix G16 G614PH-TS118W.jpg', 2),
	(12, 'Tai nghe Asus ROG Pelta WL RGB Black', 'Tai nghe Asus ROG Pelta WL RGB Black là dòng tai nghe gaming không dây cao cấp, nổi bật với công nghệ không dây ROG SpeedNova siêu độ trễ thấp. Sở hữu màng loa titan 50mm, micro siêu băng thông và thiết kế siêu nhẹ, đây là lựa chọn hoàn hảo cho game thủ.', 3190000.00, 'uploads/Asus ROG Pelta WL RGB Black.jpg', 4),
	(13, 'Loa Razer Leviathan V2', 'Loa Razer Leviathan V2 là hệ thống loa thanh (soundbar) đa driver kết hợp loa siêu trầm (subwoofer) dành cho PC, nổi bật với công nghệ âm thanh vòm THX Spatial Audio và hệ thống LED Razer Chroma RGB. Thiết bị mang lại trải nghiệm âm thanh sống động, đắm chìm cho cả chơi game và giải trí.', 5190000.00, 'uploads/Razer Leviathan V2.jpg', 5),
	(14, 'Máy tính bảng iPad Pro M5 11 inch WiFi 256GB', 'iPad Pro M5 11 inch WiFi 256GB là chiếc máy tính bảng cao cấp hàng đầu với sức mạnh vượt trội, hướng đến người dùng chuyên nghiệp và sáng tạo. Nổi bật với thiết kế siêu mỏng nhẹ, thiết bị tích hợp công nghệ AI tiên tiến, mang đến trải nghiệm đột phá cho công việc và giải trí.', 28090000.00, 'uploads/iPad Pro M5 11 inch WiFi 256GB.jpg', 3),
	(15, 'Điện thoại Asus ROG Phone 9 Pro (Snapdragon 8 Elite)', 'Asus ROG Phone 9 Pro (Snapdragon 8 Elite) là siêu phẩm smartphone gaming cao cấp nhất. Sở hữu chip Snapdragon 8 Elite mạnh mẽ cùng màn hình LTPO AMOLED 165Hz siêu mượt và hệ thống tản nhiệt GameCool 9, máy mang lại hiệu năng đỉnh cao cho các tựa game đồ họa nặng.', 27950000.00, 'uploads/AsusROGPhone9Pro.jpg', 1),
	(16, 'iPhone 17 Pro Max 2TB', 'iPhone 17 Pro Max là mẫu smartphone cao cấp nhất của Apple, sở hữu màn hình lớn 6,9 inch, chip A19 Pro mạnh mẽ và RAM 12GB tối ưu cho Apple Intelligence. Máy có thiết kế tản nhiệt buồng hơi mới, đi kèm các tùy chọn màu sắc nổi bật như Xanh Đậm.', 61990000.00, 'uploads/iphone17prm.jpg', 1),
	(17, 'Loa Logitech G560', 'Logitech G560 là hệ thống loa gaming 2.1 cao cấp, nổi bật với công suất khủng 240W và hệ thống đèn LIGHTSYNC RGB 16.8 triệu màu. Nó đồng bộ ánh sáng và âm thanh theo từng diễn biến trên màn hình, mang lại trải nghiệm đắm chìm cho mọi game thủ.', 4070000.00, 'uploads/Loa Logitech G560.jpg', 5),
	(18, 'Samsung Galaxy S25 Ultra 5G 12GB/512GB', 'Samsung Galaxy S25 Ultra 5G 12GB/512GB là siêu phẩm flagship hàng đầu, nổi bật với thiết kế khung viền Titanium sang trọng, bo góc cầm nắm thoải mái. Máy sở hữu sức mạnh đột phá từ chip Snapdragon 8 Elite, camera AI 200MP chuyên nghiệp, màn hình lớn 6.9 inch tuyệt đẹp cùng viên pin 5000mAh bền bỉ.', 30990000.00, 'uploads/Samsung Galaxy S25 Ultra .jpg', 1),
	(44, 'Giá treo màn hình máy tính North Bayou NB-F80-XE Trắng', 'Giá treo màn hình North Bayou NB-F80-XE Trắng là tay đỡ (arm) để bàn đa năng nổi bật với thiết kế công thái học (Ergonomic). Sản phẩm hỗ trợ hầu hết các màn hình kích thước từ 17&quot; đến 30&quot; (hoặc 32&quot;) với tải trọng dao động từ 2kg đến 9kg.', 350000.00, 'uploads/products/product_1780889160_9847.jpg', 4);

-- Dumping structure for table my_store.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `reset_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_reset_token` (`reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table my_store.users: ~2 rows (approximately)
INSERT INTO `users` (`id`, `name`, `phone`, `address`, `avatar`, `is_active`, `reset_token`, `reset_expires`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
	(1, 'thuandeptrai11111', '0825470113222', '', 'uploads/avatars/avatar_1_1780879573.jpg', 1, 'cbc7d534891e0981816716a2b68e55e32478ca6d58e683a893b6ba793f71b98b', '2026-06-08 02:36:51', 'user@gmail.com', '$2y$10$hqcRAYuWm.zD1Yzni.bgvOv2g2yhm5DDp.AXHg6QVYq0lEDC0GzV6', 'user', '2026-06-01 03:31:00', '2026-06-08 02:22:24'),
	(2, 'Admin2410kxk', NULL, NULL, NULL, 1, NULL, NULL, '2410kxk@gmail.com', '$2y$10$x4wLUvJHdfRT7hHPnFwe1e4CB2O/hUjvGBJCkOkFobm8F5Ok/BSym', 'admin', '2026-06-01 03:32:27', '2026-06-08 00:45:51'),
	(3, 'thanhthuan2410', NULL, NULL, 'uploads/avatars/avatar_3_1780885272.jpg', 1, NULL, NULL, 'kxk2410@gmail.com', '$2y$10$g87eiQlR7aQh.wGGgwtvUuqFYRdhnRKuIHEULVHkneHx8.M/1lpqu', 'user', '2026-06-08 00:50:26', '2026-06-08 03:33:56');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
