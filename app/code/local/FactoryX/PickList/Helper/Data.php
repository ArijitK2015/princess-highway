<?php

/**
 * Class FactoryX_PickList_Helper_Data
 */
class FactoryX_PickList_Helper_Data extends Mage_Core_Helper_Data {

    protected $_weekdays;
    protected $_locale;
    protected $orderStatuses;

    const LOG_FILE = 'factoryx_picklist.log';

    /**
     * get available order source
     * return array
     */
    public function getOrderSourceOptions() {
        return array(
            array( 'value'=>'all', 'label' => $this->__('All'), 'default' => 1 ),
            array( 'value'=>'magento', 'label' => $this->__('Magento') ),
            array( 'value'=>'ebay', 'label' => $this->__('eBay') )
        );
    }

    /**
     * select label from sales_order_status where status = 'processing_stage2';
     * @param $status
     * @return string
     */
       public function getStatusLabel($status) {
           //$this->log(sprintf("%s->statusLabel: %s", __METHOD__, $status));

        if (preg_match("/1.4.2.0/", Mage::getVersion())) {
            //$statusLabel = $this->status;
            $statusLabel = $status;
        }
        else {
            /*
            $orderStatus = Mage::getModel('sales/order_status')->getCollection()->addFieldToFilter('status', array('eq'=>$status));
            $statusLabel = "status";
            if (count($orderStatus) >= 1) {
                $statusLabel = $orderStatus->getFirstItem()->getLabel();
            }
            */
            if (!$this->orderStatuses) {
                $this->orderStatuses = Mage::getModel('sales/order_status')->getCollection();
            }
            $statusLabel = "status";
            foreach($this->orderStatuses as $orderStatus) {
                //$this->log(sprintf("%s->statusLabel: %s == %s", __METHOD__, $status, $orderStatus->getStatus()));
                if ($orderStatus->getStatus() == $status) {
                    $statusLabel = $orderStatus->getLabel();
                }
            }
        }
        //$this->log(sprintf("%s->statusLabel=%s", __METHOD__, $statusLabel));
           return $statusLabel;
    }


    /**
     *
     * return array
     */
    public function getOptions() {
        return array(
            array( 'value'=>'today', 'label' => $this->__('Today'), 'default' => 1 ),
            array( 'value'=>'yesterday', 'label' => $this->__('Yesterday') ),
            array( 'value'=>'last_7_days', 'label' => $this->__('Last 7 days') ),
            array( 'value'=>'last_week', 'label' => $this->_getLastWeekLabel()  ),
            array( 'value'=>'last_business_week', 'label' => $this->__('Last business week (Mon - Fri)') ),
            array( 'value'=>'this_month', 'label' => $this->__('This month') ),
            array( 'value'=>'last_month', 'label' => $this->__('Last month') ),
            array( 'value'=>'custom', 'label' => $this->__('Custom date range') ),
        );
    }

