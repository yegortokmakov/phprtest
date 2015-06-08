<?php

namespace Phprtest;

class TestStub extends TestSuite
{
    /**
     * @assert memoryUsage 10 20
     * @assert timeUsage 5
     * @provider providerStub
     */
    public function testStub(){}

    public function providerStub(){}
}