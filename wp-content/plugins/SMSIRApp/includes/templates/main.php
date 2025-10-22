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

$url = plugins_url('/assets/', __FILE__);
wp_enqueue_style('smsIr', $url . 'css/smsir.css', true, 103);
wp_enqueue_script('smsIr', $url . 'js/smsir.js', true, 103);
require_once dirname(__FILE__) . "/../SMSIRAppClass.php";

$receivesCount = 0;
$sendsCount = 0;
$linesCount = 0;
$credit = 0;
if (get_option('sms_ir_info_api_key')) {
    $receivesObject = SMSIRAppClass::getReceiveReport();
    $sendsObject = SMSIRAppClass::getSendReport();
    $linesObject = SMSIRAppClass::getLine();
    $creditObject = SMSIRAppClass::getCredit();

    if ($receivesObject->status == 1) {
        $receivesCount = count($receivesObject->data);
    }
    if ($receivesObject->status == 1) {
        $sendsCount = count($sendsObject->data);
    }
    if ($receivesObject->status == 1) {
        $credit = $creditObject->data;
    }
    if ($linesObject->status == 1) {
        $linesCount = count($linesObject->data);
    }
}
if ((isset($_POST["deactivate"])) && ($_POST["deactivate"])) {
    deactivate_plugins("WoocommercePluginSMSIR-V3.1/WoocommerceIR_SMS.php");
}
?>
<div class="sms-ir-header-div">
    <h1>
        <img width="100px" src="<?= plugin_dir_url(__FILE__) . 'assets/img/logo.svg' ?>">
        ماژول پیامکی sms.ir
    </h1>
</div>
<div class="sms-ir-main-div">
    <table class="form-table">
        <tbody>
        <tr>
            <td>
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="dashicons dashicons-money-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">اعتبار پنل (پیامک)</span>
                        <span class="info-box-number"><?php echo $credit ?></span>
                    </div>
                </div>
            </td>
            <td>
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="dashicons dashicons-format-status"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">خطوط پیامکی فعال</span>
                        <span class="info-box-number"><?php echo $linesCount ?></span>
                    </div>
                </div>
            </td>
            <td>
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="dashicons dashicons-upload"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">پیامک های ارسالی امروز</span>
                        <span class="info-box-number"><?php echo $sendsCount ?></span>
                    </div>
                </div>
            </td>
            <td>
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="dashicons dashicons-download"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">پیامک های دریافتی امروز</span>
                        <span class="info-box-number"><?php echo $receivesCount ?></span>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
    <?php if (get_option('sms_ir_info_api_key')) { ?>
        <div class="updated">
            <p>تنظیمات مربوط به حساب پیامکی شما با موفقیت در سیستم ثبت شده است.</p>
        </div>
    <?php } else { ?>
        <div class="error">
            <p>متاسفانه تنظیمات مربوط به حساب پیامکی شما انجام نشده است.</p>
        </div>
    <?php } ?>
    <?php if (is_plugin_active("WoocommercePluginSMSIR-V3.1/WoocommerceIR_SMS.php")) { ?>
        <div class="error">
            <p>نصب همزمان این افزونه با افزونه ارسال پیامک ووکامرس sms.ir، باعث بروز مشکلاتی در وبسایت شما خواهد شد.
                لطفا نسبت به غیر فعال کردن ماژول مورد نظر اقدام فرمایید.</p>
            <form method="post" action="">
                <input type="hidden" name="deactivate" value="1">
                <button type="submit" class="button button-error">غیر فعال کردن</button>
            </form>
        </div>
    <?php } elseif ((isset($_POST["deactivate"])) && ($_POST["deactivate"]) && (!is_plugin_active("WoocommercePluginSMSIR-V3.1/WoocommerceIR_SMS.php"))) { ?>
        <div class="updated">
            <p>افزونه ارسال پیامک ووکامرس با موفقیت غیر فعال شد.</p>
        </div>
    <?php } ?>
    <hr>
    <table class="form-table">
        <tbody>
        <tr>
            <td class="sms-ir-main-footer-text">
                <p>تهران، خیابان آزادی، ناحیه نوآوری شریف، بلوار اکبری، ابتدای کوچه اتکا (ناهید)، پلاک۷، برج فناوری، طبقه پنجم، شرکت sms.ir شماره تماس :021-2853</p>

            </td>
        </tr>
        <tr>
            <td class="sms-ir-main-footer-text">
                <p>"© تمامی حقوق این محصول متعلق به سامانه پیامکی SMS.ir است."</p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
