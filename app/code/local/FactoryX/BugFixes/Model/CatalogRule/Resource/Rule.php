<?php

class FactoryX_BugFixes_Model_CatalogRule_Resource_Rule extends Mage_CatalogRule_Model_Resource_Rule
{
	/**
     * Inserts rule data into catalogrule/rule_product table
     *
     * @param Mage_CatalogRule_Model_Rule $rule
     * @param array $websiteIds
     * @param array $productIds
     */
    public function insertRuleData(Mage_CatalogRule_Model_Rule $rule, array $websiteIds, array $productIds = array())
    {
        if (version_compare(Mage::getVersion(),"1.8.0.0","==")) {
            /** @var $write Varien_Db_Adapter_Interface */
            $write = $this->_getWriteAdapter();

            $customerGroupIds = $rule->getCustomerGroupIds();
            $fromTime = (int)strtotime($rule->getFromDate());
            $toTime = (int)strtotime($rule->getToDate());
            $toTime = $toTime ? ($toTime + self::SECONDS_IN_DAY - 1) : 0;
            $sortOrder = (int)$rule->getSortOrder();
            $actionOperator = $rule->getSimpleAction();
            $actionAmount = (float)$rule->getDiscountAmount();
            $subActionOperator = $rule->getSubIsEnable() ? $rule->getSubSimpleAction() : '';
            $subActionAmount = (float)$rule->getSubDiscountAmount();
            $actionStop = (int)$rule->getStopRulesProcessing();
            /** @var $helper Mage_Catalog_Helper_Product_Flat */
            $helper = $this->_factory->getHelper('catalog/product_flat');

            if (count($productIds) == 0) {
                Varien_Profiler::start('__MATCH_PRODUCTS__');
                $productIds = $rule->getMatchingProductIds();
                Varien_Profiler::stop('__MATCH_PRODUCTS__');
            }

            $rows = array();
            foreach ($productIds as $productId => $validationByWebsite) {
                foreach ($websiteIds as $websiteId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        if (empty($validationByWebsite[$websiteId])) {
                            continue;
                        }
                        $rows[] = array(
                            'rule_id' => $rule->getId(),
                            'from_time' => $fromTime,
                            'to_time' => $toTime,
                            'website_id' => $websiteId,
                            'customer_group_id' => $customerGroupId,
                            'product_id' => $productId,
                            'action_operator' => $actionOperator,
                            'action_amount' => $actionAmount,
                            'action_stop' => $actionStop,
                            'sort_order' => $sortOrder,
                            'sub_simple_action' => $subActionOperator,
                            'sub_discount_amount' => $subActionAmount,
                        );

                        if (count($rows) == 1000) {
                            $write->insertMultiple($this->getTable('catalogrule/rule_product'), $rows);
                            $rows = array();
                        }
                    }
                }
            }

            if (!empty($rows)) {
                $write->insertMultiple($this->getTable('catalogrule/rule_product'), $rows);
            }
        }
        else parent::insertRuleData($rule,$websiteIds,$productIds);
    }

}