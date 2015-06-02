<?php

namespace testname;

use Phpperftest\TestSuite;

//@todo: class annotations inheritance
class DummyTest extends TestSuite
{
    /**
     * @assert memoryUsage 1 3
     * @assert timeUsage 1
     */
    public function testSimple()
    {
        // echo 'simpleTest' . PHP_EOL;
        
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