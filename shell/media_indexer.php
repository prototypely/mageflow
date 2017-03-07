#!/usr/bin/env php
<?php

/**
 * media_indexer.php
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
 * Mageflow_Connect_Media_Indexer
 * MageFlow Media Indexer script
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Shell
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Media_Indexer extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     * @return int
     */
    public function run()
    {
        $verbose = ('' != $this->getArg('v') || '' != $this->getArg('verbose'));
        try {
            $helper = Mage::helper('mageflow_connect/media');
            $helper->setCli(true);
            $helper->setVerbose($verbose);
            if ($this->getArg('i') || $this->getArg('info')) {
                echo $this->longHelp();
                $retval = 0;
            } elseif ($this->getArg('n') || $this->getArg('initialize')) {
                if ($verbose) {
                    echo "Creating media index ...\n";
                }
                $helper->initializeIndex();
                $retval = 0;
            } elseif ($this->getArg('r') || $this->getArg('refresh')) {
                if ($verbose) {
                    echo "Reindexing media ...\n";
                }
                $helper->refreshIndex();
                $retval = 0;
            } else {
                echo $this->usageHelp();
                $retval = 0;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage() . "\n";
        }
        if ($verbose) {
            echo "Done.\n";
        }
        return $retval;
    }

    /**
     * Return help text
     *
     * @return string
     */
    public function usageHelp()
    {
        $program = basename(__FILE__);
        return <<< USAGE
    USAGE
        Usage: php $program <options>
        Options are:
        --initialize, -n    Creates fresh media index with current state of media files
        --refresh, -r       Synchronizes media index with current state of media files
        --info, -i          Display license info and long help
        --verbose, -v       Be verbose

USAGE;
    }

    /**
     * Return longer vesion of help text
     *
     * @return string
     */
    public function longHelp()
    {
        return <<<LONGHELP

  __  __                  _____ _
 |  \/  | __ _  __ _  ___|  ___| | _____      __
 | |\/| |/ _` |/ _` |/ _ \ |_  | |/ _ \ \ /\ / /
 | |  | | (_| | (_| |  __/  _| | | (_) \ V  V /
 |_|  |_|\__,_|\__, |\___|_|   |_|\___/ \_/\_/
  __  __       |___/_
 |  \/  | ___  __| (_) __ _
 | |\/| |/ _ \/ _` | |/ _` |
 | |  | |  __/ (_| | | (_| |
 |_|_ |_|\___|\__,_|_|\__,_|
 |_ _|_ __   __| | _____  _____ _ __
  | || '_ \ / _` |/ _ \ \/ / _ \ '__|
  | || | | | (_| |  __/>  <  __/ |
 |___|_| |_|\__,_|\___/_/\_\___|_|


    LICENSE AND COPYRIGHT NOTICE

        PLEASE READ THIS SOFTWARE LICENSE AGREEMENT ("LICENSE") CAREFULLY
        BEFORE USING THE SOFTWARE. BY USING THE SOFTWARE, YOU ARE AGREEING
        TO BE BOUND BY THE TERMS OF THIS LICENSE.
        IF YOU DO NOT AGREE TO THE TERMS OF THIS LICENSE, DO NOT USE THE SOFTWARE.

        Full text of this license is available @license

        @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
        @author     MageFlow
        @copyright  2014 MageFlow http://mageflow.com/

    GENERAL INFO

        This script will create or update Media Index. Media Index is an up-to-date list of
        all media files under WYSIWYG folder in Magento media folder.


LONGHELP;

    }
}

$shell = new Mageflow_Connect_Media_Indexer();
$retval = $shell->run();
exit($retval);
