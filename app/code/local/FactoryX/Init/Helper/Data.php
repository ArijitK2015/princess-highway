<?php
/**

*/

class FactoryX_Init_Helper_Data extends Mage_Core_Helper_Abstract {


    /**

    */
    public function createCategoryTree($treeFilePath, $removeExisting = 1) {

        if ($removeExisting) {
            $resource = Mage::getSingleton('core/resource');
            $db_read = $resource->getConnection('core_read');
            $sql = sprintf("SELECT entity_id FROM %s WHERE entity_id>2 ORDER BY entity_id DESC", $resource->getTableName("catalog_category_entity"));
            Mage::log(sprintf("%s->var=%s", __METHOD__, $sql) );
            $categories = $db_read->fetchCol($sql);
            foreach ($categories as $catId) {
                $category = Mage::getModel("catalog/category")->load($catId);
                if ($catId <= 2) {
                    Mage::log(sprintf("%s->skip=%d. %s", __METHOD__, $catId, $category->getName()) );
                    continue;
                }
                try {
                    Mage::log(sprintf("%s->delete=%d. %s", __METHOD__, $catId, $category->getName()) );
                    $category->delete();
                }
                catch (Exception $e) {
                    Mage::log(sprintf("Error: %s", $e->getMessage()));
                }
            }
        }

        Mage::log(sprintf("%s->load cats from: %s", __METHOD__, $treeFilePath) );

        // open the tree file
        if (!$handle = fopen($treeFilePath, "r")) {
            $err = sprintf("failed to open file %s", $treeFilePath);
            Mage::log($err);
            die($err);
        }

        // process tree
        $last_offsets = 0;
        $last_item_per_offset = array();
        while (($line = fgets($handle)) !== false) {

        	$offset = strlen(substr($line, 0, strpos($line, '-')));
        	Mage::log(sprintf("%s->offset=%d", __METHOD__, $offset) );

        	$catName = trim(substr($line, $offset + 1));
        	Mage::log(sprintf("catName=%s", $catName));

        	$catColl = Mage::getModel('catalog/category')->getCollection()->addFieldToFilter('name', $catName)->setPageSize(1);
        	if (isset($last_item_per_offset[$offset - 1])) {
        		$catColl->addAttributeToFilter('parent_id', (int)$last_item_per_offset[$offset-1]->getId());
        	}

        	 // item exists, move on to next tree item
        	if ($catColl->count()) {
        		$last_item_per_offset[$offset] = $catColl->getFirstItem();
        		continue;
        	}
        	else {
                // no root item found
        		if ($offset - 1 == 0 && !isset($last_item_per_offset[$offset-1])) {
        			Mage::log("ERROR: root category not found. Please create the root");
        		}
        		// no parent found. something must be wrong in the file
        		else if(!isset($last_item_per_offset[$offset-1])) {
        			Mage::log("ERROR: parent item does not exist. Please check your tree file");
        		}

        		$parentitem = $last_item_per_offset[$offset-1];
        		//Mage::log(sprintf("parentitem=%s", $parentitem));

        		// create a new category item
        		$category = Mage::getModel('catalog/category');
        		$category->setStoreId(0);

                $name = $catName;
                try {
                    //$name = self::_seoUrl($catName);
            		$category->addData(array(
            			'name' 			=> $catName,
            			'meta_title'	=> $catName,
            			'display_mode'	=> Mage_Catalog_Model_Category::DM_PRODUCT,
            			'is_active'		=> 1,
            			'is_anchor'		=> 1,
            			'path'			=> $parentitem->getPath(),
            		));
        			$category->save();
        		}
        		catch (Exception $e){
        		    $err = sprintf("ERROR: %s", $e->getMessage());
        		    Mage::log($err);
        			die($err);
        		}
        		$last_item_per_offset[$offset] = $category;
        		Mage::log(sprintf("> created %s: %s", $name, $catName));
        	}
        }
        fclose($handle);
    }

