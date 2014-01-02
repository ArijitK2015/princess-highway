/* DF Database name: df_dev (REPLACE THIS STRING WITH YOUR DANGERFIELD DATABASE NAME IN THIS FILE) */
/* Clean Magento 1.8.1.0 Database name: magento_1_8_1_0 (REPLACE THIS STRING WITH YOUR CLEAN MAGENTO DATABASE NAME IN THIS FILE) */

/*
PH
 select * from core_store;
+----------+---------+------------+----------+--------------------+------------+-----------+
| store_id | code    | website_id | group_id | name               | sort_order | is_active |
+----------+---------+------------+----------+--------------------+------------+-----------+
|        0 | admin   |          0 |        0 | Admin              |          0 |         1 |
|        1 | default |          1 |        1 | Default Store View |          0 |         1 |
+----------+---------+------------+----------+--------------------+------------+-----------+

select * from core_website;
+------------+-------+--------------+------------+------------------+------------+
| website_id | code  | name         | sort_order | default_group_id | is_default |
+------------+-------+--------------+------------+------------------+------------+
|          0 | admin | Admin        |          0 |                0 |          0 |
|          1 | base  | Main Website |          0 |                1 |          1 |
+------------+-------+--------------+------------+------------------+------------+

select * from core_store_group;
+----------+------------+--------------------+------------------+------------------+
| group_id | website_id | name               | root_category_id | default_store_id |
+----------+------------+--------------------+------------------+------------------+
|        0 |          0 | Default            |                0 |                0 |
|        1 |          1 | Main Website Store |                2 |                1 |
+----------+------------+--------------------+------------------+------------------+

DF

 select * from core_store;
+----------+---------+------------+----------+---------+------------+-----------+
| store_id | code    | website_id | group_id | name    | sort_order | is_active |
+----------+---------+------------+----------+---------+------------+-----------+
|        0 | admin   |          0 |        0 | Admin   |          0 |         1 |
|        1 | default |          1 |        1 | English |          0 |         0 |
|        5 | girls   |          3 |        3 | Girls   |          0 |         1 |
|        6 | boys    |          3 |        4 | Boys    |          0 |         1 |
|        7 | women   |          4 |        5 | Women   |          0 |         1 |
|        8 | men     |          4 |        6 | Men     |          0 |         1 |
+----------+---------+------------+----------+---------+------------+-----------+

select * from core_website;
+------------+-------------+--------------------+------------+------------------+------------+
| website_id | code        | name               | sort_order | default_group_id | is_default |
+------------+-------------+--------------------+------------+------------------+------------+
|          0 | admin       | Admin              |          0 |                0 |          0 |
|          1 | base        | Main Website       |          0 |                1 |          1 |
|          3 | dangerfield | Dangerfield Online |          2 |                3 |          0 |
|          4 | pulpkitchen | Pulp Kitchen       |          0 |                5 |          0 |
+------------+-------------+--------------------+------------+------------------+------------+

select * from core_store_group;
+----------+------------+--------------------+------------------+------------------+
| group_id | website_id | name               | root_category_id | default_store_id |
+----------+------------+--------------------+------------------+------------------+
|        0 |          0 | Default            |                0 |                0 |
|        1 |          1 | Dangerfield Online |                3 |                1 |
|        3 |          3 | Girls              |               36 |                5 |
|        4 |          3 | Boys               |               35 |                6 |
|        5 |          4 | Women              |              168 |                7 |
|        6 |          4 | Men                |              167 |                8 |
+----------+------------+--------------------+------------------+------------------+

*/

/* Dangerfield variables */
SET @DF_WEBSITE = 3;
SET @DF_STORE = 5;
SET @DF_STORE_GROUP = 3;
/* Princess Highway variables */
SET @PH_WEBSITE = 1;
SET @PH_STORE = 1;
SET @PH_STORE_GROUP = 1;

