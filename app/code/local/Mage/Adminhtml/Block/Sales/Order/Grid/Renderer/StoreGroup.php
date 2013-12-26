<?php
class Mage_Adminhtml_Block_Sales_Order_Grid_Renderer_StoreGroup extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public static function getUsers(){
        // Get admin users
        $model = Mage::getModel("admin/user");
        $admins = $model->getCollection();
        foreach($admins as $admin){
            $result[strtolower($admin->getUsername())] = strtolower($admin->getUsername());
        }
        return $result;
    }

    public static function getStores(){
        $query = 'SELECT title FROM ustorelocator_location'; 
        $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query);
        $result = array();
        foreach ($data as $item){
            $result[$item['title']] = $item['title'];
        }
        return $result;
    }

}