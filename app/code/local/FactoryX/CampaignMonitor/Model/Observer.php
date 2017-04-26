<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 12/10/2015
 * Time: 10:52
 */
class FactoryX_CampaignMonitor_Model_Observer
{
    /**
     * Add the coupon chooser to the system configuration form
     * @param Varien_Event_Observer $observer
     */
    public function addFormCouponChooser(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        // Check if the form created is a system config form
        if ($block instanceof Mage_Adminhtml_Block_System_Config_Form){
            $form = $observer->getBlock()->getForm();
            // Check if the newsletter coupon fieldset is present
            if ($fieldset = $form->getElement('newsletter_coupon'))
            {
                // Remove the original field from the system.xml
                $fieldset->removeField('newsletter_coupon_coupon');

                // Generate the salesrule chooser widget
                $config = array(
                    'input_id'    => 'newsletter_coupon_coupon',
                    'input_name'  => 'groups[coupon][fields][coupon][value]',
                    'input_label' => 'Coupon',
                    'button_text' => 'Select Coupon...'
                );

                $model = Mage::getModel('salesrule/rule');

                $chooserBlock = 'campaignmonitor/adminhtml_salesrule_rule_widget_chooser';
                // Create our chooser with our custom block
                Mage::helper('chooserwidget')->createChooser($model, $fieldset, $config, $chooserBlock);
            }
        }
    }

    /**
     * Editor handle is required for the chooser widget to work
     * @param Varien_Event_Observer $observer
     */
    public function addEditorHandle(Varien_Event_Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        $params = $observer->getAction()->getRequest()->getParams();
        // We add the editor handle when viewing the section where the chooser widget is displayed
        if (array_key_exists('section',$params) && $params['section'] == "newsletter")
        {
            $layout->getUpdate()->addHandle('editor');
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function blockCreateAfter(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Newsletter_Subscriber_Grid) {

            // Add mobile subscription
            $block->addColumnAfter('mobilesubscription',
                array(
                    'header' => Mage::helper('newsletter')->__('SMS Subscription'),
                    'index' => 'subscriber_mobilesubscription',
                    'type' => 'options',
                    'options' => array(
                        'YES' => 'YES',
                        'NO' => 'NO'
                    )
                ),
                'status'
            );

            // Add mobile
            $block->addColumnAfter('mobile',
                array(
                    'header' => Mage::helper('newsletter')->__('Mobile'),
                    'index' => 'subscriber_mobile'
                ),
                'mobilesubscription'
            );

            // Add state
            $block->addColumnAfter('state',
                array(
                    'header' => Mage::helper('newsletter')->__('State'),
                    'index' => 'subscriber_state',
                    'type' => 'options',
                    'options' => Mage::helper('campaignmonitor')->getCampaignMonitorStates()
                ),
                'mobile'
            );

            // Add postcode
            $block->addColumnAfter('postcode',
                array(
                    'header' => Mage::helper('newsletter')->__('Postcode'),
                    'index' => 'subscriber_postcode'
                    //'default'   => '----'
                ),
                'state'
            );

            // Add preferred store
            $block->addColumnAfter('preferredstore',
                array(
                    'header' => Mage::helper('newsletter')->__('Preferred Store'),
                    'index' => 'subscriber_preferredstore',
                    'type' => 'options',
                    'options' => Mage::helper('campaignmonitor')->getStores()
                ),
                'postcode'
            );

            // Add date of birth
            $block->addColumnAfter('dob',
                array(
                    'header' => Mage::helper('newsletter')->__('Birthday'),
                    'index' => 'subscriber_dob',
                    'type' => 'date',
                    'width' => '100px',
                    'renderer' => 'campaignmonitor/adminhtml_newsletter_subscriber_renderer_dob'
                ),
                'preferredstore'
            );

            // Add subscription date
            $block->addColumnAfter('subscriptiondate',
                array(
                    'header' => Mage::helper('newsletter')->__('Subscription Date'),
                    'index' => 'subscriber_subscriptiondate',
                    'type' => 'date',
                ),
                'dob'
            );

            $block->addColumnAfter('coupon',
                array(
                    'header' => Mage::helper('newsletter')->__('Coupon'),
                    'index' => 'subscriber_coupon'
                ),
                'subscriptiondate'
            );

            $block->addColumnAfter('securehash',
                array(
                    'header' => Mage::helper('newsletter')->__('Secure Hash'),
                    'index' => 'subscriber_securehash'
                ),
                'coupon'
            );
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function blockHtmlBefore(Varien_Event_Observer $observer)
    {

        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Newsletter_Subscriber_Grid) {

            // Add custom renderer to first name column
            $firstNameCol = $block->getColumn('firstname');
            $firstNameColData = $firstNameCol->getData();
            $firstNameColData['renderer'] = 'campaignmonitor/adminhtml_newsletter_subscriber_renderer_firstName';
            $firstNameCol->setData($firstNameColData);

            // Add custom renderer to last name column
            $lastnameCol = $block->getColumn('lastname');
            $lastnameColData = $lastnameCol->getData();
            $lastnameColData['renderer'] = 'campaignmonitor/adminhtml_newsletter_subscriber_renderer_lastName';
            $lastnameCol->setData($lastnameColData);

            // Remove unused columns
            $block->removeColumn('website');
            $block->removeColumn('middlename');
            $block->removeColumn('group');
            $block->removeColumn('store');
        }
    }

    /**
     * Add custom routes to the ReCAPTCHA module
     * @param Varien_Event_Observer $observer
     */
    public function addCustomRoute(Varien_Event_Observer $observer)
    {
        $routes = $observer->getEvent()->getRoutes();
        $routes->add(FactoryX_CampaignMonitor_Helper_Data::CAMPAIGNMONITOR_SUBSCRIPTION_ROUTE, Mage::helper('campaignmonitor')->__('Newsletter Subscription'));
    }

    /**
     * Add the recaptcha blocks dynamically based on the recaptcha module presence and configuration
     * @param Varien_Event_Observer $observer
     */
    public function addRecaptchaBlocks(Varien_Event_Observer $observer)
    {
        if (Mage::helper('campaignmonitor')->isRecaptchaAllowedOnSubscription()) {
            $layout = $observer->getLayout();
            $layout->getUpdate()->addHandle('factoryx_campaignmonitor_recaptcha');
        }
    }

}