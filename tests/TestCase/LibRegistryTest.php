<?php
/**
 * Test the LibRegistry singleton class.
 */
namespace LibRegistry\Test\TestCase;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use LibRegistry\LibRegistry;
use ReflectionClass;

/**
 * Subclass that
 */
class MyOwnRegistry extends LibRegistry {
	public $customVar = 'foo';
}

/**
 * Test case for LibRegistry
 */
class LibRegistryTest extends TestCase {

	/**
	 * setup
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		Configure::write('App.namespace', 'TestApp');
	}

	/**
	 * tear down
	 *
	 * @return void
	 */
	public function tearDown() {
		parent::tearDown();
		LibRegistry::clear();
	}

	/**
	 * Test the exists() method.
	 *
	 * @return void
	 */
	public function testExists() {
		$this->assertFalse(LibRegistry::exists('SampleLib'));

		LibRegistry::get('SampleLib', ['table' => 'articles']);
		$this->assertTrue(LibRegistry::exists('SampleLib'));
	}

	/**
	 * Test the exists() method with plugin-prefixed models.
	 *
	 * @return void
	 */
	public function testExistsPlugin() {
		$this->assertFalse(
			LibRegistry::exists('SampleLib')
		);
		$this->assertFalse(
			LibRegistry::exists('TestPlugin.SampleLib')
		);

		LibRegistry::get('TestPlugin.SampleLib');
		$this->assertFalse(
			LibRegistry::exists('SampleLib'),
			'The Comments key should not be populated'
		);
		$this->assertTrue(
			LibRegistry::exists('TestPlugin.SampleLib'),
			'The plugin.alias key should now be populated'
		);
	}

	/**
	 * Test getting instances from the registry.
	 *
	 * @return void
	 */
	public function testGet() {
		$result = LibRegistry::get('SampleLib', [
			'table' => 'my_articles',
		]);
		$this->assertInstanceOf('TestApp\Lib\SampleLib', $result);
		$this->assertEquals('bar', $result->foo());

		$result2 = LibRegistry::get('SampleLib');
		$this->assertSame($result, $result2);
		$this->assertEquals('bar', $result->foo());
	}

	/**
	 * Test get() with full namespaced classname
	 *
	 * @return void
	 */
	public function testGetPluginWithFullNamespaceName() {
		$class = 'TestPlugin\Lib\SampleLib';
		$table = LibRegistry::get($class);
		$this->assertInstanceOf(
			$class,
			$table
		);
		$this->assertFalse(
			LibRegistry::exists('TestPluginSampleLib'),
			'Class name should not exist'
		);
		$this->assertFalse(
			LibRegistry::exists('TestPlugin.TestPluginSampleLib'),
			'Full class alias should not exist'
		);
		$this->assertTrue(
			LibRegistry::exists($class),
			'Class name should exist'
		);
	}

	/**
	 * Test setting an instance.
	 *
	 * @return void
	 */
	public function testSet() {
		$mock = $this->getMock('Cake\ORM\Table');
		$this->assertSame($mock, LibRegistry::set('Articles', $mock));
		$this->assertSame($mock, LibRegistry::get('Articles'));
	}

	/**
	 * Test setting an instance with plugin syntax aliases
	 *
	 * @return void
	 */
	public function testSetPlugin() {
		$mock = $this->getMock('TestPlugin\Lib\SampleLib');

		$this->assertSame(
			$mock,
			LibRegistry::set('TestPlugin.SampleLib', $mock),
			'Return from setting the object in the registry should be the same object.'
		);
		$this->assertSame(
			$mock,
			LibRegistry::get('TestPlugin.SampleLib'),
			'The object retrieved from the registry must be the same instance.'
		);
	}

	/**
	 * Tests remove an instance
	 *
	 * @return void
	 */
	public function testRemove() {
		$first = LibRegistry::get('SampleLib');

		$this->assertTrue(LibRegistry::exists('SampleLib'));

		LibRegistry::remove('SampleLib');
		$this->assertFalse(LibRegistry::exists('SampleLib'));

		$second = LibRegistry::get('SampleLib');

		$this->assertNotSame($first, $second, 'Should be different objects, as the reference to the first was destroyed');
		$this->assertTrue(LibRegistry::exists('SampleLib'));
	}

