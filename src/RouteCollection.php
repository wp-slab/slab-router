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
	 * @param mixed Args
	 * @return void
	 **/
	public function get($path, $args) {

		$this->on('GET', $path, $args);

	}



	/**
	 * Add a POST route
	 *
	 * @param string Path
	 * @param mixed Args
	 * @return void
	 **/
	public function post($path, $args) {

		$this->on('POST', $path, $args);

	}



	/**
	 * Add a PUT route
	 *
	 * @param string Path
	 * @param mixed Args
	 * @return void
	 **/
	public function put($path, $args) {

		$this->on('PUT', $path, $args);

	}



	/**
	 * Add a DELETE route
	 *
	 * @param string Path
	 * @param mixed Args
	 * @return void
	 **/
	public function delete($path, $args) {

		$this->on('DELETE', $path, $args);

	}



	/**
	 * Add a HEAD route
	 *
	 * @param string Path
	 * @param mixed Args
	 * @return void
	 **/
	public function head($path, $args) {

		$this->on('HEAD', $path, $args);

	}



	/**
	 * Add an OPTIONS route
	 *
	 * @param string Path
	 * @param mixed Args
	 * @return void
	 **/
	public function options($path, $args) {

		$this->on('OPTIONS', $path, $args);

	}



	/**
	 * Add a combined GET and POST route
	 *
	 * @param string Path
	 * @param mixed Args
	 * @return void
	 **/
	public function any($path, $args) {

		$this->on('GET', $path, $args);
		$this->on('POST', $path, $args);

	}



	/**
	 * Regsiter a route handler
	 *
	 * @param string Method
	 * @param string Path
	 * @param mixed Args
	 * @return void
	 **/
	public function on($method, $path, $args) {

		$method = strtoupper($method);

		if(!array_key_exists($method, $this->methods)) {
			throw new RuntimeException("Invalid route method: $method");
		}

		$path = trim($path, '/');

		$i = array_push($this->routes[$method], [$path, $args]);
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
