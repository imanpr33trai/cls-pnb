-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: live_pnb_cllsified_db
-- ------------------------------------------------------
-- Server version	8.0.42-0ubuntu0.24.04.1

    ;
    ;
    ;
    ;
    ;
    ;
    ;
    ;
    ;
    ;

--
-- Table structure for table `ad_categories`
--

DROP TABLE IF EXISTS `ad_categories`;
    ;
    ;
CREATE TABLE `ad_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('live','hold') DEFAULT 'live',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `ad_form`
--

DROP TABLE IF EXISTS `ad_form`;
    ;
    ;
CREATE TABLE `ad_form` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` int NOT NULL,
  `subcategory` int NOT NULL,
  `other_category` varchar(255) DEFAULT NULL,
  `ad_title` varchar(255) NOT NULL,
  `ad_slug` varchar(255) DEFAULT NULL,
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
  `platforms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `platform_links` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ad_slug` (`ad_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `ad_reviews`
--

DROP TABLE IF EXISTS `ad_reviews`;
    ;
    ;
CREATE TABLE `ad_reviews` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `ad_subcategories`
--

DROP TABLE IF EXISTS `ad_subcategories`;
    ;
    ;
CREATE TABLE `ad_subcategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('live','hold') DEFAULT 'live',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `ad_subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `ad_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `admin_personal_info`
--

DROP TABLE IF EXISTS `admin_personal_info`;
    ;
    ;
CREATE TABLE `admin_personal_info` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_name2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin_about` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
    ;
    ;
CREATE TABLE `admins` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
    ;
    ;
CREATE TABLE `blog_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` enum('live','hold') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'live',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `blog_comments`
--

DROP TABLE IF EXISTS `blog_comments`;
    ;
    ;
CREATE TABLE `blog_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `blog_id` int NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_image` varchar(255) DEFAULT 'assets/images/userimage.png',
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`),
  CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
    ;
    ;
CREATE TABLE `blog_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `blog_slug` varchar(255) DEFAULT NULL,
  `author_name` varchar(100) NOT NULL,
  `category_id` int NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `platform` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `platform_link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_slug` (`blog_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
    ;
    ;
CREATE TABLE `reviews` (
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
    ;
    ;
CREATE TABLE `sessions` (
  `id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `data` blob,
  `access` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_session_access` (`access`),
  KEY `idx_session_user` (`user_id`),
  CONSTRAINT `fk_sessions_to_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
    ;
    ;
CREATE TABLE `site_settings` (
  `id` int NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `header_logo` varchar(255) DEFAULT NULL,
  `footer_logo` varchar(255) DEFAULT NULL,
  `admin_phone` varchar(50) DEFAULT NULL,
  `admin_details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
    ;
    ;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `auth_provider` enum('local','google','github') NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`),
  UNIQUE KEY `uq_google_id` (`google_id`),
  UNIQUE KEY `uq_github_id` (`github_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ;
    ;

    ;
    ;
    ;
    ;
    ;
    ;
    ;

-- Dump completed on 2025-07-30 15:50:13
