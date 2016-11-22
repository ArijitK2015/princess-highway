<?php

/**
 * iKantam
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade InstagramConnect to newer
 * versions in the future.
 *
 * @category    Ikantam
 * @package     FactoryX_Instagram
 * @copyright   Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FactoryX_Instagram_Model_Resource_Instagramlog extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init("instagram/instagramlog", "log_id");
    }

    public function loadByIds(FactoryX_Instagram_Model_Instagramlog $log, $imageId, $listId)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array('image_id' => $imageId, 'list_id' => $listId);
        $select  = $adapter->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('image_id = :image_id')
            ->where('list_id = :list_id');

        $logId = $adapter->fetchOne($select, $bind);
        if ($logId) {
            $this->load($log, $logId);
        } else {
            $log->setData(array());
        }

        return $this;
    }
}
