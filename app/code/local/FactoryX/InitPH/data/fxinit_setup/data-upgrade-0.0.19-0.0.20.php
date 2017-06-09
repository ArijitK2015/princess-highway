<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** @var Mage_Cms_Model_Block $block */
$block = Mage::getModel('cms/block');
$block->load('footer_links');
$block->setContent(
    '<ul class="list list-inline text-uppercase">
<li class="text-center-xs"><a href="{{store direct_url="sizes"}}">Sizes</a></li>
<li class="hidden-sm hidden-xs">|</li>
<li class="text-center-xs"><a href="{{store direct_url="careers"}}">Jobs</a></li>
<li class="hidden-sm hidden-xs">|</li>
<li class="text-center-xs"><a href="{{store direct_url="help"}}">Help</a></li>
<li class="hidden-sm hidden-xs">|</li>
<li class="text-center-xs"><a href="{{store direct_url="customer-support"}}">Customer Support</a></li>
<li class="hidden-sm hidden-xs">|</li>
<li class="text-center-xs"><a href="{{store direct_url="privacy-policy"}}">Privacy Policy</a></li>
<li class="hidden-sm hidden-xs">|</li>
<li class="text-center-xs"><a href="{{store direct_url="returns-exchanges"}}">Returns &amp; Exchanges</a></li>
<li class="hidden-sm hidden-xs">|</li>
<li class="text-center-xs"><a href="{{store direct_url="contacts"}}">Contact</a></li>
<li class="hidden-sm hidden-xs">|</li>
<li class="text-center-xs"><a href="{{store direct_url="customersurvey/index/view/id/1"}}">Feedback</a></li>
</ul>'
);
$block->save();

$block->save();

$installer->endSetup();