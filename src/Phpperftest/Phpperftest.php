<?php

namespace Phpperftest;

use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;

class Phpperftest
{
    const VERSION = "0.0.1";

    protected $annotations = array(
        memoryUsage'] = 'Phpperftest\Annotations\MemoryUsageAnnotation'
        'timeUsage'] = 'Phpperftest\Annotations\TimeUsageAnnotation';
    );

    public function run($suite, $test)
    {
        require_once($suite . '/' . $test);
        Annotations::$config['cache'] = new AnnotationCache(__DIR__ . '/../../tests/phpperf/cache');
        $annotationManager = Annotations::getManager();
        $annotationManager->registry['memoryUsage'] = 'Phpperftest\Annotations\MemoryUsageAnnotation';
        $annotationManager->registry['timeUsage'] = 'Phpperftest\Annotations\TimeUsageAnnotation';

        $testName = str_replace('.php', '', $test);

        $testSuite = new $testName;

        $testReflection = new \ReflectionClass($testSuite);

        foreach ($testReflection->getMethods() as $testMethod) {
            if (substr($testMethod->getName(), 0, 4) != 'test') {
                continue;
            }

            $a = $annotationManager->getMethodAnnotations($testReflection, $testMethod->getName());
            //@todo: I want to have names of annotations
            foreach
            var_dump($a);die;

            $testMethod->invoke($testSuite);
        }
    }
}
