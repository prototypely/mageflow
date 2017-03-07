#!/usr/bin/env php
<?php

/**
 * reindex_changeitems.php
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
class Mageflow_Connect_Reindex_Changeitems extends Mage_Shell_Abstract
{

    /**
     * @var Mageflow_Connect_Helper_Type
     */
    private $typeHelper;

    /**
     * Run script
     *
     * @return int
     */
    public function run()
    {
        set_time_limit(0);
        $options = 'a:t:m:ihlv';
        $longOptions = array(
            'all:',
            'type:',
            'limit:',
            'info',
            'help',
            'longhelp',
            'verbose'
        );
        $verbose = $this->getArg('v') | $this->getArg('verbose') | false;
        $retval = 1;
        try {

            $opt = getopt($options, $longOptions);

            $limit = -1;
            $shortLimit = isset($opt['m']) ? $opt['m'] : -1;
            $longLimit = isset($opt['limit']) ? $opt['limit'] : -1;

            $limit = $shortLimit > $limit ? $shortLimit : $longLimit > $limit ? $longLimit : -1;

            $allTypes = (isset($opt['all']) || isset($opt['a'])) ? true : false;

            $shortType = isset($opt['t']) ? $opt['t'] : null;
            $longType = isset($opt['type']) ? $opt['type'] : null;

            if ($verbose) {
                printf("Limit:\t%s\n", $limit);
            }

            if (
                null !== $shortType || null !== $longType || $allTypes
            ) {

                $typeList = array_merge(
                    is_array($shortType) ? $shortType : array($shortType),
                    is_array($longType) ? $longType : array($longType)
                );

                if ($allTypes) {
                    $typeList = $this->getSupportedDataTypes();
                    if ($verbose) {
                        echo "Re-indexing of ALL data types was requested. Grab a coffee, it may take a while ...\n";
                    }
                }

                if ($verbose) {
                    printf("Data types:\n%s\n", trim(implode('; ', $typeList)));
                }

                foreach ($typeList as $typeName) {
                    if ('' != trim($typeName)) {
                        $type = $this->getTypeHelper()->getType($typeName);
                        if ($type instanceof stdClass && null !== $type->handler) {
                            $dataHandlerClass = $this->getTypeHelper()->getHandlerClass($type->short);
                            $dataHandler = Mage::getModel($dataHandlerClass);
                            if ($dataHandler instanceof Mageflow_Connect_Model_Interfaces_Dataprocessor) {
                                if ($verbose) {
                                    echo sprintf("Started re-indexing items of type %s ...\n", $typeName);
                                }

                                $count = $dataHandler->reindex($type, $limit);

                                if ($verbose) {
                                    echo sprintf("Re-indexed %s items\n", $count);
                                }
                            }
                        }
                    }
                }
            }

            if ($this->getArg('info') || $this->getArg('i')) {
                echo $this->getSupportedDataTypesAsString();
                return 0;
            }
            $retval = 0;
        } catch (Exception $ex) {
            echo $ex->getMessage() . "\n";
        }

        if ($this->getArg('help') || sizeof($this->_args) < 1) {
            echo $this->usageHelp();
            return;
        } elseif ($this->getArg('longhelp') || $this->getArg('l')) {
            echo $this->longHelp();
            return;
        }

        if ($verbose) {
            echo "Done.\n";
        }
        return $retval;
    }


    /**
     * @return Mageflow_Connect_Helper_Type
     */
    private function getTypeHelper()
    {
        if (null === $this->typeHelper) {
            $this->typeHelper = Mage::helper('mageflow_connect/type');
        }
        return $this->typeHelper;
    }

    /**
     * Returns list of MageFlow supported data types as string
     *
     * @return string
     */
    private function getSupportedDataTypesAsString()
    {
        $out = '';
        foreach ($this->getSupportedDataTypes() as $typeName) {
            $out .= sprintf("%s\n", $typeName);
        }
        return $out;
    }

    /**
     * Returns list of MageFlow supported and index_enabled
     * data types as array
     *
     * @return array
     */
    private function getSupportedDataTypes()
    {
        $out = array();
        $typeHelper = $this->getTypeHelper();
        foreach ($typeHelper->getSupportedTypes() as $typeName) {
            $type = $typeHelper->getType($typeName);
            if ('' != $type->handler && $type->index_enabled) {
                $out [] = $typeName;
            }
        }
        return $out;
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
        --all, -a           Reindex ALL items of ALL supported data types in Magento database
        --type, -t          Reindex items of specified type. Multiple --type arguments can be added
        --limit, -m         Limit indexing to <limit> items of specified type (or all).
        --info, -i          Display supported data types
        --help, -h          Display information about usage
        --longhelp, -l      Display long help and license info
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
        $longHelp
            = <<<LONGHELP
  __  __                  _____ _
 |  \/  | __ _  __ _  ___|  ___| | _____      __
 | |\/| |/ _` |/ _` |/ _ \ |_  | |/ _ \ \ /\ / /
 | |  | | (_| | (_| |  __/  _| | | (_) \ V  V /
 |_|  |_|\__,_|\__, |\___|_|   |_|\___/ \_/\_/
  ____      _  |___/    _
 |  _ \ ___(_)_ __   __| | _____  __
 | |_) / _ \ | '_ \ / _` |/ _ \ \/ /
 |  _ <  __/ | | | | (_| |  __/>  <
 |_|_\_\___|_|_| |_|\__,_|\___/_/\_\ _ _
  / ___| |__   __ _ _ __   __ _  ___(_) |_ ___ _ __ ___  ___
 | |   | '_ \ / _` | '_ \ / _` |/ _ \ | __/ _ \ '_ ` _ \/ __|
 | |___| | | | (_| | | | | (_| |  __/ | ||  __/ | | | | \__ \
  \____|_| |_|\__,_|_| |_|\__, |\___|_|\__\___|_| |_| |_|___/
                          |___/

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

        This script will create or update Media Index. Media Index is an up-to-date list of
        all media files under WYSIWYG folder in Magento media folder.

    LIMITING NUMBER OF REINDEXED ITEMS

        Sometimes reindexing is a very lengthy process. For example there may be thousands of products in the database.
        In order to limit number of items to reindexed at once use --limit N parameter where N is an integer > 0.
        Program would then reindex N *unindexed and unchanged items*. Because a checksum is calculated for each change item
        the program will reindex only those items that haven't been indexed yet.


LONGHELP;
        return $longHelp . $this->usageHelp() . "\n";
    }
}

$shell = new Mageflow_Connect_Reindex_Changeitems();
$retval = $shell->run();
exit($retval);