<?php
/**

*/
class FactoryX_Consolidated_Helper_Data extends Mage_Core_Helper_Abstract  {
    
	protected $logFileName = 'factoryx_consolidated.log';
	
	private static $_CONSOLIDATED_ATTRIBUTES = array(
	    0 => 'online_only',
	    1 => 'pre_order',       // TODO: remove and replace with module
	    2 => 'fx_cons_location',
	    3 => 'fx_cons_is_con',   // TODO: rename online_only to fx_cons_is_con
        4 => 'rrp',
        5 => 'season',
        6 => 'brand'
    );

    /**
     * Retrives helper
     *
     * @return FactoryX_Consolidated_Helper_Data
     */
    protected function _helper() {
        return Mage::helper('fxcons');
    }

    /**
    */
    function productAttributeExists($attributeCode) {
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
        // alternateive method
        //$attributes = Mage::getSingleton('eav/config')->getEntityAttributeCodes('catalog_product');
        //return in_array($attributeCode, $attributes);
        return (null !== $attribute->getId());
    }

    /*
    to assign attribute to Attribute Set and Attribute Group
    */
    function assignAttribute($attributeCode, $attributeSetId, $attributeGroupId) {
        /*
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
        if (null !== $attribute->getId()) {
            $attributeId = $attribute->getId();
        }
        */
        /*
        // alternate method
        $installer->addAttributeGroup(
            'catalog_product',
            $setId,
            $groupName,
            $sortOrder
        );

        $groupId = $installer->getAttributeGroup(
            'catalog_product',
            $setId,
            $groupName,
            'attribute_group_id'
        );

        // assign the attribute to the group and set
        $attributeId = $installer->getAttributeId('catalog_product', $attributeId);
        if ($attributeId > 0) {
            $installer->addAttributeToSet(
                'catalog_product',
                $setId,
                $groupId,
                $attributeId
            );
        }
        */
        
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
        //$attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);
        $attribute->setAttributeSetId($attributeSetId);
        $attribute->setAttributeGroupId($attributeGroupId);
        $attribute->save();
    }

    /**
     * add attribute
     *
     * addAttributeToSetGroup
     *
     * @return array|false
     */
    public function addAttributeToSetGroup($attributeCode, $sets = array(), $groupName) {

        // get all attribute sets
        $setIds = array();
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $attributeSetCollection = Mage::getModel('eav/entity_attribute_set')->getCollection()->setEntityTypeFilter($entityTypeId);
        foreach($attributeSetCollection as $_attributeSet) {
            Mage::helper('fxcons')->log(sprintf("%s->load %d", __METHOD__, $_attributeSet->getId()) );
            //print_r($_attributeSet->getData());
            $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($_attributeSet->getId());
            Mage::helper('fxcons')->log(sprintf("%s->attributeSet=%s", __METHOD__, $attributeSet->getAttributeSetName()) );
            // skip default
            if (preg_match("/default/i", $attributeSet->getAttributeSetName())) {
                continue;
            }
            if (count($sets) && !in_array($attributeSet->getAttributeSetName(), $sets) ) {
                continue;
            }
            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($_attributeSet->getId())
                ->setSortOrder()
                ->load();
            $groupIdGeneral = 0;
            $addedToGroup = false;
            foreach ($groups as $group) {
                if (preg_match("/general/i", $group->getAttributeGroupName()) ) {
                    $groupIdGeneral = $group->getAttributeGroupId();
                }                
                if (preg_match(sprintf("/%s/i", $groupName), $group->getAttributeGroupName()) ) {
                    $groupId = $group->getAttributeGroupId();
                    Mage::helper('fxcons')->log(sprintf("%s->assign attribute '%s' to set:%d, group:%d", __METHOD__, $attributeCode, $_attributeSet->getId(), $groupId) );
                    $this->assignAttribute($attributeCode, $_attributeSet->getId(), $groupId);
                    $addedToGroup = true;
                }
            }
            if (!$addedToGroup && $groupIdGeneral) {
                Mage::helper('fxcons')->log(sprintf("%s->assign attribute '%s' to set:%d, group:%d", __METHOD__, $attributeCode, $_attributeSet->getId(), $groupIdGeneral) );
                $this->assignAttribute($attributeCode, $_attributeSet->getId(), $groupIdGeneral);
            }
        }
        return;
    }


    /**
     * create an attribute
     *
     * For reference, see Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
     *
     * @param mixed[] $items Array structure to count the elements of.
     * @param string $labelText label
     *
     * @return int|false
     */
    public function createAttribute($labelText, $attributeCode, $values = null, $setInfo = null, $options = null, $replaceAttribute = 0) {

        // check if attribute exists
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
        if (!$replaceAttribute && null !== $attribute->getId()) {
            Mage::helper('fxcons')->log(sprintf("%s->attribute '%s' exists! skip creation", __METHOD__, $attributeCode) );
            return;
        }

        // delete attribute if it already exists
        if ($replaceAttribute && null !== $attribute->getId()) {
            Mage::helper('fxcons')->log(sprintf("%s->delete=%s", __METHOD__, $attributeCode) );
            Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode)->delete();
        }

