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

global $wpdb, $table_prefix;
$url = plugins_url('/assets/', __FILE__);
wp_enqueue_style('smsIr', $url . 'css/smsir.css', true, 103);
wp_enqueue_script('smsIr', $url . 'js/smsir.js', true, 103);

if (isset($_POST["sms_ir_setting_button"])) {
    foreach ($_POST as $key => $post) {
        if (((strpos($key, 'sms_ir_') === 0)) && (strlen(trim($post)))) {
            update_option($key, $post);
        } else {
            delete_option($key);
        }
    }
    echo "<div class='updated'><p>تغییرات با موفقیت در سیستم ثبت شد.</p></div>";
}

$contactForms = $wpdb->get_results("SELECT p.`ID` AS `id`, p.`post_title` AS `title`, pm.`meta_value` FROM `{$table_prefix}posts` AS p JOIN `{$table_prefix}postmeta` AS pm ON pm.`post_id` = p.`ID` AND pm.`meta_key` = '_form' WHERE p.`post_type` = 'wpcf7_contact_form'");

$gravityForms = [];
$tableName = "{$table_prefix}gf_form";
if ($wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) {
    $gravityForms = $wpdb->get_results("SELECT gf.`id`, gf.`title`, gfm.`display_meta` FROM `{$table_prefix}gf_form` AS gf JOIN `{$table_prefix}gf_form_meta` AS gfm ON gf.`id` = gfm.`form_id`");
}

$woocommerceStatuses = [];
if (class_exists('woocommerce')) {
    $woocommerceStatuses = wc_get_order_statuses();
}
?>

<div class="sms-ir-header-div">
    <h1>
        <img width="100px" src="<?= plugin_dir_url(__FILE__) . 'assets/img/logo.png' ?>">
        تنظیمات ماژول پیامکی
    </h1>
