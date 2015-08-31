<?php

namespace Slab\Router;

use RuntimeException;

use Slab\Core\Container;
use Slab\Core\Http\RequestInterface;

/**
 * Router
 *
 * @package default
 * @author Luke Lanchester
 **/
class Router {


	/**
	 * @var Slab\Core\Container
	 **/
	protected $container;


	/**
	 * @var Slab\Core\Http\RequestInterface
	 **/
	protected $request;


	/**
	 * @var Slab\Router\RouteCollection
	 **/
	protected $routes;


	/**
	 * Constructor
	 *
	 * @param Slab\Core\Container
	 * @return void
	 **/
	public function __construct(Container $container) {

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

		if($result['status'] !== $dispatcher::FOUND) {
			return null;
		}

		if(!empty($result['params'])) {
			$this->request->attributes->set($result['params']);
		}

		return $result['route'];

	}



	/**
	 * Execute the middleware controllers on the given route
	 *
	 * @param array Route
	 * @return mixed Response
	 **/
	protected function executeRoute(array $route) {

		if(empty($route['handler'])) {
			return null;
		}

		$callback = $route['handler'];

		if(is_a($callback, 'Closure')) {
			return $callback->__invoke($this->request);
		} elseif(strpos($callback, '@') !== false) {
			return $this->container->makeMethod($callback);
			return 'app-dispatcher';
		} elseif(is_callable($callback)) {
			return call_user_func($callback, $this->request);
		}

		throw new RuntimeException('Unable to execute route handler');

	}



}
