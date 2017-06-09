<?php
/**
 *	That is the frontend lookbook block
 */
class FactoryX_Lookbook_Block_Lookbook extends Mage_Core_Block_Template
{
	public $_lookbookId;
	public $_currentLookbook;
	
	// Configurable attributes
	public $_lookBundleWindowWidth = 1003;
	public $_lookbookWidth = 1120;
	public $_looksPerPage = 4;
	public $_lookBorder = 0;
	public $_lookWidth = 240;
	public $_lookHeight = 550;
	public $_lookColor = "black"; // black | white
	public $_lookDescTopPadding = 5;
	public $_lookDescMaxLines = 6;
	public $_lookDescHeight = 90; // allows for 6 lines at 11px (truncated if >)
	public $_lookScaleHeight = 45;
	public $_lookDescBorderWidth = 0;
	public $_lookDescSidePadding = 10;
	public $_barWidth = 2;
	public $_scaleLeftPad = 30;	// Scale left padding
	
	public $_lookbookNavTyoe = "";
		
	/**
	 *	Getter for _lookBundleWindowWidth
	 */
	public function getLookBundleWindowWidth() {
	    $this->_lookBundleWindowWidth = 1003;
		if ($this->getCurrentLookbook()->getLookBundleWindowWidth()) {
			$this->_lookBundleWindowWidth = $this->getCurrentLookbook()->getLookBundleWindowWidth();
		}
		return $this->_lookBundleWindowWidth;
	}

		
	/**
	 *	Getter for the lookbook width
	 */
	public function getLookbookWidth()
	{
        // For Magento version before RWD
        if (version_compare(Mage::getVersion(),"1.9.1.0","<"))
        {
            $this->_lookbookWidth = 980;
        }
		if ($this->getCurrentLookbook()->getLookbookWidth()) {
			$this->_lookbookWidth = $this->getCurrentLookbook()->getLookbookWidth();
		}
		return $this->_lookbookWidth;
	}
	
	/**
	 *	Getter for the looks per page
	 */
	public function getLooksPerPage()
	{
		if ($this->getCurrentLookbook()->getLooksPerPage()) {
			$this->_looksPerPage = $this->getCurrentLookbook()->getLooksPerPage();
		}
		
		return $this->_looksPerPage;
	}

	/**
	 *	Getter for the lookbook nav type

	public function getLookbookNavType()
	{
		if ($this->getCurrentLookbook()->getLookbookNavType()) {
			$this->_lookbookNavType = $this->getCurrentLookbook()->getLookbookNavType();
		}
		
		return $this->_lookbookNavType;
	}
	 */
	 	
	/**
	 *	Getter for the lookbook border
	 */
	public function getLookbookBorder()
	{
		if ($this->getCurrentLookbook()->getLookbookBorder()) {
			$this->_lookBorder = $this->getCurrentLookbook()->getLookbookBorder();
		}
		
		return $this->_lookBorder;
	}
	
	/**
	 *	Getter for the look width
	 */
	public function getLookWidth()
	{
		// If width is set via the lookbook
		if ($this->getCurrentLookbook()->getLookWidth()) {
			$this->_lookWidth = $this->getCurrentLookbook()->getLookWidth();
		}
		elseif ($this->getLooksPerPage() != 4 && $this->getLooksPerPage() != "auto")
		{
			// If the look per page is not 4 then we recalculate the look width
			$this->_lookWidth = $this->getLookbookWidth() / $this->getLooksPerPage();
		}
		
		return $this->_lookWidth;
	}
	
	/**
	 *	Getter for the look height
	 */
	public function getLookHeight() {
		// If height is set via the lookbook
		if ($this->getCurrentLookbook()->getLookHeight()) {
			$this->_lookHeight = $this->getCurrentLookbook()->getLookHeight();
		}
		else {
			// Calculate the look height based on the first image dimensions
			$dimensions = Mage::helper('lookbook')->calculateLookDimensions($this->getCurrentLookbook());
			$this->_lookHeight = $this->getLookWidth() * $dimensions['ratio'];
		}
		//Mage::helper('lookbook')->log(sprintf("%s->lookHeight=%s", __METHOD__, $this->_lookHeight) );
		return $this->_lookHeight;
	}
	
	/**
	 *	Getter for the look color
	 */
	public function getLookColor()
	{
		if ($this->getCurrentLookbook()->getLookColor()) {
			$this->_lookColor = $this->getCurrentLookbook()->getLookColor();
		}
		
		return $this->_lookColor;
	}
	
	/**
	 *	Getter for the look description top padding
	 */
	public function getLookDescTopPadding()
	{
		if ($this->getCurrentLookbook()->getLookDescriptionPadding()) {
			$this->_lookDescTopPadding = $this->getCurrentLookbook()->getLookDescriptionPadding();
		}
		
		return $this->_lookDescTopPadding;
	}
	
	/**
	 *	Getter for the look description maximum lines
	 */
	public function getLookDescMaxLines()
	{
		if ($this->getCurrentLookbook()->getLookDescriptionMaxLines()) {
			$this->_lookDescMaxLines = $this->getCurrentLookbook()->getLookDescriptionMaxLines();
		}
		
		return $this->_lookDescMaxLines;
	}
	
