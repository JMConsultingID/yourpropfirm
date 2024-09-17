<?php
/**
 * @link              https://yourpropfirm.com
 * @since             1.2.1
 * @package           yourpropfirm
 * GitHub Plugin URI: https://github.com/JMConsultingID/your-propfirm-addon
 * GitHub Branch: develop
 * @wordpress-plugin
 * Plugin Name:       YourPropfirm Connection Dashboard
 * Plugin URI:        https://yourpropfirm.com
 * Description:       This Plugin to Create User and Account to Dashboard YourPropfirm
 * Version:           1.2.1.0
 * Author:            YourPropfirm Team
 * Author URI:        https://yourpropfirm.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yourpropfirm
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'YOURPROPFIRM_VERSION', '1.1.6.9' );

if (!function_exists('is_plugin_active')) {
    include_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

require plugin_dir_path( __FILE__ ) . 'inc/yourpropfirm-functions.php';

// Enqueue external JS in the WooCommerce product admin page from plugin directory
function yourpropfirm_enqueue_admin_script($hook) {
    global $post;

    // Check if we are on the product edit page
    if ('post.php' === $hook && 'product' === $post->post_type) {
        // Register and enqueue your external JS from plugin directory
        wp_enqueue_script(
            'yourpropfirm-admin-js', // Unique handle for the script
            plugin_dir_url(__FILE__) . 'admin/js/yourpropfirm-admin.js', // Path to your JS file in plugin directory
            array('jquery'), // Dependencies (in this case, jQuery)
            YOURPROPFIRM_VERSION, // Version of the script
            true // Load in footer
        );
    }
}
add_action('admin_enqueue_scripts', 'yourpropfirm_enqueue_admin_script');
