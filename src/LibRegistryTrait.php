<?php
/**
 * Defines the necessary properties and functions to enable a Cake class
 * to work with non-Cake libraries. Extends the singleton LibRegistry
 * class to make it convenient to use in an instanced context. To use:
 *
 * ```
 *  use App\Lib\LibRegistryTrait;
 *
 *  class MyController {
 *	use LibRegistryTrait;
 *
 *	public function some_action() {
 *	  $this->loadLib('SomeLib', ['config']);
 *	  $this->SomeLib->doSomeLibStuff();
 *	}
 *  }
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
	 * Stores a LibRegistry instance used to load non-Cake libraries.
	 *
	 * Call via `$this->loadLib('FullLibName')` where "FullLibName"
	 * is a file from `/src/Lib/Loadsys`. The Lib will be made
	 * available to the controller at `$this->FullLibName`.
	 *
	 * @var App\Lib\LibRegistry
	 */
	protected $_libs = null;

	/**
	 * Get the component registry for this controller.
	 *
	 * @return \Cake\Controller\ComponentRegistry
	 */
	public function libs() {
		if ($this->_libs === null) {
			$this->_libs = LibRegistry::getInstance();
		}

		return $this->_libs;
	}

	/**
	 * Add a library to the controller's registry.
	 *
	 * This method will also set the library to a property.
	 * For example:
	 *
	 * ```
	 * $this->loadLib('ProcessTransaction');
	 * ```
	 *
	 * Will result in a `ProcessTransaction` property being set.
	 *
	 * NOTE! This can cause collisions if for example you try to load the same library name from your app and a plugin.
	 *
	 * ```
	 * $this->loadLib('SameLib');
	 * $this->loadLib('SomePlugin.Subfolder/SameLib');
	 * // $this->SameLib is the plugin's version now!
	 * ```
	 *
	 * @param string $name The name of the library to load from `src/Lib/Loadsys/`.
	 * @param array $config The config for the library.
	 * @return mixed
	 */
	public function loadLib($name, array $config = []) {
		list($plugin, $class) = pluginSplit($name);
		$prop = basename($class);

		$this->{$prop} = $this->libs()->get($name, $config);
		return $this->{$prop};
	}
}
