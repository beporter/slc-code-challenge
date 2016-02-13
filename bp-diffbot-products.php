<?php
/**
 * BP Diffbot Products bootstrap
 *
 * Provides plugin info the the UI. Pulls in required source files,
 * registers de/activation hooks and defines the entry method for the
 * plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
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

// Abort if called directly.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Register bootstrapping callbacks.
 */
function bpdiff_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bpdiff-bootstrap.php';
	Bpdiff_Bootstrap::activate();
}

function bpdiff_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bpdiff-bootstrap.php';
	Bpdiff_Bootstrap::deactivate();
}

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
 * The core plugin class is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bpdiff.php';
function run_bpdiff() {
	$plugin = new Bpdiff();
	$plugin->run();
}
run_bpdiff();
