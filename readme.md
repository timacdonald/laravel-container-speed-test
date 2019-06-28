# Speed test the Laravel container during testing

This repo has been setup as a way to test the time it takes for Laravel's container to boot during a test suite run. This is obviously a flawed process as with most (all?) benchmarks.

If anyone is looking to make the Container faster during tests, this might be a good place to start!

## Running the test suites

To clone, composer install, and run both suites, you can use the following script in your terminal

```
git clone https://github.com/timacdonald/laravel-container-speed-test:5.5
cd laravel-container-speed-test
composer install
vendor/bin/phpunit --testsuite=WithoutContainer --repeat=3
vendor/bin/phpunit --testsuite=Container --repeat=3
```

We are running each suite 3 times to normalise the PHPUnit startup time.

## Reporting your results

Would love you to run the tests and let me know how long they take, along with some additional information:

- PHP version
- Operating system version
- Xdebug on / off (would love results from both if you have the time)

[Send your results over to my Twitter post](https://twitter.com/timacdonald87/status/1144067916146462720).

If you have access to multiple machines / platforms, I would love to see the results from them as well...but I might be stretching the friendship at that point 😁

## The suites

Each suite contains the same test files and methods but they extend different `TestCase` classes. Each suite contains 100 test files, each file containing 15 test methods, resulting in 1,500 tests in total per suite.

The container test suite extends the `TestCase` class the comes out of the box with Laravel, and the other extends the PHPUnit `TestCase` class directly.

## Build script

If you want to make adjustments, check out the build script in `tests/build.php`. You can run that script to regenerate the test files with the following...

```
$ php -f tests/build.php
```

## Results

I'm currently (on my machine) seeing the following average times with the repo in its current state:

- Container: 10.49s
- WithoutContainer: 0.289s
