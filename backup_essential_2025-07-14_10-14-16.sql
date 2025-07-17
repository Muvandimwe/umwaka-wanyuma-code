-- Database Backup
-- Created: 2025-07-14 10:14:16
-- Type: Essential


-- Table structure for `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_type` enum('seller','buyer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `preferred_language` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT 'en',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_user_type` (`user_type`),
  KEY `idx_status` (`status`),
  KEY `idx_users_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `users`
INSERT INTO `users` VALUES ('5', 'MUVANDIMWE', 'muvandimwedivine123@gmail.com', '$2y$10$zQ9EAvPCJvjCXptSpndWH.sAVw5mQQLlz1n2MS53sMghf47kHDAtC', 'MUVANDIMWE Marie Divine', '0789307856', 'MUHANGA', 'MUHANGA', 'Rwanda', 'buyer', 'active', '2025-07-08 18:59:47', '2025-07-14 10:25:12', 'rw');
INSERT INTO `users` VALUES ('6', 'UWERA', 'uweragloriose@gmail.com', '$2y$10$N8YJr7OpXXXYRN8/x4xjiucceKa5Rm9bwOJj3F1.MCp83rqBqldfq', 'UWERA Gloriose', '0782657394', 'MUSANZE', 'MUSANZE', 'RWANDA', 'seller', 'active', '2025-07-08 19:02:53', '2025-07-08 19:02:53', 'en');
INSERT INTO `users` VALUES ('7', 'Fathia', 'fathia@gmail.com', '$2y$10$i.eZPOTAt7T2zcMKQaGTDuHiKGdA5UncquGIDQ7ksYcAaBhfkVI/S', 'KAMIKAZI Peace Fathia', '0788835334', 'Rwanda/Amajyepfo/Muhanga/Nyamabuye', 'Muhanga', 'Rwanda', 'seller', 'active', '2025-07-12 09:14:44', '2025-07-14 08:56:33', 'rw');


-- Table structure for `products`
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `status` enum('active','inactive','out_of_stock') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `featured` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_rw` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_fr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `idx_status` (`status`),
  KEY `idx_featured` (`featured`),
  KEY `idx_category` (`category`),
  KEY `idx_price` (`price`),
  KEY `idx_products_name` (`name`),
  KEY `idx_products_created_at` (`created_at`),
  FULLTEXT KEY `name` (`name`,`description`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `products`
INSERT INTO `products` VALUES ('12', '7', 'shoes', 'We have a good shoes for every one', '10000.00', '6871fe4651409_1752301126.PNG', 'Sports', '93', 'active', '0', '0', '2025-07-12 08:18:46', '2025-07-13 08:18:01', NULL, NULL, NULL, NULL);
INSERT INTO `products` VALUES ('13', '7', 'Cloths', 'we have a good cloths from young to abover', '30000.00', '6871fede80097_1752301278.PNG', 'Clothing', '65', 'active', '0', '0', '2025-07-12 08:21:18', '2025-07-13 08:15:08', NULL, NULL, NULL, NULL);
INSERT INTO `products` VALUES ('14', '7', 'Bag', 'we have a good cloths from young to above', '30000.00', '6874a18279fad_1752473986.PNG', 'Art &amp; Crafts', '80', 'active', '0', '0', '2025-07-14 08:19:46', '2025-07-14 09:19:46', NULL, NULL, NULL, NULL);


-- Table structure for `orders`
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buyer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','payment_pending','confirmed','payment_received','payment_disputed','preparing','shipped','delivered','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `shipping_address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_confirmed_at` timestamp NULL DEFAULT NULL,
  `payment_confirmed_amount` decimal(10,2) DEFAULT NULL,
  `payment_notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `product_id` (`product_id`),
  KEY `seller_id` (`seller_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_order_date` (`order_date`),
  KEY `idx_orders_total_price` (`total_price`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `orders`
INSERT INTO `orders` VALUES ('9', '5', '12', '7', '1', '0.00', '10000.00', 'payment_received', 'pending', NULL, NULL, '2025-07-12 09:36:30', '2025-07-12 10:02:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `orders` VALUES ('10', '5', '13', '7', '1', '0.00', '30000.00', 'payment_received', 'pending', NULL, NULL, '2025-07-12 09:38:34', '2025-07-12 10:00:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `orders` VALUES ('11', '5', '13', '7', '1', '0.00', '30000.00', 'delivered', 'pending', NULL, NULL, '2025-07-12 09:49:31', '2025-07-12 10:03:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `orders` VALUES ('12', '5', '12', '7', '1', '0.00', '10000.00', 'pending', 'pending', NULL, NULL, '2025-07-12 10:14:26', '2025-07-12 10:14:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `orders` VALUES ('13', '5', '12', '7', '3', '0.00', '30000.00', 'payment_received', 'pending', NULL, NULL, '2025-07-12 10:23:01', '2025-07-12 11:00:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `orders` VALUES ('14', '5', '13', '7', '1', '0.00', '30000.00', 'payment_pending', 'pending', NULL, NULL, '2025-07-12 10:46:23', '2025-07-12 10:46:44', 'bank_transfer', '{\"phone\":\"\",\"account_number\":\"4008100747030\",\"bank_name\":\"BK\",\"wallet_id\":\"\"}', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `orders` VALUES ('15', '5', '13', '7', '2', '0.00', '60000.00', 'delivered', 'pending', NULL, NULL, '2025-07-13 08:15:08', '2025-07-13 08:24:08', 'bank_transfer', '{\"phone\":\"\",\"account_number\":\"4008100747030\",\"bank_name\":\"BK\",\"wallet_id\":\"\"}', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `orders` VALUES ('16', '5', '12', '7', '2', '0.00', '20000.00', 'payment_received', 'pending', NULL, NULL, '2025-07-13 08:18:01', '2025-07-13 08:22:46', 'bank_transfer', '{\"phone\":\"\",\"account_number\":\"4008100747030\",\"bank_name\":\"BK\",\"wallet_id\":\"\"}', NULL, NULL, '2025-07-13 08:22:46', '20000.00', 'thanks');


-- Table structure for `system_settings`
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=15335 DEFAULT CHARSET=utf8mb4;

-- Data for table `system_settings`
INSERT INTO `system_settings` VALUES ('1', 'site_name', 'International Commerce', 'string', 'Name of the website', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('2', 'site_description', 'Your trusted e-commerce platform', 'string', 'Site description for SEO', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('3', 'admin_email', 'admin@internationalcommerce.com', 'string', 'Primary admin email', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('4', 'currency', 'FRW', 'string', 'Default currency', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('5', 'timezone', 'Africa/Kigali', 'string', 'Default timezone', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('6', 'items_per_page', '12', 'number', 'Products per page', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('7', 'max_upload_size', '5', 'number', 'Max upload size in MB', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('8', 'allow_registration', '1', 'boolean', 'Allow user registration', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('9', 'require_email_verification', '0', 'boolean', 'Require email verification', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('10', 'maintenance_mode', '0', 'boolean', 'Maintenance mode status', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('11', 'google_translate_enabled', '1', 'boolean', 'Google Translate enabled', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('12', 'auto_approve_products', '0', 'boolean', 'Auto-approve new products', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('13', 'low_stock_threshold', '5', 'number', 'Low stock warning threshold', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('14', 'order_auto_complete_days', '7', 'number', 'Days to auto-complete orders', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('15', 'backup_frequency', 'weekly', 'string', 'Backup frequency', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('16', 'email_notifications', '1', 'boolean', 'Email notifications enabled', '2025-07-10 10:30:35', '2025-07-10 10:30:35');
INSERT INTO `system_settings` VALUES ('17', 'sms_notifications', '0', 'boolean', 'SMS notifications enabled', '2025-07-10 10:30:35', '2025-07-10 10:30:35');

