<?php
/**
 * Ajax controller to update the available sizes and the price
 */
 
class FactoryX_ProductRefresh_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction() {
		$params = $this->getRequest()->getParams();
		$response = array();
		
		// Load the parent product if the product_id has been given
		if (!isset($params['product_id'])) 
		{
			$response['status'] = "ERROR";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		$product = Mage::getModel('catalog/product')->load($params['product_id']);
		
		// Double check if the product is configurable
		if ($product->getTypeId() == "configurable")
		{
			$isConfigurable = true;
			$associatedProducts = $product->getTypeInstance()->getUsedProducts();
		}
		else 
		{
			$response['status'] = "ERROR";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		
		// Responses array
		$response['sizes'] = array();
		$response['prices'] = array();
		
		// Get the attributes of the product
		$attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
		
		foreach ($attributes as $attr)
		{
			// If we have found the size
			if($attr['label']=="Size") 
			{
				$sizeAttributeId = $attr['attribute_id'];
				$sizeAttributeCode = $attr['attribute_code'];
			}
			// If we have found the colour
			else if($attr['label']=="Colour") 
			{
				foreach ($attr['values'] as $value)
				{
					// Load the extra prices depending on the colour
					$response['prices'][$value['value_index']] = $value['pricing_value'];
				}
			}
		}
		
		if (isset($sizeAttributeId))
		{
			// Then we load the size attribute
			$attributeLoaded = Mage::getModel('catalog/resource_eav_attribute')->load($sizeAttributeId);

			if ($isConfigurable)
			{
				// Fetch the associated products
				foreach($associatedProducts as $child)
				{
					// If the child product has got the chosen color and is saleable (not out of stock, not disabled) we retrieve the corresponding size into the response
					$response["tmp"][] = $child->getColourDescription();
					if($child->getColourDescription() == $params['color'] && $child->isSaleable())
					{
						$value = $child->getResource()->getAttribute($sizeAttributeCode)->getFrontend()->getValue($child);
						if (!in_array($value,$response['sizes'])) array_push($response['sizes'],str_replace(array(" ","/"),"-",$value));
					}
				}
			}
		}
		
		// Use the SESSION to store the color attribute
		Mage::getSingleton('core/session')->setChosenColor($params['color']);
		
		$response['status'] = "SUCCESS";
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	}

	public function unitsPerSizeAction() {
		$params = $this->getRequest()->getParams();
		$response = array();

		// Load the parent product if the product_id has been given
		if (!isset($params['product_id'])) 
		{
			$response['status'] = "ERROR: product id unspecified";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		$product = Mage::getModel('catalog/product')->load($params['product_id']);
		
		// Double check if the product is configurable
		if ($product->getTypeId() == "configurable")
		{
			$isConfigurable = true;
			$associatedProducts = $product->getTypeInstance()->getUsedProducts();
		}
		else 
		{
			$response['status'] = "ERROR: product is not configurable";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		
		// Responses array
		$response['units'] = array();
		
		// Get the attributes of the product
		$attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
		
		// If the attributes array is not loaded or empty
		if (!$attributes || empty($attributes))
		{
			$response['status'] = "ERROR: attributes are not loaded";
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}
		
		foreach ($attributes as $attr)
		{
			// If we have found the size
			if($attr['label']=="Size") 
			{
				$sizeAttributeId = $attr['attribute_id'];
				$sizeAttributeCode = $attr['attribute_code'];
			}
			// If we have found the colour
			else if($attr['label']=="Colour") 
			{
				$colorAttributeId = $attr['attribute_id'];
				$colorAttributeCode = $attr['attribute_code'];
			}
		}
		
		$chosenColor = Mage::getSingleton('core/session')->getChosenColor();
		
		if (isset($sizeAttributeId))
		{
			// Then we load the size attribute
			$attributeLoaded = Mage::getModel('catalog/resource_eav_attribute')->load($sizeAttributeId);

			if ($isConfigurable)
			{
				// Fetch the associated products
				foreach($associatedProducts as $child)
				{
					// If the child product has got the chosen color and is saleable (not out of stock, not disabled) we retrieve the corresponding size into the response
					if(isset($colorAttributeId) && $child->getColourDescription() == $chosenColor)
					{
						$value = $child->getResource()->getAttribute($sizeAttributeCode)->getFrontend()->getValue($child);
						// If chosen size found
						if ($value == $params['size'])
						{
							$qtyStock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty();
							$response['units'] = $qtyStock;
						}
					}
					elseif(!isset($colorAttributeId))
					{
						$value = $child->getResource()->getAttribute($sizeAttributeCode)->getFrontend()->getValue($child);
						// If chosen size found
						if ($value == $params['size'])
						{
							$qtyStock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty();
							$response['units'] = $qtyStock;
						}
					}
				}
			}
		}
		$response['status'] = "SUCCESS";
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
		return;
	} 	
	
}
?>