<?php

namespace Phpperftest\Command;

use Phpperftest\Phpperftest;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class Run extends Command
{
    /**
     * @var Phpperftest
     */
    protected $phpperftest;

    /**
     * @var array of options (command run)
     */
    protected $options=[];

    /**
     * @var OutputInterface
     */
    protected $output;


    /**
     * Sets Run arguments
     */
    protected function configure()
    {
        $this->setDefinition(
            array(
                new InputArgument('suite', InputArgument::REQUIRED, 'suite to be tested'),
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
        $this->options = $input->getOptions();
        $this->output = $output;

        $suite = $input->getArgument('suite');

        $tests = $this->getTests($suite);

        $this->phpperftest = new Phpperftest();

        foreach ($tests as $test) {
            $this->phpperftest->run($suite, $test);
        }

//        $this->phpperftest->printResult();
//
//        if (! $this->phpperftest->getResult()->wasSuccessful()) {
//            exit(1);
//        }
    }

    protected function getTests($path)
    {
        $tests = array();

        if (is_file($path)) {
            $tests[] = $path;
        } elseif (is_dir($path)) {
            $files = scandir($path);

            foreach ($files as $file) {
                if (substr($file, -8) == 'Test.php') {
                    $tests[] = $file;
                }
            }
        }

        return $tests;
    }
}