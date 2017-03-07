<?php

/**
 *
 * PushGrid.php
 *
 * @author sven
 * @created 08/05/2014 18:07
 */
class Mageflow_Connect_PushGrid extends PHPUnit_Extensions_SeleniumTestCase
{

    public function setUp()
    {
        $this->shareSession(true);
        $this->setBrowser('*googlechrome');
        $this->setBrowserUrl('http://magento1.dev.mageflow.com/');
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Tests if MF GUID column is present in push grid
     */
    public function testMfGuidColumnIsPresent()
    {
        $this->open('/admin');
        $this->type('id=username', 'tester@mageflow.com');
        $this->type('id=login', 'mageflowtest2014');
        $this->clickAndWait('css=input.form-button');
        $this->clickAndWait("xpath=//ul[@id='nav']/*//a/span[text()='Push Change Sets']");
        $this->assertTextPresent('MF GUID');
    }
}
 