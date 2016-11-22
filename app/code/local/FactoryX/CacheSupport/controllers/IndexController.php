<?php

/**
 * Class FactoryX_CacheSupport_IndexController
 */
class FactoryX_CacheSupport_IndexController extends Mage_Core_Controller_Front_Action{

    public function indexAction(){
        $this->getResponse()
            ->clearHeaders()
            ->setBody(date('m:s'));
    }

    public function cartAction(){

        $layout = Mage::app()->getLayout();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $layout->getUpdate()->addHandle('default')->addHandle('customer_logged_in')->load();
        }else{
            $layout->getUpdate()->addHandle('default')->addHandle('customer_logged_out')->load();
        }

        $layout->generateXml()->generateBlocks();

        $topLink = $layout->getBlock('top.links')->toHtml();

        $this->getResponse()
            ->clearHeaders()
            ->setBody($topLink);
    }

    public function mobileCartAction(){

        try {
            $layout = Mage::app()->getLayout();
            $layout->getUpdate()->addHandle('checkout_cart_index')->addHandle('default')->load();
            $layout->generateXml()->generateBlocks($layout->getNode('cart.content'));
            $cartContent = $layout->getBlock('cart.content');
            if ($cartContent) {
                $body = $cartContent->toHtml();
            }
        } catch (Exception $e) {
            if (!isset($layout))
            {
                $layout = Mage::app()->getLayout();
            }
            $body = $layout->getBlock('cart.content')->toHtml();
        }

        $this->getResponse()
            ->clearHeaders()
            ->setBody($body);

    }

    public function cartQtyAction(){
        $qty = 0;
        $cart = Mage::getModel('checkout/cart')->getQuote()->getData();
        if (isset($cart['items_qty'])){
            $qty = (int)$cart['items_qty'];
        }
        $this->getResponse()
            ->clearHeaders()
            ->setBody($qty);
    }
}