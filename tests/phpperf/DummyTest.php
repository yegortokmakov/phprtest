<?php

namespace testname;

use Phpperftest\TestSuite;

class DummyTest extends TestSuite
{
    /**
     * @assert memoryUsage 1 3
     * @assert timeUsage 1
     * @provider simpleProvider
     */
    public function testSimple($a, $s, $d)
    {
    }

    public function simpleProvider()
    {
        return [1, 2, 3];
    }

    /**
     * @assert memoryUsage 1 3M
     * @assert timeUsage 1
     */
    public function testSimple2()
    {
        // echo 'simpleTest' . PHP_EOL;
        
    }
}