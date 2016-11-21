<?php
class FactoryX_Careers_Block_Careers_List extends FactoryX_Careers_Block_Careers
{
    protected function _construct()
    {
        $collection =  Mage::getModel('careers/careers')->getCollection()
            ->addFieldToFilter('status',1)->setOrder('sort','asc');
        $area = $this->getRequest()->getParam('area');
        if (!empty($area) && $area != 'all areas') {
            $collection->addFieldToFilter('area', $area);
        } else {
            $area = 'all areas';
        }
        $this->setCareerArea($area);
        $this->setCollection($collection);
    }

    public function _prepareLayout()
    {

        parent::_prepareLayout();

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $area = $this->getCareerArea();
            $breadcrumbs->addCrumb(
                'list',
                array(
                    'label' => Mage::helper('careers')->__($area),
                    'title' => Mage::helper('careers')->__('Career List Page'),
                    'link'  => $this->getUrl('careers/index/list', array('area' => $area))
                )
            );
        }

        return parent::_prepareLayout();
    }
}