<?php
require_once 'Mage/Adminhtml/controllers/Sales/Order/ShipmentController.php';

/**
 *
 * override saveAction
 * add emailStoreAction
 */
class FactoryX_ShippedFrom_Sales_Order_ShipmentController extends Mage_Adminhtml_Sales_Order_ShipmentController {

    /**
    */
    public function emailStoreAction() {

        try {
            $shipment = $this->_initShipment();
            if ($shipment) {
                //$shipment->sendEmail(true)->setEmailSent(true)->save();
                $email = Mage::app()->getRequest()->getParam('email');
                $email = Mage::helper('shippedfrom')->sendPackingSlipToStore($shipment, $email);
                if (is_array($email)) {
                    foreach($email as $e) {
                        $this->_getSession()->addSuccess($this->__(sprintf("The packing slip has been sent to '%s'.", $e)));
                    }
                }
                else {
                    $this->_getSession()->addSuccess($this->__(sprintf("The packing slip has been sent to '%s'.", $email)));
                }
            }
        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot send shipment information: ' . $e->getMessage()));
        }
        /*
        $this->_redirect('*\/*\/view', array(
            'shipment_id' => $this->getRequest()->getParam('shipment_id')
        ));
        */
        
        $redirect = Mage::helper('adminhtml')->getUrl("adminhtml/sales_shipment/view",
            array(
                '_store' => Mage::getModel('core/store')->load(0),
                'shipment_id' => $this->getRequest()->getParam('shipment_id')
            )
        );
        //Mage::helper('shippedfrom')->log(sprintf("redirect=%s", $redirect));
        Mage::app()->getResponse()->setRedirect($redirect);
        return $this;
    }

    /**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return null
     */
    public function saveAction() {

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
                $shipment->setShippedBy($currentUser->getName());
            }

            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $responseAjax = new Varien_Object();
            $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];

            if ($isNeedCreateLabel && $this->_createShippingLabel($shipment)) {
                $responseAjax->setOk(true);
            }

            $this->_saveShipment($shipment);

            $shipment->sendEmail(!empty($data['send_email']), $comment);

            $shipmentCreatedMessage = $this->__('The shipment has been created.');
            $labelCreatedMessage    = $this->__('The shipping label has been created.');

            $this->_getSession()->addSuccess($isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelCreatedMessage
                : $shipmentCreatedMessage);
            Mage::getSingleton('adminhtml/session')->getCommentText(true);

            if (isset($data['sourced_from']))
            {
                foreach ($shipment->getOrder()->getAllItems() as $orderedItem)
                {
                    if (isset($data['sourced_from'][$orderedItem->getId()]))
                    {
                        $sourcedFrom = $data['sourced_from'][$orderedItem->getId()];
                        $orderedItem->setSourcedFrom($sourcedFrom);
                        $orderedItem->save();
                    }
                }
            }

            if (isset($data['sourced_by']))
            {
                foreach ($shipment->getOrder()->getAllItems() as $orderedItem)
                {
                    if (isset($data['sourced_by'][$orderedItem->getId()]))
                    {
                        $sourcedBy = $data['sourced_by'][$orderedItem->getId()];
                        $orderedItem->setSourcedBy($sourcedBy);
                        $orderedItem->save();
                    }
                }
            }

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

    public function updateStoreAction()
    {
        $data = $this->getRequest()->getPost('shipment');

        try {
            $shippedfrom = $data['shipped_from'];
            $shipment = $this->_initShipment();
            if ($shipment) {
                // Store titles
                $locationModel = Mage::getModel('ustorelocator/location');
                $orgStore = $locationModel->load($shipment->getShippedFrom())->getTitle();
                $newStore = $locationModel->load($shippedfrom)->getTitle();
                // Add comment
                $shipment->addComment(
                    Mage::helper('shippedfrom')->__('Shipped from store changed from %s to %s',$orgStore,$newStore),
                    false,
                    false
                );
                // Update shippedfrom
                $shipment->setShippedFrom($shippedfrom)->save();
                // Update the layout
                $this->loadLayout();
                $response = $this->getLayout()->getBlock('shippedfrom_view')->toHtml();
            } else {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot initialize shipment for updating shippedfrom.'),
                );
            }
        } catch (Mage_Core_Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $e->getMessage(),
            );
        } catch (Exception $e) {
            $response = array(
                'error'     => true,
                'message'   => $this->__('Cannot update shippedfrom.'),
            );
        }
        if (is_array($response)) {
            $response = Mage::helper('core')->jsonEncode($response);
        }
        $this->getResponse()->setBody($response);
    }

}
