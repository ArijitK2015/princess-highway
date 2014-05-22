SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `catalog_product_bundle_option`;
TRUNCATE TABLE `catalog_product_bundle_option_value`;
TRUNCATE TABLE `catalog_product_bundle_selection`;
TRUNCATE TABLE `catalog_product_entity_datetime`;
TRUNCATE TABLE `catalog_product_entity_decimal`;
TRUNCATE TABLE `catalog_product_entity_gallery`;
TRUNCATE TABLE `catalog_product_entity_int`;
TRUNCATE TABLE `catalog_product_entity_media_gallery`;
TRUNCATE TABLE `catalog_product_entity_media_gallery_value`;
TRUNCATE TABLE `catalog_product_entity_text`;
TRUNCATE TABLE `catalog_product_entity_tier_price`;
TRUNCATE TABLE `catalog_product_entity_varchar`;
TRUNCATE TABLE `catalog_product_link`;
TRUNCATE TABLE `catalog_product_link_attribute`;
TRUNCATE TABLE `catalog_product_link_attribute_decimal`;
TRUNCATE TABLE `catalog_product_link_attribute_int`;
TRUNCATE TABLE `catalog_product_link_attribute_varchar`;
TRUNCATE TABLE `catalog_product_link_type`;
TRUNCATE TABLE `catalog_product_option`;
TRUNCATE TABLE `catalog_product_option_price`;
TRUNCATE TABLE `catalog_product_option_title`;
TRUNCATE TABLE `catalog_product_option_type_price`;
TRUNCATE TABLE `catalog_product_option_type_title`;
TRUNCATE TABLE `catalog_product_option_type_value`;
TRUNCATE TABLE `catalog_product_super_attribute`;
TRUNCATE TABLE `catalog_product_super_attribute_label`;
TRUNCATE TABLE `catalog_product_super_attribute_pricing`;
TRUNCATE TABLE `catalog_product_super_link`;
TRUNCATE TABLE `catalog_product_enabled_index`;
TRUNCATE TABLE `catalog_product_website`;
TRUNCATE TABLE `catalog_product_entity`;

TRUNCATE TABLE `cataloginventory_stock`;
TRUNCATE TABLE `cataloginventory_stock_item`;
TRUNCATE TABLE `cataloginventory_stock_status`;

TRUNCATE TABLE `catalog_category_entity`;
TRUNCATE TABLE `catalog_category_entity_datetime`;
TRUNCATE TABLE `catalog_category_entity_decimal`;
TRUNCATE TABLE `catalog_category_entity_int`;
TRUNCATE TABLE `catalog_category_entity_text`;
TRUNCATE TABLE `catalog_category_entity_varchar`;
TRUNCATE TABLE `catalog_category_product`;
TRUNCATE TABLE `catalog_category_product_index`;

INSERT INTO `catalog_category_entity` (`entity_id`, `entity_type_id`, `attribute_set_id`, `parent_id`, `created_at`, `updated_at`, `path`, `position`, `level`, `children_count`) VALUES (1, 3, 0, 0, '2012-06-17 22:20:47', '2012-06-17 22:20:47', '1', 0, 0, 1), (2, 3, 3, 1, '2012-06-17 22:20:47', '2012-06-17 22:20:47', '1/2', 1, 1, 0);
INSERT INTO `catalog_category_entity_int` (`value_id`, `entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`) VALUES (1, 3, 67, 0, 1, 1), (2, 3, 67, 1, 1, 1), (3, 3, 42, 0, 2, 1), (4, 3, 67, 0, 2, 1), (5, 3, 42, 1, 2, 1), (6, 3, 67, 1, 2, 1);
INSERT INTO `catalog_category_entity_varchar` (`value_id`, `entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`) VALUES (1, 3, 41, 0, 1, 'Root Catalog'), (2, 3, 41, 1, 1, 'Root Catalog'), (3, 3, 43, 1, 1, 'root-catalog'), (4, 3, 41, 0, 2, 'Default Category'), (5, 3, 41, 1, 2, 'Default Category'), (6, 3, 49, 1, 2, 'PRODUCTS'), (7, 3, 43, 1, 2, 'default-category');

INSERT INTO `catalog_product_link_type` (`link_type_id`, `code`) VALUES (1, 'relation'), (3, 'super'), (4, 'up_sell'), (5, 'cross_sell');
INSERT INTO `catalog_product_link_attribute` (`product_link_attribute_id`, `link_type_id`, `product_link_attribute_code`, `data_type`) VALUES (1, 1, 'position', 'int'), (2, 3, 'position', 'int'), (3, 3, 'qty', 'decimal'), (4, 4, 'position', 'int'), (5, 5, 'position', 'int');
INSERT INTO `cataloginventory_stock` (`stock_id`, `stock_name`) VALUES (1, 'Default');

SET FOREIGN_KEY_CHECKS = 1;
