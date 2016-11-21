<?php

/**
 * Class FactoryX_CreditmemoReasons_Model_Observer
 */
class FactoryX_CreditmemoReasons_Model_Observer {

    protected $_addReasonToCreditmemoComment = false;

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function registerController(Varien_Event_Observer $observer)
    {
        //Mage::helper('creditmemoreasons')->log(sprintf("%s->Mage::register: %s", __METHOD__ , $observer->getControllerAction()->getFullActionName()) );

        // flag the right controller
        $action = $observer->getControllerAction()->getFullActionName();
        switch ($action) {
            case 'adminhtml_sales_order_view':
                Mage::register('adminhtml_sales_order_view', true);
                break;
            case 'adminhtml_sales_creditmemo_index':
                Mage::register('adminhtml_sales_creditmemo_index', true);
                break;
        }

        return $this;
    }

    /**
     * see adminhtml_sales_order_creditmemo_register_before
     *
	 * @param Varien_Event_Observer $observer
     */
    public function addReasonToCreditmemo(Varien_Event_Observer $observer)
    {
        // Get the instantiated credit memo
        $creditmemo = $observer->getEvent()->getCreditmemo();
        // Get the request
        $request = $observer->getEvent()->getRequest();
        // Get the creditmemo parameters
        $creditmemoParams = $request->getParam('creditmemo');
        // Get the reason
        if (is_array($creditmemoParams) && array_key_exists('reason', $creditmemoParams)) {
            $reason = $creditmemoParams['reason'];
            // Set the reason
            $creditmemo->setData("reason", $reason);
            //Mage::helper('creditmemoreasons')->log(sprintf("%s->add reason '%s' to creditmemo %s", __METHOD__ , $reason, $creditmemo->getIncrementId()) );
        }

		$user = Mage::getSingleton('admin/session');
		$userId = $user->getUser()->getUserId();
		$userUsername = $user->getUser()->getUsername();
        $creditmemo->setData('created_by', $userUsername);
        //Mage::helper('creditmemoreasons')->log(sprintf("%s->add user %s:%s to creditmemo %s", __METHOD__ , $userId, $userUsername, $creditmemo->getIncrementId()) );
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addCommentToCreditmemo(Varien_Event_Observer $observer)
    {
        if ($this->_addReasonToCreditmemoComment) {
            // Get the request
            $request = $observer->getEvent()->getControllerAction()->getRequest();
            // Get the creditmemo post data
            $creditmemoParams = $request->getPost('creditmemo');
            // Get the reason
            if (array_key_exists('reason', $creditmemoParams)) {
                $reason = $creditmemoParams['reason'];
                // Update the comment
                $creditmemoParams['comment_text'] .= " " . Mage::helper('creditmemoreasons')->__('Reason: %s', Mage::helper('creditmemoreasons')->getReasonLabel($reason));
                // Update post data
                $observer->getEvent()->getControllerAction()->getRequest()->setPost('creditmemo', $creditmemoParams);
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function blockCreateAfter(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Creditmemo_Grid || $block instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Creditmemos) {

            // Add created_by to the grid
            $block->addColumnAfter(
                'created_by',
                array(
                    'header'    => Mage::helper('creditmemoreasons')->__('Created By'),
                    'index'     => 'created_by',
                    //'filter_index' => 'created_by',
                    'type'      => 'options',
                    'options'   => Mage::getModel('creditmemoreasons/system_config_source_admins')->toOptionArray()
                    //'options'   => Mage::getModel('creditmemoreasons/system_config_source_admins')->toOptionHash('username','username')
                ), 'state'
            );
            // Add reason to the grid
            $block->addColumnAfter(
                'reason',
                array(
                    'header'    => Mage::helper('creditmemoreasons')->__('Reason'),
                    'index'     => 'reason',
                    'type'      => 'options',
                    'options'   => Mage::getModel('creditmemoreasons/system_config_source_reasons')->toOptionHash('identifier','title')
                ), 'created_by'
            );
        }
    }

    /**
     *
     * @param Varien_Event_Observer $observer
     */
    public function beforeCoreCollectionLoad(Varien_Event_Observer $observer)
    {
        // Check if the controller has been flagged
        if (Mage::registry('adminhtml_sales_order_view')) {

            $collection = $observer->getCollection();
            if (!isset($collection)) {
                return;
            }
            // Check if the collection is the right one
            /*
            Mage_Sales_Model_Resource_Order_Creditmemo_Collection
            Mage_Sales_Model_Resource_Order_Creditmemo_Comment_Collection
            Mage_Sales_Model_Resource_Order_Creditmemo_Grid_Collection
            */
            //Mage::helper('creditmemoreasons')->log(sprintf("%s->%s", __METHOD__, get_class($collection)) );
            if ($collection instanceof Mage_Sales_Model_Resource_Order_Creditmemo_Grid_Collection) {
                // Add reason to collection
                $collection->addFieldToSelect('reason');
                // add created_by to collection
                $collection->addFieldToSelect('created_by');
            }
        }
    }

    /**
     *  Generate a new header text
     *
     * @param Varien_Event_Observer $observer
     */
    public function afterToHtml(Varien_Event_Observer $observer)
    {
        // Get the block
        $block = $observer->getBlock();

        // Check if this is the right block
        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Creditmemo_View) {

            // Get the credit memo
            $creditMemo = $block->getCreditmemo();

            // Check if reason
            if ($reason = $creditMemo->getReason()) {
                // Get the transport
                $transport = $observer->getTransport();

                // Get the HTML
                $html = $transport->getHtml();

                // Get the original header text
                $headerText = $block->getHeaderText();

                if ($creditMemo->getEmailSent()) {
                    $emailSent = Mage::helper('sales')->__('the credit memo email was sent');
                }
                else {
                    $emailSent = Mage::helper('sales')->__('the credit memo email is not sent');
                }
                // Generate a new header text
                $newHeaderText = Mage::helper('sales')->__('Credit Memo #%1$s | %3$s | %5$s | %2$s (%4$s)', $creditMemo->getIncrementId(), $block->formatDate($creditMemo->getCreatedAtDate(), 'medium', true), $creditMemo->getStateName(), $emailSent, Mage::helper('creditmemoreasons')->getReasonLabel($reason));

                // Replace in the original HTML
                $newHtml = str_replace($headerText,$newHeaderText,$html);

                // Set the new HTML
                $transport->setHtml($newHtml);
            }
        }
    }

}