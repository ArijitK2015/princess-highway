<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancedreports
 * @version    2.6.4
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Advancedreports_Model_System_Config_Source_Weekdays
{
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => Mage::helper('advancedreports')->__('Monday')),
            array('value' => '2', 'label' => Mage::helper('advancedreports')->__('Tuesday')),
            array('value' => '3', 'label' => Mage::helper('advancedreports')->__('Wednesday')),
            array('value' => '4', 'label' => Mage::helper('advancedreports')->__('Thursday')),
            array('value' => '5', 'label' => Mage::helper('advancedreports')->__('Friday')),
            array('value' => '6', 'label' => Mage::helper('advancedreports')->__('Saturday')),
            array('value' => '0', 'label' => Mage::helper('advancedreports')->__('Sunday')),
        );
    }
}