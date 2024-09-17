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
add_action('admin_init', 'yourpropfirm_connection_challenge_settings_fields');

// Register and define the settings
function yourpropfirm_connection_challenge_settings_fields() { 
    register_setting('yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_trading_platforms',  array('sanitize_callback' => 'yourpropfirm_connection_sanitize_checkbox_array', 'default' => array()));
    register_setting('yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_default_mt_version_field', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'MT4'));
    register_setting('yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_mt_version_field', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'disable'));
    register_setting('yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_request_method', array('sanitize_callback' => 'sanitize_text_field', 'default' => 'wp_remote_post'));
    register_setting('yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_request_delay', array('sanitize_callback' => 'sanitize_text_field', 'default' => '2'));
    register_setting('yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_enable_addon', array('sanitize_callback' => 'sanitize_text_field', 'default' => '0'));
    register_setting('yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_enable_response_header', array('sanitize_callback' => 'sanitize_text_field', 'default' => '1'));

    add_settings_section('yourpropfirm_connection_challenge', 'General Settings', 'yourpropfirm_connection_challenge_section_callback', 'yourpropfirm_connection_challenge_settings');

   
    add_settings_field('yourpropfirm_connection_trading_platforms', 'Enable Trading Platforms', 'yourpropfirm_connection_trading_platforms_callback', 'yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_challenge');
    add_settings_field('yourpropfirm_connection_default_mt_version_field', 'Select Default MT Version Field', 'yourpropfirm_connection_default_mt_version_field_callback', 'yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_challenge');  
    add_settings_field('yourpropfirm_connection_mt_version_field', 'Enable MT Version Field (On Checkout Page)', 'yourpropfirm_connection_mt_version_field_callback', 'yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_challenge');
    add_settings_field('yourpropfirm_connection_request_method', 'Request Method', 'yourpropfirm_connection_request_method_callback', 'yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_challenge');
    add_settings_field('yourpropfirm_connection_request_delay', 'Delay Request (for multiple product)', 'yourpropfirm_connection_request_delay_callback', 'yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_challenge');
    add_settings_field('yourpropfirm_connection_enable_addon', 'Enable YPF Addons', 'yourpropfirm_connection_enable_addon_callback', 'yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_challenge');
    add_settings_field('yourpropfirm_connection_enable_response_header', 'Save Log Response', 'yourpropfirm_connection_enable_response_header_callback', 'yourpropfirm_connection_challenge_settings', 'yourpropfirm_connection_challenge');
}


// Render section callback
function yourpropfirm_connection_challenge_section_callback() {
    echo '<p>Configure The Challenge Settings for YourPropFirm plugin.</p>';
}

// Render enable Custom Platform
function yourpropfirm_connection_trading_platforms_callback() {
    $options = get_option('yourpropfirm_connection_trading_platforms'); 
    $platforms = array(
        'enable_mt4' => 'MT4',
        'enable_mt5' => 'MT5',
        'enable_ctrader' => 'cTrader',
        'enable_sirix' => 'Sirix',
        'enable_dx_trade' => 'DX Trade',
        'enable_match_trader' => 'Match Trader',
        'enable_tradelocker' => 'TradeLocker'
    );

    foreach ($platforms as $key => $label) {
        $checked = isset($options[$key]) ? 'checked' : '';
        echo '<label><input type="checkbox" name="yourpropfirm_connection_trading_platforms[' . esc_attr($key) . ']" value="1" ' . esc_attr($checked) . '> ' . esc_html($label) . '</label><br>';
    }
}

// Render default MT version field
function yourpropfirm_connection_default_mt_version_field_callback() {
    $option = get_option('yourpropfirm_connection_default_mt_version_field');
    echo '<select name="yourpropfirm_connection_default_mt_version_field">';
    echo '<option value="MT4"' . selected($option, 'MT4', false) . '>MT4 Version</option>';
    echo '<option value="MT5"' . selected($option, 'MT5', false) . '>MT5 Version</option>';
    echo '<option value="CTrader"' . selected($option, 'CTrader', false) . '>CTrader</option>';
    echo '<option value="Sirix"' . selected($option, 'Sirix', false) . '>Sirix</option>';
    echo '<option value="DXTrade"' . selected($option, 'DXTrade', false) . '>DX Trade</option>';
    echo '<option value="matchTrader"' . selected($option, 'matchTrader', false) . '>Match Trader</option>';
    echo '<option value="tradeLocker"' . selected($option, 'tradeLocker', false) . '>TradeLocker</option>';
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

// Render environment field
function yourpropfirm_connection_enable_addon_callback() {
    $option = get_option('yourpropfirm_connection_enable_addon');
    echo '<label><input type="radio" name="yourpropfirm_connection_enable_addon" value="1"' . checked($option, '1', false) . '>Yes </label>';
    echo '<label><input type="radio" name="yourpropfirm_connection_enable_addon" value="0"' . checked($option, '0', false) . '>No </label>';
}

// Render enable response header field
function yourpropfirm_connection_enable_response_header_callback() {
    $option = get_option('yourpropfirm_connection_enable_response_header');
    echo '<label><input type="radio" name="yourpropfirm_connection_enable_response_header" value="1"' . checked($option, 1, false) . '> Yes</label>';
    echo '<label><input type="radio" name="yourpropfirm_connection_enable_response_header" value="0"' . checked($option, 0, false) . '> No</label>';
}
function yourpropfirm_connection_sanitize_checkbox_array($input) {
    $output = array();
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $output[$key] = sanitize_text_field($value);
        }
    }
    return $output;
}
?>
