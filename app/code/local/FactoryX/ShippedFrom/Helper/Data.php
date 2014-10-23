<?php
/**

*/
class FactoryX_ShippedFrom_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $logFileName = 'factoryx_shippedfrom.log';
	
	public $stores = array();
	public $users = array();
	
	public $useShortTitle = false;
	public $byName = false;
	
	public function generateStoresArray($args)
	{
		//$this->log(sprintf("store=%s", print_r($store, true)));
		$title = $args['row']['title'];
		if ($this->useShortTitle && preg_match("/-/", $title)) {
			$parts = preg_split("/-/", $title, 2);
			$title = $parts[1];
		}
		/*
		$stores[$store->getId()] = array(
			'code'      => strtoupper($store->getStoreCode()),
			'region'    => strtoupper($store->getRegion()),
			'title'     => $title
		);
		*/
		$this->stores[$args['row']['location_id']] = sprintf("%s - %s %s",
			strtoupper($args['row']['store_code']),
			strtoupper($args['row']['region']),
			$title
		);
	}
	
	public function generateUsersArray($args)
	{
		if ($this->byName) {
			$this->users[$args['row']['username']] = $args['row']['username'];
		}
		else {
			$this->users[$args['row']['user_id']] = $args['row']['username'];
		}
	}

	/*
	getStore
	*/
    public function getStore($storeId) {
        return Mage::getModel('ustorelocator/location')->load($storeId);
    }

    /**
    */
    public function sendPackingSlipToStore($shipment, $recipientEmail = null) {

        if (!$shipment) {
            return $this;
        }

        Mage::helper('shippedfrom')->log(sprintf("%s->recipientEmail=%s", __METHOD__, $recipientEmail) );
        $sendStoreEmail = Mage::getStoreConfigFlag('shippedfrom/settings_email/send_store_email');

        if (filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            $parts = explode('@', $recipientEmail);
            $excludeStores = Mage::getStoreConfig('shippedfrom/settings_email/exclude_stores');
            $excludeStores = array_map('strtolower', explode(",", $excludeStores));
            if (in_array(strtolower($parts[0]), $excludeStores)) {
                Mage::helper('shippedfrom')->log(sprintf("%s->exclude=%s|%s", __METHOD__, $parts[0], $recipientEmail) );
                $sendStoreEmail = false;
            }

        }

        $recipientName = null;

        // generate email ???
        /*
        $shippedFrom = $shipment->getShippedFrom(); // shipped_from
        $store = Mage::helper('shippedfrom')->getStore($shippedFrom);
        $recipientName = $store->getTitle();
        if (!$recipientEmail) {
            $recipientEmail = $this->getStoreEmail($store);
            $recipientName = $recipientEmail;
        }
        */

        // Zend_Pdf
        $pdf = Mage::getModel('sales/order_pdf_shipment')->getPdf(array($shipment));
        $fileName = sprintf("packing_slip_%s.pdf", $shipment->getIncrementId());

        $templateCode = Mage::getStoreConfig('shippedfrom/settings_email/template');
        if (!empty($templateCode) && is_numeric($templateCode)) {
            $emailTemplate = Mage::getModel('core/email_template')->load($templateCode);
        }
        else {
            // value defaults to config path
            if (empty($templateCode) || preg_match("/shippedfrom_settings_email_template/", $templateCode)) {
                $templateCode = 'shippedfrom_packing_slip_text';
            }
            $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateCode);
        }

        //$emailTemplate = Mage::getModel('core/email_template')->setDesignConfig(array('area' => 'frontend', 'store' => $storeId = 1));
        Mage::helper('shippedfrom')->log(sprintf("%s->emailTemplate=%s [%s]", __METHOD__, $templateCode, get_class($emailTemplate)) );

        // default sender
        $sender = array(
            'name'  => Mage::getStoreConfig('trans_email/ident_general/name'),
            'email' => Mage::getStoreConfig('trans_email/ident_general/email')
        );
        if (Mage::getStoreConfig('shippedfrom/settings_email/sender_name')) {
            $sender['name'] = Mage::getStoreConfig('shippedfrom/settings_email/sender_name');
        }
        if (Mage::getStoreConfig('shippedfrom/settings_email/sender_email')) {
            $sender['email'] = Mage::getStoreConfig('shippedfrom/settings_email/sender_email');
        }
        $this->log(sprintf("%s->sender=%s", __METHOD__, print_r($sender, true)) );
        $emailTemplate->setSenderName($sender['name']);
        $emailTemplate->setSenderEmail($sender['email']);

        // Set variables that can be used in email template
        $templateVars = array(
            'subject' => sprintf("%s - Packing Slip for Order #%s",
                Mage::app()->getStore()->getName(),
                $shipment->getOrder()->getIncrementId()
            ),
            'orderNbr' => $shipment->getOrder()->getIncrementId()
        );

        //$this->log(sprintf("%s->templateVars: %s", __METHOD__, print_r($templateVars, true)));
        //$emailBody = $emailTemplate->getProcessedTemplate($templateVars);
        //$this->log(sprintf("%s->body: %s", __METHOD__, $emailBody));
        $recipientEmails = array();
        if ($sendStoreEmail) {
            Mage::helper('shippedfrom')->log(sprintf("email '%s' to '%s'", $fileName, $recipientEmail) );
            $this->addAttachment($emailTemplate, $pdf->render(), $fileName);
            try {
                $recipientEmails[] = $recipientEmail;
                $emailTemplate->send($recipientEmail, $sender['name'], $templateVars);
            }
            catch(Exception $ex) {
                $this->log(sprintf("error: %s", $ex->getMessage()) );
            }
        }
        $additionalEmails = Mage::getStoreConfig('shippedfrom/settings_email/additional_emails');
        if ($additionalEmails) {
            $addr = explode(",", $additionalEmails);
            foreach($addr as $recipientEmail) {
                if (filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                    // don't send twice
                    if (!in_array($recipientEmail, $recipientEmails)) {
                        $this->log(sprintf("email additional '%s' to '%s'", $fileName, $recipientEmail) );
                        $this->addAttachment($emailTemplate, $pdf->render(), $fileName);
                        try {
                            $recipientEmails[] = $recipientEmail;
                            $emailTemplate->send($recipientEmail, $sender['name'], $templateVars);
                        }
                        catch(Exception $ex) {
                            $this->log(sprintf("error: %s", $ex->getMessage()) );
                        }
                    }
                }
                else {
                    $this->log(sprintf("%s->additional email '%s' invalid!", __METHOD__, $recipientEmail));
                }
            }
        }
        return $recipientEmails;
    }

    /**
     * addAttachment
     *
     * use before each call to sendTransactional, as subsequent sends removes attachments (not sure why)
     *
     * @param Zend_Mail $mailer mail to add attachment to
     * @param string $data the data to encode
     * @param string $fileName attachment file name
     * @return (type) (name)
     */
    public function addAttachment($emailTemplate, $data, $fileName) {
        $emailTemplate->getMail()->createAttachment(
            $data,
            //file_get_contents($tmpFileName),
            Zend_Mime::TYPE_OCTETSTREAM,
            Zend_Mime::DISPOSITION_ATTACHMENT,
            Zend_Mime::ENCODING_BASE64,
            $fileName
            //basename($tmpFileName)
        );

    }

    /**
    */
    public function getStoreEmail($store) {
        $emailPostfix = "factoryx.com.au";
        if (Mage::getStoreConfig('shippedfrom/settings_email/email_postfix')) {
            $emailPostfix = Mage::getStoreConfig('shippedfrom/settings_email/email_postfix');
        }
        $recipientEmail = sprintf("%s@%s", $store->getStoreCode(), $emailPostfix);
        $this->log(sprintf("%s->recipientEmail=%s", __METHOD__, $recipientEmail));
        return $recipientEmail;
    }

	/**
	*/
	public function getStores($useShortTitle = false) {
		//$stores['NULL'] = "> None selected"; // used for grids
		
		$this->useShortTitle = $useShortTitle;

		$storesColl = Mage::getModel('ustorelocator/location')->getCollection()
            ->setOrder('region', 'ASC')
            ->setOrder('store_code', 'ASC');
			
		// Call iterator walk method with collection query string and callback method as parameters
		// Has to be used to handle massive collection instead of foreach
		Mage::getSingleton('core/resource_iterator')->walk($storesColl->getSelect(), array(array($this, 'generateStoresArray')));

		asort($stores);
		//$this->log(sprintf("%s->stores=%s", __METHOD__, print_r($stores, true)) );
		return $this->stores;
	}

	/**
	get a hash of user ids and names

	$currentUser = Mage::getSingleton('admin/session')->getUser();
	*/
	public function getUsers($byName = false) {
	
		$this->byName = $byName;
	
		// used for grids
		if ($this->byName) {
			$this->users['NULL'] = "> None selected";
		}
		$usersColl = Mage::getResourceModel('admin/user_collection');
		
		// Call iterator walk method with collection query string and callback method as parameters
		// Has to be used to handle massive collection instead of foreach
		Mage::getSingleton('core/resource_iterator')->walk($usersColl->getSelect(), array(array($this, 'generateUsersArray')));

		asort($this->users);
		return $this->users;
	}


	/**
	email admin only
	*/
    public function isShowAllStores($storeId = null) {
        $retVal = Mage::getStoreConfigFlag('shippedfrom/settings_form/show_all_stores', $storeId);
        return ($retVal ? 1 : 0);
    }

    public function isCheckSendStoreEmail($storeId = null) {
        $retVal = Mage::getStoreConfigFlag('shippedfrom/settings_form/check_send_store_email', $storeId);
        return ($retVal ? 1 : 0);
    }

	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) {
		Mage::log($data, null, $this->logFileName);
	}

    /**
     * getDefaultShippedFrom
     */
    public function getDefaultShippedFrom() {
        $shippedFrom = Mage::getStoreConfig('shippedfrom/default_values/shipped_from');
        if (empty($shippedFrom)) {
            $shippedFrom = "H00";
        }
		$stores = Mage::getModel('ustorelocator/location')->getCollection();
		$storeId = 0;
		foreach($stores as $store) {
		    if ( preg_match(sprintf("/%s/i", $shippedFrom), $store->getStoreCode()) ) {
                $storeId = $store->getId();
		    }
		}
        return $storeId;
    }

    /**
     * getDefaultShippedFrom
     */
    public function getDefaultShippedBy() {
        $shippedBy = Mage::getStoreConfig('shippedfrom/default_values/shipped_by');
        $userName = "Unknown";
        if (empty($shippedBy) || preg_match("/user/", $shippedBy) ) {
            $user = Mage::getSingleton('admin/session');
            $userName = $user->getUser()->getName();
        }
        else {
            $user = Mage::getModel('admin/user')->load($shippedBy);
            if ($user) {
                $userName = $user->getName();
            }
        }
        return $userName;
    }

}
