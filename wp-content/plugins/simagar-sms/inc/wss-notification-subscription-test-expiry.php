<?php
defined("ABSPATH") || exit("No Access ...");

class WSS_SMS_Notification_Test_Expiry
{
    public function __construct() {
        add_action('init', [$this, 'init_test_expiry_reminder']);
        add_action('wss_hourly_test_check', [$this, 'check_test_expiring_subscriptions']);
        
        add_filter('cron_schedules', [$this, 'add_custom_cron_intervals']);
    }
    
    public function add_custom_cron_intervals($schedules) {
        $schedules['wss_hourly'] = array(
            'interval' => 60 * 60,
            'display'  => 'هر 1 ساعت (WSS)'
        );
        
        $schedules['wss_5min'] = array(
            'interval' => 5 * 60,
            'display'  => 'هر 5 دقیقه (WSS Test)'
        );
        
        return $schedules;
    }
    
    public function init_test_expiry_reminder() {
        if (class_exists('WooCommerce') && class_exists('WC_Subscriptions')) {
            if (!wp_next_scheduled('wss_hourly_test_check')) {
                wp_schedule_event(time(), 'wss_5min', 'wss_hourly_test_check');
                error_log('✅ TEST: Cron job scheduled - 5 minute intervals');
            }
        }
    }

    public function check_test_expiring_subscriptions() {
        if(!wss_simagar("active-sms-test-expiry")) {
            error_log('🔔 TEST: Expiry reminder SMS service is disabled');
            return;
        }

        $target_date = date('Y-m-d H:i:s', strtotime('+23 hours 35 minutes'));
        
        error_log('🔔 TEST: Checking subscriptions expiring on: ' . $target_date);

        $subscriptions = wcs_get_subscriptions(array(
            'subscription_status' => array('active'),
            'date_expires' => $target_date,
            'limit' => -1
        ));

        if(empty($subscriptions)) {
            error_log('🔔 TEST: No subscriptions expiring in 23 hours 35 minutes');
            return;
        }

        error_log('🔔 TEST: Found ' . count($subscriptions) . ' subscriptions expiring in 23:35 hours');

        foreach($subscriptions as $subscription) {
            $this->send_test_expiry_reminder($subscription);
        }
    }

    private function send_test_expiry_reminder($subscription) {
        $phone = $subscription->get_billing_phone();
        if(!$phone) {
            error_log('🔔 TEST: No phone number for subscription: ' . $subscription->get_id());
            return;
        }

        $service = wss_simagar("setting-sms-portal");
        $class   = 'WSS_' . ucfirst($service);

        if(!$service || !class_exists($class)) {
            error_log('🔔 TEST: SMS service not configured');
            return;
        }

        $message = $this->get_test_expiry_message($subscription);
        $code = wss_simagar("sms-test-expiry-code");

        error_log('🔔 TEST: Sending expiry reminder for subscription: ' . $subscription->get_id());
        error_log('🔔 TEST: Phone: ' . $phone);
        error_log('🔔 TEST: Message: ' . print_r($message, true));
        error_log('🔔 TEST: Pattern Code: ' . $code);
        
        try {
            $sms_handler = new $class($phone, $message, $code);
            $response = $sms_handler->send();
            
            error_log('✅ TEST: Expiry reminder sent successfully: ' . print_r($response, true));
            
        } catch (Exception $e) {
            error_log('❌ TEST: Expiry reminder failed: ' . $e->getMessage());
        }
    }

    private function get_test_expiry_message($subscription) {
        $pattern = explode(PHP_EOL, wss_simagar("sms-test-expiry-patern"));

        $pattern_array = [];
        foreach ($pattern as $value) {
            $value = trim($value);

            if($value == '{{item_product}}'){
                $pattern_array['item_product'] = $this->get_product_name($subscription);
            }
            if($value == '{{name}}'){
                $pattern_array['name'] = $subscription->get_billing_first_name() ?: 'مشتری';
            }
            if($value == '{{expiry_date}}'){
                $expiry_date = $subscription->get_date('end');
                $pattern_array['expiry_date'] = $expiry_date ? date_i18n('Y-m-d H:i:s', strtotime($expiry_date)) : 'تعیین نشده';
            }
            if($value == '{{hours_remaining}}'){
                $expiry_date = $subscription->get_date('end');
                $hours_remaining = $expiry_date ? floor((strtotime($expiry_date) - time()) / (60 * 60)) : 0;
                $pattern_array['hours_remaining'] = max(0, $hours_remaining);
            }
        }
        
        error_log('🔔 TEST: Message array: ' . print_r($pattern_array, true));
        return $pattern_array;
    }

    private function get_product_name($subscription) {
        $product_names = [];
        foreach ($subscription->get_items() as $item) {
            $product = $item->get_product();
            if($product) {
                $product_names[] = $product->get_name();
            }
        }
        return implode(', ', $product_names) ?: 'محصول نامشخص';
    }
}