<?php

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
		$fieldset->addField('status', 'select', array(
            'label' => Mage::helper('contests')->__('Status'),
            'name' => 'status',
            'values' => array(
				array(
                    'value' => 2,
                    'label' => Mage::helper('contests')->__('Automatic'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('Disabled'),
                ),
				array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Enabled'),
                )
            ),
			'note'      => Mage::helper('contests')->__('The automatic status will use start and end dates to automatically enable and disable the contest.')
		));
		
		// Output format for the start and end dates
		$outputFormat = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

		// Field for the start date validated with javascript
        $fieldset->addField('start_date', 'date', array(
            'name' => 'start_date',
            'label' => $this->__('Start Date'),
            'title' => $this->__('Start Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
			'class' => 'validate-date-au',
			'note'      => Mage::helper('contests')->__('Only with automatic status: contest will automatically start at 1am on the start date.')
        ));
		
		// Field for the end date validated with javascript
		$fieldset->addField('end_date', 'date', array(
            'name' => 'end_date',
            'label' => $this->__('End Date'),
            'title' => $this->__('End Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format' => $outputFormat,
			'class' => 'validate-date-au',
			'note'      => Mage::helper('contests')->__('Only with automatic status: contest will automatically end at 1am on the end date.')
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

        // Displaying the more friends
        $fieldset->addField('more_friend_line', 'text', array(
            'label' => Mage::helper('contests')->__('More Friend Line'),
            'name' => 'more_friend_line',
            'note' => 'The line to display before email field (e.g. the more friend you refer...)'
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
			'note'	=>	Mage::helper('contests')->__('Only for refer a friend contests.')
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
		
		// Field for the thank you type of the contest
		$fieldset->addField('thank_you_link_type', 'select', array(
            'label' => Mage::helper('contests')->__('Thank You Link Option'),
            'name' => 'thank_you_link_type',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('contests')->__('Click Through On The Image'),
                ),
                array(
                    'value' => 1,
                    'label' => Mage::helper('contests')->__('Auto Redirect'),
                )
            )
		));
		
		// Field for the thank you redirect url of the contest
        $fieldset->addField('thank_you_redirect_url', 'text', array(
            'label' => Mage::helper('contests')->__('Thank You Redirect Url'),
            'name' 	=> 'thank_you_redirect_url',
			'note'  => Mage::helper('contests')->__('URL the thank you page redirects to. Start with forward slash for absolute path. Leave empty for home page.')
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
            $form->setValues($data);
        }
        return parent::_prepareForm();
    }
	
}