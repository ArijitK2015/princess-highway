<?php

/**
 * Class FactoryX_ShippedFrom_Helper_Data
 */
class FactoryX_ShippedFrom_Helper_Data extends Mage_Core_Helper_Abstract
{

    public $stores = array();
    public $users = array();
    public $useShortTitle = false;
    public $userField = "user_id";

    protected static $_shippedByUserFields = array("user_id", "username", "fullname");

    protected $_logFileName = 'factoryx_shippedfrom.log';
    protected $_editPermissionsPath = 'sales/shipment/actions/can_edit_shippedfrom';

    /**
     * @param $args
     */
    public function generateStoresArray($args)
    {
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

        $showStores = array_map(
            'strtolower',
            explode(",", Mage::getStoreConfig('shippedfrom/settings_form/show_stores'))
        );
        if (in_array(strtolower($args['row']['store_code']), $showStores)) {
            $this->stores[$args['row']['location_id']] = sprintf(
                "%s - %s %s",
                strtoupper($args['row']['store_code']),
                strtoupper($args['row']['region']),
                $title
            );
        }
    }

    /**
     * what is the key value?
     * @param $args
     */
    public function generateUsersArray($args)
    {
        $value = sprintf("%s %s", $args['row']['firstname'], $args['row']['lastname']);
        $field = $this->getShippedByUserField();
        if (preg_match("/(user_id|username)/i", $field)) {
            $key = $args['row'][$field];
            $this->users[$key] = $value;
        } else {
            $key = $value;
            $this->users[$key] = $value;
        }
    }

    /**
     * @param $storeId
     * @return Mage_Core_Model_Abstract
     */
    public function getStore($storeId)
    {
        return Mage::getModel('ustorelocator/location')->load($storeId);
    }

