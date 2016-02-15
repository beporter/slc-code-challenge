<?php
/**
 * BP Diffbot Products bootstrap
 *
 * Provides plugin info the the WP UI. Pulls in required source files,
 * registers de/activation hooks as well as any functionality hooks.
 *
 * @link              https://github.com/beporter/wirecutter-code-challenge
 * @package           Bpdiff
 *
 * @wordpress-plugin
 * Plugin Name:       BP Diffbot Products
 * Plugin URI:        https://github.com/beporter/wirecutter-code-challenge
 * Description:       WordPress plugin for adding custom Product posts based on information retrieved from the DiffBot API.
 * Version:           0.0.1
 * Author:            Brian Porter
 * Author URI:        https://github.com/beporter
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       bp-diffbot-products
 * Domain Path:       /languages
 */

/**
 * Abort if called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Register plugin activation callback.
 */
function bpdiff_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bpdiff-bootstrap.php';
	Bpdiff_Bootstrap::activate();
}

/**
 * Register plugin deactivation callback.
 */
function bpdiff_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bpdiff-bootstrap.php';
	Bpdiff_Bootstrap::deactivate();
}

/**
 * Register plugin uninstall callback.
 */
function bpdiff_uninstall() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bpdiff-bootstrap.php';
	Bpdiff_Bootstrap::uninstall();
}

register_activation_hook( __FILE__, 'bpdiff_activate' );
register_deactivation_hook( __FILE__, 'bpdiff_deactivate' );
register_uninstall_hook( __FILE__, 'bpdiff_uninstall' );

/**
 * Begins execution of the plugin.
 *
 * The core plugin class is used to include and declare the classes
 * reponsible for handling the admin hooks.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bpdiff.php';

/**
 * Wrap up plugin launch.
 */
function run_bpdiff() {
	$plugin = new Bpdiff();
	$plugin->run();
}
run_bpdiff();
