<?php

/**
 * Class FactoryX_ShippedFrom_Block_Adminhtml_Auspost
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Auspost extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_auspost';
        $this->_blockGroup = 'shippedfrom';
        $this->_headerText = Mage::helper('shippedfrom')->__('Auspost Shipments');
        parent::__construct();
        $this->_removeButton('add');
        $this->setTemplate('factoryx/shippedfrom/auspost/grid.phtml');
    }

    /**
     * Prepare the layout
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'process_pending_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label' => Mage::helper('shippedfrom')->__('Process Pending Shipping Queue'),
                        'onclick' => "setLocation('" . $this->getUrl('*/*/processPending') . "')",
                        'class' => 'save'
                    )
                )
            );

        $this->setChild(
            'process_pending_labels_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label' => Mage::helper('shippedfrom')->__('Process Pending Labels'),
                        'onclick' => "setLocation('" . $this->getUrl('*/*/processPendingLabels') . "')",
                        'class' => 'save'
                    )
                )
            );

        $this->setChild(
            'process_manifests',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label' => Mage::helper('shippedfrom')->__('Process Manifests'),
                        'onclick' => "setLocation('" . $this->getUrl('*/*/processManifests') . "')",
                        'class' => 'save'
                    )
                )
            );

        return parent::_prepareLayout();
    }

    /**
     * Getter for the process pending button
     */
    public function getProcessPendingButtonHtml()
    {
        return $this->getChildHtml('process_pending_button');
    }

    /**
     * Getter for the process pending labels button
     */
    public function getProcessPendingLabelsButtonHtml()
    {
        return $this->getChildHtml('process_pending_labels_button');
    }

    /**
     * Getter for the process manifests button
     */
    public function getProcessManifestsButtonHtml()
    {
        return $this->getChildHtml('process_manifests');
    }

    /**
     * Getter for the grid HTML
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}