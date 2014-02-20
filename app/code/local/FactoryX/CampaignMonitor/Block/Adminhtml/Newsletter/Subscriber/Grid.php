<?php

class FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
    protected function _prepareColumns()
    {
        $this->addColumn('subscriber_id', array(
            'header'    => Mage::helper('newsletter')->__('ID'),
            'index'     => 'subscriber_id'
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('newsletter')->__('Email'),
            'index'     => 'subscriber_email'
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('newsletter')->__('Type'),
            'index'     => 'type',
            'type'      => 'options',
            'options'   => array(
                1  => Mage::helper('newsletter')->__('Guest'),
                2  => Mage::helper('newsletter')->__('Customer')
            )
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('newsletter')->__('First Name'),
            'index'     => 'customer_firstname',
            'default'   => '----',
            'renderer'  => 'FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_FirstName'
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('newsletter')->__('Last Name'),
            'index'     => 'customer_lastname',
            'default'   => '----',
            'renderer'  => 'FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_LastName'
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('newsletter')->__('Status'),
            'index'     => 'subscriber_status',
            'type'      => 'options',
            'options'   => array(
                Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE   => Mage::helper('newsletter')->__('Not Activated'),
                Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED   => Mage::helper('newsletter')->__('Subscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => Mage::helper('newsletter')->__('Unsubscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED => Mage::helper('newsletter')->__('Unconfirmed'),
            )
        ));
		
		/*
		$this->addColumn('mobilesubscription', array(
            'header'    => Mage::helper('newsletter')->__('SMS Subscription'),
            'index'     => 'subscriber_mobilesubscription',
            'type'      => 'options',
            'options'   => array(
                'YES'	=>	'YES',
				'NO'	=>	'NO'
            )
        ));
		*/

        $this->addColumn('mobile', array(
            'header'    => Mage::helper('newsletter')->__('Mobile'),
            'index'     => 'subscriber_mobile'
            //'default'   => '----'
        ));

        $this->addColumn('state', array(
            'header'    => Mage::helper('newsletter')->__('State'),
            'index'     => 'subscriber_state',
            'type'		=> 'options',
            'options'   => Mage::helper('campaignmonitor')->getCampaignMonitorStates()
        ));
		
		$this->addColumn('postcode', array(
            'header'    => Mage::helper('newsletter')->__('Postcode'),
            'index'     => 'subscriber_postcode'
            //'default'   => '----'
        ));
		
		
		
		
		/*
		$this->addColumn('periodicity', array(
            'header'    => Mage::helper('newsletter')->__('Periodicity'),
            'index'     => 'subscriber_periodicity',
            'type'		=> 'options',
            'options'   => array(
				'Weekly'									=>	'Weekly',
            	'Fortnightly'								=>	'Fortnightly',
            	'Monthly'									=>	'Monthly',
				'Only when we have special news and events'	=>	'Only when we have special news and events'
            )
        ));
		
        $this->addColumn('preferredstore', array(
            'header'    => Mage::helper('newsletter')->__('Preferred Store'),
            'index'     => 'subscriber_preferredstore',
            'type'      => 'options',
            'options'   => Mage::helper('campaignmonitor')->getStores()
        ));

        $this->addColumn('jobinterest', array(
            'header'    => Mage::helper('newsletter')->__('Job Interest'),
            'index'     => 'subscriber_jobinterest',
            'type'      => 'options',
            'options'   => array(
                'YES'   =>  'YES',
                'NO'    =>  'NO'
            )
        ));
		*/
		
        $this->addColumn('promocode', array(
            'header'    => Mage::helper('newsletter')->__('Promo Code'),
            'index'     => 'subscriber_promocode'
            //'default'   => '----'
        ));
				
		$this->addColumn('dob', array(
            'header'    => Mage::helper('newsletter')->__('Birthday'),
            'index'     => 'subscriber_dob',
            'type'		=> 'date',
            'width'		=> '100px',
            'renderer'  => 'FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_Dob'
        ));
		
		$this->addColumn('subscriptiondate', array(
            'header'    => Mage::helper('newsletter')->__('Subscription Date'),
            'index'     => 'subscriber_subscriptiondate',
			'type'		=> 'date',
            //'default'   => '----'
        ));
		
		$this->addColumn('coupon', array(
            'header'    => Mage::helper('newsletter')->__('Coupon'),
            'index'     => 'subscriber_coupon'
        ));
		
        $this->addColumn('securehash', array(
            'header'    => Mage::helper('newsletter')->__('Secure Hash'),
            'index'     => 'subscriber_securehash'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('XML'));
        
		// We don't call the Mage_Adminhtml_Block_Newsletter_Subscriber_Grid function as it would rewrite our columns
        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}