/* Start */
SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;
SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;
SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;
SET NAMES utf8;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0;
/* Drop Tables */
DROP TABLE IF EXISTS catalog_category_anc_categs_index_idx;
DROP TABLE IF EXISTS catalog_category_anc_categs_index_tmp;
DROP TABLE IF EXISTS catalog_category_anc_products_index_idx;
DROP TABLE IF EXISTS catalog_category_anc_products_index_tmp;
DROP TABLE IF EXISTS catalog_category_entity;
DROP TABLE IF EXISTS catalog_category_entity_datetime;
DROP TABLE IF EXISTS catalog_category_entity_decimal;
DROP TABLE IF EXISTS catalog_category_entity_int;
DROP TABLE IF EXISTS catalog_category_entity_text;	
DROP TABLE IF EXISTS catalog_category_entity_varchar;
DROP TABLE IF EXISTS catalog_category_product;
DROP TABLE IF EXISTS catalog_category_product_index	;
DROP TABLE IF EXISTS catalog_category_product_index_enbl_idx;
DROP TABLE IF EXISTS catalog_category_product_index_enbl_tmp;
DROP TABLE IF EXISTS catalog_category_product_index_idx	;
DROP TABLE IF EXISTS catalog_category_product_index_tmp	;
DROP TABLE IF EXISTS catalog_compare_item;
DROP TABLE IF EXISTS catalog_eav_attribute;
DROP TABLE IF EXISTS catalog_product_bundle_option;
DROP TABLE IF EXISTS catalog_product_bundle_option_value;
DROP TABLE IF EXISTS catalog_product_bundle_price_index	;
DROP TABLE IF EXISTS catalog_product_bundle_selection;
DROP TABLE IF EXISTS catalog_product_bundle_selection_price	;
DROP TABLE IF EXISTS catalog_product_bundle_stock_index	;
DROP TABLE IF EXISTS catalog_product_enabled_index	;
DROP TABLE IF EXISTS catalog_product_entity;
DROP TABLE IF EXISTS catalog_product_entity_datetime;
DROP TABLE IF EXISTS catalog_product_entity_decimal;
DROP TABLE IF EXISTS catalog_product_entity_gallery;	
DROP TABLE IF EXISTS catalog_product_entity_group_price	;
DROP TABLE IF EXISTS catalog_product_entity_int	;
DROP TABLE IF EXISTS catalog_product_entity_media_gallery;
DROP TABLE IF EXISTS catalog_product_entity_media_gallery_value	;
DROP TABLE IF EXISTS catalog_product_entity_text;
DROP TABLE IF EXISTS catalog_product_entity_tier_price;
DROP TABLE IF EXISTS catalog_product_entity_varchar;
DROP TABLE IF EXISTS catalog_product_index_eav;
DROP TABLE IF EXISTS catalog_product_index_eav_decimal;
DROP TABLE IF EXISTS catalog_product_index_eav_decimal_idx;
DROP TABLE IF EXISTS catalog_product_index_eav_decimal_tmp;	
DROP TABLE IF EXISTS catalog_product_index_eav_idx;
DROP TABLE IF EXISTS catalog_product_index_eav_tmp;
DROP TABLE IF EXISTS catalog_product_index_group_price;
DROP TABLE IF EXISTS catalog_product_index_price;
DROP TABLE IF EXISTS catalog_product_index_price_bundle_idx	;
DROP TABLE IF EXISTS catalog_product_index_price_bundle_opt_idx	;
DROP TABLE IF EXISTS catalog_product_index_price_bundle_opt_tmp	;
DROP TABLE IF EXISTS catalog_product_index_price_bundle_sel_idx	;
DROP TABLE IF EXISTS catalog_product_index_price_bundle_sel_tmp	;
DROP TABLE IF EXISTS catalog_product_index_price_bundle_tmp;
DROP TABLE IF EXISTS catalog_product_index_price_cfg_opt_agr_idx;
DROP TABLE IF EXISTS catalog_product_index_price_cfg_opt_agr_tmp;
DROP TABLE IF EXISTS catalog_product_index_price_cfg_opt_idx;
DROP TABLE IF EXISTS catalog_product_index_price_cfg_opt_tmp;	
DROP TABLE IF EXISTS catalog_product_index_price_downlod_idx;	
DROP TABLE IF EXISTS catalog_product_index_price_downlod_tmp;	
DROP TABLE IF EXISTS catalog_product_index_price_final_idx;
DROP TABLE IF EXISTS catalog_product_index_price_final_tmp;
DROP TABLE IF EXISTS catalog_product_index_price_idx;
DROP TABLE IF EXISTS catalog_product_index_price_opt_agr_idx;
DROP TABLE IF EXISTS catalog_product_index_price_opt_agr_tmp;	
DROP TABLE IF EXISTS catalog_product_index_price_opt_idx;
DROP TABLE IF EXISTS catalog_product_index_price_opt_tmp;
DROP TABLE IF EXISTS catalog_product_index_price_tmp;
DROP TABLE IF EXISTS catalog_product_index_tier_price; 
DROP TABLE IF EXISTS catalog_product_index_website ;
DROP TABLE IF EXISTS catalog_product_link;
DROP TABLE IF EXISTS catalog_product_link_attribute;
DROP TABLE IF EXISTS catalog_product_link_attribute_decimal;
DROP TABLE IF EXISTS catalog_product_link_attribute_int;
DROP TABLE IF EXISTS catalog_product_link_attribute_varchar;
DROP TABLE IF EXISTS catalog_product_link_type;
DROP TABLE IF EXISTS catalog_product_option;
DROP TABLE IF EXISTS catalog_product_option_price;
DROP TABLE IF EXISTS catalog_product_option_title;	
DROP TABLE IF EXISTS catalog_product_option_type_price;
DROP TABLE IF EXISTS catalog_product_option_type_title;	
DROP TABLE IF EXISTS catalog_product_option_type_value;
DROP TABLE IF EXISTS catalog_product_relation;
DROP TABLE IF EXISTS catalog_product_super_attribute;
DROP TABLE IF EXISTS catalog_product_super_attribute_label;
DROP TABLE IF EXISTS catalog_product_super_attribute_pricing;
DROP TABLE IF EXISTS catalog_product_super_link;
DROP TABLE IF EXISTS catalog_product_website;
DROP TABLE IF EXISTS cataloginventory_stock;
DROP TABLE IF EXISTS cataloginventory_stock_item;
DROP TABLE IF EXISTS cataloginventory_stock_status;
DROP TABLE IF EXISTS cataloginventory_stock_status_idx;
DROP TABLE IF EXISTS cataloginventory_stock_status_tmp;
DROP TABLE IF EXISTS core_url_rewrite;
DROP TABLE IF EXISTS customer_eav_attribute;
DROP TABLE IF EXISTS customer_eav_attribute_website;
DROP TABLE IF EXISTS customer_form_attribute;
DROP TABLE IF EXISTS eav_attribute;
DROP TABLE IF EXISTS eav_attribute_group;
DROP TABLE IF EXISTS eav_attribute_label;	
DROP TABLE IF EXISTS eav_attribute_option;
DROP TABLE IF EXISTS eav_attribute_option_value;
DROP TABLE IF EXISTS eav_attribute_set;
DROP TABLE IF EXISTS eav_entity;
DROP TABLE IF EXISTS eav_entity_attribute;
DROP TABLE IF EXISTS eav_entity_datetime;
DROP TABLE IF EXISTS eav_entity_decimal	;
DROP TABLE IF EXISTS eav_entity_int	;
DROP TABLE IF EXISTS eav_entity_store;	
DROP TABLE IF EXISTS eav_entity_text;
DROP TABLE IF EXISTS eav_entity_type;
DROP TABLE IF EXISTS eav_entity_varchar	;
DROP TABLE IF EXISTS eav_form_element;
DROP TABLE IF EXISTS eav_form_fieldset;
DROP TABLE IF EXISTS eav_form_fieldset_label;
DROP TABLE IF EXISTS eav_form_type	;
DROP TABLE IF EXISTS eav_form_type_entity;
/* End Drop tables */

/* Truncate Flat Tables (automatically rebuilt when reindexing) */
TRUNCATE catalog_category_flat_store_1;
TRUNCATE catalog_product_flat_1;
/* End Truncate Flat Tables */

