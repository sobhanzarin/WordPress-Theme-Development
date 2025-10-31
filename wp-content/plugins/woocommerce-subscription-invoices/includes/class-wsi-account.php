<?php
class WSI_Account {
    
    public static function init() {
        add_filter('woocommerce_account_menu_items', array(__CLASS__, 'add_invoices_tab'));
        add_action('init', array(__CLASS__, 'add_endpoint'));
        add_action('woocommerce_account_invoices_endpoint', array(__CLASS__, 'invoices_tab_content'));
        add_action('template_redirect', array(__CLASS__, 'handle_invoice_payment'));
        
        // رفرش rewrite rules وقتی افزونه فعال میشه
        register_activation_hook(__FILE__, array(__CLASS__, 'flush_rewrite_rules'));
    }
    
    public static function flush_rewrite_rules() {
        self::add_endpoint();
        flush_rewrite_rules();
    }
    
    public static function add_endpoint() {
        add_rewrite_endpoint('invoices', EP_PAGES);
        
        // مطمئن شو که ووکامرس endpointها رو میشناسه
        if (function_exists('WC')) {
            WC()->query->init_query_vars();
            WC()->query->add_endpoints();
        }
    }
    
    public static function add_invoices_tab($items) {
        // حذف تب لاگاوت از انتهای لیست
        $logout = $items['customer-logout'];
        unset($items['customer-logout']);
        
        // اضافه کردن تب فاکتورها
        $items['invoices'] = 'فاکتورها';
        
        // اضافه کردن مجدد لاگاوت
        $items['customer-logout'] = $logout;
        
        return $items;
    }
    
