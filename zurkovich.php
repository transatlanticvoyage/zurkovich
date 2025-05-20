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
    add_menu_page(
        'Zurkovich',
        'Zurkovich',
        'manage_options',
        'zurkoscreen1',
        'zurkovich_screen1',
        'dashicons-admin-generic',
        2 // Position between Dashboard and Posts
    );

    // Add submenu pages
    add_submenu_page('zurkoscreen1', 'Screen 1', 'Screen 1', 'manage_options', 'zurkoscreen1', 'zurkovich_screen1');
    add_submenu_page('zurkoscreen1', 'Screen 2', 'Screen 2', 'manage_options', 'zurkoscreen2', 'zurkovich_screen2');
    add_submenu_page('zurkoscreen1', 'Screen 3', 'Screen 3', 'manage_options', 'zurkoscreen3', 'zurkovich_screen3');
    add_submenu_page('zurkoscreen1', 'Screen 4', 'Screen 4', 'manage_options', 'zurkoscreen4', 'zurkovich_screen4');
    add_submenu_page('zurkoscreen1', 'Screen 5', 'Screen 5', 'manage_options', 'zurkoscreen5', 'zurkovich_screen5');
    add_submenu_page('zurkoscreen1', 'Screen 6', 'Screen 6', 'manage_options', 'zurkoscreen6', 'zurkovich_screen6');
    add_submenu_page('zurkoscreen1', 'Screen 7', 'Screen 7', 'manage_options', 'zurkoscreen7', 'zurkovich_screen7');
    add_submenu_page('zurkoscreen1', 'Screen 8', 'Screen 8', 'manage_options', 'zurkoscreen8', 'zurkovich_screen8');
    add_submenu_page('zurkoscreen1', 'Screen 9', 'Screen 9', 'manage_options', 'zurkoscreen9', 'zurkovich_screen9');
    add_submenu_page('zurkoscreen1', 'Screen 10', 'Screen 10', 'manage_options', 'zurkoscreen10', 'zurkovich_screen10');
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

