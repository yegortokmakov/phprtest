<?php

namespace Phpperftest\Printer;

class Console implements PrinterInterface
{
    protected $tableConverter;

    public function __construct()
    {
        $this->tableConverter = new ArrayToTextTable;
    }

    public function render($results, $status)
    {
        foreach ($results as $suiteName => $suiteResult) {
            echo $suiteName . PHP_EOL;

            $suiteResultMerge = array();

            foreach ($suiteResult as $testName => $testRuns) {
                foreach ($testRuns as $runNumber => $testResult) {
                    array_walk($testResult, function ($item, $key) use ($testName, &$suiteResultMerge, $runNumber) {
                        $item = array_reverse($item, true);
                        $item['metric'] = sprintf('%s:%s #%s', $testName, $key, $runNumber);
                        $item['result'] = $key == 'timeUsage' ? sprintf('%f', $item['result']) : sprintf('%d', $item['result']);
                        $item = array_reverse($item, true);

                        $suiteResultMerge[] = $item;
                    });
                }
            }

            $this->tableConverter->convert($suiteResultMerge);
            $this->tableConverter->showHeaders(true);
            $this->tableConverter->render();

            echo PHP_EOL . PHP_EOL;
        }

        if (count($status['softHits'])) {
            echo 'Warnings:' . PHP_EOL;
            foreach ($status['softHits'] as $value) {
                echo $value . PHP_EOL;
            }
            echo PHP_EOL;
        }


        if (count($status['hardHits'])) {
            echo 'Failures:' . PHP_EOL;
            foreach ($status['hardHits'] as $value) {
                echo $value . PHP_EOL;
            }
            echo PHP_EOL;
        }

        printf("%d tests completed. %d warnings, %d failures. \r\n", $status['tests'], count($status['softHits']), count($status['hardHits']));
    }
}