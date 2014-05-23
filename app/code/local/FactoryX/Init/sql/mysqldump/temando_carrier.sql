-- MySQL dump 10.13  Distrib 5.1.73, for unknown-linux-gnu (x86_64)
--
-- Host: 116.118.248.76    Database: shopjack_mage-1-8-0-0
-- ------------------------------------------------------
-- Server version	5.0.77

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
-- Not dumping tablespaces as no INFORMATION_SCHEMA.FILES table on this server
--

--
-- Table structure for table `temando_carrier`
--

DROP TABLE IF EXISTS `temando_carrier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temando_carrier` (
  `id` int(13) unsigned NOT NULL auto_increment,
  `carrier_id` bigint(20) NOT NULL,
  `company_name` varchar(250) NOT NULL,
  `company_contact` text NOT NULL,
  `street_address` text NOT NULL,
  `street_suburb` varchar(255) NOT NULL,
  `street_city` varchar(255) NOT NULL,
  `street_state` varchar(255) NOT NULL,
  `street_postcode` varchar(255) NOT NULL,
  `street_country` varchar(255) NOT NULL,
  `postal_address` text NOT NULL,
  `postal_suburb` varchar(255) NOT NULL,
  `postal_city` varchar(255) NOT NULL,
  `postal_state` varchar(255) NOT NULL,
  `postal_postcode` varchar(255) NOT NULL,
  `postal_country` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temando_carrier`
--

LOCK TABLES `temando_carrier` WRITE;
/*!40000 ALTER TABLE `temando_carrier` DISABLE KEYS */;
INSERT INTO `temando_carrier` VALUES (1,54381,'Allied Express','Customer Service','2 Unwin St','Rosehill','Sydney','NSW','2142','AU','PO Box 257','Parramatta','Sydney','NSW','2124','AU','Please Email Only','info@temando.com','http://www.alliedexpress.com.au/'),(2,54426,'Allied Express (Bulk)','','','','','','','','','','','','','','','',''),(3,54359,'Startrack','Customer Service','Level 7 440 Elizabeth St','Melbourne','Melbourne','VIC','3000','AU','Level 7 440 Elizabeth St','Melbourne','Melbourne','VIC','3000','AU','13 12 13','','http://www.startrack.com.au/'),(4,54396,'Startrack - Auth To Leave','Customer Service','Level 7 440 Elizabeth St','Melbourne','Melbourne','VIC','3000','AU','Level 7 440 Elizabeth St','Melbourne','Melbourne','VIC','3000','AU','13 12 13','','http://www.startrack.com.au/'),(5,54360,'Bluestar Logistics','','','','','','','','','','','','','','','',''),(6,54429,'Bluestar Logistics Bulk','','','','','','','','','','','','','','','',''),(7,54433,'Capital Transport Courier','','','','','','','','','','','','','','','',''),(8,54432,'Capital Transport HDS','','','','','','','','','','','','','','','',''),(9,54425,'Couriers Please','Admiin','Unit 4, 29-33 Carter St','Homebush','Sydney','NSW','2140','AU','Unit 4, 29-33 Carter St','Homebush','Sydney','NSW','2140','AU','1300 163 244','enquiries@couriersplease.com.au','http://www.couriersplease.com.au/ezytrak'),(10,54343,'DHL','','','','','','','','','','','','','','','',''),(11,54430,'DHL MultiZone','','','','','','','','','','','','','','','',''),(12,54431,'DHL SingleZone','','','','','','','','','','','','','','','',''),(13,54427,'Fastway Couriers','Unknown','Level 9, 491 Kent Street','Sydney','Sydney','NSW','2000','AU','PO Box Q841','Sydney','QVB Post Office','NSW','1230','AU','02 8263 3900','temando.support@fastway.com.au','http://fastway.com.au/track/temando'),(14,54428,'Fastway Couriers Bulk','','','','','','','','','','','','','','','',''),(15,54344,'Hunter Express.','Customer Service','U7 26-32 Cosgrove Rd','Enfield','Sydney','NSW','2136','AU','PO BOX 5365','Chullora','Sydney','NSW','2190','AU','13 22 52','admin@hunterexpress.com.au','http://www.hunterexpress.com.au/home'),(16,54398,'Hunter Express (bulk)','','','','','','','','','','','','','','','',''),(17,54358,'Mainfreight','','','','','','','','','','','','','','','',''),(18,54410,'Northline','','','','','','','','','','','','','','','',''),(19,60031,'Australia Post (Bulk) eParcel','pending','pending','pending','pending','QLD','4000','AU','pending','pending','pending','QLD','4000','AU','pending','austposteparcel@temando.com','http://auspost.com.au/track/track.html'),(20,54371,'Civic Transport Services','Tom Leaper','1894 Princes Hwy','Clayton','Melbourne','VIC','3167','AU','PO BOX 10','South Oakleigh','Melbourne','VIC','3167','AU','13 11 27','toml@civic.com.au',''),(21,54405,'Startrack__','Customer Service','Level 7 440 Elizabeth St','Melbourne','Melbourne','VIC','3000','AU','Level 7 440 Elizabeth St','Melbourne','Melbourne','VIC','3000','AU','13 12 13','','http://www.startrack.com.au/'),(22,60006,'Allied Express - Express','Unknown','2 Unwin St','Rosehill','Sydney','NSW','2000','AU','2 Unwin St','Rosehill','Sydney','NSW','2000','AU','131373','alliedadhoc.airexpress@temando.com','http://www.alliedexpress.com.au/'),(23,54444,'Fastway Couriers','Unknown','Level 9, 491 Kent Street','Sydney','Sydney','NSW','2000','AU','PO Box Q841','Sydney','QVB Post Office','NSW','1230','AU','02 8263 3900','temando.support@fastway.com.au','http://fastway.com.au/track/temando'),(24,60052,'Couriers Please Metro Parcel','Admin','Unit 4, 29-33 Carter St','Homebush','Sydney','','2140','AU','Unit 4, 29-33 Carter St','Homebush','Sydney','','2140','AU','1300 163 244','couriersplease3@temando.com','http://www.couriersplease.com.au/ezytrak'),(25,60032,'TNT (Temando rates)','Stewart Seddon','127 Riawena Road','Salisbury','Brisbane','QLD','4107','AU','127 Riawena Road','Salisbury','Brisbane','QLD','4107','AU','131150','tntaccount@temando.com','http://www.tntexpress.com.au/interaction/asps/trackdtl_tntau.asp'),(26,60037,'TNT (Satchel Only- ATL)','Stewart Seddon','127 Riawena Road','Salisbury','Brisbane','QLD','4107','AU','127 Riawena Road','Salisbury','Brisbane','QLD','4107','AU','131150','tntsatchel@temando.com','http://www.tntexpress.com.au/interaction/asps/trackdtl_tntau.asp'),(27,60046,'Couriers Please - Parcel','Admin','Unit 4, 29-33 Carter St','Homebush','Sydney','NSW','2140','AU','Unit 4, 29-33 Carter St','Homebush','Sydney','NSW','2140','AU','1300 163 244','couriersplease2@temando.com','http://www.couriersplease.com.au/ezytrak');
/*!40000 ALTER TABLE `temando_carrier` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-23 13:14:36
