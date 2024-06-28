<?php
/**
 * Plugin functions and definitions for Helper.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
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