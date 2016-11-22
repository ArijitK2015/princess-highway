<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_CacheSupport_Test_Controller_IndexController extends EcomDev_PHPUnit_Test_Case_Controller
{
    public function testIndexAction()
    {
        $this->getRequest()->setServer('REQUEST_URI',"/cachesupport");
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('cachesupport');
        $this->assertRequestRoute('cachesupport/index/index');
        $this->assertResponseBodyContains(date("m:s"));
    }

    public function testCartNotLoggedInAction()
    {
        $this->getRequest()->setServer('REQUEST_URI',"/cachesupport/index/cart");
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('cachesupport/index/cart');
        $this->assertRequestRoute('cachesupport/index/cart');
        $this->assertLayoutBlockRendered("top.links");
        $this->assertResponseBodyContains("My Account");
        $this->assertResponseBodyContains("My Wishlist");
        $this->assertResponseBodyContains("My Cart");
        $this->assertResponseBodyContains("Checkout");
        $this->assertResponseBodyContains("Log In");
    }

    public function testCartLoggedInAction()
    {
        $this->markTestIncomplete("Cannot handle sessions yet");
        $this->getRequest()->setServer('REQUEST_URI',"/cachesupport/index/cart");
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('cachesupport/index/cart');
        $this->assertRequestRoute('cachesupport/index/cart');
        $this->assertLayoutBlockRendered("top.links");
        $this->assertResponseBodyContains("My Account");
        $this->assertResponseBodyContains("My Wishlist");
        $this->assertResponseBodyContains("My Cart");
        $this->assertResponseBodyContains("Checkout");
        $this->assertResponseBodyContains("Log Out");
    }

    public function testMobileCartAction()
    {
        $this->markTestIncomplete("Not sure how the mobile cart action is supposed to work, on hold");
        $this->getRequest()->setServer('REQUEST_URI',"/cachesupport/index/mobile_cart");
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('cachesupport/index/mobile_cart');
        $this->assertRequestRoute('cachesupport/index/mobile_cart');
        $this->assertLayoutBlockRendered("cart.content");
        $this->assertResponseBodyContains("My Account");
        $this->assertResponseBodyContains("My Wishlist");
        $this->assertResponseBodyContains("My Cart");
        $this->assertResponseBodyContains("Checkout");
        $this->assertResponseBodyContains("Log Out");
    }

    public function testCartQtyAction()
    {
        $this->markTestIncomplete("Not sure how the cart qty action is supposed to work, on hold");
        $this->getRequest()->setServer('REQUEST_URI',"/cachesupport/index/cart_qty");
        $this->getRequest()->setHeader('User-Agent',"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0 FirePHP/0.7.4");
        $this->dispatch('cachesupport/index/cart_qty');
        $this->assertRequestRoute('cachesupport/index/cart_qty');
        $this->assertResponseBodyContains("My Account");
        $this->assertResponseBodyContains("My Wishlist");
        $this->assertResponseBodyContains("My Cart");
        $this->assertResponseBodyContains("Checkout");
        $this->assertResponseBodyContains("Log Out");
    }
}