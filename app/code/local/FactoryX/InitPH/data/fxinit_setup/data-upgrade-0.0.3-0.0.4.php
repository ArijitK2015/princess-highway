<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var Mage_Cms_Model_Page $page */
$page = Mage::getModel('cms/page');
$page->load('home');
$page->setRootTemplate('one_column');
$page->setContent('<!-- -->');
$page->save();

$installer->endSetup();