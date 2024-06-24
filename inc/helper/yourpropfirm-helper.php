<?php
/**
 * Plugin functions and definitions for Helper.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
// Function to handle sending account creation request
function send_account_request($endpoint_url, $user_id, $api_key, $program_id, $mt_version, $delay, $order, $order_id, $products_loop_id, $product_woo_id, $quantity, $metaKeyCurrency) {
    $invoicesId = $order_id;
    $productsId = $product_woo_id;
    $invoicesIdStr = strval($invoicesId);
    $productsIdStr = strval($productsId);
    
    $api_data_account = array(
        'mtVersion' => $mt_version,
        'programId' => $program_id,
        'InvoiceId' => $invoicesIdStr,
        'ProductId' => $productsIdStr
    );
    $endpoint_url_full = $endpoint_url . '/' . $user_id . '/accounts';
    $response = ypf_your_propfirm_plugin_send_wp_remote_post_request($endpoint_url_full, $api_key, $api_data_account, $delay);
    
    $http_status = $response['http_status'];
    $api_response = $response['api_response'];

    handle_api_response_error($order, $http_status, $api_response, $order_id, $program_id, $products_loop_id, $mt_version, $product_woo_id, $quantity, $user_id, $metaKeyCurrency);
}


function ypf_your_propfirm_plugin_send_wp_remote_post_request($endpoint_url, $api_key, $api_data, $request_delay=0) {
    $api_url = $endpoint_url;
    $headers = array(
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'X-Client-Key' => $api_key
    );

    $response = wp_remote_post(
        $api_url,
        array(
            'timeout' => 30,
            'redirection' => 5,
            'headers' => $headers,            
            'body' => json_encode($api_data)
        )
    );

    $http_status = wp_remote_retrieve_response_code($response);
    $api_response = wp_remote_retrieve_body($response);

    // Delay execution if $delay is greater than 0
    if ($request_delay > 0) {
        usleep($request_delay * 1000000); // Delay in micro seconds
    }

    return array(
        'http_status' => $http_status,
        'api_response' => $api_response
    );
}

function handle_api_response_error($order, $http_status, $api_response, $order_id, $program_id_value, $products_loop_id, $mt_version_value, $product_woo_id, $quantity, $user_id, $metaKeyCurrency = '') {
    global $woocommerce;
    // Check if WooCommerce is active and the function exists
    if (function_exists('get_woocommerce_currency')) {
        $defaultcurrency = get_woocommerce_currency();
    }

    // Ensure the $order object is a valid WC_Order object
    if (!($order instanceof WC_Order)) {
        $order = wc_get_order($order_id);
        if (!$order) return; // Exit if the order cannot be retrieved
    }

    // Get the currency from the order
    $order_currency_value = $order->get_currency();

    $multi_currency_enabled = get_option('fyfx_your_propfirm_plugin_enable_multi_currency');
    $defaultCurrency = get_option('fyfx_your_propfirm_plugin_default_multi_currency');
    $enable_response_header = get_option('fyfx_your_propfirm_plugin_enable_response_header');
    $log_data = ypf_connection_logger();

    if ($multi_currency_enabled === 'enable') { 
        if (!empty($metaKeyCurrency)) {
            $metaKeyCurrencyValue = $metaKeyCurrency;
        } else {
            $metaKeyCurrencyValue = $defaultcurrency;
        }
    } else{
        $metaKeyCurrencyValue = $defaultcurrency;
    }

    $error_message = 'An error occurred while creating the user. Error Type Unknown.';
    if ($http_status == 201) {
        $error_message = 'success created account.!!';
    } elseif ($http_status == 400) {
        $error_message = isset($api_response['error']) ? $api_response['error'] : 'An error occurred while creating the user. Error Type 400.';
    } elseif ($http_status == 409) {
        $error_message = isset($api_response['error']) ? $api_response['error'] : 'An error occurred while creating the user. Error Type 409.';
    } elseif ($http_status == 500) {
        $error_message = isset($api_response['error']) ? $api_response['error'] : 'An error occurred while creating the user. Error Type 500.';
    } else {
        $error_message = isset($api_response['error']) ? $api_response['error'] : 'An error occurred while creating the user. Error Response : Error Type Unknown.!!';
    }

    $api_response_note = $error_message ." Code : ".$http_status;
    $api_response_logs = $error_message ." Code : ".$http_status ." Message : ".$api_response ;
    
    //update_post_meta($order_id, 'api_response_ypf_product-'.$products_loop_id,$api_response_test);
    //update_post_meta($order_id, 'api_response_ypf_programId-'.$products_loop_id,$program_id_value);
    //update_post_meta($order_id, 'api_response_mt_version-'.$products_loop_id, $mt_version_value);

    // Combine all API responses into one note
    // $combined_note = "API Responses for Product " . $products_loop_id . ":\n";
    // $combined_note .= "Response: " . $api_response_test . "\n";
    // $combined_note .= "Program ID: " . $program_id_value . "\n";
    // $combined_note .= "MT Version: " . $mt_version_value . "\n";

    // Add the combined note
    // $order->add_order_note($combined_note);

    // Combine all API responses into one note
    $combined_notes = "--Begin YPF Response--\n";
    $combined_notes .= "Response Loop : " . $products_loop_id . "\n";
    $combined_notes .= "ProductID : " . $product_woo_id . "\n"; 
    $combined_notes .= "Quantity : " . $quantity . "\n";
    $combined_notes .= "Currency : " . $order_currency_value . "\n";
    $combined_notes .= "HTTP Response : " . $http_status . "\n";
    $combined_notes .= "YPF User ID : " . $user_id . "\n";       
    $combined_notes .= "ProgramID: " . $program_id_value . "\n";
    $combined_notes .= "MTVersion: " . $mt_version_value . "\n";
    $combined_notes .= "Response: " . $api_response_note . "\n";
    $combined_notes .= "--End Response--\n";

    // Combine all API responses For Log WC-Logger
    $combined_note_logs = "\n";
    $combined_note_logs .= "--Begin YPF Response--\n";
    $combined_note_logs .= "Response Loop : " . $products_loop_id . "\n";
    $combined_note_logs .= "OrderID : " . $order_id . "\n";  
    $combined_note_logs .= "ProductID : " . $product_woo_id . "\n"; 
    $combined_note_logs .= "Quantity : " . $quantity . "\n";
    $combined_note_logs .= "Currency : " . $order_currency_value . "\n";
    $combined_note_logs .= "HTTP Response : " . $http_status . "\n";
    $combined_note_logs .= "YPF User ID : " . $user_id . "\n";         
    $combined_note_logs .= "ProgramID: " . $program_id_value . "\n";
    $combined_note_logs .= "MTVersion: " . $mt_version_value . "\n";
    $combined_note_logs .= "Response: " . $api_response_logs . "\n";
    $combined_note_logs .= "--End Response--\n";

    // Add the combined note
    wc_create_order_note($order_id, $combined_notes, $added_by_user = false, $customer_note = false);

    // Using WooCommerce methods to store the API response in the order meta
    //$order->update_meta_data('api_response_ypf_product-'.$products_loop_id, $api_response_test);
    //$order->update_meta_data('api_response_ypf_programId-'.$products_loop_id, $program_id_value);
    //$order->update_meta_data('api_response_mt_version-'.$products_loop_id, $mt_version_value);
    $order->save(); // Don't forget to save the order to store these meta data

    if ($enable_response_header){
        $log_data['logger']->info($combined_note_logs,  $log_data['context']);
    }
}