<?php

/**
 * @deprecated since 1.5.0
 * Class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Linkedattributes
 */
class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Auspostmapping
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Array that will contain the mapping options
     */
    protected $_optionsArray = array();

    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Auspostmapping constructor.
     */
    public function __construct()
    {
        $this->addColumn(
            'storecode',
            array(
                'label' => Mage::helper('adminhtml')->__('Store Code'),
                'size'  => 25,
            )
        );

        $this->addColumn(
            'apikey',
            array(
                'label' => Mage::helper('adminhtml')->__('API Key'),
                'size'  => 25
            )
        );

        $this->addColumn(
            'apipwd',
            array(
                'label' => Mage::helper('adminhtml')->__('API Password'),
                'size'  => 25
            )
        );

        $this->addColumn(
            'accountno',
            array(
                'label' => Mage::helper('adminhtml')->__('Account Number'),
                'size'  => 25
            )
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Mapping');

        parent::__construct();
        $this->setTemplate('factoryx/shippedfrom/system/config/form/field/array_dropdown.phtml');

        $this->populateStoreCodes();
    }

    /**
     * @param string $columnName
     * @return string
     * @throws Exception
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            Mage::throwException('Wrong column name specified.');
        }
        
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
        
        if (isset($this->_optionsArray[$columnName])) {
            $rendered = '<select name="' . $inputName . '">';
            $rendered .= '<option value=""></option>';
            foreach ($this->_optionsArray[$columnName] as $att => $name) {
                $rendered .= '<option value="'.$att.'">' . $name . '</option>';
            }

            $rendered .= '</select>';
        } else {
            $html = '<input type="text" class="input-text" name="';
            $html .= $inputName;
            $html .= '" value="#{';
            $html .= $columnName;
            $html .= '}" ';
            $html .= $this->getColumnSize($column);
            $html .= '/>';
            return  $html;
        }

        return $rendered;
    }

    /**
     *
     */
    protected function populateStoreCodes()
    {
        $this->_optionsArray['storecode'] = array();
        /** @var FactoryX_StoreLocator_Model_Mysql4_Location_Collection $collection */
        $collection = Mage::getModel('ustorelocator/location')->getCollection();
        /** @var FactoryX_StoreLocator_Model_Location $location */
        foreach ($collection as $location) {
            $this->_optionsArray['storecode'][$location->getData('location_id')] = sprintf(
                "%s - %s",
                $location->getData('store_code'),
                $location->getData('title')
            );
        }

        asort($this->_optionsArray['storecode']);
    }

    /**
     * @param $column
     * @return string
     */
    protected function getColumnSize($column): string
    {
        return ($column['size'] ? ' size="' . $column['size'] . '"' : '');
    }
}
