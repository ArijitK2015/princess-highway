<?php
	/**
		Update the configuration for FactoryX_Campaignmonitor
	**/

	// Is the module active?
	if (!Mage::getConfig()->getModuleConfig('FactoryX_CampaignMonitor')->is('active', 'true')) {
		return;
	}

	$installer = $this;
	$installer->startSetup();	

	$envConfig = array(
	    "default" => array(
	        "newsletter/campaignmonitor/api_key" => '05a9080738e05b84c21490161664fd9e',
	        "newsletter/campaignmonitor/list_id" => '12c46dc0fb65648c33bf9f2a941cf150',
	        "newsletter/campaignmonitor/m_to_cm_attributes" => 'a:9:{s:18:"_1400826933455_455";a:4:{s:10:"formfields";s:5:"email";s:7:"magento";s:5:"email";s:10:"subscriber";s:16:"subscriber_email";s:15:"campaignmonitor";s:5:"email";}s:18:"_1400826959989_989";a:4:{s:10:"formfields";s:9:"firstname";s:7:"magento";s:9:"firstname";s:10:"subscriber";s:20:"subscriber_firstname";s:15:"campaignmonitor";s:9:"firstname";}s:18:"_1400826960608_608";a:4:{s:10:"formfields";s:8:"lastname";s:7:"magento";s:8:"lastname";s:10:"subscriber";s:19:"subscriber_lastname";s:15:"campaignmonitor";s:8:"lastname";}s:18:"_1400826961263_263";a:4:{s:10:"formfields";s:6:"mobile";s:7:"magento";s:0:"";s:10:"subscriber";s:17:"subscriber_mobile";s:15:"campaignmonitor";s:6:"Mobile";}s:18:"_1400826961750_750";a:4:{s:10:"formfields";s:5:"state";s:7:"magento";s:0:"";s:10:"subscriber";s:16:"subscriber_state";s:15:"campaignmonitor";s:5:"State";}s:18:"_1400826962344_344";a:4:{s:10:"formfields";s:8:"postcode";s:7:"magento";s:0:"";s:10:"subscriber";s:19:"subscriber_postcode";s:15:"campaignmonitor";s:8:"Postcode";}s:18:"_1400826962833_833";a:4:{s:10:"formfields";s:3:"dob";s:7:"magento";s:0:"";s:10:"subscriber";s:14:"subscriber_dob";s:15:"campaignmonitor";s:3:"DOB";}s:18:"_1400826963405_405";a:4:{s:10:"formfields";s:0:"";s:7:"magento";s:0:"";s:10:"subscriber";s:21:"subscriber_securehash";s:15:"campaignmonitor";s:10:"securehash";}s:18:"_1400827029175_175";a:4:{s:10:"formfields";s:0:"";s:7:"magento";s:0:"";s:10:"subscriber";s:27:"subscriber_subscriptiondate";s:15:"campaignmonitor";s:4:"Date";}}'
	    ),
	    "prod" => array(
	    ),
	    "staging" => array(	       
	    ),
	    "dev" => array(
	        "newsletter/campaignmonitor/api_key" => '05a9080738e05b84c21490161664fd9e',
	        "newsletter/campaignmonitor/list_id" => '12c46dc0fb65648c33bf9f2a941cf150',
	        "newsletter/campaignmonitor/m_to_cm_attributes" => 'a:9:{s:18:"_1400826933455_455";a:4:{s:10:"formfields";s:5:"email";s:7:"magento";s:5:"email";s:10:"subscriber";s:16:"subscriber_email";s:15:"campaignmonitor";s:5:"email";}s:18:"_1400826959989_989";a:4:{s:10:"formfields";s:9:"firstname";s:7:"magento";s:9:"firstname";s:10:"subscriber";s:20:"subscriber_firstname";s:15:"campaignmonitor";s:9:"firstname";}s:18:"_1400826960608_608";a:4:{s:10:"formfields";s:8:"lastname";s:7:"magento";s:8:"lastname";s:10:"subscriber";s:19:"subscriber_lastname";s:15:"campaignmonitor";s:8:"lastname";}s:18:"_1400826961263_263";a:4:{s:10:"formfields";s:6:"mobile";s:7:"magento";s:0:"";s:10:"subscriber";s:17:"subscriber_mobile";s:15:"campaignmonitor";s:6:"Mobile";}s:18:"_1400826961750_750";a:4:{s:10:"formfields";s:5:"state";s:7:"magento";s:0:"";s:10:"subscriber";s:16:"subscriber_state";s:15:"campaignmonitor";s:5:"State";}s:18:"_1400826962344_344";a:4:{s:10:"formfields";s:8:"postcode";s:7:"magento";s:0:"";s:10:"subscriber";s:19:"subscriber_postcode";s:15:"campaignmonitor";s:8:"Postcode";}s:18:"_1400826962833_833";a:4:{s:10:"formfields";s:3:"dob";s:7:"magento";s:0:"";s:10:"subscriber";s:14:"subscriber_dob";s:15:"campaignmonitor";s:3:"DOB";}s:18:"_1400826963405_405";a:4:{s:10:"formfields";s:0:"";s:7:"magento";s:0:"";s:10:"subscriber";s:21:"subscriber_securehash";s:15:"campaignmonitor";s:10:"securehash";}s:18:"_1400827029175_175";a:4:{s:10:"formfields";s:0:"";s:7:"magento";s:0:"";s:10:"subscriber";s:27:"subscriber_subscriptiondate";s:15:"campaignmonitor";s:4:"Date";}}',
	    	"newsletter/campaignmonitor/proxy" => '192.168.100.3:3128'
	    )
	);

	foreach($envConfig[$env] as $path => $val) {
        Mage::log(sprintf("%s->%s: %s", __METHOD__, $path, $val) );
        $coreConfig->saveConfig($path, $val, 'default', 0);
    }

	$installer->endSetup();

?>