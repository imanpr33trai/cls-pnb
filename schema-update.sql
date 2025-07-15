-- SQL statements to update schema for slug columns



ALTER TABLE `ad_form`
ADD COLUMN `ad_slug` VARCHAR(255) UNIQUE AFTER `ad_title`;

ALTER TABLE `blog_posts`
ADD COLUMN `blog_slug` VARCHAR(255) UNIQUE AFTER `title`;