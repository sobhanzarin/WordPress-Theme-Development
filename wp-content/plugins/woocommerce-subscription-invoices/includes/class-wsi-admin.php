<?php
/**
 * مدیریت بخش ادمین
 */

class WSI_Admin {
    
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_notices', array(__CLASS__, 'admin_notices'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
    }
    
    public static function add_admin_menu() {
        add_options_page(
            __('تنظیمات فاکتورهای اشتراک', 'wc-sub-invoices'),
            __('فاکتورهای اشتراک', 'wc-sub-invoices'),
            'manage_woocommerce',
            'wsi-settings',
            array(__CLASS__, 'settings_page')
        );
    }
    
    public static function register_settings() {
        register_setting('wsi_settings', 'wsi_sms_provider');
        register_setting('wsi_settings', 'wsi_sms_api_key');
        register_setting('wsi_settings', 'wsi_sms_api_url');
        register_setting('wsi_settings', 'wsi_sms_sender_number');
        register_setting('wsi_settings', 'wsi_admin_phone');
        register_setting('wsi_settings', 'wsi_days_before_expiry');
        register_setting('wsi_settings', 'wsi_remove_data_on_uninstall');
    }
    
    public static function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('تنظیمات فاکتورهای اشتراک', 'wc-sub-invoices'); ?></h1>
            
            <form method="post" action="options.php">
                <?php 
                settings_fields('wsi_settings');
                do_settings_sections('wsi_settings');
                ?>
                
                <h2 class="title"><?php _e('تنظیمات پیامک', 'wc-sub-invoices'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="wsi_sms_provider"><?php _e('سرویس پیامک', 'wc-sub-invoices'); ?></label>
                        </th>
                        <td>
                            <select name="wsi_sms_provider" id="wsi_sms_provider">
                                <option value="none" <?php selected(get_option('wsi_sms_provider'), 'none'); ?>><?php _e('غیرفعال', 'wc-sub-invoices'); ?></option>
                                <option value="web_service" <?php selected(get_option('wsi_sms_provider'), 'web_service'); ?>><?php _e('وب سرویس', 'wc-sub-invoices'); ?></option>
                                <option value="farapayamak" <?php selected(get_option('wsi_sms_provider'), 'farapayamak'); ?>><?php _e('فراپیامک', 'wc-sub-invoices'); ?></option>
                                <option value="smsir" <?php selected(get_option('wsi_sms_provider'), 'smsir'); ?>><?php _e('SMS.ir', 'wc-sub-invoices'); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="wsi_sms_api_key"><?php _e('API Key', 'wc-sub-invoices'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="wsi_sms_api_key" id="wsi_sms_api_key" value="<?php echo esc_attr(get_option('wsi_sms_api_key')); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="wsi_sms_api_url"><?php _e('API URL', 'wc-sub-invoices'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="wsi_sms_api_url" id="wsi_sms_api_url" value="<?php echo esc_attr(get_option('wsi_sms_api_url')); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="wsi_sms_sender_number"><?php _e('شماره فرستنده', 'wc-sub-invoices'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="wsi_sms_sender_number" id="wsi_sms_sender_number" value="<?php echo esc_attr(get_option('wsi_sms_sender_number')); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="wsi_admin_phone"><?php _e('شماره مدیر برای تست', 'wc-sub-invoices'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="wsi_admin_phone" id="wsi_admin_phone" value="<?php echo esc_attr(get_option('wsi_admin_phone')); ?>" class="regular-text">
                            <p class="description"><?php _e('شماره موبایل مدیر برای ارسال پیامک تست', 'wc-sub-invoices'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h2 class="title"><?php _e('تنظیمات فاکتور', 'wc-sub-invoices'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="wsi_days_before_expiry"><?php _e('تعداد روز قبل از انقضا', 'wc-sub-invoices'); ?></label>
                        </th>
                        <td>
                            <input type="number" name="wsi_days_before_expiry" id="wsi_days_before_expiry" value="<?php echo esc_attr(get_option('wsi_days_before_expiry', '20')); ?>" min="1" max="30" class="small-text">
                            <p class="description"><?php _e('تعداد روز قبل از انقضای اشتراک که فاکتور جدید ایجاد شود', 'wc-sub-invoices'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <h2 class="title"><?php _e('تنظیمات سیستم', 'wc-sub-invoices'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="wsi_remove_data_on_uninstall"><?php _e('حذف داده‌ها', 'wc-sub-invoices'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="wsi_remove_data_on_uninstall" id="wsi_remove_data_on_uninstall" value="yes" <?php checked(get_option('wsi_remove_data_on_uninstall', 'yes'), 'yes'); ?>>
                                <?php _e('حذف تمام داده‌های افزونه هنگام پاک کردن', 'wc-sub-invoices'); ?>
                            </label>
                            <p class="description">
                                <?php _e('در صورت انتخاب، تمام فاکتورها، تنظیمات و داده‌های مربوط به افزونه هنگام پاک کردن حذف خواهند شد.', 'wc-sub-invoices'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <div class="card">
                <h2><?php _e('تست سیستم', 'wc-sub-invoices'); ?></h2>
                <p>
                    <button type="button" id="wsi-test-sms" class="button button-secondary">
                        <?php _e('تست ارسال پیامک', 'wc-sub-invoices'); ?>
                    </button>
                    <span id="wsi-test-result" style="margin-right: 10px;"></span>
                </p>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#wsi-test-sms').on('click', function() {
                var $button = $(this);
                var $result = $('#wsi-test-result');
                
                $button.prop('disabled', true).text('<?php _e('در حال ارسال...', 'wc-sub-invoices'); ?>');
                $result.html('');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wsi_test_sms',
                        nonce: '<?php echo wp_create_nonce("wsi_ajax_nonce"); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $result.html('<span style="color: green;">✓ ' + response.data + '</span>');
                        } else {
                            $result.html('<span style="color: red;">✗ ' + response.data + '</span>');
                        }
                    },
                    error: function() {
                        $result.html('<span style="color: red;">✗ <?php _e('خطا در ارتباط با سرور', 'wc-sub-invoices'); ?></span>');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('<?php _e('تست ارسال پیامک', 'wc-sub-invoices'); ?>');
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    public static function enqueue_admin_scripts($hook) {
        // فقط در صفحات مربوط به افزونه اسکریپت‌ها رو بارگذاری کن
        if ($hook === 'settings_page_wsi-settings' || strpos($hook, 'wsi_') !== false || $hook === 'woocommerce_page_wc-settings') {
            wp_enqueue_style(
                'wsi-admin',
                WSI_PLUGIN_URL . 'admin/css/admin.css',
                array(),
                WSI_PLUGIN_VERSION
            );
            
            wp_enqueue_script(
                'wsi-admin',
                WSI_PLUGIN_URL . 'admin/js/admin.js',
                array('jquery'),
                WSI_PLUGIN_VERSION,
                true
            );
        }
    }
    
    public static function admin_notices() {
        // نمایش پیام‌های مدیریتی
        if (isset($_GET['wsi_message'])) {
            switch ($_GET['wsi_message']) {
                case 'manual_check_completed':
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php _e('بررسی دستی اشتراک‌ها با موفقیت انجام شد.', 'wc-sub-invoices'); ?></p>
                    </div>
                    <?php
                    break;
                    
                case 'test_sms_sent':
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p><?php _e('پیامک تست ارسال شد.', 'wc-sub-invoices'); ?></p>
                    </div>
                    <?php
                    break;
                    
                case 'test_sms_failed':
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php _e('ارسال پیامک تست با خطا مواجه شد.', 'wc-sub-invoices'); ?></p>
                    </div>
                    <?php
                    break;
            }
        }
    }
}

// راه‌اندازی کلاس
WSI_Admin::init();
?>