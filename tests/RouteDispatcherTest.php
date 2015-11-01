<?php

use Mockery as m;

use Slab\Router\RouteDispatcher;

/**
 * Test RouteDispatcher
 *
 * @package default
 * @author Luke Lanchester
 **/
class RouteDispatcherTest extends PHPUnit_Framework_TestCase {


	/**
	 * Test can instantiate an empty route dispatcher
	 *
	 * @return void
	 **/
	public function testCanInstantiateDispatcher() {

		$dispatcher = new RouteDispatcher;
		$this->assertInstanceOf('Slab\Router\RouteDispatcher', $dispatcher);

	}



	/**
	 * Test dispatching against empty routes
	 *
	 * @return void
	 **/
	public function testDispatcherWithNoRoutes() {

		$request = m::mock('Slab\Core\Http\RequestInterface');
		$request->shouldReceive('getMethod')->once()->andReturn('GET');
		$request->shouldReceive('getPath')->once()->andReturn('foo/bar');

		$routes = m::mock('Slab\Router\RouteCollection');
		$routes->shouldReceive('getRoutes')->with('GET')->once()->andReturn([]);

		$dispatcher = new RouteDispatcher;

		$result = $dispatcher->dispatch($request, $routes);

		$this->assertEquals(['status' => 404], $result);

	}



	/**
	 * Test dispatching a request
	 *
	 * @return void
	 **/
	public function testDispatcherHit() {

		$request = m::mock('Slab\Core\Http\RequestInterface');
		$request->shouldReceive('getMethod')->once()->andReturn('GET');
		$request->shouldReceive('getPath')->once()->andReturn('foo/bar');

		$route = ['path' => 'foo/bar', 'handler' => 'myHandler'];

		$routes = m::mock('Slab\Router\RouteCollection');
		$routes->shouldReceive('getRoutes')->with('GET')->once()->andReturn([$route]);

		$dispatcher = new RouteDispatcher;

		$result = $dispatcher->dispatch($request, $routes);

		$this->assertEquals([
			'status' => 200,
			'route' => $route,
			'params' => [],
		], $result);

	}



	/**
	 * Test dispatching a request against not-found
	 *
	 * @return void
	 **/
	public function testDispatcherMiss() {

		$request = m::mock('Slab\Core\Http\RequestInterface');
		$request->shouldReceive('getMethod')->once()->andReturn('POST');
		$request->shouldReceive('getPath')->once()->andReturn('foo/bar');

		$routes = m::mock('Slab\Router\RouteCollection');
		$routes->shouldReceive('getRoutes')->with('POST')->once()->andReturn([
			['path' => '/', 'handler' => 'myHandler1'],
			['path' => 'foo', 'handler' => 'myHandler2'],
			['path' => 'foo/bar/baz', 'handler' => 'myHandler3'],
			['path' => 'bar', 'handler' => 'myHandler4'],
		]);

		$dispatcher = new RouteDispatcher;

		$result = $dispatcher->dispatch($request, $routes);

		$this->assertEquals(['status' => 404], $result);

	}



	/**
	 * Tear down tests
	 *
	 * @return void
	 **/
	public function tearDown() {

		m::close();

	}



}
