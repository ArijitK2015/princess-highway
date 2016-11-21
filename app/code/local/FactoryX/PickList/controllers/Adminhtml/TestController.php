<?php
/**

Test the file writing and configuration integrity

Mage_Adminhtml_Controller_Action
*/

class FactoryX_PickList_Adminhtml_TestController extends Mage_Adminhtml_Controller_Action {

    private $_helper;

    protected $KNOWN_ERRORS = array(
        "/name/" => "label"
    );

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config/picklist');
    }

    /*
    main test
    */
    public function indexAction() {
        $msg = "";

        $this->_helper = Mage::helper('picklist');

        $currModule = Mage::app()->getRequest()->getModuleName();
        $this->_helper->log(sprintf("running %s test...", $currModule));

        $success = true;

        $websiteModel = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
        //$this->TEST_EMAIL = Mage::getStoreConfig('trans_email/ident_general/email', $websiteModel->getId());

        /*
        if (!$this->_helper->isEnabled()) {
            $msg = $msg . "<br/>". $this->_helper->__("Extension disabled, cannot run test.");
            $this->_helper->log($this->_helper->__("Extension disabled, cannot run test."));
            Mage::getSingleton('adminhtml/session')->addError($msg);
            $this->_redirectReferer();
            return;
        }
        */

        $output = $this->_helper->getOutput($websiteModel->getId());
        $this->_helper->log(sprintf("output=%s", get_class($output)) );

        //if (is_subclass_of($output, 'FactoryX_PickList_Model_Output_Ftp')) {
        if (get_class($output) == 'FactoryX_PickList_Model_Output_Ftp') {
            $this->testConnection();
        }
        if (get_class($output) == 'FactoryX_PickList_Model_Output_Download') {
            $msg .= sprintf("test file writes to %s", $output->getOutputDir());
            $url = $this->testMedia($output->getOutputDir());
            $msg .= "<br/>ok";
            $msg .= "<br/>" . sprintf("<a href='%s' target='_new'>%s</a>", $url, $url);
            // create .htaccess file
        }
        else {
            $option = Mage::getStoreConfig('picklist/options/file_output');
            //$this->_helper->log(sprintf("skipping test for option '%s' [%s].", $option, get_class($output)));
            $this->_helper->log(sprintf("skipping test for option '%s'", $option));
        }

        /*
        try {

            $transport = new Varien_Object(); // for observers to set if required
            Mage::dispatchEvent('aschroder_smtppro_before_send', array(
                'mail' => $mail,
                'email' => $this,
                'transport' => $transport
            ));

            $emailTransport = $transport->getTransport();

            if (!empty($emailTransport)) {

                $mail->send($emailTransport);

                Mage::dispatchEvent('aschroder_smtppro_after_send', array('to' => $to,
                    'template' => "Email Self Test",
                    'subject' => $sub,
                    'html' => false,
                    'email_body' => $body));

                $msg = $msg . "<br/>". $this->_helper->__("Test email was sent successfully.");
                $this->_helper->log($this->_helper->__("Test email was sent successfully"));

            } else {
                $success = false;
                $this->_helper->log($this->_helper->__("Failed to find transport for test."));
                $msg = $msg . "<br/>". $this->_helper->__("Failed to find transport for test.");
            }

        } catch (Exception $e) {
            $success = false;
            $msg = $msg . $this->_helper->__("Unable to send test email.");
            if ($help = $this->knowError($e->getMessage())) {
                $msg = $msg . "<br/>" . $help;
            } else {
                $msg = $msg . "<br/>". $this->_helper->__("Exception message was: %s", $e->getMessage());
                $msg = $msg . "<br/>". $this->_helper->__("Please check the user guide for frequent error messages and their solutions.");
            }
            $this->_helper->log($this->_helper->__("Test email was not sent successfully: %s", $e->getMessage()));
            $this->_helper->log($this->_helper->__("See exception log for more details."));
            Mage::logException($e);
        }

        $this->_helper->log($this->_helper->__("Checking that a template exists for the default locale and that email communications are enabled..."));

        try {

            $mailTemplate = Mage::getModel('core/email_template');
            $testTemplateId = Mage::getStoreConfig(self::XML_PATH_TEST_TEMPLATE);

            if (is_numeric($testTemplateId)) {
                $mailTemplate->load($testTemplateId);
            } else {
                $localeCode = Mage::getStoreConfig('general/locale/code');
                $mailTemplate->loadDefault($testTemplateId, $localeCode);
            }

            $mailTemplate->setSenderName("Test Name");
            $mailTemplate->setSenderEmail("test@email.com");

            if ($mailTemplate->isValidForSend()) {
                $msg = $msg . "<br/>". $this->_helper->__("Default templates exist.");
                $msg = $msg . "<br/>". $this->_helper->__("Email communications are enabled.");
                $this->_helper->log($this->_helper->__("Default templates exist and email communications are enabled."));
            } else {
                $success = false;
                $msg = $msg . "<br/>". $this->_helper->__("Could not find default template, or template not valid, or email communications disabled in Advanced > System settings.");
                $msg = $msg . "<br/>". $this->_helper->__("Please check that you have templates in place for your emails. These are in app/locale, or custom defined in System > Transaction Emails. Also check Advanced > System settings to ensure email communications are enabled.");
                $this->_helper->log($this->_helper->__("Could not find default template, or template not valid, or email communications disabled in Advanced > System settings."));
            }

        } catch (Exception $e) {

            $success = false;
            $msg = $msg . "<br/>". $this->_helper->__("Could not test default template validity.");
            $msg = $msg . "<br/>". $this->_helper->__("Exception message was: %s", $e->getMessage() . "...");
            $msg = $msg . "<br/>". $this->_helper->__("Please check that you have templates in place for your emails. These are in app/locale, or custom defined in System > Transaction Emails.");
            $this->_helper->log($this->_helper->__("Could not test default template validity: %s", $e->getMessage()));
        }

        $this->_helper->log($this->_helper->__("Checking that tables are created..."));

        try {

            $logName = Mage::getSingleton('core/resource')->getTableName("smtppro_email_log");
            $logExists = Mage::getSingleton('core/resource')->getConnection('core_read')->isTableExists($logName);

            if (!$logExists) {
                $success = false;
                $msg = $msg . "<br/>". $this->_helper->__("Could not find required database tables.");
                $msg = $msg . "<br/>". $this->_helper->__("Please try to manually re-run the table creation script. For assistance please contact us.");
                $this->_helper->log($this->_helper->__("Could not find required database tables."));
            } else {
                $msg = $msg . "<br/>". $this->_helper->__("Required database tables exist.");
                $this->_helper->log($this->_helper->__("Required database tables exist."));
            }

        } catch (Exception $e) {

            $success = false;
            $msg = $msg . "<br/>". $this->_helper->__("Could not find required database tables.");
            $msg = $msg . "<br/>". $this->_helper->__("Exception message was: %s", $e->getMessage());
            $msg = $msg . "<br/>". $this->_helper->__("Please try to manually re-run the table creation script. For assistance please contact us.");
            $this->_helper->log($this->_helper->__("Could not find required database tables: %s", $e->getMessage()));
        }

        */

        $this->_helper->log($this->_helper->__("Complete"));

        if ($success) {
            $msg = $msg . "<br/>". $this->_helper->__("testing complete!");
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        }
        else {
            $msg = $msg . "<br/>". $this->_helper->__("testing failed! please review the reported problems.");
            Mage::getSingleton('adminhtml/session')->addError($msg);
        }

        $this->_redirectReferer();
    }

    /**
     * @param $path
     * @throws Exception
     * @return string
     */
    private function testMedia($path) {

        $dir = hash("md5", time());
        $path = sprintf("%s/%s", $path, $dir);

        $this->_helper->log(sprintf("mkdir '%s'", $path));
        if (!mkdir($path)) {
            throw new Exception(sprintf("cannot create directory '%s': %s", $path, error_get_last()));
        }
        $this->_helper->log(sprintf("ok"));

        $fileName = date('Ymd-his');
        $file = sprintf("%s/%s.txt", $path, $fileName);
        if (!touch($file)) {
            throw new Exception(sprintf("cannot create file '%s': %s", $file, error_get_last()));
        }

        $url = sprintf("%s%s/%s", Mage::getBaseUrl('media'), FactoryX_PickList_Model_Output_Download::DIR, $dir);
               

/*
        $this->_helper->log(sprintf("rmdir '%s'", $path));
        if (!rmdir($path)) {
            throw new Exception(sprintf("cannot delete directory '%s': %s", $path, error_get_last()));
        }
        $this->_helper->log(sprintf("ok"));
*/        
        return $url;
    }

    /**
     * @param string $host
     * @param int $port
     * @return bool
     */
    private function testConnection($host = 'localhost', $port = 25) {

        $success = false;
        $this->_helper->log(sprintf("%s->%s:%s", __METHOD__, $host, $port));

        $fp = false;
        try {
            $fp = fsockopen($transport->getHost(), $transport->getPort(), $errno, $errstr, $timeout = 5);
        }
        catch ( Exception $e) {
            // An error will be reported below.
        }

        $this->_helper->log($this->_helper->__("complete"));

        if (!$fp) {
            $this->_helper->log($this->_helper->__("Failed to connect to SMTP server. Reason: ") . $errstr . "(" . $errno . ")");
            $msg = $msg . "<br/>". $this->_helper->__("Failed to connect to SMTP server. Reason: ") . $errstr . "(" . $errno . ")";
            $msg = $msg . "<br/>". $this->_helper->__("This extension requires an outbound SMTP connection on port: ") . $transport->getPort();
        }
        else {
            $success = true;
            $this->_helper->log($this->_helper->__("Connection to Host SMTP server successful"));
            $msg = $msg . "<br/>". $this->_helper->__("Connection to Host SMTP server successful.");
            fclose($fp);
        }
        return $success;
    }

    /**
     * @param $message
     * @return bool
     */
    private function knowError($message) {

        foreach($this->KNOWN_ERRORS as $known => $help) {
            if (preg_match($known, $message)) {
                return $help;
            }
        }
        return false;
    }

    /**
     * @param $expected
     * @param $actual
     * @return bool
     */
    private function checkRewrite($expected, $actual) {

        return $expected != $actual &&
            !is_subclass_of($actual, $expected);
    }
}

