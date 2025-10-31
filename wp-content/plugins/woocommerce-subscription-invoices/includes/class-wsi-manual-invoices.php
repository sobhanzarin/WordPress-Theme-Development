<?php
class WSI_Manual_Invoices {
    
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'handle_invoice_creation'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
    }
    
    public static function enqueue_scripts($hook) {
        if ($hook === 'wsi_invoice_page_wsi-create-invoice') {
            wp_enqueue_script('jquery');
        }
    }
    
    public static function create_invoice_page() {
        ?>
        <div class="wrap">
            <h1>ایجاد فاکتور جدید</h1>
            
            <?php if (isset($_GET['message'])): ?>
                <div class="notice notice-<?php echo $_GET['type'] ?? 'success'; ?>">
                    <p><?php echo esc_html($_GET['message']); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <form method="post" action="">
                    <?php wp_nonce_field('wsi_create_invoice', 'wsi_nonce'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="user_id">مشتری</label></th>
                            <td>
                                <select name="user_id" id="user_id" style="width: 100%;" required>
                                    <option value="">انتخاب مشتری</option>
                                    <?php
                                    $users = get_users(array('role' => 'customer', 'number' => 100));
                                    foreach ($users as $user) {
                                        echo '<option value="' . $user->ID . '">' . 
                                             $user->display_name . ' (' . $user->user_email . ')' . 
                                             '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label for="product_id">محصول اشتراکی</label></th>
                            <td>
                                <select name="product_id" id="product_id" style="width: 100%;" required>
                                    <option value="">انتخاب محصول اشتراکی</option>
                                    <?php
                                    $products = wc_get_products(array(
                                        'type' => 'subscription',
                                        'limit' => -1,
                                        'status' => 'publish'
                                    ));
                                    
                                    foreach ($products as $product) {
                                        echo '<option value="' . $product->get_id() . '" data-price="' . $product->get_price() . '">' . 
                                             $product->get_name() . ' - ' . wc_price($product->get_price()) . 
                                             '</option>';
                                    }
                                    ?>
                                </select>
                                <p class="description">مبلغ به صورت خودکار از محصول گرفته می‌شود</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row"><label>مبلغ</label></th>
                            <td>
                                <div id="display_amount" style="padding: 8px 0; font-weight: bold; color: #0073aa;">
                                    لطفاً ابتدا محصول را انتخاب کنید
                                </div>
                                <input type="hidden" name="amount" id="amount" value="">
                            </td>
                        </tr>
                    </table>
                    
                    <?php submit_button('ایجاد فاکتور', 'primary', 'create_invoice'); ?>
                </form>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // نمایش مبلغ وقتی محصول انتخاب شد
            $('#product_id').on('change', function() {
                var price = $(this).find(':selected').data('price');
                if (price) {
                    $('#display_amount').html('مبلغ: ' + price.toLocaleString() + ' ریال');
                    $('#amount').val(price);
                } else {
                    $('#display_amount').html('لطفاً ابتدا محصول را انتخاب کنید');
                    $('#amount').val('');
                }
            });
        });
        </script>
        <?php
    }
    
    public static function handle_invoice_creation() {
        if (!isset($_POST['create_invoice']) || !isset($_POST['wsi_nonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['wsi_nonce'], 'wsi_create_invoice')) {
            wp_die('خطای امنیتی!');
        }
        
        if (!current_user_can('manage_woocommerce')) {
            wp_die('دسترسی غیرمجاز!');
        }
        
        $user_id = intval($_POST['user_id']);
        $product_id = intval($_POST['product_id']);
        $amount = floatval($_POST['amount']);
        
        // بررسی اینکه مبلغ با محصول مطابقت دارد
        $product = wc_get_product($product_id);
        if (!$product || $product->get_price() != $amount) {
            wp_die('مبلغ با محصول انتخاب شده مطابقت ندارد!');
        }
        
        // ایجاد فاکتور
        $invoice_id = self::create_invoice(array(
            'user_id' => $user_id,
            'product_id' => $product_id,
            'amount' => $amount
        ));
        
        if ($invoice_id && !is_wp_error($invoice_id)) {
            $redirect_url = add_query_arg(array(
                'message' => urlencode('فاکتور با موفقیت ایجاد شد.'),
                'type' => 'success'
            ), admin_url('edit.php?post_type=wsi_invoice&page=wsi-create-invoice'));
        } else {
            $redirect_url = add_query_arg(array(
                'message' => urlencode('خطا در ایجاد فاکتور!'),
                'type' => 'error'
            ), admin_url('edit.php?post_type=wsi_invoice&page=wsi-create-invoice'));
        }
        
        wp_redirect($redirect_url);
        exit;
    }
    
    public static function create_invoice($data) {
        $user = get_userdata($data['user_id']);
        $product = wc_get_product($data['product_id']);
        
        if (!$user || !$product) {
            return new WP_Error('invalid_data', 'کاربر یا محصول معتبر نیست.');
        }
        
        // تاریخ‌ها
        $start_date = current_time('mysql');
        $end_date = date('Y-m-d H:i:s', strtotime('+1 year'));
        
        $invoice_id = wp_insert_post(array(
            'post_type' => 'wsi_invoice',
            'post_title' => 'فاکتور برای ' . $user->display_name . ' - ' . $product->get_name(),
            'post_status' => 'publish',
            'post_author' => $data['user_id']
        ));
        
        if (is_wp_error($invoice_id)) {
            return $invoice_id;
        }
        
        // ذخیره متا داده‌ها
        update_post_meta($invoice_id, '_user_id', $data['user_id']);
        update_post_meta($invoice_id, '_subscription_product_id', $data['product_id']);
        update_post_meta($invoice_id, '_amount', $data['amount']);
        update_post_meta($invoice_id, '_status', 'pending');
        update_post_meta($invoice_id, '_due_date', $end_date);
        update_post_meta($invoice_id, '_invoice_type', 'manual');
        update_post_meta($invoice_id, '_created_date', $start_date);
        
        return $invoice_id;
    }
}

WSI_Manual_Invoices::init();
?>