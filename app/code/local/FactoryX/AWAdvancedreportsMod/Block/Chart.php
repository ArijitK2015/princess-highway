<?php

/**
 * Class FactoryX_AWAdvancedreportsMod_Block_Chart
 */
class FactoryX_AWAdvancedreportsMod_Block_Chart extends AW_Advancedreports_Block_Chart
{
    public function getDataForMap()
    {
        $_data = array();
        foreach ($this->_values as $row) {
            if (array_key_exists('region_name',$row)) {
                $country = $row['region_name'];
            } else {
                $country = $row['country_name'];
            }
            $value = isset($row[$this->_option]) ? $row[$this->_option] : 0;
            $_data[] = array($country, $value);
        }
        return $_data;
    }

    /**
     * @param bool|true $directUrl
     * @return array|string
     */
    public function getMapChartUrl($directUrl = true)
    {
        # try to prevent foreach() errors
        if (!$this->_values) {
            return '';
        }

        $params = array(
            'cht' => 't',
            'chs' => ($this->_width <= 440 ? $this->_width : 440) . 'x220', //$this->_height,
            'chco' => 'fcfcc2,FF0000,FFFF00,00FF00',
            'chtm' => 'world',
            'chf' => 'bg,s,' . $this->getBackgroundColor(),
        );

        # Getting data
        $countrys = array();
        $values = array();
        $maxValue = 0;
        $minValue = 0;
        foreach ($this->_values as $row)
        {
            if (array_key_exists('region_id',$row)) {
                $countrys[] = $row['region_id'];
            } else {
                $countrys[] = $row['country_id'];
            }

            $values[] = isset($row[$this->_option]) ? $row[$this->_option] : 0;
            if (isset($row[$this->_option]) && $row[$this->_option] > $maxValue) {
                $maxValue = $row[$this->_option];
            }
            if (isset($row[$this->_option]) && $row[$this->_option] < $minValue) {
                $minValue = $row[$this->_option];
            }
        }

        # Set up data
        $params['chd'] = 't:';
        $dataDelimiter = ',';
        $coof = $maxValue / 100;
        $is_first = true;
        foreach ($values as $value)
        {
            if (!$is_first) {
                $params['chd'] .= $dataDelimiter;
            }
            $params['chd'] .= $coof != 0 ? round($value / $coof) : '0';
            $is_first = false;
        }

        # Return URL
        $url = $this->getApiUrl();
        $isFirst = true;
        foreach ($params as $key => $param) {
            $url .= $isFirst ? '?' : '&';
            $url .= $key . '=' . $param;
            $isFirst = false;
        }
        return $directUrl ? $url : $params;
    }
}
