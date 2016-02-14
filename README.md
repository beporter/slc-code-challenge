# bp-diffbot-products

Code challenge for wirecutter.com. Provides a WP-Admin interface for adding custom Product posts based on information retrieved from the DiffBot API.


## Requirements

* PHP 5.5
* WordPress 4.4 or later (maybe earlier, not tested).

## Features

* Provides a new custom post type `bpdiff-products` that includes custom fields for `regular_price`, `offer_price`, `source_url`.
* Presents a settings page for saving your DiffBot API token.
* Presents an **Import from URL** page for creating a new Product post using a public URL from Amazon.com, Target.com and practically any other online storefront.


## Installation

### Plugin manager from your `wp-admin`

Download the [latest release](https://github.com/dbeporter/wirecutter-code-challenge/releases) and upload it in the WordPress admin from **Plugins > Add New > Upload Plugin**.

### Composer

#### Packagist

@TODO: These instructions require the project being public and published on [Packagist](https://packagist.org).

```sh
composer require beporter/bp-diffbot-products ~0.0.1
```

or

```json
"require": {
  "php": ">=5.5.0",
  "wordpress": "~4.4.0",
  "beporter/bp-diffbot-products": "~0.0.1"
}
```

#### Wordpress Packagist

@TODO: These instructions require the project being public and published in the [Plugins Directory](https://wordpress.org/plugins/) (and mirrored to [WordPress Packagist](http://wpackagist.org/)).

If you're using Composer to manage WordPress, add this plugin to your project's dependencies. Run:

First, ensure your composer.json includes the Wordpress Packagist repository:

```json
    "repositories":[
        {
            "type":"composer",
            "url":"http://wpackagist.org"
        }
    ]
```

Then, inject the plugin into your config:

```sh
composer require wpackagist-plugin/bp-diffbot-products ~0.0.1
```

Or manually add it to your `composer.json`:

```json
"require": {
  "php": ">=5.5.0",
  "wordpress": "~4.4.0",
  "wpackagist-plugin/bp-diffbot-products": "~0.0.1"
}
```

Finally, from your WP Admin panel, activate the plugin.


## Usage

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


## Contributing

> _This plugin is not intended for public use. I humbly discourage you from contributing, since it would likely be a waste of your time. However, in the interest of thoroughness..._

### Code of Conduct

Please note that this project is released with a Contributor [Code of Conduct](CODE_OF_CONDUCT.md), based on the [Contributor Covenant](http://contributor-covenant.org/). By participating in this project you agree to abide by its terms.

### Reporting Issues

Please use [GitHub Isuses](https://github.com/dbeporter/wirecutter-code-challenge/issues) for listing any known defects or issues.

### Development

When developing this plugin, please fork and issue a PR for any new development.


## License

[MIT](LICENSE.md)


## Copyright

&copy; 2016 Brian Porter
