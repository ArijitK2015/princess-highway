<?php

/**
 * Class FactoryX_BugFixes_Model_Salesrule_Rule_Condition_Product_Subselect
 */
class FactoryX_BugFixes_Model_Salesrule_Rule_Condition_Product_Subselect extends Mage_SalesRule_Model_Rule_Condition_Product_Subselect{
        /**
         * validate
         *
         * @param Varien_Object $object Quote
         * @return boolean
         */
        public function validate(Varien_Object $object)
        {
            if (version_compare(Mage::getVersion(),"1.9.1.0","<"))
            {
                return parent::validate($object);
            }
            else {
                if (!$this->getConditions()) {
                    return false;
                }

    //        $value = $this->getValue();
    //        $aggregatorArr = explode('/', $this->getAggregator());
    //        $this->setValue((int)$aggregatorArr[0])->setAggregator($aggregatorArr[1]);

                $attr = $this->getAttribute();
                $total = 0;
                foreach ($object->getQuote()->getAllVisibleItems() as $item) {
                    if (Mage_Rule_Model_Condition_Combine::validate($item)) {
                        $total += $item->getData($attr);
                    }
                }
    //        $this->setAggregator(join('/', $aggregatorArr))->setValue($value);

                return $this->validateAttribute($total);
            }
        }
    }