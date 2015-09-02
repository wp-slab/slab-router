<?php

use Slab\Router\RouteCollection;

/**
 * Test RouteCollection
 *
 * @package default
 * @author Luke Lanchester
 **/
class RouteCollectionTest extends PHPUnit_Framework_TestCase {


	/**
	 * Test can instantiate an empty route collection
	 *
	 * @return void
	 **/
	public function testCanInstantiateCollection() {

		$collection = new RouteCollection;
		$this->assertInstanceOf('Slab\Router\RouteCollection', $collection);

	}



}
