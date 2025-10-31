<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// حذف فاکتورها
$invoices = get_posts(array(
    'post_type' => 'wsi_invoice',
    'numberposts' => -1,
    'post_status' => 'any',
    'fields' => 'ids'
));

foreach ($invoices as $invoice_id) {
    wp_delete_post($invoice_id, true);
}

// حذف rewrite rules
flush_rewrite_rules();
?>