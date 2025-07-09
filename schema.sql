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


-- Dumping database structure for mandb
CREATE DATABASE IF NOT EXISTS `mandb` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `mandb`;

-- Dumping structure for table mandb.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.admins: ~0 rows (approximately)
INSERT INTO `admins` (`id`, `username`, `email`, `password`, `full_name`, `profile_image`, `role`, `created_at`) VALUES
	(1, 'admin', 'admin@example.com', '$2y$10$J.pcnHhzKpldtwiyqNqJLen1HXLZOOFLWxjDtSNtHDP5KflBLtTxC', 'Test Admin', 'default.jpg', 'admin', '2025-04-21 04:22:53');

-- Dumping structure for table mandb.admin_personal_info
CREATE TABLE IF NOT EXISTS `admin_personal_info` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_name2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `admin_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `admin_pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `admin_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `admin_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `admin_about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.admin_personal_info: ~0 rows (approximately)
INSERT INTO `admin_personal_info` (`admin_id`, `admin_name2`, `admin_email`, `admin_pass`, `admin_image`, `admin_contact`, `admin_about`) VALUES
	(1, 'Gursewak Singh', 'gs6180673@gmail.com', 'Guru@1356001', 'admin_img.jpg', '9413700903', 'Full Stack');

-- Dumping structure for table mandb.ad_categories
CREATE TABLE IF NOT EXISTS `ad_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('live','hold') DEFAULT 'live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.ad_categories: ~8 rows (approximately)
INSERT INTO `ad_categories` (`id`, `name`, `slug`, `image`, `created_at`, `status`) VALUES
	(15, 'Sports', 'sports', '1745222863_sports.svg', '2025-04-21 08:07:43', 'live'),
	(16, 'Education', 'education', '1745222874_education.svg', '2025-04-21 08:07:54', 'live'),
	(17, 'announcement', 'announcement', '1745222885_announcement.svg', '2025-04-21 08:08:05', 'live'),
	(18, 'Vehicles', 'vehicles', '1745757133_pngwing.com (1).png', '2025-04-21 08:08:20', 'live'),
	(19, 'Clothes', 'clothes', '1745222913_clothes.svg', '2025-04-21 08:08:33', 'live'),
	(20, 'Electronicsww', 'electronicsww', '1745222923_electronics.svg', '2025-04-21 08:08:43', 'live'),
	(21, 'Property', 'property', '1745222935_property.svg', '2025-04-21 08:08:55', 'live'),
	(29, 'Furniture', 'furniture', '1745757913_furniture.svg', '2025-04-27 12:45:13', 'live');

-- Dumping structure for table mandb.ad_form
CREATE TABLE IF NOT EXISTS `ad_form` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` int NOT NULL,
  `subcategory` int NOT NULL,
  `other_category` varchar(255) DEFAULT NULL,
  `ad_title` varchar(255) NOT NULL,
  `asking_price` decimal(10,2) DEFAULT NULL,
  `description` text,
  `user_name` varchar(100) DEFAULT NULL,
  `organisation` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `city_town_neighbourhood` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `expires_in` varchar(50) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `verification_code` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'live',
  `platforms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `platform_links` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.ad_form: ~13 rows (approximately)
