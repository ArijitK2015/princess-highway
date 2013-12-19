<?php
/**
 * Order information tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MDN_EasyReview_Block_Adminhtml_Sales_Order_View_Tab_EasyReview
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	public $OrderToPrepare = null;

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('EasyReview/Sales/Order/View/Tab/EasyReview.phtml');
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
        return $this->getUrl('EasyReview/Admin/SendOrderEmail', array('order_id' => $this->getOrder()->getId()));
    }

    public function getDirectUrl()
    {
        $order = $this->getOrder();
        $hashCode = md5($order->getincrement_id());
        return Mage::getUrl('EasyReview/Front/PostReviews', array('_store' => $order->getStoreId(), 'security_key' => $hashCode));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('EasyReview')->__('Easy review');
    }

    public function getTabTitle()
    {
        return Mage::helper('EasyReview')->__('Easy review');
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