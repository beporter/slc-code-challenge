<?php
/**
 * All functionality provided by this plugin happens in the admin interface.
 *
 * Provides two additional admin pages. One for submitting a URL and
 * creating a new Product post from the DiffBot results, and another for
 * saving the DiffBot token to use with requests to the API.
 *
 * @link https://codex.wordpress.org/Creating_Options_Pages#Example_.232
 *
 * @package    Bpdiff
 * @subpackage Bpdiff/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class Bpdiff_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Holds the values to be used in the fields callbacks.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * The prefix used for all meta fields and settings handled by this plugin.
	 *
	 * @var string
	 */
	private $prefix = 'bpdiff';

	/**
	 * Stores an array of internal error keys and their descriptive messages.
	 *
	 * Used with ::redirect_error() and ::error() to pass failures and messages between
	 * requests in the url. When set, [type] must be one of 'error', 'updated',
	 * 'update-nag'. (Default is `error`.)
	 *
	 * @var array
	 */
	private $errors = [
		'no-url' => [
			'msg' => 'No product URL was provided.',
		],
		'bad-url' => [
			'msg' => 'The string provided is not a valid URL. Values must start with `http://` or `https://`.',
		],
		'invalid-key' => [
			'msg' => 'The DiffBot API token is not valid.',
		],
		'bad-key' => [
			'msg' => 'The DiffBot API token stored in <strong>Settings > Product Posts</strong> is not valid.',
		],
		'no-privs' => [
			'msg' => 'Insufficient access.',
		],
		'create-post-failed' => [
			'msg' => 'Unable to create a new Product post from the returned DiffBot data.',
		],
		'create-post-successful' => [
			'msg' => 'New Product post created successfully.',
			'type' => 'updated',
		],
	];

	/**
	 * Default postmeta property properties. Used for formating and
	 * sanitizing meta fields in our custom post type. These defaults
	 * are used whenever an override is not present in ::$metas.
	 *
	 * @var array
	 */
	private $meta_defaults = [
		'template' => '
			<tr>
				<th><label for="%1$s">%2$s</label></th>
				<td><input type="text" class="large-text" id="%1$s" name="%1$s" value="%3$s" />
			</tr>
		',
		'sanitizer' => 'sanitize_text_field',
	];

	/**
	 * Defines the full set of meta properties used by the custom post type in this plugin.
	 *
	 * These properties are used both to render the fields and to validate them.
	 * Keys are the non-prefixed version of the stored postmeta field names.
	 * Each element can include (if not present, the defaults will be used):
	 * - A "pretty" display [label] for the field.
	 * - A sprintf() compatible formatting [template] for rendering a form input.
	 *     %1$s = field name, %2$s = label, %3$s = value
	 * - A callable [sanitizer] method for the field. Invoked during ::save_meta().
	 * - Whether the column should be displayed in the Products index table.
	 * - The name of the field returned from the API call to retrieve for this meta property.
	 *
	 * @var array
	 */
	private $metas = [
		'regular_price' => [
			'label' => 'Regular Price',
			'column' => true,
			'api_field' => 'regularPrice',
		],
		'offer_price' => [
			'label' => 'Offer Price',
			'column' => true,
			'api_field' => 'offerPrice',
		],
		'source_url' => [
			'label' => 'Product Source URL',
			'sanitizer' => 'esc_url_raw',
			'api_field' => 'pageUrl',
		],
	];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @return void
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Callback to register menu links for two admin pages.
	 *
	 * One to configure the DiffBot API key to use, and one to submit URLs
	 * for creation of new Product posts.
	 *
	 * @see Bpdiff::define_admin_hooks()
	 * @return void
	 */
	public function add_plugin_pages() {
		/*
		// If we wanted to remove the stock "Add New" page normally listed
		// under the "Products" post type heading and replace it with our
		// custom one:
		remove_submenu_page(
			'edit.php?post_type=' . Bpdiff::postType,
			'post-new.php?post_type=' . Bpdiff::postType
		);
		*/

		// Replace it with a custom page for submitting a URL to DiffBot.
		add_submenu_page(
			'edit.php?post_type=' . Bpdiff::postType,
			'Add Product Post', // Page title.
			'Import from URL', // Menu title.
			'publish_posts', // Capability to access.
			"{$this->prefix}-addpost", // Menu slug.
			[ $this, 'create_addpost_page' ] // Callback to render the page.
		);

		// Listed under "Settings", for saving a DiffBot API key.
		add_options_page(
			'Product Post Settings',
			'Product Posts',
			'manage_options',
			"{$this->prefix}-settings",
			[ $this, 'create_setting_page' ]
		);
	}

	/**
	 * Register and add settings for the plugin.
	 *
	 * We only have a single `apikey` setting to store.
	 *
	 * @see Bpdiff::define_admin_hooks()
	 * @return void
	 */
	public function pages_init() {
		$this->register_notices();

		// Settings page for setting/storing the DiffBot API token.
		register_setting(
			"{$this->prefix}_settings",
			"{$this->prefix}_settings",
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			"{$this->prefix}_diffbot",
			'DiffBot API Key',
			array( $this, 'print_settings_info' ),
			"{$this->prefix}-settings"
		);

		add_settings_field(
			'apikey',
			'DiffBot API Key',
			array( $this, 'draw_setting_apikey' ),
			"{$this->prefix}-settings",
			"{$this->prefix}_diffbot"
		);
	}

	/**
	 * Inject meta boxes into the add/edit screens for our custom
	 * Products post type.
	 *
	 * @see Bpdiff::define_admin_hooks()
	 * @return void
	 */
	public function meta_init() {
		add_meta_box(
			"{$this->prefix}_products", // Unique field id.
			'DiffBot Products Properties', // Box title.
			[ $this, 'draw_meta_box' ], // Content callback.
			Bpdiff::postType // Post type.
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * Stubbed, but not used as we current have no custom styles to load.
	 *
	 * @see Bpdiff::define_admin_hooks()
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . "css/{$this->prefix}-admin.css",
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * Stubbed, but not used as we current have no custom scripts to load.
	 *
	 * @see Bpdiff::define_admin_hooks()
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . "js/{$this->prefix}-admin.js",
			rray( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	 * Callback for processing meta fields unique to our custom Product
	 * post type and saving them into the database.
	 *
	 * @see Bpdiff::define_admin_hooks()
	 * @param int $post_id The ID of the current Post records being saved.
	 * @return void
	 */
	public function save_meta_hook( $post_id ) {
		// Short circuit if there is no POST data to work on.
		if ( empty( $_POST ) ) {
			return;
		}

		// Short circuit if current user doesn't have access to edit posts.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// @TODO: Check a nonce?
		$this->save_meta( $post_id, $_POST );
	}

	/**
	 * Callback for accepting and processing the "addpost" form submission
	 * containing the URL to send to DiffBot for conversion into a custom
	 * Product post entry.
	 *
	 * @see ::add_plugin_pages()
	 * @return void
	 */
	public function scrape_product_url() {
		// Abort on lack of capability by current user.
		if ( ! current_user_can( 'publish_posts' ) ) {
			$this->redirect( 'no-privs' );
		}

		// Validate necessary input params. Redirect back on missing.
		$params = $this->get_url_params();
		if ( empty( $params['url'] ) ) {
			$this->redirect( 'no-url' );
		}

		// Validate the URL provided to the best of our ability.
		// esc_url_raw() doesn't cut it here by itself (try entering
		// "bad url" and see), so we use that only as the second stage later.
		$this->options = get_option( "{$this->prefix}_settings" );
		$url = $params['url'];
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			$this->redirect( 'bad-url' );
		}

		// Spin up the DiffBot wrapper. Redirect back on failure.
		try {
			$bot = new Bpdiff_DiffBot( $this->options['apikey'] );
			$result = $bot->product( esc_url_raw( $url, [ 'http', 'https' ] ) );
		} catch ( \Exception $e ) {
			$this->redirect( 'bad-key', compact( 'url' ) );
		}

		// Attempt to create the new Product post. Redirect back on failure.
		$product_post_id = $this->new_product_post( $result );
		if ( false === $product_post_id ) {
			$this->redirect( 'create-post-failed', compact( 'url' ) );
		}

		// Product post was created successfully. Redirect to it.
		$params = [
			'post' => $product_post_id,
			// 'post_type' => Bpdiff::postType,
			'action' => 'edit',
			$this->prefix => [
				'e' => 'create-post-successful',
			],
		];
		wp_redirect( '/wp-admin/post.php?' . http_build_query( $params ) );
		exit;
	}

	/**
	 * "Add a Product Post" page callback.
	 *
	 * Renders the form on the page manually.
	 *
	 * @return void
	 */
	public function create_addpost_page() {
		$params = $this->get_url_params();
		$url = ( isset( $params['url'] ) ? $params['url'] : '' );

		?>
		<div class="wrap">
			<h2>Add Product Post</h2>
			<p>Paste a product URL below to have the <a href="https://www.diffbot.com">DiffBot</a> service scrape a product page from a remote site and create a new Product Post in WordPress. DiffBot may take up to a minute to return results, so please be patient.</p>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="scrape_product_url">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="url">URL:</label></th>
							<td><input type="text" class="large-text" id="url" name="<?php echo esc_attr( $this->prefix ); ?>[url]" value="<?php echo esc_url( $url ); ?>" /></td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Import Product Page">
				</p>
			</form>
		</div>
		<?php
	}

	/**
	 * Options page callback.
	 *
	 * @return void
	 */
	public function create_setting_page() {
		$this->options = get_option( "{$this->prefix}_settings" );

		?>
		<div class="wrap">
			<h2>DiffBot Product API Configuration</h2>
			<form method="post" action="options.php">
			<?php
				settings_fields( "{$this->prefix}_settings" );
				do_settings_sections( "{$this->prefix}-settings" );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Callback to inject custom columns into a Products index.
	 *
	 * @param array $columns The original set of columns being rendered.
	 * @return array The nodified set of columns to render for this post type.
	 */
	public function inject_custom_columns( $columns ) {
		$date = $columns['date'];
		unset( $columns['date'] );

		foreach ( $this->metas as $name => $props ) {
			if ( isset( $props['column'] ) && true === $props['column'] ) {
				$columns[ "{$name}" ] = $props['label'];
			}
		}

		$columns['date'] = $date;

		return $columns;
	}

	/**
	 * Callback to render custom columns into a row in Products listings.
	 *
	 * @param string $column The name of the current column to render.
	 * @param int    $post_id The ID of the curent Post being rendered.
	 * @return void
	 */
	public function draw_custom_columns( $column, $post_id ) {
		if ( isset( $this->metas[ $column ]['column'] )
			&& true === $this->metas[ $column ]['column']
		) {
			echo esc_attr( get_post_meta( $post_id, sanitize_key( "{$this->prefix}_{$column}" ), true ) );
		}
	}

	/**
	 * Sanitize each setting field as needed.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 * @return array Sanitized values for $input.
	 */
	public function sanitize_settings( $input ) {
		$valid_input = array();

		if ( isset( $input['apikey'] ) ) {
			if ( Bpdiff_Diffbot::validate_key( $input['apikey'] ) ) {
				$valid_input['apikey'] = trim( $input['apikey'] );
			} else {
				$err = $this->errors['invalid-key'];
				$type = ( isset( $err['type'] ) ? $err['type'] : 'error' );
				add_settings_error( $apikey, 'invalid-key', $err['msg'], $type );
			}
		}

		return $valid_input;
	}

	/**
	 * Print the Setting Section text for the API key config page.
	 *
	 * @return void
	 */
	public function print_settings_info() {
		echo 'In order to use the DiffBot Product API, you must configure WordPress with a valid API key. <a href="https://www.diffbot.com/plans/trial">A free trial key</a> can be obtained for temporary use, otherwise you must register for a <a href="https://www.diffbot.com/pricing/">paid plan</a>. Please enter your DiffBot API key below.';
	}

	/**
	 * Print a form input for the apikey setting.
	 *
	 * @return void
	 */
	public function draw_setting_apikey() {
		$key = 'apikey';
		printf(
			'<input type="text" class="standard-text code" id="%2$s" name="%1$s_settings[%2$s]" value="%3$s" />',
			$this->prefix,
			sanitize_key( $key ), // Unnecessary, but the code sniffer is dumb.
			( isset( $this->options[ $key ] ) ? esc_attr( $this->options[ $key ] ) : '' )
		);
	}

	/**
	 * Echo HTML to render the form inputs for all associated meta fields.
	 *
	 * @param WP_Post $post The current Post object being rendered.
	 * @return void
	 */
	public function draw_meta_box( $post ) {
		// @TODO: Set a nonce?
		echo '<table class="form-table"><tbody>';

		foreach ( $this->metas as $name => $props ) {
			$template = ( ! empty( $props['template'] ) ? $props['template'] : $this->meta_defaults['template'] );
			$field = "{$this->prefix}_{$name}";
			$label = ( ! empty( $props['label'] ) ? $props['label'] : $name );
			$value = get_post_meta( $post->ID, $field, true );

			// @codingStandardsIgnoreStart
			printf($template, $field, $label, $value);
			// @codingStandardsIgnoreEnd
		}

		echo '</tbody></table>';
	}

	/**
	 * Creates a new Product post using the supplied $fields.
	 *
	 * @param array $fields The set of custom fields needed to create a new Product post.
	 * @return int|false The Post.id of the new record if created successfully, false on failure.
	 */
	protected function new_product_post( $fields ) {
		// Create the new Post record.
		$post = [
			'post_title' => wp_strip_all_tags( $fields['title'] ),
			'post_content' => $fields['text'],
			'post_type' => Bpdiff::postType,
		];
		$post_id = wp_insert_post( $post );
		if ( $post_id instanceof WP_Error ) {
			return false;
		}

		// Save the associated meta fields.
		$meta = [];
		foreach ( $this->metas as $name => $props ) {
			if ( ! empty( $props['api_field'] ) ) {
				$meta[ "{$this->prefix}_{$name}" ] = $fields[ $props['api_field'] ];
			}
		}
		if ( ! $this->save_meta( $post_id, $meta ) ) {
			return false;
		}

		// Return the post ID to redirect to.
		return $post_id;
	}

	/**
	 * Internal helper for saving meta fields related to our custom post type.
	 *
	 * @see Bpdiff::define_admin_hooks()
	 * @param int   $post_id The ID of the current Post record being saved.
	 * @param array $data The set of meta fields and values being saved.
	 * @return bool True on successful save of all meta fields, false on any failure.
	 */
	protected function save_meta( $post_id, $data ) {
		foreach ( $this->metas as $name => $props ) {
			$key = "{$this->prefix}_{$name}";
			$sanitizer = $this->meta_defaults['sanitizer'];
			if ( isset( $props['sanitizer'] ) && is_callable( $props['sanitizer'] ) ) {
				$sanitizer = $props['sanitizer'];
			}

			if ( ! empty( $data[ $key ] ) ) {
				$value = call_user_func( $sanitizer, $data[ $key ] );
				$success = update_post_meta(
					$post_id,
					$key,
					$value
				);
				if ( ! $success ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Register an admin_notice for everything in $_GET[ $this->prefix ]['e'].
	 *
	 * Called at the top of ::pages_init() to display any notices
	 * passed from the previous page load.
	 *
	 * @return void
	 */
	protected function register_notices() {
		$params = $this->get_url_params();
		$notices = ( ! empty( $params['e'] ) ? (array) $params['e'] : [] );

		foreach ( $notices as $notice ) {
			$this->add_notice( $notice );
		}
	}

	/**
	 * Registers an admin_notice action for the provided ::$errors $key and $type.
	 *
	 * This message will be displayed at the top of the admin page.
	 *
	 * @param string $key The key from ::$errors for which to return a message.
	 * @return void
	 */
	protected function add_notice( $key ) {
		$msg = $this->format_notice( $key );

		add_action(
			'admin_notices',
			function () use ( $msg ) {
				// @codingStandardsIgnoreStart
				echo $msg;
				// @codingStandardsIgnoreEnd
			}
		);
	}

	/**
	 * Format an internal error for rendering in the admin interface via admin_notices.
	 *
	 * @param string $key The key from ::$errors for which to return a message.
	 * @return string A formatted HTML admin error/notice message.
	 */
	protected function format_notice( $key ) {
		$err = $this->errors[ $key ];
		return sprintf(
			'<div class="%1$s"> <p>%2$s</p></div>',
			( isset( $err['type'] ) ? $err['type'] : 'error' ),
			$err['msg']
		);
	}

	/**
	 * Return an array of URL params unique to this plugin (in the
	 * [ $this->prefix ] subkey). Return empty array if none found.
	 *
	 * Can't get around having to access $_REQUEST at some point.
	 *
	 * @return array Associative array of [key => value]s found in the
	 *    current $_REQUEST (GET or POST). Empty array when nothing found.
	 */
	protected function get_url_params() {
		// @codingStandardsIgnoreStart
		if ( ! empty( $_REQUEST[ $this->prefix ] ) ) {
			$prefixed_params = (array) $_REQUEST[ $this->prefix ] ;
		// @codingStandardsIgnoreEnd
		} else {
			$prefixed_params = [];
		}
		return $prefixed_params;
	}

	/**
	 * Wrapper around wp_redirect().
	 *
	 * Redirects back to the "Add Product" URL with an error flag set in
	 * the URL. Used to pass status between requests.
	 * (::create_addpost_page() and ::scrape_product_url() mostly.)
	 *
	 * @param string $code The key from ::$errors indicating the failure that
	 *    occurred. Will be used on the next page load to register an admin_notice.
	 * @param array  $params Any additional [ $this->prefix ] URL params that should
	 *    be persisted to the next page load.
	 * @return void
	 */
	protected function redirect( $code, $params = [] ) {
		$nested = array_merge(
			[
				'e' => $code,
			],
			$params
		);
		$options = [
			'post_type' => Bpdiff::postType,
			'page' => "{$this->prefix}-addpost",
			$this->prefix => $nested,
		];

		wp_redirect( '/wp-admin/edit.php?' . http_build_query( $options ) );
		exit;
	}
}
