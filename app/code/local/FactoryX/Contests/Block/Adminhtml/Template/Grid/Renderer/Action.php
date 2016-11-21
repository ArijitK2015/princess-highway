<?php
/**
 * Adminhtml contests templates grid block action item renderer
 *
 * @category   FactoryX
 * @package    FactoryX_Contests
 * @author     Factory X Team <developers@factoryx.com.au>
 */

class FactoryX_Contests_Block_Adminhtml_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {

    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        //Mage::helper('contests')->log(sprintf("%s->row->getId()=%s", __METHOD__, $row->getId()) );

        // for href actions
        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("*/*/edit", array('id' => $row->getId()))
            ),
            '#'	=> Mage::helper('contests')->__('Edit')
        );
		
		if (Mage::app()->isSingleStoreMode())
		{
			$viewUrl = $this->getUrl("contests/index/view", array('id' => $row->getId(),'_store' => 'default'));
		}
		else
		{
			// Database resources
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			
			// Get the contest related stores
			$query = "SELECT store_id 
						FROM {$resource->getTableName('contests/store')}
						WHERE contest_id = {$row->getId()}";
			
			// We use the first store URL even if there's several stores for the same lookbook
			$contestStoreId = $readConnection->fetchOne($query);
								
			if ($contestStoreId)
			{
				$viewUrl = Mage::getUrl("contests/index/view",array('id' => $row->getId(),'_store'=>$contestStoreId));
			}
			else 
			{
				$viewUrl = Mage::getUrl("contests/index/view", array('id' => $row->getId(),'_store' => 'default'));
			}
		}

        $actions[] = array(
            '@' => array(
                'href'  => $viewUrl,
                'target'=>	'_blank',
                'rel'   =>  'noopener noreferrer'
            ),
            '#'	=> Mage::helper('contests')->__('View')
        );
        
		// link to the facebook frontend view		
		if ($row->getData('facebook_app_id')) {
    		$facebookViewUrl = preg_replace("/index/i", "facebook", $viewUrl);
            $actions[] = array(
                '@' => array(
                    'href'  => $facebookViewUrl,
                    'target'=>'_blank',
					'rel'   =>	'noopener noreferrer'
                ),
                '#'	=> Mage::helper('lookbook')->__('Facebook')
            );
        }
                
        return $this->_actionsToHtml($actions);
    }

    /**
     * @param $value
     * @return string
     */
    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    /**
     * @param array $actions
     * @return string
     */
    protected function _actionsToHtml(array $actions)
    {
        //Mage::helper('contests')->log(sprintf("%s->actions=%s", __METHOD__, print_r($actions, true)) );
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">|</span> ', $html);
    }

}
