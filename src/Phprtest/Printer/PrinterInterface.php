<?php

namespace Phprtest\Printer;

interface PrinterInterface
{
    public function render($output, $status);
}