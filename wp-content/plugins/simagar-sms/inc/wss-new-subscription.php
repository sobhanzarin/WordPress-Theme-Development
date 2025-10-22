<?php
defined("ABSPATH") || exit("No Access ...");

class WSS_SMS_New_Subscription
{
    public $subscription;

    public function __construct() {
        add_action('woocommerce_subscription_payment_complete', [$this, 'ss_sms_on_subscription_payment'], 10, 1);
    }

    public function ss_sms_on_subscription_payment($subscription) {
        if(is_numeric($subscription)){
            $this->subscription = wc_get_subscription($subscription);
        }
        if(!$this->subscription) return null;

        if(wss_simagar("active-sms-sub")) return null;

        $this->send();
    }

    private function send() {
        $phone = $this->subscription->get_billing_phone();

        $service = wss_simagar("setting-sms-portal");
        $class   = 'WSS_' . ucfirst($service);

        if(!$service || !class_exists($class)) return null;
        $message  = $this->get_message();
        // $message  = $this->get_message();
        $code = wss_simagar("user-sms-parent-code-sub");

        // ارسال پیامک و دریافت response
        $responses = (new $class($phone, $message, $code))->send();

        // ذخیره response در متای سفارش
        update_post_meta($this->subscription->subscription_id(), '_wss_sms_sub', 'TEST_RESPONSE_' . time());  
        wc_get_logger()->info(
            print_r($responses, true), 
            ['source' => 'wss-sms']
        );

        update_post_meta($this->subscription->get_id(), '_wss_sms_sub', $responses);

        // دیباگ در لاگ
        error_log('Order ID: ' . $this->subscription->get_id());
        error_log(print_r($responses, true));
        error_log('Response from SMS service: ' . json_encode($responses));
    }

    private function get_message() {
        $pattern = explode(PHP_EOL, wss_simagar("user-sms-parent-sub"));

        $patern_arra = [];
        foreach ($pattern as $value) {
            $value = trim($value);

            if($value == '{{item_product}}'){
                $patern_arra['item_product'] = $this->get_product();
            }
            if($value == '{{start_date}}'){
                $patern_arra['start_date'] = $this->subscription->get_status();
            }
            if($value == '{{end_date}}'){
                $patern_arra['end_date'] =  $this->subscription->get_date('start');
            }
            if($value == '{{subscription_status}}'){
                $patern_arra['subscription_status'] =  $this->subscription->get_status();
            }
            if($value == '{{billing_period}}'){
                $patern_arra['billing_period'] =  $this->subscription->get_billing_period();
            }
            if($value == '{{billing_interval}}'){
                $patern_arra['billing_interval'] =  $this->subscription->get_billing_interval();
            }
            if($value == '{{next_payment_date}}'){
                $patern_arra['next_payment_date'] =  $this->subscription->get_date('next_payment');
            }
        }
    }

    private function get_product() {
        $subscription_list = [];

        foreach ($this->subscription->get_items() as $item) {
            $subscription = $item->get_product();
            if(!$subscription) continue;

            $name = $subscription->get_name();

            $subscription_list[] = $name;
        }
        $final_string = implode(', ', $subscription_list);

        return $final_string;
    }
}
