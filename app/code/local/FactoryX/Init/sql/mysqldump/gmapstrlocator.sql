-- MySQL dump 10.13  Distrib 5.1.63, for redhat-linux-gnu (i686)
--
-- Host: localhost    Database: shopprinc_1_8_1_0
-- ------------------------------------------------------
-- Server version	5.1.63-ius

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
-- Table structure for table `gmapstrlocator`
--

DROP TABLE IF EXISTS `gmapstrlocator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gmapstrlocator` (
  `gmapstrlocator_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `store_name` varchar(255) NOT NULL DEFAULT '',
  `district` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(255) DEFAULT '',
  `country` varchar(255) NOT NULL DEFAULT '',
  `postal_code` varchar(255) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `latitude` float(10,6) NOT NULL DEFAULT '0.000000',
  `longitude` float(10,6) NOT NULL DEFAULT '0.000000',
  `store_phone` tinytext,
  `store_fax` tinytext,
  `store_image` varchar(255) DEFAULT '',
  `store_description` text,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`gmapstrlocator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gmapstrlocator`
--

LOCK TABLES `gmapstrlocator` WRITE;
/*!40000 ALTER TABLE `gmapstrlocator` DISABLE KEYS */;
INSERT INTO `gmapstrlocator` VALUES (1,'Dangerfield Melbourne Central','Melbourne','VIC','Australia','3000','211 LaTrobe Street',-37.810104,144.962830,'03 9663 6700','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-5:00</span>',1,'2014-05-20 10:22:50','2014-05-20 10:22:50'),(2,'Dangerfield Flinders St','Melbourne','VIC','Australia','3000','224 Flinders St',-37.817600,144.966949,'03 9654 1759','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-8:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-6:00</span>',1,'2014-05-20 10:26:03','2014-05-20 10:26:03'),(3,'Dangerfield Chapel St','Prahran','VIC','Australia','3181','225 Chapel St',-37.850784,144.993317,'03 9529 5100','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-6:00</span>',1,'2014-05-20 10:33:02','2014-05-20 10:33:02'),(4,'Dangerfield Brunswick St','Fitzroy','VIC','Australia','3065','289 Brunswick St',-37.798607,144.978302,'03 9416 2032','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-6:00</span>',1,'2014-05-20 10:34:58','2014-05-20 10:34:58'),(5,'Dangerfield Chadstone','Chadstone','VIC','Australia','3148','Shop B 50 Chadstone Shopping Center 1341 Dandenong Rd',-37.887707,145.080048,'03 9564 8299','','','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Thu-Fri</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">9:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-5:00</span>',1,'2014-05-20 10:37:10','2014-05-20 10:37:10'),(6,'Dangerfield Highpoint','Maribyrnong','VIC','Australia','3032','Shop 1131 120 Rosamond Road Highpoint S.C.',-37.773140,144.886154,'03 9317 0511','','','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">10:00-5:30</span>\r\n<span class=\"openday\">Thu-Fri</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-5:30</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>',1,'2014-05-20 10:39:09','2014-05-20 10:39:09'),(7,'Dangerfield Doncaster','Doncaster','VIC','Australia','3108','Shop G 133 Doncaster S.C. 619 Doncaster Road',-37.784626,145.126160,'03 9848 2255','','','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Thu-Fri</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">9:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-5:00</span>',1,'2014-05-20 10:40:35','2014-05-20 10:40:35'),(8,'Dangerfield Newtown','Newtown','NSW','Australia','2042','268A King St',-33.896030,151.180954,'02 9550 3076','','','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Fri-Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-6:00</span>',1,'2014-05-20 10:43:50','2014-05-20 10:43:50'),(9,'Dangerfield Bondi','Bondi Junction','NSW','Australia','2022','420-422 Oxford Street',-33.891666,151.248291,'02 9389 6342','','','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">9:00-6:00</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Fri-Sat</span><span class=\"opentime\">9:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-5:00</span>',1,'2014-05-20 10:45:38','2014-05-20 10:45:38'),(10,'Dangerfield Market City','Haymarket','NSW','Australia','2000','Shop 132A Market City S.C.',-33.879616,151.204224,'02 9280 3760','','','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">10:00-8:00</span>\r\n<span class=\"openday\">Fri-Sat</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-7:00</span>',1,'2014-05-20 10:47:05','2014-05-20 10:47:05'),(11,'Dangerfield Galeries','Sydney','NSW','Australia','2000','Shop RLG 04 The Galeries Victoria 500 George Street',-33.872395,151.207886,'02 9264 5011','','','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Fri-Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>',1,'2014-05-20 10:48:31','2014-05-20 10:48:31'),(12,'Dangerfield Rundle St','Adelaide','SA','Australia','5000','242 Rundle St',-34.922413,138.608582,'08 8232 7766','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-10:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-6:00</span>',1,'2014-05-20 10:50:11','2014-05-20 10:50:11'),(13,'Dangerfield Perth Enex 100','Perth','WA','Australia','6000','Shop P 148 683-703 Hay St Mall',-31.952854,115.857338,'08 9322 1877','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">9:00-5:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>',1,'2014-05-20 10:51:41','2014-05-20 10:51:41'),(14,'Dangerfield Claremont','Claremont Perth','WA','Australia','6010','T 232 Claremont Quarter 23 St Quentin Ave',-31.982801,115.779213,'08 9383 1359','','','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">9:00-5:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>',1,'2014-05-20 10:53:51','2014-05-20 10:53:51'),(15,'Princess Highway Brunswick Street','Fitzroy','VIC','Australia','3065','306-308 Brunswick Street',-37.797997,144.978775,'03 9419 2324','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>',1,'2014-05-20 10:56:06','2014-05-20 10:56:06'),(16,'Princess Highway Chapel Street','Prahran','VIC','Australia','3181','399 Chapel Street',-37.850121,144.993561,'03 9827 2950','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>',1,'2014-05-20 10:57:05','2014-05-20 10:57:05'),(17,'Princess Highway Flinders Lane','Melbourne','VIC','Australia','3000','13/258 Flinders Ln',-37.816833,144.965607,'03 9650 0075','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>',1,'2014-05-20 10:58:24','2014-05-20 10:58:24'),(18,'Dangerfield / Princess Highway Sydney Rd','Brunswick','VIC','Australia','3056','105 Sydney Road',-37.772926,144.961044,'03 9380 2928','','','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>',1,'2014-05-20 10:59:46','2014-05-20 10:59:46');
/*!40000 ALTER TABLE `gmapstrlocator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gmapstrlocator_store`
--

DROP TABLE IF EXISTS `gmapstrlocator_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gmapstrlocator_store` (
  `gmapstrlocator_id` int(11) NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`gmapstrlocator_id`,`store_id`),
  KEY `FK_GMAPSTRLOCATOR_STORE_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='GMapStoreLocator Stores';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gmapstrlocator_store`
--

LOCK TABLES `gmapstrlocator_store` WRITE;
/*!40000 ALTER TABLE `gmapstrlocator_store` DISABLE KEYS */;
INSERT INTO `gmapstrlocator_store` VALUES (1,0),(2,0),(3,0),(4,0),(5,0),(6,0),(7,0),(8,0),(9,0),(10,0),(11,0),(12,0),(13,0),(14,0),(15,0),(16,0),(17,0),(18,0);
/*!40000 ALTER TABLE `gmapstrlocator_store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gmapstrlocator_products`
--

DROP TABLE IF EXISTS `gmapstrlocator_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gmapstrlocator_products` (
  `gmapstrlocator_product_id` int(11) NOT NULL AUTO_INCREMENT,
  `gmapstrlocator_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  UNIQUE KEY `gmapstrlocator_product_id` (`gmapstrlocator_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gmapstrlocator_products`
--

LOCK TABLES `gmapstrlocator_products` WRITE;
/*!40000 ALTER TABLE `gmapstrlocator_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `gmapstrlocator_products` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-20 21:05:16
