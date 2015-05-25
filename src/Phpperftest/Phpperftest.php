<?php

namespace Phpperftest;

use Phpperftest\Printer\Console as ConsolePrinter;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;

class Phpperftest
{
    const VERSION = "0.1";

    protected $annotationManager;

    protected $printer;

    protected $result = array();
    protected $status = array(
        'tests' => 0,
        'softHits' => array(),
        'hardHits' => array(),            
    );

    public function __construct()
    {
        Annotations::$config['cache'] = new AnnotationCache(__DIR__ . '/../../tests/phpperf/cache');
        $this->annotationManager = Annotations::getManager();
        $this->annotationManager->registry['assert'] = 'Phpperftest\Annotations\AssertAnnotation';

        $this->printer = new ConsolePrinter;
    }

    public function run($testClassName)
    {
        $testObject = new $testClassName;

        foreach (get_class_methods($testClassName) as $methodName) {
            try {
                if (substr($methodName, 0, 4) != 'test') continue;

                $this->results[$testClassName][$methodName] = array_merge_recursive(
                    $this->runTest($testObject, $methodName),
                    $this->getLimits($testClassName, $methodName)
                );
            } catch (\Exception $e) {
                throw new PhpperftestException(sprintf('[%s:%s] %s', $testClassName, $methodName, $e->getMessage()));
            }
        }
    }

    public function processResults()
    {
        ob_start();
        $this->printer->render($this->results, $this->status);
        $results = ob_get_contents();
        ob_end_clean();

        return $results;
    }

    public static function versionString()
    {
        return 'PHP Performance Tests v.' . self::VERSION;
    }

    public function isFailure()
    {
        return (bool) count($this->status['hardHits']);
    }

    protected function runTest($testObject, $methodName)
    {
        $memBefore = memory_get_usage(true);
        $timeBefore = microtime(true);

        $testObject->$methodName();

        return array(
            'memoryUsage' => array(
                'result' => memory_get_usage(true) - $memBefore,
            ),
            'timeUsage' => array(
                'result' => microtime(true) - $timeBefore,
            ),
            'memoryPeakUsage' => array(
                'result' => memory_get_peak_usage(true),
            ),
        );
    }

    public function checkLimits()
    {
        foreach ($this->results as $suiteName => &$suiteResult) {
            foreach ($suiteResult as $testName => &$testResult) {
                $this->status['tests']++;

                foreach ($testResult as $metricName => &$metricResults) {
                    if (isset($metricResults['hardLimit']) && $metricResults['result'] > $metricResults['hardLimit']) {
                        $metricResults['status'] = 'hardHit';
                        $this->status['hardHits'][] = sprintf('%s:%s failed assertion for %s: %f > %s', $suiteName, $testName, $metricName, $metricResults['result'], $metricResults['hardLimit']);
                    } elseif (isset($metricResults['softLimit']) && $metricResults['result'] > $metricResults['softLimit']) {
                        $metricResults['status'] = 'softHit';
                        $this->status['softHits'][] = sprintf('%s:%s warning assertion for %s: %f > %s', $suiteName, $testName, $metricName, $metricResults['result'], $metricResults['softLimit']);
                    } else {
                        $metricResults['status'] = 'ok';
                    }
                }
            }
        }
    }

    protected function getLimits($testClassName, $methodName)
    {
        $annotations = $this->annotationManager->getMethodAnnotations($testClassName, $methodName, '@assert');
        $limits = array();

        foreach ($annotations as $annotation) {
            $limits[$annotation->metric] = array(
                'softLimit' => $annotation->softLimit,
                'hardLimit' => $annotation->hardLimit,
            );
        }

        return $limits;
    }
}
