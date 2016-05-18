<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright	 Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link		  http://cakephp.org CakePHP(tm) Project
 * @license	   http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;

$findRoot = function ($root) {
	do {
		$lastRoot = $root;
		$root = dirname($root);
		if (is_dir($root . '/vendor/cakephp/cakephp')) {
			return $root;
		}
	} while ($root !== $lastRoot);
	throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);

require_once 'vendor/cakephp/cakephp/src/basics.php';
require_once 'vendor/autoload.php';

define('ROOT', $root . DS . 'tests' . DS . 'test_app' . DS);
define('APP', ROOT . 'TestApp' . DS);
define('TMP', sys_get_temp_dir() . DS);

Configure::write('debug', true);
Configure::write('App', [
	'namespace' => 'TestApp',
	'paths' => [
		'plugins' => [ROOT . 'Plugin' . DS],
	],
]);

if (!getenv('db_dsn')) {
	putenv('db_dsn=sqlite:///:memory:');
}

ConnectionManager::config('test', ['url' => getenv('db_dsn')]);


// Wipe out any accumulated caches before running tests.
foreach (\Cake\Cache\Cache::configured() as $key) {
	\Cake\Cache\Cache::clear(false, $key);
	echo "Cleared cache: $key\n";
}

echo PHP_EOL;