        $labelText = trim($labelText);
        $attributeCode = trim($attributeCode);

        if ($labelText == '' || $attributeCode == '') {
            Mage::helper('fxcons')->log("Can't import the attribute with an empty label or code.  LABEL= [$labelText]  CODE= [$attributeCode]");
            return false;
        }

        if (empty($values)) {
            $values = array();
        }

        //Mage::helper('fxcons')->log(sprintf("create attribute '%s->%s'", $attributeCode, $labelText));

        // Build the data structure that will define the attribute. See
        // Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
        $data = array(
            //'label'                           => $labelText, // set below
            //  eav_attribute
            'attribute_model'                   => NULL,
            'backend_model'                     => NULL,
            'backend_type'                      => 'varchar',   // datetime, decimal, int, static, text, varchar
            'backend_table'                     => NULL,
            'is_user_defined'                   => '1',
            'frontend_model'                    => NULL,
            'source_model'                      => NULL,    // eav/entity_attribute_source_boolean, eav/entity_attribute_source_table            //'is_required'
            'is_user_defined'                   => '1',
            //'default_value'
            //'note'
            // Attribute Properties (catalog_eav_attribute)
            'is_configurable'                   => '0',
            'is_global'                         => '0',
            'frontend_input'                    => 'text',  // select, multiselect etc
            'is_unique'                         => '0',
            'is_required'                       => '0',
            'frontend_class'                    => NULL,
            'apply_to'                          => NULL,    // simple, grouped, configurable, virtual, bundle, downloadable, giftcard
            // Frontend Properties
            'is_searchable'                     => '0',     // x
            'is_visible_in_advanced_search'     => '0',     // x
            'is_comparable'                     => '0',     // x
            'is_filterable'                     => '0',     // x
            'layered_navigation_canonical'      => '0',     // x
            'is_used_for_promo_rules'           => '0',     // x
            'position'                          => '0',     // x
            'is_html_allowed_on_front'          => '1',     // x
            'is_visible_on_front'               => '0',     // x
            'used_in_product_listing'           => '0',     // x
            'used_for_sort_by'                  => '0',     // x
            // ???
            //'wysiwyg_enabled'               => '0',
       );

        // now, overlay the function param $values over the defaults
        foreach($values as $key => $newValue) {
            if (!array_key_exists($key, $data)) {
                Mage::helper('fxcons')->log("attribute feature [$key] is not valid!");
                return false;
            }
            else {
                $data[$key] = $newValue;
            }
        }

        //Mage::helper('fxcons')->log(sprintf("%s->attribute=%s", __METHOD__, print_r($data, true)) );
        $data['attribute_code'] = $attributeCode;
        $data['frontend_label'] = array(
            0 => $labelText,
            1 => '',
            3 => '',
            2 => '',
            4 => ''
        );

        // build the model
        $model = Mage::getModel('catalog/resource_eav_attribute');
        $model->addData($data);

        if (!empty($setInfo)) {
            $model->setAttributeSetId($setInfo['SetID']);
            $model->setAttributeGroupId($setInfo['GroupID']);
        }

        $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $model->setEntityTypeId($entityTypeID);
        $model->setIsUserDefined(1);

        try {
            $model->save();
        }
        catch(Exception $ex) {
            Mage::helper('fxcons')->log(sprintf("attribute [%s] could not be saved: %s", $labelText, $ex->getMessage()) );
            return false;
        }

        $id = $model->getId();
        Mage::helper('fxcons')->log(sprintf("attribute '%s' has been saved as ID %d", $labelText, $id));

        if ($options && is_array($options) && count($options)) {
            $i = 0;
            $maxOpts = 0;
            foreach($options as $key => $val) {
                //Mage::helper('fxcons')->log(sprintf("add option: %d. %s: %s", $i, $key, $val));
                $adminVal = (self::_is_assoc($options) ? $key : $val);
                $this->addAttributeValue($attributeCode, array($adminVal, $val), $i++);
                if ($maxOpts != 0 && $i >= $maxOpts) {
                    break;
                }
            }
        }
        return $id;
    }

    /**
    */
    private static function _is_assoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}

	public function getConsolidatedAttribute($index = null) {
		if (array_key_exists($index, self::$_CONSOLIDATED_ATTRIBUTES) ) {
			return self::$_CONSOLIDATED_ATTRIBUTES[$index];
		}
		else {
		    throw new Exception(sprintf("attribute at position %d not found", $index));
		}
	}
	
	public function getConsolidatedAttributes($index = null) {
	    return self::$_CONSOLIDATED_ATTRIBUTES;
	}
	
	public function isEnabled()
	{
		return Mage::getStoreConfig('fxcons/options/enable');
	}

}

?>