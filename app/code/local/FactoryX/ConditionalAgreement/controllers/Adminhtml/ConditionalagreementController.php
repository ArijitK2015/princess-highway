<?php
/**
 * Class FactoryX_ConditionalAgreement_Adminhtml_ConditionalagreementController
 * Admin controller used for the coupon chooser
 */
class FactoryX_ConditionalAgreement_Adminhtml_ConditionalagreementController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/conditionalagreement');
    }

    /**
     * Action to trigger the salesrule chooser
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $rulesGrid = $this->getLayout()->createBlock('conditionalagreement/adminhtml_salesrule_rule_widget_chooser', '', array(
            'id' => $uniqId,
        ));
        $this->getResponse()->setBody($rulesGrid->toHtml());
    }
}
