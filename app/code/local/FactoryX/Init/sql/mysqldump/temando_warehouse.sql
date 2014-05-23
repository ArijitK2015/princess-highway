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
-- Table structure for table `temando_warehouse`
--

DROP TABLE IF EXISTS `temando_warehouse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temando_warehouse` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `postcode` varchar(50) default NULL,
  `country` varchar(50) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone_1` varchar(255) NOT NULL,
  `contact_phone_2` varchar(255) default NULL,
  `contact_fax` varchar(255) default NULL,
  `priority` int(11) default NULL,
  `location_type` varchar(255) NOT NULL,
  `store_ids` varchar(255) NOT NULL,
  `zone_ids` varchar(255) default NULL,
  `loading_facilities` char(1) NOT NULL default 'N',
  `dock` char(1) NOT NULL default 'N',
  `forklift` char(1) NOT NULL default 'N',
  `limited_access` char(1) NOT NULL default 'N',
  `postal_box` char(1) NOT NULL default 'N',
  `label_type` varchar(15) NOT NULL default '0',
  `whs_users` text,
  `account_mode` tinyint(1) NOT NULL default '0',
  `account_sandbox` tinyint(1) NOT NULL default '0',
  `account_username` varchar(255) default NULL,
  `account_password` varchar(255) default NULL,
  `account_clientid` varchar(15) default NULL,
  `account_payment` varchar(15) NOT NULL default 'Account',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temando_warehouse`
--

LOCK TABLES `temando_warehouse` WRITE;
/*!40000 ALTER TABLE `temando_warehouse` DISABLE KEYS */;
INSERT INTO `temando_warehouse` VALUES (1,'FX VIC H00',1,'Jack London','22 Mayfield Street','Abbotsford','VIC','3067','AU','Jack London Online','adam.dunstan@factoryx.com.au','+61 3 9429 0000',NULL,NULL,5,'Business','1','1','N','N','N','N','N','Thermal','a:5:{i:2;a:1:{s:8:\"position\";s:1:\"0\";}i:36;a:1:{s:8:\"position\";s:1:\"0\";}i:54;a:1:{s:8:\"position\";s:1:\"0\";}i:58;a:1:{s:8:\"position\";s:1:\"0\";}i:59;a:1:{s:8:\"position\";s:1:\"0\";}}',0,0,NULL,NULL,NULL,'Account');
/*!40000 ALTER TABLE `temando_warehouse` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-23 13:15:40
