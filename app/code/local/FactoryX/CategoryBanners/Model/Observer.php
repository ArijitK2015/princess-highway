<?php

/**
 * Class FactoryX_CategoryBanners_Model_Observer
 */
class FactoryX_CategoryBanners_Model_Observer extends Mage_Core_Model_Abstract
{

	public function toggleBanners(Mage_Cron_Model_Schedule $schedule = null, $dryrun = false)
	{
		$this->_disableBanners($dryrun);
		$this->_enableBanners($dryrun);
	}

	/**
	 * Automatically disable the banners when the end date is reached
	 * @param boolean $dryrun if set to true, it won't disable the banner
	 */
	protected function _disableBanners($dryrun = false) {
		try {
			// Current date			
			$today = Mage::app()->getLocale()->date();
			
			// Get all automatic banners
			$banners = Mage::getResourceModel('categorybanners/banner_collection')->addStatusFilter(FactoryX_CategoryBanners_Model_Status::STATUS_AUTOMATIC);
			
			// Foreach banner compare end date with today's date
			foreach ($banners as $banner)
			{
				$endDate = Mage::app()->getLocale()->date(
				    $banner->getEndDate(),
				    Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
				    null,
				    true
                );
				$endDate->set('00:00:00',Zend_Date::TIMES);

				// If the end date is in the past
				if ($today->isLater($endDate))
				{
					if ($banner->getDisplayed())
					{
						// Hide and save
						$banner->setDisplayed(0);
						if (!$dryrun)
						{
							$banner->getResource()->saveAttribute($banner,array('displayed'));
						}
					}
				}
			}
		}
		catch (Exception $e)
		{
			Mage::helper('categorybanners')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
		}
		
	}
	
