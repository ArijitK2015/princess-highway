<?php
/**
 */

class FactoryX_PickList_Model_System_Config_Source_ProductTypes extends Varien_Object {

    const PRODUCT_TYPE_INSTORE      = "in-store";
    const PRODUCT_TYPE_PREORDER     = "pre-order";
    const PRODUCT_TYPE_CONSOLIDATED = "consol";

    private static $productTypes = array(
        "ALL"       => "All Product",
        "in-store"  => "In-Store Product",
        "pre-order" => "Pre-Order Product",
        "consol"    => "Consolidated Product"
    );

    /**
     * @return array
     */
    public static function getProductTypes() {
        return self::$productTypes;
    }

    /**
     * @return array
     */
    public function toOptionArray() {
        $options = array();
        foreach(self::$productTypes as $value => $label) {
            $options[] = array(
                "value" => $value,
                "label" => $label
            );
        }
        return $options;
    }
}
