<?php

/**
 * Class FactoryX_Gua_Block_Gua
 */
class FactoryX_Gua_Block_Gua extends Mage_Core_Block_Template
{
    public $_order;

    /**
     * @return mixed
     */
    public function getAccountId()
    {
        return Mage::getStoreConfig('gua/general/account_id');
    }

    /**
     * @return string
     */
    public function isAnonymizeIp()
    {
        return Mage::getStoreConfigFlag('gua/general/anonymize_ip') ? 'true' : 'false';
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        if(Mage::getStoreConfigFlag('gua/general/enable')
            && Mage::getStoreConfig('gua/general/add_to') == $this->getParentBlock()->getNameInLayout()){
                return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isEcommerce()
    {
        $successPath =  Mage::getStoreConfig('gua/ecommerce/success_url') != "" ? Mage::getStoreConfig('gua/ecommerce/success_url') : '/checkout/onepage/success';
        if(Mage::getStoreConfigFlag('gua/ecommerce/enable')
            && strpos($this->getRequest()->getPathInfo(), $successPath) !== false){
                return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isEnhancedEcommerceAndCart()
	{
		$cartPath =  Mage::getStoreConfig('gua/ecommerce/cart_url') != "" ? Mage::getStoreConfig('gua/ecommerce/cart_url') : '/checkout/cart';
        if(Mage::getStoreConfigFlag('gua/ecommerce/enableec')
            && strpos($this->getRequest()->getPathInfo(), $cartPath) !== false ){
                return true;
        }
        return false;
	}

    /**
     * @return bool
     */
    public function isEnhancedEcommerce()
    {
        $successPath =  Mage::getStoreConfig('gua/ecommerce/success_url') != "" ? Mage::getStoreConfig('gua/ecommerce/success_url') : '/checkout/onepage/success';
        if(Mage::getStoreConfigFlag('gua/ecommerce/enableec')
            && strpos($this->getRequest()->getPathInfo(), $successPath) !== false){
                return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isCheckout()
    {
        $checkoutPath =  Mage::getStoreConfig('gua/ecommerce/checkout_url') != "" ?  Mage::getStoreConfig('gua/ecommerce/checkout_url') : '/checkout/onepage';
        if(Mage::getStoreConfigFlag('gua/ecommerce/funnel_enable')
            && strpos($this->getRequest()->getPathInfo(), $checkoutPath) !== false){
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
       return Mage::getStoreConfig('gua/ecommerce/checkout_url') != "" ?  Mage::getStoreConfig('gua/ecommerce/checkout_url') : '/checkout/onepage';
    }

    /**
     * @return string
     */
    public function getActiveStep()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn() ? 'billing' : 'login';
    }

    /**
     * @return mixed
     */
    public function isSSL()
    {
        return Mage::getStoreConfigFlag('gua/general/force_ssl');
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if(!isset($this->_order)){
            $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $this->_order = Mage::getModel('sales/order')->load($orderId);
        }
        return $this->_order;
    }

    /**
     * @return string
     */
    public function getTransactionIdField()
    {
        return Mage::getStoreConfig('gua/ecommerce/transaction_id') != false ? Mage::getStoreConfig('gua/ecommerce/transaction_id') : 'entity_id';
    }

    /**
     * @return bool
     */
    public function isCustomerGroup()
    {
        return Mage::getStoreConfigFlag('gua/customer/enable_customergroup') && $this->getCustomerGroupDimensionId() != '';
    }

    /**
     * @return mixed
     */
    public function getCustomerGroupDimensionId()
    {
        return Mage::getStoreConfig('gua/customer/dimension_customergroup');
    }

    /**
     * @return mixed
     */
    public function getCustomerGroup()
    {
        $groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        return Mage::getModel('customer/group')->load($groupId)->getCode();
    }

    /**
     * @return bool
     */
    public function isFirstPurchase()
    {
        return Mage::getStoreConfigFlag('gua/customer/enable_first_order') && $this->getFirstPurchaseDimensionId() !='';
    }

    /**
     * @return mixed
     */
    public function getFirstPurchaseDimensionId()
    {
        return Mage::getStoreConfig('gua/customer/dimension_first_purchase');
    }

    /**
     * @return bool
     */
    public function isNumberOfPurchase()
    {
        return Mage::getStoreConfigFlag('gua/customer/enable_customer_orders') && $this->getNumberOfPurchaseMetricId() !='';
    }

    /**
     * @return mixed
     */
    public function getNumberOfPurchaseMetricId()
    {
        return Mage::getStoreConfig('gua/customer/metric_customer_orders');
    }

    /**
     * @return mixed
     */
    public function getNumberOfOrders()
    {
        return Mage::getResourceModel('sale/order_collection')
            ->addFieldToFilter('customer_email', array('eq' => $this->getOrder()->getCustomerEmail()))
            ->getSize();
    }

    /**
     * @return mixed
     */
    public function isRemarketing()
    {
        return Mage::getStoreConfigFlag('gua/remarketing/enable');
    }

    /**
     * @return bool
     */
    public function isPriceTracking()
    {
        return Mage::getStoreConfigFlag('gua/product/enable_price') && $this->getPriceMetricId() !='';
    }

    /**
     * @return mixed
     */
    public function getPriceMetricId()
    {
        return Mage::getStoreConfig('gua/product/metric_price');
    }

    /**
     * @return bool
     */
    public function isAvailabilityTracking()
    {
        return Mage::getStoreConfigFlag('gua/product/enable_availability') && Mage::getStoreConfig('gua/product/dimension_availability') != '';
    }

    /**
     * @return mixed
     */
    public function getAvailabilityDimensionId()
    {
        return Mage::getStoreConfig('gua/product/dimension_availability');
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }
}