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
		// Set UI labels for Custom Post Type.
		$labels = [
			'name'                => _x( 'Products', 'Post Type General Name', Bpdiff::prefix ),
			'singular_name'       => _x( 'Product', 'Post Type Singular Name', Bpdiff::prefix ),
			'menu_name'           => __( 'Products', Bpdiff::prefix ),
			'all_items'           => __( 'All Products', Bpdiff::prefix ),
			'view_item'           => __( 'View Product', Bpdiff::prefix ),
			'add_new_item'        => __( 'Add New Product', Bpdiff::prefix ),
			'add_new'             => __( 'Add New', Bpdiff::prefix ),
			'edit_item'           => __( 'Edit Product', Bpdiff::prefix ),
			'update_item'         => __( 'Update Product', Bpdiff::prefix ),
			'search_items'        => __( 'Search Product', Bpdiff::prefix ),
			'not_found'           => __( 'Not Found', Bpdiff::prefix ),
			'not_found_in_trash'  => __( 'Not found in Trash', Bpdiff::prefix ),
		];

		// Set other options for Custom Post Type.
		$args = [
			'label'               => __( 'products', Bpdiff::prefix ),
			'description'         => __( 'Product information', Bpdiff::prefix ),
			'labels'              => $labels,
			'rewrite'             => [ 'slug' => 'products' ], // @TODO: should proably be configurable in the Admin UI.
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-products',
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		];

		// Registering your Custom Post Type.
		register_post_type( Bpdiff::postType, $args );
	}

	/**
	 * Deactivation hook.
	 *
	 * Executed when plugin is deactivated. Update site settings to remove
	 * dependencies on the plugin.
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
	 * Executed when (deactivated) plugin is deleted. Clean up custom
	 * options, db tables.
	 *
	 * @return void
	 */
	public static function uninstall() {
		$options = [
			'bpdiff_settings',
		];
		foreach ( $options as $option ) {
			delete_option( $option );
			delete_site_option( $option );
		}
	}
}
