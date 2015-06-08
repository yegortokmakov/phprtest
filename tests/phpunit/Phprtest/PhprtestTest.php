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

    public function testIsFailureFalse()
    {
        $this->assertFalse($this->instance->isFailure());
    }

    public function testSetGetProfiler()
    {
        $profiler = $this->getMock('\Phprtest\ProfilerInterface');

        $this->instance->setProfiler($profiler);

        $this->assertEquals($profiler, $this->instance->getProfiler());
    }

    public function testProcessResults()
    {
        $printer = $this->getMock('\Phprtest\Printer\PrinterInterface');
        $printer->expects($this->once())->method('render')->will($this->returnValue('value'));

        $this->instance->setPrinter($printer);

        $this->assertEquals($printer, $this->instance->getPrinter());

        $this->assertEquals('value', $this->instance->processResults());
    }

    public function testRunHardHit()
    {
        $maxMemory = 15;
        $timeUsed = 42;

        $expectedResult = array(
            '\\Phprtest\\TestStub' => array(
                'testStub' => array(
                    1 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                            'softLimit' => 10,
                            'hardLimit' => 20,
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                            'softLimit' => '5',
                            'hardLimit' => '5',
                        ),
                    ),
                ),
            ),
        );

        $expectedResultChecked = array(
            '\\Phprtest\\TestStub' => array(
                'testStub' => array(
                    1 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                            'softLimit' => 10,
                            'hardLimit' => 20,
                            'status' => 'softHit',
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                            'softLimit' => '5',
                            'hardLimit' => '5',
                            'status' => 'hardHit',
                        ),
                    ),
                ),
            ),
        );

        $expectedStatus = array(
            'tests' => 0,
            'softHits' => array(),
            'hardHits' => array(),
        );

        $expectedStatusChecked = array(
            'tests' => 1,
            'softHits' => ['\\Phprtest\\TestStub:testStub warning assertion for memoryUsage: 15.000000 > 10'],
            'hardHits' => ['\\Phprtest\\TestStub:testStub failed assertion for timeUsage: 42.000000 > 5'],
        );

        $expectedOutput = 'output';

        $printer = $this->getMock('\Phprtest\Printer\PrinterInterface');
        $printer->expects($this->at(0))->method('render')
            ->with($expectedResult, $expectedStatus)
            ->will($this->returnValue($expectedOutput));
        $printer->expects($this->at(1))->method('render')
            ->with($expectedResultChecked, $expectedStatusChecked)
            ->will($this->returnValue($expectedOutput));

        $profiler = $this->getMock('\Phprtest\ProfilerInterface');
        $profiler->expects($this->any())->method('getMaxMemory')
            ->will($this->returnValue($maxMemory));
        $profiler->expects($this->any())->method('getTimeUsed')
            ->will($this->returnValue($timeUsed));

        $this->instance->setProfiler($profiler);
        $this->instance->setPrinter($printer);
        $this->instance->run('\Phprtest\TestStub');

        $this->assertEquals($expectedOutput, $this->instance->processResults(), "", 0.1, 5);

        $this->instance->checkLimits();

        $this->assertEquals($expectedOutput, $this->instance->processResults(), "", 0.1, 5);

        $this->assertTrue($this->instance->isFailure());
    }

    public function testRunHardOk()
    {
        $maxMemory = 1;
        $timeUsed = 2;

        $expectedResult = array(
            '\\Phprtest\\TestStub' => array(
                'testStub' => array(
                    1 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                            'softLimit' => 10,
                            'hardLimit' => 20,
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                            'softLimit' => '5',
                            'hardLimit' => '5',
                        ),
                    ),
                ),
            ),
        );

        $expectedResultChecked = array(
            '\\Phprtest\\TestStub' => array(
                'testStub' => array(
                    1 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                            'softLimit' => 10,
                            'hardLimit' => 20,
                            'status' => 'ok',
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                            'softLimit' => '5',
                            'hardLimit' => '5',
                            'status' => 'ok',
                        ),
                    ),
                ),
            ),
        );

        $expectedStatus = array(
            'tests' => 0,
            'softHits' => array(),
            'hardHits' => array(),
        );

        $expectedStatusChecked = array(
            'tests' => 1,
            'softHits' => [],
            'hardHits' => [],
        );

        $expectedOutput = 'output';

        $printer = $this->getMock('\Phprtest\Printer\PrinterInterface');
        $printer->expects($this->at(0))->method('render')
            ->with($expectedResult, $expectedStatus)
            ->will($this->returnValue($expectedOutput));
        $printer->expects($this->at(1))->method('render')
            ->with($expectedResultChecked, $expectedStatusChecked)
            ->will($this->returnValue($expectedOutput));

        $profiler = $this->getMock('\Phprtest\ProfilerInterface');
        $profiler->expects($this->any())->method('getMaxMemory')
            ->will($this->returnValue($maxMemory));
        $profiler->expects($this->any())->method('getTimeUsed')
            ->will($this->returnValue($timeUsed));

        $this->instance->setProfiler($profiler);
        $this->instance->setPrinter($printer);
        $this->instance->run('\Phprtest\TestStub');

        $this->assertEquals($expectedOutput, $this->instance->processResults(), "", 0.1, 5);

        $this->instance->checkLimits();

        $this->assertEquals($expectedOutput, $this->instance->processResults(), "", 0.1, 5);

        $this->assertFalse($this->instance->isFailure());
    }

    public function testRunMissingProvider()
    {
        $missingProvider = 'missingProvider';

        $providerAnnotation = $this->getMock('\Phprtest\Annotations\ProviderAnnotation');
        $providerAnnotation->expects($this->any())->method('getProvider')
            ->will($this->returnValue($missingProvider));

        $annotations = $this->getMock('\mindplay\annotations\AnnotationManager');
        $annotations->expects($this->any())->method('getMethodAnnotations')
            ->with('Phprtest\TestStub', 'testStub', '@provider')
            ->will($this->returnValue([$providerAnnotation]));

        $this->instance->setAnnotations($annotations);
        $this->assertEquals($annotations, $this->instance->getAnnotations());

        $this->setExpectedException('Phprtest\PhprtestException', '[\Phprtest\TestStub:testStub] Provider method ' . $missingProvider . ' not found');
        $this->instance->run('\Phprtest\TestStub');
    }

    public function testRunRepeatNoProviderNoAssert()
    {
        $maxMemory = 1;
        $timeUsed = 2;

        $expectedResult = array(
            '\\Phprtest\\TestStub' => array(
                'testStub' => array(
                    1 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                        ),
                    ),
                    2 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                        ),
                    ),
                    3 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                        ),
                    ),
                ),
            ),
        );

        $expectedResultChecked = array(
            '\\Phprtest\\TestStub' => array(
                'testStub' => array(
                    1 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                            'status' => 'ok',
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                            'status' => 'ok',
                        ),
                    ),
                    2 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                            'status' => 'ok',
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                            'status' => 'ok',
                        ),
                    ),
                    3 => array(
                        'memoryUsage' => array(
                            'result' => $maxMemory,
                            'status' => 'ok',
                        ),
                        'timeUsage' => array(
                            'result' => $timeUsed,
                            'status' => 'ok',
                        ),
                    ),
                ),
            ),
        );

        $expectedStatus = array(
            'tests' => 0,
            'softHits' => array(),
            'hardHits' => array(),
        );

        $expectedStatusChecked = array(
            'tests' => 3,
            'softHits' => [],
            'hardHits' => [],
        );

        $expectedOutput = 'output';

        $printer = $this->getMock('\Phprtest\Printer\PrinterInterface');
        $printer->expects($this->at(0))->method('render')
            ->with($expectedResult, $expectedStatus)
            ->will($this->returnValue($expectedOutput));
        $printer->expects($this->at(1))->method('render')
            ->with($expectedResultChecked, $expectedStatusChecked)
            ->will($this->returnValue($expectedOutput));

        $profiler = $this->getMock('\Phprtest\ProfilerInterface');
        $profiler->expects($this->any())->method('getMaxMemory')
            ->will($this->returnValue($maxMemory));
        $profiler->expects($this->any())->method('getTimeUsed')
            ->will($this->returnValue($timeUsed));

        $repeatAnnotation = $this->getMock('\Phprtest\Annotations\RepeatAnnotation');
        $repeatAnnotation->expects($this->any())->method('getRepeatTimes')
            ->will($this->returnValue(3));

        $annotations = $this->getMock('\mindplay\annotations\AnnotationManager');
        $annotations->expects($this->at(0))->method('getMethodAnnotations')
            ->with('Phprtest\TestStub', 'testStub', '@provider')
            ->will($this->returnValue([]));
        $annotations->expects($this->at(1))->method('getMethodAnnotations')
            ->with('Phprtest\TestStub', 'testStub', '@repeat')
            ->will($this->returnValue([$repeatAnnotation]));
        $annotations->expects($this->at(2))->method('getMethodAnnotations')
            ->with('Phprtest\TestStub', 'testStub', '@assert')
            ->will($this->returnValue([]));

        $this->instance->setAnnotations($annotations);
        $this->instance->setProfiler($profiler);
        $this->instance->setPrinter($printer);
        $this->instance->run('\Phprtest\TestStub');

        $this->assertEquals($expectedOutput, $this->instance->processResults(), "", 0.1, 5);

        $this->instance->checkLimits();

        $this->assertEquals($expectedOutput, $this->instance->processResults(), "", 0.1, 5);

        $this->assertFalse($this->instance->isFailure());
    }

}
