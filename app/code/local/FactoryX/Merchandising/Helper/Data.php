<?php

/**
 * Class FactoryX_Merchandising_Helper_Data
 */
class FactoryX_Merchandising_Helper_Data extends Mage_Core_Helper_Abstract
{
    
    protected $logFileName = 'factoryx_merchandising.log';

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
    
    /**
     * @param $rootCategory
     * @param $currentLevel
     * @param $maxLevel
     * @param $output
     * @return string
     */
    public function renderChildren($rootCategory,$currentLevel,$maxLevel,$output){
		$buffer = "<div class='category' cat_id='".$rootCategory->getId()."'>";
		for ($i=1;$i<$currentLevel;$i++){
			$buffer .= "---";
		}
		$buffer .= " ".$rootCategory->getName()."</div>";
		$output .= $buffer;
		$subCats = $rootCategory->getChildrenCategories();
		if ((count($subCats) == 0) || ($currentLevel > $maxLevel)) {
			return $output;
		}
        foreach ($subCats as $subCat){
            $childCategory = Mage::getModel('catalog/category')->load($subCat->getId());
            $output = $this->renderChildren($childCategory,$currentLevel+1,$maxLevel,$output);
        }
        return $output;
	}

}