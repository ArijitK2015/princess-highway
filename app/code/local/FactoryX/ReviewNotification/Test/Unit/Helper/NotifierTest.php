<?php

class FactoryX_ReviewNotification_Test_Unit_Helper_NotifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Exception
     * @expectedExceptionMessageRegExp /Cannot notify order \d+ as it is not complete/
     */
    public function testNotifyWithIncompleteOrder()
    {
        $order = $this->_getLastIncompleteOrder();

        Mage::helper('reviewnotification/notifier')->notify($order);
    }

    public function testNotifyWithCompleteOrder()
    {
        $order = $this->_getLastCompleteOrder();

        Mage::helper('reviewnotification/notifier')->notify($order);

        $this->assertEquals(1, $order->getEasyreviewNotified());
    }

    protected function setUp()
    {
        $this->getConnection()->beginTransaction();
        Mage::app()->getStore()->setConfig('reviewnotification/general/email_template', $this->_getIdFromNewEmailTemplate());
    }

    protected function tearDown()
    {
        $this->getConnection()->rollBack();
        DigitalPianism_TestFramework_Helper_Magento::reset();
    }

    private function getConnection()
    {
        /** @var \Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        return $resource->getConnection('default_write');
    }

    private function _getLastCompleteOrder()
    {
        return Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('state','complete')
            ->setPageSize(1)
            ->getFirstItem();
    }

    private function _getLastIncompleteOrder()
    {
        return Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('state',array('neq'=>'complete'))
            ->setPageSize(1)
            ->getFirstItem();
    }

    private function _getIdFromNewEmailTemplate()
    {
        /** @var Mage_Core_Model_Email_Template $template */
        $template = Mage::getModel('core/email_template')
            ->loadDefault('reviewnotification_general_email_template')
            ->setTemplateCode('Custom Review Notification Email Template')
            ->setAddedAt(Mage::getSingleton('core/date')->gmtDate())
            ->setOrigTemplateCode('reviewnotification_general_email_template')
            ->setTemplateId(NULL)
            ->save();

        return $template->getId();
    }
}