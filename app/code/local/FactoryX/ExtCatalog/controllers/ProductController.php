<?php
//require_once ("Mage/Catalog/controllers/ProductController.php");
require_once 'Mage/Catalog/controllers/ProductController.php';

class FactoryX_ExtCatalog_ProductController extends Mage_Catalog_ProductController
{       
    public function indexAction()
    {
        echo "Extend Catalog";
    }    

     
}
