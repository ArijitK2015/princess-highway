<?php

/**
 * Class FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Csv
 */
class FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Csv extends FactoryX_ShippedFrom_Model_Shipping_Carrier_Common_Export_Csv_Abstract
{
    /**
     * @param $orders
     * @return string
     * @throws FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Exception
     */
    public function exportOrders($orders)
    {
        $clickandsend = Mage::getModel('shippedfrom/shipping_carrier_clickandsend');

        /**
        add header
        */
        $header = array(
            'Delivery Company Name',
            'Delivery Name',
            'Delivery Telephone',
            'Delivery Email',
            'Delivery Address (Line 1)',
            'Delivery Address (Line 2)',
            'Delivery Address (Line 3)',
            'Delivery City/ Suburb',
            'Delivery State',
            'Delivery Postcode',
            'Delivery Country Code',
            'Service Code',
            /*
                PP  Parcel Post
                EP  Expres Post
                PPS Parcel Post <500g
                EPS Express Post <500g
            */
            'Article Type',
            /*
                7   : Own Packaging
                19  : Small Flat Rate Satchel (up to 500g)
                20  : Medium Flat Rate Satchel (up to 3kg)
                21  : Large Flat Rate Satchel (up to 5kg)
                16  : Small Flat Rate Box (up to 1kg)
                17  : Medium Flat Rate Box (up to 3kg)
            */
            // cm
            'Length',
            'Width',
            'Height',
            'Declared Weight',
            'Extra Cover',
            'Declared Value',
            'Description of Goods',
            /*
            'Item description',
            */
            'From Name',
            'From Company Name',
            'From Phone',
            'From Fax',
            'From Email',
            'From ABN',
            'From Address (line 1)',
            'From Address (line 2)',
            'From Address (line 3)',
            'From City/ Suburb',
            'From State',
            'From Postcode',
            'From Country Code',
            'Your Reference',
            'Delivery instructions',
            /*
                1. None
                2. If premises unattended leave in a secure location
                3. If premises unattended leave in a secure location out of the weather
                4. If premises unattended leave in a secure location behind gate/fence
                5. If premises unattended leave in a secure location in carport/garage
                6. If premises unattended leave in a secure location near front door
                7. If premises unattended leave in a secure location on back porch
                8. If premises unattended place a card in the post box and leave parcel at nearest AP Outlet
            */
            'Additional services',
            /*
                0 = Tracking
                1 = Tracking with Signature
            */
            'Box or Irregular shaped item',
            /*
                0 = Box or Carton
                1 = Irregular shapped item
            */
            'CDP Location code',
            'Embargoed date',
            'Specify delivery day',
            'Specify delivery date',
            'Delivery timslots',
            'Receiver customer number',
            'Sender track advice',
            'Receiver track advice',
            'Expected delivery date',
            'ATL Options'
        );        
        $clickandsend->addHeaderItem($header);

        foreach ($orders as $order) {
            /*/** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order')->load($order);

            Mage::helper('shippedfrom')->log(sprintf("%s->instanceof=%s", __METHOD__, get_class($order->getShippingCarrier())) );
            /*
            add everything!

            we don't care at present as there is no intergration on the front end
            */
            /*
            if (!$order->getShippingCarrier() instanceof FactoryX_ShippedFrom_Model_Shipping_Carrier_Australiapost) {
                throw new FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Exception(
                    "Order #" . $order->getIncrementId() . " doesn't use Australia Post as its carrier!"
                );
            }
            */
            
            // domestic only
            $shippingAddress = $order->getShippingAddress();
            // gift certs don;t have addresses
            if ($shippingAddress && preg_match("/au/i", $shippingAddress->getCountry()) ) {
                $clickandsend->addItem($order);
            }
        }

        // Save file
        //$name = Mage::app()->getStore()->getName();
        $name = Mage::app()->getStore()->getFrontendName();
        $storename = preg_replace("/\s+/", "-", strtolower($name));
        $fileName = sprintf("%s_%s_clickandsend.csv", date('Ymd_His'), $storename);
        $filePath = Mage::getBaseDir('export') . '/' . $fileName;

        if ($clickandsend->makeCsv($filePath)) {
            return $filePath;
        }

        throw new FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Exception(
            'Unable to build a CSV file!'
        );
    }
}