/* Create tables (Better to create those tables from original 1.8.1.0 clean database) */
CREATE TABLE catalog_category_anc_categs_index_idx LIKE magento_1_8_1_0.catalog_category_anc_categs_index_idx;
CREATE TABLE catalog_category_anc_categs_index_tmp LIKE magento_1_8_1_0.catalog_category_anc_categs_index_tmp;
CREATE TABLE catalog_category_anc_products_index_idx LIKE magento_1_8_1_0.catalog_category_anc_products_index_idx;
CREATE TABLE catalog_category_anc_products_index_tmp LIKE magento_1_8_1_0.catalog_category_anc_products_index_tmp;
CREATE TABLE catalog_category_entity LIKE magento_1_8_1_0.catalog_category_entity;
CREATE TABLE catalog_category_entity_datetime LIKE magento_1_8_1_0.catalog_category_entity_datetime;
CREATE TABLE catalog_category_entity_decimal LIKE magento_1_8_1_0.catalog_category_entity_decimal;
CREATE TABLE catalog_category_entity_int LIKE magento_1_8_1_0.catalog_category_entity_int;
CREATE TABLE catalog_category_entity_text LIKE magento_1_8_1_0.catalog_category_entity_text;
CREATE TABLE catalog_category_entity_varchar LIKE magento_1_8_1_0.catalog_category_entity_varchar;
CREATE TABLE catalog_category_product LIKE magento_1_8_1_0.catalog_category_product;
CREATE TABLE catalog_category_product_index LIKE magento_1_8_1_0.catalog_category_product_index;
CREATE TABLE catalog_category_product_index_enbl_idx LIKE magento_1_8_1_0.catalog_category_product_index_enbl_idx;
CREATE TABLE catalog_category_product_index_enbl_tmp LIKE magento_1_8_1_0.catalog_category_product_index_enbl_tmp;
CREATE TABLE catalog_category_product_index_idx LIKE magento_1_8_1_0.catalog_category_product_index_idx;
CREATE TABLE catalog_category_product_index_tmp LIKE magento_1_8_1_0.catalog_category_product_index_tmp;
CREATE TABLE catalog_compare_item LIKE magento_1_8_1_0.catalog_compare_item;
CREATE TABLE catalog_eav_attribute LIKE magento_1_8_1_0.catalog_eav_attribute;
CREATE TABLE catalog_product_bundle_option LIKE magento_1_8_1_0.catalog_product_bundle_option;
CREATE TABLE catalog_product_bundle_option_value LIKE magento_1_8_1_0.catalog_product_bundle_option_value;
CREATE TABLE catalog_product_bundle_price_index LIKE magento_1_8_1_0.catalog_product_bundle_price_index;
CREATE TABLE catalog_product_bundle_selection LIKE magento_1_8_1_0.catalog_product_bundle_selection;
CREATE TABLE catalog_product_bundle_selection_price LIKE magento_1_8_1_0.catalog_product_bundle_selection_price;
CREATE TABLE catalog_product_bundle_stock_index LIKE magento_1_8_1_0.catalog_product_bundle_stock_index;
CREATE TABLE catalog_product_enabled_index LIKE magento_1_8_1_0.catalog_product_enabled_index;
CREATE TABLE catalog_product_entity LIKE magento_1_8_1_0.catalog_product_entity;
CREATE TABLE catalog_product_entity_datetime LIKE magento_1_8_1_0.catalog_product_entity_datetime;
CREATE TABLE catalog_product_entity_decimal LIKE magento_1_8_1_0.catalog_product_entity_decimal;
CREATE TABLE catalog_product_entity_gallery LIKE magento_1_8_1_0.catalog_product_entity_gallery;
CREATE TABLE catalog_product_entity_group_price LIKE magento_1_8_1_0.catalog_product_entity_group_price;
CREATE TABLE catalog_product_entity_int LIKE magento_1_8_1_0.catalog_product_entity_int;
CREATE TABLE catalog_product_entity_media_gallery LIKE magento_1_8_1_0.catalog_product_entity_media_gallery;
CREATE TABLE catalog_product_entity_media_gallery_value LIKE magento_1_8_1_0.catalog_product_entity_media_gallery_value;
CREATE TABLE catalog_product_entity_text LIKE magento_1_8_1_0.catalog_product_entity_text;
CREATE TABLE catalog_product_entity_tier_price LIKE magento_1_8_1_0.catalog_product_entity_tier_price;
CREATE TABLE catalog_product_entity_varchar LIKE magento_1_8_1_0.catalog_product_entity_varchar;
CREATE TABLE catalog_product_index_eav LIKE magento_1_8_1_0.catalog_product_index_eav;
CREATE TABLE catalog_product_index_eav_decimal LIKE magento_1_8_1_0.catalog_product_index_eav_decimal;
CREATE TABLE catalog_product_index_eav_decimal_idx LIKE magento_1_8_1_0.catalog_product_index_eav_decimal_idx;
CREATE TABLE catalog_product_index_eav_decimal_tmp LIKE magento_1_8_1_0.catalog_product_index_eav_decimal_tmp;
CREATE TABLE catalog_product_index_eav_idx LIKE magento_1_8_1_0.catalog_product_index_eav_idx;
CREATE TABLE catalog_product_index_eav_tmp LIKE magento_1_8_1_0.catalog_product_index_eav_tmp;
CREATE TABLE catalog_product_index_group_price LIKE magento_1_8_1_0.catalog_product_index_group_price;
CREATE TABLE catalog_product_index_price LIKE magento_1_8_1_0.catalog_product_index_price;
CREATE TABLE catalog_product_index_price_bundle_idx LIKE magento_1_8_1_0.catalog_product_index_price_bundle_idx;
CREATE TABLE catalog_product_index_price_bundle_opt_idx LIKE magento_1_8_1_0.catalog_product_index_price_bundle_opt_idx;
CREATE TABLE catalog_product_index_price_bundle_opt_tmp LIKE magento_1_8_1_0.catalog_product_index_price_bundle_opt_tmp;
CREATE TABLE catalog_product_index_price_bundle_sel_idx LIKE magento_1_8_1_0.catalog_product_index_price_bundle_sel_idx;
CREATE TABLE catalog_product_index_price_bundle_sel_tmp LIKE magento_1_8_1_0.catalog_product_index_price_bundle_sel_tmp;
CREATE TABLE catalog_product_index_price_bundle_tmp LIKE magento_1_8_1_0.catalog_product_index_price_bundle_tmp;
CREATE TABLE catalog_product_index_price_cfg_opt_agr_idx LIKE magento_1_8_1_0.catalog_product_index_price_cfg_opt_agr_idx;
CREATE TABLE catalog_product_index_price_cfg_opt_agr_tmp LIKE magento_1_8_1_0.catalog_product_index_price_cfg_opt_agr_tmp;
CREATE TABLE catalog_product_index_price_cfg_opt_idx LIKE magento_1_8_1_0.catalog_product_index_price_cfg_opt_idx;
CREATE TABLE catalog_product_index_price_cfg_opt_tmp LIKE magento_1_8_1_0.catalog_product_index_price_cfg_opt_tmp;
CREATE TABLE catalog_product_index_price_downlod_idx LIKE magento_1_8_1_0.catalog_product_index_price_downlod_idx;
CREATE TABLE catalog_product_index_price_downlod_tmp LIKE magento_1_8_1_0.catalog_product_index_price_downlod_tmp;
CREATE TABLE catalog_product_index_price_final_idx LIKE magento_1_8_1_0.catalog_product_index_price_final_idx;
CREATE TABLE catalog_product_index_price_final_tmp LIKE magento_1_8_1_0.catalog_product_index_price_final_tmp;
CREATE TABLE catalog_product_index_price_idx LIKE magento_1_8_1_0.catalog_product_index_price_idx;
CREATE TABLE catalog_product_index_price_opt_agr_idx LIKE magento_1_8_1_0.catalog_product_index_price_opt_agr_idx;
CREATE TABLE catalog_product_index_price_opt_agr_tmp LIKE magento_1_8_1_0.catalog_product_index_price_opt_agr_tmp;
CREATE TABLE catalog_product_index_price_opt_idx LIKE magento_1_8_1_0.catalog_product_index_price_opt_idx;
CREATE TABLE catalog_product_index_price_opt_tmp LIKE magento_1_8_1_0.catalog_product_index_price_opt_tmp;
CREATE TABLE catalog_product_index_price_tmp LIKE magento_1_8_1_0.catalog_product_index_price_tmp;
CREATE TABLE catalog_product_index_tier_price LIKE magento_1_8_1_0.catalog_product_index_tier_price;
CREATE TABLE catalog_product_index_website LIKE magento_1_8_1_0.catalog_product_index_website;
CREATE TABLE catalog_product_link LIKE magento_1_8_1_0.catalog_product_link;
CREATE TABLE catalog_product_link_attribute LIKE magento_1_8_1_0.catalog_product_link_attribute;
CREATE TABLE catalog_product_link_attribute_decimal LIKE magento_1_8_1_0.catalog_product_link_attribute_decimal;
CREATE TABLE catalog_product_link_attribute_int LIKE magento_1_8_1_0.catalog_product_link_attribute_int;
CREATE TABLE catalog_product_link_attribute_varchar LIKE magento_1_8_1_0.catalog_product_link_attribute_varchar;
CREATE TABLE catalog_product_link_type LIKE magento_1_8_1_0.catalog_product_link_type;
CREATE TABLE catalog_product_option LIKE magento_1_8_1_0.catalog_product_option;
CREATE TABLE catalog_product_option_price LIKE magento_1_8_1_0.catalog_product_option_price;
CREATE TABLE catalog_product_option_title LIKE magento_1_8_1_0.catalog_product_option_title;
CREATE TABLE catalog_product_option_type_price LIKE magento_1_8_1_0.catalog_product_option_type_price;
CREATE TABLE catalog_product_option_type_title LIKE magento_1_8_1_0.catalog_product_option_type_title;
CREATE TABLE catalog_product_option_type_value LIKE magento_1_8_1_0.catalog_product_option_type_value;
CREATE TABLE catalog_product_relation LIKE magento_1_8_1_0.catalog_product_relation;
CREATE TABLE catalog_product_super_attribute LIKE magento_1_8_1_0.catalog_product_super_attribute;
CREATE TABLE catalog_product_super_attribute_label LIKE magento_1_8_1_0.catalog_product_super_attribute_label;
CREATE TABLE catalog_product_super_attribute_pricing LIKE magento_1_8_1_0.catalog_product_super_attribute_pricing;
CREATE TABLE catalog_product_super_link LIKE magento_1_8_1_0.catalog_product_super_link;
CREATE TABLE catalog_product_website LIKE magento_1_8_1_0.catalog_product_website;
CREATE TABLE cataloginventory_stock LIKE magento_1_8_1_0.cataloginventory_stock;
CREATE TABLE cataloginventory_stock_item LIKE magento_1_8_1_0.cataloginventory_stock_item;
CREATE TABLE cataloginventory_stock_status LIKE magento_1_8_1_0.cataloginventory_stock_status;
CREATE TABLE cataloginventory_stock_status_idx LIKE magento_1_8_1_0.cataloginventory_stock_status_idx;
CREATE TABLE cataloginventory_stock_status_tmp LIKE magento_1_8_1_0.cataloginventory_stock_status_tmp;
CREATE TABLE core_url_rewrite LIKE magento_1_8_1_0.core_url_rewrite;
CREATE TABLE customer_eav_attribute LIKE magento_1_8_1_0.customer_eav_attribute;
CREATE TABLE customer_eav_attribute_website LIKE magento_1_8_1_0.customer_eav_attribute_website;
CREATE TABLE customer_form_attribute LIKE magento_1_8_1_0.customer_form_attribute;
CREATE TABLE eav_attribute LIKE magento_1_8_1_0.eav_attribute;
CREATE TABLE eav_attribute_group LIKE magento_1_8_1_0.eav_attribute_group;
CREATE TABLE eav_attribute_label LIKE magento_1_8_1_0.eav_attribute_label;
CREATE TABLE eav_attribute_option LIKE magento_1_8_1_0.eav_attribute_option;
CREATE TABLE eav_attribute_option_value	LIKE magento_1_8_1_0.eav_attribute_option_value;
CREATE TABLE eav_attribute_set LIKE magento_1_8_1_0.eav_attribute_set;
CREATE TABLE eav_entity	LIKE magento_1_8_1_0.eav_entity;
CREATE TABLE eav_entity_attribute LIKE magento_1_8_1_0.eav_entity_attribute;
CREATE TABLE eav_entity_datetime LIKE magento_1_8_1_0.eav_entity_datetime;
CREATE TABLE eav_entity_decimal	LIKE magento_1_8_1_0.eav_entity_decimal;
CREATE TABLE eav_entity_int LIKE magento_1_8_1_0.eav_entity_int;
CREATE TABLE eav_entity_store LIKE magento_1_8_1_0.eav_entity_store;
CREATE TABLE eav_entity_text LIKE magento_1_8_1_0.eav_entity_text;
CREATE TABLE eav_entity_type LIKE magento_1_8_1_0.eav_entity_type;
CREATE TABLE eav_entity_varchar LIKE magento_1_8_1_0.eav_entity_varchar;
CREATE TABLE eav_form_element LIKE magento_1_8_1_0.eav_form_element;
CREATE TABLE eav_form_fieldset LIKE magento_1_8_1_0.eav_form_fieldset;
CREATE TABLE eav_form_fieldset_label LIKE magento_1_8_1_0.eav_form_fieldset_label;
CREATE TABLE eav_form_type LIKE magento_1_8_1_0.eav_form_type;
CREATE TABLE eav_form_type_entity LIKE magento_1_8_1_0.eav_form_type_entity;
/* End Create Tables */

