<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Grid_Renderer_LayoutThemes
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Grid_Renderer_LayoutThemes extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) 
	{
		// If the layout of the row is not set we display nothing
		if (!$row->getThemes()) {
			return '';
		}
		else  {
		    $html = $row->getThemes();
		    if (preg_match("/,/", $row->getThemes()) ) {
		        $html = "";
			    $themes = explode(",", $row->getThemes());
			    asort($themes);
			    foreach($themes as $theme) {
			        $html .= $theme . "<br/>";
			    }
			}
			$html = sprintf("<pre>%s</pre>", $html);
			return $html;
		}
    }
}
