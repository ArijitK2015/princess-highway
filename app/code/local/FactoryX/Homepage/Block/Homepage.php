<?php

class FactoryX_Homepage_Block_Homepage extends Mage_Core_Block_Template 
{
	
	/**
	 *	Retrieve the current homepage for the frontend
	 */
	public function getCurrentHomepage($store = null)     
    {
		if ($store == "")	$store = null;
		
		try
		{
			// If we are on the preview page
			if (Mage::app()->getRequest()->getActionName() == "preview")
			{
				// We retrieve the home page based on the id
				$currentHomepage = Mage::getModel('homepage/homepage')->load(Mage::app()->getRequest()->getParam('id'));
			}
			else
			{
				// Else we retrieve it using the first enabled homepage (there should be only one anyway)
				$currentHomepage = Mage::getModel('homepage/homepage')
									->getCollection()
									->addStatusFilter(1)
									->addStoreFilter($store)
									->getFirstItem();
			}
			
			// Ensure the homepage is viewable in the store
			if (!Mage::app()->isSingleStoreMode()) 
			{
				if ($currentHomepage->isStoreViewable()) 
					return $currentHomepage;
				else 
					throw new Exception ('This homepage is not available with this store.');
			}
			else
			{
				return $currentHomepage;
			}
		}
		catch (Exception $e)
		{
			Mage::helper('homepage')->log($this->__('Exception caught in %s under % with message: %s', __FILE__, __FUNCTION__, $e->getMessage()));
			return false;
		}
    }
	
