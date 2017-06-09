<?php

/**
 * Class FactoryX_ShippedFrom_Model_System_Config_Source_Stores
 */
class FactoryX_ShippedFrom_Model_System_Config_Source_Stores
    extends Varien_Object
{

    /**
     * @var
     */
    protected $_options;

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

        $this->_options[] = array(
            'value' => $code,
            'label' => sprintf("%s - %s", $region, $code)
        );
    }

    /**
     * toOptionArray
     *
     * @return array $options
     */
    public function toOptionArray()
    {
        if (empty($this->_options)) {
            $stores = Mage::getModel('ustorelocator/location')->getCollection()
                ->setOrder('region', 'ASC')
                ->setOrder('store_code', 'ASC');
    
            // Call iterator walk method with collection query string and callback method as parameters
            // Has to be used to handle massive collection instead of foreach
            Mage::getSingleton('core/resource_iterator')->walk(
                $stores->getSelect(),
                array(
                    array($this, 'generateOptions')
                )
            );

            usort($this->_options, array('FactoryX_ShippedFrom_Model_System_Config_Source_Stores','sortByStore'));
        }

        return $this->_options;
    }


    /**
     * @param $a
     * @param $b
     * @return int
     */
    protected static function sortByStore($a, $b)
    {
        return strcmp($a['label'], $b['label']);
    }    
    
}
