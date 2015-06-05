<?php

namespace Phpperftest\Printer;

interface PrinterInterface
{
    public function render($output, $status);
}