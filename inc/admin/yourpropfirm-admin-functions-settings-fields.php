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
add_action('admin_init', 'yourpropfirm_connection_general_settings_fields');

// Register and define the settings
function yourpropfirm_connection_general_settings_fields() {
	register_setting('yourpropfirm_connection_general_settings', 'yourpropfirm_connection_enabled', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'disable'));
    register_setting('yourpropfirm_connection_general_settings', 'yourpropfirm_connection_environment', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'sandbox'));
    register_setting('yourpropfirm_connection_general_settings', 'yourpropfirm_connection_sandbox_endpoint_url');
    register_setting('yourpropfirm_connection_general_settings', 'yourpropfirm_connection_sandbox_test_key');
    register_setting('yourpropfirm_connection_general_settings', 'yourpropfirm_connection_endpoint_url');
    register_setting('yourpropfirm_connection_general_settings', 'yourpropfirm_connection_api_key');

    add_settings_section('yourpropfirm_connection_general', 'General Settings', 'yourpropfirm_connection_general_section_callback', 'yourpropfirm_connection_general_settings');

    add_settings_field('yourpropfirm_connection_enabled', 'Enable Plugin', 'yourpropfirm_connection_enabled_callback', 'yourpropfirm_connection_general_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_environment', 'Environment', 'yourpropfirm_connection_environment_callback', 'yourpropfirm_connection_general_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_sandbox_endpoint_url', 'Sandbox Endpoint URL', 'yourpropfirm_connection_sandbox_endpoint_url_callback', 'yourpropfirm_connection_general_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_sandbox_test_key', 'Sandbox Test Key', 'yourpropfirm_connection_sandbox_test_key_callback', 'yourpropfirm_connection_general_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_endpoint_url', 'Live Endpoint URL', 'yourpropfirm_connection_endpoint_url_callback', 'yourpropfirm_connection_general_settings', 'yourpropfirm_connection_general');
    add_settings_field('yourpropfirm_connection_api_key', 'Live API Key', 'yourpropfirm_connection_api_key_callback', 'yourpropfirm_connection_general_settings', 'yourpropfirm_connection_general');
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