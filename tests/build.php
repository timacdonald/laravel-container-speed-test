<?php

use Faker\Factory;

require __DIR__.'/../vendor/autoload.php';

/**
 * A bunch of helpers we use throughout the build process.
 */

$faker = Factory::create();

$stub = function ($name) {
    return file_get_contents(__DIR__."/{$name}_stub.php");
};

$create_test = function ($suite, $name, $content) {
    file_put_contents(__DIR__."/{$suite}/{$name}.php", $content);
};

$create_route_file = function ($name, $content) {
    file_put_contents(__DIR__."/../routes/{$name}.php", '<?php'.$content);
};

$build_routes = function ($properties) use ($stub) {
    return array_reduce($properties, function ($body, $properties) use ($stub) {
        return $body.array_reduce(array_keys($properties), function ($route, $property) use ($properties) {
            return str_replace("{{$property}}", $properties[$property], $route);
        }, $stub('route'));
    }, '');
};

/**
 * Generate the methods used in each of the Container and WithoutContainer
 * test suite files.
 */

$body = '';

for ($i = 0; $i < 15; $i++) {
    $body .= str_replace('{method_name}', "test_{$i}", $stub('method'));
}

/**
 * Generate the test files for both the Conatainer and WithoutContainer test suite.
 */

for ($i = 0; $i < 100; $i++) {
    $container = str_replace('{className}', "Test{$i}Test", $stub('container_test'));
    $without = str_replace('{className}', "Test{$i}Test", $stub('without_container_test'));

    $container = str_replace('{body}', $body, $container);
    $without = str_replace('{body}', $body, $without);

    $create_test('Container', "Test{$i}Test", $container);
    $create_test('WithoutContainer', "Test{$i}Test", $without);
}

/**
 * Generate the route properties to use when making the route files.
 */

$routes = [];

for ($i = 0; $i < 75; $i++) {
    $routes['web'][] = [
        'url' => $faker->unique()->regexify('[a-z]{4,10}-[a-z]{4,10}\/{[a-z]{4,10}}\/[a-z]{4,10}'),
        'name' => $faker->unique()->domainName,
        'method' => $faker->randomElement(['get', 'post', 'patch', 'delete']),
        'function' => $faker->randomElement(['index', 'create', 'edit', 'update', 'destroy']),
        'controller' => str_replace(' ', '', $faker->unique()->jobTitle.'Controller'),
    ];
}

$routes['api'] = array_map(function ($properties) {
    return array_merge($properties, [
        'url' => 'api/'.$properties['url'],
        'name' => 'api.'.$properties['name'],
    ]);
}, $routes['web']);

$create_route_file('web', $build_routes($routes['web']));
$create_route_file('api', $build_routes($routes['api']));
