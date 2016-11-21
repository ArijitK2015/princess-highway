<?php

/**
 * Class FactoryX_Contests_Model_Observer
 */
class FactoryX_Contests_Model_Observer extends Mage_Core_Model_Abstract
{

    protected $_cmPopupConfigPath = 'newsletter/popup/enable';
    
    public function toggleContests(Mage_Cron_Model_Schedule $schedule = null, $dryrun = false)
    {
        $this->_disableContests($dryrun);
        $this->_enableContests($dryrun);
    }

    /**
     * Automatically disable the contests when the end date is reached
     * @param boolean if dryrun is set to true, it won't disable the contest
     */
    protected function _disableContests($dryrun = false)
    {
        try
        {

            // Current date
            $today = Mage::app()->getLocale()->date();

            // Get all automatic contests
            $contests = Mage::getResourceModel('contests/contest_collection')->addStatusFilter(2);

            // Cache refresh flag
            $cacheRefresh = false;

            // Foreach contest compare end date with today's date
            foreach ($contests as $contest)
            {
                $endDate = Mage::app()->getLocale()->date($contest->getEndDate(), Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),null, false);

                if ($today->isLater($endDate) && $contest->getDisplayed())
                {
                    // Hide and save
                    $contest->setDisplayed(0);
                    if (!$dryrun)
                    {
                        $contest->save();
                    }
                    $cacheRefresh = true;
                }
            }
            // Clean cache of the contests to avoid disabled contests to be displayed
            if ($cacheRefresh) {
                Mage::app()->cleanCache(FactoryX_Contests_Model_Contest::CACHE_TAG);
            }
        }
        catch (Exception $e)
        {
            Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
        }

    }

    /**
     * Automatically enable the contests when the start date is reached
     * @param boolean if dryrun is set to true, it won't enable the contest
     */
    protected function _enableContests($dryrun = false)
    {
        try
        {

            // Current date
            $today = Mage::app()->getLocale()->date();

            // Get all automatic contests
            $contests = Mage::getResourceModel('contests/contest_collection')->addStatusFilter(2);

            // Cache refresh flag
            $cacheRefresh = false;

            // Foreach contest compare start date with today's date
            foreach ($contests as $contest)
            {
                $startDate = Mage::app()->getLocale()->date($contest->getStartDate(), Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),null, false);
                $endDate = Mage::app()->getLocale()->date($contest->getEndDate(), Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),null, false);


                if ($startDate->isEarlier($today) && $endDate->isLater($today) && !$contest->getDisplayed())
                {
                    // Display and save
                    $contest->setDisplayed(1);
                    if (!$dryrun)
                    {
                        $contest->save();
                    }
                    $cacheRefresh = true;
                }
            }
            // Clean cache of the contests to avoid disabled contests to be displayed
            if ($cacheRefresh) {
                Mage::app()->cleanCache(FactoryX_Contests_Model_Contest::CACHE_TAG);
            }
        }
        catch (Exception $e)
        {
            Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
        }

    }

    public function generateContestUrl(Varien_Event_Observer $observer)
    {
        $contestId = $observer->getId();
        $contestData = $observer->getContest();

        // Handle URL Rewrites
        $targetPath = "contests/index/view/id/".$contestId;
        $requestedPath = $contestData['identifier'];
        Mage::helper('core/url_rewrite')->validateRequestPath($requestedPath);

        if (!Mage::app()->isSingleStoreMode() && isset($contestData['stores']))
        {
            foreach ($contestData['stores'] as $key => $storeId)
            {
                $existingUrlRewrite = Mage::getModel('core/url_rewrite');
                $existingUrlRewrite->setId(null);
                $existingUrlRewrite->setStoreId($storeId);
                Mage::getResourceSingleton('core/url_rewrite')->loadByRequestPath($existingUrlRewrite, $requestedPath);
                $existingUrlRewrite->setOrigData();
                $existingUrlRewrite->setDataChanges(false);
                $existingTargetPath = $existingUrlRewrite->getTargetPath();

                if ($existingTargetPath != "")
                {
                    if ($targetPath != $existingTargetPath)
                        throw new Exception (Mage::helper('contests')->__('The identifier you provided is already used with the store %s to redirect the following path: %s', $storeId, $existingTargetPath));
                }
                else
                {
                    $idPath = $requestedPath."_".$storeId;

                    // Create the URL Rewrite
                    Mage::getModel('core/url_rewrite')
                        ->setIsSystem(0)
                        ->setIdPath($idPath)
                        ->setTargetPath($targetPath)
                        ->setRequestPath($requestedPath)
                        ->setStoreId($storeId)
                        ->save();
                }
            }
        }
        else
        {
            $existingUrlRewrite = Mage::getModel('core/url_rewrite')->loadByRequestPath($requestedPath);
            $existingTargetPath = $existingUrlRewrite->getTargetPath();

            if ($existingTargetPath != "")
            {
                if ($targetPath != $existingTargetPath)
                    throw new Exception (Mage::helper('contests')->__('The identifier you provided is already used to redirect the following path: %s', $existingTargetPath));
            }
            else
            {
                // Create the URL Rewrite
                Mage::getModel('core/url_rewrite')
                    ->setIsSystem(0)
                    ->setIdPath($requestedPath)
                    ->setTargetPath($targetPath)
                    ->setRequestPath($requestedPath)
                    ->save();
            }
        }
    }

    /**
     * Disable newsletter popup
     * @param Varien_Event_Observer $observer
     */
    public function disableCmPopup(Varien_Event_Observer $observer)
    {
        $contestData = $observer->getContest();

        if (Mage::getStoreConfigFlag($this->_cmPopupConfigPath)
            && array_key_exists('is_popup',$contestData)
            && $contestData['is_popup'])
        {
            // Disable CM newsletter config
            Mage::getModel('core/config')->saveConfig($this->_cmPopupConfigPath, 0);
        }
    }
}