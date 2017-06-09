<?php
/**
 * Sales Rule Chooser
 *
 * @category   FactoryX
 * @package    FactoryX_CampaignMonitor
 * @author     Raphael @ Factory X <raphael@factoryx.com.au>
 */
class FactoryX_CampaignMonitor_Block_Adminhtml_Salesrule_Rule_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        //$this->setDefaultSort('name');
        $this->setUseAjax(true);
        $this->setDefaultFilter(array('chooser_is_active' => '1'));
    }

    /**
     * Before the HTML preparation to set the value on the chooser
     * @param Varien_Data_Form_Element_Abstract $element
     */
    public function beforePrepareElementHtml(Varien_Data_Form_Element_Abstract  $element)
    {
        if ($configValue = Mage::getStoreConfig('newsletter/coupon/coupon'))
        {
            $element->setValue($configValue);
        }
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->beforePrepareElementHtml($element);

        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('adminhtml/cmwidget/chooser', array('uniq_id' => $uniqId));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);


        if ($element->getValue()) {
            $rule = Mage::getModel('salesrule/rule')->load((int)$element->getValue());
            if ($rule->getId()) {
                $chooser->setLabel($rule->getName());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var ruleTitle = trElement.down("td").next().innerHTML;
                var ruleId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                '.$chooserJsObject.'.setElementValue(ruleId);
                '.$chooserJsObject.'.setElementLabel(ruleTitle);
                '.$chooserJsObject.'.close();
            }
        ';
        return $js;
    }

    /**
     * Prepare rules collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('salesrule/rule')->getResourceCollection();
        // Only display the auto generated rules
        $collection->addFieldToFilter('use_auto_generation', array('eq' =>1));
        // Only enabled rules
        $collection->addFieldToFilter('is_active', array('eq' =>1));
        $collection->addWebsitesToResult();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for rules grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('chooser_rule_id', array(
            'header'    => Mage::helper('cms')->__('ID'),
            'align'     => 'right',
            'index'     => 'rule_id',
            'width'     => 50
        ));

        $this->addColumn('chooser_name', array(
            'header'    => Mage::helper('cms')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('chooser_coupon_code', array(
            'header'    => Mage::helper('cms')->__('Coupon Code'),
            'align'     => 'left',
            'index'     => 'code'
        ));

        $this->addColumn('chooser_from_date', array(
            'header'    => Mage::helper('cms')->__('Date Start'),
            'index'     => 'from_date',
            'type'      => 'date'
        ));

        $this->addColumn('chooser_to_date', array(
            'header'    => Mage::helper('cms')->__('Date Expire'),
            'index'     => 'to_date',
            'type'      => 'date'
        ));

        $this->addColumn('chooser_is_active', array(
            'header'    => Mage::helper('cms')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            )
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('chooser_rule_website', array(
                'header'    => Mage::helper('salesrule')->__('Website'),
                'align'     =>'left',
                'index'     => 'website_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                'width'     => 200,
            ));
        }

        $this->addColumn('chooser_sort_order', array(
            'header'    => Mage::helper('salesrule')->__('Priority'),
            'align'     => 'right',
            'index'     => 'sort_order',
            'width'     => 100,
        ));

        return parent::_prepareColumns();
    }

    /**
     * @return mixed
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/cmwidget/chooser', array('_current' => true));
    }
}
