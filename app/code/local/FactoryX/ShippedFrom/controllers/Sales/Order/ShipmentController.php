<?php
require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';

class FactoryX_ShippedFrom_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController {
    
    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return null
     */
    public function saveAction()
    {
    		//$post = $this->getRequest()->getPost();
    		//Mage::helper('shippedfrom')->log(sprintf("%s->post=%s", __METHOD__, var_export($post, true)));
    	
        $data = $this->getRequest()->getPost('shipment');
        //Mage::helper('shippedfrom')->log(sprintf("%s->data=%s", __METHOD__, var_export($data, true)));
        
        if (!empty($data['comment_text'])) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
        }

        try {
            $shipment = $this->_initShipment();
            if (!$shipment) {
                $this->_forward('noRoute');
                return;
            }

            $shipment->register();
            $comment = '';
            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
            }

            if (!empty($data['send_email'])) {
                $shipment->setEmailSent(true);
            }
            
            if (!empty($data['shipped_from'])) {
            	//Mage::helper('shippedfrom')->log(sprintf("%s->shipped_from=%s", __METHOD__, $data['shipped_from']));
            	$shipment->setShippedFrom($data['shipped_from']);
            }

            if (!empty($data['shipped_by'])) {
            	//Mage::helper('shippedfrom')->log(sprintf("%s->shipped_by=%s", __METHOD__, $data['shipped_by']));
            	$shipment->setShippedBy($data['shipped_by']);
            }
            // default to current user
            else {
            	$currentUser = Mage::getSingleton('admin/session')->getUser();
            	//Mage::helper('shippedfrom')->log(sprintf("%s->default-shipped_by=%s", __METHOD__, $user->getName()));
            	$shipment->setShippedBy($user->getName());
            }
            
            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $responseAjax = new Varien_Object();
            $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];
            if ($isNeedCreateLabel) {
                if ($this->_createShippingLabel($shipment)) {
                    $this->_getSession()
                        ->addSuccess($this->__('The shipment has been created. The shipping label has been created.'));
                    $responseAjax->setOk(true);
                }
            } else {
                $this->_getSession()
                    ->addSuccess($this->__('The shipment has been created.'));
            }
            $this->_saveShipment($shipment);
            $shipment->sendEmail(!empty($data['send_email']), $comment);
            Mage::getSingleton('adminhtml/session')->getCommentText(true);
            
            //$shipment1 = Mage::getModel('sales/order_shipment')->load($shipment->getId());
           // Mage::helper('shippedfrom')->log("shipment with shipped from attribute:".$shipment1->getShippedFrom());
            
        } catch (Mage_Core_Exception $e) {
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }
        } catch (Exception $e) {
            Mage::logException($e);
            if ($isNeedCreateLabel) {
                $responseAjax->setError(true);
                $responseAjax->setMessage(Mage::helper('sales')->__('An error occurred while creating shipping label.'));
            } else {
                $this->_getSession()->addError($this->__('Cannot save shipment.'));
                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
            }

        }
        if ($isNeedCreateLabel) {
            $this->getResponse()->setBody($responseAjax->toJson());
        } else {
            $this->_redirect('*/sales_order/view', array('order_id' => $shipment->getOrderId()));
        }
    }

}
