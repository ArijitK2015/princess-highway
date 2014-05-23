<?php

class FactoryX_Lookbook_Adminhtml_LookbookController extends Mage_Adminhtml_Controller_Action
{
	
	protected function _initAction() 
	{
        $this->loadLayout()
                ->_setActiveMenu('factoryx_menu/lookbook');

        return $this;
    }

    public function indexAction() 
	{
        $this->_initAction()
                ->renderLayout();
    }
	
	public function deleteAction() {
        $lookbookId = (int) $this->getRequest()->getParam('id');
        if ($lookbookId) {
            try {
                $model = Mage::getModel('lookbook/lookbook')->load($lookbookId);
				$model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('lookbook')->__('Lookbook was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	
	public function newAction() 
	{
		if ($data = $this->getRequest()->getPost()) 
		{
			// We add the lookbook type to the session
			Mage::getSingleton('adminhtml/session')->setLookbookType($data['lookbook_type']);
			
			// If previous page has been filled 
			if ($data['lookbook_type'] == "category")
			{
				// We add the category_id to the session
				Mage::getSingleton('adminhtml/session')->setCategory($data['category_id']);
			}
			else
			{
				// We add the category_id to the session
				Mage::getSingleton('adminhtml/session')->setCategory(NULL);
			}
			
			// If an ID is provided, that means it is an existing lookbook and there is a type change
			if ($id = $this->getRequest()->getParam('id')) 
				// We set a flag
				Mage::getSingleton('adminhtml/session')->setChangingType(1);
			$this->_forward('edit'); 
		}
		else
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('lookbook')->__('Lookbook does not exist'));
            $this->_redirect('*/*/');
		}
    }
	
	public function choosecatAction() 
	{		
		// If an ID is provided, there is a layout change
        if ($id = $this->getRequest()->getParam('id')) 
		{
			// We load the existing homepage
			$model = Mage::getModel('lookbook/lookbook')->load($id);
			if ($model->getId())
			{
				// We use sessions to pass parameters to the next page
				Mage::getSingleton('adminhtml/session')->setLookbookType($model->getLookbookType());
				if ($model->getLookbookType() == 'category')
					Mage::getSingleton('adminhtml/session')->setCategory($model->getCategoryId());
			}
		}
		
		$this->loadLayout();
		$this->_setActiveMenu('factoryx_menu/lookbook');

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('lookbook/adminhtml_lookbook_choosecat'));

		$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);

		$this->renderLayout();
    }
	
