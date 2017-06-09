<?php
/**
 * reference
 * http://www.demacmedia.com/ecommerce/enabling-linked-field-configuration-magento-module/
 */
class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Linkedattributes
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var array
     */
    protected $_optionsArray;

    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Linkedattributes constructor.
     */
    public function __construct()
    {
        $this->_optionsArray = array();

        $this->addColumn(
            'storecode',
            array(
                'label' => Mage::helper('adminhtml')->__('Store Code'),
                'size'  => 10,
            )
        );

        $this->addColumn(
            'emailaddress',
            array(
                'label' => Mage::helper('adminhtml')->__('Email Address'),
                'size'  => 70
            )
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Mapping');

        parent::__construct();
        $this->setTemplate('factoryx/shippedfrom/system/config/form/field/array_dropdown.phtml');

        /**
        populate drop downs
        */
        $this->_optionsArray['storecode'] = array();
        $collection = Mage::getModel('ustorelocator/location')->getCollection();        
        foreach ($collection as $location) {
            $this->_optionsArray['storecode'][$location->store_code] = sprintf(
                "%s - %s",
                $location->store_code,
                $location->title
            );
        }

        asort($this->_optionsArray['storecode']);
    }

    /**
     * @param string $columnName
     * @return string
     * @throws Exception
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            Mage::throwException(Mage::helper('shippedfrom')->__('Wrong column name specified.'));
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
            $html .= ($column['size'] ? 'size="' . $column['size'] . '"' : '');
            $html .= '/>';
            return  $html;
        }

        return $rendered;
    }
}
