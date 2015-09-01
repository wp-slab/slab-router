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
			return ['status' => static::NOT_FOUND];
		}

		$path = trim($request->getPath(), '/');

		foreach($routes as $route) {

			$match = $this->match($path, $route);

			if($match !== false) {
				return ['status' => static::FOUND, 'route' => $route, 'params' => $match];
			}

		}

		return ['status' => static::NOT_FOUND];

	}



	/**
	 * Does a path match against a route
	 *
	 * @param string Path
	 * @param array Route info
	 * @return false|array Match info
	 **/
	public function match($path, array $route) {

		if($route['path'] === $path) {
			return [];
		}

		if(strpos($route['path'], '{') === false) {
			return false;
		}

		$matches = [];
		list($pattern, $keys) = $this->compilePattern($route['path']);
		$match = preg_match($pattern, $path, $matches);

		if($match !== 1) {
			return false;
		}

		return array_intersect_key($matches, $keys);

	}



	/**
	 * Convert a path into a regex pattern
	 *
	 * @param string Path
	 * @return array [Regex pattern, keys]
	 **/
	public function compilePattern($path) {

		$pattern = "|^$path$|";
		$keys = [];

		$pattern = preg_replace_callback('|{(?<key>[^}]+)}|',
			function($matches) use(&$keys) {
				$keys[$matches['key']] = null;
				return "(?<{$matches['key']}>[a-zA-Z0-9-_]+)";
			},
			$pattern
		);

		// @todo optional params
		// @todo override regex pattern

		return [$pattern, $keys];

	}



}
