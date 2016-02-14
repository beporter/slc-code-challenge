<?php
/**
 * Utility class for collecting and registering filter and action hooks.
 *
 * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/blob/master/plugin-name/includes/class-plugin-name-loader.php
 * @package    Bpdiff
 * @subpackage Bpdiff/includes
 */

/**
 * Register actions and filters for the plugin.
 *
 * Maintains a list of all hooks that are registered throughout
 * the plugin, and registers them with the WordPress API. Call the
 * run() function to register the list of actions and filters with WP.
 */
class Bpdiff_Loader {

	/**
	 * The array of actions to register with WordPress.
	 *
	 * @var array
	 */
	protected $actions = [];

	/**
	 * The array of filters to register with WordPress.
	 *
	 * @var array
	 */
	protected $filters = [];

	/**
	 * Create a new loader instance.
	 *
	 * @return void
	 */
	public function __construct() {
		// Nothing to do.
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    callable             $callback         Either an array in the form [$object, 'method'] or a string function name or an anonymous function to supply as the callback method.
	 * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = compact( 'hook', 'callback', 'priority', 'accepted_args' );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    callable             $callback         Either an array in the form [$object, 'method'] or a string function name or an anonymous function to supply as the callback method.
	 * @param    int                  $priority         Optional. he priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = compact( 'hook', 'callback', 'priority', 'accepted_args' );
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], $hook['callback'], $hook['priority'], $hook['accepted_args'] );
		}
	}
}
