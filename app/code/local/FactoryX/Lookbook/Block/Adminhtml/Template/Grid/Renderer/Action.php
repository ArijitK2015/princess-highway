<?php
/**
 * Adminhtml lookbook templates grid block action item renderer
 *
 * @category   FactoryX
 * @package    FactoryX_Lookbook
 * @author     Factory X Team <developers@factoryx.com.au>
 */

class FactoryX_Lookbook_Block_Adminhtml_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {
        
    public function render(Varien_Object $row)
    {
        //Mage::helper('lookbook')->log(sprintf("%s->row->getId()=%s", __METHOD__, $row->getId()) );

        // for href actions
        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("*/*/edit", array('id' => $row->getId()))
            ),
            '#'	=> Mage::helper('lookbook')->__('Edit')
        );

		if (Mage::app()->isSingleStoreMode())
		{
			if ($row->getLookbookType() == "slideshow") {
				$viewUrl = $this->getUrl("lookbook/index/slideshow", array('id' => $row->getId(),'_store' => 'default'));
			}
			elseif ($row->getLookbookType() == "flipbook") {
				$viewUrl = $this->getUrl("lookbook/index/flipbook", array('id' => $row->getId(),'_store' => 'default'));
			}
			else {
			    $viewUrl = $this->getUrl("lookbook/index/view", array('id' => $row->getId(),'_store' => 'default'));
			}
		}
		else {
			// Database resources
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			
			// Get the contest related stores
			$query = "SELECT store_id FROM {$resource->getTableName('lookbook/store')} WHERE lookbook_id = {$row->getId()}";
			
			// We use the first store URL even if there's several stores for the same lookbook
			$lookbookStoreId = $readConnection->fetchOne($query);
								
			if ($lookbookStoreId) {
				if ($row->getLookbookType() == "slideshow") {
					$viewUrl = $this->getUrl("lookbook/index/slideshow", array('id' => $row->getId(),'_store'=>$lookbookStoreId));
				}
				elseif ($row->getLookbookType() == "flipbook") {
					$viewUrl = $this->getUrl("lookbook/index/flipbook", array('id' => $row->getId(),'_store'=>$lookbookStoreId));
				}
				else {
				    $viewUrl = Mage::getUrl("lookbook/index/view",array('id' => $row->getId(),'_store'=>$lookbookStoreId));
				}
			}
			else  {
				if ($row->getLookbookType() == "slideshow") {
					$viewUrl = Mage::getUrl("lookbook/index/slideshow", array('id' => $row->getId(),'_store' => 'default'));
				}
				elseif ($row->getLookbookType() == "flipbook") {
					$viewUrl = Mage::getUrl("lookbook/index/flipbook", array('id' => $row->getId(),'_store' => 'default'));
				}
				else {
				    $viewUrl = Mage::getUrl("lookbook/index/view", array('id' => $row->getId(),'_store' => 'default'));
				}
			}
		}
		
		// link to the frontend view
        $actions[] = array(
            '@' => array(
                'href'  => $viewUrl,
                'target'=>	'_blank'
            ),
            '#'	=> Mage::helper('lookbook')->__('View')
        );
        

		// link to the facebook frontend view
		if ($row->getLookbookFacebook()) {
    		$facebookViewUrl = preg_replace("/index/i", "facebook", $viewUrl);
            $actions[] = array(
                '@' => array(
                    'href'  => $facebookViewUrl,
                    'target'=>'_blank'
                ),
                '#'	=> Mage::helper('lookbook')->__('Facebook')
            );
        }
                
        return $this->_actionsToHtml($actions);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        //Mage::helper('lookbook')->log(sprintf("%s->actions=%s", __METHOD__, print_r($actions, true)) );
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }

}
