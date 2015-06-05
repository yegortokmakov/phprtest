<?php

namespace Phpperftest;

use Phpperftest\Printer\Console as ConsolePrinter;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;

class Phpperftest
{
    const VERSION = "0.1";

    /**
     * @var \mindplay\annotations\AnnotationManager
     */
    protected $annotations;

    protected $printer;

    protected $profiler;

    protected $results = array();

    protected $status = array(
        'tests' => 0,
        'softHits' => array(),
        'hardHits' => array(),            
    );

    public function __construct()
    {
//        Annotations::$config['cache'] = new AnnotationCache(__DIR__ . '/../../tests/phpperf/cache');
        Annotations::$config['cache'] = false;
        $this->annotations = Annotations::getManager();
        $this->annotations->registry['assert'] = 'Phpperftest\Annotations\AssertAnnotation';
        $this->annotations->registry['provider'] = 'Phpperftest\Annotations\ProviderAnnotation';
        $this->annotations->registry['repeat'] = 'Phpperftest\Annotations\RepeatAnnotation';

        $this->printer = new ConsolePrinter;
        $this->profiler = new Profiler(true);
    }

    public function run($testClassName)
    {
        $testObject = new $testClassName;

        foreach (get_class_methods($testClassName) as $methodName) {
            try {
                if (substr($methodName, 0, 4) != 'test') continue;

                $this->results[$testClassName][$methodName] = $this->runTest($testObject, $methodName);
            } catch (\Exception $e) {
                throw new PhpperftestException(sprintf('[%s:%s] %s', $testClassName, $methodName, $e->getMessage()));
            }
        }
    }

    public function processResults()
    {
        return $this->printer->render($this->results, $this->status);
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
        /** @var \Phpperftest\Annotations\ProviderAnnotation[] $provider */
        $provider = $this->annotations->getMethodAnnotations(get_class($testObject), $methodName, '@provider');
        if (count($provider)) {
            $provider = $provider[0]->getProvider();

            if (!method_exists($testObject, $provider)) {
                throw new PhpperftestException(sprintf('[%s:%s] Provider method %s not found', get_class($testObject), $methodName, $provider));
            }
            $providerData = $testObject->$provider();
        } else {
            $providerData = [];
        }

        /** @var \Phpperftest\Annotations\RepeatAnnotation[] $repeat */
        $repeat = $this->annotations->getMethodAnnotations(get_class($testObject), $methodName, '@repeat');
        if (count($repeat)) {
            $repeat = $repeat[0]->getRepeatTimes();
        } else {
            $repeat = 1;
        }

        $result = [];
        for ($runId = 1; $runId <= $repeat; $runId++) {
            $this->profiler->start();

            call_user_func_array([$testObject, $methodName], $providerData);

            $this->profiler->stop();

            $result[$runId] = array_merge_recursive(
                array(
                    'memoryUsage' => array(
                        'result' => $this->profiler->getMaxMemory(),
                    ),
                    'timeUsage' => array(
                        'result' => $this->profiler->getTimeUsed(),
                    ),
                ),
                $this->getLimits(get_class($testObject), $methodName)
            );
        }

        return $result;
    }

    public function checkLimits()
    {
        foreach ($this->results as $suiteName => &$suiteResult) {
            foreach ($suiteResult as $testName => &$testRuns) {
                foreach ($testRuns as $runId => &$testResult) {
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
    }

    protected function getLimits($testClassName, $methodName)
    {
        $annotations = $this->annotations->getMethodAnnotations($testClassName, $methodName, '@assert');
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
