<?php

/**
 * Class FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_FirstName
 */
class FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_FirstName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return mixed|string
     */
    public function render(Varien_Object $row)
    {
        if ($row->getData('type') == 2) 
        {
            $value = $row->getData('customer_firstname');
        }
        else 
        {
            $value = $row->getData('subscriber_firstname');
        }
        return $value;
    }
}