	/**
	 * Build the frontend home page
	 */
	public function __construct()
    {
        parent::__construct();

		// Retrieve the current home page and use the store id if multistore
		if (!Mage::app()->isSingleStoreMode())
		{
			$storeId = Mage::app()->getStore()->getId();
		}
		else
		{
			$storeId = "";
		}
		
		$homepage = $this->getCurrentHomepage($storeId);
		
		if ($homepage)
		{
		
			// Set the template based on the layout chosen
			$this->setTemplate('factoryx/homepage/templates/'.$homepage->getLayout().'.phtml');
			
			// Switch based on the layout chosen
			switch ($homepage->getLayout())
			{
				// 1 image layout
				case '1-layout/1-main':
					$this->setImage($homepage->getImage(1));
					break;
				// 2 images layouts
				case '2-layout/1-left-1-right':
					$this->setLeft($homepage->getImage(1));
					$this->setRight($homepage->getImage(2));
					break;
				case '2-layout/1-top-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setBottom($homepage->getImage(2));
					break;
				case '2-layout/2-slider':
				case '3-layout/3-slider':
				case '4-layout/4-slider':
				case '5-layout/5-slider':
					$this->setSlides($homepage->getAllImages());
					break;
				// 3 images layouts
				case '3-layout/1-left-2-right':
					$this->setLeft($homepage->getImage(1));
					$this->setRight1($homepage->getImage(2));
					$this->setRight2($homepage->getImage(3));
					break;
				case '3-layout/1-top-2-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setBottom1($homepage->getImage(2));
					$this->setBottom2($homepage->getImage(3));
					break;
				case '3-layout/2-left-1-right':
					$this->setRight($homepage->getImage(1));
					$this->setLeft1($homepage->getImage(2));
					$this->setLeft2($homepage->getImage(3));
					break;
				case '3-layout/2-top-1-bottom':
					$this->setBottom($homepage->getImage(1));
					$this->setTop1($homepage->getImage(2));
					$this->setTop2($homepage->getImage(3));
					break;	
				case '3-layout/1-top-2-slider':
					$this->setSlides($homepage->getImages(array('1','2')));
					$this->setTop($homepage->getImage(3));
					break;	
				case '3-layout/2-slider-1-bottom':
					$this->setSlides($homepage->getImages(array('1','2')));
					$this->setBottom($homepage->getImage(3));
					break;
				case '3-layout/1-left-1-middle-1-right':
					$this->setLeft($homepage->getImage(1));
					$this->setMiddle($homepage->getImage(2));
					$this->setRight($homepage->getImage(3));
					break;
				case '3-layout/1-top-1-middle-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setMiddle($homepage->getImage(2));
					$this->setBottom($homepage->getImage(3));
					break;
				// 4 images layouts
				case '4-layout/1-left-3-right':
					$this->setLeft($homepage->getImage(1));
					$this->setRight1($homepage->getImage(2));
					$this->setRight2($homepage->getImage(3));
					$this->setRight3($homepage->getImage(4));
					break;
				case '4-layout/1-top-1-bottom-2-bottomright':
					$this->setTop($homepage->getImage(1));
					$this->setBottom($homepage->getImage(4));
					$this->setBottomRight1($homepage->getImage(2));
					$this->setBottomRight2($homepage->getImage(3));
					break;
				case '4-layout/1-top-2-bottomleft-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setBottom($homepage->getImage(4));
					$this->setBottomLeft1($homepage->getImage(2));
					$this->setBottomLeft2($homepage->getImage(3));
					break;
				case '4-layout/1-top-2-topright-1-bottom':
					$this->setTop($homepage->getImage(4));
					$this->setBottom($homepage->getImage(1));
					$this->setTopRight1($homepage->getImage(2));
					$this->setTopRight2($homepage->getImage(3));
					break;
				case '4-layout/1-top-3-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setBottom1($homepage->getImage(2));
					$this->setBottom2($homepage->getImage(3));
					$this->setBottom3($homepage->getImage(4));
					break;
				case '4-layout/2-top-2-bottom':
					$this->setTop1($homepage->getImage(1));
					$this->setTop2($homepage->getImage(2));
					$this->setBottom1($homepage->getImage(3));
					$this->setBottom2($homepage->getImage(4));
					break;
				case '4-layout/2-topleft-1-top-1-bottom':
					$this->setTop($homepage->getImage(4));
					$this->setBottom($homepage->getImage(1));
					$this->setTopLeft1($homepage->getImage(2));
					$this->setTopLeft2($homepage->getImage(3));
					break;
				case '4-layout/3-left-1-right':
					$this->setRight($homepage->getImage(1));
					$this->setLeft1($homepage->getImage(2));
					$this->setLeft2($homepage->getImage(3));
					$this->setLeft3($homepage->getImage(4));
					break;
				case '4-layout/3-top-1-bottom':
					$this->setBottom($homepage->getImage(1));
					$this->setTop1($homepage->getImage(2));
					$this->setTop2($homepage->getImage(3));
					$this->setTop3($homepage->getImage(4));
					break;
				case '4-layout/1-top-3-slider':
					$this->setSlides($homepage->getImages(array('1','2','3')));
					$this->setTop($homepage->getImage(4));
					break;
				case '4-layout/3-slider-1-bottom':
					$this->setSlides($homepage->getImages(array('1','2','3')));
					$this->setBottom($homepage->getImage(4));
					break;
				case '4-layout/2-slider-2-bottom':
					$this->setSlides($homepage->getImages(array('1','2')));
					$this->setBottom1($homepage->getImage(3));
					$this->setBottom2($homepage->getImage(4));
					break;
				case '4-layout/2-top-2-slider':
					$this->setSlides($homepage->getImages(array('1','2')));
					$this->setTop1($homepage->getImage(3));
					$this->setTop2($homepage->getImage(4));
					break;
				case '4-layout/1-left-1-middletop-1-middlebottom-1-right':
					$this->setMiddletop($homepage->getImage(1));
					$this->setLeft($homepage->getImage(2));
					$this->setRight($homepage->getImage(3));
					$this->setMiddlebottom($homepage->getImage(4));
					break;
				case '4-layout/1-left-2-middle-1-right':
					$this->setLeft($homepage->getImage(1));
					$this->setMiddle1($homepage->getImage(2));
					$this->setMiddle2($homepage->getImage(3));
					$this->setRight($homepage->getImage(4));
					break;
				case '4-layout/1-top-1-middleleft-1-middleright-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setMiddleleft($homepage->getImage(2));
					$this->setMiddleright($homepage->getImage(3));
					$this->setBottom($homepage->getImage(4));
					break;
				case '4-layout/1-top-2-middle-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setMiddle1($homepage->getImage(2));
					$this->setMiddle2($homepage->getImage(3));
					$this->setBottom($homepage->getImage(4));
					break;
				// 5 images layouts
				case '5-layout/1-left-4-right':
					$this->setLeft($homepage->getImage(1));
					$this->setRight1($homepage->getImage(2));
					$this->setRight2($homepage->getImage(3));
					$this->setRight3($homepage->getImage(4));
					$this->setRight4($homepage->getImage(5));
					break;
				case '5-layout/1-top-2-middle-2-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setMiddle1($homepage->getImage(2));
					$this->setMiddle2($homepage->getImage(4));
					$this->setBottom1($homepage->getImage(3));
					$this->setBottom2($homepage->getImage(5));
					break;
				case '5-layout/1-top-4-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setBottom1($homepage->getImage(2));
					$this->setBottom2($homepage->getImage(3));
					$this->setBottom3($homepage->getImage(4));
					$this->setBottom4($homepage->getImage(5));
					break;
				case '5-layout/2-left-1-middle-2-right':
					$this->setMiddle($homepage->getImage(1));
					$this->setLeft1($homepage->getImage(2));
					$this->setLeft2($homepage->getImage(3));
					$this->setRight1($homepage->getImage(4));
					$this->setRight2($homepage->getImage(5));
					break;
				case '5-layout/2-top-1-middle-2-bottom':
					$this->setMiddle($homepage->getImage(1));
					$this->setTop1($homepage->getImage(2));
					$this->setTop2($homepage->getImage(4));
					$this->setBottom1($homepage->getImage(3));
					$this->setBottom2($homepage->getImage(5));
					break;
				case '5-layout/2-top-2-middle-1-bottom':
					$this->setBottom($homepage->getImage(1));
					$this->setTop1($homepage->getImage(2));
					$this->setTop2($homepage->getImage(4));
					$this->setMiddle1($homepage->getImage(3));
					$this->setMiddle2($homepage->getImage(5));
					break;
				case '5-layout/1-top-2-middle-2-bottom':
					$this->setTop1($homepage->getImage(2));
					$this->setTop2($homepage->getImage(4));
					$this->setMiddle1($homepage->getImage(3));
					$this->setMiddle2($homepage->getImage(5));
					$this->setBottom($homepage->getImage(1));
					break;
				case '5-layout/4-left-1-right':
					$this->setRight($homepage->getImage(1));
					$this->setLeft1($homepage->getImage(2));
					$this->setLeft2($homepage->getImage(3));
					$this->setLeft3($homepage->getImage(4));
					$this->setLeft4($homepage->getImage(5));
					break;
				case '5-layout/4-top-1-bottom':
					$this->setBottom($homepage->getImage(1));
					$this->setTop1($homepage->getImage(2));
					$this->setTop2($homepage->getImage(3));
					$this->setTop3($homepage->getImage(4));
					$this->setTop4($homepage->getImage(5));
					break;
				case '5-layout/1-top-2-slider-2-bottom':
					$this->setSlides($homepage->getImages(array('1','2')));
					$this->setTop($homepage->getImage(5));
					$this->setBottom1($homepage->getImage(3));
					$this->setBottom2($homepage->getImage(4));
					break;
				case '5-layout/1-top-4-slider':
					$this->setSlides($homepage->getImages(array('1','2','3','4')));
					$this->setTop($homepage->getImage(5));
					break;
				case '5-layout/2-slider-3-bottom':
					$this->setSlides($homepage->getImages(array('1','2')));
					$this->setBottom1($homepage->getImage(3));
					$this->setBottom2($homepage->getImage(4));
					$this->setBottom3($homepage->getImage(5));
					break;
				case '5-layout/2-top-2-slider-1-bottom':
					$this->setSlides($homepage->getImages(array('1','2')));
					$this->setTop1($homepage->getImage(3));
					$this->setTop2($homepage->getImage(4));
					$this->setBottom($homepage->getImage(5));
					break;
				case '5-layout/2-top-3-slider':
					$this->setSlides($homepage->getImages(array('1','2','3')));
					$this->setTop1($homepage->getImage(4));
					$this->setTop2($homepage->getImage(5));
					break;
				case '5-layout/3-slider-2-bottom':
					$this->setSlides($homepage->getImages(array('1','2','3')));
					$this->setBottom1($homepage->getImage(4));
					$this->setBottom2($homepage->getImage(5));
					break;
				case '5-layout/3-top-2-slider':
					$this->setSlides($homepage->getImages(array('1','2')));
					$this->setTop1($homepage->getImage(3));
					$this->setTop2($homepage->getImage(4));
					$this->setTop3($homepage->getImage(5));
					break;
				case '5-layout/4-slider-1-bottom':
					$this->setSlides($homepage->getImages(array('1','2','3','4')));
					$this->setBottom($homepage->getImage(5));
					break;
				case '5-layout/1-left-1-middle-1-righttop-1-rightmiddle-1-rightbottom':
					$this->setLeft($homepage->getImage(1));
					$this->setMiddle($homepage->getImage(2));
					$this->setRighttop($homepage->getImage(3));
					$this->setRightmiddle($homepage->getImage(4));
					$this->setRightbottom($homepage->getImage(5));
					break;
				case '5-layout/1-left-1-middleleft-1-middle-1-middleright-1-right':
					$this->setLeft($homepage->getImage(1));
					$this->setMiddleleft($homepage->getImage(2));
					$this->setMiddle($homepage->getImage(3));
					$this->setMiddleright($homepage->getImage(4));
					$this->setRight($homepage->getImage(5));
					break;
				case '5-layout/1-left-1-middletop-1-middle-1-middlebottom-1-right':
					$this->setMiddletop($homepage->getImage(1));
					$this->setLeft($homepage->getImage(2));
					$this->setRight($homepage->getImage(3));
					$this->setMiddlebottom($homepage->getImage(4));
					$this->setMiddle($homepage->getImage(5));
					break;
				case '5-layout/1-lefttop-1-leftmiddle-1-leftbottom-1-middle-1-right':
					$this->setLefttop($homepage->getImage(1));
					$this->setLeftmiddle($homepage->getImage(2));
					$this->setLeftbottom($homepage->getImage(3));
					$this->setMiddle($homepage->getImage(4));
					$this->setRight($homepage->getImage(5));
					break;
				case '5-layout/1-top-1-middle-1-bottomleft-1-bottommiddle-1-bottomright':
					$this->setTop($homepage->getImage(1));
					$this->setMiddle($homepage->getImage(2));
					$this->setBottomleft($homepage->getImage(3));
					$this->setBottommiddle($homepage->getImage(4));
					$this->setBottomright($homepage->getImage(5));
					break;
				case '5-layout/1-top-1-middleleft-1-middle-1-middleright-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setMiddleleft($homepage->getImage(2));
					$this->setMiddleright($homepage->getImage(3));
					$this->setBottom($homepage->getImage(4));
					$this->setMiddle($homepage->getImage(5));
					break;
				case '5-layout/1-top-1-middletop-1-middle-1-middlebottom-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setMiddletop($homepage->getImage(2));
					$this->setMiddle($homepage->getImage(3));
					$this->setMiddlebottom($homepage->getImage(4));
					$this->setBottom($homepage->getImage(5));
					break;
				case '5-layout/1-topleft-1-topmiddle-1-topright-1-middle-1-bottom':
					$this->setTopleft($homepage->getImage(1));
					$this->setTopmiddle($homepage->getImage(2));
					$this->setTopright($homepage->getImage(3));
					$this->setMiddle($homepage->getImage(4));
					$this->setBottom($homepage->getImage(5));
					break;
				case '5-layout/1-top-2-middle-1-bottomleft-1-bottomright':
					$this->setTop($homepage->getImage(1));
					$this->setMiddle1($homepage->getImage(2));
					$this->setMiddle2($homepage->getImage(3));
					$this->setBottomleft($homepage->getImage(4));
					$this->setBottomright($homepage->getImage(5));
					break;
				case '5-layout/1-topleft-1-topright-2-middle-1-bottom':
					$this->setTopleft($homepage->getImage(1));
					$this->setTopright($homepage->getImage(2));
					$this->setMiddle1($homepage->getImage(3));
					$this->setMiddle2($homepage->getImage(4));
					$this->setBottom($homepage->getImage(5));
					break;
				case '5-layout/1-top-1-middleleft-1-middleright-1-middlebottom-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setMiddleleft($homepage->getImage(2));
					$this->setMiddleright($homepage->getImage(3));
					$this->setMiddlebottom($homepage->getImage(4));
					$this->setBottom($homepage->getImage(5));
					break;
				case '5-layout/1-top-1-middletop-1-middleleft-1-middleright-1-bottom':
					$this->setTop($homepage->getImage(1));
					$this->setMiddletop($homepage->getImage(2));
					$this->setMiddleleft($homepage->getImage(3));
					$this->setMiddleright($homepage->getImage(4));
					$this->setBottom($homepage->getImage(5));
					break;
				default;
					break;
			}
			
			// Cache data
			$this->addData(array(
				'cache_lifetime'    => 120,
				'cache_tags'        => array(FactoryX_Homepage_Model_Homepage::CACHE_TAG),
				'cache_key'         => $this->makeCacheKey()
			));
		}
    }
	
