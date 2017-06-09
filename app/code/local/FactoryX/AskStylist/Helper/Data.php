<?php

class FactoryX_ASkStylist_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('askstylist/options/enable');
    }

    public function getCorrespondingRecipient($enquiry)
    {
        // set default
        $recipient = Mage_Contacts_IndexController::XML_PATH_EMAIL_RECIPIENT;

        // Get the linked emails
        $linkedEmails = $this->_getLinkedEmails();
        // Loop
        if ($linkedEmails && is_array($linkedEmails)) {
            foreach($linkedEmails as $linkedEmail) {
                // Get the right email
                if ($linkedEmail['enquiry'] == $enquiry) {
                    $recipient = sprintf("trans_email/%s/email", $linkedEmail['email']);
                }
            }
        }
        return $recipient;
    }

    /**
     * @return mixed
     */
    protected function _getLinkedEmails()
    {
        return unserialize(Mage::getStoreConfig('askstylist/options/linked_emails', Mage::app()->getStore()->getStoreId()));
    }
}