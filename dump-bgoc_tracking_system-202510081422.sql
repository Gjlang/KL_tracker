-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: bgoc_tracking_system
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `artwork_editings`
--

DROP TABLE IF EXISTS `artwork_editings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artwork_editings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL DEFAULT 2025,
  `month` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_artwork` varchar(255) DEFAULT NULL,
  `pending` varchar(255) DEFAULT NULL,
  `draft_wa` tinyint(1) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `artwork_editings_master_file_id_year_month_unique` (`master_file_id`,`year`,`month`),
  UNIQUE KEY `artwork_editings_master_year_month_unique` (`master_file_id`,`year`,`month`),
  KEY `artwork_editings_master_file_id_index` (`master_file_id`),
  CONSTRAINT `artwork_editings_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artwork_editings`
--

LOCK TABLES `artwork_editings` WRITE;
/*!40000 ALTER TABLE `artwork_editings` DISABLE KEYS */;
/*!40000 ALTER TABLE `artwork_editings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `billboards`
--

DROP TABLE IF EXISTS `billboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `billboards` (
  `id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `site_number` varchar(255) NOT NULL,
  `site_type` varchar(255) DEFAULT NULL,
  `gps_latitude` varchar(255) NOT NULL,
  `gps_longitude` varchar(255) NOT NULL,
  `traffic_volume` varchar(255) DEFAULT NULL,
  `size` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `prefix` varchar(255) NOT NULL,
  `lighting` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `gps_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `billboards_location_id_foreign` (`location_id`),
  KEY `billboards_created_by_foreign` (`created_by`),
  KEY `billboards_updated_by_foreign` (`updated_by`),
  CONSTRAINT `billboards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `billboards_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `billboards_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `billboards`
--

LOCK TABLES `billboards` WRITE;
/*!40000 ALTER TABLE `billboards` DISABLE KEYS */;
INSERT INTO `billboards` VALUES (2,2,'TB-NSB-0001-MBS-A',NULL,'2.8240058','101.7997796','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 09:47:22','2025-09-30 14:15:30',NULL),(3,3,'TB-NSB-0002-MBS-A',NULL,'2.6937479','101.9128172','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 09:48:59','2025-09-30 14:15:34',NULL),(4,4,'TB-SEL-0001-MPKJ-A','existing_1','3.032992','101.4371438','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:04:58','2025-09-30 09:55:29','https://maps.app.goo.gl/VsUVdnSQP1USTDwd6'),(5,5,'TB-SEL-0002-MPKJ-A','existing_1','3.053607','101.462329','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:06:23','2025-09-30 09:55:46','https://maps.app.goo.gl/8hgUrauDnCRZaSSr8'),(6,6,'TB-SEL-0003-MPKJ-A',NULL,'3.0355219','101.4412962','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:07:51','2025-09-02 10:07:51',NULL),(7,7,'TB-SEL-0004-MPAJ-A',NULL,'3.2000921','101.7777082','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:08:59','2025-09-02 10:08:59',NULL),(8,8,'TB-SEL-0005-MPAJ-A',NULL,'3.1193678','101.743002','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:10:18','2025-09-02 10:10:18',NULL),(9,9,'TB-SEL-0006-MPKJ-A',NULL,'3.0053881','101.7855107','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:12:05','2025-09-02 10:12:05',NULL),(10,10,'TB-SEL-0007-MPKJ-A',NULL,'3.0429246','101.7913962','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:13:38','2025-09-02 10:13:38',NULL),(11,11,'TB-SEL-0008-MPKJ-A','new','2.903418','101.720541','1','10x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:15:00','2025-09-29 15:24:32','https://maps.app.goo.gl/9QptyLaLU46B7sJJA'),(12,12,'TB-SEL-0009-MPKJ-A','new','2.907045','101.710907','1','10x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:16:18','2025-09-29 15:26:35','https://maps.app.goo.gl/UKcytmBUoWk8U8JF6'),(13,13,'TB-SEL-0010-MDKS-A',NULL,'3.2365763','101.4892308','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:17:51','2025-09-02 10:17:51',NULL),(14,14,'TB-SEL-0011-MDKS-A','existing_1','3.2120754','101.6403736','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:19:44','2025-09-30 10:11:08','https://maps.app.goo.gl/WhRf8tYi2B6iLBrKA'),(15,15,'TB-SEL-0012-MDKS-A',NULL,'3.050381','101.774039','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:21:59','2025-09-02 10:21:59',NULL),(16,16,'TB-SEL-0013-MPS-A','existing_3','3.2343738','101.6792048','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:23:46','2025-09-25 12:35:38','https://maps.app.goo.gl/BYio2HhJ6pVmaAAT6'),(17,17,'TB-SEL-0014-MPS-A',NULL,'3.2446152','101.6588715','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:25:19','2025-09-02 10:25:19',NULL),(18,18,'TB-SEL-0015-MPS-A',NULL,'3.2774129','101.4532294','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:27:11','2025-09-02 10:27:11',NULL),(19,19,'TB-SEL-0016-MPS-A',NULL,'3.3040428','101.5962887','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:29:05','2025-09-02 10:29:05',NULL),(20,20,'TB-SEL-0017-MPS-A',NULL,'3.313655','101.536189','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 10:30:23','2025-09-02 10:30:23',NULL),(21,21,'TB-SEL-0018-MPS-A','existing_1','3.278255','101.452702','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:25:51','2025-09-30 10:01:44','https://maps.app.goo.gl/yPEJR4SCKNiCzkVg8'),(22,22,'TB-SEL-0019-MDSK-A',NULL,'3.0328652','101.6788969','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:29:04','2025-09-02 16:29:04',NULL),(23,23,'TB-SEL-0020-MBSJ-A','existing_1','2.9846137','101.662668','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:31:34','2025-09-30 10:02:02','https://maps.app.goo.gl/9Yt9dUB46uSpCp9a6'),(24,24,'TB-SEL-0021-MDSK-A',NULL,'3.0192353','101.7187767','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:33:48','2025-09-02 16:33:48',NULL),(25,25,'TB-SEL-0022-MDSK-A',NULL,'2.9932365','101.6631793','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:35:56','2025-09-02 16:35:56',NULL),(26,26,'TB-SEL-0023-MDSK-A',NULL,'3.0192353','101.7187767','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:38:00','2025-09-02 16:38:00',NULL),(27,27,'TB-SEL-0024-MDSK-A','new','3.0271171','101.7099607','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:39:29','2025-09-29 14:36:42','https://maps.app.goo.gl/U7Bq5MRjBbSyrBWm6'),(28,28,'TB-SEL-0025-MBSJ-A','existing_1','2.979036','101.661632','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:41:02','2025-09-30 10:02:28','https://maps.app.goo.gl/eYHxfGiv6V7be1sE8'),(29,29,'TB-SEL-0026-MPSepang-A','rejected','2.939875','101.661101','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:43:02','2025-09-30 14:05:49','https://maps.app.goo.gl/2zXGfRdZrJXxvjU77'),(31,31,'TB-SEL-0028-MBSJ-A','existing_1','3.0428752','101.7002731','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:46:44','2025-09-30 10:02:49','https://maps.app.goo.gl/5PA8ezGwgGfzkeiW6'),(34,34,'TB-SEL-0031-MBSJ-A',NULL,'3.0633005','101.5567981','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:55:15','2025-09-02 16:55:15',NULL),(36,30,'TB-SEL-0033-MBSJ-A','rejected','3.073345','101.599071','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 16:58:33','2025-09-22 15:40:08',NULL),(37,36,'TB-SEL-0034-MBSJ-A','existing_1','3.073568','101.586389','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:00:28','2025-09-30 09:56:36','https://maps.app.goo.gl/USmGsHFBPQmZZvq98'),(38,35,'TB-SEL-0035-MBSJ-A','rejected','3.067533','101.613756','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:02:12','2025-09-22 16:29:22',NULL),(39,37,'TB-SEL-0036-MBSJ-A',NULL,'3.0547506','101.5553248','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:05:02','2025-09-02 17:05:02',NULL),(40,38,'TB-SEL-0037-MPKJ-A','existing_1','2.9570776','101.7555908','1','10x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:07:01','2025-09-24 16:56:58',NULL),(41,39,'TB-SEL-0038-MPKJ-A',NULL,'2.9627245','101.7597266','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:13:18','2025-09-02 17:13:18',NULL),(42,40,'TB-SEL-0039-MPKJ-A',NULL,'2.9337328','101.7643152','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:14:43','2025-09-02 17:14:43',NULL),(44,41,'TB-SEL-0040-MPKJ-A',NULL,'2.965029','101.7741285','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:16:29','2025-09-02 17:16:29',NULL),(45,42,'TB-SEL-0041-MBPJ-A',NULL,'3.1856917','101.5805882','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:17:49','2025-09-02 17:17:49',NULL),(46,43,'TB-SEL-0042-MBPJ-A',NULL,'3.0651524','101.6135091','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:19:16','2025-09-02 17:19:16',NULL),(47,44,'TB-SEL-0043-MBPJ-A',NULL,'3.159036','101.5639578','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:20:43','2025-09-02 17:20:43',NULL),(48,45,'TB-SEL-0044-MBPJ-A','existing_1','3.1499946','101.5889706','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:22:04','2025-09-30 09:57:03','https://maps.app.goo.gl/nGYAzd5EwV2Z9oja8'),(49,46,'TB-SEL-0045-MBPJ-A','existing_1','3.1338843','101.5936509','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:23:21','2025-09-30 09:58:40','https://maps.app.goo.gl/LCApmLo8UfaD2mUXA'),(50,47,'TB-SEL-0046-MBPJ-A',NULL,'3.126378','101.623150','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:24:36','2025-09-02 17:24:36',NULL),(51,48,'TB-SEL-0047-MBPJ-A',NULL,'3.0327015','101.6188575','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:26:11','2025-09-02 17:26:11',NULL),(52,49,'TB-SEL-0048-MBPJ-A',NULL,'3.1746209','101.5741036','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:28:39','2025-09-02 17:28:39',NULL),(53,50,'TB-SEL-0049-MBPJ-A','existing_3','3.112912','101.611370','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:31:55','2025-09-25 12:41:34','https://maps.app.goo.gl/juaPvMutS9cCVr1DA'),(55,52,'TB-SEL-0051-MBPJ-A',NULL,'3.1089317','101.5830461','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:34:46','2025-09-02 17:34:46',NULL),(56,53,'TB-SEL-0052-MBSA-A',NULL,'3.1387451','101.5291488','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:36:32','2025-09-02 17:36:32',NULL),(57,54,'TB-SEL-0053-MBSA-A',NULL,'3.0785115','101.5498325','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:38:07','2025-09-02 17:38:07',NULL),(59,56,'TB-SEL-0055-MBSA-A',NULL,'3.0808516','101.554549','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:41:03','2025-09-02 17:41:03',NULL),(60,57,'TB-SEL-0056-MBSA-A',NULL,'3.0581526','101.4947346','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:42:10','2025-09-02 17:42:10',NULL),(61,58,'TB-SEL-0057-MBSA-A',NULL,'3.0018893','101.5436451','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:43:29','2025-09-02 17:43:29',NULL),(62,59,'TB-SEL-0058-MBSA-A',NULL,'3.0961194','101.5553248','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:44:58','2025-09-02 17:44:58',NULL),(63,60,'TB-SEL-0059-MBSA-A',NULL,'3.0813264','101.4814574','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:46:31','2025-09-02 17:46:31',NULL),(64,61,'TB-SEL-0060-MBSA-A',NULL,'3.0452481','101.546617','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:47:44','2025-09-02 17:47:44',NULL),(65,62,'TB-SEL-0061-MBSA-A',NULL,'3.0878517','101.5484577','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:48:55','2025-09-02 17:48:55',NULL),(66,63,'TB-SEL-0062-MBSA-A',NULL,'3.078750','101.494611','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:50:29','2025-09-02 17:50:29',NULL),(67,64,'TB-SEL-0063-MBSA-A',NULL,'3.0749583','101.5492168','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:51:47','2025-09-02 17:51:47',NULL),(68,65,'TB-SEL-0064-MBSA-A',NULL,'3.1234065','101.6831768','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:52:52','2025-09-02 17:52:52',NULL),(69,66,'TB-SEL-0065-MBSA-A',NULL,'3.0800488','101.5512115','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:54:17','2025-09-02 17:54:17',NULL),(70,67,'TB-SEL-0066-MBSA-A',NULL,'3.0509579','101.5352087','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 17:55:32','2025-09-02 17:55:32',NULL),(72,69,'TB-SEL-0068-MBSA-A',NULL,'3.0879669','101.5531685','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 18:02:44','2025-09-02 18:02:44',NULL),(73,70,'TB-SEL-0069-MBPJ-A','rejected','3.070836','101.542362','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 18:10:20','2025-09-30 14:05:35','https://maps.app.goo.gl/4xv3QQby4Wap9SF27'),(75,72,'TB-WPK-0001-DBKL-A','rejected','3.160370','101.722102','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-02 18:14:13','2025-09-25 15:05:21',NULL),(77,74,'BB-WPK-0004-DBKL-A',NULL,'3.0403118','101.6726286','1','15x10','Billboard','BB','None','1',1,NULL,'2025-09-02 18:16:38','2025-09-23 17:37:22',NULL),(78,75,'TB-WPK-0004-DBKL-A','rejected','3.158355','101.681570','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:19:45','2025-09-25 15:05:42','https://maps.app.goo.gl/i14XVAgFAD3sQDcY8'),(80,77,'TB-WPK-0006-DBKL-A','rejected','3.117296','101.723634','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:22:13','2025-09-25 15:05:58','https://maps.app.goo.gl/dtDnoasAtBZCwMXSA'),(81,78,'TB-WPK-0007-DBKL-A','rejected','3.143678','101.706713','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:26:08','2025-09-25 15:06:12','https://maps.app.goo.gl/nG1ipernuJeqHc836'),(82,79,'TB-WPK-0008-DBKL-A','rejected','3.136078','101.672511','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:41:14','2025-09-25 15:06:30','https://maps.app.goo.gl/czssR3MwGTCq5JDf7'),(83,80,'TB-WPK-0009-DBKL-A','rejected','3.164042','101.668339','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:42:18','2025-09-30 14:07:13','https://maps.app.goo.gl/YLKrcBMNNEoLu2rp7'),(87,84,'TB-WPK-0013-DBKL-A',NULL,'3.1699976','101.6908087','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:54:32','2025-09-23 17:15:14',NULL),(88,85,'TB-WPK-0014-DBKL-A',NULL,'3.1547317','101.7336345','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:55:37','2025-09-23 17:15:14',NULL),(89,86,'TB-WPK-0015-DBKL-A',NULL,'2.9974415','101.786724','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:56:49','2025-09-23 17:15:14',NULL),(90,87,'TB-WPK-0016-DBKL-A',NULL,'3.0799153','101.7427843','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:58:04','2025-09-23 17:15:14',NULL),(91,88,'TB-WPK-0017-DBKL-A',NULL,'3.1179386','101.6660729','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 09:59:16','2025-09-23 17:15:14',NULL),(92,89,'TB-WPK-0018-DBKL-A',NULL,'3.047856','101.5319981','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:00:29','2025-09-23 17:15:14',NULL),(93,90,'TB-WPK-0019-DBKL-A',NULL,'3.1339781','101.7346265','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:01:58','2025-09-23 17:15:14',NULL),(94,91,'TB-WPK-0020-DBKL-A',NULL,'3.1092588','101.728191','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:02:41','2025-09-23 17:15:14',NULL),(95,92,'TB-WPK-0021-DBKL-A',NULL,'3.1081915','101.7429508','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:04:02','2025-09-23 17:15:14',NULL),(96,93,'TB-WPK-0022-DBKL-A',NULL,'3.1261691','101.7250801','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:04:54','2025-09-23 17:15:14',NULL),(97,94,'TB-WPK-0023-DBKL-A',NULL,'3.065906','101.7650463','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:06:22','2025-09-23 17:15:14',NULL),(98,95,'TB-WPK-0024-DBKL-A','existing_1','3.050381','101.774039','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:07:26','2025-09-30 10:08:07','https://maps.app.goo.gl/KUVaMDvQbFRHC9gg9'),(99,73,'TB-WPK-0025-DBKL-A','existing_1','3.1498981','101.6748125','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:07:58','2025-09-30 10:19:20','https://maps.app.goo.gl/fnvcPUS7owJ4K9c19'),(101,97,'TB-WPK-0027-DBKL-A',NULL,'3.075073','101.7360847','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:11:40','2025-09-23 17:15:14',NULL),(102,98,'TB-WPK-0028-DBKL-A',NULL,'3.1011833','101.7229068','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:12:42','2025-09-23 17:15:14',NULL),(103,99,'TB-WPK-0029-DBKL-A',NULL,'3.216368','101.6892622','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:13:35','2025-09-23 17:15:14',NULL),(104,74,'TB-WPK-0030-DBKL-A',NULL,'3.0364053','101.6766034','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:14:26','2025-09-23 17:15:14',NULL),(105,100,'TB-WPK-0031-DBKL-A',NULL,'3.1591459','101.6268433','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:15:35','2025-09-23 17:15:14',NULL),(106,76,'TB-WPK-0032-DBKL-A',NULL,'3.1704031','101.6636608','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:16:47','2025-09-23 17:15:14',NULL),(107,101,'TB-WPK-0033-DBKL-A',NULL,'3.1700156','101.670723','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:17:47','2025-09-23 17:15:14',NULL),(108,102,'TB-WPK-0034-DBKL-A',NULL,'3.2103522','101.7292407','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:19:06','2025-09-23 17:15:14',NULL),(110,104,'TB-WPK-0036-DBKL-A','existing_1','3.1344031','101.7131772','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:21:44','2025-09-30 10:21:35','https://maps.app.goo.gl/gfCaoAFRoUE5Bo9R6'),(111,105,'TB-WPK-0037-DBKL-A',NULL,'3.1234065','101.6831768','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:22:55','2025-09-23 17:15:14',NULL),(112,106,'TB-WPK-0038-DBKL-A',NULL,'3.2251027','101.6893468','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:23:58','2025-09-23 17:15:14',NULL),(113,107,'TB-WPK-0039-DBKL-A','existing_1','3.1393822','101.6819009','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:25:03','2025-09-30 10:10:05','https://maps.app.goo.gl/qaE73qo8wkU3Hw469'),(114,108,'TB-WPK-0040-DBKL-A',NULL,'3.2251027','101.6893468','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:26:10','2025-09-23 17:15:14',NULL),(115,109,'TB-WPK-0041-DBKL-A','existing_1','3.1879026','101.669856','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:27:06','2025-09-30 10:11:25','https://maps.app.goo.gl/eDUpxd5GfQ11F8Mx7'),(116,110,'TB-WPK-0042-DBKL-A','existing_1','3.135672','101.703921','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:28:14','2025-09-30 10:20:43','https://maps.app.goo.gl/MJdiPakuEPgx2Aah6'),(117,111,'TB-WPK-0043-DBKL-A','existing_1','3.160667','101.732162','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:29:23','2025-09-30 10:09:35','https://maps.app.goo.gl/H2MGdSuSXkUNf4J89'),(119,113,'TB-WPK-0045-DBKL-A','existing_1','3.1544574','101.68106','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:31:50','2025-09-30 10:19:36','https://maps.app.goo.gl/FwhuUuMRkvi8MpA87'),(120,114,'TB-WPK-0046-DBKL-A','existing_1','3.143729','101.706048','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-03 10:32:47','2025-09-30 10:21:52','https://maps.app.goo.gl/G49SjP22YK1xsPmP7'),(121,115,'BB-SEL-0001-MBPJ-A',NULL,'3.113721','101.600321','3604680','30x20','Billboard','BB','TNB','1',1,NULL,'2025-09-03 15:11:18','2025-09-03 15:11:50',NULL),(122,116,'BB-SEL-0002-MBPJ-A',NULL,'3.113721','101.600321','3604680','30x20','Billboard','BB','TNB','1',1,NULL,'2025-09-03 15:14:11','2025-09-03 15:14:11',NULL),(123,117,'BB-SEL-0005-MBPJ-A',NULL,'3.110951','101.579143','4866690','30x20','Billboard','BB','SOLAR','1',1,NULL,'2025-09-03 15:24:18','2025-09-03 15:24:29',NULL),(124,118,'BB-SEL-0006-MBPJ-A',NULL,'3.110951','101.579143','4866690','30x20','Billboard','BB','SOLAR','1',1,NULL,'2025-09-03 15:25:41','2025-09-03 15:25:41',NULL),(125,119,'BB-SEL-0008-MPS-A',NULL,'3.2324279','101.6757559','4820220','30x20','Billboard','BB','TNB','1',1,NULL,'2025-09-03 15:27:01','2025-09-03 15:29:06',NULL),(126,120,'BB-SEL-0009-MPS-A',NULL,'3.2324279','101.6757559','4820220','30x20','Billboard','BB','TNB','1',1,NULL,'2025-09-03 15:28:15','2025-09-03 15:29:12',NULL),(127,121,'BB-SEL-0010-MBSJ-A',NULL,'3.023945','101.712352','5386286','30x20','Billboard','BB','SOLAR','1',1,NULL,'2025-09-03 15:30:20','2025-09-03 15:30:20',NULL),(128,122,'BB-SEL-0011-MBSJ-A',NULL,'3.023945','101.712352','5386286','30x20','Billboard','BB','SOLAR','1',1,NULL,'2025-09-03 15:31:29','2025-09-03 15:31:29',NULL),(129,123,'BB-SEL-0012-MBSJ-A',NULL,'2.972684','101.574003','5386286','30x20','Billboard','BB','SOLAR','1',1,NULL,'2025-09-03 15:32:34','2025-09-03 15:32:34',NULL),(130,124,'BB-SEL-0013-MBSJ-A',NULL,'2.972684','101.574003','5386286','30x20','Billboard','BB','SOLAR','1',1,NULL,'2025-09-03 15:34:27','2025-09-03 15:34:27',NULL),(131,125,'BB-SEL-0018-MBSJ-A',NULL,'2.9933778','101.615991','3024460','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 15:35:34','2025-09-03 15:35:45',NULL),(132,126,'BB-SEL-0019-MBSJ-A',NULL,'2.9933778','101.615991','3024460','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 15:36:41','2025-09-03 15:36:41',NULL),(133,127,'BB-SEL-0016-MBSJ-A',NULL,'2.945823','101.592001','5386286','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 15:57:28','2025-09-03 15:58:45',NULL),(134,128,'BB-SEL-0017-MBSJ-A',NULL,'2.945823','101.592001','5386286','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 15:58:35','2025-09-03 15:58:49',NULL),(135,129,'BB-SEL-0014-MBSJ-A',NULL,'3.151153','101.597376','3810550','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 16:01:04','2025-09-03 16:01:19',NULL),(136,130,'BB-SEL-0015-MBSJ-A',NULL,'3.151153','101.597376','3810550','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 16:07:30','2025-09-03 16:07:30',NULL),(137,131,'BB-WPK-0003-DBKL-A',NULL,'3.173047','101.619260','3604680','10x40','Billboard','BB','None','1',1,NULL,'2025-09-03 16:09:26','2025-09-30 15:55:08',NULL),(138,132,'BB-WPK-0001-DBKL-A',NULL,'3.055123','101.667378','3185255','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 16:11:26','2025-09-03 16:11:41',NULL),(139,133,'BB-WPK-0002-DBKL-A',NULL,'3.055123','101.667378','3185255','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 16:12:34','2025-09-23 17:15:14',NULL),(140,134,'BB-SEL-0022-MBPJ-A',NULL,'3.157432','101.5724969','2424420','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 16:21:30','2025-09-03 16:21:44',NULL),(141,135,'BB-SEL-0023-MBPJ-A',NULL,'3.157432','101.5724969','2424460','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 16:23:40','2025-09-03 16:23:40',NULL),(142,136,'BB-SEL-0024-MBPJ-A',NULL,'3.157432','101.5724969','1026640','30x20','Billboard','BB','None','1',1,NULL,'2025-09-03 16:25:05','2025-09-03 16:25:05',NULL),(145,139,'TB-SEL-0026-MBSA-A','existing','3.1668891','101.5686235','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-10 18:20:56','2025-09-22 16:25:41',NULL),(146,140,'TB-SEL-0027-MBPJ-A','existing_1','3.1499565','101.5889955','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-10 18:22:38','2025-09-30 09:56:00','https://maps.app.goo.gl/m5figHbvZuLDA2SWA'),(147,141,'TB-WPK-0050-DBKL-A','existing_1','3.08304','101.6632855','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-10 18:29:07','2025-09-30 10:19:51','https://maps.app.goo.gl/eGQqv4RHkLXJcSkb6'),(152,145,'TB-WPK-0096-DBKL-A','rejected','3.160022','101.650842','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-10 19:23:28','2025-09-30 14:06:50','https://maps.app.goo.gl/65uRt6xUVHaoC3yq7'),(154,147,'TB-WPK-0097-DBKL-A','new','3.1357557','101.672674','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 09:18:42','2025-09-24 11:21:25',NULL),(155,148,'TB-WPK-0098-DBKL-A','duplicate','3.1344748','101.7131213','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 09:22:02','2025-09-25 15:06:52','https://maps.app.goo.gl/cacGf9gQ1RtM6HhG7'),(156,149,'TB-WPK-0099-DBKL-A','existing','3.2104999','101.6718244','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 09:23:52','2025-09-24 11:23:57',NULL),(157,150,'TB-WPK-0100-DBKL-A','new','3.183185','101.663884','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 09:40:12','2025-09-24 11:25:26',NULL),(158,151,'TB-WPK-0101-DBKL-A','existing','3.173404','101.663767','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 09:41:59','2025-09-24 11:26:28',NULL),(159,152,'TB-WPK-0102-DBKL-A','new','3.170198','101.652980','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 09:44:12','2025-09-24 11:37:35',NULL),(160,153,'TB-WPK-0103-DBKL-A','new','3.163333','101.658086','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:00:52','2025-09-25 15:08:34','https://maps.app.goo.gl/dk2sBT3Dg2huixaE6'),(161,154,'TB-SEL-0091-MPKJ-A','new','2.964515','101.773865','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:08:46','2025-09-22 21:13:19',NULL),(162,155,'TB-SEL-0092-MPKJ-A','existing_3','2.936219','101.769886','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:12:15','2025-09-25 09:45:02','https://maps.app.goo.gl/V6k8wtsVU3ThyQWP8'),(163,156,'TB-SEL-0093-MPS-A','new','3.240629','101.699648','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:14:45','2025-09-22 21:14:47',NULL),(164,157,'TB-SEL-0032-MPS-A','existing_3','3.235560','101.641361','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:18:10','2025-09-25 09:37:54','https://maps.app.goo.gl/MZtp2MKWmuczFhci9'),(165,158,'TB-SEL-0094-MBPJ-A','existing_3','3.1602775','101.5866858','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:22:54','2025-09-30 15:14:31','https://maps.app.goo.gl/fSKjCRjHga3sFc9r7'),(167,160,'TB-WPK-0061-DBKL-A','new','3.017973','101.675106','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:29:07','2025-09-23 17:15:14',NULL),(168,161,'TB-SEL-0096-MBSA-A','rejected','3.088968','101.504802','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:30:57','2025-09-30 14:06:26','https://maps.app.goo.gl/x8jjb5CFaN18chZ67'),(169,162,'TB-SEL-0097-MBSJ-A','existing_3','3.1722498','101.5447112','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:33:22','2025-09-30 15:38:32','https://maps.app.goo.gl/jh9VFFSLx81Y2w9CA'),(170,163,'TB-SEL-0098-MBSA-A','existing','3.208462','101.573808','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:41:02','2025-09-22 21:20:53',NULL),(172,165,'TB-WPK-0062-DBKL-A','existing','3.1343346','101.713426','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-12 10:50:34','2025-09-23 17:15:14',NULL),(174,167,'TB-WPK-0063-DBKL-A','existing','3.1755832','101.6765322','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:23:20','2025-09-23 17:15:14',NULL),(175,168,'TB-SEL-0130-MBPJ-A','existing_1','3.16677','101.5686139','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:27:07','2025-09-30 10:07:10','https://maps.app.goo.gl/dwco11dEyzBEiFHp6'),(177,169,'TB-WPK-0064-DBKL-A','existing_1','3.149873','101.674746','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:30:33','2025-09-30 10:10:35','https://maps.app.goo.gl/NqjLqABFh3feFGxn8'),(178,170,'TB-SEL-0040-MBSJ-A','existing_1','3.0734076','101.5975609','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:31:30','2025-09-30 09:57:21','https://maps.app.goo.gl/E1PxVM4E1YL4ufBeA'),(179,171,'TB-WPK-0065-DBKL-A','rejected','3.166632','101.714284','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:31:34','2025-09-23 17:15:14',NULL),(180,172,'TB-WPK-0066-DBKL-A','existing_1','3.095435','101.694920','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:33:31','2025-09-30 10:20:09','https://maps.app.goo.gl/YMtfVLXedZsMFwQm8'),(183,175,'TB-SEL-0102-MBPJ-A','existing','3.157193','101.6155512','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:46:36','2025-09-22 21:28:07',NULL),(184,176,'TB-SEL-0103-MBPJ-A','existing_1','3.1571903','101.6155508','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:47:58','2025-09-30 09:58:57','https://maps.app.goo.gl/tbmNhQXFk4BawQ2f8'),(185,177,'TB-SEL-0104-MBPJ-A','existing_1','3.0786028','101.6137372','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:50:27','2025-09-30 10:22:57','https://maps.app.goo.gl/yaQGedi4TgY42Dg66'),(186,178,'TB-SEL-0105-MBPJ-A','existing','3.1136449','101.5998863','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:51:48','2025-09-22 21:28:31',NULL),(187,179,'TB-SEL-0106-MBPJ-A','existing','3.078319','101.6137263','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:52:20','2025-09-22 21:28:39',NULL),(189,181,'TB-WPK-0068-DBKL-A','existing','3.056175','101.664214','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:57:30','2025-09-23 17:15:14',NULL),(190,182,'TB-WPK-0069-DBKL-A','existing','3.072114','101.762089','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:58:48','2025-09-23 17:15:14',NULL),(191,183,'TB-WPK-0070-DBKL-A','existing','3.044353','101.775289','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 16:59:29','2025-09-23 17:15:14',NULL),(192,184,'TB-WPK-0071-DBKL-A','existing','3.053864','101.773994','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:00:24','2025-09-23 17:15:14',NULL),(193,185,'TB-WPK-0072-DBKL-A','existing','3.071891','101.762322','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:01:16','2025-09-23 17:15:14',NULL),(194,186,'TB-SEL-0107-MBPJ-A','existing_2','3.113505','101.601645','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:03:14','2025-09-24 18:26:10','https://maps.app.goo.gl/EjBSYxjRxy3QgZX26'),(195,187,'TB-SEL-0108-MBPJ-A','rejected','3.129355','101.624024','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:24:28','2025-09-25 15:12:46','https://maps.app.goo.gl/xXBtwSPBGC1zps5k9'),(196,188,'TB-WPK-0109-DBKL-A','existing_2','3.1930132','101.6192588','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:25:53','2025-09-30 15:50:33','https://maps.app.goo.gl/CwwBTELLdqnBTSk37'),(198,190,'TB-WPK-0073-DBKL-A','existing_1','3.0815173','101.7253021','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:29:27','2025-09-30 10:20:25','https://maps.app.goo.gl/GhcE4hcNw1tUd5YL6'),(199,191,'TB-SEL-0111-MBSJ-A','existing','3.023081','101.713628','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:35:54','2025-09-22 21:31:39',NULL),(200,192,'TB-WPK-0074-DBKL-A','existing','3.132342','101.649958','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:37:19','2025-09-23 17:15:14',NULL),(201,193,'TB-SEL-0112-MBSJ-A','rejected','3.0293838','101.7075796','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:41:26','2025-09-25 15:17:02',NULL),(202,194,'TB-SEL-0113-MBPJ-A','new','3.1823646','101.6028706','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:46:48','2025-09-29 15:02:06','https://maps.app.goo.gl/v1QMuLTABzhACZ1u9'),(203,195,'TB-SEL-0114-MBSJ-A','existing_1','3.0650243','101.5989674','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:48:03','2025-09-30 10:06:27','https://maps.app.goo.gl/72Pb2rVtTUeKfQoH9'),(204,196,'TB-SEL-0115-MBPJ-A','existing','3.1263796','101.623234','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:50:00','2025-09-22 21:35:07',NULL),(205,197,'TB-SEL-0116-MBDK-A','existing_3','3.048736','101.4527975','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:54:28','2025-09-30 15:33:02','https://maps.app.goo.gl/sXwYFu2fLGvCopqs6'),(206,198,'TB-SEL-0117-MBPJ-A','existing_1','3.0841022','101.5968409','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:56:03','2025-09-30 09:59:23','https://maps.app.goo.gl/tteoKuUpWPvojU4f6'),(207,199,'TB-WPK-0075-DBKL-A','new','3.017973','101.675106','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:58:53','2025-09-25 15:08:20','https://maps.app.goo.gl/GWnt4JAdGAxg6Bru9'),(208,200,'TB-WPK-0076-DBKL-A','existing_1','3.1601587','101.7421964','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 17:59:14','2025-09-30 10:09:50','https://maps.app.goo.gl/cdkjsJadwntT8mKJ7'),(209,201,'TB-SEL-0118-MBSA-A','existing_1','3.0805072','101.5528682','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:00:43','2025-09-30 10:03:56','https://maps.app.goo.gl/h3zRve5dG1xUhwDU6'),(210,202,'TB-SEL-0119-MPKJ-A','existing_1','3.0926889','101.7400706','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:00:45','2025-09-30 09:55:17','https://maps.app.goo.gl/ZQcSPxxQ5R7o5ttN6'),(212,204,'TB-WPK-0078-DBKL-A','existing_2','3.0938906','101.7396743','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:02:34','2025-09-24 17:40:11','https://maps.app.goo.gl/CDyBEjpeyR4jRaC7A'),(213,205,'TB-WPK-0079-DBKL-A','existing','3.085715','101.741675','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:03:59','2025-09-23 17:15:14',NULL),(214,206,'TB-SEL-0120-MBPJ-A','existing_3','3.155034','101.612910','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:04:17','2025-09-25 10:12:24','https://maps.app.goo.gl/LrcL1rCxL3bp17FN7'),(215,207,'TB-WPK-0080-DBKL-A','rejected','3.140830','101.680737','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:05:04','2025-09-23 17:15:14',NULL),(216,208,'TB-SEL-0121-MBPJ-A','existing_1','3.1502403','101.5801957','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:05:40','2025-09-30 09:56:15','https://maps.app.goo.gl/bivnVYRTaEcg4XWaA'),(217,209,'TB-WPK-0081-DBKL-A','existing_1','3.139703','101.6814148','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:06:02','2025-09-30 10:10:21','https://maps.app.goo.gl/od9yoofCSQUuED5D8'),(218,210,'TB-WPK-0082-DBKL-A','existing','3.170001','101.670374','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:06:56','2025-09-23 17:15:14',NULL),(219,211,'TB-WPK-0083-DBKL-A','new','3.1831559','101.6637334','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:07:44','2025-09-30 14:40:39','https://maps.app.goo.gl/AKMj8BvttstQZXtr8'),(220,212,'TB-SEL-0122-MPDK-A','duplicate','3.069745','101.414801','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:09:23','2025-09-25 15:11:10','https://maps.app.goo.gl/atRBH8ih7W4KXqxq5'),(221,213,'TB-SEL-0123-MPDK-A','duplicate','3.066924','101.425249','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:10:07','2025-09-25 15:11:20','https://maps.app.goo.gl/MwjQAyKa3KEwnH9K8'),(222,214,'TB-SEL-0124-MBSJ-A','existing','3.081779','101.592783','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:11:27','2025-09-22 21:42:25',NULL),(223,215,'TB-SEL-0125-MBPJ-A','rejected','3.151190','101.601581','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:12:04','2025-09-30 14:06:06','https://maps.app.goo.gl/85UxFiiz8TEcmCSK9'),(224,216,'TB-WPK-0084-DBKL-A','rejected','3.170198','101.652980','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:12:57','2025-09-25 15:08:54','https://maps.app.goo.gl/xbTK3zu42zmQE8k86'),(225,217,'TB-SEL-0126-MBSA-A','rejected','3.024158','101.517245','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:13:44','2025-09-25 15:13:37','https://maps.app.goo.gl/m5AnLFnU7KfqvXFF7'),(226,218,'TB-WPK-0085-DBKL-A','existing','3.210542','101.671843','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:14:01','2025-09-24 09:12:22',NULL),(227,219,'TB-SEL-0127-MBPJ-A','existing','3.126378','101.623150','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:15:27','2025-09-22 21:44:18',NULL),(228,220,'TB-SEL-0128-MBSA-A','new','3.208385','101.5734542','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:16:58','2025-09-30 15:36:53','https://maps.app.goo.gl/9N5bQPdKWU58j4Aw8'),(230,222,'TB-SEL-0071-MBSJ-A','duplicate','3.109989','101.580704','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:18:35','2025-09-25 15:13:48','https://maps.app.goo.gl/AHj1fTqByg8ng7zp8'),(231,223,'TB-SEL-0072-MBSA-A','rejected','3.078750','101.494611','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:19:24','2025-09-30 14:05:15','https://maps.app.goo.gl/sN2XCqpjwcx5vpNz6'),(232,224,'TB-WPK-0086-DBKL-A','existing_1','3.079625','101.7200881','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:19:35','2025-09-30 10:21:06','https://maps.app.goo.gl/h7EjYCko9uAPKBADA'),(233,225,'TB-WPK-0087-DBKL-A','rejected','3.134500','101.705952','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:20:49','2025-09-23 17:15:14',NULL),(234,226,'TB-WPK-0088-DBKL-A','existing','3.142581','101.662957','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:24:06','2025-09-23 17:15:14',NULL),(235,227,'TB-WPK-0089-DBKL-A','existing_2','3.1426487','101.6630274','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:25:08','2025-09-30 14:16:28','https://maps.app.goo.gl/74jrYdsHMcDtD9w16'),(236,228,'TB-SEL-0073-MPS-A','existing_2','3.3200253','101.5793383','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:29:26','2025-09-30 14:24:34','https://maps.app.goo.gl/s3ZDFq7aKxAwzYmC8'),(237,229,'TB-SEL-0074-MBPJ-A','existing_1','3.0807831','101.6102779','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:30:33','2025-09-23 22:08:05',NULL),(239,231,'TB-WPK-0090-DBKL-A','rejected','3.184412','101.691798','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:35:42','2025-09-25 15:09:37','https://maps.app.goo.gl/2d2CHRDsVc9W3sHV7'),(240,232,'TB-SEL-0076-MBSJ-A','duplicate','3.0658419','101.5931352','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:37:04','2025-09-25 15:14:49','https://maps.app.goo.gl/djRUfSDZPkknuArF7'),(241,233,'TB-SEL-0077-MBSJ-A','existing_1','3.1100427','101.5804517','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:53:13','2025-09-30 09:59:44','https://maps.app.goo.gl/8XcjRR8ZBJ6UJEbV7'),(242,234,'TB-WPK-0091-DBKL-A','existing_3','3.203384','101.670130','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:54:41','2025-09-25 10:05:42','https://maps.app.goo.gl/BYPrtqobi7aXRkHY9'),(243,235,'TB-SEL-0078-MBSA-A','existing','2.972684','101.574003','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:55:33','2025-09-22 18:55:33',NULL),(244,236,'TB-SEL-0079-MBSA-A','existing','2.978395','101.572128','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:56:26','2025-09-22 18:56:26',NULL),(245,237,'TB-SEL-0080-MPKJ-A','existing','2.9506502','101.7577097','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:58:12','2025-09-22 18:58:12',NULL),(246,238,'TB-SEL-0081-MBSA-A','existing_1','3.0773433','101.5627954','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:59:07','2025-09-30 10:04:10','https://maps.app.goo.gl/MMbL1cKrCxGGgMBL8'),(247,239,'TB-SEL-0082-MBSJ-A','existing_2','3.0819933','101.5734399','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 18:59:58','2025-09-25 14:33:43','https://maps.app.goo.gl/TgJSxuzqDCJ1qBEn9'),(248,240,'TB-SEL-0083-MPS-A','existing','3.2496305','101.6975601','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 19:04:14','2025-09-22 19:04:14',NULL),(250,242,'TB-SEL-0085-MBSJ-A','existing_2','3.0525452','101.6246091','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 19:07:16','2025-09-24 17:42:48','https://maps.app.goo.gl/YVciKGw45okTXzMg8'),(251,243,'TB-SEL-0086-MBSJ-A','existing','2.979850','101.613963','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 19:08:59','2025-09-22 19:08:59',NULL),(252,244,'TB-WPK-0047-DBKL-A','existing','3.134333','101.713389','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 19:09:45','2025-09-23 17:15:14',NULL),(253,245,'TB-WPK-0048-DBKL-A','rejected','3.1437024','101.7068555','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 19:10:22','2025-09-23 17:15:14',NULL),(255,246,'TB-WPK-0049-DBKL-A','existing','3.120573','101.708075','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 19:12:03','2025-09-23 17:15:14',NULL),(256,247,'TB-SEL-0088-MBPJ-A','existing','3.10287','101.34254','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 19:16:38','2025-09-22 19:16:38',NULL),(257,248,'TB-SEL-0089-MBSA-A','existing','3.0808795','101.5542463','0','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-22 19:17:27','2025-09-22 19:17:27',NULL),(258,249,'TB-SEL-0090-MBSJ-A','existing_1','2.9784117','101.6621775','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 15:43:57','2025-09-30 10:03:08','https://maps.app.goo.gl/ExdMxCvNZfhEcUKp9'),(259,250,'TB-SEL-0091-MBPJ-A','existing_1','3.0633581','101.6141642','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 15:52:52','2025-09-30 10:00:03','https://maps.app.goo.gl/zaLsDVBPv18kFR197'),(260,251,'TB-SEL-0092-MBPJ-A','existing_1','3.112632','101.6112723','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 15:54:40','2025-09-30 10:00:18','https://maps.app.goo.gl/VyHbNcFxmvn3Ydye8'),(261,252,'TB-SEL-0093-MBPJ-A','existing_1','3.0961384','101.6299621','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 15:56:10','2025-09-30 10:00:46','https://maps.app.goo.gl/UhBr9eZuHYTWQqJz6'),(262,253,'TB-SEL-0094-MBSA-A','existing_1','3.159116','101.5167747','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:00:38','2025-09-30 10:04:31','https://maps.app.goo.gl/qS5WJ7HwdjbPvMHw7'),(263,254,'TB-SEL-0095-MBSA-A','existing_1','3.0769984','101.5612176','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:03:09','2025-09-30 10:04:47','https://maps.app.goo.gl/xhvFkwRRVkCzdci59'),(266,256,'TB-WPK-0052-DBKL-A','existing_1','3.1903054','101.6213004','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:07:58','2025-09-30 10:08:59','https://maps.app.goo.gl/WPPNn1b7gVocfEnk8'),(267,96,'TB-WPK-0053-DBKL-A','existing_1','3.0754623','101.6893709','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:09:10','2025-09-30 10:22:25','https://maps.app.goo.gl/Fonr5VXrZS6Rc3Cu8'),(268,257,'TB-SEL-0054-MBSJ-A','existing_1','3.0458999','101.6470608','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:11:40','2025-09-30 10:01:09','https://maps.app.goo.gl/mHGafPtxQjJjcVE8A'),(269,258,'TB-SEL-0096-MBPJ-A','existing_1','3.126355','101.6230245','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:22:02','2025-09-30 10:05:59','https://maps.app.goo.gl/zHLuo2cdsckx6wxU7'),(270,259,'TB-SEL-0097-MBSJ-A','existing_1','3.0261538','101.7090308','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:30:42','2025-09-30 10:03:35','https://maps.app.goo.gl/cDeh41dcHNDL4Pby6'),(271,260,'TB-SEL-0098-MBSJ-A','existing_1','3.078999','101.5855677','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:32:49','2025-09-30 10:06:49','https://maps.app.goo.gl/hmXteo9xiimTGEBs5'),(272,261,'TB-WPK-0055-DBKL-A','existing_1','3.0443904','101.7752679','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:36:07','2025-09-30 10:08:21','https://maps.app.goo.gl/zJmbAMW3KwVpoXx2A'),(273,243,'TB-SEL-0099-MBSJ-A','existing_1','2.9795279','101.6138777','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:38:22','2025-09-30 10:01:25','https://maps.app.goo.gl/MTywatwT1ZCzMQ6E6'),(274,262,'TB-WPK-0056-DBKL-A','existing_1','3.1660274','101.7535154','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:39:23','2025-09-30 10:21:21','https://maps.app.goo.gl/Qzn4ZxJPF39UCQKD6'),(275,263,'TB-WPK-0057-DBKL-A','existing_1','3.055861','101.6595103','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:40:51','2025-09-30 10:07:50','https://maps.app.goo.gl/k2RujTipBKzDEmcz9'),(276,264,'TB-WPK-0058-DBKL-A','existing_1','3.1203253','101.708092','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:43:03','2025-09-30 10:22:41','https://maps.app.goo.gl/Cu5K7jwwLCE2HUYo8'),(277,265,'TB-WPK-0059-DBKL-A','existing_1','3.1700163','101.6707233','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:44:35','2025-09-30 10:09:16','https://maps.app.goo.gl/YjcSTpVs6V8ynKzW6'),(278,192,'TB-WPK-0060-DBKL-A','existing_1','3.1326295','101.6504219','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:49:30','2025-09-30 10:22:09','https://maps.app.goo.gl/oforQD3gQSGq1ppv7'),(279,266,'TB-SEL-0100-MBPJ-A','existing_1','3.1548224','101.6041125','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 16:55:59','2025-09-30 09:58:18','https://maps.app.goo.gl/ezaSvhXVARhdJ3Wp6'),(280,267,'TB-SEL-0101-MBSA-A','existing_1','3.1240018','101.4677356','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-23 21:52:31','2025-09-30 10:05:05','https://maps.app.goo.gl/U5b7fBtWx61J2Qaq6'),(285,272,'TB-WPK-0094-DBKL-A','existing_2','3.1428624','101.6628645','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 10:52:53','2025-09-24 17:39:20','https://maps.app.goo.gl/GEjhW1hXhm2vyWpW6'),(286,273,'TB-WPK-0095-DBKL-A','existing_2','3.0559913','101.6639774','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 11:08:15','2025-09-30 14:18:21','https://maps.app.goo.gl/pb8RKFqDZUgibEAK7'),(287,274,'TB-SEL-0131-MBPJ-A','existing_2','3.073308','101.600337','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 11:11:04','2025-09-24 17:41:38','https://maps.app.goo.gl/Ekgei7FbUMSxgm5R8'),(288,275,'TB-SEL-0132-MBSJ-A','existing_2','3.0658421','101.5931352','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 11:13:45','2025-09-24 17:42:08','https://maps.app.goo.gl/JNc9kCZVp1C1HKr17'),(289,276,'TB-SEL-0133-MBSJ-A','existing_2','3.081779','101.592783','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 11:22:45','2025-09-24 17:43:09','https://maps.app.goo.gl/w2XGPD8NBMjyUNyn7'),(290,277,'TB-SEL-0134-MPDK-A','existing_2','3.0697504','101.4121791','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 14:49:50','2025-09-24 17:44:17','https://maps.app.goo.gl/XNR9hcFQ8sfCv49BA'),(291,278,'TB-SEL-0135-MBDK-A','existing_2','3.0669793','101.4253979','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 14:53:04','2025-09-30 14:39:23','https://maps.app.goo.gl/zig83RPowRS9z2F38'),(292,279,'TB-NSB-0136-MBS-A','existing_2','2.7673467','101.8254118','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 14:57:29','2025-09-30 14:19:23','https://maps.app.goo.gl/bsGEPf2Wp1kZvrQS8'),(294,281,'TB-SEL-0137-MPS-A','new','3.240629','101.699648','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 16:46:55','2025-09-25 15:10:04','https://maps.app.goo.gl/hAKtPWewnLLNgYJu6'),(295,282,'TB-WPK-0104-DBKL-A','duplicate','3.055952','101.660079','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 16:51:14','2025-09-25 15:04:31','https://maps.app.goo.gl/DwDYBH6JNMVJfkWA9'),(296,283,'TB-SEL-0138-MBSA-A','duplicate','3.0819933','101.5734399','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 17:05:46','2025-09-25 15:17:17','https://maps.app.goo.gl/AHj1fTqByg8ng7zp8'),(297,284,'TB-SEL-0139-MBPJ-A','duplicate','3.1571903','101.6155512','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 18:21:47','2025-09-25 15:15:50','https://maps.app.goo.gl/jLw1LmZXkapPScpf7'),(298,285,'TB-SEL-0140-MBPJ-A','new','3.0992079','101.6072215','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 18:57:39','2025-09-30 15:33:46','https://maps.app.goo.gl/zT5vMRAnQ2BY2YqQA'),(299,286,'TB-SEL-0141-MBSA-A','new','3.1002065','101.5409215',NULL,'15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 18:59:05','2025-09-30 15:22:02','https://maps.app.goo.gl/Mzh5h6PorSQSeqPh9'),(300,287,'TB-SEL-0142-MBSJ-A','rejected','3.081834','101.587295',NULL,'15x10','Tempboard','TB','None','1',1,NULL,'2025-09-24 19:03:57','2025-09-25 18:24:04','https://maps.app.goo.gl/ktwY2mVqFiQekTK67'),(301,288,'TB-SEL-0143-MPKJ-A','new','2.9732369','101.7497586','1','10x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 12:53:23','2025-09-30 15:00:12','https://maps.app.goo.gl/6VEft3g2TAF7GcRBA'),(302,289,'TB-NSB-0137-MBS-A','new','2.857237','101.863102','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 12:55:52','2025-09-30 14:15:37','https://maps.app.goo.gl/43zMkCmEi3Ys7D6AA'),(303,290,'TB-SEL-0144-MPKJ-A','new','2.991524','101.759973','1','10x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:06:22','2025-09-29 14:06:22','https://maps.app.goo.gl/SN9PLftdiU8MbaNf7'),(304,291,'TB-NSB-0138-MBS-A','new','2.8066034','101.7571779','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:08:27','2025-09-30 15:27:31','https://maps.app.goo.gl/nVUcxUe7ucZN2P6X7'),(305,292,'TB-SEL-0145-MBSJ-A','new','3.0270117','101.7195826','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:10:45','2025-09-30 15:20:24','https://maps.app.goo.gl/8ioBi5Ru4ycWAq9u7'),(306,293,'TB-WPK-0110-DBKL-A','new','3.1640375','101.6874022','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:16:15','2025-09-30 15:25:52','https://maps.app.goo.gl/7VK53JSxdjqiAfpe8'),(308,295,'TB-WPK-0111-DBKL-A','new','3.1525052','101.6499162','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:20:34','2025-09-30 14:39:59','https://maps.app.goo.gl/oFW4jBgVFyPWwSLz7'),(309,296,'TB-SEL-0147-MBSA-A','new','3.1089174','101.5829403','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:27:04','2025-09-30 15:37:41','https://maps.app.goo.gl/jBykgW9kta24pb7g6'),(310,297,'TB-SEL-0148-MPDK-A','new','3.053952','101.4630505','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:32:59','2025-09-29 14:32:59','https://maps.app.goo.gl/kAmA6CJbEnVV91bGA'),(311,298,'TB-SEL-0149-MBSJ-A','new','2.981518','101.656667','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:34:53','2025-09-30 14:28:28','https://maps.app.goo.gl/r2grJVBQimUnjnsv6'),(312,299,'TB-SEL-0150-MPS-A','new','3.303946','101.596833','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:39:28','2025-09-29 14:39:28','https://maps.app.goo.gl/zh21EXAfBynSV6Un8'),(313,300,'TB-SEL-0151-MBPJ-A','new','3.154735','101.557103','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:43:55','2025-09-29 14:43:55','https://maps.app.goo.gl/u6zjFzxcfs5kpovJA'),(314,301,'TB-SEL-0152-MBPJ-A','new','3.0740779','101.6462197','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:46:10','2025-09-30 15:34:24','https://maps.app.goo.gl/xWTBQx8JhiSAaWB98'),(315,302,'TB-SEL-0153-MBPJ-A','new','3.074010','101.645302','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 14:50:14','2025-09-29 14:50:14','https://maps.app.goo.gl/zoKpJvVWBdNk82JN7'),(316,303,'TB-WPK-0112-DBKL-A','new','3.2280198','101.7269426','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:03:00','2025-09-29 15:03:00','https://maps.app.goo.gl/2zzJrw1Vz2fzjg7P7'),(317,304,'TB-SEL-0154-MBPJ-A','new','3.075578','101.634694','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:06:51','2025-09-29 15:06:51','https://maps.app.goo.gl/mCbdQEUoEsJVi69i7'),(318,305,'TB-SEL-0155-MBPJ-A','new','3.0760813','101.6364243','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:08:46','2025-09-30 15:15:03','https://maps.app.goo.gl/ABwDBjW93u1LkSvg9'),(319,306,'TB-NSB-0139-MBS-A','new','2.771177','101.820399','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:10:28','2025-09-30 14:15:44','https://maps.app.goo.gl/eqtNMTGRH7bT7QzT7'),(320,307,'TB-WPK-0113-DBKL-A','new','3.220344','101.734110','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:12:45','2025-09-29 15:12:45','https://maps.app.goo.gl/N4c6P7kTMLjC1wGP8'),(321,308,'TB-SEL-0156-MPSP-A','new','2.8292155','101.6930195','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:15:11','2025-09-30 14:35:08','https://maps.app.goo.gl/Vv56Szc25tz7X9BQ8'),(322,309,'TB-SEL-0157-MPKJ-A','new','2.957909','101.7296145','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:18:12','2025-09-30 15:07:23','https://maps.app.goo.gl/cRQ9xRJmHwedWTob6'),(323,310,'TB-SEL-0158-MPKJ-A','new','2.973352','101.733402','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:19:49','2025-09-30 14:30:54','https://maps.app.goo.gl/vDUgvYbLW5hfSfCWA'),(324,311,'TB-SEL-0159-MBSJ-A','new','2.958286','101.7158272','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:22:51','2025-09-30 15:36:02','https://maps.app.goo.gl/8gaufZFLLExaovXN6'),(325,312,'TB-SEL-0160-MBSJ-A','new','2.9797975','101.6600575','1','15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:28:22','2025-09-30 14:32:50','https://maps.app.goo.gl/vFhC1fQ1vCEqiccK7'),(326,313,'TB-SEL-0161-MBSJ-A','new','2.9725719','101.5739598',NULL,'15x10','Tempboard','TB','None','1',1,NULL,'2025-09-29 15:30:37','2025-10-06 08:02:56','https://maps.app.goo.gl/8uK1vhgGfg1G2amz6');
/*!40000 ALTER TABLE `billboards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:18:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"dashboard.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:15:\"masterfile.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:15:\"masterfile.show\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:17:\"masterfile.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:18:\"masterfile.monthly\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:16:\"coordinator.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:9:\"kltg.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:12:\"outdoor.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:10:\"media.edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:10:\"export.run\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:4;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:15:\"calendar.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:22:\"information.booth.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:24:\"information.booth.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:17:\"masterfile.import\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:13:\"calendar.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:17:\"masterfile.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:19:\"report.summary.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:21:\"report.summary.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:4:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"support\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:4:\"user\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:7:\"limited\";s:1:\"c\";s:3:\"web\";}}}',1759906434);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_companies`
--

DROP TABLE IF EXISTS `client_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_companies_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_companies`
--

LOCK TABLES `client_companies` WRITE;
/*!40000 ALTER TABLE `client_companies` DISABLE KEYS */;
INSERT INTO `client_companies` VALUES (1,'Eyelevel Malaysia','Petaling Jaya','0162271762',1,'2025-09-05 17:18:21','2025-09-05 17:18:21',NULL),(2,'IPC Shopping Mall','KL','0123456789',1,'2025-09-05 18:07:12','2025-09-05 18:07:12',NULL),(3,'Region Food / Life Sauce','kl','0123456789',1,'2025-09-05 18:15:26','2025-09-05 18:15:26',NULL),(4,'More Design','kl','0123456789',1,'2025-09-05 18:16:09','2025-09-05 18:16:09',NULL),(5,'The Pastel Shop','kl','0121111112',1,'2025-09-05 18:16:49','2025-10-07 03:49:07',NULL),(6,'Lynn\'s Catering','kl','0123456789',1,'2025-09-05 18:17:26','2025-09-05 18:17:26',NULL),(7,'Merdeka Trading','kl','0123456789',1,'2025-09-05 18:18:09','2025-09-05 18:18:09',NULL),(8,'Rayson','kl','0123456789',1,'2025-09-05 18:18:45','2025-09-05 18:18:45',NULL),(9,'IJN','kl','0123456789',1,'2025-09-05 18:19:13','2025-09-05 18:19:13',NULL),(10,'AIC Exhibitions',NULL,NULL,1,'2025-09-09 16:57:19','2025-09-10 18:04:50',NULL),(11,'Marvel',NULL,NULL,1,'2025-09-09 16:57:32','2025-09-09 16:57:32',NULL),(12,'KLPJ Wedding Fair',NULL,NULL,1,'2025-09-10 18:06:39','2025-09-10 18:06:39',NULL),(19,'test',NULL,NULL,1,'2025-10-07 07:58:54','2025-10-07 07:58:54',NULL);
/*!40000 ALTER TABLE `client_companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `client_feed_backlogs`
--

DROP TABLE IF EXISTS `client_feed_backlogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_feed_backlogs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `expected_finish_date` date DEFAULT NULL,
  `servicing` varchar(255) DEFAULT NULL,
  `product` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `client` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `status` enum('pending','in-progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `attended_by` varchar(255) DEFAULT NULL,
  `reasons` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `billboard_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_feed_backlogs_master_file_id_index` (`master_file_id`),
  KEY `client_feed_backlogs_date_index` (`date`),
  KEY `client_feed_backlogs_client_index` (`client`),
  KEY `client_feed_backlogs_status_index` (`status`),
  KEY `client_feed_backlogs_expected_finish_date_index` (`expected_finish_date`),
  KEY `client_feed_backlogs_company_index` (`company`),
  KEY `client_feed_backlogs_billboard_FK` (`billboard_id`),
  KEY `client_feed_backlogs_client_company_FK` (`company_id`),
  CONSTRAINT `client_feed_backlogs_billboard_FK` FOREIGN KEY (`billboard_id`) REFERENCES `billboards` (`id`) ON DELETE SET NULL,
  CONSTRAINT `client_feed_backlogs_client_company_FK` FOREIGN KEY (`company_id`) REFERENCES `client_companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_feed_backlogs`
--

LOCK TABLES `client_feed_backlogs` WRITE;
/*!40000 ALTER TABLE `client_feed_backlogs` DISABLE KEYS */;
INSERT INTO `client_feed_backlogs` VALUES (1,NULL,'2025-09-17','2025-09-19','AG','KLTG','Kuala Lumpur','Elvin Noon','FastBridge','completed','AG',NULL,'2025-09-16 05:26:46','2025-09-16 05:31:56',NULL,NULL),(2,NULL,'2025-09-23','2025-09-24','AG','KLTG','Kuala Lumpur','Fatiya','FastBridge','pending','AG','None','2025-09-21 23:17:48','2025-09-21 23:17:48',NULL,NULL);
/*!40000 ALTER TABLE `client_feed_backlogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clients_company_id_foreign` (`company_id`),
  CONSTRAINT `clients_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `client_companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Fatihah','abc@gmail.com','0162271762','Manager',1,1,'2025-09-05 17:18:21','2025-09-05 17:18:21',NULL),(2,'Vickie','test@gmail.com','0123456789','Mr.',2,1,'2025-09-05 18:07:12','2025-09-05 18:07:12',NULL),(3,'Life Sauce','test@gmail.com','0123456789','Mr.',3,1,'2025-09-05 18:15:26','2025-09-05 18:15:26',NULL),(4,'More Design','test@gmail.com','0123456789','Mr.',4,1,'2025-09-05 18:16:09','2025-09-05 18:16:09',NULL),(5,'saja','test2@gmail.com','0123456789','Mr.',5,1,'2025-09-05 18:16:49','2025-10-07 07:55:39',NULL),(6,'Lynn\'s Catering','test@gmail.com','0123456789','Mr.',6,1,'2025-09-05 18:17:26','2025-09-05 18:17:26',NULL),(7,'Merdeka Trading','test@gmail.com','0123456789','Mr.',7,1,'2025-09-05 18:18:09','2025-09-05 18:18:09',NULL),(8,'Rayson','test@gmail.com','0123456789','Mr.',8,1,'2025-09-05 18:18:45','2025-09-05 18:18:45',NULL),(9,'IJN','test@gmail.com','0123456789','Mr.',9,1,'2025-09-05 18:19:13','2025-09-05 18:19:13',NULL),(10,'Yong Keat / Allen',NULL,'0172437202',NULL,10,1,'2025-09-09 16:57:19','2025-09-10 18:05:17',NULL),(11,'Marvel',NULL,NULL,NULL,11,1,'2025-09-09 16:57:32','2025-09-09 16:57:32',NULL),(12,'Alvin',NULL,'0122837386',NULL,12,1,'2025-09-10 18:06:39','2025-09-10 18:06:39',NULL),(13,'Celine',NULL,'01128107651',NULL,12,1,'2025-09-10 18:06:39','2025-09-10 18:06:39',NULL),(24,'test1',NULL,NULL,'test',19,1,'2025-10-07 07:58:54','2025-10-07 08:01:11',NULL),(25,'test2',NULL,NULL,NULL,19,1,'2025-10-07 07:58:54','2025-10-07 07:58:54',NULL),(26,'test3',NULL,NULL,NULL,19,1,'2025-10-07 07:58:54','2025-10-07 07:58:54',NULL),(29,'test4','test4@gmail.com','0121112222','Mr.',19,1,'2025-10-07 08:07:39','2025-10-07 08:08:10',NULL);
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `content_calendars`
--

DROP TABLE IF EXISTS `content_calendars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_calendars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL DEFAULT 2025,
  `month` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_artwork` varchar(255) DEFAULT NULL,
  `pending` varchar(255) DEFAULT NULL,
  `draft_wa` tinyint(1) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_calendars_master_file_id_year_month_unique` (`master_file_id`,`year`,`month`),
  UNIQUE KEY `content_calendars_master_year_month_unique` (`master_file_id`,`year`,`month`),
  KEY `content_calendars_master_file_id_index` (`master_file_id`),
  CONSTRAINT `content_calendars_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `content_calendars`
--

LOCK TABLES `content_calendars` WRITE;
/*!40000 ALTER TABLE `content_calendars` DISABLE KEYS */;
/*!40000 ALTER TABLE `content_calendars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contractors`
--

DROP TABLE IF EXISTS `contractors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contractors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contractors`
--

LOCK TABLES `contractors` WRITE;
/*!40000 ALTER TABLE `contractors` DISABLE KEYS */;
/*!40000 ALTER TABLE `contractors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `councils`
--

DROP TABLE IF EXISTS `councils`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `councils` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `councils_state_id_foreign` (`state_id`),
  CONSTRAINT `councils_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `councils`
--

LOCK TABLES `councils` WRITE;
/*!40000 ALTER TABLE `councils` DISABLE KEYS */;
INSERT INTO `councils` VALUES (1,1,'Majlis Bandaraya Petaling Jaya','MBPJ','2025-09-01 17:40:23','2025-09-01 17:40:23'),(2,1,'Majlis Bandaraya Shah Alam','MBSA','2025-09-01 17:40:23','2025-09-01 17:40:23'),(3,1,'Majlis Bandaraya Subang Jaya','MBSJ','2025-09-01 17:40:23','2025-09-01 17:40:23'),(4,1,'Majlis Bandaraya Diraja Klang','MBDK','2025-09-01 17:40:23','2025-09-01 17:40:23'),(5,1,'Majlis Perbandaran Ampang Jaya','MPAJ','2025-09-01 17:40:23','2025-09-01 17:40:23'),(6,1,'Majlis Perbandaran Selayang','MPS','2025-09-01 17:40:23','2025-09-01 17:40:23'),(7,1,'Majlis Daerah Kuala Selangor','MDKS','2025-09-01 17:40:23','2025-09-01 17:40:23'),(8,1,'Majlis Daerah Hulu Selangor','MDHS','2025-09-01 17:40:23','2025-09-01 17:40:23'),(9,1,'Majlis Perbandaran Kajang','MPKJ','2025-09-01 17:40:23','2025-09-01 17:40:23'),(10,1,'Majlis Daerah Sabak Bernam','MDSB','2025-09-01 17:40:23','2025-09-01 17:40:23'),(11,1,'Majlis Daerah Seri Kembangan','MDSK','2025-09-01 17:40:23','2025-09-01 17:40:23'),(12,1,'Majlis Daerah Sepang','MPSP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(13,1,'Majlis Daerah Kuala Langat','MDKL','2025-09-01 17:40:24','2025-09-01 17:40:24'),(14,2,'Dewan Bandaraya Kuala Lumpur','DBKL','2025-09-01 17:40:24','2025-09-01 17:40:24'),(15,3,'Perbadanan Putrajaya','PPJ','2025-09-01 17:40:24','2025-09-01 17:40:24'),(16,4,'Perbadanan Labuan','PL','2025-09-01 17:40:24','2025-09-01 17:40:24'),(17,5,'Majlis Bandaraya Johor Bahru','MBJB','2025-09-01 17:40:24','2025-09-01 17:40:24'),(18,5,'Majlis Bandaraya Iskandar Puteri','MBIP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(19,5,'Majlis Perbandaran Batu Pahat','MPBP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(20,5,'Majlis Perbandaran Kluang','MPKluang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(21,5,'Majlis Perbandaran Muar','MPMuar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(22,5,'Majlis Perbandaran Segamat','MPSegamat','2025-09-01 17:40:24','2025-09-01 17:40:24'),(23,5,'Majlis Perbandaran Kulai','MPKulai','2025-09-01 17:40:24','2025-09-01 17:40:24'),(24,5,'Majlis Perbandaran Pontian','MPPn','2025-09-01 17:40:24','2025-09-01 17:40:24'),(25,5,'Majlis Perbandaran Pengerang','MPPengerang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(26,5,'Majlis Bandaraya Pasir Gudang','MBPG','2025-09-01 17:40:24','2025-09-01 17:40:24'),(27,5,'Majlis Daerah Kota Tinggi','MDKT','2025-09-01 17:40:24','2025-09-01 17:40:24'),(28,5,'Majlis Daerah Mersing','MDMersing','2025-09-01 17:40:24','2025-09-01 17:40:24'),(29,5,'Majlis Daerah Tangkak','MDTangkak','2025-09-01 17:40:24','2025-09-01 17:40:24'),(30,5,'Majlis Daerah Simpang Renggam','MDSR','2025-09-01 17:40:24','2025-09-01 17:40:24'),(31,5,'Majlis Daerah Labis','MDLabis','2025-09-01 17:40:24','2025-09-01 17:40:24'),(32,5,'Majlis Daerah Yong Peng','MDYP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(33,6,'Majlis Bandaraya Pulau Pinang','MBPP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(34,6,'Majlis Bandaraya Seberang Perai','MBSP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(35,7,'Majlis Bandaraya Ipoh','MBI','2025-09-01 17:40:24','2025-09-01 17:40:24'),(36,7,'Majlis Perbandaran Taiping','MPTaiping','2025-09-01 17:40:24','2025-09-01 17:40:24'),(37,7,'Majlis Perbandaran Manjung','MPM','2025-09-01 17:40:24','2025-09-01 17:40:24'),(38,7,'Majlis Daerah Perak Tengah','MDPT','2025-09-01 17:40:24','2025-09-01 17:40:24'),(39,7,'Majlis Perbandaran Kuala Kangsar','MPKKPK','2025-09-01 17:40:24','2025-09-01 17:40:24'),(40,7,'Majlis Daerah Selama','MDSelama','2025-09-01 17:40:24','2025-09-01 17:40:24'),(41,7,'Majlis Daerah Batu Gajah','MDBG','2025-09-01 17:40:24','2025-09-01 17:40:24'),(42,7,'Majlis Daerah Kampar','MDKampar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(43,7,'Majlis Daerah Gerik','MDG','2025-09-01 17:40:24','2025-09-01 17:40:24'),(44,7,'Majlis Daerah Lenggong','MDLG','2025-09-01 17:40:24','2025-09-01 17:40:24'),(45,7,'Majlis Daerah Pengkalan Hulu','MDPH','2025-09-01 17:40:24','2025-09-01 17:40:24'),(46,7,'Majlis Daerah Tapah','MDTapah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(47,7,'Majlis Daerah Tanjong Malim','MDTM','2025-09-01 17:40:24','2025-09-01 17:40:24'),(48,7,'Majlis Perbandaran Teluk Intan','MPTI','2025-09-01 17:40:24','2025-09-01 17:40:24'),(49,7,'Majlis Daerah Kerian','MDKerian','2025-09-01 17:40:24','2025-09-01 17:40:24'),(50,8,'Majlis Bandaraya Kuantan','MBK','2025-09-01 17:40:24','2025-09-01 17:40:24'),(51,8,'Majlis Perbandaran Temerloh','MPT','2025-09-01 17:40:24','2025-09-01 17:40:24'),(52,8,'Majlis Perbandaran Bentong','MPBENTONG','2025-09-01 17:40:24','2025-09-01 17:40:24'),(53,8,'Majlis Perbandaran Pekan Bandar Diraja','MPPekan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(54,8,'Majlis Daerah Lipis','MDLipis','2025-09-01 17:40:24','2025-09-01 17:40:24'),(55,8,'Majlis Daerah Cameron Highlands','MDCH','2025-09-01 17:40:24','2025-09-01 17:40:24'),(56,8,'Majlis Daerah Raub','MDRaub','2025-09-01 17:40:24','2025-09-01 17:40:24'),(57,8,'Majlis Daerah Bera','MDBera','2025-09-01 17:40:24','2025-09-01 17:40:24'),(58,8,'Majlis Daerah Maran','MDMaran','2025-09-01 17:40:24','2025-09-01 17:40:24'),(59,8,'Majlis Daerah Rompin','MDRompin','2025-09-01 17:40:24','2025-09-01 17:40:24'),(60,8,'Majlis Daerah Jerantut','MDJerantut','2025-09-01 17:40:24','2025-09-01 17:40:24'),(61,9,'Majlis Bandaraya Alor Setar','MBAS','2025-09-01 17:40:24','2025-09-01 17:40:24'),(62,9,'Majlis Perbandaran Sungai Petani','MPSPK','2025-09-01 17:40:24','2025-09-01 17:40:24'),(63,9,'Majlis Perbandaran Kulim','MPKK','2025-09-01 17:40:24','2025-09-01 17:40:24'),(64,9,'Majlis Perbandaran Kubang Pasu','MPKPasu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(65,9,'Majlis Perbandaran Langkawi Bandaraya Pelancongan','MPL','2025-09-01 17:40:24','2025-09-01 17:40:24'),(66,9,'Majlis Daerah Baling','MDBaling','2025-09-01 17:40:24','2025-09-01 17:40:24'),(67,9,'Majlis Daerah Yan','MDYan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(68,9,'Majlis Daerah Sik','MDSik','2025-09-01 17:40:24','2025-09-01 17:40:24'),(69,9,'Majlis Daerah Pendang','MDPendang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(70,9,'Majlis Daerah Padang Terap','MDPTerap','2025-09-01 17:40:24','2025-09-01 17:40:24'),(71,9,'Majlis Daerah Bandar Baharu','MDBB','2025-09-01 17:40:24','2025-09-01 17:40:24'),(72,10,'Majlis Perbandaran Kota Bharu - Bandar Raya Islam','MPKBBRI','2025-09-01 17:40:24','2025-09-01 17:40:24'),(73,10,'Majlis Daerah Bachok Bandar Pelancongan Islam','MDBachok','2025-09-01 17:40:24','2025-09-01 17:40:24'),(74,10,'Majlis Daerah Gua Musang','MDGM','2025-09-01 17:40:24','2025-09-01 17:40:24'),(75,10,'Majlis Daerah Jeli','MDJeli','2025-09-01 17:40:24','2025-09-01 17:40:24'),(76,10,'Majlis Daerah Ketereh - Perbandaran Islam','MDKetereh','2025-09-01 17:40:24','2025-09-01 17:40:24'),(77,10,'Majlis Daerah Dabong','MDDabong','2025-09-01 17:40:24','2025-09-01 17:40:24'),(78,10,'Majlis Daerah Kuala Krai','MDKK','2025-09-01 17:40:24','2025-09-01 17:40:24'),(79,10,'Majlis Daerah Machang','MDMachang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(80,10,'Majlis Daerah Pasir Mas','MDPM','2025-09-01 17:40:24','2025-09-01 17:40:24'),(81,10,'Majlis Daerah Pasir Puteh','MDPP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(82,10,'Majlis Daerah Tanah Merah','MDTMerah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(83,10,'Majlis Daerah Tumpat','MDTumpat','2025-09-01 17:40:24','2025-09-01 17:40:24'),(84,11,'Majlis Bandaraya Kuala Terengganu','MBKT','2025-09-01 17:40:24','2025-09-01 17:40:24'),(85,11,'Majlis Daerah Besut','MDBesut','2025-09-01 17:40:24','2025-09-01 17:40:24'),(86,11,'Majlis Daerah Setiu','MDSetiu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(87,11,'Majlis Perbandaran Dungun','MPDungun','2025-09-01 17:40:24','2025-09-01 17:40:24'),(88,11,'Majlis Daerah Hulu Terengganu','MDHT','2025-09-01 17:40:24','2025-09-01 17:40:24'),(89,11,'Majlis Perbandaran Kemaman','MPKemaman','2025-09-01 17:40:24','2025-09-01 17:40:24'),(90,11,'Majlis Daerah Marang','MDMarang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(91,12,'Majlis Bandaraya Melaka Bersejarah','MBMB','2025-09-01 17:40:24','2025-09-01 17:40:24'),(92,12,'Majlis Perbandaran Alor Gajah','MPAG','2025-09-01 17:40:24','2025-09-01 17:40:24'),(93,12,'Majlis Perbandaran Majlis Perbandaran Hang Tuah Jaya','	MPHTJ','2025-09-01 17:40:24','2025-09-01 17:40:24'),(94,12,'Majlis Perbandaran Jasin','MPJ','2025-09-01 17:40:24','2025-09-01 17:40:24'),(95,13,'Majlis Bandaraya Seremban','MBS','2025-09-01 17:40:24','2025-09-01 17:40:24'),(96,13,'Majlis Daerah Kuala Pilah','MDKP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(97,13,'Majlis Daerah Tampin','MDTampin','2025-09-01 17:40:24','2025-09-01 17:40:24'),(98,13,'Majlis Perbandaran Port Dickson','MPPD','2025-09-01 17:40:24','2025-09-01 17:40:24'),(99,13,'Majlis Daerah Jelebu','MDJelebu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(100,13,'Majlis Daerah Rembau','MDR','2025-09-01 17:40:24','2025-09-01 17:40:24'),(101,13,'Majlis Perbandaran Jempol','MPJL','2025-09-01 17:40:24','2025-09-01 17:40:24'),(102,14,'Majlis Perbandaran Kangar','MPKgr','2025-09-01 17:40:24','2025-09-01 17:40:24'),(103,15,'Dewan Bandaraya Kota Kinabalu','DBKK','2025-09-01 17:40:24','2025-09-01 17:40:24'),(104,15,'Majlis Perbandaran Sandakan','MPS','2025-09-01 17:40:24','2025-09-01 17:40:24'),(105,15,'Majlis Perbandaran Tawau','MPT','2025-09-01 17:40:24','2025-09-01 17:40:24'),(106,15,'Lembaga Bandaran Kudat','LBK','2025-09-01 17:40:24','2025-09-01 17:40:24'),(107,15,'Majlis Daerah Beaufort','MDBeaufort','2025-09-01 17:40:24','2025-09-01 17:40:24'),(108,15,'Majlis Daerah Beluran','MDBeluran','2025-09-01 17:40:24','2025-09-01 17:40:24'),(109,15,'Majlis Daerah Keningau','MDKeningau','2025-09-01 17:40:24','2025-09-01 17:40:24'),(110,15,'Majlis Daerah Kinabatangan','MDKinabatangan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(111,15,'Majlis Daerah Kota Belud','MDKB','2025-09-01 17:40:24','2025-09-01 17:40:24'),(112,15,'Majlis Daerah Kota Marudu','MDKM','2025-09-01 17:40:24','2025-09-01 17:40:24'),(113,15,'Majlis Daerah Kuala Penyu','MDKPenyu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(114,15,'Majlis Daerah Kunak','MDKunak','2025-09-01 17:40:24','2025-09-01 17:40:24'),(115,15,'Majlis Daerah Lahad Datu','MDLD','2025-09-01 17:40:24','2025-09-01 17:40:24'),(116,15,'Majlis Daerah Nabawan','MDN','2025-09-01 17:40:24','2025-09-01 17:40:24'),(117,15,'Majlis Daerah Papar','MDPapar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(118,15,'Majlis Perbandaran Penampang','MPPenampang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(119,15,'Majlis Daerah Ranau','MDRanau','2025-09-01 17:40:24','2025-09-01 17:40:24'),(120,15,'Majlis Daerah Semporna','MDSemporna','2025-09-01 17:40:24','2025-09-01 17:40:24'),(121,15,'Majlis Daerah Sipitang','MDSipitang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(122,15,'Majlis Daerah Tambunan','MDTambunan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(123,15,'Majlis Daerah Tenom','MDTenom','2025-09-01 17:40:24','2025-09-01 17:40:24'),(124,15,'Majlis Daerah Tuaran','MDTuaran','2025-09-01 17:40:24','2025-09-01 17:40:24'),(125,15,'Majlis Daerah Putatan','MDPutatan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(126,15,'Majlis Daerah Pitas','MDPitas','2025-09-01 17:40:24','2025-09-01 17:40:24'),(127,15,'Majlis Daerah Tongod','MDTongod','2025-09-01 17:40:24','2025-09-01 17:40:24'),(128,15,'Majlis Daerah Telupid','MDTelupid','2025-09-01 17:40:24','2025-09-01 17:40:24'),(129,16,'Lembaga Kemajuan Bintulu','BDA','2025-09-01 17:40:24','2025-09-01 17:40:24'),(130,16,'Dewan Bandaraya Kuching Utara','DBKU','2025-09-01 17:40:24','2025-09-01 17:40:24'),(131,16,'Majlis Bandaraya Kuching Selatan','MBKS','2025-09-01 17:40:24','2025-09-01 17:40:24'),(132,16,'Majlis Perbandaran Padawan','MPP','2025-09-01 17:40:24','2025-09-01 17:40:24'),(133,16,'Majlis Perbandaran Sibu','SMC','2025-09-01 17:40:24','2025-09-01 17:40:24'),(134,16,'Majlis Bandaraya Miri','MBM','2025-09-01 17:40:24','2025-09-01 17:40:24'),(135,16,'Majlis Daerah Bau','BAUDC','2025-09-01 17:40:24','2025-09-01 17:40:24'),(136,16,'Majlis Daerah Betong','MDBetong','2025-09-01 17:40:24','2025-09-01 17:40:24'),(137,16,'Majlis Daerah Dalat & Mukah','MDDM','2025-09-01 17:40:24','2025-09-01 17:40:24'),(138,16,'Majlis Daerah Kanowit','MDKanowit','2025-09-01 17:40:24','2025-09-01 17:40:24'),(139,16,'Majlis Daerah Kapit','MDKapit','2025-09-01 17:40:24','2025-09-01 17:40:24'),(140,16,'Majlis Daerah Lawas','MDLawas','2025-09-01 17:40:24','2025-09-01 17:40:24'),(141,16,'Majlis Daerah Limbang','MDLimbang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(142,16,'Majlis Daerah Lubok Antu','MDLA','2025-09-01 17:40:24','2025-09-01 17:40:24'),(143,16,'Majlis Daerah Lundu','MDLundu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(144,16,'Majlis Daerah Maradong & Julau','MDMJ','2025-09-01 17:40:24','2025-09-01 17:40:24'),(145,16,'Majlis Daerah Marudi','MDM','2025-09-01 17:40:24','2025-09-01 17:40:24'),(146,16,'Majlis Daerah Matu & Daro','MDMD','2025-09-01 17:40:24','2025-09-01 17:40:24'),(147,16,'Majlis Daerah Saratok','MDSaratok','2025-09-01 17:40:24','2025-09-01 17:40:24'),(148,16,'Majlis Perbandaran Kota Samarahan','MPKS','2025-09-01 17:40:24','2025-09-01 17:40:24'),(149,16,'Majlis Daerah Serian','MDSerian','2025-09-01 17:40:24','2025-09-01 17:40:24'),(150,16,'Majlis Daerah Sarikei','MDSarikei','2025-09-01 17:40:24','2025-09-01 17:40:24'),(151,16,'Majlis Daerah Simunjan','MDSimunjan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(152,16,'Majlis Daerah Sri Aman','MDSA','2025-09-01 17:40:24','2025-09-01 17:40:24'),(153,16,'Majlis Daerah Subis','MDSubis','2025-09-01 17:40:24','2025-09-01 17:40:24'),(154,16,'Majlis Daerah Luar Bandar Sibu','MDLBS','2025-09-01 17:40:24','2025-09-01 17:40:24'),(155,16,'Majlis Daerah Gedong','MDG','2025-09-01 17:40:24','2025-09-01 17:40:24'),(156,NULL,'Kementerian Kerja Raya','KKR','2025-09-01 17:40:24','2025-09-01 17:40:24');
/*!40000 ALTER TABLE `councils` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `districts`
--

DROP TABLE IF EXISTS `districts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `districts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `state_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `districts_state_id_foreign` (`state_id`),
  CONSTRAINT `districts_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `districts`
--

LOCK TABLES `districts` WRITE;
/*!40000 ALTER TABLE `districts` DISABLE KEYS */;
INSERT INTO `districts` VALUES (1,1,'Petaling','2025-09-01 17:40:23','2025-09-01 17:40:23'),(2,1,'Klang','2025-09-01 17:40:23','2025-09-01 17:40:23'),(3,1,'Hulu Langat','2025-09-01 17:40:23','2025-09-01 17:40:23'),(4,1,'Gombak','2025-09-01 17:40:23','2025-09-01 17:40:23'),(5,1,'Kuala Selangor','2025-09-01 17:40:23','2025-09-01 17:40:23'),(6,1,'Sabak Bernam','2025-09-01 17:40:23','2025-09-01 17:40:23'),(7,1,'Kuala Langat','2025-09-01 17:40:23','2025-09-01 17:40:23'),(8,1,'Hulu Selangor','2025-09-01 17:40:23','2025-09-01 17:40:23'),(9,1,'Ampang Jaya','2025-09-01 17:40:23','2025-09-01 17:40:23'),(10,1,'Kajang','2025-09-01 17:40:23','2025-09-01 17:40:23'),(11,1,'Seri Kembangan','2025-09-01 17:40:23','2025-09-01 17:40:23'),(12,1,'Sepang','2025-09-01 17:40:23','2025-09-01 17:40:23'),(13,2,'Kuala Lumpur','2025-09-01 17:40:24','2025-09-01 17:40:24'),(14,2,'Cheras','2025-09-01 17:40:24','2025-09-01 17:40:24'),(15,2,'Kepong','2025-09-01 17:40:24','2025-09-01 17:40:24'),(16,2,'Lembah Pantai','2025-09-01 17:40:24','2025-09-01 17:40:24'),(17,2,'Segambut','2025-09-01 17:40:24','2025-09-01 17:40:24'),(18,2,'Setiawangsa','2025-09-01 17:40:24','2025-09-01 17:40:24'),(19,2,'Titiwangsa','2025-09-01 17:40:24','2025-09-01 17:40:24'),(20,2,'Wangsa Maju','2025-09-01 17:40:24','2025-09-01 17:40:24'),(21,3,'Precinct 1','2025-09-01 17:40:24','2025-09-01 17:40:24'),(22,3,'Precinct 2','2025-09-01 17:40:24','2025-09-01 17:40:24'),(23,3,'Precinct 3','2025-09-01 17:40:24','2025-09-01 17:40:24'),(24,3,'Precinct 4','2025-09-01 17:40:24','2025-09-01 17:40:24'),(25,4,'Labuan Town','2025-09-01 17:40:24','2025-09-01 17:40:24'),(26,5,'Johor Bahru','2025-09-01 17:40:24','2025-09-01 17:40:24'),(27,5,'Batu Pahat','2025-09-01 17:40:24','2025-09-01 17:40:24'),(28,5,'Kluang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(29,5,'Muar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(30,5,'Segamat','2025-09-01 17:40:24','2025-09-01 17:40:24'),(31,5,'Pontian','2025-09-01 17:40:24','2025-09-01 17:40:24'),(32,5,'Kulai','2025-09-01 17:40:24','2025-09-01 17:40:24'),(33,5,'Kota Tinggi','2025-09-01 17:40:24','2025-09-01 17:40:24'),(34,5,'Mersing','2025-09-01 17:40:24','2025-09-01 17:40:24'),(35,5,'Tangkak','2025-09-01 17:40:24','2025-09-01 17:40:24'),(36,6,'Timur Laut','2025-09-01 17:40:24','2025-09-01 17:40:24'),(37,6,'Barat Daya','2025-09-01 17:40:24','2025-09-01 17:40:24'),(38,6,'Seberang Perai Utara','2025-09-01 17:40:24','2025-09-01 17:40:24'),(39,6,'Seberang Perai Tengah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(40,6,'Seberang Perai Selatan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(41,7,'Kinta','2025-09-01 17:40:24','2025-09-01 17:40:24'),(42,7,'Larut Matang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(43,7,'Hilir Perak','2025-09-01 17:40:24','2025-09-01 17:40:24'),(44,7,'Kuala Kangsar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(45,7,'Manjung','2025-09-01 17:40:24','2025-09-01 17:40:24'),(46,7,'Perak Tengah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(47,7,'Selama','2025-09-01 17:40:24','2025-09-01 17:40:24'),(48,7,'Batu Gajah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(49,7,'Kampar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(50,7,'Gerik','2025-09-01 17:40:24','2025-09-01 17:40:24'),(51,7,'Lenggong','2025-09-01 17:40:24','2025-09-01 17:40:24'),(52,7,'Pengkalan Hulu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(53,7,'Tapah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(54,7,'Tanjong Malim','2025-09-01 17:40:24','2025-09-01 17:40:24'),(55,7,'Kerian','2025-09-01 17:40:24','2025-09-01 17:40:24'),(56,8,'Kuantan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(57,8,'Temerloh','2025-09-01 17:40:24','2025-09-01 17:40:24'),(58,8,'Bentong','2025-09-01 17:40:24','2025-09-01 17:40:24'),(59,8,'Pekan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(60,8,'Kuala Lipis','2025-09-01 17:40:24','2025-09-01 17:40:24'),(61,8,'Cameron Highlands','2025-09-01 17:40:24','2025-09-01 17:40:24'),(62,8,'Raub','2025-09-01 17:40:24','2025-09-01 17:40:24'),(63,8,'Bera','2025-09-01 17:40:24','2025-09-01 17:40:24'),(64,8,'Maran','2025-09-01 17:40:24','2025-09-01 17:40:24'),(65,8,'Rompin','2025-09-01 17:40:24','2025-09-01 17:40:24'),(66,8,'Jerantut','2025-09-01 17:40:24','2025-09-01 17:40:24'),(67,9,'Alor Setar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(68,9,'Sungai Petani','2025-09-01 17:40:24','2025-09-01 17:40:24'),(69,9,'Kulim','2025-09-01 17:40:24','2025-09-01 17:40:24'),(70,9,'Kubang Pasu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(71,9,'Baling','2025-09-01 17:40:24','2025-09-01 17:40:24'),(72,9,'Yan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(73,9,'Sik','2025-09-01 17:40:24','2025-09-01 17:40:24'),(74,9,'Pendang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(75,9,'Padang Terap','2025-09-01 17:40:24','2025-09-01 17:40:24'),(76,9,'Bandar Baharu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(77,9,'Langkawi','2025-09-01 17:40:24','2025-09-01 17:40:24'),(78,10,'Kota Bharu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(79,10,'Bachok','2025-09-01 17:40:24','2025-09-01 17:40:24'),(80,10,'Gua Musang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(81,10,'Jeli','2025-09-01 17:40:24','2025-09-01 17:40:24'),(82,10,'Ketereh','2025-09-01 17:40:24','2025-09-01 17:40:24'),(83,10,'Dabong','2025-09-01 17:40:24','2025-09-01 17:40:24'),(84,10,'Kuala Krai','2025-09-01 17:40:24','2025-09-01 17:40:24'),(85,10,'Machang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(86,10,'Pasir Mas','2025-09-01 17:40:24','2025-09-01 17:40:24'),(87,10,'Pasir Puteh','2025-09-01 17:40:24','2025-09-01 17:40:24'),(88,10,'Tanah Merah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(89,10,'Tumpat','2025-09-01 17:40:24','2025-09-01 17:40:24'),(90,11,'Kuala Terengganu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(91,11,'Besut','2025-09-01 17:40:24','2025-09-01 17:40:24'),(92,11,'Setiu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(93,11,'Dungun','2025-09-01 17:40:24','2025-09-01 17:40:24'),(94,11,'Hulu Terengganu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(95,11,'Kemaman','2025-09-01 17:40:24','2025-09-01 17:40:24'),(96,11,'Marang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(97,12,'Melaka Tengah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(98,12,'Alor Gajah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(99,12,'Jasin','2025-09-01 17:40:24','2025-09-01 17:40:24'),(100,12,'Majlis Perbandaran Hang Tuah Jaya','2025-09-01 17:40:24','2025-09-01 17:40:24'),(101,13,'Seremban','2025-09-01 17:40:24','2025-09-01 17:40:24'),(102,13,'Port Dickson','2025-09-01 17:40:24','2025-09-01 17:40:24'),(103,13,'Jempol','2025-09-01 17:40:24','2025-09-01 17:40:24'),(104,13,'Kuala Pilah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(105,13,'Tampin','2025-09-01 17:40:24','2025-09-01 17:40:24'),(106,13,'Jelebu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(107,13,'Rembau','2025-09-01 17:40:24','2025-09-01 17:40:24'),(108,14,'Kangar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(109,14,'Arau','2025-09-01 17:40:24','2025-09-01 17:40:24'),(110,14,'Padang Besar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(111,15,'Kota Kinabalu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(112,15,'Sandakan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(113,15,'Tawau','2025-09-01 17:40:24','2025-09-01 17:40:24'),(114,15,'Kudat','2025-09-01 17:40:24','2025-09-01 17:40:24'),(115,15,'Beaufort','2025-09-01 17:40:24','2025-09-01 17:40:24'),(116,15,'Beluran','2025-09-01 17:40:24','2025-09-01 17:40:24'),(117,15,'Keningau','2025-09-01 17:40:24','2025-09-01 17:40:24'),(118,15,'Kinabatangan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(119,15,'Kota Belud','2025-09-01 17:40:24','2025-09-01 17:40:24'),(120,15,'Kota Marudu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(121,15,'Kuala Penyu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(122,15,'Kunak','2025-09-01 17:40:24','2025-09-01 17:40:24'),(123,15,'Lahad Datu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(124,15,'Nabawan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(125,15,'Papar','2025-09-01 17:40:24','2025-09-01 17:40:24'),(126,15,'Penampang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(127,15,'Ranau','2025-09-01 17:40:24','2025-09-01 17:40:24'),(128,15,'Semporna','2025-09-01 17:40:24','2025-09-01 17:40:24'),(129,15,'Sipitang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(130,15,'Tambunan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(131,15,'Tenom','2025-09-01 17:40:24','2025-09-01 17:40:24'),(132,15,'Tuaran','2025-09-01 17:40:24','2025-09-01 17:40:24'),(133,15,'Putatan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(134,15,'Pitas','2025-09-01 17:40:24','2025-09-01 17:40:24'),(135,15,'Tongod','2025-09-01 17:40:24','2025-09-01 17:40:24'),(136,15,'Telupid','2025-09-01 17:40:24','2025-09-01 17:40:24'),(137,16,'Bintulu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(138,16,'Kuching','2025-09-01 17:40:24','2025-09-01 17:40:24'),(139,16,'Sibu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(140,16,'Miri','2025-09-01 17:40:24','2025-09-01 17:40:24'),(141,16,'Bau','2025-09-01 17:40:24','2025-09-01 17:40:24'),(142,16,'Betong','2025-09-01 17:40:24','2025-09-01 17:40:24'),(143,16,'Mukah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(144,16,'Kanowit','2025-09-01 17:40:24','2025-09-01 17:40:24'),(145,16,'Kapit','2025-09-01 17:40:24','2025-09-01 17:40:24'),(146,16,'Lawas','2025-09-01 17:40:24','2025-09-01 17:40:24'),(147,16,'Limbang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(148,16,'Lubok Antu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(149,16,'Lundu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(150,16,'Bintangor','2025-09-01 17:40:24','2025-09-01 17:40:24'),(151,16,'Baram','2025-09-01 17:40:24','2025-09-01 17:40:24'),(152,16,'Matu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(153,16,'Saratok','2025-09-01 17:40:24','2025-09-01 17:40:24'),(154,16,'Kota Samarahan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(155,16,'Serian','2025-09-01 17:40:24','2025-09-01 17:40:24'),(156,16,'Sarikei','2025-09-01 17:40:24','2025-09-01 17:40:24'),(157,16,'Simunjan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(158,16,'Sri Aman','2025-09-01 17:40:24','2025-09-01 17:40:24'),(159,16,'Bekenu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(160,16,'Gedong','2025-09-01 17:40:24','2025-09-01 17:40:24'),(162,2,'Desa Park City','2025-09-23 23:44:14','2025-09-23 23:44:14'),(163,1,'Sungai Buloh','2025-09-23 23:45:58','2025-09-23 23:45:58'),(164,2,'Kepong','2025-09-23 23:46:36','2025-09-23 23:46:36'),(165,1,'Rawang','2025-09-23 23:47:08','2025-09-23 23:47:08'),(166,1,'Bangi','2025-09-23 23:49:48','2025-09-23 23:49:48'),(167,1,'Petaling Jaya','2025-09-23 23:50:46','2025-09-23 23:50:46'),(168,1,'Shah Alam','2025-09-23 23:52:39','2025-09-23 23:52:39'),(169,2,'Jalan Ampang','2025-09-23 23:56:49','2025-09-23 23:56:49'),(170,2,'Jalan Duta','2025-09-23 23:57:08','2025-09-23 23:57:08'),(171,2,'Loke Yew','2025-09-23 23:57:28','2025-09-23 23:57:28'),(172,2,'Sri Petaling','2025-09-23 23:58:01','2025-09-23 23:58:01'),(173,1,'Puchong','2025-09-23 23:58:34','2025-09-23 23:58:34'),(174,2,'Pudu','2025-09-23 23:59:00','2025-09-23 23:59:00'),(175,2,'Jalan Damansara','2025-09-23 23:59:16','2025-09-23 23:59:16'),(176,2,'Jalan Kuching','2025-09-23 23:59:33','2025-09-23 23:59:33'),(177,1,'Bandar Sunway','2025-09-24 00:00:24','2025-09-24 00:00:24'),(178,1,'SS2','2025-09-24 00:01:35','2025-09-24 00:01:35'),(179,1,'Persiaran Surian','2025-09-24 00:02:18','2025-09-24 00:02:18'),(180,1,'Subang Jaya','2025-09-24 00:02:45','2025-09-24 00:02:45'),(181,2,'CKE','2025-09-24 00:03:13','2025-09-24 00:03:13'),(182,2,'MRR2','2025-09-24 00:03:41','2025-09-24 00:03:41'),(183,2,'Bukit Jalil','2025-09-24 00:03:56','2025-09-24 00:03:56'),(184,2,'Sungai Besi','2025-09-24 00:04:07','2025-09-24 00:04:07'),(185,2,'Dutamas','2025-09-24 00:04:22','2025-09-24 00:04:22'),(186,2,'Jalan Tuanku Abdul Hakim','2025-09-24 00:04:57','2025-09-24 00:04:57'),(187,2,'SPRINT','2025-09-24 00:05:09','2025-09-24 00:05:09'),(188,2,'Lebuhraya SALAK','2025-09-24 00:05:26','2025-09-24 00:05:26'),(189,1,'Jalan Cheras','2025-09-24 00:05:45','2025-09-24 00:05:45'),(190,1,'Batu Caves','2025-09-24 00:46:55','2025-09-24 00:46:55'),(191,1,'Bangi, Kajang','2025-09-24 17:30:09','2025-09-24 17:30:09'),(192,2,'Bangsar','2025-09-24 22:20:12','2025-09-24 22:20:12'),(193,13,'Mantin','2025-09-28 20:55:52','2025-09-28 20:55:52'),(194,13,'Nilai','2025-09-28 22:08:27','2025-09-28 22:08:27'),(195,13,'Labu','2025-09-28 23:10:28','2025-09-28 23:10:28'),(196,1,'Kota Warisan','2025-09-28 23:15:11','2025-09-28 23:15:11'),(197,1,'Serdang','2025-09-28 23:22:51','2025-09-28 23:22:51'),(198,2,'Sri Hartamas','2025-09-29 20:51:34','2025-09-29 20:51:34'),(199,2,'Damansara','2025-09-29 21:01:17','2025-09-29 21:01:17'),(200,1,'Desa Park City','2025-09-29 21:43:07','2025-09-29 21:43:07'),(201,13,'PLUS','2025-09-29 21:50:18','2025-09-29 21:50:18');
/*!40000 ALTER TABLE `districts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_approval` varchar(255) DEFAULT NULL,
  `design` varchar(255) NOT NULL,
  `installation` varchar(255) NOT NULL,
  `printing` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('pending','ongoing','completed') NOT NULL DEFAULT 'pending',
  `section` varchar(255) NOT NULL DEFAULT 'general',
  `remarks` text DEFAULT NULL,
  `site_name` varchar(255) NOT NULL,
  `progress` int(11) NOT NULL DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL,
  `assigned_user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `client_company_id` bigint(20) unsigned DEFAULT NULL,
  `billboard_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_client_company_FK` (`client_company_id`),
  KEY `jobs_billboard_FK` (`billboard_id`),
  CONSTRAINT `jobs_billboard_FK` FOREIGN KEY (`billboard_id`) REFERENCES `billboards` (`id`) ON DELETE SET NULL,
  CONSTRAINT `jobs_client_company_FK` FOREIGN KEY (`client_company_id`) REFERENCES `client_companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'1','1','1','1','TechNova Sdn Bhd','Outdoor Billboard','2025-09-05 07:05:19','2025-09-12 07:05:19','completed','general','Delivered ahead of time','Mid Valley',0,NULL,NULL,'2025-09-14 23:05:19','2025-09-14 23:05:19',NULL,NULL,NULL),(2,'1','1','0','0','CreativeWorks','Banner','2025-09-11 07:05:19','2025-09-17 07:05:19','ongoing','general','Waiting for printing','KLCC',0,NULL,NULL,'2025-09-14 23:05:19','2025-09-14 23:05:19',NULL,NULL,NULL),(3,'0','0','0','0','EcoPrint Malaysia','Flyers','2025-09-15 07:05:19','2025-09-22 07:05:19','pending','general','Waiting for client feedback','Bangsar South',0,NULL,NULL,'2025-09-14 23:05:19','2025-09-14 23:05:19',NULL,NULL,NULL),(4,'1','1','1','1','Red Dot Agency','LED Display','2025-08-31 07:05:19','2025-09-07 07:05:19','completed','general','Perfect execution','Pavilion KL',0,NULL,NULL,'2025-09-14 23:05:19','2025-09-14 23:05:19',NULL,NULL,NULL),(5,'0','1','0','0','UrbanGraphix','Vehicle Wrap','2025-09-13 07:05:19','2025-09-20 07:05:19','ongoing','general','Client needs revision on design','Sunway Pyramid',0,NULL,NULL,'2025-09-14 23:05:19','2025-09-14 23:05:19',NULL,NULL,NULL),(6,'1','1','1','1','TechNova Sdn Bhd','Outdoor Billboard','2025-09-05 07:05:29','2025-09-12 07:05:29','completed','general','Delivered ahead of time','Mid Valley',0,NULL,NULL,'2025-09-14 23:05:29','2025-09-14 23:05:29',NULL,NULL,NULL),(7,'1','1','0','0','CreativeWorks','Banner','2025-09-11 07:05:29','2025-09-17 07:05:29','ongoing','general','Waiting for printing','KLCC',0,NULL,NULL,'2025-09-14 23:05:29','2025-09-14 23:05:29',NULL,NULL,NULL),(8,'0','0','0','0','EcoPrint Malaysia','Flyers','2025-09-15 07:05:29','2025-09-22 07:05:29','pending','general','Waiting for client feedback','Bangsar South',0,NULL,NULL,'2025-09-14 23:05:29','2025-09-14 23:05:29',NULL,NULL,NULL),(9,'1','1','1','1','Red Dot Agency','LED Display','2025-08-31 07:05:29','2025-09-07 07:05:29','completed','general','Perfect execution','Pavilion KL',0,NULL,NULL,'2025-09-14 23:05:29','2025-09-14 23:05:29',NULL,NULL,NULL),(10,'0','1','0','0','UrbanGraphix','Vehicle Wrap','2025-09-13 07:05:29','2025-09-20 07:05:29','ongoing','general','Client needs revision on design','Sunway Pyramid',0,NULL,NULL,'2025-09-14 23:05:29','2025-09-14 23:05:29',NULL,NULL,NULL),(11,'1','1','1','1','TechNova Sdn Bhd','Outdoor Billboard','2025-09-05 07:05:36','2025-09-12 07:05:36','completed','general','Delivered ahead of time','Mid Valley',0,NULL,NULL,'2025-09-14 23:05:36','2025-09-14 23:05:36',NULL,NULL,NULL),(12,'1','1','0','0','CreativeWorks','Banner','2025-09-11 07:05:36','2025-09-17 07:05:36','ongoing','general','Waiting for printing','KLCC',0,NULL,NULL,'2025-09-14 23:05:36','2025-09-14 23:05:36',NULL,NULL,NULL),(13,'0','0','0','0','EcoPrint Malaysia','Flyers','2025-09-15 07:05:36','2025-09-22 07:05:36','pending','general','Waiting for client feedback','Bangsar South',0,NULL,NULL,'2025-09-14 23:05:36','2025-09-14 23:05:36',NULL,NULL,NULL),(14,'1','1','1','1','Red Dot Agency','LED Display','2025-08-31 07:05:36','2025-09-07 07:05:36','completed','general','Perfect execution','Pavilion KL',0,NULL,NULL,'2025-09-14 23:05:36','2025-09-14 23:05:36',NULL,NULL,NULL),(15,'0','1','0','0','UrbanGraphix','Vehicle Wrap','2025-09-13 07:05:36','2025-09-20 07:05:36','ongoing','general','Client needs revision on design','Sunway Pyramid',0,NULL,NULL,'2025-09-14 23:05:36','2025-09-14 23:05:36',NULL,NULL,NULL),(16,'1','1','1','1','TechNova Sdn Bhd','Outdoor Billboard','2025-09-07 03:09:49','2025-09-14 03:09:49','completed','general','Delivered ahead of time','Mid Valley',0,NULL,NULL,'2025-09-16 19:09:49','2025-09-16 19:09:49',NULL,NULL,NULL),(17,'1','1','0','0','CreativeWorks','Banner','2025-09-13 03:09:49','2025-09-19 03:09:49','ongoing','general','Waiting for printing','KLCC',0,NULL,NULL,'2025-09-16 19:09:49','2025-09-16 19:09:49',NULL,NULL,NULL),(18,'0','0','0','0','EcoPrint Malaysia','Flyers','2025-09-17 03:09:49','2025-09-24 03:09:49','pending','general','Waiting for client feedback','Bangsar South',0,NULL,NULL,'2025-09-16 19:09:49','2025-09-16 19:09:49',NULL,NULL,NULL),(19,'1','1','1','1','Red Dot Agency','LED Display','2025-09-02 03:09:49','2025-09-09 03:09:49','completed','general','Perfect execution','Pavilion KL',0,NULL,NULL,'2025-09-16 19:09:49','2025-09-16 19:09:49',NULL,NULL,NULL),(20,'0','1','0','0','UrbanGraphix','Vehicle Wrap','2025-09-15 03:09:49','2025-09-22 03:09:49','ongoing','general','Client needs revision on design','Sunway Pyramid',0,NULL,NULL,'2025-09-16 19:09:49','2025-09-16 19:09:49',NULL,NULL,NULL);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kltg_coordinator_lists`
--

DROP TABLE IF EXISTS `kltg_coordinator_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kltg_coordinator_lists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` int(11) DEFAULT NULL,
  `month` tinyint(3) unsigned DEFAULT NULL,
  `subcategory` varchar(20) DEFAULT NULL,
  `company_snapshot` varchar(255) DEFAULT NULL,
  `client_bp` varchar(255) DEFAULT NULL,
  `material_reminder_text` varchar(255) DEFAULT NULL,
  `title_snapshot` varchar(255) DEFAULT NULL,
  `x` varchar(500) DEFAULT NULL,
  `edition` varchar(255) DEFAULT NULL,
  `publication` varchar(255) DEFAULT NULL,
  `artwork_bp_client` varchar(255) DEFAULT NULL,
  `artwork_reminder` date DEFAULT NULL,
  `material_record` date DEFAULT NULL,
  `artwork_done` date DEFAULT NULL,
  `send_chop_sign` date DEFAULT NULL,
  `chop_sign_approval` date DEFAULT NULL,
  `park_in_file_server` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `collection_printer` varchar(255) DEFAULT NULL,
  `sent_to_client` varchar(255) DEFAULT NULL,
  `approved_client` varchar(255) DEFAULT NULL,
  `sent_to_printer` varchar(255) DEFAULT NULL,
  `printed` varchar(255) DEFAULT NULL,
  `delivered` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `post_link` varchar(255) DEFAULT NULL,
  `em_date_write` date DEFAULT NULL,
  `em_date_to_post` date DEFAULT NULL,
  `em_post_date` date DEFAULT NULL,
  `em_qty` int(10) unsigned DEFAULT NULL,
  `blog_link` varchar(255) DEFAULT NULL,
  `video_done` date DEFAULT NULL,
  `pending_approval` date DEFAULT NULL,
  `video_approved` date DEFAULT NULL,
  `video_scheduled` date DEFAULT NULL,
  `video_posted` date DEFAULT NULL,
  `article_done` date DEFAULT NULL,
  `article_approved` date DEFAULT NULL,
  `article_scheduled` date DEFAULT NULL,
  `article_posted` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kcl_unique_slot` (`master_file_id`,`subcategory`,`year`,`month`),
  KEY `kltg_coord_master_subcat_idx` (`master_file_id`,`subcategory`),
  KEY `kcl_year_month_idx` (`year`,`month`),
  KEY `kcl_mf_year_month_idx` (`master_file_id`,`year`,`month`),
  CONSTRAINT `kltg_coordinator_lists_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kltg_coordinator_lists`
--

LOCK TABLES `kltg_coordinator_lists` WRITE;
/*!40000 ALTER TABLE `kltg_coordinator_lists` DISABLE KEYS */;
INSERT INTO `kltg_coordinator_lists` VALUES (1,1,2025,9,'KLTG',NULL,'Client',NULL,'-','testing',NULL,NULL,'dsfs',NULL,'2025-09-24','2025-09-16',NULL,NULL,NULL,'2025-09-21 04:47:23','2025-09-21 23:15:21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,1,2025,9,'VIDEO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-21 04:43:28',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,1,2025,9,'EM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-15 05:02:40','2025-09-21 03:31:49',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-10',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,2,2025,9,'KLTG',NULL,NULL,NULL,'-',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-17 23:25:27','2025-09-21 23:15:21',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,2,2025,9,'LB',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-21 03:31:49',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,1,2025,5,'KLTG',NULL,'CLIENT',NULL,'KLTG',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-16 23:30:55','2025-09-16 23:30:58',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,2,2025,7,'KLTG',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-15 10:58:46',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,1,2025,10,'KLTG',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-15 10:59:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,2,2025,10,'KLTG',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-15 10:59:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,1,2025,8,'KLTG',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-15 10:59:39',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,2,2025,1,'KLTG',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-16 23:26:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,1,2025,5,'VIDEO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-16 23:30:56',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(34,1,2025,5,'ARTICLE',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-16 23:30:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,1,2025,5,'LB',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-16 23:30:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,1,2025,5,'EM',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-16 23:30:48',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `kltg_coordinator_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kltg_coordinator_trackings`
--

DROP TABLE IF EXISTS `kltg_coordinator_trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kltg_coordinator_trackings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `subcategory` enum('print','video','article','lb','em') NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `client_bp` varchar(255) DEFAULT NULL,
  `x` varchar(255) DEFAULT NULL,
  `material_reminder_text` varchar(255) DEFAULT NULL,
  `post_link` varchar(255) DEFAULT NULL,
  `material_received_date` date DEFAULT NULL,
  `video_done_date` date DEFAULT NULL,
  `pending_approval_date` date DEFAULT NULL,
  `video_approved_date` date DEFAULT NULL,
  `video_scheduled_date` date DEFAULT NULL,
  `video_posted_date` date DEFAULT NULL,
  `article_done_date` date DEFAULT NULL,
  `article_approved_date` date DEFAULT NULL,
  `article_scheduled_date` date DEFAULT NULL,
  `article_posted_date` date DEFAULT NULL,
  `edition` varchar(255) DEFAULT NULL,
  `publication` varchar(255) DEFAULT NULL,
  `artwork_party` varchar(255) DEFAULT NULL,
  `artwork_reminder_date` date DEFAULT NULL,
  `artwork_done_date` date DEFAULT NULL,
  `send_chop_sign_date` date DEFAULT NULL,
  `chop_sign_approval_date` date DEFAULT NULL,
  `park_in_server_date` date DEFAULT NULL,
  `em_date_write` date DEFAULT NULL,
  `em_date_to_post` date DEFAULT NULL,
  `em_post_date` date DEFAULT NULL,
  `em_qty` varchar(255) DEFAULT NULL,
  `blog_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_master_subcat` (`master_file_id`,`subcategory`),
  KEY `kltg_coordinator_trackings_subcategory_index` (`subcategory`),
  CONSTRAINT `kltg_coordinator_trackings_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kltg_coordinator_trackings`
--

LOCK TABLES `kltg_coordinator_trackings` WRITE;
/*!40000 ALTER TABLE `kltg_coordinator_trackings` DISABLE KEYS */;
/*!40000 ALTER TABLE `kltg_coordinator_trackings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kltg_monthly_details`
--

DROP TABLE IF EXISTS `kltg_monthly_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kltg_monthly_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` int(11) NOT NULL,
  `client` varchar(255) DEFAULT NULL,
  `month` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `field_type` enum('text','date') NOT NULL DEFAULT 'text',
  `value` varchar(255) DEFAULT NULL,
  `value_text` varchar(255) DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `is_date` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kltg_mf_y_m_cat_ft_type_unique_small` (`master_file_id`,`year`,`month`(4),`category`(100),`field_type`,`type`(100)),
  KEY `kltg_monthly_details_year_index` (`year`),
  KEY `kltg_monthly_details_category_index` (`category`),
  KEY `kltg_monthly_details_master_file_id_idx` (`master_file_id`),
  CONSTRAINT `kltg_monthly_details_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kltg_monthly_details`
--

LOCK TABLES `kltg_monthly_details` WRITE;
/*!40000 ALTER TABLE `kltg_monthly_details` DISABLE KEYS */;
INSERT INTO `kltg_monthly_details` VALUES (3,2,2025,NULL,'0','KLTG','text','KLTG','KLTG',NULL,0,'PUBLICATION','ACTIVE','2025-09-15 02:35:42','2025-09-15 02:35:42'),(4,1,2025,NULL,'0','KLTG','text','KLTG','KLTG',NULL,0,'PUBLICATION','ACTIVE','2025-09-15 02:35:43','2025-09-15 02:35:43'),(5,1,2025,NULL,'0','KLTG','text','51 - 52','51 - 52',NULL,0,'EDITION','ACTIVE','2025-09-15 02:36:12','2025-09-15 02:36:15'),(6,2,2025,NULL,'0','KLTG','text','52,53,54,55','52,53,54,55',NULL,0,'EDITION','ACTIVE','2025-09-15 02:36:30','2025-09-15 02:36:37'),(8,1,2025,NULL,'5','KLTG','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 02:38:11','2025-09-15 02:38:11'),(9,1,2025,NULL,'5','VIDEO','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 02:38:13','2025-09-15 02:38:13'),(10,1,2025,NULL,'5','ARTICLE','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 02:38:15','2025-09-15 02:38:15'),(11,1,2025,NULL,'5','LB','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 02:38:16','2025-09-15 02:38:16'),(12,1,2025,NULL,'5','EM','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 02:38:18','2025-09-15 02:38:18'),(13,1,2025,NULL,'5','KLTG','date','2025-05-30',NULL,'2025-05-30',1,'START','ACTIVE','2025-09-15 02:38:29','2025-09-15 02:38:29'),(14,1,2025,NULL,'5','VIDEO','date','2025-05-30',NULL,'2025-05-30',1,'START','ACTIVE','2025-09-15 02:38:33','2025-09-15 02:38:33'),(15,1,2025,NULL,'5','ARTICLE','date','2025-05-30',NULL,'2025-05-30',1,'START','ACTIVE','2025-09-15 02:38:40','2025-09-15 02:38:40'),(16,1,2025,NULL,'5','LB','date','2025-05-30',NULL,'2025-05-30',1,'START','ACTIVE','2025-09-15 02:38:43','2025-09-15 02:38:43'),(17,1,2025,NULL,'5','EM','date','2025-05-30',NULL,'2025-05-30',1,'START','ACTIVE','2025-09-15 02:38:47','2025-09-15 02:38:47'),(18,1,2025,NULL,'6','KLTG','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 02:39:06','2025-09-15 02:39:06'),(19,1,2025,NULL,'6','KLTG','date','2025-06-01',NULL,'2025-06-01',1,'START','ACTIVE','2025-09-15 02:39:12','2025-09-15 02:39:12'),(20,1,2025,NULL,'6','ARTICLE','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 02:39:31','2025-09-15 02:39:31'),(21,1,2025,NULL,'6','ARTICLE','date','2025-06-01',NULL,'2025-06-01',1,'START','ACTIVE','2025-09-15 02:39:37','2025-09-15 02:39:37'),(22,1,2025,NULL,'7','VIDEO','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 02:40:12','2025-09-15 02:40:12'),(23,1,2025,NULL,'7','VIDEO','date','2025-07-08',NULL,'2025-07-08',1,'START','ACTIVE','2025-09-15 02:40:15','2025-09-15 02:40:15'),(24,1,2025,NULL,'7','EM','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:40:26','2025-09-15 02:40:26'),(25,1,2025,NULL,'7','EM','date','2025-07-01',NULL,'2025-07-01',1,'START','ACTIVE','2025-09-15 02:40:35','2025-09-15 02:40:35'),(26,1,2025,NULL,'8','KLTG','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 02:40:50','2025-09-15 02:40:50'),(27,1,2025,NULL,'8','KLTG','date','2025-08-01',NULL,'2025-08-01',1,'START','ACTIVE','2025-09-15 02:40:54','2025-09-15 02:40:54'),(28,1,2025,NULL,'9','KLTG','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 02:41:15','2025-09-15 02:41:15'),(29,1,2025,NULL,'9','KLTG','date','2025-09-04',NULL,'2025-09-04',1,'START','ACTIVE','2025-09-15 02:41:20','2025-09-15 02:41:20'),(30,1,2025,NULL,'9','VIDEO','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 02:41:25','2025-09-15 02:41:25'),(31,1,2025,NULL,'9','VIDEO','date','2025-09-10',NULL,'2025-09-10',1,'START','ACTIVE','2025-09-15 02:41:26','2025-09-15 02:41:26'),(32,1,2025,NULL,'9','EM','text','Completed','Completed',NULL,0,'STATUS','ACTIVE','2025-09-15 02:41:40','2025-09-15 02:41:40'),(33,1,2025,NULL,'9','EM','date','2025-09-01',NULL,'2025-09-01',1,'START','ACTIVE','2025-09-15 02:41:43','2025-09-15 02:41:43'),(34,1,2025,NULL,'10','KLTG','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:41:57','2025-09-15 02:41:57'),(35,1,2025,NULL,'10','VIDEO','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:41:58','2025-09-15 02:41:58'),(36,1,2025,NULL,'10','ARTICLE','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:41:59','2025-09-15 02:41:59'),(37,1,2025,NULL,'10','LB','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:42:01','2025-09-15 02:42:01'),(38,1,2025,NULL,'10','EM','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:42:02','2025-09-15 02:42:02'),(39,1,2025,NULL,'10','KLTG','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 02:42:08','2025-09-15 02:42:08'),(40,1,2025,NULL,'10','VIDEO','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 02:42:11','2025-09-15 02:42:11'),(41,1,2025,NULL,'10','ARTICLE','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 02:42:15','2025-09-15 02:42:15'),(42,1,2025,NULL,'10','LB','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 02:42:18','2025-09-15 02:42:18'),(43,1,2025,NULL,'10','EM','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 02:42:21','2025-09-15 02:42:21'),(44,1,2025,NULL,'11','KLTG','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:42:30','2025-09-15 02:42:30'),(45,1,2025,NULL,'11','VIDEO','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:42:31','2025-09-15 02:42:31'),(46,1,2025,NULL,'11','ARTICLE','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:42:32','2025-09-15 02:42:32'),(47,1,2025,NULL,'11','LB','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:42:34','2025-09-15 02:42:34'),(48,1,2025,NULL,'11','EM','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 02:42:36','2025-09-15 02:42:36'),(49,1,2025,NULL,'11','KLTG','date','2025-11-01',NULL,'2025-11-01',1,'START','ACTIVE','2025-09-15 02:42:41','2025-09-15 02:42:41'),(50,1,2025,NULL,'11','VIDEO','date','2025-11-01',NULL,'2025-11-01',1,'START','ACTIVE','2025-09-15 02:42:47','2025-09-15 02:42:47'),(51,1,2025,NULL,'11','ARTICLE','date','2025-11-01',NULL,'2025-11-01',1,'START','ACTIVE','2025-09-15 02:42:51','2025-09-15 02:42:51'),(52,1,2025,NULL,'11','LB','date','2025-11-01',NULL,'2025-11-01',1,'START','ACTIVE','2025-09-15 02:42:58','2025-09-15 02:42:58'),(53,1,2025,NULL,'11','EM','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 02:43:01','2025-09-15 02:43:01'),(54,1,2025,NULL,'12','ARTICLE','text','Completed','Completed',NULL,0,'STATUS','ACTIVE','2025-09-15 02:43:14','2025-09-15 02:43:14'),(55,1,2025,NULL,'12','ARTICLE','date','2025-12-01',NULL,'2025-12-01',1,'START','ACTIVE','2025-09-15 02:43:17','2025-09-15 02:43:17'),(56,1,2026,NULL,'1','KLTG','text',NULL,NULL,NULL,0,'STATUS','ACTIVE','2025-09-15 03:21:32','2025-09-15 03:21:36'),(58,2,2026,NULL,'0','KLTG','text','KLTG','KLTG',NULL,0,'PUBLICATION','ACTIVE','2025-09-15 03:22:17','2025-09-15 03:22:18'),(59,1,2026,NULL,'0','KLTG','text','KLTG','KLTG',NULL,0,'PUBLICATION','ACTIVE','2025-09-15 03:22:19','2025-09-15 03:22:20'),(60,2,2025,NULL,'6','KLTG','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 04:28:26','2025-09-15 04:28:26'),(61,2,2025,NULL,'6','VIDEO','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 04:28:28','2025-09-15 04:28:28'),(62,2,2025,NULL,'6','ARTICLE','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 04:28:29','2025-09-15 04:28:29'),(63,2,2025,NULL,'6','LB','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 04:28:31','2025-09-15 04:28:31'),(64,2,2025,NULL,'6','EM','text','Payment','Payment',NULL,0,'STATUS','ACTIVE','2025-09-15 04:28:35','2025-09-15 04:28:35'),(65,2,2025,NULL,'7','KLTG','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 04:28:36','2025-09-15 04:29:07'),(66,2,2025,NULL,'6','KLTG','date','2025-06-01',NULL,'2025-06-01',1,'START','ACTIVE','2025-09-15 04:28:43','2025-09-15 04:28:43'),(67,2,2025,NULL,'6','VIDEO','date','2025-06-01',NULL,'2025-06-01',1,'START','ACTIVE','2025-09-15 04:28:46','2025-09-15 04:28:50'),(68,2,2025,NULL,'6','ARTICLE','date','2025-06-01',NULL,'2025-06-01',1,'START','ACTIVE','2025-09-15 04:28:52','2025-09-15 04:28:52'),(69,2,2025,NULL,'6','LB','date','2025-06-01',NULL,'2025-06-01',1,'START','ACTIVE','2025-09-15 04:28:56','2025-09-15 04:28:56'),(70,2,2025,NULL,'6','EM','date','2025-06-01',NULL,'2025-06-01',1,'START','ACTIVE','2025-09-15 04:28:59','2025-09-15 04:28:59'),(71,2,2025,NULL,'7','KLTG','date','2025-07-01',NULL,'2025-07-01',1,'START','ACTIVE','2025-09-15 04:29:13','2025-09-15 04:29:13'),(72,2,2025,NULL,'8','LB','text','Installation','Installation',NULL,0,'STATUS','ACTIVE','2025-09-15 04:29:42','2025-09-15 04:29:42'),(73,2,2025,NULL,'8','LB','date','2025-08-01',NULL,'2025-08-01',1,'START','ACTIVE','2025-09-15 04:29:57','2025-09-15 04:29:57'),(74,2,2025,NULL,'8','VIDEO','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 04:30:15','2025-09-15 04:30:15'),(75,2,2025,NULL,'8','VIDEO','date','2025-08-01',NULL,'2025-08-01',1,'START','ACTIVE','2025-09-15 04:30:20','2025-09-15 04:30:20'),(76,2,2025,NULL,'9','LB','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:30:39','2025-09-15 04:30:39'),(77,2,2025,NULL,'9','LB','date','2025-09-01',NULL,'2025-09-01',1,'START','ACTIVE','2025-09-15 04:30:45','2025-09-15 04:30:45'),(78,2,2025,NULL,'9','KLTG','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 04:31:01','2025-09-15 04:31:01'),(79,2,2025,NULL,'9','KLTG','date','2025-09-01',NULL,'2025-09-01',1,'START','ACTIVE','2025-09-15 04:31:08','2025-09-15 04:31:08'),(80,2,2025,NULL,'10','LB','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:31:20','2025-09-15 04:31:20'),(81,2,2025,NULL,'10','LB','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 04:31:23','2025-09-15 04:31:23'),(82,2,2025,NULL,'10','VIDEO','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 04:31:32','2025-09-15 04:31:32'),(83,2,2025,NULL,'10','VIDEO','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 04:31:35','2025-09-15 04:31:35'),(84,2,2025,NULL,'11','LB','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:31:48','2025-09-15 04:31:48'),(85,2,2025,NULL,'11','LB','date','2025-11-01',NULL,'2025-11-01',1,'START','ACTIVE','2025-09-15 04:31:50','2025-09-15 04:31:50'),(86,2,2025,NULL,'11','ARTICLE','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 04:32:01','2025-09-15 04:32:01'),(87,2,2025,NULL,'11','ARTICLE','date','2025-11-01',NULL,'2025-11-01',1,'START','ACTIVE','2025-09-15 04:32:06','2025-09-15 04:32:06'),(88,2,2025,NULL,'11','KLTG','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:32:23','2025-09-15 04:32:23'),(89,2,2025,NULL,'11','KLTG','date','2025-11-01',NULL,'2025-11-01',1,'START','ACTIVE','2025-09-15 04:32:26','2025-09-15 04:32:26'),(90,2,2025,NULL,'10','KLTG','text','Completed','Completed',NULL,0,'STATUS','ACTIVE','2025-09-15 04:32:31','2025-09-15 04:32:31'),(91,2,2025,NULL,'10','KLTG','date','2025-10-01',NULL,'2025-10-01',1,'START','ACTIVE','2025-09-15 04:32:34','2025-09-15 04:32:34'),(92,2,2025,NULL,'11','EM','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:32:53','2025-09-15 04:32:53'),(93,2,2025,NULL,'11','EM','date','2025-11-01',NULL,'2025-11-01',1,'START','ACTIVE','2025-09-15 04:32:56','2025-09-15 04:32:56'),(94,2,2025,NULL,'12','KLTG','text','Artwork','Artwork',NULL,0,'STATUS','ACTIVE','2025-09-15 04:32:59','2025-09-15 04:32:59'),(95,2,2025,NULL,'12','KLTG','date','2025-12-01',NULL,'2025-12-01',1,'START','ACTIVE','2025-09-15 04:33:04','2025-09-15 04:33:04'),(96,2,2025,NULL,'12','LB','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:33:08','2025-09-15 04:33:08'),(97,2,2025,NULL,'12','LB','date','2025-12-01',NULL,'2025-12-01',1,'START','ACTIVE','2025-09-15 04:33:12','2025-09-15 04:33:12'),(98,2,2025,NULL,'12','VIDEO','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:33:31','2025-09-15 04:33:31'),(99,2,2025,NULL,'12','VIDEO','date','2025-12-04',NULL,'2025-12-04',1,'START','ACTIVE','2025-09-15 04:33:35','2025-09-15 04:33:35'),(110,2,2026,NULL,'0','KLTG','text','52,53,54,55','52,53,54,55',NULL,0,'EDITION','ACTIVE','2025-09-15 04:35:08','2025-09-15 04:35:08'),(111,2,2026,NULL,'1','LB','text','Completed','Completed',NULL,0,'STATUS','ACTIVE','2025-09-15 04:40:22','2025-09-15 04:40:22'),(112,2,2026,NULL,'1','LB','date','2026-01-01',NULL,'2026-01-01',1,'START','ACTIVE','2025-09-15 04:40:26','2025-09-15 04:40:26'),(113,2,2026,NULL,'1','ARTICLE','text','Ongoing','Ongoing',NULL,0,'STATUS','ACTIVE','2025-09-15 04:40:37','2025-09-15 04:40:37'),(114,2,2026,NULL,'1','ARTICLE','date','2026-01-01',NULL,'2026-01-01',1,'START','ACTIVE','2025-09-15 04:40:40','2025-09-15 04:40:40'),(115,2,2026,NULL,'2','VIDEO','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:41:03','2025-09-15 04:41:03'),(116,2,2026,NULL,'2','VIDEO','date','2026-02-01',NULL,'2026-02-01',1,'START','ACTIVE','2025-09-15 04:41:07','2025-09-15 04:41:07'),(117,2,2026,NULL,'3','ARTICLE','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:41:16','2025-09-15 04:41:16'),(118,2,2026,NULL,'3','ARTICLE','date','2026-03-01',NULL,'2026-03-01',1,'START','ACTIVE','2025-09-15 04:41:20','2025-09-15 04:41:20'),(119,2,2026,NULL,'4','VIDEO','text','Completed','Completed',NULL,0,'STATUS','ACTIVE','2025-09-15 04:41:36','2025-09-15 04:41:36'),(120,2,2026,NULL,'4','VIDEO','date','2026-04-01',NULL,'2026-04-01',1,'START','ACTIVE','2025-09-15 04:41:39','2025-09-15 04:41:39'),(121,2,2026,NULL,'5','ARTICLE','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:41:47','2025-09-15 04:41:47'),(122,2,2026,NULL,'5','ARTICLE','date','2026-04-01',NULL,'2026-04-01',1,'START','ACTIVE','2025-09-15 04:42:02','2025-09-15 04:42:02'),(123,2,2026,NULL,'6','VIDEO','text','Posted','Posted',NULL,0,'STATUS','ACTIVE','2025-09-15 04:42:11','2025-09-15 04:42:11'),(124,2,2026,NULL,'6','VIDEO','date','2026-04-01',NULL,'2026-04-01',1,'START','ACTIVE','2025-09-15 04:42:17','2025-09-15 04:42:17'),(125,2,2025,NULL,'1','KLTG','text',NULL,NULL,NULL,0,'STATUS','ACTIVE','2025-09-16 22:09:54','2025-09-16 22:09:57');
/*!40000 ALTER TABLE `kltg_monthly_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `council_id` bigint(20) unsigned NOT NULL,
  `district_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `locations_council_id_foreign` (`council_id`),
  KEY `locations_district_id_foreign` (`district_id`),
  KEY `locations_created_by_foreign` (`created_by`),
  KEY `locations_updated_by_foreign` (`updated_by`),
  CONSTRAINT `locations_council_id_foreign` FOREIGN KEY (`council_id`) REFERENCES `councils` (`id`) ON DELETE CASCADE,
  CONSTRAINT `locations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `locations_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `locations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
INSERT INTO `locations` VALUES (1,4,2,'Persiaran Raja Muda Musa - Klang',NULL,NULL,'2025-09-01 17:45:36','2025-09-01 17:45:36'),(2,95,101,'Persiaran Pusat Bandar – Nilai',NULL,NULL,'2025-09-01 17:47:22','2025-09-01 17:47:22'),(3,95,101,'Seremban 2 Persiaran S2/B3 (Near Uncle Don)',NULL,NULL,'2025-09-01 17:48:59','2025-09-01 17:48:59'),(4,9,2,'Persiaran Raja Muda Musa - Klang',NULL,NULL,'2025-09-01 18:04:58','2025-09-01 18:04:58'),(5,9,2,'Exit Persiaran Rajawali (Near Roundabout), Klang',NULL,NULL,'2025-09-01 18:06:23','2025-09-01 18:06:23'),(6,9,2,'Klang',NULL,NULL,'2025-09-01 18:07:51','2025-09-01 18:07:51'),(7,5,9,'39 Jalan Ukay Perdana, Ampang Jaya',NULL,NULL,'2025-09-01 18:08:59','2025-09-01 18:08:59'),(8,5,9,'Jalan Perdana 1 – Pandan Perdana',NULL,NULL,'2025-09-01 18:10:18','2025-09-01 18:10:18'),(9,9,10,'Persiaran Saujana Impian, Kajang (Opposite Lotus)',NULL,NULL,'2025-09-01 18:12:05','2025-09-01 18:12:05'),(10,9,10,'Sungai Long',NULL,NULL,'2025-09-01 18:13:38','2025-09-01 18:13:38'),(11,9,10,'Jalan Bukit Dugang – Before Turning right to Persiaran Selatan Putrajaya',NULL,NULL,'2025-09-01 18:15:00','2025-09-01 18:15:00'),(12,9,10,'Desa Pinggiran Putra – Before turning Left to Persiaran Timur',NULL,NULL,'2025-09-01 18:16:18','2025-09-01 18:16:18'),(13,7,5,'Near KIP Mall – Laluan 54 – Jalan Kuala Selangor',NULL,NULL,'2025-09-01 18:17:51','2025-09-01 18:17:51'),(14,7,164,'Jalan Metro Prima (AEON Kepong)',NULL,NULL,'2025-09-01 18:19:44','2025-09-23 23:46:36'),(15,7,3,'Lebuh Utama  Tun Hussien Onn',NULL,NULL,'2025-09-01 18:21:59','2025-09-01 18:21:59'),(16,6,190,'MRR2 – Towards Ampang',NULL,NULL,'2025-09-01 18:23:46','2025-09-29 20:52:27'),(17,6,9,'Jln Kuching – Towards Rawang (Near Caltex & Warta)',NULL,NULL,'2025-09-01 18:25:19','2025-09-01 18:25:19'),(18,6,9,'Jalan Kuasa Selangor',NULL,NULL,'2025-09-01 18:27:11','2025-09-01 18:27:11'),(19,6,9,'Jalan Rawang Before Tuning to Lotus (Tesco)',NULL,NULL,'2025-09-01 18:29:05','2025-09-01 18:29:05'),(20,6,9,'Persiaran Angun – Rawang',NULL,NULL,'2025-09-01 18:30:23','2025-09-01 18:30:23'),(21,6,165,'Jalan Batu Arang (heading to KL)',NULL,NULL,'2025-09-02 00:25:51','2025-09-23 23:47:08'),(22,11,11,'Persiaran Puncak Jalil',NULL,NULL,'2025-09-02 00:29:04','2025-09-02 00:29:04'),(23,3,11,'Jalan Putra Permai – Seri Kembangan (Opposite Giant)',NULL,NULL,'2025-09-02 00:31:34','2025-09-02 00:31:34'),(24,11,11,'Jalan Robson – Towards Mid Valley',NULL,NULL,'2025-09-02 00:33:48','2025-09-02 00:33:48'),(25,11,11,'Persiaran Lestari Perdana',NULL,NULL,'2025-09-02 00:35:56','2025-09-02 00:35:56'),(26,11,11,'Seri Kembangan – NS Highway (Towards Seremban)',NULL,NULL,'2025-09-02 00:38:00','2025-09-02 00:38:00'),(27,11,11,'Seri Kembangan – near Mines',NULL,NULL,'2025-09-02 00:39:29','2025-09-02 00:39:29'),(28,3,11,'Lebuhraya Putrajaya – Cyberjaya (heading to KLIA)',NULL,NULL,'2025-09-02 00:41:02','2025-09-02 00:41:02'),(29,12,11,'Jalan Teknokrat 1/1 (towards Limkokwing University, Cyberjaya)',NULL,NULL,'2025-09-02 00:43:02','2025-09-02 00:43:02'),(30,3,1,'Along NPE (from Subang Jaya to Bandar Sunway)',NULL,NULL,'2025-09-02 00:45:20','2025-09-22 00:26:52'),(31,3,11,'Jalan Perindustrian Bukit Serdang',NULL,NULL,'2025-09-02 00:46:44','2025-09-23 23:49:08'),(32,3,1,'Persiaran Jengka towards Inti International College Subang Jaya)',NULL,NULL,'2025-09-02 00:48:28','2025-09-02 00:48:28'),(33,3,180,'Subang Jaya SS15',NULL,NULL,'2025-09-02 00:53:07','2025-09-24 20:38:47'),(34,3,1,'Before Tunnel towards Persiaran Teknologi Subang (Subang Hi-Tech)',NULL,NULL,'2025-09-02 00:55:15','2025-09-02 00:55:15'),(35,3,1,'Jalan Taylors (towards Taylors College, PJS7)',NULL,NULL,'2025-09-02 00:56:55','2025-09-02 00:56:55'),(36,3,1,'Persiaran Jengka towards Inti International College Subang Jaya',NULL,NULL,'2025-09-02 01:00:28','2025-09-02 01:00:28'),(37,3,1,'Jln Batu 3 Lama, Near Sek 19, Shah Alam',NULL,NULL,'2025-09-02 01:05:02','2025-09-02 01:05:02'),(38,9,166,'Persiaran Kemajuan Bangi',NULL,NULL,'2025-09-02 01:07:01','2025-09-23 23:49:48'),(39,9,10,'Persiaran Bangi',NULL,NULL,'2025-09-02 01:13:18','2025-09-02 01:13:18'),(40,9,10,'Roundabout Near Bangi Exit to Plus Highway',NULL,NULL,'2025-09-02 01:14:43','2025-09-02 01:14:43'),(41,9,10,'Bulatan Sek 7 Bangi',NULL,NULL,'2025-09-02 01:15:50','2025-09-02 01:15:50'),(42,1,1,'Sek 9, Kota Damansara – Turning From Sg.Buloh (PJ)',NULL,NULL,'2025-09-02 01:17:49','2025-09-02 01:17:49'),(43,1,1,'From Sunway to Puchong PJ',NULL,NULL,'2025-09-02 01:19:16','2025-09-02 01:19:16'),(44,1,1,'Jln Hevea – Jln Sg. Buloh (Near Kwasa Land)',NULL,NULL,'2025-09-02 01:20:43','2025-09-02 01:20:43'),(45,1,1,'Persiaran Surian – Towards Jln Mahogani PJ',NULL,NULL,'2025-09-02 01:22:04','2025-09-02 01:22:04'),(46,1,167,'Persiaran Tropicana Petaling Jaya',NULL,NULL,'2025-09-02 01:23:21','2025-09-23 23:50:46'),(47,1,1,'Petaling Jaya',NULL,NULL,'2025-09-02 01:24:36','2025-09-02 01:24:36'),(48,1,1,'Jalan Puchong – Near Uptown Night Bazar',NULL,NULL,'2025-09-02 01:26:11','2025-09-02 01:26:11'),(49,1,1,'Jalan Sungai Buloh – Persiaran Jati',NULL,NULL,'2025-09-02 01:28:39','2025-09-02 01:28:39'),(50,1,167,'Jalan SS24/2, Taman Megah PJ',NULL,NULL,'2025-09-02 01:31:55','2025-09-24 20:41:34'),(51,1,1,'Jalan 222 (near Shell Petrol Station) / Persiaran PP Narayanan, Jalan 222 (PJ heading to Federal Highway)',NULL,NULL,'2025-09-02 01:33:22','2025-09-22 00:53:28'),(52,1,1,'Jln Lapangan Terbang – Towards Airport',NULL,NULL,'2025-09-02 01:34:46','2025-09-02 01:34:46'),(53,2,1,'Jalan Pekan Subang – In Front of Econsave',NULL,NULL,'2025-09-02 01:36:32','2025-09-02 01:36:32'),(54,2,1,'Section 13 Shah Alam (Persiaran Akuatik – Persiaran Sukan, Seksyen 13',NULL,NULL,'2025-09-02 01:38:07','2025-09-02 01:38:07'),(55,1,1,'Persiaran Shah Alam (1.8km to Setia City Mall)',NULL,NULL,'2025-09-02 01:39:28','2025-09-02 01:39:28'),(56,2,1,'Section 13 Shah Alam (Jalan Subang to Persiaran Sukan)',NULL,NULL,'2025-09-02 01:41:03','2025-09-02 01:41:03'),(57,2,1,'Padang Jawa – Persiaran Selangor',NULL,NULL,'2025-09-02 01:42:10','2025-09-02 01:42:10'),(58,2,1,'Kota Kemuning – Persiaran Anggerik Mokara',NULL,NULL,'2025-09-02 01:43:29','2025-09-02 01:43:29'),(59,2,1,'Jalan Monfort – Near Patron TTDI Shah Alam',NULL,NULL,'2025-09-02 01:44:58','2025-09-02 01:44:58'),(60,2,1,'Jalan Pegaga U12 Shah Alam',NULL,NULL,'2025-09-02 01:46:30','2025-09-02 01:46:30'),(61,2,1,'Jalan SU 4, Near Nippon Before Turning Left to Per Tengku Ampuan',NULL,NULL,'2025-09-02 01:47:44','2025-09-02 01:47:44'),(62,2,1,'Jalan Lompat Galah 13/36 – Near Acapella Hotel',NULL,NULL,'2025-09-02 01:48:55','2025-09-02 01:48:55'),(63,2,1,'Shah Alam (sekyen 7)',NULL,NULL,'2025-09-02 01:50:29','2025-09-02 01:50:29'),(64,2,1,'Persiaran Akuatik 2 – Near AEON Mall',NULL,NULL,'2025-09-02 01:51:47','2025-09-02 01:51:47'),(65,2,1,'Jalan Nyiur, Sek 18 Shah Alam (Near KTM Shah Alam & Selangor Bus Stop)',NULL,NULL,'2025-09-02 01:52:52','2025-09-02 01:52:52'),(66,2,1,'Persiaran Sukan – Before Turning to Highway',NULL,NULL,'2025-09-02 01:54:17','2025-09-02 01:54:17'),(67,2,1,'Jalan Nelayan, Sek 19 Shah Alam (Near Shell De’ Palma)',NULL,NULL,'2025-09-02 01:55:32','2025-09-02 01:55:32'),(68,1,1,'Persiaran Elektron Towards Caltex Petrol Station',NULL,NULL,'2025-09-02 01:56:41','2025-09-02 01:56:41'),(69,2,1,'GCE Seksyen 13 – Towards Bukit Jelutong – Near TTDI Shah Alam',NULL,NULL,'2025-09-02 02:02:44','2025-09-02 02:02:44'),(70,1,168,'Federal Highway (towards Shah Alam) / (Alternative) Federal Highway (towards Shah Alam)',NULL,NULL,'2025-09-02 02:10:20','2025-09-24 18:33:33'),(71,2,1,'Batu Tiga Federal Highway (Shah AlamPJ) (towards PJ/KL) - 24km to MITEC',NULL,NULL,'2025-09-02 02:11:53','2025-09-10 03:30:06'),(72,14,13,'Jalan Ampang (2km towards Raffles College KL) - site 1',NULL,NULL,'2025-09-02 02:14:13','2025-09-22 00:55:23'),(73,14,186,'Jalan Tuanku Abdul Halim (heading to MITEC)',NULL,NULL,'2025-09-02 02:15:39','2025-09-29 17:52:51'),(74,14,13,'Persiaran Puncak Jalil',NULL,NULL,'2025-09-02 02:16:38','2025-09-02 02:16:38'),(75,14,13,'Lebuhraya Sultan Iskandar (heading City Centre – 6.1km to MITEC)',NULL,NULL,'2025-09-02 17:19:45','2025-09-02 17:19:45'),(76,14,13,'Jalan Dutamas 1 - Near Publika - Site 2',NULL,NULL,'2025-09-02 17:21:05','2025-09-22 01:09:13'),(77,14,13,'Jalan Loke Yew (heading City Centre - 11km to MITEC)',NULL,NULL,'2025-09-02 17:22:13','2025-09-23 23:33:05'),(78,14,13,'Jalan Pudu (heading to Kotaraya- 7.4km to MITEC) / Jalan Pudu (heading to Kotaraya)',NULL,NULL,'2025-09-02 17:26:08','2025-09-22 01:27:34'),(79,14,13,'Bangsar Kuala Lumpur – 8.5 km to MITEC / (Alternative) Bangsar, Kuala Lumpur',NULL,NULL,'2025-09-02 17:41:14','2025-09-22 04:20:30'),(80,14,13,'Jalan Tuanku Abdul Halim (Site 2)',NULL,NULL,'2025-09-02 17:42:18','2025-09-02 17:42:18'),(81,14,13,'Lebuhraya Bukit Jalil (heading to Pavilion Bukit Jalil) – 6.4 km to Pinehill International School',NULL,NULL,'2025-09-02 17:44:01','2025-09-02 17:44:01'),(82,14,13,'Jalan Metro Prima (near AEON Kepong)',NULL,NULL,'2025-09-02 17:51:59','2025-09-23 05:35:03'),(83,14,13,'Desa Park City – Jalan 1/62b',NULL,NULL,'2025-09-02 17:53:06','2025-09-02 17:53:06'),(84,14,13,'Lebuhraya Sultan Iskandar KL – Near 	Maxwell School',NULL,NULL,'2025-09-02 17:54:32','2025-09-02 17:54:32'),(85,14,13,'Jalan U Thant 2, KL  (Near Royal Embassy of The Kingdom of Cambodia',NULL,NULL,'2025-09-02 17:55:37','2025-09-02 17:55:37'),(86,14,13,'Jalan Cheras (From Kajang to Stadium Towards Sj Impian)',NULL,NULL,'2025-09-02 17:56:49','2025-09-02 17:56:49'),(87,14,13,'Taman Len Seng',NULL,NULL,'2025-09-02 17:58:04','2025-09-02 17:58:04'),(88,14,13,'Jalan Lingkungan Budi – Towards Mid Valley',NULL,NULL,'2025-09-02 17:59:16','2025-09-02 17:59:16'),(89,14,13,'Persiaran Perusahaan – Before Turning 	right to Sek 19',NULL,NULL,'2025-09-02 18:00:29','2025-09-02 18:00:29'),(90,14,13,'Taman Maluri',NULL,NULL,'2025-09-02 18:01:58','2025-09-02 18:01:58'),(91,14,13,'Towards Rehabilitasi Cheras',NULL,NULL,'2025-09-02 18:02:41','2025-09-02 18:02:41'),(92,14,13,'Bulatan Jalan Kuari',NULL,NULL,'2025-09-02 18:04:02','2025-09-02 18:04:02'),(93,14,13,'Near Sunway Medical Center Velocity',NULL,NULL,'2025-09-02 18:04:54','2025-09-02 18:04:54'),(94,14,13,'Jln Cheras Perdana',NULL,NULL,'2025-09-02 18:06:22','2025-09-02 18:06:22'),(95,14,14,'Lebuh Utama Tun Hussien Onn',NULL,NULL,'2025-09-02 18:07:26','2025-09-23 23:46:50'),(96,14,172,'Jalan Selesaria 1, Sri Petaling',NULL,NULL,'2025-09-02 18:10:42','2025-09-23 23:58:01'),(97,14,13,'Persiaran Alam Damai',NULL,NULL,'2025-09-02 18:11:40','2025-09-02 18:11:40'),(98,14,13,'Towards HUKM Cheras',NULL,NULL,'2025-09-02 18:12:42','2025-09-02 18:12:42'),(99,14,13,'Jln 46/10, Near Mosque',NULL,NULL,'2025-09-02 18:13:35','2025-09-02 18:13:35'),(100,14,13,'Jalan Tun Dr. Ismail, KL',NULL,NULL,'2025-09-02 18:15:35','2025-09-02 18:15:35'),(101,14,13,'Jalan Dutamas 1 – Near Publika (Site 2)District : Kuala Lumpur',NULL,NULL,'2025-09-02 18:17:47','2025-09-02 18:17:47'),(102,14,13,'Jln Genting Klang – (Near Army Camp) Towards Setapak Danau Kota',NULL,NULL,'2025-09-02 18:19:06','2025-09-02 18:19:06'),(103,14,13,'Jalan Kinrara 5A, Bandar Kinrara, Puchong– 19.6 km to MITEC / Jalan Kinrara 5A, Bandar Kinrara, Puchong – 11km to Midvalley',NULL,NULL,'2025-09-02 18:20:20','2025-09-22 01:13:47'),(104,14,174,'Jalan Pudu (opposite Bomba Jalan Pudu)',NULL,NULL,'2025-09-02 18:21:44','2025-09-24 19:56:05'),(105,14,13,'Jalan Robson – Towards Mid Valley',NULL,NULL,'2025-09-02 18:22:55','2025-09-02 18:22:55'),(106,14,13,'Jln 46/10, Near Tmn Koperasi Polis',NULL,NULL,'2025-09-02 18:23:58','2025-09-02 18:23:58'),(107,14,175,'Jalan  Damansara (next to Jalan Kelantan)',NULL,NULL,'2025-09-02 18:25:03','2025-09-29 17:51:32'),(108,14,13,'Near Petronas Wangsa Melawati @ MRR2 Towards Setapak',NULL,NULL,'2025-09-02 18:26:10','2025-09-02 18:26:10'),(109,14,176,'Jalan Kuching (heading to City Centre)',NULL,NULL,'2025-09-02 18:27:06','2025-09-29 17:52:25'),(110,14,171,'Jalan Loke Yew (turning to Dewan Bahasa Pustaka)',NULL,NULL,'2025-09-02 18:28:14','2025-09-23 23:57:28'),(111,14,169,'Jalan Ampang (opposite Russian Embassy)',NULL,NULL,'2025-09-02 18:29:23','2025-09-29 17:51:06'),(112,14,14,'Taman Connaught',NULL,NULL,'2025-09-02 18:30:45','2025-09-24 18:53:24'),(113,14,13,'Jalan Sultan Salahuddin (towards Lebuhraya Sultan Iskandar)',NULL,NULL,'2025-09-02 18:31:50','2025-09-02 18:31:50'),(114,14,174,'Jalan Galloway, Pudu',NULL,NULL,'2025-09-02 18:32:47','2025-09-24 00:00:07'),(115,1,1,'Lebuhraya Damansara-Puchong, Taman Mayang 1',NULL,NULL,'2025-09-02 23:11:18','2025-09-30 01:50:45'),(116,1,1,'Lebuhraya Damansara-Puchong, Taman Mayang 2',NULL,NULL,'2025-09-02 23:14:11','2025-09-02 23:14:11'),(117,1,1,'Ara Damansara 1',NULL,NULL,'2025-09-02 23:24:18','2025-09-02 23:24:18'),(118,1,1,'Ara Damansara 2',NULL,NULL,'2025-09-02 23:25:41','2025-09-02 23:25:41'),(119,6,1,'MRR2, Batu Caves 1',NULL,NULL,'2025-09-02 23:27:01','2025-09-02 23:27:01'),(120,1,1,'MRR2, Batu Caves 2',NULL,NULL,'2025-09-02 23:28:15','2025-09-02 23:28:15'),(121,3,1,'Lebuhraya Sungai Besi 1',NULL,NULL,'2025-09-02 23:30:20','2025-09-02 23:30:20'),(122,3,1,'Lebuhraya Sungai Besi 2',NULL,NULL,'2025-09-02 23:31:29','2025-09-02 23:31:29'),(123,3,1,'Lebuhraya ELITE 1',NULL,NULL,'2025-09-02 23:32:34','2025-09-02 23:32:34'),(124,3,1,'Lebuhraya ELITE 2',NULL,NULL,'2025-09-02 23:34:27','2025-09-02 23:34:27'),(125,3,1,'Persiaran Puchong Utama 1 – Exit to LDP',NULL,NULL,'2025-09-02 23:35:34','2025-09-02 23:35:34'),(126,3,1,'Persiaran Puchong Utama 2 – From LDP',NULL,NULL,'2025-09-02 23:36:41','2025-09-02 23:36:41'),(127,3,1,'Lebuhraya ELITE – Near BSP  1',NULL,NULL,'2025-09-02 23:57:28','2025-09-02 23:57:28'),(128,3,1,'Lebuhraya ELITE – Near BSP 2',NULL,NULL,'2025-09-02 23:58:35','2025-09-02 23:58:35'),(129,3,1,'NKVE 1 – Towards Sg Buloh',NULL,NULL,'2025-09-03 00:01:04','2025-09-03 00:01:04'),(130,3,1,'NKVE 2 – Towards Sg Buloh',NULL,NULL,'2025-09-03 00:07:30','2025-09-03 00:07:30'),(131,14,13,'LDP – BUKIT LANJAN 1 – Towards PJ',NULL,NULL,'2025-09-03 00:09:26','2025-09-03 00:09:26'),(132,14,13,'Lebuhraya Bukit Jalil 1',NULL,NULL,'2025-09-03 00:11:26','2025-09-03 00:11:26'),(133,14,13,'Lebuhraya Bukit Jalil 2',NULL,NULL,'2025-09-03 00:12:34','2025-09-03 00:12:34'),(134,1,1,'Persiaran Surian 1 - Near Emporis Kota Damansara (From Thomson Medical Centre)',NULL,NULL,'2025-09-03 00:21:30','2025-09-03 00:21:30'),(135,1,1,'Persiaran Surian 2 - Near Emporis Kota Damansara (From Sungai Buloh)',NULL,NULL,'2025-09-03 00:23:40','2025-09-03 00:23:40'),(136,1,1,'Persiaran Surian 3 - Near Emporis Kota 	Damansara (From Uptown Damansara)',NULL,NULL,'2025-09-03 00:25:05','2025-09-03 00:25:05'),(137,14,13,'Lebuhraya Bukit Jalil',NULL,NULL,'2025-09-05 01:10:43','2025-09-05 01:10:43'),(138,1,1,'Taman Medan',NULL,NULL,'2025-09-09 03:35:49','2025-09-09 03:35:49'),(139,2,1,'Jalan Sungai Buloh (Kwasa Land)',NULL,NULL,'2025-09-10 02:20:56','2025-09-10 02:20:56'),(140,1,179,'Persiaran Surian - Sunway Giza Mall',NULL,NULL,'2025-09-10 02:22:38','2025-09-24 00:02:18'),(141,14,13,'Old Klang Road (Near Petron)',NULL,NULL,'2025-09-10 02:29:07','2025-09-10 02:29:07'),(142,14,13,'Jalan Ampang (2km towards Raffles College KL) - site 2',NULL,NULL,'2025-09-10 02:45:13','2025-09-22 00:56:22'),(143,14,13,'Jalan Damansara (Next to Jalan Kelantan) 2',NULL,NULL,'2025-09-10 02:54:35','2025-09-10 02:54:35'),(144,14,13,'Jalan Tuanku Abdul Halim (Site 1)',NULL,NULL,'2025-09-10 03:19:32','2025-09-10 03:19:32'),(145,14,13,'Jalan Bukit Kiara (towards Sri Hartamas) - 4.5 km to MITEC / Jalan Bukit Kiara (towards Sri Hartamas) – 8.3km to Midvalley',NULL,NULL,'2025-09-10 03:23:28','2025-09-22 01:03:13'),(146,3,11,'South City - The Mines, Seri Kembangan',NULL,NULL,'2025-09-10 03:34:13','2025-09-10 03:34:13'),(147,14,13,'Jalan Maarof Bangsar',NULL,NULL,'2025-09-11 17:18:42','2025-09-11 17:18:42'),(148,14,13,'Jalan Pudu - 9.5km to MITEC',NULL,NULL,'2025-09-11 17:22:02','2025-09-11 17:22:02'),(149,14,13,'Jalan Kuching (heading to City Centre - 5.1km to MITEC)',NULL,NULL,'2025-09-11 17:23:52','2025-09-11 17:23:52'),(150,14,13,'Jln Dutamas 5 (towards Persiaran Dutamas)',NULL,NULL,'2025-09-11 17:40:12','2025-09-11 17:40:12'),(151,14,13,'Persiaran Dutamas (towards Jln Duta Kiara)',NULL,NULL,'2025-09-11 17:41:59','2025-09-11 17:41:59'),(152,14,13,'Jln Kiara,, Mont Kiara - 9.8km to Mid Valley',NULL,NULL,'2025-09-11 17:44:12','2025-09-11 17:44:12'),(153,14,198,'Jalan Sri Hartamas 1 (opposite Hartamas Shopping Centre)',NULL,NULL,'2025-09-11 18:00:52','2025-09-29 20:51:34'),(154,9,10,'Persiaran Bangi (towards Persiaran Pekeliling) - 6.7km to UKM Bangi',NULL,NULL,'2025-09-11 18:08:46','2025-09-11 18:08:46'),(155,9,191,'Persiaran Pekeliling (towards Persiaran Bandar), Bangi',NULL,NULL,'2025-09-11 18:12:15','2025-09-24 22:13:06'),(156,6,4,'Lebuh Utama Sri Gombak - 17.6km to Mid Valley',NULL,NULL,'2025-09-11 18:14:45','2025-09-11 18:14:45'),(157,6,190,'Lebuhraya Selayang - Kepong – 12.8 km to MITEC',NULL,NULL,'2025-09-11 18:18:10','2025-09-29 21:59:03'),(158,1,167,'Jalan Sepah Puteri 5/1, Kota Damansara',NULL,NULL,'2025-09-11 18:22:54','2025-09-29 20:55:00'),(159,1,1,'Jalan Majlis (towards Jalan SS3/36) – 16.3 km to MITEC',NULL,NULL,'2025-09-11 18:24:29','2025-09-22 01:23:51'),(160,14,13,'Persiaran Puncak Jalil 1, Bukit Jalil - 8km to IMU University',NULL,NULL,'2025-09-11 18:29:07','2025-09-11 18:29:07'),(161,2,1,'Persiaran Kayangan, Seksyen 8 Shah Alam - 2.6km to UITM Shah Alam',NULL,NULL,'2025-09-11 18:30:57','2025-09-11 18:30:57'),(162,3,168,'Persiaran Fajar, Shah Alam',NULL,NULL,'2025-09-11 18:33:22','2025-09-29 20:55:49'),(163,2,5,'Seksyen U20, Jalan Kuala Selangor - 23.1km to Mid Valley',NULL,NULL,'2025-09-11 18:41:02','2025-09-11 18:41:02'),(164,2,1,'Persiaran Gerbang Utama, Bukit Jelutong - 5.2km to MSU',NULL,NULL,'2025-09-11 18:43:30','2025-09-11 18:43:30'),(165,14,13,'Jalan Pudu - 3km to Berjaya University College',NULL,NULL,'2025-09-11 18:50:34','2025-09-11 18:50:34'),(166,1,1,'taman medan 2',NULL,NULL,'2025-09-17 03:34:06','2025-09-17 03:34:06'),(167,14,13,'Jalan Sultan Abdul Halim - Site 1 (turning to Arkib Negara) - KL',NULL,NULL,'2025-09-22 00:23:20','2025-09-22 00:23:20'),(168,1,163,'Jalan Sungai Buloh, Kota Damansara (Kwasa Land)',NULL,NULL,'2025-09-22 00:27:07','2025-09-23 23:45:58'),(169,14,170,'Jalan Tuanku Abdul Halim (heading to MITEC)',NULL,NULL,'2025-09-22 00:30:33','2025-09-23 23:57:08'),(170,3,180,'Along NPE (Subang Jaya to Bandar Sunway near BHP)',NULL,NULL,'2025-09-22 00:31:30','2025-09-29 17:57:49'),(171,14,13,'Jalan Tun Razak (1.3km to The LINC KL & 4.6km to TRX)',NULL,NULL,'2025-09-22 00:31:34','2025-09-22 00:31:34'),(172,14,13,'KL-Seremban Expressway (from Nirwana towards Taman Danau Desa)',NULL,NULL,'2025-09-22 00:33:31','2025-09-22 00:33:31'),(173,1,1,'LDP – KEPONG TO PJ (2.3 Km to 1 Utama Shopping Mall)',NULL,NULL,'2025-09-22 00:44:40','2025-09-22 00:44:40'),(174,1,1,'LDP- Kepong to PJ (2.3KM to 1 Utama Shopping Mall)',NULL,NULL,'2025-09-22 00:45:54','2025-09-22 00:45:54'),(175,1,1,'LDP to Kepong PJ (2.3m TO 1 Utama)',NULL,NULL,'2025-09-22 00:46:36','2025-09-22 00:46:36'),(176,1,167,'LDP Kepong to PJ (opposite Ikea)',NULL,NULL,'2025-09-22 00:47:58','2025-09-24 00:01:07'),(177,1,177,'LDP Sunway-Puchong (near BHP) / LDP (Sunway-Puchong, near BHP)',NULL,NULL,'2025-09-22 00:50:27','2025-09-24 00:01:48'),(178,1,1,'LDP Taman Mayang (heading to SS2) - Site 2',NULL,NULL,'2025-09-22 00:51:48','2025-09-22 00:51:48'),(179,1,1,'LDP Taman Mayang (heading to SS2) - Site 1',NULL,NULL,'2025-09-22 00:52:20','2025-09-22 00:52:20'),(180,14,13,'Lebuh Utama Sri Gombak – 12.2 km to MITEC / Lebuh Utama Sri Gombak – 17.6km to Midvalley',NULL,NULL,'2025-09-22 00:53:31','2025-09-22 00:53:31'),(181,14,13,'Lebuhraya Bukit Jalil (Depot Imigresen Bukit Jalil) - Concrete Pole',NULL,NULL,'2025-09-22 00:57:30','2025-09-22 00:57:30'),(182,14,13,'Lebuhraya Cheras Kajang (from Cheras Batu 11 towards Cheras Central Mall)',NULL,NULL,'2025-09-22 00:58:48','2025-09-22 00:58:48'),(183,14,13,'Lebuhraya Cheras Kajang (towards Sungai Long exit)',NULL,NULL,'2025-09-22 00:59:29','2025-09-22 00:59:29'),(184,14,13,'Lebuhraya Cheras-Kajang (from Cheras Batu 11 to Cheras Sentral Mall)',NULL,NULL,'2025-09-22 01:00:24','2025-09-22 01:00:24'),(185,14,13,'Lebuhraya Cheras-Kajang (heading towards Taman Len Seng)',NULL,NULL,'2025-09-22 01:01:16','2025-09-22 01:01:16'),(186,1,167,'Lebuhraya LDP (opposite Kelana Jaya LRT)',NULL,NULL,'2025-09-22 01:03:14','2025-09-24 22:25:41'),(187,1,1,'Lebuhraya LDP (heading to Lebuhraya Sprint)',NULL,NULL,'2025-09-22 01:24:28','2025-09-24 20:19:28'),(188,14,164,'Lebuhraya LDP (towards exit to Desa Park City)',NULL,NULL,'2025-09-22 01:25:53','2025-09-24 22:44:48'),(189,1,1,'Lebuhraya LDP (towards exit to Desa Park City), KL (Site 1)',NULL,NULL,'2025-09-22 01:26:34','2025-09-22 01:26:34'),(190,14,188,'Lebuhraya SALAK (heading towards IKON Connaught)',NULL,NULL,'2025-09-22 01:29:27','2025-09-24 00:05:26'),(191,3,11,'Lebuhraya Sungai Besi (Seremban - KL)',NULL,NULL,'2025-09-22 01:35:54','2025-09-22 01:35:54'),(192,14,187,'Lebuhraya SPRINT (from KGPA heading to Mont Kiara)',NULL,NULL,'2025-09-22 01:37:19','2025-09-24 00:05:09'),(193,3,11,'North South Expressway (in front of South City Plaza)',NULL,NULL,'2025-09-22 01:41:26','2025-09-22 01:41:26'),(194,1,167,'North South Expressway (Near Persiaran Meranti Bridge Towards Plaza Tol Jalan Duta)',NULL,NULL,'2025-09-22 01:46:48','2025-09-29 21:35:36'),(195,3,180,'Off Jalan Lagoon Selatan (next to Monash University Sunway)',NULL,NULL,'2025-09-22 01:48:03','2025-09-24 00:02:58'),(196,1,1,'Bomba SS2, PJ',NULL,NULL,'2025-09-22 01:50:00','2025-09-22 01:50:00'),(197,4,2,'Bulatan Seratus (exit to Federal Hiighway), KLANG',NULL,NULL,'2025-09-22 01:54:28','2025-09-22 01:54:28'),(198,1,167,'Federal Highway (from Empire Subang towards LDP Bridge)',NULL,NULL,'2025-09-22 01:56:03','2025-09-29 17:59:23'),(199,14,183,'Persiaran Puncak Jalil 1, Bukit Jalil (8km to IMU University Bukit Jalil)',NULL,NULL,'2025-09-22 01:58:53','2025-09-29 20:51:53'),(200,14,169,'Jalan Ampang (4km to Avenue K Shopping Mall)',NULL,NULL,'2025-09-22 01:59:14','2025-09-23 23:56:49'),(201,2,168,'Persiaran Sukan, Shah Alam (towards MSU Shah Alam)',NULL,NULL,'2025-09-22 02:00:42','2025-09-24 00:00:43'),(202,9,189,'Jalan Cheras (5.9km to Eko Cheras mall)',NULL,NULL,'2025-09-22 02:00:45','2025-09-24 00:05:45'),(203,14,15,'Lebuhraya Selayang - Kepong – 12.8 km to MITEC / Lebuhraya Selayang - Kepong – 5km to Kolej Komuniti Selayang',NULL,NULL,'2025-09-22 02:02:06','2025-09-22 02:02:06'),(204,14,14,'Jalan Cheras (opposite Eko Cheras Mall)',NULL,NULL,'2025-09-22 02:02:34','2025-09-22 02:02:34'),(205,14,14,'Jalan Cheras (towards City Centre)',NULL,NULL,'2025-09-22 02:03:59','2025-09-22 02:03:59'),(206,1,167,'Persiaran Surian (1.2km to Ikea Damansara)',NULL,NULL,'2025-09-22 02:04:17','2025-09-24 18:12:24'),(207,14,13,'Jalan Damansara (heading City Centre- 6.5KM to MITEC)',NULL,NULL,'2025-09-22 02:05:04','2025-09-22 02:05:04'),(208,1,179,'Persiaran Surian (opposite Petronas Statin Near Thomson Hospital)',NULL,NULL,'2025-09-22 02:05:40','2025-09-24 00:06:52'),(209,14,175,'Jalan Damansara (next to Jalan Kelantan) - Site 2',NULL,NULL,'2025-09-22 02:06:02','2025-09-24 00:04:35'),(210,14,13,'Jalan Dutamas - Site 2 (400m to Publika)',NULL,NULL,'2025-09-22 02:06:56','2025-09-22 02:06:56'),(211,14,185,'Jalan Dutamas 5 (towards Persiaran Dutamas) - 1.3 km to MITEC',NULL,NULL,'2025-09-22 02:07:44','2025-09-24 22:18:35'),(212,4,2,'Jalan Kapar (10.4km to Setia City Mall), KLANG / Jalan Kapar (5.6km heading towards BSSB Furniture), Klang',NULL,NULL,'2025-09-22 02:09:23','2025-09-22 02:09:23'),(213,4,2,'Jalan Kapar (11.1km to i-City Theme Park Shah Alam), KLANG / Jalan Kapar (6.8km heading towards BSSB Furniture), Klang',NULL,NULL,'2025-09-22 02:10:07','2025-09-22 02:10:07'),(214,3,1,'Jalan Kewajipan, Subang Jaya (heading to Jalan Kemajuan)',NULL,NULL,'2025-09-22 02:11:27','2025-09-22 02:11:27'),(215,1,167,'Persiaran Surian (3.4 km heading to IOI Mall Damansara)',NULL,NULL,'2025-09-22 02:12:04','2025-09-24 18:26:35'),(216,14,13,'Jalan Kiara, Mont Kiara – 2.9 km to MITEC',NULL,NULL,'2025-09-22 02:12:57','2025-09-22 02:12:57'),(217,2,1,'Persiaran Tun Teja, Alam Impian (1.3 km to Eaton School Setia Alam)',NULL,NULL,'2025-09-22 02:13:44','2025-09-22 02:13:44'),(218,14,15,'Jalan Kuching (under flyover before turning to Jalan Kepong Lama) / Jalan Kuching (heading to City Centre- 5.1KM to MITEC)',NULL,NULL,'2025-09-22 02:14:01','2025-09-22 02:14:01'),(219,1,1,'Petaling Jaya / Petaling Jaya SS2 (near Bomba)',NULL,NULL,'2025-09-22 02:15:27','2025-09-22 02:15:27'),(220,2,168,'Seksyen U20, Jalan Kuala Selangor, Shah Alam',NULL,NULL,'2025-09-22 02:16:58','2025-09-29 20:57:42'),(221,3,1,'Jalan lapangan Terbang - Towards Airport - site 1 / Jalan Peel (KL)',NULL,NULL,'2025-09-22 02:17:11','2025-09-22 02:17:11'),(222,3,1,'Jalan Lapangan Terbang Subang (towards Kelana Jaya)',NULL,NULL,'2025-09-22 02:18:35','2025-09-22 02:18:35'),(223,2,168,'Shah Alam (sekyen 7)',NULL,NULL,'2025-09-22 02:19:23','2025-09-24 22:15:18'),(224,14,182,'Jalan Lingkaran Tengah 2 (heading to Sri Petaling)',NULL,NULL,'2025-09-22 02:19:35','2025-09-24 00:06:07'),(225,14,13,'Jalan Loke Yew (towards City Centre/ 3.5km to MyTown Shopping Center)',NULL,NULL,'2025-09-22 02:20:49','2025-09-22 02:20:49'),(226,14,13,'Jalan Maarof, Bangsar (towards Bangsar Shopping Centre, far site)',NULL,NULL,'2025-09-22 02:24:06','2025-09-22 02:24:06'),(227,14,192,'Jalan Maarof, Bangsar (towards Pusat Bandar Damansara, near site)',NULL,NULL,'2025-09-22 02:25:08','2025-09-24 22:20:12'),(228,6,165,'Jalan Pintas Bandar Rawang (opposite KPJ Rawang)',NULL,NULL,'2025-09-22 02:29:26','2025-09-24 22:23:15'),(229,1,177,'Jalan PJS 8/9, Bandar Sunway (towards ESMOD KL)',NULL,NULL,'2025-09-22 02:30:33','2025-09-24 00:00:24'),(230,3,11,'Jalan Putra Permai, Seri Kembangan (opposite Giant)',NULL,NULL,'2025-09-22 02:34:38','2025-09-22 02:34:38'),(231,14,13,'Jalan Sentul (5km to Sunway Putra Mall)',NULL,NULL,'2025-09-22 02:35:42','2025-09-23 19:17:49'),(232,3,1,'Jalan SS 13/1A (towards Jalan Lagoon Selatan/ 1.8KM to Summit USJ)',NULL,NULL,'2025-09-22 02:37:04','2025-09-22 02:37:04'),(233,3,167,'Ara Damansara - next to Citta Mall (from Subang Airport to Subang Jaya)',NULL,NULL,'2025-09-22 02:53:13','2025-09-24 20:34:54'),(234,14,15,'Bulatan Kepong (heading to City Centre)',NULL,NULL,'2025-09-22 02:54:41','2025-09-22 02:54:41'),(235,2,1,'ELITE Highway (Subang Jaya - KLIA)',NULL,NULL,'2025-09-22 02:55:33','2025-09-22 02:55:33'),(236,2,1,'ELITE Highway Subang Jaya - KLIA',NULL,NULL,'2025-09-22 02:56:26','2025-09-22 02:56:26'),(237,9,3,'Federal Highway (Batu 3 Shah Alam - KL)',NULL,NULL,'2025-09-22 02:58:12','2025-09-22 02:58:12'),(238,2,168,'Federal Highway (towards Shah Alam near KTM Batu Tiga)',NULL,NULL,'2025-09-22 02:59:07','2025-09-29 17:49:50'),(239,3,180,'Federal Highway (towards Shah Alam in front of Al-Ikhsan)',NULL,NULL,'2025-09-22 02:59:58','2025-09-24 22:30:35'),(240,6,4,'Jalan Permai (Exit to Lebuh Utama Sri Gombak)',NULL,NULL,'2025-09-22 03:04:14','2025-09-22 03:04:14'),(241,6,4,'Jalan Pintas Bandar Rawang (290m from MYDIN Rawang)',NULL,NULL,'2025-09-22 03:05:32','2025-09-22 03:05:32'),(242,3,173,'Jalan Puchong (before Traffic Light 6.8KM Heading to LDP & Bukit Jalil Highway)',NULL,NULL,'2025-09-22 03:07:16','2025-09-29 20:50:03'),(243,3,173,'Jalan Puchong (near Uptown Puchong)',NULL,NULL,'2025-09-22 03:08:59','2025-09-24 00:03:26'),(244,14,13,'Jalan Pudu (opposite Bomba Jalan Pudu)',NULL,NULL,'2025-09-22 03:09:45','2025-09-22 03:09:45'),(245,14,13,'Jalan Pudu– 11 km to MITEC / Jalan Pudu- 9.5KM to MITEC / Jalan Pudu – 3km to Berjaya University College',NULL,NULL,'2025-09-22 03:10:22','2025-09-22 03:10:22'),(246,14,13,'Jalan Sg Besi towards Jalan Istana',NULL,NULL,'2025-09-22 03:12:03','2025-09-22 03:12:03'),(247,1,1,'Jalan Sg Buloh- Persiaran Jati',NULL,NULL,'2025-09-22 03:16:38','2025-09-22 03:16:38'),(248,2,1,'Jalan Subang-Persiaran Sukan, Sek 13 Shah Alam',NULL,NULL,'2025-09-22 03:17:27','2025-09-22 03:17:27'),(249,3,11,'Lebuhraya Putrajaya – Cyberjaya (heading to Puchong)',NULL,NULL,'2025-09-22 23:43:57','2025-09-22 23:43:57'),(250,1,167,'Jalan PJS 7/1A, Petaling Jaya',NULL,NULL,'2025-09-22 23:52:52','2025-09-23 23:51:00'),(251,1,167,'Jalan SS 24/2, Taman Megah',NULL,NULL,'2025-09-22 23:54:40','2025-09-23 23:52:10'),(252,1,167,'Jalan 222, Petaling Jaya (near Shell Petrol Station)',NULL,NULL,'2025-09-22 23:56:10','2025-09-23 23:52:25'),(253,2,168,'Persiaran Elektron Towards Caltex Petrol Station',NULL,NULL,'2025-09-23 00:00:38','2025-09-23 23:52:51'),(254,2,168,'Batu Tiga Federal Highway (Shah Alam to PJ)',NULL,NULL,'2025-09-23 00:03:09','2025-09-23 23:55:43'),(255,14,13,'Jalan Loke Yew (turning into Dewan Bahasa)',NULL,NULL,'2025-09-23 00:06:53','2025-09-23 00:06:53'),(256,14,162,'Jalan 1/62B - Desa Park City',NULL,NULL,'2025-09-23 00:07:58','2025-09-23 23:44:14'),(257,3,173,'Jalan Kinrara 5A, Bandar Kinrara, Puchong – 19.6 km to MITEC',NULL,NULL,'2025-09-23 00:11:40','2025-09-23 23:58:34'),(258,1,178,'LDP SS2 Petaling Jaya (near Bomba)',NULL,NULL,'2025-09-23 00:22:02','2025-09-24 00:01:35'),(259,3,11,'Seri Kembangan (towards South City Plaza)',NULL,NULL,'2025-09-23 00:30:42','2025-09-23 00:30:42'),(260,3,180,'Persiaran Jengka (towards Countryard SS15)',NULL,NULL,'2025-09-23 00:32:49','2025-09-24 00:02:45'),(261,14,181,'Lebuhraya Cheras-Kajang (towards Sg Long exit)',NULL,NULL,'2025-09-23 00:36:07','2025-09-24 00:03:13'),(262,14,182,'MRR2 towards exit Jalan Ampang',NULL,NULL,'2025-09-23 00:39:23','2025-09-24 00:03:41'),(263,14,183,'Lebuhraya Bukit Jalil (heading to Pavilion Bukit Jalil)',NULL,NULL,'2025-09-23 00:40:51','2025-09-24 00:03:56'),(264,14,184,'Jalan Sungai Besi towards Jalan Istana',NULL,NULL,'2025-09-23 00:43:03','2025-09-24 00:04:07'),(265,14,185,'Jalan Dutamas 1 – towards Publika',NULL,NULL,'2025-09-23 00:44:35','2025-09-24 00:04:22'),(266,1,179,'Persiaran Surian (towards Mutiara Damansara)',NULL,NULL,'2025-09-23 00:55:59','2025-09-29 17:58:18'),(267,2,168,'Persiaran Shah Alam (1.8km to Setia City Mall)',NULL,NULL,'2025-09-23 05:52:31','2025-09-23 23:52:39'),(268,14,14,'test ajah',NULL,NULL,'2025-09-23 17:54:14','2025-09-23 17:54:14'),(269,14,13,'test ajah 2',NULL,NULL,'2025-09-23 17:54:52','2025-09-23 17:54:52'),(270,14,13,'test 3',NULL,NULL,'2025-09-23 17:55:26','2025-09-23 17:55:26'),(271,14,13,'test3',NULL,NULL,'2025-09-23 17:55:53','2025-09-23 17:55:53'),(272,14,192,'Jalan Maarof, Bangsar (heading towards Jalan Bangsar, far site)',NULL,NULL,'2025-09-23 18:52:53','2025-09-29 20:39:39'),(273,14,183,'Lebuhraya Bukit Jalil (next to Depot Imigresen Bukit Jalil)',NULL,NULL,'2025-09-23 19:08:15','2025-09-24 22:22:07'),(274,1,177,'Lebuhraya NPE (PJS 10)',NULL,NULL,'2025-09-23 19:11:04','2025-09-24 22:35:28'),(275,3,180,'Jalan SS13/1A, Subang Jaya (towards Jalan Lagoon Selatan)',NULL,NULL,'2025-09-23 19:13:45','2025-09-24 22:38:59'),(276,3,180,'Persiaran Kemajuan heading to Jalan Kewajipan',NULL,NULL,'2025-09-23 19:22:45','2025-09-24 22:39:31'),(277,4,2,'Jalan Kapar (near Pusat eKhidmat MyEG)',NULL,NULL,'2025-09-23 22:49:50','2025-09-23 22:49:50'),(278,4,2,'Jalan Klang – Teluk Intan, Klang',NULL,NULL,'2025-09-23 22:53:04','2025-09-23 22:53:04'),(279,95,201,'North South Highway (Seremban - KL)',NULL,NULL,'2025-09-23 22:57:29','2025-09-29 21:50:18'),(280,1,NULL,'Test Aje',NULL,NULL,'2025-09-23 22:58:52','2025-09-23 22:58:52'),(281,6,190,'Lebuh Utama Sri Gombak – 12.2 km to MITEC',NULL,NULL,'2025-09-24 00:46:55','2025-09-24 00:46:55'),(282,14,183,'Lebuhraya Bukit Jalil (2.5km heading to Pavilion Bukit Jalil)',NULL,NULL,'2025-09-24 00:51:14','2025-09-24 00:51:14'),(283,2,168,'Federal Highway (towards Shah Alam/ 7.4km to SACC Mall)',NULL,NULL,'2025-09-24 01:05:46','2025-09-24 01:05:46'),(284,1,167,'LDP – KEPONG TO PJ (2.3 Km to 1 Utama Shopping Mall)',NULL,NULL,'2025-09-24 02:21:47','2025-09-24 02:21:47'),(285,1,167,'Jalan Majlis (towards Jalan SS3/36)',NULL,NULL,'2025-09-24 02:57:39','2025-09-29 22:00:51'),(286,2,168,'Persiaran Gerbang Utama, Bukit Jelutong',NULL,NULL,'2025-09-24 02:59:05','2025-09-29 20:53:44'),(287,3,180,'Subang Jaya SS15',NULL,NULL,'2025-09-24 03:03:57','2025-09-24 03:03:57'),(288,9,166,'Persiaran Jaya, Bandar Baru Bangi',NULL,NULL,'2025-09-28 20:53:23','2025-09-29 20:58:22'),(289,95,193,'Route 1 (Jalan Mantin)',NULL,NULL,'2025-09-28 20:55:52','2025-09-28 20:55:52'),(290,9,166,'Lingkaran Bandar Puncak Utama, Kajang',NULL,NULL,'2025-09-28 22:06:22','2025-09-29 20:59:23'),(291,95,194,'Persiaran Negeri (heading towards Bandar Baru Nilai)',NULL,NULL,'2025-09-28 22:08:27','2025-09-28 22:08:27'),(292,3,11,'Jalan Dulang Perdana (The Mines Shopping Mall – 350 Meters)',NULL,NULL,'2025-09-28 22:10:45','2025-09-28 22:10:45'),(293,14,13,'Jalan Tun Ismail',NULL,NULL,'2025-09-28 22:16:15','2025-09-28 22:16:15'),(294,3,11,'Jalan Putra Permai – Near Equine Park',NULL,NULL,'2025-09-28 22:18:15','2025-09-28 22:18:15'),(295,14,199,'Jalan Beringin, Pusat Bandar Damansara',NULL,NULL,'2025-09-28 22:20:34','2025-09-29 21:01:17'),(296,2,168,'Jalan Lapangan Terbang – Towards Aiport',NULL,NULL,'2025-09-28 22:27:04','2025-09-28 22:27:04'),(297,4,2,'Exit Federal Highway Towards Persiaran Rajawali, Klang',NULL,NULL,'2025-09-28 22:32:59','2025-09-28 22:32:59'),(298,3,11,'LDP (After Puchong Gateway & Turning to Jalan Putra Permai – Seri Kembangan)',NULL,NULL,'2025-09-28 22:34:53','2025-09-28 22:34:53'),(299,6,165,'Jalan Rawang Before Turning to Lotus (Tesco)',NULL,NULL,'2025-09-28 22:39:28','2025-09-28 22:39:28'),(300,1,167,'Jalan Sungai Buloh (6.8km to IOI Mall Damansara)',NULL,NULL,'2025-09-28 22:43:55','2025-09-28 22:43:55'),(301,1,167,'Jalan PJS 3/44, Petaling Jaya',NULL,NULL,'2025-09-28 22:46:10','2025-09-29 21:35:11'),(302,1,167,'Jalan PJS 3/44, Petaling Jaya (5.9km to KMI Taman Desa Medical center)',NULL,NULL,'2025-09-28 22:50:14','2025-09-28 22:50:14'),(303,14,182,'Jalan Lingkaran 2 tengah, KL (7.9km to KL East Mall)',NULL,NULL,'2025-09-28 23:03:00','2025-09-29 21:35:54'),(304,1,167,'Jalan PJS 3/2 (5.1km to Sunway Pyramid)',NULL,NULL,'2025-09-28 23:06:51','2025-09-28 23:06:51'),(305,1,167,'Jalan Sri Manja, Petaling Jaya (8km to Amcorp Mall PJ)',NULL,NULL,'2025-09-28 23:08:45','2025-09-28 23:08:45'),(306,95,195,'KL – Seremban Highway (towards KL)',NULL,NULL,'2025-09-28 23:10:28','2025-09-28 23:10:28'),(307,14,13,'Jalan Lingkaran Tengah (1.9 km heading to KL East Mall)',NULL,NULL,'2025-09-28 23:12:45','2025-09-28 23:12:45'),(308,12,12,'Lebuhraya Putrajaya - Cyberjaya (After Exit to Kota Warisan, Sepang)',NULL,NULL,'2025-09-28 23:15:11','2025-09-29 21:40:32'),(309,9,10,'Jalan P14 I, Bangi (4.2 km to Alamanda Shopping Centre, Putrajaya)',NULL,NULL,'2025-09-28 23:18:12','2025-09-28 23:18:12'),(310,9,10,'Jalan Ayer Hitam B13, Bangi (7.1 km to Alamanda Shopping Centre, Putrajaya)',NULL,NULL,'2025-09-28 23:19:49','2025-09-29 21:37:45'),(311,3,197,'Persiaran Utara, Serdang (3.3 km to Alamanda Shopping Centre, Putrajaya)',NULL,NULL,'2025-09-28 23:22:51','2025-09-28 23:22:51'),(312,3,11,'LDP towards KLIA (1.6km from Puchong Gateway)',NULL,NULL,'2025-09-28 23:28:22','2025-09-28 23:28:22'),(313,3,168,'Lebuhraya Elite to KLIA/ Seremban (7.5km to Exit Putrajaya)',NULL,NULL,'2025-09-28 23:30:37','2025-10-06 19:45:36'),(314,12,12,'Lebuhraya Putrajaya-Cyberjaya, Sepang (6.5km to Kipmall Kota Warisan)',NULL,NULL,'2025-09-28 23:35:41','2025-09-28 23:35:41'),(315,4,2,'Lebuharaya Selat Klang (12.6km to Collinz International School Klang Campus), KLANG',NULL,NULL,'2025-09-28 23:44:40','2025-09-28 23:44:40'),(316,4,2,'Bulatan Berkeley (entrance from Jalan Pelangi), KLANG',NULL,NULL,'2025-09-28 23:46:42','2025-09-29 21:41:51'),(317,14,200,'Lebuhraya LDP (exit to Desa Park City)',NULL,NULL,'2025-09-28 23:48:14','2025-09-29 21:43:07'),(318,3,180,'Federal Highway - Exit from Subang Jaya to Batu Tiga Shah Alam',NULL,NULL,'2025-09-29 23:40:48','2025-09-29 23:40:48');
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_file_timelines`
--

DROP TABLE IF EXISTS `master_file_timelines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_file_timelines` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `product` date DEFAULT NULL,
  `site` date DEFAULT NULL,
  `client` date DEFAULT NULL,
  `payment` date DEFAULT NULL,
  `material_received` date DEFAULT NULL,
  `artwork` date DEFAULT NULL,
  `approval` date DEFAULT NULL,
  `sent_to_printer` date DEFAULT NULL,
  `installation` date DEFAULT NULL,
  `dismantle` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `next_follow_up` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `master_file_timelines_master_file_id_foreign` (`master_file_id`),
  CONSTRAINT `master_file_timelines_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_file_timelines`
--

LOCK TABLES `master_file_timelines` WRITE;
/*!40000 ALTER TABLE `master_file_timelines` DISABLE KEYS */;
/*!40000 ALTER TABLE `master_file_timelines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_files`
--

DROP TABLE IF EXISTS `master_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `master_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `month` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `product_category` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `traffic` varchar(255) NOT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `client` varchar(255) NOT NULL,
  `sales_person` varchar(255) DEFAULT NULL,
  `barter` varchar(255) DEFAULT NULL,
  `date_finish` date DEFAULT NULL,
  `job_number` varchar(255) DEFAULT NULL,
  `artwork` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `kltg_industry` varchar(255) DEFAULT NULL,
  `kltg_x` varchar(255) DEFAULT NULL,
  `kltg_edition` varchar(255) DEFAULT NULL,
  `kltg_material_cbp` varchar(255) DEFAULT NULL,
  `kltg_print` varchar(255) DEFAULT NULL,
  `kltg_article` varchar(255) DEFAULT NULL,
  `kltg_video` varchar(255) DEFAULT NULL,
  `kltg_leaderboard` varchar(255) DEFAULT NULL,
  `kltg_qr_code` varchar(255) DEFAULT NULL,
  `kltg_blog` varchar(255) DEFAULT NULL,
  `kltg_em` varchar(255) DEFAULT NULL,
  `kltg_remarks` varchar(255) DEFAULT NULL,
  `outdoor_size` varchar(255) DEFAULT NULL,
  `outdoor_district_council` varchar(255) DEFAULT NULL,
  `outdoor_coordinates` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `dbp_approval` enum('not_available','in_review','approved','rejected') DEFAULT 'not_available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_job_number` (`job_number`),
  KEY `master_files_client_company_FK` (`company_id`),
  CONSTRAINT `master_files_client_company_FK` FOREIGN KEY (`company_id`) REFERENCES `client_companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_files`
--

LOCK TABLES `master_files` WRITE;
/*!40000 ALTER TABLE `master_files` DISABLE KEYS */;
INSERT INTO `master_files` VALUES (1,'June','2025-06-01 00:00:00','Mongkeys Canopy','THE GUIDE','KLTG',NULL,'FH','9 Month',7900.00,'pending','None','Louise','AG',NULL,'2025-12-01','BP-0925-0001','Client',NULL,NULL,'2025-09-14 23:11:07','2025-09-19 01:33:07',NULL,NULL,'KLTG','3 (June,Sept,Dec)','51,52,53','Client','2 - June, September','2 - June, Aug','2 - July, Sept','None','None','None','6000 - July & 6000 - Nov','None',NULL,NULL,NULL,NULL,'not_available'),(2,'July','2025-07-05 00:00:00','Avis','THE GUIDE','KLTG',NULL,'FH','12 month',17900.00,'pending','None','Andrew','AG','TESting','2026-08-07','BP-0925-0002','Client',NULL,NULL,'2025-09-14 23:17:26','2025-09-16 17:48:33',NULL,NULL,'KLTG','4 Edition','52,53,54,55','Client','4 (Sept,Dec,March,June)','6 (July,Sept,Nov,Jan,March,May)','6 (Aug,Oct,Dec,Feb)','6 (Aug,Sept,Oct,Nov,Dec,Jan)','None','None','10,000 November','None',NULL,NULL,NULL,NULL,'not_available'),(10,'September','2024-12-01 00:00:00','Region Food (Life sauce)','BB','Outdoor',NULL,'GE','12 month',192000.00,'pending','Renewal','En. Nik','AG',NULL,'2025-11-30','BB-0925-0004','BGOC',NULL,NULL,'2025-09-15 00:12:48','2025-09-15 00:12:48','0179407253',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(11,'September','2025-08-29 00:00:00','KLPJ Wedding Fair','TB','Outdoor',NULL,'GE','1 Month',57000.00,'pending','20 Sites Refer Attachment','Alvin','AG',NULL,'2025-09-28','TB-0925-0005','Client',NULL,NULL,'2025-09-15 00:38:03','2025-09-19 01:33:30','0122837386',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(12,'May','2025-07-01 00:00:00','AA Pharmacy','Bunting','Outdoor',NULL,'GE','1 Month',15000.00,'pending','None','Elvin Noon','AG',NULL,'2025-08-01','OD-0925-0006','Client',NULL,NULL,'2025-09-15 00:47:55','2025-09-15 00:47:55','0177835367','elvinnoon@aapharmacy.com.my',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(13,'September','2024-06-09 00:00:00','IJN','BB','Outdoor',NULL,'FH','24 Month',12350.00,'pending','None','En. Mehazzar','AG',NULL,'2026-09-08','BB-0925-0007','BGOC',NULL,NULL,'2025-09-15 23:10:55','2025-09-19 01:32:50','01164317954',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(15,'September','2025-07-15 00:00:00','Seven Skies Islamic International School','TB','Outdoor',NULL,'N/A','3 Month',317752.00,'pending','Client Provide Artwork','Nadia',NULL,NULL,'2025-10-14','TB-0925-0008','Client',NULL,NULL,'2025-09-19 00:41:20','2025-09-19 00:41:20','0127821449',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(16,'September','2025-06-01 00:00:00','Avaland - Ecolake','Bunting','Outdoor',NULL,'N/A','3 Month',43200.00,'pending','Artwork provided by client','Hafiz',NULL,NULL,'2025-10-31','OD-0925-0009','Client',NULL,NULL,'2025-09-19 00:58:03','2025-09-19 00:58:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(17,'September','2025-08-05 00:00:00','Avaland','TB','Outdoor',NULL,'N/A','12 month',57240.00,'pending','Client and Free 2 Weeks','Hafiz',NULL,NULL,'2026-08-18','TB-0925-0010','Client',NULL,NULL,'2025-09-19 01:07:52','2025-09-19 01:07:52',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(18,'September','2025-05-01 00:00:00','PPB Hartabina (Megah Rise)','TB','Outdoor',NULL,'N/A','3 Month',11900.00,'pending','Refer NS Group','Jerek Wong',NULL,NULL,'2025-07-31','TB-0925-0011','Client',NULL,NULL,'2025-09-19 01:22:53','2025-09-19 01:22:53','0126510808',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(19,'September','2025-10-03 00:00:00','Marvel Management com','BB','Outdoor',NULL,'N/A','1 Month',11850.00,'pending','Artwork provided by client','Lifa',NULL,NULL,'2025-11-02','BB-0925-0012','Client',NULL,NULL,'2025-09-19 01:26:41','2025-09-19 01:26:41','0143083306',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(20,'September','2026-01-01 00:00:00','Ikano Coorporation','BB','Outdoor',NULL,'N/A','12 month',68000.00,'pending','Artwork provided by client','Vickie',NULL,NULL,'2026-12-31','BB-0925-0013','Client',NULL,NULL,'2025-09-19 01:29:09','2025-09-19 01:29:09','0176740661','kylie.beh@ikano.asia',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(21,'September','2025-11-01 00:00:00','Yakin Capital','TB','Outdoor',NULL,'N/A','2 Month',7900.00,'pending','none','Mr Prem',NULL,NULL,'2025-12-31','TB-0925-0014','Client',NULL,NULL,'2025-09-19 01:32:27','2025-09-19 01:32:27','0129785868',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(22,'September','2025-10-01 00:00:00','Yakin Capital','TB','Outdoor',NULL,'N/A','3 Month',12000.00,'pending','Artwork provided by client','Mr Prem',NULL,NULL,'2025-12-31','TB-0925-0015','Client',NULL,'INV-18-08-17-001','2025-09-19 01:40:25','2025-09-21 01:01:07','0129785868',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(23,'September','2024-09-01 00:00:00','Evelyn - Sungai wang','THE GUIDE','KLTG',NULL,'N/A','12 month',38000.00,'pending','None','Evelyn - Sungai wang','AG',NULL,'2025-08-31','BP-0925-0016','Client',NULL,NULL,'2025-09-19 01:47:07','2025-09-19 01:47:07','0193190827',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'not_available'),(24,'September','2025-06-01 00:00:00','Sunway Lagoon','THE GUIDE','KLTG',NULL,'N/A','6 Months',7900.00,'pending','Artwork provided by client','Emiw',NULL,NULL,'2025-09-30','BP-0925-0017','Client',NULL,NULL,'2025-09-19 01:49:47','2025-09-19 01:49:47','01126548292',NULL,NULL,NULL,NULL,NULL,'2x - 51,52 June n Sept','2 - June n sept','2 Videos - June n Sept',NULL,NULL,NULL,'12000 - Sept',NULL,NULL,NULL,NULL,NULL,'not_available');
/*!40000 ALTER TABLE `master_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_artwork_editings`
--

DROP TABLE IF EXISTS `media_artwork_editings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_artwork_editings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `total_artwork_date` date DEFAULT NULL,
  `pending_date` date DEFAULT NULL,
  `draft_wa` smallint(5) unsigned DEFAULT NULL,
  `approved` smallint(5) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_artwork_editings_master_file_id_year_month_unique` (`master_file_id`,`year`,`month`),
  KEY `media_artwork_editings_year_month_index` (`year`,`month`),
  KEY `media_artwork_editings_master_file_id_index` (`master_file_id`),
  CONSTRAINT `media_artwork_editings_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_artwork_editings`
--

LOCK TABLES `media_artwork_editings` WRITE;
/*!40000 ALTER TABLE `media_artwork_editings` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_artwork_editings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_content_calendars`
--

DROP TABLE IF EXISTS `media_content_calendars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_content_calendars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `total_artwork_date` date DEFAULT NULL,
  `pending_date` date DEFAULT NULL,
  `draft_wa` smallint(5) unsigned DEFAULT NULL,
  `approved` smallint(5) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_content_calendars_master_file_id_year_month_unique` (`master_file_id`,`year`,`month`),
  KEY `media_content_calendars_year_month_index` (`year`,`month`),
  KEY `media_content_calendars_master_file_id_index` (`master_file_id`),
  CONSTRAINT `media_content_calendars_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_content_calendars`
--

LOCK TABLES `media_content_calendars` WRITE;
/*!40000 ALTER TABLE `media_content_calendars` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_content_calendars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_coordinator_trackings`
--

DROP TABLE IF EXISTS `media_coordinator_trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_coordinator_trackings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `section` enum('content','editing','schedule','report','valueadd') NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `date_in_snapshot` varchar(255) DEFAULT NULL,
  `company_snapshot` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `client_bp` varchar(255) DEFAULT NULL,
  `x` varchar(255) DEFAULT NULL,
  `material_reminder` varchar(255) DEFAULT NULL,
  `material_received` varchar(255) DEFAULT NULL,
  `video_done` varchar(255) DEFAULT NULL,
  `video_approval` varchar(255) DEFAULT NULL,
  `video_approved` varchar(255) DEFAULT NULL,
  `video_scheduled` varchar(255) DEFAULT NULL,
  `video_posted` date DEFAULT NULL,
  `post_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mct_unique_master_year_month_section` (`master_file_id`,`year`,`month`,`section`),
  KEY `media_coordinator_trackings_created_at_index` (`created_at`),
  KEY `mct_master_file_id_idx` (`master_file_id`),
  CONSTRAINT `media_coordinator_trackings_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_coordinator_trackings`
--

LOCK TABLES `media_coordinator_trackings` WRITE;
/*!40000 ALTER TABLE `media_coordinator_trackings` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_coordinator_trackings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_monthly_details`
--

DROP TABLE IF EXISTS `media_monthly_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_monthly_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `subcategory` varchar(255) NOT NULL,
  `value_text` text DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_media_detail` (`master_file_id`,`year`,`month`,`subcategory`),
  KEY `media_monthly_details_master_file_id_index` (`master_file_id`),
  KEY `media_monthly_details_year_index` (`year`),
  KEY `media_monthly_details_month_index` (`month`),
  CONSTRAINT `media_monthly_details_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_monthly_details`
--

LOCK TABLES `media_monthly_details` WRITE;
/*!40000 ALTER TABLE `media_monthly_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_monthly_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_ongoing_jobs`
--

DROP TABLE IF EXISTS `media_ongoing_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_ongoing_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `company` varchar(255) NOT NULL,
  `product` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `jan` varchar(50) DEFAULT NULL,
  `feb` varchar(50) DEFAULT NULL,
  `mar` varchar(50) DEFAULT NULL,
  `apr` varchar(50) DEFAULT NULL,
  `may` varchar(50) DEFAULT NULL,
  `jun` varchar(50) DEFAULT NULL,
  `jul` varchar(50) DEFAULT NULL,
  `aug` varchar(50) DEFAULT NULL,
  `sep` varchar(50) DEFAULT NULL,
  `oct` varchar(50) DEFAULT NULL,
  `nov` varchar(50) DEFAULT NULL,
  `dec` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_ongoing_jobs_date_index` (`date`),
  KEY `media_ongoing_jobs_company_index` (`company`),
  KEY `media_ongoing_jobs_product_index` (`product`),
  KEY `media_ongoing_jobs_master_file_id_index` (`master_file_id`),
  CONSTRAINT `media_ongoing_jobs_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_ongoing_jobs`
--

LOCK TABLES `media_ongoing_jobs` WRITE;
/*!40000 ALTER TABLE `media_ongoing_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_ongoing_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_posting_schedulings`
--

DROP TABLE IF EXISTS `media_posting_schedulings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_posting_schedulings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `total_artwork_date` date DEFAULT NULL,
  `crm_date` date DEFAULT NULL,
  `meta_ads_manager_date` date DEFAULT NULL,
  `tiktok_ig_draft` smallint(5) unsigned DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_posting_schedulings_master_file_id_year_month_unique` (`master_file_id`,`year`,`month`),
  KEY `media_posting_schedulings_year_month_index` (`year`,`month`),
  KEY `media_posting_schedulings_master_file_id_index` (`master_file_id`),
  CONSTRAINT `media_posting_schedulings_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_posting_schedulings`
--

LOCK TABLES `media_posting_schedulings` WRITE;
/*!40000 ALTER TABLE `media_posting_schedulings` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_posting_schedulings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_reports`
--

DROP TABLE IF EXISTS `media_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL DEFAULT 2025,
  `month` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pending` varchar(255) DEFAULT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_reports_master_file_id_year_month_unique` (`master_file_id`,`year`,`month`),
  UNIQUE KEY `media_reports_master_year_month_unique` (`master_file_id`,`year`,`month`),
  KEY `media_reports_master_file_id_index` (`master_file_id`),
  CONSTRAINT `media_reports_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_reports`
--

LOCK TABLES `media_reports` WRITE;
/*!40000 ALTER TABLE `media_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_value_adds`
--

DROP TABLE IF EXISTS `media_value_adds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_value_adds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL DEFAULT 2025,
  `month` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `quota` varchar(255) DEFAULT NULL,
  `completed` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_value_adds_master_file_id_year_month_unique` (`master_file_id`,`year`,`month`),
  UNIQUE KEY `media_value_adds_master_year_month_unique` (`master_file_id`,`year`,`month`),
  KEY `media_value_adds_master_file_id_index` (`master_file_id`),
  CONSTRAINT `media_value_adds_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_value_adds`
--

LOCK TABLES `media_value_adds` WRITE;
/*!40000 ALTER TABLE `media_value_adds` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_value_adds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'2025_07_25_083558_create_jobs_table',1),(4,'2025_07_30_033615_add_missing_columns_to_jobs_table',1),(5,'2025_07_30_064056_create_master_files_table',1),(6,'2025_07_31_063254_add_location_and_remarks_to_master_files_table',1),(7,'2025_07_31_075702_add_month_check_columns_to_master_files_table',1),(8,'2025_08_01_072203_create_master_file_timelines_table',1),(9,'2025_08_04_061809_add_product_category_to_master_files_table',1),(10,'2025_08_04_093334_create_kltg_monthly_details_table',1),(11,'2025_08_05_022038_create_media_ongoing_jobs_table',1),(12,'2025_08_05_074244_add_monthly_matrix_to_master_files_table',1),(13,'2025_08_06_043426_create_outdoor_coordinator_trackings_table',1),(14,'2025_08_10_082858_add_master_file_id_to_media_ongoing_jobs_table',1),(15,'2025_08_10_172628_create_kltg_coordinator_lists_table',1),(16,'2025_08_10_193118_create_media_coordinator_trackings_table',1),(17,'2025_08_11_062752_create_outdoor_track_coordinator_table',1),(18,'2025_08_13_095552_update_kltg_monthly_details_normalized',1),(19,'2025_08_13_141219_add_location_to_master_files_table',1),(20,'2025_08_14_061549_add_field_type_and_value_to_kltg_monthly_details_table',1),(21,'2025_08_14_062253_fix_unique_index_on_kltg_monthly_details',1),(22,'2025_08_14_085829_alter_unique_on_kltg_monthly_details',1),(23,'2025_08_15_000000_create_media_monthly_details_table',1),(24,'2025_08_15_000002_create_outdoor_ongoing_jobs_table',1),(25,'2025_08_15_041450_alter_master_files_make_duration_nullable',1),(26,'2025_08_15_191752_fix_kltg_monthly_unique_index',1),(27,'2025_08_16_084806_fix_kltg_coordinator_date_columns',1),(28,'2025_08_16_111216_create_outdoor_monthly_details',1),(29,'2025_08_16_203146_update_x_column_in_kltg_coordinator_lists_table',1),(30,'2025_08_17_081925_create_or_fix_kltg_coordinator_lists',1),(31,'2025_08_17_142324_alter_x_to_string_on_kltg_coordinator_lists',1),(32,'2025_08_18_072005_add_unique_index_to_job_number',1),(33,'2025_08_19_060722_add_subcategory_to_kltg_coordinator_lists_table',1),(34,'2025_08_19_143931_create_kltg_coordinator_trackings_table',1),(35,'2025_08_20_030237_fix_unique_on_kltg_monthly_details',1),(36,'2025_08_20_051807_add_missing_cols_to_kltg_coordinator_lists',1),(37,'2025_08_20_061131_replace_unique_index_on_kltg_coordinator_lists',1),(38,'2025_08_21_032021_create_media_coordinator_tables',1),(39,'2025_08_31_182537_fix_unique_index_on_kltg_monthly_details',1),(40,'2025_08_31_200909_update_unique_index_on_kltg_monthly_details',1),(41,'2025_09_02_044624_add_contact_and_email_to_master_files_table',1),(42,'2025_09_02_145427_add_artwork_done_to_kltg_coordinator_lists_table',1),(43,'2025_09_02_163006_setup_media_coordinator_tables',1),(44,'2025_09_03_032819_refactor_media_coordinator_trackings',1),(45,'2025_09_04_042927_create_media_coordinator_tables',1),(46,'2025_09_05_045311_add_remarks_to_media_reports',1),(47,'2025_09_06_064802_add_kltg_and_outdoor_fields_to_master_files_table',1),(48,'2025_09_06_075624_add_amount_to_master_files_table',1),(49,'2025_09_06_093630_create_outdoor_items_table',1),(50,'2025_09_06_115119_add_sales_person_to_master_files_table',1),(51,'2025_09_06_194450_add_index_to_outdoor_monthly_details',1),(52,'2025_09_07_124124_add_year_month_to_outdoor_coordinator_trackings',1),(53,'2025_09_08_021720_create_client_feed_backlogs_table',1),(54,'2025_09_08_071942_add_barter_to_master_files',1),(55,'2025_09_09_000001_add_masterfile_created_to_outdoor',1),(56,'2025_09_09_000001_add_outdoor_item_id_to_outdoor_coordinator_trackings',1),(57,'2025_09_09_071356_fix_outdoor_monthly_unique_constraint',1),(58,'2025_09_09_084923_add_unique_on_outdoor_coordinator',1),(59,'2025_09_09_115241_add_outdoor_item_id_to_outdoor_monthly_details',1),(60,'2025_09_09_141255_rebuild_outdoor_unique_slot',1),(61,'2025_09_11_033212_add_year_month_to_kltg_coordinator_lists',1),(62,'2025_09_11_042017_add_year_month_to_kltg_coordinator_lists',1),(63,'2025_09_11_093201_add_company_and_expected_finish_date_to_client_feed_backlogs_table',1),(64,'2025_09_12_041929_fix_oct_unique_index',1),(65,'2025_09_12_042300_ensure_unique_on_outdoor_monthly_details',1),(66,'2025_09_12_095323_add_completed_to_client_feed_backlogs_status_enum',1),(67,'2025_09_15_075425_drop_check_columns_from_master_files',2),(68,'2025_09_15_075721_add_dates_to_outdoor_items_table',3),(69,'2025_09_16_131939_add_expected_finish_date_to_client_feed_backlogs',4),(70,'2025_09_16_132310_add_company_to_client_feed_backlogs',5),(71,'2025_09_16_132545_add_company_to_client_feed_backlogs',6),(72,'2025_09_17_021806_add_role_to_users',7),(73,'2025_09_17_032558_create_permission_tables',8),(74,'2025_09_18_073747_add_notes_and_dates_to_outdoor_coordinator_trackings',9),(75,'2025_09_18_083842_create_outdoor_whiteboards_table',10),(76,'2025_09_18_084605_create_outdoor_whiteboards_table',11),(77,'2025_09_19_020315_add_completed_to_outdoor_whiteboards',12),(78,'2025_09_19_065522_make_outdoor_item_id_not_nullable_on_outdoor_whiteboards',13);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',4),(3,'App\\Models\\User',1),(4,'App\\Models\\User',6),(4,'App\\Models\\User',7);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outdoor_coordinator_trackings`
--

DROP TABLE IF EXISTS `outdoor_coordinator_trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outdoor_coordinator_trackings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned DEFAULT NULL,
  `outdoor_item_id` bigint(20) unsigned DEFAULT NULL,
  `masterfile_created_at` timestamp NULL DEFAULT NULL,
  `year` smallint(5) unsigned DEFAULT NULL,
  `month` tinyint(3) unsigned DEFAULT NULL,
  `client` varchar(255) DEFAULT NULL,
  `product` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `site_date` date DEFAULT NULL,
  `payment` varchar(255) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `material` varchar(255) DEFAULT NULL,
  `material_date` date DEFAULT NULL,
  `artwork` varchar(255) DEFAULT NULL,
  `artwork_date` date DEFAULT NULL,
  `received_approval` date DEFAULT NULL,
  `received_approval_note` varchar(255) DEFAULT NULL,
  `sent_to_printer` date DEFAULT NULL,
  `sent_to_printer_note` varchar(255) DEFAULT NULL,
  `collection_printer` date DEFAULT NULL,
  `collection_printer_note` varchar(255) DEFAULT NULL,
  `installation` date DEFAULT NULL,
  `installation_note` varchar(255) DEFAULT NULL,
  `dismantle` date DEFAULT NULL,
  `dismantle_note` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `next_follow_up` date DEFAULT NULL,
  `next_follow_up_note` varchar(255) DEFAULT NULL,
  `status` enum('pending','ongoing','completed') NOT NULL DEFAULT 'pending',
  `month_jan` varchar(255) DEFAULT NULL,
  `month_feb` varchar(255) DEFAULT NULL,
  `month_mar` varchar(255) DEFAULT NULL,
  `month_apr` varchar(255) DEFAULT NULL,
  `month_may` varchar(255) DEFAULT NULL,
  `month_jun` varchar(255) DEFAULT NULL,
  `month_jul` varchar(255) DEFAULT NULL,
  `month_aug` varchar(255) DEFAULT NULL,
  `month_sep` varchar(255) DEFAULT NULL,
  `month_oct` varchar(255) DEFAULT NULL,
  `month_nov` varchar(255) DEFAULT NULL,
  `month_dec` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oct_master_outdoor_unique` (`master_file_id`,`outdoor_item_id`,`year`,`month`),
  KEY `oct_year_month_mfid` (`year`,`month`,`master_file_id`),
  KEY `outdoor_coordinator_trackings_outdoor_item_id_index` (`outdoor_item_id`),
  CONSTRAINT `outdoor_coordinator_trackings_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outdoor_coordinator_trackings`
--

LOCK TABLES `outdoor_coordinator_trackings` WRITE;
/*!40000 ALTER TABLE `outdoor_coordinator_trackings` DISABLE KEYS */;
INSERT INTO `outdoor_coordinator_trackings` VALUES (17,12,74,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'testing','2025-10-03','gilang','2025-09-29',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-17 23:30:14','2025-09-21 22:10:20'),(18,12,73,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'done','2025-09-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-18 00:25:09','2025-09-18 00:25:12'),(19,12,75,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-21 19:54:46','2025-09-21 19:54:46'),(20,16,80,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-01',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-21 20:18:39','2025-09-21 20:18:39'),(21,13,77,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-30',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pending',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-21 23:00:27','2025-09-21 23:00:28');
/*!40000 ALTER TABLE `outdoor_coordinator_trackings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outdoor_items`
--

DROP TABLE IF EXISTS `outdoor_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outdoor_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `sub_product` varchar(255) NOT NULL,
  `qty` int(10) unsigned NOT NULL DEFAULT 1,
  `site` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `district_council` varchar(255) DEFAULT NULL,
  `coordinates` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `billboard_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outdoor_items_master_file_id_index` (`master_file_id`),
  KEY `outdoor_items_start_date_index` (`start_date`),
  KEY `outdoor_items_end_date_index` (`end_date`),
  KEY `outdoor_items_billboard_FK` (`billboard_id`),
  CONSTRAINT `outdoor_items_billboard_FK` FOREIGN KEY (`billboard_id`) REFERENCES `billboards` (`id`) ON DELETE SET NULL,
  CONSTRAINT `outdoor_items_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outdoor_items`
--

LOCK TABLES `outdoor_items` WRITE;
/*!40000 ALTER TABLE `outdoor_items` DISABLE KEYS */;
INSERT INTO `outdoor_items` VALUES (49,10,'BB',1,'LDP','30x20','PJ - Paradigm Mall','2.3097624,101.3209304','Near Paradigm Mall','2024-12-01','2025-11-30','2025-09-15 00:12:48','2025-09-15 00:12:48',NULL),(50,10,'BB',1,'Citta Mall','30x20','SJ - Subang Airport','3.1281649,101.5497406','Near Subang Airport','2024-12-01','2025-11-30','2025-09-15 00:12:48','2025-09-15 00:12:48',NULL),(51,10,'BB',1,'Sg Besi','30x20','KL - Seremban','3.0565897,101.6978143','Near sungai besi','2024-12-01','2025-11-30','2025-09-15 00:12:48','2025-09-15 00:12:48',NULL),(52,10,'BB',1,'mrr2','30x20','Kepong - Ampang','3.1445108,101.7411432',NULL,'2025-01-01','2025-12-31','2025-09-15 00:12:48','2025-09-15 00:12:48',NULL),(53,11,'TB',1,'Jalan Sungai Buloh','15x10','Kawasan Land','3.168680,101.602861','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(54,11,'TB',1,'Persiaran Surian','15x10','Sunway Giza Mall','3.155495,101.593895','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(55,11,'TB',1,'Old Klang Road','15x10','Near Petron','3.099153,101.668226','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(56,11,'TB',1,'Jalan Sultan Ismail','15x10','Near Raffles College KL','3.153823,101.707662','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(57,11,'TB',1,'Jalan Tun Razak','15x10','Near Publika','3.169918,101.679442','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(58,11,'TB',1,'Jalan Yaacob Latif','15x10','Next to Jalan Kelantan','3.125032,101.726587','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(59,11,'TB',1,'Jalan Damansara','15x10',NULL,'3.134988,101.627961','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(60,11,'TB',1,'Jalan Tun Razak','15x10','Near MITEC','3.178867,101.671296','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(61,11,'TB',1,'Federal Highway','15x10','Near Bangsar South','3.111555,101.664318','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(62,11,'TB',1,'South City','15x10','The Mines, Seri Kembangan','2.0274556,101.7104306','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(63,11,'TB',1,'Jalan Maarof','15x10','Bangsar','3.1357537,101.6736747','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(64,11,'TB',1,'Along NPE','15x10','from SubangJaya to Bandar Sunway','3.097354,101.599071','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(65,11,'TB',1,'Jalan Pudu','15x10','9.5KM to MITEC','3.141694,101.711323','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(66,11,'TB',1,'Jalan Kuching','15x10','Heading to City Centre-5.1KM to MITEC','3.210499,101.671824',NULL,'2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(67,11,'TB',1,'Jalan Putra Permai','15x10','Seri Kembangan (Opposite Giant)','3.021499,101.671824','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(68,11,'TB',1,'Taman Connaught','15x10','outside IKON','TBC','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(69,11,'TB',1,'Jalan Cheras','15x10','outside Eko Cheras','TBC','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(70,11,'TB',1,'Jalan Cheras','15x10','before Leisure Mall','TBC','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(71,11,'TB',1,'KL-Seremban Highway','15x10','Towards KL','TBC','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(72,11,'TB',1,'Jalan Lapangan Terbang','15x10','from Citta Mall to Kelana Jaya Immi Office','TBC','None','2025-08-29','2025-09-28','2025-09-15 00:38:03','2025-09-15 00:38:03',NULL),(73,12,'Bunting',1,'Jalan nenada 2','6x3','Ampang','3.1674789,101.6802326','None','2025-07-04','2025-08-03','2025-09-15 00:47:55','2025-09-15 00:47:55',NULL),(74,12,'Bunting',1,'Jalan Ara bangsar','6x3','Bangsar','3.1290313,101.6694841','None','2025-07-25','2025-08-24','2025-09-15 00:47:55','2025-09-15 00:47:55',NULL),(75,12,'Bunting',1,'Jalan Seri Bintang','6x3','Kepong','3.1864989,101.6415057','None','2025-07-18','2025-08-17','2025-09-15 00:47:55','2025-09-15 00:47:55',NULL),(76,12,'Bunting',1,'Persiaran Subang','6x3','SUbang','3.0303664,101.5440178','None','2025-07-11','2025-08-10','2025-09-15 00:47:55','2025-09-15 00:47:55',NULL),(77,13,'BB',1,'Sungai Besi','30x20','KL','1.332423,101.2312','None','2024-06-06','2026-09-08','2025-09-15 23:10:55','2025-09-16 17:48:25',NULL),(78,15,'TB',1,'Persiaran Shah Alam','15x10','Towards Seven Skies',NULL,'None','2025-07-15','2025-10-14','2025-09-19 00:41:20','2025-09-19 00:41:20',NULL),(79,15,'TB',1,'Persiaran Surian','15x10','Towards Mutiara Damansara',NULL,'None','2025-07-15','2025-10-14','2025-09-19 00:41:20','2025-09-19 00:41:20',NULL),(80,16,'Bunting',1,'Lebuhraya Putrajaya - Cyberjaya','7x3','Dengkil (50)',NULL,'50 Buntings','2025-06-01','2025-10-31','2025-09-19 00:58:03','2025-09-19 00:58:03',NULL),(81,17,'BB',1,'Lebuhraya Putrajaya - Cyberjaya','15x10','Opposite Farm In The City',NULL,NULL,'2025-08-08','2026-08-18','2025-09-19 01:07:52','2025-09-19 01:07:52',NULL),(82,18,'TB',1,'Jalan SS24/2 Taman Megah','15x10','Taman megah',NULL,NULL,'2025-05-01','2025-07-31','2025-09-19 01:22:53','2025-09-19 01:22:53',NULL),(83,19,'BB',1,'Ara Damansara','30x20','Subang Airport - SJ',NULL,NULL,'2025-10-03','2025-11-02','2025-09-19 01:26:41','2025-09-19 01:26:41',NULL),(84,19,'BB',1,'Elite Highway','30x20','Subang Jaya - KLIA',NULL,NULL,'2025-10-03','2025-11-02','2025-09-19 01:26:41','2025-09-19 01:26:41',NULL),(85,19,'BB',1,'Sg Besi','30x20','Seremban - KL',NULL,NULL,'2025-10-03','2025-11-02','2025-09-19 01:26:41','2025-09-19 01:26:41',NULL),(86,20,'BB',1,'LDP','30x20','Paradigm - Curve',NULL,NULL,'2026-01-01','2026-12-31','2025-09-19 01:29:09','2025-09-19 01:29:09',NULL),(87,21,'TB',1,'TBC - Shah Alam','15x10','Shah Alam',NULL,NULL,'2025-11-01','2025-12-31','2025-09-19 01:32:27','2025-09-19 01:32:27',NULL),(88,22,'TB',1,'Near Think Tree Kindergarten','15x10',NULL,NULL,NULL,'2025-10-01','2025-12-31','2025-09-19 01:40:25','2025-09-19 01:40:25',NULL);
/*!40000 ALTER TABLE `outdoor_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outdoor_monthly_details`
--

DROP TABLE IF EXISTS `outdoor_monthly_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outdoor_monthly_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `outdoor_item_id` bigint(20) unsigned DEFAULT NULL,
  `year` int(11) NOT NULL,
  `month` tinyint(4) NOT NULL,
  `field_key` varchar(64) NOT NULL,
  `field_type` enum('text','date') NOT NULL,
  `value_text` text DEFAULT NULL,
  `value_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outdoor_unique_slot` (`master_file_id`,`outdoor_item_id`,`year`,`month`,`field_key`),
  UNIQUE KEY `omd_unique_slot` (`master_file_id`,`outdoor_item_id`,`year`,`month`,`field_key`),
  KEY `outdoor_monthly_details_outdoor_item_id_index` (`outdoor_item_id`),
  KEY `omd_master_idx` (`master_file_id`),
  KEY `omd_item_idx` (`outdoor_item_id`),
  CONSTRAINT `outdoor_monthly_details_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outdoor_monthly_details`
--

LOCK TABLES `outdoor_monthly_details` WRITE;
/*!40000 ALTER TABLE `outdoor_monthly_details` DISABLE KEYS */;
INSERT INTO `outdoor_monthly_details` VALUES (1,12,74,2025,7,'status','text','Installation',NULL,'2025-09-15 11:54:10','2025-09-15 11:54:10'),(2,12,74,2025,7,'installed_on','date',NULL,'2025-07-11','2025-09-15 11:54:28','2025-09-15 11:54:28'),(3,12,74,2025,8,'status','text','Dismantle',NULL,'2025-09-15 11:54:29','2025-09-15 11:54:29'),(4,12,74,2025,8,'installed_on','date',NULL,'2025-08-03','2025-09-15 11:55:21','2025-09-15 11:55:21'),(5,12,73,2025,7,'status','text','Installation',NULL,'2025-09-15 11:55:23','2025-09-15 11:55:23'),(6,12,73,2025,7,'installed_on','date',NULL,'2025-07-18','2025-09-15 11:55:31','2025-09-15 11:55:31'),(7,12,73,2025,8,'status','text','Dismantle',NULL,'2025-09-15 11:55:32','2025-09-15 11:55:32'),(8,12,73,2025,8,'installed_on','date',NULL,'2025-08-17','2025-09-15 11:55:51','2025-09-15 11:55:51'),(9,12,75,2025,7,'status','text','Installation',NULL,'2025-09-15 11:55:52','2025-09-15 11:55:52'),(10,12,75,2025,8,'status','text','Dismantle',NULL,'2025-09-15 11:55:55','2025-09-15 11:55:55'),(11,12,76,2025,7,'status','text','Installation',NULL,'2025-09-15 11:55:56','2025-09-15 11:55:56'),(12,12,76,2025,8,'status','text','Renewal',NULL,'2025-09-15 11:55:57','2025-09-28 23:47:16'),(13,12,76,2025,7,'installed_on','date',NULL,'2025-07-11','2025-09-15 11:56:09','2025-09-15 11:56:09'),(14,12,76,2025,8,'installed_on','date',NULL,'2025-08-10','2025-09-15 11:56:17','2025-09-15 11:56:17'),(15,12,75,2025,7,'installed_on','date',NULL,'2025-07-18','2025-09-15 11:56:23','2025-09-15 11:56:23'),(16,12,75,2025,8,'installed_on','date',NULL,'2025-08-17','2025-09-15 11:56:28','2025-09-15 11:56:28'),(17,10,49,2025,1,'status','text','Installation',NULL,'2025-09-15 11:57:01','2025-09-15 11:57:01'),(18,10,49,2025,1,'installed_on','date',NULL,'2025-01-01','2025-09-15 11:57:09','2025-09-15 11:57:09'),(19,10,52,2025,1,'status','text','Installation',NULL,'2025-09-15 11:57:10','2025-09-15 11:57:10'),(20,10,50,2025,1,'status','text','Installation',NULL,'2025-09-15 11:57:15','2025-09-15 11:57:15'),(21,10,51,2025,1,'status','text','Installation',NULL,'2025-09-15 11:57:16','2025-09-15 11:57:16'),(22,10,50,2025,1,'installed_on','date',NULL,'2025-01-01','2025-09-15 11:57:21','2025-09-15 11:57:21'),(23,10,52,2025,1,'installed_on','date',NULL,'2025-01-01','2025-09-15 11:57:26','2025-09-15 11:57:26'),(24,10,51,2025,1,'installed_on','date',NULL,'2025-01-01','2025-09-15 11:57:30','2025-09-15 11:57:30'),(25,10,50,2025,12,'status','text','Dismantle',NULL,'2025-09-15 11:57:34','2025-09-15 11:57:34'),(26,10,49,2025,12,'status','text','Dismantle',NULL,'2025-09-15 11:57:35','2025-09-15 11:57:35'),(27,10,52,2025,12,'status','text','Dismantle',NULL,'2025-09-15 11:57:37','2025-09-15 11:57:39'),(28,10,51,2025,12,'status','text','Dismantle',NULL,'2025-09-15 11:57:40','2025-09-15 11:57:40'),(29,10,50,2025,12,'installed_on','date',NULL,'2025-12-31','2025-09-15 11:57:45','2025-09-15 11:57:45'),(30,10,49,2025,12,'installed_on','date',NULL,'2025-12-31','2025-09-15 11:57:52','2025-09-15 11:57:52'),(31,10,52,2025,12,'installed_on','date',NULL,'2025-12-31','2025-09-15 11:57:56','2025-09-15 11:57:56'),(32,10,51,2025,12,'installed_on','date',NULL,'2025-12-31','2025-09-15 11:57:59','2025-09-15 11:57:59'),(33,10,50,2025,1,'payment','text','48000 - Done',NULL,'2025-09-15 11:59:52','2025-09-15 11:59:52'),(34,10,49,2025,1,'payment','text','48000 - Done',NULL,'2025-09-15 11:59:53','2025-09-15 11:59:53'),(35,10,52,2025,1,'payment','text','48000 - Done',NULL,'2025-09-15 11:59:54','2025-09-15 11:59:54'),(36,10,51,2025,1,'payment','text','48000 - Done',NULL,'2025-09-15 11:59:56','2025-09-15 11:59:56'),(37,10,50,2025,1,'material','text','On Going',NULL,'2025-09-15 12:00:07','2025-09-15 12:00:07'),(38,10,49,2025,1,'material','text','On Going',NULL,'2025-09-15 12:00:03','2025-09-15 12:00:03'),(39,10,52,2025,1,'material','text','On Going',NULL,'2025-09-15 12:00:04','2025-09-15 12:00:04'),(40,10,51,2025,1,'material','text','On Going',NULL,'2025-09-15 12:00:05','2025-09-15 12:00:05'),(41,10,50,2025,1,'artwork','text','Client',NULL,'2025-09-15 12:00:13','2025-09-15 12:00:13'),(42,10,49,2025,1,'artwork','text','Client',NULL,'2025-09-15 12:00:14','2025-09-15 12:00:14'),(43,10,52,2025,1,'artwork','text','Client',NULL,'2025-09-15 12:00:14','2025-09-15 12:00:14'),(44,10,51,2025,1,'artwork','text','Client',NULL,'2025-09-15 12:00:24','2025-09-15 12:00:24'),(45,10,50,2025,1,'received_approval','date',NULL,'2025-01-01','2025-09-15 12:00:31','2025-09-15 12:00:31'),(46,10,49,2025,1,'received_approval','date',NULL,'2025-01-01','2025-09-15 12:00:35','2025-09-15 12:00:35'),(47,10,52,2025,1,'received_approval','date',NULL,'2025-01-01','2025-09-15 12:00:38','2025-09-15 12:00:38'),(48,10,51,2025,1,'received_approval','date',NULL,'2025-01-16','2025-09-15 12:00:41','2025-09-15 12:00:41'),(49,10,50,2025,1,'sent_to_printer','date',NULL,'2025-01-05','2025-09-15 12:00:50','2025-09-15 12:00:50'),(50,10,49,2025,1,'sent_to_printer','date',NULL,'2025-01-05','2025-09-15 12:00:54','2025-09-15 12:00:54'),(51,10,52,2025,1,'sent_to_printer','date',NULL,'2025-01-05','2025-09-15 12:00:58','2025-09-15 12:00:58'),(52,10,51,2025,1,'sent_to_printer','date',NULL,'2025-01-05','2025-09-15 12:01:01','2025-09-15 12:01:01'),(53,10,50,2025,1,'collection_printer','date',NULL,'2025-01-10','2025-09-15 12:01:05','2025-09-15 12:01:05'),(54,10,49,2025,1,'collection_printer','date',NULL,'2025-01-10','2025-09-15 12:01:10','2025-09-15 12:01:10'),(55,10,52,2025,1,'collection_printer','date',NULL,'2025-01-10','2025-09-15 12:01:14','2025-09-15 12:01:14'),(56,10,51,2025,1,'collection_printer','date',NULL,'2025-01-10','2025-09-15 12:01:27','2025-09-15 12:01:27'),(57,10,50,2025,1,'installation','date',NULL,'2025-01-13','2025-09-15 12:01:34','2025-09-15 12:01:34'),(58,10,49,2025,1,'installation','date',NULL,'2025-01-13','2025-09-15 12:01:37','2025-09-15 12:01:37'),(59,10,52,2025,1,'installation','date',NULL,'2025-01-13','2025-09-15 12:01:41','2025-09-15 12:01:41'),(60,10,51,2025,1,'installation','date',NULL,'2025-01-13','2025-09-15 12:01:46','2025-09-15 12:01:46'),(61,10,50,2025,1,'dismantle','date',NULL,'2025-11-30','2025-09-15 12:02:01','2025-09-15 12:02:01'),(62,10,49,2025,1,'dismantle','date',NULL,'2025-11-30','2025-09-15 12:02:05','2025-09-15 12:02:05'),(63,10,52,2025,1,'dismantle','date',NULL,'2025-11-30','2025-09-15 12:02:09','2025-09-15 12:02:09'),(64,10,51,2025,1,'dismantle','date',NULL,'2025-12-31','2025-09-15 12:02:15','2025-09-15 12:02:15'),(65,10,50,2025,12,'payment','text','48000 - Done',NULL,'2025-09-15 12:07:18','2025-09-15 12:07:18'),(66,10,49,2025,12,'payment','text','48000 - Done',NULL,'2025-09-15 12:07:19','2025-09-15 12:07:19'),(67,10,52,2025,12,'payment','text','48000 - Done',NULL,'2025-09-15 12:07:20','2025-09-15 12:07:20'),(68,10,51,2025,12,'payment','text','48000 - Done',NULL,'2025-09-15 12:07:21','2025-09-15 12:07:21'),(69,13,77,2026,1,'status','text','Completed',NULL,'2025-09-16 00:37:55','2025-09-16 00:37:55'),(70,13,77,2026,1,'installed_on','date',NULL,'2026-01-21','2025-09-16 00:38:32','2025-09-16 00:38:32'),(71,13,77,2026,2,'status','text','Material',NULL,'2025-09-16 00:38:51','2025-09-16 00:38:51'),(72,13,77,2024,1,'status','text','Dismantle',NULL,'2025-09-21 00:37:01','2025-09-21 00:37:01'),(73,13,77,2024,1,'installed_on','date',NULL,'2025-09-09','2025-09-21 00:37:15','2025-09-21 00:37:15'),(74,10,50,2025,1,'payment_date','date',NULL,'2025-09-25','2025-09-21 18:55:10','2025-09-21 18:55:10'),(75,12,74,2025,1,'installed_on','date',NULL,'2025-09-04','2025-09-21 22:14:54','2025-09-21 22:14:54'),(76,17,81,2025,7,'status','text','Dismantle',NULL,'2025-09-28 23:47:50','2025-09-28 23:47:50');
/*!40000 ALTER TABLE `outdoor_monthly_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outdoor_ongoing_jobs`
--

DROP TABLE IF EXISTS `outdoor_ongoing_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outdoor_ongoing_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned DEFAULT NULL,
  `year` int(10) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `product` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `jan` varchar(255) DEFAULT NULL,
  `feb` varchar(255) DEFAULT NULL,
  `mar` varchar(255) DEFAULT NULL,
  `apr` varchar(255) DEFAULT NULL,
  `may` varchar(255) DEFAULT NULL,
  `jun` varchar(255) DEFAULT NULL,
  `jul` varchar(255) DEFAULT NULL,
  `aug` varchar(255) DEFAULT NULL,
  `sep` varchar(255) DEFAULT NULL,
  `oct` varchar(255) DEFAULT NULL,
  `nov` varchar(255) DEFAULT NULL,
  `dec` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'ongoing',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `outdoor_ongoing_jobs_master_file_id_foreign` (`master_file_id`),
  KEY `outdoor_ongoing_jobs_date_index` (`date`),
  KEY `outdoor_ongoing_jobs_company_index` (`company`),
  KEY `outdoor_ongoing_jobs_product_index` (`product`),
  KEY `outdoor_ongoing_jobs_year_company_index` (`year`,`company`),
  CONSTRAINT `outdoor_ongoing_jobs_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outdoor_ongoing_jobs`
--

LOCK TABLES `outdoor_ongoing_jobs` WRITE;
/*!40000 ALTER TABLE `outdoor_ongoing_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `outdoor_ongoing_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outdoor_track_coordinator`
--

DROP TABLE IF EXISTS `outdoor_track_coordinator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outdoor_track_coordinator` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `company_snapshot` varchar(255) DEFAULT NULL,
  `product_snapshot` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `payment` varchar(255) DEFAULT NULL,
  `material` varchar(255) DEFAULT NULL,
  `artwork` varchar(255) DEFAULT NULL,
  `approval` varchar(255) DEFAULT NULL,
  `sent` varchar(255) DEFAULT NULL,
  `collected` varchar(255) DEFAULT NULL,
  `install` varchar(255) DEFAULT NULL,
  `dismantle` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outdoor_track_coordinator_master_file_id_unique` (`master_file_id`),
  CONSTRAINT `outdoor_track_coordinator_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outdoor_track_coordinator`
--

LOCK TABLES `outdoor_track_coordinator` WRITE;
/*!40000 ALTER TABLE `outdoor_track_coordinator` DISABLE KEYS */;
/*!40000 ALTER TABLE `outdoor_track_coordinator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outdoor_whiteboards`
--

DROP TABLE IF EXISTS `outdoor_whiteboards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outdoor_whiteboards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `outdoor_item_id` bigint(20) unsigned NOT NULL,
  `client_text` varchar(255) DEFAULT NULL,
  `client_date` date DEFAULT NULL,
  `po_text` varchar(255) DEFAULT NULL,
  `po_date` date DEFAULT NULL,
  `supplier_text` varchar(255) DEFAULT NULL,
  `supplier_date` date DEFAULT NULL,
  `storage_text` varchar(255) DEFAULT NULL,
  `storage_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `outdoor_item_id` (`outdoor_item_id`),
  KEY `outdoor_whiteboards_completed_at_index` (`completed_at`),
  KEY `master_file_id` (`master_file_id`),
  CONSTRAINT `outdoor_whiteboards_outdoor_item_id_foreign` FOREIGN KEY (`outdoor_item_id`) REFERENCES `outdoor_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outdoor_whiteboards`
--

LOCK TABLES `outdoor_whiteboards` WRITE;
/*!40000 ALTER TABLE `outdoor_whiteboards` DISABLE KEYS */;
INSERT INTO `outdoor_whiteboards` VALUES (35,13,77,NULL,NULL,'PO-19/9/25','2025-09-20','None',NULL,'None',NULL,NULL,NULL,'2025-09-18 22:57:07','2025-09-20 23:47:37'),(36,12,73,NULL,NULL,NULL,'2025-09-24',NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-18 23:28:19','2025-09-19 00:16:24'),(37,20,86,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-21 03:00:40','2025-09-21 03:00:44'),(38,22,88,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-28 23:57:47','2025-09-21 03:27:17','2025-09-28 23:57:47');
/*!40000 ALTER TABLE `outdoor_whiteboards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'dashboard.view','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(2,'masterfile.view','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(3,'masterfile.show','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(4,'masterfile.create','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(5,'masterfile.monthly','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(6,'coordinator.view','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(7,'kltg.edit','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(8,'outdoor.edit','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(9,'media.edit','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(10,'export.run','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(11,'calendar.manage','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(12,'information.booth.view','web','2025-09-16 20:43:32','2025-09-16 20:43:32'),(13,'information.booth.create','web','2025-09-16 20:43:32','2025-09-16 20:43:32'),(14,'masterfile.import','web','2025-09-16 20:43:32','2025-09-16 20:43:32'),(15,'calendar.view','web','2025-09-16 20:47:31','2025-09-16 20:47:31'),(16,'masterfile.delete','web','2025-09-16 22:14:21','2025-09-16 22:14:21'),(17,'report.summary.view','web','2025-09-16 23:46:34','2025-09-16 23:46:34'),(18,'report.summary.export','web','2025-09-16 23:46:34','2025-09-16 23:46:34');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posting_schedulings`
--

DROP TABLE IF EXISTS `posting_schedulings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posting_schedulings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_file_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL DEFAULT 2025,
  `month` tinyint(3) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_artwork` varchar(255) DEFAULT NULL,
  `crm` varchar(255) DEFAULT NULL,
  `meta_manager` varchar(255) DEFAULT NULL,
  `tiktok_ig_draft` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posting_schedulings_master_file_id_year_month_unique` (`master_file_id`,`year`,`month`),
  UNIQUE KEY `posting_schedulings_master_year_month_unique` (`master_file_id`,`year`,`month`),
  KEY `posting_schedulings_master_file_id_index` (`master_file_id`),
  CONSTRAINT `posting_schedulings_master_file_id_foreign` FOREIGN KEY (`master_file_id`) REFERENCES `master_files` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posting_schedulings`
--

LOCK TABLES `posting_schedulings` WRITE;
/*!40000 ALTER TABLE `posting_schedulings` DISABLE KEYS */;
/*!40000 ALTER TABLE `posting_schedulings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(2,1),(2,2),(2,3),(3,1),(3,2),(3,4),(4,1),(4,2),(4,4),(5,1),(5,2),(6,1),(6,2),(7,1),(7,2),(8,1),(8,2),(9,1),(9,2),(10,1),(10,2),(10,4),(11,1),(11,2),(12,1),(12,4),(13,1),(14,1),(14,4),(15,1),(15,4),(16,1),(17,1),(18,1);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(2,'support','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(3,'user','web','2025-09-16 19:30:12','2025-09-16 19:30:12'),(4,'limited','web','2025-09-16 20:47:37','2025-09-16 20:47:37');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('CWbFxnuWU4YZCI5iKop3QZFbIXoau7ux20St5kGL',4,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYVpmR2dSVkVhVkVTUG1VTFdwZ281NmZwYXdMWXBVb093R3VLNDFRTSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZGFzaGJvYXJkL291dGRvb3IiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=',1759824952);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prefix` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `states_prefix_unique` (`prefix`),
  UNIQUE KEY `states_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (1,'SEL','Selangor','2025-09-01 17:40:23','2025-09-01 17:40:23'),(2,'WPK','Kuala Lumpur','2025-09-01 17:40:24','2025-09-01 17:40:24'),(3,'PJA','Putrajaya','2025-09-01 17:40:24','2025-09-01 17:40:24'),(4,'LAB','Labuan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(5,'JOH','Johor','2025-09-01 17:40:24','2025-09-01 17:40:24'),(6,'PNG','Pulau Pinang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(7,'PRK','Perak','2025-09-01 17:40:24','2025-09-01 17:40:24'),(8,'PHG','Pahang','2025-09-01 17:40:24','2025-09-01 17:40:24'),(9,'KDH','Kedah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(10,'KTN','Kelantan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(11,'TRG','Terengganu','2025-09-01 17:40:24','2025-09-01 17:40:24'),(12,'MLK','Melaka','2025-09-01 17:40:24','2025-09-01 17:40:24'),(13,'NSB','Negeri Sembilan','2025-09-01 17:40:24','2025-09-01 17:40:24'),(14,'PLS','Perlis','2025-09-01 17:40:24','2025-09-01 17:40:24'),(15,'SBH','Sabah','2025-09-01 17:40:24','2025-09-01 17:40:24'),(16,'SWK','Sarawak','2025-09-01 17:40:24','2025-09-01 17:40:24');
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_inventories`
--

DROP TABLE IF EXISTS `stock_inventories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_inventories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contractor_id` bigint(20) unsigned NOT NULL,
  `balance_contractor` int(10) unsigned NOT NULL DEFAULT 0,
  `balance_bgoc` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_inventories_contractor_id_foreign` (`contractor_id`),
  CONSTRAINT `stock_inventories_contractor_id_foreign` FOREIGN KEY (`contractor_id`) REFERENCES `contractors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_inventories`
--

LOCK TABLES `stock_inventories` WRITE;
/*!40000 ALTER TABLE `stock_inventories` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_inventories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_inventory_transactions`
--

DROP TABLE IF EXISTS `stock_inventory_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_inventory_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_inventory_id` bigint(20) unsigned NOT NULL,
  `billboard_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned DEFAULT NULL,
  `from_contractor_id` bigint(20) unsigned DEFAULT NULL,
  `to_contractor_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('in','out') NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 0,
  `transaction_date` datetime NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_inventory_transactions_stock_inventory_id_foreign` (`stock_inventory_id`),
  KEY `stock_inventory_transactions_billboard_id_foreign` (`billboard_id`),
  KEY `stock_inventory_transactions_client_id_foreign` (`client_id`),
  KEY `stock_inventory_transactions_from_contractor_id_foreign` (`from_contractor_id`),
  KEY `stock_inventory_transactions_to_contractor_id_foreign` (`to_contractor_id`),
  KEY `stock_inventory_transactions_created_by_foreign` (`created_by`),
  CONSTRAINT `stock_inventory_transactions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `client_companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_inventory_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_inventory_transactions_from_contractor_id_foreign` FOREIGN KEY (`from_contractor_id`) REFERENCES `contractors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_inventory_transactions_stock_inventory_id_foreign` FOREIGN KEY (`stock_inventory_id`) REFERENCES `stock_inventories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_inventory_transactions_to_contractor_id_foreign` FOREIGN KEY (`to_contractor_id`) REFERENCES `contractors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_inventory_transactions`
--

LOCK TABLES `stock_inventory_transactions` WRITE;
/*!40000 ALTER TABLE `stock_inventory_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_inventory_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@example.com',NULL,'$2y$12$IKOzgusVMQ1whuonRvC33OJMLdGNOCUrruFmOsjPfo4A16Dpn.N3C',NULL,'user','2025-09-14 23:05:29','2025-09-14 23:05:29'),(2,'Gilang Eko','gilangeko70@gmail.com',NULL,'$2y$12$oEjKlgTB2juEbLYChz57/OGx/nYnj87Bq7oeaHFtmIfpPpcuAQmPa',NULL,'user','2025-09-16 18:24:42','2025-09-16 18:24:42'),(3,'Gilang','mobilberputar@gmail.com',NULL,'$2y$12$i2i8uDC5Uxa9sFMZXXltCel/PtHv70y/fGJF0mFR1YycBKK11IZua',NULL,'user','2025-09-16 18:37:34','2025-09-16 18:37:34'),(4,'Admin One','admin1@bluedale.com.my','2025-09-16 19:14:20','$2y$12$S5gKVizG9sbTL0OLHeLXy.N59u8m5Xbwf7..J.KxUKo0WaRZdO2aS','2fJD1VJENQtFsvLvZw4gE36Woktq6rvxTclnFUM5MBoju7IwrlDUrvc4m0Sd','admin','2025-09-16 19:14:21','2025-09-16 19:14:21'),(5,'Admin Two','admin2@bluedale.com.my','2025-09-16 19:14:21','$2y$12$PEKOYv1nyYqDFXC3IJt2DemO0fggpZXdwYtP4QKe7flt4wmp32HQW',NULL,'admin','2025-09-16 19:14:21','2025-09-16 19:14:21'),(6,'User One','user1@bluedale.com.my','2025-09-16 19:14:21','$2y$12$ZiCDoJg4iniZKD6RQ2YT5uwRwrojt/KL7UM8s.E.UdZhsr54u0gGy',NULL,'limited','2025-09-16 19:14:21','2025-09-16 22:31:12'),(7,'User Two','user2@bluedale.com.my','2025-09-16 19:14:21','$2y$12$Msa2aecED45dBuDRxqMLR.NOXefsGrpwltwyFHRM1MAWnFLV7Uysy',NULL,'limited','2025-09-16 19:14:21','2025-09-16 22:22:49'),(8,'Support One','support1@bluedale.com.my','2025-09-16 19:14:21','$2y$12$6z0NkUK5G.ueYZcrGZeGrOJ7d5uRvIJWIVvFUOLNtGk57rfx5s/Oi',NULL,'support','2025-09-16 19:14:21','2025-09-16 19:14:21'),(9,'Support Two','support2@bluedale.com.my','2025-09-16 19:14:21','$2y$12$cO/Vpfb3jH89WbaGXT28Ce4Ey2WQmzaNhqZ8VqK8DmtdcEQSZlDyC',NULL,'support','2025-09-16 19:14:21','2025-09-16 19:14:21');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'bgoc_tracking_system'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-08 14:22:10