INSERT INTO `ad_form` (`id`, `category`, `subcategory`, `other_category`, `ad_title`, `asking_price`, `description`, `user_name`, `organisation`, `email`, `phone`, `location`, `city_town_neighbourhood`, `postal_code`, `expires_in`, `expires_at`, `verification_code`, `image`, `status`, `platforms`, `platform_links`, `created_at`) VALUES
	(10, 13, 1, 'fvads', 'asdfadsfasd', 2532.00, '23452', '2435234', '2435243', 'gs6180673@gmail.com', '09413700903', 'erersfaer', 'HANUMANGARH', '335801', '+1 week', '2025-06-15 19:05:14', 'sregsrfg', '68063e1f124a4.png', 'live', 'adsfadfads', 'adsfasdf', '2025-04-21 12:46:23'),
	(11, 13, 1, 'dfvsdfgvs2222222222', 'sdfjgvbsdfgsdfjkgns', 2132.00, '1232', '25fgsd', '2435243', 'gs6180673@gmail.com', '09413700903', 'aefaef', 'HANUMANGARH', '335801', '+1 week', '2025-06-15 19:05:13', '12312', '680650063458c.png', 'live', 'adsfadfads', 'jksfhgjsdf', '2025-04-21 14:02:46'),
	(12, 18, 3, 'dfvsdfgvs', 'adfadsfasdf', 1111.00, 'sdcd', 'afdsasd', 'sdfgsdf', 'gs6180673@gmail.com', '09413700903', 'adfadfad', 'HANUMANGARH', '335801', '+1 week', '2025-06-15 19:05:07', 'dfdf', '6806716dd430a.png', 'live', 'adsfadfads', 'jksfhgjsdf', '2025-04-21 16:25:17'),
	(13, 18, 4, 'fvads', 'sdczhdsshd', 1212312.00, ',jvdcn,dsbc', '25fgsd', 'adfads', 'gs6180673@gmail.com', '09413700903', 'akjsdgfajl', 'HANUMANGARH', '335801', '+1 week', '2025-06-15 19:04:54', 'a.dsgfa', '680671b9375ce.jpeg', 'live', 'sdh', 'mshdg', '2025-04-21 16:26:33'),
	(14, 29, 8, '', 'ewrawer', 34.00, 'dfga  ear er', 'Gursewak Singh', 'aeraer', 'gs6180673@gmail.com', '9413700903', 'afsdddddddddddd', 'aere', '334455', '+1 week', '2025-06-22 18:43:23', NULL, '', 'live', '[{"platform":"Facebook","link":"https:\\/\\/dgjasgd.com"}]', '', '2025-06-15 13:13:23'),
	(15, 18, 3, '', 'adfadsfasdftgds', 45.00, 'wh g wrg wrg wrtg wr', 'Gursewak Singh', 'wrtwert', 'gs6180673@gmail.com', '9413700903', 'aefaef', 'HANUMANGARH', '335801', '+1 week', '2025-06-22 19:07:43', NULL, '', 'live', '[{"platform":"Instagram","link":"https:\\/\\/dgjasgd.com"}]', '', '2025-06-15 13:37:43'),
	(16, 20, 10, 'fvads', 'asdfadsfasdscc', 23.00, 'SD sd ASD', 'Gursewak Singh', 'hfhgf', 'gs6180673@gmail.com', '9413700903', 'fgfjgff', 'gfhgfhgf', '554433', '+1 week', '2025-06-22 19:12:12', NULL, '', 'live', '[{"platform":"Facebook","link":"https:\\/\\/dgjasgd.com"}]', '', '2025-06-15 13:42:12'),
	(17, 20, 10, 'df', 'adfadsfasdfgf', 458888.00, 'hgjfghgh f    fffhgfghff', 'Gursewak Singh', 'fg ghfgf', 'gs6180673@gmail.com', '9413700903', 'gfjg hjfgg', 'hfjhf', '335804', '+2 weeks', '2025-06-29 19:17:06', NULL, '', 'live', '[{"platform":"Facebook","link":"https:\\/\\/dgjasgd.com"}]', '', '2025-06-15 13:47:06'),
	(18, 20, 10, 'ghjg', 'jhgjhgggjhg jghjg jh', 7765555.00, 'hjg g g sdf f dfahdfkjhdjk afjhdfjklha', 'Gursewak Singh', 'hjd j', 'gs6180673@gmail.com', '9413700903', 'hdsjfhs dfahsdjk', 'dhjef sdf', '987456', '+1 month', '2025-07-15 19:19:09', NULL, 'ad_684ecf55c43997.88207895.png', 'live', NULL, '', '2025-06-15 13:49:09'),
	(19, 29, 8, 'fdasdfsdf', 'asdfadsfasd', 12345.00, 'sdcsdf df sdf sdaf f df adfa f df\r\nfdjhvkj\r\ndbfjadhfjah\r\n\r\nlkjfgjkzhdfjkh', 'Gursewak Singh', 'sdfsdfsdf', 'gs6180673@gmail.com', '9413700903', 'location', 'city town neighbourhood', '335801', '+1 week', '2025-06-22 20:06:26', NULL, 'ad_684eda6acccf48.80164644.png', 'live', NULL, '', '2025-06-15 14:36:26'),
	(20, 29, 8, '', 'asdfadsfasd', 12212.00, 'dfe erae r ert rtre ter trter', 'Gursewak Singh', 'rtwrt trt wert wt wertr', 'gs6180673@gmail.com', '9413700903', 'afsddddddddddddwrt', 'HANUMANGARHrtw', '335801', '+1 week', '2025-06-22 20:14:00', NULL, 'ad_684edc30cc5028.91959150.png', 'live', NULL, '', '2025-06-15 14:44:00'),
	(21, 20, 10, '', 'adfadsfasdfdfSDfdf', 2323.00, 'df s  dff asdf df adsf adfads f', 'Gursewak Singh', 'adf asdfadf', 'gs6180673@gmail.com', '9413700903', 'adfadfadsf', 'adfadfadfad', '223355', '+1 week', '2025-06-22 20:19:54', NULL, 'ad_684edd92149779.35457230.png', 'live', '[{"platform":"Facebook","link":"https:\\/\\/dgjasgd.com"},{"platform":"LinkedIn","link":"https:\\/\\/dgjadsfasgd.com"}]', '', '2025-06-15 14:49:54'),
	(22, 20, 10, '', 'asdfadsfasdsadSA', 23423.00, 'dfd d gg g', 'Gursewak Singh', 'dfgsdfg', 'gs6180673@gmail.com', '9413700903', 'afsdddddddddddd', 'sfgsfg', '123456', '+2 weeks', '2025-06-29 20:23:45', NULL, 'ad_684ede79414788.26873611.jpg', 'live', '[{"platform":"Facebook","link":"https:\\/\\/dgjadsfasgd.com"},{"platform":"Twitter","link":"https:\\/\\/dgjasgd.com"},{"platform":"Website","link":"https:\\/\\/dgjadsfasdfasdfasdfdsfasgd.com"}]', '', '2025-06-15 14:53:45');

