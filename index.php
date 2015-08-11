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

	$routes = new Slab\Router\RouteCollection;

	$slab->singleton('Slab\Router\RouteCollection', $routes);
	$slab->alias('router', 'Slab\Router\RouteCollection'); // alias as router, not routes

}


// Fire
function slab_router_fire($slab) {

	$router = $slab->make('Slab\Router\Router');

	$response = $router->execute($slab->make('router'), $slab->make('request'));

	if($response === null) {
		return;
	}

	if($response instanceof Slab\Core\Http\ResponseInterface) {
		$response->serve();
	} else {
		echo $response;
	}

	die();

}
