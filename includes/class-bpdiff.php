<?php
/**
 * BP Diffbot Products
 *
 * Central plugin logic. Handles loading library dependencies and
 * registering filter/action hooks with WordPress.
 *
 * @package    Bpdiff
 * @subpackage Bpdiff/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Bpdiff {

	/**
	 * The loader that's responsible for maintaining and registering all
	 * hooks that power the plugin.
	 *
	 * @var Bpdiff_Loader
	 */
	protected $loader = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * The short prefix used throughout the plugin is `bpdiff` or `Bpdiff`.
	 *
	 * @var string
	 */
	protected $plugin_name = 'bp-diffbot-products';

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $version = '0.0.1';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used
	 * throughout the plugin. Load the dependencies and set the hooks used
	 * in the admin area.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Run the loader to register all of the plugin's hooks with WordPress.
	 *
	 * This is normally done at the end of the main plugin script
	 * (`bp-diffbot-products.php`).
	 *
	 * @return void
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the
	 * plugin's hooks with WordPress.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		// Import the Composer autoloader via our shim.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'autoload.php';

		// The class responsible common initialization routines.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bpdiff-bootstrap.php';

		// The class responsible for orchestrating the actions and filters used by the plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bpdiff-loader.php';

		// The class responsible for defining all actions that occur in the admin area.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-bpdiff-admin.php';

		// The class responsible for interacting with the DiffBot API.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bpdiff-diffbot.php';

		$this->loader = new Bpdiff_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @return void
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Bpdiff_Admin( $this->get_plugin_name(), $this->get_version() );

		// We currently have no styles or scripts to load.
		//$this->loader()->add_action( 'admin_enqueue_scripts', [$plugin_admin, 'enqueue_styles'] );
		//$this->loader()->add_action( 'admin_enqueue_scripts', [$plugin_admin, 'enqueue_scripts'] );

		$this->loader()->add_action( 'init', ['Bpdiff_Bootstrap', 'init'] );
		$this->loader()->add_action( 'admin_init', [$plugin_admin, 'pages_init'] );
		$this->loader()->add_action( 'admin_menu', [$plugin_admin, 'add_plugin_pages'] );
		$this->loader()->add_action( 'admin_post_scrape_product_url', [$plugin_admin, 'scrape_product_url'] );
		$this->loader()->add_action( 'when editing a Product post', [$plugin_admin, 'meta_init'] );
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return Bpdiff_Loader Orchestrates the hooks of the plugin.
	 */
	public function loader() {
		return $this->loader;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
