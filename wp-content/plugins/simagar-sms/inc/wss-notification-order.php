<?php
defined("ABSPATH") || exit("No Access ...");

class WSS_SMS_Notification_Order
{
    public $order;

    public function __construct() {
        add_action('woocommerce_thankyou', [$this, 'wss_send_sms_after_payment'], 10, 1);
    }

    public function wss_send_sms_after_payment($order_id) {
        $this->order = wc_get_order($order_id);

        // اگر سفارش وجود نداشت یا پرداخت نشده بود برگرد
        // if(!$this->order || !$this->order->is_paid()) {
        //     return;
        // }
        if(wss_simagar("active-sms-order")) return null;

        $this->send();
    }

    private function send() {
        // if(!$this->order) return;

        $phone = $this->order->get_billing_phone();
        // if(!$phone) return;

        $service = wss_simagar("setting-sms-portal");
        $class   = 'WSS_' . ucfirst($service);

        // if(!$service || !class_exists($class)) return null;

        $message  = $this->get_message();
        // $message  = $this->get_message();
        $code = wss_simagar("user-sms-parent-code");

        // ارسال پیامک و دریافت response
        $responses = (new $class($phone, $message, $code))->send();

        // ذخیره response در متای سفارش
        update_post_meta($this->order->get_id(), '_wss_sms_response', 'TEST_RESPONSE_' . time());  
        wc_get_logger()->info(
            print_r($responses, true), 
            ['source' => 'wss-sms']
        );

        update_post_meta($this->order->get_id(), '_wss_sms_response', $responses);

        // دیباگ در لاگ
        error_log('Order ID: ' . $this->order->get_id());
        error_log(print_r($responses, true));
        error_log('Response from SMS service: ' . json_encode($responses));
    }

    private function get_message() {
        $pattern = explode(PHP_EOL, wss_simagar("user-sms-parent"));

        $patern_arra = [];
        foreach ($pattern as $value) {
            $value = trim($value);

            if($value == '{{item_id}}'){
                $patern_arra['item_id'] = $this->order->get_id();
            }
            if($value == '{{item_product}}'){
                $patern_arra['item_product'] = $this->get_product();
            }
            if($value == '{{name}}'){
                $patern_arra['name'] = $this->order->get_billing_first_name();
            }
            if($value == '{{status}}'){
                $patern_arra['status'] = $this->order->get_status();
            }
            if($value == '{{price}}'){
                $patern_arra['price'] =  $this->order->get_total();
            }
        
        }

        return $patern_arra;

        // $pattern_array = [
        //     'item_id'      => $this->order->get_id(),
        //     'item_product' => $this->get_product(),
        //     'name'         => $this->order->get_billing_first_name(),
        //     'status'       => $this->order->get_status(),
        //     'price'        => $this->order->get_total(),
        // ];

        // foreach($pattern_array as $key => $value){
        //     $pattern = str_replace('{{'.$key.'}}', $value, $pattern);
        // }

        // return $pattern;
    }

    private function get_product() {
        $product_list = [];

        foreach ($this->order->get_items() as $item) {
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
