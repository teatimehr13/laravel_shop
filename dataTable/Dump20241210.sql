-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: hr_shop
-- ------------------------------------------------------
-- Server version	8.0.31

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
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quantity` int unsigned NOT NULL,
  `cart_id` bigint unsigned NOT NULL,
  `product_option_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (14,31,15,1,'2024-12-05 01:34:22','2024-12-05 01:36:53'),(15,57,15,3,'2024-12-05 01:34:22','2024-12-05 01:36:53');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_type` int unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (6,0,2,NULL,NULL),(15,0,1,'2024-12-05 01:23:01','2024-12-05 01:23:01');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `search_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_index` int unsigned NOT NULL,
  `show_in_list` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (7,'類別A','test_key5',2,1,'2024-09-30 01:55:10','2024-09-30 23:58:11'),(8,'類別B','test_key1',1,1,'2024-10-01 00:29:35','2024-10-01 00:29:35'),(9,'類別C','test_key2',3,1,'2024-10-01 00:29:53','2024-10-01 00:29:53'),(12,'Henry_2','test_key22',4,1,'2024-12-03 00:56:05','2024-12-03 00:56:05'),(13,'Henry_2fd','sdfsdf',5,1,'2024-12-03 00:56:26','2024-12-03 00:56:26');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2024_09_27_065919_create_users_table',1),(3,'2024_09_27_070102_create_categories_table',1),(4,'2024_09_27_070120_create_subcategories_table',1),(5,'2024_09_27_070200_create_carts_table',1),(6,'2024_09_27_070304_create_cart_items_table',1),(64,'2024_09_27_070342_create_orders_table',2),(65,'2024_09_27_070400_create_order_items_table',2),(66,'2024_09_27_070504_create_stores_table',2),(67,'2024_09_27_070518_create_news_table',2),(68,'2024_09_27_070542_create_products_table',2),(69,'2024_09_27_070605_create_product_options_table',2),(70,'2024_11_12_085134_create_product_images_table',2),(74,'2024_11_12_092103_create_product_descriptions_table',3),(75,'2024_11_13_083803_create_product_option_images_table',3),(76,'2024_11_14_092009_add_order_and_is_combination_to_product_images_table',4),(77,'2024_12_03_094916_add_unique_constraint_to_subcategories_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `news` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `news_type` tinyint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,'猛毒 最終章 最後一舞 ','2024.10.15','/storage/sony/news/VENOM_241015to1201_232d93325.jpg',NULL,1,1,NULL,NULL),(2,'BRAVIA 最高降1萬元 入手超優惠 ','2024.10.09','/storage/sony/news/202401BRAVIA_680x440_627ad3451.jpg',NULL,1,1,NULL,NULL),(3,'聲色有型 風格隨行 ','2024.10.08','/storage/sony/news/MDR_241008to1201_215b03541.jpg',NULL,1,1,NULL,NULL),(4,'Sony 感恩回饋購物慶 ','2024.10.01','/storage/sony/news/Direct_241001to1130_f4fc14854.jpg',NULL,1,1,NULL,NULL),(5,'秋季之美 捕捉精彩瞬間 ','2024.10.01','/storage/sony/news/DI_2024_autumn_8073f0747.jpg',NULL,1,1,NULL,NULL),(6,'BRAVIA 好視成雙 ','2024.10.01','/storage/sony/news/Direct_home_241001to1231_30d004741.jpg',NULL,1,1,NULL,NULL),(7,'PS5 Pro & PlayStation 30 週年系列商品登記抽購買活動','2024.10.04','/storage/sony/news/Sony_logo_a0d0e4731.jpg','客服人員已於 2024年09月30日下午 16：30 透過「客服信件」 (info-tw@sony.com ) 通知，',1,2,NULL,NULL),(8,'花蓮地區配送不保證到貨日期','2024.09.01','/storage/sony/news/Sony_logo_a0d0e4731.jpg','親愛的顧客您好：',1,2,NULL,NULL),(9,'「2024 可攜式無線藍牙喇叭型錄資訊更正」啟事','2024.08.29','/storage/sony/news/Sony_logo_a0d0e4731.jpg','本年度發行之「2024 可攜式無線藍牙喇叭」型錄',1,2,NULL,NULL),(10,'反詐騙聲明，一頁式購物詐騙網站，提醒切勿受騙','2024.05.07','/storage/sony/news/Sony_logo_a0d0e4731.jpg','近來詐騙猖獗，行騙手法也不斷更新，提醒您：近日網路上出現有關 Sony 耳機產品一頁式購物詐騙網站，盜用百貨商標及 Sony 官網商品資訊等，並以限量體驗為促銷理由，企圖混淆消費者於一頁式購物網站下單，進行詐騙。',1,2,NULL,NULL);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` bigint unsigned NOT NULL,
  `product_option_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_status` int unsigned NOT NULL,
  `amount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'TestToken','7f0152a100282aaf5a166b0ed5e1de6856191628e3b862dc449ad8a7dce74262','[\"*\"]','2024-10-18 01:20:21',NULL,'2024-10-18 01:02:10','2024-10-18 01:20:21');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_descriptions`
--

DROP TABLE IF EXISTS `product_descriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_descriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` tinyint unsigned NOT NULL,
  `order` int unsigned NOT NULL,
  `link_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `promo_start_date` date DEFAULT NULL,
  `promo_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_descriptions`
--

LOCK TABLES `product_descriptions` WRITE;
/*!40000 ALTER TABLE `product_descriptions` DISABLE KEYS */;
INSERT INTO `product_descriptions` VALUES (1,1,'這個耳機很好用',0,1,NULL,NULL,NULL,NULL,NULL,NULL),(2,1,'他的顏色很還原',0,2,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,'由其是粉紅色的，超可愛',0,3,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `product_descriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order` int unsigned NOT NULL,
  `is_combination` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (9,1,'/storage/product_options/225978216_cups02.jpg','qqwwee','2024-12-04 00:46:05','2024-12-04 00:46:05',1,0),(14,1,'/storage/product_options/250196440_cups11.jpg','ffffff','2024-12-04 02:58:29','2024-12-04 02:58:29',2,0);
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_option_images`
--

