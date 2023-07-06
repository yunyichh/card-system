-- MySQL dump 10.13  Distrib 5.7.26, for Win64 (x86_64)
--
-- Host: localhost    Database: card-system
-- ------------------------------------------------------
-- Server version	5.7.26

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `card_order`
--

DROP TABLE IF EXISTS `card_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `card_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `card_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `card_order_order_id_index` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `card_order`
--

LOCK TABLES `card_order` WRITE;
/*!40000 ALTER TABLE `card_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `card_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cards`
--

DROP TABLE IF EXISTS `cards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `card` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `count_sold` int(11) NOT NULL DEFAULT '0',
  `count_all` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cards_user_id_index` (`user_id`),
  KEY `cards_product_id_index` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cards`
--

LOCK TABLES `cards` WRITE;
/*!40000 ALTER TABLE `cards` DISABLE KEYS */;
INSERT INTO `cards` VALUES (1,1,1,'11111',0,0,0,1,'2023-07-06 02:25:18',NULL,NULL),(2,1,1,'11112',0,1,1,1,'2023-07-06 02:25:18',NULL,NULL),(3,1,1,'11113',0,0,0,1,'2023-07-06 02:25:18',NULL,NULL),(4,1,2,'123456',1,1,2,100,'2023-07-06 02:25:18',NULL,NULL),(5,1,3,'123456123456',0,0,0,1,'2023-07-06 03:28:44',NULL,NULL);
/*!40000 ALTER TABLE `cards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '1000',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_open` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,1,'测试分组2',1000,NULL,0,1,'2023-07-06 02:25:18','2023-07-06 03:15:39'),(2,1,'测试分组1',999,NULL,0,1,'2023-07-06 02:25:18','2023-07-06 03:15:50'),(3,1,'测试分组3密码',1000,'123456',1,1,'2023-07-06 02:25:18','2023-07-06 03:15:06');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT '-1',
  `product_id` int(11) NOT NULL DEFAULT '-1',
  `type` int(11) NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0',
  `coupon` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` int(11) NOT NULL,
  `discount_val` int(11) NOT NULL,
  `count_used` int(11) NOT NULL DEFAULT '0',
  `count_all` int(11) NOT NULL DEFAULT '1',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expire_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupons_user_id_index` (`user_id`),
  KEY `coupons_coupon_index` (`coupon`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
INSERT INTO `coupons` VALUES (1,2,-1,-1,1,0,'happy-new-year',1,10,0,10,'系统生成','2023-06-06 10:25:18',NULL,NULL);
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `driver` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fund_records`
--

