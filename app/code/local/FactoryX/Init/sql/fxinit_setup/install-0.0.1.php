<?php

$email_content = "";

$installer = $this;
$installer->startSetup();

//Import CMS Block
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'cms_block.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	mail('alvin@factoryx.com.au','fx install',$sql);
	$installer->run($sql);
	$email_content .= "Script ran for CMS block import<br/>";
}else{
	$email_content .= "Cannot find CMS block dump<br/>";
}

// Import Email Template
$path = Mage::getBaseDir().'/app/code/local/FactoryX/Init/sql/mysqldump/'.'core_email_template.sql';
if (file_exists($path)) {
	$sql = file_get_contents($path);
	mail('alvin@factoryx.com.au','fx install',$sql);
	$installer->run($sql);
	$email_content .= "Script ran for email template import<br/>";
}else{
	$email_content .= "Cannot find email template dump<br/>";
}

// Create navigation
$installer->run(
		"DELETE FROM pronav;"
	);

try {
    	
    // GET TOP CATEGORIES
    $categories = Mage::getModel('catalog/category')->getCollection()
                    ->addAttributeToFilter('level', array('eq'=>2))
                    ->addAttributeToFilter('is_active', array('eq'=>1))
                    ->addAttributeToFilter('include_in_menu', array('eq'=>1))
                    ->load();
    $sql = '';
    foreach ($categories as $id => $category) {
        $fullcategory = Mage::getModel('catalog/category')->load($category->getId());
        echo $fullcategory->getId() . " ". $fullcategory->getParentId() ." ". $fullcategory->getName()  ." " . $fullcategory->getUrlPath() .  "\n";

        // Create Block for the nav
        $block = Mage::getModel('cms/block');
        $block->setTitle('Pronav - '.$fullcategory->getName());
        $block->setIdentifier('pronav_'.$fullcategory->getId());
        $block->setStores(array(array(0)));
        $block->setIsActive(1);
        $block->setContent('<div class="row">
                <div class="span12">
                <table class="pronav-sub-menu">
                <tr>
                <td>
                <div class="sub-category-menu">
                   <ul>
                        {{widget type="pronav/category_widget_subcategories_list" levels="1" columns="1" thumbnail_images="No" category_images="No" selected_cat="Yes" template="pronav/items/widget/link/subcategories/list.phtml" id_path="category/'.$fullcategory->getId().'"}}
                   </ul>
                </div>
                </td>
                <td>
                     <div class="menu-promo">
                           <!-- SETUP YOUR PIC HERE -->
                           <a href="#small-promo"></a>
                           <!-- END PIC SETUP -->
                     </div>
                </td>
                </tr>
                </table>
                </div>
                </div>');
        $block->save();

        echo $block->getId()."\n";
        $pronav = Mage::getModel('pronav/pronav');
        $pronav_data = array(
                'name' => $fullcategory->getName(),
                'url_key' => $fullcategory->getUrlPath(),
                'i_index' => $fullcategory->getPosition(),
                'store_id' => 0,
                'static_block' => $block->getId(),
                'link' => 1,
                'sub_position' => 1,
                'sub_start' => 1,
                'no_follow' => 1,
                'responsive' => 1,
                'status' => 1
            );
        $pronav->setData($pronav_data);
        $pronav->save();   
    }
    $email_content .= "Created navigation<br/>";
}
catch(Exception $e) {
    var_dump($e);
}

mail('alvin@factoryx.com.au','fx install',$email_content);

$installer->endSetup();

?>