</div>
<?php if (!get_option('sms_ir_info_api_key')) { ?>
    <div class="error">
        <p>متاسفانه تنظیمات مربوط به حساب پیامکی شما انجام نشده است.</p>
    </div>
<?php } ?>
<?php if (count($contactForms) == 0) { ?>
    <div class="error">
        <p>متاسفانه افزونه Contact7 در وبسایت شما فعال نیست و یا هیچ فرمی ایجاد نشده است.</p>
    </div>
<?php } ?>
<?php if (count($gravityForms) == 0) { ?>
    <div class="error">
        <p>متاسفانه افزونه GravityForm در وبسایت شما فعال نیست و یا هیچ فرمی ایجاد نشده است.</p>
    </div>
<?php } ?>
<?php if (count($woocommerceStatuses) == 0) { ?>
    <div class="error">
        <p>متاسفانه افزونه WooCommerce در وبسایت شما فعال نیست و یا هیچ وضعیت سفارشی ایجاد نشده است.</p>
    </div>
<?php } ?>
<div class="row sms-ir-main-div sms-ir-setting-main-div">
    <div class="col-md-12" id="smsIrTabMenu">
        <div class="nav-tab-wrapper">
            <a href="#Info" onclick="smsIrChangeTab('Info')" class="nav-tab nav-tab-active">اطلاعات</a>
            <a href="#Gravity" onclick="smsIrChangeTab('Gravity')" class="nav-tab ">Gravity</a>
            <a href="#Contact" onclick="smsIrChangeTab('Contact')" class="nav-tab ">Contact7</a>
            <a href="#Woocommerce" onclick="smsIrChangeTab('Woocommerce')" class="nav-tab ">WooCommerce</a>
            <a href="#Wordpress" onclick="smsIrChangeTab('Wordpress')" class="nav-tab ">Wordpress</a>
        </div>
    </div>
    <div class="col-md-12" id="smsIrTabContent">
        <form action="" method="post">
            <div class="sms-ir-tab" id="smsIrInfoTab">
                <table class="form-table sms-ir-form-table">
                    <tbody>
                    <tr>
                        <td class="form-table-input">
                            <p class="sms-ir-label">شماره موبایل مدیر کل سایت:
                                <i class="dashicons dashicons-info"
                                   title="هنگام ارسال پیامک به مدیران قسمت های مختلف، به این شماره موبایل نیز پیامک ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-input">
                            <input type="tel" name="sms_ir_info_admin" class="sms-ir-form-control" id="smsIrInfoAdmin"
                                   value="<?= get_option('sms_ir_info_admin') ?>" placeholder="0912*******">
                            <p class="sms-ir-form-text">
                                <small>در صورت وارد کردن شماره موبایل، در تمامی قسمت ها علاوه بر مدیر، به مدیر کل نیز
                                    پیامک ارسال خواهد شد.</small>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-table-input">
                            <p class="sms-ir-label">شماره پیامکی:
                                <i class="dashicons dashicons-info"
                                   title="تمامی پیامک های اطلاع رسانی بدون قالب از طریق این شماره ارسال خواهند شد. به منظور مشاهده و خرید شماره پیامکی، به پورتال sms.ir، قسمت شماره پیامکی مراجعه کنید."></i>
                            </p>
                        </td>
                        <td class="form-table-input">
                            <input type="tel" name="sms_ir_info_number" class="sms-ir-form-control" id="smsIrInfoNumber"
                                   value="<?= get_option('sms_ir_info_number') ?>" placeholder="3000******">
                            <p class="sms-ir-form-text">
                                <small>شماره ارسال کننده پیامک در پنل <a href="https://sms.ir/"
                                                                         target="_blank">sms.ir</a> را وارد
                                    نمایید.</small>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-table-input">
                            <p class="sms-ir-label">کلید وب سرویس:
                                <i class="dashicons dashicons-info"
                                   title="کلید وب سرویس، کد یکتایی است که به منظور احراز هویت و صحت سنجی ارسال کننده پیامک ها مورد استفاده قرار می گیرد و در صورت ثبت نکردن و یا ثبت اشتباه آن، ارسال پیامک از طریق این ماژول امکان پذیر نخواهد بود. جهت ساخت این کلید به منوی برنامه نویسان در پورتال sms.ir مراجعه نمایید."></i>
                            </p>
                        </td>
                        <td class="form-table-input">
                            <input type="text" name="sms_ir_info_api_key" class="sms-ir-form-control"
                                   id="smsIrInfoApiKey" value="<?= get_option('sms_ir_info_api_key') ?>"
                                   placeholder="YV4A5LS9JehIN9HgNWg7V***************************************" required>
                            <p class="sms-ir-form-text">
                                <small>کلید وب سرویس خود در پنل <a href="https://sms.ir/" target="_blank"> sms.ir </a>را
                                    وارد
                                    نمایید.</small>
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p class="tab-content-tips">جهت کسب اطلاعات بیشتر به وبسایت <a href="https://sms.ir/" target="_blank">www.sms.ir</a>
                    مراجعه نمایید.</p>
                <button type="submit" name="sms_ir_setting_button" value="1"
                        class="sms-ir-setting-button button-primary">ذخیره سازی
                </button>
            </div>
            <div class="sms-ir-tab" style="display: none" id="smsIrGravityTab">
                <p class="sms-ir-head-label">ارسال پیامک هنگام تکمیل فرم های شما:
                    <i class="dashicons dashicons-info"
                       title="تمامی فرم های شما که توسط افزونه Gravity ایجاد شده اند در لیست زیر قرار داده شده است. شما می توانید با انتخاب هر یک از آن ها، نسبت به ثبت تنظیمات مورد نظر خود اقدام فرمایید. پس از فعال سازی هر یک از فرم ها، در صورت تکمیل فرم توسط کاربر پیامک های مربوطه به ادمین سایت و خود کاربر ارسال خواهد شد."></i>
                </p>
                <div class="gravity-form-list">
                    <table class="form-table sms-ir-form-table">
                        <tbody>
                        <?php foreach ($gravityForms as $form) {
                            $parameters = "#FieldTypeFiledID#: ";
                            $displayMeta = json_decode($form->display_meta);
                            foreach ($displayMeta->fields as $field) {
                                $fieldType = str_replace('_', '', $field->type);
                                $parameters .= "<span class='sms-ir-setting-parameter'> #$fieldType$field->id# </span>,";
                            }
                            ?>
                            <tr>
                                <td class="form-table-checkbox">
                                    <p class="sms-ir-label"><?= $form->title ?>:</p>
                                </td>
                                <td class="form-table-checkbox">
                                    <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                           name="sms_ir_gravity_admin_<?= $form->id ?>" <?php if (get_option("sms_ir_gravity_admin_{$form->id}_status")) { ?> checked <?php } ?>>
                                    <input type="hidden" name="sms_ir_gravity_admin_<?= $form->id ?>_status"
                                           value="<?= get_option("sms_ir_gravity_admin_{$form->id}_status") ?>">
                                    <input type="hidden" name="sms_ir_gravity_admin_<?= $form->id ?>_mobile"
                                           value="<?= get_option("sms_ir_gravity_admin_{$form->id}_mobile") ?>">
                                    <input type="hidden" name="sms_ir_gravity_admin_<?= $form->id ?>_template"
                                           value="<?= get_option("sms_ir_gravity_admin_{$form->id}_template") ?>">
                                    <input type="hidden" name="sms_ir_gravity_admin_<?= $form->id ?>_text"
                                           value="<?= get_option("sms_ir_gravity_admin_{$form->id}_text") ?>">
                                    <span name="sms_ir_gravity_admin_<?= $form->id ?>_parameter"
                                          class="hidden"><?= $parameters ?></span>
                                    <i class="dashicons dashicons-visibility"
                                       data-name="sms_ir_gravity_admin_<?= $form->id ?>" <?php if (!get_option("sms_ir_gravity_admin_{$form->id}_status")) { ?> style="display: none" <?php } ?>></i>
                                    <span>به مدیر</span>
                                </td>
                                <td class="form-table-checkbox">
                                    <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                           name="sms_ir_gravity_user_<?= $form->id ?>"
                                           data-type="user" <?php if (get_option("sms_ir_gravity_user_{$form->id}_status")) { ?> checked <?php } ?>>
                                    <input type="hidden" name="sms_ir_gravity_user_<?= $form->id ?>_status"
                                           value="<?= get_option("sms_ir_gravity_user_{$form->id}_status") ?>">
                                    <input type="hidden" name="sms_ir_gravity_user_<?= $form->id ?>_mobile"
                                           value="<?= get_option("sms_ir_gravity_user_{$form->id}_mobile") ?>">
                                    <input type="hidden" name="sms_ir_gravity_user_<?= $form->id ?>_template"
                                           value="<?= get_option("sms_ir_gravity_user_{$form->id}_template") ?>">
                                    <input type="hidden" name="sms_ir_gravity_user_<?= $form->id ?>_text"
                                           value="<?= get_option("sms_ir_gravity_user_{$form->id}_text") ?>">
                                    <span name="sms_ir_gravity_user_<?= $form->id ?>_parameter"
                                          class="hidden"><?= $parameters ?></span>
                                    <i class="dashicons dashicons-visibility"
                                       data-name="sms_ir_gravity_user_<?= $form->id ?>" data-type="user"
                                        <?php if (!get_option("sms_ir_gravity_user_{$form->id}_status")) { ?> style="display: none" <?php } ?>></i>
                                    <span>به کاربر</span>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <table class="form-table sms-ir-form-table">
                    <tbody>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک هنگام ایجاد فرم:
                                <i class="dashicons dashicons-info"
                                   title="با فعالسازی این قابلیت هنگام ایجاد فرم جدید در Gravity، پیامک اطلاع رسانی به مدیر سایت ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                   name="sms_ir_gravity_create_admin" <?php if (get_option("sms_ir_gravity_create_admin_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_gravity_create_admin_status"
                                   value="<?= get_option("sms_ir_gravity_create_admin_status") ?>">
                            <input type="hidden" name="sms_ir_gravity_create_admin_mobile"
                                   value="<?= get_option("sms_ir_gravity_create_admin_mobile") ?>">
                            <input type="hidden" name="sms_ir_gravity_create_admin_template"
                                   value="<?= get_option("sms_ir_gravity_create_admin_template") ?>">
                            <input type="hidden" name="sms_ir_gravity_create_admin_text"
                                   value="<?= get_option("sms_ir_gravity_create_admin_text") ?>">
                            <span name="sms_ir_gravity_create_admin_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#title#</span>,
                                <span class='sms-ir-setting-parameter'>#description#</span>
                            </span>
                            <i data-name="sms_ir_gravity_create_admin"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_gravity_create_admin_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به مدیر</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک هنگام ویرایش فرم:
                                <i class="dashicons dashicons-info"
                                   title="با فعالسازی این قابلیت هنگام ویرایش هریک از فرم های Gravity، پیامک اطلاع رسانی به همراه جزئیات مورد نیاز به مدیر سایت ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                   name="sms_ir_gravity_edit_admin" <?php if (get_option("sms_ir_gravity_edit_admin_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_gravity_edit_admin_status"
                                   value="<?= get_option("sms_ir_gravity_edit_admin_status") ?>">
                            <input type="hidden" name="sms_ir_gravity_edit_admin_mobile"
                                   value="<?= get_option("sms_ir_gravity_edit_admin_mobile") ?>">
                            <input type="hidden" name="sms_ir_gravity_edit_admin_template"
                                   value="<?= get_option("sms_ir_gravity_edit_admin_template") ?>">
                            <input type="hidden" name="sms_ir_gravity_edit_admin_text"
                                   value="<?= get_option("sms_ir_gravity_edit_admin_text") ?>">
                            <span name="sms_ir_gravity_edit_admin_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#title#</span>,
                                <span class='sms-ir-setting-parameter'>#description#</span>
                            </span>
                            <i data-name="sms_ir_gravity_edit_admin"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_gravity_edit_admin_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به مدیر</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک هنگام تاییدیه پرداخت:
                                <i class="dashicons dashicons-info"
                                   title="در صورتی که از افزونه Gravity به منظور ایجاد فرم پرداخت استفاده می کنید، می توانید این قابلیت را فعال سازی نمایید. پس از اعمال تنظیمات در سیستم، به کاربرانی که تراکنش موفق در درگاه بانکی داشته باشند، به محض بازگشت به سایت، پیامک تاییدیه به همراه جزئیات پرداخت ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                   name="sms_ir_gravity_confirm_transaction_admin" <?php if (get_option("sms_ir_gravity_confirm_transaction_admin_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_gravity_confirm_transaction_admin_status"
                                   value="<?= get_option("sms_ir_gravity_confirm_transaction_admin_status") ?>">
                            <input type="hidden" name="sms_ir_gravity_confirm_transaction_admin_mobile"
                                   value="<?= get_option("sms_ir_gravity_confirm_transaction_admin_mobile") ?>">
                            <input type="hidden" name="sms_ir_gravity_confirm_transaction_admin_template"
                                   value="<?= get_option("sms_ir_gravity_confirm_transaction_admin_template") ?>">
                            <input type="hidden" name="sms_ir_gravity_confirm_transaction_admin_text"
                                   value="<?= get_option("sms_ir_gravity_confirm_transaction_admin_text") ?>">
                            <span name="sms_ir_gravity_confirm_transaction_admin_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#formid#</span>,
                                <span class='sms-ir-setting-parameter'>#amount#</span>,
                                <span class='sms-ir-setting-parameter'>#transaction#</span>,
                                <span class='sms-ir-setting-parameter'>#gateway#</span>
                            </span>
                            <i data-name="sms_ir_gravity_confirm_transaction_admin"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_gravity_confirm_transaction_admin")) { ?> style="display:none;" <?php } ?>></i>
                            <span>به مدیر</span>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="user"
                                   name="sms_ir_gravity_confirm_transaction_user" <?php if (get_option("sms_ir_gravity_confirm_transaction_user_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_gravity_confirm_transaction_user_status"
                                   value="<?= get_option("sms_ir_gravity_confirm_transaction_user_status") ?>">
                            <input type="hidden" name="sms_ir_gravity_confirm_transaction_user_mobile"
                                   value="<?= get_option("sms_ir_gravity_confirm_transaction_user_mobile") ?>">
                            <input type="hidden" name="sms_ir_gravity_confirm_transaction_user_template"
                                   value="<?= get_option("sms_ir_gravity_confirm_transaction_user_template") ?>">
                            <input type="hidden" name="sms_ir_gravity_confirm_transaction_user_text"
                                   value="<?= get_option("sms_ir_gravity_confirm_transaction_user_text") ?>">
                            <span name="sms_ir_gravity_confirm_transaction_user_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#formid#</span>,
                                <span class='sms-ir-setting-parameter'>#amount#</span>,
                                <span class='sms-ir-setting-parameter'>#transaction#</span>,
                                <span class='sms-ir-setting-parameter'>#gateway#</span>
                            </span>
                            <i data-name="sms_ir_gravity_confirm_transaction_user" data-type="user"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_gravity_confirm_transaction_user")) { ?> style="display: none;" <?php } ?>></i>
                            <span>به کاربر</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک هنگام رد پرداخت:
                                <i class="dashicons dashicons-info"
                                   title="در صورتی که از افزونه Gravity به منظور ایجاد فرم پرداخت استفاده می کنید، می توانید این قابلیت را فعال سازی نمایید. پس از اعمال تنظیمات در سیستم، به کاربرانی که تراکنش ناموفق در درگاه بانکی داشته باشند، به محض بازگشت به سایت، پیامک رد پرداخت به همراه جزئیات تراکنش ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                   name="sms_ir_gravity_failed_transaction_admin" <?php if (get_option("sms_ir_gravity_failed_transaction_admin_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_gravity_failed_transaction_admin_status"
                                   value="<?= get_option("sms_ir_gravity_failed_transaction_admin_status") ?>">
                            <input type="hidden" name="sms_ir_gravity_failed_transaction_admin_mobile"
                                   value="<?= get_option("sms_ir_gravity_failed_transaction_admin_mobile") ?>">
                            <input type="hidden" name="sms_ir_gravity_failed_transaction_admin_template"
                                   value="<?= get_option("sms_ir_gravity_failed_transaction_admin_template") ?>">
                            <input type="hidden" name="sms_ir_gravity_failed_transaction_admin_text"
                                   value="<?= get_option("sms_ir_gravity_failed_transaction_admin_text") ?>">
                            <span name="sms_ir_gravity_failed_transaction_admin_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#formid#</span>,
                                <span class='sms-ir-setting-parameter'>#amount#</span>,
                                <span class='sms-ir-setting-parameter'>#transaction#</span>,
                                <span class='sms-ir-setting-parameter'>#gateway#</span>
                            </span>
                            <i data-name="sms_ir_gravity_failed_transaction_admin"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_gravity_failed_transaction_admin_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به مدیر</span>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="user"
                                   name="sms_ir_gravity_failed_transaction_user" <?php if (get_option("sms_ir_gravity_failed_transaction_user_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_gravity_failed_transaction_user_status"
                                   value="<?= get_option("sms_ir_gravity_failed_transaction_user_status") ?>">
                            <input type="hidden" name="sms_ir_gravity_failed_transaction_user_mobile"
                                   value="<?= get_option("sms_ir_gravity_failed_transaction_user_mobile") ?>">
                            <input type="hidden" name="sms_ir_gravity_failed_transaction_user_template"
                                   value="<?= get_option("sms_ir_gravity_failed_transaction_user_template") ?>">
                            <input type="hidden" name="sms_ir_gravity_failed_transaction_user_text"
                                   value="<?= get_option("sms_ir_gravity_failed_transaction_user_text") ?>">
                            <span name="sms_ir_gravity_failed_transaction_user_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#formid#</span>,
                                <span class='sms-ir-setting-parameter'>#amount#</span>,
                                <span class='sms-ir-setting-parameter'>#transaction#</span>,
                                <span class='sms-ir-setting-parameter'>#gateway#</span>
                            </span>
                            <i data-name="sms_ir_gravity_failed_transaction_user" data-type="user"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_gravity_failed_transaction_user_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به کاربر</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p class="tab-content-tips">جهت کسب اطلاعات بیشتر به وبسایت <a href="https://sms.ir/" target="_blank">www.sms.ir</a>
                    مراجعه نمایید.</p>
                <button type="submit" name="sms_ir_setting_button" value="1"
                        class="sms-ir-setting-button button-primary">ذخیره سازی
                </button>
            </div>
            <div class="sms-ir-tab" style="display: none" id="smsIrContactTab">
                <p class="sms-ir-head-label">ارسال پیامک هنگام تکمیل فرم های شما:
                    <i class="dashicons dashicons-info"
                       title="تمامی فرم های تماس ایجاد شده توسط افزونه Contact7 در لیست زیر قرار داده شده است. شما می توانید با انتخاب هر یک از آن ها، نسبت به ثبت تنظیمات مورد نظر خود اقدام فرمایید. پس از فعال سازی هر یک از فرم ها، در صورت تکمیل فرم توسط کاربر پیامک های مربوطه به ادمین سایت و خود کاربر ارسال خواهد شد."></i>
                </p>
                <div class="contact-form-list">
                    <table class="form-table sms-ir-form-table">
                        <tbody>
                        <?php foreach ($contactForms as $form) {
                            preg_match_all("/\\[(.*?)\\]/", $form->meta_value, $fields);
                            $parameters = "#FieldTypeFiledID#: ";
                            foreach ($fields[1] as $field) {
                                $explodeFields = explode(" ", $field);
                                foreach ($explodeFields as $explodeField) {
                                    if (preg_match('~[\w]-[\d]~', $explodeField)) {
                                        $fieldTypeId = str_replace("-", "", $explodeField);
                                        $parameters .= "<span class='sms-ir-setting-parameter'> #$fieldTypeId# </span>,";
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <td class="form-table-checkbox">
                                    <p class="sms-ir-label"><?= $form->title ?>:</p>
                                </td>
                                <td class="form-table-checkbox">
                                    <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                           name="sms_ir_contact_admin_<?= $form->id ?>" <?php if (get_option("sms_ir_contact_admin_{$form->id}_status")) { ?> checked <?php } ?>>
                                    <input type="hidden" name="sms_ir_contact_admin_<?= $form->id ?>_status"
                                           value="<?= get_option("sms_ir_contact_admin_{$form->id}_status") ?>">
                                    <input type="hidden" name="sms_ir_contact_admin_<?= $form->id ?>_mobile"
                                           value="<?= get_option("sms_ir_contact_admin_{$form->id}_mobile") ?>">
                                    <input type="hidden" name="sms_ir_contact_admin_<?= $form->id ?>_template"
                                           value="<?= get_option("sms_ir_contact_admin_{$form->id}_template") ?>">
                                    <input type="hidden" name="sms_ir_contact_admin_<?= $form->id ?>_text"
                                           value="<?= get_option("sms_ir_contact_admin_{$form->id}_text") ?>">
                                    <span name="sms_ir_contact_admin_<?= $form->id ?>_parameter"
                                          class="hidden"><?= $parameters ?></span>
                                    <i class="dashicons dashicons-visibility"
                                       data-name="sms_ir_contact_admin_<?= $form->id ?>" <?php if (!get_option("sms_ir_contact_admin_{$form->id}_status")) { ?> style="display: none" <?php } ?>></i>
                                    <span>به مدیر</span>
                                </td>
                                <td class="form-table-checkbox">
                                    <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="user"
                                           name="sms_ir_contact_user_<?= $form->id ?>" <?php if (get_option("sms_ir_contact_user_{$form->id}_status")) { ?> checked <?php } ?>>
                                    <input type="hidden" name="sms_ir_contact_user_<?= $form->id ?>_status"
                                           value="<?= get_option("sms_ir_contact_user_{$form->id}_status") ?>">
                                    <input type="hidden" name="sms_ir_contact_user_<?= $form->id ?>_mobile"
                                           value="<?= get_option("sms_ir_contact_user_{$form->id}_mobile") ?>">
                                    <input type="hidden" name="sms_ir_contact_user_<?= $form->id ?>_template"
                                           value="<?= get_option("sms_ir_contact_user_{$form->id}_template") ?>">
                                    <input type="hidden" name="sms_ir_contact_user_<?= $form->id ?>_text"
                                           value="<?= get_option("sms_ir_contact_user_{$form->id}_text") ?>">
                                    <span name="sms_ir_contact_user_<?= $form->id ?>_parameter"
                                          class="hidden"><?= $parameters ?></span>
                                    <i class="dashicons dashicons-visibility" data-type="user"
                                       data-name="sms_ir_contact_user_<?= $form->id ?>" <?php if (!get_option("sms_ir_contact_user_{$form->id}_status")) { ?> style="display: none" <?php } ?>></i>
                                    <span>به کاربر</span>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <table class="form-table sms-ir-form-table">
                    <tbody>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک هنگام ایجاد فرم:
                                <i class="dashicons dashicons-info"
                                   title="با فعالسازی این قابلیت هنگام ایجاد فرم تماس جدید در Contact7، پیامک اطلاع رسانی به مدیر سایت ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                   name="sms_ir_contact_create_admin" <?php if (get_option("sms_ir_contact_create_admin_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_contact_create_admin_status"
                                   value="<?= get_option("sms_ir_contact_create_admin_status") ?>">
                            <input type="hidden" name="sms_ir_contact_create_admin_mobile"
                                   value="<?= get_option("sms_ir_contact_create_admin_mobile") ?>">
                            <input type="hidden" name="sms_ir_contact_create_admin_template"
                                   value="<?= get_option("sms_ir_contact_create_admin_template") ?>">
                            <input type="hidden" name="sms_ir_contact_create_admin_text"
                                   value="<?= get_option("sms_ir_contact_create_admin_text") ?>">
                            <span name="sms_ir_contact_create_admin_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#title#</span>
                            </span>
                            <i data-name="sms_ir_contact_create_admin"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_contact_create_admin_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به مدیر</span>
                        </td>
                        <td class="form-table-checkbox"></td>
                    </tr>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک هنگام ویرایش فرم:
                                <i class="dashicons dashicons-info"
                                   title="با فعالسازی این قابلیت هنگام ویرایش هریک از فرم های تماس Contact7، پیامک اطلاع رسانی به همراه جزئیات مورد نیاز به مدیر سایت ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                   name="sms_ir_contact_edit_admin" <?php if (get_option("sms_ir_contact_edit_admin_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_contact_edit_admin_status"
                                   value="<?= get_option("sms_ir_contact_edit_admin_status") ?>">
                            <input type="hidden" name="sms_ir_contact_edit_admin_mobile"
                                   value="<?= get_option("sms_ir_contact_edit_admin_mobile") ?>">
                            <input type="hidden" name="sms_ir_contact_edit_admin_template"
                                   value="<?= get_option("sms_ir_contact_edit_admin_template") ?>">
                            <input type="hidden" name="sms_ir_contact_edit_admin_text"
                                   value="<?= get_option("sms_ir_contact_edit_admin_text") ?>">
                            <span name="sms_ir_contact_edit_admin_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#title#</span>
                            </span>
                            <i data-name="sms_ir_contact_edit_admin"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_contact_edit_admin_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به مدیر</span>
                        </td>
                        <td class="form-table-checkbox"></td>
                    </tr>
                    </tbody>
                </table>
                <p class="tab-content-tips">جهت کسب اطلاعات بیشتر به وبسایت <a href="https://sms.ir/" target="_blank">www.sms.ir</a>
                    مراجعه نمایید.</p>
                <button type="submit" name="sms_ir_setting_button" value="1"
                        class="sms-ir-setting-button button-primary">ذخیره سازی
                </button>
            </div>
            <div class="sms-ir-tab" style="display: none" id="smsIrWoocommerceTab">
                <p class="sms-ir-head-label">ارسال پیامک هنگام تغییر وضعیت سفارش:
                    <i class="dashicons dashicons-info"
                       title="در افزونه ووکامرس، وضعیت های مختلفی برای سفارشات در نظر گرفته شده است. لیست زیر حاوی وضعیت های مختلف یک سفارش می باشد. شما می توانید با انتخاب و ثبت تنظیمات مورد نیاز، هنگام تغییر وضعیت سفارش از یک وضعیت به وضعیت دیگر، پیامک متناسب با آن را به کاربر و ادمین سایت ارسال نمایید."></i>
                </p>
                <div class="woocommerce-form-list">
                    <table class="form-table sms-ir-form-table">
                        <tbody>
                        <?php foreach ($woocommerceStatuses as $statusKey => $statusValue) {
                            $statusKey = substr($statusKey, 3);
                            ?>
                            <tr>
                                <td class="form-table-checkbox">
                                    <p class="sms-ir-label"><?= $statusValue ?>:</p>
                                </td>
                                <td class="form-table-checkbox">
                                    <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                           name="sms_ir_woocommerce_order_admin_<?= $statusKey ?>" <?php if (get_option("sms_ir_woocommerce_order_admin_{$statusKey}_status")) { ?> checked <?php } ?>>
                                    <input type="hidden" name="sms_ir_woocommerce_order_admin_<?= $statusKey ?>_status"
                                           value="<?= get_option("sms_ir_woocommerce_order_admin_{$statusKey}_status") ?>">
                                    <input type="hidden" name="sms_ir_woocommerce_order_admin_<?= $statusKey ?>_mobile"
                                           value="<?= get_option("sms_ir_woocommerce_order_admin_{$statusKey}_mobile") ?>">
                                    <input type="hidden"
                                           name="sms_ir_woocommerce_order_admin_<?= $statusKey ?>_template"
                                           value="<?= get_option("sms_ir_woocommerce_order_admin_{$statusKey}_template") ?>">
                                    <input type="hidden" name="sms_ir_woocommerce_order_admin_<?= $statusKey ?>_text"
                                           value="<?= get_option("sms_ir_woocommerce_order_admin_{$statusKey}_text") ?>">
                                    <span name="sms_ir_woocommerce_order_admin_<?= $statusKey ?>_parameter"
                                          class="hidden">
                                        <span class='sms-ir-setting-parameter'>#orderid#</span>,
                                        <span class='sms-ir-setting-parameter'>#trackingcode#</span>,
                                        <span class='sms-ir-setting-parameter'>#description#</span>,
                                        <span class='sms-ir-setting-parameter'>#paymentmethod#</span>,
                                        <span class='sms-ir-setting-parameter'>#oldstatus#</span>,
                                        <span class='sms-ir-setting-parameter'>#newstatus#</span>,
                                        <span class='sms-ir-setting-parameter'>#price#</span>,
                                        <span class='sms-ir-setting-parameter'>#discount#</span>,
                                        <span class='sms-ir-setting-parameter'>#shipping#</span>,
                                        <span class='sms-ir-setting-parameter'>#items#</span>,
                                        <span class='sms-ir-setting-parameter'>#itemscount#</span>,
                                        <span class='sms-ir-setting-parameter'>#productscount#</span>,
                                        <span class='sms-ir-setting-parameter'>#firstname#</span>,
                                        <span class='sms-ir-setting-parameter'>#lastname#</span>,
                                        <span class='sms-ir-setting-parameter'>#company#</span>,
                                        <span class='sms-ir-setting-parameter'>#address1#</span>,
                                        <span class='sms-ir-setting-parameter'>#address2#</span>,
                                        <span class='sms-ir-setting-parameter'>#city#</span>,
                                        <span class='sms-ir-setting-parameter'>#state#</span>,
                                        <span class='sms-ir-setting-parameter'>#postcode#</span>,
                                        <span class='sms-ir-setting-parameter'>#country#</span>,
                                        <span class='sms-ir-setting-parameter'>#email#</span>,
                                        <span class='sms-ir-setting-parameter'>#phone#</span>
                                    </span>
                                    <i class="dashicons dashicons-visibility"
                                       data-name="sms_ir_woocommerce_order_admin_<?= $statusKey ?>" <?php if (!get_option("sms_ir_woocommerce_order_admin_{$statusKey}_status")) { ?> style="display: none" <?php } ?>></i>
                                    <span>به مدیر</span>
                                </td>
                                <td class="form-table-checkbox">
                                    <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                           data-type="order"
                                           name="sms_ir_woocommerce_order_user_<?= $statusKey ?>" <?php if (get_option("sms_ir_woocommerce_order_user_{$statusKey}_status")) { ?> checked <?php } ?>>
                                    <input type="hidden" name="sms_ir_woocommerce_order_user_<?= $statusKey ?>_status"
                                           value="<?= get_option("sms_ir_woocommerce_order_user_{$statusKey}_status") ?>">
                                    <input type="hidden" name="sms_ir_woocommerce_order_user_<?= $statusKey ?>_mobile"
                                           value="<?= get_option("sms_ir_woocommerce_order_user_{$statusKey}_mobile") ?>">
                                    <input type="hidden" name="sms_ir_woocommerce_order_user_<?= $statusKey ?>_template"
                                           value="<?= get_option("sms_ir_woocommerce_order_user_{$statusKey}_template") ?>">
                                    <input type="hidden" name="sms_ir_woocommerce_order_user_<?= $statusKey ?>_text"
                                           value="<?= get_option("sms_ir_woocommerce_order_user_{$statusKey}_text") ?>">
                                    <span name="sms_ir_woocommerce_order_user_<?= $statusKey ?>_parameter"
                                          class="hidden">
                                        <span class='sms-ir-setting-parameter'>#orderid#</span>,
                                        <span class='sms-ir-setting-parameter'>#trackingcode#</span>,
                                        <span class='sms-ir-setting-parameter'>#description#</span>,
                                        <span class='sms-ir-setting-parameter'>#paymentmethod#</span>,
                                        <span class='sms-ir-setting-parameter'>#oldstatus#</span>,
                                        <span class='sms-ir-setting-parameter'>#newstatus#</span>,
                                        <span class='sms-ir-setting-parameter'>#price#</span>,
                                        <span class='sms-ir-setting-parameter'>#discount#</span>,
                                        <span class='sms-ir-setting-parameter'>#shipping#</span>,
                                        <span class='sms-ir-setting-parameter'>#items#</span>,
                                        <span class='sms-ir-setting-parameter'>#itemscount#</span>,
                                        <span class='sms-ir-setting-parameter'>#productscount#</span>,
                                        <span class='sms-ir-setting-parameter'>#firstname#</span>,
                                        <span class='sms-ir-setting-parameter'>#lastname#</span>,
                                        <span class='sms-ir-setting-parameter'>#company#</span>,
                                        <span class='sms-ir-setting-parameter'>#address1#</span>,
                                        <span class='sms-ir-setting-parameter'>#address2#</span>,
                                        <span class='sms-ir-setting-parameter'>#city#</span>,
                                        <span class='sms-ir-setting-parameter'>#state#</span>,
                                        <span class='sms-ir-setting-parameter'>#postcode#</span>,
                                        <span class='sms-ir-setting-parameter'>#country#</span>,
                                        <span class='sms-ir-setting-parameter'>#email#</span>,
                                        <span class='sms-ir-setting-parameter'>#phone#</span>
                                    </span>
                                    <i class="dashicons dashicons-visibility"
                                       data-name="sms_ir_woocommerce_order_user_<?= $statusKey ?>" <?php if (!get_option("sms_ir_woocommerce_order_user_{$statusKey}_status")) { ?> style="display: none" <?php } ?>></i>
                                    <span>به کاربر</span>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <table class="form-table sms-ir-form-table">
                    <tbody>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک هنگام ثبت سفارش:
                                <i class="dashicons dashicons-info"
                                   title="با فعال سازی این گزینه، بلافاصله پس از ثبت سفارش جدید توسط کاربر در وبسایت شما پیامک هایی به کاربر و ادمین سایت و مطابق با تنظیمات ثبت شده توسط شما ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                   name="sms_ir_woocommerce_new_order_admin" <?php if (get_option("sms_ir_woocommerce_new_order_admin_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_woocommerce_new_order_admin_status"
                                   value="<?= get_option("sms_ir_woocommerce_new_order_admin_status") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_new_order_admin_mobile"
                                   value="<?= get_option("sms_ir_woocommerce_new_order_admin_mobile") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_new_order_admin_template"
                                   value="<?= get_option("sms_ir_woocommerce_new_order_admin_template") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_new_order_admin_text"
                                   value="<?= get_option("sms_ir_woocommerce_new_order_admin_text") ?>">
                            <span name="sms_ir_woocommerce_new_order_admin_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#orderid#</span>,
                                <span class='sms-ir-setting-parameter'>#trackingcode#</span>,
                                <span class='sms-ir-setting-parameter'>#description#</span>,
                                <span class='sms-ir-setting-parameter'>#paymentmethod#</span>,
                                <span class='sms-ir-setting-parameter'>#price#</span>,
                                <span class='sms-ir-setting-parameter'>#discount#</span>,
                                <span class='sms-ir-setting-parameter'>#shipping#</span>,
                                <span class='sms-ir-setting-parameter'>#items#</span>,
                                <span class='sms-ir-setting-parameter'>#itemscount#</span>,
                                <span class='sms-ir-setting-parameter'>#productscount#</span>,
                                <span class='sms-ir-setting-parameter'>#firstname#</span>,
                                <span class='sms-ir-setting-parameter'>#lastname#</span>,
                                <span class='sms-ir-setting-parameter'>#company#</span>,
                                <span class='sms-ir-setting-parameter'>#address1#</span>,
                                <span class='sms-ir-setting-parameter'>#address2#</span>,
                                <span class='sms-ir-setting-parameter'>#city#</span>,
                                <span class='sms-ir-setting-parameter'>#state#</span>,
                                <span class='sms-ir-setting-parameter'>#postcode#</span>,
                                <span class='sms-ir-setting-parameter'>#country#</span>,
                                <span class='sms-ir-setting-parameter'>#email#</span>,
                                <span class='sms-ir-setting-parameter'>#phone#</span>
                            </span>
                            <i data-name="sms_ir_woocommerce_new_order_admin"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_woocommerce_new_order_admin_status")) { ?> style="display:none;" <?php } ?>></i>
                            <span>به مدیر</span>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="order"
                                   name="sms_ir_woocommerce_new_order_user" <?php if (get_option("sms_ir_woocommerce_new_order_user_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_woocommerce_new_order_user_status"
                                   value="<?= get_option("sms_ir_woocommerce_new_order_user_status") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_new_order_user_mobile"
                                   value="<?= get_option("sms_ir_woocommerce_new_order_user_mobile") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_new_order_user_template"
                                   value="<?= get_option("sms_ir_woocommerce_new_order_user_template") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_new_order_user_text"
                                   value="<?= get_option("sms_ir_woocommerce_new_order_user_text") ?>">
                            <span name="sms_ir_woocommerce_new_order_user_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#orderid#</span>,
                                <span class='sms-ir-setting-parameter'>#trackingcode#</span>,
                                <span class='sms-ir-setting-parameter'>#description#</span>,
                                <span class='sms-ir-setting-parameter'>#paymentmethod#</span>,
                                <span class='sms-ir-setting-parameter'>#price#</span>,
                                <span class='sms-ir-setting-parameter'>#discount#</span>,
                                <span class='sms-ir-setting-parameter'>#shipping#</span>,
                                <span class='sms-ir-setting-parameter'>#items#</span>,
                                <span class='sms-ir-setting-parameter'>#itemscount#</span>,
                                <span class='sms-ir-setting-parameter'>#productscount#</span>,
                                <span class='sms-ir-setting-parameter'>#firstname#</span>,
                                <span class='sms-ir-setting-parameter'>#lastname#</span>,
                                <span class='sms-ir-setting-parameter'>#company#</span>,
                                <span class='sms-ir-setting-parameter'>#address1#</span>,
                                <span class='sms-ir-setting-parameter'>#address2#</span>,
                                <span class='sms-ir-setting-parameter'>#city#</span>,
                                <span class='sms-ir-setting-parameter'>#state#</span>,
                                <span class='sms-ir-setting-parameter'>#postcode#</span>,
                                <span class='sms-ir-setting-parameter'>#country#</span>,
                                <span class='sms-ir-setting-parameter'>#email#</span>,
                                <span class='sms-ir-setting-parameter'>#phone#</span>
                            </span>
                            <i class="dashicons dashicons-visibility"
                               data-name="sms_ir_woocommerce_new_order_user" <?php if (!get_option("sms_ir_woocommerce_new_order_user_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به کاربر</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک موجودی انبار:
                                <i class="dashicons dashicons-info"
                                   title="در افزونه مدیریت محصولات ووکامرس، قسمتی با عنوان مدیریت موجودی و انبارداری محصول وجود دارد. شما می توانید در قسمت یاد شده نسبت به ثبت حداقل موجودی محصول اقدام فرمایید. پس از ثبت حداقل موجودی و فعال سازی این قابلیت، سیستم به صورت خودکار هنگامی که موجودی محصول به میزان یاد شده کاهش پیدا کند، پیامک اطلاع رسانی برای ادمین سایت ارسال خواهد کرد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox"
                                   name="sms_ir_woocommerce_stock_admin" <?php if (get_option("sms_ir_woocommerce_stock_admin_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_woocommerce_stock_admin_status"
                                   value="<?= get_option("sms_ir_woocommerce_stock_admin_status") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_stock_admin_mobile"
                                   value="<?= get_option("sms_ir_woocommerce_stock_admin_mobile") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_stock_admin_template"
                                   value="<?= get_option("sms_ir_woocommerce_stock_admin_template") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_stock_admin_text"
                                   value="<?= get_option("sms_ir_woocommerce_stock_admin_text") ?>">
                            <span name="sms_ir_woocommerce_stock_admin_parameter" class="hidden">
                                <span class='sms-ir-setting-parameter'>#id#</span>,
                                <span class='sms-ir-setting-parameter'>#name#</span>,
                                <span class='sms-ir-setting-parameter'>#quantity#</span>,
                                <span class='sms-ir-setting-parameter'>#lowstock#</span>
                            </span>
                            <i data-name="sms_ir_woocommerce_stock_admin"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_woocommerce_stock_admin_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به مدیر</span>
                        </td>
                        <td class="form-table-checkbox"></td>
                    </tr>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک هنگام موجود شدن کالا:
                                <i class="dashicons dashicons-info"
                                   title="پس از فعال سازی و ثبت تنظیمات مورد نیاز این قابلیت، گزینه ای تحت عنوان 'موجود شد خبرم کن!'، به صفحه جزئیات محصولاتی که ناموجود می باشند اضافه خواهد شد. کاربران سایت می توانند شماره موبایل و اطلاعات خود را در این قسمت وارد نمایند. پس از موجود شدن کالا، به تمامی کاربران ثبت نام شده، پیامک اطلاع رسانی ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox"></td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="system"
                                   name="sms_ir_woocommerce_inventory_user" <?php if (get_option("sms_ir_woocommerce_inventory_user_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_woocommerce_inventory_user_status"
                                   value="<?= get_option("sms_ir_woocommerce_inventory_user_status") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_inventory_user_mobile"
                                   value="<?= get_option("sms_ir_woocommerce_inventory_user_mobile") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_inventory_user_template"
                                   value="<?= get_option("sms_ir_woocommerce_inventory_user_template") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_inventory_user_text"
                                   value="<?= get_option("sms_ir_woocommerce_inventory_user_text") ?>">
                            <span name="sms_ir_woocommerce_inventory_user_parameter" class="hidden">
                                <span class='sms-ir-setting-parameter'>#clientname#</span>,
                                <span class='sms-ir-setting-parameter'>#productid#</span>,
                                <span class='sms-ir-setting-parameter'>#productname#</span>,
                                <span class='sms-ir-setting-parameter'>#productsku#</span>
                            </span>
                            <i data-name="sms_ir_woocommerce_inventory_user" data-type="system"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_woocommerce_inventory_user_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به کاربر</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال پیامک پیشنهادات شگفت انگیز:
                                <i class="dashicons dashicons-info"
                                   title="پس از فعال سازی و ثبت تنظیمات مورد نیاز این قابلیت، گزینه ای تحت عنوان 'تخفیف دار شد خبرم کن!'، به صفحه جزئیات محصولات اضافه خواهد شد. کاربران سایت می توانند شماره موبایل و اطلاعات خود را در این قسمت وارد نمایند. پس از آن که محصول مورد نظر شامل تخفیف شد، به تمامی کاربران ثبت نام شده، پیامک اطلاع رسانی ارسال خواهد شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox"></td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="system"
                                   name="sms_ir_woocommerce_promotion_user" <?php if (get_option("sms_ir_woocommerce_promotion_user_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_woocommerce_promotion_user_status"
                                   value="<?= get_option("sms_ir_woocommerce_promotion_user_status") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_promotion_user_mobile"
                                   value="<?= get_option("sms_ir_woocommerce_promotion_user_mobile") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_promotion_user_template"
                                   value="<?= get_option("sms_ir_woocommerce_promotion_user_template") ?>">
                            <input type="hidden" name="sms_ir_woocommerce_promotion_user_text"
                                   value="<?= get_option("sms_ir_woocommerce_promotion_user_text") ?>">
                            <span name="sms_ir_woocommerce_promotion_user_parameter" class="hidden">
                                <span class='sms-ir-setting-parameter'>#clientname#</span>,
                                <span class='sms-ir-setting-parameter'>#productid#</span>,
                                <span class='sms-ir-setting-parameter'>#productname#</span>,
                                <span class='sms-ir-setting-parameter'>#productprice#</span>,
                                <span class='sms-ir-setting-parameter'>#productsku#</span>
                            </span>
                            <i data-name="sms_ir_woocommerce_promotion_user" data-type="system"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_woocommerce_promotion_user_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به کاربر</span>
                        </td>
                    </tr>

                    </tbody>
                </table>
                <p class="tab-content-tips">جهت کسب اطلاعات بیشتر به وبسایت <a href="https://sms.ir/" target="_blank">www.sms.ir</a>
                    مراجعه نمایید.</p>
                <button type="submit" name="sms_ir_setting_button" value="1"
                        class="sms-ir-setting-button button-primary">ذخیره سازی
                </button>
            </div>
            <div class="sms-ir-tab" style="display: none" id="smsIrWordpressTab">
                <table class="form-table sms-ir-form-table">
                    <tbody>

                    <tr class="form-table-description-input">
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال کد تاییدیه هنگام ورود به سایت:
                                <i class="dashicons dashicons-info"
                                   title="در صورتی که بر روی وبسایت شما افزونه Digits نصب شده باشد، امکان استفاده از این قابلیت مهیا خواهد شد. به منظور استفاده از این قابلیت تنها کافیست که در افزونه یاد شده، سامانه پیامکی را به حالت سفارشی (Custom) تغییر داده و در قسمت آدرس درگاه (Gateway URL)، آدرس sms.ir را ثبت نمایید. پس از اعمال تغییرات در افزونه دیجیتس و فعال سازی این قابلیت، پیامک های مربوط به ورود به سایت و دریافت کد تاییدیه، از طریق این ماژول ارسال خواهند شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox"></td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="system"
                                   name="sms_ir_wordpress_login_user" <?php if (get_option("sms_ir_wordpress_login_user_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_wordpress_login_user_status"
                                   value="<?= get_option("sms_ir_wordpress_login_user_status") ?>">
                            <input type="hidden" name="sms_ir_wordpress_login_user_mobile"
                                   value="<?= get_option("sms_ir_wordpress_login_user_mobile") ?>">
                            <input type="hidden" name="sms_ir_wordpress_login_user_template"
                                   value="<?= get_option("sms_ir_wordpress_login_user_template") ?>">
                            <input type="hidden" name="sms_ir_wordpress_login_user_text"
                                   value="<?= get_option("sms_ir_wordpress_login_user_text") ?>">
                            <span name="sms_ir_wordpress_login_user_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#code#</span>,
                                <span class='sms-ir-setting-parameter'>#mobile#</span>,
                                <span class='sms-ir-setting-parameter'>#site#</span>
                            </span>
                            <i data-name="sms_ir_wordpress_login_user" data-type="system"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_wordpress_login_user_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به کاربر</span>
                        </td>
                    </tr>
                    <tr class="form-table-description-text">
                        <td colspan="3">
                            <small>لطفا جهت استفاده از این قابلیت، پلاگین <a href="https://sms.ir/%d8%ae%d8%af%d9%85%d8%a7%d8%aa/%d9%88%d8%a8-%d8%b3%d8%b1%d9%88%db%8c%d8%b3/"
                                                                             target="_blank"> Digits </a>
                                را نصب نمایید.</small>
                        </td>
                    </tr>
                    <tr class="form-table-description-input">
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال کد تاییدیه هنگام ثبت نام در سایت:
                                <i class="dashicons dashicons-info"
                                   title="در صورتی که بر روی وبسایت شما افزونه Digits نصب شده باشد، امکان استفاده از این قابلیت مهیا خواهد شد. به منظور استفاده از این قابلیت تنها کافیست که در افزونه یاد شده، سامانه پیامکی را به حالت سفارشی (Custom) تغییر داده و در قسمت آدرس درگاه (Gateway URL)، آدرس sms.ir را ثبت نمایید. پس از اعمال تغییرات در افزونه دیجیتس و فعال سازی این قابلیت، پیامک های مربوط به ثبت نام در سایت و دریافت کد تاییدیه، از طریق این ماژول ارسال خواهند شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox"></td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="system"
                                   name="sms_ir_wordpress_register_user" <?php if (get_option("sms_ir_wordpress_register_user_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_wordpress_register_user_status"
                                   value="<?= get_option("sms_ir_wordpress_register_user_status") ?>">
                            <input type="hidden" name="sms_ir_wordpress_register_user_mobile"
                                   value="<?= get_option("sms_ir_wordpress_register_user_mobile") ?>">
                            <input type="hidden" name="sms_ir_wordpress_register_user_template"
                                   value="<?= get_option("sms_ir_wordpress_register_user_template") ?>">
                            <input type="hidden" name="sms_ir_wordpress_register_user_text"
                                   value="<?= get_option("sms_ir_wordpress_register_user_text") ?>">
                            <span name="sms_ir_wordpress_register_user_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#code#</span>,
                                <span class='sms-ir-setting-parameter'>#mobile#</span>,
                                <span class='sms-ir-setting-parameter'>#site#</span>
                            </span>
                            <i data-name="sms_ir_wordpress_register_user" data-type="system"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_wordpress_register_user_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به کاربر</span>
                        </td>
                    </tr>
                    <tr class="form-table-description-text">
                        <td colspan="3">
                            <small>لطفا جهت استفاده از این قابلیت، پلاگین <a href="https://sms.ir/%d8%ae%d8%af%d9%85%d8%a7%d8%aa/%d9%88%d8%a8-%d8%b3%d8%b1%d9%88%db%8c%d8%b3/"
                                                                             target="_blank"> Digits </a>
                                را نصب نمایید.</small>
                        </td>
                    </tr>
                    <tr class="form-table-description-input">
                        <td class="form-table-checkbox">
                            <p class="sms-ir-label">ارسال کد تاییدیه هنگام فراموشی رمز عبور:
                                <i class="dashicons dashicons-info"
                                   title="در صورتی که بر روی وبسایت شما افزونه Digits نصب شده باشد، امکان استفاده از این قابلیت مهیا خواهد شد. به منظور استفاده از این قابلیت تنها کافیست که در افزونه یاد شده، سامانه پیامکی را به حالت سفارشی (Custom) تغییر داده و در قسمت آدرس درگاه (Gateway URL)، آدرس sms.ir را ثبت نمایید. پس از اعمال تغییرات در افزونه دیجیتس و فعال سازی این قابلیت، پیامک های مربوط به فراموش رمز عبور و دریافت کد تاییدیه، از طریق این ماژول ارسال خواهند شد."></i>
                            </p>
                        </td>
                        <td class="form-table-checkbox"></td>
                        <td class="form-table-checkbox">
                            <input class="form-control sms-ir-setting-checkbox" type="checkbox" data-type="system"
                                   name="sms_ir_wordpress_password_user" <?php if (get_option("sms_ir_wordpress_password_user_status")) { ?> checked <?php } ?>>
                            <input type="hidden" name="sms_ir_wordpress_password_user_status"
                                   value="<?= get_option("sms_ir_wordpress_password_user_status") ?>">
                            <input type="hidden" name="sms_ir_wordpress_password_user_mobile"
                                   value="<?= get_option("sms_ir_wordpress_password_user_mobile") ?>">
                            <input type="hidden" name="sms_ir_wordpress_password_user_template"
                                   value="<?= get_option("sms_ir_wordpress_password_user_template") ?>">
                            <input type="hidden" name="sms_ir_wordpress_password_user_text"
                                   value="<?= get_option("sms_ir_wordpress_password_user_text") ?>">
                            <span name="sms_ir_wordpress_password_user_parameter" class="hidden">#FieldTypeFiledID#:
                                <span class='sms-ir-setting-parameter'>#code#</span>,
                                <span class='sms-ir-setting-parameter'>#mobile#</span>,
                                <span class='sms-ir-setting-parameter'>#site#</span>
                            </span>
                            <i data-name="sms_ir_wordpress_password_user" data-type="system"
                               class="dashicons dashicons-visibility" <?php if (!get_option("sms_ir_wordpress_password_user_status")) { ?> style="display: none" <?php } ?>></i>
                            <span>به کاربر</span>
                        </td>
                    </tr>
                    <tr class="form-table-description-text">
                        <td colspan="3">
                            <small>لطفا جهت استفاده از این قابلیت، پلاگین <a href="https://sms.ir/%d8%ae%d8%af%d9%85%d8%a7%d8%aa/%d9%88%d8%a8-%d8%b3%d8%b1%d9%88%db%8c%d8%b3/"
                                                                             target="_blank"> Digits </a>
                                را نصب نمایید.</small>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p class="tab-content-tips">جهت کسب اطلاعات بیشتر به وبسایت <a href="https://sms.ir/" target="_blank">www.sms.ir</a>
                    مراجعه نمایید.</p>
                <button type="submit" name="sms_ir_setting_button" value="1"
                        class="sms-ir-setting-button button-primary">ذخیره سازی
                </button>
            </div>
        </form>
        <div id="smsIrModal" class="sms-ir-modal">
            <div class="sms-ir-modal-content">
                <input type="hidden" id="smsIrModalType">
                <span class="sms-ir-modal-close" onclick="hideSmsIrModal()">&times;</span>
                <p class="sms-ir-label" id="smsIrModalMobileLabel">شماره مدیران را وارد نمایید:</p>
                <input type="tel" class="sms-ir-form-control sms-ir-modal-form-control" id="smsIrModalMobile"
                       placeholder="0911*******,0912*******,0913*******" required>
                <small class="sms-ir-form-text" id="smsIrModalMobileSmall"></small>
                 <br>
                <small class="sms-ir-form-text sms-ir-form-error" id="smsIrModalMobileError"></small>
                <p class="sms-ir-label">ارسال سریع (ارسال با قالب پیامکی):</p>
                <div class="sms-ir-toggle">
                    <input type="checkbox" class="sms-ir-check" onchange="smsIrSenderType(this.checked)">
                    <b class="sms-ir-switch"></b>
                    <b class="sms-ir-track"></b>
                </div>
                <div id="smsIrModalMobileVerify" style="display: none">
                    <p class="sms-ir-label">شماره قالب خود را وارد نمایید:
                        <i class="dashicons dashicons-info"
                           title="پس از ورود به پورتال سامانه sms.ir، بر روی منوی ماژول ها، گزینه ارسال سریع کلیک نمایید. در این قسمت امکان ثبت و مدیریت قالب های مختلف برای شما فراهم شده است. پس از ثبت قالب مورد نظر، نیاز است که شناسه قالب خود را در این قسمت وارد نمایید. پس از ثبت شناسه قالب، تمامی مراحل آماده سازی، تکمیل و ارسال پیامک به صورت خودکار و توسط سیستم انجام خواهد شد. در نظر داشته باشید که هنگام ایجاد قالب جدید در پورتال پیامکی، حتما از پارامترهای معرفی شده در انتهای این پاپ آپ استفاده نمایید."></i>
                    </p>
                    <input type="number" class="sms-ir-form-control sms-ir-modal-form-control" dir="ltr"
                           id="smsIrModalTemplate" placeholder="12345***" min="0" value="0" required>
                    <small class="sms-ir-form-text">
                        شماره قالبی که در <a href="https://sms.ir">sms.ir</a> ثبت کرده اید را وارد نمایید.
                    </small>
                    <br>
                    <small class="sms-ir-form-text sms-ir-form-error" id="smsIrModalTemplateError">
                        *شماره قالب وارد شده صحیح نمی باشد
                    </small>
                </div>
                <div id="smsIrModalMobileBulk">
                    <p class="sms-ir-label">متن پیامک را وارد نمایید:
                        <i class="dashicons dashicons-info"
                           title="در صورتی که قصد ارسال پیامک بدون استفاده از ماژول ارسال سریع را دارید، این قسمت را تکمیل نمایید. به منظور ثبت متن پیامک مورد نظر خود، نیاز است که از پارامترهای گفته شده در انتهای این پاپ آپ استفاده نمایید. تمامی مراحل بعدی اعم از نهایی سازی متن و ارسال پیامک به صورت خودکار انجام خواهد شد."></i>
                    </p>
                    <textarea rows="7" class="sms-ir-form-control sms-ir-modal-form-control" id="smsIrModalText"
                              lang="fa-IR" charset="utf-8" dir="rtl" placeholder="سلام، #FirstName# عزیز *****"
                              required></textarea>
                    <small class="sms-ir-form-text">
                        طبق پارامترهای زیر متن خود را تکمیل کنید.
                    </small>
                </div>
                <p class="sms-ir-label">پارامترهای مورد قبول جهت استفاده در پیامک بدین صورت می باشد:</p>
                <p class="sms-ir-label" id="smsIrTemplateParameter" dir="ltr"></p>
                <p class="sms-ir-label-danger">جهت ثبت قالب جدید به لینک <a href="https://sms.ir">
                        www.sms.ir </a> مراجعه نمایید.</p>
                <p class="sms-ir-modal-button">
                    <button type="button" id="smsIrModalButton" class="sms-ir-setting-button button-primary"
                            onclick="saveSmsIrModal()">ثبت
                    </button>
                </p>
            </div>
        </div>
    </div>
</div
