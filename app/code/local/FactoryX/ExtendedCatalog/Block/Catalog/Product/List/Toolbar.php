<?php
/*
 *	Modify the default behaviour to not using the sort by attribute stored in the session which
 *	will not work with current caching
 */

/**
 * Class FactoryX_ExtendedCatalog_Block_Catalog_Product_List_Toolbar
 */
class FactoryX_ExtendedCatalog_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{

    public function getPagerUrl($params=array())
    {
        // Code from MageWorx_SeoSuite_Block_Catalog_Product_List_Toolbar
        if ((string)Mage::getConfig()->getModuleConfig('MageWorx_SeoSuite')->active == 'true'){
            if(Mage::getStoreConfig('mageworx_seo/seosuite/disable_layered_rewrites')) return parent::getPagerUrl($params);
            $urlParams = array();
            $urlParams['_current']  = true;
            $urlParams['_escape']   = true;
            $urlParams['_use_rewrite']   = true;
            $urlParams['_query']    = $params;
            return Mage::helper('seosuite')->getLayerFilterUrl($urlParams);
        }
        else return parent::getPagerUrl($params);
    }

    /**
     * Init Toolbar
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_orderField  = Mage::getStoreConfig(
            Mage_Catalog_Model_Config::XML_PATH_LIST_DEFAULT_SORT_BY
        );

        $this->_availableOrder = $this->_getConfig()->getAttributeUsedForSortByArray();

        switch (Mage::getStoreConfig('catalog/frontend/list_mode')) {
            case 'grid':
                $this->_availableMode = array('grid' => $this->__('Grid'));
                break;

            case 'gridx5':
                $this->_availableMode = array('gridx5' => $this->__('Grid x5'));
                break;

            case 'list':
                $this->_availableMode = array('list' => $this->__('List'));
                break;

            case 'grid-list':
                $this->_availableMode = array('grid' => $this->__('Grid'), 'list' =>  $this->__('List'));
                break;

            case 'gridx5-list':
                $this->_availableMode = array('gridx5' => $this->__('Grid x5'), 'list' =>  $this->__('List'));
                break;

            case 'list-grid':
                $this->_availableMode = array('list' => $this->__('List'), 'grid' => $this->__('Grid'));
                break;

            case 'list-gridx5':
                $this->_availableMode = array('list' => $this->__('List'), 'gridx5' => $this->__('Grid x5'));
                break;

            case 'gridx5-grid':
                $this->_availableMode = array('gridx5' => $this->__('Grid x5'), 'grid' =>  $this->__('Grid'));
                break;

            case 'grid-gridx5':
                $this->_availableMode = array('grid' => $this->__('Grid'), 'gridx5' =>  $this->__('Grid x5'));
                break;

            case 'grid-list-gridx5':
                $this->_availableMode = array('grid' => $this->__('Grid'), 'list' => $this->__('List'), 'gridx5' =>  $this->__('Grid x5'));
                break;

            case 'list-grid-gridx5':
                $this->_availableMode = array('list' => $this->__('List'), 'grid' => $this->__('Grid'), 'gridx5' =>  $this->__('Grid x5'));
                break;

            case 'gridx5-list-grid':
                $this->_availableMode = array('gridx5' => $this->__('Grid x5'), 'list' => $this->__('List'), 'grid' =>  $this->__('Grid'));
                break;
        }
        $this->setTemplate('catalog/product/list/toolbar.phtml');
    }

    /**
     * Get specified products limit display per page
     *
     * @return string
     */
    public function getLimit()
    {
        $limit = $this->_getData('_current_limit');
        if ($limit) {
            return $limit;
        }

        $limits = $this->getAvailableLimit();

        if (Mage::getStoreConfig('catalog/frontend/viewallbydefault')){
            $defaultLimit = 'all';
        }
        else {
            $defaultLimit = $this->getDefaultPerPageValue();
        }
        
        if (!$defaultLimit || !isset($limits[$defaultLimit])) {
            $keys = array_keys($limits);
            $defaultLimit = $keys[0];
        }

        $limit = $this->getRequest()->getParam($this->getLimitVarName());
        if ($limit && isset($limits[$limit])) {
            if ($limit == $defaultLimit) {
                Mage::getSingleton('catalog/session')->unsLimitPage();
            } else {
                $this->_memorizeParam('limit_page', $limit);
            }
        } else {
            $limit = Mage::getSingleton('catalog/session')->getLimitPage();
        }
        if (!$limit || !isset($limits[$limit])) {
            $limit = $defaultLimit;
        }

        $this->setData('_current_limit', $limit);
        return $limit;
    }
    
    /**
     * Retrieve default per page values
     *
     * @return string (comma separated)
     */
    public function getDefaultPerPageValue()
    {
        if ($this->getCurrentMode() == 'list') {
            if ($default = $this->getDefaultListPerPage()) {
                return $default;
            }
            return Mage::getStoreConfig('catalog/frontend/list_per_page');
        }
        elseif ($this->getCurrentMode() == 'grid') {
            if ($default = $this->getDefaultGridPerPage()) {
                return $default;
            }
            return Mage::getStoreConfig('catalog/frontend/grid_per_page');
        }
        elseif ($this->getCurrentMode() == 'gridx5') {
            if ($default = $this->getDefaultGridx5PerPage()) {
                return $default;
            }
            return Mage::getStoreConfig('catalog/frontend/grid_per_page');
        }
        return 0;
    }

    /**
     * Retrieve available limits for current view mode
     *
     * @return array
     */
    public function getAvailableLimit()
    {
        $currentMode = $this->getCurrentMode();
        if (in_array($currentMode, array('list', 'grid', 'gridx5'))) {
            return $this->_getAvailableLimit($currentMode);
        } else {
            return $this->_defaultAvailableLimit;
        }
    }

}
