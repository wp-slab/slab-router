<?php

namespace Slab\Router;

use Slab\Core\Http\RequestInterface;

/**
 * Router
 *
 * @package default
 * @author Luke Lanchester
 **/
class Router {


	/**
	 * @var Slab\Core\Http\RequestInterface
	 **/
	protected $request;


	/**
	 * @var Slab\Router\RouteCollection
	 **/
	protected $routes;


	/**
	 * Execute the router against a request
	 *
	 * @param Slab\Router\RouteCollection
	 * @param Slab\Core\Http\RequestInterface
	 * @return mixed Response
	 **/
	public function execute(RouteCollection $routes, RequestInterface $request) {

		$this->request = $request;
		$this->routes  = $routes;

		$route = $this->findRoute();
		if(!$route) {
			return null; // don't die, just let WordPress continue
		}

		return $this->executeRoute($route);

	}



	/**
	 * Find the route that matches the current request
	 *
	 * @return array Route
	 **/
	public function findRoute() {

		$dispatcher = new RouteDispatcher;

		$result = $dispatcher->dispatch($this->request, $this->routes);

		if($result[0] !== $dispatcher::FOUND) {
			return null;
		}

		if(!empty($result[2])) {
			$this->request->attributes->set($result[2]);
		}

		return $result[1];

	}



	/**
	 * Execute the middleware controllers on the given route
	 *
	 * @param array Route
	 * @return mixed Response
	 **/
	protected function executeRoute(array $route) {

		if(empty($route[1])) {
			return null;
		}

		$callbacks = $route[1];
		$response = null;

		$callFn = function() use(&$callbacks, &$response, &$callFn) {

			$callback = array_shift($callbacks);
			$fired = false;

			$nextFn = function() use(&$response, &$fired, &$callFn) {
				$fired = true;
				return $callFn();
			};

			$response = $callback->__invoke($this->request, $nextFn);

			if(is_null($response) and $fired === false) {
				$response = $next();
			}

			return $response;

		};

		return $callFn($callbacks);

	}



}
