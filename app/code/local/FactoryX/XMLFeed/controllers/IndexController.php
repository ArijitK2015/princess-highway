<?php
/**
<availability>1</availability>

// URL
$product = Mage::getModel('catalog/product')->load($productId);
$rewrite = Mage::getModel('core/url_rewrite')->loadByRequestPath($product->getUrlPath());
var_dump(
$rewrite->getTargetPath()
);


get
magiczoom[category][thumb-max-width]
magiczoom[category][thumb-max-height]

 */
class FactoryX_XMLFeed_IndexController extends Mage_Core_Controller_Front_Action {

	protected $imageWidth = 0;
	protected $imageHeight = 0;
	protected $mzSettings = null;
	protected $enabled = true;
	protected $limit = 0;
	protected $brand = "";
	protected $attributes = array();

	private $prodXML = '
	<product type="%PROD_TYPE%">
		<name><![CDATA[%PROD_NAME%]]></name>
		<url>%PROD_URL%</url>
		<sku>%PROD_SKU%</sku>
		<short_desc><![CDATA[%PROD_DESC_SHORT%]]></short_desc>
		<long_desc><![CDATA[%PROD_DESC_LONG%]]></long_desc>
		<images>%PROD_IMG%</images>
		<category><![CDATA[%PROD_CAT%]]></category>
		<sub_category><![CDATA[%PROD_SUBCAT%]]></sub_category>
		<price>%PROD_PRICE%</price>
		<saleprice>%PROD_SALEPRICE%</saleprice>
		<availability>%PROD_AVAIL%</availability>
		%BRAND%
		%PROD_ATTS%
		%PROD_VARS%
	</product>';

