-- This SQL script can be used to clean up database after uninstalling MageFlowConnector extension
-- BEWARE! It won't work if you use prefixed table names. Be sure to fix table names below.

DROP TABLE 	mageflow_changeset_item;
DROP TABLE 	mageflow_changeset_item_cache;
DROP TABLE 	mageflow_media_index;
DROP TABLE 	mageflow_performance_history;

ALTER TABLE admin_role DROP COLUMN  mf_guid;
ALTER TABLE admin_user DROP COLUMN mf_guid;
ALTER TABLE catalog_category_entity DROP COLUMN mf_guid;
ALTER TABLE catalog_product_entity DROP COLUMN mf_guid;
ALTER TABLE cms_block DROP COLUMN mf_guid;
ALTER TABLE cms_page DROP COLUMN mf_guid;
ALTER TABLE core_config_data DROP COLUMN mf_guid;
ALTER TABLE core_email_template DROP COLUMN mf_guid;
ALTER TABLE core_store DROP COLUMN mf_guid;
ALTER TABLE core_store_group DROP COLUMN mf_guid;
ALTER TABLE core_website DROP COLUMN mf_guid;
ALTER TABLE eav_attribute DROP COLUMN mf_guid;
ALTER TABLE eav_attribute_group DROP COLUMN mf_guid;
ALTER TABLE eav_attribute_set DROP COLUMN mf_guid;

DELETE FROM core_resource WHERE code='mageflow_connect_setup';

DELETE FROM core_config_data WHERE path LIKE 'mageflow_connect%';

