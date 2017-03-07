#!/usr/bin/env php
<?php

/**
 * create_oauth_consumer.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Shell
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

require_once 'abstract.php';


/**
 * Mageflow_Connect_Create_OAuth_Consumer
 * MageFlow OAuth key setup script
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Shell
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Create_OAuth_Consumer extends Mage_Shell_Abstract
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        return parent::__construct();
    }

    /**
     * Class constructor
     *
     * @return Mage_Shell_Abstract
     */
    public function _construct()
    {
        //include Mageflow client lib and its autoloader
        //to ensure classloading
        @include_once 'Mageflow/Connect/Module.php';
        $m = new \Mageflow\Connect\Module();
        return parent::_construct();
    }

    /**
     * Saves all extension configuration from command line arguments
     *
     * @param $parameters
     */
    public function saveExtensionConfiguration($parameters)
    {
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_PROJECT,
            $parameters['project_id']
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_PROJECT_NAME,
            $parameters['project_name']
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_INSTANCE_KEY,
            $parameters['instance_key']
        );
        $arr = array(
            'id' => $parameters['company_id'],
            'name' => $parameters['company_name']
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_COMPANY,
            $parameters['company_id']
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_COMPANY_NAME,
            serialize($arr)
        );
        Mage::app()->getConfig()
            ->saveConfig(Mageflow_Connect_Model_System_Config::API_ENABLED, 1);
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_KEY,
            $parameters['consumer_key']
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_CONSUMER_SECRET,
            $parameters['consumer_secret']
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN,
            $parameters['token']
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_TOKEN_SECRET,
            $parameters['token_secret']
        );
        Mage::app()->getConfig()->saveConfig(
            Mageflow_Connect_Model_System_Config::API_URL,
            $parameters['api_url']
        );
    }

    /**
     * Sets up OAuth consumer that can consumer MageFlow API resources
     *
     * @param array $parameters
     *
     * @return int
     */
    private function setupOauthConsumer($parameters)
    {
        $retval = 0;
        $this->saveExtensionConfiguration($parameters);
        $oauthHelper = Mage::helper('mageflow_connect/oauth');
        $response = $oauthHelper->createOAuthConsumer(
            $parameters['instance_key']
        );
        $jsonData = Mage::helper('core')->jsonEncode($response);
        echo "\n" . $jsonData . "\n";
        if (!$response->success) {
            $retval = 180;
        }
        return $retval;
    }

    /**
     * Run script
     */
    public function run()
    {
        //disable output buffering
        while (@ob_end_clean()) {
            ;
        }
        ob_implicit_flush(true);
        $mandatoryArgs = array(
            'consumer_key',
            'consumer_secret',
            'token',
            'token_secret',
            'instance_key',
            'company_id',
            'company_name',
            'project_id',
            'project_name',
            'api_url',
        );
        foreach ($mandatoryArgs as $arg) {
            if (!$this->getArg($arg)) {
                echo sprintf("Mandatory argument %s not defined.\n", $arg);
                echo $this->usageHelp();
                return;
            }
        }
        if ($this->getArg('help')) {
            echo $this->usageHelp();
            return;
        } elseif ($this->getArg('longhelp')) {
            echo $this->longHelp();
            return;
        }

        Mage::helper('mageflow_connect/system')->cleanCache();

        $retval = $this->setupOauthConsumer($this->_args);

        return $retval;
    }

    /**
     * Return longer version of help text
     *
     * @return string
     */
    public function longHelp()
    {
        $script = basename(__FILE__);
        return <<<USAGE
  ____       _                   __  __                  _____ _
 / ___|  ___| |_   _   _ _ __   |  \/  | __ _  __ _  ___|  ___| | _____      __
 \___ \ / _ \ __| | | | | '_ \  | |\/| |/ _` |/ _` |/ _ \ |_  | |/ _ \ \ /\ / /
  ___) |  __/ |_  | |_| | |_) | | |  | | (_| | (_| |  __/  _| | | (_) \ V  V /
 |____/ \___|\__|  \__,_| .__/  |_|  |_|\__,_|\__, |\___|_|   |_|\___/ \_/\_/
   ___    _         _   |_|      ____         |___/
  / _ \  / \  _   _| |_| |__    / ___|___  _ __  ___ _   _ _ __ ___   ___ _ __
 | | | |/ _ \| | | | __| '_ \  | |   / _ \| '_ \/ __| | | | '_ ` _ \ / _ \ '__|
 | |_| / ___ \ |_| | |_| | | | | |__| (_) | | | \__ \ |_| | | | | | |  __/ |
  \___/_/   \_\__,_|\__|_| |_|  \____\___/|_| |_|___/\__,_|_| |_| |_|\___|_|

    LICENSE AND COPYRIGHT NOTICE

        PLEASE READ THIS SOFTWARE LICENSE AGREEMENT ("LICENSE") CAREFULLY
        BEFORE USING THE SOFTWARE. BY USING THE SOFTWARE, YOU ARE AGREEING
        TO BE BOUND BY THE TERMS OF THIS LICENSE.
        IF YOU DO NOT AGREE TO THE TERMS OF THIS LICENSE, DO NOT USE THE SOFTWARE.

        Full text of this license is available @license

        @author     Prototypely Ltd, Estonia <info@prototypely.com>
        @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
        @license MIT Copyright (c) 2017 Prototypely Ltd
        @link       http://mageflow.com/

    GENERAL INFO

        This script is meant to be run by MageFlow Instance Setup Process.
        There should be no need to run this script manually.
        Be careful with all the parameters if you still want to run this script manually.

    USAGE

        Usage:  php -f $script -- [options]

        --consumer_key          MageFlow API OAuth consumer key
        --consumer_secret       MageFlow API OAuth consumer secret
        --token                 MageFlow API OAuth token
        --token_secret          MageFlow API OAuth token secret
        --instance_key          Instance key at MageFlow
        --company_id            Company ID at MageFlow
        --company_name          Company name at MageFlow
        --project_id            Project ID at MageFlow
        --project_name          Project name at MageFlow
        --api_url               MageFlow API URL
        --help                  This help
        --longhelp              Long help and copyright info

    ADDITIONAL INFO AND HELP

        Don't hesitate to contact us at info@mageflow.com in case
        you need additional info and help with MageFlow


USAGE;
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        $script = basename(__FILE__);
        return <<<USAGE
    USAGE

        Usage:  php -f $script -- [options]

        --consumer_key          MageFlow API OAuth consumer key
        --consumer_secret       MageFlow API OAuth consumer secret
        --token                 MageFlow API OAuth token
        --token_secret          MageFlow API OAuth token secret
        --instance_key          Instance key at MageFlow
        --company_id            Company ID at MageFlow
        --company_name          Company name at MageFlow
        --project_id            Project ID at MageFlow
        --project_name          Project name at MageFlow
        --api_url               MageFlow API URL
        --help                  This help
        --longhelp              Long help and copyright info


USAGE;
    }
}

$shell = new Mageflow_Connect_Create_OAuth_Consumer();
$retval = $shell->run();
exit($retval);
