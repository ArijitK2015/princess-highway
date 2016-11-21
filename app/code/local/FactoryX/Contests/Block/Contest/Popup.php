<?php

/**
 * Class FactoryX_Contests_Block_Contest_Popup
 */
class FactoryX_Contests_Block_Contest_Popup extends Mage_Core_Block_Template
{
    const SHOW_POPUP_ON_CONTEST_PAGE = false;

    protected $_popupText = null;
    protected $_popupIdentifier = null;
    protected $_popupContestId = null;
    protected $_popupContestReferers = null;
    protected $_popupImage = null;
    protected $_popupType = null;

    /**
     * @param null $store
     * @return bool
     */
    public function hasPopupContest($store = null)
    {
        $hasPopup = false;
        try {
            $store = (empty($store)) ? null : $store;
            
            $popupContests = Mage::getResourceModel('contests/contest_collection')
                ->addIsPopupFilter(1)
                ->addDisplayedFilter(1)
                ->addStoreFilter($store);

            if (count($popupContests) == 1) {
                $popupContest = $popupContests->getFirstItem();
                
                // check that we arent on the contest page
                $urlParts = explode('/', ltrim(Mage::app()->getRequest()->getRequestUri(), '/'));
                //Mage::helper('contests')->log(sprintf("urlPart: %s", print_r($urlPart, true)) );
				// NB: this is also handled on the template level
                if (
                    !(
                        !self::SHOW_POPUP_ON_CONTEST_PAGE
                        &&
                        (is_array($urlParts) && preg_match("/contests/", $urlParts[0]))
                        // do we want to show contest popups on different contests? probably not
                        // && end($urlParts) == $popupContest->getId()
                    )) {
                    //Mage::helper('contests')->log(sprintf("contest page %s", $popupContest->getId()));
                    $this->_popupText = $popupContest->getPopupText();
                    $this->_popupIdentifier = $popupContest->getIdentifier();
                    $this->_popupContestId = $popupContest->getId();
                    $this->_popupContestReferers = $popupContest->getPopupReferers();
                    $this->_popupImage = $popupContest->getPopupImageUrl();
                    $this->_popupType = $popupContest->getPopupType();
                    $hasPopup = true;
                }
            }
            elseif(count($popupContests) > 1) {
                throw new Exception("It seems like there is more than one popup contest");
            }
        }
        catch (Exception $e) {
            Mage::helper('contests')->log("Exception caught in %s under % with message: %s", __FILE__, __FUNCTION__, $e->getMessage());
            Mage::getSingleton('customer/session')->addError($this->__('There was a problem loading the popup contest'));
        }
        //Mage::helper('contests')->log(sprintf("%s=%d", __METHOD__, $hasPopup));
        return $hasPopup;
    }

    /**
     * @return array|bool
     */
    public function getReferersLimitation()
    {
        if ($this->_popupContestReferers != "")
        {
            $limitationsArray = explode(',',$this->_popupContestReferers);
            return $limitationsArray;
        }
        else return false;
    }

    /**
     * @return null
     */
    public function getPopupText()
    {
        return $this->_popupText;
    }

    /**
     * @return null
     */
    public function getPopupIdentifier()
    {
        return $this->_popupIdentifier;
    }

    /**
     * @return null
     */
    public function getPopupContestId()
    {
        return $this->_popupContestId;
    }

    /**
     * @return null
     */
    public function getPopupImage()
    {
        return $this->_popupImage;
    }

    /**
     * @return null
     */
    public function getPopupType()
    {
        return $this->_popupType;
    }

}
