<?php
/**
 * Collects all of the install/uninstall, activate/deactivate hook methods
 * together.
 *
 * @package    Bpdiff
 * @subpackage Bpdiff/includes
 */

/**
 * Fired during plugin activation and deactivation
 *
 * This class defines all code necessary to run during the plugin's install and uninstall operations.
 */
class Bpdiff_Bootstrap {
	/**
	 * Activation hook.
	 *
	 * Executed when the plugin is turned on. (Installed fresh or re-activated.) Perform any initial setup. (Must be repeatable safely.)
	 *
	 * @return void
	 */
	public static function activate() {
		// Trigger function to register `product` post type.
		static::init();

		// Refresh permalinks after `product` post type has been registered.
		flush_rewrite_rules();
	}

	/**
	 * Plugin init.
	 *
	 * Registers Products post type.
	 *
	 * @return void
	 */
	public static function init() {
		register_post_type( 'product', [ 'public' => 'true' ] );
	}

	/**
	 * Deactivation hook.
	 *
	 * Executed when plugin is deactivated. Update site settings to remove dependencies on the plugin.
	 *
	 * @return void
	 */
	public static function deactivate() {
		// (Product post type automatically removed.)

		// Clear permalinks to remove `product` post type rules.
		flush_rewrite_rules();
	}

	/**
	 * Uninstall hook.
	 *
	 * Executed when (deactivated) plugin is deleted. Clean up custom options, db tables.
	 *
	 * @return void
	 */
	public static function uninstall() {
		$options = [
			'bpdiff_todo',
		];
		foreach ( $options as $option ) {
			delete_option( $option );
			delete_site_option( $option );
		}
	}
}
