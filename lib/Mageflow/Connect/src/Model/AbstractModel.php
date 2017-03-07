<?php

/**
 * AbstractModel.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

namespace Mageflow\Connect\Model;

/**
 * AbstractModel
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Lib
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class AbstractModel
{

    /**
     * Class constructor
     *
     * @return AbstractModel
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * call
     *
     * @param type $name
     * @param type $arguments
     */
    public function __call($name, $arguments)
    {
        $propertyName = '_' . lcfirst(substr($name, 3));
        if (substr($name, 0, 3) == 'set') {
            $this->$propertyName = $arguments[0];
        } elseif (substr($name, 0, 3) == 'get') {
            return $this->$propertyName;
        }
    }

}
