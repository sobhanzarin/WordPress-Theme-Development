<?php
defined("ABSPATH") || exit("No Access ...");

class WSS_SMS_Notification_Order_Copy
{
    public function __construct() {
        
        add_action('woocommerce_new_order', [$this, 'wss_new_order_handler'], 10, 2);
    }

    public function wss_new_order_handler($order_id){
        // if(!wss_simagar("active-sms")) {
        //     return null;
        // }
        $order = wc_get_order($order_id);
        $this->send($order);

    }
    public function send($order){
        if(!$order) return;

    $phone = $order->get_billing_phone();
    if(!$phone) return;

    $service = wss_simagar("setting-sms-portal");
    $class   = 'WSS_' . ucfirst($service);

    if(!$service || !class_exists($class)) return null;

    $message  = $this->get_message($order);
    $code     = wss_simagar("user-sms-parent-code");

    // ارسال پیامک و دریافت response
    $responses = (new $class($phone, $message, $code))->send();

    // ذخیره response در متای سفارش
    update_post_meta($order->get_id(), '_wss_sms_response', $responses);

    // دیباگ در لاگ
    error_log('Order ID: ' . $order->get_id());
    error_log(print_r($responses, true));
    error_log('Response from SMS service: ' . json_encode($responses));
    }

   public function get_message($order)
{
    $pattern = wss_simagar("user-sms-parent-text");

    $products = $this->get_product($order);

    // آرایه placeholderها با مقدار واقعی
    if (!is_array($products)) {
        $products = [$products ?: '-'];
    }

    $pattern_array = [
        'item_id'      => $order->get_id(),
        'item_product' => implode(', ', $products),
        'name'         => $order->get_billing_first_name(),
        'status'       => $order->get_status(),
        'price'        => $order->get_total(),
    ];
    // جایگزینی تمام placeholderها در متن
      foreach($pattern_array as $key => $value){
            $pattern = str_replace('{{'.$key.'}}', $value, $pattern);
        }

        return $pattern;
}

    public function get_product($order)
    {
        $product_list =[];
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if(!$product) continue;
            $name = $product->get_name();
            $quantiy = $item->get_quantity();

            $product_list[] = $name . '*' . $quantiy;
        }
        return $product_list;

    }

}