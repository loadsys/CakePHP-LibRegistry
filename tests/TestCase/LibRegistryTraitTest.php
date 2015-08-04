<?php
/**
 * Test the LibRegistryTrait trait.
 */
namespace LibRegistry\Test\TestCase;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use LibRegistry\LibRegistry;
use LibRegistry\LibRegistryTrait;

/**
 * Testing class that uses the LibRegistryTrait.
 */
class TestLibRegistryTrait {
	use LibRegistryTrait;
}

/**
 * Test case for LibRegistryTrait
 */
class LibRegistryTraitTest extends TestCase {

	/**
	 * setup
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		Configure::write('App.namespace', 'TestApp');
		$this->TraitedClass = new TestLibRegistryTrait();
	}

	/**
	 * tear down
	 *
	 * @return void
	 */
	public function tearDown() {
		unset($this->TraitedClass);
		LibRegistry::clear();
		parent::tearDown();
	}

	/**
	 * Test the libs() method.
	 *
	 * @return void
	 */
	public function testLibs() {
		$this->assertSame(
			LibRegistry::getInstance(),
			$this->TraitedClass->libs(),
			'The LibRegistry instance attached to our traited class should be the same as the global LibRegistry singleton.'
		);
	}

	/**
	 * Test the loadLib() method.
	 *
	 * @return void
	 * @dataProvider provideLoadLibsArgs
	 */
	public function testLoadLib($name, $property, $namespacedClass) {
		$result = $this->TraitedClass->loadLib($name);
		$this->assertInstanceOf(
			$namespacedClass,
			$result,
			'Returned object should be of the expected class.'
		);
		$this->assertObjectHasAttribute(
			$property,
			$this->TraitedClass,
			'Calling class should have the expected property set.'
		);
		$this->assertInstanceOf(
			$namespacedClass,
			$this->TraitedClass->{$property},
			'Class property should be the expected type.'
		);
	}

	/**
	 * provide input/output data sets to testLoadLib().
	 *
	 * @return array
	 */
	public function provideLoadLibsArgs() {
		return [
			[
				'SampleLib',
				'SampleLib',
				'TestApp\Lib\SampleLib',
			],

			[
				'TestPlugin.Subdir/TestpluginLibrary',
				'TestpluginLibrary',
				'TestPlugin\Lib\Subdir\TestpluginLibrary',
			],
		];
	}
}