DROP TABLE IF EXISTS `fund_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fund_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '2',
  `amount` int(11) NOT NULL,
  `all` int(11) DEFAULT NULL,
  `frozen` int(11) DEFAULT NULL,
  `paid` int(11) DEFAULT NULL,
  `balance` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) DEFAULT NULL,
  `withdraw_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fund_records_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fund_records`
--

LOCK TABLES `fund_records` WRITE;
/*!40000 ALTER TABLE `fund_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `fund_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `logs_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` VALUES (1,1,'192.168.35.120','请下载qqwry.dat放在app/Library/QQWry目录下',0,'2023-07-06 02:30:23');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2017_12_23_223031_create_categories_table',1),(4,'2017_12_23_223124_create_products_table',1),(5,'2017_12_23_223252_create_cards_table',1),(6,'2017_12_23_223508_create_orders_table',1),(7,'2017_12_23_223755_create_pays_table',1),(8,'2018_01_02_142012_create_card_order_table',1),(9,'2018_01_28_183143_create_coupons_table',1),(10,'2018_01_29_195459_create_logs_table',1),(11,'2018_01_29_205026_create_systems_table',1),(12,'2018_02_01_174100_create_fund_records_table',1),(13,'2018_02_01_202439_create_jobs_table',1),(14,'2018_02_01_234941_create_files_table',1),(15,'2018_05_17_112228_create_shop_themes_table',1),(16,'2019_02_07_195259_add_count_to_products',1),(17,'2019_02_14_203213_add_name_to_orders',1),(18,'2019_04_28_230220_add_inventory_to_products',1),(19,'2019_05_18_131719_add_all_to_fund_records',1),(20,'2019_06_17_185712_add_options_to_shop_theme',1),(21,'2019_06_18_112211_add_manual_products',1),(22,'2019_07_03_213426_add_sms_price_to_orders',1),(23,'2019_12_27_175550_create_pay_ways_table',1),(24,'2019_12_30_210448_add_address_to_logs',1),(25,'2021_02_25_152603_add_query_password_to_orders',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_no` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `count` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_ext` text COLLATE utf8mb4_unicode_ci,
  `query_password` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `send_status` tinyint(4) NOT NULL DEFAULT '0',
  `remark` text COLLATE utf8mb4_unicode_ci,
  `cost` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `discount` int(11) NOT NULL DEFAULT '0',
  `sms_price` int(11) NOT NULL DEFAULT '0',
  `paid` int(11) NOT NULL DEFAULT '0',
  `fee` int(11) NOT NULL DEFAULT '0',
  `system_fee` int(11) NOT NULL DEFAULT '0',
  `income` int(11) NOT NULL DEFAULT '0',
  `pay_id` int(11) NOT NULL,
  `pay_trade_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `frozen_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_out_no` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_info` text COLLATE utf8mb4_unicode_ci,
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_index` (`user_id`),
  KEY `orders_order_no_index` (`order_no`)
) ENGINE=InnoDB AUTO_INCREMENT=1012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1001,1,'20230706104029NAz2j',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,1,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:40:29','2023-07-06 02:40:29'),(1002,1,'202307061042045l27v',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,1,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:42:04','2023-07-06 02:42:04'),(1003,1,'20230706104304C7Mrp',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,1,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:43:04','2023-07-06 02:43:04'),(1004,1,'202307061045203KoAG',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,1,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:45:20','2023-07-06 02:45:20'),(1005,1,'20230706104827mJMQ5',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,1,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:48:27','2023-07-06 02:48:27'),(1006,1,'202307061048499W7lh',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,1,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:48:49','2023-07-06 02:48:49'),(1007,1,'20230706104918j6Nfk',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,3,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:49:18','2023-07-06 02:49:18'),(1008,1,'202307061049575MDn7',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,3,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:49:57','2023-07-06 02:49:57'),(1009,1,'20230706105033oyd8V',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,3,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 02:50:33','2023-07-06 02:50:33'),(1010,1,'20230706110757KWlIp',1,'测试商品',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,1,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 03:07:57','2023-07-06 03:07:57'),(1011,1,'20230706112910Jgchf',3,'测试商品1-1',1,'192.168.35.120','76d9ead5c257c713c04badaed0011f71','18280041000','{}',NULL,0,NULL,0,1,0,0,1,0,0,1,1,NULL,0,NULL,NULL,NULL,NULL,'2023-07-06 03:29:10','2023-07-06 03:29:10');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pay_ways`
--

DROP TABLE IF EXISTS `pay_ways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pay_ways` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '1000',
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `channels` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '子渠道信息',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pay_ways_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pay_ways`
--