	/**
	 * Automatically enable the banners when the start date is reached
	 * @param boolean $dryrun if set to true, it won't enable the banner
	 */
	protected function _enableBanners($dryrun = false)
	{
		try
		{
			// Current date			
			$today = Mage::app()->getLocale()->date();
			
			// Get all automatic banners
			$banners = Mage::getResourceModel('categorybanners/banner_collection')->addStatusFilter(FactoryX_CategoryBanners_Model_Status::STATUS_AUTOMATIC);
			
			// Foreach banner compare start date with today's date
			foreach ($banners as $banner)
			{
				$startDate = Mage::app()->getLocale()->date($banner->getStartDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
				$endDate = Mage::app()->getLocale()->date($banner->getEndDate(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
				$startDate->set('00:00:00',Zend_Date::TIMES);
				$endDate->set('00:00:00',Zend_Date::TIMES);

				// If the start date is in the past or the end date is in the future
				if ($startDate->isEarlier($today) && $endDate->isLater($today))
				{
					if (!$banner->getDisplayed())
					{
                        // Show and save
                        $banner->setDisplayed(1);
                        if (!$dryrun)
                        {
                            $banner->getResource()->saveAttribute($banner,array('displayed'));
                        }
					}
				}
			}
		}
		catch (Exception $e)
		{
			Mage::helper('categorybanners')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
		}
		
	}

	/**
	 * Add the category banner to the category
	 * @param Varien_Event_Observer $observer
     */
	public function setCategoryBanner(Varien_Event_Observer $observer)
	{
		// Get the block
		$block = $observer->getEvent()->getBlock();

		// Check if the block is Category View block plus double check using the controller name
		if (
		    (
		        $block instanceof Mage_Catalog_Block_Category_View
		        ||
		        $block instanceof MageWorx_SeoSuite_Block_Catalog_Category_View
            )
			&&
			$block->getRequest()->getControllerName() == "category"
        ) {
			// Retrieve the current category
			$categoryId = Mage::registry('current_category')->getId();

			if ($categoryId) {
				// Get the banners displayed for this category
				$categoryBanner = Mage::getModel('categorybanners/banner')
					->getCollection()
					->addCatFilter($categoryId)
					->addDisplayedFilter(1);

				// If there is banners for this category
				if ($categoryBanner->getSize()) {
					// We set the banner of the block as the first item of the collection
					$block->setBanner($categoryBanner->getFirstItem());
				}
				// Else we check the parent category banners
				else {
					$category = $block->getCurrentCategory();
					// Loop through the parent categories
					while ($parentCategory = $category->getParentCategory()) {
						// Load the banner corresponding to the parent category with the display on children attribute
						$categoryBanner = Mage::getModel('categorybanners/banner')
							->getCollection()
							->addCatFilter($parentCategory->getId())
							->addDisplayedFilter(1)
							->addDisplayOnChildren(1);
						// Break if found
						if ($categoryBanner->getSize()) {
						    Mage::log(sprintf("%s->break", __METHOD__) );
						    break;
						}
						else {
							// If we reached the top root category we break
							if ($parentCategory->getId() == Mage::app()->getStore()->getRootCategoryId()) {
							    Mage::log(sprintf("%s->break", __METHOD__) );
								break;
							}
							else {
								// Assign the parent as the category to go to higher category level
								$category = $parentCategory;
							}
						}
					}
					// Return category banner if found
					if ($categoryBanner->getSize()) {
					    $block->setBanner($categoryBanner->getFirstItem());
					}
				}
			}
		}
	}

	/**
	 * Rewrite default Magento HTML to use category banner images
	 * @param Varien_Event_Observer $observer
     */
	public function bannerToHtml(Varien_Event_Observer $observer)
	{
		// Get the rendered block
		$block = $observer->getBlock();

		// If it is a Category View block
		if (
		    $block instanceof Mage_Catalog_Block_Category_View
		    ||
		    $block instanceof MageWorx_SeoSuite_Block_Catalog_Category_View
        ) {            
			// We retrieve the HTML via the transport
			$transport = $observer->getTransport();
			$html = $transport->getHtml();
			// If there is already a category img
			$ogCategoryImg = $block->getCurrentCategory()->getImageUrl();
			// Check the override flag
			$overrideFlag = Mage::helper('categorybanners')->overrideOriginalCategoryImg();
			// Get the banner
			$_banner = $block->getBanner();
			
			// Get the banner link
			$target = "";
			if ($_banner) {
    			$_imgLink = $_banner->getUrl();
    			// check if the base url is different then set a target     			
    			$url1 = parse_url(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB));
    			$url2 = parse_url($_imgLink);
    			if ($url1['host'] !== $url2['host']) {
    			    $target = " target='_blank' rel='noopener'";
                    Mage::log(sprintf("%s->target=%s", __METHOD__, $target) );    			    
    			}
            }
            			
			// If category img + override + banner
			if ($overrideFlag && $_banner && $ogCategoryImg) {
				// Get the banner src
				$_imgSrc = Mage::getBaseUrl('media') . 'categorybanners' . $_banner->getImage();
				// Get the banner alt
				$_imgAlt = $_banner->getAlt() ? $_banner->getAlt() : $block->getCurrentCategory()->getName();

				// We replace the og img src with the banner src
				$newHtml = str_replace($ogCategoryImg, $_imgSrc, $html);
				// We replace the og alt with the banner alt
				$newHtml = str_replace(
				    'alt="'.$block->escapeHtml($block->getCurrentCategory()->getName()).'" title="'.$block->escapeHtml($block->getCurrentCategory()->getName()).'"',
				    'alt="'.$_imgAlt.'" title="'.$_imgAlt.'"',
				    $newHtml
                );
				// If there is a link of the banner
				if ($_imgLink) {
					// Deconstruct the HTML
					// First part
					$firstPart = explode('<p class="category-image">', $newHtml, 2);
					if (count($firstPart) > 1) {
						// Second Part
						$secondPart = explode('</p>', $firstPart[1], 2);
						if (count($secondPart) > 1) {
                            $newHtml = sprintf("%s<p class=\"category-image\"><a href=\"%s\"%s>%s</a></p>%s", $firstPart[0], $_imgLink, $target, $secondPart[0], $secondPart[1]);
						}
					}
				}

				// Add custom CSS if there is some
				if ($css = Mage::helper('categorybanners')->getCss()) {
					$newHtml .= '<style type="text/css">' . $css . '</style>';
				}

				// Change output HTML
				$transport->setHtml($newHtml);
			}
			// In the case that there is no OG img we need to add the banner img to the HTML instead of replacing
			elseif ($_banner && !$ogCategoryImg) {
				// Get the banner src
				$_imgSrc = Mage::getBaseUrl('media') . 'categorybanners' . $_banner->getImage();
				
				// Get the banner alt
				$_imgAlt = $_banner->getAlt() ? $_banner->getAlt() : $block->getCurrentCategory()->getName();

				// Generate HTML
				$_imgHtml = '<p class="category-image">';
				
				// Add the banner link
				if ($_imgLink)  {
				    $_imgHtml .= '<a href="'.$_imgLink.'">';
				}
				$_imgHtml .= '<img src="'.$_imgSrc.'" alt="'.$block->escapeHtml($_imgAlt).'" />';
				if ($_imgLink) {
				    $_imgHtml .= '</a>';
				}
				$_imgHtml .= '</p>';

				// Add custom CSS
				if ($css = Mage::helper('categorybanners')->getCss()) {
					$_imgHtml .= '<style type="text/css">' . $css . '</style>';
				}

				// Split OG HTML
				$explode = explode('</div>', $html, 2);
				// Add banner HTML
				$newHtml = $explode[0] . "</div>" . $_imgHtml . $explode[1];
				// Set HTML
				$transport->setHtml($newHtml);
			}
			// Any other case means no changes
		}
	}
}