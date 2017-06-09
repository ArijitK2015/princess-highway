<?php
error_reporting(E_ALL | E_STRICT);

require_once 'abstract.php';

class Mage_Shell_BirthdayGift extends Mage_Shell_Abstract {

    protected $_limit = 0;
    protected $_bdayRecipient;
    protected $_dryRun = false;
    protected $_verbose = false;
    protected $_test = false;
    protected $_force = false;

    public function dumpSettings() {
        $value = Mage::helper('birthdaygift')->getCouponValue();
        echo sprintf("CouponValue: %s\n", $value);
        
        $coupon = Mage::helper('birthdaygift')->_createSalesRule("test");
        echo sprintf("Coupon: %s\n", print_r($coupon->getData(), true));
    }

    /**
     * Run script
     */
    public function run() {

        $args = $this->arguments($GLOBALS['argv']);
        //$this->message(sprintf("args=%s", print_r($args, true)) );

        foreach($args["flags"] as $flag) {
            switch ($flag) {
                case 'd':
                    $this->_dryRun  = true;
                    break;
                case 'v':
                    $this->_verbose  = true;
                    break;
                case 't':
                    $this->_test  = true;
                    break;
                case 'f':
                    $this->_force  = true;
                    break;
                case 'h':
                    $this->_usageHelp();
                    break;
            }
        }

        if (array_key_exists("limit", $args["commands"])) {
            $this->_limit = $args["commands"]["limit"];
        }
        if (array_key_exists("email", $args["commands"])) {
            $this->_bdayRecipient = $args["commands"]["email"];
        }
        //$this->message(sprintf("commands=%s", print_r($args["commands"], true)));

        //$coupon = Mage::helper('birthdaygift')->_createSalesRule("test");
        //$this->message($this->message(sprintf("CouponCode: %s", $coupon->getCouponCode())));

        $this->message(sprintf("run: %s", __FILE__));
        $this->message(sprintf("test         : %s", ($this->_test ? "y" : "n")) );
        $this->message(sprintf("dryRun       : %s", ($this->_dryRun ? "y" : "n")) );
        $this->message(sprintf("verbose      : %s", ($this->_verbose ? "y" : "n")) );
        $this->message(sprintf("force        : %s", ($this->_force ? "y" : "n")) );
        $this->message(sprintf("limit        : %s", ($this->_limit ? $this->_limit : "none")) );
        $this->message(sprintf("email        : %s", ($this->_bdayRecipient ? $this->_bdayRecipient : "none")) );

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $storeId = Mage::app()->getStore()->getId();

        Varien_Profiler::enable();
        Mage::setIsDeveloperMode(true);
        umask(0);
        Mage::app('default');

        if ($this->_test) {
            $ob = Mage::getModel("birthdaygift/observer");
            $ob->sendBirthdayEmail($this->_bdayRecipient, $this->_limit, $this->_dryRun);
        }
        else {
            try {
                $this->sendBirthdayEmail($this->_bdayRecipient, $this->_dryRun, $ignoreSent = $this->_force);
            }
            catch(Exception $ex) {
                $this->message(sprintf("Error: %s", $ex->getMessage()) );
            }
        }
    }

    /**
     * dont use "echo - it will generate session_start(): Cannot send session cookie
     */
    function message($msg) {
        Mage::helper("birthdaygift")->log($msg);
        if ($this->_verbose) {
            echo sprintf("%s\n", $msg);
        }
    }

