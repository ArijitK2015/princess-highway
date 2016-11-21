<?php
class FactoryX_Careers_Block_Careers_View extends FactoryX_Careers_Block_Careers_List
{
    protected function _construct()
    {
        $career_id = $this->getRequest()->getParam('id');
        if (!empty($career_id)){
            $career = Mage::getModel('careers/careers')->load($career_id);
            $data = $career->getData();
            $this->setCareer($data);
            $this->setCareerArea($data['area']);
        }
    }

    public function _prepareLayout()
    {

        parent::_prepareLayout();

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
             $breadcrumbs->addCrumb(
                'detail',
                array(
                    'label' => Mage::helper('careers')->__('Detail'),
                    'title' => Mage::helper('careers')->__('Career Detail Page')
                )
            );
        }

        return parent::_prepareLayout();
    }

}