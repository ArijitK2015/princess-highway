<?php
/**
 * Fontis Australia Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com and you will be sent a copy immediately.
 *
 * @category   Fontis
 * @package    Fontis_Australia
 * @author     Peter Spiller
 * @copyright  Copyright (c) 2008 Fontis Pty. Ltd. (http://www.fontis.com.au)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Fontis_Australia_Model_ShoppingDotCom_Cron extends Fontis_Australia_Model_FeedCronBase {

    public $config_path = 'shoppingdotcomfeed'; 
    public $generate_categories = false;
    protected $doc;
    protected $required_fields = array(
        "name" => "Product_Name",
        "link" => "Product_URL",
        "image_url" => "Image_URL",
        "final_price" => "Current_Price",
        "sku" => "Merchant_SKU",
        "manufacturer_name" => "Manufacturer",
        "description" => "Product_Description",
        "is_salable" => "Stock_Availability"
    );

    public static function update() {
        // Static launcher fopr Magento's cron logic
        $obj = new self();
        $obj->generateFeed();
    }

    protected function setupStoreData() {
        $this->doc = new SimpleXMLElement('<products></products>');
    }
    
    protected function populateFeedWithBatchData($batch_data) {
        $fields = $this->collectAttributeMapping();

        foreach($batch_data as $product) {
            $product_node = $this->doc->addChild('product');
            foreach($fields as $key => $feed_tag) {
                //$this->log("Mapping $key to $feed_tag with value: ".(substr($product[$key], 0, 20)));
                if (strstr($feed_tag, "_Price") != false) {
                    // Prices require two decimal places and a $ sign.
                    $safe_string = sprintf('$%.2f', $product[$key]);
                } else {
                    // Shopping.Com requires no carriage returns or HTML tags in their feed...
                    $safe_string = strip_tags($product[$key]);
                    $safe_string = preg_replace("/\s*\n\s*/", " ", $safe_string);
                    // ...we also need to make it XML safe
                    $safe_string = htmlspecialchars($safe_string);
                }
                $product_node->addChild($feed_tag, $safe_string);
            }
        }
    }
    
    protected function finaliseStoreData() {
        // Use DOM to nicely format XML
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom_sxml = dom_import_simplexml($this->doc);
        $dom_sxml = $dom->importNode($dom_sxml, true);
        $dom->appendChild($dom_sxml);
        //$this->log("Generated XML:\n".$dom->saveXML());

        // Write dom to file
        $filename = $this->info('clean_store_name') . '-products.xml';
        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $this->getPath()));
        $io->write($filename, $dom->saveXML());
        $io->close();
    }
    
}
