<?php
/**
 * Class FactoryX_CustomGrids_Helper_Data
 */
class FactoryX_CustomGrids_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $logFileName = 'factoryx_customgrids.log';

    /**
     * Get attribute options
     * @param $field
     * @return array
     */
    public function _getOptions($attribute)
    {
        $attrOptions = $attribute->getSource()->getAllOptions(false);

        $optionsArr = array();
        foreach ($attrOptions as $option)
        {
            $optionsArr[$option['value']] = $option['label'];
        }

        return $optionsArr;
    }

    /**
     * Get the admin roles
     * @return array
     */
    public function getAdminRoles()
    {
        $array = array();
        $collection = Mage::getResourceModel('admin/roles_collection');
        foreach($collection as $role)
        {
            $array[] = array('value' => $role->getRoleId(), 'label' => $role->getRoleName());
        }
        return $array;
    }

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }

    /**
     * Get the countries as option hash
     * @return array
     */
    public function countryToOptionHash()
    {
        // get allowed countries
        $countries = Mage::getModel('directory/country')->getResourceCollection()->loadByStore();
        $res = array();
        foreach ($countries as $item)
        {
            $res[$item->getData('country_id')] = Mage::getModel('directory/country')->loadByCode($item->getData('iso2_code'))->getName();
        }
        asort($res, SORT_REGULAR);
        return $res;
    }
    
    /**
     * isExportCsv()
     *
     * determine if this is a exportCsv action
     * @return bool $isExportCsv
     */
    public function isExportCsv() {
        $isExportCsv = false;
        if (preg_match("/exportCsv/", $this->getCurrentUrl())) {
            $isExportCsv = true;
        }
        return $isExportCsv;
    }

    /**
     * getCurrentUrl()
     *
     * filter out cms routes
     * @return string $currentUrl
     */    
    public function getCurrentUrl() {
        $currentUrl = null;
        if (!in_array(Mage::app()->getFrontController()->getAction()->getFullActionName(), array('cms_index_noRoute', 'cms_index_defaultNoRoute'))) {
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        }
        return $currentUrl;
    }
}