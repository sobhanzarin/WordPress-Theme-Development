<!-- <?php
defined("ABSPATH") || exit("No Access ...");

class WSS_SMS_NOTIFICATION
{
    public function __construct() {
        
        add_action('woocommerce_subscription_status_active', [$this, 'wss_subscription_active_handler'], 10, 1);
    }

    public function wss_subscription_active_handler($subscription){
        if(!wss_simagar('active-sms')) return null;

        $this->send();

    }
    public function send($subscription){

        $service = wss_setting("setting-sms-portal");
        $class = 'WSS' . ucfirst($service);

        if(!$service || !class_exists($class)){
            return null;
        }
        
        $phone = $subscription->get_billing_phone();
        $name  = $subscription->get_billing_first_name();
        $sub_id = $subscription->get_id();

        (new $class->send($phone));
    }

    public function get_message($subscription)
    {
        $patern = wss_setting("user-sms-parent-text");

        $items = $subscription->get_items();
        foreach ( $items as $item ) {
        $product = $item->get_product();    
        $product_title = $product->get_name(); 
        break;
    }

        $patern = explode(PHP_EOL, $patern);
        $pattern_arra = [];
        foreach ( $patern as $code) {
            if($code == "{{subscription_id}}"){
                $pattern_arra['subscription_id'] = $subscription->get_id();
            }
              if($code == "{{title}}"){
                $pattern_arra['title'] = $product_title;
            }
             if($code == "{{name}}"){
                $pattern_arra['name'] = $subscription->get_billing_first_name();
            }
        }
    }


}