<?php

/**
 * Plugin Name:       Testimonials CPT
 * Plugin URI:        https://yourwebsite.com/my-awesome-plugin
 * Description:       Professional Testimonials Custom Post Type with metaboxes and shortcode.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Iftiar Hossain
 * Author URI:        https://www.iftiarhossain.com
 * License:           GPL-2.0-or-later
 * Text Domain:       testimonials-cpt
 * Domain Path:       /languages
 */

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

define('TCPT_VERSION', '1.0.0');
define('TCPT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TCPT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load the main class
require_once TCPT_PLUGIN_DIR . 'includes/class-testimonials-cpt.php';

// Initialize the plugin
function run_testimonials_cpt()
{
    Testimonials_CPT::get_instance()->run();
}
add_action('plugins_loaded', 'run_testimonials_cpt');