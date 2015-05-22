<?php

use Phpperftest\TestSuite;

//@todo: class annotations inheritance
class DummyTest extends TestSuite
{
    /**
     * @memoryUsage 2 3
     * @timeUsage 1
     */
    public function testSimple()
    {
        echo 'simpleTest' . PHP_EOL;
    }
}