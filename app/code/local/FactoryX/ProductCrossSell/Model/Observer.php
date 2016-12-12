<?php
class FactoryX_ProductCrossSell_Model_Observer
{
    /**
     * afterProductCollectionLoad
     *
     *
     * @param Varien_Event_Observer $observer
     * @internal param $ (type) (name) about this param
     */
    public function afterProductCollectionLoad(Varien_Event_Observer $observer)
    {
        // Get the collection
        $collection = $observer->getCollection();
        //Mage::helper('productcrosssell')->log(sprintf("%s->collection: %s", __METHOD__, get_class($collection)) );

        if ($collection instanceof Mage_Catalog_Model_Resource_Product_Link_Product_Collection) {
            /*
            const LINK_TYPE_RELATED     = 1;
            const LINK_TYPE_GROUPED     = 3;
            const LINK_TYPE_UPSELL      = 4;
            const LINK_TYPE_CROSSSELL   = 5;
            */
            //Mage::helper('productcrosssell')->log(sprintf("%s->linkTypeId: %d", __METHOD__, $collection->getLinkModel()->getLinkTypeId()) );
            if ($collection->getLinkModel()->getLinkTypeId() === Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL) {
                if ($customImage = Mage::helper('productcrosssell')->getCustomImageUpsells()) {
                    Mage::helper('productcrosssell')->addCustomImage($collection, $customImage);
                }
            }
            else if ($collection->getLinkModel()->getLinkTypeId() === Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED) {
                if ($customImage = Mage::helper('productcrosssell')->getCustomImageRelatedProducts()) {
                    Mage::helper('productcrosssell')->addCustomImage($collection, $customImage);
                }
            }
        }
        else if ($collection instanceof Mage_Reports_Model_Resource_Product_Index_Viewed_Collection) {
            if (Mage::helper('productcrosssell')->isEnabledRecentlyViewed() && $customImage = Mage::helper('productcrosssell')->getCustomImageRecentlyViewed()) {
                Mage::helper('productcrosssell')->addCustomImage($collection, $customImage);
            }
        }
    }
}