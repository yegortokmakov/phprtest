<?php

namespace Phprtest;

class TestSutieTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Phprtest\TestSuite */
    protected $instance = null;

    public function setUp()
    {
        $this->instance = new TestSuite();
    }

    public function testGetMockObjectGenerator()
    {
        $class = new \ReflectionClass('\Phprtest\TestSuite');
        $method = $class->getMethod('getMockObjectGenerator');
        $method->setAccessible(true);
        $generator = $method->invokeArgs($this->instance, []);

        $this->assertInstanceOf('PHPUnit_Framework_MockObject_Generator', $generator);
    }

    public function testGetMock()
    {
        $className = 'class';
        $methods   = ['method1', 'method2'];
        $arguments = ['a', 'b', 'c'];
        $mockClassName = 'className';
        $callOriginalConstructor = false;
        $callOriginalClone = false;
        $callAutoload = false;
        $cloneArguments = true;
        $callOriginalMethods = true;

        $expectedReturn = 'testValue';

        $generator = $this->getMock('\PHPUnit_Framework_MockObject_Generator');
        $generator->expects($this->once())->method('getMock')
            ->with($className, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $cloneArguments, $callOriginalMethods)
            ->will($this->returnValue($expectedReturn));

        $class = new \ReflectionClass('\Phprtest\TestSuite');
        $method = $class->getMethod('setMockObjectGenerator');
        $method->setAccessible(true);
        $method->invokeArgs($this->instance, [$generator]);

        $method = $class->getMethod('getMock');
        $method->setAccessible(true);
        $actualReturn = $method->invokeArgs($this->instance, [$className, $methods, $arguments, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload, $cloneArguments, $callOriginalMethods]);

        $this->assertEquals($expectedReturn, $actualReturn);
    }
}
