-- Add ad_slug column to ad_form table
ALTER TABLE `ad_form`
ADD COLUMN `ad_slug` VARCHAR(255) UNIQUE AFTER `ad_title`;