/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: live_pnb_cllsified_db
-- ------------------------------------------------------
-- Server version	11.8.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `ad_categories`
--

DROP TABLE IF EXISTS `ad_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('live','hold') DEFAULT 'live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_categories`
--

LOCK TABLES `ad_categories` WRITE;
/*!40000 ALTER TABLE `ad_categories` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `ad_categories` VALUES
(15,'Sports','sports','1745222863_sports.svg','2025-04-21 08:07:43','live'),
(16,'Education','education','1745222874_education.svg','2025-04-21 08:07:54','live'),
(17,'announcement','announcement','1745222885_announcement.svg','2025-04-21 08:08:05','live'),
(19,'Clothes','clothes','1745222913_clothes.svg','2025-04-21 08:08:33','live');
/*!40000 ALTER TABLE `ad_categories` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `ad_form`
--

DROP TABLE IF EXISTS `ad_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) NOT NULL,
  `subcategory` int(11) NOT NULL,
  `other_category` varchar(255) DEFAULT NULL,
  `ad_title` varchar(255) NOT NULL,
  `ad_slug` varchar(255) DEFAULT NULL,
  `asking_price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
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
  `platforms` text DEFAULT NULL,
  `platform_links` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `ad_slug` (`ad_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_form`
--


INSERT INTO `ad_form` (`id`, `category`, `subcategory`, `other_category`, `ad_title`, `ad_slug`, `asking_price`, `description`, `user_name`, `organisation`, `email`, `phone`, `location`, `city_town_neighbourhood`, `postal_code`, `expires_in`, `expires_at`, `verification_code`, `image`, `status`, `platforms`, `platform_links`, `created_at`) VALUES
(10, 13, 1, 'fvads', 'asdfadsfasd', 'asdfadsfasd', 2532.00, '23452', '2435234', '2435243', 'gs6180673@gmail.com', '09413700903', 'erersfaer', 'HANUMANGARH', '335801', '+1 week', '2025-08-15 19:05:14', 'sregsrfg', '68063e1f124a4.png', 'live', 'adsfadfads', 'adsfasdf', '2025-04-21 12:46:23'),
(11, 13, 1, 'dfvsdfgvs2222222222', 'sdfjgvbsdfgsdfjkgns', 'sdfjgvbsdfgsdfjkgns', 2132.00, '1232', '25fgsd', '2435243', 'gs6180673@gmail.com', '09413700903', 'aefaef', 'HANUMANGARH', '335801', '+1 week', '2025-08-15 19:05:13', '12312', '680650063458c.png', 'live', 'adsfadfads', 'jksfhgjsdf', '2025-04-21 14:02:46'),
(12, 18, 3, 'dfvsdfgvs', 'adfadsfasdf', 'adfadsfasdf', 1111.00, 'sdcd', 'afdsasd', 'sdfgsdf', 'gs6180673@gmail.com', '09413700903', 'adfadfad', 'HANUMANGARH', '335801', '+1 week', '2025-08-15 19:05:07', 'dfdf', '6806716dd430a.png', 'live', 'adsfadfads', 'jksfhgjsdf', '2025-04-21 16:25:17'),
(13, 18, 4, 'fvads', 'sdczhdsshd', 'sdczhdsshd', 1212312.00, ',jvdcn,dsbc', '25fgsd', 'adfads', 'gs6180673@gmail.com', '09413700903', 'akjsdgfajl', 'HANUMANGARH', '335801', '+1 week', '2025-08-15 19:04:54', 'a.dsgfa', '680671b9375ce.jpeg', 'live', 'sdh', 'mshdg', '2025-04-21 16:26:33'),
(14, 29, 8, '', 'ewrawer', 'ewrawer', 34.00, 'dfga  ear er', 'Gursewak Singh', 'aeraer', 'gs6180673@gmail.com', '9413700903', 'afsdddddddddddd', 'aere', '334455', '+1 week', '2025-08-22 18:43:23', NULL, '', 'live', '[{\"platform\":\"Facebook\",\"link\":\"https:\\/\\/dgjasgd.com\"}]', '', '2025-06-15 13:13:23'),
(15, 18, 3, '', 'adfadsfasdftgds', 'adfadsfasdftgds', 45.00, 'wh g wrg wrg wrtg wr', 'Gursewak Singh', 'wrtwert', 'gs6180673@gmail.com', '9413700903', 'aefaef', 'HANUMANGARH', '335801', '+1 week', '2025-08-22 19:07:43', NULL, '', 'live', '[{\"platform\":\"Instagram\",\"link\":\"https:\\/\\/dgjasgd.com\"}]', '', '2025-06-15 13:37:43'),
(16, 20, 10, 'fvads', 'asdfadsfasdscc', 'asdfadsfasdscc', 23.00, 'SD sd ASD', 'Gursewak Singh', 'hfhgf', 'gs6180673@gmail.com', '9413700903', 'fgfjgff', 'gfhgfhgf', '554433', '+1 week', '2025-08-22 19:12:12', NULL, '', 'live', '[{\"platform\":\"Facebook\",\"link\":\"https:\\/\\/dgjasgd.com\"}]', '', '2025-06-15 13:42:12'),
(17, 20, 10, 'df', 'adfadsfasdfgf', 'adfadsfasdfgf', 458888.00, 'hgjfghgh f    fffhgfghff', 'Gursewak Singh', 'fg ghfgf', 'gs6180673@gmail.com', '9413700903', 'gfjg hjfgg', 'hfjhf', '335804', '+2 weeks', '2025-08-29 19:17:06', NULL, '', 'live', '[{\"platform\":\"Facebook\",\"link\":\"https:\\/\\/dgjasgd.com\"}]', '', '2025-06-15 13:47:06'),
(18, 20, 10, 'ghjg', 'jhgjhgggjhg jghjg jh', 'jhgjhgggjhg-jghjg-jh', 7765555.00, 'hjg g g sdf f dfahdfkjhdjk afjhdfjklha', 'Gursewak Singh', 'hjd j', 'gs6180673@gmail.com', '9413700903', 'hdsjfhs dfahsdjk', 'dhjef sdf', '987456', '+1 month', '2025-08-15 19:19:09', NULL, 'ad_684ecf55c43997.88207895.png', 'live', NULL, '', '2025-06-15 13:49:09'),
(19, 29, 8, 'fdasdfsdf', 'asdfadsfasd', 'asdfadsfasd-19', 12345.00, 'sdcsdf df sdf sdaf f df adfa f df\r\nfdjhvkj\r\ndbfjadhfjah\r\n\r\nlkjfgjkzhdfjkh', 'Gursewak Singh', 'sdfsdfsdf', 'gs6180673@gmail.com', '9413700903', 'location', 'city town neighbourhood', '335801', '+1 week', '2025-08-22 20:06:26', NULL, 'ad_684eda6acccf48.80164644.png', 'live', NULL, '', '2025-06-15 14:36:26'),
(20, 29, 8, '', 'asdfadsfasd', 'asdfadsfasd-20', 12212.00, 'dfe erae r ert rtre ter trter', 'Gursewak Singh', 'rtwrt trt wert wt wertr', 'gs6180673@gmail.com', '9413700903', 'afsddddddddddddwrt', 'HANUMANGARHrtw', '335801', '+1 week', '2025-08-22 20:14:00', NULL, 'ad_684edc30cc5028.91959150.png', 'live', NULL, '', '2025-06-15 14:44:00'),
(21, 20, 10, '', 'adfadsfasdfdfSDfdf', 'adfadsfasdfdfsdfdf', 2323.00, 'df s  dff asdf df adsf adfads f', 'Gursewak Singh', 'adf asdfadf', 'gs6180673@gmail.com', '9413700903', 'adfadfadsf', 'adfadfadfad', '223355', '+1 week', '2025-08-22 20:19:54', NULL, 'ad_684edd92149779.35457230.png', 'live', '[{\"platform\":\"Facebook\",\"link\":\"https:\\/\\/dgjasgd.com\"},{\"platform\":\"LinkedIn\",\"link\":\"https:\\/\\/dgjadsfasgd.com\"}]', '', '2025-06-15 14:49:54'),
(22, 20, 10, '', 'asdfadsfasdsadSA', 'asdfadsfasdsadsa', 23423.00, 'dfd d gg g', 'Gursewak Singh', 'dfgsdfg', 'gs6180673@gmail.com', '9413700903', 'afsdddddddddddd', 'sfgsfg', '123456', '+2 weeks', '2025-08-29 20:23:45', NULL, 'ad_684ede79414788.26873611.jpg', 'live', '[{\"platform\":\"Facebook\",\"link\":\"https:\\/\\/dgjadsfasgd.com\"},{\"platform\":\"Twitter\",\"link\":\"https:\\/\\/dgjasgd.com\"},{\"platform\":\"Website\",\"link\":\"https:\\/\\/dgjadsfasdfasdfasdfdsfasgd.com\"}]', '', '2025-06-15 14:53:45'),
(23, 18, 3, 'dgdsf', 'dfgsdfg dfs gdf df df gdf df df', 'dfgsdfg-dfs-gdf-df-df', 34534.00, 'dsfg sdf', 'man singh', 'sdfgdfgd', 'manpreet.singh01356@gmail.com', '34534545', 'dsfgsdgdg', 'gdsfgsdg', '345345', '+1 month', '2025-09-06 12:25:30', NULL, 'ad_6892fc624a9f03.72579716.jpg', 'live', '', '', '2025-08-06 06:55:30');

--
-- Table structure for table `ad_reviews`
--

DROP TABLE IF EXISTS `ad_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `ad_reviews_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ad_form` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_reviews`
--

INSERT INTO `ad_reviews` (`id`, `ad_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(37, 23, 58, 3, 'sdfa', '2025-08-06 06:56:25'),
(38, 23, 58, 2, 'asdf', '2025-08-06 06:56:32'),
(39, 23, 58, 3, 'asdasd', '2025-08-06 07:08:47'),
(40, 23, 58, 3, 'asdf', '2025-08-06 07:11:19'),
(41, 23, 58, 3, 'ads', '2025-08-06 07:49:07'),
(42, 23, 58, 2, 'asdf', '2025-08-06 08:00:40'),
(43, 23, 58, 2, 'asdf', '2025-08-06 08:30:06'),
(44, 23, 58, 3, 'sdf', '2025-08-06 08:30:09'),
(45, 23, 59, 2, 'mnbn', '2025-08-06 13:40:13');

--
-- Table structure for table `ad_subcategories`
--

DROP TABLE IF EXISTS `ad_subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ad_subcategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('live','hold') DEFAULT 'live',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `ad_subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `ad_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ad_subcategories`
--

INSERT INTO `ad_subcategories` (`id`, `category_id`, `title`, `created_at`, `status`) VALUES
(3, 18, 'car', '2025-04-21 16:24:21', 'live'),
(8, 29, 'furniture 1', '2025-04-27 07:15:35', 'live'),
(10, 20, 'new sub cat', '2025-04-27 10:33:29', 'live');

--
-- Table structure for table `admin_personal_info`
--

DROP TABLE IF EXISTS `admin_personal_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_personal_info` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name2` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_pass` varchar(255) NOT NULL,
  `admin_image` text NOT NULL,
  `admin_contact` varchar(255) NOT NULL,
  `admin_about` text NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_personal_info`
--

LOCK TABLES `admin_personal_info` WRITE;
/*!40000 ALTER TABLE `admin_personal_info` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `admin_personal_info` VALUES
(1,'Gursewak Singh','gs6180673@gmail.com','Guru@1356001','admin_img.jpg','9413700903','Full Stack');
/*!40000 ALTER TABLE `admin_personal_info` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `admins` VALUES
(1,'admin','admin@example.com','$2y$12$e8DnsfGoh8gqAFBhS3xrTeaDqDWN5kbUQV1tXBjpR7k9SBfXQzWFG','Test Admin','default.jpg','admin','2025-04-21 04:22:53'),
(2,'man','manpreet@gmail.com','$2y$12$xZ6mb00U/UlpL05UKkT7luhDdBRHoh2hkgfdzSfq6KK9uGlFOfrhO','man admin',NULL,'admin','2025-08-08 08:20:25'),
(3,'man1','man@example.com','$2y$12$vxs1miFwKda2ba29PosxZe1z29h2liXCy0Jq7B.Jp4Ts7/yzD3ehy','manE',NULL,'admin','2025-08-08 08:40:37');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` enum('live','hold') DEFAULT 'live',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_categories`
--

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `blog_categories` VALUES
(7,'blog cat 2','live','2025-04-27 14:45:43'),
(8,'blog cat 3','live','2025-04-27 14:46:18');
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `blog_comments`
--

DROP TABLE IF EXISTS `blog_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_image` varchar(255) DEFAULT 'assets/images/userimage.png',
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`),
  CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_comments`
--

LOCK TABLES `blog_comments` WRITE;
/*!40000 ALTER TABLE `blog_comments` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `blog_comments` VALUES
(1,6,'kjsdhfsdh','assets/images/userimage.png','ksjjsefhekjhf','2025-04-21 18:27:33'),
(2,6,'jkdsjkd','assets/images/userimage.png','jksd','2025-04-21 18:27:52'),
(3,6,'jkdhsfkjs','assets/images/userimage.png','fkjsdhfkjs','2025-04-21 18:29:09'),
(4,9,'Gursewak Singh','assets/images/userimage.png','hello','2025-04-21 19:16:27'),
(5,6,'Gursewak Singh','assets/images/userimage.png','ewfwewe','2025-06-15 17:50:24'),
(6,6,'Gursewak Singh','assets/images/userimage.png','ewfaef','2025-06-15 17:50:27'),
(10,11,'Gursewak Singh','assets/images/userimage.png','fasefa','2025-06-15 18:33:08');
/*!40000 ALTER TABLE `blog_comments` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `blog_slug` varchar(255) DEFAULT NULL,
  `author_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `platform` text DEFAULT NULL,
  `platform_link` text DEFAULT NULL,
  `status` enum('pending','live','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_slug` (`blog_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `blog_posts` VALUES
(1,'dasdfadsf','dasdfadsf','dsafsdfads',1,'adfvafd','gs6180673@gmail.com','9638527410','68064ba3dec22.jpeg','234dfs','sfgsfg','live','2025-04-21 13:44:03'),
(2,'dasdfadsf','dasdfadsf-2','dsafsdfads',1,'sakjhfaef','gs6180673@gmail.com','9874563210','68064bc7705f2.png','jkdjfv','kjahjdf','live','2025-04-21 13:44:39'),
(3,'dasdfadsf','dasdfadsf-3','dsafsdfads',1,'kjdjfdj','gs6180673@gmail.com','888888888888','68064edede962.png','kjdfv','fffffffffffffffffff','live','2025-04-21 13:57:50'),
(5,'zdfgvldhfiuafa','zdfgvldhfiuafa','1111111111111',1,'gdkfsgadkfgajdshgf','gs6180673@gmail.com','546464564','[]','234dfs','kjahjdf','live','2025-04-21 16:44:01'),
(6,'zdfgvldhfiuafa','zdfgvldhfiuafa-6','1111111111111',8,'gdkfsgadkfgajdshgf','gs6180673@gmail.com','546464564','[]','234dfs','kjahjdf','rejected','2025-04-21 16:46:54'),
(9,'this is my new article and this is updating by form','this-is-my-new-article-and-this-is-updating-by-form','gursewak singh',7,'adsf','gs6180673@gmail.com','9413700903','[\"6806996bb41df.png\",\"6806996bb5cb4.png\",\"6806996bb71bf.png\"]','instagram','https://www.google.com','live','2025-04-21 19:15:55'),
(11,'this is my new article and this is updating','this-is-my-new-article-and-this-is-updating-by-form-222222222222','Gursewak Singh',7,'<h1>hedajhf</h1>\r\n<p>aefafafajdgfjafjafgajgfhasdf</p>\r\n<h3>afasdfasfasdfasdfasdf</h3>fgd\r\n<p>fjhfdjhgjhdfljasdfasdf</p>\r\n<h1>sdfdfasdf</h1>\r\n<p>lahsdgkhfagsdf</p>','gs6180673@gmail.com','9413700903','[\"blog_684f11cd84f0b8.36153100.jpg\"]','[{\"platform\":\"Twitter\",\"link\":\"https:\\/\\/dgjadsfasgd.com\"}]','','live','2025-06-15 18:32:45');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `reported_ads`
--

DROP TABLE IF EXISTS `reported_ads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reported_ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `reason` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reported_ad_id` (`ad_id`),
  KEY `fk_reported_ads_user_id` (`user_id`),
  CONSTRAINT `fk_reported_ads_ad_id` FOREIGN KEY (`ad_id`) REFERENCES `ad_form` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reported_ads_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reported_ads`
--

LOCK TABLES `reported_ads` WRITE;
/*!40000 ALTER TABLE `reported_ads` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `reported_ads` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ad_form` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `data` blob DEFAULT NULL,
  `access` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_session_access` (`access`),
  KEY `idx_session_user` (`user_id`),
  CONSTRAINT `fk_sessions_to_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `sessions` VALUES
('01d70d2c3d1890f81753ff2eb3ca568a',NULL,'',1754977237),
('0279a70f4c0e6f8882d30b6e5db345c1',NULL,'',1754904442),
('162bbd78da35c84a882b553ba8a889bb',NULL,'admins_id|i:1;admins_username|s:5:\"admin\";',1754911235),
('1de9f82d92b8fdf842db23618e9c157c',NULL,'',1754912158),
('20fa8f6f69cf909f51f2c4645b2a4c55',NULL,'',1754904198),
('231ecf9fe95c3a89dca2b2a751776b5d',NULL,'',1754986681),
('2a165820024af53b8aef28aa6e765f8d',NULL,'admin_id|i:1;admin_username|s:5:\"admin\";oauth2state|s:32:\"7904cf0f217f942dd172f9ed694a9285\";redirect_to|s:11:\"ad-form.php\";admins_id|i:3;admins_username|s:4:\"man1\";',1754911149),
('2b3ca2810e9a00484c24b271c4d60437',60,'admins_id|i:1;admins_username|s:5:\"admin\";redirect_to|s:11:\"ad-form.php\";oauth2state|s:32:\"29d9eec22151b2bc5a3dcf2126db77a9\";user_id|i:60;user_name|s:9:\"sdfg sdfg\";user_email|s:29:\"manpreet.singh01356@gmail.com\";captcha_question|s:14:\"What is 5 + 5?\";captcha_answer|i:10;',1754989552),
('2e4e12a404ca8955f68d52e1b6717417',NULL,'',1754975296),
('2fde79f0d7f1ee871e0994a47fb0913d',NULL,'',1754985463),
('33b0c9ec78ec483d8b98b420a4b41b8c',NULL,'',1754912158),
('384cc7b50bbe75420597782e4c4cbe0f',NULL,'',1754910895),
('3a9c094068155666cb14e8e5a8e101bb',NULL,'',1754915351),
('3e8df38b67ac0dc8c2ace135fc7cfcbe',NULL,'',1754986681),
('489b2951d9c826729997772c60d37569',NULL,'',1754976524),
('55296257a751b327a5b0f2f587f6ff72',NULL,'',1754975296),
('55818ff6c940bdf45a94923835bd05c9',NULL,'',1754972831),
('5637b2c4321c42952ea086db8a7e297f',NULL,'',1754909267),
('569f2a5ea5528568f730b43b96ad7f29',NULL,'',1754916676),
('615e02683039828e2c59480c36dfd33c',NULL,'',1754978750),
('655c640df5094032a4f7efb113c39599',NULL,'',1754972831),
('68ae86ecdeac2533bea7e36309434a00',NULL,'',1754981394),
('7da5b1d3b1915a09a1285a88cfe59d8f',NULL,'',1754915351),
('8c43555c932530fd5a389f1677c35baf',NULL,'admins_id|i:3;admins_username|s:4:\"man1\";',1754911252),
('94dc9a0e457a4f802ccc9e293560ea51',NULL,'',1754987121),
('9f0e341dc01e11c1ba9b3630d7bd1aff',NULL,'',1754916676),
('a34c3f7fcc310d62a0eaaf4610e5be76',NULL,'',1754983485),
('a4fd327bde775f0e1ee54c3651d3a387',NULL,'',1754904674),
('adedbe90300234421755c35fa8ed7540',NULL,'',1754909303),
('bdc2412b9621ba23aba336e0f6993fdc',NULL,'',1754983485),
('bed575b9191632d9aaadb46614050b3b',NULL,'',1754985463),
('c2a6954c34b71d340659c44d314f5175',NULL,'',1754910895),
('c3820d988623d5df4b31113469d8bc8e',NULL,'',1754987121),
('c92838b8f8b641154c4f816a283ff330',NULL,'',1754976524),
('cd526d74260a862de289c26f34e8d4f0',NULL,'',1754915766),
('cd946dbf8925434e01d1ce2c781f1a2a',NULL,'',1754904442),
('d03023e87dc2358a5b2f82f0977b158a',NULL,'',1754981394),
('d6fa97443e21262e5113e4b43bf3ceee',NULL,'',1754978693),
('d89a8d2cbf5497ee68f7bbafd8f4fd1c',NULL,'',1754904674),
('dae8e6121ebf3240ced00e215416f562',NULL,'',1754915766),
('e3b6f4d5c793afdfcdc22e92b446c120',NULL,'',1754909267),
('f75d3870225ed288251efc2fc78526e7',NULL,'',1754977237),
('fa40b4b2696ccfb106d5b2a1deaa587d',NULL,'',1754978694),
('fef6b86704a52307bfa5152036567505',NULL,'',1754904198);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `header_logo` varchar(255) DEFAULT NULL,
  `footer_logo` varchar(255) DEFAULT NULL,
  `admin_phone` varchar(50) DEFAULT NULL,
  `admin_details` text DEFAULT NULL,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT NULL,
  `smtp_secure` varchar(10) DEFAULT NULL,
  `smtp_user` varchar(255) DEFAULT NULL,
  `smtp_pass` varchar(255) DEFAULT NULL,
  `smtp_from_email` varchar(255) DEFAULT NULL,
  `smtp_from_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `subscribers`
--

DROP TABLE IF EXISTS `subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscribers`
--

LOCK TABLES `subscribers` WRITE;
/*!40000 ALTER TABLE `subscribers` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `subscribers` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(35) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('unverified','active','suspended') NOT NULL DEFAULT 'unverified',
  `verification_otp` varchar(10) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `github_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `auth_provider` enum('local','google','github') NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  UNIQUE KEY `uq_google_id` (`google_id`),
  UNIQUE KEY `uq_github_id` (`github_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
(60,'sdfg','sdfg',NULL,'manpreet.singh01356@gmail.com',NULL,'$2y$12$9yVE1.TZZMi59bxW6zvrmuzaNqRuj1yRDmqi62dFSoW9CBeXz2ww.','active',NULL,NULL,NULL,NULL,'2025-08-12 07:48:16','2025-08-12 07:48:48','local');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Dumping routines for database 'live_pnb_cllsified_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-08-12 14:41:30
