<?php
/*
Plugin Name: پنل اختصاصی اس ام اس سیماگر
Plugin URI: 
Description:  
Version: 1.0.0
Author URI:
*/
defined("ABSPATH") || exit("No Access ...");

class WSS_Core
{
    private static $_instance = null;
    const MIN_PHP_VERSION = '7.4';

    public static function instance()
    {
        if(is_null(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {

        // add_action('init', [$this, 'wss_load_textdomain']);
        add_action('plugins_loaded', [$this, 'wss_load_textdomain']);
        add_action('plugins_loaded', [$this, 'load_plugin']);

        $this->constant();

        register_activation_hook(WSS_BASE_FILE, [$this, 'active']);
        register_deactivation_hook(WSS_BASE_FILE, [$this, 'deactive']);


        // if(version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<')){
        //     add_action('admin_notices', [$this, 'admin_notices_handler']);
        // }

        // init hook برای همه کلاس‌ها و ترجمه‌ها

    }

    public function constant()
    {
        if(!function_exists('get_plugin_data')){
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        define('WSS_BASE_FILE', __FILE__);
        define('WSS_PATH', plugin_dir_path(WSS_BASE_FILE));
        define('WSS_URL', plugin_dir_url(WSS_BASE_FILE));
        define('WSS_ASSETS_ADMIN', trailingslashit(WSS_URL . 'assets/admin'));
        define('WSS_ASSETS_FRONT', trailingslashit(WSS_URL . 'assets/front'));
        define('WSS_INC_PATH', trailingslashit(WSS_PATH . 'inc'));
        
        $wss_plugin_data = get_plugin_data(WSS_BASE_FILE);
        define("WSS_VER", $wss_plugin_data['Version']);

    }
        public function load_plugin()
    {

        require_once WSS_PATH . 'vendor/autoload.php';
        require_once WSS_INC_PATH . 'admin/codestar/codestar-framework.php';
        // require_once WSS_INC_PATH . 'wss-ajax.php';
        require WSS_INC_PATH . 'admin/wss-setting.php';
        // require_once WSS_INC_PATH . 'wss-notification-test.php';

        new Assets();
        new WSS_SMS_Notification_Order();
    }
    public function wss_load_textdomain()
    {
                // ترجمه‌ها
        load_plugin_textdomain('simagar-sms', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    public function active() {}
    public function deactive() {}

    // public function admin_notices_handler()
    // {}

}

WSS_Core::instance();
