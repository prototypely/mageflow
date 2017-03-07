<?php

/**
 *
 * Mageflow_Connect_Model_Async_UpdaterTest.php
 *
 * @author sven
 * @created 08/12/2014 09:01
 */
class Mageflow_Connect_Model_Async_UpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mageflow_Connect_Model_Async_Itemcacheupdater $model
     */
    protected $model;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->model = Mage::getModel('mageflow_connect/async_itemcacheupdater');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    public function testInstance()
    {
        $this->assertInstanceOf('Mageflow_Connect_Model_Async_Itemcacheupdater', $this->model, 'Model is not of correct type');
    }

    /**
     * @covers Mageflow_Connect_Model_Async_Itemcacheupdater::run()
     */
    public function testUpdaterRun()
    {
        $retval = $this->model->run();
        $this->assertTrue($retval, 'Return value is not TRUE');
    }
} 