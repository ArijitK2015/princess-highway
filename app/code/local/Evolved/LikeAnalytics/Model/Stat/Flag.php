<?php

/**
 * Retail Evolved - Facebook Like Analytics
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with this
 * package in the file EVOLVED_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://retailevolved.com/eula-1-0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to service@retailevolved.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * You may edit this file, but only at your own risk, as long as it is within
 * the constraints of the license agreement. Before upgrading the module (not Magento), 
 * be sure to back up your original installation as the upgrade may override your
 * changes.
 *
 * @category   Evolved
 * @package    Evolved_LikeAnalytics
 * @copyright  Copyright (c) 2010 Kaelex Inc. DBA Retail Evolved (http://retailevolved.com)
 * @license    http://retailevolved.com/eula-1-0 (Retail Evolved EULA 1.0)
 */

class Evolved_LikeAnalytics_Model_Stat_Flag extends Mage_Core_Model_Flag
{
	protected $_flagCode = 'evlikeanalytics';

    const STATE_QUEUED = 1;
    const STATE_RUNNING = 2;

    protected function _beforeSave()
    {
        switch ($this->getState()) {
			case self::STATE_QUEUED:
                $this->setFlagData($this->getQueueInfo());
                break;
            case self::STATE_RUNNING:
                $this->setFlagData(getmypid());
                break;

            default:
                break;
        }

        return parent::_beforeSave();
    }
}