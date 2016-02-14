=== bp-diffbot-products ===
Contributors: beporter
Tags: diffbot,products
Requires at least: 4.4.0
Tested up to: 4.4.2
License: MIT
License URI: https://opensource.org/licenses/MIT

Provides a WP-Admin interface for adding custom Product posts based on information retrieved from the DiffBot API.

== Description ==

The plugin will create a new section in your admin interface titled **Products**, as well as a new **Settings** pane for manaing your DiffBot API key.

You will not be able to generate new Product posts until you have entered a valid DiffBot API token in **Settings > Product Posts**.

To import a new product page:

* Browse the web and locate the product page you wish to import. For example:
	* WordPress for Dummies: http://amzn.com/1119088577
	* My Neighor Totoro: http://amzn.com/B00BEYYEJ4
	* Super Mario Maker: http://www.target.com/p/-/A-47904515
* Copy the URL from your address bar.
* Log into your WP Admin portal.
* Navigate to **Products > Import from URL**.
* Paste the URL and submit the form.
* If the process is successful, you'll be redirected to the newly-created Product post to review it.

== Installation ==

* Download the latest release of this plugin from the source repository:
* Upload into your Wordpress installation in `wp-content/plugins/bp-diffbot-products/`.
* Log into your WP Admin panel and activate the plugin.


== Changelog ==
0.0.1 - Initial draft.