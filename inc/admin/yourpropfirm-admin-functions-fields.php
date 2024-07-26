<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */

// Hook for adding admin settings
add_action('admin_init', 'yourpropfirm_connection_settings_fields');

// Register and define the settings
function yourpropfirm_connection_settings_fields() {
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_enabled', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'disable'));
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_environment', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'sandbox'));
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_sandbox_endpoint_url');
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_sandbox_test_key');
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_endpoint_url');
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_api_key');
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_enable_mt_ctrader', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'disable'));
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_default_mt_version_field', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'MT4'));
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_mt_version_field', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'disable'));
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_request_method', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'wp_remote_post'));
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_request_delay', array('sanitize_callback' => 'sanitize_text_field', 'default' => '2'));
    register_setting('yourpropfirm_connection_settings', 'yourpropfirm_connection_enable_response_header', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

    add_settings_section('yourpropfirm_connection_general', 'General Settings', 'yourpropfirm_connection_general_section_callback', 'yourpropfirm_connection_settings');

    add_settings_field('yourpropfirm_connection_enabled', 'Enable Plugin', 'yourpropfirm_connection_enabled_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_environment', 'Environment', 'yourpropfirm_connection_environment_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_sandbox_endpoint_url', 'Sandbox Endpoint URL', 'yourpropfirm_connection_sandbox_endpoint_url_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_sandbox_test_key', 'Sandbox Test Key', 'yourpropfirm_connection_sandbox_test_key_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_endpoint_url', 'Live Endpoint URL', 'yourpropfirm_connection_endpoint_url_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_api_key', 'Live API Key', 'yourpropfirm_connection_api_key_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_enable_mt_ctrader', 'Enable Ctrader', 'yourpropfirm_connection_enable_mt_ctrader_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_default_mt_version_field', 'Select Default MT Version Field', 'yourpropfirm_connection_default_mt_version_field_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_mt_version_field', 'Enable MT Version Field (On Checkout Page)', 'yourpropfirm_connection_mt_version_field_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_request_method', 'Request Method', 'yourpropfirm_connection_request_method_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_request_delay', 'Delay Request (for multiple product)', 'yourpropfirm_connection_request_delay_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_enable_response_header', 'Save Log Response', 'yourpropfirm_connection_enable_response_header_callback', 'yourpropfirm_connection_settings', 'yourpropfirm_connection_general');
}

// Render section callback
function yourpropfirm_connection_general_section_callback() {
    echo '<p>Configure the general settings for YourPropFirm plugin.</p>';
}

// Render enable plugin field
function yourpropfirm_connection_enabled_callback() {
    $option = get_option('yourpropfirm_connection_enabled');
    echo '<select name="yourpropfirm_connection_enabled">';
    echo '<option value="enable"' . selected($option, 'enable', false) . '>Enable</option>';
    echo '<option value="disable"' . selected($option, 'disable', false) . '>Disable</option>';
    echo '</select>';
}

// Render environment field
function yourpropfirm_connection_environment_callback() {
    $option = get_option('yourpropfirm_connection_environment');
    echo '<label><input type="radio" name="yourpropfirm_connection_environment" value="sandbox"' . checked($option, 'sandbox', false) . '> Sandbox Version</label>';
    echo '<label><input type="radio" name="yourpropfirm_connection_environment" value="live"' . checked($option, 'live', false) . '> Live Version</label>';
}

// Render sandbox endpoint URL field
function yourpropfirm_connection_sandbox_endpoint_url_callback() {
    $option = get_option('yourpropfirm_connection_sandbox_endpoint_url');
    echo '<input type="text" name="yourpropfirm_connection_sandbox_endpoint_url" value="' . esc_attr($option) . '" style="width: 400px;">';
}

// Render sandbox test key field
function yourpropfirm_connection_sandbox_test_key_callback() {
    $option = get_option('yourpropfirm_connection_sandbox_test_key');
    echo '<input type="text" name="yourpropfirm_connection_sandbox_test_key" value="' . esc_attr($option) . '" style="width: 400px;">';
}

// Render live endpoint URL field
function yourpropfirm_connection_endpoint_url_callback() {
    $option = get_option('yourpropfirm_connection_endpoint_url');
    echo '<input type="text" name="yourpropfirm_connection_endpoint_url" value="' . esc_attr($option) . '" style="width: 400px;">';
}

// Render live API key field
function yourpropfirm_connection_api_key_callback() {
    $option = get_option('yourpropfirm_connection_api_key');
    echo '<input type="text" name="yourpropfirm_connection_api_key" value="' . esc_attr($option) . '" style="width: 400px;">';
}

// Render enable Ctrader field
function yourpropfirm_connection_enable_mt_ctrader_callback() {
    $option = get_option('yourpropfirm_connection_enable_mt_ctrader');
    echo '<select name="yourpropfirm_connection_enable_mt_ctrader">';
    echo '<option value="enable"' . selected($option, 'enable', false) . '>Enable</option>';
    echo '<option value="disable"' . selected($option, 'disable', false) . '>Disable</option>';
    echo '</select>';
}

// Render default MT version field
function yourpropfirm_connection_default_mt_version_field_callback() {
    $option = get_option('yourpropfirm_connection_default_mt_version_field');
    echo '<select name="yourpropfirm_connection_default_mt_version_field">';
    echo '<option value="MT4"' . selected($option, 'MT4', false) . '>MT4 Version</option>';
    echo '<option value="MT5"' . selected($option, 'MT5', false) . '>MT5 Version</option>';
    echo '<option value="CTrader"' . selected($option, 'CTrader', false) . '>CTrader</option>';
    echo '</select>';
}

// Render MT version field
function yourpropfirm_connection_mt_version_field_callback() {
    $option = get_option('yourpropfirm_connection_mt_version_field');
    echo '<select name="yourpropfirm_connection_mt_version_field">';
    echo '<option value="enable"' . selected($option, 'enable', false) . '>Enable</option>';
    echo '<option value="disable"' . selected($option, 'disable', false) . '>Disable</option>';
    echo '</select>';
}

// Render request method field
function yourpropfirm_connection_request_method_callback() {
    $option = get_option('yourpropfirm_connection_request_method');
    echo '<select name="yourpropfirm_connection_request_method">';
    echo '<option value="wp_remote_post"' . selected($option, 'wp_remote_post', false) . '>WP REMOTE POST</option>';
    echo '</select>';
}

// Render request delay field
function yourpropfirm_connection_request_delay_callback() {
    $option = get_option('yourpropfirm_connection_request_delay');
    echo '<select name="yourpropfirm_connection_request_delay">';
    echo '<option value="0"' . selected($option, '0', false) . '>0</option>';
    for ($i = 1; $i <= 10; $i++) {
        echo '<option value="' . $i . '"' . selected($option, (string)$i, false) . '>' . $i . '</option>';
    }
    echo '</select>';
}

// Render enable response header field
function yourpropfirm_connection_enable_response_header_callback() {
    $option = get_option('yourpropfirm_connection_enable_response_header');
    echo '<label><input type="radio" name="yourpropfirm_connection_enable_response_header" value="1"' . checked($option, 1, false) . '> Yes</label>';
    echo '<label><input type="radio" name="yourpropfirm_connection_enable_response_header" value="0"' . checked($option, 0, false) . '> No</label>';
}
?>
