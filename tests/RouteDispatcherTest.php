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
			['path' => '', 'handler' => 'myHandler1'],
			['path' => 'foo', 'handler' => 'myHandler2'],
			['path' => 'foo/bar/baz', 'handler' => 'myHandler3'],
			['path' => 'bar', 'handler' => 'myHandler4'],
		]);

		$dispatcher = new RouteDispatcher;

		$result = $dispatcher->dispatch($request, $routes);

		$this->assertEquals(['status' => 404], $result);

	}



	/**
	 * Test matching a route against a path
	 *
	 * @param string Request URL path
	 * @param array Route path
	 * @param array Expected result
	 * @return void
	 * @dataProvider matcherProvider
	 **/
	public function testMatcher($request_path, $route_path, $test_result) {

		$dispatcher = new RouteDispatcher;

		$result = $dispatcher->match($request_path, ['path' => $route_path]);

		$this->assertEquals($test_result, $result);

	}



	/**
	 * Provide data for matcher
	 *
	 * @return array Data
	 **/
	public function matcherProvider() {

		return [

			['', '', []],
			['foo', '', false],
			['', 'foo', false],
			['foo', 'foo', []],
			['fo', 'foo', false],
			['fooo', 'foo', false],
			['foo', 'foo/bar', false],
			['foo/bar', 'foo/bar', []],
			['foo/bar/baz', 'foo/bar', false],

			['foo/bar', 'foo/{slug}', ['slug' => 'bar']],
			['foo/bar/baz', 'foo/{slug}/{slug2}', ['slug' => 'bar', 'slug2' => 'baz']],
			['foo', 'foo/{slug?}', []],
			['foo/bar', 'foo/{slug?}', ['slug' => 'bar']],

		];

	}



	/**
	 * Test compiling patterns
	 *
	 * @param string Input path
	 * @param string Compiled pattern
	 * @param array Pattern keys
	 * @return void
	 * @dataProvider patternProvider
	 **/
	public function testPatternCompiler($path, $test_pattern, $test_keys) {

		$dispatcher = new RouteDispatcher;

		list($pattern, $keys) = $dispatcher->compilePattern($path);

		$this->assertEquals($test_pattern, $pattern);
		$this->assertEquals($test_keys, array_keys($keys));

	}



	/**
	 * Provide data for compiler
	 *
	 * @return array Data
	 **/
	public function patternProvider() {

		return [
			['', '|^$|', []],
			['foo', '|^foo$|', []],
			['123', '|^123$|', []],
			['foo/bar', '|^foo/bar$|', []],
			['{slug}', '|^(?<slug>[a-zA-Z0-9-_]+)$|', ['slug']],
			['{slug}/{slug2}', '|^(?<slug>[a-zA-Z0-9-_]+)/(?<slug2>[a-zA-Z0-9-_]+)$|', ['slug', 'slug2']],
			['{num:[0-9]+}', '|^(?<num>[0-9]+)$|', ['num']],
			['{num:[0-9]+?}', '|^(?<num>[0-9]+)?$|', ['num']],
			['{slug?}', '|^(?<slug>[a-zA-Z0-9-_]+)?$|', ['slug']],
			['foo/{slug}', '|^foo/(?<slug>[a-zA-Z0-9-_]+)$|', ['slug']],
			['foo/{slug?}', '|^foo(/(?<slug>[a-zA-Z0-9-_]+))?$|', ['slug']],
			['{slug}/foo', '|^(?<slug>[a-zA-Z0-9-_]+)/foo$|', ['slug']],
			['{slug}/{slug2}', '|^(?<slug>[a-zA-Z0-9-_]+)/(?<slug2>[a-zA-Z0-9-_]+)$|', ['slug', 'slug2']],
			['{slug}/{slug2?}', '|^(?<slug>[a-zA-Z0-9-_]+)(/(?<slug2>[a-zA-Z0-9-_]+))?$|', ['slug', 'slug2']],
		];

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
