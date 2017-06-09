<?php
class FactoryX_Careers_Block_Careers extends Mage_Core_Block_Template
{
    /**
     * Add breadcrumbs
     */
    public function _prepareLayout() {

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs)
        {
            $breadcrumbs->addCrumb(
                'home',
                array(
                    'label' => Mage::helper('careers')->__('Home'),
                    'title' => Mage::helper('careers')->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
                )
            )->addCrumb(
                'careers',
                array(
                    'label' => Mage::helper('careers')->__('Careers'),
                    'title' => Mage::helper('careers')->__('Go to the Careers page'),
                    'link' => Mage::getUrl("careers")
                )
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Get areas from available careers
     */
    public function getAvailableCareerAreas()
    {
        $collection =  Mage::getModel('careers/careers')->getCollection()->addFieldToSelect('area')->addFieldToFilter('status',1);
        $areas = array();
        foreach ($collection as $career)
        {
            if (!in_array($career->getArea(),$areas))
            {
                $areas[] = $career->getArea();
            }
            else continue;
        }
        return $areas;
    }
}