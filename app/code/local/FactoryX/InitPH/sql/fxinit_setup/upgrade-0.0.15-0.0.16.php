<?php
/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

Mage::getModel('aw_layerednavigation/synchronization')->run();

// AW layered navigation configuration
$installer->run("
UPDATE aw_layerednavigation_filter_eav SET value = 0 where name = 'is_enabled' and filter_id NOT IN (select entity_id from aw_layerednavigation_filter where code LIKE '%size%');
UPDATE aw_layerednavigation_filter_eav SET value = 0 where name = 'is_enabled_in_search' and filter_id NOT IN (select entity_id from aw_layerednavigation_filter where code LIKE '%size%');
UPDATE aw_layerednavigation_filter_eav SET value = 0 where name = 'is_enabled' and filter_id != (select entity_id from aw_layerednavigation_filter where code = 'colour_base');
UPDATE aw_layerednavigation_filter_eav SET value = 0 where name = 'is_enabled_in_search' and filter_id != (select entity_id from aw_layerednavigation_filter where code = 'colour_base');
UPDATE aw_layerednavigation_filter_eav SET value = 1 where name = 'is_enabled' and filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base');
UPDATE aw_layerednavigation_filter_eav SET value = 1 where name = 'is_enabled_in_search' and filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base');
UPDATE aw_layerednavigation_filter_eav SET value = 1 where name = 'is_enabled' and filter_id IN (select entity_id from aw_layerednavigation_filter where code LIKE '%size%');
UPDATE aw_layerednavigation_filter_eav SET value = 1 where name = 'is_enabled_in_search' and filter_id IN (select entity_id from aw_layerednavigation_filter where code LIKE '%size%');
UPDATE aw_layerednavigation_filter_eav SET value = 2 where name = 'column_layout' and filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base');
UPDATE aw_layerednavigation_filter_eav SET value = 2 where name = 'column_layout' and filter_id IN (select entity_id from aw_layerednavigation_filter where code LIKE '%size_%');
UPDATE aw_layerednavigation_filter SET image_position = 4 where code = 'colour_base';
UPDATE aw_layerednavigation_filter SET position = 1 where code = 'colour_base';
UPDATE aw_layerednavigation_filter SET display_type = 1 where code LIKE '%size_%';
UPDATE aw_layerednavigation_filter SET display_type = 1 where code = 'category';
UPDATE aw_layerednavigation_filter SET position = 2 where code LIKE '%size_%';
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/black.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/blue.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/brown.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/green.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/grey.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/metallic.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/multi.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/neutral.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/orange.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/pink.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/purple.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/red.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/white.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_option SET image = 'skin/frontend/factoryx/princess/aw_layerednavigation/swatches/yellow.png' where filter_id = (select entity_id from aw_layerednavigation_filter where code = 'colour_base') and image IS NULL LIMIT 1;
UPDATE aw_layerednavigation_filter_eav SET value = 1 where name = 'is_enabled' and filter_id = (select entity_id from aw_layerednavigation_filter where code = 'price');
UPDATE aw_layerednavigation_filter_eav SET value = 1 where name = 'is_enabled_in_search' and filter_id = (select entity_id from aw_layerednavigation_filter where code = 'price');
UPDATE aw_layerednavigation_filter_eav SET value = 1 where name = 'is_enabled' and filter_id = (select entity_id from aw_layerednavigation_filter where code = 'category');
UPDATE aw_layerednavigation_filter_eav SET value = 1 where name = 'is_enabled_in_search' and filter_id = (select entity_id from aw_layerednavigation_filter where code = 'category');
");

$installer->endSetup();