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
date_default_timezone_set('Asia/Tehran');

require_once dirname(__FILE__) . "/../SMSIRAppClass.php";

$url = plugins_url('/assets/', __FILE__);
wp_enqueue_style('smsIr', $url . 'css/smsir.css', true, 103);
wp_enqueue_style('dataTable', $url . 'css/jquery.dataTables.min.css', true, 103);
wp_enqueue_script('smsIr', $url . 'js/smsir.js', true, 103);
wp_enqueue_script('dataTable', $url . 'js/jquery.dataTables.min.js', true, 103);

$sends = [];
if (get_option('sms_ir_info_api_key')) {
    $sends = SMSIRAppClass::getSendReport();
    $sends = $sends->data;
}
?>

<div class="sms-ir-header-div">
    <h1>
        <img width="100px" src="<?= plugin_dir_url(__FILE__) . 'assets/img/logo.png' ?>">
        پیامک های ارسالی
    </h1>
</div>
<?php if (!get_option('sms_ir_info_api_key')) { ?>
    <div class="error">
        <p>متاسفانه تنظیمات مربوط به حساب پیامکی شما انجام نشده است.</p>
    </div>
<?php } ?>
<div class="row sms-ir-main-div sms-ir-list-div" id="smsIrTabContent">
    <p>لیست زیر نمایش دهنده تمامی پیام های ارسالی امروز شما می باشد. به منظور مشاهده آرشیو پیام ها می توانید به لینک <a
                href="https://app.sms.ir/report/web-service" target="_blank">sms.ir/send</a> مراجعه نمایید.</p>
    <hr>
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th>شناسه</th>
                <th>متن</th>
                <th>شماره فرستنده</th>
                <th>شماره گیرنده</th>
                <th>وضعیت</th>
                <th>زمان ارسال</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($sends as $send) { ?>
                <tr>
                    <td><?= $send->messageId ?></td>
                    <td><?= $send->messageText ?></td>
                    <td><?= $send->lineNumber ?></td>
                    <td><?= $send->mobile ?></td>
                    <td><?= SMSIRAppClass::STATES[$send->deliveryState] ?></td>
                    <td><?= date("H:i:s", $send->sendDateTime) ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
            <tr>
                <th>شناسه</th>
                <th>متن</th>
                <th>شماره فرستنده</th>
                <th>شماره گیرنده</th>
                <th>وضعیت</th>
                <th>زمان ارسال</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
