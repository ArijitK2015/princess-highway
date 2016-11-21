<?php

/**
 * Class FactoryX_BugFixes_Helper_Cms_Wysiwyg_Images
 */
class FactoryX_BugFixes_Helper_Cms_Wysiwyg_Images extends Mage_Cms_Helper_Wysiwyg_Images
{
    /**
     * @return string
     */
    public function getCurrentPath()
    {
        if (version_compare(Mage::getVersion(),"1.7.0.2",">"))
        {
            return parent::getCurrentPath();
        }
        else return realpath(parent::getCurrentPath());
    }
}