<?php
/**
 * Class FactoryX_CategoryBanners_Block_Adminhtml_Template_Grid_Renderer_Image
 * Custom renderer for the category image
 */
class FactoryX_CategoryBanners_Block_Adminhtml_Template_Grid_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        // We display nothing if there is no image
        if ($row->getImage() == 'no_selection' || $row->getImage() == '' || $row->getImage() == NULL)
        {
            return '';
        }
        else
        {
            // Else we generate the URL then display the image
            $imgUrl = Mage::getBaseUrl('media') . 'categorybanners' . $row->getImage();
            return "<img src=\"$imgUrl\" width=\"94\" height=\"75\"/>";
        }
    }
}
