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
-- Table structure for table `temando_rule`
--

DROP TABLE IF EXISTS `temando_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temando_rule` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `from_date` date default NULL,
  `to_date` date default NULL,
  `priority` int(11) default NULL,
  `stop_other` tinyint(1) NOT NULL default '0',
  `store_ids` text NOT NULL,
  `group_ids` text NOT NULL,
  `condition_time_type` tinyint(4) default NULL,
  `condition_time_value` varchar(8) default NULL,
  `condition_day` text,
  `conditions_serialized` mediumtext,
  `action_rate_type` tinyint(4) NOT NULL,
  `action_static_value` float default NULL,
  `action_static_label` varchar(500) default NULL,
  `action_dynamic_carriers` text,
  `action_dynamic_filter` tinyint(4) default NULL,
  `action_dynamic_filter_auspost` tinyint(4) default NULL,
  `action_dynamic_adjustment_type` tinyint(4) default NULL,
  `action_dynamic_adjustment_value` varchar(15) default NULL,
  `action_dynamic_show_carrier_name` tinyint(4) NOT NULL default '1',
  `action_dynamic_show_carrier_method` tinyint(4) NOT NULL default '1',
  `action_dynamic_show_carrier_time` tinyint(4) NOT NULL default '1',
  `action_dynamic_label` varchar(500) default NULL,
  `action_restrict_note` text,
  `servicetype` tinyint(1) default '3',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temando_rule`
--

LOCK TABLES `temando_rule` WRITE;
/*!40000 ALTER TABLE `temando_rule` DISABLE KEYS */;
INSERT INTO `temando_rule` VALUES (1,'Standard (5 - 10 days)',1,NULL,NULL,4,0,'1','0,1,2,3',0,'00,00,00',NULL,'a:7:{s:4:\"type\";s:30:\"temando/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:2:{i:0;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:10:\"country_id\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:2:\"AU\";s:18:\"is_value_processed\";b:0;}i:1;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:27:\"base_subtotal_with_discount\";s:8:\"operator\";s:1:\"<\";s:5:\"value\";s:3:\"100\";s:18:\"is_value_processed\";b:0;}}}',1,7,'Standard (5 - 10 days)','60031',1,1,6,'8',0,0,0,'Parcel (5 - 10 days)',NULL,3),(2,'Free Parcel (5 - 10 days)',1,NULL,NULL,4,0,'1','0,1,2,3',0,'00,00,00',NULL,'a:7:{s:4:\"type\";s:30:\"temando/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:3:{i:0;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:27:\"base_subtotal_with_discount\";s:8:\"operator\";s:2:\">=\";s:5:\"value\";s:3:\"100\";s:18:\"is_value_processed\";b:0;}i:1;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:27:\"base_subtotal_with_discount\";s:8:\"operator\";s:1:\"<\";s:5:\"value\";s:3:\"200\";s:18:\"is_value_processed\";b:0;}i:2;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:10:\"country_id\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:2:\"AU\";s:18:\"is_value_processed\";b:0;}}}',2,0,'Free Parcel (5 - 10 days)','60031',1,1,6,'0',0,0,0,NULL,NULL,3),(3,'Free Express (3 - 5 days)',1,NULL,NULL,4,0,'1','0,1,2,3',0,'00,00,00',NULL,'a:7:{s:4:\"type\";s:30:\"temando/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:2:{i:0;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:27:\"base_subtotal_with_discount\";s:8:\"operator\";s:2:\">=\";s:5:\"value\";s:3:\"200\";s:18:\"is_value_processed\";b:0;}i:1;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:10:\"country_id\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:2:\"AU\";s:18:\"is_value_processed\";b:0;}}}',2,0,'Free Express (3 - 5 days)','60031',1,2,6,'0',0,0,0,NULL,NULL,2),(4,'Express (3 - 5 days)',1,NULL,NULL,4,0,'1','0,1,2,3',0,'00,00,00',NULL,'a:7:{s:4:\"type\";s:30:\"temando/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:2:{i:0;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:10:\"country_id\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:2:\"AU\";s:18:\"is_value_processed\";b:0;}i:1;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:27:\"base_subtotal_with_discount\";s:8:\"operator\";s:1:\"<\";s:5:\"value\";s:3:\"200\";s:18:\"is_value_processed\";b:0;}}}',1,10,'Express (3 - 5 days)','60031',1,2,6,'10',0,0,0,NULL,NULL,2),(5,'Test Free Parcel (5 - 10 days)',0,NULL,NULL,0,0,'1','0,1,2,3',0,'00,00,00',NULL,'a:7:{s:4:\"type\";s:30:\"temando/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:5:{s:4:\"type\";s:30:\"temando/rule_condition_address\";s:9:\"attribute\";s:8:\"postcode\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:4:\"3067\";s:18:\"is_value_processed\";b:0;}}}',2,NULL,'Test Free Parcel (5 - 10 days)',NULL,1,0,0,NULL,0,0,0,NULL,NULL,3);
/*!40000 ALTER TABLE `temando_rule` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-23 13:15:19
