<?php
class WSI_Post_Types {
    
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_meta_fields'));
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post_wsi_invoice', array(__CLASS__, 'save_meta_boxes'));
    }
    
    public static function register_post_types() {
        register_post_type('wsi_invoice', array(
            'labels' => array(
                'name' => 'فاکتورها',
                'singular_name' => 'فاکتور',
                'menu_name' => 'فاکتورها',
                'add_new' => 'افزودن فاکتور',
                'add_new_item' => 'افزودن فاکتور جدید',
                'edit_item' => 'ویرایش فاکتور',
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'supports' => array('title'),
            'capability_type' => 'post',
        ));
    }
    
    public static function register_meta_fields() {
        $meta_fields = array(
            '_user_id',
            '_subscription_product_id',
            '_amount',
            '_status', 
            '_due_date',
            '_invoice_type',
            '_paid_date'
        );
        
        foreach ($meta_fields as $field) {
            register_post_meta('wsi_invoice', $field, array(
                'type' => 'string',
                'single' => true,
                'show_in_rest' => true,
            ));
        }
    }
    
    public static function add_meta_boxes() {
        add_meta_box(
            'wsi_invoice_details',
            'جزییات فاکتور',
            array(__CLASS__, 'invoice_meta_box_callback'),
            'wsi_invoice',
            'normal',
            'high'
        );
    }
    
    public static function invoice_meta_box_callback($post) {
        wp_nonce_field('wsi_save_invoice_meta', 'wsi_invoice_nonce');
        
        $user_id = get_post_meta($post->ID, '_user_id', true);
        $product_id = get_post_meta($post->ID, '_subscription_product_id', true);
        $amount = get_post_meta($post->ID, '_amount', true);
        $status = get_post_meta($post->ID, '_status', true);
        $due_date = get_post_meta($post->ID, '_due_date', true);
        ?>
        <div class="wsi-metabox">
            <div class="wsi-field">
                <label for="wsi_user_id"><strong>مشتری</strong></label>
                <select name="wsi_user_id" id="wsi_user_id" style="width: 100%;">
                    <option value="">انتخاب مشتری</option>
                    <?php
                    $users = get_users(array('role' => 'customer', 'number' => 100));
                    foreach ($users as $user) {
                        $selected = $user_id == $user->ID ? 'selected' : '';
                        echo '<option value="' . $user->ID . '" ' . $selected . '>' . 
                             $user->display_name . ' (' . $user->user_email . ')' . 
                             '</option>';
                    }
                    ?>
                </select>
            </div>
            
            <div class="wsi-field">
                <label for="wsi_status"><strong>وضعیت</strong></label>
                <select name="wsi_status" id="wsi_status" style="width: 100%;">
                    <option value="pending" <?php selected($status, 'pending'); ?>>در انتظار پرداخت</option>
                    <option value="paid" <?php selected($status, 'paid'); ?>>پرداخت شده</option>
                </select>
            </div>
        </div>
        <?php
    }
    
    public static function save_meta_boxes($post_id) {
        if (!isset($_POST['wsi_invoice_nonce']) || !wp_verify_nonce($_POST['wsi_invoice_nonce'], 'wsi_save_invoice_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        
        if (isset($_POST['wsi_user_id'])) {
            update_post_meta($post_id, '_user_id', sanitize_text_field($_POST['wsi_user_id']));
        }
        
        if (isset($_POST['wsi_status'])) {
            $new_status = sanitize_text_field($_POST['wsi_status']);
            $old_status = get_post_meta($post_id, '_status', true);
            
            update_post_meta($post_id, '_status', $new_status);
            
            if ($new_status === 'paid' && $old_status !== 'paid') {
                update_post_meta($post_id, '_paid_date', current_time('mysql'));
            }
        }
    }
}

WSI_Post_Types::init();
?>