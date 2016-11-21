<?php
/**
 */

class FactoryX_ShippedFrom_Model_System_Config_Source_Stores extends Varien_Object {

    protected $options; // = array();

    /**
     * @param $args
     */
    public function generateOptions($args)
    {
        $code = strtolower($args['row']['store_code']);
        $region = $args['row']['region'];
        if (empty($region)) {
            $region = "?";
        }
        $this->options[] = array(
            'value' => $code,
            'label' => sprintf("%s - %s", $region, $code)
        );
    }

    /**
     * toOptionArray
     *
     * @return array $options
     */
    public function toOptionArray() {
        if (!count($this->options)) {
            //->addFieldToFilter('region', array('in' => 'vic'))
            $stores = Mage::getModel('ustorelocator/location')->getCollection()
                ->setOrder('region', 'ASC')
                ->setOrder('store_code', 'ASC');
            //Mage::log(sprintf("%s->sql=%s", __METHOD__, $stores->getSelect()) );
    
            // Call iterator walk method with collection query string and callback method as parameters
            // Has to be used to handle massive collection instead of foreach
            Mage::getSingleton('core/resource_iterator')->walk($stores->getSelect(), array(array($this, 'generateOptions')));

            usort($this->options, array('FactoryX_ShippedFrom_Model_System_Config_Source_Stores','sortByStore'));
            //Mage::log(sprintf("%s->options=%s", __METHOD__, print_r($this->options, true)) );
        }
        return $this->options;
    }


    /**
     * @param $a
     * @param $b
     * @return int
     */
    private static function sortByStore($a, $b) {
        //if ($a['value'] == $b['value']) return 0;
        //return ($a['value'] < $b['value']) ? 1 : -1;
        return strcmp($a['label'], $b['label']);
    }    
    
}
