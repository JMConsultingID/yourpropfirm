<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
function yourpropfirm_send_wp_remote_post_request($endpoint_url, $api_key, $api_data, $request_delay=0) {
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

function yourpropfirm_get_api_data($order, $order_id, $product_woo_id, $program_id_value, $mt_version_value) {
    $invoicesId = $order->get_id();
    $productsId = $product_woo_id;
    $invoicesIdStr = strval($invoicesId);
    $productsIdStr = strval($productsId);
    $user_email = $order->get_billing_email();
    $user_first_name = $order->get_billing_first_name();
    $user_last_name = $order->get_billing_last_name();
    $user_address = $order->get_billing_address_1();
    $user_city = $order->get_billing_city();
    $user_zip_code = $order->get_billing_postcode();
    $user_country = $order->get_billing_country();
    $user_phone = $order->get_billing_phone();

    return array(
        'email' => $user_email,
        'firstname' => $user_first_name,
        'lastname' => $user_last_name,
        'programId' => $program_id_value,             
        'mtVersion' => $mt_version_value,
        'addressLine' => $user_address,
        'city' => $user_city,
        'zipCode' => $user_zip_code,
        'country' => $user_country,
        'phone' => $user_phone,
        'InvoiceId' => $invoicesIdStr,
        'ProductId' => $productsIdStr
    );
}

function yourpropfirm_handle_api_response_error($order, $http_status, $api_response, $order_id, $program_id_value, $products_loop_id, $mt_version_value, $product_woo_id, $quantity, $user_id) {
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

    $enable_response_header = get_option('yourpropfirm_connection_response_logger');
    $log_data = yourpropfirm_connection_response_logger();

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
        $error_message = isset($api_response['error']) ? $api_response['error'] : 'An error occurred while creating the user. Error Response : Unknown Error Code.!!';
    }

    $api_response_note = $error_message ." Code : ".$http_status;
    $api_response_logs = $error_message ." Code : ".$http_status ." Message : ".$api_response ;

    // Combine all API responses into one note
    $combined_notes = "--YourPropfirm--\n";
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
    $combined_note_logs .= "APIResponse: " . $api_response_logs . "\n";
    $combined_note_logs .= "--End Response--\n";

    // Add the combined note
    wc_create_order_note($order_id, $combined_notes, $added_by_user = false, $customer_note = false);
    $order->save(); // Don't forget to save the order to store these meta data

    if ($enable_response_header){
        $log_data['logger']->info($combined_note_logs,  $log_data['context']);
    }
}