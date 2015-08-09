<?php

namespace Slab\Router;

use RuntimeException;

/**
 * Route Collection
 *
 * @package default
 * @author Luke Lanchester
 **/
class RouteCollection {


	/**
	 * @var array Routes by method
	 **/
	protected $routes = [
		'*'       => [],
		'GET'     => [],
		'POST'    => [],
		'PUT'     => [],
		'DELETE'  => [],
		'HEAD'    => [],
		'OPTIONS' => [],
	];


	/**
	 * @var array Allowed methods
	 **/
	protected $methods = ['GET'=>1, 'POST'=>1, 'PUT'=>1, 'DELETE'=>1, 'HEAD'=>1, 'OPTIONS'=>1];


	/**
	 * Add a GET route
	 *
	 * @param string Path
	 * @return void
	 **/
	public function get($path) {

		$callbacks = func_get_args();
		array_shift($callbacks);
		$this->addRoute('GET', $path, $callbacks);

	}



	/**
	 * Add a POST route
	 *
	 * @param string Path
	 * @return void
	 **/
	public function post($path) {

		$callbacks = func_get_args();
		array_shift($callbacks);
		$this->addRoute('POST', $path, $callbacks);

	}



	/**
	 * Add a PUT route
	 *
	 * @param string Path
	 * @return void
	 **/
	public function put($path) {

		$callbacks = func_get_args();
		array_shift($callbacks);
		$this->addRoute('PUT', $path, $callbacks);

	}



	/**
	 * Add a DELETE route
	 *
	 * @param string Path
	 * @return void
	 **/
	public function delete($path) {

		$callbacks = func_get_args();
		array_shift($callbacks);
		$this->addRoute('DELETE', $path, $callbacks);

	}



	/**
	 * Add a HEAD route
	 *
	 * @param string Path
	 * @return void
	 **/
	public function head($path) {

		$callbacks = func_get_args();
		array_shift($callbacks);
		$this->addRoute('HEAD', $path, $callbacks);

	}



	/**
	 * Add an OPTIONS route
	 *
	 * @param string Path
	 * @return void
	 **/
	public function options($path) {

		$callbacks = func_get_args();
		array_shift($callbacks);
		$this->addRoute('OPTIONS', $path, $callbacks);

	}



	/**
	 * Regsiter a route handler
	 *
	 * @param string Method
	 * @param string Path
	 * @param array Callbacks
	 * @return void
	 **/
	public function addRoute($method, $path, array $callbacks) {

		$method = strtoupper($method);

		if(!array_key_exists($method, $this->methods)) {
			throw new RuntimeException("Invalid route method: $method");
		}

		$path = trim($path, '/');

		$i = array_push($this->routes[$method], [$path, $callbacks]);
		$this->routes['*'][] = [$method, $path, $i - 1];

	}



	/**
	 * Get all routes
	 *
	 * @return array Routes
	 **/
	public function getRoutes($method = null) {

		if($method !== null) {
			return array_key_exists($method, $this->routes) ? $this->routes[$method] : [];
		}

		return []; // @todo

	}



}
