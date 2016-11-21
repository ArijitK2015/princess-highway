<?php
/**
 * Ajax controller to update the available sizes and the price
 */

class FactoryX_ProductRefresh_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * indexAction
     *
     * outputs json config based on the query string params
     *
     * example request
     * /productrefresh/index?product_id=26428&colour_all=29
     *
     * example json response
     * {"sizes":["6","8","10","12","14","16"],"prices":{"29":null},"units":[],"extra":[],"status":"SUCCESS"}
     */
	public function indexAction() {

		$response = array();
		$colourParam = null;

		$sizeAttributeId = null;
		$sizeAttributeCode = null;

		$colourAttributeId = null;
		$colourAttributeCode = null;

		// get the color parameter from the query string
		$params = $this->getRequest()->getParams();
		//Mage::helper('productrefresh')->log(sprintf("%s->params=%s", __METHOD__, print_r($params, true)) );
		foreach($params as $key => $val) {
		    if (preg_match("/(color|colour)/", $key)) {
		        $colourParam = $key;
		    }
		}
		//Mage::helper('productrefresh')->log(sprintf("%s->colourParam=%s", __METHOD__, $colourParam) );

		// load the parent product if the product_id has been given
		if (!isset($params['product_id'])) {
			$response['status'] = "ERROR: product id unspecified";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		$product = Mage::getModel('catalog/product')->load($params['product_id']);

		// check if the product is configurable
		if ($product->getTypeId() == "configurable") {
			$isConfigurable = true;
			$associatedProducts = $product->getTypeInstance()->getUsedProducts();
		}
		else {
			$response['status'] = "ERROR: product is not configurable";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}

		// responses array
		$response['sizes'] = array();
		$response['prices'] = array();
		$response['units'] = array();
		$response['extra'] = array();

		// get the attributes of the product
		$attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

		// if the attributes array is not loaded or empty
		if (!$attributes || empty($attributes)) {
			$response['status'] = "ERROR: attributes are not loaded";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}

        // get the size and colour attribute from the product
		foreach ($attributes as $attr) {
		    //Mage::helper('productrefresh')->log(sprintf("%s->attr=%s", __METHOD__, print_r($attr, true)) );

			// if we have found the size
			if (preg_match("/size/i", $attr['label'])) {
				$sizeAttributeId = $attr['attribute_id'];
				$sizeAttributeCode = $attr['attribute_code'];
				//Mage::helper('productrefresh')->log(sprintf("%s->size: %s=%s", __METHOD__, $sizeAttributeCode, $sizeAttributeId) );
			}
			// if we have found the colour (use label OR attribute_code)
			if (preg_match("/(colour|color)/i", $attr['attribute_code'])) {
				$colourAttributeId = $attr['attribute_id'];
				$colourAttributeCode = $attr['attribute_code'];
                //Mage::helper('productrefresh')->log(sprintf("%s->colour: %s=%s", __METHOD__, $colourAttributeCode, $colourAttributeId) );
				foreach ($attr['values'] as $value) {
					// load the extra prices depending on the colour
					$response['prices'][$value['value_index']] = Mage::helper('core')->currency($value['pricing_value'], false, false);
				}
			}
		}

		if (isset($sizeAttributeId) && $isConfigurable) {
			// fetch the associated products
			foreach($associatedProducts as $child) {
				/*
				If the child product has got the chosen color and is saleable (not out of stock, not disabled)
				we retrieve the corresponding size into the response
				*/
				if ($child->isSaleable()) {
				    // filter by colour if available
				    if (isset($colourAttributeCode)) {
				        if ($child->getData($colourAttributeCode) == $params[$colourParam]) {
                            $value = $child->getResource()->getAttribute($sizeAttributeCode)->getFrontend()->getValue($child);
                            $value = Mage::helper('productrefresh')->cleanAttribute($value);
        					if (!in_array($value, $response['sizes'])) {        					    
        					    //Mage::helper('productrefresh')->log(sprintf("%s->value: %s", __METHOD__, $value) );
        					    array_push($response['sizes'], $value);
        					}
                        }
                    }
                    else {
                        // size only
                        $value = $child->getResource()->getAttribute($sizeAttributeCode)->getFrontend()->getValue($child);
                        $value = Mage::helper('productrefresh')->cleanAttribute($value);
    					if (!in_array($value, $response['sizes'])) {
    					    array_push($response['sizes'], $value);
    					}
                    }
				}
			}
		}

		// Use the SESSION to store the color attribute (where does this get used?)
		Mage::getSingleton('core/session')->setChosenColor($params[$colourParam]);

		$finalResponse = new Varien_Object();
		//Mage::helper('productrefresh')->log(sprintf("%s->dispatch::productrefresh_finalise_response", __METHOD__) );
		Mage::dispatchEvent('productrefresh_finalise_response',
			array(
				'response'      => $finalResponse,
				'product_id'    => $params['product_id'],
				'filter'        => array(
					$colourAttributeCode => $params[$colourParam]
				)
			)
		);
		// append any new responses to response
		if ($finalResponse->getResponse()) {
			$extraResponse = $finalResponse->getResponse();
			//Mage::helper('productrefresh')->log(sprintf("%s->finalResponse=%s", __METHOD__, print_r($extraResponse, true)) );
			foreach($extraResponse as $key => $val) {
				$response[$key] = $val;
			}
		}

		// give observer a chance to set status
		if (!array_key_exists('status', $response)) {
			$response['status'] = "SUCCESS";
		}
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	}

    /**
     * unitsPerSizeAction
     *
     * example request
     * productrefresh/index/unitsPerSize?product_id=26428&size=8
     *
     * example json response
     * {"units":1,"status":"SUCCESS"}
     */
	public function unitsPerSizeAction() {
		$params = $this->getRequest()->getParams();
		$response = array();

		// load the parent product if the product_id has been given
		if (!isset($params['product_id'])) {
			$response['status'] = "ERROR: product id unspecified";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		$product = Mage::getModel('catalog/product')->load($params['product_id']);

		// load the parent product if the product_id has been given
		if (!isset($params['size'])) {
			$response['status'] = "ERROR: no size attribute specified";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}

		// Double check if the product is configurable
		if ($product->getTypeId() == "configurable") {
			$isConfigurable = true;
			$associatedProducts = $product->getTypeInstance()->getUsedProducts();
		}
		else {
			$response['status'] = "ERROR: product is not configurable";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}

		// Responses array
		$response['units'] = array();

		// Get the attributes of the product
		$attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

		// If the attributes array is not loaded or empty
		if (!$attributes || empty($attributes)) {
			$response['status'] = "ERROR: attributes are not loaded";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}

		foreach ($attributes as $attr) {
			// If we have found the size
			if (preg_match("/size/i", $attr['label'])) {
				$sizeAttributeId = $attr['attribute_id'];
				$sizeAttributeCode = $attr['attribute_code'];
				//Mage::helper('productrefresh')->log(sprintf("%s->size: %s=%s", __METHOD__, $sizeAttributeCode, $sizeAttributeId) );
			}
			// If we have found the colour
			if (preg_match("/(colour|color)/i", $attr['label']) ) {
				$colourAttributeId = $attr['attribute_id'];
				$colourAttributeCode = $attr['attribute_code'];
                //Mage::helper('productrefresh')->log(sprintf("%s->colour: %s=%s", __METHOD__, $colourAttributeCode, $colourAttributeId) );
			}
		}

        $chosenColor = Mage::getSingleton('core/session')->getChosenColor();
		//Mage::helper('productrefresh')->log(sprintf("%s->chosenColor=%s", __METHOD__, $chosenColor));
		
		if (isset($sizeAttributeId) && $isConfigurable) {
			// fetch the associated products
			foreach($associatedProducts as $child) {
				/*
				If the child product has got the chosen color and is saleable (not out of stock, not disabled)
				we retrieve the corresponding size into the response
				*/
				if (isset($colourAttributeId) && $child->getData($colourAttributeCode) == $chosenColor) {
					$value = $child->getResource()->getAttribute($sizeAttributeCode)->getFrontend()->getValue($child);
					$value = Mage::helper('productrefresh')->cleanAttribute($value);
					$param = Mage::helper('productrefresh')->cleanAttribute($params['size']);
					// if chosen size found
					//Mage::helper('productrefresh')->log(sprintf("%s->sizeTest: %s == %s", __METHOD__, $value, $param) );
					if ($value == $param) {
						$qtyStock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty();
						$response['units'] = $qtyStock;
					}
				}
				elseif(!isset($colourAttributeId)) {
					$value = $child->getResource()->getAttribute($sizeAttributeCode)->getFrontend()->getValue($child);
					// If chosen size found
					if ($value == $params['size']) {
						$qtyStock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty();
						$response['units'] = $qtyStock;
					}
				}
			}
		}
		$response['status'] = "SUCCESS";
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	}

}