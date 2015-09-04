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


// Includes
include SLAB_ROUTER_DIR . 'functions.php';


// Hooks
add_action('slab_init', 'Slab\Router\slab_router_init');
add_action('slab_loaded', 'Slab\Router\slab_router_fire', 20);
