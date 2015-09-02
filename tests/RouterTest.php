<?php

use Mockery as m;

use Slab\Router\Router;

/**
 * Test Router
 *
 * @package default
 * @author Luke Lanchester
 **/
class RouterTest extends PHPUnit_Framework_TestCase {


	/**
	 * Test can instantiate an empty router
	 *
	 * @return void
	 **/
	public function testCanInstantiateRouter() {

		$container = m::mock('Slab\Core\Application');
		$router = new Router($container);

		$this->assertInstanceOf('Slab\Router\Router', $router);

	}



	/**
	 * Test executing a route
	 *
	 * @return void
	 **/
	public function testExecuterHit() {

		$closure = function(){};

		$container = m::mock('Slab\Core\Application');
		$container->shouldReceive('fireMethod')->with($closure, null, ['slug' => 'bar'])->once()->andReturn('response');

		$routes = m::mock('Slab\Router\RouteCollection');
		$routes->shouldReceive('getRoutes')->with('POST')->once()->andReturn([
			['path' => 'foo', 'handler' => 'myHandler1'],
			['path' => 'foo/{slug}', 'handler' => $closure],
			['path' => 'foo/bar/baz', 'handler' => 'myHandler3'],
		]);

		$request = m::mock('Slab\Core\Http\RequestInterface');
		$request->shouldReceive('getMethod')->once()->andReturn('POST');
		$request->shouldReceive('getPath')->once()->andReturn('foo/bar');

		$request->attributes = m::mock('Slab\Core\Http\Collection\AttributeCollection');
		$request->attributes->shouldReceive('set')->with(['slug' => 'bar'])->once()->andReturn(null);

		$router = new Router($container);

		$result = $router->execute($routes, $request);

		$this->assertEquals('response', $result);

	}



	/**
	 * Test finding against no routes
	 *
	 * @return void
	 **/
	public function testFinderEmpty() {

		$container = m::mock('Slab\Core\Application');

		$routes = m::mock('Slab\Router\RouteCollection');
		$routes->shouldReceive('getRoutes')->with('GET')->once()->andReturn([]);

		$request = m::mock('Slab\Core\Http\RequestInterface');
		$request->shouldReceive('getMethod')->once()->andReturn('GET');

		$router = new Router($container);

		$result = $router->findRoute($routes, $request);

		$this->assertEquals(null, $result);

	}



	/**
	 * Test finding a route
	 *
	 * @return void
	 **/
	public function testFinderHit() {

		$container = m::mock('Slab\Core\Application');

		$routes = m::mock('Slab\Router\RouteCollection');
		$routes->shouldReceive('getRoutes')->with('POST')->once()->andReturn([
			['path' => 'foo', 'handler' => 'myHandler1'],
			['path' => 'foo/{slug}', 'handler' => 'myHandler2'],
			['path' => 'foo/bar/baz', 'handler' => 'myHandler3'],
		]);

		$request = m::mock('Slab\Core\Http\RequestInterface');
		$request->shouldReceive('getMethod')->once()->andReturn('POST');
		$request->shouldReceive('getPath')->once()->andReturn('foo/bar');

		$router = new Router($container);

		$result = $router->findRoute($routes, $request);

		$this->assertEquals([
			'status' => 200,
			'route' => ['path' => 'foo/{slug}', 'handler' => 'myHandler2'],
			'params' => ['slug' => 'bar'],
		], $result);

	}



	/**
	 * Test finding no route
	 *
	 * @return void
	 **/
	public function testFinderMiss() {

		$container = m::mock('Slab\Core\Application');

		$routes = m::mock('Slab\Router\RouteCollection');
		$routes->shouldReceive('getRoutes')->with('PUT')->once()->andReturn([
			['path' => 'foo'],
			['path' => 'foo/bar/baz'],
			['path' => 'bar'],
		]);

		$request = m::mock('Slab\Core\Http\RequestInterface');
		$request->shouldReceive('getMethod')->once()->andReturn('PUT');
		$request->shouldReceive('getPath')->once()->andReturn('foo/bar');

		$router = new Router($container);

		$result = $router->findRoute($routes, $request);

		$this->assertEquals(null, $result);

	}



	/**
	 * Test calling an empty route handler
	 *
	 * @return void
	 **/
	public function testCallEmptyRouteHandler() {

		$container = m::mock('Slab\Core\Application');
		$router = new Router($container);

		$result = $router->callRoute([], []);

		$this->assertNull($result);

	}



	/**
	 * Test calling a closure route handler
	 *
	 * @return void
	 **/
	public function testCallClosureRouteHandler() {

		$closure = function(){};

		$container = m::mock('Slab\Core\Application');
		$container->shouldReceive('fireMethod')->with($closure, null, [])->once()->andReturn('response');

		$router = new Router($container);

		$result = $router->callRoute(['handler' => $closure], []);

		$this->assertEquals('response', $result);

	}



	/**
	 * Test calling a Class@method route handler
	 *
	 * @return void
	 **/
	public function testCallStringRouteHandler() {

		$container = m::mock('Slab\Core\Application');
		$container->shouldReceive('makeMethod')->with('MyClass@myMethod', [])->once()->andReturn('response');

		$router = new Router($container);

		$result = $router->callRoute(['handler' => 'MyClass@myMethod'], []);

		$this->assertEquals('response', $result);

	}



	/**
	 * Test calling a callable route handler
	 *
	 * @return void
	 **/
	public function testCallCallableRouteHandler() {

		$container = m::mock('Slab\Core\Application');

		$router = new Router($container);

		$result = $router->callRoute(['handler' => 'date_default_timezone_get'], []);

		$this->assertEquals(date_default_timezone_get(), $result);

	}



	/**
	 * Test calling an uncallable route handler
	 *
	 * @return void
	 **/
	public function testCallUncallableRouteHandler() {

		$container = m::mock('Slab\Core\Application');

		$router = new Router($container);

		$this->setExpectedException('RuntimeException');

		$result = $router->callRoute(['handler' => 'not_a_real_php_function'], []);

		$this->assertEquals(null, $result);

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
