<?php
/*
Plugin Name: Slab &mdash; Router
Plugin URI: http://www.wp-slab.com/components/router
Description: The Slab Router component. Create your own routes and controllers dynamically.
Version: 1.0.0
Author: Slab
Author URI: http://www.wp-slab.com
Created: 2014-06-30
Updated: 2015-08-08
Repo URI: github.com/wp-slab/slab-router
Requires: slab-core
*/


// Define
define('SLAB_ROUTER_INIT', true);
define('SLAB_ROUTER_DIR', plugin_dir_path(__FILE__));
define('SLAB_ROUTER_URL', plugin_dir_url(__FILE__));


// Hooks
add_action('slab_init', 'slab_router_init');
add_action('slab_loaded', 'slab_router_fire', 20);


// Init
function slab_router_init($slab) {

	$slab->autoloader->registerNamespace('Slab\Router', SLAB_ROUTER_DIR . 'src');

	$router = new Slab\Router\RouteCollection;

	$slab->singleton('Slab\Router\RouteCollection', $router);
	$slab->alias('router', 'Slab\Router\RouteCollection');

}


// Fire
function slab_router_fire($slab) {

	$dispatcher = new Slab\Router\RouteDispatcher;

	$request = $slab->request;
	$routes = $slab->router;

	$result = $dispatcher->dispatch($request, $routes);

	if($result[0] !== $dispatcher::FOUND) {
		die('404');
	}

	$route = $result[1];

	if(!empty($result[2])) {
		$request->attributes->set($result[2]);
	}

	_var_dump($route);
	die();

}
