<?php
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

class FactoryX_ProductExport_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    public function massExportAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
            $this->_redirect('*/*/index');
        }
        else {
            //write headers to the csv file
            $content = "id,name,url,sku,price,special_price\n";
            try {
                foreach ($productIds as $productId) {
                    $product = Mage::getSingleton('catalog/product')->load($productId);
                    $content .= "\"{$product->getId()}\",\"{$product->getName()}\",\"{$product->getProductUrl()}\",\"{$product->getSku()}\",\"{$product->getPrice()}\",\"{$product->getSpecialPrice()}\"\n";
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/index');
            }
            $this->_prepareDownloadResponse('export.csv', $content, 'text/csv');
        }

    }
}
?>