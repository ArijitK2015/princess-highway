<?php

/**
 * Class FactoryX_PromoRestriction_Model_Admin_Observer
 */
class FactoryX_PromoRestriction_Model_Admin_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function registerController(Varien_Event_Observer $observer) {
        $action = $observer->getControllerAction()->getFullActionName();

        switch ($action) {
            case 'adminhtml_promo_quote_index':
                Mage::register('adminhtml_promo_quote_index', true);
                break;
        }
        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function blockCreateAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Promo_Quote_Grid $block */
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Promo_Quote_Grid) {
            //$store = Mage::app()->getStore((int) Mage::app()->getRequest()->getParam('store', 0));
            $block->addColumnAfter(
                "promo_restriction",
                array(
                    'header'    => Mage::helper('promorestriction')->__('Restriction'),
                    'index'     => "promo_restriction",
                    'align'     => 'left',
                    'width'     => '100px'
                ),
                'is_active'
            );

        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function prepareQuoteForm(Varien_Event_Observer $observer)
    {
        // Get form
        /** @var Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main $form */
        $form = $observer->getEvent()->getForm();
        // Get fieldset
        $fieldset = $form->getElement('base_fieldset');
        // Add the field
        $fieldset->addField('restriction_type', 'select',
            [
                'name'      => 'restriction_type',
                'label'     => Mage::helper('promorestriction')->__('Restriction Type'),
                'title'     => Mage::helper('promorestriction')->__('Restriction Type'),
                'required'  => false,
                'values' => [
                    [
                        'value' => FactoryX_PromoRestriction_Model_Restriction::RESTRICT_NONE,
                        'label' => Mage::helper('promorestriction')->__('No Restriction'),
                    ],
                    [
                        'value' => FactoryX_PromoRestriction_Model_Restriction::RESTRICT_IP,
                        'label' => Mage::helper('promorestriction')->__('IP Address'),
                    ],
                    [
                        'value' => FactoryX_PromoRestriction_Model_Restriction::RESTRICT_EMAIL,
                        'label' => Mage::helper('promorestriction')->__('Email Address'),
                    ],
                    [
                        'value' => FactoryX_PromoRestriction_Model_Restriction::RESTRICT_STORE,
                        'label' => Mage::helper('promorestriction')->__('Store Location (IP Address)'),
                    ]
                ]
            ]
        );

        $fieldset->addField('promo_restriction', 'text',
            [
                'name'      => 'promo_restriction',
                'label'     => Mage::helper('promorestriction')->__('Restriction'),
                'title'     => Mage::helper('promorestriction')->__('Restriction'),
                'required'  => false
            ]
        );

        /** @var array $options */
        $options = Mage::helper('promorestriction')->getStores();

        $fieldset->addField('promo_restriction_store', 'select',
            [
                'name'      => 'promo_restriction_store',
                'label'     => Mage::helper('promorestriction')->__('Restricted Store'),
                'title'     => Mage::helper('promorestriction')->__('Restricted Store'),
                'required'  => false,
                'values' => $options
            ]
        );

        // Get the rule id
        $id = Mage::app()->getRequest()->getParam('id');
        // Load possible restriction
        /** @var FactoryX_PromoRestriction_Model_Restriction $customerRestriction */
        $customerRestriction = Mage::getModel('promorestriction/restriction')->load($id, 'salesrule_id');

        if (FactoryX_PromoRestriction_Model_Restriction::RESTRICT_NONE != $customerRestriction->getType()) {
            // Get the restriction type
            $restrictionType = $customerRestriction->getType();

            // We add the value to the form so it is transparent
            $form->addValues(['restriction_type' => $restrictionType]);

            // Get the restriction
            $restriction = $customerRestriction->getRestrictedField();

            // Add the values to the form based on the type of restriction
            switch ($restrictionType) {
                case FactoryX_PromoRestriction_Model_Restriction::RESTRICT_IP:
                case FactoryX_PromoRestriction_Model_Restriction::RESTRICT_EMAIL:
                    $form->addValues(['promo_restriction' => $restriction]);
                    break;
                case FactoryX_PromoRestriction_Model_Restriction::RESTRICT_STORE:
                    $form->addValues(['promo_restriction_store' =>  $restriction]);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function blockHtmlBefore(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main $block */
        $block = $observer->getEvent()->getBlock();

        if (!isset($block)) {
            return $this;
        }

        if ($block instanceof Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main)
        {
            // Dynamic field dependencies
            $block->getChild('form_after')
                ->addFieldMap('rule_restriction_type', 'rule_restriction_type')
                ->addFieldMap('rule_promo_restriction', 'rule_promo_restriction')
                ->addFieldMap('rule_promo_restriction_store', 'rule_promo_restriction_store')
                ->addFieldDependence('rule_promo_restriction_store', 'rule_restriction_type', FactoryX_PromoRestriction_Model_Restriction::RESTRICT_STORE)
                ->addFieldDependence('rule_promo_restriction', 'rule_restriction_type', [
                    FactoryX_PromoRestriction_Model_Restriction::RESTRICT_IP,
                    FactoryX_PromoRestriction_Model_Restriction::RESTRICT_EMAIL
                ]);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function promoQuoteSave(Varien_Event_Observer $observer)
    {
        // Get the action
        $action = $observer->getEvent()->getControllerAction();
        // Get the data
        $data = $action->getRequest()->getParams();

        // Add customer restriction if needed
        if (
            array_key_exists('rule_id', $data)
            &&
            $data['rule_id'] !== false
        ) {
            /** @var FactoryX_PromoRestriction_Model_Restriction $customerRestriction */
            $customerRestriction = Mage::getModel('promorestriction/restriction')->load($data['rule_id'], 'salesrule_id');

            // Case 1: existing restriction => update
            if (
                $customerRestriction->getRestrictedField()
                &&
                FactoryX_PromoRestriction_Model_Restriction::RESTRICT_NONE != $data['restriction_type']
            ) {
                $customerRestriction->delete();
                $this->_setupRestriction($data);
            }
            // Case 2: existing restriction => delete
            elseif ($customerRestriction->getRestrictedField()) {
                //Mage::helper('promorestriction')->log(sprintf("delete promo_restriction: %s", $data['rule_id']) );
                $customerRestriction->delete();
            }
            // Case 3: new restriction => insert
            elseif (FactoryX_PromoRestriction_Model_Restriction::RESTRICT_NONE != $data['restriction_type']) {
                //Mage::helper('promorestriction')->log(sprintf("insert promo_restriction: %s", $data['rule_id']) );
                $this->_setupRestriction($data);
            }
        }
    }

    /**
     * Inert/Update a restriction
     * @param $data
     * @return FactoryX_PromoRestriction_Model_Restriction
     */
    protected function _setupRestriction($data)
    {
        $restrictionType = $data['restriction_type'];
        if (FactoryX_PromoRestriction_Model_Restriction::RESTRICT_STORE == $restrictionType) {
            $restriction = $data['promo_restriction_store'];
        } else {
            $restriction = $data['promo_restriction'];
        }
        //Mage::helper('promorestriction')->log(sprintf("update promo_restriction: %s", $data['rule_id']) );
        $customerRestriction = Mage::getModel('promorestriction/restriction')->setData([
                'salesrule_id'      =>  $data['rule_id'],
                'restricted_field'  =>  $restriction,
                'type'              =>  $data['restriction_type']
            ]
        )->save();

        return $customerRestriction;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function salesRuleDelete(Varien_Event_Observer $observer)
    {
        // Get the sales rule
        /** @var Mage_SalesRule_Model_Rule $rule */
        $rule = $observer->getEvent()->getRule();

        // Get the related customer restriction
        /** @var FactoryX_PromoRestriction_Model_Restriction $customerRestriction */
        $customerRestriction = Mage::getModel('promorestriction/restriction')->load($rule->getRuleId(),'salesrule_id');

        // Check if it exists
        if ($customerRestriction->getRestrictedField()) {
            $customerRestriction->delete();
        }
    }

}