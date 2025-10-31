<?php
/**
 * مدیریت کارهای زمان‌بندی شده
 */

class WSI_Cron_Manager {
    
    public static function init() {
        add_action('wsi_daily_invoice_check', array(__CLASS__, 'check_subscriptions_for_invoices'));
        add_filter('cron_schedules', array(__CLASS__, 'add_custom_schedules'));
        add_action('admin_init', array(__CLASS__, 'handle_manual_check'));
    }
    
    public static function schedule_events() {
        if (!wp_next_scheduled('wsi_daily_invoice_check')) {
            wp_schedule_event(time(), 'daily', 'wsi_daily_invoice_check');
        }
    }
    
    public static function clear_scheduled_events() {
        wp_clear_scheduled_hook('wsi_daily_invoice_check');
    }
    
    public static function add_custom_schedules($schedules) {
        $schedules['wsi_every_6_hours'] = array(
            'interval' => 6 * HOUR_IN_SECONDS,
            'display' => __('هر 6 ساعت', 'wc-sub-invoices')
        );
        
        $schedules['wsi_every_12_hours'] = array(
            'interval' => 12 * HOUR_IN_SECONDS,
            'display' => __('هر 12 ساعت', 'wc-sub-invoices')
        );
        
        return $schedules;
    }
    
    public static function handle_manual_check() {
        if (isset($_GET['wsi_manual_check']) && current_user_can('manage_woocommerce')) {
            check_admin_referer('wsi_manual_check');
            self::check_subscriptions_for_invoices();
            
            wp_redirect(add_query_arg(array(
                'wsi_message' => 'manual_check_completed',
                'page' => 'wc-settings',
                'tab' => 'subscriptions'
            ), admin_url('admin.php')));
            exit;
        }
    }
    
    public static function check_subscriptions_for_invoices() {
        if (!class_exists('WC_Subscriptions')) {
            return;
        }
        
        // فقط اشتراک‌های فعال رو بررسی کن
        $subscriptions = wcs_get_subscriptions(array(
            'subscriptions_per_page' => -1,
            'subscription_status' => 'active'
        ));

        $invoices_created = 0;
        
        foreach ($subscriptions as $subscription) {
            if (self::check_single_subscription($subscription)) {
                $invoices_created++;
            }
        }
        
        // ذخیره گزارش
        if ($invoices_created > 0) {
            error_log("WSI Cron: {$invoices_created} فاکتور جدید ایجاد شد.");
        }
    }
    
    private static function check_single_subscription($subscription) {
        $end_date = $subscription->get_date('end');
        if (!$end_date) {
            return false;
        }
        
        $end_timestamp = strtotime($end_date);
        $twenty_days_before = strtotime('-20 days', $end_timestamp);
        $today = current_time('timestamp');
        
        // اگر 20 روز یا کمتر تا انقضا مانده
        if ($today >= $twenty_days_before) {
            return self::create_renewal_invoice($subscription);
        }
        
        return false;
    }
    
    private static function create_renewal_invoice($subscription) {
        $subscription_id = $subscription->get_id();
        $user_id = $subscription->get_user_id();
        
        // بررسی وجود فاکتور pending برای این اشتراک
        $existing_invoice = get_posts(array(
            'post_type' => 'wsi_invoice',
            'meta_query' => array(
                array(
                    'key' => '_subscription_id',
                    'value' => $subscription_id
                ),
                array(
                    'key' => '_status',
                    'value' => 'pending'
                )
            ),
            'posts_per_page' => 1
        ));
        
        if (!empty($existing_invoice)) {
            return false; // فاکتور pending از قبل وجود دارد
        }
        
        // ایجاد فاکتور جدید
        $invoice_id = wp_insert_post(array(
            'post_type' => 'wsi_invoice',
            'post_title' => sprintf(__('فاکتور تمدید اشتراک #%s', 'wc-sub-invoices'), $subscription_id),
            'post_status' => 'publish',
            'post_author' => $user_id
        ));
        
        if (is_wp_error($invoice_id)) {
            error_log("WSI Error: ایجاد فاکتور برای اشتراک {$subscription_id} با خطا مواجه شد: " . $invoice_id->get_error_message());
            return false;
        }
        
        // محاسبه تاریخ‌ها و مبالغ
        $next_payment = $subscription->get_date('next_payment');
        $amount = $subscription->get_total();
        
        // اگر next_payment وجود ندارد، از end date استفاده کن
        if (!$next_payment) {
            $next_payment = $subscription->get_date('end');
        }
        
        // ذخیره متا داده‌ها
        update_post_meta($invoice_id, '_subscription_id', $subscription_id);
        update_post_meta($invoice_id, '_user_id', $user_id);
        update_post_meta($invoice_id, '_amount', $amount);
        update_post_meta($invoice_id, '_status', 'pending');
        update_post_meta($invoice_id, '_due_date', $next_payment);
        update_post_meta($invoice_id, '_invoice_type', 'renewal');
        update_post_meta($invoice_id, '_created_date', current_time('mysql'));
        
        // ارسال پیامک
        do_action('wsi_invoice_created', $invoice_id, $user_id, 'renewal');

        error_log("WSI: فاکتور تمدید #{$invoice_id} برای اشتراک #{$subscription_id} ایجاد شد.");
        
        return true;
    }
    
    /**
     * اجرای دستی بررسی اشتراک‌ها
     */
    public static function run_manual_check() {
        if (!current_user_can('manage_woocommerce')) {
            return false;
        }
        
        return self::check_subscriptions_for_invoices();
    }
}

// راه‌اندازی کلاس
WSI_Cron_Manager::init();
?>