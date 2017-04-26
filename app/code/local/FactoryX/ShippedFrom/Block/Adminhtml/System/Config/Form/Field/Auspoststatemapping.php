<?php

/**
 * @deprecated since 1.5.19
 * Class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Auspoststatemapping
 */
class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Auspoststatemapping
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Array that will contain the mapping options
     */
    protected $_optionsArray = array();

    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Auspoststatemapping constructor.
     */
    public function __construct()
    {
        $this->addColumn(
            'state',
            array(
                'label' => Mage::helper('adminhtml')->__('State'),
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

        $this->populateStates();
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
    protected function populateStates()
    {
        $this->_optionsArray['state'] = array();
        $collection = Mage::helper('ustorelocator')->getRegionsToOptionArray(false);
        foreach ($collection as $region) {
            $this->_optionsArray['state'][$region['value']] = $region['label'];
        }

        asort($this->_optionsArray['state']);
    }

    /**
     * @param $column
     * @return string
     */
    protected function getColumnSize($column) //: string
    {
        return ($column['size'] ? ' size="' . $column['size'] . '"' : '');
    }
}
