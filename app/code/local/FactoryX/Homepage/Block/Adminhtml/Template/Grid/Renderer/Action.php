<?php
/**
 * Adminhtml homepage templates grid block action item renderer
 *
 * @category   FactoryX
 * @package    FactoryX_Homepage
 * @author     Factory X Team <developers@factoryx.com.au>
 */

class FactoryX_Homepage_Block_Adminhtml_Template_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action 
{
    /**
	 *	Renderer for the action column
	 */
    public function render(Varien_Object $row)
    {
        // Mage::helper('homepage')->log(sprintf("%s->row->getId()=%s", __METHOD__, $row->getId()) );

        // Edit action
        $actions[] = array(
            '@' => array(
                'href'  => $this->getUrl("*/*/edit", array('id' => $row->getId()))
            ),
            '#'	=> Mage::helper('homepage')->__('Edit')
        );

		if (Mage::app()->isSingleStoreMode())
		{
			$previewUrl = Mage::getUrl("homepage/index/preview",
			    array(
			        'id'        => $row->getId(),
			        '_store'    => 0 // 'default' causes Mage_Core_Model_Store_Exception
			    )
            );
		}
		else
		{
			// Database resources
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			
			// Get the contest related stores
			$query = "SELECT store_id 
						FROM {$resource->getTableName('homepage/store')}
						WHERE homepage_id = {$row->getId()}";
			
			// We use the first store URL even if there's several stores for the same homepage
			$homepageStoreId = $readConnection->fetchOne($query);
            
            /**
            Mage::getUrl(routePath, routeParams)
            routeParams = array(
                _store int or string, Either the numeric store ID or textual store code. It will use the correct domain as the base
            )
            */
			if ($homepageStoreId) {
                $previewUrl = Mage::getUrl(
                    "homepage/index/preview",
                    array(
                        'id'        => $row->getId(),
                        '_store'    => $homepageStoreId
                    )
                );
            }
			else {
                $previewUrl = Mage::getUrl(
                    "homepage/index/preview",
                    array(
                        'id'        => $row->getId(),
                        '_store'    => 0 // 'default' causes Mage_Core_Model_Store_Exception
                    )
                );
            }
		}
		
		// Preview action
		$actions[] = array(
			'@' => array(
				'href'  => $previewUrl,
				'target'=> '_blank'
			),
			'#'	=> Mage::helper('homepage')->__('Preview')
		);
        return $this->_actionsToHtml($actions);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        // Mage::helper('homepage')->log(sprintf("%s->actions=%s", __METHOD__, print_r($actions, true)) );
        $html = array();
        $attributesObject = new Varien_Object();
        foreach ($actions as $action) {
            $attributesObject->setData($action['@']);
            $html[] = '<a ' . $attributesObject->serialize() . '>' . $action['#'] . '</a>';
        }
        return implode(' <span class="separator">&nbsp;|&nbsp;</span> ', $html);
    }

}
