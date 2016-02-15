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
	public function __construct($key) {
		$this->key = $key;
		$this->bot = new diffbot( $key, 3 );
	}

	/**
	 * Spawns a one-off instance and hits the Account API endpoint to validate the API key provided is valid and active. Returns true if the key is valid and can be used, false on failure.
	 *
	 * @param string $key The DiffBot token to use with all requests to the API.
	 * @return void
	 */
	public static function validateKey($key) {
		try {
			$bot = new static( $key );
		} catch ( \Exception $e ) {
			return false;
		}

		$account = $bot->account();
		return ( $account['status'] === 'active' );
	}

	/**
	 * Fetch API results for the account associated with the loaded token.
	 *
	 * @return object
	 */
	public function account() {
		$results = $this->bot->account();
		return $this->mapAccountFields($results);
	}

	/**
	 * Fetch API results for a Product APi request against a public URL and
	 * return the first result.
	 *
	 * @param string $url The public URL the DiffBot API should scrape for product information
	 * @return object
	 */
	public function product($url) {
		$fields = [];
		$results = $this->bot->product( $url, $fields );
		return $this->mapProductFields($results->objects[0]);
	}

	/**
	 * Converts the custom Entity returned by the API into a plain
	 * associative array suitable for use with the custom Products post
	 * type. This completely hides the details of the API results from
	 * the WP plugin.
	 *
	 * @param string $url The public URL the DiffBot API should scrape for product information
	 * @return object
	 */
	protected function mapAccountFields($account) {
		return [
			'name' => $account->name,
			'email' => $account->email,
			'plan' => $account->plan,
			'planCalls' => $account->planCalls,
			'status' => $account->status,
		];
	}

	/**
	 * Converts the custom Entity returned by the API into a plain
	 * associative array suitable for use with the custom Products post
	 * type. This completely hides the details of the API results from
	 * the WP plugin.
	 *
	 * @param object $product The returned json_decode()d product object.
	 * @return array
	 */
	protected function mapProductFields($product) {
		return (array) $product;
	}






	/**
	 * Converts the custom Entity returned by the API into a plain
	 * associative array suitable for use with the custom Products post
	 * type. This completely hides the details of the API results from
	 * the WP plugin.
	 *
	 * @param \Swader\Diffbot\Entity\Product $product @TODO desc.
	 * @return object
	 */
	protected function mapProductFields_oldlib(\Swader\Diffbot\Entity\Product $product) {
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
	 * @return void
	 */
	public static function validateKey_oldlib($key) {
		try {
			$bot = new static( $key );
		} catch ( \Exception $e ) {
			return false;
		}

		// Ref: https://www.diffbot.com/dev/docs/account/
		// This doesn't exist in the vendor lib yet. When it does, we can
		// confirm the account for the provided key is in good standing
		// using something like the following:
		//$account = $bot->createAccountApi()->call();
		//return ( $account->getStatus() === 'active' );

		return true;
	}

	/**
	 * Fetch API results for a Product APi request against a public URL and
	 * return the first result.
	 *
	 * @param string $url The public URL the DiffBot API should scrape for product information
	 * @return object
	 */
	public function product_oldlib($url) {
		$productApi = $this->bot->createProductAPI( $url );
		$productApi
			->setMeta( false )
			->setDiscussion( false )
			->setColors( false )
			->setSize( false)
			->setAvailability(false);

		// Results from call() are always an iterable collection.
		// We only care about the first result.
		$results = $productApi->call()->current();

		return $this->mapProductFields($results);
	}
}
