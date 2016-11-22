<?php

/**
 * Class FactoryX_ColorMapping_Helper_Data
 */
class FactoryX_ColorMapping_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $logFileName = 'factoryx_colormapping.log';

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data)
	{
		Mage::log($data, null, $this->logFileName);
	}

	/**
	 * Check if enable
	 * @return mixed
	 */
	public function isEnable()
	{
		return Mage::getStoreConfig('colormapping/options/enable');
	}

	/**
	 * Get the base colour configured
	 * @return mixed
	 */
	public function getBaseColour()
	{
		return Mage::getStoreConfig('colormapping/options/base_colour');
	}

	/**
	 * Get the colour configured
	 * @return mixed
	 */
	public function getColour()
	{
		return Mage::getStoreConfig('colormapping/options/colour');
	}

	/**
	 * Generate mapping
	 * @param null $baseColor
	 * @return array
	 */
	public function generateMapping($baseColor = null)
	{
		$mappings = unserialize(
		    Mage::getStoreConfig('colormapping/options/mappedcolors',
            Mage::app()->getStore()->getStoreId())
		);
		$this->log(sprintf("%s->mappings=%d", __METHOD__, count($mappings)) );

		$result = null;
		if (
		    $mappings
			&&
			is_array($mappings)
		) {
			$result = array();
			foreach($mappings as $mapping) {
			    //$this->log(sprintf("%s->mapping=%s", __METHOD__, print_r($mapping, true)) );
			    if (is_array($result[$mapping["basecolors"]])) {
			        $result[$mapping["basecolors"]] = array_merge($result[$mapping["basecolors"]], array($mapping["colors"]));
			    }
			    else {
				    $result[$mapping["basecolors"]] = array();
				    $result[$mapping["basecolors"]][] = $mapping["colors"];
				}
			}

			// If a base color is provided we try to find it
			if ($baseColor && array_key_exists($baseColor, $result)) {
			    //$this->log(sprintf("%s->baseColor: %s|%d", __METHOD__, $baseColor, count($result[$baseColor])) );
				$result = $result[$baseColor];
			}
			else {
				$result[$baseColor] = 0;
			}
		}

		return $result;
	}

	/**
	 * Get the matching labels
	 * @param $baseColor
	 * @return array
	 */
	public function getPossibleMatchingLabels($baseColor)
	{
	    //$this->log(sprintf("%s->baseColor=%s", __METHOD__, print_r($baseColor, true)) );
		$mapping = $this->generateMapping($baseColor);
		//$this->log(sprintf("%s->mapping=%s", __METHOD__, print_r($mapping, true)) );
		return $mapping;
	}

	/**
	 * Get the filtered color
	 * @return null
	 */
	public function getFilteredColor()
	{
		if ($this->isEnable()) {
			$uri = $_SERVER['REQUEST_URI'];
			$arr = parse_url($uri);
			if (array_key_exists('query', $arr)) {
				// Cut query
				parse_str($arr['query'], $arr);
				if (array_key_exists($this->getBaseColour(), $arr)) {
					if ((string)Mage::getConfig()->getModuleConfig('AW_Layerednavigation')->active == 'true'
						&& Mage::helper('aw_layerednavigation/config')->isEnabled())
					{
						$awFilter = Mage::getResourceModel('aw_layerednavigation/filter_option_collection')
							->addFieldToSelect(array('additional_data'))
							->addFieldToFilter('option_id',$arr[$this->getBaseColour()])
							->setPageSize(1);

						$additionalData = $awFilter->getFirstItem()->getAdditionalData();
						return $additionalData['option_id'];
					} else {
						return $arr[$this->getBaseColour()];
					}
				}
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	/**
	 * Get the matching images
	 * @param $product
	 * @param $potentialColors
	 * @return array
	 */
	public function getMatchingImages($product, $potentialColors)
	{
	    if (!$potentialColors) {
	        $potentialColors = array();
	    }
	    //$this->log(sprintf("potentialColors=%s", print_r($potentialColors, true)));
	    
		foreach ($potentialColors as $potentialColorId) {
			// Get the attribute id for the colour
			$attributeId = Mage::getModel('eav/entity_attribute')
				->getIdByCode("catalog_product", $this->getColour());

			// Get the collection of values
			$_collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
				->setAttributeFilter($attributeId)
				->addFieldToFilter(
					'tdv.option_id',
					array('eq' => $potentialColorId)
				)
				->setStoreFilter(Mage::app()->getStore()->getStoreId())
				->load()
				->getFirstItem();

			// Get the potential color
			$potentialColor = $_collection->getStoreDefaultValue();

			// Generate the corresponding label
			$label = "front_".strtolower($potentialColor);

			// Load the product if no gallery
			/** @TODO avoid loading */
			if (!($gallery = $product->getMediaGalleryImages())) {
				$backendModel = $product->getResource()->getAttribute('media_gallery')->getBackend();
				$backendModel->afterLoad($product);
				$gallery = $product->getMediaGalleryImages();
			}

			// Get the gallery entry for the label
			$_image = $gallery->getItemByColumnValue('label', $label);

			// If image found
			if ($_image) {
				return array(
					true,
					strtolower($potentialColor),
					$_image->getFile(),
					$_image->getFile()
				);
			}
		}
		return array(false,"",null,null);
	}

	/**
	 * getMatchingBackImage
	 * gets an image by label
	 *
	 * @param $product
	 * @param $color
	 * @return array
	 */
	public function getMatchingBackImage($product, $color)
	{
		if (version_compare(Mage::getVersion(),"1.9.1.0","<")) {
			// front_white    
			if (preg_match("/_/", $color)) {
				$parts = preg_split("/_/", $color);
				$color = $parts[1];
			}
		} else {
			// white-front
			if (preg_match("/-/", $color)) {
				$parts = preg_split("/-/", $color);
				$color = $parts[0];
			}
		}
		//$this->log(sprintf("%s->product:%d:%s=color: %s", __METHOD__, $product->getId(), $product->getSku(), $color) );
		if (version_compare(Mage::getVersion(),"1.9.1.0","<")) {
			$label = sprintf("back_%s", strtolower($color));
		} else {
			$label = sprintf("%s-back", strtolower($color));
		}

		if (!($gallery = $product->getMediaGalleryImages())) {
			$backendModel = $product->getResource()->getAttribute('media_gallery')->getBackend();
			$backendModel->afterLoad($product);
			$gallery = $product->getMediaGalleryImages();
		}
		//$this->log(sprintf("%s->label: %s", __METHOD__, $label) );
		$_image = $gallery->getItemByColumnValue('label', $label);
		$retVal = array(false,null,null);
		if ($_image) {
			$_image_file = $_image->getFile();
			$_small_image_file = $_image->getFile();
			$retVal = array(true,$_image_file,$_small_image_file);
		}
		//$this->log(sprintf("%s->retVal: %s", __METHOD__, print_r($retVal, true)) );
		return $retVal;
	}

}
