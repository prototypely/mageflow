<?php

/**
 * Mageflow_Connect_Model_Api2_System_Log_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Log_Rest_Admin_V1
    extends Mageflow_Connect_Model_Api2_Abstract
{
    /**
     * resource type
     *
     * @var string
     */
    protected $_resourceType = 'system_configuration';


    /**
     * Returns array with system info
     *
     * @return array
     */
    public function _retrieve()
    {
        $out = array(
            'log' => array(),
            'exception' => array()
        );
        $this->log($this->getRequest()->getParams());

        try {
            $maxLines = Mage::getStoreConfig(
                'mageflow_connect/advanced/log_lines'
            );
            //failsafe is 100 lines
            if (!$maxLines) {
                $maxLines = 100;
            }

            $file = Mage::getStoreConfig('dev/log/file');
            $exceptionFile = Mage::getStoreConfig('dev/log/exception_file');
            $logDir = Mage::getBaseDir('var') . DS . 'log';

            $logFilePath = $logDir . DS . $file;
            $exceptionFilePath = $logDir . DS . $exceptionFile;
            $logTypes = array(
                'log' => $logFilePath,
                'exception' => $exceptionFilePath
            );
            //safety output
            $out['log'] = $logFilePath;
            $out['exception'] = $exceptionFilePath;

            foreach ($logTypes as $logType => $path) {
                $cmd = sprintf('wc -l %s', $path);
                $numLines = shell_exec($cmd);
                $cmd = sprintf('tail -%s %s', $maxLines, $path);
                $logStr = shell_exec($cmd);
                $lastLines = explode(PHP_EOL, $logStr);
                $logLines = array_combine(
                    range($numLines - $maxLines, $numLines),
                    $lastLines
                );
                $out[$logType] = $logLines;
            }

        } catch (Exception $e) {
            $this->log($e->getMessage());
        }

        return array($out);
    }

    /**
     * retrieve collection
     *
     * @return array
     */
    public function _retrieveCollection()
    {
        return $this->_retrieve();
    }


}