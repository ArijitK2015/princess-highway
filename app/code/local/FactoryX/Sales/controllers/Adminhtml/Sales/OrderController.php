<?php
/**
 */

/**
 * Adminhtml sales orders controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

include_once("Mage/Adminhtml/controllers/Sales/OrderController.php");

/**
 * Class FactoryX_Sales_Sales_OrderController
 */
class FactoryX_Sales_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController {

    /**
     * Add order comment action
     */
    public function addCommentAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
                    ->setIsVisibleOnFront($visible)
                    ->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $order->save();

                /* ============= CUSTOM CODE START ============= */
				// Set notify false if processing_stage2
                Mage::helper('fx_sales')->log(sprintf("%s->check order status :%s", __METHOD__, $data['status']));
				if (preg_match("/processing_stage2/", $data['status'])) {
					Mage::helper('fx_sales')->log("do NOT notify!");
					$notify = false;
				}
                /* ============= CUSTOM CODE END */
                
                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                Mage::helper('fx_sales')->log(sprintf("Error in %s :%s", __FUNCTION__, $e->getMessage()));
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
            	Mage::helper('fx_sales')->log(sprintf("Error in %s :%s", __FUNCTION__, $e->getMessage()));
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }
}
