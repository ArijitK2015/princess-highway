<?php
/**
 */

/**
 * Pick List observer model
 */
class FactoryX_PickList_Model_Observer {

    /**
     * daily pick list generation
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @param int $test
     * @return FactoryX_PickList_Model_Observer
     */
    public function dailyGeneratePickList(Mage_Cron_Model_Schedule $schedule = null, $test = 0) {

        try {

            if (!Mage::getStoreConfigFlag('picklist/cron_job_daily_generation/enabled')) {
                if ($test) {
                    Mage::helper('picklist')->log(sprintf("running test...") );
                }
                else {
                    Mage::helper('picklist')->log(sprintf("%s->cron disabled!", __METHOD__) );
                    return false;
                }
            }

            // TODO: make the time(s) flexible using a UI with some kind of range drop down

            /**
            determine the date range via the day of the week

            DoW (sun - sat)
            s   n/a
            m   [thu, fri, sat]
            t   [fri, sat, sun, mon]
            w   [sun, mon, tue]
            t   [tues, wed]
            f   [wed, thur]
            s   n/a
            */

            $theDay = date('D', Mage::getModel('core/date')->timestamp(time()));
            //Mage::helper('picklist')->log(sprintf("%s->theDay=%s[%s]", __METHOD__, $theDay, Mage::getModel('core/date')->timestamp(time()) ) );

            if (preg_match("/(sat|sun)/i", $theDay)) {
                Mage::helper('picklist')->log(sprintf("%s->we don't work on weekends!", __METHOD__) );
                throw new Exception(sprintf("we don't work on '%s'!", $theDay));
                return false;
            }

            $range = Mage::helper('picklist/date')->getDateRange($theDay);
            $tsFrom = Mage::helper('picklist/date')->getStartOfDay($range['start']);
            $tsTo = Mage::helper('picklist/date')->getEndOfDay($range['end']);

            // test dates
            //$tsFrom = mktime(0, 0, 0, 8, 21, 2014);
            //$tsTo = mktime(0, 0, 0, 8, 24, 2014) + (3600 * 24 - 1);

            $from = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $tsFrom);
            Mage::helper('picklist')->log(sprintf("%s->from: %s, %s [%s]", __METHOD__, $from, date("Y-m-d H:i:s", $tsFrom), $tsFrom) );

            $to = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $tsTo);
            Mage::helper('picklist')->log(sprintf("%s->to  : %s, %s [%s]", __METHOD__, $to, date("Y-m-d H:i:s", $tsTo), $tsTo) );

            $orderSource = "m"; // m | e
            $state = "processing";
            $status = "processing";

            $includeZero = Mage::getStoreConfig('picklist/default_filter/include_zero');

            // filter options
            $includeConsolidations  = Mage::getStoreConfig('picklist/cron_job_daily_generation/include_consolidations');
            $includePreorders       = Mage::getStoreConfig('picklist/cron_job_daily_generation/include_preorders');
            // outout options
            $includeImage           = Mage::getStoreConfig('picklist/cron_job_daily_generation/show_images');
            $includeSummary         = Mage::getStoreConfig('picklist/cron_job_daily_generation/include_summary');
            $sendToStores           = Mage::getStoreConfig('picklist/cron_job_daily_generation/send_to_stores');
            $splitPdf               = Mage::getStoreConfig('picklist/cron_job_daily_generation/split_pdf');
            $sortby                 = "created_at";

            //$regionFilter = "ALL";
            //$regionFilter = array("1061","1062","1063","1064","1065","1066","1067","1068");
            $regionFilter = array_keys(Mage::helper('picklist')->getRegions());
            $regionFilterApply = "include";

            $customerGroupFilter = "ALL";
            $customerGroupFilterApply = "include";

            $store = "admin"; // used for email prefix
            $numberPdf = 1;
            if ($sendToStores) {
                $stores = explode(",", $sendToStores);
                //$stores = $sendToStores;
                if (is_array($stores)) {
                    shuffle($stores);
                }
                elseif(!empty($stores)) {
                    $store = $stores;
                }
            }

