<?php

/**
 * Class FactoryX_CustomGrids_Adminhtml_CustomgridController
 */
class FactoryX_CustomGrids_Adminhtml_CustomgridController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/customgrids');
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('customgrids/adminhtml_customgrid'));
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select column(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('customgrids/column')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('customgrids')->__('An error occurred while mass deleting items. Please review log and try again.')
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('customgrids/column');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('customgrids')->__('This column no longer exists.')
                );
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('current_model', $model);

        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('customgrids/adminhtml_customgrid_edit'));
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('customgrids/column');
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->_getSession()->addError(
                        Mage::helper('customgrids')->__('This column no longer exists.')
                    );
                    $this->_redirect('*/*/index');
                    return;
                }
            }

            // save model
            try {
                if (array_key_exists('remove',$data) && $data['remove'])
                {
                    // Get the right attribute based on the block selected
                    if (array_key_exists('remove_column_'.$data['grid_block_type'],$data))
                    {
                        $data['attribute_code'] = $data['remove_column_'.$data['grid_block_type']];
                        $data['collection_model_type'] = Mage::getSingleton('customgrids/config')->getCollectionType($data['grid_block_type']);
                    }
                    else
                    {
                        throw new Exception();
                    }
                }
                else
                {
                    // Get the right attribute based on the block selected
                    if (array_key_exists('attribute_code_'.$data['grid_block_type'],$data))
                    {
                        $data['attribute_code'] = $data['attribute_code_'.$data['grid_block_type']];
                        $data['collection_model_type'] = Mage::getSingleton('customgrids/config')->getCollectionType($data['grid_block_type']);
                    }
                    else
                    {
                        throw new Exception();
                    }
                    // Get the right after column based on the block selected
                    if (array_key_exists('after_column_'.$data['grid_block_type'],$data))
                    {
                        $data['after_column'] = $data['after_column_'.$data['grid_block_type']];
                    }
                    else
                    {
                        throw new Exception();
                    }
                }
                // Circular dependency validation (this is an extra check but with the last updates it should never fail)
                $this->_validateAfterColumn($data);

                // Handle the roles
                if (array_key_exists('admin_roles',$data) && $data['admin_roles'])
                    $data['admin_roles'] = implode(',',$data['admin_roles']);
                $model->addData($data);
                $this->_getSession()->setFormData($data);
                $model->save();
                $this->_getSession()->setFormData(false);
                $this->_getSession()->addSuccess(
                    Mage::helper('customgrids')->__('The column has been saved.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('customgrids')->__('Unable to save the column.'));
                $redirectBack = true;
                Mage::logException($e);
            }

            if ($redirectBack) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        $this->_redirect('*/*/index');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('customgrids/column');
                $model->load($id);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('customgrids')->__('Unable to find a column to delete.'));
                }
                $model->delete();
                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('customgrids')->__('The column has been deleted.')
                );
                // go to grid
                $this->_redirect('*/*/index');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('customgrids')->__('An error occurred while deleting column data. Please review log and try again.')
                );
                Mage::logException($e);
            }
            // redirect to edit form
            $this->_redirect('*/*/edit', array('id' => $id));
            return;
        }
        // display error message
        $this->_getSession()->addError(
            Mage::helper('customgrids')->__('Unable to find a column to delete.')
        );
        // go to grid
        $this->_redirect('*/*/index');
    }

    /**
     * Circular dependency check
     * @param $data
     * @throws Exception
     */
    protected function _validateAfterColumn($data)
    {
        $afterColumn = $data['after_column'];
        $gridBlockType = $data['grid_block_type'];

        // Check if the related after column is created by the module (custom)
        $existingColumns = Mage::getResourceModel('customgrids/column_collection')
            ->addFieldToSelect('after_column')
            ->addFieldToFilter('attribute_code',$afterColumn)
            ->addFieldToFilter('grid_block_type',$gridBlockType);

        if ($existingColumns->getSize())
        {
            // Check if the after column of the existing columns does not reference the column we're about to create (circular dependency)
            foreach ($existingColumns as $existingColumn)
            {
                if ($existingColumn->getAfterColumn() == $data['attribute_code'])
                {
                    throw new Exception('The after column you specified cannot be used (circular dependency check failed)');
                }
            }
        }
    }
}