<?php

namespace Phprtest;

use Phprtest\Printer\Console as ConsolePrinter;
use mindplay\annotations\AnnotationCache;
use mindplay\annotations\Annotations;
use Phprtest\Printer\PrinterInterface;

class Phprtest
{
    const VERSION = "0.1";

    /**
     * @var \mindplay\annotations\AnnotationManager
     */
    protected $annotations;

    /**
     * @var PrinterInterface
     */
    protected $printer;

    /**
     * @var ProfilerInterface
     */
    protected $profiler;

    protected $results = array();

    protected $status = array(
        'tests' => 0,
        'softHits' => array(),
        'hardHits' => array(),
    );

    public function __construct()
    {
        Annotations::$config['cache'] = false;
        $annotationManager = Annotations::getManager();
        $annotationManager->registry['assert'] = 'Phprtest\Annotations\AssertAnnotation';
        $annotationManager->registry['provider'] = 'Phprtest\Annotations\ProviderAnnotation';
        $annotationManager->registry['repeat'] = 'Phprtest\Annotations\RepeatAnnotation';

        $this->setAnnotations($annotationManager);
        $this->setPrinter(new ConsolePrinter);
        $this->setProfiler(new Profiler(true));
    }

    /**
     * @param PrinterInterface $printer
     */
    public function setPrinter(PrinterInterface $printer)
    {
        $this->printer = $printer;
    }

    /**
     * @return PrinterInterface
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @param ProfilerInterface $profiler
     */
    public function setProfiler(ProfilerInterface $profiler)
    {
        $this->profiler = $profiler;
    }

    /**
     * @return ProfilerInterface
     */
    public function getProfiler()
    {
        return $this->profiler;
    }

    /**
     * @param \mindplay\annotations\AnnotationManager $annotations
     */
    public function setAnnotations($annotations)
    {
        $this->annotations = $annotations;
    }

    /**
     * @return \mindplay\annotations\AnnotationManager
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    public function run($testClassName)
    {
        $testObject = new $testClassName;

        foreach (get_class_methods($testClassName) as $methodName) {
            try {
                if (substr($methodName, 0, 4) != 'test') continue;

                $this->results[$testClassName][$methodName] = $this->runTest($testObject, $methodName);
            } catch (\Exception $e) {
                throw new PhprtestException(sprintf('[%s:%s] %s', $testClassName, $methodName, $e->getMessage()));
            }
        }
    }

    public function processResults()
    {
        return $this->printer->render($this->results, $this->status);
    }

    public static function versionString()
    {
        return 'PHP Resources usage tests v.' . self::VERSION;
    }

    public function isFailure()
    {
        return (bool) count($this->status['hardHits']);
    }

    protected function runTest($testObject, $methodName)
    {
        /** @var \Phprtest\Annotations\ProviderAnnotation[] $provider */
        $provider = $this->annotations->getMethodAnnotations(get_class($testObject), $methodName, '@provider');
        if (count($provider)) {
            $provider = $provider[0]->getProvider();

            if (!method_exists($testObject, $provider)) {
                throw new PhprtestException(sprintf('Provider method %s not found', $provider));
            }
            $providerData = $testObject->$provider();

            if (is_null($providerData)) $providerData = [];
        } else {
            $providerData = [];
        }

        /** @var \Phprtest\Annotations\RepeatAnnotation[] $repeat */
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
        /** @var \Phprtest\Annotations\AssertAnnotation $annotations */
        $annotations = $this->annotations->getMethodAnnotations($testClassName, $methodName, '@assert');
        $limits = array();

        if (count($annotations)) {
            foreach ($annotations as $annotation) {
                $limits[$annotation->metric] = array(
                    'softLimit' => $annotation->softLimit,
                    'hardLimit' => $annotation->hardLimit,
                );
            }
        }

        return $limits;
    }
}
