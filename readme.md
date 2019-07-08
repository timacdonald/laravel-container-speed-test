# Speed test the Laravel container during testing

This repo has been setup as a way to test the time it takes for Laravel's container to boot during a test suite run. This is obviously a flawed process as with most (all?) benchmarks.

If anyone is looking to make the Container faster during tests, this might be a good place to start!

## Getting started

Best place to start is probably just running the build script and checking out the test suites it creates:

```
php -f tests/build.php
```

You can modify the contents of the build script to change the number of tests and routes generated.
