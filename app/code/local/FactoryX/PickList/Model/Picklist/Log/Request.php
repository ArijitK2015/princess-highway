<?php
/**

fields


 */

class FactoryX_PickList_Model_Picklist_Log_Request extends Mage_Core_Model_Abstract {
    
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('picklist/picklist_log_request');
    }

    /**
     * clean up log table
     *
     * @param int|null $lifetime Lifetime of entries in days
     * @return $this
     */
    public function clean($lifetime = null)
    {
        $this->getResource()->clean($lifetime);
        return $this;
    }
}