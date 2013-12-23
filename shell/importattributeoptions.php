<?php
		//////////////////////////////////// RESOURCES TO LEARN HOW THIS CAN/COULD/SHOULD WORK ////////////////////////////////////////////
		/// http://www.sharpdotinc.com/mdost/2009/04/06/magento-getting-product-attributes-values-and-labels/
		/// http://stackoverflow.com/questions/3275009/magento-getting-attributes-from-an-attribute-set-without-a-product
		/// http://www.magentocommerce.com/knowledge-base/entry/magento-for-dev-part-8-varien-data-collections/
		/// ** http://www.magentocommerce.com/wiki/4_-_themes_and_template_customization/catalog/get_manufacturers_and_any_attribute_list
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
* Magento
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* DISCLAIMER
* Provided AS IS, no warranty.
*
* @category    Mage
* @package     Mage_Shell
* @copyright   Copyright (c) 2011 Charles Peterson, BrokerBin.com
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
 
require_once 'abstract.php';
 
/**
* Magento Attribute Import Shell Script
*
* @category    Mage
* @package     Mage_Shell
* @author      Charles Peterson <cpeterson@brokerbin.com>
*/
class Mage_Shell_Attributeimport extends Mage_Shell_Abstract
{
	/**
	 * Attribute name with "option" value
	 *
	 * @var string
	 */
	protected $_attribute_with_option_value;
	/**
	 * Attribute Object
	 *
	 * @var eav/entity_attribute
	 */
	protected $_attribute;
 
	/**
	 * Retrieve Attribute Object
	 *
	 * @return eav/entity_attribute
	 */
	protected function _getAttribute()
	{
		if (is_null($this->_attribute_with_option_value)) {
			$this->_log = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $this->_attribute_with_option_value);;
		}
		return $this->_attribute_with_option_value;
	}
 
	/**
	 * Run script
	 *
	 */
	public function run()
	{
		if ($this->getArg('attribute') && $this->getArg('file')) {
			try{
				// need the product typeid to filter the results
				$product = Mage::getModel('catalog/product');
				// build the "query" to get only the attribute we want
				$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
					->setEntityTypeFilter($product->getResource()->getTypeId())
					->addFieldToFilter('attribute_code', $this->getArg('attribute')) // This can be changed to any attribute code
					->load(false);
				// get the first item in the result set.
				$attribute = $attributes->getFirstItem()->setEntity($product->getResource());
				unset($product,$attributes);
				/* @var $attribute Mage_Eav_Model_Entity_Attribute */
				$eoptions = $attribute->getSource()->getAllOptions(false);
				// build existing options array so we can not duplicate the options
				$existing_options=array();
				foreach($eoptions as $opt){
					$existing_options[trim($opt['label'])] = $opt['value'];
				}
				unset($eoptions);
 
				$_new_options = file($this->getArg('file'));
				$options = array('value' => array(), 'order' => array(), 'delete' => array());
				$i = 0;
				foreach($_new_options as $option){
					$option = trim($option);
					if(!isset($existing_options[$option])){
						$i++;
						$options['value']['option_' . $i] = array($option);
					}
				}
				unset($_new_options,$existing_options);
 
				if(count($options['value'])>0){
					$_attribute =  Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $this->getArg('attribute'));
					$_attribute->setOption($options);
					$_attribute->save();
					echo "Options successfully imported.\n";
					echo "attribute: ".$this->getArg('attribute')."\n";
					echo "count: ".count($options['value'])."\n";
					unset($_attribute,$options);
				} else {
					echo "No NEW options to import.\n";
				}
			}catch(Exception $e){
				echo "Import Error::".$e->getMessage()."\n";
			}
		} else {
			echo $this->usageHelp();
		}
	}
 
	/**
	 * Retrieve Usage Help Message
	 *
	 */
	public function usageHelp()
	{
		return <<<USAGE
Usage:  php -f attributeimport.php -- [options]
		php -f attributeimport.php -- --attribute manufacturer --file ../manufacturers.txt
 
  --attribute <attribute>       name of the attribute to update
  --file <file>                 file path to import from, one value per line
  help                          This help
USAGE;
	}
}
 
$shell = new Mage_Shell_Attributeimport();
$shell->run();