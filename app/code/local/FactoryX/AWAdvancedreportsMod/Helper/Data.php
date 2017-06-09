<?php

/**
 * Class FactoryX_AWAdvancedreportsMod_Helper_Data
 */
class FactoryX_AWAdvancedreportsMod_Helper_Data extends AW_Advancedreports_Helper_Data
{
    const ROUTE_ADVANCED_REGION = 'advanced_region';

    /**
     * @param null $gridType
     * @return array
     */
    public function getOptions($gridType = null)
    {
        $options = array(
            array('value' => 'today', 'label' => $this->__('Today')),
            array('value' => 'yesterday', 'label' => $this->__('Yesterday')),
            array('value' => 'last_7_days', 'label' => $this->__('Last 7 days'), 'default' => 1),
            array('value' => 'last_week', 'label' => $this->__($this->getLastWeekLabel())),
            array('value' => 'last_business_week', 'label' => $this->__($this->getLastBusinessWeekLabel())),
            array('value' => 'this_month', 'label' => $this->__('This month')),
            array('value' => 'last_month', 'label' => $this->__('Last month')),
            array('value' => 'custom', 'label' => $this->__('Custom date range')),
        );

        return array_merge($options, $this->getCustomizedLabels($gridType));
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getChartParams($key)
    {
        $params = array();
        $params[self::ROUTE_ADVANCED_REGION] = array(
            array('value' => 'percent_data', 'label' => 'Percent'),
        );

        if (array_key_exists($key,$params)) {
            return $params[$key];
        } else {
            return parent::getChartParams($key);
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getNeedReload($key)
    {
        $params = array();
        $params[self::ROUTE_ADVANCED_REGION] = true;
        Mage::log(sprintf("%s->key=%s", __METHOD__, $key) );
        if (in_array($key, $params)) {
            $retVal = $params[$key];
        }
        else {
            $retVal = parent::getNeedReload($key);
        }
        return $retVal;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getNeedTotal($key)
    {
        $params = array();
        $params[self::ROUTE_ADVANCED_REGION] = true;
        Mage::log(sprintf("%s->key=%s", __METHOD__, $key) );
        if (in_array($key, $params)) {
            $retVal = $params[$key];
        }
        else {
            $retVal = parent::getNeedTotal($key);
        }
        return $retVal;
    }
}
