<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Model_Observer extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture observer.yaml
     */
    public function disableContests()
    {
        // Set dates
        $existingContest = Mage::getModel('contests/contest')->load(1);
        $oldDisplayed = $existingContest->getDisplayed();
        $startdate = Mage::app()->getLocale()->date(Zend_Date::now()->sub(7,Zend_Date::DAY), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
        $startdate->set('00:00:00',Zend_Date::TIMES);
        $enddate = Mage::app()->getLocale()->date(Zend_Date::now()->sub(1,Zend_Date::DAY), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
        $enddate->set('00:00:00',Zend_Date::TIMES);
        $existingContest->setStartDate($startdate);
        $existingContest->setEndDate($enddate);
        $existingContest->save();

        // Run the observer
        $observer = Mage::getModel('contests/observer');
        $observer->disableContests();

        // Check
        $existingContestReloaded = Mage::getModel('contests/contest')->load(1);
        $newDisplayed = $existingContestReloaded->getDisplayed();
        $this->assertEquals($newDisplayed,0);
        $this->assertNotEquals($oldDisplayed,$newDisplayed);
    }

    /**
     * @test
     * @loadFixture observer.yaml
     */
    public function enableContests()
    {
        // Set dates
        $existingContest = Mage::getModel('contests/contest')->load(2);
        $oldDisplayed = $existingContest->getDisplayed();
        $startdate = Mage::app()->getLocale()->date(Zend_Date::now()->sub(1,Zend_Date::DAY), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
        $startdate->set('00:00:00',Zend_Date::TIMES);
        $enddate = Mage::app()->getLocale()->date(Zend_Date::now()->add(7,Zend_Date::DAY), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
        $enddate->set('00:00:00',Zend_Date::TIMES);
        $existingContest->setStartDate($startdate);
        $existingContest->setEndDate($enddate);
        $existingContest->save();

        // Run the observer
        $observer = Mage::getModel('contests/observer');
        $observer->enableContests();

        // Check
        $existingContestReloaded = Mage::getModel('contests/contest')->load(2);
        $newDisplayed = $existingContestReloaded->getDisplayed();
        $this->assertEquals($newDisplayed,1);
        $this->assertNotEquals($oldDisplayed,$newDisplayed);
    }
}