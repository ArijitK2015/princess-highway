<?php
/**
uses libdmtx to extract the DPID from the Australia Post label
https://sourceforge.net/p/libdmtx/libdmtx/ci/master/tree/

DPID is the Australia Post, Delivery Point Identifier

# libdmtx installation

yum install -y ImageMagick ImageMagick-devel

sudo su -
cd /usr/local/src
git clone https://git.code.sf.net/p/libdmtx/libdmtx
cd libdmtx
./autogen.sh
./configure
make && make install

cd ..
git clone https://git.code.sf.net/p/libdmtx/dmtx-utils
cd dmtx-utils
./autogen.sh
./configure PKG_CONFIG_PATH=/usr/local/lib/pkgconfig
make && make install

# help

http://libdmtx.wikidot.com/libdmtx-faq

*/
class FactoryX_ShippedFrom_Helper_PdfDmtx extends Mage_Core_Helper_Abstract
{
    private static $debug = 0;
    private static $_dmtxread = "dmtxread";
    private static $_dmtxwrite = "dmtxwrite";
    private static $_appPath = "/usr/local/bin";
    private static $_outputPath = "/tmp";

    public static function generateDmtxFromPdf($pdfPath, $outputPath = false)
    {
        if (self::$debug) {
            echo sprintf("%s(%s, %s)\n", __METHOD__, $pdfPath, $outputPath);
        }
        // default output path
        if (!$outputPath) {
            if (self::$debug) {
                echo sprintf("use default output path: %s\n", self::$_outputPath);
            }
            $outputPath = self::$_outputPath;
        }
        $barcode = self::dmtxread($pdfPath);
        if (self::$debug) {
            echo sprintf("barcode=%s\n", $barcode);
        }
        $barcodePath = sprintf("%s/%s.png", $outputPath, $barcode);
        if (self::$debug) {
            echo sprintf("barcodePath=%s\n", $barcodePath);
        }
        self::dmtxwrite($barcode, $barcodePath);
        return $barcodePath;
    }

    protected static function dmtxwrite($barcode = "test", $barcodePath) {
        if (!self::commandExist(self::$_dmtxwrite)) {
            Mage::throwException(Mage::helper('shippedfrom')->__("cannot find dmtxwrite!"));
        }
        $barcodeText = sprintf("%s.txt", $barcodePath);
        $dmtxwriteCmd = sprintf("echo -n %s > %s 2>&1",
            $barcode, $barcodeText
        );
        if (self::$debug) {
            echo sprintf("dmtxwriteCmd=%s\n", $dmtxwriteCmd);
        }
        $output = shell_exec($dmtxwriteCmd);
        if (self::$debug) {
            echo sprintf("output=%s\n", $output);
        }

        $dmtxwriteCmd = sprintf("dmtxwrite \"%s\" > \"%s\" 2>&1",
            $barcodeText, $barcodePath
        );
        if (self::$debug) {
            echo sprintf("dmtxwriteCmd=%s\n", $dmtxwriteCmd);
        }
        $output = shell_exec($dmtxwriteCmd);
        if (self::$debug) {
            echo sprintf("output=%s\n", $output);
        }
        return $output;
    }

    protected static function dmtxread($pdfPath) {
        if (!self::commandExist(self::$_dmtxread)) {
            Mage::throwException(Mage::helper('shippedfrom')->__("cannot find dmtxread!"));
        }
        $dmtxreadCmd = sprintf("dmtxread --milliseconds=%d --stop-after=%d \"%s\" 2>&1",
            $maxMilliseconds = 5000,
            $maxBarcodes = 1,
            $pdfPath);
        if (self::$debug) {
            echo sprintf("dmtxreadCmd=%s\n", $dmtxreadCmd);
        }
        $barcode = shell_exec($dmtxreadCmd);
        return $barcode;
    }

    private static function commandExist($cmd)
    {
        $return = shell_exec(sprintf("which %s", escapeshellarg($cmd)));
        return !empty($return);
    }
}