<?php

require __DIR__.'/../vendor/autoload.php';

$builder = new Tests\Builder;

/**
 * Generate the test files for both the Conatainer and WithoutContainer test suite.
 */
$builder->createSimpleTests(1500);

/**
 * Generate the route properties to use when making the route files.
 */
$routes = $builder->loadRoutes(100);

/**
 * Populate the routes files
 */
$builder->createRouteFile("web", $builder->buildRoutes($routes["web"]));
$builder->createRouteFile("api", $builder->buildRoutes($routes["api"]));

/**
 * Create the controllers
 */
$builder->createControllerFiles("web", $routes["web"]);
$builder->createControllerFiles("api", $routes["api"]);

/**
 * Create the HTTP Tests
 */
$builder->createHttpTests("web", $routes["web"]);
$builder->createHttpTests("api", $routes["api"]);