-- Dumping structure for table mandb.ad_reviews
CREATE TABLE IF NOT EXISTS `ad_reviews` (
  `id` int NOT NULL,
  `ad_id` int NOT NULL,
  `user_id` int unsigned NOT NULL,
  `rating` int NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `ad_reviews_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ad_form` (`id`),
  CONSTRAINT `ad_reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.ad_reviews: ~32 rows (approximately)
INSERT INTO `ad_reviews` (`id`, `ad_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
	(1, 13, 1, 2, 'dsd', '2025-05-11 12:09:59'),
	(2, 11, 1, 3, 'dfvdf', '2025-05-11 12:10:43'),
	(3, 13, 1, 5, 'sdcas', '2025-05-11 12:36:59'),
	(4, 13, 1, 2, 'sdf', '2025-05-11 12:39:32'),
	(5, 13, 1, 2, 'guru', '2025-05-11 12:40:19'),
	(6, 10, 1, 3, 'sd', '2025-05-11 12:40:31'),
	(7, 13, 1, 3, 'dsf', '2025-05-11 12:41:47'),
	(8, 13, 1, 2, 'sdfas', '2025-05-11 12:46:35'),
	(9, 13, 1, 2, 'dcs', '2025-05-11 12:51:07'),
	(10, 13, 1, 2, 'dcs', '2025-05-11 12:51:39'),
	(11, 13, 1, 2, 'dcsrrrrrrrrrrrrrrrrrrrrr', '2025-05-11 12:51:49'),
	(12, 13, 1, 2, 'dcsrrrrrrrrrrrrrrrrrrrrr', '2025-05-11 12:51:54'),
	(13, 13, 1, 2, 'sdfc', '2025-05-11 12:54:58'),
	(14, 13, 1, 4, 'gurdeep', '2025-05-11 12:55:07'),
	(15, 13, 1, 2, 'fasdf', '2025-05-11 13:06:53'),
	(16, 14, 1, 2, 'ghncgh', '2025-06-15 19:04:23'),
	(17, 20, 1, 2, 'sdfas', '2025-06-15 19:42:03'),
	(18, 16, 1, 3, 'sdfd', '2025-06-15 22:52:45'),
	(19, 16, 1, 3, 'zdfgzfdgsfd', '2025-06-15 22:52:52'),
	(20, 22, 1, 3, 'zsdfsd', '2025-06-15 22:53:07'),
	(21, 22, 1, 2, 'zdsfzsdf', '2025-06-15 23:02:58'),
	(22, 22, 1, 3, 'fgsdfgsdfgs', '2025-06-15 23:03:05'),
	(23, 22, 1, 4, 'xfdgsdfgsdfg', '2025-06-15 23:03:10'),
	(24, 22, 1, 5, 'zdsfsdfsd', '2025-06-15 23:03:15'),
	(25, 22, 1, 3, 'zdsfzsdfzsdf', '2025-06-15 23:03:19'),
	(26, 22, 1, 1, 'eeeeeeeeeeeeeeeeeeee', '2025-06-15 23:03:35'),
	(27, 22, 1, 3, 'sdfeeeeeeeerrrrrrrrrrrrrrrr', '2025-06-15 23:16:39'),
	(28, 22, 1, 3, 'sdfeeeeeeeerrrrrrrrrrrrrrrr', '2025-06-15 23:16:39'),
	(29, 22, 1, 4, 'eer3333333333333', '2025-06-15 23:16:50'),
	(30, 22, 1, 4, 'eer3333333333333', '2025-06-15 23:16:50'),
	(31, 22, 1, 2, '22222222222222222222', '2025-06-15 23:17:09'),
	(32, 18, 1, 3, 'xfvzf', '2025-06-15 23:17:50');

-- Dumping structure for table mandb.ad_subcategories
CREATE TABLE IF NOT EXISTS `ad_subcategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('live','hold') DEFAULT 'live',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `ad_subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `ad_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.ad_subcategories: ~3 rows (approximately)
INSERT INTO `ad_subcategories` (`id`, `category_id`, `title`, `created_at`, `status`) VALUES
	(3, 18, 'car', '2025-04-21 16:24:21', 'live'),
	(8, 29, 'furniture 1', '2025-04-27 07:15:35', 'live'),
	(10, 20, 'new sub cat', '2025-04-27 10:33:29', 'live');

-- Dumping structure for table mandb.blog_categories
CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` enum('live','hold') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'live',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.blog_categories: ~3 rows (approximately)
INSERT INTO `blog_categories` (`id`, `name`, `status`, `created_at`) VALUES
	(6, 'blog cat 11', 'live', '2025-04-27 14:45:00'),
	(7, 'blog cat 2', 'live', '2025-04-27 14:45:43'),
	(8, 'blog cat 3', 'live', '2025-04-27 14:46:18');

-- Dumping structure for table mandb.blog_comments
CREATE TABLE IF NOT EXISTS `blog_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `blog_id` int NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_image` varchar(255) DEFAULT 'assets/images/userimage.png',
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`),
  CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.blog_comments: ~10 rows (approximately)
INSERT INTO `blog_comments` (`id`, `blog_id`, `user_name`, `user_image`, `comment`, `created_at`) VALUES
	(1, 6, 'kjsdhfsdh', 'assets/images/userimage.png', 'ksjjsefhekjhf', '2025-04-21 18:27:33'),
	(2, 6, 'jkdsjkd', 'assets/images/userimage.png', 'jksd', '2025-04-21 18:27:52'),
	(3, 6, 'jkdhsfkjs', 'assets/images/userimage.png', 'fkjsdhfkjs', '2025-04-21 18:29:09'),
	(4, 9, 'Gursewak Singh', 'assets/images/userimage.png', 'hello', '2025-04-21 19:16:27'),
	(5, 6, 'Gursewak Singh', 'assets/images/userimage.png', 'ewfwewe', '2025-06-15 17:50:24'),
	(6, 6, 'Gursewak Singh', 'assets/images/userimage.png', 'ewfaef', '2025-06-15 17:50:27'),
	(7, 10, 'Gursewak Singh', 'assets/images/userimage.png', 'dfgdfgs', '2025-06-15 18:10:14'),
	(8, 10, 'Gursewak Singh', 'assets/images/userimage.png', 'sdrg fgs', '2025-06-15 18:10:15'),
	(9, 10, 'Gursewak Singh', 'assets/images/userimage.png', 'sdrg fgg', '2025-06-15 18:10:17'),
	(10, 11, 'Gursewak Singh', 'assets/images/userimage.png', 'fasefa', '2025-06-15 18:33:08');

-- Dumping structure for table mandb.blog_posts
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author_name` varchar(100) NOT NULL,
  `category_id` int NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `platform` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `platform_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.blog_posts: ~12 rows (approximately)
INSERT INTO `blog_posts` (`id`, `title`, `author_name`, `category_id`, `description`, `email`, `phone`, `image`, `platform`, `platform_link`, `created_at`) VALUES
	(1, 'dasdfadsf', 'dsafsdfads', 1, 'adfvafd', 'gs6180673@gmail.com', '9638527410', '68064ba3dec22.jpeg', '234dfs', 'sfgsfg', '2025-04-21 13:44:03'),
	(2, 'dasdfadsf', 'dsafsdfads', 1, 'sakjhfaef', 'gs6180673@gmail.com', '9874563210', '68064bc7705f2.png', 'jkdjfv', 'kjahjdf', '2025-04-21 13:44:39'),
	(3, 'dasdfadsf', 'dsafsdfads', 1, 'kjdjfdj', 'gs6180673@gmail.com', '888888888888', '68064edede962.png', 'kjdfv', 'fffffffffffffffffff', '2025-04-21 13:57:50'),
	(4, '', '', 0, '', '', '', '[]', '', '', '2025-04-21 16:42:53'),
	(5, 'zdfgvldhfiuafa', '1111111111111', 1, 'gdkfsgadkfgajdshgf', 'gs6180673@gmail.com', '546464564', '[]', '234dfs', 'kjahjdf', '2025-04-21 16:44:01'),
	(6, 'zdfgvldhfiuafa', '1111111111111', 1, 'gdkfsgadkfgajdshgf', 'gs6180673@gmail.com', '546464564', '[]', '234dfs', 'kjahjdf', '2025-04-21 16:46:54'),
	(7, '', '', 0, '', '', '', '["6806779e1de8c.png","6806779e1eebb.png","6806779e20293.png"]', '', '', '2025-04-21 16:51:42'),
	(8, '', '', 0, '', '', '', '["680678064df1f.png","680678064eb04.png","6806780650461.png"]', '', '', '2025-04-21 16:53:26'),
	(9, 'this is my new article and this is updating by form', 'gursewak singh', 2, 'this is description for this article and this is very long and here we can add more text sto increase page length', 'gs6180673@gmail.com', '9413700903', '["6806996bb41df.png","6806996bb5cb4.png","6806996bb71bf.png"]', 'instagram', 'https://www.google.com', '2025-04-21 19:15:55'),
	(10, 'this is my new article and this is updating by formzzfzsefq32323', 'Gursewak Singh', 7, 'dDfdsfdfasdf dfa ef aef asdfa sf asdfa sdf asdf saf asdf asdf as f sfa sdf asfa sfas fasdfaefwerawef af aef aef ewa eaeraer aef aea era era er aer aer er aer aer aer re aer aer aer aet dh dh hjfhfh jhjd hdj gh dghdgdh dgh h dfgh d', 'gs6180673@gmail.com', '9413700903', '["blog_684f0c7145d338.06942234.png"]', '[{"platform":"Facebook","link":"https:\\/\\/dgjadsfasgd.com"},{"platform":"Instagram","link":"https:\\/\\/dgjadsfasgd.com"}]', '', '2025-06-15 18:09:53'),
	(11, 'this is my new article and this is updating by form 222222222222', 'Gursewak Singh', 7, '<h1>hedajhf</h1>\r\n<p>aefafafajdgfjafjafgajgfhasdf</p>\r\n<h3>afasdfasfasdfasdfasdf</h3>\r\n<p>fjhfdjhgjhdfljasdfasdf</p>\r\n<h1>sdfdfasdf</h1>\r\n<p>lahsdgkhfagsdf</p>', 'gs6180673@gmail.com', '9413700903', '["blog_684f11cd84f0b8.36153100.jpg"]', '[{"platform":"Twitter","link":"https:\\/\\/dgjadsfasgd.com"}]', '', '2025-06-15 18:32:45'),
	(12, 'this is my new article and this is updating by form 22222222222222', 'Gursewak Singh', 7, '<h2>this is head 2 1</h2>\r\n<p>hjkfajf<strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\r\n<h2>this is head 2 2</h2>\r\n<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\r\n<h2>this is head 2 3</h2>\r\n<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>', 'gs6180673@gmail.com', '9413700903', '["blog_684f123e6aa741.83349190.jpg"]', '[{"platform":"Facebook","link":"https:\\/\\/dgjasgd.com"}]', '', '2025-06-15 18:34:38');

-- Dumping structure for table mandb.reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ad_id` int NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ad_form` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.reviews: ~4 rows (approximately)
INSERT INTO `reviews` (`id`, `ad_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
	(1, 12, NULL, 5, 'hello', '2025-05-11 06:14:57'),
	(2, 12, NULL, 5, 'this is review for this post', '2025-05-11 06:20:42'),
	(3, 12, NULL, 2, 'this is review for this post', '2025-05-11 06:20:58'),
	(4, 12, NULL, 2, 'fd', '2025-05-11 06:25:18');

-- Dumping structure for table mandb.site_settings
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `header_logo` varchar(255) DEFAULT NULL,
  `footer_logo` varchar(255) DEFAULT NULL,
  `admin_phone` varchar(50) DEFAULT NULL,
  `admin_details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.site_settings: ~0 rows (approximately)

-- Dumping structure for table mandb.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(35) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `github_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `auth_provider` enum('local','google','github') NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  UNIQUE KEY `uq_google_id` (`google_id`),
  UNIQUE KEY `uq_github_id` (`github_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table mandb.users: ~12 rows (approximately)
INSERT INTO `users` (`id`, `first_name`, `last_name`, `country`, `email`, `phone`, `password`, `google_id`, `github_id`, `created_at`, `updated_at`, `auth_provider`) VALUES
	(3, 'Gurdeep', 'Singh', NULL, 'deep17799@gmail.com', '9414200903', 'Guru@1356001', NULL, NULL, '2025-04-29 16:09:23', '2025-06-21 12:49:53', 'local'),
	(5, 'ssas', '212', NULL, 'gs61806ss73@gmail.com', '', '$2y$10$9Y9D67wre32lXLAPcFfdJ.BrQAe5mfDYz5ZchdftKDfRFhqB4h0kK', NULL, NULL, '2025-06-15 11:18:23', '2025-06-21 12:49:53', 'local'),
	(6, 'Gursewak', 'Singh', NULL, 'gs618ss0673@gmail.com', '', '$2y$10$mUF5cBsgqORh7TMZWUEI9OqFwc5Zi8dlzpNp7WLrV1qJwfdbRYIvC', NULL, NULL, '2025-06-15 11:23:28', '2025-06-21 12:49:53', 'local'),
	(7, 'Gursewak', 'Singh', NULL, 'gs61806ddfd73@gmail.comd', '', '$2y$10$sewLo51C8wwVPBlB157aSuEY1e4wzXD48vevuw2hp4gNYJNC6055y', NULL, NULL, '2025-06-15 11:24:43', '2025-06-21 12:49:53', 'local'),
	(8, 'Gursewak', 'Singh', NULL, 'gs6180sada673@gmail.com', '09413700903', '$2y$10$62zHadYn1N1.HajG1ss3zejBKx2tY9VRE7SRzwUTto6BPC/1W7Mva', NULL, NULL, '2025-06-15 11:29:26', '2025-06-21 12:49:53', 'local'),
	(9, 'Gursewak', 'Singh', NULL, 'gs6180dcsd673@gmail.com', '0941370090334', '$2y$10$gUBAHTVxP.4EBx5ugqFU.OHb0iVjc1wP7jSpIx4z2baBsAVyCqTMy', NULL, NULL, '2025-06-15 11:38:25', '2025-06-21 12:49:53', 'local'),
	(10, 'Gursewak', 'Singh', 'Albania', 'gsasd6180673@gmail.com', '987456', '$2y$10$FEcZpiQMsWM7JDl1ay3jIe5CsBEHe5qSNQdihKMgwC1XxPeWsORim', NULL, NULL, '2025-06-15 11:45:01', '2025-06-21 12:49:53', 'local'),
	(11, 'Man', 'Singh', 'India', 'man@gmail.com', '', '$2y$12$wkXsW97N6pnYaLrqXXvJB.s1lP8m.CZJ.v7QaOcWTSzFEO9VnP9N2', NULL, NULL, '2025-06-20 05:54:30', '2025-06-21 12:49:53', 'local'),
	(12, 'man', 'singh', 'India', 'man1@gmail.com', '', '$2y$12$mcpIq1fQUtPo1uczBzW1YutFKZIN.nvM80zB6OX8ew0YwSrMd62NO', NULL, NULL, '2025-06-20 05:57:33', '2025-06-21 12:49:53', 'local'),
	(14, 'GURSEWAK', 'SINGH', NULL, 'gs6180673@gmail.com', NULL, NULL, '113326719675697553203', NULL, '2025-06-21 13:02:41', '2025-06-21 13:02:41', 'google'),
	(15, 'GUR SEWAK', 'SINGH', NULL, 'gs6180674@gmail.com', NULL, NULL, '112614342918916230243', NULL, '2025-06-22 03:52:42', '2025-06-22 03:52:42', 'google'),
	(16, 'imanpr33trai', '', NULL, 'manpreet.singh01356@gmail.com', NULL, NULL, NULL, '123877434', '2025-06-22 08:04:57', '2025-06-22 08:04:57', 'github');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
