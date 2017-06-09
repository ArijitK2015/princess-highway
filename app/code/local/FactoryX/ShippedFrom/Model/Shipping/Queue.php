<?php

use \Picqer\Barcode\BarcodeGeneratorHTML;
use \Zff\Html2Pdf\Html2PdfFactory;

/**
 * Class FactoryX_ShippedFrom_Model_Shipping_Queue
 */
class FactoryX_ShippedFrom_Model_Shipping_Queue extends Mage_Core_Model_Abstract
{
    /**
     * Status for an initialized AusPost shipment
     */
    const STATUS_INITIALIZED = "initialized";

    /**
     * Status for an existing AusPost shipment
     */
    const STATUS_SHIPPED = "shipped";

    /**
     * Status for an AusPost shipment with an initiated label
     */
    const STATUS_LABEL_INITIALIZED = "label-initiated";

    /**
     * Status for an AusPost shipment with a created label
     */
    const STATUS_LABEL_CREATED = "label-created";

    /**
     * Status for an AusPost shipment where the label has been sent
     */
    const STATUS_LABEL_SENT = "label-sent";

    /**
     * Status for an AusPost shipment which order has been lodged
     */
    const STATUS_ORDERED = "ordered";

    /**
     * Status for a complete AusPost shipment
     */
    const STATUS_COMPLETE = "complete";

    /**
     * Barcode prefix
     */
    const BARCODE_PREFIX = "019931265099999891";

    /**
     * Aviation notice in HTML to include in the labels
     */
    const AVIATION_NOTICE_HTML = "<strong>Aviation Security and Dangerous Goods Declaration</strong><br/>The sender acknowledges that this article may be carried by air and will be subject to aviation security and clearing procedures; and the sender declares that the article does not contain any dangerous or prohibited goods, explosive or incendiary devices. A false declaration is a criminal offence.";

    /**
     * @var
     */
    protected $_shipmentData;

