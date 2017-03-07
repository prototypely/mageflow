<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestSalesTaxClassCustomer extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new entity called...
     */
    public function testPostSalesTaxClassCustomer()
    {
        $json = $this->testData->getSalesTaxClassCustomerForPost();
        $resource = 'sales/tax/class/customer';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }



/**
     * GETs sales/tax/class/customer. Made by Peeter on December 17th, 2014
     */
    public function testGetSalesTaxClassCustomer()
    {
        
        $resource = 'sales/tax/class/customer';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->getJson($url);
    }

    
}
