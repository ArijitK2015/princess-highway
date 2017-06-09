<?php
/**
 * Class FactoryX_CustomGrids_Block_Adminhtml_Renderer_AdminUser
 */
class FactoryX_CustomGrids_Block_Adminhtml_Renderer_AdminUser extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @return array
     */
    public static function getUsers()
	{
        // Get admin users
        $model = Mage::getModel("admin/user");
        $admins = $model->getCollection();
        $result = array();
        foreach($admins as $admin)
		{
            $result[strtolower($admin->getUsername())] = strtolower($admin->getUsername());
        }
        return $result;
    }
}