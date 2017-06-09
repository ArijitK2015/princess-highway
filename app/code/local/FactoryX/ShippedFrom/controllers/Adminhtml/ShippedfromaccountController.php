<?php

/**
 * Class FactoryX_ShippedFrom_Adminhtml_ShippedfromaccountController
 */
class FactoryX_ShippedFrom_Adminhtml_ShippedfromaccountController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/auspost/accounts');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('factoryx_menu/auspost/accounts');

        return $this;
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('shippedfrom/adminhtml_account'));
        $this->renderLayout();
    }

    /**
     *
     */
    public function deleteAction()
    {
        $accountId  = (int) $this->getRequest()->getParam('id');
        if ($accountId) {
            try {
                /** @var FactoryX_ShippedFrom_Model_Account $account */
                $account = Mage::getModel('shippedfrom/account');
                $account->load($accountId);
                $accountName = $account->getName();
                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(
                        Mage::helper('shippedfrom')->__("The account '%s' has been deleted.", $accountName)
                    );
                $account->delete();
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }

        Mage::getSingleton('adminhtml/session')
            ->addError(
                Mage::helper('shippedfrom')->__('Unable to find an account to delete.')
            );
        $this->_redirect('*/*/');
    }

    /**
     *
     */
    public function deleteProductAction()
    {
        $productId = (int)$this->getRequest()->getParam('id');
        if ($productId) {
            try {
                /** @var FactoryX_ShippedFrom_Model_Account_Product $product */
                $product = Mage::getModel('shippedfrom/account_product');
                $product->load($productId);
                $productId = $product->getData('product_id');
                $accountId = $product->getData('associated_account');

                Mage::getSingleton('adminhtml/session')
                    ->addSuccess(
                        Mage::helper('shippedfrom')->__("The product item '%s' has been deleted.", $productId)
                    );
                $product->delete();
                $this->_redirect(
                    '*/*/edit',
                    array(
                        'id' => $accountId,
                        '_query' => array(
                            'active_tab' => 'product_tab'
                        )
                    )
                );
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }

        Mage::getSingleton('adminhtml/session')
            ->addError(
                Mage::helper('shippedfrom')->__('Unable to find a product item to delete.')
            );
        $this->_redirect('*/*/');
    }

    /**
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * This is the action of the edit page
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var FactoryX_ShippedFrom_Model_Account $model */
        $model = Mage::getModel('shippedfrom/account')->load($id);

        if ($model->getId()
            || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);

            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('account_data', $model);

            $this->_initAction();

            $this->_addContent($this->getLayout()->createBlock('shippedfrom/adminhtml_account_edit'))
                ->_addLeft($this->getLayout()->createBlock('shippedfrom/adminhtml_account_edit_tabs'));;


            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')
                ->addError(
                    Mage::helper('shippedfrom')->__('Account does not exist')
                );
            $this->_redirect('*/*/');
        }
    }

    /**
     * This is the action called when clicking the save button
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            /** @var FactoryX_ShippedFrom_Model_Account $model */
            $model = Mage::getModel('shippedfrom/account');

            try {

                /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Accounts $accountsRepository */
                $accountsRepository = Mage::getModel('shippedfrom/auspost_shipping_accounts');
                $data = $accountsRepository->retrieveAuspostAccountDetails($data);
                $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

                if (!$model->getId()) {
                    $saveFlag = true;
                } else {
                    $saveFlag = false;
                }

                $products = Mage::getResourceModel('shippedfrom/account_product_collection')
                    ->addFieldToFilter('associated_account', $model->getAccountId());
                $model->save();

                $msg = 'Account was successfully saved';
                if ($saveFlag
                    || count($products) == 0) {
                    $productsAdded = $this->createAssociatedProducts($data, $model);
                    if ($productsAdded > 0) {
                        $msg .= sprintf(", %d product(s) where added", $productsAdded);
                    }
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shippedfrom')->__($msg));
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
                if ($model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                } else {
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }

                return;
            }
        }

        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('shippedfrom')->__('Unable to find account to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * @param $data
     * @param $model
     * @return int
     */
    protected function createAssociatedProducts($data, $model)
    {
        $count = 0;
        foreach ($data['postage_products'] as $product) {
            $newProduct = Mage::getModel('shippedfrom/account_product');
            if (!array_key_exists('type', $product)
                || !array_key_exists('group', $product)
                || !array_key_exists('product_id', $product)) {
                Mage::throwException(
                    Mage::helper('shippedfrom')->__(
                        'The postage product is missing one of the required data: type, group or product id: %s',
                        print_r($product)
                    )
                );
            } else {
                $newProduct
                    ->setType($product['type'])
                    ->setGroup($product['group'])
                    ->setProductId($product['product_id'])
                    ->setAssociatedShippingMethod($this->getAssociatedShippingMethod($product['type']))
                    ->setAssociatedAccount($model->getId())
                    ->setData(
                        'option_signature_on_delivery_option',
                        (array_key_exists('options', $product)
                            && array_key_exists('signature_on_delivery_option', $product['options']))
                            ? $product['options']['signature_on_delivery_option']
                            : ""
                    )
                    ->setData(
                        'option_authority_to_leave_option',
                        (array_key_exists('options', $product)
                            && array_key_exists('authority_to_leave_option', $product['options']))
                            ? $product['options']['authority_to_leave_option']
                            : ""
                    )
                    ->setData(
                        'option_dangerous_goods_allowed',
                        (array_key_exists('options', $product)
                            && array_key_exists('dangerous_goods_allowed', $product['options']))
                            ? $product['options']['dangerous_goods_allowed']
                            : ""
                    )
                    ->setData(
                        'contract_valid_from',
                        (array_key_exists('contract', $product)
                            && array_key_exists('valid_from', $product['contract']))
                            ? $product['contract']['valid_from']
                            : ""
                    )
                    ->setData(
                        'contract_valid_to',
                        (array_key_exists('contract', $product)
                            && array_key_exists('valid_to', $product['contract']))
                            ? $product['contract']['valid_to']
                            : ""
                    )
                    ->setData(
                        'contract_expired',
                        (array_key_exists('contract', $product)
                            && array_key_exists('expired', $product['contract']))
                            ? $product['contract']['expired']
                            : ""
                    )
                    ->setData(
                        'contract_volumetric_pricing',
                        (array_key_exists('contract', $product)
                            && array_key_exists('volumetric_pricing', $product['contract']))
                            ? $product['contract']['volumetric_pricing']
                            : ""
                    )
                    ->setData(
                        'contract_cubing_factor',
                        (array_key_exists('contract', $product)
                            && array_key_exists('cubing_factor', $product['contract']))
                            ? $product['contract']['cubing_factor']
                            : ""
                    )
                    ->setData(
                        'contract_max_item_count',
                        (array_key_exists('contract', $product)
                            && array_key_exists('max_item_count', $product['contract']))
                            ? $product['contract']['max_item_count']
                            : ""
                    )
                    ->setData(
                        'authority_to_leave_threshold',
                        array_key_exists('authority_to_leave_threshold', $product)
                            ? $product['authority_to_leave_threshold']
                            : ""
                    )
                    ->setData(
                        'credit_blocked',
                        array_key_exists('credit_blocked', $product)
                            ? $product['credit_blocked']
                            : ""
                    )
                    ->save();
                $count++;
            }
        }

        return $count;
    }


    /**
     * @param $productGroup
     * @return string
     */
    protected function getAssociatedShippingMethod($productGroup)
    {
        if (stripos($productGroup, 'Parcel') !== false) {
            if (stripos($productGroup, 'Signature') !== false) {
                return "AUS_PARCEL_REGULAR_AUS_SERVICE_OPTION_SIGNATURE_ON_DELIVERY";
            } else {
                return "AUS_PARCEL_REGULAR_AUS_SERVICE_OPTION_STANDARD";
            }
        } elseif (stripos($productGroup, 'Express') !== false) {
            if (stripos($productGroup, 'Signature') !== false) {
                return "AUS_PARCEL_EXPRESS_AUS_SERVICE_OPTION_SIGNATURE_ON_DELIVERY";
            } else {
                return "AUS_PARCEL_EXPRESS_AUS_SERVICE_OPTION_STANDARD";
            }
        } else {
            return "";
        }
    }

    /**
     * Profile main view
     */
    public function viewAction()
    {
        $this->_viewAction();
    }

    /**
     * Generic profile view action
     */
    protected function _viewAction()
    {
        try {
            $product = $this->_initProduct();
            $this->_title($this->__('Australia Post Product'))
                ->_title($this->__('Product #%s', $product->getProductId()));
            $this->_initAction();
            /** @var FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Product_View $block **/
            $block = $this->getLayout()->createBlock('shippedfrom/adminhtml_auspost_product_view');
            $this->_addContent($block);
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $this->_redirect('*/*/');
    }

    /**
     * Instantiate current product and put it into registry
     *
     * @return FactoryX_ShippedFrom_Model_Account_Product
     * @throws Mage_Core_Exception
     */
    protected function _initProduct()
    {
        /** @var FactoryX_ShippedFrom_Model_Account_Product $product */
        $product = Mage::getModel('shippedfrom/account_product')->load($this->getRequest()->getParam('id'));
        if (!$product->getId()) {
            Mage::throwException($this->__('Specified product does not exist.'));
        }

        Mage::register('current_auspost_product', $product);
        return $product;
    }

    /**
     * @function         : exportCsvAction
     * @description      : Export data grid to CSV format
     * @params           : null
     */
    public function exportCsvAction()
    {
        $fileName = sprintf("accounts-%s.csv", gmdate('YmdHis'));
        $blockName = "shippedfrom/adminhtml_account_grid";
        $grid = $this->getLayout()->createBlock($blockName);
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
}