    /**
    return product count associated with attribute set
    */
    public function productsUseAttributeSet($label) {
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($label, 'attribute_set_name');
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('status', array('eq' => 1))
           ->addAttributeToFilter('attribute_set_id', $attributeSet->getAttributeSetId())
           ->addAttributeToSelect('*');
        return $collection->count();
    }

    /**
    */
    public function createPronav() {

        $resource = Mage::getSingleton('core/resource');
        $dbRead = $resource->getConnection('core_read');
        $sql = "DELETE FROM pronav;";
        Mage::log(sprintf("%s->var=%s", __METHOD__, $sql) );
        $categories = $dbRead->query($sql);

        try {
            // GET TOP CATEGORIES
            $categories = Mage::getModel('catalog/category')->getCollection()
                            ->addAttributeToFilter('level', array('eq'=>2))
                            ->addAttributeToFilter('is_active', array('eq'=>1))
                            ->addAttributeToFilter('include_in_menu', array('eq'=>1))
                            ->load();
            $sql = '';
            foreach ($categories as $id => $category) {
                $fullcategory = Mage::getModel('catalog/category')->load($category->getId());
                $msg = sprintf("%s %s %s %s", $fullcategory->getId(), $fullcategory->getParentId(), $fullcategory->getName(), $fullcategory->getUrlPath());
                //Mage::log(sprintf("%s->msg=%s", __METHOD__, $msg) );

                // Create Block for the nav
                $block = Mage::getModel('cms/block');
                $block->setTitle('Pronav - '.$fullcategory->getName());
                $block->setIdentifier('pronav_'.$fullcategory->getId());
                //$block->setStores(array(array(0)));
                $block->setStores(array(0));
                $block->setIsActive(1);
                $block->setContent('<div class="row">
                        <div class="span12">
                        <table class="pronav-sub-menu">
                        <tr>
                        <td>
                        <div class="sub-category-menu">
                           <ul>
                                {{widget type="pronav/category_widget_subcategories_list" levels="1" columns="1" thumbnail_images="No" category_images="No" selected_cat="Yes" template="pronav/items/widget/link/subcategories/list.phtml" id_path="category/'.$fullcategory->getId().'"}}
                           </ul>
                        </div>
                        </td>
                        <td>
                             <div class="menu-promo">
                                   <!-- SETUP YOUR PIC HERE -->
                                   <a href="#small-promo"></a>
                                   <!-- END PIC SETUP -->
                             </div>
                        </td>
                        </tr>
                        </table>
                        </div>
                        </div>');
                //Mage::log(var_export($block, true));
                $block->save();

                Mage::log(sprintf("%s->block->getId=%s", __METHOD__, $block->getId()) );

                $pronav = Mage::getModel('pronav/pronav');
                $pronav_data = array(
                        'name' => $fullcategory->getName(),
                        'url_key' => $fullcategory->getUrlPath(),
                        'i_index' => $fullcategory->getPosition(),
                        'store_id' => 0,
                        'static_block' => $block->getId(),
                        'link' => 1,
                        'sub_position' => 1,
                        'sub_start' => 1,
                        'no_follow' => 1,
                        'responsive' => 1,
                        'status' => 1
                    );
                $pronav->setData($pronav_data);
                $pronav->save();
            }
            Mage::log("created navigation");
        }
        catch(Exception $ex) {
            //Mage::log(var_export($e, true));
            Mage::log(sprintf("%s->Error: %s", __METHOD__, $ex->getMessage()) );
            Mage::log($ex->getTraceAsString());
            //var_dump($e);
        }
    }

    /**
     * Create an atribute-set.
     *
     * For reference, see Mage_Adminhtml_Catalog_Product_SetController::saveAction().
     *
     * @return array|false
     */
    public function createAttributeSet($setName, $groups, $copyGroupsFromId = null, $attributeGroups, $replaceAttributeSet = 1) {

        // check if exists & delete
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $attributeSetCollection = Mage::getModel('eav/entity_attribute_set')->getCollection()->setEntityTypeFilter($entityTypeId);
        foreach($attributeSetCollection as $_attributeSet) {
            Mage::log(sprintf("%s->load %d", __METHOD__, $_attributeSet->getId()) );
            //print_r($_attributeSet->getData());
            $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($_attributeSet->getId());
            Mage::log(sprintf("%s->attributeSet=%s", __METHOD__, $attributeSet->getAttributeSetName()) );
            if (preg_match("/default/i", $attributeSet->getAttributeSetName())) {
                $copyGroupsFromId = $_attributeSet->getId();
            }
            if (preg_match(sprintf("/%s/i", $setName), $attributeSet->getAttributeSetName())) {
                if ($replaceAttributeSet) {
                    Mage::log(sprintf("%s->delete=%s", __METHOD__, $attributeSet->getAttributeSetName()) );
                    $attributeSet->delete();
                }
                else {
                    Mage::log(sprintf("%s->attribute set '%s' exists! skip creation", __METHOD__, $setName) );
                    return;
                }
            }
        }

        $attributeSetId = Mage::getModel('eav/entity_attribute_set');
        $setName = trim($setName);

        Mage::log(sprintf("creating attribute-set: %s", $setName));

        if (empty($setName)) {
            Mage::log(sprintf("could not create attribute set with an empty name!", $setName));
            return false;
        }

        $model = Mage::getModel('eav/entity_attribute_set');
        $entityTypeID = Mage::getModel('catalog/product')->getResource()->getTypeId();
        Mage::log("using entity-type-id ($entityTypeID).");

        $model->setEntityTypeId($entityTypeID);

        // We don't currently support groups, or more than one level. See
        // Mage_Adminhtml_Catalog_Product_SetController::saveAction().
        Mage::log("creating vanilla attribute-set with name [$setName].");
        $model->setAttributeSetName($setName);

        // We suspect that this isn't really necessary since we're just
        // initializing new sets with a name and nothing else, but we do
        // this for the purpose of completeness, and of prevention if we
        // should expand in the future.
        Mage::log(sprintf("%s->validate", __METHOD__) );
        $model->validate();

        // create the record
        try {
            Mage::log(sprintf("%s->save", __METHOD__) );
            $model->save();
        }
        catch(Exception $ex) {
            Mage::log("Initial attribute-set with name [$setName] could not be saved: " . $ex->getMessage());
            return false;
        }

        if (($setId = $model->getId()) == false) {
            Mage::log(sprintf("could not get ID from new attribute-set: %s.", $setName));
            return false;
        }

        Mage::log(sprintf("set created id=%d", $setId));

        //>>>> Load the new set with groups (mandatory).
        // Attach the same groups from the given set-ID to the new set.

        if (!empty($copyGroupsFromId)) {
            Mage::log(sprintf("cloning group configuration from existing set %d", $copyGroupsFromId));
            $model->initFromSkeleton($copyGroupsFromId);
            $model->save();
        }

        // add a group(s)
        $sortOrder = 1;
        $modelGroups = array();
        foreach($groups as $groupName) {
            Mage::log(sprintf("add group '%s'", $groupName));
            $modelGroup = Mage::getModel('eav/entity_attribute_group');
            $modelGroup->setAttributeGroupName($groupName);
            $modelGroup->setAttributeSetId($setId);
            // This is optional, and just a sorting index in the case of multiple groups
            $modelGroup->setSortOrder($sortOrder);
            $modelGroups[$sortOrder] = $modelGroup;
            $sortOrder++;
        }
        
        //$model->setGroups(array($modelGroup));
        $model->setGroups($modelGroups);
        
        // Save the final version of our set.
        try {
            $model->save();
        }
        catch(Exception $ex) {
            Mage::log("Final attribute-set with name [$setName] could not be saved: " . $ex->getMessage());
            return false;
        }

        /*
        if (($groupId = $modelGroup->getId()) == false) {
            Mage::log(sprintf("could not get ID from new group [%s]", $groupName));
            return false;
        }
        */
        
        foreach($attributeGroups as $sortOrder => $attributes) {
            Mage::log(sprintf("%s->add %d attributes to group %s", __METHOD__, count($attributes), $sortOrder) );
            if (($groupId = $modelGroups[$sortOrder]->getId()) == false) {
                Mage::log(sprintf("could not get ID from new group [%s]", get_class($modelGroups[$sortOrder])) );
                return false;
            }
            foreach($attributes as $attribute) {
                Mage::log(sprintf("%s->add attribute: %s", __METHOD__, $attribute) );
                $this->assignAttribute($attribute, $groupId, $setId);
            }
        }

        Mage::log(sprintf("created attribute-set id:%d, default-group id:%d, attributes: %s", $setId, $groupId, print_r($attributes, true)) );
        return array('setID' => $setId, 'groupID' => $groupId);
    }

/*
    $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')->load();
    foreach ($attributeSetCollection as $id=>$attributeGroup) {
        echo $attributeGroup->getAttributeGroupName();
        echo $attributeGroup->getAttributeGroupId();
        echo $attributeGroup->getAttributeSetId();
    }
*/

    /*
    to assign attribute to Attribute Set and Attribute Group
    */
    function assignAttribute($attributeCode, $attributeGroupId, $attributeSetId) {
        /*
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
        if (null !== $attribute->getId()) {
            $attributeId = $attribute->getId();
        }
        */
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
        //$attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);
        $attribute->setAttributeSetId($attributeSetId);
        $attribute->setAttributeGroupId($attributeGroupId);
        $attribute->save();
    }

    /**
     * create an attribute
     *
     * For reference, see Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
     *
     * @return int|false
     */
    public function createAttribute($labelText, $attributeCode, $values = null, $setInfo = null, $options = null, $replaceAttribute = 0) {

        // check if exists
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
        if (!$replaceAttribute && null !== $attribute->getId()) {
            Mage::log(sprintf("%s->attribute '%s' exists! skip creation", __METHOD__, $attributeCode) );
            return;
        }

        // delete attribute
        if ($replaceAttribute) {
            Mage::log(sprintf("%s->delete=%s", __METHOD__, $attributeCode) );
            Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode)->delete();
        }

        $labelText = trim($labelText);
        $attributeCode = trim($attributeCode);

        if ($labelText == '' || $attributeCode == '') {
            Mage::log("Can't import the attribute with an empty label or code.  LABEL= [$labelText]  CODE= [$attributeCode]");
            return false;
        }

        if (empty($values)) {
            $values = array();
        }

/*
        if (!empty($setInfo) && (isset($setInfo['SetID']) == false || isset($setInfo['GroupID']) == false)) {
            Mage::log("Please provide both the set-ID and the group-ID of the attribute-set if you'd like to subscribe to one.");
            return false;
        }
*/
        Mage::log("Creating attribute [$labelText] with code [$attributeCode].");

        //>>>> Build the data structure that will define the attribute. See
        //     Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
        $data = array(
            //'label'                             => $labelText,

            //  eav_attribute
            'attribute_model'                   => NULL,    // x
            'backend_model'                     => NULL,    // x
            'backend_type'                      => 'varchar',   // datetime, decimal, int, static, text, varchar
            'backend_table'                     => NULL,    // x
            'is_user_defined'                   => '1',     // x
            'frontend_model'                    => NULL,    // x
            'source_model'                      => NULL,    // eav/entity_attribute_source_boolean, eav/entity_attribute_source_table
            //'is_required'
            'is_user_defined'                   => '1',     // x
            //'default_value'
            //'note'

            // Attribute Properties (catalog_eav_attribute)
            'is_configurable'                   => '0',     // x
            'is_global'                         => '0',     // x
            'frontend_input'                    => 'text',  // select, multiselect etc
            'is_unique'                         => '0',     // x
            'is_required'                       => '0',     // x
            'frontend_class'                    => NULL,    // x
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

        // Now, overlay the incoming values on to the defaults.
        foreach($values as $key => $newValue) {
            if (!array_key_exists($key, $data)) {
                Mage::log("attribute feature [$key] is not valid!");
                return false;
            }
            else {
                $data[$key] = $newValue;
            }
        }

        //Mage::log(sprintf("%s->attribute=%s", __METHOD__, print_r($data, true)) );
        $data['attribute_code'] = $attributeCode;
        $data['frontend_label'] = array(
            0 => $labelText,
            1 => '',
            3 => '',
            2 => '',
            4 => ''
        );

        //<<<<
        //>>>> Build the model.
        $model = Mage::getModel('catalog/resource_eav_attribute');
        $model->addData($data);

        if (!empty($setInfo)) {
            $model->setAttributeSetId($setInfo['SetID']);
            $model->setAttributeGroupId($setInfo['GroupID']);
        }

        $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $model->setEntityTypeId($entityTypeID);
        $model->setIsUserDefined(1);

        //<<<<
        // Save.
        try {
            $model->save();
        }
        catch(Exception $ex) {
            Mage::log(sprintf("attribute [%s] could not be saved: %s", $labelText, $ex->getMessage()) );
            return false;
        }

        $id = $model->getId();
        Mage::log(sprintf("attribute [$labelText] has been saved as ID %d", $labelText, $id));

        if ($options && is_array($options) && count($options)) {
            $i = 0;
            $maxOpts = 0;
            foreach($options as $key => $val) {
                //Mage::log(sprintf("add option: %d. %s: %s", $i, $key, $val));
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
    private function addAttributeMulti($attributeCode, $attributeValues) {
        Mage::log(sprintf("attribute %s options: %s", $attributeCode, $attributeCode, print_r($attributeValues, true)) );
        // Get Attribute by attribute code
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        $attribute->setData('option', $attributeValues);
        $attribute->save();
    }

    private function addAttributeValue($attributeCode, $attributeValues, $i = 0) {
        // Get Attribute by attribute code
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
        // Check if a value exists in Attribute
        if (!$this->attributeValueExists($attributeCode, $attributeValues[0])) {
            // add value to option array
            $value['option'] = array(
                0 => $attributeValues[0],
                1 => $attributeValues[1],
            );
            // Set order for the option
            $order['option'] = $i;
            // Assign values to result array
            $result = array(
                'value' => $value,
                'order' => $order
            );
            // set attribute data
            //Mage::log(sprintf("add option: %s", print_r($result, true)));
            $attribute->setData('option', $result);
            // save attribute
            $attribute->save();
        }
    }

    private function attributeValueExists($arg_attribute, $arg_value) {
        $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $arg_attribute);
        $attribute_table         = $attribute_options_model->setAttribute($attribute);
        $options                 = $attribute_options_model->getAllOptions(false);
        foreach ($options as $option) {
            if ($option['label'] == $arg_value) {
                return $option['value'];
            }
        }
        return false;
    }

    /**
    convert New Arrivals to new-arrivals
    */
    public static function _seoUrl($string) {
        //Lower case everything
        $string = strtolower($string);
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9\/_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert forward slashes, underscore & whitespaces to dash
        $string = preg_replace("/[\/_\s]/", "-", $string);
        return $string;
    }

    /**
    */
    private static function _is_assoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    /**
    setup env via checking base url
    */
    public static function _getEnv() {
        $baseUrl = $configValue = Mage::getStoreConfig('web/unsecure/base_url');
        
        $env = "default";
        if (preg_match("/staging/", $baseUrl)) {
            $env = "staging";
        }
        else if (preg_match("/(www\.|shop\.)/", $baseUrl)) {
            $env = "prod";
        }
        else {
            $env = "dev";
        }
        return $env;
    }

}

?>