<?php
/**
 * Plugin Name: WooCommerce Subscription Pending Orders
 * Description: نمایش سفارش‌های در انتظار پرداخت برای محصولات اشتراکی در حساب کاربری مشتری
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: wc-sub-pending-orders
 * Requires Plugins: woocommerce, woocommerce-subscriptions
 */

// جلوگیری از دسترسی مستقیم
if (!defined('ABSPATH')) {
    exit;
}

// بررسی وجود ووکامرس و سابسکریپشن
register_activation_hook(__FILE__, 'wspo_check_dependencies');
function wspo_check_dependencies() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('این افزونه نیاز به WooCommerce دارد.');
    }
    
    if (!class_exists('WC_Subscriptions')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die('این افزونه نیاز به WooCommerce Subscriptions دارد.');
    }
}

class WC_Subscription_Pending_Orders {
    
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
        // اضافه کردن تب به حساب کاربری
        add_filter('woocommerce_account_menu_items', array($this, 'add_pending_subscription_orders_tab'));
        
        // ثبت endpoint
        add_action('init', array($this, 'add_pending_subscription_orders_endpoint'));
        
        // محتوای تب
        add_action('woocommerce_account_pending-subscription-orders_endpoint', array($this, 'pending_subscription_orders_content'));
        
        // پرداخت سفارش
        add_action('template_redirect', array($this, 'handle_order_payment'));
        
