<?php

namespace Tests;

use Faker\Factory;

class Builder
{
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function stub($name)
    {
        return file_get_contents(__DIR__."/stubs/{$name}_stub.php");
    }

    public function createTest(string $suite, string $name, string $content)
    {
        file_put_contents(__DIR__."/{$suite}/{$name}.php", $content);
    }

    public function createRouteFile(string $name, string $content)
    {
        file_put_contents(__DIR__."/../routes/{$name}.php", '<?php'.$content);
    }

    public function createControllerFiles(string $group, $routes)
    {
        foreach ($routes as $route) {
            $this->createControllerFile($route);
        }
    }

    public function createControllerFile($route)
    {
        $name = $route["controller"];

        $content = $this->stub('controller');
        $content = str_replace('{className}', $name, $content);

        file_put_contents(__DIR__."/../app/Http/Controllers/{$name}.php", $content);
    }

    public function buildRoutes($properties)
    {
        return array_reduce($properties, function ($body, $properties) {
            return $body.array_reduce(array_keys($properties), function ($route, $property) use ($properties) {
                return str_replace("{{$property}}", $properties[$property], $route);
            }, $this->stub('route'));
        }, '');
    }

    public function createRoutes($count)
    {
        $routes = [];

        for ($i = 0; $i < $count; $i++) {
            $routes['web'][] = [
                'url' => $this->faker->unique()->regexify('[a-z]{4,10}-[a-z]{4,10}\/{[a-z]{4,10}}\/[a-z]{4,10}'),
                'name' => $this->faker->unique()->domainName,
                'method' => $this->faker->randomElement(['get', 'post', 'patch', 'delete']),
                'function' => $this->faker->randomElement(['index', 'create', 'edit', 'update', 'destroy']),
                'controller' => preg_replace('/[^A-Z]/i', '', $this->faker->unique()->jobTitle.'Controller'),
            ];
        }

        $routes['api'] = array_map(function ($properties) {
            return array_merge($properties, [
                // The middleware already prefixes with /api
                'url' => $properties['url'],
                'name' => 'api.'.$properties['name'],
            ]);
        }, $routes['web']);

        return $routes;
    }

    public function loadRoutes($count)
    {
        if ($this->routeCacheExists() && $this->cachedRouteCount() !== $count) {
            unlink($this->routeCachePath());
        }

        if (! $this->routeCacheExists()) {
            $contents = var_export($this->createRoutes($count), true);

            file_put_contents($this->routeCachePath(), "<?php return {$contents};");
        }

        return $this->cachedRoutes();
    }

    private function cachedRouteCount()
    {
        return count($this->cachedRoutes()['web']);
    }

    private function cachedRoutes()
    {
        return require $this->routeCachePath();
    }

    private function routeCacheExists()
    {
        return file_exists($this->routeCachePath());
    }

    private function routeCachePath()
    {
        return __DIR__ . "/meta/routes.php";
    }

    public function createSimpleTests($count)
    {
        /**
         * Generate the methods used in each of the Container and WithoutContainer
         * test suite files.
         */

        $body = '';

        for ($i = 0; $i < $count; $i++) {
            $body .= str_replace('{method_name}', "test_{$i}", $this->stub('simple_method'));
        }

        /**
         * Generate the test files for both the Conatainer and WithoutContainer test suite.
         */

        $container = str_replace('{className}', "Test{$i}Test", $this->stub('container_test'));
        $without = str_replace('{className}', "Test{$i}Test", $this->stub('without_container_test'));

        $container = str_replace('{body}', $body, $container);
        $without = str_replace('{body}', $body, $without);

        $this->createTest('Simple', "ContainerTest", $container);
        $this->createTest('Simple', "WithoutContainer", $without);
    }

    public function createHttpTests(string $group, $routes)
    {
        $body = "";

        foreach ($routes as $i => $route) {
            $body .= $this->methodStubForRoute($i, $route);
        }

        $group = ucfirst($group);
        $className =  "TestHttp{$group}Test";

        $contents = $this->stub("http_test");

        $contents = str_replace("{body}", $body, $contents);
        $contents = str_replace("{className}", $className, $contents);

        $this->createTest("Http", $className, $contents);
    }

    private function methodStubForRoute($i, $route)
    {
        $contents = $this->stub('route_method');
        $contents = str_replace('{method_name}', "test_{$i}", $contents);
        $contents = str_replace('{http_path}', $route["url"], $contents);
        $contents = str_replace('{http_method}', $route["method"], $contents);

        return $contents;
    }
}
