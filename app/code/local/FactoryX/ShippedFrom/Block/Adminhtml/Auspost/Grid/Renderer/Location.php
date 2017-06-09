<?php

/** 
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Location
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Grid_Renderer_Location
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    protected static $_states = array("act", "nsw", "nt", "qld", "sa", "tas", "vic", "wa");
    
    /**
     * Renderer for the action column
     * @param Varien_Object $row
     * @return string
     */    
    public function render(Varien_Object $row)
    {
        $locationUrl = Mage::helper('adminhtml')->getUrl(
            'adminhtml/location/edit',
            array('id' => $row->getData($this->getColumn()->getIndex()))
        );
        $shippedFrom = $this->_getShippedFrom($row);
        // check if a state OR location
        if (in_array($shippedFrom, self::$_states)) {
            $retVal = strtoupper($shippedFrom);
        } else {
            $retVal = sprintf("<a href='%s'>%s</a>", $locationUrl, $shippedFrom);
        }

        return $retVal;
    }

    /**
     *  Render for export
     * @param Varien_Object $row
     * @return string
     */
    public function renderExport(Varien_Object $row)
    {
        return $this->_getShippedFrom($row);
    }

    /**
     * @param $row
     * @return
     */
    protected function _getShippedFrom($row)
    {
        $shippedFromId = $row->getData($this->getColumn()->getIndex());   
        $shippedFrom = $shippedFromId;
        // get data the bad way (use join in _prepareCollection)
        if (Mage::helper('core')->isModuleEnabled('FactoryX_StoreLocator') && is_numeric($shippedFromId)) {
            $shippedFrom = Mage::getModel('ustorelocator/location')->load($shippedFrom)->getTitle();
        }

        if (empty($shippedFrom)) {
            $shippedFrom = $row->getData('state');
        }

        return $shippedFrom;
    }
}