        // رفرش rewrite rules هنگام فعال‌سازی
        register_activation_hook(__FILE__, array($this, 'flush_rewrite_rules'));
    }
    
    public function add_pending_subscription_orders_tab($items) {
        $new_items = array();
        
        // اضافه کردن تب قبل از خروج
        foreach ($items as $key => $value) {
            if ($key === 'customer-logout') {
                $new_items['pending-subscription-orders'] = 'سفارش‌های اشتراکی در انتظار پرداخت';
            }
            $new_items[$key] = $value;
        }
        
        return $new_items;
    }
    
    public function add_pending_subscription_orders_endpoint() {
        add_rewrite_endpoint('pending-subscription-orders', EP_PAGES);
    }
    
    public function pending_subscription_orders_content() {
        $user_id = get_current_user_id();
        
        // گرفتن سفارش‌های در انتظار پرداخت کاربر که شامل محصولات اشتراکی هستند
        $pending_orders = wc_get_orders(array(
            'customer_id' => $user_id,
            'status' => 'pending',
            'limit' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        // فیلتر کردن سفارش‌هایی که محصول اشتراکی دارند
        $subscription_orders = array();
        foreach ($pending_orders as $order) {
            if ($this->order_contains_subscription($order)) {
                $subscription_orders[] = $order;
            }
        }
        ?>
        <div class="woocommerce-MyAccount-content">
            <h3>سفارش‌های اشتراکی در انتظار پرداخت</h3>
            
            <?php if (empty($subscription_orders)): ?>
                <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                    <p>در حال حاضر هیچ سفارش اشتراکی در انتظار پرداختی ندارید.</p>
                </div>
            <?php else: ?>
                <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
                    <thead>
                        <tr>
                            <th class="woocommerce-orders-table__header">شماره سفارش</th>
                            <th class="woocommerce-orders-table__header">محصول اشتراکی</th>
                            <th class="woocommerce-orders-table__header">دوره</th>
                            <th class="woocommerce-orders-table__header">مبلغ</th>
                            <th class="woocommerce-orders-table__header">وضعیت</th>
                            <th class="woocommerce-orders-table__header">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscription_orders as $order): ?>
                        <?php
                        // اطلاعات محصول اشتراکی
                        $subscription_info = $this->get_subscription_order_info($order);
                        ?>
                        <tr class="woocommerce-orders-table__row">
                            <td class="woocommerce-orders-table__cell">
                                <a href="<?php echo esc_url($order->get_view_order_url()); ?>">
                                    #<?php echo $order->get_order_number(); ?>
                                </a>
                            </td>
                            <td class="woocommerce-orders-table__cell">
                                <?php echo esc_html($subscription_info['product_name']); ?>
                            </td>
                            <td class="woocommerce-orders-table__cell">
                                <?php echo esc_html($subscription_info['billing_period']); ?>
                            </td>
                            <td class="woocommerce-orders-table__cell">
                                <?php echo $order->get_formatted_order_total(); ?>
                            </td>
                            <td class="woocommerce-orders-table__cell">
                                <span style="color: #ffba00; font-weight: bold; background: #fff8e5; padding: 4px 8px; border-radius: 3px; font-size: 12px;">
                                    در انتظار پرداخت
                                </span>
                            </td>
                            <td class="woocommerce-orders-table__cell">
                                <form method="post" action="">
                                    <?php wp_nonce_field('wspo_pay_order_' . $order->get_id(), 'wspo_nonce'); ?>
                                    <input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
                                    <button type="submit" name="pay_subscription_order" class="woocommerce-button button pay">
                                        پرداخت اشتراک
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <style>
            .woocommerce-MyAccount-content .woocommerce-button.button.pay {
                background: #00a32a;
                color: white;
                border: none;
                padding: 8px 15px;
                border-radius: 3px;
                text-decoration: none;
                display: inline-block;
                font-size: 13px;
                white-space: nowrap;
                cursor: pointer;
            }
            .woocommerce-MyAccount-content .woocommerce-button.button.pay:hover {
                background: #008a20;
            }
            
            .woocommerce-MyAccount-content form {
                margin: 0;
                display: inline;
            }
        </style>
        <?php
    }
    
    /**
     * پرداخت سفارش
     */
    public function handle_order_payment() {
        if (!isset($_POST['pay_subscription_order']) || !isset($_POST['wspo_nonce']) || !isset($_POST['order_id'])) {
            return;
        }
        
        $order_id = intval($_POST['order_id']);
        $nonce = $_POST['wspo_nonce'];
        
        if (!wp_verify_nonce($nonce, 'wspo_pay_order_' . $order_id)) {
            wc_add_notice('خطای امنیتی! لطفا مجددا تلاش کنید.', 'error');
            return;
        }
        
        $order = wc_get_order($order_id);
        $user_id = get_current_user_id();
        
        // بررسی مالکیت سفارش
        if (!$order || $order->get_customer_id() !== $user_id) {
            wc_add_notice('شما مجاز به پرداخت این سفارش نیستید.', 'error');
            return;
        }
        
        // بررسی وضعیت سفارش
        if ($order->get_status() !== 'pending') {
            wc_add_notice('این سفارش قبلاً پرداخت شده است.', 'error');
            return;
        }
        
        // اضافه کردن محصولات سفارش به سبد خرید
        WC()->cart->empty_cart();
        
        foreach ($order->get_items() as $item) {
            // بررسی نوع آیتم
            if (is_a($item, 'WC_Order_Item_Product')) {
                $product_id = $item->get_product_id();
                $variation_id = $item->get_variation_id();
                $quantity = $item->get_quantity();
                
                if ($variation_id && $variation_id > 0) {
                    // اگر محصول متغیر هست
                    WC()->cart->add_to_cart($product_id, $quantity, $variation_id);
                } else {
                    // اگر محصول ساده هست
                    WC()->cart->add_to_cart($product_id, $quantity);
                }
            }
        }
        
        // هدایت به صفحه checkout
        wp_redirect(wc_get_checkout_url());
        exit;
    }
    
    /**
     * بررسی می‌کند که سفارش شامل محصول اشتراکی هست یا نه
     */
    private function order_contains_subscription($order) {
        foreach ($order->get_items() as $item) {
            // بررسی نوع آیتم
            if (is_a($item, 'WC_Order_Item_Product')) {
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);
                
                if ($product && class_exists('WC_Subscriptions_Product') && WC_Subscriptions_Product::is_subscription($product)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * گرفتن اطلاعات اشتراک از سفارش
     */
    private function get_subscription_order_info($order) {
        $info = array(
            'product_name' => '',
            'billing_period' => '1 ماه'
        );
        
        foreach ($order->get_items() as $item) {
            // بررسی نوع آیتم
            if (is_a($item, 'WC_Order_Item_Product')) {
                $product_id = $item->get_product_id();
                $product = wc_get_product($product_id);
                
                if ($product && class_exists('WC_Subscriptions_Product') && WC_Subscriptions_Product::is_subscription($product)) {
                    $info['product_name'] = $product->get_name();
                    
                    // گرفتن دوره اشتراک
                    $period = WC_Subscriptions_Product::get_period($product);
                    $interval = WC_Subscriptions_Product::get_interval($product);
                    
                    if ($period && $interval) {
                        $periods = array(
                            'day' => 'روز',
                            'week' => 'هفته', 
                            'month' => 'ماه',
                            'year' => 'سال'
                        );
                        
                        $persian_period = isset($periods[$period]) ? $periods[$period] : $period;
                        $info['billing_period'] = $interval . ' ' . $persian_period;
                    }
                    break;
                }
            }
        }
        
        return $info;
    }
    
    public function flush_rewrite_rules() {
        $this->add_pending_subscription_orders_endpoint();
        flush_rewrite_rules();
    }
}

// راه‌اندازی افزونه
function wc_subscription_pending_orders_init() {
    return WC_Subscription_Pending_Orders::get_instance();
}
add_action('plugins_loaded', 'wc_subscription_pending_orders_init');

// وقتی افزونه غیرفعال شد
register_deactivation_hook(__FILE__, 'wspo_deactivate');
function wspo_deactivate() {
    flush_rewrite_rules();
}
?>