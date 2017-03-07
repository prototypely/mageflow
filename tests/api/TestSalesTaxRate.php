<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestSalesTaxRate extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new entity called...
     */
    public function testPostSalesTaxRate()
    {
        $json = $this->testData->getSalesTaxRateForPost();
        $resource = 'sales/tax/rate';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }
/**
     * GETs sales/tax/rate. Made by Peeter on December 17th, 2014
     */
    public function testGetSalesTaxRate()
    {
        
        $resource = 'sales/tax/rate';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }

}
