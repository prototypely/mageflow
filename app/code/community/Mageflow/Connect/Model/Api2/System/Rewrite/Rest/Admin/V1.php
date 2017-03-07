<?php

/**
 * V1.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Api2_System_Rewrite_Rest_Admin_V1
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Api2_System_Rewrite_Rest_Admin_V1
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
        $this->log($this->getRequest()->getParams());
        $classInfoNodeList = Mage::getConfig()->getNode()->xpath(
            '//global//rewrite/..'
        );
        $outItems = array();

        foreach ($classInfoNodeList as $classInfoNode) {
            $rewrite = $classInfoNode->xpath('rewrite');
            if (is_array($rewrite) && sizeof($rewrite) > 0) {
                $keys = array_keys($rewrite[0]->asArray());
                $classSuffix = $keys[0];
                $rewriteClass = (string)$classInfoNode->rewrite->$classSuffix;
                $className = $classInfoNode->class . '_' . uc_words(
                        $classSuffix,
                        '_'
                    );
                $outItem = array(
                    'original' => $className,
                    'rewriter' => $rewriteClass
                );
                $outItems[] = $outItem;
            }
        }
        $this->log($outItems);
        return $outItems;
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