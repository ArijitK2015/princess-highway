<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_General
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the general tab to edit a contest
     */
    protected function _prepareForm()
    {
        // Model registered as a contest
        $model = Mage::registry('contests');

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contests_form', array('legend' => Mage::helper('contests')->__('General Information')));

        // Field for the title of the contest
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('contests')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));

        // Field for the identifier (URL Key) of the contest validated with javascript
        $fieldset->addField('identifier', 'text', array(
                'label' => Mage::helper('contests')->__('Identifier'),
                'class' => 'required-entry validate-identifier',
                'required' => true,
                'name' => 'identifier',
                'note'      => Mage::helper('contests')->__('eg: domain.com/identifier')
            )
        );

        // Field for the status (Enabled or not)
        $statusField = $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('contests')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => FactoryX_Contests_Model_Status::STATUS_AUTOMATIC,
                    'label' => Mage::helper('contests')->__('Automatic'),
                ),
                array(
                    'value' => FactoryX_Contests_Model_Status::STATUS_DISABLED,
                    'label' => Mage::helper('contests')->__('Disabled'),
                ),
                array(
                    'value' => FactoryX_Contests_Model_Status::STATUS_ENABLED,
                    'label' => Mage::helper('contests')->__('Enabled'),
                )
            ),
            'note'      => Mage::helper('contests')->__('The automatic status will use start and end dates to automatically enable and disable the contest.')
        ));

        // Output format for the start and end dates
        // Ugly version compatibility
        $version = Mage::getVersionInfo();
        $outputFormat = ($version['minor'] == 9) ? Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) : Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        // Field for the start date validated with javascript
        $startDateField = $fieldset->addField('start_date', 'date', array(
            'name' => 'start_date',
            'label' => $this->__('Start Date'),
            'title' => $this->__('Start Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'class' => 'validate-datetime-au',
            'time'  => true,
            'note'      => Mage::helper('contests')->__('Only with automatic status.')
        ));

        // Field for the end date validated with javascript
        $endDateField = $fieldset->addField('end_date', 'date', array(
            'name' => 'end_date',
            'label' => $this->__('End Date'),
            'title' => $this->__('End Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
            'class' => 'validate-datetime-au',
            'time'  => true,
            'note'      => Mage::helper('contests')->__('Only with automatic status.')
        ));

        // Field for the type of the contest (Refer A Friend or Give Away)
        $fieldset->addField('type', 'select', array(
            'label' => Mage::helper('contests')->__('Type'),
            'name' => 'type',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Refer A Friend'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('contests')->__('Give Away'),
                )
            )
        ));

        // Field to enable gender
        $fieldset->addField('gender', 'select', array(
            'label' => Mage::helper('contests')->__('Ask for gender?'),
            'name' => 'gender',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Yes'),
                )
            )
        ));

        $states = Mage::helper('contests')->getStates();
        $valuesStates = array();
        foreach ($states as $state)
        {
            $valuesStates[] = array('value' => $state, 'label' => $state);
        }

        $fieldset->addField('states', 'multiselect', array(
            'label' => Mage::helper('contests')->__('States Selector'),
            'values' => $valuesStates,
            'name' => 'states'
        ));

        // Displaying the more friends
        $fieldset->addField('more_friend_line', 'text', array(
            'label' => Mage::helper('contests')->__('More Friend Line'),
            'name' => 'more_friend_line',
            'note' => 'The line to display before email field (e.g. the more friend you refer...)'
        ));

        // Displaying the more friends
        $fieldset->addField('please_text', 'text', array(
            'label' => Mage::helper('contests')->__('Please Enter Your Details text'),
            'name' => 'please_text',
            'note' => 'Default to "Please Enter Your Details"'
        ));

        // Field for the allow duplicate entries of the contest
        $fieldset->addField('allow_duplicate_entries', 'select', array(
            'label' => Mage::helper('contests')->__('Allow Duplicate Entries ?'),
            'name' => 'allow_duplicate_entries',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Yes'),
                )
            )
        ));

        // Field for the allow duplicate referrals of the contest (Refer A Friend only)
        $fieldset->addField('allow_duplicate_referrals', 'select', array(
            'label' => Mage::helper('contests')->__('Allow Duplicate Referrals ?'),
            'name' => 'allow_duplicate_referrals',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Yes'),
                )
            ),
            'note'  =>  Mage::helper('contests')->__('Only for refer a friend contests.')
        ));

        // Field to enable the facebook sharing
        $fieldset->addField('share_on_facebook', 'select', array(
            'label' => Mage::helper('contests')->__('Display Facebook Sharer'),
            'name' => 'share_on_facebook',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('No'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Yes'),
                )
            )
        ));

        // Field for facebook app id
        $fieldset->addField('facebook_app_id', 'text', array(
            'label' => Mage::helper('contests')->__('Facebook App Id'),
            'name' => 'facebook_app_id',
        ));

        // Field for facebook app id
        $fieldset->addField('facebook_app_secret', 'text', array(
            'label' => Mage::helper('contests')->__('Facebook App Secret'),
            'name' => 'facebook_app_secret',
        ));

        // Field for facebook page
        $fieldset->addField('facebook_page', 'text', array(
            'label' => Mage::helper('contests')->__('Facebook Page Link'),
            'name' => 'facebook_page',
            'note' => Mage::helper('contests')->__('E.g. https://www.facebook.com/jacklondonofficial/')
        ));

        // Field for the thank you redirect url of the contest
        $fieldset->addField('thank_you_redirect_url', 'text', array(
            'label' => Mage::helper('contests')->__('Thank You Redirect Url'),
            'name'  => 'thank_you_redirect_url',
            'note'  => Mage::helper('contests')->__('URL the thank you page redirects to. Start with forward slash for absolute path. Leave empty for home page.')
        ));

        // Field for the campaign monitor list of the contest
        $fieldset->addField('campaignmonitor_list', 'select', array(
            'label' => Mage::helper('contests')->__('Campaign Monitor Subscription List'),
            'name' => 'campaignmonitor_list',
            'values' => Mage::helper('contests')->getCampaignMonitorLists(),
            'note' => Mage::helper('contests')->__('Default to the website campaign monitor list.')
        ));

        // We fill the form based on the session or the data registered

        if (Mage::getSingleton('adminhtml/session')->getContestsData())
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getContestsData());
            Mage::getSingleton('adminhtml/session')->setContestsData(null);
        }
        elseif (Mage::registry('contests_data'))
        {
            $data = Mage::registry('contests_data')->getData();
            // Make automatic status default
            if (!array_key_exists('status',$data)) $data['status'] = 2;
            // Make start date the beginning of the current date
            if (!array_key_exists('start_date',$data))
            {
                // 2016-04-25 00:00:00
                $startDate = Mage::app()->getLocale()->date(null, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
                $startDate->set('00:00:00',Zend_Date::TIMES);
                $data['start_date'] = $startDate;
            }
            // Make end date the end of the current date
            if (!array_key_exists('end_date',$data))
            {
                // 2016-04-25 00:00:00
                $endDate = Mage::app()->getLocale()->date(null, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),null, true);
                $endDate->set('23:59:59',Zend_Date::TIMES);
                $data['end_date'] = $endDate;
            }
            $form->setValues($data);
        }

        // Add dynamic dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($statusField->getHtmlId(), $statusField->getName())
            ->addFieldMap($startDateField->getHtmlId(), $startDateField->getName())
            ->addFieldMap($endDateField->getHtmlId(), $endDateField->getName())
            ->addFieldDependence(
                $startDateField->getName(),
                $statusField->getName(),
                FactoryX_Contests_Model_Status::STATUS_AUTOMATIC)
            ->addFieldDependence(
                $endDateField->getName(),
                $statusField->getName(),
                FactoryX_Contests_Model_Status::STATUS_AUTOMATIC)
        );

        return parent::_prepareForm();
    }

}