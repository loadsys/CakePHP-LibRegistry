# CakePHP-LibRegistry

<!--
[![Latest Version](https://img.shields.io/github/release/loadsys/{PLUGIN_NAME}.svg?style=flat-square)](https://github.com/loadsys/{PLUGIN_NAME}/releases)
**or**
[![Packagist Version](https://img.shields.io/packagist/v/loadsys/cakephp-libregistry.svg?style=flat-square)](https://packagist.org/packages/loadsys/cakephp-libregistry)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/loadsys/cakephp-libregistry/master.svg?style=flat-square)](https://travis-ci.org/loadsys/CakePHP-LibRegistry)
[![Coverage Status](https://img.shields.io/coveralls/loadsys/CakePHP-LibRegistry/master.svg)](https://coveralls.io/r/loadsys/cakephp-libregistry)
[![Total Downloads](https://img.shields.io/packagist/dt/loadsys/cakephp-libregistry.svg?style=flat-square)](https://packagist.org/packages/loadsys/cakephp-libregistry)
-->

The LibRegistry provides a mechanism for loading and storing instances of non-Cake PHP libraries. It works a lot like Cake 3's `TableRegistry`. Object instances can be requested by name and instantiated automatically, or can be loaded into the registry manually.


## Requirements

* CakePHP 3.0+


## Installation

### Composer

````bash
$ composer require loadsys/cakephp-libregistry:~1.0
````


## Usage

* Add this plugin to your application by adding this line to your bootstrap.php

	````php
	CakePlugin::load('LibRegistry');
	````

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
		use LibRegsitryTrait;
		public function index() {
			$this->loadLib('MyObj', ['configs' => 'here']);
			$this->MyObj->doSomethingNeat();
			// Works like loadComponent() in this context.
		}
	}

	```


## Contributing

### Reporting Issues

Please use [GitHub Isuses](https://github.com/loadsys/CakePHP-LibRegistry/issues) for listing any known defects or issues.

### Development

Please fork and issue a PR targeting the `master` branch for any new development.

The full test suite for the plugin can be run via this command:

```shell
$ vendor/bin/phpunit
```

Code must conform to the Loadsys coding standard:

```shell
$ vendor/bin/phpcs -p --standard=vendor/loadsys/loadsys_codesniffer/Loadsys src
```


## License

[MIT](https://github.com/loadsys/CakePHP-LibRegistry/blob/master/LICENSE.md)


## Copyright

[Loadsys Web Strategies](http://www.loadsys.com) 2015
