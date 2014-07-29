<?php
/**
 * Modifications from Creative Factory (use ???)
 */
abstract class FactoryX_ExtendedCatalog_Block_Catalog_Product_Abstract extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Gets minimal sales quantity
     *
     * @param Mage_Catalog_Model_Product $product
     * @return int|null
     */
    public function getMinimalQty($product)
    {
        $stockItem = $product->getStockItem();
        if ($stockItem) {
            return ($stockItem->getMinSaleQty()
            && $stockItem->getMinSaleQty() > 0 ? $stockItem->getMinSaleQty() * 1 : 1);
        }
        return null;
    }

    /**
     * Returns product tier price block html
     *
     * @param null|Mage_Catalog_Model_Product $product
     * @param null|Mage_Catalog_Model_Product $parent
     * @return string
     */
    public function getTierPriceHtml($product = null, $parent = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $this->_getPriceBlock($product->getTypeId())
            ->setTemplate($this->getTierPriceTemplate())
            ->setProduct($product)
            ->setInGrouped($product->isGrouped())
            ->setParent($parent)
            ->callParentToHtml();
    }

    /*
     * Calls the object's to Html method.
     * This method exists to make the code more testable.
     * By having a protected wrapper for the final method toHtml, we can 'mock' out this method
     * when unit testing
     *
     *  @return string
     */
    protected function callParentToHtml()
    {
        return $this->toHtml();
    }
}
