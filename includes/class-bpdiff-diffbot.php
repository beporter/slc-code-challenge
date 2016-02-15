<?php
/**
 * Provide a wrapper around the diffbot library.
 *
 * @package    Bpdiff
 * @subpackage Bpdiff/includes
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-diffbot.php';

/**
 * Provides an interface layer between the WP plugin and the vendor library,
 * isolating the changes needed to swap out vendors in the future without
 * mucking up the rest of the plugin.
 */
class Bpdiff_Diffbot {

	/**
	 * The DiffBot API token to use with all requests.
	 *
	 * @var string $key
	 */
	private $key = null;

	/**
	 * Instance of the vendor library.
	 *
	 * @var object $bot
	 */
	private $bot = null;

	/**
	 * Create a new DiffBot wrapper instance using the provided API key.
	 *
	 * The call to `new DiffBot()` can throw an exception if the provided
	 * key is rejected. Be ready to catch and process that on the calling
	 * side.
	 *
	 * @param string $key The DiffBot token to use with all requests to the API.
	 * @return void
	 */
	public function __construct( $key ) {
		$this->key = $key;
		$this->bot = new diffbot( $key, 3 );
	}

	/**
	 * Spawns a one-off instance and hits the Account API endpoint to
	 * validate the API key provided is valid and active. Returns true
	 * if the key is valid and can be used, false on failure.
	 *
	 * @param string $key The DiffBot token to use with all requests to the API.
	 * @return bool True when the token is found to be valid and active, false on any failure.
	 */
	public static function validate_key( $key ) {
		if ( empty( $key ) ) {
			return false;
		}

		try {
			$bot = new static( $key );
		} catch ( \Exception $e ) {
			return false;
		}

		$account = $bot->account();
		return ( 'active' === $account['status'] );
	}

	/**
	 * Fetch API results for the account associated with the loaded token.
	 *
	 * @return object
	 */
	public function account() {
		$results = $this->bot->account();
		return $this->map_account_fields( $results );
	}

	/**
	 * Fetch API results for a Product APi request against a public URL and
	 * return the first result.
	 *
	 * @param string $url The public URL the DiffBot API should scrape for product information.
	 * @return object
	 */
	public function product( $url ) {
		$fields = [];
		$results = $this->bot->product( $url, $fields );
		return $this->map_product_fields( $results->objects[0] );
	}

	/**
	 * Converts the custom Entity returned by the API into a plain
	 * associative array suitable for use with the custom Products post
	 * type. This completely hides the details of the API results from
	 * the WP plugin.
	 *
	 * @param string $account The returned json_decode()d account object.
	 * @return array An associative array of mapped account fields.
	 */
	protected function map_account_fields( $account ) {
		return [
			'name' => $account->name,
			'email' => $account->email,
			'plan' => $account->plan,
			// @codingStandardsIgnoreStart
			'planCalls' => $account->planCalls,
			// @codingStandardsIgnoreEnd
			'status' => $account->status,
		];
	}

	/**
	 * Converts the object returned by the API into a plain
	 * associative array suitable for use with the custom Products post
	 * type. This completely hides the details of the API results from
	 * the WP plugin.
	 *
	 * For now this stub is good enough. If we needed to adapt the API
	 * result in the future, we have a place to do it.
	 *
	 * We have to provide some default keys to ensure they are always
	 * present in the returned array even if they are not present in the
	 * API response payload.
	 *
	 * @param object $product The returned json_decode()d product object.
	 * @return array An associative array of mapped product fields.
	 */
	protected function map_product_fields( $product ) {

		$defaults = [
			'title' => 'No title available',
			'text' => 'No description available.',
			'regularPrice' => 'N/A',
			'offerPrice' => 'N/A',
			'pageUrl' => '',
		];
		return (array) $product + $defaults;

	}






	/**
	 * Converts the custom Entity returned by the API into a plain
	 * associative array suitable for use with the custom Products post
	 * type. This completely hides the details of the API results from
	 * the WP plugin.
	 *
	 * @param \Swader\Diffbot\Entity\Product $product The returned Product entity from which to extract values.
	 * @return object
	 */
	protected function map_product_fields_oldlib( \Swader\Diffbot\Entity\Product $product ) {
		return [
			'title' => $product->getTitle(),
			'text' => $product->getText(),
			'offer_price' => $product->getOfferPrice(),
			'regular_price' => $product->getRegularPrice(),
			'source_url' => $product->getPageUrl(),
		];
	}

	/**
	 * Spawns a one-off instance and hits the Account API endpoint to
	 * validate the API key provided is valid and active. Returns true if
	 * the key is valid and can be used, false on failure.
	 *
	 * @param string $key The DiffBot token to use with all requests to the API.
	 * @return bool True when the token is found to be valid and active, false on any failure.
	 */
	public static function validate_key_oldlib( $key ) {
		try {
			$bot = new static( $key );
		} catch ( \Exception $e ) {
			return false;
		}

		/*
		// Ref: https://www.diffbot.com/dev/docs/account/
		// This doesn't exist in the vendor lib yet. When it does, we can
		// confirm the account for the provided key is in good standing
		// using something like the following:
		$account = $bot->createAccountApi()->call();
		return ( $account->getStatus() === 'active' );
		*/

		return true;
	}

	/**
	 * Fetch API results for a Product APi request against a public URL and
	 * return the first result.
	 *
	 * @param string $url The public URL the DiffBot API should scrape for product information.
	 * @return object
	 */
	public function product_oldlib( $url ) {
		$product_api = $this->bot->createProductAPI( $url );
		$product_api
			->setMeta( false )
			->setDiscussion( false )
			->setColors( false )
			->setSize( false )
			->setAvailability( false );

		// Results from call() are always an iterable collection.
		// We only care about the first result.
		$results = $product_api->call()->current();

		return $this->map_product_fields( $results );
	}
}
