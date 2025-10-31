<?php
class WSI_Core {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init();
    }
    
    public function init() {
        // بررسی وابستگی‌ها
        add_action('admin_init', array($this, 'check_dependencies'));
        
        // بارگذاری ماژول‌ها
        add_action('plugins_loaded', array($this, 'load_modules'));
    }
    
    public function check_dependencies() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return false;
        }
        
        if (!class_exists('WC_Subscriptions')) {
            add_action('admin_notices', array($this, 'subscriptions_missing_notice'));
            return false;
        }
        
        return true;
    }
    
    public function load_modules() {
        if (!$this->check_dependencies()) return;
        
        $modules = array(
            'WSI_Post_Types',
            'WSI_Manual_Invoices', 
            'WSI_Account'
        );
        
        foreach ($modules as $class) {
            if (class_exists($class) && method_exists($class, 'init')) {
                call_user_func(array($class, 'init'));
            }
        }
    }
    
    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>افزونه WooCommerce Subscription Invoices نیاز به WooCommerce دارد.</p></div>';
    }
    
    public function subscriptions_missing_notice() {
        echo '<div class="error"><p>افزونه WooCommerce Subscription Invoices نیاز به WooCommerce Subscriptions دارد.</p></div>';
    }
}
?>