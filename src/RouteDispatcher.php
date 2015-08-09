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
	 * Dispatch a request against a collection of routes
	 *
	 * @param Slab\Core\Http\RequestInterface
	 * @param Slab\Router\RouteCollection
	 * @return array [Code, Data]
	 **/
	public function dispatch(RequestInterface $request, RouteCollection $collection) {

		$method = $request->getMethod();

		$routes = $collection->getRoutes($method);

		if(empty($routes)) {
			return [static::NOT_FOUND];
		}

		$path = trim($request->getPath(), '/');

		foreach($routes as $route) {

			$match = $this->match($path, $route);

			if($match !== false) {
				return [static::FOUND, $route, $match];
			}

		}

		return [static::NOT_FOUND];

	}



	/**
	 * Does a path match against a route
	 *
	 * @param string Path
	 * @param array Route info
	 * @return false|array Match info
	 **/
	public function match($path, array $route) {

		// _var_dump($path, $route[0], $route[0] === $path);
		// _print_r($route);

		if($route[0] === $path) {
			return null;
		}

		// @todo regex

		return false;

	}



}
