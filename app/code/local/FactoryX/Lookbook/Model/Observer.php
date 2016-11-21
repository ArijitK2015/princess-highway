<?php

class FactoryX_Lookbook_Model_Observer
{
    public function generateLookbookUrl(Varien_Event_Observer $observer) {
        try {
            $this->generateLookbookUrlWrapper($observer);
        }
        catch(Exception $ex) {
            Mage::getSingleton('core/session')->addNotice(sprintf("There was an problem creating the lookbook URL rewrite. Error: %s", $ex->getMessage()));
        }
    }

    public function generateLookbookUrlWrapper(Varien_Event_Observer $observer)
    {
        $lookbookId = $observer->getId();
        $lookbookData = $observer->getLookbook();

        // Handle URL Rewrites
        if ($lookbookData['lookbook_type'] == "slideshow")
        {
            $targetPath = "lookbook/index/slideshow/id/".$lookbookId;
        }
        elseif ($lookbookData['lookbook_type'] == "flipbook")
        {
            $targetPath = "lookbook/index/flipbook/id/".$lookbookId;
        }
        else
        {
            $targetPath = "lookbook/index/view/id/".$lookbookId;
        }

        $requestedPath = $lookbookData['identifier'];
        Mage::helper('core/url_rewrite')->validateRequestPath($requestedPath);

        if (!Mage::app()->isSingleStoreMode() && isset($lookbookData['stores']))
        {
            foreach ($lookbookData['stores'] as $key => $storeId)
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
                        throw new Exception (Mage::helper('lookbook')->__('The identifier you provided is already used with the store %s to redirect the following path: %s', $storeId, $existingTargetPath));
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
                    throw new Exception (Mage::helper('lookbook')->__('The identifier you provided is already used to redirect the following path: %s', $existingTargetPath));
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

}