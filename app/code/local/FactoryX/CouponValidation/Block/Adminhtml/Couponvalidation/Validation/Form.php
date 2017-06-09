<?php

/**
 * Class FactoryX_CouponValidation_Block_Adminhtml_Couponvalidation_Validation_Form
 * This is the edit form
 */
class FactoryX_CouponValidation_Block_Adminhtml_Couponvalidation_Validation_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the edit validation page
     */
    protected function _prepareForm()
    {

        // Create a form
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/validate'),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        // General Information fieldset
        $fieldset = $form->addFieldset('validation_form', array(
                'legend'    => Mage::helper('couponvalidation')->__('General Information'),
                //'class'     => 'fieldset-wide',
                'expanded'  => true // open
            )
        );

        // Field for the coupon code
        $fieldset->addField('code', 'text', array(
            'label' => Mage::helper('couponvalidation')->__('Code'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'code'
        ));

        $websites = Mage::app()->getWebsites();
        $webOptions = [];
        foreach ($websites as $website) {
            $webOptions[] = ['value' => $website->getId(), 'label' => $website->getName()];
        }

        // Field for the website id
        $fieldset->addField('website', 'select', array(
            'label' => Mage::helper('couponvalidation')->__('Website'),
            'name' => 'website',
            'values' => $webOptions
        ));

        $customerGroups = Mage::getResourceModel('customer/group_collection');
        $custOptions = [];
        foreach ($customerGroups as $customerGroup) {
            $custOptions[] = ['value' => $customerGroup->getCustomerGroupId(), 'label' => $customerGroup->getCustomerGroupCode()];
        }

        // Field for the customer group
        $fieldset->addField('customer_group', 'select', array(
            'label' => Mage::helper('couponvalidation')->__('Customer Group'),
            'name' => 'customer_group',
            'values' => $custOptions
        ));

        // Field for the customer
        $fieldset->addField('customer', 'text', array(
            'label' => Mage::helper('couponvalidation')->__('Customer Id'),
            'name' => 'customer'
        ));

        if (Mage::app()->getRequest()->getParam('customer') || Mage::app()->getRequest()->getParam('subscriber')) {
            // General Information fieldset
            $fieldset2 = $form->addFieldset('related_data', array(
                    'legend'    => Mage::helper('couponvalidation')->__('Related Data'),
                    //'class'     => 'fieldset-wide',
                    'expanded'  => true // open
                )
            );

            if ($customer = Mage::app()->getRequest()->getParam('customer')) {

                $loadedCustomer = Mage::getResourceModel('customer/customer_collection')
                    ->addAttributeToSelect('entity_id')
                    ->addAttributeToFilter('email',$customer)
                    ->setPageSize(1)
                    ->getFirstItem();


                $fieldset2->addField('related_customer', 'link', array(
                    'label' => Mage::helper('couponvalidation')->__('Related Customer'),
                    'href'  => $this->getUrl('*/customer/edit/',['id' => $loadedCustomer->getEntityId()]),
                    'name' => 'related_customer'
                ));

                $form->addValues(['related_customer' => $customer]);
            }

            if ($subscriber = Mage::app()->getRequest()->getParam('subscriber')) {
                $fieldset2->addField('related_subscriber', 'link', array(
                    'label' => Mage::helper('couponvalidation')->__('Related Subscriber'),
                    'href'  => $this->getUrl('*/newsletter_subscriber'),
                    'name' => 'related_subscriber'
                ));

                $form->addValues(['related_subscriber' => $subscriber]);
            }
        }

        return parent::_prepareForm();
    }

}