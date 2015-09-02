<?php

include 'vendor/autoload.php';

include 'wp-content/plugins/slab-core/src/Autoloader.php';
(new Slab\Core\Autoloader)->registerNamespace('Slab\Router', __DIR__ . '/src');
