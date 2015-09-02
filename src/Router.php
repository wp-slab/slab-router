<?php

namespace Slab\Router;

use RuntimeException;

use Slab\Core\Application;
use Slab\Core\Http\RequestInterface;

/**
 * Router
 *
 * @package default
 * @author Luke Lanchester
 **/
class Router {


	/**
	 * @var Slab\Core\Application
	 **/
	protected $container;


	/**
	 * Constructor
	 *
	 * @param Slab\Core\Application
	 * @return void
	 **/
	public function __construct(Application $container) {

		$this->container = $container;

	}



	/**
	 * Execute the router against a request
	 *
	 * @param Slab\Router\RouteCollection
	 * @param Slab\Core\Http\RequestInterface
	 * @return mixed Response
	 **/
	public function execute(RouteCollection $routes, RequestInterface $request) {

		$result = $this->findRoute($routes, $request);
		if(!$result) {
			return null; // don't die, just let WordPress continue
		}

		if(!empty($result['params'])) {
			$request->attributes->set($result['params']);
		}

		return $this->callRoute($result['route'], $result['params']);

	}



	/**
	 * Find the route that matches the current request
	 *
	 * @param Slab\Router\RouteCollection
	 * @param Slab\Core\Http\RequestInterface
	 * @return array Result
	 **/
	public function findRoute(RouteCollection $routes, RequestInterface $request) {

		$dispatcher = new RouteDispatcher;

		$result = $dispatcher->dispatch($request, $routes);

		if($result['status'] !== $dispatcher::FOUND) {
			return null;
		}

		return $result;

	}



	/**
	 * Execute the middleware controllers on the given route
	 *
	 * @param array Route
	 * @param array Params
	 * @return mixed Response
	 **/
	public function callRoute(array $route, array $params) {

		if(empty($route['handler'])) {
			return null;
		}

		$callback = $route['handler'];

		if(is_a($callback, 'Closure')) {
			return $this->container->fireMethod($callback, null, $params);
		} elseif(strpos($callback, '@') !== false) {
			return $this->container->makeMethod($callback, $params);
		} elseif(is_callable($callback)) {
			return empty($params) ? call_user_func($callback) : call_user_func_array($callback, $params);
		}

		throw new RuntimeException('Unable to execute route handler');

	}



}
