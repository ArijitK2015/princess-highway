<?php
/**
 * Class FactoryX_CampaignMonitor_AuthController
 * Frontend controller used for the OAuth authentication
 */
class FactoryX_CampaignMonitor_AuthController extends Mage_Core_Controller_Front_Action
{
    /**
     * Frontend redirect URI for the CM OAuth authentication
     * @TODO check isAdmin login
     */
    public function indexAction()
    {
        // Get the query data
        $code = $this->getRequest()->getQuery('code');
        $state = $this->getRequest()->getQuery('state');
        // Generate the admin URL
        $adminUrl = str_replace("//","//admin.",Mage::helper("adminhtml")->getUrl("adminhtml/campaignmonitor/callback", array( 'code' => $code, 'state' => $state )));
        // Redirect
        $this->_redirectUrl($adminUrl);
        return;

    }

}
