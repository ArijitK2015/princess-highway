<?php

class FactoryX_ExtendedCatalog_Block_Catalog_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    /**
     * @var Magento_Db_Adapter_Pdo_Mysql
     */
    protected $_read;

    /**
     * @var string
     */
    protected $_tbl_eav_attribute_option;

    public function getJsonConfig()
    {
        $config = parent::getJsonConfig();
        $decode = false;
        if (Mage::helper('extendedcatalog')->replaceDefaultOptionText())
        {
            // Decode OG config
            $config = Mage::helper('core')->jsonDecode($config);
            // Use a variable to avoid decode again
            $decode = true;
            // Remove the default text
            unset($config['chooseText']);
        }
        if (version_compare(Mage::getVersion(),"1.9.1.0",">="))
        {
            if (!$decode)
            {
                // Decode OG config
                $config = Mage::helper('core')->jsonDecode($config);
                // Use a variable to avoid decode again
                $decode = true;
            }
            $config['attributes'] = $this->_sortOptions($config['attributes']);
        }
        if ($decode)
        {
            // Decode OG config
            $config = Mage::helper('core')->jsonEncode($config);
        }
        return $config;
    }

    /**
     * Sort the options based off their position.
     *
     * @param array $options
     * @return array
     */
    protected function _sortOptions($attributes)
    {
        foreach($attributes as $key => $options)
        {
            if (count($options['options']))
            {
                if (!$this->_read || !$this->_tbl_eav_attribute_option)
                {
                    $resource = Mage::getSingleton('core/resource');

                    $this->_read = $resource->getConnection('core_read');
                    $this->_tbl_eav_attribute_option = $resource->getTableName('eav_attribute_option');
                }

                // Gather the option_id for all our current options
                $option_ids = array();
                foreach ($options['options'] as $option) {
                    $option_ids[] = $option['id'];

                    $var_name  = 'option_id_'.$option['id'];
                    $$var_name = $option;
                }

                $sql    = "SELECT `option_id` FROM `{$this->_tbl_eav_attribute_option}` WHERE `option_id` IN('".implode('\',\'', $option_ids)."') ORDER BY `sort_order`";
                $result = $this->_read->fetchCol($sql);

                $options['options'] = array();
                foreach ($result as $option_id) {
                    $var_name  = 'option_id_'.$option_id;
                    $options['options'][] = $$var_name;
                }

                $attributes[$key]['options'] = $options['options'];
            }
        }

        return $attributes;
    }
}