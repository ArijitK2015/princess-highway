<?php

/**
 * Class FactoryX_CartImage_Model_System_Config_Source_SuperAttributes
 */
class FactoryX_CartImage_Model_System_Config_Source_SuperAttributes  {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {

        $query = <<<SQL
select eav.attribute_id, eav.attribute_code, eav.frontend_label from eav_attribute as eav 
right join catalog_product_super_attribute as super on eav.attribute_id = super.attribute_id
where entity_type_id = 4
group by eav.attribute_code;
SQL;

        $rCon = Mage::getSingleton('core/resource')->getConnection('core_read');
        $superAtts = $rCon->fetchAll($query);

        foreach ($superAtts as $att) {
            //Mage::helper('cartimage')->log(sprintf("%s->att: %s", __METHOD__, print_r($att, true)));
            $options[] = array(
                //'value' => $att['attribute_id'],
                'value' => $att['attribute_code'],
                'label' => sprintf("%s [%s]", $att['frontend_label'], $att['attribute_code'])
            );
        }
        return $options;
    }

}