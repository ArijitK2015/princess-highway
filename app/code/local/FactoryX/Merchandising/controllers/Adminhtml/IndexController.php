<?php

class FactoryX_Merchandising_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {    	
    	$this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction(){
    	$cat_id = $this->getRequest()->getParam('cat_id');

    	$readConnection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $query = "SELECT product_id,position FROM catalog_category_product WHERE category_id=".$cat_id." ORDER BY position";
        $data = $readConnection->fetchAll($query);

    	$this->loadLayout();    	
        $view = $this->getLayout()->getBlock('merchandising');
        $view->setData('cat_id',$cat_id);
        $view->setData('products',$data);
        $this->renderLayout();
    }

    public function saveAction(){
    	$pos_array = explode(",",$this->getRequest()->getPost('prod'));
    	$cat_id=$this->getRequest()->getPost('cat_id');
        $invalid_ids= unserialize(stripslashes($this->getRequest()->getPost('invalid_ids')));
    	$max_count = count($pos_array)+1;
    	$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $query = "UPDATE catalog_category_product SET position = $max_count WHERE category_id = $cat_id";
        $writeConnection->query($query);	
        foreach ($pos_array as $position => $product_id) {
        	$truePosition = $position+1;
        	$query = "UPDATE catalog_category_product SET position = $truePosition WHERE category_id = $cat_id AND product_id = $product_id";
        	$writeConnection->query($query); 	
        }
        if ((is_array($invalid_ids)) && (count($invalid_ids) > 0)) {
            foreach($invalid_ids as $invalid_id){
                $query = "DELETE FROM catalog_category_product WHERE category_id = $cat_id AND product_id = $invalid_id";
                $writeConnection->query($query);
            }
        }
        Mage::getSingleton('adminhtml/session')->addSuccess('Product Merchandising Saved');
        $this->_redirect('*/*/view', array('cat_id'=>$cat_id));
    }


}