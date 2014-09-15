<?php
class FactoryX_ExtendedCatalog_Model_Observer
{
    function disableCsrf($observer)
    {
        $route = $observer->getEvent()->getControllerAction()->getFullActionName();

		// Disable CSRF for the add to cart event
        if ($route == "checkout_cart_add") {
            $key = Mage::getSingleton('core/session')->getFormKey();
            Mage::app()->getRequest()->setParam('form_key', $key);
        }
    }
}
?>