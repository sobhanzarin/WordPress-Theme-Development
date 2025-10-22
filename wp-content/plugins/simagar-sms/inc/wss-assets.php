<?php 
class Assets
{
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'front_assets']);
        add_action('admin_enqueue_scripts', [$this, 'admin_assets']);
    }

    public function admin_assets()
    {
        // اگه فایل js برای مدیریت داری اینجا درستش کن
        wp_enqueue_script('wss-admin-main', WSS_ASSETS_ADMIN . 'js/admin.js', ['jquery'], WSS_VER, true); 
        // wp_enqueue_style('wss-admin-style', WSS_ASSETS_ADMIN . 'css/admin.css', [], WSS_VER);
    }

   public function front_assets() {
    if(!is_checkout()) return null;

    wp_enqueue_script(
        'wss-sms-checkout',
        WSS_ASSETS_FRONT . 'js/sms-checkout.js',
        ['jquery'],
        WSS_VER,
        true
    );

    wp_localize_script(
        'wss-sms-checkout',
        'wss_ajax_obj',
        [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('wss_sms_nonce')
        ]
    );
}


}
