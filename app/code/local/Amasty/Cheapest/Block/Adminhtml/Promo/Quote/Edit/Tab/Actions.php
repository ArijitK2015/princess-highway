<?php
/**
 * @copyright   Copyright (c) 2010 Amasty
 */

class Amasty_Cheapest_Block_Adminhtml_Promo_Quote_Edit_Tab_Actions extends Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Actions
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_quote_rule');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('salesrule')->__('Update prices using the following information')));

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Apply'),
            'name'      => 'simple_action',
            'options'    => array(
                'by_percent' => Mage::helper('salesrule')->__('Percent of product price discount'),
                'by_fixed' => Mage::helper('salesrule')->__('Fixed amount discount'),
                'cart_fixed' => Mage::helper('salesrule')->__('Fixed amount discount for whole cart'),
                'buy_x_get_y' => Mage::helper('salesrule')->__('Buy X get Y free (discount amount is Y)'),
                'cheapest_for_free' => Mage::helper('amcheapest')->__('Get cheapest product for free'), // Amasty action type "Get Cheapest for free"
            ),
        ));
        $fieldset->addField('discount_amount', 'text', array(
            'name' => 'discount_amount',
            'required' => true,
            'class' => 'validate-not-negative-number',
            'label' => Mage::helper('salesrule')->__('Discount amount'),
        ));
        $model->setDiscountAmount($model->getDiscountAmount()*1);

        $fieldset->addField('discount_qty', 'text', array(
            'name' => 'discount_qty',
            'label' => Mage::helper('salesrule')->__('Maximum Qty Discount is Applied to'),
        ));
        $model->setDiscountQty($model->getDiscountQty()*1);

        $fieldset->addField('discount_step', 'text', array(
            'name' => 'discount_step',
            'label' => Mage::helper('salesrule')->__('Discount Qty Step (Buy X)'),
        ));

        $fieldset->addField('apply_to_shipping', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Apply to Shipping Amount'),
            'title'     => Mage::helper('salesrule')->__('Apply to Shipping Amount'),
            'name'      => 'apply_to_shipping',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
        ));

        $fieldset->addField('simple_free_shipping', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Free shipping'),
            'title'     => Mage::helper('salesrule')->__('Free shipping'),
            'name'      => 'simple_free_shipping',
            'options'    => array(
                0 => Mage::helper('salesrule')->__('No'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ITEM => Mage::helper('salesrule')->__('For matching items only'),
                Mage_SalesRule_Model_Rule::FREE_SHIPPING_ADDRESS => Mage::helper('salesrule')->__('For shipment with matching items'),
            ),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('salesrule')->__('Stop further rules processing'),
            'title'     => Mage::helper('salesrule')->__('Stop further rules processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => Mage::helper('salesrule')->__('Yes'),
                '0' => Mage::helper('salesrule')->__('No'),
            ),
        ));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_quote/newActionHtml/form/rule_actions_fieldset'));

        $fieldset = $form->addFieldset('actions_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Apply the rule only to cart items matching the following conditions (leave blank for all items)')
        ))->setRenderer($renderer);

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => Mage::helper('salesrule')->__('Apply to'),
            'title' => Mage::helper('salesrule')->__('Apply to'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/actions'));

        Mage::dispatchEvent('adminhtml_block_salesrule_actions_prepareform', array('form' => $form));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }
        //$form->setUseContainer(true);

        $this->setForm($form);

        //return parent::_prepareForm();
        return $this;
    }

}
