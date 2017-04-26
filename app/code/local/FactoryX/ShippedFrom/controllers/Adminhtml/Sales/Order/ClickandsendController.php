<?php

/**
 * Class FactoryX_ShippedFrom_Adminhtml_Sales_Order_ClickandsendController
 */
class FactoryX_ShippedFrom_Adminhtml_Sales_Order_ClickandsendController
    extends Mage_Adminhtml_Controller_Action
{

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Export Orders to CSV
     *
     * This action exports orders to a CSV file and downloads the file.
     * The orders to be exported depend on the HTTP POST param "order_ids".
     */
    public function exportAction()
    {
        $orders = $this->getRequest()->getPost('order_ids', array());

        try {
            // Build the CSV and retrieve its path
            $filePath = Mage::getModel('shippedfrom/shipping_carrier_clickandsend_export_csv')->exportOrders($orders);

            // Download the file
            $this->_prepareDownloadResponse(basename($filePath), file_get_contents($filePath));
        }
        catch (FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('adminhtml/sales_order/index');
        }
        catch (Exception $e) {
            Mage::getSingleton('core/session')->addError('An error occurred. ' . $e->getMessage());
            $this->_redirect('adminhtml/sales_order/index');
        }
    }
}
