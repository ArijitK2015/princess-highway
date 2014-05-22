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
-- Table structure for table `magiczoom`
--

DROP TABLE IF EXISTS `magiczoom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `magiczoom` (
  `setting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `website_id` smallint(5) unsigned DEFAULT NULL,
  `group_id` smallint(5) unsigned DEFAULT NULL,
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `package` varchar(255) NOT NULL DEFAULT '',
  `theme` varchar(255) NOT NULL DEFAULT '',
  `last_edit_time` datetime DEFAULT NULL,
  `custom_settings_title` varchar(255) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `magiczoom`
--

LOCK TABLES `magiczoom` WRITE;
/*!40000 ALTER TABLE `magiczoom` DISABLE KEYS */;
INSERT INTO `magiczoom` VALUES (1,NULL,NULL,NULL,'','',NULL,'Edit Magic Zoom default settings',NULL),(2,1,1,1,'default','princess','2014-05-22 06:36:00','Settings for Main Website => Main Website Store => Default Store View => default/princess theme','a:5:{s:7:\"default\";a:1:{s:28:\"include-headers-on-all-pages\";s:3:\"Yes\";}s:7:\"product\";a:76:{s:13:\"enable-effect\";s:3:\"Yes\";s:8:\"template\";s:6:\"bottom\";s:11:\"magicscroll\";s:3:\"Yes\";s:15:\"thumb-max-width\";s:3:\"380\";s:16:\"thumb-max-height\";s:3:\"430\";s:13:\"square-images\";s:2:\"No\";s:10:\"zoom-width\";s:3:\"500\";s:11:\"zoom-height\";s:3:\"520\";s:13:\"zoom-position\";s:5:\"right\";s:10:\"zoom-align\";s:3:\"top\";s:13:\"zoom-distance\";s:2:\"15\";s:7:\"opacity\";s:2:\"50\";s:15:\"opacity-reverse\";s:3:\"Yes\";s:9:\"zoom-fade\";s:2:\"No\";s:18:\"zoom-window-effect\";s:6:\"shadow\";s:18:\"zoom-fade-in-speed\";s:3:\"400\";s:19:\"zoom-fade-out-speed\";s:3:\"400\";s:3:\"fps\";s:2:\"25\";s:9:\"smoothing\";s:3:\"Yes\";s:15:\"smoothing-speed\";s:2:\"40\";s:18:\"selector-max-width\";s:2:\"70\";s:19:\"selector-max-height\";s:2:\"70\";s:21:\"use-individual-titles\";s:3:\"Yes\";s:16:\"selectors-margin\";s:1:\"5\";s:16:\"selectors-change\";s:5:\"click\";s:15:\"selectors-class\";s:0:\"\";s:23:\"preload-selectors-small\";s:3:\"Yes\";s:21:\"preload-selectors-big\";s:3:\"Yes\";s:16:\"selectors-effect\";s:8:\"dissolve\";s:22:\"selectors-effect-speed\";s:3:\"400\";s:25:\"selectors-mouseover-delay\";s:2:\"60\";s:13:\"initialize-on\";s:4:\"load\";s:17:\"click-to-activate\";s:2:\"No\";s:19:\"click-to-deactivate\";s:2:\"No\";s:12:\"show-loading\";s:3:\"Yes\";s:11:\"loading-msg\";s:15:\"Loading zoom...\";s:15:\"loading-opacity\";s:2:\"75\";s:18:\"loading-position-x\";s:2:\"-1\";s:18:\"loading-position-y\";s:2:\"-1\";s:12:\"entire-image\";s:2:\"No\";s:10:\"show-title\";s:7:\"disable\";s:29:\"option-associated-with-images\";s:129:\"accessories_size, colour_description, dress_size, shirt_size, size_mens_28_to_36, size_shoes_girls_36_to_41, size_smlxl, top_size\";s:30:\"show-associated-product-images\";s:3:\"Yes\";s:30:\"load-associated-product-images\";s:23:\"when option is selected\";s:18:\"ignore-magento-css\";s:2:\"No\";s:12:\"show-message\";s:3:\"Yes\";s:7:\"message\";s:54:\"<i class=\"icon-search\"></i> Move your mouse over image\";s:11:\"right-click\";s:2:\"No\";s:12:\"disable-zoom\";s:2:\"No\";s:16:\"always-show-zoom\";s:2:\"No\";s:9:\"drag-mode\";s:2:\"No\";s:13:\"move-on-click\";s:3:\"Yes\";s:1:\"x\";s:2:\"-1\";s:1:\"y\";s:2:\"-1\";s:17:\"preserve-position\";s:2:\"No\";s:15:\"fit-zoom-window\";s:3:\"Yes\";s:4:\"hint\";s:2:\"No\";s:9:\"hint-text\";s:4:\"Zoom\";s:13:\"hint-position\";s:8:\"top left\";s:12:\"hint-opacity\";s:2:\"75\";s:12:\"scroll-style\";s:7:\"default\";s:16:\"show-image-title\";s:3:\"Yes\";s:4:\"loop\";s:8:\"continue\";s:5:\"speed\";s:1:\"0\";s:5:\"width\";s:1:\"0\";s:6:\"height\";s:1:\"0\";s:10:\"item-width\";s:1:\"0\";s:11:\"item-height\";s:1:\"0\";s:4:\"step\";s:1:\"3\";s:5:\"items\";s:1:\"5\";s:6:\"arrows\";s:7:\"outside\";s:14:\"arrows-opacity\";s:2:\"60\";s:20:\"arrows-hover-opacity\";s:3:\"100\";s:11:\"slider-size\";s:3:\"10%\";s:6:\"slider\";s:5:\"false\";s:8:\"duration\";s:4:\"1000\";}s:8:\"category\";a:55:{s:13:\"enable-effect\";s:3:\"Yes\";s:15:\"thumb-max-width\";s:3:\"220\";s:16:\"thumb-max-height\";s:3:\"280\";s:13:\"square-images\";s:2:\"No\";s:10:\"zoom-width\";s:3:\"500\";s:11:\"zoom-height\";s:3:\"520\";s:13:\"zoom-position\";s:5:\"right\";s:10:\"zoom-align\";s:3:\"top\";s:13:\"zoom-distance\";s:2:\"15\";s:7:\"opacity\";s:2:\"50\";s:15:\"opacity-reverse\";s:3:\"Yes\";s:9:\"zoom-fade\";s:3:\"Yes\";s:18:\"zoom-window-effect\";s:6:\"shadow\";s:18:\"zoom-fade-in-speed\";s:3:\"400\";s:19:\"zoom-fade-out-speed\";s:3:\"400\";s:3:\"fps\";s:2:\"25\";s:9:\"smoothing\";s:3:\"Yes\";s:15:\"smoothing-speed\";s:2:\"40\";s:18:\"selector-max-width\";s:2:\"70\";s:19:\"selector-max-height\";s:2:\"70\";s:31:\"show-selectors-on-category-page\";s:3:\"Yes\";s:16:\"selectors-margin\";s:1:\"5\";s:16:\"selectors-change\";s:5:\"click\";s:15:\"selectors-class\";s:0:\"\";s:23:\"preload-selectors-small\";s:3:\"Yes\";s:21:\"preload-selectors-big\";s:3:\"Yes\";s:16:\"selectors-effect\";s:8:\"dissolve\";s:22:\"selectors-effect-speed\";s:3:\"400\";s:25:\"selectors-mouseover-delay\";s:2:\"60\";s:13:\"initialize-on\";s:4:\"load\";s:17:\"click-to-activate\";s:2:\"No\";s:19:\"click-to-deactivate\";s:2:\"No\";s:12:\"show-loading\";s:3:\"Yes\";s:11:\"loading-msg\";s:15:\"Loading zoom...\";s:15:\"loading-opacity\";s:2:\"75\";s:18:\"loading-position-x\";s:2:\"-1\";s:18:\"loading-position-y\";s:2:\"-1\";s:12:\"entire-image\";s:2:\"No\";s:10:\"show-title\";s:3:\"top\";s:20:\"link-to-product-page\";s:3:\"Yes\";s:12:\"show-message\";s:2:\"No\";s:7:\"message\";s:54:\"<i class=\"icon-search\"></i> Move your mouse over image\";s:11:\"right-click\";s:2:\"No\";s:12:\"disable-zoom\";s:3:\"Yes\";s:16:\"always-show-zoom\";s:2:\"No\";s:9:\"drag-mode\";s:2:\"No\";s:13:\"move-on-click\";s:3:\"Yes\";s:1:\"x\";s:2:\"-1\";s:1:\"y\";s:2:\"-1\";s:17:\"preserve-position\";s:2:\"No\";s:15:\"fit-zoom-window\";s:3:\"Yes\";s:4:\"hint\";s:2:\"No\";s:9:\"hint-text\";s:4:\"Zoom\";s:13:\"hint-position\";s:8:\"top left\";s:12:\"hint-opacity\";s:2:\"75\";}s:16:\"newproductsblock\";a:44:{s:13:\"enable-effect\";s:2:\"No\";s:15:\"thumb-max-width\";s:3:\"135\";s:16:\"thumb-max-height\";s:3:\"135\";s:13:\"square-images\";s:2:\"No\";s:10:\"zoom-width\";s:3:\"300\";s:11:\"zoom-height\";s:3:\"300\";s:13:\"zoom-position\";s:5:\"right\";s:10:\"zoom-align\";s:3:\"top\";s:13:\"zoom-distance\";s:2:\"15\";s:7:\"opacity\";s:2:\"50\";s:15:\"opacity-reverse\";s:2:\"No\";s:9:\"zoom-fade\";s:3:\"Yes\";s:18:\"zoom-window-effect\";s:6:\"shadow\";s:18:\"zoom-fade-in-speed\";s:3:\"200\";s:19:\"zoom-fade-out-speed\";s:3:\"200\";s:3:\"fps\";s:2:\"25\";s:9:\"smoothing\";s:3:\"Yes\";s:15:\"smoothing-speed\";s:2:\"40\";s:13:\"initialize-on\";s:4:\"load\";s:17:\"click-to-activate\";s:2:\"No\";s:19:\"click-to-deactivate\";s:2:\"No\";s:12:\"show-loading\";s:3:\"Yes\";s:11:\"loading-msg\";s:15:\"Loading zoom...\";s:15:\"loading-opacity\";s:2:\"75\";s:18:\"loading-position-x\";s:2:\"-1\";s:18:\"loading-position-y\";s:2:\"-1\";s:12:\"entire-image\";s:2:\"No\";s:10:\"show-title\";s:3:\"top\";s:20:\"link-to-product-page\";s:3:\"Yes\";s:12:\"show-message\";s:2:\"No\";s:7:\"message\";s:26:\"Move your mouse over image\";s:11:\"right-click\";s:2:\"No\";s:12:\"disable-zoom\";s:2:\"No\";s:16:\"always-show-zoom\";s:2:\"No\";s:9:\"drag-mode\";s:2:\"No\";s:13:\"move-on-click\";s:3:\"Yes\";s:1:\"x\";s:2:\"-1\";s:1:\"y\";s:2:\"-1\";s:17:\"preserve-position\";s:2:\"No\";s:15:\"fit-zoom-window\";s:3:\"Yes\";s:4:\"hint\";s:3:\"Yes\";s:9:\"hint-text\";s:4:\"Zoom\";s:13:\"hint-position\";s:8:\"top left\";s:12:\"hint-opacity\";s:2:\"75\";}s:27:\"recentlyviewedproductsblock\";a:44:{s:13:\"enable-effect\";s:2:\"No\";s:15:\"thumb-max-width\";s:2:\"76\";s:16:\"thumb-max-height\";s:2:\"76\";s:13:\"square-images\";s:2:\"No\";s:10:\"zoom-width\";s:3:\"300\";s:11:\"zoom-height\";s:3:\"300\";s:13:\"zoom-position\";s:5:\"right\";s:10:\"zoom-align\";s:3:\"top\";s:13:\"zoom-distance\";s:2:\"15\";s:7:\"opacity\";s:2:\"50\";s:15:\"opacity-reverse\";s:2:\"No\";s:9:\"zoom-fade\";s:3:\"Yes\";s:18:\"zoom-window-effect\";s:6:\"shadow\";s:18:\"zoom-fade-in-speed\";s:3:\"200\";s:19:\"zoom-fade-out-speed\";s:3:\"200\";s:3:\"fps\";s:2:\"25\";s:9:\"smoothing\";s:3:\"Yes\";s:15:\"smoothing-speed\";s:2:\"40\";s:13:\"initialize-on\";s:4:\"load\";s:17:\"click-to-activate\";s:2:\"No\";s:19:\"click-to-deactivate\";s:2:\"No\";s:12:\"show-loading\";s:3:\"Yes\";s:11:\"loading-msg\";s:15:\"Loading zoom...\";s:15:\"loading-opacity\";s:2:\"75\";s:18:\"loading-position-x\";s:2:\"-1\";s:18:\"loading-position-y\";s:2:\"-1\";s:12:\"entire-image\";s:2:\"No\";s:10:\"show-title\";s:3:\"top\";s:20:\"link-to-product-page\";s:3:\"Yes\";s:12:\"show-message\";s:2:\"No\";s:7:\"message\";s:26:\"Move your mouse over image\";s:11:\"right-click\";s:2:\"No\";s:12:\"disable-zoom\";s:2:\"No\";s:16:\"always-show-zoom\";s:2:\"No\";s:9:\"drag-mode\";s:2:\"No\";s:13:\"move-on-click\";s:3:\"Yes\";s:1:\"x\";s:2:\"-1\";s:1:\"y\";s:2:\"-1\";s:17:\"preserve-position\";s:2:\"No\";s:15:\"fit-zoom-window\";s:3:\"Yes\";s:4:\"hint\";s:3:\"Yes\";s:9:\"hint-text\";s:4:\"Zoom\";s:13:\"hint-position\";s:8:\"top left\";s:12:\"hint-opacity\";s:2:\"75\";}}');
/*!40000 ALTER TABLE `magiczoom` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-05-22 16:37:41
