<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
function yourpropfirm_display_custom_field_after_billing_form() {
    $plugin_enabled = get_option('yourpropfirm_connection_enabled');
    $enable_mtversion_field = get_option('yourpropfirm_connection_mt_version_field');
    $default_mt = get_option('yourpropfirm_connection_default_mt_version_field');
    $trading_platforms_options = get_option('yourpropfirm_connection_trading_platforms');

    if ($plugin_enabled !== 'enable' || $enable_mtversion_field !== 'enable') {
        return;
    }


    if (!empty($trading_platforms_options['enable_mt4'])) {
        $options['MT4'] = __('MT4', 'yourpropfirm');
    }
    if (!empty($trading_platforms_options['enable_mt5'])) {
        $options['MT5'] = __('MT5', 'yourpropfirm');
    }

    if (!empty($trading_platforms_options['enable_ctrader'])) {
        $options['CTrader'] = __('cTrader', 'yourpropfirm');
    }
    if (!empty($trading_platforms_options['enable_sirix'])) {
        $options['Sirix'] = __('Sirix', 'yourpropfirm');
    }
    if (!empty($trading_platforms_options['enable_dx_trade'])) {
        $options['DXTrade'] = __('DX Trade', 'yourpropfirm');
    }
    if (!empty($trading_platforms_options['enable_match_trader'])) {
        $options['MatchTrade'] = __('MatchTrade', 'yourpropfirm');
    }
    if (!empty($trading_platforms_options['enable_tradelocker'])) {
        $options['tradeLocker'] = __('TradeLocker', 'yourpropfirm');
    }

    ?>
    <div class="yourpropfirm_mt_version yourpropfirm_mt_version_field_wrapper">
        <?php
        woocommerce_form_field('yourpropfirm_mt_version', array(
            'type' => 'select',
            'class' => array('form-row-wide ypf_mt_version_field'),
            'label' => __('Trading Platforms', 'yourpropfirm'),
            'required' => true,
            'options' => $options // Use the conditional options here
        ), '');
        ?>
    </div>
    <?php
}

function yourpropfirm_mt_version_validate_field() {
    if (isset($_POST['yourpropfirm_mt_version']) && empty($_POST['yourpropfirm_mt_version'])) {
        wc_add_notice(__('Please select a Trading Platforms.', 'yourpropfirm'), 'error');
    }
}

// Additional Settings fo Woocommerce
add_filter('woocommerce_checkout_registration_enabled', '__return_false');
add_filter('woocommerce_checkout_login_enabled', '__return_false');
add_filter('option_woocommerce_enable_signup_and_login_from_checkout', '__return_false');
add_filter('pre_option_woocommerce_enable_signup_and_login_from_checkout', '__return_false');

add_action('woocommerce_after_checkout_billing_form', 'yourpropfirm_display_custom_field_after_billing_form');
add_action('woocommerce_checkout_process', 'yourpropfirm_mt_version_validate_field');