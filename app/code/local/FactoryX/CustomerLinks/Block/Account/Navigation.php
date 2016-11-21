<?php
/**
 * Customer Navigation Block 
 * New function added to be able to manage the links via local.xml file
 *
 * @category   Mage
 * @package    FactoryX_CustomerLinks
 * @author     Team FactoryX <raphael@factoryx.com.au>
 */
class FactoryX_CustomerLinks_Block_Account_Navigation extends Mage_Customer_Block_Account_Navigation {

    /**
     * Remove a customer account navigation link identified by its name
     * @param $name
     */
    public function removeLinkByName($name) {
        unset($this->_links[$name]);
    }

}