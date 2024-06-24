<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
function yourpropfirm_program_id_post_meta_on_order_creation($order_id) {
    $order = wc_get_order($order_id);
    $items = $order->get_items();
    
    foreach ($items as $item_id => $item) {
        $product_id = $item->get_product_id();
        $program_id = get_post_meta($product_id, '_yourpropfirm_program_id', true);
        
        if (!empty($program_id)) {
            wc_update_order_item_meta($item_id, '_yourpropfirm_program_id', $program_id);
        } else {
            wc_update_order_item_meta($item_id, '_yourpropfirm_program_id', 'programId is not loaded');
        }
    }
}
add_action('woocommerce_new_order', 'yourpropfirm_program_id_post_meta_on_order_creation');

function yourpropfirm_mt_version_post_meta_on_order_creation($order_id) {
    $default_mt = get_option('yourpropfirm_connection_default_mt_version_field');
    $mt_version = isset($_POST['yourpropfirm_mt_version']) ? $_POST['yourpropfirm_mt_version'] : '';
    if (!empty($mt_version)){
        $mt_version_value = $mt_version;
    }
    else{
        if (!empty($default_mt)){
            $mt_version_value = $default_mt;
        }
        else{
            $mt_version_value = 'CTrader';
        }
    }
    update_post_meta($order_id, '_yourpropfirm_mt_version', $mt_version_value);
}

function yourpropfirm_additional_post_meta_on_order_creation($order_id) {
    $ypf_connection_completed = 0;
    update_post_meta($order_id, '_yourpropfirm_connection_completed', $ypf_connection_completed);
}

function yourpropfirm_display_order_meta_after_billing_admin_order($order) {
    $order_id = $order->get_id();
    $yourpropfirm_programId = get_post_meta($order_id, '_yourpropfirm_program_id', true);
    $yourpropfirm_meta_version = get_post_meta($order_id, '_yourpropfirm_mt_version', true);
    $yourpropfirm_connection_completed = get_post_meta($order_id, '_yourpropfirm_connection_completed', true);
    echo '<h3>' . __('YourPropFirm Program Details') . '</h3>';
    
    echo '<p><strong>' . __('YourPropFirm programID') . ':</strong> ' . esc_html($yourpropfirm_programId) . '</p>';
    echo '<p><strong>' . __('YourPropFirm MetaVersion') . ':</strong> ' . esc_html($yourpropfirm_meta_version) . '</p>';
    echo '<p><strong>' . __('YourPropFirm Completed') . ':</strong> ' . esc_html($yourpropfirm_connection_completed) . '</p>';
    echo '</div>';

}

add_action('woocommerce_new_order', 'yourpropfirm_program_id_post_meta_on_order_creation');
add_action('woocommerce_new_order', 'yourpropfirm_mt_version_post_meta_on_order_creation');
add_action('woocommerce_new_order', 'yourpropfirm_additional_post_meta_on_order_creation');
add_action('woocommerce_admin_order_data_after_billing_address', 'yourpropfirm_display_order_meta_after_billing_admin_order', 10, 1);