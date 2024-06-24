<?php
/**
 * Plugin functions and definitions for Helper.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */

function yourpropfirm_send_wp_remote_post_request($endpoint_url, $api_key, $api_data) {
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

    return array(
        'http_status' => $http_status,
        'api_response' => $api_response
    );
}


function yourpropfirm_connection_response_logger() {
    $logger = wc_get_logger();
    $context = array('source' => 'yourpropfirm_connection_response_log');
    return array('logger' => $logger, 'context' => $context);
}

// Hook for adding admin scripts
add_action('admin_enqueue_scripts', 'yourpropfirm_enqueue_admin_assets');
// Function to enqueue admin scripts and styles
function yourpropfirm_enqueue_admin_assets() {
    // Enqueue CSS file
    wp_enqueue_style('yourpropfirm-admin-css', plugin_dir_url(__FILE__) . '../../assets/css/yourpropfirm-admin.css');
    // Enqueue JS file
    wp_enqueue_script('yourpropfirm-admin-js', plugin_dir_url(__FILE__) . '../../assets/js/yourpropfirm-admin.js', array('jquery'), null, true);
}