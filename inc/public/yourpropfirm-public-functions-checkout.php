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
    if ($plugin_enabled !== 'enable') {
        return;
    }
    $checkout_form = get_option('yourpropfirm_connection_checkout_form');
    $mt_version_field = get_option('yourpropfirm_connection_mt_version_field');
    $default_mt = get_option('yourpropfirm_connection_default_mt_version_field');
    $enable_mt_ctrader = get_option('yourpropfirm_connection_enable_mt_ctrader');

    // Initialize options array
    // Initialize options array with a default 'select' option
    $options = ['' => __('Select Meta Trader Version', 'yourpropfirm')]; // Default prompt option


    // Determine options based on enable_mt_ctrader setting
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
                'options' => $checkout_form // Use the conditional options here
            ), '');
            ?>
        </div>
    }
}
add_action('woocommerce_after_checkout_billing_form', 'yourpropfirm_display_custom_field_after_billing_form');

add_action('woocommerce_checkout_process', 'yourpropfirm_validate_mt_version_field');
function yourpropfirm_validate_mt_version_field() {
    if (isset($_POST['yourpropfirm_mt_version']) && empty($_POST['yourpropfirm_mt_version'])) {
        wc_add_notice(__('Please select a MetaTrader version.', 'yourpropfirm'), 'error');
    }
}