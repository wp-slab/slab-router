<?php

namespace Slab\Router;

use Slab\Core\Http\RequestInterface;

/**
 * Route Dispatcher
 *
 * @package default
 * @author Luke Lanchester
 **/
class RouteDispatcher {


	/**
	 * @var int Route found constant
	 **/
	const FOUND = 200;


	/**
	 * @var int Route not found constant
	 **/
	const NOT_FOUND = 404;


	/**
	 * @var string Default regex for params
	 **/
	protected $default_regex = '[a-zA-Z0-9-_]+';


	/**
	 * Dispatch a request against a collection of routes
	 *
	 * @param Slab\Core\Http\RequestInterface
	 * @param Slab\Router\RouteCollection
	 * @return array [Code, Data]
	 **/
	public function dispatch(RequestInterface $request, RouteCollection $collection) {

		$method = $request->getMethod();
		$path   = $request->getPath();
		$routes = $collection->getRoutes($method);

		$dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) use($method, $routes) {

			foreach($routes as $route) {
				$r->addRoute($method, $route['path'], $route);
			}

		});

		$result = $dispatcher->dispatch($method, trim($path, '/'));

		switch ($result[0]) {

			case \FastRoute\Dispatcher::FOUND:
				return ['status' => static::FOUND, 'route' => $result[1], 'params' => $result[2]];

			case \FastRoute\Dispatcher::NOT_FOUND:
			case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
			default:
				return ['status' => static::NOT_FOUND];
		}

	}



}
