<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Queue_View
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Queue_View extends Mage_Core_Block_Template
{

    /**
     * @var FactoryX_ShippedFrom_Model_Shipping_Queue
     */
    protected $_schedule = null;

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
        $backUrl = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        
        $this->addData(
            array(
                'status' => $this->_schedule->getData('status'),
                'shipment_id' => $this->_schedule->getData('shipment_id'),
                'ap_shipment_id' => $this->_schedule->getData('ap_shipment_id'),
                'ap_request_id' => $this->_schedule->getData('ap_request_id'),
                'ap_consignment_id' => $this->_schedule->getData('ap_consignment_id'),
                'ap_order_id' => $this->_schedule->getData('ap_order_id'),
                'ap_label_uri' => $this->_schedule->getData('ap_label_uri'),
                'shipped_from' => $this->_schedule->getData('shipped_from'),
                'ap_last_message' => $this->_schedule->getData('ap_last_message'),
                'created_at' => $this->_schedule->getData('created_at'),
                'json_request' => $this->_schedule->getData('json_request'),
                'ap_last_url' => $this->_schedule->getData('ap_last_url'),
                'local_label_link' => $this->_schedule->getData('local_label_link'),
                //'can_delete'                            => $this->_schedule->canDelete(),
                //'delete_url'                            => $this->getUrl('*/*/updateState', array('profile' => $this->_schedule->getId(), 'action' => 'cancel')),
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
     * Prepare profile main reference info
     */
    public function prepareReferenceInfo()
    {
        $this->_shouldRenderInfo = true;

        $fields = array('schedule_id','status','shipment_id','ap_shipment_id','ap_request_id','ap_consignment_id','ap_order_id','ap_label_uri','shipped_from','ap_last_message','created_at','json_request','ap_last_url','local_label_link');
        //$fields = array_keys($this->getData());

        foreach ($fields as $key) {
            //Mage::helper('shippedfrom')->log(sprintf("%s->%s", __METHOD__, $key) );
            $info = array(
                'label' => $this->_schedule->getFieldLabel($key),
                'value' => $this->_schedule->renderData($key)
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
        $this->_schedule = Mage::registry('current_auspost_schedule');
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_schedule || !$this->_shouldRenderInfo) {
            return '';
        }

        return parent::_toHtml();
    }
}
