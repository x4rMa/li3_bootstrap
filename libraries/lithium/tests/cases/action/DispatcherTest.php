<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2010, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace lithium\tests\cases\action;

use \lithium\action\Request;
use \lithium\net\http\Router;
use \lithium\action\Dispatcher;
use \lithium\tests\mocks\action\MockDispatcher;

class DispatcherTest extends \lithium\test\Unit {

	protected $_routes = array();

	public function setUp() {
		$this->_routes = Router::get();
		Router::reset();
	}

	public function tearDown() {
		Router::reset();

		foreach ($this->_routes as $route) {
			Router::connect($route);
		}
	}

	public function testRun() {
		Router::connect('/', array('controller' => 'test', 'action' => 'test'));
		MockDispatcher::run(new Request(array('url' => '/')));

		$result = end(MockDispatcher::$dispatched);
		$expected = array('controller' => 'test', 'action' => 'test');
		$this->assertEqual($expected, $result->params);
	}

	public function testRunWithNoRouting() {
		$this->expectException('/Could not route request/');
		MockDispatcher::run(new Request(array('url' => '/')));
	}

	public function testConfigManipulation() {
		$config = MockDispatcher::config();
		$expected = array('rules' => array());
		$this->assertEqual($expected, $config);

		MockDispatcher::config(array('rules' => array(
			'admin' => array('action' => 'admin_{:action}')
		)));

		Router::connect('/', array('controller' => 'test', 'action' => 'test', 'admin' => true));
		MockDispatcher::run(new Request(array('url' => '/')));

		$result = end(MockDispatcher::$dispatched);
		$expected = array('action' => 'admin_test', 'controller' => 'test', 'admin' => true);
		$this->assertEqual($expected, $result->params);
	}

	public function testControllerLookupFail() {
		Dispatcher::config(array('classes' => array('router' => __CLASS__)));

		$this->expectException('/Controller SomeNonExistentController not found/');
		Dispatcher::run(new Request(array('url' => '/')));
	}

	public function testPluginControllerLookupFail() {
		Dispatcher::config(array('classes' => array('router' => __CLASS__)));

		$this->expectException('/Controller some_invalid_plugin.Controller not found/');
		Dispatcher::run(new Request(array('url' => '/plugin')));
	}

	public static function process($request) {
		$params = array(
			'' => array('controller' => 'some_non_existent_controller', 'action' => 'index'),
			'/plugin' => array(
				'controller' => 'some_invalid_plugin.controller', 'action' => 'index'
			)
		);
		if (isset($params[$request->url])) {
			$request->params = $params[$request->url];
		}
		return $request;
	}
}

?>