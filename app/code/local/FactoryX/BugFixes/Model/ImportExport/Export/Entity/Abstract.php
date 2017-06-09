<?php
/**
 *	Fix date format
 *	Note. should change the ui date picker format to d/m/yyyy
*/
abstract class FactoryX_BugFixes_Model_ImportExport_Export_Entity_Abstract extends Mage_ImportExport_Model_Export_Entity_Abstract
{
    /**
     * Apply filter to collection and add not skipped attributes to select.
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _prepareEntityCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        if (!isset($this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP])
            || !is_array($this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP])) {
            $exportFilter = array();
        } else {
            $exportFilter = $this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP];
        }
        $exportAttrCodes = $this->_getExportAttrCodes();

        foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
            $attrCode = $attribute->getAttributeCode();

            // filter applying
            if (isset($exportFilter[$attrCode])) {
                $attrFilterType = Mage_ImportExport_Model_Export::getAttributeFilterType($attribute);

                if (Mage_ImportExport_Model_Export::FILTER_TYPE_SELECT == $attrFilterType) {
                    if (is_scalar($exportFilter[$attrCode]) && trim($exportFilter[$attrCode])) {
                        $collection->addAttributeToFilter($attrCode, array('eq' => $exportFilter[$attrCode]));
                    }
                } elseif (Mage_ImportExport_Model_Export::FILTER_TYPE_INPUT == $attrFilterType) {
                    if (is_scalar($exportFilter[$attrCode]) && trim($exportFilter[$attrCode])) {
                        $collection->addAttributeToFilter($attrCode, array('like' => "%{$exportFilter[$attrCode]}%"));
                    }
                /*
                strtotime is failing due to separator
                http://php.net/manual/en/function.strtotime.php
                if the separator is a slash (/), then the American m/d/y is assumed
                */
                } elseif (Mage_ImportExport_Model_Export::FILTER_TYPE_DATE == $attrFilterType) {
                    if (is_array($exportFilter[$attrCode]) && count($exportFilter[$attrCode]) == 2) {
                        $from = array_shift($exportFilter[$attrCode]);
                        if (strlen($from) && Mage::getStoreConfig('general/country/default') == 'AU') {
                        	$parts = explode("/", $from);
	                        $from = sprintf("%d-%d-%d",$parts[2] + 2000,$parts[1],$parts[0]);	                        
	                    }
                        $to = array_shift($exportFilter[$attrCode]);
                        if (strlen($to) && Mage::getStoreConfig('general/country/default') == 'AU') {
                        	$parts = explode("/", $to);
	                        $to = sprintf("%d-%d-%d",$parts[2] + 2000,$parts[1],$parts[0]);
	                    }
                        if (is_scalar($from) && strtotime($from)) {
                            $collection->addAttributeToFilter($attrCode, array('from' => $from, 'date' => true));
                        }
                        if (is_scalar($to) && strtotime($to)) {
                            $collection->addAttributeToFilter($attrCode, array('to' => $to, 'date' => true));
                        }
                    }
                } elseif (Mage_ImportExport_Model_Export::FILTER_TYPE_NUMBER == $attrFilterType) {
                    if (is_array($exportFilter[$attrCode]) && count($exportFilter[$attrCode]) == 2) {
                        $from = array_shift($exportFilter[$attrCode]);
                        $to   = array_shift($exportFilter[$attrCode]);

                        if (is_numeric($from)) {
                            $collection->addAttributeToFilter($attrCode, array('from' => $from));
                        }
                        if (is_numeric($to)) {
                            $collection->addAttributeToFilter($attrCode, array('to' => $to));
                        }
                    }
                }
            }
            if (in_array($attrCode, $exportAttrCodes)) {
                $collection->addAttributeToSelect($attrCode);
            }
        }
        return $collection;
    }
}