    protected function _construct()
    {
        $this->_init('shippedfrom/shipping_queue', 'schedule_id');
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param bool $save
     * @return $this
     */
    public function initQueue(Mage_Sales_Model_Order_Shipment $shipment, $save = true)
    {
        $this->setShipmentId($shipment->getIncrementId())
            ->setStatus(self::STATUS_INITIALIZED)
            ->setShippedFrom($shipment->getShippedFrom())
            ->setCreatedAt(strftime('%Y-%m-%d %H:%M:00', time()));

        if ($save) {
            $this->save();
        }

        return $this;
    }
    
    /**
     * Getter for field label
     *
     * @param string $field
     * @return string|null
     */
    public function getFieldLabel($field)
    {
        $label = "";
        switch ($field) {
            case 'group':
                $label = Mage::helper('shippedfrom')->__('Group');
                break;
            default:
                $field = preg_replace("/_/", " ", $field);
                $label = ucwords($field);
        }

        return $label .= ":";
    }
    
    /**
     * Render data
     *
     * @param string $key
     * @return mixed
     */
    public function renderData($key)
    {
        $value = $this->_getData($key);
        switch ($key) {
            case 'json_request':
                $value = html_entity_decode($value);
                $value = json_encode(json_decode($value), JSON_PRETTY_PRINT);
                break;
        }

        return $value;
    }

    /**
     * @return mixed
     */
    protected function getToPhone()
    {
        if (null === $this->_shipmentData) {
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            $shipment = Mage::helper('shippedfrom/auspost')->getShipmentFromQueueEntry($this);
            $this->_shipmentData = Mage::getModel('shippedfrom/auspost_shipping_shipments')
                ->generateAuspostShipmentData($shipment);
        }

        return $this->_shipmentData['shipments'][0]['to']['phone'];
    }

    /**
     * @param $fromTo
     * @return mixed
     */
    protected function getAddress($fromTo)
    {
        if (null === $this->_shipmentData) {
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            $shipment = Mage::helper('shippedfrom/auspost')->getShipmentFromQueueEntry($this);
            $this->_shipmentData = Mage::getModel('shippedfrom/auspost_shipping_shipments')
                ->generateAuspostShipmentData($shipment);
        }

        $address = array();
        $address[] = $this->_shipmentData['shipments'][0][$fromTo]['name'];
        foreach ($this->_shipmentData['shipments'][0][$fromTo]['lines'] as $addresLine) {
            $address[] = $addresLine;
        }

        $address[] = strtoupper($this->_shipmentData['shipments'][0][$fromTo]['suburb'])
            . " "
            . strtoupper($this->_shipmentData['shipments'][0][$fromTo]['state'])
            . " "
            . $this->_shipmentData['shipments'][0][$fromTo]['postcode'];

        return implode("<br/>", $address);
    }

    /**
     * @return mixed
     */
    protected function getWeight()
    {
        if (null === $this->_shipmentData) {
            /** @var Mage_Sales_Model_Order_Shipment $shipment */
            $shipment = Mage::helper('shippedfrom/auspost')->getShipmentFromQueueEntry($this);
            $this->_shipmentData = Mage::getModel('shippedfrom/auspost_shipping_shipments')
                ->generateAuspostShipmentData($shipment);
        }

        $weight = 0;
        foreach ($this->_shipmentData['shipments'][0]['items'] as $item) {
            $weight += $item['weight'];
        }

        return $weight . "kg";
    }

    /**
     * @return string
     */
    public function getLabelGenerationHtml()
    {
        if (!class_exists(BarcodeGeneratorHTML::class)) {
            Mage::throwException(
                Mage::helper('shippedfrom')->__(
                    'Class \Picquer\Barcode\BarcodeGeneratorHTML does not exist, please install https://github.com/picqer/php-barcode-generator'
                )
            );
        }

        if (!class_exists(Html2PdfFactory::class)) {
            Mage::throwException(
                Mage::helper('shippedfrom')->__(
                    'Class \Zff\Html2Pdf\Html2PdfFactory does not exist, please install https://github.com/fagundes/ZffHtml2pdf'
                )
            );
        }

        $html = "";
        $generator = new BarcodeGeneratorHTML();
        $barCodeHtml = $generator->getBarcode(
            self::BARCODE_PREFIX . $this->getApArticleId(),
            $generator::TYPE_CODE_128,
            1.1,
            75
        );
        $baseDir = Mage::getBaseDir() . DS;
        $baseUrl = str_replace('index.php/', '', Mage::getBaseUrl());
        $labelPath = str_replace($baseUrl, $baseDir, $this->getLocalLabelLink());
        $qrCode = Mage::helper('shippedfrom/pdfDmtx')->generateDmtxFromPdf($labelPath,
            Mage::getBaseDir('media') . DS . 'auspost');
        $qrCode = str_replace($baseDir, $baseUrl, $qrCode);
        $phone = $this->getToPhone();
        $toAddress = $this->getAddress('to');
        $weight = $this->getWeight();
        $consignmentId = $this->getApConsignmentId();
        $articleId = $this->getApArticleId();
        $shipmentNumber = $this->getShipmentId();
        $fromAddress = $this->getAddress('from');
        $html .= "<div class='top-image'>";
        $html .= "<img style='width: 100%' src='skin/adminhtml/default/default/images/factoryx/shippedfrom/express-000.jpg' alt='Auspost Logo' />";
        $html .= "</div>";
        /** @TODO signature required ? */
        $signature = "Signature NOT required";
        /** @TODO parcel ? */
        $parcel = "1";
        $html .= "<table style='width:100%'><tr><th valign='top' style='text-align: left'><span>DELIVER TO</span><span style='margin-left:20px'>PHONE " . $phone . "</span></th><th valign='top'><img width='50' height='50' src='" . $qrCode . "' alt='QR Code'/></th></tr>";
        $html .= "<tr><td colspan='2' valign='top'><div style='padding-bottom:70px'>" . $toAddress . "</div></td></tr>";
        $html .= "<tr><td style='border-top:1px solid black;height:40px;' valign='top'><strong>DELIVERY INSTRUCTIONS</strong></td><td style='border-top:1px solid black;text-align:right;height:40px;' valign='top'><strong>" . $weight . "</strong></td></tr>";
        $html .= "<tr><td style='border-top:1px solid black;border-right:1px solid black' valign='top'><strong>" . $signature . "</strong></td><td style='border-top:1px solid black'>CON NO " . $consignmentId . "<br/><strong>PARCEL</strong> " . $parcel . "</td></tr>";
        $html .= "<tr><td colspan='2' style='border-top:1px solid black;text-align:center'>AP Article Id: " . $articleId . "</td></tr>";
        $html .= "<tr><td colspan='2'><div style='padding-left:30px;padding-top:5px'>" . $barCodeHtml . "</div></td></tr>";
        $html .= "<tr><td colspan='2' style='text-align:center;'>AP Article Id: " . $articleId . "</td></tr>";
        $html .= "</table>";
        $html .= "<table style='width:100%;table-layout:fixed'>";
        $html .= "<tr><td rowspan='2' style='width:40%;border-top:1px solid black;border-right:1px solid black' valign='top'><strong>SENDER</strong><br/>" . $fromAddress . "</td><td style='border-top:1px solid black;width:60%'>" . self::AVIATION_NOTICE_HTML . "</td></tr>";
        $html .= "<tr><td style='border-top:1px solid black'>" . $shipmentNumber . "</td></tr>";
        $html .= "</table>";
        return $html;
    }

    /**
     * @return string
     */
    public function getLabelHeadHtml()
    {
        return "<html xmlns='http://www.w3.org/1999/html'><head><style='text/css'>* {font-size:11px;}</style></head><body>";
    }

    /**
     * @return string
     */
    public function getLabelFootHtml()
    {
        return "</body></html>";
    }
}