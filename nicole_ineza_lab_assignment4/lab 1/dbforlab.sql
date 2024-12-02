-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
-- Host: localhost
-- Generation Time: Jun 13, 2022 at 02:42 PM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Character set and collation settings
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Create the database if it does not exist
CREATE DATABASE IF NOT EXISTS `dbforlab` CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Select the database
USE `dbforlab`;

-- --------------------------------------------------------
-- Table structure for `brands`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `brands` (
  `brand_id` INT(11) NOT NULL AUTO_INCREMENT,
  `brand_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `categories`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `customer`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(50) NOT NULL,
  `customer_pass` VARCHAR(150) NOT NULL,
  `customer_country` VARCHAR(30) NOT NULL,
  `customer_city` VARCHAR(30) NOT NULL,
  `customer_contact` VARCHAR(15) NOT NULL,
  `customer_image` VARCHAR(100) DEFAULT NULL,
  `user_role` INT(11) NOT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `products`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_cat` INT(11) NOT NULL,
  `product_brand` INT(11) NOT NULL,
  `product_title` VARCHAR(200) NOT NULL,
  `product_price` DOUBLE NOT NULL,
  `product_desc` VARCHAR(500) DEFAULT NULL,
  `product_image` VARCHAR(100) DEFAULT NULL,
  `product_keywords` VARCHAR(100) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `product_cat` (`product_cat`),
  KEY `product_brand` (`product_brand`),
  FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_brand`) REFERENCES `brands` (`brand_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `cart`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart` (
  `p_id` INT(11) NOT NULL,
  `ip_add` VARCHAR(50) NOT NULL,
  `c_id` INT(11) DEFAULT NULL,
  `qty` INT(11) NOT NULL,
  KEY `p_id` (`p_id`),
  KEY `c_id` (`c_id`),
  FOREIGN KEY (`p_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`c_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `orders`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `invoice_no` INT(11) NOT NULL,
  `order_date` DATE NOT NULL,
  `order_status` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `orderdetails`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orderdetails` (
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL,
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `payment`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payment` (
  `pay_id` INT(11) NOT NULL AUTO_INCREMENT,
  `amt` DOUBLE NOT NULL,
  `customer_id` INT(11) NOT NULL,
  `order_id` INT(11) NOT NULL,
  `currency` VARCHAR(10) NOT NULL,
  `payment_date` DATE NOT NULL,
  PRIMARY KEY (`pay_id`),
  KEY `customer_id` (`customer_id`),
  KEY `order_id` (`order_id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;
-- Adding services table to dbforlab database

CREATE TABLE IF NOT EXISTS `services` (
  `service_id` INT(11) NOT NULL AUTO_INCREMENT,
  `service_name` VARCHAR(100) NOT NULL,
  `service_description` TEXT NOT NULL,
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB 
CHARACTER SET=utf8mb4 
COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------
-- AUTO_INCREMENT for dumped tables
-- --------------------------------------------------------
ALTER TABLE `brands`
  MODIFY `brand_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
  MODIFY `cat_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `customer`
  MODIFY `customer_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `orders`
  MODIFY `order_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `payment`
  MODIFY `pay_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `products`
  MODIFY `product_id` INT(11) NOT NULL AUTO_INCREMENT;

COMMIT;

-- Restore previous settings
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