            Mage::helper('picklist')->log(sprintf("stores=%s", print_r($stores, true)) );
            if (isset($splitPdf) && isset($stores) && is_array($stores)) {
                $numberPdf = count($stores);
            }
            Mage::helper('picklist')->log(sprintf("numberPdf=%s", $numberPdf) );
            $fileOutput = "download";
            $emailSent = false;
            $sendEmail = Mage::getStoreConfig('picklist/cron_job_daily_generation/send_email');
            $additionalEmails = Mage::getStoreConfig('picklist/cron_job_daily_generation/additional_emails');

            $picklist = Mage::getModel('picklist/picklist');
            $config = array(
                'order_from'        => $tsFrom,
                'order_to'          => $tsTo,
                'order_source'      => $orderSource,
                'order_state'       => $state,
                'order_status'      => $status,
                'include_image'     => $includeImage,
                'include_cons'      => $includeConsolidations,
                'include_zero'      => $includeZero,
                'include_summary'   => $includeSummary,
                'sort_by'           => $sortby,
                'filter_region'         => $regionFilter,
                'filter_region_apply'   => $regionFilterApply,
                'filter_cg'             => $customerGroupFilter,
                'filter_cg_apply'       => $customerGroupFilterApply,
                'number_pdf'        => $numberPdf
            );

            $response = "";
            $outFiles = $picklist->generate($config);
            //Mage::helper('picklist')->log(sprintf("outFiles=%s", print_r($outFiles, true)) );

            // Mage::getSingleton('core/session')->addSuccess('Ok ' . $pdf);
            foreach($outFiles as $file) {
                if (!file_exists($file)) {
                    throw new Exception(sprintf("failed to create file %s.", $file));
                }
            }

            $output = Mage::getSingleton('picklist/output_download');

            $outputPath = $output->createTmpDir();
            $files = $output->copyFilesToMedia($outputPath, $outFiles, $stores, $store);

            Mage::helper('picklist')->log(sprintf("stores: %s", print_r($stores, true)) );
            $response = substr($outputPath, strrpos($outputPath, "media"));

            // send emails
            if ($sendEmail) {
                $vars = array('from' => $tsFrom, 'to' => $tsTo);
                Mage::helper('picklist')->sendEmails($files, $vars, $additionalEmails);
                $emailSent = true;
            }

            $job = Mage::helper('picklist')->logPickListJob(
                'create',
                'cron',
                json_encode($config),
                'download',
                ($emailSent ? 1 : 0),
                $response
            );
        }
        catch(Exception $ex) {
            $response = $ex->getMessage();
            Mage::helper('picklist')->log(sprintf("error: %s", $ex->getMessage()) );

            $job = Mage::helper('picklist')->logPickListJob(
                'create',
                'cron',
                json_encode($config),
                'error',
                0,
                $ex->getMessage()
            );
        }

        return $job->getJobId();
    }

    /**
     * daily pick list purge, deletes everything in picklist/default_output/download_path by cron
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @param int $test
     * @return FactoryX_PickList_Model_Observer
     */
    public function dailyOutputDirPurge(Mage_Cron_Model_Schedule $schedule = null, $test = 0) {

        if (!Mage::getStoreConfigFlag('picklist/cron_job_daily_delete/enabled')) {
            if ($test) {
                Mage::helper('picklist')->log(sprintf("running test...") );
            }
            else {
                Mage::helper('picklist')->log(sprintf("%s->cron disabled!", __METHOD__) );
                return false;
            }
        }

        /*
        $params = array();
        if (isset($schedule) && $schedule->getEvent()) {
            $params = $schedule->getEvent()->getRequest()->getParams();
            Mage::helper('picklist')->log(sprintf("%s->params=%s", __METHOD__, print_r($params, true)) );
        }
        */

        /** @var $resource FactoryX_PickList_Model_Output_Download 'picklist/output_download' */
        $model = 'picklist/output_download';
        //$resource = Mage::getResourceSingleton($model);
        $resource = Mage::getModel($model);

        try {
            //$request = json_encode($params);
            $request = sprintf("purge folder '%s'", $resource->getOutputDir());
            $response = $resource->purgeOutputDir();
        }
        catch(Exception $ex) {
            $response = $ex->getMessage();
        }

        // log job
        $job = Mage::helper('picklist')->logPickListJob(
            'delete',
            'cron',
            $request,
            'n/a',
            0,
            $response
        );
        return $job->getJobId();
    }

}

