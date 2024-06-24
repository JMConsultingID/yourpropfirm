<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
// Additional Settings fo Woocommerce
add_filter('woocommerce_checkout_registration_enabled', '__return_false');
add_filter('woocommerce_checkout_login_enabled', '__return_false');
add_filter('option_woocommerce_enable_signup_and_login_from_checkout', '__return_false');
add_filter('pre_option_woocommerce_enable_signup_and_login_from_checkout', '__return_false');

function yourpropfirm_display_custom_field_after_billing_form() {
    $plugin_enabled = get_option('yourpropfirm_connection_enabled');
    $enable_mtversion_field = get_option('yourpropfirm_connection_mt_version_field');
    $default_mt = get_option('yourpropfirm_connection_default_mt_version_field');
    $enable_mt_ctrader = get_option('yourpropfirm_connection_enable_mt_ctrader');

    if ($plugin_enabled !== 'enable' || $enable_mtversion_field !== 'enable') {
        return;
    }

    $options = ['' => __('Select Meta Trader Version', 'yourpropfirm')]; // Default prompt option

    if ($enable_mt_ctrader === 'enable') {
        $options['CTrader'] = __('CTrader', 'yourpropfirm');
    } else {
        if ($default_mt === 'CTrader') {
            $options = array(
                '' => __('Select MetaTrader Version*', 'yourpropfirm'),
                'MT4' => __('MetaTrader Version 4', 'yourpropfirm'),
                'MT5' => __('MetaTrader Version 5', 'yourpropfirm'),
            );
        } elseif ($default_mt === 'MT5') {
            $options = array(
                '' => __('Select MetaTrader Version*', 'yourpropfirm'),
                'MT5' => __('MetaTrader Version 5', 'yourpropfirm'),
                'MT4' => __('MetaTrader Version 4', 'yourpropfirm'),
            );
        } elseif ($default_mt === 'MT4') {
            $options = array(
                '' => __('Select MetaTrader Version*', 'yourpropfirm'),
                'MT4' => __('MetaTrader Version 4', 'yourpropfirm'),
                'MT5' => __('MetaTrader Version 5', 'yourpropfirm'),
            );
        } else { // Default to MT4 if not MT5 or CTrader
            $options = array(
                '' => __('Select MetaTrader Version*', 'yourpropfirm'),         
                'MT4' => __('MetaTrader Version 4', 'yourpropfirm'),
                'MT5' => __('MetaTrader Version 5', 'yourpropfirm'),
            );
        }
    }

    ?>
    <div class="yourpropfirm_mt_version yourpropfirm_mt_version_field_wrapper">
        <?php
        woocommerce_form_field('yourpropfirm_mt_version', array(
            'type' => 'select',
            'class' => array('form-row-wide ypf_mt_version_field'),
            'label' => __('MetaTrader Version', 'yourpropfirm'),
            'required' => true,
            'options' => $options // Use the conditional options here
        ), '');
        ?>
    </div>
    <?php
}
add_action('woocommerce_after_checkout_billing_form', 'yourpropfirm_display_custom_field_after_billing_form');

add_action('woocommerce_checkout_process', 'yourpropfirm_mt_version_validate_field');
function yourpropfirm_mt_version_validate_field() {
    if (isset($_POST['yourpropfirm_mt_version']) && empty($_POST['yourpropfirm_mt_version'])) {
        wc_add_notice(__('Please select a MetaTrader version.', 'yourpropfirm'), 'error');
    }
}

function yourpropfirm_mt_version_update_post_meta_on_order_creation($order_id) {
    $default_mt = get_option('yourpropfirm_connection_default_mt_version_field');
    $mt_version = isset($_POST['yourpropfirm_mt_version']) ? $_POST['yourpropfirm_mt_version'] : '';
    if (!empty($mt_version)){
        $mt_version_value = $mt_version;
    }
    else{
        if (!empty($default_mt)){
            $mt_version_value = $default_mt;
        }
        else{
            $mt_version_value = 'CTrader';
        }
    }
    update_post_meta($order_id, '_yourpropfirm_mt_version', $mt_version_value);
}
add_action('woocommerce_new_order', 'yourpropfirm_mt_version_update_post_meta_on_order_creation');

function yourpropfirm_display_order_meta_in_admin_order($order) {
    $order_id = $order->get_id();
    $ypf_meta_version = get_post_meta($order_id, '_yourpropfirm_mt_version', true);
    echo '<h3>' . __('YourPropFirm Program Details') . '</h3>';
    echo '<p><strong>' . __('YourPropFirm MetaVersion') . ':</strong> ' . esc_html($ypf_meta_version) . '</p>';
    echo '</div>';

}
add_action('woocommerce_admin_order_data_after_billing_address', 'yourpropfirm_display_order_meta_in_admin_order', 10, 1);