    /**
     * @param $shipment
     * @param null $recipientEmail
     * @param null $labelUri
     * @return array | $this
     */
    public function sendPackingSlipToStore($shipment, $recipientEmail = null, $labelUri = null)
    {
        // FactoryX_Sales_Model_Order_Shipment || Mage_Sales_Model_Order_Shipment
        if (!$shipment || !preg_match("/Sales_Model_Order_Shipment/i", get_class($shipment))) {
            return $this;
        }

        $sendStoreEmail = Mage::getStoreConfigFlag('shippedfrom/settings_email/send_store_email');

        /** @todo check which store */
        if (filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            $parts = explode('@', $recipientEmail);
            $excludeStores = Mage::getStoreConfig('shippedfrom/settings_email/exclude_stores');
            $excludeStores = array_map('strtolower', explode(",", $excludeStores));
            if (in_array(strtolower($parts[0]), $excludeStores)) {
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
        $attachmentPrefix = Mage::getStoreConfig('shippedfrom/settings_email_attachment/attachment_prefix');
        $fileName = sprintf("%s%s.pdf", $attachmentPrefix, $shipment->getIncrementId());

        $templateCode = Mage::getStoreConfig('shippedfrom/settings_email/template');
        if (!empty($templateCode) && is_numeric($templateCode)) {
            $emailTemplate = Mage::getModel('core/email_template')->load($templateCode);
        } else {
            // value defaults to config path
            if (empty($templateCode) || preg_match("/shippedfrom_settings_email_template/", $templateCode)) {
                $templateCode = 'shippedfrom_packing_slip_text';
            }

            $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateCode);
        }

        /*$emailTemplate = Mage::getModel('core/email_template')
        ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId = 1));*/

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

        $emailTemplate->setSenderName($sender['name']);
        $emailTemplate->setSenderEmail($sender['email']);

        // Set variables that can be used in email template
        $templateVars = array(
            'subject'   => $this->getSubject($shipment),
            'orderNbr'  => $shipment->getOrder()->getIncrementId(),
            'labelUri'  => $labelUri ? $labelUri : null
        );

        //$emailBody = $emailTemplate->getProcessedTemplate($templateVars);
        $recipientEmails = array();
        if ($sendStoreEmail) {
            $addr = explode(",", $recipientEmail);
            foreach ($addr as $recipientEmail) {
                if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                    $this->log(sprintf("%s->additional email '%s' invalid!", __METHOD__, $recipientEmail));
                    continue;
                }

                $this->addAttachment($emailTemplate, $pdf->render(), $fileName);
                try {
                    $recipientEmails[] = $recipientEmail;
                    $emailTemplate->send($recipientEmail, $sender['name'], $templateVars);
                }
                catch(Exception $ex) {
                    $this->log(sprintf("error: %s", $ex->getMessage()));
                }
            }
        }

        $additionalEmails = Mage::getStoreConfig('shippedfrom/settings_email/additional_emails');
        if ($additionalEmails) {
            $addr = explode(",", $additionalEmails);
            foreach ($addr as $recipientEmail) {
                if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                    $this->log(sprintf("%s->additional email '%s' invalid!", __METHOD__, $recipientEmail));
                    continue;
                }

                if (!in_array($recipientEmail, $recipientEmails)) {
                    $this->log(sprintf("email additional '%s' to '%s'", $fileName, $recipientEmail));
                    $this->addAttachment($emailTemplate, $pdf->render(), $fileName);
                    try {
                        $recipientEmails[] = $recipientEmail;
                        $emailTemplate->send($recipientEmail, $sender['name'], $templateVars);
                    }
                    catch(Exception $ex) {
                        $this->log(sprintf("error: %s", $ex->getMessage()));
                    }
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
     * @param $emailTemplate
     * @param string $data the data to encode
     * @param string $fileName attachment file name
     * @internal param Zend_Mail $mailer mail to add attachment to
     * @return void (type) (name)
     */
    public function addAttachment($emailTemplate, $data, $fileName)
    {
        $emailTemplate->getMail()->createAttachment(
            $data,
            //file_get_contents($tmpFileName),
            Zend_Mime::TYPE_OCTETSTREAM,
            Zend_Mime::DISPOSITION_ATTACHMENT,
            Zend_Mime::ENCODING_BASE64,
            $fileName //basename($tmpFileName)
        );

    }

    /**
     * @param $store
     * @return string
     */
    public function getStoreEmail($store)
    {
        // check for mapping
        $mapping = $this->generateMapping('storecode', 'emailaddress');
        if (is_array($mapping) && !empty($mapping) && array_key_exists($store->getStoreCode(), $mapping)) {
            $recipientEmail = $mapping[$store->getStoreCode()];
        } else {
            $emailPostfix = "factoryx.com.au";
            if (Mage::getStoreConfig('shippedfrom/settings_email/email_postfix')) {
                $emailPostfix = Mage::getStoreConfig('shippedfrom/settings_email/email_postfix');
            }

            $recipientEmail = sprintf("%s@%s", $store->getStoreCode(), $emailPostfix);
        }

        $this->log(sprintf("%s->recipientEmail=%s", __METHOD__, $recipientEmail));
        return $recipientEmail;
    }


    /**
     * generateMapping
     *
     * $mapping = $this->generateMapping('storecode','emailaddress');
     *
     * @param string $source
     * @param string $destination
     * @return array
     */
    public function generateMapping($source, $destination)
    {
        $result = array();
        $storeMapping = Mage::getStoreConfig(
            'shippedfrom/settings_email/store_mappings',
            Mage::app()->getStore()->getStoreId()
        );
        if ($storeMapping) {
            $mappings = $linkedAttributes = unserialize($storeMapping);
        }

        if (isset($mappings)) {
            foreach ($mappings as $mapping) {
                if (!empty($mapping[$source]) && !empty($mapping[$destination])) {
                    $result[$mapping[$source]] = $mapping[$destination];
                }
            }
        }

        return $result;
    }

    /**
     * get a hash of stores
     *
     * @param bool $useShortTitle
     * @param bool $forGrid
     * @return array
     */
    public function getStores($useShortTitle = false, $forGrid = false)
    {
        $this->useShortTitle = $useShortTitle;

        $storesColl = Mage::getModel('ustorelocator/location')->getCollection()
            ->setOrder('region', 'ASC')
            ->setOrder('store_code', 'ASC');

        // Call iterator walk method with collection query string and callback method as parameters
        // Has to be used to handle massive collection instead of foreach
        Mage::getSingleton('core/resource_iterator')->walk(
            $storesColl->getSelect(),
            array(
                array($this, 'generateStoresArray')
            )
        );

        asort($this->stores);

        // used for grids
        if ($forGrid) {
            $this->stores = array('NULL' => "> None selected") + $this->stores;
        }

        return $this->stores;
    }

    /**
     * get a hash of user ids and names
     *
     * $currentUser = Mage::getSingleton('admin/session')->getUser();
     * @param bool $showNull
     * @return array
     */
    public function getUsers($showNull = false)
    {
        $usersColl = Mage::getResourceModel('admin/user_collection');

        // Call iterator walk method with collection query string and callback method as parameters
        // Has to be used to handle massive collection instead of foreach
        Mage::getSingleton('core/resource_iterator')->walk(
            $usersColl->getSelect(),
            array(
                array($this, 'generateUsersArray')
            )
        );

        asort($this->users);

        // used for grids
        if ($showNull) {
            $this->users = array('NULL' => "> None selected") + $this->users;
            //$this->users['NULL'] = "> None selected";
        }

        return $this->users;
    }

    /**
     * getSubject
     *
     * @param shipment $shipment
     * @return string subject
     */
    protected function getSubject($shipment)
    {
        $subject = Mage::getStoreConfig('shippedfrom/settings_email/subject');
        $this->log(sprintf("subject=%s", $subject));

        // default subject
        if (empty($subject)) {
            $subject = "Shipment";
        } else {
            if (preg_match("/%STORE_NAME%/", $subject)) {
                $subject = preg_replace("/%STORE_NAME%/", Mage::app()->getStore()->getName(), $subject);
            }

            if (preg_match("/%SHIPMENT_NBR%/", $subject)) {
                $subject = preg_replace("/%SHIPMENT_NBR%/", $shipment->getIncrementId(), $subject);
            }

            if (preg_match("/%ORDER_NBR%/", $subject)) {
                $subject = preg_replace("/%ORDER_NBR%/", $shipment->getOrder()->getIncrementId(), $subject);
            }
        }

        return $subject;
    }

    /**
     * email admin only
     * @param null $storeId
     * @return int
     */
    public function isShowAllStores($storeId = null)
    {
        $retVal = Mage::getStoreConfigFlag('shippedfrom/settings_form/show_all_stores', $storeId);
        return ($retVal ? 1 : 0);
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function addShippedFromToNotes($storeId = null)
    {
        $retVal = Mage::getStoreConfigFlag('shippedfrom/settings_email_attachment/add_shipped_from_to_notes', $storeId);
        return ($retVal ? 1 : 0);
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function addShippedByToNotes($storeId = null)
    {
        $retVal = Mage::getStoreConfigFlag('shippedfrom/settings_email_attachment/add_shipped_by_to_notes', $storeId);
        return ($retVal ? 1 : 0);
    }

    /**
     * @param null $storeId
     * @return int
     */
    public function isCheckSendStoreEmail($storeId = null)
    {
        $retVal = Mage::getStoreConfigFlag('shippedfrom/settings_form/check_send_store_email', $storeId);
        return ($retVal ? 1 : 0);
    }

    /**
     * Log data
     * @param string|object|array data to log
     * @param int $level
     */
    public function log($data, $level = Zend_Log::DEBUG)
    {
        Mage::log($data, $level, $this->_logFileName);
    }

    /**
     * getDefaultShippedFrom
     */
    public function getDefaultShippedFrom()
    {
        $shippedFrom = Mage::getStoreConfig('shippedfrom/default_values/shipped_from');
        if (empty($shippedFrom)) {
            $shippedFrom = "H00";
        }

        $stores = Mage::getModel('ustorelocator/location')->getCollection();
        $storeId = 0;
        foreach ($stores as $store) {
            if (preg_match(sprintf("/%s/i", $shippedFrom), $store->getStoreCode())) {
                $storeId = $store->getId();
            }
        }

        return $storeId;
    }

    /**
     * getDefaultShippedFrom
     */
    public function getDefaultShippedBy()
    {
        $userValue = "Unknown";
        $shippedByUserValue = Mage::getStoreConfig('shippedfrom/default_values/shipped_by');

        $aUser = "";
        // user = Logged In User
        if (empty($shippedByUserValue) || preg_match("/user/", $shippedByUserValue)) {
            $user = Mage::getSingleton('admin/session');
            $aUser = $user->getUser();
        }

        if (preg_match("/user_id/", $this->getShippedByUserField())) {
            if (!$aUser) {
                $aUser = Mage::getModel('admin/user')->load($shippedByUserValue)->getData();
            }

            $userValue = $aUser->getId();
        }

        if (preg_match("/username/", $this->getShippedByUserField())) {
            if (!$aUser) {
                $aUser = Mage::getModel('admin/user')
                    ->getCollection()
                    ->addFieldToFilter('username', $shippedByUserValue)
                    ->getData();
            }

            $userValue = $aUser->getUsername();
        }

        if (preg_match("/fullname/", $this->getShippedByUserField())) {
            if (!$aUser) {
                $users = Mage::getModel('admin/user')->getCollection();
                foreach ($users as $u) {
                    $fullname = sprintf("%s %s", $u->getFirstname(), $u->getlastname());
                    if (preg_match(sprintf("/%s/", $shippedByUserValue), $fullname)) {
                        $aUser = $u;
                    }
                }
            }

            $userValue = $aUser->getName();
        }

        return $userValue;
    }

    /**
     * getShippedByUserField()
     *
     * determines which user field is used for shipped by
     * WARNING: if this gets changed then ALL values should be changed
     *
     * values: user_id, username, fullname
     * @return shippedByUserField
     * @throws Exception
     */
    public function getShippedByUserField()
    {
        $shippedByUserField = Mage::getStoreConfig('shippedfrom/default_values/shipped_by_user_field');

        if (empty($shippedByUserField)) {
            $shippedByUserField = "user_id";
        }

        if (!in_array($shippedByUserField, self::$_shippedByUserFields)) {
            Mage::throwException(
                Mage::helper('shippedfrom')->__("unknown shipped by user field '%s'!", $shippedByUserField)
            );
        }

        return $shippedByUserField;
    }

    /**
     * @param $date
     * @return bool
     */
    public function isAllowedToEdit($date)
    {
        // Shipment date
        $shipmentDate = Mage::app()->getLocale()->date($date, Varien_Date::DATETIME_INTERNAL_FORMAT);
        // Current date
        $currentDate = Mage::app()->getLocale()->date(null, Varien_Date::DATETIME_INTERNAL_FORMAT);
        // Comparison and check if allowed
        if (($currentDate->compareDate($shipmentDate) == 0)
            && Mage::getSingleton('admin/session')->isAllowed($this->_editPermissionsPath)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Mage_Core_Helper_Abstract
     */
    protected function _auspostHlp() //: Mage_Core_Helper_Abstract
    {
        return Mage::helper('shippedfrom/auspost');
    }

    /**
     * @param FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry
     */
    public function notifyStore(FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry)
    {
        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $this->_auspostHlp()->getShipmentFromQueueEntry($queueEntry);
        $storeId = $queueEntry->getShippedFrom();
        $store = $this->getStore($storeId);
        $email = $this->getStoreEmail($store);
        $this->sendPackingSlipToStore($shipment, $email, $queueEntry->getApLabelUri());

        $queueEntry->setData('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_SENT)
            ->save();
    }

    /**
     * containsWord
     * @param $str
     * @param $word
     * @return bool
     */
    public function containsWord($str, $word)
    {
        $this->log(sprintf("%s->search for '%s' in '%s'", __METHOD__, $word, $str));
        $found = !!preg_match('#\\b' . preg_quote($word, '#') . '\\b#i', $str);
        if ($found) {
            $this->log(sprintf("%s->FOUND!", __METHOD__));
        }

        return $found;
    }

}
