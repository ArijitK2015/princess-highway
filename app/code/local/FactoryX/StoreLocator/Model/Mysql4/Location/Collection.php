<?php
/**
 * FactoryX_StoreLocator extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @copyright
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   FactoryX
 * @package    FactoryX_StoreLocator
 * @author
 */
class FactoryX_StoreLocator_Model_Mysql4_Location_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ustorelocator/location');
    }

    /**
     * @param $center_lat
     * @param $center_lng
     * @param $radius
     * @param string $units
     * @return $this
     */
    public function addAreaFilter($center_lat, $center_lng, $radius, $units='mi')
    {
        $conn = $this->getConnection();
        $dist = sprintf(
            "(%s*acos(cos(radians(%s))*cos(radians(`latitude`))*cos(radians(`longitude`)-radians(%s))+sin(radians(%s))*sin(radians(`latitude`))))",
            $units=='mi' ? 3959 : 6371,
            (float)$center_lat,
            (float)$center_lng,
            (float)$center_lat
        );
        $this->_select = $conn->select()->from(array('main_table' => $this->getResource()->getMainTable()), array('*', 'distance'=>$dist))
            ->where('`latitude` is not null and `latitude`<>0 and `longitude` is not null and `longitude`<>0');

        if ($radius) {
            $this->_select->where($dist.'<=?', (float)$radius);
        }
        
        return $this;
    }

    /**
     * @param null $storeId
     * @return $this
     */
    public function addStoreFilter($storeId = null)
    {
        $store = Mage::app()->getStore($storeId);
        if ($store->getId()) {
            $this->getSelect()->where("FIND_IN_SET(?, `stores`) OR `stores` IS NULL OR stores=''", $store->getId());
        }
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function addProductTypeFilter($type)
    {
        if ($type) {
            $this->_select->where('find_in_set(?, product_types)', $type);
        }
        return $this;
    }

    /**
     * @param $center_lat
     * @param $center_lng
     * @param $country
     * @param string $units
     * @return $this
     */
    public function addCountryFilter($center_lat, $center_lng, $country, $units = 'mi')
    {
        $this->addAreaFilter($center_lat, $center_lng, null, $units)
            ->getSelect()
            ->where('country=?', $country);
        return $this;
    }

    /**
     * @param $attribute
     * @param string $dir
     * @return $this
     */
    public function addAttributeToSort($attribute, $dir='asc')
    { 
        if (!is_string($attribute)) { 
            return $this; 
        } 
        $this->setOrder($attribute, $dir); 
        return $this; 
    }
}

