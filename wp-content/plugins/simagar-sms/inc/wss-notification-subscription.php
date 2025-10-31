<?php
defined("ABSPATH") || exit("No Access ...");
 
class WSS_SMS_Notification_Subscription
{
    public function __construct() {
        add_action('init', [$this, 'init_sms_subscription']);
        add_action('wss_send_subscription_sms', [$this, 'scheduled_sms_send'], 10, 2);
    }
    
    public function init_sms_subscription() {
        if (class_exists('WooCommerce') && class_exists('WC_Subscriptions')) {
            add_action('woocommerce_subscription_payment_complete', [$this, 'send_sms_on_subscription_payment'], 10, 1);
        }
    }

    public function send_sms_on_subscription_payment($subscription) {
        if (!is_a($subscription, 'WC_Subscription')) {
            return;
        }

        if(!wss_simagar("active-sms-sub")) {
            return;
        }

        $phone = $subscription->get_billing_phone();
        if(!$phone) {
            return;
        }

        // ارسال پیامک رو 5 ثانیه دیگه schedule کن (non-blocking)
        wp_schedule_single_event(time() + 5, 'wss_send_subscription_sms', array(
            'subscription_id' => $subscription->get_id(),
            'phone' => $phone
        ));

        error_log('SMS scheduled for subscription: ' . $subscription->get_id());
    }

    public function scheduled_sms_send($subscription_id, $phone) {
        $subscription = wcs_get_subscription($subscription_id);
        if(!$subscription) {
            error_log('Subscription not found for SMS: ' . $subscription_id);
            return;
        }

        $service = wss_simagar("setting-sms-portal");
        $class   = 'WSS_' . ucfirst($service);

        if(!$service || !class_exists($class)) {
            error_log('SMS service not configured');
            return;
        }

        $message = $this->get_message($subscription);
        $code = wss_simagar("user-sms-parent-code-sub");
        
        error_log('Sending scheduled SMS for subscription: ' . $subscription_id);
        
        try {
            $sms_handler = new $class($phone, $message, $code);
            $responses = $sms_handler->send();
            error_log('Scheduled SMS sent: ' . print_r($responses, true));
            
        } catch (Exception $e) {
            error_log('Scheduled SMS failed: ' . $e->getMessage());
        }
    }

    private function get_message($subscription) {
        $pattern = explode(PHP_EOL, wss_simagar("user-sms-parent-sub"));

        $pattern_array = [];
        foreach ($pattern as $value) {
            $value = trim($value);

            if($value == '{{name}}'){
                $pattern_array[] = $subscription->get_billing_first_name();
            }
            if($value == '{{status}}'){
                $pattern_array[] = $subscription->get_status();
            }
        }
        
        return $pattern_array;
    }
}
