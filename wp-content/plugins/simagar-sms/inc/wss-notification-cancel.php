<?php
defined("ABSPATH") || exit("No Access ...");

class WSS_SMS_Notification_Cancel
{
    public $subscription;

    public function __construct() {
        add_action('init', [$this, 'init_sms_subscription_cancel']);
       
    }
    public function init_sms_subscription_cancel(){
        if (class_exists('WooCommerce') && class_exists('WC_Subscriptions')) {
            add_action('woocommerce_subscription_status_cancelled', [$this, 'wss_send_sms_on_cancel'], 10, 1);
            add_action('woocommerce_subscription_status_on-hold', [$this, 'wss_send_sms_on_cancel'], 10, 1);
            add_action('woocommerce_subscription_status_expired', [$this, 'wss_send_sms_on_cancel'], 10, 1);
        }
    }
    public function wss_send_sms_on_cancel($subscription) {

        $this->subscription = $subscription;

        if(!wss_simagar("active-sms-cancel")) return null;

        $this->send();
    }
    private function send() {
        if(!$this->subscription) {
        error_log('No subscription object available');
        return; }

        $phone = $this->subscription->get_billing_phone();
        if(!$phone) {
            error_log('No phone number found for subscription: ' . $this->subscription->get_id());
            return;
        }
        $service = wss_simagar("setting-sms-portal");
        $class   = 'WSS_' . ucfirst($service);

        if(!$service || !class_exists($class)) {
            error_log('SMS service not configured or class not found: ' . $service);
            return;
        }

        $message = $this->get_message();
        $code = wss_simagar("user-sms-parent-code-cancel");
        $sms_handler = new $class($phone, $message, $code);
        $responses = $sms_handler->send();

        wc_get_logger()->info(
            print_r($responses, true), 
            ['source' => 'wss-sms']
        );
    }

    private function get_message() {
        $pattern = explode(PHP_EOL, wss_simagar("user-sms-parent-cancel"));

        $pattern_array = [];
        foreach ($pattern as $value) {
            $value = trim($value);

            if($value == '{{item_product}}'){
                $pattern_array['item_product'] = $this->get_products();
            }
            if($value == '{{name}}'){
                $pattern_array['name'] = $this->subscription->get_billing_first_name();
            }
            if($value == '{{status}}'){
                $pattern_array['status'] = $this->subscription->get_status();
            }
        
        }

        return $pattern_array;
    }

    private function get_products() {
        $product_list = [];

        foreach ($this->subscription->get_items() as $item) {
            $product = $item->get_product();
            if(!$product) continue;

            $name = $product->get_name();
            $quantity = $item->get_quantity();

            $product_list[] = $name . '*' . $quantity;
        }
        $final_string = implode(', ', $product_list);

        return $final_string;
    }
}
