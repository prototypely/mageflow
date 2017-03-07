<?php
include_once 'AbstractTest.php';


/**
 *
 * Apitest.php
 *
 * @author peeter
 * @created 
 */
class TestPromotionRuleCheckout extends Mageflow_Connect_AbstractTest
{


    /**
     * POSTs new entity called...
     */
    public function testPostPromotionRuleCheckout()
    {
        $json = $this->testData->getPromotionRuleCheckoutForPost();
        $resource = 'promotion/rule/checkout';
        $url = sprintf('%s/%s', $this->baseUrl, $resource);
        $response = $this->postJson($url, $json);
    }
}
