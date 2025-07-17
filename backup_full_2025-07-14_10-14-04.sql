-- Database Backup
-- Created: 2025-07-14 10:14:04
-- Type: Full


-- Table structure for `categories`
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `categories`
INSERT INTO `categories` VALUES ('1', 'Electronics', 'Electronic devices and gadgets', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('2', 'Clothing', 'Clothing and fashion items', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('3', 'Home & Garden', 'Home improvement and garden items', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('4', 'Books', 'Books and educational materials', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('5', 'Sports', 'Sports and fitness equipment', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('6', 'Food & Beverages', 'Food items and beverages', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('7', 'Health & Beauty', 'Health and beauty products', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('8', 'Automotive', 'Car parts and automotive accessories', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('9', 'Toys & Games', 'Toys and games for children', NULL, 'active', '2025-07-08 18:55:11');
INSERT INTO `categories` VALUES ('10', 'Art & Crafts', 'Art supplies and handmade crafts', NULL, 'active', '2025-07-08 18:55:11');


-- Table structure for `category_translations`
DROP TABLE IF EXISTS `category_translations`;
CREATE TABLE `category_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `language_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_category_language` (`category_id`,`language_code`),
  KEY `idx_language` (`language_code`),
  CONSTRAINT `category_translations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `category_translations`
INSERT INTO `category_translations` VALUES ('1', '1', 'en', 'Electronics', 'Electronic devices and gadgets', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('2', '1', 'rw', 'Ibikoresho bya Elegitoronike', 'Ibikoresho bya elegitoronike n\'ibindi', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('3', '1', 'fr', 'Électronique', 'Appareils électroniques et gadgets', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('4', '2', 'en', 'Clothing', 'Clothing and fashion items', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('5', '2', 'rw', 'Imyambaro', 'Imyambaro n\'ibindi byerekeye imyambarire', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('6', '2', 'fr', 'Vêtements', 'Vêtements et articles de mode', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('7', '3', 'en', 'Home & Garden', 'Home improvement and garden items', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('8', '3', 'rw', 'Inzu n\'Ubusitani', 'Ibikoresho byo mu nzu no mu busitani', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('9', '3', 'fr', 'Maison et Jardin', 'Amélioration de la maison et articles de jardin', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('10', '4', 'en', 'Books', 'Books and educational materials', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('11', '4', 'rw', 'Ibitabo', 'Ibitabo n\'ibikoresho by\'uburezi', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('12', '4', 'fr', 'Livres', 'Livres et matériel éducatif', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('13', '5', 'en', 'Sports', 'Sports and fitness equipment', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('14', '5', 'rw', 'Siporo', 'Ibikoresho bya siporo n\'ubuzima', '2025-07-08 18:55:12');
INSERT INTO `category_translations` VALUES ('15', '5', 'fr', 'Sports', 'Équipements de sport et de fitness', '2025-07-08 18:55:12');


-- Table structure for `google_translation_cache`
DROP TABLE IF EXISTS `google_translation_cache`;
CREATE TABLE `google_translation_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_language` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translated_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_translation` (`original_text`(255),`source_language`,`target_language`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `google_translation_cache`
INSERT INTO `google_translation_cache` VALUES ('1', 'Bag', 'en', 'fr', 'Bag', '2025-07-14 11:02:53', '2025-07-14 11:02:53');
INSERT INTO `google_translation_cache` VALUES ('2', 'we have a good cloths from young to above', 'en', 'fr', 'we have a good cloths from young to above', '2025-07-14 11:02:53', '2025-07-14 11:02:53');
INSERT INTO `google_translation_cache` VALUES ('3', 'Art &amp; Crafts', 'en', 'fr', 'Art &amp; Crafts', '2025-07-14 11:02:53', '2025-07-14 11:02:53');
INSERT INTO `google_translation_cache` VALUES ('4', 'Cloths', 'en', 'fr', 'Cloths', '2025-07-14 11:02:54', '2025-07-14 11:02:54');
INSERT INTO `google_translation_cache` VALUES ('5', 'we have a good cloths from young to abover', 'en', 'fr', 'we have a good cloths from young to abover', '2025-07-14 11:02:54', '2025-07-14 11:02:54');
INSERT INTO `google_translation_cache` VALUES ('6', 'Clothing', 'en', 'fr', 'Vêtements', '2025-07-14 11:02:54', '2025-07-14 11:02:54');
INSERT INTO `google_translation_cache` VALUES ('7', 'shoes', 'en', 'fr', 'shoes', '2025-07-14 11:02:54', '2025-07-14 11:02:54');
INSERT INTO `google_translation_cache` VALUES ('8', 'We have a good shoes for every one', 'en', 'fr', 'We have a good shoes for every one', '2025-07-14 11:02:54', '2025-07-14 11:02:54');
INSERT INTO `google_translation_cache` VALUES ('9', 'Sports', 'en', 'fr', 'Sports', '2025-07-14 11:02:55', '2025-07-14 11:02:55');
INSERT INTO `google_translation_cache` VALUES ('10', 'Bag', 'en', 'rw', 'Bag', '2025-07-14 11:10:56', '2025-07-14 11:10:56');
INSERT INTO `google_translation_cache` VALUES ('11', 'we have a good cloths from young to above', 'en', 'rw', 'we have a good cloths from young to above', '2025-07-14 11:10:56', '2025-07-14 11:10:56');
INSERT INTO `google_translation_cache` VALUES ('12', 'Art &amp; Crafts', 'en', 'rw', 'Art &amp; Crafts', '2025-07-14 11:10:56', '2025-07-14 11:10:56');
INSERT INTO `google_translation_cache` VALUES ('13', 'Cloths', 'en', 'rw', 'Cloths', '2025-07-14 11:10:57', '2025-07-14 11:10:57');
INSERT INTO `google_translation_cache` VALUES ('14', 'we have a good cloths from young to abover', 'en', 'rw', 'we have a good cloths from young to abover', '2025-07-14 11:10:57', '2025-07-14 11:10:57');
INSERT INTO `google_translation_cache` VALUES ('15', 'Clothing', 'en', 'rw', 'Imyambaro', '2025-07-14 11:10:57', '2025-07-14 11:10:57');
INSERT INTO `google_translation_cache` VALUES ('16', 'shoes', 'en', 'rw', 'shoes', '2025-07-14 11:10:57', '2025-07-14 11:10:57');
INSERT INTO `google_translation_cache` VALUES ('17', 'We have a good shoes for every one', 'en', 'rw', 'We have a good shoes for every one', '2025-07-14 11:10:57', '2025-07-14 11:10:57');
INSERT INTO `google_translation_cache` VALUES ('18', 'Sports', 'en', 'rw', 'Siporo', '2025-07-14 11:10:57', '2025-07-14 11:10:57');


-- Table structure for `messages`
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `product_id` (`product_id`),
  KEY `order_id` (`order_id`),
  KEY `idx_is_read` (`is_read`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `messages_ibfk_4` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table structure for `notifications`
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('new_order','status_update','new_message','payment_received') NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

-- Data for table `notifications`
INSERT INTO `notifications` VALUES ('4', '7', 'new_message', 'New message from MUVANDIMWE Marie Divine regarding Order #10', '10', '0', '2025-07-12 09:40:33');
INSERT INTO `notifications` VALUES ('5', '7', 'new_message', 'New message from MUVANDIMWE Marie Divine regarding Order #10', '10', '0', '2025-07-12 09:41:57');
INSERT INTO `notifications` VALUES ('6', '5', 'status_update', 'Order #10 status updated to: Payment received', '10', '0', '2025-07-12 10:00:03');
INSERT INTO `notifications` VALUES ('7', '5', 'status_update', 'Order #9 status updated to: Payment received', '9', '0', '2025-07-12 10:02:26');
INSERT INTO `notifications` VALUES ('8', '5', 'status_update', 'Order #11 status updated to: Delivered', '11', '0', '2025-07-12 10:03:25');
INSERT INTO `notifications` VALUES ('9', '7', 'new_order', 'New order received! Product: shoes, Quantity: 3, Total: 30,000 RWF', '13', '0', '2025-07-12 10:23:01');
INSERT INTO `notifications` VALUES ('10', '7', 'new_order', 'New order received! Product: Cloths, Quantity: 1, Total: 30,000 RWF', '14', '0', '2025-07-12 10:46:23');
INSERT INTO `notifications` VALUES ('11', '7', 'new_order', 'New order received! Order #14 - Product: Cloths - Quantity: 1 - Payment method: Bank transfer - Total: 30,000 RWF', '14', '0', '2025-07-12 10:46:44');
INSERT INTO `notifications` VALUES ('12', '7', 'new_message', 'New message from MUVANDIMWE Marie Divine regarding Order #11', '11', '0', '2025-07-12 10:48:48');
INSERT INTO `notifications` VALUES ('13', '5', 'status_update', 'Order #13 status updated to: Payment received', '13', '0', '2025-07-12 11:00:27');
INSERT INTO `notifications` VALUES ('14', '5', 'new_message', 'New message from KAMIKAZI Peace Fathia regarding Order #9', '9', '0', '2025-07-12 11:01:09');
INSERT INTO `notifications` VALUES ('15', '5', 'status_update', 'Order #9 status updated to: Payment received', '9', '0', '2025-07-12 11:01:20');
INSERT INTO `notifications` VALUES ('16', '7', 'new_order', 'New order received! Product: Cloths, Quantity: 2, Total: 60,000 RWF', '15', '0', '2025-07-13 08:15:08');
INSERT INTO `notifications` VALUES ('17', '7', 'new_order', 'New order received! Order #15 - Product: Cloths - Quantity: 2 - Payment method: Bank transfer - Total: 60,000 RWF', '15', '0', '2025-07-13 08:15:51');
INSERT INTO `notifications` VALUES ('18', '7', 'new_message', 'New message from MUVANDIMWE Marie Divine regarding Order #15', '15', '0', '2025-07-13 08:16:14');
INSERT INTO `notifications` VALUES ('19', '7', 'new_order', 'New order received! Product: shoes, Quantity: 2, Total: 20,000 RWF', '16', '0', '2025-07-13 08:18:01');
INSERT INTO `notifications` VALUES ('20', '7', 'new_order', 'New order received! Order #16 - Product: shoes - Quantity: 2 - Payment method: Bank transfer - Total: 20,000 RWF', '16', '0', '2025-07-13 08:18:15');
INSERT INTO `notifications` VALUES ('21', '5', '', 'Payment confirmed for Order #16', '16', '0', '2025-07-13 08:22:46');
INSERT INTO `notifications` VALUES ('22', '5', 'new_message', 'New message from KAMIKAZI Peace Fathia regarding Order #15', '15', '0', '2025-07-13 08:23:27');
INSERT INTO `notifications` VALUES ('23', '5', 'status_update', 'Order #15 status updated to: Delivered', '15', '0', '2025-07-13 08:24:08');


-- Table structure for `order_messages`
DROP TABLE IF EXISTS `order_messages`;
CREATE TABLE `order_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `message_type` enum('general','delivery','payment','status_update') DEFAULT 'general',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `sender_id` (`sender_id`),
  CONSTRAINT `order_messages_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

-- Data for table `order_messages`
INSERT INTO `order_messages` VALUES ('4', '10', '5', 'hello', 'general', '1', '2025-07-12 09:40:33');
INSERT INTO `order_messages` VALUES ('5', '10', '5', 'i want 20pcs', 'general', '1', '2025-07-12 09:41:57');
INSERT INTO `order_messages` VALUES ('6', '10', '7', 'Order status updated to: Payment received', 'status_update', '0', '2025-07-12 10:00:03');
INSERT INTO `order_messages` VALUES ('7', '9', '7', 'Order status updated to: Payment received - THANKS', 'status_update', '0', '2025-07-12 10:02:25');
INSERT INTO `order_messages` VALUES ('8', '11', '7', 'Order status updated to: Delivered', 'status_update', '1', '2025-07-12 10:03:25');
INSERT INTO `order_messages` VALUES ('9', '11', '5', 'Hello', 'general', '0', '2025-07-12 10:48:48');
INSERT INTO `order_messages` VALUES ('10', '13', '7', 'Order status updated to: Payment received - THANKS', 'status_update', '0', '2025-07-12 11:00:27');
INSERT INTO `order_messages` VALUES ('11', '9', '7', 'Hello', 'general', '0', '2025-07-12 11:01:09');
INSERT INTO `order_messages` VALUES ('12', '9', '7', 'Order status updated to: Payment received - THANKS', 'status_update', '0', '2025-07-12 11:01:20');
INSERT INTO `order_messages` VALUES ('13', '15', '5', 'HELO', 'general', '1', '2025-07-13 08:16:14');
INSERT INTO `order_messages` VALUES ('14', '16', '7', 'Payment confirmed by seller. Amount: 20,000 FRW - Notes: thanks', '', '0', '2025-07-13 08:22:46');
INSERT INTO `order_messages` VALUES ('15', '15', '7', 'hello', 'general', '0', '2025-07-13 08:23:27');
INSERT INTO `order_messages` VALUES ('16', '15', '7', 'Order status updated to: Delivered - THANKS', 'status_update', '0', '2025-07-13 08:24:08');


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


-- Table structure for `product_translations`
DROP TABLE IF EXISTS `product_translations`;
CREATE TABLE `product_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `language_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_language` (`product_id`,`language_code`),
  KEY `idx_language` (`language_code`),
  FULLTEXT KEY `name` (`name`,`description`),
  CONSTRAINT `product_translations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `product_translations`
INSERT INTO `product_translations` VALUES ('21', '12', 'rw', 'shoes', 'We have a good shoes for every one', '2025-07-12 10:49:27');
INSERT INTO `product_translations` VALUES ('22', '13', 'rw', 'Cloths', 'we have a good cloths from young to abover', '2025-07-12 10:49:27');


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


-- Table structure for `reviews`
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `order_id` (`order_id`),
  KEY `idx_rating` (`rating`),
  KEY `idx_status` (`status`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Table structure for `settings`
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `settings`
INSERT INTO `settings` VALUES ('1', 'site_name', 'Local Language Commerce System', 'Website name', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('2', 'site_description', 'A Web-Based Local Language Support System for International Commerce', 'Website description', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('3', 'default_language', 'en', 'Default system language', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('4', 'currency_symbol', '$', 'Default currency symbol', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('5', 'currency_code', 'USD', 'Default currency code', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('6', 'items_per_page', '12', 'Number of items to display per page', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('7', 'max_upload_size', '5242880', 'Maximum file upload size in bytes (5MB)', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('8', 'allowed_image_types', 'jpg,jpeg,png,gif', 'Allowed image file extensions', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('9', 'site_email', 'admin@localcommerce.com', 'Site contact email', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('10', 'enable_reviews', '1', 'Enable product reviews (1=yes, 0=no)', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('11', 'enable_messaging', '1', 'Enable user messaging (1=yes, 0=no)', '2025-07-08 18:55:12');
INSERT INTO `settings` VALUES ('12', 'require_email_verification', '0', 'Require email verification for new accounts (1=yes, 0=no)', '2025-07-08 18:55:12');


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
) ENGINE=InnoDB AUTO_INCREMENT=15318 DEFAULT CHARSET=utf8mb4;

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

