<?php

namespace testname2;

use Phpperftest\TestSuite;

//@todo: class annotations inheritance
class DummyTest extends TestSuite
{
    /**
     * @assert memoryUsage 1 3
     * @assert timeUsage 1
     * @assert memoryPeakUsage 10 9999999
     */
    public function testSimple()
    {
        // echo 'simpleTest' . PHP_EOL;
        
    }

    /**
     * @assert memoryUsage 1 3
     * @assert timeUsage 1
     * @assert memoryPeakUsage 10
     */
    public function testSimple2()
    {
        // echo 'simpleTest' . PHP_EOL;
        
    }
}