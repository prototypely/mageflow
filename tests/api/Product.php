<?php


include_once 'AbstractTest.php';

/**
 *
 * Product.php
 *
 * Tests product resource related aspects
 *
 * @author sven
 * @created 07/18/2014 11:51
 */
class Mageflow_Connect_Product extends Mageflow_Connect_AbstractTest
{

    protected $resourceBase = 'catalog/product';
    protected $testSku = 'test';


    /**
     * @codeCoverageIgnore
     */
    public function testGetProducts()
    {
        printf("%s\n", $this->getName());
        printf("Testing /%s resource\n", $this->resourceBase);
        $url = sprintf('%s/%s', $this->baseUrl, $this->resourceBase);
        printf("Url: %s\n", $url);
        $retval = $this->getApiClient()->fetch($url, array(), OAUTH_HTTP_METHOD_GET, $this->headers);
        $json = $this->getApiClient()->getLastResponse();
        $this->assertTrue($retval);
        $responseObject = json_decode($json);
        $this->assertTrue(is_array($responseObject));
        $this->assertTrue(count($responseObject) > 0);

        //Find test product
        foreach($responseObject as $product){
            if($product->sku == $this->testSku){
                break;
            }
        }

        $this->assertInternalType('object', $product->stock_data, 'Stock data not PUT correctly');
        $this->assertObjectHasAttribute('qty', $product->stock_data, 'Stock data does not have QTY attribute!?');
    }

    /**
     * This method tests getting specific product with SKU=test
     * @codeCoverageIgnore
     */
    public function testGetTestProduct()
    {
        $this->getTestProduct();

    }

    public function testPutTestProduct()
    {
        $arr = $this->getTestProduct();
        $url = sprintf('%s/%s/%s', $this->baseUrl, $this->resourceBase, $this->testSku);
        printf("Url: %s\n", $url);
        $newWeight = mt_rand(100, 9999);
        $product = $arr[0];
        $product->weight = $newWeight;
        $product->stock_data->qty = mt_rand(23,23545);
        $json = json_encode(array($product));

//        printf("\n%s\n",$json);

        $retval = $this->getApiClient()->fetch($url, $json, OAUTH_HTTP_METHOD_PUT, $this->headers);
        $this->assertTrue($retval);
        $json = $this->getApiClient()->getLastResponse();
        $responseObj = json_decode($json);
        $originalEntity = $responseObj->success[0]->original_entity;
        $currentEntity = $responseObj->success[0]->current_entity;
        $this->assertTrue(is_object($currentEntity), 'Response does not contain current entity');
        $this->assertTrue(is_object($originalEntity), 'Response does not contain original entity');

        $this->assertTrue($currentEntity->weight == $product->weight, 'New product weight has not been PUT correctly');
        $this->assertTrue(count(array_diff($currentEntity->category_ids, $originalEntity->category_ids)) == 0, 'Categories have NOT been PUT correctly');

        $arr = $this->getTestProduct();
        $product = $arr[0];
        $this->assertInternalType('object', $product->stock_data, 'Stock data not PUT correctly');
        $this->assertObjectHasAttribute('qty', $product->stock_data, 'Stock data does not have QTY attribute!?');
    }

    /**
     * This test tests POSTing a new product.
     * To create product data it GETs existing test product,
     * changes its sku and name and POSTs it
     * @codeCoverageIgnore
     */
    public function testPostProduct()
    {
        $arr = $this->getTestProduct();
        $url = sprintf('%s/%s', $this->baseUrl, $this->resourceBase);
        printf("Url: %s\n", $url);
        $faker = Faker\Factory::create('en_US');
        $nameArr = explode(' ', str_replace('.', '', $faker->text()));
        $namePartKeys = array_rand($nameArr, mt_rand(1, 5));
        $nameParts = array();
        foreach ($namePartKeys as $namePartKey) {
            $nameParts[] = $nameArr[$namePartKey];
        }
        $newName = ucfirst(str_replace('\.', '', implode(' ', $nameParts)));
        $newSku = 'sku-' . mt_rand(10000, 999999);
        $newWeight = mt_rand(100, 9999);
        $product = $arr[0];
        $product->name = $newName;
        $product->short_description = $faker->text();
        $product->description = $faker->realText();
        $product->sku = $newSku;
        $product->weight = $newWeight;
        $product->stock_data->manage_stock = 1;
        $product->stock_data->qty = mt_rand(1, 9999);
        $product->price = round(mt_rand(100, 9999) / mt_rand(2, 99), 2);

        $productArr = array($product);
        $json = json_encode($productArr);
//        print_r($json);

        $this->setExpectedException('OAuthException');
        $retval = $this->getApiClient()->fetch($url, $json, OAUTH_HTTP_METHOD_POST, $this->headers);

        $this->assertTrue($retval);
        $json = $this->getApiClient()->getLastResponse();
        $responseObj = json_decode($json);
        $this->assertTrue(is_object($responseObj->success[0]->current_entity), 'Response does not contain current entity');
        //newly added products must not have original entity
        $this->assertFalse(is_object($responseObj->success[0]->original), 'Response does not contain current entity');

        $this->assertInternalType('object', $responseObj->success[0]->stock_data, 'Stock data not POSTed correctly');
        $this->assertObjectHasAttribute('qty', $product->stock_data, 'Stock data does not have QTY attribute!?');

    }

    private function getTestProduct()
    {
        $url = sprintf('%s/%s/%s', $this->baseUrl, $this->resourceBase, $this->testSku);
        printf("Url: %s\n", $url);
        $retval = $this->getApiClient()->fetch($url, array(), OAUTH_HTTP_METHOD_GET, $this->headers);
        $json = $this->getApiClient()->getLastResponse();
        $this->assertTrue($retval);
        $responseArr = json_decode($json);
        $this->assertTrue(is_array($responseArr));
        $this->assertTrue(count($responseArr) == 1);
        return $responseArr;
    }

    /**
     * This test verifies that product has category MFGUID's, not category ID's.
     *
     * It uses test product and thus test product must be mapped to at least one category.
     * An error will be thrown if product is not mapped to any category.
     *
     * @codeCoverageIgnore
     */
    public function testProductHasMfGuidsNotCategoryIds()
    {
        $productArr = $this->getTestProduct();
        $product = $productArr[0];

        $this->assertTrue(count($product->category_ids) > 0, 'Test product is not assigned ot any categories. Specify at least one category');

        foreach ($product->category_ids as $categoryId) {
            $this->assertTrue(strlen($categoryId) == 32, 'Category ID seems not to be a MF GUID');
        }
    }

    /**
     * This test verifies that test product is assigned to at least one websites
     * and website codes are in the list, not website IDs
     */
    public function testTestProductIsAssignedToWebsites()
    {
        $productArr = $this->getTestProduct();
        $product = $productArr[0];
        $this->assertTrue(is_array($product->website_codes), 'Website ID list is not an array');
    }

}
 