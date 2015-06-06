<?php

namespace Phpperftest;

class PhpperftestTest extends \PHPUnit_Framework_TestCase
{
    protected $instance = null;

    public function setUp()
    {
        $this->instance = new Phpperftest();
    }

    public function testVersionString()
    {
        $this->assertContains(Phpperftest::VERSION, Phpperftest::versionString());
    }
}

