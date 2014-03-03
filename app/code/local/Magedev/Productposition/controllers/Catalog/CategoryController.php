<?php 
/**
 * Set Category controller
 * @category   Category Save
 * @package    Magedev_Productposition
 * @author     Mage Developer(mage.devloper@gmail.com)
 */
require_once 'Mage/Adminhtml/controllers/Catalog/CategoryController.php';
class Magedev_Productposition_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
{ 
    /**
     * Category save
     * required so to not clobber new positions ???
	 */
	 
    public function saveAction() {
        if (!$category = $this->_initCategory()) {
            return;
        }
        $storeId = $this->getRequest()->getParam('store');
        if ($data = $this->getRequest()->getPost()) {
            $category->addData($data['general']);
            if (!$category->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    if ($storeId) {
                        $parentId = Mage::app()->getStore($storeId)->getRootCategoryId();
                    }
                    else {
                        $parentId = Mage_Catalog_Model_Category::TREE_ROOT_ID;
                    }
                }
                $parentCategory = Mage::getModel('catalog/category')->load($parentId);
                $category->setPath($parentCategory->getPath());
            }
            //  Check "Use Default Value" checkboxes values
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $category->setData($attributeCode, null);
                }
            }

            $category->setAttributeSetId($category->getDefaultAttributeSetId());

            if (isset($data['category_products']) && !$category->getProductsReadonly()) {
            	//Mage::log(sprintf("category_products: %s", print_r($data['category_products'], true)));
            	//Mage::log(sprintf("getProductsReadonly: %s", $category->getProductsReadonly()));
            	
				// get new product associations
				$newProducts = array();
                parse_str($data['category_products'], $newProducts);
                //Mage::log(sprintf("newProducts: %s", print_r($newProducts, true)));
				
				// get unassigned(old) product position
				$oldProducts = $category->getProductsPosition();
				//Mage::log(sprintf("oldProducts: %s", print_r($oldProducts, true)));
				
				// merging new and old product position(|| assigned)
				$newlyAddedProducts = array_diff_key($newProducts, $oldProducts);
				//Mage::log(sprintf("newlyAddedProducts: %s", print_r($newlyAddedProducts, true)));
				
				$newlyDeletedProducts = array_diff_key($oldProducts, $newProducts);
				//Mage::log(sprintf("newlyDeletedProducts: %s", print_r($newlyDeletedProducts, true)));
				
				$products = $oldProducts + $newlyAddedProducts;				
				foreach($newlyDeletedProducts as $key=>$val) {
					unset($products[$key]);
				}
				
				/* handle newly associated products */
				foreach($products as $productId => $position) {
					// if never has a pos then set
					//Mage::log(sprintf("%d => %s,%s", $productId, print_r($position, true), empty($position)));
					if (empty($position) || $position == 0) {
						$newPos = max($products) + 1;
						//Mage::log(sprintf("set pos = %d", $newPos));
						$products[$productId] = $newPos;
					}
					// otherwise use new position
					if (array_key_exists($productId, $newProducts)) {
						$products[$productId] = $newProducts[$productId];
					}
				}
				/*
				$resetPositions = false;
				$prevPos = -99999;
				foreach($products as $productId => $position) {
					if ($products[$productId] == $prevPos) {
						$resetPositions = true;
						break;
					}
					$prevPos = $products[$productId];
				}
				// rest all positions
				if ($resetPositions) {
					$newPos = 1;
					foreach($products as $productId => $position) {
						$products[$productId] = $newPos++;
					}
				}
				*/
				
				//Mage::log(sprintf("repos products=%s", print_r($products, true)));
				// handling unassociated products (if any)
				/*
				if ($products && count($products) > 0) {
					asort($products);				
					$productPositionIndex = array_flip($products);
					$shift = array_shift($productPositionIndex);
					array_unshift($productPositionIndex, 0, $shift);
					unset($productPositionIndex[0]);				
					$products = array_flip($productPositionIndex);
				}
				*/
				//Mage::log(sprintf("products=%s", print_r($products, true)));
                $category->setPostedProducts($products);
            }

            Mage::dispatchEvent('catalog_category_prepare_save', array(
                'category' => $category,
                'request' => $this->getRequest()
            ));

            try {
                $category->save();
				$catgId = $category->getId();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('catalog')->__('Category saved'));
                $refreshTree = 'true';
            }
            catch (Exception $e){
                $this->_getSession()->addError($e->getMessage())->setCategoryData($data);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $category->getId()));
        $body = sprintf("<script type=\"text/javascript\">parent.updateContent('%s', {}, '%s');</script>", $url, $refreshTree);
        //Mage::log(sprintf("body=%s", $body));
        $this->getResponse()->setBody($body);
    }
    
	public function updateProductAction() {
		$storeId = $this->getRequest()->getParam('store_id');
		$field = $this->getRequest()->getParam('field');
		$data = $this->getRequest()->getPost();
		$value = $this->getRequest()->getPost('value');
		$productId = $this->getRequest()->getParam('productid');
		$_product = Mage::getModel('catalog/product')->load($productId);
		//$_product->setName($value);
	    try {
		if($field == "inventory")
		{
			if(!is_numeric($value)){echo $value = 'Error: Please enter only numeric.';die;}
			$newQty = $value;
			$status = 0;
			if ($newQty>=0){$status=1;}
			  $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
			  $stock->setQty($newQty)->setIsInStock((bool)$status);
			  // save stock record
			  if(!$stock->save()){$value = 'Error: found in request.';}
		  
		}else{
			
 			if($field == "price" || $field == "special_price"){if(!is_numeric($value)){echo $value = 'Error: Please enter only numeric.';die;}}
			
			$_product->setData($field, $value);
			if(isset($storeId) && $storeId!= ""){Mage::app()->setCurrentStore($storeId);}
			if(!$_product->save()){$value = 'Error: found in request.';}
			
		 }	
		if($field == 'status'){
			if($value == 1){$returnStatus = "Enabled";}else if($value == 2){$returnStatus = "Disabled";}
			$this->getResponse()->setBody($returnStatus);
		}else{$this->getResponse()->setBody($value);}
	 
	 } catch (Exception $e) {
            die($e->getMessage());
        }
	}
	
	public function getGridBackAfterUpdateAction() {
        if (!$category = $this->_initCategory()) {
            return;
        }		
		$catId = $category->getId();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('adminhtml/catalog_category_tab_enahancedproducts')
			//->setId($catId)
			->setData('id', $catId)
			->setData('store', '1')
			->toHtml()
        );
    }
	
	public function updateProductPositionsAction() {
		$categoryId = (int) $this->getRequest()->getParam('categoryId');
		$productId = (int) $this->getRequest()->getParam('productId');
		$productNewPosition = (int) $this->getRequest()->getParam('productNewPosition');
		
		// Load Category
		$category = Mage::getModel('catalog/category')->load($categoryId);

		// Get Product Postions
		$prodductPositionArray = $category->getProductsPosition();
		asort($prodductPositionArray);
		//Mage::log(sprintf("%s->prodductPositionArray=%s", __METHOD__, print_r($prodductPositionArray, true)) );
		
        // Update product position
		if (!isset($prodductPositionArray[$productId])) {
            $this->_fault('product is not assigned');
        }
		
		$i = 1;
		$positions = array();
		foreach($prodductPositionArray as $key => $val) {
			$positions[$key] = $i;
			$i++;
		}
		
		// current product postion
		$currentPosition = $positions[$productId];
		
		// adjust product position values in sequence for all associated products
		$positions = array_flip($positions);
		//Mage::log(sprintf("%s->positions=%s", __METHOD__, print_r($positions, true)) );
		
		$pageNumber = $this->getRequest()->getParam('page');
		if (isset($pageNumber) && !empty($pageNumber)) {	
			$pageLimit = $this->getRequest()->getParam('limit');
			if (!isset($pageLimit) && empty($pageLimit)) {
			    $pageLimit = 20;
            }
			$pageNumber = $pageNumber - 1;
			$noOfProducts = $pageNumber * $pageLimit;
			$productNewPosition = $noOfProducts + $productNewPosition;
		}
		
		if ($currentPosition < $productNewPosition) {
			$i = $currentPosition +1;
			// decrement indexes by 1 in position
			while($i <= $productNewPosition) {
				$positions[$i-1] = $positions[$i];
				$i++;
			}
		}
		else if ($currentPosition > $productNewPosition) {
		    //Mage::log(sprintf("%s->currentPosition=%s,productNewPosition=%s", __METHOD__, $currentPosition, $productNewPosition) );
		    
			$i = $currentPosition - 1;
			if ($i <= 0) {
			    $i = 1;
			}
			// increment indexes by 1 in position
			while($i >= $productNewPosition) {
			    //if (array_key_exists($i, $positions)) {
				    $positions[$i+1] = $positions[$i];
				//}
				$i--;
			}
		}
		$positions[$productNewPosition] = $productId;
		$positions = array_flip($positions);
		
		
		$positions[$productId] = $productNewPosition;
		$category->setPostedProducts($positions);

        try {
			$category->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
		
		// set grid after update
		//$this->getGridBackAfterUpdateAction();
		$this->enhancedgridAction();
	}
	
	public function enhancedgridAction() {
        if (!$category = $this->_initCategory(true)) {
            return;
        }
		$catId = $category->getId();
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/catalog_category_tab_enahancedproducts')->toHtml());        
    }
 }
