<?php
/**
 * Order model
 *
 * Supported events:
 *  sales_order_load_after
 *  sales_order_save_before
 *  sales_order_save_after
 *  sales_order_delete_before
 *  sales_order_delete_after
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FactoryX_Sales_Model_Order
    extends Mage_Sales_Model_Order {

    /**
     * Processing Partially Shipped
     */
    const STATUS_PROCESSING_PARTIALLY_SHIPPED = 'processing_part_shipped';

    /**
     * Processing Partially Shipped No Tracking
     */
    const STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING = 'processing_part_shipped_nt';

    /**
     * Processhing Shipped No Tracking
     */
    const STATUS_PROCESSING_SHIPPED_NO_TRACKING = 'processing_shipped_nt';

    /**
     * Processing Stage 2
     */
    const STATUS_PROCESSING_STAGE2 = 'processing_stage2';
    
    /**
     * Whether specified state can be set from outside
     * @param $state
     * @return bool
     */
    public function isStateProtected($state)
    {
        if (empty($state)) {
            return false;
        }
        return self::STATE_CLOSED == $state;
        //return self::STATE_COMPLETE == $state || self::STATE_CLOSED == $state;
    }

    /**
     * Add a comment to order
     * Different or default status may be specified
     *
     * @param $comment
     * @param bool $status
     * @return Mage_Sales_Model_Order_Status_History
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        Mage::dispatchEvent(
            'add_status_history_comment_before',
            array(
                'order' => $this,
                'status' => $status,
                'comment' => $comment
            )
        );

        //Mage::helper('fx_sales')->log(sprintf("%s->comment=%s[%s]", __METHOD__, $comment, $status));
        //$data = $this->getRequest()->getPost('history');
        //Mage::helper('fx_sales')->log(sprintf("%s->comment:%s[%s]", __METHOD__, $data['comment'], $data['status']));

        // Skip if no comments
        if ($this->getStatus() == $status && strlen(trim($comment)) == 0) {
            //Mage::helper('fx_sales')->log("no comment");
            $history = Mage::getModel('sales/order_status_history');
            return $history;
        }

        $history = parent::addStatusHistoryComment($comment,$status);

        Mage::dispatchEvent(
            'add_status_history_comment_after',
            array(
                'history' => $history
            )
        );
        
        return $history;
    }

    /**
     * Get the created by
     * @TODO: is that needed ? it's a magic getter we should not need that
     * @return float|mixed
     */
    public function getCreatedBy()
    {
        return $this->getData('created_by');
    }

    /**
     * Get the shipped from location
     * @return mixed
     */
    public function getShipFrom()
    {
        $shipments = parent::getShipmentsCollection();
        
        foreach($shipments as $shipment){
            
            $collection = Mage::getResourceModel('ustorelocator/location')
                ->addFieldToFilter('location_id', $shipment->getShippedFrom())
                ->addFieldToSelect('title')
                ->setPageSize(1)
                ->setCurPage(1);
            
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getTitle();
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Rewrite in order to credit memo zero total orders
     * @return bool
     */
    public function canCreditmemo()
    {
        $canCreditmemo = parent::canCreditmemo();
        $canCreditmemoZeroTotalOrders = Mage::helper('fx_sales')->canRefundZeroTotalOrders();

        if (!$canCreditmemo
            && $canCreditmemoZeroTotalOrders
            && abs($this->getStore()->roundPrice($this->getTotalPaid()) - $this->getTotalRefunded()) < .0001
            && $this->_anymoreItemsToRefund()
        ) {
            return true;
        } else {
            return $canCreditmemo;
        }
    }

    /**
     * Check if there are more items that can be refunded
     * @return bool
     */
    protected function _anymoreItemsToRefund()
    {
        $orderItems = $this->getItemsCollection();

        $qtysRefunded = $orderItems->getColumnValues('qty_refunded');
        $qtysOrdered = $orderItems->getColumnValues('qty_ordered');
        $qtydInvoiced = $orderItems->getColumnValues('qty_invoiced');

        $condition = (array_sum($qtydInvoiced) == array_sum($qtysOrdered))
            && (array_sum($qtysRefunded) < array_sum($qtysOrdered));

        return $condition ? true : false;
    }
}
