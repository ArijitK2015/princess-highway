<?php

/**
 * Class FactoryX_ShippedFrom_Model_Account_Product
 */
class FactoryX_ShippedFrom_Model_Account_Product extends Mage_Core_Model_Abstract
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('shippedfrom/account_product', 'entity_id');
    }


    /**
     * Getter for field label
     *
     * @param string $field
     * @return string|null
     */
    public function getFieldLabel($field)
    {
        $label = "";
        switch ($field) {
            case 'group':
                $label = Mage::helper('shippedfrom')->__('Group');
                break;
            case 'signature_on_delivery_option':
                $label = Mage::helper('shippedfrom')->__('Signature On Delivery Option');
                break;
            case 'authority_to_leave_option':
                $label = Mage::helper('shippedfrom')->__('Authority To Leave Option');
                break;                
            default:
                $field = preg_replace("/_/", " ", $field);
                $label = ucwords($field);
        }

        return $label .= ":";
    }
    
}