<?php

/**
 * Class FactoryX_InitPH_Helper_Data
 */
class FactoryX_InitPH_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Log file name
     */
    const LOG_FILE = 'factoryx_initph.log';

    private $groupsClothing = array(
        1 => "Descriptions",
        2 => "Clothing Attributes"
    );

    private $groupsShoes = array(
        1 => "Descriptions",
        2 => "Shoe Attributes"
    );

    private $groupsAccessories = array(
        1 => "Descriptions",
        2 => "Accessory Attributes"
    );

    private $attributeSetsToAdd = array(
        'Clothing Colour & Size 28-36' => array(
            1 => array('size_and_fit'),
            2 => array('colour_description', 'size_mens_28_to_36', 'colour_base', 'season', 'available_date', 'online_only', 'in_store_only')
        ),
        'Clothing Colour & Size 06-16' => array(
            1 => array('size_and_fit'),
            2 => array('colour_description', 'size_06_to_16', 'colour_base', 'season', 'available_date', 'online_only', 'in_store_only')
        ),
        'Clothing Colour & Size SMLXMLXXL' => array(
            1 => array('size_and_fit'),
            2 => array('colour_description', 'size_smlxl', 'colour_base', 'season', 'available_date', 'online_only', 'in_store_only')
        ),
        'Shoes Colour & Size 03 to 14' => array(
            1 => array('size_and_fit'),
            2 => array('colour_description', 'size_shoes_girls_08_to_14', 'colour_base', 'season', 'available_date', 'online_only', 'in_store_only')
        ),
        'Shoes Colour & Size 36 to 41' => array(
            1 => array('size_and_fit'),
            2 => array('colour_description', 'size_shoes_girls_36_to_41', 'colour_base', 'season', 'available_date', 'online_only', 'in_store_only')
        ),
        'Accessories Colour' => array(
            1 => array('size_and_fit'),
            2 => array('colour_description', 'colour_base', 'season', 'available_date', 'online_only', 'in_store_only')
        ),
        'Accessories Colour & Size SML' => array(
            1 => array('size_and_fit'),
            2 => array('colour_description', 'colour_base', 'accessories_size', 'season', 'available_date', 'online_only', 'in_store_only')
        )
    );

    private $attributesToAdd = array(
        "size_and_fit" => array(
            "group" => 1,
            "label" => "Size & Fit",
            "values" => array(
                "is_global"             => 0,
                "frontend_input"        => "textarea",
                "source_model"          => NULL,
                "backend_type"          => "text",
                //"is_visible"            => 1,
                "is_required"           => 0,
                "is_configurable"       => 0,
                "is_visible_on_front"   => 1
            )
        ),
        "season" => array(
            "group" => 2,
            "label" => "Season",
            "options" => array(
                "aw10",
                "ss10",
                "aw11",
                "ss11",
                "aw12",
                "ss12",
                "aw13",
                "ss13",
                "aw14",
                "ss14",
                "aw15",
                "ss15",
                "aw16",
                "ss16",
                "aw17",
                "ss17"
            ),
            "values" => array(
                "is_global"             => 0,
                "frontend_input"        => "select",
                "source_model"          => "eav/entity_attribute_source_table",
                "backend_type"          => "int",
                "is_required"           => 1,
                "is_configurable"       => 0,
                "is_visible_on_front"   => 1
            )
        ),
        "size_06_to_16" => array(
            "group" => 2,
            "label" => "Size 06-16",
            "options" => array(
                "06" => 6,
                "07" => 7,
                "08" => 8,
                "09" => 9,
                "10" => 10,
                "11" => 11,
                "12" => 12,
                "13" => 13,
                "14" => 14,
                "16" => 16
            ),
            "values" => array(
                "is_global"         => 1, // store view = 0, is_global = 1, website = 2
                "frontend_input"    => "select",
                "source_model"      => "eav/entity_attribute_source_table",
                "backend_type"      => "int",
                "is_required"       => 1,
                "is_configurable"   => 1,
                "is_visible_on_front"   => 1,
                'is_filterable'             => 1,
                'is_filterable_in_search'   => 1,
                'apply_to'          => NULL
            )
        ),
        "size_mens_28_to_36" => array(
            "group" => 2,
            "label" => "Size Man 28-36",
            "options" => array(
                "24" => 24,
                "25" => 25,
                "26" => 26,
                "27" => 27,
                "28" => 28,
                "29" => 29,
                "30" => 30,
                "31" => 31,
                "32" => 32,
                "33" => 33,
                "34" => 34,
                "35" => 35,
                "36" => 36
            ),
            "values" => array(
                "is_global"         => 1, // store view = 0, is_global = 1, website = 2
                "frontend_input"    => "select",
                "source_model"      => "eav/entity_attribute_source_table",
                "backend_type"      => "int",
                "is_required"       => 1,
                "is_configurable"   => 1,
                "is_visible_on_front"   => 1,
                'is_filterable'             => 1,
                'is_filterable_in_search'   => 1,
                'apply_to'          => NULL
            )
        ),
        "size_shoes_girls_36_to_41" => array(
            "group" => 2,
            "label" => "Shoe Size Woman 36-14",
            "options" => array(36,37,38,39,40,41),
            "values" => array(
                "is_global"         => 1, // store view = 0, is_global = 1, website = 2
                "frontend_input"    => "select",
                "source_model"      => "eav/entity_attribute_source_table",
                "backend_type"      => "int",
                "is_required"       => 1,
                "is_configurable"   => 1,
                "is_visible_on_front"   => 1,
                'is_filterable'             => 1,
                'is_filterable_in_search'   => 1,
                'apply_to'          => NULL
            )
        ),
        "size_smlxl" => array(
            "group" => 2,
            "label" => "Size SMLXL",
            "options" => array("XS","ML","SM","S","SM","M","L","XL","XXL"),
            "values" => array(
                "is_global"         => 1, // store view = 0, is_global = 1, website = 2
                "frontend_input"    => "select",
                "source_model"      => "eav/entity_attribute_source_table",
                "backend_type"      => "int",
                "is_required"       => 1,
                "is_configurable"   => 1,
                "is_visible_on_front"   => 1,
                'is_filterable'             => 1,
                'is_filterable_in_search'   => 1,
                'apply_to'          => NULL
            )
        ),
        "size_shoes_girls_08_to_14" => array(
            "group" => 2,
            "label" => "Shoe Size Woman 08-14",
            "options" => array(3,4,5,6,7,8,9,10,11,12,13,14),
            "values" => array(
                "is_global"         => 1, // store view = 0, is_global = 1, website = 2
                "frontend_input"    => "select",
                "source_model"      => "eav/entity_attribute_source_table",
                "backend_type"      => "int",
                "is_required"       => 1,
                "is_configurable"   => 1,
                "is_visible_on_front"   => 1,
                'is_filterable'             => 1,
                'is_filterable_in_search'   => 1,
                'apply_to'          => NULL
            )
        ),
        "accessories_size" => array(
            "group" => 2,
            "label" => "Accessory Size",
            "options" => array("Small","Small/Medium","Medium","Medium/Large","Large"),
            "values" => array(
                "is_global"         => 1, // store view = 0, is_global = 1, website = 2
                "frontend_input"    => "select",
                "source_model"      => "eav/entity_attribute_source_table",
                "backend_type"      => "int",
                "is_required"       => 1,
                "is_configurable"   => 1,
                "is_visible_on_front"   => 1,
                'is_filterable'             => 1,
                'is_filterable_in_search'   => 1,
                'apply_to'          => NULL
            )
        ),
        "colour_base" => array(
            "group" => 2,
            "label" => "Colour",
            "values" => array(
                'backend_model'             => 'eav/entity_attribute_backend_array',
                'backend_type'              => 'varchar',
                'frontend_input'            => "multiselect",
                "is_required"               => 1,
                "is_global"                 => 0,
                'apply_to'                  => 'simple,configurable',
                'is_configurable'           => 0,
                'is_filterable'             => 1,
                'is_filterable_in_search'   => 1,
                'is_visible_on_front'       => 1,
            ),
            "options" => array(
                "black",
                "blue",
                "brown",
                "green",
                "grey",
                "metallic",
                "multi",
                "neutral",
                "orange",
                "pink",
                "purple",
                "red",
                "white",
                "yellow"
            )
        ),
        "colour_description" => array(
            "group" => 2,
            "label" => "Colour",
            "values" => array(
                "is_global"         => 1, // store view = 0, is_global = 1, website = 2
                "frontend_input"    => "select",
                "source_model"      => "eav/entity_attribute_source_table",
                "backend_type"      => "int",
                "is_required"       => 1,
                "is_configurable"   => 1,
                "is_visible_on_front"   => 1,
                'is_filterable'             => 1,
                'is_filterable_in_search'   => 1,
                'apply_to'          => NULL
            ),
            "options" => array(
                'Airforce' => 'Airforce',
                'Amber' => 'Amber',
                'amethyst' => 'amethyst',
                'Antique Rose' => 'Antique Rose',
                'Apricot' => 'Apricot',
                'Aqua' => 'Aqua',
                'aqua-green' => 'aqua-green',
                'Army' => 'Army',
                'Army Green' => 'Army Green',
                'Aubergine' => 'Aubergine',
                'Baby Pink' => 'Baby Pink',
                'Baby-blue' => 'Baby-blue',
                'Beige' => 'Beige',
                'Berry' => 'Berry',
                'Black' => 'Black',
                'Black Brown' => 'Black Brown',
                'Black Khaki' => 'Black Khaki',
                'Black Lilac' => 'Black Lilac',
                'Black Purple' => 'Black Purple',
                'Black Shiny' => 'Black Shiny',
                'Black Stripe' => 'Black Stripe',
                'Black/Charcoal' => 'Black/Charcoal',
                'Black/Cream' => 'Black/Cream',
                'Black/Flesh' => 'Black/Flesh',
                'Black/Fushia' => 'Black/Fushia',
                'Black/Gold' => 'Black/Gold',
                'Black/Green' => 'Black/Green',
                'Black/Grey' => 'Black/Grey',
                'Black/Mustard' => 'Black/Mustard',
                'Black/Natural' => 'Black/Natural',
                'Black/Navy' => 'Black/Navy',
                'Black/Olive' => 'Black/Olive',
                'Black/Orange' => 'Black/Orange',
                'Black/Pink' => 'Black/Pink',
                'Black/Red' => 'Black/Red',
                'Black/Silver' => 'Black/Silver',
                'Black/Tan' => 'Black/Tan',
                'Black/White' => 'Black/White',
                'Black/Yellow' => 'Black/Yellow',
                'Blackberry' => 'Blackberry',
                'Blue' => 'Blue',
                'Blue Grey' => 'Blue Grey',
                'Blue/Black' => 'Blue/Black',
                'Blue/Cream' => 'Blue/Cream',
                'Blue/Navy' => 'Blue/Navy',
                'Blue/Orange' => 'Blue/Orange',
                'Blue/Red' => 'Blue/Red',
                'Blue/White' => 'Blue/White',
                'Blue/Yellow' => 'Blue/Yellow',
                'Bluebird' => 'Bluebird',
                'Blush' => 'Blush',
                'Bone' => 'Bone',
                'Bordeaux' => 'Bordeaux',
                'Bottle' => 'Bottle',
                'Brick' => 'Brick',
                'Bronze' => 'Bronze',
                'Brown' => 'Brown',
                'Brown/Black' => 'Brown/Black',
                'Brown/Cream' => 'Brown/Cream',
                'Brown/Stone' => 'Brown/Stone',
                'Brown/Tan' => 'Brown/Tan',
                'Burgundy' => 'Burgundy',
                'Burgundy Navy' => 'Burgundy Navy',
                'Burnt Orange' => 'Burnt Orange',
                'Camel' => 'Camel',
                'Camo' => 'Camo',
                'Caramel' => 'Caramel',
                'Chambray' => 'Chambray',
                'Charcoal' => 'Charcoal',
                'Chartreuse' => 'Chartreuse',
                'Check' => 'Check',
                'Cherry' => 'Cherry',
                'cherry red' => 'cherry red',
                'chilli' => 'chilli',
                'Choc Tan' => 'Choc Tan',
                'Choc/Orange' => 'Choc/Orange',
                'Chocolate' => 'Chocolate',
                'Cinnamon' => 'Cinnamon',
                'Citrus' => 'Citrus',
                'Clear' => 'Clear',
                'Cobalt' => 'Cobalt',
                'Coffee' => 'Coffee',
                'Coral' => 'Coral',
                'Coral/Creme' => 'Coral/Creme',
                'Cream' => 'Cream',
                'Cream/Black' => 'Cream/Black',
                'Cream/Blue' => 'Cream/Blue',
                'Creme' => 'Creme',
                'Creme/Black' => 'Creme/Black',
                'Crimson' => 'Crimson',
                'Dark Blue' => 'Dark Blue',
                'Dark Brown' => 'Dark Brown',
                'Dark Denim' => 'Dark Denim',
                'Dark Green' => 'Dark Green',
                'Dark Grey' => 'Dark Grey',
                'Dark Navy' => 'Dark Navy',
                'dark pink' => 'dark pink',
                'Dark Red' => 'Dark Red',
                'Deep Blue' => 'Deep Blue',
                'Deep Purple' => 'Deep Purple',
                'Deep Red' => 'Deep Red',
                'Denim' => 'Denim',
                'Donkey' => 'Donkey',
                'Dot' => 'Dot',
                'Dusty Blue' => 'Dusty Blue',
                'Dusty Pink' => 'Dusty Pink',
                'dusty-rose' => 'dusty-rose',
                'Electric Blue' => 'Electric Blue',
                'Emerald' => 'Emerald',
                'Evergreen' => 'Evergreen',
                'Flame' => 'Flame',
                'Floral' => 'Floral',
                'Forest' => 'Forest',
                'Fuchsia' => 'Fuchsia',
                'Fuschia' => 'Fuschia',
                'geometric' => 'geometric',
                'Gold' => 'Gold',
                'Gold/Black' => 'Gold/Black',
                'Gold/Silver' => 'Gold/Silver',
                'Grape' => 'Grape',
                'Green' => 'Green',
                'Green Black' => 'Green Black',
                'Green/Creme' => 'Green/Creme',
                'Green/Grey' => 'Green/Grey',
                'Green/Pink' => 'Green/Pink',
                'Green/Purple' => 'Green/Purple',
                'green/white' => 'green/white',
                'Grey' => 'Grey',
                'Grey Blue' => 'Grey Blue',
                'Grey Check' => 'Grey Check',
                'Grey Marle' => 'Grey Marle',
                'Grey Olive' => 'Grey Olive',
                'grey red ' => 'grey red ',
                'grey red' => 'grey red',
                'Grey/Black' => 'Grey/Black',
                'Grey/Brown' => 'Grey/Brown',
                'Grey/Cream' => 'Grey/Cream',
                'Grey/Orange' => 'Grey/Orange',
                'Grey/Pink' => 'Grey/Pink',
                'Grey/Red' => 'Grey/Red',
                'Gunmetal' => 'Gunmetal',
                'Honey' => 'Honey',
                'Hot Pink' => 'Hot Pink',
                'Houndstooth' => 'Houndstooth',
                'Ice' => 'Ice',
                'Ice Blue' => 'Ice Blue',
                'Ice Pink' => 'Ice Pink',
                'Indigo' => 'Indigo',
                'Ink' => 'Ink',
                'Ivory' => 'Ivory',
                'Jade' => 'Jade',
                'Khaki' => 'Khaki',
                'Latte' => 'Latte',
                'Lavender' => 'Lavender',
                'Lemon' => 'Lemon',
                'Leopard' => 'Leopard',
                'Light Beige' => 'Light Beige',
                'Light Blue' => 'Light Blue',
                'Light Brown' => 'Light Brown',
                'Light Denim' => 'Light Denim',
                'Light Green' => 'Light Green',
                'Light Grey' => 'Light Grey',
                'Light Indigo' => 'Light Indigo',
                'Light Pink' => 'Light Pink',
                'light-denim' => 'light-denim',
                'Lilac' => 'Lilac',
                'Lime' => 'Lime',
                'Magenta' => 'Magenta',
                'Marigold' => 'Marigold',
                'Maroon' => 'Maroon',
                'Maroon/Black' => 'Maroon/Black',
                'Maroon/Pink' => 'Maroon/Pink',
                'Mauve' => 'Mauve',
                'Melon' => 'Melon',
                'Mid Blue' => 'Mid Blue',
                'Midnight' => 'Midnight',
                'Mint' => 'Mint',
                'Mint/Green' => 'Mint/Green',
                'Mocha' => 'Mocha',
                'Moss' => 'Moss',
                'Mud' => 'Mud',
                'Mulit Dark' => 'Mulit Dark',
                'Multi' => 'Multi',
                'Multi-Bright' => 'Multi-Bright',
                'Multi-Pink' => 'Multi-Pink',
                'Mushroom' => 'Mushroom',
                'Mustard' => 'Mustard',
                'Natural' => 'Natural',
                'Navy' => 'Navy',
                'navy check' => 'navy check',
                'navy grey' => 'navy grey',
                'Navy Olive' => 'Navy Olive',
                'Navy/Beige' => 'Navy/Beige',
                'Navy/Blue' => 'Navy/Blue',
                'Navy/Cherry' => 'Navy/Cherry',
                'Navy/Creme' => 'Navy/Creme',
                'Navy/Green' => 'Navy/Green',
                'Navy/Orange' => 'Navy/Orange',
                'Navy/Pink' => 'Navy/Pink',
                'Navy/Red' => 'Navy/Red',
                'Navy/White' => 'Navy/White',
                'Navy/Yellow' => 'Navy/Yellow',
                'Nude' => 'Nude',
                'Oatmeal' => 'Oatmeal',
                'Off white' => 'Off white',
                'old gold ' => 'old gold ',
                'old gold' => 'old gold',
                'Olive' => 'Olive',
                'Olive/Natural' => 'Olive/Natural',
                'Orange' => 'Orange',
                'Orange/Blue' => 'Orange/Blue',
                'Orange/Navy' => 'Orange/Navy',
                'Oyster' => 'Oyster',
                'Paisley' => 'Paisley',
                'Pale Blue' => 'Pale Blue',
                'Pale Green' => 'Pale Green',
                'Pale Pink' => 'Pale Pink',
                'Pastels' => 'Pastels',
                'Peach' => 'Peach',
                'Peacock' => 'Peacock',
                'Pearl ' => 'Pearl ',
                'Pearl' => 'Pearl',
                'Peppermint' => 'Peppermint',
                'petrol' => 'petrol',
                'Pewter' => 'Pewter',
                'Pina Colada' => 'Pina Colada',
                'Pink' => 'Pink',
                'Pink Floral' => 'Pink Floral',
                'Pink/Black' => 'Pink/Black',
                'Pink/Blue' => 'Pink/Blue',
                'Pink/Creme' => 'Pink/Creme',
                'Pink/Navy' => 'Pink/Navy',
                'Pink/Orange' => 'Pink/Orange',
                'Plum' => 'Plum',
                'Print' => 'Print',
                'Purple' => 'Purple',
                'Purple/Black' => 'Purple/Black',
                'Purple/Green' => 'Purple/Green',
                'Raspberry' => 'Raspberry',
                'Red' => 'Red',
                'Red Check' => 'Red Check',
                'Red Marle' => 'Red Marle',
                'Red/Black' => 'Red/Black',
                'Red/Blue' => 'Red/Blue',
                'Red/Cream' => 'Red/Cream',
                'Red/Navy' => 'Red/Navy',
                'Red/Pink' => 'Red/Pink',
                'Red/White' => 'Red/White',
                'Rose' => 'Rose',
                'Rose Gold' => 'Rose Gold',
                'Rose Grey' => 'Rose Grey',
                'Rose Pink' => 'Rose Pink',
                'Rose/Beige' => 'Rose/Beige',
                'Royal' => 'Royal',
                'Rust' => 'Rust',
                'Saffron' => 'Saffron',
                'Sage' => 'Sage',
                'sage' => 'sage',
                'Salmon' => 'Salmon',
                'Sand' => 'Sand',
                'Sapphire' => 'Sapphire',
                'Sea Blue' => 'Sea Blue',
                'Sea Foam' => 'Sea Foam',
                'Silver' => 'Silver',
                'Sky Blue' => 'Sky Blue',
                'Slate' => 'Slate',
                'Smoke' => 'Smoke',
                'Snake' => 'Snake',
                'Snow' => 'Snow',
                'Soft Pink' => 'Soft Pink',
                'Spot' => 'Spot',
                'Steel' => 'Steel',
                'Steel blue' => 'Steel blue',
                'Stone' => 'Stone',
                'straw' => 'straw',
                'Strawberry' => 'Strawberry',
                'Stripe' => 'Stripe',
                'Tan' => 'Tan',
                'Tan/Beige' => 'Tan/Beige',
                'Tan/Navy' => 'Tan/Navy',
                'Tangerine' => 'Tangerine',
                'Tartan' => 'Tartan',
                'Taupe' => 'Taupe',
                'tea-green' => 'tea-green',
                'Teal' => 'Teal',
                'Terracotta' => 'Terracotta',
                'Tiger ' => 'Tiger ',
                'Tobacco' => 'Tobacco',
                'Toffee' => 'Toffee',
                'Tomato' => 'Tomato',
                'Tortoise' => 'Tortoise',
                'Turquoise' => 'Turquoise',
                'Tweed' => 'Tweed',
                'vintage pink' => 'vintage pink',
                'vintage-blue' => 'vintage-blue',
                'Violet' => 'Violet',
                'Watermelon' => 'Watermelon',
                'White' => 'White',
                'White Green' => 'White Green',
                'White Stripe' => 'White Stripe',
                'White/Black' => 'White/Black',
                'White/Blue' => 'White/Blue',
                'White/Navy' => 'White/Navy',
                'White/Pink' => 'White/Pink',
                'White/Red' => 'White/Red',
                'White/Yellow' => 'White/Yellow',
                'Wine' => 'Wine',
                'Wood' => 'Wood',
                'Yellow' => 'Yellow',
                'Yellow/Navy' => 'Yellow/Navy',
                'Yellow/Pink' => 'Yellow/Pink',
                'Zebra' => 'Zebra'
            ),
            "sanitize" => true
        )
    );

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, self::LOG_FILE, true);
    }

    public function generateAttributes()
    {
        $this->checkIfAttributeSetsAreUsed();

        $this->createAllAttributes();

        $this->createAllAttributesSets();
    }

    /**
     * @param $treeFilePath
     * @param int $removeExisting
     */
    public function createCategoryTree($treeFilePath, $removeExisting = 1)
    {

        if ($removeExisting) {
            $this->removeExistingCategories();
        }

        $this->log(sprintf("%s->load cats from: %s", __METHOD__, $treeFilePath));

        // Open the tree file
        if (!$handle = fopen($treeFilePath, "r")) {
            $err = sprintf("failed to open file %s", $treeFilePath);
            $this->log($err);
            die($err);
        }

        // Process tree
        $lastItemPerOffset = array();
        while (($line = fgets($handle)) !== false) {
            $offset = $this->getOffset($line);
            $this->log(sprintf("%s->offset=%d", __METHOD__, $offset));

            $catName = $this->getCategoryName($line, $offset);
            $this->log(sprintf("catName=%s", $catName));

            /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
            $categoryCollection = Mage::getResourceModel('catalog/category_collection')
                        ->addFieldToFilter('name', $catName)
                        ->setPageSize(1)
                        ->setCurPage(1);
            if (isset($lastItemPerOffset[$offset - 1])) {
                $categoryCollection->addAttributeToFilter('parent_id', (int)$lastItemPerOffset[$offset-1]->getId());
            }

            // item exists, move on to next tree item
            if ($categoryCollection->getSize()) {
                $lastItemPerOffset[$offset] = $categoryCollection->getFirstItem();
                continue;
            } else {
                if ($offset - 1 == 0
                    && !isset($lastItemPerOffset[$offset-1])
                ) {
                    // no root item found
                    $this->log("ERROR: root category not found. Please create the root");
                } elseif (!isset($lastItemPerOffset[$offset-1])) {
                    // no parent found. something must be wrong in the file
                    $this->log("ERROR: parent item does not exist. Please check your tree file");
                }

                $parentitem = $lastItemPerOffset[$offset-1];

                // Create a new category item
                $category = $this->createNewCategory();

                try {
                    $category->addData(
                        array(
                            'name' 			=> $catName,
                            'meta_title'	=> $catName,
                            'display_mode'	=> Mage_Catalog_Model_Category::DM_PRODUCT,
                            'is_active'		=> 1,
                            'is_anchor'		=> 1,
                            'path'			=> $parentitem->getPath(),
                        )
                    );
                    $category->save();
                } catch (Exception $e){
                    $err = sprintf("ERROR: %s", $e->getMessage());
                    $this->log($err);
                    die($err);
                }

                $lastItemPerOffset[$offset] = $category;
                $this->log(sprintf("> created %s", $catName));
            }
        }

        fclose($handle);
    }

    protected function removeExistingCategories()
    {
        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getSingleton('core/resource');
        $dbRead = $resource->getConnection('core_read');
        $sql = sprintf(
            "SELECT entity_id FROM %s WHERE entity_id>2 ORDER BY entity_id DESC",
            $resource->getTableName("catalog_category_entity")
        );
        $this->log(sprintf("%s->var=%s", __METHOD__, $sql));
        $categories = $dbRead->fetchCol($sql);
        foreach ($categories as $catId) {
            $loadedCategory = Mage::getModel("catalog/category")->load($catId);
            if ($catId <= 2) {
                $this->log(sprintf("%s->skip=%d. %s", __METHOD__, $catId, $loadedCategory->getName()));
                continue;
            }

            try {
                $this->log(sprintf("%s->delete category '%d. %s'", __METHOD__, $catId, $loadedCategory->getName()));
                $loadedCategory->delete();
            } catch (Exception $exception) {
                $this->log(sprintf("Error: %s", $exception->getMessage()));
            }
        }
    }

    /**
     * @param $line
     * @return int
     */
    protected function getOffset($line): int
    {
        return strlen(substr($line, 0, strpos($line, '-')));
    }

    /**
     * @param $line
     * @param $offset
     * @return string
     */
    protected function getCategoryName($line, $offset): string
    {
        return trim(substr($line, $offset + 1));
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    protected function createNewCategory()
    {
        $category = Mage::getModel('catalog/category');
        $category->setStoreId(0);
        return $category;
    }

    /**
     * return product count associated with attribute set
     * @param $label
     * @return
     */
    protected function productsUseAttributeSet($label)
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($label, 'attribute_set_name');
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToFilter('status', array('eq' => 1))
            ->addAttributeToFilter('attribute_set_id', $attributeSet->getAttributeSetId())
            ->addAttributeToSelect('*');
        return $collection->getSize();
    }

    /**
     * create an attribute
     *
     * For reference, see Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
     *
     * @param $labelText
     * @param $attributeCode
     * @param null $values
     * @param null $setInfo
     * @param null $options
     * @param int $replaceAttribute
     * @return int|false
     */
    protected function createAttribute($labelText, $attributeCode, $values = null, $setInfo = null, $options = null, $replaceAttribute = 0)
    {

        // check if exists
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
        if (!$replaceAttribute && null !== $attribute->getId()) {
            $this->log(sprintf("%s->attribute '%s' exists! skip creation", __METHOD__, $attributeCode) );
            return false;
        }

        // delete attribute
        if ($replaceAttribute) {
            $this->log(sprintf("%s->delete=%s", __METHOD__, $attributeCode) );
            Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode)->delete();
        }

        $labelText = trim($labelText);
        $attributeCode = trim($attributeCode);

        if ($labelText == '' || $attributeCode == '') {
            $this->log("Can't import the attribute with an empty label or code.  LABEL= [$labelText]  CODE= [$attributeCode]");
            return false;
        }

        if (empty($values)) {
            $values = array();
        }

        /*
                if (!empty($setInfo) && (isset($setInfo['SetID']) == false || isset($setInfo['GroupID']) == false)) {
                    $this->log("Please provide both the set-ID and the group-ID of the attribute-set if you'd like to subscribe to one.");
                    return false;
                }
        */
        $this->log("Creating attribute [$labelText] with code [$attributeCode].");

        //>>>> Build the data structure that will define the attribute. See
        //     Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
        $data = array(
            //'label'                             => $labelText,

            //  eav_attribute
            'attribute_model'                   => NULL,    // x
            'backend_model'                     => NULL,    // x
            'backend_type'                      => 'varchar',   // datetime, decimal, int, static, text, varchar
            'backend_table'                     => NULL,    // x
            'is_user_defined'                   => '1',     // x
            'frontend_model'                    => NULL,    // x
            'source_model'                      => NULL,    // eav/entity_attribute_source_boolean, eav/entity_attribute_source_table
            //'is_required'
            //'default_value'
            //'note'

            // Attribute Properties (catalog_eav_attribute)
            'is_configurable'                   => '0',     // x
            'is_global'                         => '0',     // x
            'frontend_input'                    => 'text',  // select, multiselect etc
            'is_unique'                         => '0',     // x
            'is_required'                       => '0',     // x
            'frontend_class'                    => NULL,    // x
            'apply_to'                          => NULL,    // simple, grouped, configurable, virtual, bundle, downloadable, giftcard

            // Frontend Properties
            'is_searchable'                     => '0',     // x
            'is_visible_in_advanced_search'     => '0',     // x
            'is_comparable'                     => '0',     // x
            'is_filterable'                     => '0',     // x
            'is_filterable_in_search'           => '0',     // x
            'layered_navigation_canonical'      => '0',     // x
            'is_used_for_promo_rules'           => '0',     // x
            'position'                          => '0',     // x
            'is_html_allowed_on_front'          => '1',     // x
            'is_visible_on_front'               => '0',     // x
            'used_in_product_listing'           => '0',     // x
            'used_for_sort_by'                  => '0',     // x

            // ???
            //'wysiwyg_enabled'               => '0',
        );

        // Now, overlay the incoming values on to the defaults.
        foreach($values as $key => $newValue) {
            if (!array_key_exists($key, $data)) {
                $this->log("attribute feature [$key] is not valid!");
                return false;
            }
            else {
                $data[$key] = $newValue;
            }
        }

        //$this->log(sprintf("%s->attribute=%s", __METHOD__, print_r($data, true)) );
        $data['attribute_code'] = $attributeCode;
        $data['frontend_label'] = array(
            0 => $labelText,
            1 => '',
            3 => '',
            2 => '',
            4 => ''
        );

        //<<<<
        //>>>> Build the model.
        $model = Mage::getModel('catalog/resource_eav_attribute');
        $this->log($data);
        $model->addData($data);

        if (!empty($setInfo)) {
            $model->setAttributeSetId($setInfo['SetID']);
            $model->setAttributeGroupId($setInfo['GroupID']);
        }

        $entityTypeID = Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
        $this->log($entityTypeID);
        $model->setEntityTypeId($entityTypeID);
        $model->setIsUserDefined(1);

        //<<<<
        // Save.
        try {
            $model->save();
        }
        catch(Exception $ex) {
            $this->log(sprintf("attribute [%s] could not be saved: %s", $labelText, $ex->getMessage()) );
            return false;
        }

        $id = $model->getId();
        $this->log(sprintf("attribute [$labelText] has been saved as ID %d", $labelText, $id));

        if ($options && is_array($options) && count($options)) {
            $i = 0;
            $maxOpts = 0;
            foreach($options as $key => $val) {
                // $this->log(sprintf("add option: %d. %s: %s", $i, $key, $val));
                $adminVal = (self::_is_assoc($options) ? $key : $val);
                $this->addAttributeValue($attributeCode, array($adminVal, $val), $i++);
                if ($maxOpts != 0 && $i >= $maxOpts) {
                    break;
                }
            }
        }
        return $id;
    }

    /**
     * @param $attributeCode
     * @param $attributeValues
     * @param int $i
     */
    private function addAttributeValue($attributeCode, $attributeValues, $i = 0) {
        // Check if a value exists in Attribute
        if (!$this->attributeValueExists($attributeCode, $attributeValues[0])) {
            // Get Attribute by attribute code
            $attribute = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            $attribute_id = $attribute->getAttributeId();
            $arr['attribute_id'] = $attribute_id;
            $arr['value'][$attributeCode . "_" . $attributeValues[0]][0] = $attributeValues[0];
            $arr['value'][$attributeCode . "_" . $attributeValues[0]][1] = $attributeValues[1];
            $arr['order'][$attributeCode . "_" . $attributeValues[0]] = $i;

            $setup = Mage::getModel('eav/entity_setup','core_setup');
            $setup->addAttributeOption($arr);
        }
    }

    /**
     * @param $arg_attribute
     * @param $arg_value
     * @return bool
     */
    private function attributeValueExists($arg_attribute, $arg_value) {
        $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
        $attribute = Mage::getModel('catalog/resource_eav_attribute')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, $arg_attribute);
        $attribute_options_model->setAttribute($attribute);
        $options                 = $attribute_options_model->getAllOptions(false);
        foreach ($options as $option) {
            if ($option['label'] == $arg_value) {
                return $option['value'];
            }
        }
        return false;
    }

    /**
     * to assign attribute to Attribute Set and Attribute Group
     * @param $attributeCode
     * @param $attributeGroupId
     * @param $attributeSetId
     */
    private function assignAttribute($attributeCode, $attributeGroupId, $attributeSetId) {
        /*
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', $attributeCode);
        if (null !== $attribute->getId()) {
            $attributeId = $attribute->getId();
        }
        */
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode('catalog_product', $attributeCode);
        //$attribute = Mage::getModel('eav/entity_attribute')->load($attributeId);
        $attribute->setAttributeSetId($attributeSetId);
        $attribute->setAttributeGroupId($attributeGroupId);
        $attribute->save();
    }

    /**
     * Create an atribute-set.
     *
     * For reference, see Mage_Adminhtml_Catalog_Product_SetController::saveAction().
     *
     * @param $setName
     * @param $groups
     * @param null $copyGroupsFromId
     * @param $attributeGroups
     * @param int $replaceAttributeSet
     * @throws Exception
     * @return array|false
     */
    public function createAttributeSet($setName, $groups, $copyGroupsFromId = null, $attributeGroups, $replaceAttributeSet = 1)
    {

        $this->log(sprintf("%s->setName: %s", __METHOD__, $setName) );

        // check if exists & delete
        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $attributeSetCollection = Mage::getModel('eav/entity_attribute_set')->getCollection()->setEntityTypeFilter($entityTypeId);
        foreach($attributeSetCollection as $_attributeSet) {
            $this->log(sprintf("%s->load %d", __METHOD__, $_attributeSet->getId()) );
            //print_r($_attributeSet->getData());
            $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($_attributeSet->getId());
            $this->log(sprintf("%s->attributeSet '%s'", __METHOD__, $attributeSet->getAttributeSetName()) );
            if (preg_match("/default/i", $attributeSet->getAttributeSetName())) {
                $copyGroupsFromId = $_attributeSet->getId();
            }
            if (preg_match(sprintf("/%s/i", $setName), $attributeSet->getAttributeSetName())) {
                if ($replaceAttributeSet) {
                    $this->log(sprintf("%s->delete attribute set '%s'", __METHOD__, $attributeSet->getAttributeSetName()) );
                    /*
                    Integrity constraint violation: 1062 Duplicate entry '46-Clothing Attributes' for key 'UNQ_EAV_ATTRIBUTE_GROUP_ATTRIBUTE_SET_ID_ATTRIBUTE_GROUP_NAME'
                    */
                    $attributeSet->delete();
                }
                else {
                    $this->log(sprintf("%s->attribute set '%s' exists! skip creation", __METHOD__, $setName) );
                    return false;
                }
            }
        }

        $setName = trim($setName);

        $this->log(sprintf("creating attribute-set: %s", $setName));

        if (empty($setName)) {
            $this->log(sprintf("could not create attribute set with an empty name!", $setName));
            return false;
        }

        $model = Mage::getModel('eav/entity_attribute_set');
        $entityTypeID = Mage::getModel('catalog/product')->getResource()->getTypeId();
        $this->log(sprintf("using entity-type-id '%s'", $entityTypeID));

        $model->setEntityTypeId($entityTypeID);

        // We don't currently support groups, or more than one level. See
        // Mage_Adminhtml_Catalog_Product_SetController::saveAction().
        $this->log("creating vanilla attribute-set with name [$setName].");
        $model->setAttributeSetName($setName);

        // We suspect that this isn't really necessary since we're just
        // initializing new sets with a name and nothing else, but we do
        // this for the purpose of completeness, and of prevention if we
        // should expand in the future.
        $this->log(sprintf("%s->validate", __METHOD__) );
        $model->validate();

        // create the record
        try {
            $this->log(sprintf("%s->save", __METHOD__) );
            $model->save();
        }
        catch(Exception $ex) {
            $this->log("Initial attribute-set with name [$setName] could not be saved: " . $ex->getMessage());
            return false;
        }

        if (($setId = $model->getId()) == false) {
            $this->log(sprintf("could not get ID from new attribute-set: %s.", $setName));
            return false;
        }

        $this->log(sprintf("set created id=%d", $setId));

        //>>>> Load the new set with groups (mandatory).
        // Attach the same groups from the given set-ID to the new set.

        if (!empty($copyGroupsFromId)) {
            $this->log(sprintf("cloning group configuration from existing set %d", $copyGroupsFromId));
            $model->initFromSkeleton($copyGroupsFromId);
            $model->save();
        }

        // add a group(s)
        $sortOrder = 1;
        $modelGroups = array();
        foreach($groups as $groupName) {
            $this->log(sprintf("add group [%s]: %02d. %s", $setId, $sortOrder, $groupName));
            $modelGroup = Mage::getModel('eav/entity_attribute_group');
            $modelGroup->setAttributeGroupName($groupName . "-" . sprintf("%03d", rand(10,100)) );
            $modelGroup->setAttributeSetId($setId);
            // This is optional, and just a sorting index in the case of multiple groups
            $modelGroup->setSortOrder($sortOrder);
            $this->log(sprintf("save..."));
            $modelGroup->save();
            $modelGroups[$sortOrder] = $modelGroup;
            $sortOrder++;
        }

        $this->log(sprintf("setGroups: '%d'", count($modelGroups)) );

        $model->setGroups(array($modelGroup));
        //$model->setGroups($modelGroups);

        // Save the final version of our set.
        try {
            $model->save();
        }
        catch(Exception $ex) {
            $this->log("Final attribute-set with name [$setName] could not be saved: " . $ex->getMessage());
            return false;
        }

        /*
        if (($groupId = $modelGroup->getId()) == false) {
            $this->log(sprintf("could not get ID from new group [%s]", $groupName));
            return false;
        }
        */

        $this->log(sprintf("modelGroups=%s", print_r($modelGroups, true)) );

        foreach($attributeGroups as $sortOrder => $attributes) {
            // Mage_Eav_Model_Entity_Attribute_Group
            $this->log(sprintf("%s->add %d attributes to group '%02d. [(%s|%s). %s]'",
                    __METHOD__,
                    count($attributes),
                    $sortOrder,
                    $modelGroups[$sortOrder]->getAttributeSetId(),
                    $modelGroups[$sortOrder]->getData('attribute_group_id'),
                    $modelGroups[$sortOrder]->getAttributeGroupName())
            );
            // we want the group id NOT the AttributeSetId
            //if (($groupId = $modelGroups[$sortOrder]->getAttributeSetId()) == false) {
            if (($groupId = $modelGroups[$sortOrder]->getId()) == false) {
                $this->log(sprintf("could not get ID from new group [%s]", get_class($modelGroups[$sortOrder])) );
                return false;
            }
            foreach($attributes as $attribute) {
                $this->log(sprintf("%s->assignAttribute: %s|%d|%d", __METHOD__, $attribute, $groupId, $setId) );
                $this->assignAttribute($attribute, $groupId, $setId);
            }
        }

        $this->log(sprintf("created attribute-set id:%d, default-group id:%d, attributes: %s", $setId, $groupId, print_r($attributes, true)) );
        return array('setID' => $setId, 'groupID' => $groupId);
    }

    private function checkIfAttributeSetsAreUsed()
    {
        foreach ($this->attributeSetsToAdd as $attributeSetLabel => $attributeSetDetails) {
            // check if product exist, because deleting is then a bad idea
            if ($this->productsUseAttributeSet($attributeSetLabel)) {
                $this->log(sprintf("Error: Attribute set '%s' is used by products! Remove products before proceeding with this step (see delete_all_products.sql).",
                    $attributeSetLabel));
            }
        }
    }

    private function createAllAttributes()
    {
        $j = 0;
        $maximumOpts = 0;
        foreach ($this->attributesToAdd as $code => $configuration) {
            // createAttribute($labelText, $attributeCode, $values, $productTypes, $setInfo, $options) {
            $options = null;
            if (array_key_exists('options', $configuration)) {
                $options = $configuration['options'];
            }
            $this->createAttribute($configuration['label'], $code, $configuration['values'], null, $options,
                $replaceAttribute = 1);
            $j++;
            if ($maximumOpts != 0 && $j >= $maximumOpts) {
                break;
            }
        }
    }

    private function createAllAttributesSets()
    {
        $i = 0;
        $maxOpts = 0;
        foreach ($this->attributeSetsToAdd as $label => $attributes) {
            $this->log(sprintf("create attribute set: %s", $label));
            // check AGAIN if product exist, because deleting is then a bad idea
            if ($this->productsUseAttributeSet($label)) {
                $err = sprintf("%s->attribute set '%s' is in use! skip creation", __METHOD__, $label);
                $this->log($err);
            } else {
                $this->log(sprintf("%s->createAttributeSet: %s=%s", __METHOD__, $label, print_r($attributes, true)));
                // createAttribute($labelText, $attributeCode, $values, $productTypes, $setInfo, $options) {
                $groups = $this->groupsClothing;
                if (preg_match("/shoe/i", $label)) {
                    $groups = $this->groupsShoes;
                }
                if (preg_match("/access/i", $label)) {
                    $groups = $this->groupsAccessories;
                }
                $this->createAttributeSet($label, $groups, null, $attributes);
            }
            $i++;
            if ($maxOpts != 0 && $i >= $maxOpts) {
                break;
            }
        }
    }

    /**
     * @param $array
     * @return bool
     */
    private static function _is_assoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

}