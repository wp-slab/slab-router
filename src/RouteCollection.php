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
	 * @param mixed Handler
	 * @return void
	 **/
	public function get($path, $handler) {

		$this->addRoute('GET', $path, $handler);

	}



	/**
	 * Add a POST route
	 *
	 * @param string Path
	 * @param mixed Handler
	 * @return void
	 **/
	public function post($path, $handler) {

		$this->addRoute('POST', $path, $handler);

	}



	/**
	 * Add a PUT route
	 *
	 * @param string Path
	 * @param mixed Handler
	 * @return void
	 **/
	public function put($path, $handler) {

		$this->addRoute('PUT', $path, $handler);

	}



	/**
	 * Add a DELETE route
	 *
	 * @param string Path
	 * @param mixed Handler
	 * @return void
	 **/
	public function delete($path, $handler) {

		$this->addRoute('DELETE', $path, $handler);

	}



	/**
	 * Add a HEAD route
	 *
	 * @param string Path
	 * @param mixed Handler
	 * @return void
	 **/
	public function head($path, $handler) {

		$this->addRoute('HEAD', $path, $handler);

	}



	/**
	 * Add an OPTIONS route
	 *
	 * @param string Path
	 * @param mixed Handler
	 * @return void
	 **/
	public function options($path, $handler) {

		$this->addRoute('OPTIONS', $path, $handler);

	}



	/**
	 * Regsiter a route handler
	 *
	 * @param string Method
	 * @param string Path
	 * @param mixed Handler
	 * @return void
	 **/
	public function addRoute($method, $path, $handler) {

		$method = strtoupper($method);

		if(!array_key_exists($method, $this->methods)) {
			throw new RuntimeException("Invalid route method: $method");
		}

		$path = trim($path, '/');

		$i = array_push($this->routes[$method], [
			'path'    => $path,
			'handler' => $handler,
		]);

		$this->routes['*'][] = [
			'method' => $method,
			'index'  => $i - 1,
			'path'   => $path,
		];

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

		return []; // @todo get all routes

	}



}
