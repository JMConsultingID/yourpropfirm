<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
// Add a custom field to WooCommerce product
function yourpropfirm_add_program_id_field() {
    global $woocommerce, $post;

    // Get the product ID
    $product_id = $post->ID;

    // Display the custom field on the product edit page
    woocommerce_wp_text_input(
        array(
            'id'          => '_yourpropfirm_program_id',
            'label'       => __('ProgramId (YourPropfirm)', 'yourpropfirm'),
            'placeholder' => __('Enter ProgramId (YourPropfirm)', 'yourpropfirm'),
            'desc_tip'    => true,
            'description' => __('Enter ProgramId (YourPropfirm).', 'yourpropfirm'),
            'wrapper_class' => 'show_if_simple',
        )
    );
}
add_action('woocommerce_product_options_general_product_data', 'yourpropfirm_add_program_id_field', 9);

// Save the custom field value
function yourpropfirm_save_program_id_field($product_id) {
    $program_id = sanitize_text_field($_POST['_yourpropfirm_program_id']);
    update_post_meta($product_id, '_yourpropfirm_program_id', esc_attr($program_id));
}
add_action('woocommerce_process_product_meta', 'yourpropfirm_save_program_id_field');

function yourpropfirm_add_program_id_column_to_admin_products($columns) {
    $new_columns = array();

    foreach ($columns as $key => $name) {
        $new_columns[$key] = $name;

        if ('sku' === $key) {
            $new_columns['yourpropfirm_program_id'] = __('YRPF-ID', 'yourpropfirm');
        }
    }

    return $new_columns;
}
add_filter('manage_edit-product_columns', 'yourpropfirm_add_program_id_column_to_admin_products', 20);

function yourpropfirm_display_program_id_in_admin_products($column, $post_id) {
    if ('program_id' === $column) {
        $program_id = get_post_meta($post_id, '_yourpropfirm_program_id', true);
        if ($program_id) {
            echo '<span id="yourpropfirm_program_id-' . $post_id . '">' . esc_html($program_id) . '</span>'; 
        } else {
            echo '—';
        }
    }
}
add_action('manage_product_posts_custom_column', 'yourpropfirm_display_program_id_in_admin_products', 10, 2);

function yourpropfirm_save_quick_edit_data($product_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $product_id;

    if (isset($_POST['_inline_edit']) && wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
        if (isset($_POST['_yourpropfirm_program_id'])) {
            update_post_meta($product_id, '_yourpropfirm_program_id', sanitize_text_field($_POST['_yourpropfirm_program_id']));
        }
    }

    return $product_id;
}
add_action('save_post_product', 'yourpropfirm_save_quick_edit_data');

function yourpropfirm_add_program_id_quick_edit_field($column_name, $post_type) {
    if ($column_name != 'program_id' || $post_type != 'product') return;

    // Output custom field
    echo '<fieldset class="inline-edit-col-left">
            <div class="inline-edit-col">
                <label class="alignleft">
                    <span class="title">' . __('YPF-ProgramID', 'yourpropfirm') . '</span>
                    <span class="input-text-wrap">
                        <input type="text" name="_yourpropfirm_program_id" class="ptitle" value="" />
                    </span>
                </label>
            </div>
        </fieldset>';
}
add_action('quick_edit_custom_box', 'yourpropfirm_add_program_id_quick_edit_field', 10, 2);