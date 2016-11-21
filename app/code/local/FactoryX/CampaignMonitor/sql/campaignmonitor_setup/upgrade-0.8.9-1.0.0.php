<?php

$installer = $this;

$installer->startSetup();

$idPath = "subscribe";
// We need to reinit the stores as the are not initiate when the script runs
Mage::app()->reinitStores();
// Loop through the stores
foreach (Mage::app()->getWebsites() as $website)
{
    foreach ($website->getGroups() as $group)
    {
        $stores = $group->getStores();
        // Generate URLs for each stores
        foreach ($stores as $store)
        {
            // Check if already an existing URL rewrite for subscribe
            $existingUrlRewrite = Mage::getModel('core/url_rewrite');
            $existingUrlRewrite->setStoreId($store->getId());
            $existingUrlRewrite->loadByRequestPath($idPath);
            $existingTargetPath = $existingUrlRewrite->getTargetPath();
            if ($existingTargetPath != "")
            {
                $existingUrlRewrite->setTargetPath("campaignmonitor/subscriber/index");
                $existingUrlRewrite->save();
            }
            else
            {
                // Create the URL Rewrite
                Mage::getModel('core/url_rewrite')
                    ->setIsSystem(0)
                    ->setIdPath($idPath)
                    ->setTargetPath("campaignmonitor/subscriber/index")
                    ->setRequestPath($idPath)
                    ->setStoreId($store->getId())
                    ->save();
            }

            // Check if there is an existing CMS page identified by "subscribe"
            $pageId = Mage::getModel('cms/page')->checkIdentifier($idPath, $store->getId());
            if ($pageId)
            {
                // Load the corresponding page
                $cmsPage = Mage::getModel('cms/page')->load($pageId);
                // We are going to keep it as a backup in case something goes wrong
                // Change the identifier to avoid conflict with the new controller system
                $cmsPage->setIdentifier($idPath . "_old");
                // Change the title to avoid keyboard breaking
                $cmsPage->setTitle($cmsPage->getTitle() . " / BACKUP AFTER CAMPAIGNMONITOR UPGRADE");
                // Disable it
                $cmsPage->setIsActive(false);
                // Save
                $cmsPage->save();
            }
        }
    }
}

$installer->endSetup();