<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
// Hook to display program ID on the checkout page after billing address
function yourpropfirm_show_program_id_at_checkout() {
    if (is_checkout()) {
        global $woocommerce;
        $items = $woocommerce->cart->get_cart();
        foreach ($items as $item => $values) {
            $product_id = $values['product_id'];
            $program_id = get_post_meta($product_id, '_yourpropfirm_program_id', true);
            if (!empty($program_id)) {
                echo '<div class="yourpropfirm-program-id">';
                echo '<strong>' . __('YourPropFirm Program ID') . ':</strong> ' . esc_html($program_id);
                echo '</div>';
            }
        }
    }
}
add_action('woocommerce_after_checkout_billing_form', 'yourpropfirm_show_program_id_at_checkout');

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

function yourpropfirm_mt_version_validate_field() {
    if (isset($_POST['yourpropfirm_mt_version']) && empty($_POST['yourpropfirm_mt_version'])) {
        wc_add_notice(__('Please select a MetaTrader version.', 'yourpropfirm'), 'error');
    }
}

// Additional Settings fo Woocommerce
add_filter('woocommerce_checkout_registration_enabled', '__return_false');
add_filter('woocommerce_checkout_login_enabled', '__return_false');
add_filter('option_woocommerce_enable_signup_and_login_from_checkout', '__return_false');
add_filter('pre_option_woocommerce_enable_signup_and_login_from_checkout', '__return_false');

add_action('woocommerce_after_checkout_billing_form', 'yourpropfirm_display_custom_field_after_billing_form');
add_action('woocommerce_checkout_process', 'yourpropfirm_mt_version_validate_field');