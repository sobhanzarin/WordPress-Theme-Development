<?php
defined("ABSPATH") || exit("No Access ...");

class WSS_SMS_Notification_Test
{
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_wss_test_sms_checkout', [$this, 'ajax_test_sms']);
        add_action('wp_ajax_nopriv_wss_test_sms_checkout', [$this, 'ajax_test_sms']);
    }

    public function enqueue_scripts() {
        if(is_checkout()){
            wp_enqueue_script(
                'sms-test-checkout', 
                WSS_ASSETS_FRONT . 'js/sms-checkout.js', 
                ['jquery'], 
                WSS_VER, 
                true
            );
            wp_localize_script('sms-test-checkout', 'sms_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('sms_test_nonce')
            ]);
        }
    }

    public function ajax_test_sms() {
        check_ajax_referer('sms_test_nonce', 'nonce');

        $phone = sanitize_text_field($_POST['phone']);
        $message = sanitize_textarea_field($_POST['message']);

        if(!$phone){
            wp_send_json_error(['message'=>'شماره موبایل وارد نشده']);
        }

        $service = wss_simagar("setting-sms-portal");
        $class = 'WSS'.ucfirst($service);
        if(!$service || !class_exists($class)){
            wp_send_json_error(['message'=>'سرویس پیامک یافت نشد']);
        }

        $code = wss_simagar("user-sms-parent-code");
        $instance = new $class($phone, $message, $code);
        $response = $instance->send();

        wp_send_json_success($response);
    }
}

// init

