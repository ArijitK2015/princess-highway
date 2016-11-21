<?php

/**
 * Class FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Linkedattributes
 */
class FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Linkedattributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $optionsArray;

    /**
     *
     */
    public function __construct()
    {
        $this->optionsArray = array();

        $this->addColumn('formfields', array(
            'label' => Mage::helper('adminhtml')->__('Subscription Form Field Name'),
            'size'  => 20,
        ));
        $this->addColumn('magento', array(
            'label' => Mage::helper('adminhtml')->__('Magento customer attribute'),
            'size'  => 20,
        ));
        $this->addColumn('subscriber', array(
            'label' => Mage::helper('adminhtml')->__('Magento subscriber attribute'),
            'size'  => 20,
        ));
        $this->addColumn('campaignmonitor', array(
            'label' => Mage::helper('adminhtml')->__('Campaign Monitor custom fields'),
            'size'  => 20
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add linked attribute');

        parent::__construct();
        $this->setTemplate('factoryx/campaignmonitor/system/config/form/field/array_dropdown.phtml');

        // customer options
        $magentoAttributes = Mage::getModel('customer/customer')->getAttributes();
        $this->optionsArray['magento'] = array();
        foreach(array_keys($magentoAttributes) as $att)
        {
            if($att != 'entity_type_id'
                and $att != 'entity_id'
                and $att != 'attribute_set_id'
                and $att != 'password_hash'
                and $att != 'increment_id'
                and $att != 'updated_at'
                and $att != 'created_at'
                // and $att != 'email'
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

                $this->optionsArray['magento'][$att] = $name;
            }
        }
        asort($this->optionsArray['magento']);
        // address options
        $this->optionsArray['magento']['FACTORYX-billing-firstname'] = 'Billing Address: First name';
        $this->optionsArray['magento']['FACTORYX-billing-lastname'] = 'Billing Address: Last name';
        $this->optionsArray['magento']['FACTORYX-billing-company'] = 'Billing Address: Company';
        $this->optionsArray['magento']['FACTORYX-billing-telephone'] = 'Billing Address: Phone';
        $this->optionsArray['magento']['FACTORYX-billing-fax'] = 'Billing Address: Fax';
        $this->optionsArray['magento']['FACTORYX-billing-street'] = 'Billing Address: Street';
        $this->optionsArray['magento']['FACTORYX-billing-city'] = 'Billing Address: City';
        $this->optionsArray['magento']['FACTORYX-billing-region'] = 'Billing Address: State/Province';
        $this->optionsArray['magento']['FACTORYX-billing-postcode'] = 'Billing Address: Zip/Postal Code';
        $this->optionsArray['magento']['FACTORYX-billing-country_id'] = 'Billing Address: Country';

        $this->optionsArray['magento']['FACTORYX-shipping-firstname'] = 'Shipping Address: First name';
        $this->optionsArray['magento']['FACTORYX-shipping-lastname'] = 'Shipping Address: Last name';
        $this->optionsArray['magento']['FACTORYX-shipping-company'] = 'Shipping Address: Company';
        $this->optionsArray['magento']['FACTORYX-shipping-telephone'] = 'Shipping Address: Phone';
        $this->optionsArray['magento']['FACTORYX-shipping-fax'] = 'Shipping Address: Fax';
        $this->optionsArray['magento']['FACTORYX-shipping-street'] = 'Shipping Address: Street';
        $this->optionsArray['magento']['FACTORYX-shipping-city'] = 'Shipping Address: City';
        $this->optionsArray['magento']['FACTORYX-shipping-region'] = 'Shipping Address: State/Province';
        $this->optionsArray['magento']['FACTORYX-shipping-postcode'] = 'Shipping Address: Zip/Postal Code';
        $this->optionsArray['magento']['FACTORYX-shipping-country_id'] = 'Shipping Address: Country';

        // subscriber options
        $subscriberAttributes = Mage::getModel('newsletter/subscriber')->getAttributes();
        foreach($subscriberAttributes as $att)
        {
            $this->optionsArray['subscriber'][$att] = $att;
        }
        asort($this->optionsArray['subscriber']);

        // campaign monitor options
        // Non custom fields
        $this->optionsArray['campaignmonitor']['firstname'] = "First name";
        $this->optionsArray['campaignmonitor']['lastname'] = "Last name";
        $this->optionsArray['campaignmonitor']['fullname'] = "Full name";
        $this->optionsArray['campaignmonitor']['email'] = "Email Address";
        $this->optionsArray['campaignmonitor']['Date'] = "Subscription Date";

        // Custom fields
        $customFields = Mage::helper('campaignmonitor/cm')->getCustomFields();
        if ($customFields && is_array($customFields)) {
            foreach($customFields as $customField) {
                $this->optionsArray['campaignmonitor'][substr($customField->Key,1,-1)] = $customField->FieldName;
            }
            asort($this->optionsArray['campaignmonitor']);
        }
    }

    /**
     * @param string $columnName
     * @return string
     * @throws Exception
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
        if (isset($this->optionsArray[$columnName])){
            $rendered = '<select name="'.$inputName.'">';
            $rendered .= '<option value=""></option>';
            foreach($this->optionsArray[$columnName] as $att => $name)
            {
                $rendered .= '<option value="'.$att.'">'.$name.'</option>';
            }
            $rendered .= '</select>';
        }
        else
        {
            return '<input type="text" class="input-text" name="' . $inputName . '" value="#{' . $columnName . '}" ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . '/>';
        }

        return $rendered;
    }
}
