<?php

/**
 * Class FactoryX_AdvertPanels_Model_Observer
 */
class FactoryX_AdvertPanels_Model_Observer
{
    /**
     * Redirect the product view if the product is an advert panel
     * @param Varien_Event_Observer $observer
     */
    public function redirectAdvertPanels(Varien_Event_Observer $observer)
    {
        $action = $observer->getControllerAction();
        $data = $action->getRequest()->getParams();

        if (array_key_exists('id', $data)) {
            
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToFilter('entity_id', $data['id'])
                ->addAttributeToSelect(array('type_id','description'))
                ->setPageSize(1)
                ->setCurPage(1);

            if ($collection->getSize()) {
                $product = $collection->getFirstItem();

                if ($product->getTypeId() === "panel"
                    && $description = $product->getDescription()) {
                    $action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                    $action->getResponse()->setRedirect($description);
                }
            }
        }
    }
}