    /**
     *
     */
    public function getRangeValues() {
        $ctz = date_default_timezone_get();
        //$this->log("getRangeValues()=" . $ctz);
        $mtz =  Mage::app()->getStore()->getConfig('general/locale/timezone');
        //$this->log("getRangeValues()=" . $mtz);
        @date_default_timezone_set( $mtz );

        $firstDay = $this->_getWeekDayName( $this->_getFirstWeekDay() );
        $lastDay = $this->_getWeekDayName( $this->_getLastWeekDay() );

        $format = $this->getDateFormat();
        $res = array(
            array(
               'key'  => 'today',
               'from' => strftime( $format ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'yesterday',
               'from' => strftime( $format, strtotime('yesterday') ),
               'to'   => strftime( $format, strtotime('yesterday') )
            ),
            array(
               'key'  => 'last_7_days',
               'from' => strftime( $format, strtotime('- 7 days') ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'last_week',
               'from' => strftime( $format, strtotime( $firstDay ) ) === strftime( $format, strtotime( 'today' ) ) ? strftime( $format, strtotime( 'last '.$firstDay ) ) : strftime( $format, strtotime('last week '.$firstDay.' - 7 days') ),
               'to'   => strftime( $format, strtotime('last week '.$lastDay ) )
            ),
            array(
               'key'  => 'last_business_week',
               'from' => strftime( $format, strtotime( 'monday' ) ) === strftime( $format, strtotime( 'today' ) ) ? strftime( $format, strtotime( 'last monday' ) ) : strftime( $format, strtotime('last week mon - 7 days') ),
               'to'   => strftime( $format, strtotime('last week fri') )
            ),
            array(
               'key'  => 'this_month',
               'from' => strftime( $format, strtotime( date('m/01/y') ) ),
               'to'   => strftime( $format )
            ),
            array(
               'key'  => 'last_month',
               'from' => strftime( $format, strtotime( date('m/01/y', strtotime( 'last month' ) ) ) ),
               'to'   => strftime( $format, strtotime( date('m/01/y').' - 1 day' ) )
            ),
        );
        @date_default_timezone_set( $ctz );
        return $res;
    }

    /**
     * @return string
     */
    protected function _getLastWeekLabel() {
        $firstDayNum = Mage::getStoreConfig('general/locale/firstday') ? Mage::getStoreConfig('general/locale/firstday') : 0;
        $lastDayNum = $firstDayNum + 6;
        $lastDayNum = $lastDayNum > 6 ? $lastDayNum - 7 : $lastDayNum;
        return $this->__('Last week').' ('.substr($this->getWeekday($firstDayNum), 0,3).' - '.substr($this->getWeekday($lastDayNum),0,3 ).')';
    }

    /**
     * @return int|mixed
     */
    protected function _getFirstWeekDay()
    {
        return Mage::getStoreConfig('general/locale/firstday')?Mage::getStoreConfig('general/locale/firstday'):0;
    }

    /**
     * @return int|mixed
     */
    protected function _getLastWeekDay()
    {
        $firstDayNum = Mage::getStoreConfig('general/locale/firstday')?Mage::getStoreConfig('general/locale/firstday'):0;
        $lastDayNum = $firstDayNum + 6;
        return $lastDayNum > 6 ? $lastDayNum - 7 : $lastDayNum;
    }

    /**
     * @param $index
     * @return null
     */
    protected function _getWeekDayName($index)
    {
        $days = array(
            0 => 'sun',
            1 => 'mon',
            2 => 'tue',
            3 => 'wed',
            4 => 'thu',
            5 => 'fri',
            6 => 'sat',
        );
        return isset($days[$index])? $days[$index] : null;
    }

    /**
     * @param $weekday
     * @return mixed
     */
    public function getWeekday($weekday) {
        //$this->log("getWeekday: " . $weekday);
        if (!$this->_weekdays) {
            $this->_weekdays = Mage::app()->getLocale()->getOptionWeekdays();
        }
        foreach ($this->_weekdays as $day) {
            if ($day['value'] == $weekday) {
                return $day['label'];
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getDateFormat() {
        //$date_format = $this->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        // Note. %e places a space in the day field which confuses the calendar widget
        $date_format = "%d/%m/%y";
        //$this->log("date_format=" . $date_format);
        return $date_format;
    }

    /**
     * @return Mage_Core_Model_Locale
     */
    public function getLocale() {
        if ( !$this->_locale ) {
            $this->_locale = Mage::app()->getLocale();
        }
        return $this->_locale;
    }

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data) {
        if ($this->isDebugLoggingEnabled()) {
            Mage::log($data, null, self::LOG_FILE);
        }
    }

    /**
    */
    public function getEmailDebugInfo() {
        if (!$this->isDebugLoggingEnabled()) {
            return "";
        }
        $userUsername = get_current_user();        
        $user = Mage::getSingleton('admin/session');
        if ($user) {
            $userUsername = $user->getUser()->getUsername();
        }
        //$host = Mage::app()->getStore($storeId = null)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $host = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);

        $info = "\n";
        $info .= sprintf("--- debug info ---\n");
        $info .= sprintf("date: %s\n", date('Y-m-d H:i:s'));
        $info .= sprintf("host: %s\n", $host);
        $info .= sprintf("user: %s\n", $userUsername);
        $info .= sprintf("mail: %s\n", get_class(Mage::getModel('core/email_template')) );
        return $info;
    }

    /**
    minify enabled
     * @param null $storeId
     * @return bool
     */
    public function isMinifyUrlsEnabled($storeId = null) {
        return Mage::getStoreConfigFlag('picklist/minify/enabled', $storeId);
    }

    /**
    minify

    @param string $url the url to minify
     * @param $url
     * @param bool $extended
     * @return string|void
     */
    public function minifyUrl($url, $extended = false) {
        $newUrl = "";
        try {
            $path = Mage::getModuleDir('', 'FactoryX_PickList');
            include_once $path . '/lib/Googl.class.php';
            $googl = new Googl(
                Mage::getStoreConfig('picklist/minify/google_api_key'),
                Mage::getStoreConfig('picklist/minify/http_proxy')
            );
            //$this->log(sprintf("%s->url=%s", __METHOD__, $url));
            $newUrl = $googl->shorten($url, $extended);
            //$this->log(sprintf("%s->newUrl=%s", __METHOD__, $newUrl));
        }
        catch(Exception $ex) {
            $this->log(sprintf("%s->error: %s", __METHOD__, $ex->getMessage()) );
            $newUrl = $url;
        }
        return $newUrl;
    }

    /**
    log_debug
     * @param null $storeId
     * @return bool
     */
    public function isDebugLoggingEnabled($storeId = null) {
        return Mage::getStoreConfigFlag('picklist/debug/log_debug', $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getDownloadOutputPath($storeId = null) {
        // picklist/default_output/download_path
        return Mage::getStoreConfig('picklist/default_output/download_path', $storeId);
    }

    /**
     * @param null $storeId
     * @return false|\Mage_Core_Model_Abstract
     */
    public function getOutput($storeId = null) {
        $option = Mage::getStoreConfig('picklist/default_output/file_output', $storeId);
        // e.g. picklist/output_download
        $model = sprintf("picklist/output_%s", $option);
        $this->log(sprintf("load model '%s'", $model));
        //$obj = Mage::getModel($model)->getOutput();
        $obj = Mage::getModel($model);
        $this->log(sprintf("model = '%s'", get_class($obj)));
        return $obj;
    }

    /**
    get front end store id
    */
    public function getFrontEndStoreId() {
        $storeId = 0;
        $websites = Mage::app()->getWebsites(true);
        if (count($websites) > 1) {
            $storeId = $websites[1]->getDefaultStore()->getCode();
        }
        return $storeId;
    }

    /**
     * sendEmail
     *
     * send an email
     *
     * @param string $recipientName
     * @param string $recipientEmail
     * @param string $subject
     * @param string $body
     * @param string $type text | html
     * @return void
     */
    public function sendEmail($recipientName, $recipientEmail, $subject, $body, $type = 'text') {

        //unset($subject);
        if (empty($subject)) {
            $subject = sprintf("%s - Pick List",
                Mage::app()->getStore()->getName()
            );
        }

        $this->log(sprintf("%s->%s: %s", __METHOD__, $recipientEmail, $subject));
        try {
            /**
            // simple method
            $mail = Mage::getModel('core/email');
            $mail->setToName($recipientName);
            $mail->setToEmail($recipientEmail);
            $mail->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'));
            $mail->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
            $mail->setType($type); // html | text
            $mail->setSubject($subject);
            $mail->setBody($body);
            $mail->send();
            */

            $templateVars = array(
                'subject'   => $subject,
                'body'      => $body,
                'footer'    => $this->getEmailDebugInfo()
            );

            // default sender
            $sender = array(
                'name'  => Mage::getStoreConfig('trans_email/ident_general/name'),
                'email' => Mage::getStoreConfig('trans_email/ident_general/email')
            );
            if (Mage::getStoreConfig('picklist/email_settings/sender_name')) {
                $sender['name'] = Mage::getStoreConfig('picklist/email_settings/sender_name');
            }
            if (Mage::getStoreConfig('picklist/email_settings/sender_email')) {
                $sender['email'] = Mage::getStoreConfig('picklist/email_settings/sender_email');
            }
            $this->log(sprintf("%s->sender=%s", __METHOD__, print_r($sender, true)) );

            $emailTemplate = $this->getEmailTemplate();

            //$emailTemplate->setTemplateSubject($subject);
            //$this->log(sprintf("%s->templateVars: %s", __METHOD__, print_r($templateVars, true)));
            //$emailBody = $emailTemplate->getProcessedTemplate($templateVars);
            //$this->log(sprintf("%s->body: %s", __METHOD__, $emailBody));

            $emailTemplate->setSenderName($sender['name']);
            $emailTemplate->setSenderEmail($sender['email']);
            $emailTemplate->send($recipientEmail, $sender['name'], $templateVars);
        }
        catch(Exception $ex) {
            $this->log(sprintf("%s->error: %s", __METHOD__, $ex->getMessage()));
        }
    }

    /**
    send email
     * @param $files
     * @param $vars
     * @param null $additionalEmails
     * @param string $emailPostfix
     */
    public function sendEmails($files, $vars, $additionalEmails = null, $emailPostfix = "factoryx.com.au") {

        $this->log(sprintf("%s->vars=%s", __METHOD__, print_r($vars, true)));

        $storeId = $this->getFrontEndStoreId();
        foreach ( $files as $file) {
            // link via controller
            $getDoc = Mage::app()->getStore($storeId)->getUrl('picklist/index/get', array(
                    '_nosid'    => true,
                    '_secure'   => false,
                    '_query'    => array(
                        'file_path' => $file['file'],
                        'store'     => $file['store']
                    )
                )
            );
            if ($this->isMinifyUrlsEnabled()) {
                $getDoc = $this->minifyUrl($getDoc);
            }

            // email format
            if (preg_match("/html/i", $this->getEmailFormat()) ) {
                $link = sprintf("<a href='%s' target='_blank' rel='noopener noreferrer'>%s</a><br/>", $getDoc, $file['name']);
                $body = sprintf("<html><body>%s</body></html>", $link);
            }
            else {
                $body = $getDoc;
            }


            if (Mage::getStoreConfig('picklist/email_settings/email_postfix')) {
                $emailPostfix = Mage::getStoreConfig('picklist/email_settings/email_postfix');
            }
            $recipientEmail = sprintf("%s@%s", $file['store'], $emailPostfix);
            $recipientName = sprintf("%s", $file['store']); // $recipientEmail;
            $this->log(sprintf("%s->recipient: %s", __METHOD__, $recipientEmail));

            $subject = $this->getSubject($vars['from'], $vars['to'], $file['store']);

            if (Mage::getStoreConfigFlag('picklist/email_settings/send_store_email') ) {
                $this->sendEmail($recipientName, $recipientEmail, $subject, $body);
                $this->log(sprintf("%s->email sent", __METHOD__));
            }
            else {
                $this->log(sprintf("%s->email NOT sent", __METHOD__));
            }
            
            // add to additionalEmails via config
            $extraAdditionalEmails = Mage::getStoreConfig('picklist/email_settings/additional_emails');
            if (!empty($extraAdditionalEmails) ) {
                if (empty($additionalEmails) ) {
                    $additionalEmails = $extraAdditionalEmails;
                }
                else {
                    $additionalEmails = sprintf("%s,%s", $additionalEmails, $extraAdditionalEmails);
                }
            }
            if (!empty($additionalEmails)) {
                $addr = explode(",", $additionalEmails);
                $sent = array();
                foreach($addr as $emailAddress) {
                    if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                        // avoid dups
                        if (!in_array($emailAddress, $sent)) {
                            $this->sendEmail($emailAddress, $emailAddress, $subject, $body);
                            $sent[] = $emailAddress;
                        }
                    }
                    else {
                        $this->log(sprintf("%s->additional email '%s' invalid!", __METHOD__, $emailAddress));
                    }
                }
            }
        }
    }


    /**
    long2ip(Mage::helper('core/http')->getRemoteAddr(true))
     * @param $sourceClient
     * @param $httpRequest
     * @param $responseCode
     * @return $this
     */
    public function logPickListRequest($sourceClient, $httpRequest, $responseCode) {

        $this->log(sprintf("%s->sourceClient=%s", __METHOD__, $sourceClient));

        $sourceIp = $this->getRealIpAddr();
        if (!empty($sourceIp)) {
            $sourceIp = is_long($sourceIp) ? long2ip($sourceIp) : $sourceIp;
        }
        else {
            $sourceIp = "localhost";
        }

        //if ($this->isLogRequestEnabled()) {
        $log = Mage::getModel('picklist/picklist_log_request')
            ->setCreatedAt(Mage::getSingleton('core/date')->gmtDate())
            ->setSourceClient($sourceClient)
            ->setSourceIp(is_long($sourceIp) ? long2ip($sourceIp) : $sourceIp)
            ->setHttpRequest($httpRequest)
            ->setResponseCode($responseCode)
            ->save();
        //}
        return $this;
    }

    /**
    $sourceIp
     * @param string $jobType
     * @param string $sourceType
     * @param $httpRequest
     * @param $outputType
     * @param $emailSend
     * @param $httpResponse
     * @return null
     */
    public function logPickListJob($jobType = 'create', $sourceType = 'form', $httpRequest, $outputType, $emailSend, $httpResponse) {
        $log = null;
        try {
            $this->log(sprintf("%s->httpResponse=%s", __METHOD__, $httpResponse));

            $sourceIp = $this->getRealIpAddr();
            if (!empty($sourceIp)) {
                $sourceIp = is_long($sourceIp) ? long2ip($sourceIp) : $sourceIp;
            }
            else {
                $sourceIp = "localhost";
            }

            $log = Mage::getModel('picklist/picklist_log_job')
                ->setCreatedAt(Mage::getSingleton('core/date')->gmtDate())
                ->setJobType($jobType)
                ->setSourceType($sourceType)
                ->setSourceIp($sourceIp)
                ->setHttpRequest($httpRequest)
                ->setOutputType($outputType)
                ->setEmailSent($emailSend)
                ->setHttpResponse($httpResponse);

            //$log->setData('http_response', $httpResponse);
            $log->save();
        }
        catch(Exception $ex) {
            $this->log(__METHOD__ . " Error: " . $ex->getMessage());
        }
        return $log;
    }

    /**

    */
    function getRealIpAddr() {
        //check ip from share internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
          $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //to check ip is pass from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        else {
            $ip = "localhost";
        }
        return $ip;
    }

    /**
    php 5.4 supports json_encode JSON_PRETTY_PRINT
     * @param $json
     * @return string
     */
    function pretty_json($json) {

        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '  ';
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i=0; $i<=$strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;

            // If this character is the end of an element,
            // output a new line and indent the next line.
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }

    /**
     * @param array $country
     * @return array
     */
    public function getRegions($country = array("AU")) {
        $regions = array();
        foreach($country as $countryCode) {
            $regionCollection = Mage::getModel('directory/country')->load($countryCode)->getRegionCollection();
            foreach($regionCollection as $region) {
                $regions[$region['region_id']] = $region['name'];
            }
        }
        return $regions;
    }

    /**
     * Function name
     *
     * what the function does
     *
     * @param long  (name) about this param
     * @return (type) (name)
     */
    //const DATE_FORMAT = "Y-m-d H:i:s";
    const DATE_FORMAT = "d/m/Y";

    /**
     * @param $dateFrom
     * @param $dateTo
     * @param null $id
     * @return string
     */
    public function getSubject($dateFrom, $dateTo, $id = null) {

        $storeName = Mage::getStoreConfig('general/store_information/name');
        $reportName = "Pick List";
        //$label = Mage::helper('picklist')->getStatusLabel($status);
        // get AM or PM
        $label = strtoupper(date('a', Mage::getModel('core/date')->timestamp(time())));
        if ($id) {
            $label = sprintf("%s %s", $id, $label);
        }

        $subjectTemplate = "%s :: %s - %s [%s]";

        // convert timestamps
        if (is_int($dateFrom)) {
            //$dateFrom = date(self::DATE_FORMAT, $dateFrom);
            $dateFrom = Mage::helper('core')->formatDate(new Zend_Date($dateFrom), 'medium', false);
        }
        else {
            //$dateFrom = date(self::DATE_FORMAT, strtotime($dateFrom));
        }
        if (is_int($dateTo)) {
            //$dateTo = date(self::DATE_FORMAT, $dateTo);
            $dateTo = Mage::helper('core')->formatDate(new Zend_Date($dateTo), 'medium', false);
        }
        else {
            //$dateTo = date(self::DATE_FORMAT, strtotime($dateTo));
        }

        $dateRange = $dateFrom;
        if ($dateFrom != $dateTo) {
            $dateRange = sprintf("%s - %s", $dateFrom, $dateTo);
        }
        $subject = sprintf($subjectTemplate,
            $storeName,
            $reportName,
            $label,
            $dateRange
        );
        $this->log(sprintf("%s->subject=%s", __METHOD__, $subject));
        return $subject;
    }

    /**
     * array_chunk_balance
     *
     * array_chunk will split an array, this will balance each group
     *
     * @param array $input
     * @param int $chunks
     * @param bool $preserve_keys
     * @return array array(0 => array(), 1 => array(2))
     */
    public function array_chunk_balance($input, $chunks, $preserve_keys = false) {
        $largest = ceil(count($input) / $chunks);
        $output = array();
        $offset = 0;
        for($i = 0; $i < $chunks; $i++) {
            $output[$i] = array_slice($input, $offset, $largest, $preserve_keys);
            $offset += $largest;
            if ((count($input) - $offset) > 0) {
                $largest = ceil((count($input) - $offset) / ($chunks - ($i + 1)));
            }
            else {
                break;
            }
        }
        return $output;
    }

    /**
     * getEmailFormat
     *
     * check the template set via config
     *
     * @return string text | html
     */
    private function getEmailFormat() {
        $emailTemplate = $this->getEmailTemplate();
        $format = "text";
        if ($emailTemplate) {
            if ($emailTemplate->isPlain()) {
                $format = "text";
            }
            else {
                $format = "html";
            }
        }
        return $format;
    }

    /**
     * getEmailTemplate
     *
     * @return Mage_Core_Model_Email_Template
     */
    private function getEmailTemplate() {
        $emailTemplate = null;
        $templateCode = Mage::getStoreConfig('picklist/email_settings/template');
        if (!empty($templateCode) && is_numeric($templateCode)) {
            $emailTemplate = Mage::getModel('core/email_template')->load($templateCode);
        }
        else {
            if (empty($templateCode) || preg_match("/picklist_email_settings_template/", $templateCode)) {
                $templateCode = 'picklist_basic_text_email_template';
            }
            $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateCode);
        }
        return $emailTemplate;
    }
}