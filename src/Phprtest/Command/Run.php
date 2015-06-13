<?php

namespace Phprtest\Command;

use Phprtest\Phprtest;
use Phprtest\PhprtestException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class Run extends Command
{
    /**
     * @var array of options (command run)
     */
    protected $options=[];

    /**
     * Sets Run arguments
     */
    protected function configure()
    {
        $this->setDefinition(
            array(
                new InputArgument('suite', InputArgument::REQUIRED, 'suite to be tested'),
                new InputOption('no-checks', '', InputOption::VALUE_NONE, 'Run tests without assertions'),
                new InputOption('etalon-test', '', InputOption::VALUE_NONE, 'Run etalon test'),
            )
        );

        parent::configure();
    }

    public function getDescription()
    {
        return 'Runs the test suites';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(Phprtest::versionString() . PHP_EOL . str_repeat('-', strlen(Phprtest::versionString()) + 5) . PHP_EOL);

        $tests = $this->loadTests($input->getArgument('suite'));

        if ($input->getOption('etalon-test')) {
            array_unshift($tests, 'Phprtest\EtalonTest');
        }

        $phprtest = new Phprtest();

        foreach ($tests as $test) {
            $phprtest->run($test);
        }

        if ($input->getOption('no-checks')) {
            $output->writeln("<fg=white;bg=yellow>  All assertions are skipped (--no-checks flag)  </fg=white;bg=yellow>\n");
        } else {
            $phprtest->checkLimits();
        }

        $output->write($phprtest->processResults());

        if ($phprtest->isFailure()) {
            $output->writeln("\n<fg=white;bg=red>\n TEST FAILED \n</fg=white;bg=red>");
            exit(1);
        }
    }

    protected function loadTests($path)
    {
        $path = getcwd() . DIRECTORY_SEPARATOR . $path;

        if (is_file($path)) {
            $files = array($path);
        } elseif (is_dir($path)) {
            $files = $this->scanTestDir(realpath($path));
        }

        $tests = array();

        foreach ($files as $filename) {
            $tests[] = $this->loadTest($filename);
        }

        return $tests;
    }

    protected function scanTestDir($path)
    {
        $testFiles = array();

        foreach (scandir($path) as $element) {
            if ($element == '.' || $element == '..') continue;

            $element = $path . DIRECTORY_SEPARATOR . $element;

            if (is_file($element) && substr($element, -8) == 'Test.php') {
                $testFiles[] = $element;
            } elseif (is_dir($element)) {
                $testFiles = array_merge($testFiles, $this->scanTestDir($element));
            }
        }

        return $testFiles;
    }

    protected function loadTest($filename)
    {
        require_once($filename);

        $className = pathinfo($filename)['filename'];
        foreach (array_reverse(get_declared_classes()) as $loadedClass) {
            if (strpos($loadedClass, $className) !== false) {
                return $loadedClass;
            }
        }
    }
}