	/**
	 * Cache key getter
	 */
	public function getCacheKey() 
	{
		if (!$this->hasData('cache_key')) 
		{
			$cacheKey = $this->makeCacheKey();
			$this->setCacheKey($cacheKey);
		}
		return $this->getData('cache_key');
	}	
		
	/**
	 * Cache key setter
	 */
	private function makeCacheKey() 
	{
		// Cache key generated based on the homepage id, the store id and the package name.
		$homepageId = $this->getCurrentHomepage()->getHomepageId();
		$cacheKey = sprintf("HOMEPAGE_%d_%s_%s", Mage::app()->getStore()->getId(), Mage::getSingleton('core/design_package')->getPackageName(), $homepageId);
		// Mage::helper('homepage')->log(sprintf("%s->cacheKey=%s", __METHOD__, $cacheKey));
		return $cacheKey;
	}
	
	/**
	 * Function called before the HTML output
	 */
	public function _beforeToHtml()
    {
		// Retrieve the current home page and use the store id if multistore
		if (!Mage::app()->isSingleStoreMode())
		{
			$storeId = Mage::app()->getStore()->getId();
		}
		else
		{
			$storeId = "";
		}
		
		$homepage = $this->getCurrentHomepage($storeId);
		
		if ($homepage)
		{
			
			// Add sub children based on the layout chosen
			switch ($homepage->getLayout())
			{
				// 2 images layouts
				case '2-layout/1-left-1-right':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					break;
				case '2-layout/1-top-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				case '2-layout/2-slider':
				case '3-layout/3-slider':
				case '4-layout/4-slider':
				case '5-layout/5-slider':
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
					break;
				// 3 images layouts
				case '3-layout/1-left-2-right':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set right children
					$this->setChild('right1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right1'));
					$this->setChild('right2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right2'));
					break;
				case '3-layout/1-top-2-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					break;
				case '3-layout/2-left-1-right':
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					// Set left children
					$this->setChild('left1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left1'));
					$this->setChild('left2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left2'));
					break;
				case '3-layout/2-top-1-bottom':
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set top children
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					break;
				case '3-layout/1-left-1-middle-1-right':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set middle child
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					break;
				case '3-layout/1-top-1-middle-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set middle child
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				case '3-layout/1-top-2-slider':
				case '4-layout/1-top-3-slider':
				case '5-layout/1-top-4-slider':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
					break;	
				case '3-layout/2-slider-1-bottom':
				case '4-layout/3-slider-1-bottom':
				case '5-layout/4-slider-1-bottom':
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
					break;
				// 4 images layouts
				case '4-layout/1-left-3-right':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set right children
					$this->setChild('right1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right1'));
					$this->setChild('right2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right2'));
					$this->setChild('right3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right3'));
					break;
				case '4-layout/1-top-1-bottom-2-bottomright':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set bottom right children
					$this->setChild('bottomright1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottomright1'));
					$this->setChild('bottomright2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottomright2'));
					break;
				case '4-layout/1-top-2-bottomleft-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set bottom left children
					$this->setChild('bottomleft1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottomleft1'));
					$this->setChild('bottomleft2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottomleft2'));
					break;
				case '4-layout/1-top-2-topright-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set top right children
					$this->setChild('topright1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topright1'));
					$this->setChild('topright2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topright2'));
					break;
				case '4-layout/1-top-3-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					$this->setChild('bottom3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom3'));
					break;
				case '4-layout/2-top-2-bottom':
					// Set top children
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					break;
				case '4-layout/2-topleft-1-top-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set top left children
					$this->setChild('topleft1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topleft1'));
					$this->setChild('topleft2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topleft2'));
					break;
				case '4-layout/3-left-1-right':
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					// Set left children
					$this->setChild('left1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left1'));
					$this->setChild('left2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left2'));
					$this->setChild('left3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left3'));
					break;
				case '4-layout/3-top-1-bottom':
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set top children
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					$this->setChild('top3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top3'));
					break;
				case '4-layout/1-left-1-middletop-1-middlebottom-1-right':
					// Set middletop child
					$this->setChild('middletop', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middletop'));
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					// Set middlebottom child
					$this->setChild('middlebottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middlebottom'));
					break;
				case '4-layout/1-left-2-middle-1-right':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					// Set middle children
					$this->setChild('middle1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle1'));
					$this->setChild('middle2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle2'));
					break;
				case '4-layout/1-top-1-middleleft-1-middleright-1-bottom':
					// Set middleleft child
					$this->setChild('middleleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleleft'));
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set middlebottom child
					$this->setChild('middleright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleright'));
					break;
				case '4-layout/1-top-2-middle-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set middle children
					$this->setChild('middle1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle1'));
					$this->setChild('middle2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle2'));
					break;
				case '4-layout/2-slider-2-bottom':
				case '5-layout/3-slider-2-bottom':
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
					break;
				case '4-layout/2-top-2-slider':
				case '5-layout/2-top-3-slider':
					// Set top children
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
					break;
				case '5-layout/1-left-4-right':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set right children
					$this->setChild('right1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right1'));
					$this->setChild('right2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right2'));
					$this->setChild('right3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right3'));
					$this->setChild('right4', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right4'));
					break;
				case '5-layout/1-top-2-middle-2-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set middle children
					$this->setChild('middle1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle1'));
					$this->setChild('middle2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle2'));
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					break;
				case '5-layout/1-top-4-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					$this->setChild('bottom3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom3'));
					$this->setChild('bottom4', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom4'));
					break;
				case '5-layout/2-left-1-middle-2-right':
					// Set middle child
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					// Set left children
					$this->setChild('left1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left1'));
					$this->setChild('left2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left2'));
					// Set right children
					$this->setChild('right1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right1'));
					$this->setChild('right2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right2'));
					break;
				case '5-layout/2-top-1-middle-2-bottom':
					// Set middle child
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					// Set top children
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					break;
				case '5-layout/2-top-2-middle-1-bottom':
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set middle children
					$this->setChild('middle1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle1'));
					$this->setChild('middle2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle2'));
					// Set top children
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					break;
				case '5-layout/4-left-1-right':
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					// Set left children
					$this->setChild('left1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left1'));
					$this->setChild('left2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left2'));
					$this->setChild('left3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left3'));
					$this->setChild('left4', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left4'));
					break;
				case '5-layout/4-top-1-bottom':
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					// Set top children
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					$this->setChild('top3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top3'));
					$this->setChild('top4', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top4'));
					break;
				case '5-layout/1-top-2-slider-2-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					break;
				case '5-layout/2-slider-3-bottom':
					// Set bottom children
					$this->setChild('bottom1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom1'));
					$this->setChild('bottom2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom2'));
					$this->setChild('bottom3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom3'));
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
					break;
				case '5-layout/2-top-2-slider-1-bottom':
					// Set top children
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				case '5-layout/3-top-2-slider':
					// Set top childrend
					$this->setChild('top1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top1'));
					$this->setChild('top2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top2'));
					$this->setChild('top3', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top3'));
					// Set slider child
					$this->setChild('slider', $this->getLayout()->createBlock('homepage/homepage_blocks_slider', 'homepage.slider'));
				case '5-layout/1-left-1-middle-1-righttop-1-rightmiddle-1-rightbottom':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set middle child
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					// Set right children
					$this->setChild('righttop', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.righttop'));
					$this->setChild('rightmiddle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.rightmiddle'));
					$this->setChild('rightbottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.rightbottom'));
					break;
				case '5-layout/1-left-1-middleleft-1-middle-1-middleright-1-right':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set middle children
					$this->setChild('middleleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleleft'));
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					$this->setChild('middleright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleright'));
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					break;
				case '5-layout/1-left-1-middletop-1-middle-1-middlebottom-1-right':
					// Set left child
					$this->setChild('left', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.left'));
					// Set middle children
					$this->setChild('middletop', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middletop'));
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					$this->setChild('middlebottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middlebottom'));
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					break;
				case '5-layout/1-lefttop-1-leftmiddle-1-leftbottom-1-middle-1-right':
					// Set middle child
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					// Set left children
					$this->setChild('lefttop', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.lefttop'));
					$this->setChild('leftmiddle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.leftmiddle'));
					$this->setChild('leftbottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.leftbottom'));
					// Set right child
					$this->setChild('right', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.right'));
					break;
				case '5-layout/1-top-1-middle-1-bottomleft-1-bottommiddle-1-bottomright':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set bottom children
					$this->setChild('bottomleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottomleft'));
					$this->setChild('bottommiddle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottommiddle'));
					$this->setChild('bottomright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottomright'));
					// Set middle child
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					break;
				case '5-layout/1-top-1-middleleft-1-middle-1-middleright-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set middle children
					$this->setChild('middleleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleleft'));
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					$this->setChild('middleright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleright'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				case '5-layout/1-top-1-middletop-1-middle-1-middlebottom-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set middle children
					$this->setChild('middletop', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middletop'));
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					$this->setChild('middlebottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middlebottom'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				case '5-layout/1-topleft-1-topmiddle-1-topright-1-middle-1-bottom':
					// Set middle child
					$this->setChild('middle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle'));
					// Set top children
					$this->setChild('topleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topleft'));
					$this->setChild('topmiddle', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topmiddle'));
					$this->setChild('topright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topright'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				case '5-layout/1-top-2-middle-1-bottomleft-1-bottomright':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set middle children
					$this->setChild('middle1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle1'));
					$this->setChild('middle2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle2'));
					// Set bottom children
					$this->setChild('bottomleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottomleft'));
					$this->setChild('bottomright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottomright'));
					break;
				case '5-layout/1-topleft-1-topright-2-middle-1-bottom':
					// Set top children
					$this->setChild('topleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topleft'));
					$this->setChild('topright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.topright'));
					// Set middle children
					$this->setChild('middle1', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle1'));
					$this->setChild('middle2', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middle2'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				case '5-layout/1-top-1-middleleft-1-middleright-1-middlebottom-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set middle children
					$this->setChild('middleleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleleft'));
					$this->setChild('middleright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleright'));
					$this->setChild('middlebottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middlebottom'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				case '5-layout/1-top-1-middletop-1-middleleft-1-middleright-1-bottom':
					// Set top child
					$this->setChild('top', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.top'));
					// Set middle children
					$this->setChild('middletop', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middletop'));
					$this->setChild('middleleft', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleleft'));
					$this->setChild('middleright', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.middleright'));
					// Set bottom child
					$this->setChild('bottom', $this->getLayout()->createBlock('homepage/homepage_blocks_cell', 'homepage.bottom'));
					break;
				default:
					break;
			}
		}
        
        return $this;
    }
}
?>