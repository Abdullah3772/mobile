-- ============================================
-- SmartDeals.lk - Complete Database SQL
-- Run this in MySQL/phpMyAdmin
-- ============================================

CREATE DATABASE IF NOT EXISTS smartdeals CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartdeals;

-- ============================================
-- 1. USERS
-- ============================================
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NULL DEFAULT NULL,
  `avatar` VARCHAR(255) NULL DEFAULT NULL,
  `role` ENUM('super_admin', 'shop_owner', 'customer') NOT NULL DEFAULT 'customer',
  `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
  `district` VARCHAR(255) NULL DEFAULT NULL,
  `address` TEXT NULL DEFAULT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. CATEGORIES
-- ============================================
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `icon` VARCHAR(255) NULL DEFAULT NULL,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `parent_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. BRANDS
-- ============================================
CREATE TABLE `brands` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(255) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. SHOPS & SHOP DOCUMENTS
-- ============================================
CREATE TABLE `shops` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `owner_name` VARCHAR(255) NOT NULL,
  `business_registration_number` VARCHAR(255) NULL DEFAULT NULL,
  `nic` VARCHAR(255) NOT NULL,
  `address` TEXT NOT NULL,
  `district` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NOT NULL,
  `whatsapp` VARCHAR(255) NULL DEFAULT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  `google_map_lat` VARCHAR(255) NULL DEFAULT NULL,
  `google_map_lng` VARCHAR(255) NULL DEFAULT NULL,
  `logo` VARCHAR(255) NULL DEFAULT NULL,
  `cover_image` VARCHAR(255) NULL DEFAULT NULL,
  `about` TEXT NULL DEFAULT NULL,
  `opening_hours` JSON NULL DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected', 'suspended') NOT NULL DEFAULT 'pending',
  `rejection_reason` VARCHAR(255) NULL DEFAULT NULL,
  `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `subscription_plan` ENUM('free', 'pro', 'premium', 'enterprise') NOT NULL DEFAULT 'free',
  `subscription_expires_at` TIMESTAMP NULL DEFAULT NULL,
  `rating` DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  `total_reviews` INT NOT NULL DEFAULT 0,
  `total_products` INT NOT NULL DEFAULT 0,
  `total_views` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shops_slug_unique` (`slug`),
  CONSTRAINT `shops_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `shop_documents` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `shop_documents_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. PRODUCTS, IMAGES & VIDEOS
-- ============================================
CREATE TABLE `products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `brand_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `model` VARCHAR(255) NULL DEFAULT NULL,
  `condition` ENUM('brand_new', 'used', 'refurbished') NOT NULL DEFAULT 'brand_new',
  `storage` VARCHAR(255) NULL DEFAULT NULL,
  `ram` VARCHAR(255) NULL DEFAULT NULL,
  `color` VARCHAR(255) NULL DEFAULT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `discount_price` DECIMAL(12,2) NULL DEFAULT NULL,
  `warranty` VARCHAR(255) NULL DEFAULT NULL,
  `warranty_type` VARCHAR(255) NULL DEFAULT NULL,
  `network_type` VARCHAR(255) NULL DEFAULT NULL,
  `imei` VARCHAR(255) NULL DEFAULT NULL,
  `trcsl_approved` TINYINT(1) NOT NULL DEFAULT 0,
  `box_available` TINYINT(1) NOT NULL DEFAULT 0,
  `accessories_included` TEXT NULL DEFAULT NULL,
  `stock_quantity` INT NOT NULL DEFAULT 1,
  `battery_health` VARCHAR(255) NULL DEFAULT NULL,
  `scratches` VARCHAR(255) NULL DEFAULT NULL,
  `face_id_working` TINYINT(1) NULL DEFAULT NULL,
  `original_display` TINYINT(1) NULL DEFAULT NULL,
  `repair_history` TEXT NULL DEFAULT NULL,
  `cash_price` DECIMAL(12,2) NULL DEFAULT NULL,
  `card_price` DECIMAL(12,2) NULL DEFAULT NULL,
  `emi_available` TINYINT(1) NOT NULL DEFAULT 0,
  `camera` VARCHAR(255) NULL DEFAULT NULL,
  `battery` VARCHAR(255) NULL DEFAULT NULL,
  `processor` VARCHAR(255) NULL DEFAULT NULL,
  `screen_size` VARCHAR(255) NULL DEFAULT NULL,
  `five_g_support` TINYINT(1) NOT NULL DEFAULT 0,
  `status` ENUM('active', 'inactive', 'sold', 'reserved') NOT NULL DEFAULT 'active',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `views_count` INT NOT NULL DEFAULT 0,
  `favorites_count` INT NOT NULL DEFAULT 0,
  `reservations_count` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  INDEX `products_category_brand_condition_status_index` (`category_id`, `brand_id`, `condition`, `status`),
  INDEX `products_price_discount_price_index` (`price`, `discount_price`),
  CONSTRAINT `products_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_images` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `is_360` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_videos` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `video_path` VARCHAR(255) NOT NULL,
  `thumbnail` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `product_videos_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. WISHLISTS
