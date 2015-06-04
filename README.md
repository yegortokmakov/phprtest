PHPPerfTest
-------

PHPPerfTest is a simple performance tests for modern PHP applications. In a fashion of PHPUnit,
it allows developers to keep track of memory consumption and execution time of critical parts
of software. PHPPerfTest allows to set up soft and hard limits for each test suite and is
easy to integrate with your favorite CI solution.

Usage
-------

Here is a basic test of Symfony console application. In this test we run `import` command
and execution time is less than 0.05 seconds, soft memory usage limit is 6 MB and hard memory
usage limit is 10 MB.

```php
<?php

class ImportTest extends \Phpperftest\TestSuite
{
    /**
     * @assert memoryUsage 6M 10M
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
PHP Performance Tests
--------------------------------

ImportTest
+-------------------------------+----------+-----------+-----------+---------+
|            METRIC             |  RESULT  | SOFTLIMIT | HARDLIMIT | STATUS  |
+-------------------------------+----------+-----------+-----------+---------+
| testImportCommand:memoryUsage | 7141330  | 6291456   | 10485760  | softHit |
| testImportCommand:timeUsage   | 0.0341   | 0.05      | 0.05      | ok      |
+-------------------------------+----------+-----------+-----------+---------+

Warnings:
testImportCommand:memoryUsage warning assertion for memoryUsage: 7141330 > 6291456

1 tests completed. 1 warnings, 0 failures.
```

Installation
------------

PHPPerfTest is available on Packagist ([yegortokmakov/phpperftest](http://packagist.org/packages/yegortokmakov/phpperftest))
and as such installable via [Composer](http://getcomposer.org/).

```bash
php composer.phar require yegortokmakov/phpperftest
```

Or simply add a dependency on yegortokmakov/phpperftest to your project's composer.json file
if you use Composer to manage the dependencies of your project. Here is a minimal example of a
composer.json file that just defines a development-time dependency on PHPPerfTest:

```json
{
    "require-dev": {
        "yegortokmakov/phpperftest": "dev-master"
    }
}
```

Author
-------

Yegor Tokmakov - <yegor@tokmakov.biz><br />
See also the list of [contributors](https://github.com/yegortokmakov/phpperftest/contributors) who participated in this project.

License
-------

PHPPerfTest is licensed under the MIT License - see the LICENSE file for details

Todo
-------

+ Provider annotation
+ Time weights for mock calls
+ Multiple runs of one test (average)
+ Refactor table renderer
+ Support for 2,5M unit in limits
+ Limits for etalon test
+ Test coverage
+ Move tests to examples
