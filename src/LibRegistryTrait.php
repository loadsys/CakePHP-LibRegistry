<?php
/**
 * Defines the necessary properties and functions to enable a Cake class
 * to work with non-Cake libraries. Extends the singleton LibRegistry
 * class to make it convenient to use in an instanced context. To use:
 *
 * ```
 *  use LibRegistry\LibRegistryTrait;
 *
 *  class MyController {
 *	  use LibRegistryTrait;
 *
 *	  public function some_action() {
 *	    $this->loadLib('SomeLib', ['config']);
 *	    $this->SomeLib->doSomeLibStuff();
 *    }
 * }
 * ```
 */

namespace LibRegistry;

use Cake\Core\App;
use LibRegistry\LibRegistry;

/**
 * LibRegistryTrait trait
 */
trait LibRegistryTrait {
	/**
	 * Stores the global LibRegistry instance used to load non-Cake libraries.
	 *
	 * @var LibRegistry\LibRegistry
	 */
	protected $_libs = null;

	/**
	 * Get the library registry for the host class.
	 *
	 * @return LibRegistry\LibRegistry
	 */
	public function libs() {
		if ($this->_libs === null) {
			$this->_libs = LibRegistry::getInstance();
		}

		return $this->_libs;
	}

	/**
	 * Fetch a library from the host object's registry (adding it first if necessary).
	 *
	 * Call via `$this->loadLib('Folder/LibName')` where "LibName" is defined
	 * in the file `/src/Lib/Folder/LibName.php`. The library will be made
	 * available to the host object at `$this->LibName`. The class name
	 * defined within must match the file name. Example:
	 *
	 * ```
	 * $this->loadLib('Payments/ProcessTransaction');
	 * ```
	 *
	 * Will result in a `$this->ProcessTransaction` property being set to an
	 * instance of the `class ProcessTransaction {...}` loaded from the file
	 * `src/Lib/Payments/ProcessTransaction.php`
	 *
	 * NOTE! This can cause collisions if you try to load the same library name
	 * from your app and a plugin. For example:
	 *
	 * ```
	 * $this->loadLib('SameLib');
	 * $this->loadLib('SomePlugin.Subfolder/SameLib');
	 * // $this->SameLib is the plugin's version now!
	 * ```
	 *
	 * In order for a class to be compatible with LibRegistry, its constructor
	 * must take a single array argument. Attempting to load classes that do
	 * not conform to this pattern will result in a runtime error.
	 *
	 * @param string $name The name of the library to load from `src/Lib/`.
	 * @param array $config The config for the library.
	 * @return mixed The instantiated copy of the library class.
	 */
	public function loadLib($name, array $config = []) {
		//@codingStandardsIgnoreStart
		list($plugin, $class) = pluginSplit($name);
		//@codingStandardsIgnoreEnd
		$prop = basename($class);

		$this->{$prop} = $this->libs()->get($name, $config);
		return $this->{$prop};
	}
}
