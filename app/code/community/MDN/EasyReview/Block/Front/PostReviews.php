<?php

class MDN_EasyReview_Block_Front_PostReviews extends Mage_Core_Block_Template {

    public function getOrder()
    {
        return Mage::registry('easyreview_current_order');
    }

    /**
     * Return products inside order
     * @return <type>
     */
    public function getProducts()
    {
        $products = array();

        $order = $this->getOrder();
        foreach($order->getAllItems() as $item)
        {
            $productId = $item->getproduct_id();
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product->getSku())
                    continue;

            //If has parent and child not allowed
            $hasParent = ($item->getparent_item_id() > 0);
            if ($hasParent && (Mage::getStoreConfig('easyreview/product/allow_child') == 0))
                continue;

            //if is parent and parent not allowed
            $isParent = (($item->getproduct_type() == 'configurable') || ($item->getproduct_type() == 'bundle') || ($item->getproduct_type() == 'grouped'));
            if ($isParent && (Mage::getStoreConfig('easyreview/product/allow_parent') == 0))
                continue;

            //check visibility
            $visibility = $product->getvisibility();
            $allowedVisibility = explode(',', Mage::getStoreConfig('easyreview/product/visibility'));
            if (!in_array($visibility, $allowedVisibility))
                    continue;

            $products[] = $product;
        }

        return $products;
    }

    /**
     * Return review form
     * @param <type> $product
     */
    public function getRatings()
    {
        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->load()
            ->addOptionToItems();
        return $ratingCollection;
    }

    public function getAction()
    {
        return $this->getUrl('*/*/saveReview');
    }

}