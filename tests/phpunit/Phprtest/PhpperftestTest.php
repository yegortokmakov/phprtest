<?php

namespace Phprtest;

class PhprtestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Phprtest\Phprtest */
    protected $instance = null;

    public function setUp()
    {
        $this->instance = new Phprtest();
    }

    public function testVersionString()
    {
        $this->assertContains(Phprtest::VERSION, Phprtest::versionString());
    }

    public function testProcessResults()
    {
        $printer = $this->getMock('\Phprtest\Printer\PrinterInterface');
        $printer->expects($this->once())->method('render')->will($this->returnValue('value'));

        $this->instance->setPrinter($printer);

        $this->assertEquals('value', $this->instance->processResults());
    }

    public function testRun()
    {
        $this->instance->run('\Phprtest\TestStub');
    }
}
