<?php
/*
<crontab>
	<jobs>
		<factoryx_birthdaygift_send>
			<schedule><cron_expr>0 1 * * *</cron_expr></schedule>
			<run><model>birthdaygift/observer::sendBirthdayEmail</model></run>
		</factoryx_birthdaygift_send>
	</jobs>
</crontab>
*/

/**
 * Class FactoryX_BirthdayGift_Model_Observer
 */
class FactoryX_BirthdayGift_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_ultimateRecipients = array();
    protected $_emails = array();

    protected $_customerGroups;
    protected $_templateId;
    protected $_sender = array();
    protected $_limit = 0; // used for testing, set to 0 = infinite

    /**
     * @param $args
     */
    public function generateRecipientsArray($args) {

        // If the customer email is in the CampaignMonitor array
        if (in_array($args['row']['email'], $this->_emails)) {
            Mage::helper('birthdaygift')->log(sprintf("%s->in campaign monitor, skip: %s", __METHOD__, $args['row']['email']));
            return;
        }
        // Fill a new array with the customer details
        $new_subscriber = array();
        $new_subscriber["customer_id"] = $args['row']['entity_id'];
        $new_subscriber["fullname"] = $args['row']['name'];
        $new_subscriber["firstname"] = $args['row']['firstname'];
        $new_subscriber["email"] = $args['row']['email'];
        $new_subscriber["store_id"] = $args['row']['store_id'];
        $this->_ultimateRecipients[] = $new_subscriber;
    }

    /**
     *
     */
    protected function _sendRealEmails($dryrun = false) {
        // Create an array of variables to assign to template
        $emailTemplateVariables = array();

        // Today's year
        $year = Mage::getModel('core/date')->date('y');

        // Loop through the ultimate filtered array
        $cnt = 0;
        foreach ($this->_ultimateRecipients as $item) {

            if ($this->_limit != 0 && $cnt++ >= $this->_limit) {
                Mage::helper('birthdaygift')->log(sprintf("%s->limit %d reached!", __METHOD__, $this->_limit));
                break;
            }

            Mage::helper('birthdaygift')->log(sprintf("%s->%s <%s>", __METHOD__, $item["fullname"], $item["email"]));

            if (array_key_exists('customer_id', $item)) {
                // Get the last birthday coupon year
                $customer = Mage::getModel('customer/customer')->load($item["customer_id"]);

                // last_birthday_coupon is actually the year it was sent e.g. 15
                $lastBirthdayCoupon = $customer->getResource()->getAttribute('last_birthday_coupon')->getFrontend()->getValue($customer);

                // Skip if the customer already received a coupon this year
                if ($lastBirthdayCoupon == $year) {
                    Mage::helper('birthdaygift')->log(sprintf("%s->already sent, skip: %s", __METHOD__, $item["email"]));
                    continue;
                }

                // Array that contains the data which will be used inside the template
                $emailTemplateVariables['fullname'] = $item["fullname"];
                $emailTemplateVariables['firstname'] = $item["firstname"];

                if (!$dryrun && $item["email"] != "null@factoryx.com.au") {

                    Mage::app()->setCurrentStore($item["store_id"]);
                    $coupon = Mage::helper('birthdaygift')->createSalesRule($item["email"]);
                    $emailTemplateVariables['bday_code'] = $coupon->getCouponCode();
                    if ($coupon->getToDate()) {
                        $toDate = date(Mage::helper('birthdaygift')->getDateFormat(), strtotime($coupon->getToDate()));
                        $emailTemplateVariables['bday_code_to_date'] = $toDate;
                    }
                    Mage::helper('birthdaygift')->log(sprintf("%s->emailTemplateVariables: %s", __METHOD__, print_r($emailTemplateVariables, true)) );

                    // Send the email
                    $emailSent = Mage::getModel('core/email_template')
                        ->sendTransactional(
                            $this->_templateId,
                            $this->_sender,
                            $item["email"],
                            $item["fullname"],
                            $emailTemplateVariables,
                            null);

                    if (isset($emailSent) && $emailSent->getSentSuccess()) {
                        Mage::helper('birthdaygift')->log(sprintf("%s->email sent to %s", __METHOD__, $item["email"]) );
                        // We change the notification attribute
                        $customer->setData('last_birthday_coupon', $year)->getResource()->saveAttribute($customer, 'last_birthday_coupon');
                    }
                    else {
                        Mage::helper('birthdaygift')->log(sprintf("%s->email failed!", __METHOD__) );
                    }
                }
            } else {
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($item['email']);

                // last_birthday_coupon is actually the year it was sent e.g. 15
                $lastBirthdayCoupon = $subscriber->getLastBirthdayCoupon();

                // Skip if the subscriber already received a coupon this year
                if ($lastBirthdayCoupon == $year) {
                    Mage::helper('birthdaygift')->log(sprintf("%s->already sent, skip: %s", __METHOD__, $item["email"]));
                    continue;
                }

                // Array that contains the data which will be used inside the template
                $emailTemplateVariables['fullname'] = $item["fullname"];
                $emailTemplateVariables['firstname'] = $item["firstname"];

                if (!$dryrun && $item["email"] != "null@factoryx.com.au") {

                    Mage::app()->setCurrentStore($subscriber->getStoreId());
                    $coupon = Mage::helper('birthdaygift')->createSalesRule($item["email"]);
                    $emailTemplateVariables['bday_code'] = $coupon->getCouponCode();
                    if ($coupon->getToDate()) {
                        $toDate = date(Mage::helper('birthdaygift')->getDateFormat(), strtotime($coupon->getToDate()));
                        $emailTemplateVariables['bday_code_to_date'] = $toDate;
                    }
                    Mage::helper('birthdaygift')->log(sprintf("%s->emailTemplateVariables: %s", __METHOD__, print_r($emailTemplateVariables, true)) );

                    // Send the email
                    $emailSent = Mage::getModel('core/email_template')
                        ->sendTransactional(
                            $this->_templateId,
                            $this->_sender,
                            $item["email"],
                            $item["fullname"],
                            $emailTemplateVariables,
                            null);

                    if (isset($emailSent)/* && $emailSent->getSentSuccess()*/) {
                        Mage::helper('birthdaygift')->log(sprintf("%s->email sent to %s", __METHOD__, $item["email"]) );
                        // We change the notification attribute
                        $subscriber->setLastBirthdayCoupon($year)->save();
                    }
                    else {
                        Mage::helper('birthdaygift')->log(sprintf("%s->email failed!", __METHOD__) );
                    }
                }
            }
        }
    }

    /**
     * _sendTestEmail
     *
     * TODO: i'm not sure why this is a different function to _sendRealEmails ???
     *
     * @param $email
     */
    protected function _sendTestEmail($email) {

        Mage::helper('birthdaygift')->log(sprintf("%s->email: %s", __METHOD__, $email) );

        // Create an array of variables to assign to template
        $emailTemplateVariables = array();

        $today = $this->_getBirthdayDate();
        Mage::helper('birthdaygift')->log(sprintf("%s->today: %s", __METHOD__, $today) );

        // Get the model
        $customer = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect(array('firstname', 'lastname','dob','store_id'))
            ->addAttributeToFilter('dob', array('like' => $today))
            ->addAttributeToFilter('email', $email)
            ->getFirstItem();

        // Get date of birth
        $dob = $customer->getDob();

        if (isset($dob)) {
            $dob = '%' . substr($dob, 5); // remove the year
            // compare dates
            Mage::helper('birthdaygift')->log(sprintf("%s->test: %s == %s", __METHOD__, $dob, $today) );
            if ($dob === $today) {
                Mage::helper('birthdaygift')->log(sprintf("%s->Happy Birthday %s <%s>", __METHOD__, $customer->getName(), $customer->getEmail()) );

                // Array that contains the data which will be used inside the template
                $emailTemplateVariables['fullname'] = $customer->getName();
                $emailTemplateVariables['firstname'] = $customer->getFirstname();

                $coupon = Mage::helper('birthdaygift')->createSalesRule($customer->getEmail());
                $emailTemplateVariables['bday_code'] = $coupon->getCouponCode();
                if ($coupon->getToDate()) {
                    $toDate = date(Mage::helper('birthdaygift')->getDateFormat(), strtotime($coupon->getToDate()));
                    $emailTemplateVariables['bday_code_to_date'] = $toDate;
                }

                Mage::helper('birthdaygift')->log(sprintf("%s->emailTemplateVariables: %s", __METHOD__, print_r($emailTemplateVariables, true)) );

                // Send the email
                $email = $customer->getEmail();

                Mage::app()->setCurrentStore($customer->getStoreId());
                // Aschroder_SMTPPro_Model_Email_Template
                $emailSent = Mage::getModel('core/email_template')
                    ->sendTransactional(
                        $this->_templateId,
                        $this->_sender,
                        $email,
                        $customer->getName(),
                        $emailTemplateVariables,
                        null);
                if (isset($emailSent) && $emailSent->getSentSuccess()) {
                    Mage::helper('birthdaygift')->log(sprintf("%s->email sent to %s", __METHOD__, $email) );
                }
                else {
                    Mage::helper('birthdaygift')->log(sprintf("%s->email failed!", __METHOD__) );
                }
            }
        }
        else {
            Mage::helper('birthdaygift')->log(sprintf("%s->no bday set", __METHOD__) );
        }
    }

    /**
     *
     * @params string $email
     */
    public function sendBirthdayEmailTo($email)  {

    }

    /**
     * Send birthday coupon via    email to customers
     * @param string
     * @return $this
     */
    public function sendBirthdayEmail(Mage_Cron_Model_Schedule $schedule = null, $testEmail = null, $limit = null, $dryrun = false)  {

        //Mage::helper('birthdaygift')->log(sprintf("%s->Enabled: %s", __METHOD__, Mage::helper('birthdaygift')->isEnabled()));

        if (!Mage::helper('birthdaygift')->isEnabled()) {
            Mage::helper('birthdaygift')->log(sprintf("%s->DISABLED!", __METHOD__));
            return;
        }

        // get system config
        if (!$dryrun) {
            $dryrun = Mage::helper('birthdaygift')->getDryRun();
            Mage::helper('birthdaygift')->log(sprintf("%s->set dryrun: %d", __METHOD__, $dryrun));
        }

        /*
        I'm not sure whats going here but it gets set somewhere and the 1st 3 conditions pass
        */
        //Mage::helper('birthdaygift')->log(sprintf("%s->set test email: %d|%d|%d|%d|%d", __METHOD__, $testEmail, isset($testEmail), !empty($testEmail), strlen($testEmail), filter_var($testEmail, FILTER_VALIDATE_EMAIL)) );
        if (!$testEmail || strlen($testEmail) == 0 || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            Mage::helper('birthdaygift')->log(sprintf("%s->get test email...", __METHOD__));
            $testEmail = Mage::helper('birthdaygift')->getTestEmail();
            Mage::helper('birthdaygift')->log(sprintf("%s->set test email: %s", __METHOD__, $testEmail));
        }

        if ($limit) {
            $this->_limit = $limit;
            Mage::helper('birthdaygift')->log(sprintf("%s->set limit: %d", __METHOD__, $this->_limit));
        }

        Mage::helper('birthdaygift')->log(sprintf("%s->testEmail: %s", __METHOD__, $testEmail));
        Mage::helper('birthdaygift')->log(sprintf("%s->limit: %s", __METHOD__, $limit));
        Mage::helper('birthdaygift')->log(sprintf("%s->dryrun : %s", __METHOD__, $dryrun));

        try {
            // GET ALL THE CONFIGURATIONS AND SEE IF WE HAVE ENOUGH TO WORK WITH?

            // sender name & email
            $this->_sender['email'] = Mage::helper('birthdaygift')->getSenderEmail();
            $this->_sender['name'] = Mage::helper('birthdaygift')->getSenderName();

            if (empty($this->_sender['email'])) {
                Mage::helper('birthdaygift')->log(__METHOD__ . "Sender email not recognised.");
                return false;
            }
            if (empty($this->_sender['name'])) {
                Mage::helper('birthdaygift')->log(__METHOD__ . "Sender name not recognised.");
                return false;
            }

            // email template
            $this->_templateId = Mage::helper('birthdaygift')->getTemplate();
            if (!is_numeric($this->_templateId)) {
                Mage::helper('birthdaygift')->log(__METHOD__ . "Template ID is not numeric.");
                return false;
            }
            $this->_templateId = intval($this->_templateId);

            // customer groups
            $this->_customerGroups = Mage::helper('birthdaygift')->getCustomerGroups();
            if (empty($this->_customerGroups)) {
                Mage::helper('birthdaygift')->log(__METHOD__ . "Customer groups not recognised.");
                return false;
            }

            // Get emails from campaign monitor who have thier birthday today
            if (Mage::getStoreConfigFlag('bdayconfig/campaignmonitor/enabled')) {
                $this->_generateCampaignMonitorSubscribers();
            }

            // Get the customers collection
            $customer = Mage::getModel("customer/customer")->getCollection();

            if ($this->_customerGroups) {
                $customer->addFieldToFilter('group_id', array ('in' => $this->_customerGroups));
            }

            $today = $this->_getBirthdayDate();

            // If the email is set, it is for testing purpose
            if ($testEmail && $dryrun) {
                Mage::helper('birthdaygift')->log(sprintf("%s->_sendTestEmail: %s|%s", __METHOD__, $testEmail, $dryrun));
                $this->_sendTestEmail($testEmail);
            }
            else {
                // Add name in select
                $customer->addNameToSelect();
                // Add store in select
                $customer->addAttributeToSelect('store_id');
                // Filter the collection by date of birth
                $customer->addFieldToFilter('dob', array('like' => $today));

                // Call iterator walk method with collection query string and callback method as parameters
                // Has to be used to handle massive collection instead of foreach
                Mage::getSingleton('core/resource_iterator')->walk($customer->getSelect(), array(array($this, 'generateRecipientsArray')));

                $this->_sendRealEmails($dryrun);
            }
            return $this;
        }
        catch (Exception $e) {
            Mage::helper('birthdaygift')->log(__METHOD__ . " " . $e->getMessage());
        }
    }

    /**
     *
     */
    protected function _generateCampaignMonitorSubscribers()
    {

        // Get the CampaignMonitor Details
        $segID  = trim(Mage::getStoreConfig('bdayconfig/campaignmonitor/segmentID'));
        $hlp = Mage::helper('campaignmonitor/cm');

        try {
            $size = 50;
            $page = 0;
            $today = $this->_getBirthdayDate("%d-%d");
            do {
                $tmp = $hlp->getSubscribersFromSegment($segID, '', ++$page, $size);
                foreach ($tmp->response->Results as $subscriber) {
                    $not_today = true;
                    foreach ($subscriber->CustomFields as $field) {
                        if ($field->Key == "Date Of Birth") {
                            if (substr($field->Value, 5, 5) == $today) {
                                $not_today = false;
                                break;
                            }
                        }
                    }
                    if ($not_today) {
                        continue;
                    }
                    $new_subscriber = array();
                    $new_subscriber["fullname"] = $subscriber->Name;
                    $new_subscriber["firstname"] = current(explode(' ', $subscriber->Name));
                    $new_subscriber["email"] = $subscriber->EmailAddress;
                    $this->_ultimateRecipients[] = $new_subscriber;
                    $this->_emails[] = $subscriber->EmailAddress;
                }
            } while (sizeof($tmp->response->Results) == $size);
        }
        catch (Exception $e) {
            Mage::helper('birthdaygift')->log($e->getMessage());
        }
    }

    /**
     * use to overide birthday date
     *
     * $this->_getBirthdayDate("%d-%d");
     */
    private function _getBirthdayDate($format = "%%%02d-%02d 00:00:00")
    {
        // today %MM-DD 00:00:00
        $today = sprintf($format, Mage::getModel('core/date')->date('m'), Mage::getModel('core/date')->date('d'));
        //$today = "%06-05 00:00:00";
        return $today;
    }
}