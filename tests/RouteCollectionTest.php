<?php

use Slab\Router\RouteCollection;

/**
 * Test RouteCollection
 *
 * @package default
 * @author Luke Lanchester
 **/
class RouteCollectionTest extends PHPUnit_Framework_TestCase {


	/**
	 * Test can instantiate an empty route collection
	 *
	 * @return void
	 **/
	public function testCanInstantiateCollection() {

		$collection = new RouteCollection;
		$this->assertInstanceOf('Slab\Router\RouteCollection', $collection);

	}



	/**
	 * Test adding a route
	 *
	 * @return void
	 * @dataProvider routesProvider
	 **/
	public function testAddRoute($method, $path, $handler, $test_group, $test_path) {

		$collection = new RouteCollection;

		$collection->$method($path, $handler);

		$test_routes = $collection->getRoutes($test_group);

		$this->assertEquals([['path' => $test_path, 'handler' => $handler]], $test_routes);

	}



	/**
	 * Test adding multiple routes
	 *
	 * @return void
	 **/
	public function testAddMultipleRoutes() {

		$collection = new RouteCollection;
		$routes = $this->routesProvider();
		$tests = [];

		foreach($routes as $route) {
			list($method, $path, $handler, $test_group, $test_path) = $route;
			$collection->$method($path, $handler);
			$tests[$test_group][] = ['path' => $test_path, 'handler' => $handler];
		}

		foreach($tests as $group => $test) {
			$this->assertEquals($test, $collection->getRoutes($group));
		}

	}



	/**
	 * Provide routes for testing
	 *
	 * @return void
	 **/
	public function routesProvider() {

		return [
			['get', '', 'myHandlerGet1', 'GET', ''],
			['get', '/', 'myHandlerGet2', 'GET', ''],
			['get', 'foo', 'myHandlerGet3', 'GET', 'foo'],
			['get', 'foo/bar', 'myHandlerGet4', 'GET', 'foo/bar'],
			['get', 'foo/{slug}', 'myHandlerGet5', 'GET', 'foo/{slug}'],
			['get', 'foo/{slug?}', 'myHandlerGet6', 'GET', 'foo/{slug?}'],
			['post', 'foo', 'myHandlerPost1', 'POST', 'foo'],
			['post', 'foo/bar', 'myHandlerP2', 'POST', 'foo/bar'],
			['put', 'foo/bar', 'myHandlerPut1', 'PUT', 'foo/bar'],
			['delete', 'foo/bar', 'myHandlerDelete1', 'DELETE', 'foo/bar'],
			['head', 'foo/bar', 'myHandlerHead1', 'HEAD', 'foo/bar'],
			['options', 'foo/bar', 'myHandlerOptions1', 'OPTIONS', 'foo/bar'],
		];

	}



}
