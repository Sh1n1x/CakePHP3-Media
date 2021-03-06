<?php
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Cake\Routing\DispatcherFactory;

require_once 'vendor/autoload.php';

// Path constants to a few helpful things.
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__) . DS);
define('CAKE_CORE_INCLUDE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
define('CORE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp' . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('APP', ROOT . 'tests' . DS . 'tests_app' . DS);
define('APP_DIR', 'tests_app');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', APP . 'webroot' . DS);
define('TMP', ROOT . 'tests' . DS . 'tmp' . DS);
define('CONFIG', APP . 'config' . DS);
define('CACHE', TMP);
define('LOGS', TMP);

require CAKE . 'Core/ClassLoader.php';

$loader = new \Cake\Core\ClassLoader;
$loader->register();
$loader->addNamespace('Cake\Test\Fixture', ROOT . '/vendor/cakephp/cakephp/tests/Fixture');

require_once CORE_PATH . 'config' . DS . 'bootstrap.php';

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

Configure::write('debug', true);

Configure::write('App', [
	'namespace' => 'App',
	'encoding' => 'UTF-8',
	'base' => false,
	'baseUrl' => false,
	'dir' => 'src',
	'webroot' => 'webroot',
	'www_root' => APP . 'webroot',
	'fullBaseUrl' => 'http://localhost',
	'imageBaseUrl' => 'img/',
	'jsBaseUrl' => 'js/',
	'cssBaseUrl' => 'css/',
	'paths' => [
		'plugins' => [APP . 'Plugin' . DS],
		'templates' => [APP . 'Template' . DS]
	]
]);

Configure::write('Session', [
	'defaults' => 'php'
]);

Cache::config([
	'_cake_core_' => [
		'engine' => 'File',
		'prefix' => 'cake_core_',
		'serialize' => true
	],
	'_cake_model_' => [
		'engine' => 'File',
		'prefix' => 'cake_model_',
		'serialize' => true
	],
	'default' => [
		'engine' => 'File',
		'prefix' => 'default_',
		'serialize' => true
	]
]);

// Ensure default test connection is defined
if (!getenv('db_class')) {
	putenv('db_class=Cake\Database\Driver\Sqlite');
	putenv('db_dsn=sqlite::memory:');
	
// 	putenv('db_database=test');
// 	putenv('db_login=root');
// 	putenv('db_password=root');
}


ConnectionManager::config('test', [
	'className' => 'Cake\Database\Connection',
	'driver' => getenv('db_class'),
  'dsn' => getenv('db_dsn'),
// 	'host' => 'localhost',
	'database' => getenv('db_database'),
	'username' => getenv('db_login'),
	'password' => getenv('db_password'),
	'timezone' => 'UTC'
]);

Log::config([
	'debug' => [
		'engine' => 'Cake\Log\Engine\FileLog',
		'levels' => ['notice', 'info', 'debug'],
		'file' => 'debug',
	],
	'error' => [
		'engine' => 'Cake\Log\Engine\FileLog',
		'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
		'file' => 'error',
	]
]);

Plugin::load('Media', ['path' => ROOT, 'bootstrap' => false, 'routes' => true]);

DispatcherFactory::add('Routing');
DispatcherFactory::add('ControllerFactory');
