<?php

$installer = $this;

$installer->startSetup();

try {
    $surveys = Mage::getModel('customersurvey/survey')->getCollection();
    foreach ($surveys as $survey)
    {
        // Update the existing surveys
        $coupon = Mage::getModel('salesrule/coupon');
        $coupon->load($survey->getCode(), 'code');
        $survey->setCode($coupon->getRuleId());
        $survey->save();
    }

    $installer
        ->getConnection()
        ->dropColumn(
            $installer->getTable('customersurvey/survey'), 'code_title'
        );
}
catch(Exception $e)
{
    Mage::logException($e);
}

$installer->endSetup();