    public static function invoices_tab_content() {
        $user_id = get_current_user_id();
        
        // دیباگ - بررسی اینکه تابع فراخوانی میشه
        error_log('WSI: invoices_tab_content called for user: ' . $user_id);
        
        $invoices = get_posts(array(
            'post_type' => 'wsi_invoice',
            'numberposts' => -1,
            'meta_key' => '_user_id',
            'meta_value' => $user_id,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        ?>
        <div class="woocommerce-MyAccount-content-invoices">
            <h3>فاکتورهای من</h3>
            
            <?php if (empty($invoices)): ?>
                <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
                    <p>هیچ فاکتوری یافت نشد.</p>
                </div>
            <?php else: ?>
                <table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
                    <thead>
                        <tr>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number">
                                <span class="nobr">شماره فاکتور</span>
                            </th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date">
                                <span class="nobr">محصول</span>
                            </th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status">
                                <span class="nobr">مبلغ</span>
                            </th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total">
                                <span class="nobr">تاریخ انقضا</span>
                            </th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions">
                                <span class="nobr">وضعیت</span>
                            </th>
                            <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions">
                                <span class="nobr">عملیات</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                        <?php
                        $product_id = get_post_meta($invoice->ID, '_subscription_product_id', true);
                        $amount = get_post_meta($invoice->ID, '_amount', true);
                        $status = get_post_meta($invoice->ID, '_status', true);
                        $due_date = get_post_meta($invoice->ID, '_due_date', true);
                        $product = wc_get_product($product_id);
                        ?>
                        <tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr($status); ?> order">
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="شماره فاکتور">
                                <strong>#<?php echo $invoice->ID; ?></strong>
                            </td>
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="محصول">
                                <?php echo $product ? esc_html($product->get_name()) : '<span style="color: #999;">محصول حذف شده</span>'; ?>
                            </td>
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="مبلغ">
                                <?php echo wc_price($amount); ?>
                            </td>
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="تاریخ انقضا">
                                <?php echo $due_date ? date_i18n('Y/m/d', strtotime($due_date)) : '-'; ?>
                            </td>
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="وضعیت">
                                <span class="wsi-status wsi-status-<?php echo esc_attr($status); ?>">
                                    <?php echo $status === 'pending' ? 'در انتظار پرداخت' : 'پرداخت شده'; ?>
                                </span>
                            </td>
                            <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="عملیات">
                                <?php if ($status == 'pending'): ?>
                                    <a href="<?php echo wp_nonce_url(
                                        add_query_arg('pay_invoice', $invoice->ID, wc_get_account_endpoint_url('invoices')),
                                        'pay_invoice_' . $invoice->ID
                                    ); ?>" class="woocommerce-button button pay">
                                        پرداخت
                                    </a>
                                <?php else: ?>
                                    <span class="wsi-paid">پرداخت شده</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <style>
            .wsi-status-pending { 
                color: #ffba00; 
                font-weight: bold; 
                background: #fff8e5;
                padding: 4px 8px;
                border-radius: 3px;
                font-size: 12px;
            }
            .wsi-status-paid { 
                color: #00a32a; 
                font-weight: bold;
                background: #edfaef;
                padding: 4px 8px;
                border-radius: 3px;
                font-size: 12px;
            }
            .wsi-paid { 
                color: #00a32a; 
                font-weight: bold;
            }
            .woocommerce-MyAccount-content-invoices {
                margin-top: 20px;
            }
        </style>
        <?php
    }
    
    public static function handle_invoice_payment() {
        if (!isset($_GET['pay_invoice']) || !isset($_GET['_wpnonce'])) {
            return;
        }
        
        $invoice_id = intval($_GET['pay_invoice']);
        $nonce = $_GET['_wpnonce'];
        
        if (!wp_verify_nonce($nonce, 'pay_invoice_' . $invoice_id)) {
            wc_add_notice('خطای امنیتی! لطفا مجددا تلاش کنید.', 'error');
            wp_redirect(wc_get_account_endpoint_url('invoices'));
            exit;
        }
        
        $user_id = get_current_user_id();
        $invoice_user_id = get_post_meta($invoice_id, '_user_id', true);
        
        // بررسی مالکیت فاکتور
        if ($user_id != $invoice_user_id) {
            wc_add_notice('شما مجاز به پرداخت این فاکتور نیستید.', 'error');
            wp_redirect(wc_get_account_endpoint_url('invoices'));
            exit;
        }
        
        $current_status = get_post_meta($invoice_id, '_status', true);
        
        if ($current_status !== 'pending') {
            wc_add_notice('این فاکتور قبلاً پرداخت شده است.', 'error');
            wp_redirect(wc_get_account_endpoint_url('invoices'));
            exit;
        }
        
        // ایجاد اشتراک برای کاربر
        $product_id = get_post_meta($invoice_id, '_subscription_product_id', true);
        $amount = get_post_meta($invoice_id, '_amount', true);
        
        if (self::create_subscription_for_user($user_id, $product_id, $amount)) {
            // بروزرسانی وضعیت فاکتور
            update_post_meta($invoice_id, '_status', 'paid');
            update_post_meta($invoice_id, '_paid_date', current_time('mysql'));
            
            wc_add_notice('فاکتور با موفقیت پرداخت شد و اشتراک شما فعال گردید.', 'success');
        } else {
            wc_add_notice('خطا در ایجاد اشتراک! لطفا با پشتیبانی تماس بگیرید.', 'error');
        }
        
        wp_redirect(wc_get_account_endpoint_url('invoices'));
        exit;
    }
    
    private static function create_subscription_for_user($user_id, $product_id, $amount) {
        if (!class_exists('WC_Subscriptions')) {
            return false;
        }
        
        $product = wc_get_product($product_id);
        $user = get_userdata($user_id);
        
        if (!$product || !$user) {
            return false;
        }
        
        try {
            // ایجاد اشتراک مستقیم بدون سبد خرید
            $subscription = wcs_create_subscription(array(
                'customer_id' => $user_id,
                'billing_period' => 'year',
                'billing_interval' => 1,
                'start_date' => current_time('mysql'),
                'next_payment_date' => date('Y-m-d H:i:s', strtotime('+1 year')),
            ));
            
            if (is_wp_error($subscription)) {
                throw new Exception($subscription->get_error_message());
            }
            
            $subscription->add_product($product, 1);
            $subscription->set_total($amount);
            $subscription->update_status('active');
            
            return true;
            
        } catch (Exception $e) {
            error_log('WSI Error creating subscription: ' . $e->getMessage());
            return false;
        }
    }
}

WSI_Account::init();
?>