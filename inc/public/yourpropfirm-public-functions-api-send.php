<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
// Create user via API when successful payment
function yourpropfirm_send_api_on_order_status_change($order_id, $old_status, $new_status, $order) {
    // Get the order object
    $order = wc_get_order($order_id);  
    $log_data = yourpropfirm_connection_response_logger();
    $log_data['logger']->info('YPF Connection Begin Info : ',  $log_data['context']);

    // Retrieve endpoint URL and API Key from plugin settings
    $request_method = get_option('yourpropfirm_connection_request_method');
    $request_delay = get_option('yourpropfirm_connection_request_delay'); 
    $ypf_connection_completed = get_post_meta($order->get_id(), '_yourpropfirm_connection_completed', true);   

    $plugin_enabled = get_option('yourpropfirm_connection_enabled');
    if ($plugin_enabled !== 'enable') {
        return;
    }

    // Check the selected environment
    $environment = get_option('yourpropfirm_connection_environment');
    if ($environment === 'sandbox') {
        // Perform actions for Sandbox Environment
        $endpoint_url = esc_attr(get_option('yourpropfirm_connection_sandbox_endpoint_url'));
        $api_key = esc_attr(get_option('yourpropfirm_connection_sandbox_test_key'));
    } else {
        // Perform actions for Live Environment
        $endpoint_url = esc_attr(get_option('yourpropfirm_connection_endpoint_url'));
        $api_key = esc_attr(get_option('yourpropfirm_connection_api_key'));
    }

    // Check if endpoint URL and API Key are provided
    if (empty($endpoint_url) || empty($api_key)) {
        return;
    }

    if ($new_status == 'completed' && $old_status != 'completed' && $ypf_connection_completed != 1) {
        // Check for transient to prevent duplicate API calls
        if (false === get_transient('send_api_lock_' . $order_id)) {
            // Set transient to prevent duplicate API calls within 10 seconds
            set_transient('send_api_lock_' . $order_id, true, 3);
            $enable_response_header = get_option('yourpropfirm_connection_enable_response_header');
            $default_mt = get_option('yourpropfirm_connection_default_mt_version_field');
            $mt_version_value = get_post_meta($order->get_id(), '_yourpropfirm_mt_version', true) ?: $default_mt;
            $products_loop_id = 1;
            
            // First product and quantity handling
            foreach ($order->get_items() as $item_id => $item) {
                $product = $item->get_product();
                $quantity = $item->get_quantity();            
                $program_id = get_post_meta($product->get_id(), '_yourpropfirm_order_program_id', true);
                $product_woo_id = $product->get_id();

                if (!empty($program_id) && !$first_product) {
                    // If first product, send initial request to create user
                    $first_product = true;
                    $api_data = yourpropfirm_get_api_dataa($order, $order_id, $product_woo_id, $program_id, $mt_version_value);
                    $response = yourpropfirm_send_wp_remote_post_request($endpoint_url, $api_key, $api_data, $request_delay);
                    $http_status = $response['http_status'];
                    $api_response = $response['api_response'];
                    $quantity_first_product = 1;
                                    
                    $user_data = json_decode($response['api_response'], true);
                    $user_id = isset($user_data['id']) ? $user_data['id'] : null;

                    yourpropfirm_handle_api_response_error($order, $http_status, $api_response, $order_id, $program_id, $products_loop_id, $mt_version_value, $product_woo_id, $quantity_first_product, $user_id);

                    // Loop through the quantity of the first product
                    if ($user_id && $quantity > 1) {
                        for ($i = 1; $i < $quantity; $i++) {
                            $quantity_first_product_qty = $i+1;
                            yourpropfirm_send_account_request($endpoint_url, $user_id, $api_key, $program_id, $mt_version_value, $request_delay, $order, $order_id, $products_loop_id, $product_woo_id, $quantity_first_product_qty, $user_id);
                        }
                    }
                } elseif (!empty($program_id) && $first_product && $user_id) {
                    // For subsequent products, loop through each quantity
                    for ($i = 0; $i < $quantity; $i++) {
                        $quantity_other_product_qty = $i+1;
                        yourpropfirm_send_account_request($endpoint_url, $user_id, $api_key, $program_id, $mt_version_value, $request_delay, $order, $order_id, $products_loop_id, $product_woo_id, $quantity_other_product_qty, $user_id);
                    }
                }
            $products_loop_id++;
            }            
            $ypf_connection_completed = 1; 
            update_post_meta($order_id, '_yourpropfirm_order_program_id_completed', $program_id);               
            update_post_meta($order_id, '_yourpropfirm_connection_completed', $ypf_connection_completed);

            // Clear the transient after processing
            delete_transient('send_api_lock_' . $order_id);
        }
    }
}

add_action('woocommerce_order_status_changed', 'yourpropfirm_send_api_on_order_status_change', 10, 4);