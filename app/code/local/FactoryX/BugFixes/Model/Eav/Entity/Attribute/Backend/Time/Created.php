<?php

/**
 * Class FactoryX_BugFixes_Model_Eav_Entity_Attribute_Backend_Time_Created
 * @see www.customerparadigm.com/magento-bug-magento-customer-create-date-juxtaposition/
 */
class FactoryX_BugFixes_Model_Eav_Entity_Attribute_Backend_Time_Created extends Mage_Eav_Model_Entity_Attribute_Backend_Time_Created
{
    /**
     * Set created date
     * Set created date in UTC time zone
     *
     * @param Mage_Core_Model_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Time_Created
     */
    public function beforeSave($object)
    {
        if (version_compare(Mage::getVersion(),"1.8.1","<=")) {
            $attributeCode = $this->getAttribute()->getAttributeCode();
            $date = $object->getData($attributeCode);
            if (is_null($date)) {
                if ($object->isObjectNew()) {
                    $object->setData($attributeCode, Varien_Date::now());
                }
            } else {
                // Date switch fix
                $date = strtotime($date);

                // convert to UTC
                $zendDate = Mage::app()->getLocale()->utcDate(null, $date, true);
                $object->setData($attributeCode, $zendDate->getIso());
            }
        } else {
            parent::beforeSave($object);
        }

        return $this;
    }

    /**
     * Convert create date from UTC to current store time zone
     *
     * @param Varien_Object $object
     * @return Mage_Eav_Model_Entity_Attribute_Backend_Time_Created
     */
    public function afterLoad($object)
    {
        if (version_compare(Mage::getVersion(),"1.8.1","<=")) {
            $attributeCode = $this->getAttribute()->getAttributeCode();
            $date = $object->getData($attributeCode);

            // Date switch fix
            if (!is_null($date)) {
                $date = strtotime($date);
            }

            $zendDate = Mage::app()->getLocale()->storeDate(null, $date, true);
            $object->setData($attributeCode, $zendDate->getIso());
        }

        parent::afterLoad($object);

        return $this;
    }
}