#!/usr/bin/env php
<?php

/**
 * dump_oauth_keys.php
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

require_once './abstract.php';


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
class Mageflow_Connect_Dump_Oauth_Keys extends Mage_Shell_Abstract
{

    public function run()
    {
        $tokenModelCollection = Mage::getModel('oauth/token')->getCollection();
        /**
         * @var Mage_Oauth_Model_Token $tokenModel
         */
        if ($this->getArg('b')) {

            $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'api/rest';
            printf("export bamboo_base_url='%s';\n", $baseUrl);

            /**
             * we get last item because we assume it's the newest key...
             */
            $tokenModel = $tokenModelCollection->getLastItem();
            $consumerModel = $tokenModel->getConsumer();

            printf("export bamboo_consumer_key='%s';\n", $consumerModel->getKey());
            printf("export bamboo_consumer_secret='%s';\n", $consumerModel->getSecret());
            printf("export bamboo_token='%s';\n", $tokenModel->getToken());
            printf("export bamboo_token_secret='%s';\n", $tokenModel->getSecret());


        } else {
            echo "Name:\t\tConsumer\tSecret\tToken\tSecret\n";
            foreach ($tokenModelCollection as $tokenModel) {
                $consumerModel = $tokenModel->getConsumer();
                printf(
                    "%s:\t%s\t%s\t%s\t%s\n", $consumerModel->getName(), $consumerModel->getKey(),
                    $consumerModel->getSecret(), $tokenModel->getToken(), $tokenModel->getSecret()
                );
            }
        }
    }

    public function usageHelp()
    {
        $help
            = <<<HELP

###############################################################################
#                                                                             #
# MageFlow helper script for showing oauth keys                               #
#                                                                             #
###############################################################################

    -h show this help
    -b display environment variables in "bamboo" style for local testing


HELP;
        echo $help;

    }
}

$shell = new Mageflow_Connect_Dump_Oauth_Keys();
$retval = $shell->run();
exit($retval);