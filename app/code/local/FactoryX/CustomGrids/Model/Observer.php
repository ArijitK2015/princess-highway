<?php
/**
 *
 * backticks (only used in MySQL) = quoted identifiers
 * tell the parser to handle the text between them as a literal string
 * useful for when you have a column or table that contains a keyword or space
 * SELECT @@GLOBAL.sql_mode;
 */
class FactoryX_CustomGrids_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function registerController(Varien_Event_Observer $observer)
    {
        $action = $observer->getControllerAction()->getFullActionName();
        //Mage::helper('customgrids')->log(sprintf("%s->%s", __METHOD__, $action) );

        switch ($action)
        {
            case 'adminhtml_report_product_sold':
            case 'adminhtml_report_product_exportSoldCsv':
                Mage::register('Mage_Adminhtml_Block_Report_Product_Sold_Grid', true);
                break;
            case 'adminhtml_report_product_lowstock':
                Mage::register('Mage_Adminhtml_Block_Report_Product_Lowstock_Grid', true);
                break;
            case 'adminhtml_sales_invoice_index':
            case 'adminhtml_sales_invoice_grid':
            case 'adminhtml_sales_invoice_exportCsv':
            case 'adminhtml_sales_invoice_exportExcel':
                Mage::register('Mage_Adminhtml_Block_Sales_Invoice_Grid', true);
                break;
            case 'adminhtml_sales_shipment_index':
            case 'adminhtml_sales_shipment_grid':
            case 'adminhtml_sales_shipment_exportCsv':
            case 'adminhtml_sales_shipment_exportExcel':
                Mage::register('Mage_Adminhtml_Block_Sales_Shipment_Grid', true);
                break;
            case 'adminhtml_sales_order_index':
            case 'adminhtml_sales_order_grid':
                Mage::register('Mage_Adminhtml_Block_Sales_Order_Grid', true);
                break;
            case 'adminhtml_sales_order_exportCsv':
            case 'adminhtml_sales_order_exportExcel':
                Mage::register('Mage_Adminhtml_Block_Sales_Order_Grid_Export', true);
                break;
            case 'adminhtml_catalog_category_edit':
            case 'adminhtml_catalog_category_grid':
                Mage::register('Mage_Adminhtml_Block_Catalog_Category_Tab_Product', true);
                break;
            case 'adminhtml_catalog_product_grid':
            case 'adminhtml_catalog_product_index':
                Mage::register('Mage_Adminhtml_Block_Catalog_Product_Grid', true);
                break;

        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function blockBeforeHtml(Varien_Event_Observer $observer)
    {
        // Get the block
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tab_Product)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Catalog_Category_Tab_Product';
        }
        else if($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Catalog_Product_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Sales_Order_Grid';
            if ($block->getTemplate() == 'widget/grid.phtml') {
                $block->setTemplate('factoryx/customgrids/grid.phtml');
            }
        }
        else if($block instanceof Mage_Adminhtml_Block_Sales_Invoice_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Sales_Invoice_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Sales_Shipment_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Sales_Shipment_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Report_Product_Lowstock_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Report_Product_Lowstock_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Report_Product_Sold_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Report_Product_Sold_Grid';
        }

        // Get the collection of columns to remove
        if (isset($blockType))
        {
            // Column collection to remove
            $removeCol = Mage::getResourceModel('customgrids/column_collection')->addFieldToFilter('grid_block_type',$blockType)->addFieldToFilter('remove', 1);

            foreach ($removeCol as $rm)
            {
                // Get allowed roles
                $roles = explode(',',$rm->getAdminRoles());
                // Continue if user not allowed
                if (!in_array(Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId(),$roles))
                    continue;

                $block->removeColumn($rm->getAttributeCode());
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function blockCreateAfter(Varien_Event_Observer $observer)
    {
        // Get the block
        $block = $observer->getBlock();
        //Mage::helper('customgrids')->log(sprintf("%s->%s", __METHOD__, get_class($block)) );

        if ($block instanceof Mage_Adminhtml_Block_Catalog_Category_Tab_Product)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Catalog_Category_Tab_Product';
        }
        else if($block instanceof Mage_Adminhtml_Block_Catalog_Product_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Catalog_Product_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Sales_Order_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Sales_Invoice_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Sales_Invoice_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Sales_Shipment_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Sales_Shipment_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Report_Product_Lowstock_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Report_Product_Lowstock_Grid';
        }
        else if($block instanceof Mage_Adminhtml_Block_Report_Product_Sold_Grid)
        {
            // Block type
            $blockType = 'Mage_Adminhtml_Block_Report_Product_Sold_Grid';
        }

        // Get the collection of columns to add
        if (!isset($blockType)) {
            return;
        }

        $columns = array();
        $columnsWaiting = array();

        $entityType = Mage::getSingleton('customgrids/config')->getRelatedEntityType($blockType);
        $entityConfig = Mage::getSingleton(sprintf("customgrids/config_%s", $entityType));
        $defaultColumns = $entityConfig->getDefaultColumns($blockType);
        //Mage::helper('customgrids')->log(sprintf("%s->defaultColumns: %s", __METHOD__, print_r($defaultColumns, true)) );

        // order the column collection to add, as they will fail if getAfterColumn is not added
        $columnCollection = Mage::getResourceModel('customgrids/column_collection')->addFieldToFilter('grid_block_type', $blockType)->addFieldToFilter('remove', 0);
        foreach ($columnCollection as $column)
        {
            /*
            check if the column it wants to be added after is a default column or it has been added yet
            TODO: test if the default column has been removed
            */
            if (
                in_array($column->getAfterColumn(), $defaultColumns)
                ||
                array_key_exists($column->getAfterColumn(), $columns)
            ) {
                $columns[$column->getAttributeCode()] = $column;
            }
            else {
                $columnsWaiting[$column->getAttributeCode()] = $column;
            }
            // check if all waiting columns have had thier after column added
            foreach($columnsWaiting as $key => $columnWaiting) {
                if (array_key_exists($columnWaiting->getAfterColumn(), $columns)) {
                    $columns[$key] = $columnWaiting;
                    unset($columnsWaiting[$key]);
                }
            }
        }

        // any columns left here will never be added, as a circular dependency exists
        if ($columnsWaiting && count($columnsWaiting)) {
            Mage::helper('customgrids')->log(sprintf("%s->columnsWaiting: %s", __METHOD__, print_r(array_keys($columnsWaiting), true)) );
        }

        // Loop
        foreach ($columns as $i => $column)
        {
            // Get allowed roles
            $roles = explode(',',$column->getAdminRoles());
            // Continue if user not allowed
            if (!in_array(Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId(),$roles))
                continue;

            // Entity type
            $entityType = Mage::getSingleton('customgrids/config')->getRelatedEntityType($blockType);
            // Entity config
            $model = sprintf("customgrids/config_%s", $entityType);
            Mage::helper('customgrids')->log(sprintf("%s->model=%s", __METHOD__, $model) );

            $entityConfig = Mage::getSingleton($model);
            // Get the config of the column
            $config = $entityConfig->getConfig($column, $blockType);

            Mage::helper('customgrids')->log(sprintf("%s->addColumnAfter[%s|%s|%s]", __METHOD__,
                $column->getAttributeCode(),
                $column->getAfterColumn(),
                print_r($config, true)) );

            // Add the column
            $block->addColumnAfter(
                $column->getAttributeCode(),
                $config,
                $column->getAfterColumn()
            );
        }
    }

    /**
     * beforeEavCollectionLoad
     *
     *
     * @param Varien_Event_Observer $observer
     * @internal param $ (type) (name) about this param
     */
    public function beforeEavCollectionLoad(Varien_Event_Observer $observer)
    {
        // Get the collection
        $collection = $observer->getCollection();

        if(Mage::registry('Mage_Adminhtml_Block_Catalog_Category_Tab_Product')
            && isset($collection)
            && $collection instanceof Mage_Catalog_Model_Resource_Product_Collection)
        {
            $blockType = "Mage_Adminhtml_Block_Catalog_Category_Tab_Product";
            $collectionType = "Mage_Catalog_Model_Resource_Product_Collection";
        }
        else if (Mage::registry('Mage_Adminhtml_Block_Catalog_Product_Grid')
            && isset($collection)
            && $collection instanceof Mage_Catalog_Model_Resource_Product_Collection)
        {
            $blockType = "Mage_Adminhtml_Block_Catalog_Product_Grid";
            $collectionType = "Mage_Catalog_Model_Resource_Product_Collection";
        }
        else if (Mage::registry('Mage_Adminhtml_Block_Report_Product_Lowstock_Grid')
            && isset($collection)
            && $collection instanceof Mage_Reports_Model_Resource_Product_Lowstock_Collection)
        {
            $blockType = "Mage_Adminhtml_Block_Report_Product_Lowstock_Grid";
            $collectionType = "Mage_Reports_Model_Resource_Product_Lowstock_Collection";
        }
        else if (Mage::registry('Mage_Adminhtml_Block_Report_Product_Sold_Grid')
            && isset($collection)
            && $collection instanceof Mage_Reports_Model_Resource_Product_Sold_Collection)
        {
            $blockType = "Mage_Adminhtml_Block_Report_Product_Sold_Grid";
            $collectionType = "Mage_Reports_Model_Resource_Product_Sold_Collection";
        }

        // Process the columns
        if (isset($blockType) && isset($collectionType))
        {
            $columnCollection = Mage::getResourceModel('customgrids/column_collection')->filterByModelAndBlock($collectionType,$blockType)->addFieldToFilter('remove',0);

            foreach ($columnCollection as $column)
            {
                $collection->addAttributeToSelect($column->getAttributeCode());

                /*
                add additional columns to query

                e.g.
                sales_flat_order_item.price
                sales_flat_order_item.online_only

                TODO add to correct entity config
                */
                if (
                    preg_match("/^special_price$/i", $column->getAttributeCode())
                    &&
                    preg_match("/^Mage_Reports_Model_Resource_Product_Sold_Collection$/i", $collectionType)
                ) {
                    // there really is no special_price only what the product sold for
                    $collection->getSelect()->columns(
                        array(
                            'special_price' => new Zend_Db_Expr('price')
                        )
                    );
                }
                if (
                    preg_match("/^online_only$/i", $column->getAttributeCode())
                    &&
                    preg_match("/^Mage_Reports_Model_Resource_Product_Sold_Collection$/i", $collectionType)
                ) {
                    // these fields are in the
                    $collection->getSelect()->columns(
                        array(
                            'online_only' => new Zend_Db_Expr('online_only')
                        )
                    );
                }
            } // end foreach
            //Mage::helper('customgrids')->log(sprintf("%s->SQL: %s", __METHOD__, $collection->getSelect()->__toString()));
        }
    }

    /**
     * beforeEavCollectionLoad implements
     *
     * - adminhtml_sales_order_grid (Mage_Sales_Model_Resource_Order_Collection)
     * - adminhtml_sales_invoice_grid (Mage_Sales_Model_Resource_Order_Invoice_Grid_Collection)
     *
     * @param Varien_Event_Observer $observer
     * @internal param $ (type) (name) about this param
     */
    public function beforeCoreCollectionLoad(Varien_Event_Observer $observer)
    {
        // Get the collection
        $collection = $observer->getCollection();
        
        if (Mage::registry('Mage_Adminhtml_Block_Sales_Order_Grid_Export')
            && isset($collection)
            && $collection instanceof Mage_Sales_Model_Resource_Order_Grid_Collection)
        {
            $blockType = "Mage_Adminhtml_Block_Sales_Order_Grid";
            $collectionType = "Mage_Sales_Model_Resource_Order_Collection";
        }
        else if (Mage::registry('Mage_Adminhtml_Block_Sales_Order_Grid')
            && isset($collection)
            && $collection instanceof Mage_Sales_Model_Resource_Order_Collection)
        {
            $blockType = "Mage_Adminhtml_Block_Sales_Order_Grid";
            $collectionType = "Mage_Sales_Model_Resource_Order_Collection";
        }
        else if(Mage::registry('Mage_Adminhtml_Block_Sales_Invoice_Grid')
            && isset($collection)
            && $collection instanceof Mage_Sales_Model_Resource_Order_Invoice_Grid_Collection)
        {
            $blockType = "Mage_Adminhtml_Block_Sales_Invoice_Grid";
            $collectionType = "Mage_Sales_Model_Resource_Order_Invoice_Grid_Collection";
        }
        else if(Mage::registry('Mage_Adminhtml_Block_Sales_Shipment_Grid')
            && isset($collection)
            && $collection instanceof Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection)
        {
            $blockType = "Mage_Adminhtml_Block_Sales_Shipment_Grid";
            $collectionType = "Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection";
        }

        if (isset($blockType) && isset($collectionType))
        {
            $columnCollection = Mage::getResourceModel('customgrids/column_collection')->filterByModelAndBlock($collectionType,$blockType)->addFieldToFilter('remove',0);

            $joins = array();

            $entityType = Mage::getSingleton('customgrids/config')->getRelatedEntityType($blockType);
            $configModel = sprintf("customgrids/config_%s", $entityType);
            $config =  Mage::getSingleton($configModel);

            /**
            build a list of joins
            either column OR sql_expr
            */
            foreach ($columnCollection as $column)
            {
                $flatAttr = $config->getFlatAttributes($entityType);
                foreach ($flatAttr as $key => $attributes)
                {
                    foreach ($attributes as $attribute)
                    {
                        if ($column->getAttributeCode() != $attribute['code']) {
                            continue;
                        }
                        Mage::helper('customgrids')->log(sprintf("%s->column->getAttributeCode(): %s == %s", __METHOD__, $column->getAttributeCode(), $attribute['code']) );
                        if (!array_key_exists($key, $joins)) {
                            $joins[$key] = array();
                        }

                        // Check if it's a custom SQL expression column
                        if (
                            array_key_exists('sql_expr', $attribute)
                            &&
                            $attribute['sql_expr']
                        ) {
                            Mage::helper('customgrids')->log(sprintf("%s->attributeCode: %s, with sql_expr", __METHOD__, $column->getAttributeCode()) );
                            $joins[$key][$column->getAttributeCode()]['type'] = 'left';
                            $joins[$key][$column->getAttributeCode()]['column'] = $column->getAttributeCode();
                            $joins[$key][$column->getAttributeCode()]['sql_expr'] = new Zend_Db_Expr($attribute['sql_expr']);
                        }
                        elseif (
                            array_key_exists('inner_join', $attribute)
                            &&
                            $attribute['inner_join']
                        ) {
                            Mage::helper('customgrids')->log(sprintf("%s->attributeCode: %s, with inner_join", __METHOD__, $column->getAttributeCode()));
                            $joins[$key][$column->getAttributeCode()]['type'] = 'inner';
                            //$joins[$key][$column->getAttributeCode()]['column'] = $column->getAttributeCode();
                            $joins[$key][$column->getAttributeCode()]['column'] = $config->getTableAlias($key) . "." . $column->getAttributeCode();
                            $joins[$key][$column->getAttributeCode()]['sql_expr'] = new Zend_Db_Expr($attribute['inner_join']);
                        }
                        else {
                            $joins[$key][$column->getAttributeCode()]['type'] = 'left';
                            // check for an alias
                            if ($config->getTableAlias($key)) {
                                $joins[$key][$column->getAttributeCode()]['column'] = $config->getTableAlias($key) . "." . $column->getAttributeCode();
                            }
                            else {
                                $joins[$key][$column->getAttributeCode()]['column'] = $column->getAttributeCode();
                            }
                        }
                    }
                }
            }

            /*
            check if we are either not exporting
            OR
            exporting to csv and on the first page
            
            why do we check for the first page?
            */
            $params = Mage::app()->getRequest()->getParams();
            //Mage::helper('customgrids')->log(sprintf("%s->Params()=%s", __METHOD__, print_r(Mage::app()->getRequest()->getParams(), true)) );
            
            if (
                !Mage::helper('customgrids')->isExportCsv()
                ||
                (
                    Mage::helper('customgrids')->isExportCsv()
                    &&
                    /*
                    this test fails with: Column not found, replaced with check below
                    $collection->getCurPage() == 1
                    */
                    // check for page 1 or assume page 1
                    $params['page'] == 1 || !array_key_exists('page', $params)
                )
            ) {
                Mage::helper('customgrids')->log(sprintf("%s->joins=%s", __METHOD__, print_r($joins, true)) );

                /**
                 *
                 */
                foreach ($joins as $table => $columns) {
                    Mage::helper('customgrids')->log(sprintf("%s->%s: %s", __METHOD__, $table, print_r($columns, true)) );
                    $joinType = false;
                    $innerJoin = "";
                    $columnsToJoin = array();
                    foreach ($columns as $attrCode => $data) {
                        $joinType = $data['type'];
                        // Handle the custom SQL expression columns differently
                        if (array_key_exists('sql_expr', $data) && $data['type'] == 'left') {
                            $columnsToJoin[$data['column']] = $data['sql_expr'];
                        }
                        else if (array_key_exists('sql_expr', $data) && $data['type'] == 'inner') {
                            $innerJoin = $data['sql_expr'];
                            $columnsToJoin[] = $data['column'];
                        }
                        else {
                            $columnsToJoin[] = $data['column'];
                        }
                    }
                    Mage::helper('customgrids')->log(sprintf("%s->columnsToJoin: %s", __METHOD__, print_r($columnsToJoin, true)) );
                    $tableRelation = $config->getTableRelation($entityType, $table);
                    Mage::helper('customgrids')->log(sprintf("%s->tableRelation=%s", __METHOD__, $tableRelation) );

                    // In case there is a filter applied when exporting we might get a "cannot defined correlation name" error
                    $froms = $collection->getSelect()->getPart(Zend_Db_Select::FROM);
                    Mage::helper('customgrids')->log(sprintf("%s->froms=%s", __METHOD__, print_r($froms, true)) );

                    $tableAlias = $config->getTableAlias($table);
                    //Mage::helper('customgrids')->log(sprintf("%s->tableAlias[%s]=%s[%d]", __METHOD__, $table, $tableAlias, array_key_exists($tableAlias, $froms)) );
                    if ($tableAlias && !array_key_exists($tableAlias, $froms)) {
                        Mage::helper('customgrids')->log(sprintf("%s->%s", __METHOD__,$joinType) );
                        if ($joinType == 'left') {
                            $collection->getSelect()->joinLeft(
                                array($config->getTableAlias($table) => $table),
                                $tableRelation,
                                $columnsToJoin
                            );
                        }
                        if ($joinType == 'inner' && $innerJoin) {
                            $collection->getSelect()->joinInner(
                                array($config->getTableAlias($table) => $innerJoin),
                                $tableRelation,
                                $columnsToJoin
                            );
                        }
                    }
                    else {
                        Mage::helper('customgrids')->log(sprintf("table '%s' has no alias defined, see FactoryX_CustomGrids_Model_Config_Sales::_tableAlias", $table), Zend_Log::WARN);
                    }
                }

                // Fix the "Column in where clause is ambiguous" error
                if ($where = $collection->getSelect()->getPart('where')) {
                    $ambiguousColumns = $config->getAmbiguousColumns();
                    $where = $this->_fixAmbiguousColumns($where, $ambiguousColumns);
                    //Mage::helper('customgrids')->log(sprintf("%s->where=%s", __METHOD__, print_r($where,true)));
                    $collection->getSelect()->setPart('where', $where);
                }
            }
            Mage::helper('customgrids')->log(sprintf("%s->SQL.2: %s", __METHOD__, $collection->getSelect()->__toString()) );
        }
    }

    /**
     * Fix the "Column in where clause is ambiguous" error
     * @param $where
     * @param array $ambiguousColumns
     * @return
     */
    protected function _fixAmbiguousColumns($where, $ambiguousColumns = array())
    {
        // Avoid sql error: column 'COLUMN_NAME' in where clause is ambiguous
        foreach ($where as $key => $condition)
        {
            //Mage::helper('customgrids')->log(sprintf("%s->key=%s,condition=%s", __METHOD__, $key, $condition));
            foreach ($ambiguousColumns as $column)
            {
                $old = "";
                $new = "";
                // test with backticks (only used in MySQL) = quoted identifiers
                if (preg_match(sprintf("/\(`%s`/", $column), $condition))
                {
                    $old = sprintf("`%s`", $column);
                    $new = sprintf("`main_table`.`%s`", $column);
                }
                elseif (preg_match(sprintf("/\(%s/", $column), $condition))
                {
                    $old = sprintf("%s", $column);
                    $new = sprintf("main_table.%s", $column);
                }
                if (strlen($old) && strlen($new))
                {
                    $new_condition = str_replace($old, $new, $condition);
                    $where[$key] = $new_condition;
                }
            }
        }
        return $where;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function afterDelete(Varien_Event_Observer $observer)
    {
        //Mage::helper('customgrids')->log(sprintf("%s->%s", get_class($this), __METHOD__) );

        // Get the deleted product
        $column = $observer->getEvent()->getObject();

        // Get every other columns where that column is used as an after column
        $collection = Mage::getResourceModel('customgrids/column_collection')
            ->addFieldToFilter('after_column',$column->getAttributeCode())
            ->addFieldToFilter('grid_block_type',$column->getGridBlockType());

        foreach($collection as $affectedColumn)
        {
            // Get the related entity type
            $relatedEntityType = Mage::getSingleton('customgrids/config')->getRelatedEntityType($affectedColumn->getGridBlockType());
            // Get possible values for the after column
            $allowedAfterColumns = Mage::getSingleton('customgrids/config_'.$relatedEntityType)->getAfterColumns($affectedColumn->getGridBlockType());
            // Use the first one
            if (!empty($allowedAfterColumns))
            {
                $newAfterCol = $allowedAfterColumns[0]['value'];
                // Assign the new after column
                $affectedColumn->setData('after_column',$newAfterCol);
                $affectedColumn->getResource()->saveAttribute($affectedColumn,array('after_column'));
            }
        }
    }

}