<?php

include 'vendor/autoload.php';
include 'vendor/wp-slab/slab-core/src/Autoloader.php';

$autoloader = new Slab\Core\Autoloader;
$autoloader->registerNamespace('Slab\Core', 'vendor/wp-slab/slab-core/src');
$autoloader->registerNamespace('Slab\Router', __DIR__ . '/src');
