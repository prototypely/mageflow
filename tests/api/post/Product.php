<?php
/**
 *
 * Product.php
 *
 * @author  sven
 * @created 11/17/2014 14:58
 */

class Mageflow_Connect_Post_Product extends Mageflow_Connect_AbstractTest
{
    protected $resource = 'catalog/product';

    public function setUp()
    {
        parent::setUp();
    }

    public function testProduct()
    {
        $json
            = <<<JSON
[
{
		"sku": "%1\$s",
        "attribute_set_id": "4",
        "attribute_set_name": "Default",
        "category_ids": [
            "s3j03wnenm5kb0d89woni21pfqv39d93"
        ],
        "country_of_manufacture": null,
        "created_at": "2014-11-14 07:33:12",
        "custom_design": null,
        "custom_design_from": null,
        "custom_design_to": null,
        "custom_layout_update": null,
        "description": "Simple product with custom options",
        "enable_googlecheckout": "1",
        "entity_type_id": "4",
        "gift_message_available": null,
        "group_price": [],
        "has_options": "1",
        "image": "no_selection",
        "image_label": null,
        "is_in_stock": "1",
        "is_recurring": "0",
        "manufacturer": null,
        "media_gallery": {
            "images": [],
            "values": []
        },
        "meta_description": null,
        "meta_keyword": null,
        "meta_title": null,
        "msrp": null,
        "msrp_display_actual_price_type": "4",
        "msrp_enabled": "2",
        "name": "Simple product with custom options",
        "news_from_date": null,
        "news_to_date": null,
        "options_container": "container2",
        "page_layout": null,
        "price": "99.9900",
        "product_options": [
            {
                "sku": "opt-1",
                "type": "drop_down",
                "is_require": "1",
                "sort_order": "1",
                "mf_guid": "tkbbyfam1qm6uot6jg25u4eu16g49nia",
                "created_at": "2014-11-14 11:38:25",
                "updated_at": "2014-11-14 11:38:25",
                "default_title": "Mitäs nüt",
                "store_title": "Mitäs nüt",
                "title": "Mitäs nüt",
                "values": [
                    {

						"sku":"opt-value-1",
                        "sort_order": "1",
                        "default_title": "option 1",
                        "store_title": "option 1",
                        "title": "option 1",
                        "default_price": "10.0000",
                        "default_price_type": "fixed",
                        "store_price": "10.0000",
                        "store_price_type": "fixed",
                        "price": "10.0000",
                        "price_type": "fixed"
                    },
                    {
						"sku":"opt-value-2",
                        "sort_order": "2",
                        "default_title": "option 2",
                        "store_title": "option 2",
                        "title": "option 2",
                        "default_price": "20.0000",
                        "default_price_type": "fixed",
                        "store_price": "20.0000",
                        "store_price_type": "fixed",
                        "price": "20.0000",
                        "price_type": "fixed"
                    }
                ]
            },
            {
                "type": "date_time",
                "is_require": "1",
				"sku":"opt-value-3",
                "sort_order": "2",
                "created_at": "2014-11-14 11:38:25",
                "updated_at": "2014-11-14 11:38:25",
                "default_title": "datetime option",
                "store_title": "datetime option",
                "title": "datetime option",
                "default_price": "10.0000",
                "default_price_type": "fixed",
                "store_price": "10.0000",
                "store_price_type": "fixed",
                "price": "10.0000",
                "price_type": "fixed"
            }
        ],
        "required_options": "1",
        "short_description": "Simple product with custom options",
        "small_image": "no_selection",
        "small_image_label": null,
        "special_from_date": "2014-11-12 00:00:00",
        "special_price": "89.9900",
        "special_to_date": "2014-11-28 00:00:00",
        "status": "1",
        "stock_data": {
            "backorders": "0",
            "enable_qty_increments": "0",
            "is_decimal_divided": "0",
            "is_in_stock": "1",
            "is_qty_decimal": "0",
            "low_stock_date": null,
            "manage_stock": 1,
            "max_sale_qty": "0.0000",
            "min_qty": "0.0000",
            "min_sale_qty": "1.0000",
            "notify_stock_qty": null,
            "qty": "999.0000",
            "qty_increments": "0.0000",
            "stock_id": "1",
            "stock_status_changed_auto": "0",
            "stock_status_changed_automatically": "0",
            "type_id": "simple",
            "use_config_backorders": "1",
            "use_config_enable_qty_inc": "1",
            "use_config_enable_qty_increments": "1",
            "use_config_manage_stock": 1,
            "use_config_max_sale_qty": "1",
            "use_config_min_qty": "1",
            "use_config_min_sale_qty": "1",
            "use_config_notify_stock_qty": "1",
            "use_config_qty_increments": "1"
        },
        "store_codes": {
            "1": "default",
            "2": "newst"
        },
        "store_ids": [
            "1",
            "2"
        ],
        "store_mf_guids": {
            "1": "sgo9h163zyy2bd2aaobslqpoi3ednoej",
            "2": "o3yktocvo4fkzxawqttlxiy68bt93t31"
        },
        "tax_class_id": "2",
        "thumbnail": "no_selection",
        "thumbnail_label": null,
        "tier_price": [
            {
                "price_id": "1",
                "website_id": "0",
                "all_groups": "1",
                "cust_group": 32000,
                "price": "79.9900",
                "price_qty": "10.0000",
                "website_price": "79.9900"
            }
        ],
        "type_id": "simple",
        "use_config_gift_message_available": null,
        "visibility": "4",
        "website_codes": [
            "base",
            "newweb"
        ],
        "website_ids": null,
        "website_mf_guids": {
            "1": "mki246j37auxcanswf6n529v1jyvnslr",
            "3": "ejwicuqgzrggywu6ztun204iriaow52y"
        },
        "weight": "10.0000"
    }
]
JSON;

        $json = sprintf($json, 'sku_' . mt_rand(10000, 99999));

        $url = sprintf('%s/%s', $this->baseUrl, $this->resource);
        $this->postJson($url, $json);
    }
} 