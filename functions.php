<?php

namespace Slab\Router;

use Slab\Core\Http\ResponseInterface;

/**
 * Initialize Slab Router
 *
 * @param Slab\Core\Application
 * @return void
 **/
function slab_router_init($slab) {

	$slab->autoloader->registerNamespace('Slab\Router', SLAB_ROUTER_DIR . 'src');

	$slab->singleton('Slab\Router\RouteCollection', function(){
		$routes = new RouteCollection;
		do_action('slab_router_routes', $routes);
		return $routes;
	});
	$slab->alias('router', 'Slab\Router\RouteCollection'); // alias as router, not routes

}


/**
 * Trigger Slab Router dispatcher
 *
 * @param Slab\Core\Application
 * @return void
 **/
function slab_router_fire($slab) {

	if(defined('SLAB_CLI_BOOT')) {
		return;
	}

	$router = $slab->make('Slab\Router\Router');

	$response = $router->execute($slab->make('router'), $slab->make('request'));

	if($response === null) {
		return;
	}

	if($response instanceof ResponseInterface) {
		$response->serve();
	} else {
		echo $response;
	}

	die();

}
