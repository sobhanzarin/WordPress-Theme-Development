<?php
defined("ABSPATH") || exit;

aadd_action('wp_ajax_wss_send_sms', 'wss_send_sms_callback');
add_action('wp_ajax_nopriv_wss_send_sms', 'wss_send_sms_callback');

function wss_send_sms_callback() {
    check_ajax_referer('wss_sms_nonce', 'nonce');

    if(!isset($_POST['order_id'])) {
        wp_send_json_error(['message' => 'Order ID missing']);
    }

    $order_id = intval($_POST['order_id']);
    $order = wc_get_order($order_id);
    if(!$order) {
        wp_send_json_error(['message' => 'Order not found']);
    }

    $message = WSS_SMS_Notification_Order::instance()->get_message($order);

    $phone = $order->get_billing_phone();
    $service = wss_simagar("setting-sms-portal");
    $class = 'WSS_' . ucfirst($service);
    if(!$service || !class_exists($class)) {
        wp_send_json_error(['message' => 'SMS service not configured']);
    }

    $code = wss_simagar("user-sms-parent-code");
    $response = (new $class($phone, $message, $code))->send();

    wp_send_json_success([
        'message' => $message,
        'response' => $response
    ]);
}
