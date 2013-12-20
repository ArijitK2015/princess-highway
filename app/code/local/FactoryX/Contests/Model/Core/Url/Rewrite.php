<?php

class FactoryX_Contests_Model_Core_Url_Rewrite extends Mage_Core_Model_Url_Rewrite
{
	/**
     * Load rewrite information for request
     *
     * @param   string $path
	 * @param	int $storeId
     * @return  Mage_Core_Model_Url_Rewrite
     */
    public function loadByRequestPathAndStoreId($path, $storeId)
    {
        $this->setId(null);
		$this->setStoreId($storeId);
        $this->_getResource()->loadByRequestPath($this, $path);
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }
}