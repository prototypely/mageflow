<?php

/**
 * Config.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_System_Config
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_System_Config extends Varien_Object
{

    const CONNECT_URL = "mageflow_connect/advanced/connect_url";
    const SIGNUP_URL = "mageflow_connect/advanced/signup_url";
    const GROUND_RULES_URL = "mageflow_connect/advanced/ground_rules";
    const API_URL = 'mageflow_connect/api/url';
    const API_CONSUMER_KEY = 'mageflow_connect/api/consumer_key';
    const API_CONSUMER_SECRET = 'mageflow_connect/api/consumer_secret';

    const REMOTE_CONSUMER_KEY = 'mageflow_connect/api/remote_consumer_key';
    const REMOTE_CONSUMER_SECRET = 'mageflow_connect/api/remote_consumer_secret';

    const API_TOKEN = 'mageflow_connect/api/token';
    const API_TOKEN_SECRET = 'mageflow_connect/api/token_secret';
    const API_ENABLED = 'mageflow_connect/api/enabled';
    const API_COMPANY = 'mageflow_connect/api/company';
    const API_PROJECT = 'mageflow_connect/api/project';
    const API_COMPANY_NAME = 'mageflow_connect/api/company_name';
    const API_PROJECT_NAME = 'mageflow_connect/api/project_name';
    const API_INSTANCE_KEY = 'mageflow_connect/api/instance_key';
    const API_INSTANCE_TYPE = 'mageflow_connect/api/instance_type';
    const API_LOG_LEVEL = 'mageflow_connect/advanced/log_level';
    const API_LOG_ENABLED = 'mageflow_connect/advanced/log_enabled';
    const API_PULL_DAYS_BACK = 'mageflow_connect/advanced/pull_days_back';
    const SYSTEM_MAINTENANCE_MODE = 'mageflow_connect/system/maintenance_mode';
    const DEV_RESTRICT_ALLOW_IPS = 'dev/restrict/allow_ips';
    const AUTO_TRUNCATE_CACHE = 'mageflow_connect/advanced/auto_truncate_cache';
    const AUTO_CLEAN_CACHE = 'mageflow_connect/advanced/auto_clean_cache';
    const ENABLE_COLLECTING_MEMORY = 'mageflow_connect/advanced/enable_collecting_memory';
    const AUTO_CLEAN_COLLECTED_MEMORY = 'mageflow_connect/advanced/auto_clean_collected_memory';

    const INSTANCETYPE_CE = 'community';
    const INSTANCETYPE_EE = 'enterprise';

    const ENABLE_CMS_PAGE = 'mageflow_connect/enabled_types/enable_cms_page';
    const ENABLE_CMS_BLOCK = 'mageflow_connect/enabled_types/enable_cms_block';
    const ENABLE_CMS_WIDGET = 'mageflow_connect/enabled_types/enable_cms_widget';
    const ENABLE_CMS_POLL = 'mageflow_connect/enabled_types/enable_cms_poll';
    const ENABLE_PROMOTION_CATALOG = 'mageflow_connect/enabled_types/enable_promotion_catalog';
    const ENABLE_PROMOTION_CART = 'mageflow_connect/enabled_types/enable_promotion_cart';
    const ENABLE_CATALOG_CATEGORY = 'mageflow_connect/enabled_types/enable_catalog_category';
    const ENABLE_CATALOG_ATTRIBUTE = 'mageflow_connect/enabled_types/enable_catalog_attribute';
    const ENABLE_CATALOG_ATTRIBUTESET = 'mageflow_connect/enabled_types/enable_catalog_attributeset';
    const ENABLE_CATALOG_PRODUCT = 'mageflow_connect/enabled_types/enable_catalog_product';
    const ENABLE_CONFIGURATION = 'mageflow_connect/enabled_types/enable_configuration';
    const ENABLE_OTHER = 'mageflow_connect/enabled_types/enable_other';

    function getTypeToUrlMap()
    {
        return array(
            'cms:page' => 'cmspages',
            'cms:block' => 'cmsblocks',
            'cms:widget' => 'cmswidgets',
            'cms:poll' => 'cmspolls',
            'promotion:rule:catalog' => 'promotionscatalog',
            'promotion:rule:checkout' => 'promotionscart',
            'catalog:category' => 'catalogcategories',
            'catalog:attribute' => 'catalogattributes',
            'catalog:attributeset' => 'catalogattributesets',
            'catalog:product' => 'catalogproducts',
            'system:configuration' => 'configuration',
            'other' => 'other'
        );
    }

    function getTypeMap()
    {
        return array(
            'cmspages' => array(
                'label' => 'CMS: Pages',
                'config' => 'enable_cms_page'
            ),
            'cmsblocks' => array(
                'label' => 'CMS: Static blocks',
                'config' => 'enable_cms_block'
            ),
            'cmswidgets' => array(
                'label' => 'CMS: Widgets',
                'config' => 'enable_cms_widget'
            ),
            'cmspolls' => array(
                'label' => 'CMS: Polls',
                'config' => 'enable_cms_poll'
            ),
            'promotionscatalog' => array(
                'label' => 'Promotions: Catalog',
                'config' => 'enable_promotion_catalog'
            ),
            'promotionscart' => array(
                'label' => 'Promotions: Shopping cart',
                'config' => 'enable_promotion_cart'
            ),
            'catalogcategories' => array(
                'label' => 'Catalog: Categories',
                'config' => 'enable_catalog_category'
            ),
            'catalogattributes' => array(
                'label' => 'Catalog: Attributes',
                'config' => 'enable_catalog_attribute'
            ),
            'catalogattributesets' => array(
                'label' => 'Catalog: Attribute sets',
                'config' => 'enable_catalog_attributeset'
            ),
            'catalogproducts' => array(
                'label' => 'Catalog: Products',
                'config' => 'enable_catalog_product'
            ),
            'configuration' => array(
                'label' => 'Configuration',
                'config' => 'enable_configuration'
            ),
            'other' => array(
                'label' => 'Other',
                'config' => 'enable_other'
            ),
        );
    }

    function getTypeListing()
    {
        return array(
            'cms:page',
            'cms:block',
            'cms:widget',
            'cms:poll',
            'promotion:rule:catalog',
            'promotion:rule:checkout',
            'catalog:category',
            'catalog:attribute',
            'catalog:attributeset',
            'catalog:product',
            'system:configuration'
        );
    }
}
