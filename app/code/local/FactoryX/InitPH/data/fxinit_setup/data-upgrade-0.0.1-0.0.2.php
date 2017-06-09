<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var FactoryX_NotificationBoard_Model_Notification $notification */
$notification = Mage::getModel('factoryx_notificationboard/notification');
$notification->setData(
    array(
        'notification_title'    =>  'Free Shipping',
        'status'    =>  1,
        'message'    =>  'Free shipping for domestic orders over $150',
    )
);
$notification->save();

$installer->endSetup();