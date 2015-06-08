<?php

namespace Phprtest;

interface ProfilerInterface
{
    public function getMaxMemory();

    public function getMinMemory();

    public function start();

    public function getLogs();

    public function stop();

    public function getTimeUsed();
}