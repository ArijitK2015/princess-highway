<?php
/**
 * XMLFeed Observer Model
 *
 * @category   Mage
 * @package    FactoryX_XMLFeed
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class FactoryX_XMLFeed_Model_Observer
{

    /**
     * Clean cache for catalog review rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function reviewSaveAfter(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(FactoryX_XMLFeed_Block_Catalog_Review::CACHE_TAG));

    }

    /**
     * Clean cache for notify stock rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function salesOrderItemSaveAfterNotifyStock(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(FactoryX_XMLFeed_Block_Catalog_NotifyStock::CACHE_TAG));

    }

    /**
     * Clean cache for catalog new orders rss
     *
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function salesOrderItemSaveAfterOrderNew(Varien_Event_Observer $observer)
    {

        Mage::app()->cleanCache(array(FactoryX_XMLFeed_Block_Order_New::CACHE_TAG));

    }
}
