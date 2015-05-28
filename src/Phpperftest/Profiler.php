<?php

namespace Phpperftest;

class Profiler
{
    protected $realMemoryUsage;

    protected $saveMemoryLog;
    protected $saveTimestamp;
    protected $saveBacktrace;

    protected $maxMemory = null;
    protected $minMemory = null;
    protected $iniMemory = null;

    protected $logFile = null;
    protected $logFileHandler = null;

    protected $logs = array();

    public function __construct($realMemoryUsage = true, $logFile = null, $memoryLog = false, $timestamp = false, $backtrace = false)
    {
        $this->realMemoryUsage = $realMemoryUsage;

        $this->saveMemoryLog = $memoryLog;
        $this->saveTimestamp = $timestamp;
        $this->saveBacktrace = $backtrace;

        $this->logFile = $logFile;
    }

    public function start()
    {
        if (!is_null($this->logFile)) {
            $this->logFileHandler = fopen($this->logFile, "w");
        }
        register_tick_function(array($this, "tick" ));
        declare(ticks = 1);
    }

    public function stop()
    {
        unregister_tick_function(array($this,"tick"));
        if ($this->logFileHandler) {
            fclose($this->logFileHandler);
        }
    }

    public function tick()
    {
        $memUsage = memory_get_usage($this->realMemoryUsage);

        if (is_null($this->iniMemory)) $this->iniMemory = $this->minMemory = $memUsage;

        if ($memUsage > $this->maxMemory) $this->maxMemory = $memUsage;
        if ($memUsage < $this->minMemory) $this->minMemory = $memUsage;

        if ($this->saveMemoryLog) $this->logs['memory'][] = $memUsage;
        if ($this->saveTimestamp) $this->logs['time'][] = microtime(true);
        if ($this->saveBacktrace) $this->logs['backtrace'][] = debug_backtrace(false);

        if ($this->logFileHandler) {
            fwrite($this->logFileHandler, serialize($this->logs) . "\n");
            $this->logs = array();
        }
    }

    public function getMaxMemory()
    {
        return $this->maxMemory;
    }

    public function getMinMemory()
    {
        return $this->minMemory;
    }

    public function getLogs()
    {
        if (!is_null($this->logFile)) {
            $this->loadLogsFromFile();
        }

        return $this->logs;
    }

    protected function loadLogsFromFile()
    {
        $fp = fopen($this->logFile, "r");
        if (!$fp) {
            die(':(');
        }

        $logs = array();
        while (($line = fgets($fp)) !== false) {
            $logs[] = unserialize($line);
        }

        fclose($fp);

        $this->logs = call_user_func_array('array_merge_recursive', $logs);

        unset($logs);
    }
}
