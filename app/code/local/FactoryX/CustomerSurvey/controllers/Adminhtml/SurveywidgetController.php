<?php
class FactoryX_Customersurvey_Adminhtml_SurveywidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/customersurvey');
    }

    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $rulesGrid = $this->getLayout()->createBlock('customersurvey/adminhtml_salesrule_rule_widget_chooser', '', array(
            'id' => $uniqId,
        ));
        $this->getResponse()->setBody($rulesGrid->toHtml());
    }
}
