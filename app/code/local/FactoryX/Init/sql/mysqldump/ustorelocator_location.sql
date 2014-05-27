-- MySQL dump 10.13  Distrib 5.5.33, for osx10.6 (i386)
--
-- Host: localhost    Database: princesshighway_20140521
-- ------------------------------------------------------
-- Server version	5.5.33

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
-- Table structure for table `ustorelocator_location`
--

DROP TABLE IF EXISTS `ustorelocator_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ustorelocator_location` (
  `location_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_code` varchar(3) NOT NULL,
  `title` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `region` varchar(20) NOT NULL,
  `latitude` decimal(15,10) NOT NULL,
  `longitude` decimal(15,10) NOT NULL,
  `address_display` text NOT NULL,
  `notes` text NOT NULL,
  `website_url` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `product_types` varchar(255) NOT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ustorelocator_location`
--

LOCK TABLES `ustorelocator_location` WRITE;
/*!40000 ALTER TABLE `ustorelocator_location` DISABLE KEYS */;
INSERT INTO `ustorelocator_location` VALUES (2,'Dan','Dangerfield Melbourne Central','211 LaTrobe Street Melbourne VIC Australia','VIC',-37.8101043701,144.9628295898,'211 LaTrobe Street Melbourne VIC Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-5:00</span>','','03 9663 6700',''),(3,'Dan','Dangerfield Flinders St','224 Flinders St Melbourne VIC Australia','VIC',-37.8176002502,144.9669494629,'224 Flinders St Melbourne VIC Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-8:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-6:00</span>','','03 9654 1759',''),(4,'Dan','Dangerfield Chapel St','225 Chapel St Prahran VIC Australia','VIC',-37.8507843018,144.9933166504,'225 Chapel St Prahran VIC Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-6:00</span>','','03 9529 5100',''),(5,'Dan','Dangerfield Brunswick St','289 Brunswick St Fitzroy VIC Australia','VIC',-37.7986068726,144.9783020020,'289 Brunswick St Fitzroy VIC Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-6:00</span>','','03 9416 2032',''),(6,'Dan','Dangerfield Chadstone','Shop B 50 Chadstone Shopping Center 1341 Dandenong Rd Chadstone VIC Australia','VIC',-37.8877067566,145.0800476074,'Shop B 50 Chadstone Shopping Center 1341 Dandenong Rd Chadstone VIC Australia','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Thu-Fri</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">9:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-5:00</span>','','03 9564 8299',''),(7,'Dan','Dangerfield Highpoint','Shop 1131 120 Rosamond Road Highpoint S.C. Maribyrnong VIC Australia','VIC',-37.7731399536,144.8861541748,'Shop 1131 120 Rosamond Road Highpoint S.C. Maribyrnong VIC Australia','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">10:00-5:30</span>\r\n<span class=\"openday\">Thu-Fri</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-5:30</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>','','03 9317 0511',''),(8,'Dan','Dangerfield Doncaster','Shop G 133 Doncaster S.C. 619 Doncaster Road Doncaster VIC Australia','VIC',-37.7846260071,145.1261596680,'Shop G 133 Doncaster S.C. 619 Doncaster Road Doncaster VIC Australia','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Thu-Fri</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">9:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-5:00</span>','','03 9848 2255',''),(9,'Dan','Dangerfield Newtown','268A King St Newtown NSW Australia','NSW',-33.8960304260,151.1809539795,'268A King St Newtown NSW Australia','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Fri-Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-6:00</span>','','02 9550 3076',''),(10,'Dan','Dangerfield Bondi','420-422 Oxford Street Bondi Junction NSW Australia','NSW',-33.8916664124,151.2482910156,'420-422 Oxford Street Bondi Junction NSW Australia','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">9:00-6:00</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Fri-Sat</span><span class=\"opentime\">9:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-5:00</span>','','02 9389 6342',''),(11,'Dan','Dangerfield Market City','Shop 132A Market City S.C. Haymarket NSW Australia','NSW',-33.8796157837,151.2042236328,'Shop 132A Market City S.C. Haymarket NSW Australia','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">10:00-8:00</span>\r\n<span class=\"openday\">Fri-Sat</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-7:00</span>','','02 9280 3760',''),(12,'Dan','Dangerfield Galeries','Shop RLG 04 The Galeries Victoria 500 George Street Sydney NSW Australia','NSW',-33.8723945618,151.2078857422,'Shop RLG 04 The Galeries Victoria 500 George Street Sydney NSW Australia','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Fri-Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>','','02 9264 5011',''),(13,'Dan','Dangerfield Rundle St','242 Rundle St Adelaide SA Australia','SA',-34.9224128723,138.6085815430,'242 Rundle St Adelaide SA Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-10:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">10:00-6:00</span>','','08 8232 7766',''),(14,'Dan','Dangerfield Perth Enex 100','Shop P 148 683-703 Hay St Mall Perth WA Australia','WA',-31.9528541565,115.8573379517,'Shop P 148 683-703 Hay St Mall Perth WA Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">9:00-5:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>','','08 9322 1877',''),(15,'Dan','Dangerfield Claremont','T 232 Claremont Quarter 23 St Quentin Ave Claremont Perth WA Australia','WA',-31.9828014374,115.7792129517,'T 232 Claremont Quarter 23 St Quentin Ave Claremont Perth WA Australia','<span class=\"openday\">Mon-Wed</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Thu</span><span class=\"opentime\">9:00-9:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">9:00-5:30</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">9:00-5:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>','','08 9383 1359',''),(16,'Pri','Princess Highway Brunswick Street','306-308 Brunswick Street Fitzroy VIC Australia','VIC',-37.7979965210,144.9787750244,'306-308 Brunswick Street Fitzroy VIC Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>','','03 9419 2324',''),(17,'Pri','Princess Highway Chapel Street','399 Chapel Street Prahran VIC Australia','VIC',-37.8501205444,144.9935607910,'399 Chapel Street Prahran VIC Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-7:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>','','03 9827 2950',''),(18,'Pri','Princess Highway Flinders Lane','13/258 Flinders Ln Melbourne VIC Australia','VIC',-37.8168334961,144.9656066895,'13/258 Flinders Ln Melbourne VIC Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-9:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>','','03 9650 0075',''),(19,'Dan','Dangerfield / Princess Highway Sydney Rd','105 Sydney Road Brunswick VIC Australia','VIC',-37.7729263306,144.9610443115,'105 Sydney Road Brunswick VIC Australia','<span class=\"openday\">Mon-Thu</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Fri</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sat</span><span class=\"opentime\">10:00-6:00</span>\r\n<span class=\"openday\">Sun</span><span class=\"opentime\">11:00-5:00</span>','','03 9380 2928','');
/*!40000 ALTER TABLE `ustorelocator_location` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-27 10:38:47
