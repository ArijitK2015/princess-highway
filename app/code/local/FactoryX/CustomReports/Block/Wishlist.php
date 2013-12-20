<?php
class FactoryX_CustomReports_Block_Wishlist extends Mage_Adminhtml_Block_Template
{

    public $wishlists_count;
    public $items_bought;
    public $shared_count;
    public $referrals_count;
    public $conversions_count;
    public $customer_with_wishlist;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('factoryx/customreports/wishlist.phtml');
		// Set the right URL for the form which handles the dates
		$this->setFormAction(Mage::getUrl('*/*/index'));
    }

    public function _beforeToHtml()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('customreports/wishlist_grid', 'customreports.grid'));

        $collection = Mage::getResourceModel('reports/wishlist_collection');
		/*
        list($customerWithWishlist, $wishlistsCount) = $collection->getWishlistCustomerCount();
        $this->setCustomerWithWishlist($customerWithWishlist);
        $this->setWishlistsCount($wishlistsCount);
        $this->setItemsBought(0);
        $this->setSharedCount($collection->getSharedCount());
        $this->setReferralsCount(0);
        $this->setConversionsCount(0);
		*/
        return $this;
    }

}