	public function catalogAction()
	{
		// Start the time
		$start = microtime(true);

		// Set the header as XML
		$this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');

		// Init the default values
		$this->_initDefaults();

		// Get the product collection
		$collection = Mage::getModel('catalog/product')->getCollection();
		//->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left')
		//->addAttributeToFilter('qty', array('gt' => 0));

		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

		// Test certain products
		//$collection->addAttributeToFilter('sku', array('like'=>array('awl1112803')));

		$collection->addAttributeToFilter('type_id', array('in' => array(
				Mage_Catalog_Model_Product_Type::TYPE_SIMPLE, Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
			)
		);

		$collection->addAttributeToSelect('*');

		// Additional attributes
		if (is_array($this->attributes) && count($this->attributes)) {
			foreach($this->attributes as $attribute) {
				//Mage::helper('xmlfeed')->log(sprintf("%s->attribute=%s", __METHOD__, $attribute));
				$collection->addAttributeToSelect($attribute);
			}
		}

		$collection->addFieldToFilter('status', array('eq'=>Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
		$collection->addFieldToFilter('visibility', array('eq'=>Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH));

		$labels = array("front","back","swatch");

		$xml =  "";
		$xml .= sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
		$xml .= sprintf("<product_list created='%s'>", date('c'));
		$cnt = 0;
		foreach ($collection as $product) {
			$node = $this->prodXML;

			$node = str_replace("%PROD_TYPE%", $product->getTypeId(), $node);
			$name = $product->getName();

			$cats = $product->getCategoryIds();
			foreach ($cats as $catId) {
				$catCollection = Mage::getResourceModel('catalog/category_collection')
					->addFieldToFilter('entity_id', array($catId))
					->addAttributeToSelect(array('level','name','parent_id'))
					->setPageSize(1);

				$cat = $catCollection->getFirstItem();
				//$cat->getCategoryUrl();
				//Mage::helper('xmlfeed')->log(sprintf("%s->cat=%s,level=%d", __METHOD__, $cat->getName(), $cat->getLevel()));
				$hasLevel2 = false;
				$hasLevel3 = false;
				if ($cat->getLevel() == 2) {
					$hasLevel2 = true;
					$node = str_replace("%PROD_CAT%", htmlentities($cat->getName()), $node);
				}
				if ($cat->getLevel() == 3) {
					$hasLevel3 = true;
					// get category parent
					if (!$hasLevel2) {
						$pCollection = Mage::getResourceModel('catalog/category_collection')
							->addFieldToFilter('entity_id', array($cat->getParentId()))
							->addAttributeToSelect(array('name'))
							->setPageSize(1);

						$pCat = $pCollection->getFirstItem();
						$node = str_replace("%PROD_CAT%", htmlentities($pCat->getName()), $node);
					}
					$node = str_replace("%PROD_SUBCAT%", htmlentities($cat->getName()), $node);
				}
				// no subcat attempt to place one
				if (!$hasLevel3) {
					if (preg_match("/(dress|dresses|frock|frocks)/i", $name)) {
						$node = str_replace("%PROD_SUBCAT%", "Dresses", $node);
					}
					else if (preg_match("/(cardi|cardigan)/i", $name)) {
						$node = str_replace("%PROD_SUBCAT%", "Knitwear", $node);
					}
					else if (preg_match("/(blouse|top|cami)/i", $name)) {
						$node = str_replace("%PROD_SUBCAT%", "Tops", $node);
					}
					else if (preg_match("/(skirt)/i", $name)) {
						$node = str_replace("%PROD_SUBCAT%", "Skirts", $node);
					}
					else if (preg_match("/(pant|pants|short|shorts)/i", $name)) {
						$node = str_replace("%PROD_SUBCAT%", "Pants & Shorts", $node);
					}
					else if (preg_match("/(jacket|coat)/i", $name)) {
						$node = str_replace("%PROD_SUBCAT%", "Jackets & Coats", $node);
					}
					else {
						$node = str_replace("%PROD_SUBCAT%", "Other", $node);
					}
				}
			}

			$name = html_entity_decode($product->getName(), ENT_QUOTES , 'UTF-8');
			$desc = html_entity_decode($product->getDescription(), ENT_QUOTES , 'UTF-8');
			$longDesc = html_entity_decode($product->getShortDescription(), ENT_QUOTES, 'UTF-8');

			$node = str_replace("%PROD_NAME%", $name, $node);
			$node = str_replace("%PROD_URL%", $product->getProductUrl($useSid = false), $node);
			$node = str_replace("%PROD_SKU%", $product->getSku(), $node);
			$node = str_replace("%PROD_DESC_SHORT%", $desc, $node);
			$node = str_replace("%PROD_DESC_LONG%", $longDesc, $node);
			//$node = str_replace("%PROD_DESC_SHORT%", $product->getData('description'), $node);
			//$node = str_replace("%PROD_DESC_LONG%", $product->getShortDescription(), $node);
			//$node = str_replace("%PROD_IMG%", $product->getImageUrl(), $node);

			if ($this->brand && strlen($this->brand)) {
				$brand = sprintf("<brand>%s</brand>", $this->brand);
				$node = str_replace("%BRAND%", $brand, $node);
			}
			else {
				$node = str_replace("%BRAND%", "", $node);
			}

			if (is_array($this->attributes) && count($this->attributes)) {
				$prodAtts = "";
				foreach($this->attributes as $attribute) {
					// TODO: get type and set default values
					$val = "0";
					if ($product->getData($attribute)) {
						$val = $product->getData($attribute);
					}
					$att = sprintf("<%s>%s</%s>", $attribute, $val, $attribute);
					//Mage::helper('xmlfeed')->log(sprintf("%s->att=%s", __METHOD__, $att));
					$prodAtts .= $att;
				}
				$node = str_replace("%PROD_ATTS%", $prodAtts, $node);
			}
			else {
				$node = str_replace("%PROD_ATTS%", "", $node);
			}

			/**
			front_<colour>
			back_<colour>
			swatch_<colour>
			detail_<colour>
			 */
			$imageXml = "";
			$aImages = array();
			$images = Mage::getModel('catalog/product')->load($product->getId())->getMediaGalleryImages();
			//Mage::helper('xmlfeed')->log(sprintf("%s->count=%s", __METHOD__, count($images)));
			if (count($images)) {
				$i = 0;
				foreach($images as $image) {
					// init image for getOriginalSizeArray
					Mage::helper('catalog/image')->init($product, 'image');

					//Mage::helper('xmlfeed')->log(sprintf("%s->image=%s", __METHOD__, $image->getFile()));
					if ($this->mzSettings) {
						//$sizes = Mage::helper('catalog/image')->getOriginalSizeArray();
						$sizes = array(1060,1200);

						//Mage::helper('xmlfeed')->log(sprintf("%s->sizes=%s", __METHOD__, print_r($sizes, true) ));
						list($w, $h) = $this->mzSettings->magicToolboxGetSizes('thumb', $sizes);
						//Mage::helper('xmlfeed')->log(sprintf("%s->sizes=%s,%s", __METHOD__, $w, $h ));
					}
					else {
						$w = $this->imageWidth;
						$h = $this->imageHeight;
					}

					// does not return cached version
					//$url = $product->getMediaConfig()->getMediaUrl($image->getFile());
					$url = Mage::helper('catalog/image')->init($product, 'small_image', $image->getFile())->resize($w, $h)->__toString();

					$label = preg_split("/_/", $image->getLabel());
					//Mage::helper('xmlfeed')->log(sprintf("%s->image:%s,%s", __METHOD__, $image->getLabel(), $url));

					$noLabel = true;
					if (count($label) > 1) {
						$pos = preg_replace("/\s/", "", $label[0]);
						$col = preg_replace("/(\s|-)/", "_", $label[1]);
						if (preg_match("/(front|back|side|swatch|detail)/", $pos)) {
							$aImages["$col"]["$pos"] = $url;
							$noLabel = false;
						}
						elseif (preg_match("/(inside1|inside2)/", $pos)) {
							$pos = "detail";
							$aImages["$col"]["$pos"] = $url;
							$noLabel = false;
						}
					}
					// no labels?
					if ($noLabel && $i < count($labels)) {
						Mage::helper('xmlfeed')->log(sprintf("Error: %s->no label for product [%s:%s] image: %s", __METHOD__, $product->getSku(), print_r($label, true), $url));
						//$aImages["default"][$labels[$i]] = $url;
					}
					$i++;
				}
				//Mage::helper('xmlfeed')->log(sprintf("aImages=%s", var_export($aImages, true)));
				foreach($aImages as $key => $val) {
					//Mage::helper('xmlfeed')->log(sprintf("aImages=%s,%s", $key, print_r($val, true)));
					$imageXml .= sprintf("<image>");
					$imageXml .= sprintf("<color>%s</color>", $key);
					foreach($val as $key2 => $val2) {
						$imageXml .= sprintf("<%s><![CDATA[%s]]></%s>", $key2, $val2, $key2);
					}
					$imageXml .= sprintf("</image>");
				}
				//Mage::helper('xmlfeed')->log(sprintf("imageXml=%s", $imageXml));
			}

			$node = str_replace("%PROD_IMG%", $imageXml, $node);
			$node = str_replace("%PROD_PRICE%", $product->getPrice(), $node);

			//$salePrice = $product->getSpecialPrice();
			/*
			// getFinalPrice applies the rules, or alternatively
			$store_id = 1; // Use the default store
            $salePrice = Mage::getResourceModel('catalogrule/rule')->getRulePrice(
                        Mage::app()->getLocale()->storeTimeStamp($store_id),
                        Mage::app()->getStore($store_id)->getWebsiteId(),
                        Mage::getSingleton('customer/session')->getCustomerGroupId(),
                        $product->getId());
            */
			$finalPrice = $product->getFinalPrice();
			$salePrice = "";
			if (!empty($finalPrice) && $finalPrice != 0 && $finalPrice != $product->getPrice()) {
				$salePrice = $finalPrice;
			}
			$node = str_replace("%PROD_SALEPRICE%", $salePrice, $node);

			// get colour and size variations
			//if ($product->isConfigurable()) {
			if ($product->getTypeId() == "configurable") {

				//$aProducts = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
				$aProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
				$qtyStockTotal = 0;
				foreach($aProducts as $aProduct) {
					$qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($aProduct)->getQty();
					$qtyStockTotal += $qtyStock;
					//Mage::helper('xmlfeed')->log(sprintf("%s->inventory=%d", __METHOD__, $qtyStock));
				}
				$node = str_replace("%PROD_AVAIL%", $qtyStockTotal, $node);

				$colors = $this->getAvailableColourAndSize($product);
				//var_dump($product->getData());

				$var1 = "";
				$var2 = "";
				foreach($colors as $color => $sizes) {
					$var1 .= sprintf("<option><color>%s</color><sizes> ", $color);
					foreach($sizes as $size => $avail) {
						$var1 .= sprintf("<size available='%d'>%s</size>", $avail, $size);
					}
					$var1 .= sprintf("</sizes></option>", $color);
				}
				$var2 .= sprintf("<options>%s</options>", $var1);
				$node = str_replace("%PROD_VARS%", $var2, $node);

				/*
				$sizes = $this->getAvailableSize($product);
				$colors = $this->getAvailableColour($product);

				$var1 = "";
				$var2 = "";
				foreach($sizes as $size => $avail) {
					//$var1 .= sprintf("<size availability='%d'>%s</size>", $avail, $size);
					$var1 .= sprintf("<size>%s</size>", $size);
				}
				$var2 .= sprintf("<sizes>%s</sizes>", $var1);
				$var1 = "";
				foreach(array_keys($colors) as $color) {
					$var1 .= sprintf("<color>%s</color>", $color);
				}
				$var2 .= sprintf("<colors>%s</colors>", $var1);
				$node = str_replace("%PROD_VARS%", $var2, $node);
				*/

			}
			//elseif ($product->isSimple()) {
			elseif ($product->getTypeId() == "simple") {
				$qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
				//Mage::helper('xmlfeed')->log(sprintf("%s->inventory=%d", __METHOD__, $qtyStock));
				$node = str_replace("%PROD_AVAIL%", $qtyStock, $node);
				// remove place holder
				$node = str_replace("%PROD_VARS%", "", $node);
			}
			$xml .= sprintf("%s", $node);
			$cnt++;
			if ($this->limit != 0 && $cnt >= $this->limit) {
				break;
			}
		}
		$xml .= sprintf("</product_list>");
		//echo sprintf("%s", $xml);
		$this->getResponse()->setBody($xml);

		$elapsed = microtime(true) - $start;
		//Mage::helper('xmlfeed')->log(sprintf("%s->elapsed=%d", __METHOD__, $elapsed));
	}

	/**
	 * @param $product
	 * @return array
	 */
	private function getAvailableColourAndSize($product)
	{
		$aColourAndSizes = array();
		$allProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);
		foreach ($allProducts as $subproduct) {
			//Mage::helper('xmlfeed')->log(sprintf("%s->product=%d", __METHOD__, $subproduct->getId()));
			if ($subproduct->isSaleable()) {
				$attributes = $subproduct->getAttributes();
				$color = "";
				foreach ($attributes as $attribute) {
					if (preg_match("/colour/i", $attribute->getName())) { // && $attribute->getIsVisibleOnFront()) {
						$color = trim($subproduct->getAttributeText($attribute->getName()));
						//$qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($subproduct)->getQty();
						//Mage::helper('xmlfeed')->log(sprintf("%s->inventory=%d", __METHOD__, $qtyStock));
						if (!array_key_exists($color, $aColourAndSizes)) {
							$aColourAndSizes["$color"] = array();
						}
					}
				}
				foreach ($attributes as $attribute) {
					if (preg_match("/(?!size_and_fit)size/i", $attribute->getName())) { // && $attribute->getIsVisibleOnFront()) {
						$size = trim($subproduct->getAttributeText($attribute->getName()));
						$qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($subproduct)->getQty();
						//Mage::helper('xmlfeed')->log(sprintf("%s->color=%s,size=%s,stock=%d", __METHOD__, $color, $size, $qtyStock));
						$aColourAndSizes["$color"]["$size"] = $qtyStock;
					}
				}
			}
		}
		//Mage::helper('xmlfeed')->log(sprintf("aColourAndSizes=%s", var_export($aColourAndSizes, true)));
		return $aColourAndSizes;
	}

	/**
	 *
	 */
	protected function _initDefaults()
	{
		// Default image sizes
		$this->imageWidth = 224;
		$this->imageHeight = 254;

		// Load MagicToolbox_MagicZoom settings
		if ((string)Mage::getConfig()->getModuleConfig('MagicToolbox_MagicZoom')->active == 'true') {
			$this->mzSettings = Mage::helper('magiczoom/settings');
			//$tool = $this->mzSettings->loadTool('category');
		}

		$this->limit = (int)Mage::app()->getRequest()->getParam('limit', 0);
		// check config
		if ($this->limit == 0) {
			$this->limit = Mage::helper('xmlfeed')->getLimit();
		}
		//Mage::helper('xmlfeed')->log(sprintf("%s->limit=%d", __METHOD__, $this->limit));

		$this->brand = Mage::helper('xmlfeed')->getBrand();

		$this->attributes = Mage::helper('xmlfeed')->getAdditionalAttributes();
		//Mage::helper('xmlfeed')->log(sprintf("%s->attributes=%a", __METHOD__, print_r($this->attributes, true)) );
	}


	/**
	 * @param $product
	 * @return array
	 */
	private function getAvailableSize($product)
	{
		$aSizes = array();
		$allProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);
		foreach ($allProducts as $subproduct) {
			if ($subproduct->isSaleable()) {
				$attributes = $subproduct->getAttributes();
				foreach ($attributes as $attribute) {
					if (preg_match("/(?!size_and_fit)size/i", $attribute->getName())) { // && $attribute->getIsVisibleOnFront()) {
						$size = trim($subproduct->getAttributeText($attribute->getName()));
						$qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($subproduct)->getQty();
						//Mage::helper('xmlfeed')->log(sprintf("%s->inventory=%d", __METHOD__, $qtyStock));
						$aSizes[$size] = $qtyStock;
					}
				}
			}
		}
		return $aSizes;
	}

	/**
	 * @param $product
	 * @return array
	 */
	private function getAvailableColour($product)
	{
		$aColors = array();
		$allProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);
		foreach ($allProducts as $subproduct) {
			if ($subproduct->isSaleable()) {
				$attributes = $subproduct->getAttributes();
				foreach ($attributes as $attribute) {
					if (preg_match("/colour/i", $attribute->getName())) { // && $attribute->getIsVisibleOnFront()) {
						$color = trim($subproduct->getAttributeText($attribute->getName()));
						$aColors[$color] = $color;
					}
				}
			}
		}
		return $aColors;
	}

}