<?php
/**
 *
 * @category  PLugins
 * @package   Wordpress
 * @author    IPdev.ir
 * @copyright 2022 The Ideh Pardazan (ipe.ir). All rights reserved.
 * @license   https://sms.ir/ ipe license
 * @version   IPE: 1.0.19
 * @link      https://app.sms.ir
 *
 */

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

add_action('admin_menu', 'SMSIRAppAdmin');

/**
 * @return void
 */
function SMSIRAppAdminMain()
{
    include dirname(__FILE__) . '/templates/main.php';
}

/**
 * @return void
 */
function SMSIRAppAdminSetting()
{
    include dirname(__FILE__) . '/templates/settings.php';
}

/**
 * @return void
 */
function SMSIRAppAdminTest()
{
    include dirname(__FILE__) . '/templates/test.php';
}

/**
 * @return void
 */
function SMSIRAppAdminSend()
{
    include dirname(__FILE__) . '/templates/send.php';
}

/**
 * @return void
 */
function SMSIRAppAdminReceive()
{
    include dirname(__FILE__) . '/templates/receive.php';
}

/**
 * @return void
 */
function SMSIRAppAdminLog()
{
    include dirname(__FILE__) . '/templates/log.php';
}

/**
 * @return void
 */
function SMSIRAppAdminInventory()
{
    include dirname(__FILE__) . '/templates/inventory.php';
}

/**
 * @return void
 */
function SMSIRAppAdminPromotion()
{
    include dirname(__FILE__) . '/templates/promotion.php';
}

/**
 * @return void
 */
function SMSIRAppAdminashes()
{
	include dirname(__FILE__) . '/templates/ashes.php';
}
