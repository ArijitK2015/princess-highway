<?php

/**
 * Class FactoryX_Instagram_Model_Entity_Attribute_Backend_Text
 */
class FactoryX_Instagram_Model_Entity_Attribute_Backend_Text extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract{
    /**
     * @param $object
     * @return mixed
     */
    public function beforeSave($object)
    {
        //before sabing the product check if the attribute `custom_values` is array.
        //if it is, serialize it for saving in the db
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);

        if ($object->getVisibility() == Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) {
            if (!is_array($data)) {
                $data = array();
            }
            if (array_key_exists('hash_tag', $data)) {
                $data['hash_tag'] = $data['hash_tag'] . " " . str_replace(' ', '', "#" . strtolower(Mage::getStoreConfig('general/store_information/name')) . strtolower($object->getName()));
            } else {
                $data['hash_tag'] = str_replace(' ', '', "#" . strtolower(Mage::getStoreConfig('general/store_information/name')) . strtolower($object->getName()));
            }
        }
        
        if (is_array($data)) {
            $data = array_filter($data);
            $object->setData($attributeCode, serialize($data));
        }
        return parent::beforeSave($object);
    }

    /**
     * @param $object
     * @return mixed
     */
    public function afterLoad($object) {
        //after loading the product, check if the value for custom_values is not an array. If it's not try to unserialize the value.
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attributeCode);
        if (!is_array($data)) {
            $object->setData($attributeCode, @unserialize($data));
        }
        return parent::afterLoad($object);
    }
}