    /**
     *
     */
    function sendBirthdayEmail($email, $dryRun = false, $ignoreSent = false) {

        $emailTemplateVariables = array();        
        $templateId = Mage::helper('birthdaygift')->getTemplate();
        
        $send = array();
        $sender['email'] = Mage::helper('birthdaygift')->getSenderEmail();
        $sender['name'] = Mage::helper('birthdaygift')->getSenderName();

        // Get the last birthday coupon year
        $customer = Mage::getModel("customer/customer"); 

        //$customer->setWebsiteId(Mage::app()->getWebsite('admin')->getId()); 
        //$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $customer->setWebsiteId(1);
        
        $this->message(sprintf("%s->loadByEmail(%s)", get_class($customer), $email));
        $customer = $customer->loadByEmail($email);
        
        if (!$customer || !$customer->getEmail()) {
            $msg = sprintf("cannot find customer with email '%s'", $email);
            throw new Exception($msg);
        }

        // last_birthday_coupon is actually the year it was sent e.g. 15
        $lastBirthdayCoupon = $customer->getResource()->getAttribute('last_birthday_coupon')->getFrontend()->getValue($customer);
        $this->message(sprintf("lastBirthdayCoupon: %s", $lastBirthdayCoupon));

        // Skip if the customer already received a coupon this year
        if (
            $lastBirthdayCoupon == Mage::getModel('core/date')->date('y')
            &&
            !$ignoreSent
        ) {
            $msg = sprintf("birthday code already sent to '%s' this year '%s'", __METHOD__, $email, Mage::getModel('core/date')->date('y'));
            throw new Exception($msg);
        }

        // Array that contains the data which will be used inside the template
        $emailTemplateVariables['fullname'] = $customer->getFullname();
        $emailTemplateVariables['firstname'] = $customer->getFirstname();

        $coupon = Mage::helper('birthdaygift')->createSalesRule($email);
        $emailTemplateVariables['bday_code'] = $coupon->getCouponCode();

        if ($coupon->getToDate()) {
            $toDate = date(Mage::helper('birthdaygift')->getDateFormat(), strtotime($coupon->getToDate()));
            $emailTemplateVariables['bday_code_to_date'] = $toDate;
        }
        
        if (Mage::helper('birthdaygift')->getCouponValidity()) {
            $emailTemplateVariables['bday_code_valid_for'] = Mage::helper('birthdaygift')->getCouponValidity() . " days";
        }
        
        $this->message(sprintf("%s->emailTemplateVariables: %s", __METHOD__, print_r($emailTemplateVariables, true)) );

        // Send the email
        if (!$dryRun) {
            $emailSent = Mage::getModel('core/email_template')
                ->sendTransactional(
                    $templateId,
                    $sender,
                    $customer->getEmail(),
                    $customer->getFullname(),
                    $emailTemplateVariables,
                    null);

            if (isset($emailSent) && $emailSent->getSentSuccess()) {
                $this->message(sprintf("birthday email sent to '%s'", $email) );
                // We change the notification attribute
                $customer->setData('last_birthday_coupon', Mage::getModel('core/date')->date('y'))->getResource()->saveAttribute($customer, 'last_birthday_coupon');
                $customer->save();
            }
            else {
                $this->message(sprintf("failed to send email to '%s'!", $email) );
            }
        }
    }

    /**
    parse argv
    */
    function arguments($args) {
        array_shift($args);
        $args = join($args, ' ');
        // preg_match_all('/ (--\w+ (?:[= ] [^-]+ [^\s-] )? ) | (-\w+) | (\w+) /x', $args, $match );
        preg_match_all('/(--[a-z0-9]{1,}(?:[=][a-z0-9\.@]{1,})?)|(-[a-z0-9]{1,})|([a-z0-9]{1,})/x', $args, $match);
        $args = array_shift($match);

        $ret = array(
            'input'    => array(),
            'commands' => array(),
            'flags'    => array()
        );
        foreach($args as $arg) {
            // Is it a command? (prefixed with --)
            if ( substr( $arg, 0, 2 ) === '--' ) {
                $value = preg_split('/[= ]/', $arg, 2);
                $com   = substr(array_shift($value), 2);
                $value = join($value);
                $ret['commands'][$com] = !empty($value) ? $value : true;
                continue;
            }
            // Is it a flag? (prefixed with -)
            if ( substr( $arg, 0, 1 ) === '-' ) {
                $ret['flags'][] = substr( $arg, 1 );
                continue;
            }
            $ret['input'][] = $arg;
            continue;
        }
        return $ret;
    }

    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp() {
        return <<<USAGE
Usage:  php -f bday.php -- [options]

    --limit <send limit>
    --email <test email>
    -d dry run
    -t test the observer
    -v verbose
    -f force - skips the already sent validation
    -h help (this)

Examples:
# dev/test modes
php -f bday.php -- -v -d --email=ben.incani@factoryx.com.au -t
php -f bday.php -- -v -d --email=ben.incani@factoryx.com.au

# send someone a code via email
php -f bday.php -- --email=jessica.chewe@gmail.com
# send someone a code via email ignoring last_birthday_coupon check
php -f bday.php -- --email=patricia.shooter@gmail.com -f

USAGE;
    }
}

$shell = new Mage_Shell_BirthdayGift();
$shell->run();
//$shell->dumpSettings();
?>