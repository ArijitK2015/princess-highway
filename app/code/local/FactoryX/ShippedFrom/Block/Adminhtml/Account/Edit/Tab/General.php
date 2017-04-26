<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Account_Edit_Tab_General
 * This is the edit form
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Account_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the edit account page
     */
    protected function _prepareForm()
    {
        // Model registered as a banner
        Mage::registry('account');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        // General Information fieldset
        $fieldset = $form->addFieldset(
            'auspost_credentials',
            array(
                'legend'    => Mage::helper('shippedfrom')->__('AusPost Credentials'),
                'expanded'  => true
            )
        );

        // We retrieve the data from the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getAccountData()) {
            $data = Mage::getSingleton('adminhtml/session')->getAccountData();
            Mage::getSingleton('adminhtml/session')->setAccountData(null);
        } elseif (Mage::registry('account_data')) {
            $data = Mage::registry('account_data')->getData();
        } else {
            $data = array();
        }

        $fieldset->addField(
            'api_key',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('API Key'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'api_key',
            )
        );

        $fieldset->addField(
            'api_pwd',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('API Password'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'api_pwd',
            )
        );

        $fieldset->addField(
            'account_no',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('Account Number'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'account_no',
            )
        );

        // General Information fieldset
        $fieldset = $form->addFieldset(
            'general_information',
            array(
                'legend'    => Mage::helper('shippedfrom')->__('General Information'),
                'expanded'  => true
            )
        );

        $typeField = $fieldset->addField(
            'mapping_type',
            'select',
            array(
                'label' => Mage::helper('shippedfrom')->__('Mapping Type'),
                'name' => 'mapping_type',
                'options'   =>  array(
                    'store'   =>  Mage::helper('shippedfrom')->__('Store Location'),
                    'state'   =>  Mage::helper('shippedfrom')->__('State'),
                )
            )
        );

        $locations = $this->getStoreLocations();

        $locationField = $fieldset->addField(
            'location_id',
            'select',
            array(
                'label' => Mage::helper('shippedfrom')->__('Store Location'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'location_id',
                'values'    =>  $locations
            )
        );

        $states = $this->getStates();

        $stateField = $fieldset->addField(
            'state',
            'select',
            array(
                'label' => Mage::helper('shippedfrom')->__('State'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'state',
                'values'    =>  $states
            )
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('Name'),
                'name' => 'name',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'valid_from',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('Valid From'),
                'name' => 'valid_from',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'valid_to',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('Valid To'),
                'name' => 'valid_to',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'expired',
            'select',
            array(
                'label' => Mage::helper('shippedfrom')->__('Expired'),
                'name' => 'valid_to',
                'disabled' => true,
                'options'   =>  array(
                    1   =>  Mage::helper('shippedfrom')->__('Yes'),
                    0   =>  Mage::helper('shippedfrom')->__('No'),
                )
            )
        );

        $fieldset->addField(
            'merchant_location_id',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('Merchant Location Id'),
                'name' => 'merchant_location_id',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'credit_blocked',
            'select',
            array(
                'label' => Mage::helper('shippedfrom')->__('Credit Blocked'),
                'name' => 'credit_blocked',
                'disabled' => true,
                'options'   =>  array(
                    1   =>  Mage::helper('shippedfrom')->__('Yes'),
                    0   =>  Mage::helper('shippedfrom')->__('No'),
                )
            )
        );

        // Details fieldset
        $fieldset = $form->addFieldset(
            'details', array(
                'legend'    => Mage::helper('shippedfrom')->__('Details'),
                'expanded'  => true
            )
        );

        $fieldset->addField(
            'details_lodgement_postcode',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('Lodgement Postcode'),
                'name' => 'details_lodgement_postcode',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'details_abn',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('ABN'),
                'name' => 'details_abn',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'details_acn',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('ACN'),
                'name' => 'details_acn',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'details_contact_number',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('Contact Number'),
                'name' => 'details_contact_number',
                'disabled' => true
            )
        );

        $fieldset->addField(
            'details_email_address',
            'text',
            array(
                'label' => Mage::helper('shippedfrom')->__('Email Address'),
                'name' => 'details_email_address',
                'disabled' => true
            )
        );

        // We fill the form based on the retrieved data
        $form->setValues($data);

        // Add dynamic dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($typeField->getHtmlId(), $typeField->getName())
                ->addFieldMap($locationField->getHtmlId(), $locationField->getName())
                ->addFieldMap($stateField->getHtmlId(), $stateField->getName())
                ->addFieldDependence($locationField->getName(), $typeField->getName(), 'store')
                ->addFieldDependence($stateField->getName(), $typeField->getName(), 'state')
        );

        return parent::_prepareForm();
    }

    /**
     * @return array
     */
    protected function getStoreLocations()
    {
        /** @var FactoryX_ShippedFrom_Model_Resource_Account_Collection $accountStoreLocationsCollection */
        $accountStoreLocationsCollection = Mage::getResourceModel('shippedfrom/account_collection')
            ->addFieldToSelect('location_id');
        $alreadyAssignedStoreLocations = $accountStoreLocationsCollection->getColumnValues('location_id');

        $currentAccountData = Mage::registry('account_data');
        if ($currentAccountData->getAccountId()
            && ($key = array_search($currentAccountData->getLocationId(), $alreadyAssignedStoreLocations)) !== false) {
            unset($alreadyAssignedStoreLocations[$key]);
        }

        /** @var FactoryX_StoreLocator_Model_Mysql4_Location_Collection $collection */
        $collection = Mage::getResourceModel('ustorelocator/location_collection');

        if ($alreadyAssignedStoreLocations) {
            $collection->addFieldToFilter('location_id', array('nin'   =>  $alreadyAssignedStoreLocations));
        }

        $locations = array();
        foreach ($collection as $location) {
            $locations[$location->getLocationId()] = sprintf(
                "%s - %s",
                $location->getData('store_code'),
                $location->getData('title')
            );
        }

        return $locations;
    }

    /**
     * @return array
     */
    protected function getStates()
    {
        /** @var FactoryX_ShippedFrom_Model_Resource_Account_Collection $accountStoreLocationsCollection */
        $accountStateCollection = Mage::getResourceModel('shippedfrom/account_collection')
            ->addFieldToSelect('state');
        $alreadyAssignedStates = $accountStateCollection->getColumnValues('state');

        $currentAccountData = Mage::registry('account_data');
        if ($currentAccountData->getAccountId()
            && ($key = array_search($currentAccountData->getState(), $alreadyAssignedStates)) !== false) {
            unset($alreadyAssignedStates[$key]);
        }

        $states = Mage::helper('ustorelocator')->getRegions();

        foreach ($alreadyAssignedStates as $key => $val) {
            if (empty($val)) {
                $alreadyAssignedStates[$key] = "unassigned";
            }
        }

        $alreadyAssignedStates = array_flip($alreadyAssignedStates);

        return array_diff_key($states, $alreadyAssignedStates);
    }

}