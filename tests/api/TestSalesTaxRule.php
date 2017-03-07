<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestSalesTaxRule extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new entity called...
     */
    public function testPostSalesTaxRule()
    {
        $json = $this->testData->getSalesTaxRuleForPost();
        $resource = 'sales/tax/rule';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }


/**
     * GETs sales/tax/rule. Made by Peeter on December 17th, 2014
     */
    public function testGetSalesTaxRule()
    {
        
        $resource = 'sales/tax/rule';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }

}
