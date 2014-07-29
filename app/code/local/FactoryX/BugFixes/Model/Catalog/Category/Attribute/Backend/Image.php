<?php
/**
 * Fix the exception 'Exception' with message '$_FILES array is empty'
 * Source: http://stackoverflow.com/questions/9700611/exception-raised-in-varien-file-uploader-when-using-xmlrpc-to-modify-categories
 * Official fix from Magento2: https://github.com/magento/magento2/blob/master/app/code/Magento/Catalog/Model/Category/Attribute/Backend/Image.php
 */
class FactoryX_BugFixes_Model_Catalog_Category_Attribute_Backend_Image extends Mage_Catalog_Model_Category_Attribute_Backend_Image
{

    /**
     * Save uploaded file and set its name to category
     *
     * @param Varien_Object $object
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());
		
		// if no image was set - nothing to do
        if (empty($value) && empty($_FILES)) {
            return $this;
        }

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        $path = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'category' . DS;

        try {
            $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);

            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                Mage::logException($e);
            }
            /** @TODO ??? */
            return;
        }
    }
}
