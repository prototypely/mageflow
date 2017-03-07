<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author sven
 * @created 07/18/2014 11:51
 */
class Testpost extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new e-mail template
     */
    public function testPostEmailTemplate()
    {
        $json = $this->testData->getEmailTemplateForPost();
        $resource = 'email/template';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }

    /**
     * Tests POST of catalog category
     *
     * @covers Mageflow_Connect_Model_Api2_Catalog_Category_Rest_Admin_V1
     */
    public function testPostCatalogCategory()
    {

        $json = <<<JSON
[
{
        "children_count": "0",
        "created_at": "2014-10-24 14:08:55",
        "custom_apply_to_products": "0",
        "custom_use_parent_settings": "0",
        "description": "%2\$s",
        "display_mode": "PRODUCTS",
        "include_in_menu": "1",
        "is_active": "1",
        "is_anchor": "0",
        "level": "2",
        "mf_guid": "%1\$s",
        "name": "%2\$s",
        "parent_id": "975ca8804565c1a569450d61090b2743",
        "position": "1",
        "updated_at": "2014-10-24 14:08:55",
        "url_key": "alamkass",
        "url_path": "alamkass.html"
    }
]
JSON;
        $json = sprintf($json, md5(mt_rand(-PHP_INT_MAX, PHP_INT_MAX)), 'name ' . mt_rand(1, 9999));
        $resource = 'catalog/category';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $this->postJson($url, $json);
    }

    public function testPostCatalogProductWithCustomOptions()
    {
        require_once __DIR__.'/post/Product.php';
        $productTest = new Mageflow_Connect_Post_Product();
        $productTest->testProduct();
    }
}

