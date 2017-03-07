<?php

/**
 * ClientTest.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

namespace Mageflow\Connect\Model\Api\Mageflow;

/**
 * ClientTest
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * client
     *
     * @var Client
     */
    protected $client;

    /**
     * set up
     */
    public function setUp()
    {
        $helper = \Mage::helper('mageflow_connect/oauth');
        $this->client = $helper->getApiClient();
        parent::setUp();
    }

    /**
     * tear down
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * test instance
     */
    public function testInstance()
    {
        $this->assertInstanceOf(
            '\Mageflow\Connect\Model\Api\Mageflow\Client', $this->client
        );
    }

    /**
     * test new get
     */
    public function testNewGet()
    {
        $out = $this->client->get('/find/Instance/instance_key/flwin');
        print_r($out);
        echo PHP_EOL;
    }

    /**
     * test new post
     */
    public function testNewPost()
    {
        $out = $this->client->post('/changeset', ['description' => 'blaah']);
        print_r($out);
        echo PHP_EOL;
    }

    /**
     * test new put
     */
    public function testNewPut()
    {
        $out = $this->client->put(
            '/changeset', ['description' => 'blaah', 'id' => 1]
        );
        print_r($out);
        echo PHP_EOL;
    }
}
