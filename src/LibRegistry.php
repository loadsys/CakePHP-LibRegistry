<?php
/**
 * Provides a loading mechanism for non-Cake libraries.
 *
 * Used by the LibRegistryTrait to make "libs" available to PHP
 * classes quickly. Also provides a mechanism for
 * mocking/injecting libraries into existing Cake classes.
 *
 * In order to be compatible with this loader, "libs" are expected
 * to take an array of config values as their only __construct()
 * argument.
 *
 * Largely cribbed from TableRegistry.
 */

namespace LibRegistry;

use Cake\Core\App;
use Cake\Core\Exception\Exception;

/**
 * LibRegistry class
 */
class LibRegistry {

	/**
	 * Instances that belong to the registry.
	 *
	 * @var array
	 */
	protected static $_instances = [];

	/**
	 * Create an instance of a given classname.
	 *
	 * The config is ignore on susequent requests for the same object
	 * name. To reconfigure an existing object, remove() it and re-get().
	 *
	 * @param string $class The class to build.
	 * @param array $config The constructor configs to pass to the object.
	 * @return mixed
	 */
	public static function get($class, array $config = null) {
		if (!isset(static::$_instances[$class])) {
			$className = App::className($class, 'Lib', '');
			static::$_instances[$class] = new $className($config);
		}

		return static::$_instances[$class];
	}

	/**
	 * Check to see if an instance exists in the registry.
	 *
	 * @param string $class The alias to check for.
	 * @return bool
	 */
	public static function exists($class) {
		return isset(static::$_instances[$class]);
	}

	/**
	 * Set an instance.
	 *
	 * @param string $class The class name to set.
	 * @param mixed $object The table to set.
	 * @return mixed
	 */
	public static function set($class, $object) {
		static::$_instances[$class] = $object;

		return static::$_instances[$class];
	}

	/**
	 * Clears the registry of configuration and instances.
	 *
	 * @return void
	 */
	public static function clear() {
		static::$_instances = [];
	}

	/**
	 * Removes an instance from the registry.
	 *
	 * @param string $class The class name to remove.
	 * @return void
	 */
	public static function remove($class) {
		unset(static::$_instances[$class]);
	}

	/**
	 * Returns a reference to the singleton object instance.
	 *
	 * Allows for subclasses to peacefully coexist without clobbering
	 * each other.
	 *
	 * @param string|null $class Class name.
	 * @return object
	 */
	public static function getInstance($class = null) {
		static $selfs = [];
		$class = ($class ?: get_called_class());
		if (!isset($selfs[$class])) {
			$selfs[$class] = new $class();
		}

		return $selfs[$class];
	}

	/**
	 * Ensure nobody can instantiate a copy of this class directly.
	 *
	 * Must use LibRegistry::getInstance() instead.
	 *
	 * @return void
	 * @codeCoverageIgnore Nothing to test.
	 */
	private function __construct() {
		//no-op
	}

	/**
	 * Ensure nobody can clone this class directly.
	 *
	 * Must use LibRegistry::getInstance() instead.
	 *
	 * @return void
	 * @codeCoverageIgnore Nothing to test.
	 */
	private function __clone() {
		//no-op
	}

	/**
	 * Ensure nobody can unserialize a copy of this class.
	 *
	 * Must use LibRegistry::getInstance() instead.
	 *
	 * @return void
	 * @codeCoverageIgnore Nothing to test.
	 */
	private function __wakeup() {
		//no-op
	}
}
