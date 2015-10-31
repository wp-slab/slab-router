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
		$path   = $request->getPathInfo();
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


	/**
	 * Dispatch a request against a collection of routes
	 *
	 * @param Slab\Core\Http\RequestInterface
	 * @param Slab\Router\RouteCollection
	 * @return array [Code, Data]
	 **/
	public function dispatch2(RequestInterface $request, RouteCollection $collection) {

		$method = $request->getMethod();

		$routes = $collection->getRoutes($method);

		if(empty($routes)) {
			return ['status' => static::NOT_FOUND];
		}

		$path = trim($request->getPathInfo(), '/');

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
		$has_optional_before = false;

		$patterFn = function($matches) use(&$keys, &$has_optional_before) {

			$key = $matches['key'];

			$has_slash = ($matches[0][0] === '/');

			if(substr($key, -1) === '?') {
				$key = substr($key, 0, -1);
				$is_optional = true;
				$has_optional_before = true;
			} else {
				// @todo closing optional parentheses must be after all other patterns
				$is_optional = $has_optional_before;
			}

			if(strpos($key, ':') !== false) {
				list($key, $regex) = explode(':', $key, 2);
			} else {
				$regex = $this->default_regex;
			}

			$keys[$key] = null;
			$pattern = "(?<$key>$regex)";

			if($is_optional) {
				if($has_slash) {
					return "(/$pattern)?";
				} else {
					return "$pattern?";
				}
			} elseif($has_slash) {
				return "/$pattern";
			} else {
				return $pattern;
			}


		};

		$pattern = preg_replace_callback('|/?{(?<key>[^}]+)}|', $patterFn, $pattern);

		return [$pattern, $keys];

	}



}
