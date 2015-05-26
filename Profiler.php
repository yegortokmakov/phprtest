<?php

class Profiler
{
    protected $realMemoryUsage;
    protected $logFileHandler;

    protected $saveMemoryLog;
    protected $saveTimestamp;
    protected $saveBacktrace;

    protected $maxMemory = null;
    protected $minMemory = null;
    protected $iniMemory = null;

    protected $logs = array();

    public function __construct($realMemoryUsage = true, $profileFile = null, $memoryLog = false, $timestamp = false, $backtrace = false)
    {
        $this->realMemoryUsage = $realMemoryUsage;

        $this->saveMemoryLog = $memoryLog;
        $this->saveTimestamp = $timestamp;
        $this->saveBacktrace = $backtrace;

        if (!is_null($profileFile)) {
            $this->logFileHandler = true;
        }
    }

    public function start()
    {
        register_tick_function(array($this, "tick" ));
        declare(ticks = 1);
    }

    public function stop()
    {
        unregister_tick_function(array($this,"tick"));
    }

    function tick()
    {
        $memUsage = memory_get_usage($this->realMemoryUsage);

        if (is_null($this->iniMemory)) $this->iniMemory = $this->minMemory = $memUsage;

        if ($memUsage > $this->maxMemory) $this->maxMemory = $memUsage;
        if ($memUsage < $this->minMemory) $this->minMemory = $memUsage;

        if ($this->saveMemoryLog) $this->logs['memory'][] = $memUsage;
        if ($this->saveTimestamp) $this->logs['time'][] = microtime(true);
        if ($this->saveBacktrace) $this->logs['backtrace'][] = debug_backtrace(false);

        if ($this->logFileHandler) {
            $a1 = $a2 = $this->logs;
            var_dump(array_merge_recursive($a1, $a2));die;
            var_dump(serialize($this->logs));die;
            echo 'dump to file';
        }
    }

    public function getResults()
    {
        var_dump($this->minMemory, $this->maxMemory, $this->logs);
    }
}

$a = new Profiler(0, 0, true, true, true);
$a->start();
$s = str_repeat('asd' , 99999);
$a->stop();
$a->getResults();

