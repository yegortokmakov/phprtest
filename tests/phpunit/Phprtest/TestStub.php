<?php

namespace Phprtest;

class TestStub extends TestSuite
{
    /**
     * @assert memoryUsage 1 3
     * @assert timeUsage 1
     * @provider providerStub
     */
    public function testStub(){}

    public function providerStub(){}
}