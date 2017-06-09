<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var Mage_Cms_Model_Page $page */
$page = Mage::getModel('cms/page');
$page->load('about-magento-demo-store');
$page->delete();

/** @var Mage_Cms_Model_Block $block */
$block = Mage::getModel('cms/block');
$block->load('footer_links_company');
$block->delete();

/** @var Mage_Cms_Model_Block $block */
$block = Mage::getModel('cms/block');
$block->load('footer_links');
$block->setContent(
    '<ul class="list list-inline text-uppercase">
<li><a href="{{store direct_url="sizes"}}">Sizes</a></li>
<li>|</li>
<li><a href="{{store direct_url="careers"}}">Jobs</a></li>
<li>|</li>
<li><a href="{{store direct_url="help"}}">Help</a></li>
<li>|</li>
<li><a href="{{store direct_url="customer-support"}}">Customer Support</a></li>
<li>|</li>
<li><a href="{{store direct_url="privacy-policy"}}">Privacy Policy</a></li>
<li>|</li>
<li><a href="{{store direct_url="returns-exchanges"}}">Returns &amp; Exchanges</a></li>
<li>|</li>
<li><a href="{{store direct_url="contacts"}}">Contact</a></li>
<li>|</li>
<li><a href="{{store direct_url="customersurvey/index/view/id/1"}}">Feedback</a></li>
</ul>'
);
$block->save();

/** @var Mage_Cms_Model_Block $block */
$block = Mage::getModel('cms/block');
$block->setData(
    array(
        'title'         =>  'Social Links',
        'identifier'    =>  'social-links',
        'status'        =>  '1',
        'store_id'      =>  '1',
        'content'       =>  '<ul class="list list-inline">
<li>
<a href="https://www.facebook.com/princesshighway/" rel="noopener noreferrer" target="_blank">
<i class="fa fa-facebook fa-lg">
</i>
</a>
</li>
<li>
<a href="https://www.instagram.com/princesshighwayclothing/" rel="noopener noreferrer" target="_blank">
<i class="fa fa-instagram fa-lg">
</i>
</a>
</li>
<li>
<a href="https://www.tumblr.com/tagged/princess-highway" rel="noopener noreferrer" target="_blank">
<i class="fa fa-tumblr fa-lg">
</i>
</a>
</li>
</ul>'
    )
);
$block->save();

$installer->endSetup();