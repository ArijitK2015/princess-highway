<?php
/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Auspostebaymapping
 */
class FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Auspostebaymapping
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Array that will contain the mapping options
     */
    protected $_optionsArray = array();

    /**
     * FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Field_Auspostebaymapping constructor.
     */
    public function __construct()
    {        
        $this->addColumn(
            'ebay_method',
            array(
                'label' => Mage::helper('adminhtml')->__('Ebay Method'),
                'size'  => 25,
            )
        );

        $this->addColumn(
            'auspost_service',
            array(
                'label' => Mage::helper('adminhtml')->__('Australia Post'),
                'size'  => 25
            )
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Mapping');

        parent::__construct();
        $this->setTemplate('factoryx/shippedfrom/system/config/form/field/array_dropdown.phtml');

        $this->populateEbayMethods();
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
    protected function populateEbayMethods()
    {
        $ebayMethods = array(
            'default' => "Default"
        );
        $this->_optionsArray['ebay_method'] = $ebayMethods;
        $query = "select shipping_method, shipping_description from sales_flat_order where shipping_method like 'm2eproshipping%' group by shipping_method, shipping_description";
        $readConn = Mage::getSingleton('core/resource')->getConnection('core_read');
	    $ebayMethods = $readConn->fetchAll($query);
	    foreach($ebayMethods as $method) {
            $this->_optionsArray['ebay_method'][$method['shipping_method']] = 
                sprintf("%s - %s", $method['shipping_method'], $method['shipping_description']);
        }

        if (is_array($this->_optionsArray['ebay_method'])) {
            asort($this->_optionsArray['ebay_method']);
        }
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
