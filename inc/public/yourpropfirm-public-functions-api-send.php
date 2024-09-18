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
        $baseUrl = esc_attr(get_option('yourpropfirm_connection_sandbox_endpoint_url'));
        $api_key = esc_attr(get_option('yourpropfirm_connection_sandbox_test_key'));
    } else {
        // Perform actions for Live Environment
        $baseUrl = esc_attr(get_option('yourpropfirm_connection_endpoint_url'));
        $api_key = esc_attr(get_option('yourpropfirm_connection_api_key'));
    }

    // Check if endpoint URL and API Key are provided
    if (empty($baseUrl) || empty($api_key)) {
        return;
    }

    if ($new_status == 'on-hold' && $old_status != 'on-hold' && $ypf_connection_completed != 1) {
        // Check for transient to prevent duplicate API calls
        if (false === get_transient('send_api_lock_' . $order_id)) {
            // Set transient to prevent duplicate API calls within 10 seconds
            set_transient('send_api_lock_' . $order_id, true, 3);
            $default_mt = get_option('yourpropfirm_connection_default_mt_version_field');
            $default_profitSplit = 0;
            $default_ActiveDays =  0;
            $default_TradingDays =  0;

            // Retrieve the mt_version_value, use default if not set or empty
            $mt_version_value = get_post_meta( $order_id, '_yourpropfirm_mt_version', true ) ?: $default_mt;

            $site_language = get_locale();
            $site_language_value = substr( $site_language, 0, 2 );

            $order_currency = $order->get_currency();
            $order_total_value = $order->get_total();
            $json_encoded_total = json_encode(array('order_total' => $order_total_value));
            $decoded_total = json_decode($json_encoded_total, true);
            $order_total = round((float)$decoded_total['order_total'], 2);
            
            // Retrieve the profitSplit, use default if not set or empty
            $profitSplit = $order->get_meta('profitSplit');
            $withdrawActiveDays = $order->get_meta('withdrawActiveDays');
            $withdrawTradingDays = $order->get_meta('withdrawTradingDays');

            $profitSplit = $profitSplit !== '' ? $profitSplit : $default_profitSplit;
            $withdrawActiveDays = $withdrawActiveDays !== '' ? $withdrawActiveDays : $default_ActiveDays;
            $withdrawTradingDays = $withdrawTradingDays !== '' ? $withdrawTradingDays : $default_TradingDays;


            $products_loop_id = 1;
            
            // First product and quantity handling
            foreach ($order->get_items() as $item_id => $item) {
                $product = $item->get_product();
                $quantity = $item->get_quantity();
                $yourpropfirm_selection_type = get_post_meta($product->get_id(), '_yourpropfirm_selection_type', true);         
                $program_id = get_post_meta($product->get_id(), '_yourpropfirm_program_id', true);
                $competition_id = get_post_meta($product->get_id(), '_yourpropfirm_competition_id', true);
                $product_woo_id = $product->get_id();

                if (empty($yourpropfirm_selection_type)) {
                    $yourpropfirm_selection_type = 'challenge';
                }

                if ($yourpropfirm_selection_type === 'challenge') {
                    $endpoint = "/client/v1/users";
                } elseif ($yourpropfirm_selection_type === 'competition' && !empty($competition_id)) {
                    $endpoint = "/client/v1/competitions/" . esc_attr($competition_id) . "/register";
                } else {
                    $endpoint = "/client/v1/users";
                }

                $endpoint_url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

                if (!empty($program_id) && !$first_product) {
                    // If first product, send initial request to create user
                    $first_product = true;
                    if ($yourpropfirm_selection_type === 'challenge') {
                        // Call the challenge API data function
                        $api_data = yourpropfirm_get_challenge_api_data($order, $order_id, $product_woo_id, $program_id, $mt_version_value, $site_language_value, $order_currency, $order_total, $profitSplit, $withdrawActiveDays, $withdrawTradingDays);
                    } elseif ($yourpropfirm_selection_type === 'competition') {
                        // Call the competition API data function
                        $api_data = yourpropfirm_get_competition_api_data($order, $order_id, $product_woo_id, $mt_version_value, $site_language_value, $order_currency, $order_total, $profitSplit, $withdrawActiveDays, $withdrawTradingDays);
                    } else {
                        // Call the challenge API data function
                        $api_data = yourpropfirm_get_challenge_api_data($order, $order_id, $product_woo_id, $program_id, $mt_version_value, $site_language_value, $order_currency, $order_total, $profitSplit, $withdrawActiveDays, $withdrawTradingDays);
                    }

                    $response = yourpropfirm_send_wp_remote_post_request($endpoint_url, $api_key, $api_data, $request_delay);
                    $http_status = $response['http_status'];
                    $api_response = $response['api_response'];
                    $quantity_first_product = 1;
                                    
                    $user_data = json_decode($response['api_response'], true);
                    $user_id = isset($user_data['id']) ? $user_data['id'] : null;

                    // Combine all API responses For Log WC-Logger
                    $combined_note_hit_logs = "\n";
                    $combined_note_hit_logs .= "--Log Send API YPF--\n";
                    $combined_note_hit_logs .= "YPF type: " . $yourpropfirm_selection_type . "\n";
                    if ($yourpropfirm_selection_type === 'competition') {
                         $combined_note_hit_logs .= "CompetitionID: " . $competition_id . "\n";
                    } 
                    $combined_note_hit_logs .= "Endpoint URL: " . $endpoint_url . "\n";
                    $combined_note_hit_logs .= "API Key: " . yourpropfirm_connection_mask_api_key($api_key) . "\n";
                    $combined_note_hit_logs .= "API Data: " . json_encode($api_data) . "\n";
                    $combined_note_hit_logs .= "--End Log--\n";
                    $log_data['logger']->info($combined_note_hit_logs,  $log_data['context']);

                    yourpropfirm_handle_api_response_error($order, $http_status, $api_response, $order_id, $yourpropfirm_selection_type, $program_id, $competition_id, $products_loop_id, $mt_version_value,  $site_language_value, $order_currency, $order_total, $product_woo_id, $quantity_first_product, $user_id, $profitSplit, $withdrawActiveDays, $withdrawTradingDays);

                    // Loop through the quantity of the first product
                    if ($user_id && $quantity > 1) {
                        for ($i = 1; $i < $quantity; $i++) {
                            $quantity_first_product_qty = $i+1;
                            yourpropfirm_send_account_request($endpoint_url, $user_id, $api_key, $program_id, $mt_version_value, $request_delay, $order, $order_id, $products_loop_id, $product_woo_id, $quantity_first_product_qty, $user_id, $profitSplit, $withdrawActiveDays, $withdrawTradingDays);
                        }
                    }                    
                } elseif (!empty($program_id) && $first_product && $user_id) {
                    // For subsequent products, loop through each quantity
                    for ($i = 0; $i < $quantity; $i++) {
                        $quantity_other_product_qty = $i+1;
                        yourpropfirm_send_account_request($endpoint_url, $user_id, $api_key, $program_id, $mt_version_value, $request_delay, $order, $order_id, $products_loop_id, $product_woo_id, $quantity_other_product_qty, $user_id, $profitSplit, $withdrawActiveDays, $withdrawTradingDays);
                    }
                }
            $products_loop_id++;
            }            
            $ypf_connection_completed = 1;           
            update_post_meta($order_id, '_yourpropfirm_connection_completed', $ypf_connection_completed);
            delete_transient('send_api_lock_' . $order_id);
        }
    }
}

add_action('woocommerce_order_status_changed', 'yourpropfirm_send_api_on_order_status_change', 10, 4);