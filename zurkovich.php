<?php
/**
 * Plugin Name: Zurkovich
 * Description: A custom admin tool plugin for WordPress.
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include scraping functions
require_once plugin_dir_path(__FILE__) . 'includes/functions-scrape.php';

// Function to save API key
function zurkovich_save_api_key($api_key) {
    // Encrypt the API key before saving
    $encrypted_key = base64_encode($api_key);
    update_option('zurkovich_openai_api_key', $encrypted_key);
}

// Function to get API key
function zurkovich_get_api_key() {
    $encrypted_key = get_option('zurkovich_openai_api_key');
    if ($encrypted_key) {
        return base64_decode($encrypted_key);
    }
    return '';
}

// Create database tables on plugin activation
function zurkovich_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Table name for driggs
    $table_name_driggs = $wpdb->prefix . 'zurko_driggs';
    
    // SQL for driggs table
    $sql_driggs = "CREATE TABLE $table_name_driggs (
        driggs_id bigint(20) NOT NULL AUTO_INCREMENT,
        driggs_domain varchar(255) NOT NULL,
        driggs_industry text NOT NULL,
        driggs_city varchar(255) NOT NULL,
        driggs_brand_name_1 varchar(255) NOT NULL,
        driggs_site_type_or_purpose text NOT NULL,
        driggs_email_1 varchar(255) NOT NULL,
        driggs_address_1 text NOT NULL,
        driggs_phone1 varchar(50) NOT NULL,
        PRIMARY KEY  (driggs_id)
    ) $charset_collate;";

    // Table name for pageideas
    $table_name_pageideas = $wpdb->prefix . 'zurko_pageideas';
    
    // SQL for pageideas table
    $sql_pageideas = "CREATE TABLE $table_name_pageideas (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        order_for_display_on_interface_1 int(11) NOT NULL,
        name varchar(255) NOT NULL,
        rel_wp_post_id_1 bigint(20) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_driggs);
    dbDelta($sql_pageideas);
}

// Register activation hook
register_activation_hook(__FILE__, 'zurkovich_create_tables');

// Add admin menu
function zurkovich_admin_menu() {
    // Add main menu item
    add_menu_page(
        'Zurkovich', // Page title
        'Zurkovich', // Menu title
        'manage_options', // Capability
        'zurkoscreen1', // Menu slug (updated)
        'zurkovich_screen1', // Function to display the page
        'dashicons-admin-generic', // Icon
        2 // Position between Dashboard and Posts
    );

    // Add submenu pages
    add_submenu_page(
        'zurkoscreen1', // Parent slug (updated)
        'Screen 1 - API Keys', // Page title
        'Screen 1 - API Keys', // Menu title
        'manage_options', // Capability
        'zurkoscreen1', // Menu slug (updated)
        'zurkovich_screen1' // Function to display the page
    );

    add_submenu_page(
        'zurkoscreen1', // Parent slug (updated)
        'Screen 2 - Driggs', // Page title
        'Screen 2 - Driggs', // Menu title
        'manage_options', // Capability
        'zurkoscreen2', // Menu slug (updated)
        'zurkovich_screen2' // Function to display the page
    );

    add_submenu_page(
        'zurkoscreen1',
        'Screen 3 - Prex Extract',
        'Screen 3 - Prex Extract',
        'manage_options',
        'zurkoscreen3', // updated
        'zurkovich_screen3'
    );

    add_submenu_page(
        'zurkoscreen1',
        'Screen 4',
        'Screen 4',
        'manage_options',
        'zurkoscreen4', // updated
        'zurkovich_screen4'
    );

    add_submenu_page(
        'zurkoscreen1',
        'Screen 5',
        'Screen 5',
        'manage_options',
        'zurkoscreen5', // updated
        'zurkovich_screen5'
    );

    add_submenu_page(
        'zurkoscreen1',
        'Screen 6',
        'Screen 6',
        'manage_options',
        'zurkoscreen6', // updated
        'zurkovich_screen6'
    );

    add_submenu_page(
        'zurkoscreen1',
        'Screen 7',
        'Screen 7',
        'manage_options',
        'zurkoscreen7', // updated
        'zurkovich_screen7'
    );

    add_submenu_page(
        'zurkoscreen1',
        'Screen 8',
        'Screen 8',
        'manage_options',
        'zurkoscreen8', // updated
        'zurkovich_screen8'
    );

    add_submenu_page(
        'zurkoscreen1',
        'Screen 9',
        'Screen 9',
        'manage_options',
        'zurkoscreen9', // updated
        'zurkovich_screen9'
    );

    add_submenu_page(
        'zurkoscreen1',
        'Screen 10',
        'Screen 10',
        'manage_options',
        'zurkoscreen10', // updated
        'zurkovich_screen10'
    );
}
add_action('admin_menu', 'zurkovich_admin_menu');

// Include screen files
function zurkovich_screen1() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen1.php';
}

function zurkovich_screen2() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen2.php';
}

function zurkovich_screen3() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen3.php';
}

function zurkovich_screen4() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen4.php';
}

function zurkovich_screen5() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen5.php';
}

function zurkovich_screen6() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen6.php';
}

function zurkovich_screen7() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen7.php';
}

function zurkovich_screen8() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen8.php';
}

function zurkovich_screen9() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen9.php';
}

function zurkovich_screen10() {
    include plugin_dir_path(__FILE__) . 'includes/admin/screens/screen10.php';
} 