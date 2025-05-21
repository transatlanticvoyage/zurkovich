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

// Add admin menu
function zurkovich_admin_menu() {
    // Add main menu item
    add_menu_page(
        'Zurkovich', // Page title
        'Zurkovich', // Menu title
        'manage_options', // Capability
        'zurkovich', // Menu slug
        'zurkovich_screen1', // Function to display the page
        'dashicons-admin-generic', // Icon
        2 // Position between Dashboard and Posts
    );

    // Add submenu pages
    add_submenu_page(
        'zurkovich', // Parent slug
        'Screen 1', // Page title
        'Screen 1', // Menu title
        'manage_options', // Capability
        'zurkovich', // Menu slug (same as parent for first item)
        'zurkovich_screen1' // Function to display the page
    );

    add_submenu_page(
        'zurkovich', // Parent slug
        'Screen 2', // Page title
        'Screen 2', // Menu title
        'manage_options', // Capability
        'zurkovich-screen2', // Menu slug
        'zurkovich_screen2' // Function to display the page
    );

    add_submenu_page(
        'zurkovich',
        'Screen 3',
        'Screen 3',
        'manage_options',
        'zurkovich-screen3',
        'zurkovich_screen3'
    );

    add_submenu_page(
        'zurkovich',
        'Screen 4',
        'Screen 4',
        'manage_options',
        'zurkovich-screen4',
        'zurkovich_screen4'
    );

    add_submenu_page(
        'zurkovich',
        'Screen 5',
        'Screen 5',
        'manage_options',
        'zurkovich-screen5',
        'zurkovich_screen5'
    );

    add_submenu_page(
        'zurkovich',
        'Screen 6',
        'Screen 6',
        'manage_options',
        'zurkovich-screen6',
        'zurkovich_screen6'
    );

    add_submenu_page(
        'zurkovich',
        'Screen 7',
        'Screen 7',
        'manage_options',
        'zurkovich-screen7',
        'zurkovich_screen7'
    );

    add_submenu_page(
        'zurkovich',
        'Screen 8',
        'Screen 8',
        'manage_options',
        'zurkovich-screen8',
        'zurkovich_screen8'
    );

    add_submenu_page(
        'zurkovich',
        'Screen 9',
        'Screen 9',
        'manage_options',
        'zurkovich-screen9',
        'zurkovich_screen9'
    );

    add_submenu_page(
        'zurkovich',
        'Screen 10',
        'Screen 10',
        'manage_options',
        'zurkovich-screen10',
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