	/**
	 *	Getter for the look description height
	 */
	public function getLookDescHeight()
	{
		if ($this->getCurrentLookbook()->getShowShopPix())
		{
			// If we show the products links we need to increase the desc height by 15px per line
			if ($this->getCurrentLookbook()->getShowChildProductsLink())
			{
				return ($this->_lookDescHeight + ($this->_lookDescMaxLines * 15));
			}
			elseif ($this->getCurrentLookbook()->getLookDescriptionHeight()) {
				$this->_lookDescHeight = $this->getCurrentLookbook()->getLookDescriptionHeight();
			}
		}
		else
		{
			// If we show the products links we need to increase the desc height by 15px per line
			if ($this->getCurrentLookbook()->getShowChildProductsLink())
			{
				return ($this->_lookDescMaxLines * 15);
			}
			elseif ($this->getCurrentLookbook()->getLookDescriptionHeight()) {
				$this->_lookDescHeight = $this->getCurrentLookbook()->getLookDescriptionHeight();
			}
			else
			{
				$this->_lookDescHeight = 0;
			}
		}

		return $this->_lookDescHeight;
	}
	
	/**
	 *	Getter for the look scale height
	 */
	public function getLookScaleHeight()
	{
		if ($this->getCurrentLookbook()->getLookScaleHeight()) {
			$this->_lookScaleHeight = $this->getCurrentLookbook()->getLookScaleHeight();
		}
		
		return $this->_lookScaleHeight;
	}
	
	/**
	 *	Getter for the look description border width
	 */
	public function getLookDescBorderWidth()
	{
		if ($this->getCurrentLookbook()->getLookDescriptionBorderWidth()) {
			$this->_lookDescBorderWidth = $this->getCurrentLookbook()->getLookDescriptionBorderWidth();
		}
		
		return $this->_lookDescBorderWidth;
	}
	
	/**
	 *	Getter for the look description side padding
	 */
	public function getLookDescSidePadding()
	{
		if ($this->getCurrentLookbook()->getLookDescriptionSidePadding()) {
			$this->_lookDescSidePadding = $this->getCurrentLookbook()->getLookDescriptionSidePadding();
		}
		
		return $this->_lookDescSidePadding;
	}
	
	/**
	 *	Getter for the bar width
	 */
	public function getBarWidth()
	{
		if ($this->getCurrentLookbook()->getBarWidth()) {
			$this->_barWidth = $this->getCurrentLookbook()->getBarWidth();
		}
		
		return $this->_barWidth;
	}
	
	/**
	 *	Getter for the scale left padding
	 */
	public function getScaleLeftPad()
	{
		if ($this->getCurrentLookbook()->getLookScaleSidePadding()) {
			$this->_scaleLeftPad = $this->getCurrentLookbook()->getLookScaleSidePadding();
		}
		
		return $this->_scaleLeftPad;
	}
	
	/**
	 *	Retrieve the current lookbook for the frontend
     * @return FactoryX_Lookbook_Model_Lookbook
     */
    public function getCurrentLookbook() {
        try {
            if ($this->_currentLookbook) {
                return $this->_currentLookbook;
            }

            if(!$this->_lookbookId)
            {
                if (stripos($this->getRequest()->getControllerModule(), 'catalog')) {
                    $this->_currentLookbook = Mage::getModel('lookbook/lookbook')->load($this->getRequest()->getParam('id'), 'category_id');
                    $this->_lookbookId = $this->_currentLookbook->getId();
                    return $this->_currentLookbook;
                }else {
                    $this->_lookbookId = $this->getRequest()->getParam('id');
                }
            }

            if(!$this->_lookbookId)
            {
                $this->_lookbookId = $this->getID();
            }
            //Mage::helper('lookbook')->log(sprintf("%s->_lookbookId=%s", __METHOD__, $this->_lookbookId) );
			// Load lookbook based on the given id
			$this->_currentLookbook = Mage::getModel('lookbook/lookbook')->load($this->_lookbookId);
			
			// Ensure the lookbook is viewable in the store
			if (!Mage::app()->isSingleStoreMode()) 
			{
				if ($this->_currentLookbook->isStoreViewable()) {
					return $this->_currentLookbook;
				}
				else {
					throw new Exception ('This lookbook is not available with this store');
				}
			}
			else {
				return $this->_currentLookbook;
			}
		}
		catch (Exception $e) {
			Mage::helper('lookbook')->log($this->__('Exception caught in %s under % with message: %s', __FILE__, __FUNCTION__, $e->getMessage()));
			Mage::getSingleton('customer/session')->addError($this->__("There was a problem loading the lookbook: %s", $e->getMessage()));
			$this->_redirectReferer();
			return false;
		}
    }

    protected function _construct()
    {
        $this->addData(array(
            'cache_lifetime' => 86400,
            'cache_tags'     => array(FactoryX_Lookbook_Model_Lookbook::CACHE_TAG),
            'cache_key'      => $this->makeCacheKey(get_class($this))
        ));
    }

	/**
	 * @return mixed
     */
	public function getCacheKey()
    {
        if (!$this->hasData('cache_key')) {
            $cacheKey = $this->makeCacheKey(get_class($this));
            $this->setCacheKey($cacheKey);
        }
        return $this->getData('cache_key');
    }

	/**
	 * @param $className
	 * @return string
     */
	protected function makeCacheKey($className)
    {
        $lookbookId = $this->getCurrentLookbook()->getLookbookId();
        $cacheKey  = sprintf("%s_%d_%s_%s", $className, Mage::app()->getStore()->getId(), Mage::getSingleton('core/design_package')->getPackageName(), $lookbookId);
        return $cacheKey;
    }


}