<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pp
 * Date: 11-10-21
 * Time: 0:10
 */
 
class FactoryX_StoreLocator_Model_Settings_Zoom
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        for($i =1 ; $i < 26; $i++) {
            $options[] = array('value' => $i, 'label' => $i);
        }
        return $options;
    }
}
