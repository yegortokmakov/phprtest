# PHPRTest [![Latest Stable](https://poser.pugx.org/yegortokmakov/phprtest/version.png)](https://packagist.org/packages/yegortokmakov/phprtest) [![Build Status](https://travis-ci.org/yegortokmakov/phprtest.svg?branch=master)](https://travis-ci.org/yegortokmakov/phprtest)

PHPRTest is a simple resources usage tests for modern PHP applications. In a fashion of PHPUnit,
it allows developers to keep track of memory consumption and execution time of critical parts
of software. PHPRTest allows to set up soft and hard limits for each test suite and is
easy to integrate with your favorite CI solution.

Usage
-------

Here is a basic test of Symfony console application. In this test we run `import` command
and assert that execution time is less than 0.05 seconds, soft memory usage limit is 6 MB and hard memory
usage limit is 10 MB.

```php
<?php

class ImportTest extends \Phprtest\TestSuite
{
    /**
     * @assert memoryUsage 6.5M 10M
     * @assert timeUsage 0.05
     */
    public function testImportCommand()
    {
        $app = new \Symfony\Component\Console\Application('Myapp', 1);
        $app->add(new \Myapp\Command\Import('import'));
        $app->run(new ArgvInput(['myapp', 'import']));
    }
}
```

Running this test will produce similar results:

```
PHP Resources usage tests
--------------------------------

ImportTest
+-------------------------------+----------+-----------+-----------+---------+
|            METRIC             |  RESULT  | SOFTLIMIT | HARDLIMIT | STATUS  |
+-------------------------------+----------+-----------+-----------+---------+
| testImportCommand:memoryUsage | 7141330  | 6815744   | 10485760  | softHit |
| testImportCommand:timeUsage   | 0.0341   | 0.05      | 0.05      | ok      |
+-------------------------------+----------+-----------+-----------+---------+

Warnings:
testImportCommand:memoryUsage warning assertion for memoryUsage: 7141330 > 6291456

1 tests completed. 1 warnings, 0 failures.
```

To extract some part of the code from measurements (e.g. initialization of data),
you can use @provider annotation:

```php
<?php

class ImportTest extends \Phprtest\TestSuite
{
    /**
     * @assert memoryUsage 6M 10M
     * @assert timeUsage 0.05
     * @provider applicationProvider
     */
    public function testImportCommand($app)
    {
        $app->run(new ArgvInput(['myapp', 'import']));
    }

    public function applicationProvider()
    {
        $app = new \Symfony\Component\Console\Application('Myapp', 1);
        $app->add(new \Myapp\Command\Import('import'));

        return $app;
    }
}
```

To run same test multiple times, you can use @repeat annotation:

```php
<?php

class ImportTest extends \Phprtest\TestSuite
{
    /**
     * @assert memoryUsage 6M 10M
     * @assert timeUsage 0.05
     * @repeat 5
     */
    public function testImportCommand($app)
    {
        $app = new \Symfony\Component\Console\Application('Myapp', 1);
        $app->add(new \Myapp\Command\Import('import'));
        $app->run(new ArgvInput(['myapp', 'import']));
    }
}
```


Installation
------------

PHPRTest is available on Packagist ([yegortokmakov/phprtest](http://packagist.org/packages/yegortokmakov/phprtest))
and as such installable via [Composer](http://getcomposer.org/).

```bash
php composer.phar require yegortokmakov/phprtest
```

Or simply add a dependency on yegortokmakov/phprtest to your project's composer.json file
if you use Composer to manage the dependencies of your project. Here is a minimal example of a
composer.json file that just defines a development-time dependency on PHPRTest:

```json
{
    "require-dev": {
        "yegortokmakov/phprtest": "dev-master"
    }
}
```

Todo
-------

+ Limits for etalon test
+ CPU usage
+ Memory gain
+ Calculate average of runs
+ Statistic of results: warninng if they are bad
+ Time weights for mock calls

Author
-------

Yegor Tokmakov - <yegor@tokmakov.biz><br />
See also the list of [contributors](https://github.com/yegortokmakov/phprtest/contributors) who participated in this project.

License
-------

PHPRTest is licensed under the MIT License - see the LICENSE file for details
