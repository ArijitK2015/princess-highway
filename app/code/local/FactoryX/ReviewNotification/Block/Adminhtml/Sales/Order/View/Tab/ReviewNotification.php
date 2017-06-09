<?php
/**
 * Order information tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FactoryX_ReviewNotification_Block_Adminhtml_Sales_Order_View_Tab_ReviewNotification
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	public $OrderToPrepare = null;

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('factoryx/reviewnotification/sales/order/view/tab/reviewnotification.phtml');
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getSendEmailUrl()
    {
        return $this->getUrl('adminhtml/reviewnotification/sendOrderEmail', array('order_id' => $this->getOrder()->getId()));
    }

    public function getDirectUrl($orderId)
    {
        return Mage::helper('reviewnotification')->getDirectUrl($orderId);
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('reviewnotification')->__('Review Notification');
    }

    public function getTabTitle()
    {
        return Mage::helper('reviewnotification')->__('Review Notification');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}