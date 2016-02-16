<?php
/**
 * PHPUnit Bootstrap file.
 *
 * @package Bpdiff/tests
 */

/**
 * You'll need to set `WP_TESTS_DIR` to the base directory of WordPress.
 *
 * Examples
 * `svn export http://develop.svn.wordpress.org/trunk/ /tmp/wordpress-tests`
 *
 * Then add this to your bash environment:
 *   `export WP_TESTS_DIR=/tmp/wordpress/tests`
 *
 *   (For Windows: powershell)
 *   `$env:WP_TESTS_DIR = "C:\tmp\wordpress-tests"`
 *
 *   (For Windows: cygwin)
 *   `setx WP_TESTS_DIR "C:\tmp\wordpress-tests"`
 */
$_guesses = [
	getenv( 'WP_TESTS_DIR' ), // A local environment override.
	'/tmp/wordpress-tests-lib', // Ddefault install location?
	dirname( dirname( dirname( dirname( __FILE__ ) ) ) ), // Perhaps we are in a VVV install?
];

$_tests_dir = false;
foreach ( $_guesses as $_guess ) {
	if ( file_exists( $_guess ) && file_exists( $_guess . '/tests' ) ) {
		$_tests_dir = $_guess;
		break;
	}
}

if ( ! $_tests_dir ) {
	die( "Fatal Error: Could not find the WordPress tests directory.\n" );
}

require_once $_tests_dir . '/includes/functions.php';

/**
 * Provide a hook for injecting the plugin.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/bp-diffbot-products.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
