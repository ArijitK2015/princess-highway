-- MySQL dump 10.13  Distrib 5.1.69, for redhat-linux-gnu (i386)
--
-- Host: localhost    Database: bincani_ph_dev_mage_1_8_1_0
-- ------------------------------------------------------
-- Server version	5.1.69

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
-- Table structure for table `api_role`
--

DROP TABLE IF EXISTS `api_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Role id',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Parent role id',
  `tree_level` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Role level in tree',
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort order to display on admin area',
  `role_type` varchar(1) NOT NULL DEFAULT '0' COMMENT 'Role type',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'User id',
  `role_name` varchar(50) DEFAULT NULL COMMENT 'Role name',
  PRIMARY KEY (`role_id`),
  KEY `IDX_API_ROLE_PARENT_ID_SORT_ORDER` (`parent_id`,`sort_order`),
  KEY `IDX_API_ROLE_TREE_LEVEL` (`tree_level`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Api ACL Roles';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_role`
--

LOCK TABLES `api_role` WRITE;
/*!40000 ALTER TABLE `api_role` DISABLE KEYS */;
INSERT INTO `api_role` VALUES (1,0,1,0,'G',0,'api-admin'),(2,1,1,0,'U',1,'Princess Highway Online');
/*!40000 ALTER TABLE `api_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_rule`
--

DROP TABLE IF EXISTS `api_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_rule` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Api rule Id',
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Api role Id',
  `resource_id` varchar(255) DEFAULT NULL COMMENT 'Module code',
  `api_privileges` varchar(20) DEFAULT NULL COMMENT 'Privileges',
  `assert_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Assert id',
  `role_type` varchar(1) DEFAULT NULL COMMENT 'Role type',
  `api_permission` varchar(10) DEFAULT NULL COMMENT 'Permission',
  PRIMARY KEY (`rule_id`),
  KEY `IDX_API_RULE_RESOURCE_ID_ROLE_ID` (`resource_id`,`role_id`),
  KEY `IDX_API_RULE_ROLE_ID_RESOURCE_ID` (`role_id`,`resource_id`),
  CONSTRAINT `FK_API_RULE_ROLE_ID_API_ROLE_ROLE_ID` FOREIGN KEY (`role_id`) REFERENCES `api_role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8 COMMENT='Api ACL Rules';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_rule`
--

LOCK TABLES `api_rule` WRITE;
/*!40000 ALTER TABLE `api_rule` DISABLE KEYS */;
INSERT INTO `api_rule` VALUES (1,1,'core',NULL,0,'G','deny'),(2,1,'core/store',NULL,0,'G','deny'),(3,1,'core/store/info',NULL,0,'G','deny'),(4,1,'core/store/list',NULL,0,'G','deny'),(5,1,'core/magento',NULL,0,'G','deny'),(6,1,'core/magento/info',NULL,0,'G','deny'),(7,1,'directory',NULL,0,'G','deny'),(8,1,'directory/country',NULL,0,'G','deny'),(9,1,'directory/region',NULL,0,'G','deny'),(10,1,'customer',NULL,0,'G','deny'),(11,1,'customer/create',NULL,0,'G','deny'),(12,1,'customer/update',NULL,0,'G','deny'),(13,1,'customer/delete',NULL,0,'G','deny'),(14,1,'customer/info',NULL,0,'G','deny'),(15,1,'customer/address',NULL,0,'G','deny'),(16,1,'customer/address/create',NULL,0,'G','deny'),(17,1,'customer/address/update',NULL,0,'G','deny'),(18,1,'customer/address/delete',NULL,0,'G','deny'),(19,1,'customer/address/info',NULL,0,'G','deny'),(20,1,'catalog',NULL,0,'G','deny'),(21,1,'catalog/category',NULL,0,'G','deny'),(22,1,'catalog/category/create',NULL,0,'G','deny'),(23,1,'catalog/category/update',NULL,0,'G','deny'),(24,1,'catalog/category/move',NULL,0,'G','deny'),(25,1,'catalog/category/delete',NULL,0,'G','deny'),(26,1,'catalog/category/tree',NULL,0,'G','deny'),(27,1,'catalog/category/info',NULL,0,'G','deny'),(28,1,'catalog/category/attributes',NULL,0,'G','deny'),(29,1,'catalog/category/product',NULL,0,'G','deny'),(30,1,'catalog/category/product/assign',NULL,0,'G','deny'),(31,1,'catalog/category/product/update',NULL,0,'G','deny'),(32,1,'catalog/category/product/remove',NULL,0,'G','deny'),(33,1,'catalog/product',NULL,0,'G','deny'),(34,1,'catalog/product/create',NULL,0,'G','deny'),(35,1,'catalog/product/update',NULL,0,'G','deny'),(36,1,'catalog/product/delete',NULL,0,'G','deny'),(37,1,'catalog/product/update_tier_price',NULL,0,'G','deny'),(38,1,'catalog/product/info',NULL,0,'G','deny'),(39,1,'catalog/product/listOfAdditionalAttributes',NULL,0,'G','deny'),(40,1,'catalog/product/attributes',NULL,0,'G','deny'),(41,1,'catalog/product/attribute',NULL,0,'G','deny'),(42,1,'catalog/product/attribute/read',NULL,0,'G','deny'),(43,1,'catalog/product/attribute/write',NULL,0,'G','deny'),(44,1,'catalog/product/attribute/types',NULL,0,'G','deny'),(45,1,'catalog/product/attribute/create',NULL,0,'G','deny'),(46,1,'catalog/product/attribute/update',NULL,0,'G','deny'),(47,1,'catalog/product/attribute/remove',NULL,0,'G','deny'),(48,1,'catalog/product/attribute/info',NULL,0,'G','deny'),(49,1,'catalog/product/attribute/option',NULL,0,'G','deny'),(50,1,'catalog/product/attribute/option/add',NULL,0,'G','deny'),(51,1,'catalog/product/attribute/option/remove',NULL,0,'G','deny'),(52,1,'catalog/product/attribute/set',NULL,0,'G','deny'),(53,1,'catalog/product/attribute/set/list',NULL,0,'G','deny'),(54,1,'catalog/product/attribute/set/create',NULL,0,'G','deny'),(55,1,'catalog/product/attribute/set/remove',NULL,0,'G','deny'),(56,1,'catalog/product/attribute/set/attribute_add',NULL,0,'G','deny'),(57,1,'catalog/product/attribute/set/attribute_remove',NULL,0,'G','deny'),(58,1,'catalog/product/attribute/set/group_add',NULL,0,'G','deny'),(59,1,'catalog/product/attribute/set/group_rename',NULL,0,'G','deny'),(60,1,'catalog/product/attribute/set/group_remove',NULL,0,'G','deny'),(61,1,'catalog/product/link',NULL,0,'G','deny'),(62,1,'catalog/product/link/assign',NULL,0,'G','deny'),(63,1,'catalog/product/link/update',NULL,0,'G','deny'),(64,1,'catalog/product/link/remove',NULL,0,'G','deny'),(65,1,'catalog/product/media',NULL,0,'G','deny'),(66,1,'catalog/product/media/create',NULL,0,'G','deny'),(67,1,'catalog/product/media/update',NULL,0,'G','deny'),(68,1,'catalog/product/media/remove',NULL,0,'G','deny'),(69,1,'catalog/product/option',NULL,0,'G','deny'),(70,1,'catalog/product/option/add',NULL,0,'G','deny'),(71,1,'catalog/product/option/update',NULL,0,'G','deny'),(72,1,'catalog/product/option/types',NULL,0,'G','deny'),(73,1,'catalog/product/option/info',NULL,0,'G','deny'),(74,1,'catalog/product/option/list',NULL,0,'G','deny'),(75,1,'catalog/product/option/remove',NULL,0,'G','deny'),(76,1,'catalog/product/option/value',NULL,0,'G','deny'),(77,1,'catalog/product/option/value/list',NULL,0,'G','deny'),(78,1,'catalog/product/option/value/info',NULL,0,'G','deny'),(79,1,'catalog/product/option/value/add',NULL,0,'G','deny'),(80,1,'catalog/product/option/value/update',NULL,0,'G','deny'),(81,1,'catalog/product/option/value/remove',NULL,0,'G','deny'),(82,1,'catalog/product/tag',NULL,0,'G','deny'),(83,1,'catalog/product/tag/list',NULL,0,'G','deny'),(84,1,'catalog/product/tag/info',NULL,0,'G','deny'),(85,1,'catalog/product/tag/add',NULL,0,'G','deny'),(86,1,'catalog/product/tag/update',NULL,0,'G','deny'),(87,1,'catalog/product/tag/remove',NULL,0,'G','deny'),(88,1,'catalog/product/downloadable_link',NULL,0,'G','deny'),(89,1,'catalog/product/downloadable_link/add',NULL,0,'G','deny'),(90,1,'catalog/product/downloadable_link/list',NULL,0,'G','deny'),(91,1,'catalog/product/downloadable_link/remove',NULL,0,'G','deny'),(92,1,'sales',NULL,0,'G','deny'),(93,1,'sales/order',NULL,0,'G','deny'),(94,1,'sales/order/change',NULL,0,'G','deny'),(95,1,'sales/order/info',NULL,0,'G','deny'),(96,1,'sales/order/shipment',NULL,0,'G','deny'),(97,1,'sales/order/shipment/create',NULL,0,'G','deny'),(98,1,'sales/order/shipment/comment',NULL,0,'G','deny'),(99,1,'sales/order/shipment/track',NULL,0,'G','deny'),(100,1,'sales/order/shipment/info',NULL,0,'G','deny'),(101,1,'sales/order/shipment/send',NULL,0,'G','deny'),(102,1,'sales/order/invoice',NULL,0,'G','deny'),(103,1,'sales/order/invoice/create',NULL,0,'G','deny'),(104,1,'sales/order/invoice/comment',NULL,0,'G','deny'),(105,1,'sales/order/invoice/capture',NULL,0,'G','deny'),(106,1,'sales/order/invoice/void',NULL,0,'G','deny'),(107,1,'sales/order/invoice/cancel',NULL,0,'G','deny'),(108,1,'sales/order/invoice/info',NULL,0,'G','deny'),(109,1,'sales/order/creditmemo',NULL,0,'G','deny'),(110,1,'sales/order/creditmemo/create',NULL,0,'G','deny'),(111,1,'sales/order/creditmemo/comment',NULL,0,'G','deny'),(112,1,'sales/order/creditmemo/cancel',NULL,0,'G','deny'),(113,1,'sales/order/creditmemo/info',NULL,0,'G','deny'),(114,1,'sales/order/creditmemo/list',NULL,0,'G','deny'),(115,1,'cataloginventory',NULL,0,'G','deny'),(116,1,'cataloginventory/update',NULL,0,'G','deny'),(117,1,'cataloginventory/info',NULL,0,'G','deny'),(118,1,'cart',NULL,0,'G','deny'),(119,1,'cart/create',NULL,0,'G','deny'),(120,1,'cart/order',NULL,0,'G','deny'),(121,1,'cart/info',NULL,0,'G','deny'),(122,1,'cart/totals',NULL,0,'G','deny'),(123,1,'cart/license',NULL,0,'G','deny'),(124,1,'cart/product',NULL,0,'G','deny'),(125,1,'cart/product/add',NULL,0,'G','deny'),(126,1,'cart/product/update',NULL,0,'G','deny'),(127,1,'cart/product/remove',NULL,0,'G','deny'),(128,1,'cart/product/list',NULL,0,'G','deny'),(129,1,'cart/product/moveToCustomerQuote',NULL,0,'G','deny'),(130,1,'cart/customer',NULL,0,'G','deny'),(131,1,'cart/customer/set',NULL,0,'G','deny'),(132,1,'cart/customer/addresses',NULL,0,'G','deny'),(133,1,'cart/shipping',NULL,0,'G','deny'),(134,1,'cart/shipping/method',NULL,0,'G','deny'),(135,1,'cart/shipping/list',NULL,0,'G','deny'),(136,1,'cart/payment',NULL,0,'G','deny'),(137,1,'cart/payment/method',NULL,0,'G','deny'),(138,1,'cart/payment/list',NULL,0,'G','deny'),(139,1,'cart/coupon',NULL,0,'G','deny'),(140,1,'cart/coupon/add',NULL,0,'G','deny'),(141,1,'cart/coupon/remove',NULL,0,'G','deny'),(142,1,'giftmessage',NULL,0,'G','deny'),(143,1,'giftmessage/set',NULL,0,'G','deny'),(144,1,'all',NULL,0,'G','allow');
/*!40000 ALTER TABLE `api_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_user`
--

DROP TABLE IF EXISTS `api_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User id',
  `firstname` varchar(32) DEFAULT NULL COMMENT 'First name',
  `lastname` varchar(32) DEFAULT NULL COMMENT 'Last name',
  `email` varchar(128) DEFAULT NULL COMMENT 'Email',
  `username` varchar(40) DEFAULT NULL COMMENT 'Nickname',
  `api_key` varchar(100) DEFAULT NULL COMMENT 'Api key',
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'User record create date',
  `modified` timestamp NULL DEFAULT NULL COMMENT 'User record modify date',
  `lognum` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Quantity of log ins',
  `reload_acl_flag` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Refresh ACL flag',
  `is_active` smallint(6) NOT NULL DEFAULT '1' COMMENT 'Account status',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Api Users';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_user`
--

LOCK TABLES `api_user` WRITE;
/*!40000 ALTER TABLE `api_user` DISABLE KEYS */;
INSERT INTO `api_user` VALUES (1,'Princess Highway Online','Admin','admin@princesshighway.com.au','factoryx','fce69bec6d160f56fad7f5c6b507270f:qj9doMYNdhhHkYOPS5O4iiKpEq9OUkla','2014-05-19 23:21:21','2014-05-19 23:21:21',5293,0,1);
/*!40000 ALTER TABLE `api_user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-26 14:02:48
