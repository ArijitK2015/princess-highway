<?php
/**
 * Fontis Australia Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Fontis
 * @package    Fontis_Australia
 * @author     Chris Norton
 * @copyright  Copyright (c) 2014 Fontis Pty. Ltd. (http://www.fontis.com.au)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fontis_Australia_Model_Config_Condition
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'new',         'label' => 'New'),
            array('value' => 'used',        'label' => 'Used'),
            array('value' => 'refurbished', 'label' => 'Refurbished'),
        );
    }
}
