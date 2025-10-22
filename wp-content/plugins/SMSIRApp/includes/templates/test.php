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
require_once dirname(__FILE__) . "/../SMSIRAppClass.php";

global $wpdb, $table_prefix;
$url = plugins_url('/assets/', __FILE__);
wp_enqueue_style('smsIr', $url . 'css/smsir.css', true, 103);
wp_enqueue_script('smsIr', $url . 'js/smsir.js', true, 103);
?>

<div class="sms-ir-header-div">
    <h1>
        <img width="100px" src="<?= plugin_dir_url(__FILE__) . 'assets/img/logo.png' ?>">
        ارسال پیامک تستی
    </h1>
</div>
<?php if (!get_option('sms_ir_info_api_key')) { ?>
    <div class="error">
        <p>متاسفانه تنظیمات مربوط به حساب پیامکی شما انجام نشده است.</p>
    </div>
<?php } ?>
<?php
if (isset($_POST["sms_ir_test_button"]) && ($_POST["sms_ir_test_button"])) {
    if ((!get_option('sms_ir_info_api_key')) || (!get_option('sms_ir_info_number'))) { ?>
        <div class="error">
            <p>کلید وب سرویس و یا شماره پیامکی در سیستم ثبت نشده است.</p>
        </div>
    <?php } else {
        $sms = SMSIRAppClass::sendBulkSMS($_POST["sms_ir_text"], [$_POST["sms_ir_mobile"]]);
        if ($sms->status == 1) { ?>
            <div class="updated">
                <p>ارسال پیامک با موفقیت انجام شد.</p>
            </div>
        <?php } else { ?>
            <div class="error">
                <p><?= $sms->message ?></p>
            </div>
        <?php }
    }
}
?>
<div class="row sms-ir-main-div sms-ir-setting-main-div" style="width: 98%">
    <div class="col-md-12" id="smsIrTabContent">
        <form action="" method="post">
            <table class="form-table sms-ir-form-table">
                <tbody>
                <tr>
                    <td class="form-table-input">
                        <p class="sms-ir-label">شماره موبایل:
                            <i class="dashicons dashicons-info"
                               title="شماره موبایل مقصد را به منظور ارسال پیامک تستی وارد نمایید. در نظر داشته باشید که این پیامک صرفا به منظور تست فرآیند ارسال پیامک بوده و هیچ گونه کاربرد دیگری ندارد."></i>
                        </p>
                    </td>
                    <td class="form-table-input">
                        <input type="tel" name="sms_ir_mobile" class="sms-ir-form-control" placeholder="0912*******"
                               required value="<?php if (isset($_POST["sms_ir_mobile"])) {
                            echo trim($_POST["sms_ir_mobile"]);
                        } ?>">
                    </td>
                </tr>
                <tr>
                    <td class="form-table-input">
                        <p class="sms-ir-label">متن پیامک:
                            <i class="dashicons dashicons-info"
                               title="یک متن تستی وارد کنید تا از صحت و عملکرد دقیق مراحل ارسال پیامک اطمینان پیدا کنید. در صورتی که تنظیمات ماژول به درستی انجام نشده باشد، فرآیند ارسال پیامک با مشکل مواجه شده و خطای مربوطه را به شما نمایش داده خواهد شد."></i>
                        </p>
                    </td>
                    <td class="form-table-input">
                        <textarea name="sms_ir_text" cols="5" class="sms-ir-form-control" placeholder="باسلام**********"
                                  required><?php if (isset($_POST["sms_ir_text"])) {
                                echo trim($_POST["sms_ir_text"]);
                            } ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="tab-content-tips">جهت کسب اطلاعات بیشتر به وبسایت <a href="https://sms.ir/" target="_blank">www.sms.ir</a>مراجعه
                نمایید.</p>
            <button type="submit" name="sms_ir_test_button" value="1" class="sms-ir-test-button button-primary">تست
                ارسال پیامک
            </button>
        </form>
    </div>
</div>
