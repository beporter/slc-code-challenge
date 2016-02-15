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

@TODO: These instructions require the project being public and published on [Packagist](https://packagist.org). They have not been tested.

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

@TODO: These instructions require the project being public and published in the [Plugins Directory](https://wordpress.org/plugins/) (and mirrored to [WordPress Packagist](http://wpackagist.org/)). They have not been tested.

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

#### Unit Tests

@TODO: Write test suite.

#### Code Style Standard

This project uses the WordPress Coding Standard. To test the code against the standard, run the following commands:

```shell
$ composer create-project wp-coding-standards/wpcs:dev-master --no-dev
$ wpcs/vendor/bin/phpcs -ps --colors --standard=phpcs.xml
```

Fix any warnings or errors produced from this.


## Credits:

A list of the sources I used to complete this project. In no particular order and including some notes:

* https://github.com/Varying-Vagrant-Vagrants/VVV - Gave me a jumpstart to an operational WP dev environment. Although I did have to upgrade PHP to 5.6.
* https://generatewp.com/plugin-readme/ - Roughed in my readme.txt for me so I was sure to get the format correct.
* https://github.com/loadsys/CakePHP-Plugin-Skeleton/ - Used my own existing skeleton for reference.
* https://github.com/DevinVinson/WordPress-Plugin-Boilerplate - Provided the overall architecture for mine, although I simplified it a bit.
* https://github.com/ptahdunbar/wp-skeleton-plugin - Not very helpful, although it is supposedly stubbed for unit tests.
* https://github.com/diffbot/diffbot-php-client - The old "official" library. Very poor code quality, disappointing. This was my second choice.
* https://github.com/Swader/diffbot-php-client - My first choice, but in spite of the failings of the old library, this one requires too many dependencies and additional work to get installed. With the old one at least it can be bundled directly into the plugin's repo as a single file, so I eventually replaced this one with the older one above. Thankfully that work was isolated entirely to my wrapper class and required no changes in my main admin interface class.
* https://www.diffbot.com/dev/docs/ - Didn't need to use this much since the client libraries handled 90% of the heavy lifting.
* https://developer.wordpress.org/reference/ - Spent a lot of time here refreshing my API knowledge.


## License

[MIT](LICENSE.md)


## Copyright

&copy; 2016 Brian Porter
