<?php       
class FactoryX_CampaignMonitor_Block_Linkedattributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $magentoOptions;

    public function __construct()
    {
        $this->addColumn('magento', array(
            'label' => Mage::helper('adminhtml')->__('Magento customer attribute'),
            'size'  => 28,
        ));
        $this->addColumn('campaignmonitor', array(
            'label' => Mage::helper('adminhtml')->__('Campaign Monitor custom field personalization tag'),
            'size'  => 28
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add linked attribute');
        
        parent::__construct();
        $this->setTemplate('factoryx/campaignmonitor/system/config/form/field/array_dropdown.phtml');
        
        // customer options
        $magentoAttributes = Mage::getModel('customer/customer')->getAttributes();
        $this->magentoOptions = array();
        foreach(array_keys($magentoAttributes) as $att)
        {
            if($att != 'entity_type_id'
                    and $att != 'entity_id'
                    and $att != 'attribute_set_id'
                    and $att != 'password_hash'
                    and $att != 'increment_id'
                    and $att != 'updated_at'
                    and $att != 'created_at'
                    and $att != 'email'
                    and $att != 'default_billing'
                    and $att != 'default_shipping')
            {
                // give nicer names to the attributes we're translating
                // from IDs to values
                if($att == 'store_id')
                    $name = 'Store';
                else if($att == 'group_id')
                    $name = 'Customer Group';
                else if($att == 'website_id')
                    $name = 'Website';
                else $name = $att;
                
                $this->magentoOptions[$att] = $name;
            }
        }
        asort($this->magentoOptions);
        // address options
        $this->magentoOptions['FACTORYX-billing-firstname'] = 'Billing Address: First name';
        $this->magentoOptions['FACTORYX-billing-lastname'] = 'Billing Address: Last name';
        $this->magentoOptions['FACTORYX-billing-company'] = 'Billing Address: Company';
        $this->magentoOptions['FACTORYX-billing-telephone'] = 'Billing Address: Phone';
        $this->magentoOptions['FACTORYX-billing-fax'] = 'Billing Address: Fax';
        $this->magentoOptions['FACTORYX-billing-street'] = 'Billing Address: Street';
        $this->magentoOptions['FACTORYX-billing-city'] = 'Billing Address: City';
        $this->magentoOptions['FACTORYX-billing-region'] = 'Billing Address: State/Province';
        $this->magentoOptions['FACTORYX-billing-postcode'] = 'Billing Address: Zip/Postal Code';
        $this->magentoOptions['FACTORYX-billing-country_id'] = 'Billing Address: Country';
        
        $this->magentoOptions['FACTORYX-shipping-firstname'] = 'Shipping Address: First name';
        $this->magentoOptions['FACTORYX-shipping-lastname'] = 'Shipping Address: Last name';
        $this->magentoOptions['FACTORYX-shipping-company'] = 'Shipping Address: Company';
        $this->magentoOptions['FACTORYX-shipping-telephone'] = 'Shipping Address: Phone';
        $this->magentoOptions['FACTORYX-shipping-fax'] = 'Shipping Address: Fax';
        $this->magentoOptions['FACTORYX-shipping-street'] = 'Shipping Address: Street';
        $this->magentoOptions['FACTORYX-shipping-city'] = 'Shipping Address: City';
        $this->magentoOptions['FACTORYX-shipping-region'] = 'Shipping Address: State/Province';
        $this->magentoOptions['FACTORYX-shipping-postcode'] = 'Shipping Address: Zip/Postal Code';
        $this->magentoOptions['FACTORYX-shipping-country_id'] = 'Shipping Address: Country';
    }

    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        if($columnName == 'magento')
        {
            $rendered = '<select name="'.$inputName.'">';
            foreach($this->magentoOptions as $att => $name)
            {
                $rendered .= '<option value="'.$att.'">'.$name.'</option>';
            }
            $rendered .= '</select>';
        }
        else
        {
            return '<input type="text" name="' . $inputName . '" value="#{' . $columnName . '}" ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . '/>';
        }
        
        return $rendered;
    }
}
