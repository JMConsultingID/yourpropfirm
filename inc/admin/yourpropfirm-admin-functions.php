<?php
/**
 * Plugin functions and definitions for Admin.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * @package yourpropfirm
 */
// Hook for adding admin menus
add_action('admin_menu', 'yourpropfirm_add_menus');

// Function to add menus
function yourpropfirm_add_menus() {
    // Main menu
    add_menu_page(
        'YPF Dashboard', // Page title
        'YPF Dashboard', // Menu title
        'manage_options', // Capability
        'yourpropfirm_dashboard', // Menu slug
        'yourpropfirm_dashboard_page', // Function to display the page content
        'dashicons-buddicons-forums', // Icon URL
        3 // Position
    );

    // Sub-menu: Settings
    add_submenu_page(
        'yourpropfirm_dashboard', // Parent slug
        'Settings', // Page title
        'Settings', // Menu title
        'manage_options', // Capability
        'yourpropfirm_settings', // Menu slug
        'yourpropfirm_settings_page' // Function to display the page content
    );

    // Sub-menu: Addons
    add_submenu_page(
        'yourpropfirm_dashboard', // Parent slug
        'Addons', // Page title
        'Addons', // Menu title
        'manage_options', // Capability
        'yourpropfirm_addons', // Menu slug
        'yourpropfirm_addons_page' // Function to display the page content
    );
}

// Function to display the main dashboard page content
function yourpropfirm_dashboard_page() {
    echo '<div class="wrap"><h1>YPF Dashboard</h1><p>Welcome to the YPF Dashboard.</p></div>';
}

// Function to display the settings page content
function yourpropfirm_settings_page() {
    echo '<div class="wrap"><h1>Settings</h1><p>Here you can configure the settings for YourPropFirm plugin.</p></div>';
}

// Function to display the addons page content
function yourpropfirm_addons_page() {
    echo '<div class="wrap"><h1>Addons</h1><p>This section allows you to manage addons for YourPropFirm plugin.</p></div>';
}
?>