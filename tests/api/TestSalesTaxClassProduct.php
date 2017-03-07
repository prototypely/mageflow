<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestSalesTaxClassProduct extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new entity called...
     */
    public function testPostSalesTaxClassProduct()
    {
        $json = $this->testData->getSalesTaxClassProductForPost();
        $resource = 'sales/tax/class/product';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }

/**
     * GETs sales/tax/class/product. Made by Peeter on December 17th, 2014
     */
    public function testGetSalesTaxClassProduct()
    {
        
        $resource = 'sales/tax/class/product';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }
}
