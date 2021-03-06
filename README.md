# CakePHP LibRegistry Plugin

[![Packagist Version](https://img.shields.io/packagist/v/loadsys/CakePHP-LibRegistry.svg?style=flat-square)](https://packagist.org/packages/loadsys/cakephp-libregistry)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/loadsys/CakePHP-LibRegistry/master.svg?style=flat-square)](https://travis-ci.org/loadsys/CakePHP-LibRegistry)
[![Coverage Status](https://img.shields.io/coveralls/loadsys/CakePHP-LibRegistry/master.svg?style=flat-square)](https://coveralls.io/r/loadsys/cakephp-libregistry)
[![Total Downloads](https://img.shields.io/packagist/dt/loadsys/cakephp-libregistry.svg?style=flat-square)](https://packagist.org/packages/loadsys/cakephp-libregistry)

The LibRegistry provides a mechanism for loading and storing instances of non-Cake PHP libraries. It works a lot like Cake 3's `TableRegistry`. Object instances can be requested by name and instantiated automatically, or can be loaded into the registry manually.


## Requirements

* CakePHP 3.0+


## Installation

### Composer

````bash
$ composer require loadsys/cakephp-libregistry:~1.0
````


## Usage

* Invoke the LibRegistry statically:

	```php
	<?php

	namespace App\Whatever;

	use LibRegistry\LibRegistry;

	class MyController extends Controller {
		public function index() {
			$myObj = LibRegistry::get('MyObj', ['configs' => 'here']);
			// (Where the class `src/Lib/MyObj.php` exists.)
			$myObj->doSomethingNeat();
		}
	}

	```

* A trait is also provided to load libraries into existing classes:

	```php
	<?php

	namespace App\Whatever;

	use LibRegistry\LibRegistryTrait;

	class MyController extends Controller {
		use LibRegistryTrait;
		public function index() {
			$this->loadLib('MyObj', ['configs' => 'here']);
			$this->MyObj->doSomethingNeat();
			// Works like loadComponent() in this context.
		}
	}

	```

### Library classes

* Must exist in `src/Lib/` in your Cake app or plugin and must be namespaced appropriately.

* Must accept a single array of config values as the sole `__construct()` argument. Libraries that don't conform to this interface can't be instantiated via `LibRegistry::get()`, although they can still be stored in the Registry manually via `::set()`.


## Contributing

### Code of Conduct

This project has adopted the Contributor Covenant as its [code of conduct](CODE_OF_CONDUCT.md). All contributors are expected to adhere to this code. [Translations are available](http://contributor-covenant.org/).

### Reporting Issues

Please use [GitHub Isuses](https://github.com/loadsys/CakePHP-LibRegistry/issues) for listing any known defects or issues.

### Development

Please fork and issue a PR targeting the `master` branch for any new development.

The full test suite for the plugin can be run via this command:

```shell
$ vendor/bin/phpunit
```

Code must conform to the Loadsys coding standard, which is based on the CakePHP coding standard:

```shell
$ vendor/bin/phpcs --config-set installed_paths vendor/cakephp/cakephp-codesniffer,vendor/loadsys/loadsys_codesniffer
$ vendor/bin/phpcs -p --standard=Loadsys src
```


## License

[MIT](https://github.com/loadsys/CakePHP-LibRegistry/blob/master/LICENSE.md)


## Copyright

[Loadsys Web Strategies](http://www.loadsys.com) 2016