	public function editAction() 
	{
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('lookbook/lookbook')->load($id);

        if ($model->getId() || $id == 0) 
		{
			// Data from the form
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			
			// If it is a new lookbook
			if ($id == 0)
			{
				// We retrieve the category from session variables set in the previous steps
				
				if (!is_array($data) || (!array_key_exists('category_id',$data)))
				{
					$data['category_id'] = Mage::getSingleton('adminhtml/session')->getCategory(true);
				}
				
				// We retrieve the lookbook type from session variables set in the previous steps
				
				if (!is_array($data) || (!array_key_exists('lookbook_type',$data)))
				{
					$data['lookbook_type'] = Mage::getSingleton('adminhtml/session')->getLookbookType(true);
				}
			}
			// If it is an existing home page
			else
			{
				// And if the changing layout flag is set
				if (Mage::getSingleton('adminhtml/session')->getChangingType(true))
				{
					// This is a flag
					$saveFlag = false;
					// This is the new lookbookType
					$lookbookType = Mage::getSingleton('adminhtml/session')->getLookbookType(true);
					
					// We test the org and the new lookbookType
					if ($lookbookType != $model->getLookbookType())
					{
						// Change it if needed
						$model->setLookbookType($lookbookType);
						$saveFlag = true;
					}
					
					if ($lookbookType == 'category')
					{
						$categoryId = Mage::getSingleton('adminhtml/session')->getCategory(true);
						// Change the category
						$model->setCategoryId($categoryId);
						$saveFlag = true;
					}
					else
					{
						$model->setCategoryId(NULL);
						$saveFlag = true;
					}
					
					// If the flag set, we save the homepage
					if ($saveFlag)
					{
						$model->save();
					}
				}
			}
			
			// If data from the form, we add it to the model
            if (!empty($data)) {
                $model->setData($data);
            }
			
            Mage::register('lookbook_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('factoryx_menu/lookbook');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit'))
					->_addLeft($this->getLayout()->createBlock('lookbook/adminhtml_lookbook_edit_tabs'));

			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);


            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('lookbook')->__('Lookbook does not exist'));
            $this->_redirect('*/*/');
        }
    }
	
	public function saveAction() 
	{
        if ($data = $this->getRequest()->getPost()) 
		{
            $model = Mage::getModel('lookbook/lookbook');
			
			try 
			{	
				// Before checking for errors we need to process the image data so they won't be lost in case of errors
				// Save the shop the look image
				if(isset($_FILES['shop_pix']['name']) and (file_exists($_FILES['shop_pix']['tmp_name']))) 
				{
					$uploader = new Mage_Core_Model_File_Uploader('shop_pix');
					$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$result = $uploader->save(
						Mage::getSingleton('lookbook/lookbook_media_config')->getBaseMediaPath()
					);

					/**
					 * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
					 */
					$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
					$result['path'] = str_replace(DS, "/", $result['path']);

					$result['url'] = Mage::getSingleton('lookbook/lookbook_media_config')->getMediaUrl($result['file']);
					$result['cookie'] = array(
						'name'     => session_name(),
						'value'    => $this->_getSession()->getSessionId(),
						'lifetime' => $this->_getSession()->getCookieLifetime(),
						'path'     => $this->_getSession()->getCookiePath(),
						'domain'   => $this->_getSession()->getCookieDomain()
					);
					
					$data['shop_pix'] = $result['file'];
				}
				else 
				{       
					if(isset($data['shop_pix']['delete']) && $data['shop_pix']['delete'] == 1)
					{
						$data['shop_pix'] = NULL;
					}
				}
				
				if (isset($data['shop_pix']))
				{
					if (is_array($data['shop_pix']))
					{
						$data['shop_pix'] = $data['shop_pix']['value'];
					}
					$model->setShopPix($data['shop_pix']);
				}
				
				// Check is single store mode
				if (!Mage::app()->isSingleStoreMode() && isset($data['stores'])) 
				{
					if ($data['stores'][0] == 0) 
					{
						unset($data['stores']);
						$data['stores'] = array();
						$stores = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
						foreach ($stores as $store) 
							$data['stores'][] = $store->getId();
					}
				}
				
				// Edited date
				$data['edited'] = Mage::getModel('core/date')->gmtDate();
				
				// Assign the data to the model
				$model->setData($data)
						->setId($this->getRequest()->getParam('id'));
						
				// Handle errors
				// A shop the look image must be provided if show shop the look is enabled
				if (array_key_exists('show_shop_pix',$data) && $data['show_shop_pix'] == 1 && !isset($data['shop_pix']))
				{
					// We use session to set the active tab to show where the error is
					Mage::getSingleton('admin/session')->setActiveTab('general_tab');
					throw new Exception ("If you want to display a shop the look picture, you must provide a shop the look image.");
				}
						
				// Handle gallery before
				if (isset($data['lookbook_image']))
                {
                    $media_gallery = $data['lookbook_image'];
                    /**
                     * Note, the data is returned because it can contain a list of
                     * images to be deleted. Those arehandled in afterSaveMediaGallery.
                     */
                    $media_gallery = $this->beforeSaveMediaGallery($model, $media_gallery);
                }

				// Save the lookbook
                $model->save();		
				
				// Handle gallery after
				if (isset($data['lookbook_image']))
                {
                    $this->afterSaveMediaGallery($model, $media_gallery);
                }
				
				// Handle URL Rewrites
				if ($data['lookbook_type'] == "slideshow")
				{
					$targetPath = "lookbook/index/slideshow/id/".$model->getId();
				}
				elseif ($data['lookbook_type'] == "flipbook")
				{
					$targetPath = "lookbook/index/flipbook/id/".$model->getId();
				}
				else
				{
					$targetPath = "lookbook/index/view/id/".$model->getId();
				}
				
				$requestedPath = $data['identifier'];
				Mage::helper('core/url_rewrite')->validateRequestPath($requestedPath);
				
				if (!Mage::app()->isSingleStoreMode() && isset($data['stores'])) 
				{
					foreach ($data['stores'] as $key => $storeId)
					{						
						$existingUrlRewrite = Mage::getModel('core/url_rewrite')->loadByRequestPathAndStoreId($requestedPath, $storeId);
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
				
				// Clean cache of the homepages in case status have changed
				Mage::app()->cleanCache(FactoryX_Lookbook_Model_Lookbook::CACHE_TAG);
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('lookbook')->__('Lookbook was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
				if ($model->getId()) 
				{
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
				}
				else
				{
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				}
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('lookbook')->__('Unable to find lookbook to save'));
        $this->_redirect('*/*/');
    }
	
	public function massDeleteAction() 
	{
        $lookbookIds = $this->getRequest()->getParam('lookbook');
        if (!is_array($lookbookIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select lookbook(s)'));
        } else {
            try {
                foreach ($lookbookIds as $lookbookId) {
                    $model = Mage::getModel('lookbook/lookbook')->load($lookbookId);
					$model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($lookbookIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	public function massStatusAction() 
	{
        $lookbookIds = $this->getRequest()->getParam('lookbook');
        if (!is_array($lookbookIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select lookbook(s)'));
        } else {
            try {

                foreach ($lookbookIds as $lookbookId) {
                    $lookbook = Mage::getModel('lookbook/lookbook')
                            ->load($lookbookId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setStores('')
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($lookbookIds))
                );
            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	/**
	 *	function called when clicking the gallery upload button
	 */ 
	public function uploadAction()
    {
		Mage::helper('lookbook/image');
		
        try {
            $uploader = new Mage_Core_Model_File_Uploader('image');
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->addValidateCallback('lookbook_lookbook_image',
                Mage::helper('lookbook/image'), 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save(
                Mage::getSingleton('lookbook/lookbook_media_config')->getBaseTmpMediaPath()
            );

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] = str_replace(DS, "/", $result['path']);

            $result['url'] = Mage::getSingleton('lookbook/lookbook_media_config')->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';
            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain()
            );

        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode());
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
	
	/**
     * Start based on the methods in /app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php
     */
    protected $_renamedImages = array();

    protected function beforeSaveMediaGallery(FactoryX_Lookbook_Model_Lookbook $model, $value)
    {
        if (!is_array($value) || !isset($value['images'])) {
            return;
        }

        if(!is_array($value['images']) && strlen($value['images']) > 0) {
           $value['images'] = Mage::helper('core')->jsonDecode($value['images']);
        }

        if (!is_array($value['images'])) {
           $value['images'] = array();
        }

        $clearImages = array();
        $newImages   = array();
        $existImages = array();

        /**
         * The given values are checked for files to be removed or added.
         */
        foreach ($value['images'] as &$image) {
            if(!empty($image['removed'])) {
                $clearImages[] = $image['file'];
            } else if (!isset($image['value_id'])) {
                $newFile = $this->_moveImageFromTmp($image['file']);
                $image['new_file'] = $newFile;
                $newImages[$image['file']] = $image;
                $this->_renamedImages[$image['file']] = $newFile;
                $image['file'] = $newFile;
            } else {
                $existImages[$image['file']] = $image;
            }
        }

        foreach ($model->getMediaAttributes() as $mediaAttrCode => $label) {
            $attrData = $model->getData($mediaAttrCode);

            if (in_array($attrData, $clearImages)) {
                $model->setData($mediaAttrCode, FactoryX_Lookbook_Model_Lookbook::NO_SELECTION);
                $model->setData($mediaAttrCode.'_label', null);
            }

            if (in_array($attrData, array_keys($newImages))) {
                $model->setData($mediaAttrCode, $newImages[$attrData]['new_file']);
                $model->setData($mediaAttrCode.'_label', $newImages[$attrData]['label']);
            }

            if (in_array($attrData, array_keys($existImages))) {
                $model->setData($mediaAttrCode.'_label', $existImages[$attrData]['label']);
            }
        }

        return $value;

    }

    protected function afterSaveMediaGallery(FactoryX_Lookbook_Model_Lookbook $model, $value)
    {
		
        if (!is_array($value) || !isset($value['images'])) {
            return;
        }

        $toDelete = array();
        foreach ($value['images'] as &$image) {

            if(!empty($image['removed'])) {
                if(isset($image['value_id'])) {
                    $toDelete[] = $image['value_id'];
                }
                continue;
            }
            $data = array();

            if(!isset($image['value_id'])) {
                $data['lookbook_id']      = $model->getId();
                $data['path']          = $image['file'];

                $mymodelMedia = Mage::getModel('lookbook/lookbook_media');
            } else {
				
                $mymodelMedia = Mage::getModel('lookbook/lookbook_media')
                    ->load($image['value_id']);

                if (!is_object($mymodelMedia))
                {
                    continue; /* Image should exist but doesn't */
                }
            }

            // Add label, position, disabled
            $data['label']    = $image['label'];
            $data['position'] = (int) $image['position'];
            $data['disabled'] = (int) $image['disabled'];

            $mymodelMedia
                ->addData($data)
                ->save();

        }

        foreach ($toDelete as $media_id)
        {
            $mymodelMedia = Mage::getModel('lookbook/lookbook_media')
                ->load($media_id);

            if (is_object($mymodelMedia))
            {
                $mymodelMedia->delete();
            }

        }

    }
	
	/**
     * Retrieve resource model
     *
     * @return mycompany_mymodule_Model_mymodel_Media
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('lookbook/lookbook_media');
    }

    /**
     * Retrive media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('lookbook/lookbook_media_config');
    }

    /**
     * Move image from temporary directory to normal
     *
     * @param string $file
     * @return string
     */
    protected function _moveImageFromTmp($file)
    {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->_getConfig()->getMediaPath($file));
        try {
            $ioObject->open(array('path'=>$destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path'=>$destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file)-4) {
            $file = substr($file, 0, strlen($file)-4);
        }
        $destFile = $this->_getUniqueFileName($file, $ioObject->dirsep());

        /** @var $storageHelper Mage_Core_Helper_File_Storage_Database */
        $storageHelper = Mage::helper('core/file_storage_database');

        if ($storageHelper->checkDbUsage()) {
            $storageHelper->renameFile(
                $this->_getConfig()->getTmpMediaShortUrl($file),
                $this->_getConfig()->getMediaShortUrl($destFile));

            $ioObject->rm($this->_getConfig()->getTmpMediaPath($file));
            $ioObject->rm($this->_getConfig()->getMediaPath($destFile));
        } else {
            $ioObject->mv(
                $this->_getConfig()->getTmpMediaPath($file),
                $this->_getConfig()->getMediaPath($destFile)
            );
        }

        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param <type> $file
     * @param <type> $dirsep
     * @return string
     */
    protected function _getUniqueFileName($file, $dirsep) {
        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            $destFile = Mage::helper('core/file_storage_database')
                ->getUniqueFilename(
                    $this->_getConfig()->getBaseMediaUrlAddition(),
                    $file
                );
        } else {
            $destFile = dirname($file) . $dirsep
                . Mage_Core_Model_File_Uploader::getNewFileName($this->_getConfig()->getMediaPath($file));
        }

        return $destFile;
    }


    /**
     * End based on the methods in /app/code/core/Mage/Catalog/Model/Product/Attribute/Backend/Media.php
     */

}