-- ============================================
CREATE TABLE `wishlists` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`, `product_id`),
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. RESERVATIONS
-- ============================================
CREATE TABLE `reservations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `customer_name` VARCHAR(255) NOT NULL,
  `customer_phone` VARCHAR(255) NOT NULL,
  `pickup_date` DATE NOT NULL,
  `notes` TEXT NULL DEFAULT NULL,
  `status` ENUM('pending', 'confirmed', 'rejected', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `rejection_reason` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reservations_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. REVIEWS
-- ============================================
CREATE TABLE `reviews` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `rating` INT UNSIGNED NOT NULL,
  `comment` TEXT NULL DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. CHAT (CONVERSATIONS & MESSAGES)
-- ============================================
CREATE TABLE `conversations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `last_message_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversations_user_id_shop_id_unique` (`user_id`, `shop_id`),
  CONSTRAINT `conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversations_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` BIGINT UNSIGNED NOT NULL,
  `sender_id` BIGINT UNSIGNED NOT NULL,
  `message` TEXT NULL DEFAULT NULL,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `voice_note` VARCHAR(255) NULL DEFAULT NULL,
  `shared_product_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `type` ENUM('text', 'image', 'voice', 'product_share') NOT NULL DEFAULT 'text',
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_shared_product_id_foreign` FOREIGN KEY (`shared_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. NOTIFICATIONS
-- ============================================
CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `type` VARCHAR(255) NOT NULL DEFAULT 'general',
  `link` VARCHAR(255) NULL DEFAULT NULL,
  `data` JSON NULL DEFAULT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `notifications_user_id_is_read_index` (`user_id`, `is_read`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. OFFERS & OFFER PRODUCTS
-- ============================================
CREATE TABLE `offers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `type` ENUM('flash_sale', 'weekend_offer', 'clearance_sale', 'festival_offer', 'custom') NOT NULL DEFAULT 'custom',
  `discount_percentage` DECIMAL(5,2) NULL DEFAULT NULL,
  `discount_amount` DECIMAL(12,2) NULL DEFAULT NULL,
  `banner_image` VARCHAR(255) NULL DEFAULT NULL,
  `starts_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ends_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offers_slug_unique` (`slug`),
  CONSTRAINT `offers_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `offer_products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `offer_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `offer_price` DECIMAL(12,2) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `offer_products_offer_id_product_id_unique` (`offer_id`, `product_id`),
  CONSTRAINT `offer_products_offer_id_foreign` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `offer_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. AD PACKAGES & ADVERTISEMENTS
-- ============================================
CREATE TABLE `ad_packages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `duration_days` INT NOT NULL,
  `homepage_banner` TINYINT(1) NOT NULL DEFAULT 0,
  `top_search_placement` TINYINT(1) NOT NULL DEFAULT 0,
  `featured_badge` TINYINT(1) NOT NULL DEFAULT 0,
  `max_products` INT NOT NULL DEFAULT 1,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ad_packages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `advertisements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `ad_package_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `banner_image` VARCHAR(255) NULL DEFAULT NULL,
  `link` VARCHAR(255) NULL DEFAULT NULL,
  `position` ENUM('homepage_top', 'homepage_middle', 'sidebar', 'search_top', 'category_top') NOT NULL DEFAULT 'homepage_top',
  `status` ENUM('pending', 'active', 'expired', 'rejected') NOT NULL DEFAULT 'pending',
  `starts_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ends_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `clicks` INT NOT NULL DEFAULT 0,
  `impressions` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `advertisements_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `advertisements_ad_package_id_foreign` FOREIGN KEY (`ad_package_id`) REFERENCES `ad_packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. ANNOUNCEMENTS
-- ============================================
CREATE TABLE `announcements` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `type` ENUM('info', 'warning', 'success', 'danger') NOT NULL DEFAULT 'info',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `starts_at` TIMESTAMP NULL DEFAULT NULL,
  `ends_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 14. COMPLAINTS
-- ============================================
CREATE TABLE `complaints` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `shop_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `product_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `screenshot` VARCHAR(255) NULL DEFAULT NULL,
  `status` ENUM('open', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'open',
  `admin_response` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `complaints_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `complaints_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE SET NULL,
  CONSTRAINT `complaints_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 15. BANNERS
-- ============================================
CREATE TABLE `banners` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `link` VARCHAR(255) NULL DEFAULT NULL,
  `position` ENUM('hero', 'middle', 'bottom', 'sidebar') NOT NULL DEFAULT 'hero',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `starts_at` TIMESTAMP NULL DEFAULT NULL,
  `ends_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 16. DELIVERY PARTNERS
-- ============================================
CREATE TABLE `delivery_partners` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  `logo` VARCHAR(255) NULL DEFAULT NULL,
  `coverage_areas` TEXT NULL DEFAULT NULL,
  `base_fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 17. SHOP FOLLOWERS
-- ============================================
CREATE TABLE `shop_followers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `shop_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_followers_user_id_shop_id_unique` (`user_id`, `shop_id`),
  CONSTRAINT `shop_followers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `shop_followers_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- LARAVEL MIGRATIONS TABLE
-- ============================================
CREATE TABLE `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2024_01_01_000001_create_users_table', 1),
('2024_01_01_000002_create_categories_table', 1),
('2024_01_01_000003_create_brands_table', 1),
('2024_01_01_000004_create_shops_table', 1),
('2024_01_01_000005_create_products_table', 1),
('2024_01_01_000006_create_wishlists_table', 1),
('2024_01_01_000007_create_reservations_table', 1),
('2024_01_01_000008_create_reviews_table', 1),
('2024_01_01_000009_create_chats_table', 1),
('2024_01_01_000010_create_notifications_table', 1),
('2024_01_01_000011_create_offers_table', 1),
('2024_01_01_000012_create_advertisements_table', 1),
('2024_01_01_000013_create_announcements_table', 1),
('2024_01_01_000014_create_complaints_table', 1),
('2024_01_01_000015_create_banners_table', 1),
('2024_01_01_000016_create_delivery_partners_table', 1),
('2024_01_01_000017_create_shop_followers_table', 1);

-- ============================================
-- SEED DATA
-- ============================================

-- Super Admin (password: password123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
('Super Admin', 'admin@smartdeals.lk', '$2y$10$aOf6ZoyvhNVMoSAUQ8x1F.i9k9hEQbQBM7eHsEywCMxj8aC8EIjTu', 'super_admin', 'active', NOW(), NOW());

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Smartphones', 'smartphones', 'smartphone', 1, 1, NOW(), NOW()),
(5, 'Accessories', 'accessories', 'headphones', 2, 1, NOW(), NOW()),
(12, 'Tablets', 'tablets', 'tablet', 3, 1, NOW(), NOW()),
(13, 'Laptops', 'laptops', 'laptop', 4, 1, NOW(), NOW()),
(14, 'Gaming Devices', 'gaming-devices', 'gamepad', 5, 1, NOW(), NOW()),
(15, 'Spare Parts', 'spare-parts', 'wrench', 6, 1, NOW(), NOW());

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Brand New', 'brand-new', 1, 1, 1, NOW(), NOW()),
(3, 'Used', 'used', 1, 2, 1, NOW(), NOW()),
(4, 'Refurbished', 'refurbished', 1, 3, 1, NOW(), NOW()),
(6, 'Chargers', 'chargers', 5, 1, 1, NOW(), NOW()),
(7, 'Cases', 'cases', 5, 2, 1, NOW(), NOW()),
(8, 'Earbuds', 'earbuds', 5, 3, 1, NOW(), NOW()),
(9, 'Headphones', 'headphones', 5, 4, 1, NOW(), NOW()),
(10, 'Smart Watches', 'smart-watches', 5, 5, 1, NOW(), NOW()),
(11, 'Power Banks', 'power-banks', 5, 6, 1, NOW(), NOW()),
(16, 'Phone Screens', 'phone-screens', 15, 1, 1, NOW(), NOW());

-- Brands
INSERT INTO `brands` (`name`, `slug`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
('Apple', 'apple', 1, 1, NOW(), NOW()),
('Samsung', 'samsung', 1, 2, NOW(), NOW()),
('Google', 'google', 1, 3, NOW(), NOW()),
('OnePlus', 'oneplus', 1, 4, NOW(), NOW()),
('Xiaomi', 'xiaomi', 1, 5, NOW(), NOW()),
('Huawei', 'huawei', 1, 6, NOW(), NOW()),
('Oppo', 'oppo', 1, 7, NOW(), NOW()),
('Vivo', 'vivo', 1, 8, NOW(), NOW()),
('Realme', 'realme', 1, 9, NOW(), NOW()),
('Nokia', 'nokia', 1, 10, NOW(), NOW()),
('Sony', 'sony', 1, 11, NOW(), NOW()),
('LG', 'lg', 1, 12, NOW(), NOW()),
('Motorola', 'motorola', 1, 13, NOW(), NOW()),
('Nothing', 'nothing', 1, 14, NOW(), NOW());

-- Ad Packages
INSERT INTO `ad_packages` (`name`, `slug`, `description`, `price`, `duration_days`, `homepage_banner`, `top_search_placement`, `featured_badge`, `max_products`, `is_active`, `created_at`, `updated_at`) VALUES
('Silver', 'silver', 'Basic visibility boost', 5000.00, 7, 0, 0, 0, 3, 1, NOW(), NOW()),
('Gold', 'gold', 'Enhanced visibility with homepage placement', 15000.00, 30, 1, 1, 0, 10, 1, NOW(), NOW()),
('Platinum', 'platinum', 'Maximum visibility with all features', 30000.00, 30, 1, 1, 1, 50, 1, NOW(), NOW());

-- Shop Owner 1 (password: password123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `phone`, `district`, `created_at`, `updated_at`) VALUES
(2, 'MobileZone Owner', 'shop@smartdeals.lk', '$2y$10$aOf6ZoyvhNVMoSAUQ8x1F.i9k9hEQbQBM7eHsEywCMxj8aC8EIjTu', 'shop_owner', 'active', '0771234567', 'Colombo', NOW(), NOW());

INSERT INTO `shops` (`id`, `user_id`, `name`, `slug`, `owner_name`, `nic`, `address`, `district`, `phone`, `whatsapp`, `email`, `about`, `status`, `is_verified`, `rating`, `total_reviews`, `total_products`, `opening_hours`, `created_at`, `updated_at`) VALUES
(1, 2, 'MobileZone Colombo', 'mobilezone-colombo', 'Kamal Perera', '200012345678', '123 Galle Road, Colombo 03', 'Colombo', '0771234567', '0771234567', 'shop@smartdeals.lk', 'Leading mobile phone shop in Colombo with 10+ years of experience. We sell brand new and certified pre-owned phones.', 'approved', 1, 4.50, 25, 4, '{"monday":"9:00 AM - 7:00 PM","tuesday":"9:00 AM - 7:00 PM","wednesday":"9:00 AM - 7:00 PM","thursday":"9:00 AM - 7:00 PM","friday":"9:00 AM - 7:00 PM","saturday":"9:00 AM - 5:00 PM","sunday":"Closed"}', NOW(), NOW());

-- Shop Owner 2 (password: password123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `phone`, `district`, `created_at`, `updated_at`) VALUES
(3, 'PhoneWorld Owner', 'shop2@smartdeals.lk', '$2y$10$aOf6ZoyvhNVMoSAUQ8x1F.i9k9hEQbQBM7eHsEywCMxj8aC8EIjTu', 'shop_owner', 'active', '0769876543', 'Kandy', NOW(), NOW());

INSERT INTO `shops` (`id`, `user_id`, `name`, `slug`, `owner_name`, `nic`, `address`, `district`, `phone`, `whatsapp`, `status`, `is_verified`, `rating`, `total_reviews`, `total_products`, `created_at`, `updated_at`) VALUES
(2, 3, 'PhoneWorld Kandy', 'phoneworld-kandy', 'Nuwan Silva', '199812345678', '45 Peradeniya Road, Kandy', 'Kandy', '0769876543', '0769876543', 'approved', 1, 4.20, 18, 2, NOW(), NOW());

-- Customer (password: password123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `phone`, `district`, `created_at`, `updated_at`) VALUES
(4, 'Test Customer', 'customer@smartdeals.lk', '$2y$10$aOf6ZoyvhNVMoSAUQ8x1F.i9k9hEQbQBM7eHsEywCMxj8aC8EIjTu', 'customer', 'active', '0751112233', 'Colombo', NOW(), NOW());

-- Products
INSERT INTO `products` (`shop_id`, `category_id`, `brand_id`, `title`, `slug`, `model`, `condition`, `storage`, `ram`, `color`, `price`, `discount_price`, `warranty`, `warranty_type`, `network_type`, `trcsl_approved`, `box_available`, `cash_price`, `card_price`, `emi_available`, `camera`, `battery`, `processor`, `screen_size`, `five_g_support`, `is_featured`, `stock_quantity`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'iPhone 15 Pro Max 256GB', 'iphone-15-pro-max-256gb', 'iPhone 15 Pro Max', 'brand_new', '256GB', '8GB', 'Natural Titanium', 485000.00, NULL, '1 Year', 'Official Apple Warranty', '5G', 1, 1, 475000.00, 485000.00, 1, '48MP + 12MP + 12MP', '4422mAh', 'A17 Pro', '6.7"', 1, 1, 5, 'active', NOW(), NOW()),

(1, 1, 2, 'Samsung Galaxy S25 Ultra 512GB', 'samsung-galaxy-s25-ultra-512gb', 'Galaxy S25 Ultra', 'brand_new', '512GB', '12GB', 'Titanium Black', 425000.00, 399000.00, '1 Year', 'Distributor Warranty', '5G', 1, 1, NULL, NULL, 1, '200MP + 50MP + 12MP + 10MP', '5000mAh', 'Snapdragon 8 Elite', '6.9"', 1, 1, 3, 'active', NOW(), NOW()),

(1, 1, 1, 'iPhone 14 128GB - Used', 'iphone-14-128gb-used', 'iPhone 14', 'used', '128GB', '6GB', 'Blue', 180000.00, 165000.00, NULL, NULL, NULL, 1, 0, NULL, NULL, 0, '12MP + 12MP', '3279mAh', 'A15 Bionic', '6.1"', 0, 0, 1, 'active', NOW(), NOW()),

(2, 1, 3, 'Google Pixel 9 Pro 256GB', 'google-pixel-9-pro-256gb', 'Pixel 9 Pro', 'brand_new', '256GB', '16GB', 'Obsidian', 320000.00, NULL, '1 Year', NULL, '5G', 1, 1, NULL, NULL, 0, '50MP + 48MP + 48MP', '4700mAh', 'Tensor G4', '6.3"', 1, 1, 4, 'active', NOW(), NOW()),

(2, 1, 2, 'Samsung Galaxy A55 5G 128GB', 'samsung-galaxy-a55-5g-128gb', 'Galaxy A55', 'brand_new', '128GB', '8GB', 'Awesome Iceblue', 95000.00, 89000.00, '1 Year', NULL, '5G', 1, 1, NULL, NULL, 1, '50MP + 12MP + 5MP', '5000mAh', 'Exynos 1480', '6.6"', 1, 0, 10, 'active', NOW(), NOW()),

(1, 1, 1, 'iPhone 13 Mini 128GB - Refurbished', 'iphone-13-mini-128gb-refurbished', 'iPhone 13 Mini', 'refurbished', '128GB', '4GB', 'Midnight', 120000.00, NULL, '3 Months', 'Shop Warranty', NULL, 0, 0, NULL, NULL, 0, '12MP + 12MP', '2438mAh', 'A15 Bionic', '5.4"', 0, 0, 2, 'active', NOW(), NOW());

-- Update used phone specific fields
UPDATE `products` SET `battery_health` = '89%', `scratches` = 'Minor scratches on back', `face_id_working` = 1, `original_display` = 1 WHERE `slug` = 'iphone-14-128gb-used';
UPDATE `products` SET `battery_health` = '95%', `face_id_working` = 1, `original_display` = 1 WHERE `slug` = 'iphone-13-mini-128gb-refurbished';

-- Banner
INSERT INTO `banners` (`title`, `image`, `position`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
('Welcome to SmartDeals.lk', 'banners/hero-1.jpg', 'hero', 1, 1, NOW(), NOW());

-- ============================================
-- DONE! Your SmartDeals.lk database is ready.
-- ============================================