DROP TABLE IF EXISTS `product_option_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_option_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_option_id` bigint unsigned NOT NULL,
  `product_image_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_option_images_product_option_id_product_image_id_unique` (`product_option_id`,`product_image_id`),
  KEY `product_option_images_product_image_id_foreign` (`product_image_id`),
  CONSTRAINT `product_option_images_product_image_id_foreign` FOREIGN KEY (`product_image_id`) REFERENCES `product_images` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_option_images_product_option_id_foreign` FOREIGN KEY (`product_option_id`) REFERENCES `product_options` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_option_images`
--

LOCK TABLES `product_option_images` WRITE;
/*!40000 ALTER TABLE `product_option_images` DISABLE KEYS */;
INSERT INTO `product_option_images` VALUES (9,1,9,NULL,NULL),(11,1,14,NULL,NULL);
/*!40000 ALTER TABLE `product_option_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_options`
--

DROP TABLE IF EXISTS `product_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `color_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `published_status` tinyint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_options`
--

LOCK TABLES `product_options` WRITE;
/*!40000 ALTER TABLE `product_options` DISABLE KEYS */;
INSERT INTO `product_options` VALUES (1,1,'black','#000000','black.jpg',10,'blue',1,1,NULL,NULL),(3,1,'combine','null',NULL,0,NULL,1,1,NULL,NULL),(5,2,'green','#CCC','/storage/product_options/1733300230_cups09.jpg',4444,'ttttt',1,1,NULL,'2024-12-04 00:17:10');
/*!40000 ALTER TABLE `product_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subcategory_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int unsigned DEFAULT NULL,
  `published_status` tinyint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,2,'666','edit_1204','/storage/products/1733299747_swords10.jpg',777,1,NULL,'2024-12-04 00:09:07'),(2,4,'airpods','蘋果無線耳機',NULL,6990,1,NULL,NULL),(4,2,'hello','cups04','/storage/products/1732595357_cups08.jpg',2599,1,'2024-11-25 20:29:18','2024-11-25 20:29:18');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_hours` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `store_type` tinyint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stores`
--

LOCK TABLES `stores` WRITE;
/*!40000 ALTER TABLE `stores` DISABLE KEYS */;
INSERT INTO `stores` VALUES (133,'Sony 行動通訊台中一中專賣店','台中市北區三民路三段196號1樓','週一~週五 : 11:30~20:30\n                                \n                                    週六~週日 : 11:30~20:30','04-2225-5869','/storage/sony/stores/圖片1_10d5f0250.jpg',1,1,NULL,NULL),(134,'Sony 行動通訊台中臺灣大道專賣店','台中市西屯區臺灣大道二段690號','週一~週五 : 11:00~21:00\n                                \n                                    週六~週日 : 11:00~21:00','04-3501-1128','/storage/sony/stores/行動通訊台中台灣大道_31f3a3322.jpg',1,1,NULL,NULL),(135,'Sony 行動通訊沙鹿中山專賣店','台中市沙鹿區中山路319號','週一~週五 : 11:00~21:00\n                                \n                                    週六~週日 : 11:00~21:00','04-2665-2567','/storage/sony/stores/8_8057073000_d00074635.jpg',1,1,NULL,NULL),(136,'Sony 行動通訊台北復興專賣店','台北市大安區復興南路2段84-3號','週一~週五 : 11:30~20:30\n                                \n                                    週六~週日 : 11:30~20:30','02-2755-3989','/storage/sony/stores/3_8057071000_200024049.jpg',1,1,NULL,NULL),(137,'Sony 行動通訊台南民族專賣店','台南市中西區民族路二段12號','週一~週五 : 11:00~21:30\n                                \n                                    週六~週日 : 11:00~21:30','06-224-3001','/storage/sony/stores/15_8057079000_2000e5202.jpg',1,1,NULL,NULL),(138,'Sony 行動通訊屏東博愛專賣店','屏東縣屏東市博愛路336號','週一~週五 : 11:30~21:00\n                                \n                                    週六~週日 : 11:30~21:00','08-7327834','/storage/sony/stores/圖片3_707443416.jpg',1,1,NULL,NULL),(139,'永順錩(首錩電器)','桃園市桃園區中正路397號1樓','電洽','03-337-1179','/storage/sony/stores/1530.jpg',1,1,NULL,NULL),(140,'Sony 行動通訊桃園站前專賣店','桃園市桃園區民生路16號1樓','週一~週五 : 11:00~21:20\n                                \n                                    週六~週日 : 11:00~21:20','03-339-1717','/storage/sony/stores/6_8057069000_c00054431.jpg',1,1,NULL,NULL),(141,'Sony 行動通訊高雄大順專賣店','高雄市三民區大順二路278號','週一~週五 : 12:00~21:00\n                                \n                                    週六~週日 : 12:00~21:00','07-398-1808','/storage/sony/stores/17_8057068000_000105322.jpg',1,1,NULL,NULL),(142,'Sony Center 高雄夢時代(秀翔)','高雄市前鎮區中華五路789號(藍鯨館 1 樓)','電洽','07-970-1289','/storage/sony/stores/1596.jpg',1,1,NULL,NULL),(143,'Sony 行動通訊楠梓德賢專賣店','高雄市楠梓區德賢路388號','週一~週五 : 11:00~21:00\n                                \n                                    週六 : 11:00~21:00\n                                \n                                    週日 : 11:00~20:00','07-365-8995','/storage/sony/stores/16_8057063000_3000f5247.jpg',1,1,NULL,NULL),(144,'Sony 行動通訊土城中央專賣店','新北市土城區中央路一段56號','週一~週五 : 11:00~22:00\n                                \n                                    週六~週日 : 11:00~22:00','02-8261-8228','/storage/sony/stores/5_8057070000_300044322.jpg',1,1,NULL,NULL),(145,'Sony Center 板橋大遠百店(紳鑫)','新北市板橋區新站路28號7樓','週一~週五 : 11:00~22:00\n                                \n                                    週六 : 11:00~22:30\n                                \n                                    週日 : 10:30~22:00','02-8952-0025','/storage/sony/stores/1646.jpg',1,1,NULL,NULL),(146,'Sony 行動通訊遠百竹北專賣店','新竹縣竹北市莊敬北路18號5樓','週一~週五 : 11:00~22:00\n                                \n                                    週六~週日 : 11:00~22:00','03-550-8339','/storage/sony/stores/123_3068a5654.jpg',1,1,NULL,NULL),(147,'Sony 行動通訊嘉義民族專賣店','嘉義市東區民族路402-1號','週一~週五 : 11:00~21:00\n                                \n                                    週六 : 11:00~21:00','05-222-3000','/storage/sony/stores/14_8057077000_8000d5126.jpg',1,1,NULL,NULL),(148,'Sony 行動通訊彰化中正專賣店','彰化縣彰化市中正路一段540號','週一~週五 : 10:30~21:30\n                                \n                                    週六~週日 : 11:00~21:00','04-728-0155','/storage/sony/stores/12_8057074000_7000b4956.jpg',1,1,NULL,NULL),(149,'Sony Center 台中大遠百店(秀翔)','台中市西屯區台中港路二段105號9樓','週一~週五 : 11:00~22:00\n                                \n                                    週六~週日 : 10:30~22:00','04-2254-6010','/storage/sony/stores/1112.jpg',1,1,NULL,NULL),(150,'立欣電器 (本店)','桃園市平鎮區延平路一段151號','電洽','03-4912-372','/storage/sony/stores/立欣2_506ab0943.jpg',1,1,NULL,NULL),(151,'新智實業大同店','高雄市新興區大同一路 189 號 1 樓','電洽','07-2515-811','/storage/sony/stores/1612.jpg',1,1,NULL,NULL),(152,'Sony Center 新竹巨城店 (新各界)','新竹市東區中央路 229 號 1 樓','週日~週四 : 11:00~21:30\n                                \n                                    週五~週六 : 11:00~22:00','03-620-0558','/storage/sony/stores/1132.jpg',1,1,NULL,NULL),(153,'晟美實業好時機店','桃園市桃園區民生路19號1樓','電洽','03-331-6666','/storage/sony/stores/1538.jpg',1,1,NULL,NULL),(154,'紳鑫企業京站店','台北市大同區承德路1段1號3F','平日 : 11:00~21:30\n                                \n                                    例假日前一天 : 11:00~22:00\n                                \n                                    例假日 : 11:00~22:00','02-2552-2909','/storage/sony/stores/紳鑫京站門市照片_d1ce62953.jpg',1,1,NULL,NULL),(155,'聖安科技','台中市中區三民路二段 24 號','週一~週五 : 11:00~21:00\n                                \n                                    週六~週日 : 11:00~21:00','04-2227-0000','/storage/sony/stores/1731.jpg',1,1,NULL,NULL),(156,'晴光 嚴選攝影器材 專賣','台中市中區台灣大道一段348號','平日 : 11:00~21:00\n                                \n                                    週六~週日 : 11:00~21:00','04-2220-8777','/storage/sony/stores/1726.jpg',1,1,NULL,NULL),(157,'中部電器','台中市中區綠川西街 95 號 1 樓','電洽','04-2224-0048','/storage/sony/stores/1729.jpg',1,1,NULL,NULL),(158,'中部MyEar二店','台中市北區一中街239號2樓','電洽','04-2225-1600',NULL,1,1,NULL,NULL),(159,'普羅攝影器材行','台中市北區三民路三段 52-1 號 1 樓','電洽','04-2224-8856','/storage/sony/stores/1658.jpg',1,1,NULL,NULL),(160,'建欣電器','台中市西屯區大墩路 949 之 3 號','電洽','04-2320-7981','/storage/sony/stores/1670.jpg',1,1,NULL,NULL),(161,'聖邦','台中市西屯區文心路三段 104 號','週一~週五 : 08:30~17:30','04-2314-2000',NULL,1,1,NULL,NULL),(162,'集雅社新光三越台中','台中市西屯區台灣大道三段301號8樓','平日 : 11:00~21:30\n                                \n                                    例假日前一天 : 11:00~22:00\n                                \n                                    例假日 : 11:00~21:30','04-2255-9810',NULL,1,1,NULL,NULL),(163,'相機王台中(東明台中)','台中市西屯區惠中二街36號','電洽','04-2252-0773','/storage/sony/stores/1668.jpg',1,1,NULL,NULL),(164,'天韻','台中市西屯區朝馬路131號1樓','電洽','04-2255-6077','/storage/sony/stores/1737.jpg',1,1,NULL,NULL),(165,'中聯發','台中市西屯區環中路一段2085號','電洽','04-2436-6499','/storage/sony/stores/1664.jpg',1,1,NULL,NULL),(166,'Sony Center 台中三井 LaLaport (集雅社)','台中市東區進德路600號1F(北館)','週一~週五 : 11:00~22:00\n                                \n                                    週六~週日 : 10:30~22:00','04-3609-5338','/storage/sony/stores/SCTCL店照_3085a5619.jpg',1,1,NULL,NULL),(167,'大銀幕視聽音響','台中市南屯區大墩路307號','電洽','04-2473-5826',NULL,1,1,NULL,NULL),(168,'華笙','台中市南屯區溝墘里大墩十二街169號','電洽','04-2325-9518','/storage/sony/stores/1673.jpg',1,1,NULL,NULL),(169,'兆華國際','台北市士林區福國路69號1樓','電洽','02-2381-6982','/storage/sony/stores/S__84090884_272932843.jpg',1,1,NULL,NULL),(170,'日光徠卡','台北市大安區四維路21號1樓','電洽','02-2700-7779','/storage/sony/stores/1650.jpg',1,1,NULL,NULL),(171,'尹晨視聽 (DEMO)','台北市中正區中華路一段 45 號 1 樓','電洽','02-2311-3841','/storage/sony/stores/1471.jpg',1,1,NULL,NULL),(172,'紳鑫三創秋葉原','台北市中正區市民大道三段2號2樓 201室','週日~週四 : 11:00~21:30\n                                \n                                    週五~週六 : 11:00~22:00','02-2341-3068','/storage/sony/stores/1DSC_0580_5036b2848.JPG',1,1,NULL,NULL),(173,'曜德光華','台北市中正區市民大道三段8號2樓A29','電洽','02-2395-8081','/storage/sony/stores/1242.jpg',1,1,NULL,NULL),(174,'長勵實業','台北市中正區博愛路16號','電洽','02-2311-8331','/storage/sony/stores/1491.jpg',1,1,NULL,NULL),(175,'喬翊電器 (本店)','台北市中正區博愛路3號','電洽','02-2381-2626','/storage/sony/stores/1_f020b1547.jpg',1,1,NULL,NULL),(176,'永嘉','台北市中正區新生南路一段168號','電洽','02-2394-3933','/storage/sony/stores/1451.jpg',1,1,NULL,NULL),(177,'宇利攝影器材','台北市中正區漢口街一段 71號 1 樓','電洽','02-2331-5217','/storage/sony/stores/1641.jpg',1,1,NULL,NULL),(178,'新博視聽器材','台北市中正區漢口街一段104號1樓','電洽','02-2311-3051','/storage/sony/stores/1463.jpg',1,1,NULL,NULL),(179,'震博攝影器材','台北市中正區漢口街一段104號2樓','電洽','02-2311-0058','/storage/sony/stores/1467.jpg',1,1,NULL,NULL),(180,'祥建科技','台北市松山區民生東路五段 102 號','電洽','02-2768-5411','/storage/sony/stores/1526.jpg',1,1,NULL,NULL),(181,'弘明數位科技有限公司','台南市中西區民族路二段122號','電洽','06-2251-053','/storage/sony/stores/1584.jpg',1,1,NULL,NULL),(182,'全省電器 (本店)','台南市北區公園路 219 號','電洽','06-226-6121','/storage/sony/stores/1588.jpg',1,1,NULL,NULL),(183,'曜德中壢 NOVA 107','桃園市中壢區中正路 389 號 NOVA 107','電洽','03-4915-252','/storage/sony/stores/1546.jpg',1,1,NULL,NULL),(184,'麥仕視聽','桃園市蘆竹區南崁路一段236號','電洽','03-222-8833','/storage/sony/stores/麥仕 門面_f03260054.jpg',1,1,NULL,NULL),(185,'視冠遠通','高雄市三民區建國二路 175 號','電洽','07-235-2866','/storage/sony/stores/1604.jpg',1,1,NULL,NULL),(186,'秀翔實業 (本店)','高雄市苓雅區三多二路 21 號','電洽','07-725-6606','/storage/sony/stores/1592.jpg',1,1,NULL,NULL),(187,'明功照相器材行','高雄市新興區中山一路74號','電洽','07-2618-662','/storage/sony/stores/1600.jpg',1,1,NULL,NULL),(188,'富士通影音','新北市板橋區四川路一段93號 1 樓','電洽','02-2950-9666','/storage/sony/stores/富士通 (1)_c247b1602.jpg',1,1,NULL,NULL),(189,'蘆荻電器','新北市蘆洲區中山二路 173 號 1 樓','電洽','02-2283-3227','/storage/sony/stores/1522.jpg',1,1,NULL,NULL),(190,'上位電器','嘉義市西區仁愛路 334 號','電洽','05-2245-192','/storage/sony/stores/1704.jpg',1,1,NULL,NULL),(191,'醉音影音生活企業','嘉義市東區東區彌陀路 373 號 1 樓','電洽','05-2236-522','/storage/sony/stores/1718.jpg',1,1,NULL,NULL),(192,'Sony Store 遠百信義直營店','台北市信義區松仁路 58 號 8F(遠東百貨信義店 - A13)','週日~週四 : 11:00~21:30\n                                \n                                    週五~週六 : 11:00~22:00\n                                \n                                    例假日前一天 : 11:00~22:00','02-8780-1718 (營業時間如有變動，請依百貨公告時間為準)','/storage/sony/stores/1755.jpg',1,0,NULL,NULL),(193,'Sony Store 台北復興直營店','台北市大安區忠孝東路三段 300 號 8F(遠東 SOGO 百貨復興館)','週日~週四 : 11:00~21:30\n                                \n                                    週五~週六 : 11:00~22:00\n                                \n                                    例假日 : 11:00~22:00','02-8772-6501 (營業時間如有變動，請依百貨公告時間為準)','/storage/sony/stores/SSTF_6062b1448.jpg',1,0,NULL,NULL),(194,'Sony Store 台北 101 直營店','台北市信義區市府路 45 號 B1(台北 101 購物中心)','週日~週四 : 11:00~21:30\n                                \n                                    週五~週六 : 11:00~22:00\n                                \n                                    例假日前一天 : 11:00~22:00','02-8101-7833 (營業時間如有變動，請依百貨公告時間為準)','/storage/sony/stores/SST101_22_c40a82204.jpg',1,0,NULL,NULL),(195,'Sony Store 台中直營店','台中市西屯區臺灣大道三段 301 號 9F(新光三越百貨台中中港店)','平日 : 11:00~22:00\n                                \n                                    例假日 : 10:30~22:00','04-2254-1619 (營業時間如有變動，請依百貨公告時間為準)','/storage/sony/stores/SSTC_a062d1524.jpg',1,0,NULL,NULL),(196,'Sony Store 高雄直營店','高雄市左營區博愛二路 777 號(漢神巨蛋購物廣場 B1)','週日~週四 : 11:00~22:00\n                                \n                                    週五~週六 : 11:00~22:30\n                                \n                                    例假日 : 11:00~22:30','07-554-8225 (營業時間如有變動，請依百貨公告時間為準)','/storage/sony/stores/SSKS_22_040d85115.jpg',1,0,NULL,NULL),(219,'大牛節能電器行','台中市大里區新光路6號1樓','','04-2483-6018',NULL,1,2,NULL,NULL),(220,'聖佳照相器材行','台中市中區三民路二段 90 號 1 樓','','04-2222-4600',NULL,1,2,NULL,NULL),(221,'聖影','台中市中區大墩里三民路二段31號','週一~週五 : 10:00~20:00\n                                        \n                                            週六 : 10:00~20:00','04-2222-4600',NULL,1,2,NULL,NULL),(222,'柯達行','台中市中區台灣大道一段310號','','04-2221-4813',NULL,1,2,NULL,NULL),(223,'銘錩科技','台中市中區成功路243號1樓','','04-2220-1076',NULL,1,2,NULL,NULL),(224,'中部Myear雙十店','台中市中區雙十路二段47-4號','','04-2229-9077',NULL,1,2,NULL,NULL),(225,'正昌電器','台中市太平區太平路 575 號','','04-2279-4979',NULL,1,2,NULL,NULL),(226,'鼎采器貿','台中市太平區台中市太平區新光里新福十街16之1號1樓','','04-2225-1660',NULL,1,2,NULL,NULL),(227,'禾聲電器','台中市北屯區北屯路178之4號','週一~週五 : 09:00~21:00\n                                        \n                                            週六 : 09:00~21:00','04-2236-5115',NULL,1,2,NULL,NULL),(228,'威特家電','台中市北屯區后庄里松竹路三段710號','週一~週五 : 09:00~22:00','04-22472569',NULL,1,2,NULL,NULL),(229,'名森電器行','台中市北屯區昌平路一段 109-2 號','','04-2244-1749',NULL,1,2,NULL,NULL),(230,'禾承電業','台中市北屯區昌平路一段 260 號','','04-2231-5111',NULL,1,2,NULL,NULL),(231,'映象國際企業中友店','台中市北區三民路三段161 號A棟10樓','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 11:00~22:00\n                                        \n                                            例假日 : 10:30~22:00','04-2225-1887',NULL,1,2,NULL,NULL),(232,'集盛台中中清','台中市北區邱厝里中清路一段123號','','04-3609-9268',NULL,1,2,NULL,NULL),(233,'科元電業','台中市西屯區大鵬路 17 號','','04-2291-4867',NULL,1,2,NULL,NULL),(234,'愛樂台中','台中市西屯區市政路676號1F','','04-2251-3388',NULL,1,2,NULL,NULL),(235,'大鑫電器','台中市西屯區西屯路二段 28 號','','04-2312-7723',NULL,1,2,NULL,NULL),(236,'豪順','台中市西屯區朝貴路108號','','04-2254-5591',NULL,1,2,NULL,NULL),(237,'國際極品','台中市西屯區福和里福安二街3巷65號','','04-722-9876',NULL,1,2,NULL,NULL),(238,'豪順市政','台中市西屯區環中路三段868號','週一~週五 : 11:00~21:00\n                                        \n                                            週六~週日 : 11:00~21:00','04-2452-8114',NULL,1,2,NULL,NULL),(239,'店家名稱','無資訊','無資訊','無資訊',NULL,1,2,NULL,NULL),(240,'集雅社台中廣三 SOGO','台中市西區台灣大道二段459號10樓','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 10:30~22:30\n                                        \n                                            例假日 : 10:30~22:00','04-3609-3118',NULL,1,2,NULL,NULL),(241,'福昱電器','台中市西區向上路一段 365 號 1 樓','','04-2473-9299',NULL,1,2,NULL,NULL),(242,'欣亞數位台中 NOVA 店','台中市西區英才路508號1樓119櫃位','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 11:00~22:00\n                                        \n                                            例假日 : 11:00~22:00','04-2329-0080',NULL,1,2,NULL,NULL),(243,'曜德台中NOVA B08','台中市西區英才路508號B1樓08櫃','','04-2329-3656',NULL,1,2,NULL,NULL),(244,'集雅社台中綠園道','台中市西區健行路1049號B1','','04-360-93938',NULL,1,2,NULL,NULL),(245,'穎聲','台中市東區大智路 448 號 1 樓','','04-2281-0051',NULL,1,2,NULL,NULL),(246,'佳績','台中市東區台中路93號1樓','週一~週五 : 09:00~20:30\n                                        \n                                            週六 : 09:00~20:30','04-2229-2998',NULL,1,2,NULL,NULL),(247,'飛登台中大魯閣','台中市東區新庄里復興路四段186號地下2樓B2-6櫃','週一~週五 : 11:00~22:00\n                                        \n                                            週六~週日 : 10:30~22:00','04-2222-9989',NULL,1,2,NULL,NULL),(248,'恆伸照相器材','台中市南屯區大墩六街 133 號','','04-2472-7278',NULL,1,2,NULL,NULL),(249,'記峰','台中市南屯區大興里東興路二段3巷38號1樓','','04-24717311',NULL,1,2,NULL,NULL),(250,'中野科技','台中市南屯區文心南三路677號','','04-2382-3332',NULL,1,2,NULL,NULL),(251,'鴻毅','台中市南屯區南屯路二段345號一樓','','04-2475-6188',NULL,1,2,NULL,NULL),(252,'正宜','台中市南區五權南路277號一樓1室','','04-2261-9497',NULL,1,2,NULL,NULL),(253,'欣音電器行','台中市南區台中路 188 號 1 樓','','04-2282-4575',NULL,1,2,NULL,NULL),(254,'啟新家電商品','台中市神岡區中山路 21 號','','04-2522-4582',NULL,1,2,NULL,NULL),(255,'益源電氣行','台中市梧棲區中興路 455 號','','04-2656-3522',NULL,1,2,NULL,NULL),(256,'集雅社台中三井','台中市梧棲區臺灣大道10段168號1樓','','04-3609-3808',NULL,1,2,NULL,NULL),(257,'太子電業行','台中市清水區中華路 408 之 5 號','','04-2623-3973',NULL,1,2,NULL,NULL),(258,'庫克利','台中市潭子區潭東街216號1樓','','04-2238-9596',NULL,1,2,NULL,NULL),(259,'明嘉電器','台中市豐原區三民路 176 號','','04-2527-1603',NULL,1,2,NULL,NULL),(260,'店家名稱','無資訊','無資訊','無資訊',NULL,1,2,NULL,NULL),(261,'三豐電器行','台中市豐原區三豐路二段３號1樓','','04-2523-2306',NULL,1,2,NULL,NULL),(262,'鈺陞惠陽','台中市豐原區中陽里惠陽街91號','週一~週五 : 09:30~18:00','04-2529-7497',NULL,1,2,NULL,NULL),(263,'永寶電器','台中市豐原區南陽路258號1樓','','04-2522-4310',NULL,1,2,NULL,NULL),(264,'集雅社豐原太平洋','台中市豐原區復興路2號7樓','','04-3609-3818',NULL,1,2,NULL,NULL),(265,'正貿行','台中市豐原區陽明里信義街 39 號','','04-2526-2229',NULL,1,2,NULL,NULL),(266,'釪環數位','台中市霧峰區中投西路二段442號','','04-2330-1990',NULL,1,2,NULL,NULL),(267,'旦揚','台中市霧峰區吉峰路 38 號','','04-2332-3395',NULL,1,2,NULL,NULL),(268,'昇記影音電器','台北市士林區中山北路六段 174 之 1 號 1 樓','','02-2836-6218',NULL,1,2,NULL,NULL),(269,'紳鑫企業遠東SOGO百貨天母館','台北市士林區中山北路六段 77 號 6F','平日 : 11:00~21:30\n                                        \n                                            例假日前一天 : 11:00~22:00\n                                        \n                                            例假日 : 11:00~21:30','02-2835-9880',NULL,1,2,NULL,NULL),(270,'集雅社新光三越天母','台北市士林區天母東路68號4樓','','02-6613-0335',NULL,1,2,NULL,NULL),(271,'錩偉電器','台北市士林區文林路 491 號','','02-2835-5585',NULL,1,2,NULL,NULL),(272,'海山商行','台北市士林區延平北路五段136巷27弄4號','','02-2811-3641',NULL,1,2,NULL,NULL),(273,'品銓電器','台北市士林區忠誠路二段 2 號','','02-2831-9494',NULL,1,2,NULL,NULL),(274,'集雅社大葉高島屋','台北市士林區忠誠路二段55號12樓','平日 : 11:00~21:30\n                                        \n                                            例假日前一天 : 11:00~22:00','02-6617-6088',NULL,1,2,NULL,NULL),(275,'上億立','台北市士林區福港街 129 巷 10 弄 16 號','週一~週四 : 07:00~21:00','02-2883-7777',NULL,1,2,NULL,NULL),(276,'新遠東','台北市士林區福港街118號','週一~週五 : 08:30~19:00\n                                        \n                                            週六 : 08:30~15:00','02-2810-6500',NULL,1,2,NULL,NULL),(277,'壯盛企業','台北市士林區德行東路 12 號','','02-2832-6060',NULL,1,2,NULL,NULL),(278,'承德音響','台北市大同區承德路三段 88 號 1 樓','平日 : 10:00~20:00\n                                        \n                                            週六 : 10:00~18:00','02-2592-1615',NULL,1,2,NULL,NULL),(279,'展新音響','台北市大同區歸綏街132之2號1樓','','02-2549-2496',NULL,1,2,NULL,NULL),(280,'相機王忠孝(東明忠孝)','台北市大安區仁愛路四段27巷22號','','02-2721-6555',NULL,1,2,NULL,NULL),(281,'店家名稱','無資訊','無資訊','無資訊',NULL,1,2,NULL,NULL),(282,'鼎采器貿','台北市大安區市民大道3段8號4樓6室','','02-2351-0015',NULL,1,2,NULL,NULL),(283,'飛登科技台北光華一店','台北市大安區市民大道三段 8 號 4 樓 8 室','','02-2382-2779',NULL,1,2,NULL,NULL),(284,'京東忠孝','台北市大安區忠孝東路三段253號1F','','02-2776-6388',NULL,1,2,NULL,NULL),(285,'紳鑫企業遠東SOGO百貨忠孝館','台北市大安區忠孝東路四段 45 號 8 樓','平日 : 11:00~21:30\n                                        \n                                            例假日前一天 : 11:00~22:00\n                                        \n                                            例假日 : 11:00~21:30','02-2751-7278',NULL,1,2,NULL,NULL),(286,'京東信義','台北市大安區信義路2段247號1F','','02-2341-3266',NULL,1,2,NULL,NULL),(287,'答答企業','台北市大安區復興南路一段 295 巷 1 號 1','','02-2706-3878',NULL,1,2,NULL,NULL),(288,'全英','台北市大安區復興南路一段283號B1','','02-2706-4828',NULL,1,2,NULL,NULL),(289,'三禾影電器視聽','台北市大安區復興南路二段 343 號 1 樓','','02-2735-5866',NULL,1,2,NULL,NULL),(290,'集雅社台北遠企','台北市大安區敦化南路二段203號5樓','週日~週四 : 11:00~21:30\n                                        \n                                            週五~週六 : 11:00~22:00','02-66176658',NULL,1,2,NULL,NULL),(291,'統元','台北市大安區樂利路18號1樓','','02-2704-4546',NULL,1,2,NULL,NULL),(292,'佳麗寶八德店','台北市中山區八德路二段184號','','02-8772-7118',NULL,1,2,NULL,NULL),(293,'瑞松電器','台北市中山區民生西路 48 號','','02-2542-1677',NULL,1,2,NULL,NULL),(294,'集雅社新光三越南西店','台北市中山區南京西路 12 號 7 樓','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 10:30~22:30\n                                        \n                                            例假日 : 10:30~22:00','02-2581-3653',NULL,1,2,NULL,NULL),(295,'沛聲誠品南西','台北市中山區南京西路14號5樓','週日~週四 : 11:00~22:00\n                                        \n                                            週五~週六 : 11:00~22:30','02-2581-3358#1511',NULL,1,2,NULL,NULL),(296,'相機王大直(東明大直)','台北市中山區敬業二路86號1F','週一~週五 : 13:00~22:00\n                                        \n                                            週六~週日 : 13:00~22:00','02-2880-2887',NULL,1,2,NULL,NULL),(297,'紳鑫企業美麗華店','台北市中山區敬業三路 20 號 3 樓','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 11:00~22:00\n                                        \n                                            例假日 : 11:00~22:00','02-2175-3001',NULL,1,2,NULL,NULL),(298,'欣亞台北八德數位旗艦店','台北市中正區八德路一段76號','','02-2341-6303',NULL,1,2,NULL,NULL),(299,'映象音響','台北市中正區中華路一段25號1樓','','02-2314-1696',NULL,1,2,NULL,NULL),(300,'凡順三創','台北市中正區市民大道三段2號5樓R517-2櫃','','02-3322-1449',NULL,1,2,NULL,NULL),(301,'生達電業','台北市中正區吉林路370號','','02-2591-6699',NULL,1,2,NULL,NULL),(302,'店家名稱','無資訊','無資訊','無資訊',NULL,1,2,NULL,NULL),(303,'東暉國際','台北市中正區和平西路二段 142之1號','','02-2338-0269',NULL,1,2,NULL,NULL),(304,'凡順','台北市中正區延平南路17號2樓','','02-2388-2028',NULL,1,2,NULL,NULL),(305,'集雅社台北新光三越站前','台北市中正區忠孝西路一段 66 號 10 樓','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 11:00~22:30\n                                        \n                                            例假日 : 10:30~22:00','02-2370-5338',NULL,1,2,NULL,NULL),(306,'愛樂','台北市中正區金山南路一段155號B1','','02-2321-2138',NULL,1,2,NULL,NULL),(307,'音悅音響','台北市中正區重慶南路2段59號','','02-2392-8832',NULL,1,2,NULL,NULL),(308,'科紀視聽器材','台北市中正區博愛路 5 號','','02-2381-0992',NULL,1,2,NULL,NULL),(309,'林屋照相器材','台北市中正區博愛路 60 號','','02-2381-4575',NULL,1,2,NULL,NULL),(310,'泰光視聽器材 (本店)','台北市中正區開封街一段 62 號','','02-2312-2628',NULL,1,2,NULL,NULL),(311,'勝眾','台北市中正區開封街一段101號','','02-2361-5372',NULL,1,2,NULL,NULL),(312,'北門音響','台北市中正區開封街一段99號','','02-2314-3255',NULL,1,2,NULL,NULL),(313,'正陽照相器材','台北市中正區漢口街一段92號','','02-2381-4769',NULL,1,2,NULL,NULL),(314,'加煒電子','台北市中正區羅斯福路4段134號三樓','','02-2365-1207',NULL,1,2,NULL,NULL),(315,'紳鑫企業遠東寶慶店','台北市中正區寶慶路 32 號 8 樓','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 11:00~22:30\n                                        \n                                            例假日 : 10:30~22:00','02-2382-2807',NULL,1,2,NULL,NULL),(316,'強聲音響','台北市內湖區成功路三段 89 號','','02-2790-1199',NULL,1,2,NULL,NULL),(317,'新各界','台北市內湖區行愛路 172 號 1 樓','','02-2793-3939',NULL,1,2,NULL,NULL),(318,'皇儒貴','台北市內湖區康樂街 72 巷 17弄 16 號','','02-2631-6068',NULL,1,2,NULL,NULL),(319,'雅光','台北市內湖區堤頂大道一段 215 號','','02-2796-5858',NULL,1,2,NULL,NULL),(320,'金門電器音響有限公司','台北市內湖區新湖二路19號5樓','週一~週五 : 10:00~19:30\n                                        \n                                            週六 : 10:00~19:30','02-8792-8100',NULL,1,2,NULL,NULL),(321,'佳麗寶景美店','台北市文山區興隆路一段128號1樓','','02-2363-6097',NULL,1,2,NULL,NULL),(322,'盈成興業有限公司','台北市文山區羅斯福路五段 258 號 1 樓','','02-2934-5111',NULL,1,2,NULL,NULL),(323,'店家名稱','無資訊','無資訊','無資訊',NULL,1,2,NULL,NULL),(324,'尚德師企業','台北市北投區尊賢里石牌路一段39巷79號1樓','','02-2823-5176',NULL,1,2,NULL,NULL),(325,'日月音響','台北市松山區八德路二段 366 巷 55弄 1 號','','02-2771-0912',NULL,1,2,NULL,NULL),(326,'大祥電器','台北市松山區八德路三段 160 號 1 樓','','02-2578-7788',NULL,1,2,NULL,NULL),(327,'集雅社微風廣場','台北市松山區中崙里復興南路一段39號2樓','例假日前一天 : 11:00~22:00\n                                        \n                                            週日～週三 : 11:00~21:30\n                                        \n                                            週四～週六 : 11:00~22:00','0905-591-355',NULL,1,2,NULL,NULL),(328,'乙鑫電器音響','台北市松山區長春路 496 號 1 樓','','02-2712-7383',NULL,1,2,NULL,NULL),(329,'沛聲','台北市松山區南京東路四段102號8樓','','02-2578-1780',NULL,1,2,NULL,NULL),(330,'信瑋電器','台北市信義區永吉路 278 巷 34 號','','02-2765-6158',NULL,1,2,NULL,NULL),(331,'紳鑫企業 (本店)','台北市信義區和平東路三段 425 號 1 樓','','02-2739-2915',NULL,1,2,NULL,NULL),(332,'紳鑫企業統一時代','台北市信義區忠孝東路5段8號6樓','週日~週四 : 11:00~21:30\n                                        \n                                            週五~週六 : 11:00~22:00\n                                        \n                                            例假日前一天 : 11:00~22:00','02-2723-3522',NULL,1,2,NULL,NULL),(333,'沛聲誠品信義','台北市信義區松高路11號2樓','','02-8789-3388 # 3124',NULL,1,2,NULL,NULL),(334,'集雅社新光三越 A8 館','台北市信義區松高路12號7樓','平日 : 11:00~21:30\n                                        \n                                            例假日前一天 : 11:00~22:00\n                                        \n                                            例假日 : 11:00~21:30','02-2729-5858',NULL,1,2,NULL,NULL),(335,'集雅社新光三越信義 A9','台北市信義區松壽路 9 號 B2','平日 : 11:00~21:30\n                                        \n                                            例假日前一天 : 11:00~22:00\n                                        \n                                            例假日 : 11:00~21:30','02-2758-8838',NULL,1,2,NULL,NULL),(336,'展督','台北市南港區忠孝東路七段616號','','02-2788-0596',NULL,1,2,NULL,NULL),(337,'百裕電業','台北市南港區忠孝東路六段 384 號 1 樓','','02-2652-1066',NULL,1,2,NULL,NULL),(338,'富士多媒體','台北市萬華區中華路一段14號','週一~週五 : 09:00~18:30\n                                        \n                                            週六 : 11:00~18:00','02-2370-8989',NULL,1,2,NULL,NULL),(339,'泰宜電器','台北市萬華區台北市萬華區寶興街80巷20號一樓','','02-2307-5155',NULL,1,2,NULL,NULL),(340,'金響','台北市萬華區漢口街二段 41-2 號 1 樓','','02-2382-5522',NULL,1,2,NULL,NULL),(341,'晟歌電器','台東縣台東市大同路 206 號','','089-329-375',NULL,1,2,NULL,NULL),(342,'集雅社新光三越中山店','台南市中西區中山路 162 號 9 樓','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 10:30~22:00\n                                        \n                                            例假日 : 10:30~22:00','06-228-5151',NULL,1,2,NULL,NULL),(343,'全省湖美店','台南市中西區中華西路二段769號','','06-350-4790',NULL,1,2,NULL,NULL),(344,'店家名稱','無資訊','無資訊','無資訊',NULL,1,2,NULL,NULL),(345,'大元台南','台南市中西區民族路2段34號','','06-228-3161',NULL,1,2,NULL,NULL),(346,'集雅社台南永華','台南市中西區永華路一段303號','','06-602-0168',NULL,1,2,NULL,NULL),(347,'集雅社新光三越西門','台南市中西區西門路一段 658 號 B1','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 10:30~22:00\n                                        \n                                            例假日 : 10:30~22:00','06-3030-099',NULL,1,2,NULL,NULL),(348,'東旺電器','台南市仁德區中正路2段1197號','','06-2793-371',NULL,1,2,NULL,NULL),(349,'百鈴電器影視','台南市六甲區珊瑚路 57 號','','06-6984-141',NULL,1,2,NULL,NULL),(350,'曜德台南彩虹','台南市北區北門路一段250號A04櫃','','06-222-8935',NULL,1,2,NULL,NULL),(351,'品味','台南市北區東豐路335號1樓','','06-2088-458',NULL,1,2,NULL,NULL),(352,'志偉電器行','台南市永康區中山南路 101 號','','06-2326-583',NULL,1,2,NULL,NULL),(353,'大時代量販家電','台南市佳里區中山路 376 號','','06-7210-869',NULL,1,2,NULL,NULL),(354,'采躍科技','台南市東區北門路一段80號','','06-223-9988',NULL,1,2,NULL,NULL),(355,'鴻運','台南市東區台南市東區崇學路259之5號','','06-289-4551',NULL,1,2,NULL,NULL),(356,'集雅社台南遠東百貨','台南市東區前鋒路 210 號 6 樓','平日 : 11:00~22:00\n                                        \n                                            例假日前一天 : 11:00~22:30\n                                        \n                                            例假日 : 10:30~22:00','06-2756-313',NULL,1,2,NULL,NULL),(357,'亞賓數位家電','台南市東區崇道路 47 號','','06-2902-928',NULL,1,2,NULL,NULL),(358,'集盛台南健康','台南市南區大成里健康路一段387號','','06-602-5268',NULL,1,2,NULL,NULL),(359,'集雅社南紡夢時代','台南市南區中華東路一段366號B1','週一~週五 : 11:00~22:00\n                                        \n                                            週六~週日 : 11:00~22:00','06-2095121',NULL,1,2,NULL,NULL),(360,'正聲電機','台南市南區永華路 21 號','','06-2217-878',NULL,1,2,NULL,NULL),(361,'政鑫電器行','台南市南區明興路 1265 號','','06-2621-246',NULL,1,2,NULL,NULL),(362,'鎮億電器','台南市南區健康路二段 134 號','','06-2284-977',NULL,1,2,NULL,NULL),(363,'麗聲電業行','台南市南區灣裡路 257 號','','06-2626-558',NULL,1,2,NULL,NULL),(364,'銘億電器','台南市新化區大同街 103 號','','06-5907-178',NULL,1,2,NULL,NULL);
/*!40000 ALTER TABLE `stores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcategories`
--

DROP TABLE IF EXISTS `subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subcategories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `search_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_index` int unsigned NOT NULL,
  `show_in_list` tinyint(1) NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subcategories_category_id_order_index_unique` (`category_id`,`order_index`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcategories`
--

LOCK TABLES `subcategories` WRITE;
/*!40000 ALTER TABLE `subcategories` DISABLE KEYS */;
INSERT INTO `subcategories` VALUES (2,'類別A-a','Aa',1,0,7,'2024-10-01 06:16:37','2024-10-01 06:16:37'),(3,'類別A-b','Ab',2,0,7,'2024-10-03 01:26:33','2024-10-03 01:26:33'),(4,'類別A-c','Ac',3,0,7,'2024-10-03 01:27:00','2024-10-03 01:27:00'),(5,'類別C-a','Ca',1,0,9,'2024-12-03 00:32:06','2024-12-03 00:32:06'),(9,'類別C-a','QQ',2,0,9,'2024-12-03 01:54:28','2024-12-03 01:54:28'),(11,'類別C-a','ACc',4,0,7,'2024-12-03 01:55:43','2024-12-03 01:55:43');
/*!40000 ALTER TABLE `subcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Test','test@gmail.com',NULL,'$2y$12$/8BIcIdr.wVaftoSVVnWdenXEQk7G2Hdfq27c4.h2jfHupX6/oiNK',1,'wEvqN0Yh06nhjJTRx3qlqsV2x4tIArD1Va6gxKUwJzPaAwYwmFy68JLxCf8O',NULL,'2024-10-31 23:33:52'),(2,'sf','sdfsd@yahoo.com',NULL,'$2y$12$72KuVaPKm6EljJLtG.RnVerNdOdpptdnv0P6i.dPFPcAWouionIku',0,NULL,'2024-11-04 00:13:24','2024-11-04 00:13:24');
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

-- Dump completed on 2024-12-10 22:25:39
