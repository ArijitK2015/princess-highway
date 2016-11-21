<?php

/**
 * Class FactoryX_Contests_Model_Mysql4_Contest
 */
class FactoryX_Contests_Model_Mysql4_Contest extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_arrayWinners = array();

    protected function _construct()
    {
        $this->_init('contests/contest', 'contest_id');
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @return mixed
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object) {
        $condition = $this->_getWriteAdapter()->quoteInto('contest_id = ?', $object->getId());
        $this->_getWriteAdapter()->delete($this->getTable('contests/store'), $condition);

        if (!$object->getData('stores')) {
            $storeArray = array();
            $storeArray['contest_id'] = $object->getId();
            $storeArray['store_id'] = '0';
            $this->_getWriteAdapter()->insert($this->getTable('contests/store'), $storeArray);
        } else {
            foreach ((array) $object->getData('stores') as $store) {
                $storeArray = array();
                $storeArray['contest_id'] = $object->getId();
                $storeArray['store_id'] = $store;
                $this->_getWriteAdapter()->insert($this->getTable('contests/store'), $storeArray);
            }
        }
        return parent::_afterSave($object);
    }
	
	/**
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object) {
        $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('contests/store'))
                ->where('contest_id = ?', $object->getId());
				
		if ($data = $this->_getReadAdapter()->query($select)) {
            $storesArray = array();
			while ($row = $data->fetch())
			{
				$storesArray[] = $row['store_id'];
			}
            $object->setData('store_id', $storesArray);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object) {

        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $select->join(array('cps' => $this->getTable('contests/store')), $this->getMainTable() . '.contest_id = `cps`.contest_id')
                    ->where('`cps`.store_id in (0, ?) ', $object->getStoreId())
                    ->order('store_id DESC')
                    ->limit(1);
        }
        return $select;
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object) {

        // Cleanup stats on contest delete
        $adapter = $this->_getReadAdapter();
		// 1. Delete rewrite rules
		try
		{
			// If multistore
			if (!Mage::app()->isSingleStoreMode())
			{
				$select = $this->_getReadAdapter()->select()
						->from($this->getTable('contests/store'))
						->where('contest_id = ?', $object->getId());
						
				if ($data = $this->_getReadAdapter()->query($select)) {
					while ($row = $data->fetch())
					{
						$affectedStoreId = $row['store_id'];
						$idPath = $object->getIdentifier()."_".$affectedStoreId;
						$urlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($idPath);
						$urlRewrite->delete();
					}
				}
			}
			// If single store
			else
			{
				$urlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($object->getIdentifier());
				$urlRewrite->delete();
			}
		}
		catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		// 2. Delete contest/store
        $adapter->delete($this->getTable('contests/store'), 'contest_id=' . $object->getId());
    }

    /**
     * @param $object
     * @param $numbersToDraw
     * @param $states
     * @return array
     */
    public function drawRafWinners($object,$numbersToDraw,$states)
    {
        // Get the winners using the old ReferAFriend module method with a temporary table
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');
        $readConnection = $resource->getConnection('core_read');

        if ($writeConnection->isTableExists('fx_contests_winner_tmp'))
        {
            $writeConnection->dropTable('fx_contests_winner_tmp');

        }

        $table = new Varien_Db_Ddl_Table();
        $table->setName($resource->getTableName('contests/contest_winner'));
        $table->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255);
        $table->addColumn('referrer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11);
        $table->addColumn('state', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50);
        $table->setOption('type', 'InnoDB');
        $table->setOption('charset', 'utf8');
        $writeConnection->createTable($table);

        // 1 entry per referee
        $query = "INSERT INTO {$resource->getTableName('contests/contest_winner')}
							SELECT r1.email, r1.referrer_id, r1.state
							FROM {$resource->getTableName('contests/referrer')} r1
							INNER JOIN {$resource->getTableName('contests/referee')} r2
							ON r1.referrer_id = r2.referrer_id
							AND r1.contest_id = {$object->getContestId()}";

        $writeConnection->query($query);

        // Plus 1 extra entry per referrer
        $query = "INSERT INTO {$resource->getTableName('contests/contest_winner')}
							SELECT DISTINCT r1.email, r1.referrer_id, r1.state
							FROM {$resource->getTableName('contests/referrer')} r1
							WHERE contest_id = {$object->getContestId()}";

        $writeConnection->query($query);

        if ($states)
        {
            // Single state
            if (!is_array($states))
            {
                // Select the winner(s) randomly one by one (so we don't pick someone who has already win) per state
                for ($i = 0; $i < $numbersToDraw; $i++)
                {
                    $query = "SELECT referrer_id
									FROM {$resource->getTableName('contests/contest_winner')}
									WHERE state = '$states'
									ORDER BY RAND()
									LIMIT 1";

                    if ($winnerReferrerId = $readConnection->fetchOne($query)) {

                        $this->_arrayWinners[] = $winnerReferrerId;

                        // Remove every entry with the winner referrer id
                        $query = "DELETE FROM {$resource->getTableName('contests/contest_winner')}
										WHERE referrer_id = {$winnerReferrerId}";

                        $writeConnection->query($query);
                    }
                }
            }
            else
            {
                foreach($states as $state)
                {
                    for ($i = 0; $i < $numbersToDraw; $i++)
                    {
                        $query = "SELECT referrer_id
										FROM {$resource->getTableName('contests/contest_winner')}
										WHERE state = '$state'
										ORDER BY RAND()
										LIMIT 1";

                        if ($winnerReferrerId = $readConnection->fetchOne($query))
                        {
                            $this->_arrayWinners[] = $winnerReferrerId;

                            // Remove every entry with the winner referrer id
                            $query = "DELETE FROM {$resource->getTableName('contests/contest_winner')}
												WHERE referrer_id = {$winnerReferrerId}";

                            $writeConnection->query($query);
                        }
                    }
                }
            }
        }
        else
        {
            // Select the winner(s) randomly one by one (so we don't pick someone who has already win)
            for ($i = 0; $i < $numbersToDraw; $i++)
            {
                $query = "SELECT referrer_id
								FROM {$resource->getTableName('contests/contest_winner')}
								ORDER BY RAND()
								LIMIT 1";

                if ($winnerReferrerId = $readConnection->fetchOne($query)) {

                    $this->_arrayWinners[] = $winnerReferrerId;

                    // Remove every entry with the winner referrer id
                    $query = "DELETE FROM {$resource->getTableName('contests/contest_winner')}
									WHERE referrer_id = {$winnerReferrerId}";

                    $writeConnection->query($query);
                }
            }
        }

        return $this->_arrayWinners;
    }

    /**
     * @param $object
     * @param $numbersToDraw
     * @param $states
     * @return array
     */
    public function drawGaWinners($object,$numbersToDraw,$states)
    {
        if ($states)
        {
            // Single state
            if (!is_array($states))
            {
                $collection = Mage::getResourceModel('contests/referrer_collection');
                $collection->addContestFilter($object->getContestId());
                $collection->addStateFilter($states);
                $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
                $collection->getSelect()->limit($numbersToDraw);

                // Call iterator walk method with collection query string and callback method as parameters
                // Has to be used to handle massive collection instead of foreach
                Mage::getSingleton('core/resource_iterator')->walk($collection->getSelect(), array(array($this, 'generateWinnersArray')));
            }
            else
            {
                foreach($states as $state)
                {
                    for ($i = 0; $i < $numbersToDraw; $i++)
                    {
                        $collection = Mage::getResourceModel('contests/referrer_collection');
                        $collection->addContestFilter($object->getContestId());
                        $collection->addStateFilter($state);
                        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
                        $collection->getSelect()->limit(1);
                        $collection->load();
                        if ($winnerReferrerId = $collection->getFirstItem()->getReferrerId())
                        {
                            $this->_arrayWinners[] = $winnerReferrerId;
                        }
                    }
                }
            }
        }
        else
        {
            $collection = Mage::getResourceModel('contests/referrer_collection');
            $collection->addContestFilter($object->getContestId());
            $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
            $collection->getSelect()->limit($numbersToDraw);

            // Call iterator walk method with collection query string and callback method as parameters
            // Has to be used to handle massive collection instead of foreach
            Mage::getSingleton('core/resource_iterator')->walk($collection->getSelect(), array(array($this, 'generateWinnersArray')));
        }

        return $this->_arrayWinners;
    }

    /**
     * @param $args
     */
    public function generateWinnersArray($args)
    {
        $this->_arrayWinners[] = $args['row']['referrer_id'];
    }
}