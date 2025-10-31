<?php
/**
 * Plugin Name: WooCommerce Subscription Invoices
 * Description: سیستم مدیریت فاکتورهای اشتراک ووکامرس
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: wc-sub-invoices
 * Requires Plugins: woocommerce, woocommerce-subscriptions
 */

if (!defined('ABSPATH')) exit;

define('WSI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WSI_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WSI_PLUGIN_VERSION', '1.0.0');

// بارگذاری کلاس‌ها
require_once WSI_PLUGIN_PATH . 'includes/class-wsi-core.php';
require_once WSI_PLUGIN_PATH . 'includes/class-wsi-post-types.php';
require_once WSI_PLUGIN_PATH . 'includes/class-wsi-manual-invoices.php';
require_once WSI_PLUGIN_PATH . 'includes/class-wsi-account.php';

// ثبت منوها
function wsi_add_admin_menus() {
    add_menu_page(
        'فاکتورهای اشتراک',
        'فاکتورهای اشتراک',
        'manage_woocommerce',
        'edit.php?post_type=wsi_invoice',
        '',
        'dashicons-text-page',
        56
    );
    
    add_submenu_page(
        'edit.php?post_type=wsi_invoice',
        'ایجاد فاکتور جدید',
        'ایجاد فاکتور جدید',
        'manage_woocommerce',
        'wsi-create-invoice',
        'wsi_create_invoice_page'
    );
}
add_action('admin_menu', 'wsi_add_admin_menus');

function wsi_create_invoice_page() {
    if (!class_exists('WSI_Manual_Invoices')) {
        require_once WSI_PLUGIN_PATH . 'includes/class-wsi-manual-invoices.php';
    }
    WSI_Manual_Invoices::create_invoice_page();
}

// راه‌اندازی
function wc_subscription_invoices() {
    return WSI_Core::get_instance();
}
add_action('plugins_loaded', 'wc_subscription_invoices');

register_activation_hook(__FILE__, function() {
    flush_rewrite_rules();
});
?>