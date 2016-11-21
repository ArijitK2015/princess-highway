<?php
/**
 */

class FactoryX_XMLFeed_Model_System_Config_AdditionalAttributes extends Varien_Object {

    /**
     * @return array
     */
    public function toOptionArray() {

        $options = array();
    	$ignoreAttributes = array(
    	    'sku', 'name', 'attribute_set_id', 'type_id', 'qty', 'price', 'status', 'visibility',
    	    'cost', 'manufacturer'
    	);

    	$collection = Mage::getResourceModel('catalog/product_attribute_collection')
    	    ->addFieldToFilter('is_user_defined', 1)
    	    ->addVisibleFilter();

    	foreach ($collection as $model) {
    		if (in_array($model->getAttributeCode(), $ignoreAttributes)) {
    			continue;
    		}
    		/*
    		$productCollection = Mage::getModel('catalog/product')->getCollection();
    		$productCollection->addAttributeToSelect(array($model->getAttributeCode()));
    		$productCollection->addAttributeToFilter($model->getAttributeCode(), array('gt' => 0));
    		if (count($productCollection->getData()) == 0) {
    		    continue;
    		}
            */
            $options[] = array(
                'value' => $model->getAttributeCode(),
                'label' => sprintf("%s [%s]", $model->getAttributeCode(), $model->getFrontendLabel())
            );
        }

        usort($options, function($a, $b) {
            return strcmp($a['value'], $b['value']);
        });
        return $options;
    }
}
