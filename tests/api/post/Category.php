<?php
/**
 *
 * Product.php
 *
 * @author  sven
 * @created 11/17/2014 14:58
 */

include_once __DIR__ . '/../AbstractTest.php';


class Category extends Mageflow_Connect_AbstractTest
{
    protected $resource = 'catalog/category';
    public $knownRootMfGuid = 'hparrbjjlxf0ltmaml8hn0uvfmsxqu53';

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * This test creates a new root category
     */
    public function testCreateNewRootCategory()
    {
        $json
            = <<<JSON
[
    {
        "display_mode": "PRODUCTS",
        "include_in_menu": "1",
        "is_active": "1",
        "level": "1",
        "name": "Default Category %1\$s",
        "position": "1"
    }
]
JSON;

        $json = sprintf($json, mt_rand(10000, 99999));
        $url = sprintf('%s/%s', $this->baseUrl, $this->resource);
        $this->postJson($url, $json);
    }

    /**
     * This test creates OR updates a known root category with random name
     */
    public function testCreateKnownRootCategory()
    {
        $json
            = <<<JSON
[
    {
        "display_mode": "PRODUCTS",
        "include_in_menu": "1",
        "is_active": "1",
        "level": "1",
        "name": "Root Category %1\$s",
        "mf_guid": "%2\$s",
        "position": "1"
    }
]
JSON;
        $json = sprintf($json, mt_rand(10000, 99999), $this->knownRootMfGuid);
        $url = sprintf('%s/%s', $this->baseUrl, $this->resource);
        $this->postJson($url, $json);
    }

    /**
     * This test creates OR updates a known root category with random name
     */
    public function testCreateSubCategory()
    {
        $json
            = <<<JSON
[
    {
        "display_mode": "PRODUCTS",
        "include_in_menu": "1",
        "is_active": "1",
        "level": "2",
        "name": "Sub Category %1\$s",
        "parent_id": "%2\$s",
        "mf_guid": "%3\$s"
    }
]
JSON;
        $json = sprintf($json, mt_rand(10000, 99999), $this->knownRootMfGuid, uniqid("mf_"));
        $url = sprintf('%s/%s', $this->baseUrl, $this->resource);
        $this->postJson($url, $json);
    }
} 