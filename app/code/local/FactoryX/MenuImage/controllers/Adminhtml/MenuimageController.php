<?php

/**
 * Class FactoryX_MenuImage_Adminhtml_MenuimageController
 */
class FactoryX_MenuImage_Adminhtml_MenuimageController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/menuimage');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('factoryx_menu/menuimage');

        return $this;
    }

    public function indexAction()
    {
        try {
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('menuimage/adminhtml_menuimage'));
            $this->renderLayout();
        }
        catch(Exception $ex) {
            Mage::helper('menuimage')->log(sprintf("%s->error=%s", __METHOD__, print_r($ex, true)), Zend_Log::DEBUG );
        }
    }

    public function deleteAction() {
        $menuimageId = (int) $this->getRequest()->getParam('id');
        if ($menuimageId) {
            try {
                $model = Mage::getModel('menuimage/menuimage')->load($menuimageId);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('menuimage')->__('Homepage was successfully deleted'));
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
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('menuimage/menuimage')->load($id);

        if ($model->getId() || $id == 0)
        {
            // Data from the form
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            // If data from the form, we add it to the model
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('menuimage_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('factoryx_menu/menuimage');

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('menuimage/adminhtml_menuimage_edit'))
                ->_addLeft($this->getLayout()->createBlock('menuimage/adminhtml_menuimage_edit_tabs'));

            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);


            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('menuimage')->__('Menu Image does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $model = Mage::getModel('menuimage/menuimage');

            try
            {

                // Loop based on the amount of pictures chosen
                for($i=1;$i<=Mage::helper('menuimage')->getBlockCount();$i++)
                {
                    if ($data['type_'.$i] == "image") {
                        // Before checking for errors we need to process the image data so they won't be lost in case of errors
                        // Save the frontend homepage image
                        if (isset($_FILES['image_' . $i]['name']) and (file_exists($_FILES['image_' . $i]['tmp_name']))) {
                            $uploader = Mage::getModel('core/file_uploader', 'image_' . $i);
                            $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(true);
                            $result = $uploader->save(
                                Mage::getSingleton('menuimage/menuimage_media_config')->getBaseMediaPath()
                            );

                            // Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
                            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
                            $result['path'] = str_replace(DS, "/", $result['path']);

                            $result['url'] = Mage::getSingleton('menuimage/menuimage_media_config')->getMediaUrl($result['file']);
                            $result['cookie'] = array(
                                'name' => session_name(),
                                'value' => $this->_getSession()->getSessionId(),
                                'lifetime' => $this->_getSession()->getCookieLifetime(),
                                'path' => $this->_getSession()->getCookiePath(),
                                'domain' => $this->_getSession()->getCookieDomain()
                            );

                            $data['image_' . $i] = $result['file'];
                        } else {
                            if (isset($data['image_' . $i]['delete']) && $data['image_' . $i]['delete'] == 1) {
                                $data['image_' . $i] = NULL;
                            }
                        }

                        if (isset($data['image_' . $i])) {
                            if (is_array($data['image_' . $i])) {
                                $data['image_' . $i] = $data['image_' . $i]['value'];
                            }
                        }
                    }
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
                // Images must be provided
                for($i=1;$i<=Mage::helper('menuimage')->getBlockCount();$i++)
                {
                    if ($data['type_'.$i] == "image") {
                        if (!isset($data['image_' . $i])) {
                            // We use session to set the active tab to show where the error is
                            Mage::getSingleton('admin/session')->setActiveTab('general_tab');
                            throw new Exception ("You must provide menu images.");
                        }
                    }
                }

                // Save the homepage
                $model->save();

                // Save the images to the database
                for($i=1;$i<=Mage::helper('menuimage')->getBlockCount();$i++)
                {
                    // Retrieve possible existing images for this home page
                    $existingImage = $model->getBlock($i);
                    // If there is no existing images for this home page in the database
                    if (!$existingImage)
                    {
                        if ($data['type_'.$i])
                        {
                            // We create the images
                            $pictureModel = Mage::getModel('menuimage/block');
                            if ($data['type_'.$i] == "image")
                            {
                                $pictureModel->setUrl($data['image_'.$i]);
                            }
                            elseif ($data['type_'.$i] == "product")
                            {
                                $pictureModel->setProductId($data['product_id_'.$i]);
                            }

                            // Strip query string
                            if (strpos($data['link_'.$i],"?") !== false)
                            {
                                $link = strtok($data['link_'.$i],"?");
                            }
                            else $link = $data['link_'.$i];
                            $pictureModel->setLink($link);
                            $pictureModel->setAlt($data['alt_'.$i]);
                            $pictureModel->setSortOrder($data['sort_order_'.$i]);
                            $pictureModel->setType($data['type_'.$i]);
                            $pictureModel->setMenuimageId($model->getId());
                            $pictureModel->setIndex($i);
                            $pictureModel->save();
                        }
                    }
                    else
                    {
                        if ($data['type_'.$i])
                        {
                            if ($data['type_'.$i] == "image")
                            {
                                $existingImage->setUrl($data['image_'.$i]);
                            }
                            elseif ($data['type_'.$i] == "product")
                            {
                                $existingImage->setProductId($data['product_id_'.$i]);
                            }

                            // Strip query string
                            if (strpos($data['link_' . $i], "?") !== false) {
                                $link = strtok($data['link_' . $i], "?");
                            } else $link = $data['link_' . $i];
                            $existingImage->setLink($link);
                            $existingImage->setAlt($data['alt_' . $i]);
                            $existingImage->setSortOrder($data['sort_order_' . $i]);
                            $existingImage->setType($data['type_'.$i]);
                            $existingImage->setIndex($i);
                            $existingImage->save();
                        }
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('menuimage')->__('Menu Image was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('menuimage')->__('Unable to find menuimage to save'));
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $menuimageIds = $this->getRequest()->getParam('menuimage');
        if (!is_array($menuimageIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select menuimage(s)'));
        } else {
            try {
                foreach ($menuimageIds as $menuimageId) {
                    $model = Mage::getModel('menuimage/menuimage')->load($menuimageId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($menuimageIds)
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
        $menuimageIds = $this->getRequest()->getParam('menuimage');
        if (!is_array($menuimageIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select menuimage(s)'));
        } else {
            try {

                foreach ($menuimageIds as $menuimageId)
                {
                    // Load homepage
                    $menuimage = Mage::getModel('menuimage/menuimage')
                        ->load($menuimageId);
                    // If it is disabled we don't display it
                    if ($this->getRequest()->getParam('status') == 0)
                    {
                        $menuimage->setStatus($this->getRequest()->getParam('status'))
                            ->setStores('')
                            ->setIsMassupdate(true)
                            ->save();
                    }
                    // If it is enabled we do display it
                    elseif($this->getRequest()->getParam('status') == 1)
                    {
                        $menuimage->setStatus($this->getRequest()->getParam('status'))
                            ->setStores('')
                            ->setIsMassupdate(true)
                            ->save();
                    }
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($menuimageIds))
                );
            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

}