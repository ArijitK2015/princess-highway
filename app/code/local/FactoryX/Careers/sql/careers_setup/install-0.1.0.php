<?php
/**
 * Original script from Xigmapro_Jobs
 */
$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('careers/old_careers')};
CREATE TABLE IF NOT EXISTS {$this->getTable('careers/old_careers')} (
  `Jobs_id` int(11) unsigned NOT NULL auto_increment,
  `position` varchar(255) NOT NULL default '',
   `status` smallint(6) NOT NULL default '0',
  `hours` varchar(255) NOT NULL default '',
  `entitlements` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `requirements` text NOT NULL default '',
  `countrys` varchar(255) NOT NULL default '',
  `locations` varchar(255) NOT NULL default '',
  `statuss` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`Jobs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

/*INSERT INTO `cms_page` (`title`, `root_template`, `meta_keywords`, `meta_description`, `identifier`, `content_heading`, `content`, `creation_time`, `update_time`, `is_active`, `sort_order`, `layout_update_xml`, `custom_theme`, `custom_root_template`, `custom_layout_update_xml`, `custom_theme_from`, `custom_theme_to`) VALUES
('Jobs View', 'two_columns_right', '', '', 'jobsview.html', '', '<p>{{block type=\"core/template\" name=\"Jobsview\" template=\"Jobs/Jobsview.phtml\"}}</p>', '2007-08-30 14:01:18', '2011-07-15 08:31:12', 0, 0, '', '', '', '', NULL, NULL),
('Jobs View Details', 'two_columns_right', '', '', 'jobsviewdetails.html', '', '<p>{{block type=\"core/template\" name=\"Jobsviewdetails\" template=\"Jobs/Jobsviewdetails.phtml\"}}</p>', '2011-07-15 08:30:19', '2011-07-15 08:48:34', 0, 0, '', '', '', '', NULL, NULL),
('Online From', 'two_columns_right', '', '', 'onlineform.html', '', '<p>{{block type=\"core/template\" name=\"onlineform\" template=\"Jobs/online/onlineform.phtml\"}}</p>', '2011-07-15 09:55:31', '2011-07-15 09:57:47', 0, 0, '', '', '', '', NULL, NULL);*/

$installer->endSetup();