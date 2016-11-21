<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 10/12/2014
 * Time: 17:04
 */

class FactoryX_Abandonedcarts_Test_Model_Sales_Resource_Quote extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture quote.yaml
     */
    public function saveAttribute()
    {
        // Load the quote
        $quote = Mage::getModel('sales/quote')->load(1);
        // Old attribute value
        $oldAttributeVal = $quote->getAbandonedNotified();
        // We change the notification attribute
        $quote->setAbandonedNotified(1);
        // Save the attribute
        $quote->getResource()->saveAttribute($quote,array('abandoned_notified'));
        // Retrieve the new attribute value
        $newAttributeVal = $quote->getAbandonedNotified();
        // Assertion
        $this->assertNotEquals($oldAttributeVal,$newAttributeVal);
    }
}