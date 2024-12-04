-- Create database
CREATE DATABASE IF NOT EXISTS `imena` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `imena`;

-- Users table (combining customers and sellers)
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(100) NOT NULL,
  `user_email` VARCHAR(50) NOT NULL UNIQUE,
  `user_password` VARCHAR(150) NOT NULL,
  `user_role` ENUM('customer', 'seller') DEFAULT 'customer',
  `store_name` VARCHAR(100) DEFAULT NULL, -- For sellers only
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Products table (linked to categories and sellers via `user_id`)
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` INT(11) NOT NULL AUTO_INCREMENT,
  `seller_id` INT(11) NOT NULL, -- Reference to the seller who added the product
  `product_cat` INT(11) NOT NULL, -- Reference to category
  `product_title` VARCHAR(200) NOT NULL,
  `product_price` DOUBLE NOT NULL,
  `product_desc` TEXT DEFAULT NULL,
  `product_image` BLOB DEFAULT NULL, -- Store image data as BLOB (binary large object)
  `product_keywords` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL, -- Reference to customer
  `order_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_status` VARCHAR(100) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`order_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Order Details table (products linked to orders)
CREATE TABLE IF NOT EXISTS `orderdetails` (
  `orderdetail_id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL, -- Reference to order
  `product_id` INT(11) NOT NULL, -- Reference to product
  `quantity` INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`orderdetail_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Shipping table (specific to an order or invoice)
CREATE TABLE IF NOT EXISTS `shipping` (
  `shipping_id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL, -- Reference to order
  `address_line1` VARCHAR(255) NOT NULL,
  `address_line2` VARCHAR(255) DEFAULT NULL,
  `city` VARCHAR(100) NOT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `postal_code` VARCHAR(20) NOT NULL,
  `country` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`shipping_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Payments table
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL, -- Reference to order
  `payment_amount` DOUBLE NOT NULL,
  `payment_method` VARCHAR(50) NOT NULL,
  `payment_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Cart table
CREATE TABLE IF NOT EXISTS `cart` (
  `cart_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL, -- Reference to customer
  `product_id` INT(11) NOT NULL, -- Reference to product
  `quantity` INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cart_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `orders`
ADD COLUMN `payment_reference` VARCHAR(255) DEFAULT NULL AFTER `order_status`;
ALTER TABLE `orderdetails`
ADD COLUMN `payment_reference` VARCHAR(255) DEFAULT NULL AFTER `quantity`;