/* Update Tables Structure (SeoSuite module) */
ALTER TABLE catalog_eav_attribute ADD layered_navigation_canonical TINYINT(1) UNSIGNED NOT NULL DEFAULT '0';
/* End Update Tables Structure (SeoSuite module) */

/* Update Tables Structure (AttributeOptionPro module) */
ALTER TABLE eav_attribute_option ADD image TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE eav_attribute_option ADD additional_image TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE catalog_eav_attribute ADD image TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
/* End Update Tables Structure (AttributeOptionPro module) */

/* Insert Data */
INSERT INTO catalog_category_anc_categs_index_idx SELECT * FROM df_dev.catalog_category_anc_categs_index_idx;
INSERT INTO catalog_category_anc_categs_index_tmp SELECT * FROM df_dev.catalog_category_anc_categs_index_tmp;
INSERT INTO catalog_category_anc_products_index_idx SELECT * FROM df_dev.catalog_category_anc_products_index_idx;
INSERT INTO catalog_category_anc_products_index_tmp SELECT * FROM df_dev.catalog_category_anc_products_index_tmp;
INSERT INTO catalog_category_entity SELECT * FROM df_dev.catalog_category_entity;
INSERT INTO catalog_category_entity_datetime SELECT * FROM df_dev.catalog_category_entity_datetime WHERE store_id = @DF_STORE;
INSERT INTO catalog_category_entity_datetime SELECT * FROM df_dev.catalog_category_entity_datetime WHERE store_id = 0;
INSERT INTO catalog_category_entity_decimal SELECT * FROM df_dev.catalog_category_entity_decimal WHERE store_id = @DF_STORE;
INSERT INTO catalog_category_entity_decimal SELECT * FROM df_dev.catalog_category_entity_decimal WHERE store_id = 0;
INSERT INTO catalog_category_entity_int SELECT * FROM df_dev.catalog_category_entity_int WHERE store_id = @DF_STORE;
INSERT INTO catalog_category_entity_int SELECT * FROM df_dev.catalog_category_entity_int WHERE store_id = 0;
INSERT INTO catalog_category_entity_text SELECT * FROM df_dev.catalog_category_entity_text WHERE store_id = @DF_STORE;
INSERT INTO catalog_category_entity_text SELECT * FROM df_dev.catalog_category_entity_text WHERE store_id = 0;
INSERT INTO catalog_category_entity_varchar SELECT * FROM df_dev.catalog_category_entity_varchar WHERE store_id = @DF_STORE;
INSERT INTO catalog_category_entity_varchar SELECT * FROM df_dev.catalog_category_entity_varchar WHERE store_id = 0;
INSERT INTO catalog_category_product SELECT * FROM df_dev.catalog_category_product;
INSERT INTO catalog_category_product_index SELECT * FROM df_dev.catalog_category_product_index WHERE store_id = @DF_STORE;
INSERT INTO catalog_category_product_index SELECT * FROM df_dev.catalog_category_product_index WHERE store_id = 0;
INSERT INTO catalog_category_product_index_enbl_idx SELECT * FROM df_dev.catalog_category_product_index_enbl_idx;
INSERT INTO catalog_category_product_index_enbl_tmp SELECT * FROM df_dev.catalog_category_product_index_enbl_tmp;
INSERT INTO catalog_category_product_index_idx SELECT * FROM df_dev.catalog_category_product_index_idx WHERE store_id = @DF_STORE;
INSERT INTO catalog_category_product_index_idx SELECT * FROM df_dev.catalog_category_product_index_idx WHERE store_id = 0;
INSERT INTO catalog_category_product_index_tmp SELECT * FROM df_dev.catalog_category_product_index_tmp WHERE store_id = @DF_STORE;
INSERT INTO catalog_category_product_index_tmp SELECT * FROM df_dev.catalog_category_product_index_tmp WHERE store_id = 0;
INSERT INTO catalog_compare_item SELECT * FROM df_dev.catalog_compare_item WHERE store_id = @DF_STORE;
INSERT INTO catalog_compare_item SELECT * FROM df_dev.catalog_compare_item WHERE store_id = 0;
INSERT INTO catalog_eav_attribute SELECT * FROM df_dev.catalog_eav_attribute;
INSERT INTO catalog_product_bundle_option SELECT * FROM df_dev.catalog_product_bundle_option;
INSERT INTO catalog_product_bundle_option_value SELECT * FROM df_dev.catalog_product_bundle_option_value WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_bundle_option_value SELECT * FROM df_dev.catalog_product_bundle_option_value WHERE store_id = 0;
INSERT INTO catalog_product_bundle_price_index SELECT * FROM df_dev.catalog_product_bundle_price_index WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_bundle_price_index SELECT * FROM df_dev.catalog_product_bundle_price_index WHERE website_id = 0;
INSERT INTO catalog_product_bundle_selection SELECT * FROM df_dev.catalog_product_bundle_selection;
INSERT INTO catalog_product_bundle_selection_price SELECT * FROM df_dev.catalog_product_bundle_selection_price WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_bundle_selection_price SELECT * FROM df_dev.catalog_product_bundle_selection_price WHERE website_id = 0;
INSERT INTO catalog_product_bundle_stock_index SELECT * FROM df_dev.catalog_product_bundle_stock_index WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_bundle_stock_index SELECT * FROM df_dev.catalog_product_bundle_stock_index WHERE website_id = 0;
INSERT INTO catalog_product_enabled_index SELECT * FROM df_dev.catalog_product_enabled_index WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_enabled_index SELECT * FROM df_dev.catalog_product_enabled_index WHERE store_id = 0;
/* Custom Insert due to DF database column order being corrupted */
INSERT INTO catalog_product_entity (entity_id,entity_type_id,attribute_set_id,type_id,sku,has_options,required_options,created_at,updated_at) SELECT entity_id, entity_type_id, attribute_set_id, type_id, sku, has_options, required_options, created_at, updated_at FROM df_dev.catalog_product_entity;
INSERT INTO catalog_product_entity_datetime SELECT * FROM df_dev.catalog_product_entity_datetime WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_entity_datetime SELECT * FROM df_dev.catalog_product_entity_datetime WHERE store_id = 0;
INSERT INTO catalog_product_entity_decimal SELECT * FROM df_dev.catalog_product_entity_decimal WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_entity_decimal SELECT * FROM df_dev.catalog_product_entity_decimal WHERE store_id = 0;
INSERT INTO catalog_product_entity_gallery SELECT * FROM df_dev.catalog_product_entity_gallery WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_entity_gallery SELECT * FROM df_dev.catalog_product_entity_gallery WHERE store_id = 0;
INSERT INTO catalog_product_entity_group_price SELECT * FROM df_dev.catalog_product_entity_group_price WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_entity_group_price SELECT * FROM df_dev.catalog_product_entity_group_price WHERE website_id = 0;
INSERT INTO catalog_product_entity_int	 SELECT * FROM df_dev.catalog_product_entity_int WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_entity_int	 SELECT * FROM df_dev.catalog_product_entity_int WHERE store_id = 0;
INSERT INTO catalog_product_entity_media_gallery SELECT * FROM df_dev.catalog_product_entity_media_gallery;
INSERT INTO catalog_product_entity_media_gallery_value SELECT * FROM df_dev.catalog_product_entity_media_gallery_value WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_entity_media_gallery_value SELECT * FROM df_dev.catalog_product_entity_media_gallery_value WHERE store_id = 0;
INSERT INTO catalog_product_entity_text SELECT * FROM df_dev.catalog_product_entity_text WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_entity_text SELECT * FROM df_dev.catalog_product_entity_text WHERE store_id = 0;
INSERT INTO catalog_product_entity_tier_price SELECT * FROM df_dev.catalog_product_entity_tier_price WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_entity_tier_price SELECT * FROM df_dev.catalog_product_entity_tier_price WHERE website_id = 0;
INSERT INTO catalog_product_entity_varchar SELECT * FROM df_dev.catalog_product_entity_varchar WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_entity_varchar SELECT * FROM df_dev.catalog_product_entity_varchar WHERE store_id = 0;
INSERT INTO catalog_product_index_eav SELECT * FROM df_dev.catalog_product_index_eav WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_index_eav SELECT * FROM df_dev.catalog_product_index_eav WHERE store_id = 0;
INSERT INTO catalog_product_index_eav_decimal SELECT * FROM df_dev.catalog_product_index_eav_decimal WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_index_eav_decimal SELECT * FROM df_dev.catalog_product_index_eav_decimal WHERE store_id = 0;
INSERT INTO catalog_product_index_eav_decimal_idx SELECT * FROM df_dev.catalog_product_index_eav_decimal_idx WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_index_eav_decimal_idx SELECT * FROM df_dev.catalog_product_index_eav_decimal_idx WHERE store_id = 0;
INSERT INTO catalog_product_index_eav_decimal_tmp SELECT * FROM df_dev.catalog_product_index_eav_decimal_tmp WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_index_eav_decimal_tmp SELECT * FROM df_dev.catalog_product_index_eav_decimal_tmp WHERE store_id = 0;
INSERT INTO catalog_product_index_eav_idx SELECT * FROM df_dev.catalog_product_index_eav_idx WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_index_eav_idx SELECT * FROM df_dev.catalog_product_index_eav_idx WHERE store_id = 0;
INSERT INTO catalog_product_index_eav_tmp SELECT * FROM df_dev.catalog_product_index_eav_tmp WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_index_eav_tmp SELECT * FROM df_dev.catalog_product_index_eav_tmp WHERE store_id = 0;
INSERT INTO catalog_product_index_group_price SELECT * FROM df_dev.catalog_product_index_group_price WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_group_price SELECT * FROM df_dev.catalog_product_index_group_price WHERE website_id = 0;
INSERT INTO catalog_product_index_price SELECT * FROM df_dev.catalog_product_index_price WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price SELECT * FROM df_dev.catalog_product_index_price WHERE website_id = 0;
INSERT INTO catalog_product_index_price_bundle_idx SELECT * FROM df_dev.catalog_product_index_price_bundle_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_bundle_idx SELECT * FROM df_dev.catalog_product_index_price_bundle_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_bundle_opt_idx SELECT * FROM df_dev.catalog_product_index_price_bundle_opt_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_bundle_opt_idx SELECT * FROM df_dev.catalog_product_index_price_bundle_opt_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_bundle_opt_tmp SELECT * FROM df_dev.catalog_product_index_price_bundle_opt_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_bundle_opt_tmp SELECT * FROM df_dev.catalog_product_index_price_bundle_opt_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_bundle_sel_idx SELECT * FROM df_dev.catalog_product_index_price_bundle_sel_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_bundle_sel_idx SELECT * FROM df_dev.catalog_product_index_price_bundle_sel_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_bundle_sel_tmp SELECT * FROM df_dev.catalog_product_index_price_bundle_sel_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_bundle_sel_tmp SELECT * FROM df_dev.catalog_product_index_price_bundle_sel_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_bundle_tmp SELECT * FROM df_dev.catalog_product_index_price_bundle_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_bundle_tmp SELECT * FROM df_dev.catalog_product_index_price_bundle_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_cfg_opt_agr_idx SELECT * FROM df_dev.catalog_product_index_price_cfg_opt_agr_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_cfg_opt_agr_idx SELECT * FROM df_dev.catalog_product_index_price_cfg_opt_agr_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_cfg_opt_agr_tmp SELECT * FROM df_dev.catalog_product_index_price_cfg_opt_agr_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_cfg_opt_agr_tmp SELECT * FROM df_dev.catalog_product_index_price_cfg_opt_agr_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_cfg_opt_idx SELECT * FROM df_dev.catalog_product_index_price_cfg_opt_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_cfg_opt_idx SELECT * FROM df_dev.catalog_product_index_price_cfg_opt_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_cfg_opt_tmp SELECT * FROM df_dev.catalog_product_index_price_cfg_opt_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_cfg_opt_tmp SELECT * FROM df_dev.catalog_product_index_price_cfg_opt_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_downlod_idx SELECT * FROM df_dev.catalog_product_index_price_downlod_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_downlod_idx SELECT * FROM df_dev.catalog_product_index_price_downlod_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_downlod_tmp SELECT * FROM df_dev.catalog_product_index_price_downlod_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_downlod_tmp SELECT * FROM df_dev.catalog_product_index_price_downlod_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_final_idx SELECT * FROM df_dev.catalog_product_index_price_final_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_final_idx SELECT * FROM df_dev.catalog_product_index_price_final_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_final_tmp SELECT * FROM df_dev.catalog_product_index_price_final_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_final_tmp SELECT * FROM df_dev.catalog_product_index_price_final_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_idx SELECT * FROM df_dev.catalog_product_index_price_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_idx SELECT * FROM df_dev.catalog_product_index_price_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_opt_agr_idx SELECT * FROM df_dev.catalog_product_index_price_opt_agr_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_opt_agr_idx SELECT * FROM df_dev.catalog_product_index_price_opt_agr_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_opt_agr_tmp SELECT * FROM df_dev.catalog_product_index_price_opt_agr_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_opt_agr_tmp SELECT * FROM df_dev.catalog_product_index_price_opt_agr_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_opt_idx SELECT * FROM df_dev.catalog_product_index_price_opt_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_opt_idx SELECT * FROM df_dev.catalog_product_index_price_opt_idx WHERE website_id = 0;
INSERT INTO catalog_product_index_price_opt_tmp SELECT * FROM df_dev.catalog_product_index_price_opt_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_opt_tmp SELECT * FROM df_dev.catalog_product_index_price_opt_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_price_tmp SELECT * FROM df_dev.catalog_product_index_price_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_price_tmp SELECT * FROM df_dev.catalog_product_index_price_tmp WHERE website_id = 0;
INSERT INTO catalog_product_index_tier_price SELECT * FROM df_dev.catalog_product_index_tier_price WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_index_tier_price SELECT * FROM df_dev.catalog_product_index_tier_price WHERE website_id = 0;
INSERT INTO catalog_product_index_website  SELECT * FROM df_dev. catalog_product_index_website;
INSERT INTO catalog_product_link SELECT * FROM df_dev.catalog_product_link;
INSERT INTO catalog_product_link_attribute SELECT * FROM df_dev.catalog_product_link_attribute;
INSERT INTO catalog_product_link_attribute_decimal SELECT * FROM df_dev.catalog_product_link_attribute_decimal;
INSERT INTO catalog_product_link_attribute_int SELECT * FROM df_dev.catalog_product_link_attribute_int;
INSERT INTO catalog_product_link_attribute_varchar SELECT * FROM df_dev.catalog_product_link_attribute_varchar;
INSERT INTO catalog_product_link_type SELECT * FROM df_dev.catalog_product_link_type;
INSERT INTO catalog_product_option SELECT * FROM df_dev.catalog_product_option;
INSERT INTO catalog_product_option_price SELECT * FROM df_dev.catalog_product_option_price WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_option_price SELECT * FROM df_dev.catalog_product_option_price WHERE store_id = 0;
INSERT INTO catalog_product_option_title SELECT * FROM df_dev.catalog_product_option_title WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_option_title SELECT * FROM df_dev.catalog_product_option_title WHERE store_id = 0;
INSERT INTO catalog_product_option_type_price SELECT * FROM df_dev.catalog_product_option_type_price WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_option_type_price SELECT * FROM df_dev.catalog_product_option_type_price WHERE store_id = 0;
INSERT INTO catalog_product_option_type_title SELECT * FROM df_dev.catalog_product_option_type_title WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_option_type_title SELECT * FROM df_dev.catalog_product_option_type_title WHERE store_id = 0;
INSERT INTO catalog_product_option_type_value SELECT * FROM df_dev.catalog_product_option_type_value;
INSERT INTO catalog_product_relation SELECT * FROM df_dev.catalog_product_relation;
INSERT INTO catalog_product_super_attribute SELECT * FROM df_dev.catalog_product_super_attribute;
INSERT INTO catalog_product_super_attribute_label SELECT * FROM df_dev.catalog_product_super_attribute_label WHERE store_id = @DF_STORE;
INSERT INTO catalog_product_super_attribute_label SELECT * FROM df_dev.catalog_product_super_attribute_label WHERE store_id = 0;
INSERT INTO catalog_product_super_attribute_pricing SELECT * FROM df_dev.catalog_product_super_attribute_pricing WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_super_attribute_pricing SELECT * FROM df_dev.catalog_product_super_attribute_pricing WHERE website_id = 0;
INSERT INTO catalog_product_super_link SELECT * FROM df_dev.catalog_product_super_link;
INSERT INTO catalog_product_website SELECT * FROM df_dev.catalog_product_website WHERE website_id = @DF_WEBSITE;
INSERT INTO catalog_product_website SELECT * FROM df_dev.catalog_product_website WHERE website_id = 0;
INSERT INTO cataloginventory_stock SELECT * FROM df_dev.cataloginventory_stock;
INSERT INTO cataloginventory_stock_item SELECT * FROM df_dev.cataloginventory_stock_item;
INSERT INTO cataloginventory_stock_status SELECT * FROM df_dev.cataloginventory_stock_status WHERE website_id = @DF_WEBSITE;
INSERT INTO cataloginventory_stock_status SELECT * FROM df_dev.cataloginventory_stock_status WHERE website_id = 0;
INSERT INTO cataloginventory_stock_status_idx SELECT * FROM df_dev.cataloginventory_stock_status_idx WHERE website_id = @DF_WEBSITE;
INSERT INTO cataloginventory_stock_status_idx SELECT * FROM df_dev.cataloginventory_stock_status_idx WHERE website_id = 0;
INSERT INTO cataloginventory_stock_status_tmp SELECT * FROM df_dev.cataloginventory_stock_status_tmp WHERE website_id = @DF_WEBSITE;
INSERT INTO cataloginventory_stock_status_tmp SELECT * FROM df_dev.cataloginventory_stock_status_tmp WHERE website_id = 0;
/* INSERT INTO core_url_rewrite SELECT * FROM df_dev.core_url_rewrite	WHERE (store_id = @DF_STORE) AND is_system = 1; */
INSERT INTO customer_eav_attribute SELECT * FROM df_dev.customer_eav_attribute;
INSERT INTO customer_eav_attribute_website SELECT * FROM df_dev.customer_eav_attribute_website WHERE website_id = @DF_WEBSITE;
INSERT INTO customer_eav_attribute_website SELECT * FROM df_dev.customer_eav_attribute_website WHERE website_id = 0;
INSERT INTO customer_form_attribute SELECT * FROM df_dev.customer_form_attribute;
INSERT INTO eav_attribute SELECT * FROM df_dev.eav_attribute;
INSERT INTO eav_attribute_group SELECT * FROM df_dev.eav_attribute_group;
INSERT INTO eav_attribute_label SELECT * FROM df_dev.eav_attribute_label WHERE store_id = @DF_STORE;
INSERT INTO eav_attribute_label SELECT * FROM df_dev.eav_attribute_label WHERE store_id = 0;
INSERT INTO eav_attribute_option SELECT * FROM df_dev.eav_attribute_option;
INSERT INTO eav_attribute_option_value SELECT * FROM df_dev.eav_attribute_option_value WHERE store_id = @DF_STORE;
INSERT INTO eav_attribute_option_value SELECT * FROM df_dev.eav_attribute_option_value WHERE store_id = 0;
INSERT INTO eav_attribute_set SELECT * FROM df_dev.eav_attribute_set;
INSERT INTO eav_entity SELECT * FROM df_dev.eav_entity WHERE store_id = @DF_STORE;
INSERT INTO eav_entity SELECT * FROM df_dev.eav_entity WHERE store_id = 0;
INSERT INTO eav_entity_attribute SELECT * FROM df_dev.eav_entity_attribute;
INSERT INTO eav_entity_datetime SELECT * FROM df_dev.eav_entity_datetime WHERE store_id = @DF_STORE;
INSERT INTO eav_entity_datetime SELECT * FROM df_dev.eav_entity_datetime WHERE store_id = 0;
INSERT INTO eav_entity_decimal SELECT * FROM df_dev.eav_entity_decimal WHERE store_id = @DF_STORE;
INSERT INTO eav_entity_decimal SELECT * FROM df_dev.eav_entity_decimal WHERE store_id = 0;
INSERT INTO eav_entity_int SELECT * FROM df_dev.eav_entity_int WHERE store_id = @DF_STORE;
INSERT INTO eav_entity_int SELECT * FROM df_dev.eav_entity_int WHERE store_id = 0;
/* INSERT INTO eav_entity_store SELECT * FROM df_dev.eav_entity_store	WHERE store_id = @DF_STORE; */
INSERT INTO eav_entity_text SELECT * FROM df_dev.eav_entity_text WHERE store_id = @DF_STORE;
INSERT INTO eav_entity_text SELECT * FROM df_dev.eav_entity_text WHERE store_id = 0;
INSERT INTO eav_entity_type SELECT * FROM df_dev.eav_entity_type;
INSERT INTO eav_entity_varchar SELECT * FROM df_dev.eav_entity_varchar WHERE store_id = @DF_STORE;
INSERT INTO eav_entity_varchar SELECT * FROM df_dev.eav_entity_varchar WHERE store_id = 0;
INSERT INTO eav_form_element SELECT * FROM df_dev.eav_form_element;
INSERT INTO eav_form_fieldset SELECT * FROM df_dev.eav_form_fieldset;
INSERT INTO eav_form_fieldset_label SELECT * FROM df_dev.eav_form_fieldset_label WHERE store_id = @DF_STORE;
INSERT INTO eav_form_fieldset_label SELECT * FROM df_dev.eav_form_fieldset_label WHERE store_id = 0;
INSERT INTO eav_form_type SELECT * FROM df_dev.eav_form_type WHERE store_id = @DF_STORE;
INSERT INTO eav_form_type SELECT * FROM df_dev.eav_form_type WHERE store_id = 0;
INSERT INTO eav_form_type_entity SELECT * FROM df_dev.eav_form_type_entity;
/* End Insert Data */

