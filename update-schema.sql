-- This file contains schema updates needed for new features.

-- Update the status column in the ad_form table to support more states
-- We are changing it to an ENUM to restrict the possible values,
-- which is better for data integrity.
ALTER TABLE `ad_form`
MODIFY COLUMN `status` ENUM('pending', 'live', 'rejected', 'expired', 'reported') 
NOT NULL DEFAULT 'pending';

-- Add a table for reported ads to track reports from users
CREATE TABLE IF NOT EXISTS `reported_ads` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ad_id` INT NOT NULL,
  `user_id` BIGINT UNSIGNED NULL, -- Can be null if reported by a non-logged-in user
  `reason` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_reported_ad_id` (`ad_id`),
  CONSTRAINT `fk_reported_ads_ad_id`
    FOREIGN KEY (`ad_id`)
    REFERENCES `ad_form` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_reported_ads_user_id`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add a status column to the blog_posts table for approval workflow
ALTER TABLE `blog_posts`
ADD COLUMN `status` ENUM('pending', 'live', 'rejected') NOT NULL DEFAULT 'pending' AFTER `platform_link`;

-- Create subscribers table for newsletter signups
CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add SMTP settings to the site_settings table
ALTER TABLE `site_settings`
ADD COLUMN `smtp_host` VARCHAR(255) NULL,
ADD COLUMN `smtp_port` INT NULL,
ADD COLUMN `smtp_secure` VARCHAR(10) NULL,
ADD COLUMN `smtp_user` VARCHAR(255) NULL,
ADD COLUMN `smtp_pass` VARCHAR(255) NULL,
ADD COLUMN `smtp_from_email` VARCHAR(255) NULL,
ADD COLUMN `smtp_from_name` VARCHAR(255) NULL;
