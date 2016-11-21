<?php
/**
 * Class FactoryX_CampaignMonitor_Adminhtml_CmwidgetController
 * Admin controller used for the coupon chooser
 */
class FactoryX_CampaignMonitor_Adminhtml_CmwidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/newsletter');
    }

    /**
     * Action to trigger the salesrule chooser
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $rulesGrid = $this->getLayout()->createBlock('campaignmonitor/adminhtml_salesrule_rule_widget_chooser', '', array(
            'id' => $uniqId,
        ));
        $this->getResponse()->setBody($rulesGrid->toHtml());
    }
}
