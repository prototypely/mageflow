<?php

/**
 * AbstractClient.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */


namespace Mageflow\Connect\Model\Api;

use Mageflow\Connect\Model\AbstractModel;


/**
 * AbstractClient
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 *
 * @method string getToken()
 * @method string getTokenSecret()
 * @method string getConsumerKey()
 * @method string getConsumerSecret()
 */
abstract class AbstractClient extends AbstractModel
{

    /**
     * api url
     *
     * @var null
     */
    protected $_apiUrl = null;

    /**
     * token
     *
     * @var
     */
    protected $_token;

    /**
     * token secret
     *
     * @var
     */
    protected $_tokenSecret;

    /**
     * consumer key
     *
     * @var
     */
    protected $_consumerKey;

    /**
     * consumer secret
     *
     * @var
     */
    protected $_consumerSecret;

    /**
     * logger
     *
     * @var
     */
    private $_logger;

    /**
     * Class constructor
     *
     * @param \stdClass $configuration
     *
     * @return \Mageflow\Connect\Model\Api\AbstractClient
     */
    public function __construct(\stdClass $configuration = null)
    {
        if (!is_null($configuration)) {
            foreach ($configuration as $key => $value) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    /**
     * get logger
     *
     * @return \Mageflow\Connect\Helper\Logger
     */
    protected function getLogger()
    {
        if (is_null($this->_logger)) {
            $this->_logger = new \Mageflow\Connect\Helper\Logger();
        }
        return $this->_logger;
    }

}
