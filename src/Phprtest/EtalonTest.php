<?php

namespace Phprtest;

use Phprtest\TestSuite;

//@todo: class annotations inheritance
class EtalonTest extends TestSuite
{
    /**
     * @assert timeUsage 1
     */
    public function testTime()
    {
        $a = $this->fibonacci(9991);
    }

    private function fibonacci($n, $first = 0, $second = 1)
    {
        $fib = [$first,$second];
        for($i=1; $i<$n; $i++) $fib[] = $fib[$i]+$fib[$i-1];
        return $fib;
    }
}