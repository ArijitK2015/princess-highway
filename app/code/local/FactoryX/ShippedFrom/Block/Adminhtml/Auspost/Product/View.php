<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Product_View
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Product_View extends Mage_Core_Block_Template
{

    /**
     * @var FactoryX_ShippedFrom_Model_Account_Product
     */
    protected $_product = null;

    /**
     * Whether the block should be used to render $_info
     *
     * @var bool
     */
    protected $_shouldRenderInfo = false;

    /**
     * Information to be rendered
     *
     * @var array
     */
    protected $_info = array();

    /**
     * Prepare main view data
     */
    public function prepareViewData()
    {        
        $backUrl = Mage::helper("adminhtml")->getUrl(
            'adminhtml/shippedfromaccount/edit',
            array(
                'id' => $this->_product->getData('associated_account')
            )
        );
        
        $this->addData(
            array(
                'type'                                  => $this->_product->getData('type'),
                'group'                                 => $this->_product->getData('group'),
                'product_id'                            => $this->_product->getData('product_id'),
                'associated_shipping_method'            => $this->_product->getData('associated_shipping_method'),                
                'associated_account'                    => $this->_product->getData('associated_account'),                
                'option_signature_on_delivery_option'   => $this->_product->getData('option_signature_on_delivery_option'),
                'option_authority_to_leave_option'      => $this->_product->getData('option_authority_to_leave_option'),
                'contract_valid_from'                   => $this->_product->getData('contract_valid_from'),
                'contract_valid_to'                     => $this->_product->getData('contract_valid_to'),
                'contract_expired'                      => ($this->_product->getData('contract_expired') ? 'YES' : 'NO'),
                'contract_volumetric_pricing'           => $this->_product->getData('contract_volumetric_pricing'),
                'contract_cubing_factor'                => $this->_product->getData('contract_cubing_factor'),
                'contract_max_item_count'               => $this->_product->getData('contract_max_item_count'),
                'authority_to_leave_threshold'          => $this->_product->getData('authority_to_leave_threshold'),
                'credit_blocked'                        => $this->_product->getData('credit_blocked'),
                //'can_delete'                            => $this->_product->canDelete(),
                //'delete_url'                            => $this->getUrl('*/*/updateState', array('profile' => $this->_product->getId(), 'action' => 'cancel')),
                'back_url'                              => $backUrl,
                //'confirmation_message'                  => Mage::helper('sales')->__('Are you sure you want to do this?')
            )
        );
    }

    /**
     * Getter for rendered info, if any
     *
     * @return array
     */
    public function getRenderedInfo()
    {
        return $this->_info;
    }

    /**
     * Prepare product main info
     */
    public function prepareReferenceInfo()
    {
        $this->_shouldRenderInfo = true;

        $fields = array(
            'type',
            'group',
            'product_id',
            'associated_shipping_method',
            'associated_account',
            'option_signature_on_delivery_option',
            'option_authority_to_leave_option',
            'contract_valid_from',
            'contract_valid_to',
            'contract_expired',
            'contract_volumetric_pricing',
            'contract_cubing_factor',
            'contract_max_item_count',
            'authority_to_leave_threshold',
            'credit_blocked'
        );
        //$fields = array_keys($this->getData());

        foreach ($fields as $key) {
            //Mage::helper('shippedfrom')->log(sprintf("%s->%s", __METHOD__, $key) );
            $info = array(
                'label' => $this->_product->getFieldLabel($key),
                'value' => $this->_product->getData($key)
                //'value' => $this->_product->renderData($key),
            );
            $this->_addInfo($info);
        }
    }

    /**
     * Get rendered row value
     *
     * @param Varien_Object $row
     * @return string
     */
    public function renderRowValue(Varien_Object $row)
    {
        $value = $row->getValue();
        if (is_array($value)) {
            $value = implode("\n", $value);
        }

        if (!$row->getSkipHtmlEscaping()) {
            $value = $this->escapeHtml($value);
        }

        return nl2br($value);
    }

    /**
     * Prepare related orders collection
     *
     * @param array|string $fieldsToSelect
     */
    protected function _prepareRelatedOrders($fieldsToSelect = '*')
    {
        if (null === $this->_relatedOrders) {
            $this->_relatedOrders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect($fieldsToSelect)
                ->addFieldToFilter('customer_id', Mage::registry('current_customer')->getId())
                ->addRecurringProfilesFilter($this->_product->getId())
                ->setOrder('entity_id', 'desc');
        }
    }

    /**
     * Add specified data to the $_info
     *
     * @param array $data
     * @param string $key = null
     */
    protected function _addInfo(array $data, $key = null)
    {
        $object = new Varien_Object($data);
        if ($key) {
            $this->_info[$key] = $object;
        } else {
            $this->_info[] = $object;
        }
    }

    /**
     * Get current profile from registry and assign store/locale information to it
     */
    protected function _prepareLayout()
    {
        $this->_product = Mage::registry('current_auspost_product');
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_product || !$this->_shouldRenderInfo) {
            return '';
        }
        $html = parent::_toHtml();
        return $html;
    }
}
