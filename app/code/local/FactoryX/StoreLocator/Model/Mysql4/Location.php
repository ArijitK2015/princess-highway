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
class FactoryX_StoreLocator_Model_Mysql4_Location extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('ustorelocator/location', 'location_id');
    }

    public function loadByIpAddress($model, $ip) {
        $adapter = $this->_getReadAdapter();
        $bind = array(
            'ip_address' => '%' . $ip . '%'
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), 'location_id')
            ->where('ip_address like :ip_address');
        // ->where($this->getMainTable() . "." . "ip_address" . " like ?", '%' . $ip . '%');

        Mage::helper('ustorelocator')->log(sprintf("%s->sql: %s", __METHOD__, $select));
        $locationId = $this->_getReadAdapter()->fetchOne($select, $bind);
        if ($locationId) {
            $obj = $this->load($model, $locationId);
        }
        else {
            $model->setData(array());
        }
        return $this;
    }

}
