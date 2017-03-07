#!/usr/bin/env php
<?php

/**
 * create_backend_user.php
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
 * Mageflow_Connect_Create_Backend_User
 * MageFlow Backend User creation script
 * This script creates Magento admin user to current Magento database
 * Magento has to be installed first!
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Shell
 * @author     Prototypely Ltd, Estonia <info@prototypely.com>
 * @copyright  Copyright © 2017 Prototypely Ltd, Estonia (http://prototypely.com) 
 * @license MIT Copyright (c) 2017 Prototypely Ltd
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Create_Backend_User extends Mage_Shell_Abstract
{
    /**
     * app code
     *
     * @var string
     */
    protected $_appCode = 'default';

    /**
     * @param null $email
     * @param null $firstname
     * @param null $lastname
     * @param null $password
     * @param bool $verbose
     *
     * @internal param null $username
     */
    function createUser($email = null, $firstname = null, $lastname = null, $password = null, $verbose = false)
    {
        if (is_null($firstname)) {
            echo "First name: ";
            $firstname = trim(fgets(STDIN));
        }
        if (is_null($lastname)) {
            echo 'Last name: ';
            $lastname = trim(fgets(STDIN));
        }
        if (is_null($email)) {
            echo 'Please enter your e-mail: ';
            $email = trim(fgets(STDIN));
        }
        $sendReset = false;
        if (is_null($password)) {
            $password = md5(uniqid());
            $sendReset = true;
        }

        /**
         * @var Mageflow_Connect_Helper_Oauth $oauthHelper
         */
        $oauthHelper = Mage::helper('mageflow_connect/oauth');
        $adminUser = $oauthHelper->createAdminUser($email, $firstname, $lastname, $password, $sendReset);

        $newResetPasswordLinkToken = null;
        if ($sendReset) {
            $newResetPasswordLinkToken = Mage::helper('admin')
                ->generateResetPasswordLinkToken();
            $adminUser->changeResetPasswordLinkToken($newResetPasswordLinkToken);
        }

        $adminUser->save();

        if ($newResetPasswordLinkToken) {
            $this->sendPasswordResetConfirmationEmail($adminUser);
        }

    }

    /**
     * Displays help about program usage
     */
    function usageHelp()
    {
        $program = basename(__FILE__);
        return <<< USAGE
    USAGE
        Usage: php $program [options]
        Options are:
        --email,    -e  E-mail address of admin user
        --firstname,-f  First name of admin user
        --lastname, -l  Last name of admin user
        --magento,  -m  Full path to Magento installation folder (index.php folder)
        --password, -p  Default password for the admin user. Optional.
        --info,     -i  Display license info and long help


USAGE;

    }

    /**
     * returns longer version of help text
     *
     * @return string
     */
    public function longHelp()
    {
        return <<<LONGHELP
  ____       _
 / ___|  ___| |_ _   _ _ __
 \___ \ / _ \ __| | | | '_ \
  ___) |  __/ |_| |_| | |_) |
 |____/_\___|\__|\__,_| .__/    _
 |  \/  | __ _  __ _  |_| _ __ | |_ ___
 | |\/| |/ _` |/ _` |/ _ \ '_ \| __/ _ \
 | |  | | (_| | (_| |  __/ | | | || (_) |
 |_|  |_|\__,_|\__, |\___|_| |_|\__\___/
  ____         |___/                  _   _   _
 | __ )  __ _  ___| | _____ _ __   __| | | | | |___  ___ _ __
 |  _ \ / _` |/ __| |/ / _ \ '_ \ / _` | | | | / __|/ _ \ '__|
 | |_) | (_| | (__|   <  __/ | | | (_| | | |_| \__ \  __/ |
 |____/ \__,_|\___|_|\_\___|_| |_|\__,_|  \___/|___/\___|_|

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

        This script will set up a Magento Backend (admin) user and it will send
        password reset link to the e-mail address specified as a parameter to this script.

        This script is meant to be run by MageFlow Instance Setup Process but it can also
        be used by site administrators to set up backend users.

        Be careful with all the parameters if you still want to run this script manually.


LONGHELP;

    }

    /**
     * Main entry to program
     */
    public function run()
    {
        $shortOptions = 'v::h::e:f:l:m:p:li';
        $longOptions = array(
            'verbose',
            'help',
            'email:',
            'firstname:',
            'lastname:',
            'magento:',
            'password:',
            'info'
        );
        $options = getopt($shortOptions, $longOptions);
        if (count($options) === 0
            || (isset($options['h'])
                || isset($options['help']))
        ) {
            echo $this->usageHelp();
            exit();
        }
        if (isset($options['i']) || isset($options['info'])) {
            echo $this->longHelp();
            echo $this->usageHelp();
            exit();
        }
        $magentoPath = $this->parseOption($options, 'm', 'magento');
        if (!is_null($magentoPath)) {
            include_once $magentoPath . '/app/Mage.php';
        } else {
            //find Magento
            $globArr = glob('../**/app/Mage.php');
            if (!include_once $globArr[0]) {
                exit("Magento's Mage.php not found. Please specify full path " .
                    "to Magento\n");
            }
        }

        $email = $this->parseOption($options, 'e', 'email');
        $firstName = $this->parseOption($options, 'f', 'firstname');
        $lastName = $this->parseOption($options, 'l', 'lastname');
        $password = $this->parseOption($options, 'p', 'password');
        $verbose = $this->parseOption($options, 'v', 'verbose');
        if ($verbose) {
            echo "This script creates admin user to current Magento instance\n" .
                "Please enter data as asked.\n";
        }
        $this->createUser($email, $firstName, $lastName, $password, $verbose);

    }

    /**
     * Parses command line options
     *
     * @param $options
     * @param $short
     * @param $long
     *
     * @return null
     */
    function parseOption($options, $short, $long)
    {
        $value = null;
        if (isset($options[$long]) || isset($options[$short])) {
            if (isset($options[$short])) {
                $value = $options[$short];
            }
            if (isset($options[$long])) {
                $value = $options[$long];
            }
        }
        return $value;
    }

    /**
     * This method sends out password reminder e-mail. We cannot use native method in
     * Mage_Admin_Model_User because it has hardcoded store id that messes up the link
     * in e-mail:)
     * NOTE for ALL devs out there - ALWAYS avoid hardcoding ANY ID-s ANYWHERE. You can
     * thank me later;)
     *
     * ... and we don't want to rewrite too many Mage Core classes to leave them
     * to be rewritten by you - fellow Mage devs.
     *
     * @param $adminUserModel
     *
     * @return $this
     */
    private function sendPasswordResetConfirmationEmail($adminUserModel)
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo(
            $adminUserModel->getEmail(), $adminUserModel->getName()
        );
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        if (
            Mage::getStoreConfig(
                Mage_Admin_Model_User::XML_PATH_FORGOT_EMAIL_IDENTITY
            ) != ''
        ) {
            $mailer->setSender(
                Mage::getStoreConfig(
                    Mage_Admin_Model_User::XML_PATH_FORGOT_EMAIL_IDENTITY
                )
            );
        } else {
            $mailer->setSender('webmaster@' . $_SERVER['SERVER_NAME']);
        }

        $mailer->setTemplateId(
            Mage::getStoreConfig(
                Mage_Admin_Model_User::XML_PATH_FORGOT_EMAIL_TEMPLATE
            )
        );
        $mailer->setTemplateParams(
            array(
                'user' => $adminUserModel
            )
        );
        $mailer->send();

        return $this;
    }
}

$shell = new Mageflow_Connect_Create_Backend_User();
$shell->run();
