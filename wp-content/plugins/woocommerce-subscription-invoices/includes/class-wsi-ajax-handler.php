<?php
/**
 * مدیریت درخواست‌های Ajax
 */

class WSI_Ajax_Handler {
    
    public static function init() {
        add_action('wp_ajax_wsi_get_user_subscriptions', array(__CLASS__, 'get_user_subscriptions'));
        add_action('wp_ajax_wsi_get_subscription_amount', array(__CLASS__, 'get_subscription_amount'));
        add_action('wp_ajax_wsi_test_sms', array(__CLASS__, 'test_sms'));
    }
    
    public static function get_user_subscriptions() {
        check_ajax_referer('wsi_ajax_nonce', 'nonce');
        
        $user_id = intval($_POST['user_id']);
        
        if (!$user_id) {
            wp_send_json_error(__('کاربر معتبر نیست.', 'wc-sub-invoices'));
        }
        
        if (!class_exists('WC_Subscriptions')) {
            wp_send_json_error(__('افزونه اشتراک‌ها فعال نیست.', 'wc-sub-invoices'));
        }
        
        $subscriptions = wcs_get_subscriptions(array(
            'customer_id' => $user_id,
            'subscription_status' => array('active', 'on-hold')
        ));
        
        $options = array(
            array(
                'id' => '',
                'text' => __('انتخاب اشتراک (اختیاری)', 'wc-sub-invoices')
            )
        );
        
        foreach ($subscriptions as $subscription) {
            $status = $subscription->get_status();
            $status_label = wcs_get_subscription_status_name($status);
            
            $options[] = array(
                'id' => $subscription->get_id(),
                'text' => sprintf(
                    __('اشتراک #%s - %s - %s', 'wc-sub-invoices'),
                    $subscription->get_id(),
                    wc_price($subscription->get_total()),
                    $status_label
                )
            );
        }
        
        wp_send_json_success($options);
    }
    
    public static function get_subscription_amount() {
        check_ajax_referer('wsi_ajax_nonce', 'nonce');
        
        $subscription_id = intval($_POST['subscription_id']);
        
        if (!class_exists('WC_Subscriptions')) {
            wp_send_json_error(__('افزونه اشتراک‌ها فعال نیست.', 'wc-sub-invoices'));
        }
        
        $subscription = wcs_get_subscription($subscription_id);
        
        if (!$subscription) {
            wp_send_json_error(__('اشتراک یافت نشد.', 'wc-sub-invoices'));
        }
        
        wp_send_json_success(array(
            'amount' => $subscription->get_total(),
            'currency' => get_woocommerce_currency()
        ));
    }
    
    public static function test_sms() {
        check_ajax_referer('wsi_ajax_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(__('شما دسترسی لازم برای این عمل را ندارید.', 'wc-sub-invoices'));
        }
        
        $test_phone = get_option('wsi_admin_phone', '');
        
        if (empty($test_phone)) {
            wp_send_json_error(__('لطفاً ابتدا شماره موبایل مدیر را در تنظیمات وارد کنید.', 'wc-sub-invoices'));
        }
        
        $message = __('این یک پیامک تست از افزونه فاکتورهای اشتراک است.', 'wc-sub-invoices');
        
        try {
            // استفاده از هوک برای ارسال پیامک تست
            do_action('wsi_send_sms_notification', $test_phone, $message, array(
                'type' => 'test',
                'test' => true
            ));
            
            wp_send_json_success(__('درخواست ارسال پیامک تست ثبت شد. لطفاً بررسی کنید که پیامک دریافت شده است.', 'wc-sub-invoices'));
            
        } catch (Exception $e) {
            wp_send_json_error(sprintf(__('خطا در ارسال پیامک تست: %s', 'wc-sub-invoices'), $e->getMessage()));
        }
    }
}

// راه‌اندازی کلاس
WSI_Ajax_Handler::init();
?>