/* Update Stores */
UPDATE catalog_category_entity_datetime SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_category_entity_decimal SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_category_entity_int SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_category_entity_text SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_category_entity_varchar SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_category_product_index SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_category_product_index_idx SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_category_product_index_tmp SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_compare_item SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_bundle_option_value SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_enabled_index SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_entity_datetime SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_entity_decimal SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_entity_gallery SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_entity_int	 SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_entity_media_gallery_value SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_entity_text SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_entity_varchar SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_index_eav SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_index_eav_decimal SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_index_eav_decimal_idx SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_index_eav_decimal_tmp SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_index_eav_idx SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_index_eav_tmp SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_option_price SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_option_title SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_option_type_price SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_option_type_title SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE catalog_product_super_attribute_label SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
/* UPDATE core_url_rewrite SET store_id = @PH_STORE WHERE store_id = @DF_STORE; */
UPDATE eav_attribute_label SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE eav_attribute_option_value SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE eav_entity SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE eav_entity_datetime SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE eav_entity_decimal SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE eav_entity_int SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
/* UPDATE eav_entity_store SET store_id = @PH_STORE WHERE store_id = @DF_STORE; */
UPDATE eav_entity_text SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE eav_entity_varchar SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE eav_form_fieldset_label SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
UPDATE eav_form_type SET store_id = @PH_STORE WHERE store_id = @DF_STORE;
/* End Update Stores */

/* Update Websites */
UPDATE catalog_product_bundle_price_index SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_bundle_selection_price SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_bundle_stock_index SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_entity_group_price SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_entity_tier_price SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_group_price SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_bundle_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_bundle_opt_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_bundle_opt_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_bundle_sel_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_bundle_sel_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_bundle_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_cfg_opt_agr_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_cfg_opt_agr_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_cfg_opt_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_cfg_opt_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_downlod_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_downlod_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_final_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_final_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_opt_agr_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_opt_agr_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_opt_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_opt_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_price_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_index_tier_price SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_super_attribute_pricing SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE catalog_product_website SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE cataloginventory_stock_status SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE cataloginventory_stock_status_idx SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE cataloginventory_stock_status_tmp SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
UPDATE customer_eav_attribute_website SET website_id = @PH_WEBSITE WHERE website_id = @DF_WEBSITE;
/* End Update Websites */

/* Update Root Category ID */
UPDATE core_store_group SET root_category_id = (SELECT root_category_id FROM df_dev.core_store_group WHERE group_id = @DF_STORE_GROUP) WHERE group_id = @PH_STORE_GROUP;
/* End Update Root Category ID */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;
SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS;
SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION;
SET SQL_NOTES=@OLD_SQL_NOTES;
/* End */