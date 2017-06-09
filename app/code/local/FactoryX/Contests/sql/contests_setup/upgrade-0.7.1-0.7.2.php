<?php

$installer = $this;

$installer->startSetup();

// Modify the minimum word count column into a maximum word count
$conn = $installer->getConnection();
$contestTable = $this->getTable('contests/contest');

// Add first name
if ($conn->tableColumnExists($contestTable, 'minimum_word_count')) 
{
	$conn->changeColumn (
			$contestTable, 'minimum_word_count', 'maximum_word_count', 'smallint(2) default NULL'
		);
}
// Remove old reference to competitions module
$installer->run("DELETE FROM core_resource where code = 'competitions_setup';");
	
$installer->endSetup();