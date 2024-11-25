-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: hht_appleshop
-- ------------------------------------------------------
-- Server version	9.1.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'hiếu ngu.com.vn'),(2,'hiếu ngu.com'),(3,'hiếu ngu.com'),(4,'hiếu ngu.com'),(5,'hiếu ngu.com'),(6,'hiếu ngu.com');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon`
--

DROP TABLE IF EXISTS `coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon` (
  `coupon_id` int NOT NULL AUTO_INCREMENT,
  `code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_actived` int NOT NULL DEFAULT '0',
  `discount_percent` int NOT NULL DEFAULT '0',
  `times_available` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon`
--

LOCK TABLES `coupon` WRITE;
/*!40000 ALTER TABLE `coupon` DISABLE KEYS */;
INSERT INTO `coupon` VALUES (1,'TEST','20% off on all items','2024-11-01 00:00:00','2024-12-31 00:00:00',1,50,9);
/*!40000 ALTER TABLE `coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_item`
--

DROP TABLE IF EXISTS `order_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_item` (
  `product_id` int NOT NULL,
  `order_id` int NOT NULL,
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `quantity` int DEFAULT NULL,
  `unit_price` decimal(8,2) DEFAULT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`order_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_item`
--

LOCK TABLES `order_item` WRITE;
/*!40000 ALTER TABLE `order_item` DISABLE KEYS */;
INSERT INTO `order_item` VALUES (1,2,1,10,0.00,0.00),(3,2,2,4,0.00,0.00),(1,7,3,10,100.00,1000.00),(3,7,4,4,100.00,400.00),(1,9,7,10,100.00,1000.00),(3,9,8,4,100.00,400.00);
/*!40000 ALTER TABLE `order_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_address_id` int NOT NULL,
  `user_id` int NOT NULL,
  `coupon_id` int DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `shipping_cost` decimal(8,2) DEFAULT NULL,
  `is_actived` int DEFAULT NULL,
  `payment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `total_amount` decimal(18,2) DEFAULT '0.00',
  `status` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (2,1,1,NULL,'2024-11-25 16:01:16',NULL,NULL,'string',0.00,'Pending'),(7,1,1,NULL,'2024-11-25 16:08:47',NULL,1,'string',1400.00,'Pending'),(9,1,1,1,'2024-11-25 17:08:04',NULL,1,'string',700.00,'Pending');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `product_name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `price` decimal(18,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(18,2) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `is_actived` bit(1) DEFAULT NULL,
  `color` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `image_url` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,5,'Sample Product 1','The latest iPhone model with A16 Bionic chip',1299.99,100.00,50,_binary '','Features: 6.1-inch display, 48MP camera, iOS 17',NULL),(2,5,'string','The latest iPhone model with A16 Bionic chip',1299.99,100.00,50,_binary '','Features: 6.1-inch display, 48MP camera, iOS 17',NULL),(3,5,'string','The latest iPhone model with A16 Bionic chip',1299.99,100.00,50,_binary '','Features: 6.1-inch display, 48MP camera, iOS 17',NULL),(4,5,'string','The latest iPhone model with A16 Bionic chip',1299.99,100.00,50,_binary '','Features: 6.1-inch display, 48MP camera, iOS 17',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_address`
--

DROP TABLE IF EXISTS `user_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_address` (
  `user_address_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `ward` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `district` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `province` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tel` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `is_actived` int DEFAULT NULL,
  PRIMARY KEY (`user_address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_address`
--

LOCK TABLES `user_address` WRITE;
/*!40000 ALTER TABLE `user_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `phone_number` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_of_birth` datetime DEFAULT NULL,
  `img_avatar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `otp` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `otp_expiration` datetime DEFAULT NULL,
  `roles` int DEFAULT NULL,
  `is_actived` bit(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (22,'user_ZxdTFm30sF',NULL,NULL,'assets/user-img/user_img_4e80e5.png','$2y$12$kpN13XFBvsGKi3xBgww8k.aKHDHY9P8sgTdmYtqkUP3dDA0y5GgJ.','hoanghung52304@gmail.com','Y1HYVY','2024-11-24 16:36:22',0,_binary '\0','2024-11-23 13:29:22'),(25,'ytJl1qfxOj',NULL,NULL,NULL,'$2y$12$T.xP8TfU6mU7fjySQv95NuOJ.jyFMjZPJ2bb8lIeYkUSLrbfMRuAe','tovantoi2003@gmail.com','KU7UI8','2024-11-23 15:32:18',0,_binary '','2024-11-23 15:30:48'),(26,'user_XJ5Et7Kw6s',NULL,NULL,NULL,'$2y$12$I0.529330.2UieJTcoyeh.tF5sKUM/xmlsOs4yQdnJg4tZ/gwfd22','tovantoi2003@gmail.com','EQBHLT','2024-11-25 15:57:02',0,_binary '\0','2024-11-23 16:17:28');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-26  0:13:16
