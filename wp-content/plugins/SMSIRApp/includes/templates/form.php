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

$url = plugins_url('/assets/', __FILE__);
wp_enqueue_style('smsIr', $url . 'css/smsir.css', true, 103);
wp_enqueue_script('smsIr', $url . 'js/smsir.js', true, 103);

global $product, $wpdb, $table_prefix;

if (isset($_POST["sms_ir_type"]) && $_POST["sms_ir_type"]) {
    if (empty($_POST["sms_ir_name"])) {
        $error = "لطفا نام خود را وارد کنید.";
    } elseif (!preg_match("/^09[0-9]{9}$/", $_POST["sms_ir_mobile"])) {
        $error = "شماره موبایل وارد شده نامعتبر است.";
    } elseif (!in_array($_POST["sms_ir_type"], ["inventory", "promotion"])) {
        $error = "مقادیر وارد شده نامعتبر است.";
    } else {
        $notification = $wpdb->get_var($wpdb->prepare("SELECT `id` FROM `{$table_prefix}sms_ir_app_notifications` WHERE `type` = '%s' AND `mobile` = '%d' AND `product_id` = '%d'", $_POST["sms_ir_type"], $_POST["sms_ir_mobile"], $product->get_id()));
        if (!is_null($notification)) {
            $error = "شماره موبایل شما تکراری است.";
        }
    }

    if (!isset($error)) {
        $table = $table_prefix . 'sms_ir_app_notifications';
        $data = [
            'product_id' => $product->get_id(),
            'type'       => $_POST["sms_ir_type"],
            'name'       => $_POST["sms_ir_name"],
            'mobile'     => $_POST["sms_ir_mobile"]
        ];
        $format = ['%d', '%s', '%s', '%d'];
        $wpdb->insert($table, $data, $format);

        if (!$wpdb->insert_id) {
            $error = "ذخیره سازی اطلاعات با مشکل مواجه گردید.";
        } else {
            $message = "اطلاعات شما با موفقیت ذخیره سازی شد.";
        }
    }
}

if ((!$product->is_in_stock()) && (get_option("sms_ir_woocommerce_inventory_user_status"))) { ?>
    <section class="sms-ir-client-form sms-ir-inventory-form">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h5>موجود شد خبرم کن!</h5>
            </div>
            <div class="panel-body">
                <?php if (isset($error) && $error) { ?>
                    <p style="color: red"><?php echo $error; ?></p>
                <?php } elseif (isset($message) && $message) { ?>
                    <p style="color: green"><?php echo $message; ?></p>
                <?php } ?>
                <form action="" method="post">
                    <input type="text" name="sms_ir_name" class="woocommerce-Input woocommerce-Input--text input-text form-control" value="<?php echo (isset($_POST["sms_ir_name"]) && $_POST["sms_ir_name"])? $_POST["sms_ir_name"]: '' ?>" placeholder="نام خود را وارد کنید">
                    <input type="tel" name="sms_ir_mobile" class="woocommerce-Input woocommerce-Input--text input-text form-control" value="<?php echo (isset($_POST["sms_ir_mobile"]) && $_POST["sms_ir_mobile"])? $_POST["sms_ir_mobile"]: '' ?>" placeholder="شماره موبابل خود را وارد کنید">
                    <input type="hidden" name="sms_ir_type" value="inventory">
                    <button type="submit" class="woocommerce-button button btn btn-primary">ثبت</button>
                </form>
            </div>
        </div>
    </section>
<?php } ?>
<?php if ((!$product->is_on_sale()) && (get_option("sms_ir_woocommerce_promotion_user_status"))) { ?>
    <section class="sms-ir-client-form sms-ir-promotion-form">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h5>تخفیف دار شد خبرم کن!</h5>
            </div>
            <div class="panel-body">
                <?php if (isset($error) && $error) { ?>
                    <p style="color: red"><?php echo $error; ?></p>
                <?php } elseif (isset($message) && $message) { ?>
                    <p style="color: green"><?php echo $message; ?></p>
                <?php } ?>
                <form action="" method="post">
                    <input type="text" name="sms_ir_name" class="woocommerce-Input woocommerce-Input--text input-text form-control" value="<?php echo (isset($_POST["sms_ir_name"]) && $_POST["sms_ir_name"])? $_POST["sms_ir_name"]: '' ?>" placeholder="نام خود را وارد کنید">
                    <input type="tel" name="sms_ir_mobile" class="woocommerce-Input woocommerce-Input--text input-text form-control" value="<?php echo (isset($_POST["sms_ir_mobile"]) && $_POST["sms_ir_mobile"])? $_POST["sms_ir_mobile"]: '' ?>" placeholder="شماره موبابل خود را وارد کنید">
                    <input type="hidden" name="sms_ir_type" value="promotion">
                    <button type="submit" class="woocommerce-button button btn btn-primary">ثبت</button>
                </form>
            </div>
        </div>
    </section>
<?php } ?>