LOCK TABLES `pay_ways` WRITE;
/*!40000 ALTER TABLE `pay_ways` DISABLE KEYS */;
INSERT INTO `pay_ways` VALUES (1,'前台支付方式-支付宝',1000,'/plugins/images/ali.png',1,'[[1,1]]',NULL,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(2,'前台支付方式-支付宝手机',1000,'/plugins/images/ali.png',1,'[[2,1]]',NULL,2,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(3,'前台支付方式-微信',1000,'/plugins/images/wx.png',1,'[[3,1]]',NULL,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(4,'前台支付方式-微信手机',1000,'/plugins/images/wx.png',1,'[[4,1]]',NULL,2,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(5,'前台支付方式-QQ',1000,'/plugins/images/qq.png',1,'[[13,1]]',NULL,3,'2023-07-06 02:25:18','2023-07-06 02:25:18');
/*!40000 ALTER TABLE `pay_ways` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pays`
--

DROP TABLE IF EXISTS `pays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pays` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `way` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `config` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `fee_system` double(8,4) NOT NULL DEFAULT '0.0100',
  `enabled` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pays`
--

LOCK TABLES `pays` WRITE;
/*!40000 ALTER TABLE `pays` DISABLE KEYS */;
INSERT INTO `pays` VALUES (1,'支付宝 电脑','Fakala','alipay','{\r\n  \"gateway\": \"https://www.327ka.com\",\r\n  \"api_id\": \"你的 API_ID\",\r\n  \"api_key\": \"你的 API_KEY\"\r\n}','安发卡支付 https://www.anfaka.com',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(2,'支付宝 手机','Fakala','alipaywap','{\r\n  \"gateway\": \"https://www.327ka.com\",\r\n  \"api_id\": \"你的 API_ID\",\r\n  \"api_key\": \"你的 API_KEY\"\r\n}','安发卡支付 https://www.anfaka.com',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(3,'微信 电脑','Fakala','wx','{\r\n  \"gateway\": \"https://www.327ka.com\",\r\n  \"api_id\": \"你的 API_ID\",\r\n  \"api_key\": \"你的 API_KEY\"\r\n}','安发卡支付 https://www.anfaka.com',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(4,'微信 手机','Fakala','wxwap','{\r\n  \"gateway\": \"https://www.327ka.com\",\r\n  \"api_id\": \"你的 API_ID\",\r\n  \"api_key\": \"你的 API_KEY\"\r\n}','安发卡支付 https://www.anfaka.com',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(5,'支付宝','Alipay','pc','{\r\n  \"partner\": \"partner\",\r\n  \"key\": \"key\"\r\n}','支付宝 - 即时到账套餐(企业)V2',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(6,'支付宝','Aliwap','wap','{\r\n  \"partner\": \"partner\",\r\n  \"key\": \"key\"\r\n}','支付宝 - 高级手机网站支付V4',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(7,'支付宝扫码','AliAop','f2f','{\r\n  \"app_id\": \"app_id\",\r\n  \"alipay_public_key\": \"alipay_public_key\",\r\n  \"merchant_private_key\": \"merchant_private_key\"\r\n}','支付宝 - 当面付',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(8,'支付宝','AliAop','pc','{\r\n  \"app_id\": \"app_id\",\r\n  \"alipay_public_key\": \"alipay_public_key\",\r\n  \"merchant_private_key\": \"merchant_private_key\"\r\n}','支付宝 - 电脑网站支付 (新)',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(9,'手机支付宝','AliAop','mobile','{\r\n  \"app_id\": \"app_id\",\r\n  \"alipay_public_key\": \"alipay_public_key\",\r\n  \"merchant_private_key\": \"merchant_private_key\"\r\n}','支付宝 - 手机网站支付 (新)',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(10,'微信扫码','WeChat','NATIVE','{\r\n  \"APPID\": \"APPID\",\r\n  \"APPSECRET\": \"APPSECRET\",\r\n  \"MCHID\": \"商户ID\",\r\n  \"KEY\": \"KEY\"\r\n}','微信支付 - 扫码',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(11,'微信扫码','WeChat','JSAPI','{\r\n  \"APPID\": \"APPID\",\r\n  \"APPSECRET\": \"APPSECRET\",\r\n  \"MCHID\": \"商户ID\",\r\n  \"KEY\": \"KEY\"\r\n}','微信支付 - 扫码',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(12,'微信H5','WeChat','MWEB','{\r\n  \"APPID\": \"APPID\",\r\n  \"APPSECRET\": \"APPSECRET\",\r\n  \"MCHID\": \"商户ID\",\r\n  \"KEY\": \"KEY\"\r\n}','微信支付 - H5 (需要开通权限)',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(13,'手机QQ','QPay','NATIVE','{\r\n  \"mch_id\": \"mch_id\",\r\n  \"mch_key\": \"mch_key\"\r\n}','手机QQ - 扫码',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(14,'支付宝','Youzan','alipay','{\r\n  \"client_id\": \"client_id\",\r\n  \"client_secret\": \"client_secret\",\r\n  \"kdt_id\": \"kdt_id\"\r\n}','有赞支付 - 支付宝',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(15,'微信','Youzan','wechat','{\r\n  \"client_id\": \"client_id\",\r\n  \"client_secret\": \"client_secret\",\r\n  \"kdt_id\": \"kdt_id\"\r\n}','有赞支付 - 微信',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(16,'手机QQ','Youzan','qq','{\r\n  \"client_id\": \"client_id\",\r\n  \"client_secret\": \"client_secret\",\r\n  \"kdt_id\": \"kdt_id\"\r\n}','有赞支付 - 手机QQ',0.0100,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(17,'支付宝','CodePay','alipay','{\r\n  \"id\": \"id\",\r\n  \"key\": \"key\"\r\n}','码支付 - 支付宝',0.0000,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(18,'微信','CodePay','weixin','{\r\n  \"id\": \"id\",\r\n  \"key\": \"key\"\r\n}','码支付 - 微信',0.0000,1,'2023-07-06 02:25:18','2023-07-06 02:25:18'),(19,'手机QQ','CodePay','qq','{\r\n  \"id\": \"id\",\r\n  \"key\": \"key\"\r\n}','码支付 - 手机QQ',0.0000,1,'2023-07-06 02:25:18','2023-07-06 02:25:18');
/*!40000 ALTER TABLE `pays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '1000',
  `buy_min` int(11) NOT NULL DEFAULT '1',
  `buy_max` int(11) NOT NULL DEFAULT '10',
  `count_sold` int(11) NOT NULL DEFAULT '0',
  `count_all` int(11) NOT NULL DEFAULT '0',
  `count_warn` int(11) NOT NULL DEFAULT '0',
  `support_coupon` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_open` tinyint(1) NOT NULL DEFAULT '0',
  `cost` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL,
  `price_whole` text COLLATE utf8mb4_unicode_ci,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `fields` text COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) NOT NULL,
  `inventory` tinyint(4) NOT NULL DEFAULT '2',
  `fee_type` tinyint(4) NOT NULL DEFAULT '2',
  `delivery` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_user_id_index` (`user_id`),
  KEY `products_category_id_index` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,'测试商品2-1','{\"ops\":[{\"insert\":\"这里是测试商品的一段简短的描述\\n\"}]}',1000,1,10,1,3,0,1,NULL,0,0,1,'[]','{\"ops\":[{\"insert\":\"充值网址: XXXXX\\n\"}]}','{\"type\":\"any\",\"need_ext\":false,\"ext\":[]}',1,2,2,0,'2023-07-06 02:25:18','2023-07-06 03:16:35'),(2,1,1,'测试商品2-2密码','{\"ops\":[{\"insert\":\"<h2>商品描述</h2>所十二星座运势查询,提前预测2016年十二星座运势内容,让你能够占卜吉凶;2016年生肖运势测算,生肖开运,周易风水。\\n\"}]}',1000,1,10,2,100,10,1,'123456',1,0,10,'[[\"2\",8],[\"10\",5]]','{\"ops\":[{\"insert\":\"充值网址: XXXXX\\n\"}]}','{\"type\":\"any\",\"need_ext\":false,\"ext\":[]}',1,2,2,0,'2023-07-06 02:25:18','2023-07-06 03:16:54'),(3,1,2,'测试商品1-1','{\"ops\":[{\"insert\":\"这里是测试商品的一段简短的描述, 可以插入多媒体文本\\n\"}]}',999,1,10,0,1,0,0,NULL,0,0,1,'[]','{\"ops\":[{\"insert\":\"\\n\"}]}','{\"type\":\"any\",\"need_ext\":false,\"ext\":[]}',1,2,2,0,'2023-07-06 02:25:18','2023-07-06 03:28:44'),(4,1,3,'测试商品3-1密码','{\"ops\":[{\"insert\":\"这里是测试商品的一段简短的描述, 可以插入多媒体文本\\n\"}]}',1000,1,10,0,0,0,0,NULL,0,0,1,'[]','{\"ops\":[{\"insert\":\"\\n\"}]}','{\"type\":\"any\",\"need_ext\":false,\"ext\":[]}',1,2,2,0,'2023-07-06 02:25:18','2023-07-06 03:20:30');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_themes`
--

DROP TABLE IF EXISTS `shop_themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_themes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `options` text COLLATE utf8mb4_unicode_ci,
  `config` text COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shop_themes_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_themes`
--

LOCK TABLES `shop_themes` WRITE;
/*!40000 ALTER TABLE `shop_themes` DISABLE KEYS */;
INSERT INTO `shop_themes` VALUES (1,'Classic','经典主题','{\"list_type\":{\"label\":\"\\u5546\\u54c1\\u663e\\u793a\\u65b9\\u5f0f\",\"type\":\"select\",\"values\":{\"dropdown\":{\"label\":\"\\u4e0b\\u62c9\\u5f0f\"},\"button\":{\"label\":\"\\u6309\\u94ae\\u5f0f\"}},\"value\":\"dropdown\"}}','{\"list_type\":\"dropdown\"}',1),(2,'MS','Microsoft Shop','[]','[]',1),(3,'Material','Material Design 简洁风格','{\"list_type\":{\"label\":\"\\u5546\\u54c1\\u5217\\u8868\\u663e\\u793a\\u65b9\\u5f0f\",\"type\":\"select\",\"values\":{\"dropdown\":{\"label\":\"\\u4e0b\\u62c9\\u5f0f\"},\"button\":{\"label\":\"\\u6309\\u94ae\\u5f0f\"},\"list\":{\"label\":\"\\u5217\\u8868\\u5f0f\"}},\"value\":\"button\"},\"single_mode\":{\"label\":\"\\u5355\\u5546\\u54c1\\u663e\\u793a\\u65b9\\u5f0f\",\"type\":\"select\",\"values\":{\"flow\":{\"label\":\"\\u4e0a\\u4e0b\\u5e73\\u94fa\"},\"flex\":{\"label\":\"\\u5de6\\u53f3\\u5e76\\u6392\"}},\"value\":\"flow\"},\"background\":{\"label\":\"\\u80cc\\u666f\\u56fe\\u7247\",\"type\":\"text\",\"inputType\":\"text\",\"placeholder\":\"\\u8bf7\\u586b\\u5199\\u80cc\\u666f\\u56fe\\u7247URL\",\"value\":\"https:\\/\\/open.saintic.com\\/api\\/bingPic\\/\"},\"show_background\":{\"label\":\"\\u663e\\u793a\\u80cc\\u666f\\u56fe\\u7247\",\"type\":\"checkbox\",\"value\":1},\"music\":{\"label\":\"\\u80cc\\u666f\\u97f3\\u4e50\",\"type\":\"text\",\"inputType\":\"text\",\"placeholder\":\"\\u8bf7\\u586b\\u5199\\u80cc\\u666f\\u97f3\\u4e50URL, \\u5982 https:\\/\\/link.hhtjim.com\\/qq\\/001DEjRI0ihriN.mp3\",\"value\":\"\"}}','{\"list_type\":\"button\",\"single_mode\":\"flow\",\"background\":\"https:\\/\\/open.saintic.com\\/api\\/bingPic\\/\",\"show_background\":1,\"music\":\"\"}',1),(4,'Test','经典主题','{\"list_type\":{\"label\":\"\\u5546\\u54c1\\u663e\\u793a\\u65b9\\u5f0f\",\"type\":\"select\",\"values\":{\"dropdown\":{\"label\":\"\\u4e0b\\u62c9\\u5f0f\"},\"button\":{\"label\":\"\\u6309\\u94ae\\u5f0f\"}},\"value\":\"dropdown\"}}','{\"list_type\":\"dropdown\"}',1);
/*!40000 ALTER TABLE `shop_themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `systems`
--

DROP TABLE IF EXISTS `systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `systems_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `systems`
--

LOCK TABLES `systems` WRITE;
/*!40000 ALTER TABLE `systems` DISABLE KEYS */;
INSERT INTO `systems` VALUES (1,'app_name','XX小店',NULL,NULL),(2,'app_title','自动发卡, 自动发货',NULL,NULL),(3,'app_url','http://192.168.35.120:9092',NULL,'2023-07-06 02:39:27'),(4,'app_url_api','http://192.168.35.120:9092',NULL,'2023-07-06 02:39:27'),(5,'company','©2019 Windy',NULL,NULL),(6,'keywords','在线发卡系统',NULL,NULL),(7,'description','我是一个发卡系统, 这里填写描述',NULL,NULL),(8,'shop_bkg','http://api.izhao.me/img',NULL,NULL),(9,'shop_ann','{\"ops\":[{\"insert\":\"欢迎来到XXX小店\\n\"}]}',NULL,'2023-07-06 02:39:27'),(10,'shop_ann_pop','{\"ops\":[{\"insert\":\"\\n\"}]}',NULL,'2023-07-06 02:39:27'),(11,'shop_inventory','1',NULL,NULL),(12,'js_tj','<div style=\"display: none\"><script src=\"https://s22.cnzz.com/z_stat.php?id=1272914459&web_id=1272914459\" language=\"JavaScript\"></script></div>',NULL,NULL),(13,'js_kf',NULL,NULL,'2023-07-06 02:39:27'),(14,'vcode_driver','geetest',NULL,NULL),(15,'vcode_login','0',NULL,NULL),(16,'vcode_shop_buy','0',NULL,NULL),(17,'vcode_shop_search','0',NULL,NULL),(18,'storage_driver','local',NULL,NULL),(19,'order_query_day','30',NULL,NULL),(20,'order_clean_unpay_open','0',NULL,NULL),(21,'order_clean_unpay_day','7',NULL,NULL),(22,'mail_driver','smtp',NULL,NULL),(23,'mail_smtp_host','smtp.mailtrap.io',NULL,NULL),(24,'mail_smtp_port','25',NULL,NULL),(25,'mail_smtp_username','xxx',NULL,NULL),(26,'mail_smtp_password','xxx',NULL,NULL),(27,'mail_smtp_from_address','hello@example.com',NULL,NULL),(28,'mail_smtp_from_name','test',NULL,NULL),(29,'mail_smtp_encryption','null',NULL,NULL),(30,'shop_qq',NULL,'2023-07-06 02:39:27','2023-07-06 02:39:27');
/*!40000 ALTER TABLE `systems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `m_paid` int(11) NOT NULL DEFAULT '0',
  `m_frozen` int(11) NOT NULL DEFAULT '0',
  `m_all` int(11) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@qq.com',NULL,'$2y$10$sx/V8Co4mj/3y7DXSd9KIep7JiGwBmebbqYWjA8w2z5GxmFD3dVzO',0,0,0,NULL,'2023-07-06 02:25:18',NULL);
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

-- Dump completed on 2023-07-06 11:32:06
