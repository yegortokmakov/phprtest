<?php

namespace Phprtest\Printer;

class Console implements PrinterInterface
{
    protected $tableConverter;

    /**
     * @param \Phprtest\Printer\ArrayToTextTable $tableConverter
     */
    public function setTableConverter($tableConverter)
    {
        $this->tableConverter = $tableConverter;
    }

    /**
     * @return \Phprtest\Printer\ArrayToTextTable
     */
    public function getTableConverter()
    {
        return $this->tableConverter;
    }

    public function __construct()
    {
        $this->setTableConverter(new ArrayToTextTable);
    }

    public function render(array $results, array $status)
    {
        $output = '';

        foreach ($results as $suiteName => $suiteResult) {
            $output .= $suiteName . PHP_EOL;

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

            ob_start();

            $this->getTableConverter()->convert($suiteResultMerge);
            $this->getTableConverter()->showHeaders(true);
            $this->getTableConverter()->render();

            $output .= ob_get_contents();
            ob_end_clean();

            $output .= PHP_EOL . PHP_EOL;
        }

        if (count($status['softHits'])) {
            $output .= 'Warnings:' . PHP_EOL;
            foreach ($status['softHits'] as $value) {
                $output .= $value . PHP_EOL;
            }
            $output .= PHP_EOL;
        }


        if (count($status['hardHits'])) {
            $output .= 'Failures:' . PHP_EOL;
            foreach ($status['hardHits'] as $value) {
                $output .= $value . PHP_EOL;
            }
            $output .= PHP_EOL;
        }

        $output .=sprintf("%d tests completed. %d warnings, %d failures. \r\n", $status['tests'], count($status['softHits']), count($status['hardHits']));


        return $output;
    }
}