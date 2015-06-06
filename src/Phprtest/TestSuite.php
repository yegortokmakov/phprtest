<?php

namespace Phprtest;

use PHPUnit_Framework_Exception;
use PHPUnit_Framework_MockObject_Generator;
use PHPUnit_Framework_MockObject_MockObject;

class TestSuite {
    /**
     * @var PHPUnit_Framework_MockObject_Generator
     */
    private $mockObjectGenerator = null;

    /**
     * Returns a mock object for the specified class.
     *
     * @param  string                                  $originalClassName       Name of the class to mock.
     * @param  array|null                              $methods                 When provided, only methods whose names are in the array
     *                                                                          are replaced with a configurable test double. The behavior
     *                                                                          of the other methods is not changed.
     *                                                                          Providing null means that no methods will be replaced.
     * @param  array                                   $arguments               Parameters to pass to the original class' constructor.
     * @param  string                                  $mockClassName           Class name for the generated test double class.
     * @param  boolean                                 $callOriginalConstructor Can be used to disable the call to the original class' constructor.
     * @param  boolean                                 $callOriginalClone       Can be used to disable the call to the original class' clone constructor.
     * @param  boolean                                 $callAutoload            Can be used to disable __autoload() during the generation of the test double class.
     * @param  boolean                                 $cloneArguments
     * @param  boolean                                 $callOriginalMethods
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws PHPUnit_Framework_Exception
     * @since  Method available since Release 3.0.0
     */
    public function getMock($originalClassName, $methods = array(), array $arguments = array(), $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $cloneArguments = false, $callOriginalMethods = false)
    {
        $mockObject = $this->getMockObjectGenerator()->getMock(
            $originalClassName,
            $methods,
            $arguments,
            $mockClassName,
            $callOriginalConstructor,
            $callOriginalClone,
            $callAutoload,
            $cloneArguments,
            $callOriginalMethods
        );

        // @todo: caching?
        // $this->mockObjects[] = $mockObject;

        return $mockObject;
    }

    /**
     * Get the mock object generator, creating it if it doesn't exist.
     *
     * @return   PHPUnit_Framework_MockObject_Generator
     */
    protected function getMockObjectGenerator()
    {
        if (null === $this->mockObjectGenerator) {
            $this->mockObjectGenerator = new PHPUnit_Framework_MockObject_Generator;
        }

        return $this->mockObjectGenerator;
    }
}
