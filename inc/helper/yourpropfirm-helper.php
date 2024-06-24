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