	/**
	 * testRemovePlugin
	 *
	 * Removing a plugin-prefixed model should not affect any other
	 * plugin-prefixed model, or app model.
	 * Removing an app model should not affect any other
	 * plugin-prefixed model.
	 *
	 * @return void
	 */
	public function testRemovePlugin() {
		// Create two starter objects in the Registry.
		$app = LibRegistry::get('SampleLib');
		$plugin = LibRegistry::get('TestPlugin.SampleLib');

		$this->assertTrue(
			LibRegistry::exists('SampleLib'),
			'SampleLib object should exist after an initial get().'
		);
		$this->assertTrue(
			LibRegistry::exists('TestPlugin.SampleLib'),
			'TestPlugin SampleLib object should exist after an initial get().'
		);

	   // Remove the Plugin object.
		LibRegistry::remove('TestPlugin.SampleLib');

		$this->assertTrue(
			LibRegistry::exists('SampleLib'),
			'SampleLib should still exist after TestPlugin.SampleLib is removed.'
		);
		$this->assertFalse(
			LibRegistry::exists('TestPlugin.SampleLib'),
			'TestPlugin.SampleLib should not exist after being removed.'
		);

		// Request the same object names.
		$app2 = LibRegistry::get('SampleLib');
		$plugin2 = LibRegistry::get('TestPlugin.SampleLib');

		$this->assertSame(
			$app,
			$app2,
			'Should be the same SampleLib object on second request.'
		);
		$this->assertNotSame(
			$plugin,
			$plugin2,
			'Should not be the same TestPlugin.SampleLib object on second request after removal.'
		);

	   // Remove the App object.
		LibRegistry::remove('SampleLib');

		$this->assertFalse(
			LibRegistry::exists('SampleLib'),
			'SampleLib object should no longer exist after having been removed.'
		);
		$this->assertTrue(
			LibRegistry::exists('TestPlugin.SampleLib'),
			'TestPlugin.SampleLib should still exist after SampleLib was removed.'
		);

		$plugin3 = LibRegistry::get('TestPlugin.SampleLib');

		$this->assertSame(
			$plugin2,
			$plugin3,
			'Should be the same TestPlugin.SampleLib object'
		);
	}

	/**
	 * Test that subclassing doesn't interfere with base class.
	 *
	 * @return void
	 */
	public function testSubclassing() {
		$myRegistry = MyOwnRegistry::getInstance();
		$this->assertNotSame(
			LibRegistry::getInstance(),
			$myRegistry,
			'Subclass should be a separate singleton instance from parent class.'
		);
	}

	/**
	 * Verify that the singleton class can not be instantiated.
	 *
	 * @return void
	 */
	public function testCannotInstantiateExternally() {
		$reflection = new ReflectionClass('\LibRegistry\LibRegistry');
		$constructor = $reflection->getConstructor();
		$this->assertFalse(
			$constructor->isPublic(),
			'Not allowed to instantiate the singleton externally.'
		);
	}

	/**
	 * Verify that the singleton class can not be cloned.
	 *
	 * @return void
	 */
	public function testCannotClone() {
		$reflection = new ReflectionClass('\LibRegistry\LibRegistry');
		$constructor = $reflection->getmethod('__clone');
		$this->assertFalse(
			$constructor->isPublic(),
			'Not allowed to clone the singleton.'
		);
	}

	/**
	 * Verify that the singleton class can not be unserialized.
	 *
	 * @return void
	 */
	public function testCannotUnserialize() {
		$reflection = new ReflectionClass('\LibRegistry\LibRegistry');
		$constructor = $reflection->getmethod('__wakeup');
		$this->assertFalse(
			$constructor->isPublic(),
			'Not allowed to unserialize the singleton.'
		);
	}
}
