<?php

namespace Phprtest;

class PhprtestTest extends \PHPUnit_Framework_TestCase
{
    protected $instance = null;

    public function setUp()
    {
        $this->instance = new Phprtest();
    }

    public function testVersionString()
    {
        $this->assertContains(Phprtest::VERSION, Phprtest::versionString());
    }
}

