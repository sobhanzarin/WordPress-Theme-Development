<?php
/**
 * مدیریت ارسال پیامک - Integration با افزونه پیامک موجود
 */

class WSI_SMS_Handler {
    
    public static function init() {
        // هوک‌های عمومی برای integration با افزونه پیامک
        add_action('wsi_invoice_created', array(__CLASS__, 'handle_invoice_created'), 10, 3);
        add_action('wsi_invoice_paid', array(__CLASS__, 'handle_invoice_paid'), 10, 2);
    }
    
    /**
     * هنگامی که فاکتور جدید ایجاد می‌شود (اتوماتیک یا دستی)
     */
    public static function handle_invoice_created($invoice_id, $user_id, $invoice_type = 'auto') {
        $user = get_userdata($user_id);
        $phone = get_user_meta($user_id, 'billing_phone', true);
        
        if (!$phone) {
            error_log('WSI SMS: شماره موبایل کاربر یافت نشد - User ID: ' . $user_id);
            return false;
        }
        
        $invoice_url = wc_get_account_endpoint_url('invoices');
        
        if ($invoice_type === 'renewal' || $invoice_type === 'auto') {
            $message = sprintf(
                __('کاربر گرامی، اشتراک شما 20 روز تا انقضا دارد. لطفا فاکتور #%s را از طریق لینک زیر پرداخت نمایید: %s', 'wc-sub-invoices'),
                $invoice_id,
                $invoice_url
            );
        } else {
            $message = sprintf(
                __('کاربر گرامی، یک فاکتور جدید برای شما ایجاد شد. لطفا فاکتور #%s را از طریق لینک زیر پرداخت نمایید: %s', 'wc-sub-invoices'),
                $invoice_id,
                $invoice_url
            );
        }
        
        // استفاده از هوک برای integration با افزونه پیامک موجود
        do_action('wsi_send_sms_notification', $phone, $message, array(
            'invoice_id' => $invoice_id,
            'user_id' => $user_id,
            'type' => 'invoice_created',
            'invoice_type' => $invoice_type
        ));
        
        return true;
    }
    
    /**
     * هنگامی که فاکتور پرداخت می‌شود
     */
    public static function handle_invoice_paid($invoice_id, $user_id) {
        $user = get_userdata($user_id);
        $phone = get_user_meta($user_id, 'billing_phone', true);
        
        if (!$phone) {
            error_log('WSI SMS: شماره موبایل کاربر یافت نشد - User ID: ' . $user_id);
            return false;
        }
        
        $message = sprintf(
            __('کاربر گرامی، فاکتور #%s با موفقیت پرداخت شد. اشتراک شما تمدید گردید.', 'wc-sub-invoices'),
            $invoice_id
        );
        
        // استفاده از هوک برای integration با افزونه پیامک موجود
        do_action('wsi_send_sms_notification', $phone, $message, array(
            'invoice_id' => $invoice_id,
            'user_id' => $user_id,
            'type' => 'invoice_paid'
        ));
        
        return true;
    }
    
    /**
     * تست اتصال - برای استفاده در Ajax
     */
    public static function test_connection() {
        $test_phone = get_option('wsi_admin_phone', '');
        
        if (empty($test_phone)) {
            return new WP_Error('no_phone', __('شماره تست تنظیم نشده است.', 'wc-sub-invoices'));
        }
        
        $message = __('این یک پیامک تست از افزونه فاکتورهای اشتراک است.', 'wc-sub-invoices');
        
        // استفاده از هوک برای تست با افزونه پیامک موجود
        do_action('wsi_send_sms_notification', $test_phone, $message, array(
            'type' => 'test',
            'test' => true
        ));
        
        return true;
    }
    
    /**
     * متدهای قدیمی برای backward compatibility
     */
    public static function send_renewal_notification($user_id, $subscription_id, $invoice_id) {
        return self::handle_invoice_created($invoice_id, $user_id, 'renewal');
    }
    
    public static function send_manual_invoice_notification($user_id, $invoice_id) {
        return self::handle_invoice_created($invoice_id, $user_id, 'manual');
    }
}

// راه‌اندازی کلاس
WSI_SMS_Handler::init();
?>